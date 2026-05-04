<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | Portal OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --bg-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: var(--bg-dark); height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; color: white; overflow: hidden; }
        .login-card { width: 100%; max-width: 450px; padding: 50px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); backdrop-filter: blur(10px); }
        .logo-box { text-align: center; margin-bottom: 40px; }
        .logo-box img { height: 60px; filter: brightness(0) invert(1); }
        .form-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.5; font-weight: 700; margin-bottom: 10px; }
        .form-control { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; padding: 15px; border-radius: 12px; font-weight: 500; font-size: 0.9rem; margin-bottom: 20px; }
        .form-control:focus { background: rgba(255, 255, 255, 0.08); border-color: var(--primary-gold); color: white; box-shadow: none; }
        .btn-portal { background: var(--primary-gold); color: #111923; border: none; padding: 16px; border-radius: 12px; font-weight: 700; width: 100%; letter-spacing: 1px; transition: all 0.3s; margin-top: 10px; }
        .btn-portal:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(177, 162, 118, 0.3); background: #c5b689; }
        .hint { font-size: 0.8rem; text-align: center; margin-top: 30px; opacity: 0.6; line-height: 1.6; }
        .hint a { color: var(--primary-gold); text-decoration: none; font-weight: bold; }
        .alert-error { background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: #ff6b6b; border-radius: 12px; font-size: 0.85rem; padding: 15px; margin-bottom: 30px; }
        .alert-success { background: rgba(40, 167, 69, 0.1); border: 1px solid rgba(40, 167, 69, 0.2); color: #72cc85; border-radius: 12px; font-size: 0.85rem; padding: 15px; margin-bottom: 30px; text-align: center;}
    </style>
</head>
<body>

    <div class="login-card animate__animated animate__fadeInUp">
        <div class="logo-box">
            <img src="/oagb/img/logo3.png" alt="OAGB">
            <h5 class="mt-4 fw-bold">Recuperação de Senha</h5>
            <div class="small opacity-50">Portal do Advogado</div>
        </div>

        <?php if(isset($_SESSION['reset_error'])): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['reset_success'])): ?>
            <div class="alert-success"><i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?></div>
            <a href="login.php" class="btn btn-portal text-center d-block">VOLTAR AO LOGIN</a>
        <?php else: ?>
            <form action="recuperar_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">E-mail ou Nº Cédula Registado</label>
                    <input type="text" name="identificacao" class="form-control" required placeholder="Insira o seu registo ou e-mail">
                </div>
                
                <p style="font-size: 0.75rem; opacity: 0.7; margin-bottom: 20px;">
                    Por motivos de segurança, a sua senha será automaticamente reposta para o seu número de Cédula Profissional. Após o login, é recomendado que a altere.
                </p>

                <button type="submit" class="btn btn-portal">REPOR SENHA</button>
            </form>
        <?php endif; ?>

        <div class="hint">
            Lembrou-se? <a href="login.php">Voltar ao Login</a>
        </div>
    </div>

</body>
</html>
