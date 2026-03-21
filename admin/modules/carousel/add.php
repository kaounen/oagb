<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Form Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $subtitulo = $_POST['subtitulo'] ?? '';
    $link_url = $_POST['link_url'] ?? '';
    $ordem = (int)$_POST['ordem'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Banner Upload handling
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../img/carousel/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $new_filename = 'slide_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $new_filename)) {
            $imagem = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO carousel_slides (titulo, subtitulo, imagem, link_url, ordem, ativo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $subtitulo, $imagem, $link_url, $ordem, $ativo]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao guardar banner: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Banner (Carousel)</h2>
        <div class="text-muted small">Crie um novo elemento visual de destaque para o topo do portal.</div>
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
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título de Boas-Vindas</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Defesa do Estado de Direito..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Subtítulo / Chamada</label>
                        <textarea name="subtitulo" class="form-control bg-light border-0" rows="3" placeholder="Ex: Ordem dos Advogados da Guiné-Bissau ao serviço da justiça."></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Ordem de Exibição</label>
                            <input type="number" name="ordem" class="form-control border-0" value="1" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Imagem do Banner (High Res)</label>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Aperte aqui para upload</div>
                                <input type="file" name="imagem" id="img_input" class="d-none" accept="image/*">
                            </div>
                            <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Link de Ação (Opcional)</label>
                            <input type="url" name="link_url" class="form-control border-0" placeholder="https://oagb.gw/historia">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir no site?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" checked>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Configurar Banner</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('img_input').onchange = evt => {
        const [file] = document.getElementById('img_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
