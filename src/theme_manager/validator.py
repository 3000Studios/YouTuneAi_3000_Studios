"""Theme validation module."""

import json
from pathlib import Path
from typing import List, Dict
import structlog

logger = structlog.get_logger(__name__)


class ThemeValidator:
    """Validates theme structure and content."""

    REQUIRED_FILES = ["theme.json"]
    ALLOWED_EXTENSIONS = {
        ".css",
        ".js",
        ".json",
        ".html",
        ".htm",
        ".png",
        ".jpg",
        ".jpeg",
        ".gif",
        ".svg",
        ".ico",
        ".woff",
        ".woff2",
        ".ttf",
        ".eot",
        ".md",
        ".txt",
        ".php",  # For WordPress themes
    }
    MAX_FILE_SIZE = 10 * 1024 * 1024  # 10 MB per file

    def validate_structure(self, theme_dir: Path) -> bool:
        """Validate theme directory structure.

        Args:
            theme_dir: Path to theme directory

        Returns:
            True if valid, False otherwise
        """
        logger.info("Validating theme structure", theme_dir=str(theme_dir))

        # Check required files
        for required_file in self.REQUIRED_FILES:
            file_path = theme_dir / required_file
            if not file_path.exists():
                logger.error("Required file missing", file=required_file)
                return False

        # Validate theme.json
        theme_json = theme_dir / "theme.json"
        if not self._validate_theme_json(theme_json):
            return False

        # Check all files
        for file_path in theme_dir.rglob("*"):
            if file_path.is_file():
                # Check file extension
                if file_path.suffix.lower() not in self.ALLOWED_EXTENSIONS:
                    logger.warning(
                        "Unsupported file extension",
                        file=str(file_path),
                        extension=file_path.suffix,
                    )

                # Check file size
                if file_path.stat().st_size > self.MAX_FILE_SIZE:
                    logger.error(
                        "File too large",
                        file=str(file_path),
                        size=file_path.stat().st_size,
                    )
                    return False

        logger.info("Theme structure validation passed")
        return True

    def _validate_theme_json(self, json_path: Path) -> bool:
        """Validate theme.json file.

        Args:
            json_path: Path to theme.json

        Returns:
            True if valid, False otherwise
        """
        try:
            with open(json_path, "r", encoding="utf-8") as f:
                data = json.load(f)

            # Check required fields
            required_fields = ["name", "version"]
            for field in required_fields:
                if field not in data:
                    logger.error("Missing required field in theme.json", field=field)
                    return False

            # Validate name
            if not isinstance(data["name"], str) or not data["name"]:
                logger.error("Invalid theme name")
                return False

            # Validate version
            if not isinstance(data["version"], str) or not data["version"]:
                logger.error("Invalid theme version")
                return False

            logger.info("theme.json validation passed")
            return True

        except json.JSONDecodeError as e:
            logger.error("Invalid JSON in theme.json", error=str(e))
            return False
        except Exception as e:
            logger.error("Error validating theme.json", error=str(e))
            return False

    def get_validation_report(self, theme_dir: Path) -> Dict:
        """Generate detailed validation report.

        Args:
            theme_dir: Path to theme directory

        Returns:
            Validation report dictionary
        """
        report = {
            "valid": True,
            "errors": [],
            "warnings": [],
            "file_count": 0,
            "total_size": 0,
        }

        # Check required files
        for required_file in self.REQUIRED_FILES:
            file_path = theme_dir / required_file
            if not file_path.exists():
                report["valid"] = False
                report["errors"].append(f"Missing required file: {required_file}")

        # Validate theme.json
        theme_json = theme_dir / "theme.json"
        if theme_json.exists():
            try:
                with open(theme_json, "r", encoding="utf-8") as f:
                    data = json.load(f)

                for field in ["name", "version"]:
                    if field not in data:
                        report["valid"] = False
                        report["errors"].append(f"Missing field in theme.json: {field}")
            except Exception as e:
                report["valid"] = False
                report["errors"].append(f"Invalid theme.json: {str(e)}")

        # Check all files
        for file_path in theme_dir.rglob("*"):
            if file_path.is_file():
                report["file_count"] += 1
                file_size = file_path.stat().st_size
                report["total_size"] += file_size

                if file_path.suffix.lower() not in self.ALLOWED_EXTENSIONS:
                    report["warnings"].append(
                        f"Unsupported extension: {file_path.name} ({file_path.suffix})"
                    )

                if file_size > self.MAX_FILE_SIZE:
                    report["valid"] = False
                    report["errors"].append(
                        f"File too large: {file_path.name} ({file_size} bytes)"
                    )

        return report
