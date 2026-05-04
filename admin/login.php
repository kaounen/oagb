<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão OAGB 2.0 | Autenticação</title>
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #B1A276;
            --dark-bg: #111923;
            --deep-navy: #0b1118;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(11, 17, 24, 0.85), rgba(11, 17, 24, 0.85)), url('assets/img/login-bg.png');
            background-size: cover;
            background-position: center;
            font-family: 'Open Sans', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }

        .login-title {
            color: white;
            font-family: 'Libre Baskerville', serif;
            font-size: 1.5rem;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #adb5bd;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-label {
            color: #adb5bd;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .input-group {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            padding: 5px 10px;
            transition: border 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary-gold);
        }

        .form-control {
            background: transparent !important;
            border: none !important;
            color: white !important;
            box-shadow: none !important;
            font-size: 0.95rem;
            padding: 10px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-login {
            background: var(--primary-gold);
            border: none;
            color: #111923;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
            width: 100%;
            border-radius: 6px;
            margin-top: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background-color: #9c8e65;
            box-shadow: 0 5px 15px rgba(177, 162, 118, 0.3);
        }

        .footer-text {
            margin-top: 30px;
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.2);
        }

        /* Error Message Style */
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #ff8b8b;
            font-size: 0.85rem;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
        }

        /* Float Labels effect or simple placeholders are fine for this luxury look */
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="../img/logo3.png" alt="OAGB Logo" class="login-logo">
            <h1 class="login-title">Administração OAGB</h1>
            <p class="login-subtitle">Acesso Reservado</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle me-1"></i> Credenciais inválidas. Tente novamente.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['reset_success'])): ?>
            <div class="alert alert-success border-0 bg-success-subtle text-success small mb-4 text-center">
                <i class="fas fa-check-circle me-1"></i> Palavra-passe atualizada com sucesso! Pode entrar.
            </div>
        <?php endif; ?>

        <form action="auth/login_process.php" method="POST">
            <div class="mb-4">
                <label class="form-label">Email ou Utilizador</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="far fa-envelope"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="admin@oagb.gw" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Palavra-passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" style="background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
                    <label class="form-check-label text-secondary" for="remember" style="font-size: 0.8rem;">Lembrar-me</label>
                </div>
                <a href="auth/forgot_password.php" class="text-decoration-none" style="font-size: 0.8rem; color: var(--primary-gold);">Esqueci-me?</a>
            </div>

            <button type="submit" class="btn btn-login">
                Entrar no Sistema
            </button>
        </form>

        <div class="footer-text">
            &copy; 2026 Ordem dos Advogados da Guiné-Bissau.<br>Sistema de Gestão v2.0
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
