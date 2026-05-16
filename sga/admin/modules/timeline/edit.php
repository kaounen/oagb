<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM timeline_marcos WHERE id = ?");
    $stmt->execute([$id]);
    $marco = $stmt->fetch();
    if (!$marco) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ano = $_POST['ano'];
    $titulo = $_POST['titulo'];
    $desc = $_POST['descricao'];
    $ordem = (int)$_POST['ordem'];
    
    // Handle Image upload
    $img = $marco['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/timeline/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $new_filename = 'tm_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $new_filename)) {
            if ($marco['imagem'] && file_exists($upload_dir . $marco['imagem'])) {
                unlink($upload_dir . $marco['imagem']);
            }
            $img = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE timeline_marcos SET ano = ?, titulo = ?, descricao = ?, imagem = ?, ordem = ? WHERE id = ?");
        $stmt->execute([$ano, $titulo, $desc, $img, $ordem, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Marco Histórico</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Ano / Época</label>
                <input type="text" name="ano" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo htmlspecialchars($marco['ano']); ?>">
            </div>
            <div class="col-md-10">
                <label class="form-label small fw-bold text-muted">Título do Marco Histórico</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo htmlspecialchars($marco['titulo']); ?>">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-muted">Descrição / Contexto Histórico</label>
            <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="10"><?php echo htmlspecialchars($marco['descricao']); ?></textarea>
        </div>

        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted d-block">Ilustração / Foto Histórica</label>
                <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                    <div class="small text-muted">Aperte aqui para alterar a imagem</div>
                    <input type="file" name="imagem" id="img_input" class="d-none" accept="image/*">
                </div>
                <?php if($marco['imagem']): ?>
                    <img id="preview" src="/oagb/uploads/timeline/<?php echo $marco['imagem']; ?>" class="img-fluid mt-3 rounded shadow-sm" style="max-height: 200px;">
                <?php else: ?>
                    <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none" style="max-height: 200px;">
                <?php endif; ?>
            </div>
            
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Ordem</label>
                <input type="number" name="ordem" class="form-control border-0 bg-light p-3" value="<?php echo $marco['ordem']; ?>">
            </div>
            
            <div class="col-md-4 text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">Gravar Alterações</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).catch(e => console.error(e));
    document.getElementById('img_input').onchange = evt => {
        const [file] = document.getElementById('img_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
