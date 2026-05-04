<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Palavra-passe | OAGB</title>
    
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
            background: linear-gradient(rgba(11, 17, 24, 0.85), rgba(11, 17, 24, 0.85)), url('../assets/img/login-bg.png');
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
            margin-bottom: 30px;
        }

        .login-logo {
            width: 80px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }

        .login-title {
            color: white;
            font-family: 'Libre Baskerville', serif;
            font-size: 1.4rem;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #adb5bd;
            font-size: 0.85rem;
            line-height: 1.5;
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
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background-color: #9c8e65;
        }

        .back-to-login {
            display: block;
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-to-login:hover {
            color: var(--primary-gold);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            color: #72cc85;
            font-size: 0.85rem;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #ff8b8b;
            font-size: 0.85rem;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="../../img/logo3.png" alt="OAGB Logo" class="login-logo">
            <h1 class="login-title">Recuperar Acesso</h1>
            <p class="login-subtitle">Insira o seu e-mail institucional para receber as instruções de recuperação.</p>
        </div>

        <?php if(isset($_GET['sent'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle me-1"></i> Se o e-mail existir no nosso sistema, receberá um link em breve. Verifique também a pasta de SPAM.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle me-1"></i> Erro ao processar o pedido. Tente novamente mais tarde.
            </div>
        <?php endif; ?>

        <?php if(!isset($_GET['sent'])): ?>
        <form action="forgot_password_process.php" method="POST">
            <div class="mb-4">
                <label class="form-label">E-mail Registado</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="far fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@oagb.gw" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                Enviar Link de Recuperação
            </button>
        </form>
        <?php endif; ?>

        <a href="../login.php" class="back-to-login">
            <i class="fas fa-arrow-left me-2"></i> Voltar ao Login
        </a>
    </div>

</body>
</html>
