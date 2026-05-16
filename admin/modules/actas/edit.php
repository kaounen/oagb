<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM gestao_actas WHERE id = ?");
$stmt->execute([$id]);
$acta = $stmt->fetch();
if (!$acta) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $data = $_POST['data_reuniao'];
    $conteudo = $_POST['conteudo'];
    $status = $_POST['status'];
    $codigo = $_POST['codigo'];
    $partilha_interna = isset($_POST['partilha_interna']) ? 1 : 0;
    $partilha_ordem = isset($_POST['partilha_ordem']) ? 1 : 0;
    
    $ficheiro_url = $acta['ficheiro_url'];
    if (isset($_FILES['ficheiro']) && $_FILES['ficheiro']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/actas/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['ficheiro']['name']);
        if (move_uploaded_file($_FILES['ficheiro']['tmp_name'], $upload_dir . $filename)) {
            $ficheiro_url = 'uploads/actas/' . $filename;
        }
    }

    $stmt = $pdo->prepare("UPDATE gestao_actas SET codigo = ?, titulo = ?, data_reuniao = ?, conteudo = ?, ficheiro_url = ?, status = ?, partilha_interna = ?, partilha_ordem = ? WHERE id = ?");
    $stmt->execute([$codigo, $titulo, $data, $conteudo, $ficheiro_url, $status, $partilha_interna, $partilha_ordem, $id]);

    // Trigger emails to Ordem if status is finalized and was previously rascunho (or just re-send if checked)
    if ($status === 'finalizada' && $partilha_ordem) {
        require_once __DIR__ . '/../../includes/MailHelper.php';
        
        $emails = [];
        $stmt = $pdo->query("SELECT email_contacto FROM bastonarios WHERE email_contacto != ''");
        while($row = $stmt->fetchColumn()) $emails[] = $row;
        $stmt = $pdo->query("SELECT email FROM departamentos_contactos WHERE status = 'ativo' AND email != ''");
        while($row = $stmt->fetchColumn()) $emails[] = $row;
        
        $emails = array_unique($emails);
        if (!empty($emails)) {
            $acta_link = "https://oagb.gw/view-acta.php?id=" . $id . "&code=" . $codigo;
            $subject = "OAGB: Atualização de Acta Digital - $codigo";
            $message = "
                <h2 style='color: #4D1C21;'>Notificação de Acta Oficial</h2>
                <p>Uma acta foi finalizada ou atualizada no sistema digital da OAGB.</p>
                <p><strong>Título:</strong> " . htmlspecialchars($titulo) . "<br>
                   <strong>Código:</strong> $codigo<br>
                   <strong>Data:</strong> " . date('d/m/Y', strtotime($data)) . "</p>
                <div style='margin-top: 30px;'>
                    <a href='$acta_link' style='background-color: #111923; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>VISUALIZAR ACTA</a>
                </div>
            ";
            foreach($emails as $to) { MailHelper::send($to, $subject, $message); }
        }
    }

    require_once __DIR__ . '/../../includes/LogHelper.php';
    LogHelper::log($pdo, 'MINUTES_EDIT', "Editou a acta: $titulo", 'gestao_actas', $id);

    header("Location: index.php?success=2"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Acta</h2>
        <div class="text-muted small">ID: #<?php echo $id; ?> | Criada em: <?php echo date('d/m/Y H:i', strtotime($acta['created_at'])); ?></div>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Código da Acta</label>
                <input type="text" name="codigo" class="form-control border-0 bg-light p-3 fs-6" value="<?php echo htmlspecialchars($acta['codigo']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted text-uppercase">Tìtulo da Reunião / Assembleia</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo htmlspecialchars($acta['titulo']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Data da Reunião</label>
                <input type="date" name="data_reuniao" class="form-control border-0 bg-light p-3 fs-6" required value="<?php echo $acta['data_reuniao']; ?>">
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Conteúdo Integrando Deliberações</label>
            <textarea name="conteudo" id="editor" class="form-control border-0 bg-light p-4" rows="15"><?php echo htmlspecialchars($acta['conteudo']); ?></textarea>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted text-uppercase">Anexar Ficheiro (Substituir atual)</label>
                <input type="file" name="ficheiro" class="form-control border-0 bg-light p-3">
                <?php if($acta['ficheiro_url']): ?>
                    <div class="mt-2 x-small text-primary"><i class="fas fa-paperclip"></i> Ficheiro atual: <a href="/oagb/<?php echo $acta['ficheiro_url']; ?>" target="_blank">Ver Documento</a></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 d-flex align-items-center gap-4">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="partilha_interna" id="partilha_interna" <?php echo $acta['partilha_interna'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold small text-muted text-uppercase" for="partilha_interna">Portal de Membros</label>
                </div>
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="partilha_ordem" id="partilha_ordem" <?php echo $acta['partilha_ordem'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold small text-muted text-uppercase" for="partilha_ordem">Notificar Ordem</label>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-4">
                <select name="status" class="form-select border-0 bg-light p-3 fw-bold text-uppercase small text-muted">
                    <option value="rascunho" <?php echo $acta['status'] === 'rascunho' ? 'selected' : ''; ?>>MANTER COMO RASCUNHO</option>
                    <option value="finalizada" <?php echo $acta['status'] === 'finalizada' ? 'selected' : ''; ?>>FINALIZAR E PUBLICAR</option>
                </select>
            </div>
            <div class="col-md-8 text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">Atualizar Acta Digital</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), { language: 'pt' }).catch(error => { console.error(error); });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
