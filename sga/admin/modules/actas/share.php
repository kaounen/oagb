<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/MailHelper.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM gestao_actas WHERE id = ?");
$stmt->execute([$id]);
$acta = $stmt->fetch();
if (!$acta) { header("Location: index.php"); exit; }

// Stats for recipients
$count_law = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo'")->fetchColumn();
$count_int = $pdo->query("SELECT COUNT(*) FROM advogados_estagiarios WHERE status = 'ativo'")->fetchColumn();
$count_ordem = $pdo->query("SELECT (SELECT COUNT(*) FROM bastonarios WHERE email_contacto != '') + (SELECT COUNT(*) FROM departamentos_contactos WHERE status = 'ativo' AND email != '')")->fetchColumn();

$msg = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $recipients_manual = json_decode($_POST['recipients_manual'] ?? '[]', true);

    $all_emails = [];
    foreach($recipients_manual as $r) { $all_emails[] = $r['value']; }
    $all_emails = array_unique($all_emails);

    if (empty($all_emails)) {
        $error = "Nenhum destinatário válido selecionado.";
    } else {
        $success_count = 0;
        $acta_link = "https://oagb.gw/view-acta.php?id=" . $acta['id'] . "&code=" . $acta['codigo'];
        
        $full_message = "
            <h2 style='color: #4D1C21;'>Partilha de Acta Oficial</h2>
            <p style='font-size: 16px;'>Foi partilhada consigo a acta: <strong>" . htmlspecialchars($acta['titulo']) . "</strong></p>
            <div style='background-color: #f9f9f9; padding: 20px; border-left: 4px solid #B1A276; margin: 20px 0;'>
                <p style='font-size: 14px; font-style: italic;'>" . nl2br(htmlspecialchars($body)) . "</p>
            </div>
            <p>Pode consultar o documento completo e anexos através do link abaixo:</p>
            <div style='margin-top: 30px; text-align: center;'>
                <a href='$acta_link' style='background-color: #111923; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>VISUALIZAR ACTA DIGITAL</a>
            </div>
        ";

        foreach ($all_emails as $to) {
            if (MailHelper::send($to, $subject, $full_message)) {
                $success_count++;
            }
        }

        $msg = "Partilha concluída! Enviado com sucesso para $success_count destinatários.";
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'MINUTES_SHARE', "Partilhou a acta: " . $acta['titulo'] . " com $success_count pessoas", 'gestao_actas', $id);
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
    :root { --primary-gold: #B1A276; }
    .tagify { border: none; background: #f8f9fa; border-radius: 12px; padding: 10px; }
    .tagify__tag { background: var(--primary-gold); color: #111923; font-weight: 700; border-radius: 6px; }
    .list-selection { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
    .list-item { 
        background: #f8f9fa; border: 2px solid transparent; padding: 15px 25px; border-radius: 15px; 
        cursor: pointer; transition: 0.3s; flex: 1; min-width: 180px;
    }
    .list-item:hover { background: #fff; border-color: #eee; }
    .list-item.active { border-color: var(--primary-gold); background: #fff; box-shadow: 0 5px 15px rgba(177, 162, 118, 0.1); }
</style>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Partilhar Acta</h2>
        <div class="text-muted small">Documento: <strong class="text-dark"><?php echo htmlspecialchars($acta['titulo']); ?></strong></div>
    </div>
</div>

<?php if($msg): ?>
    <div class="alert alert-success rounded-4 p-4 mb-5 shadow-sm">
        <i class="fas fa-check-circle fa-2x me-3 align-middle"></i>
        <span class="align-middle fw-bold"><?php echo $msg; ?></span>
        <div class="mt-3"><a href="index.php" class="btn btn-sm btn-success rounded-pill px-4">Voltar ao Livro de Actas</a></div>
    </div>
<?php else: ?>

    <div class="card border-0 shadow-sm p-5 bg-white mb-5 rounded-4">
        <?php if($error): ?>
            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Assunto do E-mail</label>
                <input type="text" name="subject" class="form-control border-0 bg-light p-3 fw-bold" required value="OAGB: Partilha de Acta Oficial - <?php echo htmlspecialchars($acta['codigo']); ?>">
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase mb-3">Selecione os Destinatários</label>
                <div class="list-selection">
                    <div class="list-item" onclick="importList('lawyers', this)">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary p-2 rounded-circle me-3"><i class="fas fa-user-tie"></i></div>
                            <div><div class="fw-bold small">Advogados</div><div class="x-small text-muted"><?php echo $count_law; ?></div></div>
                        </div>
                    </div>
                    <div class="list-item" onclick="importList('interns', this)">
                        <div class="d-flex align-items-center">
                            <div class="bg-info-subtle text-info p-2 rounded-circle me-3"><i class="fas fa-user-graduate"></i></div>
                            <div><div class="fw-bold small">Estagiários</div><div class="x-small text-muted"><?php echo $count_int; ?></div></div>
                        </div>
                    </div>
                    <div class="list-item" onclick="importList('ordem', this)">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning-subtle text-warning p-2 rounded-circle me-3"><i class="fas fa-building"></i></div>
                            <div><div class="fw-bold small">Ordem</div><div class="x-small text-muted"><?php echo $count_ordem; ?></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase mb-3">Lista de Destinatários (Sugestão Ativa)</label>
                <div class="p-1 bg-light rounded-4">
                    <input name="recipients_manual" class="form-control" placeholder="Escreva nomes ou cole emails...">
                </div>
                <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Smart Paste: Reconhece <code>Nome <email></code> automaticamente.</div>
            </div>

            <div class="mb-5">
                <label class="form-label small fw-bold text-muted text-uppercase">Mensagem Personalizada</label>
                <textarea name="body" class="form-control border-0 bg-light p-3" rows="5" placeholder="Digite uma breve introdução para acompanhar a acta..."></textarea>
            </div>

            <div class="text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">
                    <i class="fas fa-paper-plane me-2"></i> ENVIAR PARTILHA AGORA
                </button>
            </div>
        </form>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    // Tagify with Suggestions
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
        dropdown: { enabled: 1, maxItems: 15, classname: "tags-look", closeOnSelect: false }
    });

    tagify.on('input', function(e){
        var value = e.detail.value;
        tagify.whitelist = null;
        if(value.length < 2) return;
        tagify.loading(true);
        fetch('../newsletter/suggest_emails.php?q=' + value)
            .then(RES => RES.json())
            .then(function(newWhitelist){
                tagify.whitelist = newWhitelist;
                tagify.loading(false).dropdown.show(value);
            });
    });

    const importedTags = { lawyers: [], interns: [], ordem: [] };

    function importList(target, el) {
        const icon = el.querySelector('i');
        if (el.classList.contains('active')) {
            if (importedTags[target] && importedTags[target].length > 0) {
                tagify.removeTags(importedTags[target]);
                importedTags[target] = [];
            }
            el.classList.remove('active');
            if (target === 'lawyers') icon.className = 'fas fa-user-tie';
            else if (target === 'interns') icon.className = 'fas fa-user-graduate';
            else if (target === 'ordem') icon.className = 'fas fa-building';
            return;
        }

        const originalClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin';
        
        fetch('../newsletter/get_list_emails.php?target=' + target)
            .then(res => res.json())
            .then(data => {
                const enrichedData = data.map(item => ({ ...item, origin: target }));
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
