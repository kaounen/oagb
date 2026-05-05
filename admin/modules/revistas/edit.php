<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM revistas_oagb WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch();
if (!$item) { echo '<div class="alert alert-danger">Edição não encontrada.</div>'; require_once __DIR__ . '/../../includes/footer.php'; exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $edicao = trim($_POST['edicao'] ?? ''); $ano = intval($_POST['ano'] ?? date('Y'));
    $data_publicacao = $_POST['data_publicacao'] ?? ''; $descricao = trim($_POST['descricao'] ?? ''); $status = $_POST['status'] ?? 'ativo';
    if (empty($titulo)) $errors[] = 'O título é obrigatório.';
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE revistas_oagb SET titulo=?, edicao=?, ano=?, data_publicacao=?, descricao=?, status=? WHERE id=?");
        $stmt->execute([$titulo, $edicao, $ano, $data_publicacao, $descricao, $status, $id]);
        header('Location: index.php?msg=updated'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Editar Revista #<?php echo $id; ?></h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item->titulo); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Edição</label><input type="text" name="edicao" class="form-control" required value="<?php echo htmlspecialchars($item->edicao); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Ano</label><input type="number" name="ano" class="form-control" value="<?php echo $item->ano; ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Data</label><input type="date" name="data_publicacao" class="form-control" value="<?php echo $item->data_publicacao; ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo" <?php echo $item->status==='ativo'?'selected':''; ?>>Ativo</option><option value="inativo" <?php echo $item->status==='inativo'?'selected':''; ?>>Inativo</option></select></div>
            <div class="col-12"><label class="form-label fw-bold small">Descrição</label><textarea name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($item->descricao); ?></textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar Alterações</button></div>
        </div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
