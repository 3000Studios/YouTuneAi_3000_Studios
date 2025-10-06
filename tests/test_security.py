"""Tests for Security Scanner."""

import pytest
import tempfile
import shutil
import zipfile
from pathlib import Path

from theme_manager.security import SecurityScanner


@pytest.fixture
def temp_dir():
    """Create a temporary directory for tests."""
    temp = tempfile.mkdtemp()
    yield Path(temp)
    shutil.rmtree(temp)


@pytest.fixture
def scanner():
    """Create a SecurityScanner instance."""
    return SecurityScanner()


class TestSecurityScanner:
    """Test SecurityScanner functionality."""

    def test_scan_clean_directory(self, scanner, temp_dir):
        """Test scanning a clean directory."""
        theme_dir = temp_dir / "clean_theme"
        theme_dir.mkdir()

        (theme_dir / "style.css").write_text("body { color: black; }")
        (theme_dir / "script.js").write_text("console.log('hello');")

        issues = scanner.scan_directory(theme_dir)

        assert len(issues) == 0

    def test_detect_eval_usage(self, scanner, temp_dir):
        """Test detection of eval() usage."""
        theme_dir = temp_dir / "unsafe_theme"
        theme_dir.mkdir()

        (theme_dir / "unsafe.js").write_text("eval('alert(1)');")

        issues = scanner.scan_directory(theme_dir)

        assert len(issues) > 0
        assert any("eval()" in issue["issue"] for issue in issues)

    def test_detect_suspicious_extension(self, scanner, temp_dir):
        """Test detection of suspicious file extensions."""
        theme_dir = temp_dir / "suspicious_theme"
        theme_dir.mkdir()

        (theme_dir / "malware.exe").write_bytes(b"fake executable")

        issues = scanner.scan_directory(theme_dir)

        assert len(issues) > 0
        assert any("Suspicious file extension" in issue["issue"] for issue in issues)

    def test_scan_safe_zip(self, scanner, temp_dir):
        """Test scanning a safe zip file."""
        zip_path = temp_dir / "safe.zip"

        with zipfile.ZipFile(zip_path, "w") as zipf:
            zipf.writestr("theme.json", '{"name": "test"}')
            zipf.writestr("style.css", "body { }")

        assert scanner.scan_zip_file(zip_path) is True

    def test_detect_path_traversal_in_zip(self, scanner, temp_dir):
        """Test detection of path traversal in zip."""
        zip_path = temp_dir / "malicious.zip"

        with zipfile.ZipFile(zip_path, "w") as zipf:
            zipf.writestr("../../../etc/passwd", "malicious")

        assert scanner.scan_zip_file(zip_path) is False

    def test_security_report(self, scanner, temp_dir):
        """Test security report generation."""
        theme_dir = temp_dir / "theme"
        theme_dir.mkdir()

        (theme_dir / "safe.css").write_text("body { }")
        (theme_dir / "unsafe.js").write_text("eval('test');")

        report = scanner.generate_security_report(theme_dir)

        assert "total_issues" in report
        assert report["total_issues"] > 0
        assert "issues" in report
