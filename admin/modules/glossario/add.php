<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $termo = trim($_POST['termo'] ?? ''); $definicao = trim($_POST['definicao'] ?? '');
    $exemplo = trim($_POST['exemplo_uso'] ?? ''); $cat = $_POST['categoria'] ?? 'Geral';
    $letra = mb_strtoupper(mb_substr($termo, 0, 1)); $status = $_POST['status'] ?? 'ativo';
    if (empty($termo)) $errors[] = 'Termo obrigatório.';
    if (empty($definicao)) $errors[] = 'Definição obrigatória.';
    if (empty($errors)) {
        $pdo->prepare("INSERT INTO glossario_juridico (termo,letra,definicao,exemplo_uso,categoria,status) VALUES (?,?,?,?,?,?)")
            ->execute([$termo,$letra,$definicao,$exemplo,$cat,$status]);
        header('Location: index.php?msg=added'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Novo Termo</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold small">Termo</label><input type="text" name="termo" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Categoria</label><select name="categoria" class="form-select"><option>Geral</option><option>Latinismo</option><option>Expressao</option></select></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Definição</label><textarea name="definicao" class="form-control" rows="3" required></textarea></div>
    <div class="col-12"><label class="form-label fw-bold small">Exemplo de Uso</label><textarea name="exemplo_uso" class="form-control" rows="2"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
