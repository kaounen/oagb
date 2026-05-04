import os

# 1. Read bastonario-ordem.php (to get header and footer)
with open('bastonario-ordem.php', 'r', encoding='utf-8') as f:
    basto = f.read()

# 2. Read old_agenda_head.php (to get PHP logic and agenda loop)
with open('old_agenda_head.php', 'r', encoding='utf-8') as f:
    old_agenda = f.read()

# 3. Extract the parts we need from old_agenda
# a) PHP top block
php_end = old_agenda.find('?>\n<!DOCTYPE html>')
php_logic = old_agenda[:php_end+2]

# Modify php_logic to add the fallback for $eventos if empty
fallback_php = """
    // Se não houver eventos, mostrar os últimos carregados
    if (empty($eventos) && $tipo == 'todos' && $mes == 0 && $ano == 0 && empty($busca)) {
        $stmt = $pdo->prepare("
            SELECT * FROM agenda 
            WHERE ativo = 1 
            ORDER BY id DESC 
            LIMIT $por_pagina OFFSET $offset
        ");
        $stmt->execute();
        $eventos = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE ativo = 1");
        $stmt->execute();
        $total_eventos = $stmt->fetch()->total;
        $total_paginas = ceil($total_eventos / $por_pagina);
    }
"""
php_logic = php_logic.replace('$eventos = $stmt->fetchAll();', '$eventos = $stmt->fetchAll();\n' + fallback_php)

# b) Agenda Start ... Agenda End
agenda_start_idx = old_agenda.find('<!-- Agenda Start -->')
agenda_end_idx = old_agenda.find('<!-- Agenda End -->') + len('<!-- Agenda End -->')
agenda_content = old_agenda[agenda_start_idx:agenda_end_idx]

# Modify agenda_content:
# - Remove sidebar
# - col-lg-8 to col-lg-12
agenda_content = agenda_content.replace('<div class="col-lg-8">', '<div class="col-lg-12">')
sidebar_start = agenda_content.find('<!-- Sidebar -->')
agenda_content = agenda_content[:sidebar_start] + '            </div>\n        </div>\n    </div>\n    <!-- Agenda End -->'

# - Improve filter form
agenda_content = agenda_content.replace(
    '<div class="filter-section">',
    '<div class="filter-section bg-white p-4 rounded-4 shadow-sm mb-5 border" style="border-color: #f0ece4 !important;">'
)
agenda_content = agenda_content.replace('<form method="GET" action="agenda.php" class="row g-3">', '<form method="GET" action="agenda.php" class="row g-3 align-items-end">')
agenda_content = agenda_content.replace('<label class="form-label">', '<label class="form-label fw-bold text-muted small text-uppercase">')

# 4. Construct agenda.php
# Split bastonario to get header and footer
basto_header_end = basto.find('<!-- Content Area -->')
basto_footer_start = basto.find('<?php include \'includes/banner-inscricao.php\'; ?>')

# The header string is everything from the first <!DOCTYPE html> down to basto_header_end
basto_header = basto[basto.find('<!DOCTYPE html>'):basto_header_end]
basto_footer = basto[basto_footer_start:]

# Change title and breadcrumbs in basto_header
basto_header = basto_header.replace('$page_title = "O Bastonário";', '$page_title = "Agenda de Eventos";')
basto_header = basto_header.replace('$meta_description = "Conheça o Bastonário da Ordem dos Advogados da Guiné-Bissau.";', '$meta_description = "Agenda de eventos, formações, congressos e atividades da Ordem dos Advogados da Guiné-Bissau";')

basto_header = basto_header.replace('<a href="a-ordem-dos-advogados.php">A Ordem</a>', '<a href="#">Comunicação</a>')
basto_header = basto_header.replace('<span class="bc-active">O Bastonário</span>', '<span class="bc-active"><?php echo $page_title; ?></span>')

basto_header = basto_header.replace('<link href="css/style.css" rel="stylesheet">', '<link href="css/style.css" rel="stylesheet">\n    <link href="css/index-styles.css" rel="stylesheet">')

new_agenda = php_logic + '\n' + basto_header + '\n' + agenda_content + '\n\n' + basto_footer

# 5. Fix dropdown and border-bottom in BOTH files
dropdown_old = 'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);"'
dropdown_new = 'style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;"'

new_agenda = new_agenda.replace(dropdown_old, dropdown_new)
new_agenda = new_agenda.replace('border-bottom: 1px solid #e0dcd2;', '')

basto = basto.replace(dropdown_old, dropdown_new)
basto = basto.replace('border-bottom: 1px solid #e0dcd2;', '')

with open('agenda.php', 'w', encoding='utf-8') as f:
    f.write(new_agenda)

with open('bastonario-ordem.php', 'w', encoding='utf-8') as f:
    f.write(basto)

print("Done building agenda.php!")
