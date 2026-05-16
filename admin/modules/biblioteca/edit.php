<?php
require_once __DIR__ . '/../../includes/db.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM biblioteca_oagb WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo '<div class="alert alert-danger">Não encontrado.</div>';
    require_once __DIR__.'/../../includes/footer.php';
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $cat = trim($_POST['categoria'] ?? '');
    $ano = intval($_POST['ano_publicacao'] ?? 0);
    $resumo = $_POST['resumo'] ?? '';
    $link = trim($_POST['link_externo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    
    $capa = $item['capa'];
    $ficheiro = $item['ficheiro'];

    $upload_dir = __DIR__ . '/../../../uploads/biblioteca/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // Handle Cover Image
    if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
        $new_capa = 'capa_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['capa']['tmp_name'], $upload_dir . $new_capa)) {
            if (!empty($capa) && file_exists($upload_dir . $capa)) unlink($upload_dir . $capa);
            $capa = $new_capa;
        }
    }

    // Handle File Attachment
    if (isset($_FILES['ficheiro']) && $_FILES['ficheiro']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['ficheiro']['name'], PATHINFO_EXTENSION);
        $new_file = 'doc_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['ficheiro']['tmp_name'], $upload_dir . $new_file)) {
            if (!empty($ficheiro) && file_exists($upload_dir . $ficheiro)) unlink($upload_dir . $ficheiro);
            $ficheiro = $new_file;
        }
    }

    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    
    if (empty($errors)) {
        $pdo->prepare("UPDATE biblioteca_oagb SET titulo=?, autor=?, categoria=?, capa=?, ficheiro=?, ano_publicacao=?, resumo=?, link_externo=?, status=?, destaque=? WHERE id=?")
            ->execute([$titulo, $autor, $cat, $capa, $ficheiro, $ano ?: null, $resumo, $link, $status, $destaque, $id]);
        header('Location: index.php?msg=updated');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="row mb-4">
    <div class="col">
        <h2 class="page-title">Editar Obra #<?php echo $id; ?></h2>
        <a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
    </div>
</div>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold small">Título</label>
                    <input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item['titulo']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Autor</label>
                    <input type="text" name="autor" class="form-control" value="<?php echo htmlspecialchars($item['autor'] ?? ''); ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Categoria</label>
                    <input type="text" name="categoria" class="form-control" required value="<?php echo htmlspecialchars($item['categoria']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Ano</label>
                    <input type="number" name="ano_publicacao" class="form-control" value="<?php echo $item['ano_publicacao']; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Estado</label>
                    <select name="status" class="form-select">
                        <option value="ativo" <?php echo ($item['status'] ?? 'ativo') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($item['status'] ?? '') === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="destaque" id="destaqueCheck" <?php echo ($item['destaque'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold small" for="destaqueCheck">Marcar como Obra em Destaque</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">Capa da Obra (Imagem)</label>
                    <input type="file" name="capa" class="form-control" accept="image/*" id="capaInput">
                    <?php if(!empty($item['capa'])): ?>
                        <div class="mt-2">
                            <img src="/oagb/uploads/biblioteca/<?php echo $item['capa']; ?>" id="capaPreview" style="height: 100px; border-radius: 8px;" class="shadow-sm">
                        </div>
                    <?php else: ?>
                        <div class="mt-2">
                            <img id="capaPreview" style="height: 100px; border-radius: 8px; display: none;" class="shadow-sm">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Ficheiro (PDF/Documento)</label>
                    <input type="file" name="ficheiro" class="form-control" accept=".pdf,.doc,.docx">
                    <?php if(!empty($item['ficheiro'])): ?>
                        <div class="mt-2 small text-muted">
                            <i class="fas fa-file-pdf text-danger me-1"></i> <?php echo $item['ficheiro']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small">Link Externo (Opcional)</label>
                    <input type="url" name="link_externo" class="form-control" value="<?php echo htmlspecialchars($item['link_externo'] ?? ''); ?>">
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold small">Resumo / Descrição</label>
                    <textarea name="resumo" id="editor" class="form-control" rows="5"><?php echo $item['resumo']; ?></textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-login px-5 py-2"><i class="fas fa-save me-2"></i>Guardar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).catch(error => console.error(error));

    document.getElementById('capaInput').onchange = evt => {
        const [file] = document.getElementById('capaInput').files;
        if (file) {
            const preview = document.getElementById('capaPreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

