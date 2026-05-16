import re
import os

with open('bastonario-ordem.php', 'r', encoding='utf-8') as f:
    code = f.read()

# 1. Change Title and Meta
code = code.replace('$page_title = "O Bastonário";', '$page_title = "Agenda de Eventos";')
code = code.replace('$meta_description = "Conheça o Bastonário da Ordem dos Advogados da Guiné-Bissau.";', '$meta_description = "Agenda de eventos, formações, congressos e atividades da Ordem dos Advogados da Guiné-Bissau";')

# 2. Change breadcrumbs
code = code.replace('<a href="a-ordem-dos-advogados.php">A Ordem</a>', '<a href="#">Comunicação</a>')
code = code.replace('<span class="bc-active">O Bastonário</span>', '<span class="bc-active"><?php echo $page_title; ?></span>')

# 3. Add index-styles.css
code = code.replace('<link href="css/style.css" rel="stylesheet">', '<link href="css/style.css" rel="stylesheet">\n    <link href="css/index-styles.css" rel="stylesheet">')

# 4. We need to inject the PHP logic at the top of agenda.php for events
with open('agenda.php', 'r', encoding='utf-8') as f:
    old_agenda = f.read()

# Extract PHP logic block at the very top (lines 1-95)
php_logic_match = re.search(r'(?s)^<\?php.*?\n\}', old_agenda)
if php_logic_match:
    php_logic = php_logic_match.group(0)
    # Replace the small PHP block at top of bastonario
    code = re.sub(r'(?s)^<\?php.*?\n\} catch \(Exception \$e\) \{.*?\n\}', php_logic, code)

# 5. Extract the agenda filters and list from old_agenda
agenda_content_match = re.search(r'(?s)<!-- Agenda Start -->.*?<!-- Agenda End -->', old_agenda)
if agenda_content_match:
    agenda_content = agenda_content_match.group(0)
    # Replace bastonario Content Area
    code = re.sub(r'(?s)<!-- Content Area -->.*?</section>', agenda_content, code)

with open('agenda.php', 'w', encoding='utf-8') as f:
    f.write(code)

print("Rewrote agenda.php based strictly on bastonario-ordem.php!")
