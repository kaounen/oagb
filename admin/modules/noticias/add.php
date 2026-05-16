<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';
require_once __DIR__ . '/../../includes/AttachmentHelper.php';

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $data_pub = $_POST['data'] . ' ' . date('H:i:s');
    $conteudo = $_POST['corpo'] ?? '';
    $legenda = $_POST['legendaFoto1'] ?? '';
    $cat_tipo = $_POST['categoria_tipo'] ?? 'Notícia';
    
    // Image handling
    $imagem = '';
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('news_') . '.' . $file_ext;

        if (move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $new_filename)) {
            $imagem = $new_filename;
        }
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $autor = $_SESSION['admin_name'] ?? 'Admin';
    $status = 1; // Ativo

    try {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, conteudo, data_publicacao, imagem_destaque, resumo, categoria_tipo, slug, autor, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $conteudo, $data_pub, $imagem, $legenda, $cat_tipo, $slug, $autor, $status]);
        $new_id = $pdo->lastInsertId();

        // Handle Quick PDF Attachment (if exists)
        $legenda_pdf = $_POST['legenda_anexo'] ?? '';
        if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../../uploads/';
            $original_name = basename($_FILES['pdf_anexo']['name']);
            $safe_name = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $original_name);
            $pdf_name = time() . '_' . $safe_name;
            if (move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name)) {
                $pdo->prepare("UPDATE noticias SET ficheiro_anexo = ?, legenda_anexo = ? WHERE id = ?")->execute([$pdf_name, $legenda_pdf, $new_id]);
            }
        }

        // Handle Multiple Attachments
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'noticia', $new_id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        }

        // Handle Gallery Uploads
        if (isset($_FILES['gallery_files'])) {
            GalleryHelper::save($pdo, 'noticia', $new_id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        }

        // LOG ACTION
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::create($pdo, 'noticias', $new_id, $titulo);

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao guardar notícia: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Publicar Notícia</h2>
        <div class="text-muted small">Crie um novo artigo informativo para o portal OAGB.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Title & Content -->
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título da Notícia</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Insira o título principal..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Corpo do Artigo / Texto Completo</label>
                        <textarea name="corpo" id="editor" class="form-control bg-light border-0" rows="15"></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Sidebar Settings -->
                    <div class="card bg-light border-0">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Conteúdo</label>
                                <select name="categoria_tipo" class="form-select border-0 shadow-sm py-2">
                                    <option value="Notícia">Notícia / Artigo</option>
                                    <option value="Anúncio">Anúncio Oficial</option>
                                    <option value="Aviso">Aviso / Nota</option>
                                    <option value="Edital">Edital / Concurso</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data de Publicação</label>
                                <input type="date" name="data" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Imagem de Destaque (Principal)</label>
                                <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('foto1_input').click();">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <div class="small text-muted">Aperte aqui para carregar</div>
                                    <input type="file" name="foto1" id="foto1_input" class="d-none" accept="image/*">
                                </div>
                                <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Legenda da Imagem / Resumo</label>
                                <input type="text" name="legendaFoto1" class="form-control border-0 bg-light" placeholder="Opcional...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF (Quick Download)</label>
                                <div class="p-3 border rounded bg-white shadow-sm mb-2">
                                    <div class="mb-3">
                                        <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Carregar Novo PDF:</label>
                                        <input type="file" name="pdf_anexo" class="form-control form-control-sm border-0 bg-light" accept=".pdf">
                                    </div>
                                    <div>
                                        <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Título/Legenda do PDF:</label>
                                        <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light" placeholder="Ex: Baixar Edital Completo">
                                    </div>
                                </div>
                            </div>

                            <!-- Galeria Slider Component -->
                            <?php 
                            $type = 'noticia';
                            $gallery = []; // Vazia na criação
                            require __DIR__ . '/../../includes/partials/gallery_form.php'; 
                            ?>

                            <!-- Múltiplos Anexos Component -->
                            <?php 
                            $entity_type = 'noticia';
                            $entity_id = 0;
                            require __DIR__ . '/../../includes/partials/attachments_form.php'; 
                            ?>

                            <hr class="my-4">

                            <button type="submit" class="btn btn-login w-100 py-3 shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Publicar Artigo
                            </button>
                            <a href="index.php" class="btn btn-light w-100 mt-2 py-3 border">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Scripts for Editor & Preview -->
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    // Initialize Editor
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });

    // Image Preview
    document.getElementById('foto1_input').onchange = evt => {
        const [file] = document.getElementById('foto1_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
