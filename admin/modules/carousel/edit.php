<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM carousel_slides WHERE id = ?");
    $stmt->execute([$id]);
    $slide = $stmt->fetch();
    if(!$slide) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $subtitulo = $_POST['subtitulo'] ?? '';
    $link_url = $_POST['link_url'] ?? '';
    $ordem = (int)$_POST['ordem'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    $imagem = $slide['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../img/carousel/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['imagem']['name'], INFO_EXTENSION);
        $new_filename = 'slide_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $new_filename)) {
            // Delete old file if exists
            if (!empty($slide['imagem']) && file_exists($upload_dir . $slide['imagem'])) {
                unlink($upload_dir . $slide['imagem']);
            }
            $imagem = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE carousel_slides SET titulo = ?, subtitulo = ?, imagem = ?, link_url = ?, ordem = ?, ativo = ? WHERE id = ?");
        $stmt->execute([$titulo, $subtitulo, $imagem, $link_url, $ordem, $ativo, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Banner</h2>
        <div class="text-muted small">Altere a composição visual do banner #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título de Boas-Vindas</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($slide['titulo']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Subtítulo / Chamada</label>
                        <textarea name="subtitulo" class="form-control bg-light border-0" rows="3"><?php echo $slide['subtitulo']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Ordem de Exibição</label>
                            <input type="number" name="ordem" class="form-control border-0" value="<?php echo $slide['ordem']; ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Imagem Digital (Atual Banner)</label>
                            <img id="preview" src="/oagb/img/carousel/<?php echo $slide['imagem']; ?>" class="img-fluid rounded shadow-sm mb-3">
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Trocar Fichário Digital</div>
                                <input type="file" name="imagem" id="img_input" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Link de Ação (Opcional)</label>
                            <input type="url" name="link_url" class="form-control border-0" value="<?php echo $slide['link_url']; ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir no site?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" <?php echo $slide['ativo'] ? 'checked':''; ?>>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Gravar Alterações</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Descartar</a>
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
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
