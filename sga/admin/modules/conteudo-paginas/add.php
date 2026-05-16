<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagina = trim($_POST['pagina'] ?? ''); $secao = trim($_POST['secao'] ?? '');
    $titulo = trim($_POST['titulo'] ?? ''); $icone = trim($_POST['icone'] ?? 'fas fa-info-circle');
    $conteudo = $_POST['conteudo'] ?? ''; $ordem = intval($_POST['ordem'] ?? 0);
    $status = $_POST['status'] ?? 'ativo';
    
    // File handling
    $imagem = '';
    $arquivo = '';
    $upload_dir = __DIR__ . '/../../../uploads/conteudo/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $img_ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'pg_' . uniqid() . '.' . $img_ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $imagem);
    }
    
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $file_ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
        $arquivo = 'pg_doc_' . uniqid() . '.' . $file_ext;
        $file_dir = __DIR__ . '/../../../uploads/ficheiros/';
        if (!file_exists($file_dir)) mkdir($file_dir, 0777, true);
        move_uploaded_file($_FILES['arquivo']['tmp_name'], $file_dir . $arquivo);
    }

    if (empty($titulo) || empty($pagina)) $errors[] = 'Título e Página são obrigatórios.';
    if (empty($errors)) {
        $pdo->prepare("INSERT INTO conteudos_paginas (pagina,secao,titulo,icone,conteudo,ordem,status,imagem,arquivo) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$pagina,$secao,$titulo,$icone,$conteudo,$ordem,$status,$imagem,$arquivo]);
        header('Location: index.php?msg=added'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Novo Conteúdo de Página</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST" enctype="multipart/form-data"><div class="row g-3">
    <div class="col-md-4"><label class="form-label fw-bold small">Página (slug)</label><input type="text" name="pagina" class="form-control" required placeholder="Ex: centro-estagio"></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Secção (ID ou slug)</label><input type="text" name="secao" class="form-control" placeholder="Ex: requisitos"></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Ícone (FontAwesome)</label><input type="text" name="icone" class="form-control" value="fas fa-info-circle"></div>
    
    <div class="col-md-12"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
    
    <div class="col-md-6"><label class="form-label fw-bold small">Imagem Ilustrativa</label><input type="file" name="imagem" class="form-control" accept="image/*"></div>
    <div class="col-md-6"><label class="form-label fw-bold small">Documento/Anexo (PDF)</label><input type="file" name="arquivo" class="form-control" accept=".pdf"></div>

    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="0"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    
    <div class="col-12"><label class="form-label fw-bold small">Conteúdo (HTML)</label><textarea name="conteudo" id="editor" class="form-control" rows="8"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>ClassicEditor.create(document.querySelector('#editor')).catch(e=>console.error(e));</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
