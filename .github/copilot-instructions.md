# GitHub Copilot Instructions for YouTuneAi Theme Manager

This document provides guidance for GitHub Copilot when working with this repository.

## Project Overview

YouTubeAi Theme Manager is a fully automated, secure, production-ready theme packaging system written in Python. It provides enterprise-grade security features, automated CI/CD, and comprehensive validation for zip-based theme packages.

## Language and Framework

- **Language**: Python 3.8+
- **Package Manager**: pip
- **Testing Framework**: pytest
- **CLI Framework**: Click
- **Build Tool**: setuptools

## Code Style and Standards

### Python Style Guide

Follow PEP 8 with these repository-specific rules:

- **Line length**: Maximum 100 characters
- **Type hints**: Always use type hints for function parameters and return values
- **String formatting**: Prefer f-strings over .format() or % formatting
- **Comprehensions**: Prefer list/dict comprehensions over map/filter where readable
- **Docstrings**: Use Google-style docstrings for all public functions and classes
- **Imports**: Organize in order: standard library, third-party, local modules

### Example Function Structure

```python
def create_theme_package(
    self,
    source_dir: str,
    theme_name: str,
    version: str = "1.0.0"
) -> Path:
    """Create a theme package from a source directory.

    Args:
        source_dir: Source directory containing theme files
        theme_name: Name of the theme
        version: Theme version (default: "1.0.0")

    Returns:
        Path to created theme package

    Raises:
        ValueError: If validation fails

    Example:
        >>> manager = ThemeManager()
        >>> package = manager.create_theme_package(
        ...     "./my-theme",
        ...     "my-theme",
        ...     "1.0.0"
        ... )
    """
```

## Testing Requirements

### Test Standards

- Write tests for all new features and bug fixes
- Maintain >80% code coverage
- Use descriptive test names that explain the scenario
- Follow AAA pattern: Arrange, Act, Assert
- Use pytest fixtures for common setup

### Test Naming Convention

```python
def test_<function_name>_<scenario>_<expected_result>():
    """Test description."""
```

### Running Tests

```bash
# Run all tests
pytest

# Run with coverage
pytest --cov=src/theme_manager --cov-report=html

# Run specific test file
pytest tests/test_theme_manager.py

# Run with verbose output
pytest -v
```

## Code Quality Tools

### Formatting and Linting

Before committing, ensure code passes all quality checks:

```bash
# Format code (required)
black src/ tests/

# Lint code (required)
flake8 src/ tests/ --max-line-length=100 --extend-ignore=E203,W503

# Static analysis (recommended)
pylint src/theme_manager/ --fail-under=8.0

# Type checking (recommended)
mypy src/theme_manager/ --ignore-missing-imports

# Security scanning (required for production)
bandit -r src/
```

## Security Considerations

Security is a top priority. Always consider:

### Input Validation

- Validate all user inputs and file paths
- Check file types and extensions against allowlists
- Verify file sizes to prevent resource exhaustion

### Path Security

- Prevent path traversal attacks (e.g., `../` patterns)
- Use Path objects and resolve() to normalize paths
- Never trust user-provided paths without validation

### Code Patterns to Avoid

```python
# ❌ Avoid these patterns
eval(user_input)
exec(user_code)
innerHTML = user_content
os.system(user_command)

# ✅ Use safe alternatives
ast.literal_eval(user_input)  # for simple data structures
json.loads(user_input)        # for JSON data
textContent = user_content    # in JavaScript
subprocess.run([cmd], shell=False)  # with list of args
```

### Security Scanning

The SecurityScanner class detects dangerous patterns:
- JavaScript eval() and Function() usage
- Code injection attempts
- Path traversal attempts
- External script loading
- SQL injection patterns

## Commit Message Convention

Use conventional commit format with these prefixes:

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `test:` Adding or updating tests
- `refactor:` Code refactoring without behavior change
- `perf:` Performance improvements
- `chore:` Maintenance tasks (dependencies, config, etc.)
- `security:` Security-related changes

### Examples

```
feat: Add validation for custom theme metadata
fix: Correct path traversal detection in security scanner
docs: Update deployment guide with Kubernetes examples
test: Add edge case tests for zip bomb detection
refactor: Simplify validation logic in ThemeValidator
```

## Project Structure

```
YouTuneAi_3000_Studios/
├── src/theme_manager/        # Main package source
│   ├── __init__.py
│   ├── cli.py               # CLI commands
│   ├── theme_manager.py     # Core theme management
│   ├── validator.py         # Theme validation
│   └── security.py          # Security scanning
├── tests/                   # Test suite
│   ├── test_cli.py
│   ├── test_theme_manager.py
│   ├── test_validator.py
│   └── test_security.py
├── docs/                    # Documentation
├── config/                  # Configuration files
└── themes/                  # Theme storage directory
```

## Development Workflow

1. **Create a feature branch**: `git checkout -b feature/your-feature-name`
2. **Write code**: Follow style guide and add docstrings
3. **Write tests**: Ensure >80% coverage
4. **Run quality checks**: black, flake8, pylint, mypy
5. **Test locally**: pytest with coverage
6. **Commit**: Use conventional commit format
7. **Push and create PR**: Link related issues

## Core Components

### ThemeManager

Main class for theme package operations:
- `create_theme_package()`: Create zip packages with metadata
- `unpack_theme()`: Extract and validate theme packages
- `list_themes()`: List available theme packages
- Includes integrity verification with SHA256 hashing

### ThemeValidator

Validates theme structure and content:
- File type validation (allowlist-based)
- Size limits (max file size, max total size)
- Directory structure validation
- Metadata validation (theme.json)

### SecurityScanner

Scans for security vulnerabilities:
- Pattern-based malware detection
- Zip bomb protection
- Path traversal detection
- Suspicious file extension checking

### CLI

Command-line interface (Click-based):
- `create`: Create theme packages
- `unpack`: Extract theme packages
- `list`: List available themes
- `validate`: Validate theme structure
- `scan`: Security scan themes

## Dependencies

### Core Dependencies
- `pyyaml`: YAML configuration parsing
- `python-magic`: File type detection
- `cryptography`: Hash generation and verification
- `structlog`: Structured logging
- `click`: CLI framework

### Development Dependencies
- `pytest`: Testing framework
- `black`: Code formatting
- `flake8`: Linting
- `pylint`: Static analysis
- `mypy`: Type checking
- `bandit`: Security scanning

## Environment and Configuration

### Environment Variables

```bash
LOG_LEVEL=INFO
LOG_FORMAT=json
THEMES_DIR=/app/themes
MAX_THEME_SIZE=524288000  # 500 MB
MAX_FILE_SIZE=10485760    # 10 MB
ENABLE_SECURITY_SCAN=true
```

### Configuration Files

- `config/config.yaml`: Main configuration
- `.env`: Environment-specific settings
- `pytest.ini`: Test configuration
- `setup.py`: Package metadata

## Documentation

Update documentation when making changes:
- **Code changes**: Update docstrings and type hints
- **New features**: Add to README.md and relevant docs/
- **API changes**: Update API reference documentation
- **Breaking changes**: Document in CHANGELOG.md

## CI/CD Pipeline

The repository uses GitHub Actions for:
- **Linting**: black, flake8, pylint, mypy
- **Testing**: pytest with coverage reporting
- **Security**: bandit, safety, CodeQL, Trivy
- **Multi-platform**: Linux, macOS, Windows
- **Multi-version**: Python 3.8-3.12

All checks must pass before merging.

## Additional Resources

- [Full Contributing Guidelines](../CONTRIBUTING.md)
- [Quick Start Guide](../QUICKSTART.md)
- [Security Guidelines](../docs/security.md)
- [Deployment Guide](../docs/deployment.md)
- [README](../README.md)

## Common Patterns

### Error Handling

```python
try:
    # Operation
    result = risky_operation()
except SpecificException as e:
    logger.error("Operation failed", error=str(e))
    raise ValueError(f"Failed to complete operation: {e}")
```

### Logging

```python
import structlog

logger = structlog.get_logger(__name__)

# Use structured logging
logger.info("Theme created", theme_name=name, version=version, size=size)
logger.warning("Validation warning", file=file_path, issue=issue_desc)
logger.error("Security issue found", severity="high", pattern=pattern)
```

### Path Handling

```python
from pathlib import Path

# Use Path objects
source_path = Path(source_dir).resolve()
if not source_path.is_dir():
    raise ValueError(f"Source directory does not exist: {source_path}")

# Check for path traversal
if ".." in str(relative_path):
    raise ValueError("Path traversal detected")
```

## Performance Considerations

- Use generators for large file iterations
- Close file handles explicitly or use context managers
- Implement proper cleanup in exception handlers
- Monitor memory usage for large theme packages
- Use streaming for zip operations when possible

## Accessibility and Usability

- Provide clear error messages with actionable guidance
- Use color-coded output in CLI (via Click)
- Include verbose mode for debugging
- Support JSON output for automation
- Add progress indicators for long operations
