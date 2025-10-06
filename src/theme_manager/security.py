"""Security scanning module for themes."""

import re
import zipfile
from pathlib import Path
from typing import List, Dict
import structlog

logger = structlog.get_logger(__name__)


class SecurityScanner:
    """Scans themes for security vulnerabilities."""

    # Patterns to detect potentially malicious content
    DANGEROUS_PATTERNS = [
        # JavaScript patterns
        (r"eval\s*\(", "Potentially dangerous eval() usage"),
        (r"Function\s*\(", "Potentially dangerous Function() constructor"),
        (r"document\.write", "Potentially dangerous document.write()"),
        (r"innerHTML\s*=", "Potentially dangerous innerHTML assignment"),
        (r'<script[^>]*src\s*=\s*["\']https?://', "External script loading"),
        # Code injection patterns
        (r"exec\s*\(", "Potentially dangerous exec()"),
        (r"system\s*\(", "Potentially dangerous system() call"),
        (r"shell_exec", "Potentially dangerous shell_exec()"),
        (r"passthru", "Potentially dangerous passthru()"),
        # File operation patterns
        (r"\.\./", "Path traversal attempt"),
        (r'file_get_contents\s*\(\s*["\']https?://', "Remote file inclusion"),
        # SQL injection patterns
        (r"(?i)DROP\s+TABLE", "Potential SQL injection"),
        (r"(?i)DELETE\s+FROM", "Potential SQL injection"),
        (r"(?i)INSERT\s+INTO", "Potential SQL injection"),
    ]

    # Suspicious file extensions
    SUSPICIOUS_EXTENSIONS = {
        ".exe",
        ".dll",
        ".so",
        ".dylib",
        ".bat",
        ".sh",
        ".ps1",
        ".php",
        ".asp",
        ".aspx",
        ".jsp",
        ".cgi",
    }

    def scan_directory(self, directory: Path) -> List[Dict]:
        """Scan a directory for security issues.

        Args:
            directory: Path to directory to scan

        Returns:
            List of security issues found
        """
        logger.info("Scanning directory for security issues", directory=str(directory))

        issues = []

        for file_path in directory.rglob("*"):
            if file_path.is_file():
                # Check file extension
                if file_path.suffix.lower() in self.SUSPICIOUS_EXTENSIONS:
                    issues.append(
                        {
                            "severity": "high",
                            "file": str(file_path),
                            "issue": f"Suspicious file extension: {file_path.suffix}",
                        }
                    )

                # Scan file content
                file_issues = self._scan_file(file_path)
                issues.extend(file_issues)

        if issues:
            logger.warning("Security issues found", count=len(issues))
        else:
            logger.info("No security issues found")

        return issues

    def scan_zip_file(self, zip_path: Path) -> bool:
        """Scan a zip file for security issues.

        Args:
            zip_path: Path to zip file

        Returns:
            True if safe, False if issues found
        """
        logger.info("Scanning zip file", zip_path=str(zip_path))

        try:
            with zipfile.ZipFile(zip_path, "r") as zipf:
                # Check file names in archive
                for name in zipf.namelist():
                    # Check for path traversal
                    if ".." in name or name.startswith("/"):
                        logger.error("Path traversal in zip", filename=name)
                        return False

                    # Check for suspicious extensions
                    path = Path(name)
                    if path.suffix.lower() in self.SUSPICIOUS_EXTENSIONS:
                        logger.error("Suspicious file in zip", filename=name)
                        return False

            return True

        except Exception as e:
            logger.error("Error scanning zip file", error=str(e))
            return False

    def _scan_file(self, file_path: Path) -> List[Dict]:
        """Scan a single file for security issues.

        Args:
            file_path: Path to file

        Returns:
            List of issues found
        """
        issues = []

        # Only scan text-based files
        text_extensions = {".js", ".css", ".html", ".htm", ".json", ".txt", ".md"}
        if file_path.suffix.lower() not in text_extensions:
            return issues

        try:
            with open(file_path, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()

            # Check for dangerous patterns
            for pattern, description in self.DANGEROUS_PATTERNS:
                matches = re.finditer(pattern, content)
                for match in matches:
                    # Get line number
                    line_num = content[: match.start()].count("\n") + 1

                    issues.append(
                        {
                            "severity": "medium",
                            "file": str(file_path),
                            "line": line_num,
                            "issue": description,
                            "match": match.group(0),
                        }
                    )

        except Exception as e:
            logger.warning("Could not scan file", file=str(file_path), error=str(e))

        return issues

    def generate_security_report(self, directory: Path) -> Dict:
        """Generate comprehensive security report.

        Args:
            directory: Path to directory to scan

        Returns:
            Security report dictionary
        """
        issues = self.scan_directory(directory)

        report = {
            "total_issues": len(issues),
            "high_severity": len([i for i in issues if i.get("severity") == "high"]),
            "medium_severity": len(
                [i for i in issues if i.get("severity") == "medium"]
            ),
            "low_severity": len([i for i in issues if i.get("severity") == "low"]),
            "issues": issues,
        }

        return report
