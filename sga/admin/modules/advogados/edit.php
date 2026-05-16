<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM advogados WHERE id = ?");
$stmt->execute([$id]);
$adv = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'];
    $reg = $_POST['numero_registo'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE advogados SET nome_completo = ?, numero_registo = ?, email = ?, status = ? WHERE id = ?");
    $stmt->execute([$nome, $reg, $email, $status, $id]);
    
    header("Location: index.php?success=updated"); exit;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Advogado</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white">
    <form method="POST">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nome Completo</label>
                <input type="text" name="nome_completo" class="form-control border-0 bg-light p-3" value="<?php echo $adv['nome_completo']; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Cédula Profissional</label>
                <input type="text" name="numero_registo" class="form-control border-0 bg-light p-3" value="<?php echo $adv['numero_registo']; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Estado</label>
                <select name="status" class="form-select border-0 bg-light p-3">
                    <option value="ativo" <?php if($adv['status']=='ativo') echo 'selected'; ?>>Ativo</option>
                    <option value="suspenso" <?php if($adv['status']=='suspenso') echo 'selected'; ?>>Suspenso</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted">Email Profissional</label>
                <input type="email" name="email" class="form-control border-0 bg-light p-3" value="<?php echo $adv['email']; ?>" required>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Guardar Alterações</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
