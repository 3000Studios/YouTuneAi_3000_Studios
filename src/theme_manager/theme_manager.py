"""Core Theme Manager for packaging and managing theme zip files."""

import os
import zipfile
import hashlib
import json
import shutil
from pathlib import Path
from typing import Optional, Dict, List
from datetime import datetime
import structlog

from .validator import ThemeValidator
from .security import SecurityScanner

logger = structlog.get_logger(__name__)


class ThemeManager:
    """Manages theme packaging, validation, and deployment."""

    def __init__(self, themes_dir: str = "themes"):
        """Initialize ThemeManager.

        Args:
            themes_dir: Directory to store theme packages
        """
        self.themes_dir = Path(themes_dir)
        self.themes_dir.mkdir(parents=True, exist_ok=True)

        self.unpacked_dir = self.themes_dir / "unpacked"
        self.unpacked_dir.mkdir(parents=True, exist_ok=True)

        self.validator = ThemeValidator()
        self.security_scanner = SecurityScanner()

        logger.info("ThemeManager initialized", themes_dir=str(self.themes_dir))

    def create_theme_package(
        self,
        source_dir: str,
        theme_name: str,
        version: str = "1.0.0",
        metadata: Optional[Dict] = None,
    ) -> Path:
        """Create a theme package from a source directory.

        Args:
            source_dir: Source directory containing theme files
            theme_name: Name of the theme
            version: Theme version
            metadata: Additional metadata

        Returns:
            Path to created theme package

        Raises:
            ValueError: If validation fails
        """
        logger.info(
            "Creating theme package",
            theme_name=theme_name,
            version=version,
            source_dir=source_dir,
        )

        source_path = Path(source_dir)
        if not source_path.exists():
            raise ValueError(f"Source directory does not exist: {source_dir}")

        # Validate theme structure
        if not self.validator.validate_structure(source_path):
            raise ValueError("Theme structure validation failed")

        # Security scan
        security_issues = self.security_scanner.scan_directory(source_path)
        if security_issues:
            logger.error("Security issues found", issues=security_issues)
            raise ValueError(f"Security scan failed: {security_issues}")

        # Create metadata
        theme_metadata = {
            "name": theme_name,
            "version": version,
            "created_at": (
                datetime.now(datetime.UTC).isoformat()
                if hasattr(datetime, "UTC")
                else datetime.utcnow().isoformat()
            ),
            "metadata": metadata or {},
        }

        # Create zip package
        package_name = f"{theme_name}-{version}.zip"
        package_path = self.themes_dir / package_name

        with zipfile.ZipFile(package_path, "w", zipfile.ZIP_DEFLATED) as zipf:
            # Add metadata file
            zipf.writestr("theme.json", json.dumps(theme_metadata, indent=2))

            # Add all files from source directory
            for root, dirs, files in os.walk(source_path):
                # Skip hidden files and directories
                dirs[:] = [d for d in dirs if not d.startswith(".")]

                for file in files:
                    if file.startswith("."):
                        continue

                    file_path = Path(root) / file
                    arcname = file_path.relative_to(source_path)
                    zipf.write(file_path, arcname)

        # Generate integrity hash
        file_hash = self._calculate_hash(package_path)
        hash_file = package_path.with_suffix(".zip.sha256")
        hash_file.write_text(file_hash)

        logger.info(
            "Theme package created", package_path=str(package_path), hash=file_hash
        )

        return package_path

    def unpack_theme(self, package_path: str, verify: bool = True) -> Path:
        """Unpack a theme package.

        Args:
            package_path: Path to theme package
            verify: Whether to verify integrity

        Returns:
            Path to unpacked theme directory

        Raises:
            ValueError: If verification or validation fails
        """
        logger.info("Unpacking theme", package_path=package_path)

        package = Path(package_path)
        if not package.exists():
            raise ValueError(f"Package does not exist: {package_path}")

        # Verify integrity
        if verify:
            if not self._verify_integrity(package):
                raise ValueError("Integrity verification failed")

        # Security scan the zip file itself
        if not self.security_scanner.scan_zip_file(package):
            raise ValueError("Security scan of zip file failed")

        # Extract to temporary directory
        extract_dir = self.unpacked_dir / package.stem
        if extract_dir.exists():
            shutil.rmtree(extract_dir)
        extract_dir.mkdir(parents=True)

        with zipfile.ZipFile(package, "r") as zipf:
            # Check for zip bombs
            if not self._check_zip_safety(zipf):
                raise ValueError("Potentially unsafe zip file detected")

            zipf.extractall(extract_dir)

        # Validate extracted theme
        if not self.validator.validate_structure(extract_dir):
            shutil.rmtree(extract_dir)
            raise ValueError("Extracted theme validation failed")

        logger.info("Theme unpacked successfully", extract_dir=str(extract_dir))

        return extract_dir

    def list_themes(self) -> List[Dict]:
        """List all available theme packages.

        Returns:
            List of theme information dictionaries
        """
        themes = []

        for zip_file in self.themes_dir.glob("*.zip"):
            try:
                with zipfile.ZipFile(zip_file, "r") as zipf:
                    if "theme.json" in zipf.namelist():
                        metadata = json.loads(zipf.read("theme.json"))

                        # Add file info
                        metadata["file_size"] = zip_file.stat().st_size
                        metadata["file_path"] = str(zip_file)

                        # Check for hash file
                        hash_file = zip_file.with_suffix(".zip.sha256")
                        metadata["has_integrity_hash"] = hash_file.exists()

                        themes.append(metadata)
            except Exception as e:
                logger.warning("Failed to read theme", file=str(zip_file), error=str(e))

        return themes

    def _calculate_hash(self, file_path: Path) -> str:
        """Calculate SHA256 hash of a file."""
        sha256_hash = hashlib.sha256()

        with open(file_path, "rb") as f:
            for byte_block in iter(lambda: f.read(4096), b""):
                sha256_hash.update(byte_block)

        return sha256_hash.hexdigest()

    def _verify_integrity(self, package_path: Path) -> bool:
        """Verify integrity of a package using SHA256 hash."""
        hash_file = package_path.with_suffix(".zip.sha256")

        if not hash_file.exists():
            logger.warning("No integrity hash file found", package=str(package_path))
            return False

        expected_hash = hash_file.read_text().strip()
        actual_hash = self._calculate_hash(package_path)

        if expected_hash != actual_hash:
            logger.error(
                "Integrity verification failed",
                expected=expected_hash,
                actual=actual_hash,
            )
            return False

        logger.info("Integrity verification passed")
        return True

    def _check_zip_safety(self, zipf: zipfile.ZipFile) -> bool:
        """Check if zip file is safe (no zip bombs or path traversal).

        Args:
            zipf: Open ZipFile object

        Returns:
            True if safe, False otherwise
        """
        max_size = 1024 * 1024 * 500  # 500 MB
        max_ratio = 100  # Compression ratio

        total_size = 0

        for info in zipf.infolist():
            # Check for path traversal
            if ".." in info.filename or info.filename.startswith("/"):
                logger.error("Path traversal detected", filename=info.filename)
                return False

            # Check compression ratio
            if info.compress_size > 0:
                ratio = info.file_size / info.compress_size
                if ratio > max_ratio:
                    logger.error(
                        "Suspicious compression ratio",
                        filename=info.filename,
                        ratio=ratio,
                    )
                    return False

            total_size += info.file_size

            if total_size > max_size:
                logger.error("Zip file too large", total_size=total_size)
                return False

        return True
