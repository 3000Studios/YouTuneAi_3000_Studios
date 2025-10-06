# Contributing to YouTuneAi Theme Manager

Thank you for your interest in contributing! This document provides guidelines and instructions for contributing to the project.

## Code of Conduct

We are committed to providing a welcoming and inclusive environment. Please:
- Be respectful and considerate
- Use welcoming and inclusive language
- Accept constructive criticism gracefully
- Focus on what's best for the community

## Getting Started

### Prerequisites

- Python 3.8 or higher
- Git
- Docker (optional, for testing containers)

### Setup Development Environment

1. **Fork the repository**
   ```bash
   # Click 'Fork' on GitHub, then clone your fork
   git clone https://github.com/YOUR-USERNAME/YouTuneAi_3000_Studios.git
   cd YouTuneAi_3000_Studios
   ```

2. **Create a virtual environment**
   ```bash
   python -m venv venv
   source venv/bin/activate  # Windows: venv\Scripts\activate
   ```

3. **Install dependencies**
   ```bash
   pip install -r requirements.txt
   pip install -e .
   ```

4. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Development Workflow

### Making Changes

1. **Write code**
   - Follow PEP 8 style guidelines
   - Add docstrings to functions and classes
   - Keep functions focused and small

2. **Write tests**
   - Add tests for new features
   - Ensure existing tests pass
   - Aim for >80% code coverage

3. **Run tests**
   ```bash
   pytest
   pytest --cov=src/theme_manager
   ```

4. **Format code**
   ```bash
   black src/ tests/
   ```

5. **Lint code**
   ```bash
   flake8 src/ tests/
   pylint src/theme_manager/
   ```

6. **Type check**
   ```bash
   mypy src/theme_manager/
   ```

### Commit Messages

Use clear, descriptive commit messages:

```
feat: Add new theme validation rule
fix: Correct security scanner false positive
docs: Update deployment guide
test: Add tests for theme unpacking
refactor: Simplify validation logic
```

Prefixes:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `test:` Adding or updating tests
- `refactor:` Code refactoring
- `perf:` Performance improvements
- `chore:` Maintenance tasks

### Submitting Pull Requests

1. **Update your branch**
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

2. **Push changes**
   ```bash
   git push origin feature/your-feature-name
   ```

3. **Create Pull Request**
   - Go to GitHub and create a PR
   - Fill out the PR template
   - Link related issues
   - Wait for CI checks to pass

4. **Address review comments**
   - Respond to feedback promptly
   - Make requested changes
   - Update the PR

## Code Standards

### Python Style Guide

Follow PEP 8 with these specifics:
- Line length: 100 characters
- Use type hints for function parameters and returns
- Use f-strings for string formatting
- Prefer list comprehensions over map/filter

### Documentation

- Add docstrings to all public functions and classes
- Use Google-style docstrings
- Include examples in docstrings when helpful
- Update documentation for API changes

Example:
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

### Testing

- Write tests for all new features
- Use descriptive test names
- Follow AAA pattern: Arrange, Act, Assert
- Use fixtures for common setup

Example:
```python
def test_create_theme_package_with_valid_theme(
    theme_manager, 
    sample_theme
):
    """Test creating a package from a valid theme."""
    # Arrange
    theme_name = "test-theme"
    version = "1.0.0"
    
    # Act
    package_path = theme_manager.create_theme_package(
        str(sample_theme),
        theme_name,
        version
    )
    
    # Assert
    assert package_path.exists()
    assert package_path.suffix == ".zip"
```

## Security

### Security Considerations

When contributing, consider:
- Input validation
- Path traversal vulnerabilities
- Code injection risks
- File permission issues
- Denial of service vectors

### Reporting Security Issues

Do NOT open public issues for security vulnerabilities.

Instead:
1. Email: security@3000studios.com
2. Provide detailed description
3. Include steps to reproduce
4. Allow time for patching

## Review Process

### What Reviewers Look For

- Code quality and style
- Test coverage
- Documentation updates
- Security implications
- Performance impact
- Breaking changes

### Review Timeline

- Initial review: Within 3 business days
- Follow-up reviews: Within 1-2 business days
- Merge: After approval and passing CI

## Types of Contributions

### Bug Fixes

1. Check if issue already exists
2. Create an issue if not
3. Reference issue in PR
4. Add test to prevent regression

### New Features

1. Discuss in an issue first
2. Wait for maintainer approval
3. Follow feature proposal template
4. Document the feature

### Documentation

- Fix typos and errors
- Improve clarity
- Add examples
- Update outdated information

### Tests

- Increase coverage
- Add edge case tests
- Improve test organization
- Add integration tests

## Release Process

Maintainers handle releases:

1. Version bump
2. Update CHANGELOG
3. Create release notes
4. Tag release
5. Deploy to PyPI (future)
6. Update documentation

## Getting Help

- **Questions**: Open a GitHub Discussion
- **Bugs**: Open a GitHub Issue
- **Chat**: Join our Discord (if available)
- **Email**: support@3000studios.com

## Recognition

Contributors are recognized in:
- README contributors section
- Release notes
- Project documentation

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to YouTuneAi Theme Manager!
