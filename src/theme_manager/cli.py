"""Command-line interface for Theme Manager."""

import sys
import json
from pathlib import Path
import click
import structlog

from .theme_manager import ThemeManager
from .validator import ThemeValidator
from .security import SecurityScanner

# Configure structured logging
structlog.configure(
    processors=[
        structlog.processors.TimeStamper(fmt="iso"),
        structlog.processors.add_log_level,
        structlog.processors.JSONRenderer(),
    ]
)

logger = structlog.get_logger(__name__)


@click.group()
@click.version_option(version="1.0.0")
def main():
    """YouTuneAi Theme Manager - Secure theme packaging system."""
    pass


@main.command()
@click.argument("source_dir", type=click.Path(exists=True))
@click.argument("theme_name")
@click.option("--version", "-v", default="1.0.0", help="Theme version")
@click.option("--themes-dir", "-d", default="themes", help="Themes directory")
@click.option("--metadata", "-m", help="Additional metadata as JSON string")
def create(source_dir, theme_name, version, themes_dir, metadata):
    """Create a theme package from a source directory."""
    try:
        manager = ThemeManager(themes_dir)

        # Parse metadata if provided
        meta = None
        if metadata:
            try:
                meta = json.loads(metadata)
            except json.JSONDecodeError:
                click.echo("Error: Invalid JSON in metadata", err=True)
                sys.exit(1)

        package_path = manager.create_theme_package(
            source_dir, theme_name, version, meta
        )

        click.echo(f"✓ Theme package created: {package_path}")
        click.echo(f"✓ Integrity hash: {package_path.with_suffix('.zip.sha256')}")

    except Exception as e:
        click.echo(f"✗ Error: {str(e)}", err=True)
        logger.exception("Failed to create theme package")
        sys.exit(1)


@main.command()
@click.argument("package_path", type=click.Path(exists=True))
@click.option("--themes-dir", "-d", default="themes", help="Themes directory")
@click.option("--no-verify", is_flag=True, help="Skip integrity verification")
def unpack(package_path, themes_dir, no_verify):
    """Unpack a theme package."""
    try:
        manager = ThemeManager(themes_dir)

        extract_dir = manager.unpack_theme(package_path, verify=not no_verify)

        click.echo(f"✓ Theme unpacked to: {extract_dir}")

    except Exception as e:
        click.echo(f"✗ Error: {str(e)}", err=True)
        logger.exception("Failed to unpack theme")
        sys.exit(1)


@main.command()
@click.option("--themes-dir", "-d", default="themes", help="Themes directory")
@click.option("--json", "output_json", is_flag=True, help="Output as JSON")
def list(themes_dir, output_json):
    """List all available theme packages."""
    try:
        manager = ThemeManager(themes_dir)
        themes = manager.list_themes()

        if output_json:
            click.echo(json.dumps(themes, indent=2))
        else:
            if not themes:
                click.echo("No themes found.")
            else:
                click.echo(f"Found {len(themes)} theme(s):\n")
                for theme in themes:
                    click.echo(f"  • {theme['name']} v{theme['version']}")
                    click.echo(f"    File: {theme['file_path']}")
                    click.echo(f"    Size: {theme['file_size']:,} bytes")
                    click.echo(
                        f"    Verified: {'Yes' if theme['has_integrity_hash'] else 'No'}"
                    )
                    click.echo()

    except Exception as e:
        click.echo(f"✗ Error: {str(e)}", err=True)
        logger.exception("Failed to list themes")
        sys.exit(1)


@main.command()
@click.argument("theme_dir", type=click.Path(exists=True))
@click.option("--json", "output_json", is_flag=True, help="Output as JSON")
def validate(theme_dir, output_json):
    """Validate a theme directory."""
    try:
        validator = ThemeValidator()
        report = validator.get_validation_report(Path(theme_dir))

        if output_json:
            click.echo(json.dumps(report, indent=2))
        else:
            click.echo("Theme Validation Report")
            click.echo("=" * 50)
            click.echo(f"Status: {'✓ VALID' if report['valid'] else '✗ INVALID'}")
            click.echo(f"Files: {report['file_count']}")
            click.echo(f"Total Size: {report['total_size']:,} bytes")

            if report["errors"]:
                click.echo(f"\nErrors ({len(report['errors'])}):")
                for error in report["errors"]:
                    click.echo(f"  ✗ {error}")

            if report["warnings"]:
                click.echo(f"\nWarnings ({len(report['warnings'])}):")
                for warning in report["warnings"]:
                    click.echo(f"  ⚠ {warning}")

        sys.exit(0 if report["valid"] else 1)

    except Exception as e:
        click.echo(f"✗ Error: {str(e)}", err=True)
        logger.exception("Failed to validate theme")
        sys.exit(1)


@main.command()
@click.argument("theme_dir", type=click.Path(exists=True))
@click.option("--json", "output_json", is_flag=True, help="Output as JSON")
def scan(theme_dir, output_json):
    """Security scan a theme directory."""
    try:
        scanner = SecurityScanner()
        report = scanner.generate_security_report(Path(theme_dir))

        if output_json:
            click.echo(json.dumps(report, indent=2))
        else:
            click.echo("Security Scan Report")
            click.echo("=" * 50)
            click.echo(f"Total Issues: {report['total_issues']}")
            click.echo(f"High Severity: {report['high_severity']}")
            click.echo(f"Medium Severity: {report['medium_severity']}")
            click.echo(f"Low Severity: {report['low_severity']}")

            if report["issues"]:
                click.echo("\nIssues:")
                for issue in report["issues"]:
                    severity = issue.get("severity", "unknown").upper()
                    file_path = issue.get("file", "unknown")
                    line = issue.get("line", "N/A")
                    description = issue.get("issue", "Unknown issue")

                    click.echo(f"  [{severity}] {file_path}:{line}")
                    click.echo(f"    {description}")
                    if "match" in issue:
                        click.echo(f"    Match: {issue['match']}")
                    click.echo()
            else:
                click.echo("\n✓ No security issues found!")

        sys.exit(0 if report["total_issues"] == 0 else 1)

    except Exception as e:
        click.echo(f"✗ Error: {str(e)}", err=True)
        logger.exception("Failed to scan theme")
        sys.exit(1)


if __name__ == "__main__":
    main()
