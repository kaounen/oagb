<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $autor = trim($_POST['autor'] ?? '');
    $cat = trim($_POST['categoria'] ?? ''); $ano = intval($_POST['ano_publicacao'] ?? 0);
    $resumo = trim($_POST['resumo'] ?? ''); $link = trim($_POST['link_externo'] ?? '');
    $status = $_POST['status'] ?? 'ativo';
    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    if (empty($errors)) {
        $pdo->prepare("INSERT INTO biblioteca_oagb (titulo,autor,categoria,ano_publicacao,resumo,link_externo,status) VALUES (?,?,?,?,?,?,?)")
            ->execute([$titulo,$autor,$cat,$ano?:null,$resumo,$link,$status]);
        header('Location: index.php?msg=added'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Nova Obra</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Autor</label><input type="text" name="autor" class="form-control"></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Categoria</label><input type="text" name="categoria" class="form-control" required placeholder="Ex: Doutrina, Legislação"></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Ano</label><input type="number" name="ano_publicacao" class="form-control"></div>
    <div class="col-md-4"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Link Externo</label><input type="url" name="link_externo" class="form-control"></div>
    <div class="col-12"><label class="form-label fw-bold small">Resumo</label><textarea name="resumo" class="form-control" rows="3"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
