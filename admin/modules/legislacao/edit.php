<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$tipo = $_GET['tipo'] ?? 'nacional'; $id = intval($_GET['id'] ?? 0);
$table = $tipo === 'internacional' ? 'legislacao_internacional' : 'legislacao_nacional';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch();
if (!$item) { echo '<div class="alert alert-danger">Não encontrado.</div>'; require_once __DIR__.'/../../includes/footer.php'; exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $resumo = trim($_POST['resumo'] ?? '');
    $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    if (empty($errors)) {
        if ($tipo === 'internacional') {
            $pdo->prepare("UPDATE legislacao_internacional SET organizacao=?,titulo=?,data_adocao=?,data_ratificacao_gb=?,resumo=?,link_externo=?,ordem=?,status=? WHERE id=?")
                ->execute([$_POST['organizacao'],$titulo,$_POST['data_adocao']?:null,$_POST['data_ratificacao_gb']?:null,$resumo,$_POST['link_externo']??'',$ordem,$status,$id]);
        } else {
            $pdo->prepare("UPDATE legislacao_nacional SET categoria=?,titulo=?,diploma_legal=?,data_publicacao=?,resumo=?,ordem=?,status=? WHERE id=?")
                ->execute([$_POST['categoria'],$titulo,$_POST['diploma_legal']??'',$_POST['data_publicacao']?:null,$resumo,$ordem,$status,$id]);
        }
        header("Location: index.php?tipo=$tipo&msg=updated"); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Editar #<?php echo $id; ?> (<?php echo ucfirst($tipo); ?>)</h2><a href="index.php?tipo=<?php echo $tipo; ?>" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <?php if($tipo==='internacional'): ?>
        <div class="col-md-4"><label class="form-label fw-bold small">Organização</label><select name="organizacao" class="form-select"><?php foreach(['OHADA','CEDEAO','União Africana','CPLP','Direitos Humanos'] as $o): ?><option <?php echo $item->organizacao===$o?'selected':''; ?>><?php echo $o; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item->titulo); ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Data Adoção</label><input type="date" name="data_adocao" class="form-control" value="<?php echo $item->data_adocao; ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Data Ratificação GB</label><input type="date" name="data_ratificacao_gb" class="form-control" value="<?php echo $item->data_ratificacao_gb; ?>"></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Link Externo</label><input type="url" name="link_externo" class="form-control" value="<?php echo htmlspecialchars($item->link_externo); ?>"></div>
    <?php else: ?>
        <div class="col-md-4"><label class="form-label fw-bold small">Categoria</label><input type="text" name="categoria" class="form-control" required value="<?php echo htmlspecialchars($item->categoria); ?>"></div>
        <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item->titulo); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold small">Diploma Legal</label><input type="text" name="diploma_legal" class="form-control" value="<?php echo htmlspecialchars($item->diploma_legal); ?>"></div>
        <div class="col-md-6"><label class="form-label fw-bold small">Data</label><input type="date" name="data_publicacao" class="form-control" value="<?php echo $item->data_publicacao; ?>"></div>
    <?php endif; ?>
    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="<?php echo $item->ordem; ?>"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo" <?php echo $item->status==='ativo'?'selected':''; ?>>Ativo</option><option value="inativo" <?php echo $item->status==='inativo'?'selected':''; ?>>Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Resumo</label><textarea name="resumo" class="form-control" rows="3"><?php echo htmlspecialchars($item->resumo); ?></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
