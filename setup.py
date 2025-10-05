"""Setup configuration for YouTuneAi Theme Manager."""
from setuptools import setup, find_packages

with open("README.md", "r", encoding="utf-8") as fh:
    long_description = fh.read()

setup(
    name="youtune-theme-manager",
    version="1.0.0",
    author="3000Studios",
    author_email="",
    description="Fully automated, secure, production-ready zip file theme system",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/3000Studios/YouTuneAi_3000_Studios",
    packages=find_packages(where="src"),
    package_dir={"": "src"},
    classifiers=[
        "Development Status :: 5 - Production/Stable",
        "Intended Audience :: Developers",
        "Topic :: Software Development :: Libraries",
        "License :: OSI Approved :: MIT License",
        "Programming Language :: Python :: 3",
        "Programming Language :: Python :: 3.8",
        "Programming Language :: Python :: 3.9",
        "Programming Language :: Python :: 3.10",
        "Programming Language :: Python :: 3.11",
        "Programming Language :: Python :: 3.12",
    ],
    python_requires=">=3.8",
    install_requires=[
        "pyyaml>=6.0.1",
        "python-magic>=0.4.27",
        "cryptography>=42.0.0",
        "python-dotenv>=1.0.0",
        "structlog>=23.2.0",
        "click>=8.1.7",
    ],
    entry_points={
        "console_scripts": [
            "theme-manager=theme_manager.cli:main",
        ],
    },
)
