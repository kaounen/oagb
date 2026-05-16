import re

file_path = r'c:\xampp\htdocs\oagb\noticias.php'

with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Fix the broken Area Reservada line specifically
broken_area_reservada = r'<a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex ali\s+<i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Ã rea Reservada'
content = re.sub(broken_area_reservada, '<a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center">\n                            <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada', content)

# General encoding fixes
replacements = {
    'InÃ­cio': 'Início',
    'ComunicaÃ§Ã£o': 'Comunicação',
    'PortuguÃªs': 'Português',
    'FranÃ§ais': 'Français',
    'EspaÃ±ol': 'Español',
    'ðŸ‡µðŸ‡¹': '🇵🇹',
    'ðŸ‡ºðŸ‡¸': '🇺🇸',
    'ðŸ‡«ðŸ‡·': '🇫🇷',
    'ðŸ‡ªðŸ‡¸': '🇪🇸',
    'ðŸ‡¸ðŸ‡¦': '🇸🇦',
    'ðŸ‡¨ðŸ‡³': '🇨🇳',
    'ðŸ‡·ðŸ‡º': '🇷🇺',
    'Ð ÑƒÑ Ñ ÐºÐ¸Ð¹': 'Русский',
    'Ã rea Reservada': 'Área Reservada',
    'GuinÃ©-Bissau': 'Guiné-Bissau',
    'Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©': 'العربية',
    'ä¸­æ–‡': '中文'
}

for old, new in replacements.items():
    content = content.replace(old, new)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Aggressive fix completed.")
