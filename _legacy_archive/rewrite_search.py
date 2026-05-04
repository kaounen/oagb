import re

with open('agenda.php', 'r', encoding='utf-8') as f:
    agenda = f.read()

filter_regex = r'(?s)<!-- Filtros -->.*?</div>\s*</form>\s*</div>'
premium_search_html = """<!-- Filtros -->
            <div class="mb-5">
                <form method="GET" action="agenda.php">
                    <div class="premium-search-wrapper">
                        <!-- Tipo -->
                        <div class="premium-search-item" style="flex: 2;">
                            <label>Tipo de Evento</label>
                            <select name="tipo" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="todos">Todos os Tipos</option>
                                <option value="congresso" <?php echo $tipo == 'congresso' ? 'selected' : ''; ?>>Congresso</option>
                                <option value="conferencia" <?php echo $tipo == 'conferencia' ? 'selected' : ''; ?>>Conferência</option>
                                <option value="formacao" <?php echo $tipo == 'formacao' ? 'selected' : ''; ?>>Formação</option>
                                <option value="reuniao" <?php echo $tipo == 'reuniao' ? 'selected' : ''; ?>>Reunião</option>
                                <option value="workshop" <?php echo $tipo == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                                <option value="palestra" <?php echo $tipo == 'palestra' ? 'selected' : ''; ?>>Palestra</option>
                                <option value="outros" <?php echo $tipo == 'outros' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Mês -->
                        <div class="premium-search-item" style="flex: 1.5;">
                            <label>Mês</label>
                            <select name="mes" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="0">Qualquer</option>
                                <?php 
                                $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                                         'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                foreach ($meses as $i => $nome_mes): 
                                ?>
                                <option value="<?php echo $i + 1; ?>" <?php echo $mes == ($i + 1) ? 'selected' : ''; ?>>
                                    <?php echo $nome_mes; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Ano -->
                        <div class="premium-search-item" style="flex: 1;">
                            <label>Ano</label>
                            <select name="ano" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="0">Todos</option>
                                <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo $ano == $y ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Pesquisa -->
                        <div class="premium-search-item" style="flex: 2;">
                            <label>Palavra-chave</label>
                            <input type="text" name="busca" class="form-control form-control-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; box-shadow: none;" placeholder="O que procura?" value="<?php echo htmlspecialchars($busca); ?>">
                        </div>
                        
                        <!-- Botão -->
                        <button type="submit" class="premium-search-btn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>"""

agenda = re.sub(filter_regex, premium_search_html, agenda)

style_idx = agenda.find('</style>')
premium_css = """
        /* === PREMIUM SEARCH BAR === */
        .premium-search-wrapper {
            background: #fff;
            border-radius: 50px;
            padding: 10px 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #f0ece4;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .premium-search-wrapper:hover {
            box-shadow: 0 15px 40px rgba(177, 162, 118, 0.15);
        }
        .premium-search-item {
            position: relative;
            padding: 5px 20px;
        }
        .premium-search-divider {
            width: 1px;
            height: 40px;
            background: #e0dcd2;
        }
        .premium-search-item label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 2px;
            display: block;
        }
        .premium-search-item input::placeholder {
            color: #ccc;
            font-weight: 400;
        }
        .premium-search-btn {
            background: var(--primary-maroon);
            color: #fff;
            border-radius: 50px;
            height: 50px;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: .3s;
            flex-shrink: 0;
            margin-left: 10px;
        }
        .premium-search-btn:hover {
            background: #3a1519;
            transform: scale(1.05);
            color: #fff;
        }
        @media (max-width: 991px) {
            .premium-search-wrapper {
                flex-direction: column;
                border-radius: 20px;
                padding: 20px;
                align-items: stretch;
            }
            .premium-search-item {
                padding: 10px 0;
            }
            .premium-search-divider {
                width: 100%;
                height: 1px;
                margin: 5px 0;
                background: #f0ece4;
            }
            .premium-search-btn {
                width: 100%;
                margin-top: 15px;
                margin-left: 0;
                border-radius: 10px;
            }
        }
"""
if "/* === PREMIUM SEARCH BAR === */" not in agenda:
    agenda = agenda[:style_idx] + premium_css + agenda[style_idx:]

with open('agenda.php', 'w', encoding='utf-8') as f:
    f.write(agenda)
