<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $edicao = trim($_POST['edicao'] ?? '');
    $ano = intval($_POST['ano'] ?? date('Y'));
    $data_publicacao = $_POST['data_publicacao'] ?? date('Y-m-d');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = $_POST['status'] ?? 'ativo';

    if (empty($titulo)) $errors[] = 'O título é obrigatório.';
    if (empty($edicao)) $errors[] = 'A edição é obrigatória.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO revistas_oagb (titulo, edicao, ano, data_publicacao, descricao, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $edicao, $ano, $data_publicacao, $descricao, $status]);
            header('Location: index.php?msg=added');
            exit;
        } catch (PDOException $e) { $errors[] = 'Erro de base de dados: ' . $e->getMessage(); }
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Nova Edição da Revista</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar à lista</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Edição (ex: Nº 12)</label><input type="text" name="edicao" class="form-control" required value="<?php echo htmlspecialchars($_POST['edicao'] ?? ''); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Ano</label><input type="number" name="ano" class="form-control" value="<?php echo $_POST['ano'] ?? date('Y'); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Data de Publicação</label><input type="date" name="data_publicacao" class="form-control" value="<?php echo $_POST['data_publicacao'] ?? date('Y-m-d'); ?>"></div>
            <div class="col-md-4"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
            <div class="col-12"><label class="form-label fw-bold small">Descrição</label><textarea name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
        </div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
