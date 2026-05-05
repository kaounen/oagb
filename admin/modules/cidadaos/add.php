<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $slug = trim($_POST['slug'] ?? '');
    $icone = trim($_POST['icone'] ?? 'fas fa-info-circle'); $conteudo = $_POST['conteudo'] ?? '';
    $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
    if (empty($slug)) $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT',$titulo)));
    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    if (empty($errors)) {
        $pdo->prepare("INSERT INTO info_cidadaos (titulo,slug,icone,conteudo,ordem,status) VALUES (?,?,?,?,?,?)")
            ->execute([$titulo,$slug,$icone,$conteudo,$ordem,$status]);
        header('Location: index.php?msg=added'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Nova Secção — Cidadãos</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Slug</label><input type="text" name="slug" class="form-control" placeholder="auto-gerado"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Ícone (FontAwesome)</label><input type="text" name="icone" class="form-control" value="fas fa-info-circle"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="0"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Conteúdo (HTML)</label><textarea name="conteudo" class="form-control" rows="8"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
