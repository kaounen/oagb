<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("UPDATE advogados SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $lid]);
            $success = "Senha atualizada com sucesso!";
        } catch (PDOException $e) { $error = "Erro ao atualizar senha."; }
    } else { $error = "As senhas não coincidem."; }
}

$stmt = $pdo->prepare("SELECT * FROM advogados WHERE id = ?");
$stmt->execute([$lid]);
$lawyer = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Membro | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-profile { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .profile-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; max-width: 600px; margin-left: auto; margin-right: auto; }
    </style>
</head>
<body>

    <header class="hero-profile">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Segurança da Conta</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="profile-card">
            <h5 class="fw-bold mb-4">Atualizar Senha de Acesso</h5>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success border-0 small px-4 py-3 mb-4"><i class="fas fa-check-circle me-1"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 small px-4 py-3 mb-4"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label small text-muted text-uppercase fw-bold">Nova Senha</label>
                    <input type="password" name="new_password" class="form-control border-0 bg-light p-3" required minlength="8" placeholder="Digite a nova senha">
                </div>
                <div class="mb-4">
                    <label class="form-label small text-muted text-uppercase fw-bold">Confirmar Senha</label>
                    <input type="password" name="confirm_password" class="form-control border-0 bg-light p-3" required minlength="8" placeholder="Repita a nova senha">
                </div>

                <button type="submit" class="btn btn-dark w-100 p-3 fw-bold rounded-3">GUARDAR ALTERAÇÕES</button>
            </form>
            
            <hr class="my-5 opacity-50">
            <div class="text-center text-muted x-small">Dica: Utilize uma senha forte com letras, números e símbolos para garantir a protecção total dos seus dados.</div>
        </div>
    </main>

</body>
</html>
