# Quick Start Guide

Get started with YouTuneAi Theme Manager in under 5 minutes!

## Installation

### Option 1: Local Installation (Recommended for Development)

```bash
# Clone the repository
git clone https://github.com/3000Studios/YouTuneAi_3000_Studios.git
cd YouTuneAi_3000_Studios

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Install the package
pip install -e .

# Verify installation
theme-manager --version
```

### Option 2: Docker (Recommended for Production)

```bash
# Clone the repository
git clone https://github.com/3000Studios/YouTuneAi_3000_Studios.git
cd YouTuneAi_3000_Studios

# Build and run with Docker Compose
docker-compose up -d

# Use the CLI
docker-compose exec theme-manager theme-manager --help
```

## Creating Your First Theme

### Step 1: Create Theme Directory

```bash
mkdir my-theme
cd my-theme
```

### Step 2: Create theme.json

```bash
cat > theme.json << 'EOF'
{
  "name": "My Awesome Theme",
  "version": "1.0.0",
  "description": "My first theme",
  "author": "Your Name",
  "license": "MIT"
}
EOF
```

### Step 3: Add Theme Files

```bash
# Create a simple stylesheet
cat > style.css << 'EOF'
body {
  background-color: #1a1a2e;
  color: #ffffff;
  font-family: Arial, sans-serif;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
EOF

# Create a simple script
cat > script.js << 'EOF'
document.addEventListener('DOMContentLoaded', function() {
  console.log('My Awesome Theme loaded!');
});
EOF
```

### Step 4: Validate Your Theme

```bash
cd ..
theme-manager validate my-theme
```

Expected output:
```
Theme Validation Report
==================================================
Status: ✓ VALID
Files: 3
Total Size: XXX bytes
```

### Step 5: Security Scan

```bash
theme-manager scan my-theme
```

Expected output:
```
Security Scan Report
==================================================
Total Issues: 0
High Severity: 0
Medium Severity: 0
Low Severity: 0

✓ No security issues found!
```

### Step 6: Create Package

```bash
theme-manager create my-theme my-awesome-theme --version 1.0.0
```

Expected output:
```
✓ Theme package created: themes/my-awesome-theme-1.0.0.zip
✓ Integrity hash: themes/my-awesome-theme-1.0.0.zip.sha256
```

### Step 7: List Themes

```bash
theme-manager list
```

Expected output:
```
Found 1 theme(s):

  • My Awesome Theme v1.0.0
    File: themes/my-awesome-theme-1.0.0.zip
    Size: XXX bytes
    Verified: Yes
```

## Unpacking a Theme

```bash
theme-manager unpack themes/my-awesome-theme-1.0.0.zip
```

The theme will be unpacked to `themes/unpacked/my-awesome-theme-1.0.0/`

## Common Commands

### Get Help
```bash
theme-manager --help
theme-manager create --help
```

### Create with Metadata
```bash
theme-manager create ./my-theme my-theme \
  --version 2.0.0 \
  --metadata '{"author": "John Doe", "license": "MIT"}'
```

### Validate and Get JSON Output
```bash
theme-manager validate ./my-theme --json
```

### Scan and Get JSON Output
```bash
theme-manager scan ./my-theme --json
```

### List Themes as JSON
```bash
theme-manager list --json
```

### Unpack Without Verification (Not Recommended)
```bash
theme-manager unpack themes/my-theme-1.0.0.zip --no-verify
```

## Directory Structure

After creating a theme, your directory will look like this:

```
.
├── my-theme/                 # Your source theme directory
│   ├── theme.json           # Required metadata file
│   ├── style.css            # Theme stylesheet
│   └── script.js            # Theme scripts
└── themes/                   # Packages directory
    ├── my-awesome-theme-1.0.0.zip        # Theme package
    ├── my-awesome-theme-1.0.0.zip.sha256 # Integrity hash
    └── unpacked/            # Unpacked themes
```

## Best Practices

1. **Always validate** before creating packages
   ```bash
   theme-manager validate ./my-theme
   ```

2. **Always scan** for security issues
   ```bash
   theme-manager scan ./my-theme
   ```

3. **Use version control** for your themes
   ```bash
   git init my-theme
   cd my-theme
   git add .
   git commit -m "Initial theme"
   ```

4. **Verify integrity** when unpacking
   ```bash
   theme-manager unpack themes/theme.zip  # Verifies by default
   ```

5. **Keep themes small** - Remove unnecessary files
   - Max file size: 10 MB per file
   - Max package size: 500 MB

## Allowed File Types

Your theme can include:
- **Stylesheets**: `.css`
- **Scripts**: `.js`
- **Markup**: `.html`, `.htm`
- **Images**: `.png`, `.jpg`, `.jpeg`, `.gif`, `.svg`, `.ico`
- **Fonts**: `.woff`, `.woff2`, `.ttf`, `.eot`
- **Data**: `.json`
- **Documentation**: `.md`, `.txt`

## Security Tips

❌ **Avoid these patterns** (will be flagged by security scanner):
- `eval()` in JavaScript
- `innerHTML =` assignments
- External script loading
- Path traversal (`../`)

✅ **Use safe alternatives**:
- Use `textContent` instead of `innerHTML`
- Load scripts locally
- Use relative paths without `../`

## Troubleshooting

### Command not found: theme-manager
```bash
# Ensure you're in the virtual environment
source venv/bin/activate

# Reinstall the package
pip install -e .
```

### Theme validation fails
```bash
# Check that theme.json exists and is valid JSON
cat my-theme/theme.json | python -m json.tool

# Check required fields
cat my-theme/theme.json | grep -E "name|version"
```

### Security scan finds issues
```bash
# Review the detailed report
theme-manager scan my-theme

# Fix reported issues and scan again
```

### Package too large
```bash
# Check theme size
du -sh my-theme

# Remove unnecessary files
find my-theme -name "*.tmp" -delete
find my-theme -name ".DS_Store" -delete
```

## Next Steps

- Read the [full documentation](docs/README.md)
- Review [security guidelines](docs/security.md)
- Check [deployment guide](docs/deployment.md) for production
- Explore [contributing guidelines](CONTRIBUTING.md) to contribute

## Getting Help

- **Issues**: [GitHub Issues](https://github.com/3000Studios/YouTuneAi_3000_Studios/issues)
- **Discussions**: [GitHub Discussions](https://github.com/3000Studios/YouTuneAi_3000_Studios/discussions)
- **Documentation**: [docs/](docs/)

## Example: Full Workflow

```bash
# 1. Create theme directory
mkdir cool-theme && cd cool-theme

# 2. Create theme.json
echo '{"name":"Cool Theme","version":"1.0.0"}' > theme.json

# 3. Add files
echo "body { background: #000; }" > style.css
echo "console.log('loaded');" > script.js

# 4. Go back to project root
cd ..

# 5. Validate
theme-manager validate cool-theme

# 6. Scan
theme-manager scan cool-theme

# 7. Create package
theme-manager create cool-theme cool-theme --version 1.0.0

# 8. List all themes
theme-manager list

# 9. Unpack (if needed)
theme-manager unpack themes/cool-theme-1.0.0.zip
```

That's it! You now have a secure, validated theme package ready to use! 🎉
