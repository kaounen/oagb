<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/AttachmentHelper.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';

$id = $_GET['id'] ?? 0;

// ── Handle Attachment Deletion (AJAX) ──────────────────────────────────────────
if (isset($_GET['delete_attachment'])) {
    AttachmentHelper::delete($pdo, $_GET['delete_attachment']);
    header("Location: edit.php?id=$id&att_deleted=1");
    exit;
}

// ── Handle Gallery Image Deletion (AJAX) ───────────────────────────────────────
if (isset($_GET['delete_gallery'])) {
    GalleryHelper::delete($pdo, 'evento', $_GET['delete_gallery']);
    header("Location: edit.php?id=$id&gal_deleted=1");
    exit;
}

// ── Fetch Event ────────────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if (!$event) { header("Location: index.php"); exit; }

    $attachments = AttachmentHelper::get($pdo, 'evento', $id);
    $gallery     = GalleryHelper::get($pdo, 'evento', $id);
} catch (PDOException $e) { header("Location: index.php"); exit; }

// ── Process Form Submission ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $data_inicio = $_POST['data_evento'] ?: date('Y-m-d');
    $hora_inicio = $_POST['hora_inicio'] ?? '00:00';
    $data_fim    = $_POST['data_fim'] ?: $data_inicio;
    $hora_fim    = $_POST['hora_fim'] ?? '00:00';
    $start_dt    = $data_inicio . ' ' . $hora_inicio . ':00';
    $end_dt      = $data_fim   . ' ' . $hora_fim   . ':00';
    $local_ev    = $_POST['local_evento'] ?? '';
    $desc        = $_POST['descricao'] ?? '';
    $legenda     = trim($_POST['legendaFoto1'] ?? '');
    $ativo       = isset($_POST['ativo']) ? 1 : 0;

    // ── Imagem de Destaque ────────────────────────────────────────────────────
    $imagem = $event['imagem_destaque']; // keep existing by default
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/agenda/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        // Delete old image
        if (!empty($event['imagem_destaque']) && file_exists($upload_dir . $event['imagem_destaque'])) {
            unlink($upload_dir . $event['imagem_destaque']);
        }
        $ext    = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $imagem = uniqid('evento_') . '.' . $ext;
        move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $imagem);
    }

    // ── PDF Quick Download ────────────────────────────────────────────────────
    $legenda_pdf  = $_POST['legenda_anexo'] ?? '';
    $pdf_col_sql  = ", legenda_anexo = ?";
    $pdf_params   = [$legenda_pdf];

    if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        $safe_name  = preg_replace('/[^A-Za-z0-9.\-_]/', '_', basename($_FILES['pdf_anexo']['name']));
        $pdf_name   = time() . '_' . $safe_name;

        if (move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name)) {
            // Delete old PDF
            if (!empty($event['ficheiro_anexo']) && file_exists($upload_dir . $event['ficheiro_anexo'])) {
                unlink($upload_dir . $event['ficheiro_anexo']);
            }
            $pdf_col_sql = ", ficheiro_anexo = ?, legenda_anexo = ?";
            $pdf_params  = [$pdf_name, $legenda_pdf];
        }
    }

    try {
        $stmt = $pdo->prepare(
            "UPDATE agenda SET titulo = ?, data_evento = ?, data_fim_evento = ?, hora_inicio = ?,
             hora_fim = ?, local_evento = ?, descricao = ?, imagem_destaque = ?, resumo = ?,
             ativo = ? $pdf_col_sql WHERE id = ?"
        );
        $all_params = array_merge(
            [$titulo, $start_dt, $end_dt, $hora_inicio, $hora_fim, $local_ev, $desc, $imagem, $legenda, $ativo],
            $pdf_params,
            [$id]
        );
        $stmt->execute($all_params);

        // ── Múltiplos Anexos ──────────────────────────────────────────────────
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'evento', $id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        }
        if (isset($_POST['att_desc'])) {
            foreach ($_POST['att_desc'] as $att_id => $desc_meta) {
                AttachmentHelper::update($pdo, $att_id, $desc_meta);
            }
        }

        // ── Galeria de Imagens ────────────────────────────────────────────────
        if (isset($_FILES['gallery_files'])) {
            GalleryHelper::save($pdo, 'evento', $id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        }
        if (isset($_POST['gal_title'])) {
            foreach ($_POST['gal_title'] as $img_id => $gal_title_val) {
                $desc_meta = $_POST['gal_desc'][$img_id] ?? '';
                $order     = $_POST['gal_order'][$img_id] ?? 0;
                GalleryHelper::update($pdo, 'evento', $img_id, $gal_title_val, $desc_meta, $order);
            }
        }

        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'AGENDA_UPDATE', "Editou evento '$titulo' (#$id)", 'agenda', $id);

        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao atualizar: " . $e->getMessage();
    }
}

// ── Split datetime for UI ──────────────────────────────────────────────────────
$d_ini = date('Y-m-d', strtotime($event['data_evento']));
$h_ini = date('H:i',   strtotime($event['data_evento']));
$d_fim = $event['data_fim_evento'] ? date('Y-m-d', strtotime($event['data_fim_evento'])) : $d_ini;
$h_fim = $event['data_fim_evento'] ? date('H:i',   strtotime($event['data_fim_evento'])) : '18:00';

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
        <?php if (isset($error)): ?>
            <div class="alert alert-danger px-4 py-3 border-0 bg-danger-subtle text-danger small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- ── Left: Título + Descrição ───────────────────────── -->
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título do Evento</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($event['titulo']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Detalhes</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="12"><?php echo $event['descricao']; ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Local do Evento</label>
                        <input type="text" name="local_evento" class="form-control bg-light border-0 py-3" value="<?php echo htmlspecialchars($event['local_evento'] ?? ''); ?>" placeholder="Ex: Auditório Principal da OAGB / Zoom">
                    </div>
                </div>

                <!-- ── Right: Sidebar Settings ───────────────────────── -->
                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">

                        <!-- Datas / Horas -->
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

                        <!-- Local / Visibilidade -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir na Agenda?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" <?php echo $event['ativo'] ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <!-- Imagem de Destaque -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Imagem de Destaque (Principal)</label>
                            <div class="position-relative mb-3">
                                <img id="preview"
                                     src="<?php echo !empty($event['imagem_destaque']) ? '/oagb/uploads/agenda/' . $event['imagem_destaque'] : ''; ?>"
                                     class="img-fluid rounded shadow-sm <?php echo empty($event['imagem_destaque']) ? 'd-none' : ''; ?>">
                                <?php if (!empty($event['imagem_destaque'])): ?>
                                    <a href="javascript:void(0);"
                                       class="btn btn-danger btn-sm position-absolute"
                                       style="top:10px;right:10px;z-index:10;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;"
                                       onclick="deleteMedia(<?php echo $id; ?>, 'highlight_evento', this);">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('foto1_input').click();" style="border-style:dashed!important">
                                <i class="fas fa-<?php echo empty($event['imagem_destaque']) ? 'cloud-upload-alt' : 'sync-alt'; ?> fa-2x text-muted mb-2"></i>
                                <div class="small text-muted"><?php echo empty($event['imagem_destaque']) ? 'Aperte aqui para carregar' : 'Trocar Imagem'; ?></div>
                                <input type="file" name="foto1" id="foto1_input" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <!-- Legenda / Resumo -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Legenda da Imagem / Resumo</label>
                            <input type="text" name="legendaFoto1" class="form-control border-0 bg-white"
                                   placeholder="Opcional..."
                                   value="<?php echo htmlspecialchars($event['resumo'] ?? ''); ?>">
                        </div>

                        <!-- PDF Quick Download -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF (Quick Download)</label>
                            <div class="p-3 border rounded bg-white shadow-sm mb-2">
                                <?php if (!empty($event['ficheiro_anexo'])): ?>
                                    <div class="d-flex align-items-center mb-3 p-2 bg-light rounded border position-relative">
                                        <div class="me-3"><i class="far fa-file-pdf fa-2x text-danger"></i></div>
                                        <div class="flex-grow-1 min-width-0" style="overflow:hidden">
                                            <div class="small fw-bold text-truncate" style="max-width:100%"><?php echo htmlspecialchars(preg_replace('/^\d+_/', '', $event['ficheiro_anexo'])); ?></div>
                                            <a href="/oagb/uploads/<?php echo $event['ficheiro_anexo']; ?>" target="_blank" class="x-small text-primary text-decoration-none">Ver Ficheiro Atual</a>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger border-0"
                                               onclick="deleteMedia(<?php echo $id; ?>, 'quick_pdf_evento', this); document.querySelector('[name=legenda_anexo]').value='';">
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
                                    <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light"
                                           placeholder="Ex: Programa do Evento"
                                           value="<?php echo htmlspecialchars($event['legenda_anexo'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Galeria de Imagens -->
                        <?php
                        $type    = 'evento';
                        $gallery = GalleryHelper::get($pdo, 'evento', $id);
                        require __DIR__ . '/../../includes/partials/gallery_form.php';
                        ?>

                        <!-- Múltiplos Anexos -->
                        <?php
                        $entity_type = 'evento';
                        $entity_id   = $id;
                        require __DIR__ . '/../../includes/partials/attachments_form.php';
                        ?>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Gravar Alterações</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Descartar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).catch(e => console.error(e));

    document.getElementById('foto1_input').onchange = evt => {
        const [file] = document.getElementById('foto1_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    };
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
