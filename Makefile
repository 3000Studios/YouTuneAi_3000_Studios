.PHONY: help install test lint format clean docker-build docker-run deploy docs

help:  ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-20s %s\n", $$1, $$2}'

install:  ## Install dependencies
	pip install -r requirements.txt
	pip install -e .

test:  ## Run tests
	pytest

test-verbose:  ## Run tests with verbose output
	pytest -v

test-coverage:  ## Run tests with coverage report
	pytest --cov=src/theme_manager --cov-report=html --cov-report=term

lint:  ## Run linters
	black --check src/ tests/
	flake8 src/ tests/ --max-line-length=100 --extend-ignore=E203,W503
	pylint src/theme_manager/ --fail-under=8.0
	mypy src/theme_manager/ --ignore-missing-imports

format:  ## Format code
	black src/ tests/

security:  ## Run security checks
	bandit -r src/ -f json -o bandit-report.json
	safety check

clean:  ## Clean build artifacts
	rm -rf build/
	rm -rf dist/
	rm -rf *.egg-info
	rm -rf .pytest_cache/
	rm -rf .coverage
	rm -rf htmlcov/
	rm -rf bandit-report.json
	find . -type d -name __pycache__ -exec rm -rf {} +
	find . -type f -name "*.pyc" -delete

build:  ## Build package
	python -m build

docker-build:  ## Build Docker image
	docker build -t youtune-theme-manager:latest .

docker-run:  ## Run Docker container
	docker run -v $$(pwd)/themes:/app/themes youtune-theme-manager:latest

docker-compose-up:  ## Start services with Docker Compose
	docker-compose up -d

docker-compose-down:  ## Stop services with Docker Compose
	docker-compose down

docs:  ## Build documentation
	mkdocs build

docs-serve:  ## Serve documentation locally
	mkdocs serve

all: clean install lint test build  ## Run all checks and build
