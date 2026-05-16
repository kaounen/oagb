import re
import os

with open('index.php', 'r', encoding='utf-8') as f:
    idx = f.read()

loop_match = re.search(r'(?s)<\?php foreach \(\$proximos_eventos as \$evento\).*?<\?php endforeach; \?>', idx)
loop_content = loop_match.group(0)

# Replace proximos_eventos with eventos
loop_content = loop_content.replace('proximos_eventos as $evento', 'eventos as $evento')
# Change col-lg-10 col-xl-9 col-12 to col-12 mb-3
loop_content = loop_content.replace('col-lg-10 col-xl-9 col-12 mb-1', 'col-12 mb-3')

with open('agenda.php', 'r', encoding='utf-8') as f:
    agenda = f.read()

# I need to insert loop_content where I put `<div class="row g-0 justify-content-center">`
agenda = agenda.replace('<div class="row g-0 justify-content-center">\n                \n                </div>', '<div class="row g-0 justify-content-center">\n                ' + loop_content + '\n                </div>')

with open('agenda.php', 'w', encoding='utf-8') as f:
    f.write(agenda)
