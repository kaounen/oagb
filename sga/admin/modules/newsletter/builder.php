<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch all possible site contents for picking
// Fetch all possible site contents for picking
$noticias = $pdo->query("SELECT id, titulo, 'noticia' as origin FROM noticias ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
$anuncios = $pdo->query("SELECT id, titulo, 'anuncio' as origin FROM anuncios ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
$pareceres = $pdo->query("SELECT id, assunto as titulo, tipo as origin FROM pareceres_deliberacoes ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
$legislacao = $pdo->query("SELECT id, titulo, 'legislacao' as origin FROM legislacao_nacional ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
$agenda = $pdo->query("SELECT id, titulo, 'agenda' as origin FROM agenda ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
$paginas = $pdo->query("SELECT id, titulo, 'pagina' as origin FROM paginas_ordem ORDER BY id DESC LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);

$all_site_items = array_merge($noticias, $anuncios, $pareceres, $legislacao, $agenda, $paginas);

$edit_id = $_GET['id'] ?? null;
$edit_data = null;
$blocks = [];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM newsletter_edicoes WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $blocks = json_decode($edit_data['conteudo_json'] ?? '[]', true);
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_newsletter'])) {
    $titulo = $_POST['titulo'];
    $status = $_POST['status'] ?? 'rascunho';
    
    $posted_blocks = [];
    if (isset($_POST['block_type'])) {
        foreach($_POST['block_type'] as $index => $type) {
            $block = [
                'type' => $type, 
                'title' => $_POST['block_title'][$index] ?? '',
                'bg_color' => $_POST['block_bg'][$index] ?? '#ffffff',
                'text_color' => $_POST['block_text_color'][$index] ?? '#333333'
            ];
            
            if ($type === 'editorial' || $type === 'generic' || $type === 'site_content') {
                $block['content'] = $_POST['block_content'][$index] ?? '';
                $block['icon'] = $_POST['block_icon'][$index] ?? '';
                $block['link'] = $_POST['block_link'][$index] ?? '';
                $block['link_text'] = $_POST['block_link_text'][$index] ?? '';
                $block['items'] = $_POST['block_site_items'][$index] ?? [];
                
                // Handle Image Upload
                if (!empty($_FILES['block_image']['name'][$index])) {
                    $img_name = time() . '_' . basename($_FILES['block_image']['name'][$index]);
                    $upload_dir = dirname(dirname(dirname(dirname(__DIR__)))) . '/uploads/newsletter/';
                    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    move_uploaded_file($_FILES['block_image']['tmp_name'][$index], $upload_dir . $img_name);
                    $block['image'] = $img_name;
                } else {
                    $block['image'] = $_POST['existing_image'][$index] ?? '';
                }
            } elseif ($type === 'html') {
                $block['content'] = $_POST['block_content'][$index] ?? '';
            }
            $posted_blocks[] = $block;
        }
    }
    $conteudo_json = json_encode($posted_blocks);

    if ($edit_id) {
        $stmt = $pdo->prepare("UPDATE newsletter_edicoes SET titulo = ?, conteudo_json = ?, status = ? WHERE id = ?");
        $stmt->execute([$titulo, $conteudo_json, $status, $edit_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO newsletter_edicoes (titulo, conteudo_json, status) VALUES (?, ?, ?)");
        $stmt->execute([$titulo, $conteudo_json, $status]);
        $edit_id = $pdo->lastInsertId();
    }
    header("Location: builder.php?id=$edit_id&saved=1"); exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    :root { --primary-gold: #B1A276; }
    .builder-container { background: #f0f2f5; border-radius: 24px; padding: 30px; }
    .block-item { 
        background: white; border-radius: 20px; padding: 30px; margin-bottom: 25px; 
        border: 1px solid #e0e0e0; position: relative; transition: 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .block-item:hover { border-color: var(--primary-gold); box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    .block-handle { cursor: grab; position: absolute; left: -15px; top: 50%; transform: translateY(-50%); color: #ccc; font-size: 1.5rem; }
    .block-remove { position: absolute; right: 15px; top: 15px; color: #dc3545; cursor: pointer; opacity: 0.5; transition: 0.3s; z-index: 999 !important; }
    .block-remove:hover { opacity: 1; transform: scale(1.15); }
    .section-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-gold); font-weight: 800; margin-bottom: 8px; display: block; }
    .add-block-btn { 
        border: 2px dashed #B1A276; background: rgba(177, 162, 118, 0.05); padding: 25px; border-radius: 20px; 
        width: 100%; color: var(--primary-gold); font-weight: 800; transition: 0.3s; margin-top: 30px;
    }
    .add-block-btn:hover { background: #fff; transform: translateY(-3px); }
    .color-picker-wrap { display: flex; align-items: center; gap: 10px; background: #f8f9fa; padding: 10px; border-radius: 10px; }

    /* Premium Select2 Custom Arrow and Styling */
    .select2-container--bootstrap-5 .select2-selection--multiple {
        border: 1px solid #dee2e6 !important;
        border-radius: 12px !important;
        padding: 0.6rem 2.5rem 0.6rem 1rem !important;
        background-color: #ffffff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23B1A276' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        background-size: 16px 12px !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        cursor: pointer !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-gold) !important;
        box-shadow: 0 0 0 0.25rem rgba(177, 162, 118, 0.25) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(177, 162, 118, 0.1) !important;
        border: 1px solid rgba(177, 162, 118, 0.3) !important;
        color: #8c7b4e !important;
        border-radius: 8px !important;
        padding: 4px 10px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        margin-top: 4px !important;
        margin-bottom: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #dc3545 !important;
        margin-right: 5px !important;
        border: none !important;
        background: transparent !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #a71d2a !important;
        background-color: transparent !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
        margin-top: 6px !important;
        font-family: inherit !important;
        font-size: 0.95rem !important;
    }
</style>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold m-0 text-dark">Super Builder <span class="text-gold">PRO</span></h2>
        <p class="text-muted small">Design avançado, exportação e integração total de conteúdos.</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 dropdown-toggle" data-bs-toggle="dropdown">Exportar Edição</button>
            <ul class="dropdown-menu shadow-lg border-0">
                <li><a class="dropdown-item" href="preview_edition.php?id=<?php echo $edit_id; ?>&download=html" target="_blank"><i class="fas fa-code me-2"></i> Download HTML</a></li>
                <li><a class="dropdown-item" href="preview_edition.php?id=<?php echo $edit_id; ?>&print=1" target="_blank"><i class="fas fa-file-pdf me-2"></i> Exportar PDF / Imprimir</a></li>
            </ul>
        </div>
        <a href="index.php" class="btn btn-dark rounded-pill px-4 ms-2 shadow-sm">Sair</a>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="builder-container shadow-sm">
                <div class="mb-5">
                    <label class="section-label">Título da Newsletter</label>
                    <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-white shadow-sm fw-bold px-4 py-3" style="border-radius: 15px;" placeholder="Ex: Informativo OAGB" value="<?php echo htmlspecialchars($edit_data['titulo'] ?? ''); ?>" required>
                </div>

                <div id="blocks-container">
                    <?php foreach($blocks as $idx => $block): ?>
                        <div class="block-item">
                            <div class="block-handle"><i class="fas fa-grip-vertical"></i></div>
                            <div class="block-remove" onclick="removeBlock(this)"><i class="fas fa-times-circle"></i></div>
                            <input type="hidden" name="block_type[]" value="<?php echo $block['type']; ?>">
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="section-label">Título da Secção</label>
                                    <input type="text" name="block_title[]" class="form-control border-0 bg-light fw-bold" value="<?php echo htmlspecialchars($block['title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="section-label">Esquema de Cores (Rápido)</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-dark rounded-circle p-2" onclick="setBlockColor(this, '#111923', '#ffffff')" title="Navy"></button>
                                        <button type="button" class="btn btn-sm rounded-circle p-2" style="background:#B1A276;" onclick="setBlockColor(this, '#B1A276', '#111923')" title="Ouro"></button>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle p-2" onclick="setBlockColor(this, '#f8f9fa', '#333333')" title="Cinza"></button>
                                        <button type="button" class="btn btn-sm btn-white border rounded-circle p-2" onclick="setBlockColor(this, '#ffffff', '#333333')" title="Branco"></button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="section-label">Fundo</label>
                                    <div class="color-picker-wrap">
                                        <input type="color" name="block_bg[]" class="form-control form-control-color border-0 p-0 bg-transparent" value="<?php echo $block['bg_color'] ?? '#ffffff'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="section-label">Texto</label>
                                    <div class="color-picker-wrap">
                                        <input type="color" name="block_text_color[]" class="form-control form-control-color border-0 p-0 bg-transparent" value="<?php echo $block['text_color'] ?? '#333333'; ?>">
                                    </div>
                                </div>
                            </div>

                            <?php if($block['type'] === 'editorial' || $block['type'] === 'generic' || $block['type'] === 'site_content'): ?>
                                <div class="<?php echo $block['type'] === 'site_content' ? 'd-none' : ''; ?> mb-4">
                                    <label class="section-label">Conteúdo / Texto de Apoio</label>
                                    <textarea name="block_content[]" class="editor"><?php echo $block['content'] ?? ''; ?></textarea>
                                </div>
                                <div class="<?php echo $block['type'] === 'site_content' ? 'd-none' : 'row g-3'; ?>">
                                    <div class="col-md-3">
                                        <label class="section-label">Ícone (FA)</label>
                                        <input type="text" name="block_icon[]" class="form-control border-0 bg-light small" value="<?php echo htmlspecialchars($block['icon'] ?? ''); ?>" placeholder="fas fa-star">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="section-label">Imagem</label>
                                        <input type="file" name="block_image[]" class="form-control border-0 bg-light small">
                                        <input type="hidden" name="existing_image[]" value="<?php echo $block['image'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="section-label">Link Personalizado</label>
                                        <input type="text" name="block_link[]" class="form-control border-0 bg-light small" value="<?php echo htmlspecialchars($block['link'] ?? ''); ?>" placeholder="URL">
                                    </div>
                                </div>
                                
                                <?php if($block['type'] === 'site_content'): ?>
                                    <!-- Simplified Site Content Bloco (O que há de novo?) -->
                                    <div class="mt-3 p-3 bg-light rounded-4 border border-info border-opacity-25 animate__animated animate__fadeIn">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3 text-info">
                                                <i class="fas fa-globe fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">Mapeamento Direto do Website (O que há de novo?)</h6>
                                                <small class="text-muted">Selecione abaixo as publicações que deseja incluir na Newsletter. O sistema carregará automaticamente fotos, títulos e textos na íntegra diretamente da base de dados!</small>
                                            </div>
                                        </div>
                                        <label class="section-label">Vincular Conteúdos Publicados</label>
                                        <select name="block_site_items[<?php echo $idx; ?>][]" class="form-select select2" multiple>
                                            <?php foreach($all_site_items as $item): ?>
                                                <option value="<?php echo $item['origin'].':'.$item['id']; ?>" <?php echo in_array($item['origin'].':'.$item['id'], $block['items'] ?? []) ? 'selected' : ''; ?>>
                                                    [<?php echo strtoupper($item['origin'] == 'noticia' ? 'Notícia' : ($item['origin'] == 'anuncio' ? 'Anúncio' : ($item['origin'] == 'agenda' ? 'Agenda/Evento' : ($item['origin'] == 'pagina' ? 'Página' : ucfirst($item['origin']))))); ?>] <?php echo htmlspecialchars($item['titulo']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            <?php elseif($block['type'] === 'html'): ?>
                                <textarea name="block_content[]" class="form-control font-monospace x-small" rows="6"><?php echo $block['content'] ?? ''; ?></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="add-block-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus-circle me-2"></i> ADICIONAR MÓDULO À NEWSLETTER
                </button>
                <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2" style="max-width: 300px;">
                    <li><a class="dropdown-item py-3 px-4 fw-bold rounded-3" href="#" onclick="addBlock('editorial')"><i class="fas fa-quote-left me-3 text-primary"></i> Bloco Editorial</a></li>
                    <li><a class="dropdown-item py-3 px-4 fw-bold rounded-3" href="#" onclick="addBlock('generic')"><i class="fas fa-columns me-3 text-info"></i> Bloco Livre (Texto + Media)</a></li>
                    <li><a class="dropdown-item py-3 px-4 fw-bold rounded-3" href="#" onclick="addBlock('site_content')"><i class="fas fa-link me-3 text-success"></i> O que há de novo? (Conteúdo do Site)</a></li>
                </ul>

                <div class="mt-5 border-top pt-4">
                    <button type="submit" name="save_newsletter" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-lg">
                        <i class="fas fa-save me-2"></i> GUARDAR ALTERAÇÕES E ATUALIZAR PREVIEW
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="preview-box shadow-sm">
                <div class="preview-header">
                    <div class="small fw-bold opacity-50 mb-1">REAL-TIME PREVIEW</div>
                    <div class="badge bg-gold text-dark px-3">FULL CONTENT ENGINE</div>
                </div>
                <iframe id="preview_iframe" src="preview_edition.php?id=<?php echo $edit_id; ?>" style="width: 100%; height: 800px; border: none;"></iframe>
                <div class="p-3 bg-light text-center border-top">
                    <?php if($edit_id): ?>
                        <a href="send.php?edition=<?php echo $edit_id; ?>" class="btn btn-login w-100 py-3 rounded-pill fw-bold">
                            <i class="fas fa-paper-plane me-2"></i> SEGUIR PARA O DISPARO
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    let blockCount = <?php echo count($blocks); ?>;

    function setBlockColor(el, bg, text) {
        const item = el.closest('.block-item');
        item.querySelector('input[name="block_bg[]"]').value = bg;
        item.querySelector('input[name="block_text_color[]"]').value = text;
    }

    function addBlock(type) {
        const container = document.getElementById('blocks-container');
        const div = document.createElement('div');
        div.className = 'block-item animate__animated animate__fadeInUp';
        
        let title = '';
        let icon = '';
        if (type === 'editorial') { title = 'Editorial'; icon = 'fas fa-quote-right'; }
        else if (type === 'generic') { title = 'Título do Bloco'; icon = 'fas fa-info-circle'; }
        else if (type === 'site_content') { title = 'Destaques do Site'; icon = 'fas fa-layer-group'; }

        div.innerHTML = `
            <div class="block-handle"><i class="fas fa-grip-vertical"></i></div>
            <div class="block-remove" onclick="removeBlock(this)"><i class="fas fa-times-circle"></i></div>
            <input type="hidden" name="block_type[]" value="${type}">
            
            <div class="row g-3 mb-4">
                <div class="col-md-4"><label class="section-label">Título</label><input type="text" name="block_title[]" class="form-control border-0 bg-light fw-bold" value="${title}"></div>
                <div class="col-md-3">
                    <label class="section-label">Presets</label>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-dark rounded-circle p-2" onclick="setBlockColor(this, '#111923', '#ffffff')"></button>
                        <button type="button" class="btn btn-sm rounded-circle p-2" style="background:#B1A276;" onclick="setBlockColor(this, '#B1A276', '#111923')"></button>
                        <button type="button" class="btn btn-sm btn-light border rounded-circle p-2" onclick="setBlockColor(this, '#f8f9fa', '#333333')"></button>
                    </div>
                </div>
                <div class="col-md-2"><label class="section-label">Fundo</label><div class="color-picker-wrap"><input type="color" name="block_bg[]" class="form-control form-control-color border-0 p-0 bg-transparent" value="#ffffff"></div></div>
                <div class="col-md-2"><label class="section-label">Texto</label><div class="color-picker-wrap"><input type="color" name="block_text_color[]" class="form-control form-control-color border-0 p-0 bg-transparent" value="#333333"></div></div>
            </div>

            <div class="${type === 'site_content' ? 'd-none' : ''} mb-4"><label class="section-label">Conteúdo</label><textarea name="block_content[]" id="ed_${blockCount}" class="editor"></textarea></div>
            <div class="${type === 'site_content' ? 'd-none' : 'row g-3'}">
                <div class="col-md-3"><label class="section-label">Ícone</label><input type="text" name="block_icon[]" class="form-control border-0 bg-light small" value="${icon}"></div>
                <div class="col-md-4">
                    <label class="section-label">Imagem</label>
                    <input type="file" name="block_image[]" class="form-control border-0 bg-light small">
                    <input type="hidden" name="existing_image[]" value="">
                </div>
                <div class="col-md-5"><label class="section-label">Link</label><input type="text" name="block_link[]" class="form-control border-0 bg-light small" placeholder="https://"></div>
            </div>
            ${type === 'site_content' ? `
            <div class="mt-3 p-3 bg-light rounded-4 border border-info border-opacity-25 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3 text-info">
                        <i class="fas fa-globe fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Mapeamento Direto do Website (O que há de novo?)</h6>
                        <small class="text-muted">Selecione abaixo as publicações que deseja incluir na Newsletter. O sistema carregará automaticamente fotos, títulos e textos na íntegra diretamente da base de dados!</small>
                    </div>
                </div>
                <label class="section-label">Vincular Conteúdos Publicados</label>
                <select name="block_site_items[${blockCount}][]" class="form-select select2" multiple>
                    <?php foreach($all_site_items as $item): ?>
                        <option value="<?php echo $item['origin'].':'.$item['id']; ?>">[<?php echo strtoupper($item['origin'] == 'noticia' ? 'Notícia' : ($item['origin'] == 'anuncio' ? 'Anúncio' : ($item['origin'] == 'agenda' ? 'Agenda/Evento' : ($item['origin'] == 'pagina' ? 'Página' : ucfirst($item['origin']))))); ?>] <?php echo addslashes($item['titulo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>` : ''}
        `;

        container.appendChild(div);
        ClassicEditor.create(document.querySelector(`#ed_${blockCount}`)).catch(e => console.error(e));
        if (type === 'site_content') $(div).find('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pesquise ou selecione as publicações para vincular...'
        });
        blockCount++;
    }

    function removeBlock(el) { if(confirm('Eliminar este módulo?')) el.closest('.block-item').remove(); }

    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pesquise ou selecione as publicações para vincular...'
        });
        document.querySelectorAll('.editor').forEach(el => ClassicEditor.create(el).catch(e => console.error(e)));
        new Sortable(document.getElementById('blocks-container'), { handle: '.block-handle', animation: 150 });
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
