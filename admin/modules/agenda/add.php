<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';
require_once __DIR__ . '/../../includes/AttachmentHelper.php';

// Process Form Submission
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
    $imagem = NULL;
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/agenda/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext    = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $imagem = uniqid('evento_') . '.' . $ext;
        move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $imagem);
    }

    // ── PDF Quick Download ────────────────────────────────────────────────────
    $legenda_pdf = $_POST['legenda_anexo'] ?? '';
    $pdf_name    = NULL;
    if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir  = __DIR__ . '/../../../uploads/';
        $safe_name   = preg_replace('/[^A-Za-z0-9.\-_]/', '_', basename($_FILES['pdf_anexo']['name']));
        $pdf_name    = time() . '_' . $safe_name;
        move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name);
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO agenda (titulo, data_evento, data_fim_evento, hora_inicio, hora_fim,
             local_evento, descricao, imagem_destaque, resumo, ficheiro_anexo, legenda_anexo, ativo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $titulo, $start_dt, $end_dt, $hora_inicio, $hora_fim,
            $local_ev, $desc, $imagem, $legenda, $pdf_name, $legenda_pdf, $ativo
        ]);
        $new_id = $pdo->lastInsertId();

        // ── Múltiplos Anexos ──────────────────────────────────────────────────
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'evento', $new_id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        }

        // ── Galeria de Imagens ────────────────────────────────────────────────
        if (isset($_FILES['gallery_files'])) {
            GalleryHelper::save($pdo, 'evento', $new_id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        }

        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::create($pdo, 'agenda', $new_id, $titulo);

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao guardar evento: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Evento / Agenda</h2>
        <div class="text-muted small">Crie e divulgue acontecimentos institucionais no portal.</div>
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
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Conferência sobre Direito Digital..." required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Detalhes</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="12"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Local do Evento</label>
                        <input type="text" name="local_evento" class="form-control bg-light border-0 py-3" placeholder="Ex: Auditório Principal da OAGB / Zoom">
                    </div>
                </div>

                <!-- ── Right: Sidebar Settings ───────────────────────── -->
                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">

                        <!-- Datas / Horas -->
                        <div class="row g-2 mb-4">
                            <div class="col-7">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data Início</label>
                                <input type="date" name="data_evento" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-5">
                                <label class="form-label text-uppercase fw-bold text-muted small">Hora</label>
                                <input type="time" name="hora_inicio" class="form-control border-0" value="09:00">
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-7">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data Término</label>
                                <input type="date" name="data_fim" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-5">
                                <label class="form-label text-uppercase fw-bold text-muted small">Hora</label>
                                <input type="time" name="hora_fim" class="form-control border-0" value="18:00">
                            </div>
                        </div>

                        <!-- Visibilidade -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir na Agenda?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" checked>
                            </div>
                        </div>

                        <!-- Imagem de Destaque -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Imagem de Destaque (Principal)</label>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('foto1_input').click();" style="border-style:dashed!important">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Aperte aqui para carregar</div>
                                <input type="file" name="foto1" id="foto1_input" class="d-none" accept="image/*">
                            </div>
                            <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                        </div>

                        <!-- Legenda / Resumo -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Legenda da Imagem / Resumo</label>
                            <input type="text" name="legendaFoto1" class="form-control border-0 bg-white" placeholder="Opcional...">
                        </div>

                        <!-- PDF Quick Download -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF (Quick Download)</label>
                            <div class="p-3 border rounded bg-white shadow-sm mb-2">
                                <div class="mb-3">
                                    <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Carregar Novo PDF:</label>
                                    <input type="file" name="pdf_anexo" class="form-control form-control-sm border-0 bg-light" accept=".pdf">
                                </div>
                                <div>
                                    <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Título/Legenda do PDF:</label>
                                    <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light" placeholder="Ex: Programa do Evento">
                                </div>
                            </div>
                        </div>

                        <!-- Galeria de Imagens -->
                        <?php
                        $type    = 'evento';
                        $gallery = [];
                        require __DIR__ . '/../../includes/partials/gallery_form.php';
                        ?>

                        <!-- Múltiplos Anexos -->
                        <?php
                        $entity_type = 'evento';
                        $entity_id   = 0;
                        require __DIR__ . '/../../includes/partials/attachments_form.php';
                        ?>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> Publicar na Agenda
                        </button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Cancelar</a>
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
