"""Tests for Theme Manager."""

import pytest
import json
import tempfile
import shutil
from pathlib import Path

from theme_manager.theme_manager import ThemeManager


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

    # Create theme.json
    theme_json = {
        "name": "Sample Theme",
        "version": "1.0.0",
        "description": "A test theme",
    }

    with open(theme_dir / "theme.json", "w") as f:
        json.dump(theme_json, f)

    # Create some CSS and JS files
    (theme_dir / "style.css").write_text("body { color: black; }")
    (theme_dir / "script.js").write_text("console.log('test');")

    return theme_dir


@pytest.fixture
def theme_manager(temp_dir):
    """Create a ThemeManager instance."""
    themes_dir = temp_dir / "themes"
    return ThemeManager(str(themes_dir))


class TestThemeManager:
    """Test ThemeManager functionality."""

    def test_initialization(self, theme_manager):
        """Test ThemeManager initialization."""
        assert theme_manager.themes_dir.exists()
        assert theme_manager.unpacked_dir.exists()

    def test_create_theme_package(self, theme_manager, sample_theme):
        """Test creating a theme package."""
        package_path = theme_manager.create_theme_package(
            str(sample_theme), "sample-theme", "1.0.0"
        )

        assert package_path.exists()
        assert package_path.suffix == ".zip"

        # Check hash file
        hash_file = package_path.with_suffix(".zip.sha256")
        assert hash_file.exists()

    def test_unpack_theme(self, theme_manager, sample_theme):
        """Test unpacking a theme package."""
        # Create package
        package_path = theme_manager.create_theme_package(
            str(sample_theme), "sample-theme", "1.0.0"
        )

        # Unpack
        extract_dir = theme_manager.unpack_theme(str(package_path))

        assert extract_dir.exists()
        assert (extract_dir / "theme.json").exists()
        assert (extract_dir / "style.css").exists()

    def test_list_themes(self, theme_manager, sample_theme):
        """Test listing themes."""
        # Create a theme package
        theme_manager.create_theme_package(str(sample_theme), "sample-theme", "1.0.0")

        themes = theme_manager.list_themes()

        assert len(themes) == 1
        assert themes[0]["name"] == "Sample Theme"
        assert themes[0]["version"] == "1.0.0"

    def test_integrity_verification(self, theme_manager, sample_theme):
        """Test integrity verification."""
        package_path = theme_manager.create_theme_package(
            str(sample_theme), "sample-theme", "1.0.0"
        )

        # Should succeed with valid hash
        extract_dir = theme_manager.unpack_theme(str(package_path), verify=True)
        assert extract_dir.exists()

        # Corrupt the package
        with open(package_path, "ab") as f:
            f.write(b"corrupted")

        # Should fail with corrupted package
        with pytest.raises(ValueError, match="Integrity verification failed"):
            theme_manager.unpack_theme(str(package_path), verify=True)

    def test_invalid_source_directory(self, theme_manager):
        """Test with invalid source directory."""
        with pytest.raises(ValueError, match="does not exist"):
            theme_manager.create_theme_package(
                "/nonexistent/path", "test-theme", "1.0.0"
            )

    def test_zip_safety_path_traversal(self, theme_manager, sample_theme, temp_dir):
        """Test zip safety against path traversal."""
        import zipfile

        # Create a malicious zip with path traversal
        malicious_zip = temp_dir / "malicious.zip"
        with zipfile.ZipFile(malicious_zip, "w") as zipf:
            zipf.writestr(
                "theme.json", json.dumps({"name": "test", "version": "1.0.0"})
            )
            zipf.writestr("../../../evil.txt", "malicious content")

        # Should detect and reject
        with pytest.raises(ValueError, match="Security scan"):
            theme_manager.unpack_theme(str(malicious_zip), verify=False)
