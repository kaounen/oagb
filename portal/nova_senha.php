<?php
require_once __DIR__ . '/../connect.php';

$token = $_GET['token'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) { exit("Link inválido ou expirado."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if ($nova !== $confirm) { $error = "As senhas não coincidem."; }
    else {
        // Find which table to update
        $email = $reset['email'];
        $stmt = $pdo->prepare("SELECT id, 'advogados' as tbl FROM advogados WHERE email = ? UNION SELECT id, 'advogados_estagiarios' as tbl FROM advogados_estagiarios WHERE email = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $hashed = password_hash($nova, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE {$user['tbl']} SET senha = ? WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            
            // Delete token
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $success = "Senha atualizada com sucesso!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f6f8; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { max-width: 450px; width: 100%; background: white; border-radius: 24px; padding: 50px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); }
        .btn-gold { background: var(--primary-gold); color: white; border: none; padding: 15px; border-radius: 50px; font-weight: 700; width: 100%; }
    </style>
</head>
<body>
    <div class="reset-card text-center">
        <h4 class="fw-bold mb-4">Redefinir Senha</h4>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success border-0 py-3 mb-4"><?php echo $success; ?></div>
            <a href="login.php" class="btn btn-gold text-uppercase shadow-sm">Ir para o Login</a>
        <?php else: ?>
            <form method="POST">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger small text-start border-0 py-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-muted">NOVA SENHA</label>
                    <input type="password" name="password" class="form-control border-0 bg-light p-3 rounded-3" required minlength="6">
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-muted">CONFIRMAR SENHA</label>
                    <input type="password" name="confirm_password" class="form-control border-0 bg-light p-3 rounded-3" required>
                </div>
                <button type="submit" class="btn btn-gold text-uppercase shadow-sm">Atualizar Credenciais</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
