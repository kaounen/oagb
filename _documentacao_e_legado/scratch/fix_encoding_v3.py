file_path = r'c:\xampp\htdocs\oagb\noticias.php'

with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
    lines = f.readlines()

new_lines = []
skip = False
for i, line in enumerate(lines):
    # Check for the start of the mobile header buttons block
    if '<div class="col-12 d-flex justify-content-center align-items-center gap-3">' in line:
        new_lines.append(line)
        new_lines.append('                        <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">\n')
        new_lines.append('                             <i class="fa fa-search" style="font-size: 1rem;"></i>\n')
        new_lines.append('                        </button>\n')
        new_lines.append('                        <div class="dropdown">\n')
        new_lines.append('                            <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static">\n')
        new_lines.append('                                <i class="fa fa-globe" style="font-size: 1rem;"></i>\n')
        new_lines.append('                            </button>\n')
        new_lines.append('                            <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'pt\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'en\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'fr\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'es\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'ar\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'zh-CN\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>\n')
        new_lines.append('                                <a href="#" onclick="changeLanguage(\'ru\'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>\n')
        new_lines.append('                            </div>\n')
        new_lines.append('                        </div>\n')
        new_lines.append('                        <a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center">\n')
        new_lines.append('                            <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada\n')
        new_lines.append('                        </a>\n')
        skip = True
        continue
    
    if skip:
        if '</div>' in line and i > 0 and '</div>' in lines[i-1] and '</div>' in lines[i-2]: # End of the block
            # This is a bit weak, let's use a better marker
            pass
        # Actually, skip until we see the breadcrumbs marker
        if '<!-- Breadcrumbs -->' in line or '<!-- Navbar -->' in line:
            skip = False
        else:
            continue
            
    new_lines.append(line)

# Final encoding cleanup for other parts of the file
content = "".join(new_lines)
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

print("Block replace fix completed.")
