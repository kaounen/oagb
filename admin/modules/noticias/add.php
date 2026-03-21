<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $data = $_POST['data'] ?? date('Y-m-d');
    $corpo = $_POST['corpo'] ?? '';
    $legendaFoto1 = $_POST['legendaFoto1'] ?? '';
    
    // Simple photo upload handling for the first image
    $foto1 = '';
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../gestao/assets/uploads/files/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('news_') . '.' . $file_ext;
        

        if (move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $new_filename)) {
            $imagem = $new_filename;
        }
    }

    // Default values for new fields not present in the form
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $autor = $_SESSION['user_id'] ?? 1; // Assuming user_id is available in session, default to 1
    $status = 'publicado'; // Default status

    try {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, conteudo, data_publicacao, data_criacao, imagem, legenda_imagem, slug, autor, status) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $conteudo, $data_pub, $imagem, $legenda, $slug, $autor, $status]);
        $new_id = $pdo->lastInsertId();

        // LOG ACTION
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::create($pdo, 'noticias', $new_id, $titulo);

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao guardar notícia: " . $e->getMessage();
    }
}
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
                                <label class="form-label text-uppercase fw-bold text-muted small">Legenda da Imagem</label>
                                <input type="text" name="legendaFoto1" class="form-control border-0" placeholder="Opcional...">
                            </div>

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
