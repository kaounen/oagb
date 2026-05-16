<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM glossario_juridico WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch();
if (!$item) { echo '<div class="alert alert-danger">Não encontrado.</div>'; require_once __DIR__.'/../../includes/footer.php'; exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $termo = trim($_POST['termo'] ?? ''); $definicao = trim($_POST['definicao'] ?? '');
    $exemplo = trim($_POST['exemplo_uso'] ?? ''); $cat = $_POST['categoria'] ?? 'Geral';
    $letra = mb_strtoupper(mb_substr($termo, 0, 1)); $status = $_POST['status'] ?? 'ativo';
    if (empty($termo)) $errors[] = 'Termo obrigatório.';
    if (empty($errors)) {
        $pdo->prepare("UPDATE glossario_juridico SET termo=?,letra=?,definicao=?,exemplo_uso=?,categoria=?,status=? WHERE id=?")
            ->execute([$termo,$letra,$definicao,$exemplo,$cat,$status,$id]);
        header('Location: index.php?msg=updated'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Editar Termo #<?php echo $id; ?></h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold small">Termo</label><input type="text" name="termo" class="form-control" required value="<?php echo htmlspecialchars($item->termo); ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Categoria</label><select name="categoria" class="form-select"><option <?php echo $item->categoria==='Geral'?'selected':''; ?>>Geral</option><option <?php echo $item->categoria==='Latinismo'?'selected':''; ?>>Latinismo</option><option <?php echo $item->categoria==='Expressao'?'selected':''; ?>>Expressao</option></select></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo" <?php echo $item->status==='ativo'?'selected':''; ?>>Ativo</option><option value="inativo" <?php echo $item->status==='inativo'?'selected':''; ?>>Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Definição</label><textarea name="definicao" class="form-control" rows="3" required><?php echo htmlspecialchars($item->definicao); ?></textarea></div>
    <div class="col-12"><label class="form-label fw-bold small">Exemplo de Uso</label><textarea name="exemplo_uso" class="form-control" rows="2"><?php echo htmlspecialchars($item->exemplo_uso); ?></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
