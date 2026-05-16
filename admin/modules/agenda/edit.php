<?php
require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/AttachmentHelper.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';

// Handle Attachment Deletion
if (isset($_GET['delete_attachment'])) {
    AttachmentHelper::delete($pdo, $_GET['delete_attachment']);
    header("Location: edit.php?id=" . $id . "&att_deleted=1");
    exit;
}

// Handle Gallery Deletion
if (isset($_GET['delete_gallery'])) {
    GalleryHelper::delete($pdo, 'evento', $_GET['delete_gallery']);
    header("Location: edit.php?id=" . $id . "&gal_deleted=1");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if(!$event) { header("Location: index.php"); exit; }
    
    $attachments = AttachmentHelper::get($pdo, 'evento', $id);
    $gallery = GalleryHelper::get($pdo, 'evento', $id);
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
    
        }
    }

    // Handle Quick PDF Attachment (ficheiro_anexo column if exists)
    $legenda_pdf = $_POST['legenda_anexo'] ?? '';
    $pdf_col_sql = "";
    $pdf_params = [];
    if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        $original_name = basename($_FILES['pdf_anexo']['name']);
        $safe_name = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $original_name);
        $pdf_name = time() . '_' . $safe_name;
        
        if (move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name)) {
            // Eliminar o PDF antigo se existir
            if (!empty($event['ficheiro_anexo']) && file_exists($upload_dir . $event['ficheiro_anexo'])) {
                unlink($upload_dir . $event['ficheiro_anexo']);
            }
            $pdf_col_sql = ", ficheiro_anexo = ?, legenda_anexo = ?";
            $pdf_params = [$pdf_name, $legenda_pdf];
        }
    } else {
        $pdf_col_sql = ", legenda_anexo = ?";
        $pdf_params = [$legenda_pdf];
    }

    try {
        $stmt = $pdo->prepare("UPDATE agenda SET titulo = ?, data_evento = ?, data_fim_evento = ?, hora_inicio = ?, hora_fim = ?, local_evento = ?, descricao = ?, imagem_destaque = ?, ativo = ? $pdf_col_sql WHERE id = ?");
        $all_params = array_merge([$titulo, $start_dt, $end_dt, $hora_inicio, $hora_fim, $local_ev, $desc, $imagem, $ativo], $pdf_params, [$id]);
        $stmt->execute($all_params);
        
        // Handle Multiple Attachments
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'evento', $id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        }

        // Update existing attachments metadata
        if (isset($_POST['att_desc'])) {
            foreach ($_POST['att_desc'] as $att_id => $desc_meta) {
                AttachmentHelper::update($pdo, $att_id, $desc_meta);
            }
        }

        // Handle Gallery Uploads
        if (isset($_FILES['gallery_files'])) {
            GalleryHelper::save($pdo, 'evento', $id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        }

        // Update Gallery Metadata
        if (isset($_POST['gal_title'])) {
            foreach ($_POST['gal_title'] as $img_id => $title) {
                $desc_meta = $_POST['gal_desc'][$img_id] ?? '';
                $order = $_POST['gal_order'][$img_id] ?? 0;
                GalleryHelper::update($pdo, 'evento', $img_id, $title, $desc_meta, $order);
            }
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
require_once __DIR__ . '/../../includes/header.php';
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
                            <div class="position-relative mb-3">
                                <img id="preview" src="/oagb/uploads/<?php echo $event['imagem_destaque']; ?>" class="img-fluid rounded shadow-sm <?php echo empty($event['imagem_destaque']) ? 'd-none':''; ?>">
                                <?php if(!empty($event['imagem_destaque'])): ?>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteMedia(<?php echo $id; ?>, 'highlight_evento', this);">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Trocar Cartaz</div>
                                <input type="file" name="imagem_destaque" id="img_input" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF (Quick Download)</label>
                            <div class="p-3 border rounded bg-white shadow-sm mb-2">
                                <?php if(!empty($event['ficheiro_anexo'])): ?>
                                    <div class="d-flex align-items-center mb-3 p-2 bg-light rounded border position-relative">
                                        <div class="me-3">
                                            <i class="far fa-file-pdf fa-2x text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0" style="overflow: hidden;">
                                            <div class="small fw-bold text-truncate" title="<?php echo htmlspecialchars($event['ficheiro_anexo']); ?>" style="max-width: 100%;"><?php echo htmlspecialchars(preg_replace('/^[0-9]+_/', '', $event['ficheiro_anexo'])); ?></div>
                                            <a href="/oagb/uploads/<?php echo $event['ficheiro_anexo']; ?>?v=<?php echo time(); ?>" target="_blank" class="x-small text-primary text-decoration-none">Ver Ficheiro Atual</a>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger border-0" onclick="deleteMedia(<?php echo $id; ?>, 'quick_pdf_evento', this); document.querySelector('[name=legenda_anexo]').value='';">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Substituir ou Carregar Novo:</label>
                                    <input type="file" name="pdf_anexo" class="form-control form-control-sm border-0 bg-light" accept=".pdf">
                                </div>
                                
                                <div>
                                    <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Título/Legenda do PDF:</label>
                                    <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light" placeholder="Ex: Programa do Evento" value="<?php echo htmlspecialchars($event['legenda_anexo'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Galeria Slider Component -->
                        <?php 
                        $type = 'evento';
                        $gallery = GalleryHelper::get($pdo, 'evento', $id);
                        require __DIR__ . '/../../includes/partials/gallery_form.php'; 
                        ?>

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
