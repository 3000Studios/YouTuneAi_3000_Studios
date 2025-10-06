"""YouTuneAi Theme Manager - Secure, automated theme packaging system."""

__version__ = "1.0.0"
__author__ = "3000Studios"

from .theme_manager import ThemeManager
from .validator import ThemeValidator
from .security import SecurityScanner

__all__ = ["ThemeManager", "ThemeValidator", "SecurityScanner"]
