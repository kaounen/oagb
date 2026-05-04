import sys

file_path = r'c:\xampp\htdocs\oagb\noticias.php'

with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Fix encoding
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
    'GuinÃ©-Bissau': 'Guiné-Bissau'
}

for old, new in replacements.items():
    content = content.replace(old, new)

# Fix residue if any (though Python replace should handle it if it matches)
content = content.replace('gn-items-center">\n', '')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Fix completed.")
