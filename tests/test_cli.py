"""Tests for CLI interface."""

import pytest
import json
import tempfile
import shutil
from pathlib import Path
from click.testing import CliRunner

from theme_manager.cli import main


@pytest.fixture
def temp_dir():
    """Create a temporary directory for tests."""
    temp = tempfile.mkdtemp()
    yield Path(temp)
    shutil.rmtree(temp)


@pytest.fixture
def sample_theme(temp_dir):
    """Create a sample theme directory."""
    theme_dir = temp_dir / "sample_theme"
    theme_dir.mkdir()

    theme_json = {
        "name": "Sample Theme",
        "version": "1.0.0",
        "description": "A test theme",
    }

    with open(theme_dir / "theme.json", "w") as f:
        json.dump(theme_json, f)

    (theme_dir / "style.css").write_text("body { color: black; }")
    (theme_dir / "script.js").write_text("console.log('test');")

    return theme_dir


@pytest.fixture
def runner():
    """Create a CLI runner."""
    return CliRunner()


class TestCLI:
    """Test CLI functionality."""

    def test_version(self, runner):
        """Test version command."""
        result = runner.invoke(main, ["--version"])
        assert result.exit_code == 0
        assert "version" in result.output.lower()

    def test_help(self, runner):
        """Test help command."""
        result = runner.invoke(main, ["--help"])
        assert result.exit_code == 0
        assert "create" in result.output
        assert "list" in result.output
        assert "validate" in result.output

    def test_create_theme(self, runner, sample_theme, temp_dir):
        """Test create command."""
        themes_dir = temp_dir / "themes"

        result = runner.invoke(
            main,
            [
                "create",
                str(sample_theme),
                "test-theme",
                "--version",
                "1.0.0",
                "--themes-dir",
                str(themes_dir),
            ],
        )

        assert result.exit_code == 0
        assert "Theme package created" in result.output

    def test_validate_theme(self, runner, sample_theme):
        """Test validate command."""
        result = runner.invoke(main, ["validate", str(sample_theme)])
        assert result.exit_code == 0
        assert "VALID" in result.output

    def test_validate_invalid_theme(self, runner, temp_dir):
        """Test validate command with invalid theme."""
        invalid_theme = temp_dir / "invalid"
        invalid_theme.mkdir()

        result = runner.invoke(main, ["validate", str(invalid_theme)])
        assert result.exit_code == 1
        assert "INVALID" in result.output

    def test_scan_theme(self, runner, sample_theme):
        """Test scan command."""
        result = runner.invoke(main, ["scan", str(sample_theme)])
        assert result.exit_code == 0
        assert "Security Scan Report" in result.output

    def test_list_themes(self, runner, sample_theme, temp_dir):
        """Test list command."""
        themes_dir = temp_dir / "themes"

        # Create a theme first
        runner.invoke(
            main,
            [
                "create",
                str(sample_theme),
                "test-theme",
                "--themes-dir",
                str(themes_dir),
            ],
        )

        # List themes
        result = runner.invoke(main, ["list", "--themes-dir", str(themes_dir)])
        assert result.exit_code == 0

    def test_list_themes_json(self, runner, sample_theme, temp_dir):
        """Test list command with JSON output."""
        themes_dir = temp_dir / "themes"

        # Create a theme first
        runner.invoke(
            main,
            [
                "create",
                str(sample_theme),
                "test-theme",
                "--themes-dir",
                str(themes_dir),
            ],
        )

        # List themes as JSON
        result = runner.invoke(
            main, ["list", "--themes-dir", str(themes_dir), "--json"]
        )

        assert result.exit_code == 0
        # Output should contain JSON (may have logging lines too)
        assert "[" in result.output or "No themes" in result.output

    def test_unpack_theme(self, runner, sample_theme, temp_dir):
        """Test unpack command."""
        themes_dir = temp_dir / "themes"

        # Create a theme first
        create_result = runner.invoke(
            main,
            [
                "create",
                str(sample_theme),
                "test-theme",
                "--themes-dir",
                str(themes_dir),
            ],
        )

        assert create_result.exit_code == 0

        # Unpack the theme
        package_path = themes_dir / "test-theme-1.0.0.zip"
        result = runner.invoke(
            main, ["unpack", str(package_path), "--themes-dir", str(themes_dir)]
        )

        assert result.exit_code == 0
        assert "Theme unpacked" in result.output

    def test_create_with_metadata(self, runner, sample_theme, temp_dir):
        """Test create command with metadata."""
        themes_dir = temp_dir / "themes"
        metadata = json.dumps({"author": "Test Author", "license": "MIT"})

        result = runner.invoke(
            main,
            [
                "create",
                str(sample_theme),
                "test-theme",
                "--metadata",
                metadata,
                "--themes-dir",
                str(themes_dir),
            ],
        )

        assert result.exit_code == 0

    def test_validate_json_output(self, runner, sample_theme):
        """Test validate command with JSON output."""
        result = runner.invoke(main, ["validate", str(sample_theme), "--json"])
        assert result.exit_code == 0

        # Should be valid JSON
        data = json.loads(result.output)
        assert "valid" in data
        assert data["valid"] is True

    def test_scan_json_output(self, runner, sample_theme):
        """Test scan command with JSON output."""
        result = runner.invoke(main, ["scan", str(sample_theme), "--json"])
        assert result.exit_code == 0

        # Output should contain JSON (may have logging lines too)
        assert "total_issues" in result.output or "{" in result.output
