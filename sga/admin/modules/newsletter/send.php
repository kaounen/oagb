<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Stats for recipients
$count_sub = $pdo->query("SELECT COUNT(*) FROM newsletter_subscricoes WHERE ativo = 1 AND confirmado = 1")->fetchColumn();
$count_law = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo'")->fetchColumn();
$count_int = $pdo->query("SELECT COUNT(*) FROM advogados_estagiarios WHERE status = 'ativo'")->fetchColumn();
$count_ordem = $pdo->query("SELECT (SELECT COUNT(*) FROM bastonarios WHERE email_contacto != '') + (SELECT COUNT(*) FROM departamentos_contactos WHERE status = 'ativo' AND email != '')")->fetchColumn();

// Fetch Edition if provided
$pre_subject = "";
$pre_body = "";
$is_edition = false;
$eid = $_GET['edition'] ?? null;

if ($eid) {
    $stmt = $pdo->prepare("SELECT titulo FROM newsletter_edicoes WHERE id = ?");
    $stmt->execute([$eid]);
    $pre_subject = $stmt->fetchColumn();
    $is_edition = true;
    
    // Fetch HTML from preview internally
    ob_start();
    $_GET['id'] = $eid;
    $_GET['raw'] = 1;
    include __DIR__ . '/preview_edition.php';
    $pre_body = ob_get_clean();
}

// Handle Sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_campaign'])) {
    $subject = $_POST['subject'];
    $body = !empty($_POST['body_raw']) ? $_POST['body_raw'] : ($_POST['body'] ?? '');
    $recipients_manual = json_decode($_POST['recipients_manual'] ?? '[]', true);
    $targets = $_POST['targets'] ?? [];

    $all_emails = [];
    foreach($recipients_manual as $r) { $all_emails[] = $r['value']; }
    
    if (in_array('subs', $targets)) {
        $stmt = $pdo->query("SELECT email FROM newsletter_subscricoes WHERE ativo = 1 AND confirmado = 1");
        while($row = $stmt->fetch()) $all_emails[] = $row['email'];
    }
    if (in_array('lawyers', $targets)) {
        $stmt = $pdo->query("SELECT email FROM advogados WHERE status = 'ativo' AND email != ''");
        while($row = $stmt->fetch()) $all_emails[] = $row['email'];
    }
    if (in_array('interns', $targets)) {
        $stmt = $pdo->query("SELECT email FROM advogados_estagiarios WHERE status = 'ativo' AND email != ''");
        while($row = $stmt->fetch()) $all_emails[] = $row['email'];
    }

    $all_emails = array_unique($all_emails);
    $total_to_send = count($all_emails);
    
    $success_count = 0;
    $from_email = defined('FROM_EMAIL') ? FROM_EMAIL : 'comunicacao@oagb.gw';
    $from_name = defined('FROM_NAME') ? FROM_NAME : 'OAGB Comunicações';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $from_name <$from_email>" . "\r\n";

    foreach ($all_emails as $to) {
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            if (@mail($to, $subject, $body, $headers)) { $success_count++; }
        }
    }
    
    $msg = "Campanha finalizada! Enviados " . $success_count . " de " . $total_to_send . " e-mails com sucesso.";
    if ($success_count == 0 && $total_to_send > 0) {
        $msg .= "<br><small class='text-danger'>Nota: Em ambiente LOCAL (XAMPP), o envio de e-mails reais requer configuração de SMTP. Este erro é esperado se o servidor não estiver online.</small>";
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
    :root { --primary-gold: #B1A276; }
    .page-title { font-weight: 800; color: #111923; }
    .card-newsletter { border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    .tagify { border: none; background: #f8f9fa; border-radius: 12px; padding: 10px; }
    .tagify__tag { background: var(--primary-gold); color: #111923; font-weight: 700; border-radius: 6px; }
    .list-selection { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
    .list-item { 
        background: #f8f9fa; border: 2px solid transparent; padding: 15px 25px; border-radius: 15px; 
        cursor: pointer; transition: 0.3s; flex: 1; min-width: 200px;
    }
    .list-item:hover { background: #fff; border-color: #eee; }
    .list-item.active { border-color: var(--primary-gold); background: #fff; box-shadow: 0 5px 15px rgba(177, 162, 118, 0.1); }
    .editor-container { border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Sistema de Disseminação Digital</h2>
            <p class="text-muted small">Estado da arte em comunicação institucional. Configure e visualize o disparo.</p>
        </div>
    </div>

    <form method="POST">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-newsletter">
                    <div class="card-body p-5">
                        <?php if(isset($msg)): ?>
                            <div class="alert alert-info border-0 rounded-4 p-4 mb-4 shadow-sm"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-3">Selecione os Destinatários</label>
                            <div class="list-selection">
                                <div class="list-item" onclick="importList('lawyers', this)">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3"><i class="fas fa-user-tie"></i></div>
                                        <div>
                                            <div class="fw-bold">Advogados</div>
                                            <div class="small text-muted"><?php echo $count_law; ?> membros</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-item" onclick="importList('interns', this)">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info-subtle text-info p-3 rounded-circle me-3"><i class="fas fa-user-graduate"></i></div>
                                        <div>
                                            <div class="fw-bold">Estagiários</div>
                                            <div class="small text-muted"><?php echo $count_int; ?> membros</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-item" onclick="importList('subs', this)">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success p-3 rounded-circle me-3"><i class="fas fa-envelope-open-text"></i></div>
                                        <div>
                                            <div class="fw-bold">Subscritores</div>
                                            <div class="small text-muted"><?php echo $count_sub; ?> contactos</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-item" onclick="importList('ordem', this)">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-subtle text-warning p-3 rounded-circle me-3"><i class="fas fa-building"></i></div>
                                        <div>
                                            <div class="fw-bold">Membros da Ordem</div>
                                            <div class="small text-muted"><?php echo $count_ordem; ?> membros</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-3">Lista de Destinatários (Sugestão Ativa)</label>
                            <div class="p-1 bg-light rounded-4">
                                <input name="recipients_manual" class="form-control" placeholder="Escreva nomes ou cole emails...">
                            </div>
                            <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Smart Paste: Reconhece <code>Nome <email></code> automaticamente.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-3">Assunto da Campanha</label>
                            <input type="text" name="subject" class="form-control form-control-lg border-0 bg-light p-3 rounded-4 fw-bold" value="<?php echo htmlspecialchars($pre_subject); ?>" required>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold text-dark small text-uppercase m-0">Conteúdo da Newsletter</label>
                                <?php if($is_edition): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="fas fa-shield-alt me-1"></i> Design Modular Ativo (Fidelidade Protegida)
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="toggleEditor()">Alternar HTML / Editor Visual</button>
                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="document.getElementById('file_import').click()">Importar Ficheiro</button>
                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="document.getElementById('img_import').click()">Importar Foto</button>
                                <input type="file" id="file_import" class="d-none" accept=".html,.htm,.txt" onchange="handleFileUpload(this, 'file')">
                                <input type="file" id="img_import" class="d-none" accept="image/*" onchange="handleFileUpload(this, 'image')">
                            </div>

                            <?php if($is_edition): ?>
                            <div class="alert alert-warning border-0 rounded-4 mb-3 x-small shadow-sm">
                                <i class="fas fa-info-circle me-2"></i> <strong>Atenção:</strong> O editor visual pode desformatar o design modular. O que vê no <strong>Preview à direita</strong> é a versão exata que será enviada.
                            </div>
                            <?php endif; ?>
                            
                            <div class="editor-container" id="rich_editor_container">
                                <textarea name="body" id="editor"><?php echo htmlspecialchars($pre_body); ?></textarea>
                            </div>
                            <div class="editor-container d-none" id="raw_editor_container">
                                <textarea name="body_raw" id="raw_editor" class="form-control font-monospace bg-dark text-success p-4" rows="15" style="border-radius: 12px;"><?php echo htmlspecialchars($pre_body); ?></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-5">
                            <button type="submit" name="send_campaign" onclick="syncBeforeSubmit()" class="btn btn-dark px-5 py-3 rounded-pill fw-bold shadow-lg">
                                <i class="fas fa-paper-plane me-2 text-gold"></i> DISPARAR CAMPANHA AGORA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Informações de Envio</h5>
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="small text-muted mb-1">Destinatários Selecionados</div>
                        <div class="h4 fw-bold mb-0" id="recipient_count">0</div>
                    </div>
                    
                    <h6 class="fw-bold x-small text-uppercase text-muted mb-3">Preview de Fidelidade (REAL)</h6>
                    <div class="border rounded-4 overflow-hidden shadow-sm">
                        <?php if($eid): ?>
                            <iframe src="preview_edition.php?id=<?php echo $eid; ?>" style="width: 100%; height: 600px; border: none;"></iframe>
                        <?php else: ?>
                            <div class="p-5 text-center text-muted small">O preview aparecerá aqui.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3 x-small text-muted text-center italic">
                        <i class="fas fa-check-circle me-1 text-success"></i> O e-mail chegará exatamente como este preview.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>

<script>
    let myEditor;
    const isEdition = <?php echo $is_edition ? 'true' : 'false'; ?>;

    ClassicEditor.create(document.querySelector('#editor')).then(editor => { 
        myEditor = editor; 
        if (isEdition) toggleEditor(true);
    }).catch(e => console.error(e));

    function syncBeforeSubmit() {
        const rich = document.getElementById('rich_editor_container');
        const rawTextarea = document.getElementById('raw_editor');
        if (!rich.classList.contains('d-none')) {
            rawTextarea.value = myEditor.getData();
        }
    }

    function toggleEditor(forceRaw = false) {
        const rich = document.getElementById('rich_editor_container');
        const raw = document.getElementById('raw_editor_container');
        const rawTextarea = document.getElementById('raw_editor');
        if (raw.classList.contains('d-none') || forceRaw) {
            if (!forceRaw) rawTextarea.value = myEditor.getData();
            rich.classList.add('d-none');
            raw.classList.remove('d-none');
        } else {
            myEditor.setData(rawTextarea.value);
            raw.classList.add('d-none');
            rich.classList.remove('d-none');
        }
    }

    function handleFileUpload(input, type) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const content = e.target.result;
                if (type === 'image') {
                    const imgHtml = `<img src="${content}" style="max-width:100%; border-radius:10px; margin:20px 0;">`;
                    if (myEditor) myEditor.model.change(writer => {
                        const viewFragment = myEditor.data.processor.toView(imgHtml);
                        const modelFragment = myEditor.data.toModel(viewFragment);
                        myEditor.model.insertContent(modelFragment);
                    });
                } else {
                    if (myEditor) myEditor.setData(content);
                    document.getElementById('raw_editor').value = content;
                    toggleEditor(true);
                }
                alert('Ficheiro processado com sucesso!');
            };
            if (type === 'image') reader.readAsDataURL(input.files[0]);
            else reader.readAsText(input.files[0]);
        }
    }

    // Tagify with Suggestions & Premium Dropdown
    var input = document.querySelector('input[name=recipients_manual]');
    var tagify = new Tagify(input, {
        delimiters: ",| |;|\n",
        transformTag: function(tagData) {
            const regex = /(.+?)\s*<(.+?)>/;
            const match = tagData.value.match(regex);
            if (match) {
                tagData.name = match[1].trim();
                tagData.value = match[2].trim();
            }
        },
        dropdown: {
            enabled: 1,
            maxItems: 15,
            classname: "tags-look",
            closeOnSelect: false
        },
        templates: {
            dropdownItem: function(item){
                return `
                    <div ${this.getAttributes(item)} class='tagify__dropdown__item ${item.class ? item.class : ""}'>
                        <div class="d-flex align-items-center p-2">
                            <div class="bg-light p-2 rounded-circle me-3"><i class="fas fa-user small"></i></div>
                            <div>
                                <div class="fw-bold small">${item.name}</div>
                                <div class="x-small text-muted">${item.value} <span class="badge bg-secondary ms-1">${item.type}</span></div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    });

    tagify.on('input', function(e){
        var value = e.detail.value;
        tagify.whitelist = null;
        if(value.length < 2) return;
        tagify.loading(true);
        fetch('suggest_emails.php?q=' + value)
            .then(RES => RES.json())
            .then(function(newWhitelist){
                tagify.whitelist = newWhitelist;
                tagify.loading(false).dropdown.show(value);
            });
    });

    // Sistema de rastreio infalível para tags importadas
    const importedTags = { lawyers: [], interns: [], subs: [], ordem: [] };

    function importList(target, el) {
        const icon = el.querySelector('i');
        
        if (el.classList.contains('active')) {
            console.log('Removendo lista:', target);
            if (importedTags[target] && importedTags[target].length > 0) {
                tagify.removeTags(importedTags[target]);
                importedTags[target] = []; // Limpar rastro
            } else {
                // Fallback de emergência caso o rastro se perca
                const toRemove = tagify.value.filter(t => t.origin === target);
                tagify.removeTags(toRemove);
            }
            el.classList.remove('active');
            if (target === 'lawyers') icon.className = 'fas fa-user-tie';
            else if (target === 'interns') icon.className = 'fas fa-user-graduate';
            else if (target === 'subs') icon.className = 'fas fa-envelope-open-text';
            else if (target === 'ordem') icon.className = 'fas fa-building';
            return;
        }

        const originalClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin';
        
        fetch('get_list_emails.php?target=' + target)
            .then(res => res.json())
            .then(data => {
                const enrichedData = data.map(item => {
                    const base = typeof item === 'string' ? { value: item } : item;
                    return { ...base, origin: target };
                });
                
                // Guardar a referência direta das tags criadas
                const newTags = tagify.addTags(enrichedData);
                importedTags[target] = newTags;
                
                el.classList.add('active');
                icon.className = 'fas fa-check';
            })
            .catch(err => {
                console.error('Erro:', err);
                icon.className = originalClass;
            });
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
