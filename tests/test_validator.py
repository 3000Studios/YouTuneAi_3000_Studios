"""Tests for Theme Validator."""

import pytest
import json
import tempfile
import shutil
from pathlib import Path

from theme_manager.validator import ThemeValidator


@pytest.fixture
def temp_dir():
    """Create a temporary directory for tests."""
    temp = tempfile.mkdtemp()
    yield Path(temp)
    shutil.rmtree(temp)


@pytest.fixture
def valid_theme(temp_dir):
    """Create a valid theme directory."""
    theme_dir = temp_dir / "valid_theme"
    theme_dir.mkdir()

    theme_json = {"name": "Valid Theme", "version": "1.0.0"}

    with open(theme_dir / "theme.json", "w") as f:
        json.dump(theme_json, f)

    return theme_dir


@pytest.fixture
def validator():
    """Create a ThemeValidator instance."""
    return ThemeValidator()


class TestThemeValidator:
    """Test ThemeValidator functionality."""

    def test_validate_valid_theme(self, validator, valid_theme):
        """Test validation of a valid theme."""
        assert validator.validate_structure(valid_theme) is True

    def test_missing_theme_json(self, validator, temp_dir):
        """Test validation with missing theme.json."""
        theme_dir = temp_dir / "invalid_theme"
        theme_dir.mkdir()

        assert validator.validate_structure(theme_dir) is False

    def test_invalid_theme_json(self, validator, temp_dir):
        """Test validation with invalid theme.json."""
        theme_dir = temp_dir / "invalid_theme"
        theme_dir.mkdir()

        # Create invalid JSON
        (theme_dir / "theme.json").write_text("{ invalid json }")

        assert validator.validate_structure(theme_dir) is False

    def test_missing_required_fields(self, validator, temp_dir):
        """Test validation with missing required fields."""
        theme_dir = temp_dir / "invalid_theme"
        theme_dir.mkdir()

        # Missing version field
        theme_json = {"name": "Test Theme"}

        with open(theme_dir / "theme.json", "w") as f:
            json.dump(theme_json, f)

        assert validator.validate_structure(theme_dir) is False

    def test_file_too_large(self, validator, valid_theme):
        """Test validation with file exceeding size limit."""
        # Create a file larger than MAX_FILE_SIZE
        large_file = valid_theme / "large.txt"
        large_file.write_bytes(b"x" * (ThemeValidator.MAX_FILE_SIZE + 1))

        assert validator.validate_structure(valid_theme) is False

    def test_validation_report(self, validator, valid_theme):
        """Test validation report generation."""
        report = validator.get_validation_report(valid_theme)

        assert report["valid"] is True
        assert report["file_count"] >= 1
        assert report["total_size"] > 0
        assert len(report["errors"]) == 0

    def test_validation_report_with_errors(self, validator, temp_dir):
        """Test validation report with errors."""
        theme_dir = temp_dir / "invalid_theme"
        theme_dir.mkdir()

        report = validator.get_validation_report(theme_dir)

        assert report["valid"] is False
        assert len(report["errors"]) > 0
