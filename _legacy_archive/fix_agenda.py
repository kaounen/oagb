import re
import os

with open('old_agenda_head.php', 'r', encoding='utf-8') as f:
    old_agenda = f.read()

php_match = re.search(r'(?s)^<\?php.*?\n\}', old_agenda)
php_logic = php_match.group(0)

# Add fallback for eventos_destaque
fallback = """
    if (empty($eventos_destaque)) {
        $stmt = $pdo->prepare("SELECT * FROM agenda WHERE ativo = 1 ORDER BY data_criacao DESC LIMIT 1");
        $stmt->execute();
        $eventos_destaque = $stmt->fetchAll();
    }
"""
php_logic = php_logic.replace('$eventos_destaque = $stmt->fetchAll();', '$eventos_destaque = $stmt->fetchAll();' + fallback)

with open('agenda.php', 'r', encoding='utf-8') as f:
    agenda = f.read()

# Replace top PHP block
agenda = re.sub(r'(?s)^<\?php.*?\n\} catch \(Exception \$e\) \{.*?\n\}', php_logic.replace('\\', '\\\\'), agenda)

# Dropdown positioning
agenda = agenda.replace(
    'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);"',
    'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;"'
)

# Remove border-bottom
agenda = agenda.replace('border-bottom: 1px solid #e0dcd2;', '')

# Full width event list
agenda = agenda.replace('<div class="col-lg-8">', '<div class="col-lg-12">')

# Remove sidebar
agenda = re.sub(r'(?s)<!-- Sidebar -->.*?<!-- Agenda End -->', '            </div>\n        </div>\n    </div>\n    <!-- Agenda End -->', agenda)

# Improve filter-section
agenda = agenda.replace(
    '<div class="filter-section">',
    '<div class="filter-section bg-white p-4 rounded-4 shadow-sm mb-5 border" style="border-color: #f0ece4 !important;">'
)
agenda = agenda.replace('<form method="GET" action="agenda.php" class="row g-3">', '<form method="GET" action="agenda.php" class="row g-3 align-items-end">')
agenda = agenda.replace('<label class="form-label">', '<label class="form-label fw-bold text-muted small text-uppercase">')

with open('agenda.php', 'w', encoding='utf-8') as f:
    f.write(agenda)

with open('bastonario-ordem.php', 'r', encoding='utf-8') as f:
    basto = f.read()

basto = basto.replace(
    'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);"',
    'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;"'
)
basto = basto.replace('border-bottom: 1px solid #e0dcd2;', '')

with open('bastonario-ordem.php', 'w', encoding='utf-8') as f:
    f.write(basto)

print("All fixes applied successfully!")
