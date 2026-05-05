<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM info_cidadaos WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch();
if (!$item) { echo '<div class="alert alert-danger">Não encontrado.</div>'; require_once __DIR__.'/../../includes/footer.php'; exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $slug = trim($_POST['slug'] ?? '');
    $icone = trim($_POST['icone'] ?? 'fas fa-info-circle'); $conteudo = $_POST['conteudo'] ?? '';
    $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    if (empty($errors)) {
        $pdo->prepare("UPDATE info_cidadaos SET titulo=?,slug=?,icone=?,conteudo=?,ordem=?,status=? WHERE id=?")
            ->execute([$titulo,$slug,$icone,$conteudo,$ordem,$status,$id]);
        header('Location: index.php?msg=updated'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Editar Secção #<?php echo $id; ?></h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item->titulo); ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($item->slug); ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Ícone</label><input type="text" name="icone" class="form-control" value="<?php echo htmlspecialchars($item->icone); ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="<?php echo $item->ordem; ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo" <?php echo $item->status==='ativo'?'selected':''; ?>>Ativo</option><option value="inativo" <?php echo $item->status==='inativo'?'selected':''; ?>>Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Conteúdo (HTML)</label><textarea name="conteudo" class="form-control" rows="8"><?php echo htmlspecialchars($item->conteudo); ?></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
