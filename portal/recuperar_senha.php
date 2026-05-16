<?php
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists in advogados or advogados_estagiarios
    $stmt = $pdo->prepare("SELECT id, 'advogado' as type FROM advogados WHERE email = ? UNION SELECT id, 'estagiario' as type FROM advogados_estagiarios WHERE email = ?");
    $stmt->execute([$email, $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expiry]);
        
        // MOCK: In a real system, send email here.
        // For OAGB, we will show a success message and log it.
        $reset_link = "https://oagb.gw/portal/nova_senha.php?token=" . $token;
        $success = "Se o e-mail estiver correto, receberá instruções em breve. (Simulação: Link gerado: $reset_link)";
    } else {
        $error = "E-mail não encontrado no sistema.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Acesso | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f6f8; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .recovery-card { max-width: 450px; width: 100%; background: white; border-radius: 24px; padding: 50px; box-shadow: 0 20px 60px rgba(0,0,0,0.08); }
        .btn-gold { background: var(--primary-gold); color: white; border: none; padding: 15px; border-radius: 50px; font-weight: 700; width: 100%; }
        .btn-gold:hover { background: #9a8c63; color: white; }
    </style>
</head>
<body>
    <div class="recovery-card text-center">
        <img src="/oagb/img/logo3.png" height="50" class="mb-4">
        <h4 class="fw-bold mb-2">Esqueceu a senha?</h4>
        <p class="text-muted small mb-4">Introduza o seu e-mail profissional para receber um link de recuperação.</p>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success small text-start border-0 py-3"><?php echo $success; ?></div>
            <a href="login.php" class="btn btn-outline-secondary w-100 rounded-pill mt-3 py-3 fw-bold">VOLTAR AO LOGIN</a>
        <?php else: ?>
            <form method="POST">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger small text-start border-0 py-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-muted">E-MAIL INSTITUCIONAL</label>
                    <input type="email" name="email" class="form-control border-0 bg-light p-3 rounded-3" required placeholder="exemplo@oagb.gw">
                </div>
                <button type="submit" class="btn btn-gold text-uppercase shadow-sm">Enviar Link de Acesso</button>
            </form>
            <div class="mt-4">
                <a href="login.php" class="text-decoration-none small text-muted fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR</a>
            </div>
        <?php endif; ?>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
