<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

require_once __DIR__ . '/../../includes/AttachmentHelper.php';

$id = $_GET['id'] ?? 0;

// Handle Attachment Deletion
if (isset($_GET['delete_attachment'])) {
    AttachmentHelper::delete($pdo, $_GET['delete_attachment']);
    header("Location: edit.php?id=" . $id . "&att_deleted=1");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if(!$event) { header("Location: index.php"); exit; }
    
    $attachments = AttachmentHelper::get($pdo, 'evento', $id);
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $data_inicio = $_POST['data_evento'] ?: date('Y-m-d');
    $hora_inicio = $_POST['hora_inicio'] ?? '00:00';
    $data_fim = $_POST['data_fim'] ?: $data_inicio;
    $hora_fim = $_POST['hora_fim'] ?? '00:00';
    
    $start_dt = $data_inicio . ' ' . $hora_inicio . ':00';
    $end_dt = $data_fim . ' ' . $hora_fim . ':00';

    $local_ev = $_POST['local_evento'] ?? '';
    $desc = $_POST['descricao'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    $imagem = $event['imagem_destaque'];
    if (isset($_FILES['imagem_destaque']) && $_FILES['imagem_destaque']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../gestao/assets/uploads/files/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['imagem_destaque']['name'], PATHINFO_EXTENSION);
        $new_filename = 'event_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['imagem_destaque']['tmp_name'], $upload_dir . $new_filename)) {
            // Delete old file if exists
            if (!empty($event['imagem_destaque']) && file_exists($upload_dir . $event['imagem_destaque'])) {
                unlink($upload_dir . $event['imagem_destaque']);
            }
            $imagem = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE agenda SET titulo = ?, data_evento = ?, data_fim_evento = ?, hora_inicio = ?, hora_fim = ?, local_evento = ?, descricao = ?, imagem_destaque = ?, ativo = ? WHERE id = ?");
        $stmt->execute([$titulo, $start_dt, $end_dt, $hora_inicio, $hora_fim, $local_ev, $desc, $imagem, $ativo, $id]);
        
        // Handle Multiple Attachments
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'evento', $id, $_FILES['attachments']);
        }

        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}

// Split datetime for UI
$d_ini = date('Y-m-d', strtotime($event['data_evento']));
$h_ini = date('H:i', strtotime($event['data_evento']));
$d_fim = $event['data_fim_evento'] ? date('Y-m-d', strtotime($event['data_fim_evento'])) : $d_ini;
$h_fim = $event['data_fim_evento'] ? date('H:i', strtotime($event['data_fim_evento'])) : '18:00';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Evento</h2>
        <div class="text-muted small">Altere os detalhes do evento #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Titulo do Evento</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($event['titulo']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Detalhes</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="10"><?php echo $event['descricao']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="row g-2 mb-4">
                            <div class="col-7">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data Início</label>
                                <input type="date" name="data_evento" class="form-control border-0" value="<?php echo $d_ini; ?>" required>
                            </div>
                            <div class="col-5">
                                <label class="form-label text-uppercase fw-bold text-muted small">Hora</label>
                                <input type="time" name="hora_inicio" class="form-control border-0" value="<?php echo $h_ini; ?>">
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-7">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data Término</label>
                                <input type="date" name="data_fim" class="form-control border-0" value="<?php echo $d_fim; ?>">
                            </div>
                            <div class="col-5">
                                <label class="form-label text-uppercase fw-bold text-muted small">Hora</label>
                                <input type="time" name="hora_fim" class="form-control border-0" value="<?php echo $h_fim; ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Local / Sala</label>
                            <input type="text" name="local_evento" class="form-control border-0 py-2 small" value="<?php echo $event['local_evento']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir na Agenda?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" <?php $event['ativo'] ? 'checked':''; ?>>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Cartaz Digital (Atual Evento)</label>
                            <img id="preview" src="/oagb/gestao/assets/uploads/files/<?php echo $event['imagem_destaque']; ?>" class="img-fluid rounded shadow-sm mb-3 <?php echo empty($event['imagem_destaque']) ? 'd-none':''; ?>">
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Trocar Cartaz</div>
                                <input type="file" name="imagem_destaque" id="img_input" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <!-- Gallery & Attachments -->
                        <?php 
                        $entity_type = 'evento';
                        $entity_id = $id;
                        require __DIR__ . '/../../includes/partials/attachments_form.php'; 
                        ?>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Gravar Alterações</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Descartar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
    document.getElementById('img_input').onchange = evt => {
        const [file] = document.getElementById('img_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
