<?php
session_start();
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registo = $_POST['registo'] ?? '';
    $pass = $_POST['password'] ?? '';

    try {
        // Try Lawyer
        $stmt = $pdo->prepare("SELECT *, 'advogado' as mtype FROM advogados WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
        $stmt->execute([$registo, $registo]);
        $user = $stmt->fetch();

        // If not, Try Intern
        if (!$user) {
            $stmt = $pdo->prepare("SELECT *, 'estagiario' as mtype FROM advogados_estagiarios WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
            $stmt->execute([$registo, $registo]);
            $user = $stmt->fetch();
        }

        if ($user) {
            // First time login logic: if password is null, cédula is the password
            if ($user['password'] === null && $pass === $user['numero_registo']) {
                $_SESSION['lawyer_id'] = $user['id'];
                $_SESSION['lawyer_name'] = $user['nome_completo'];
                $_SESSION['lawyer_registo'] = $user['numero_registo'];
                $_SESSION['member_type'] = $user['mtype'];
                header("Location: index.php?setup=1"); exit;
            } else if ($user['password'] !== null && password_verify($pass, $user['password'])) {
                $_SESSION['lawyer_id'] = $user['id'];
                $_SESSION['lawyer_name'] = $user['nome_completo'];
                $_SESSION['lawyer_registo'] = $user['numero_registo'];
                $_SESSION['member_type'] = $user['mtype'];
                header("Location: index.php"); exit;
            } else { $error = "Credenciais inválidas."; }
        } else { $error = "Acesso não autorizado ou membro inactivo."; }
    } catch (PDOException $e) { $error = "Erro no servidor."; }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Advogado | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --bg-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: var(--bg-dark); height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; color: white; overflow: hidden; }
        .login-card { width: 100%; max-width: 450px; padding: 50px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); backdrop-filter: blur(10px); }
        .logo-box { text-align: center; margin-bottom: 40px; }
        .logo-box img { height: 60px; filter: brightness(0) invert(1); }
        .form-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.5; font-weight: 700; margin-bottom: 10px; }
        .form-control { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; padding: 15px; border-radius: 12px; font-weight: 500; font-size: 0.9rem; }
        .form-control:focus { background: rgba(255, 255, 255, 0.08); border-color: var(--primary-gold); color: white; box-shadow: none; }
        .btn-portal { background: var(--primary-gold); color: #111923; border: none; padding: 16px; border-radius: 12px; font-weight: 700; width: 100%; letter-spacing: 1px; transition: all 0.3s; margin-top: 20px; }
        .btn-portal:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(177, 162, 118, 0.3); background: #c5b689; }
        .hint { font-size: 0.7rem; text-align: center; margin-top: 30px; opacity: 0.4; line-height: 1.6; }
        .alert { background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: #ff6b6b; border-radius: 12px; font-size: 0.85rem; padding: 15px; margin-bottom: 30px; }
    </style>
</head>
<body>

    <div class="login-card animate__animated animate__fadeInUp">
        <div class="logo-box">
            <img src="/oagb/img/logo3.png" alt="OAGB">
            <h5 class="mt-4 fw-bold">Portal do Advogado</h5>
            <div class="small opacity-50">Área Exclusiva a Membros Inscritos</div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label">E-mail ou Nº Cédula</label>
                <input type="text" name="registo" class="form-control" required placeholder="Ex: CP-001/24">
            </div>
            <div class="mb-4">
                <label class="form-label">Senha de Acesso</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-portal">ENTRAR NO PORTAL</button>
        </form>

        <div class="hint">
            <strong>Primeiro Acesso?</strong> Se ainda não definiu uma senha, <br> utilize o seu número de cédula como senha inicial.
        </div>
    </div>

</body>
</html>
