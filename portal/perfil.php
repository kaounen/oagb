<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';
$table = ($mtype == 'estagiario') ? 'advogados_estagiarios' : 'advogados';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    if ($nova !== $confirm) { $error = "As senhas não coincidem."; }
    else {
        $hashed = password_hash($nova, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE $table SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $lid]);
        $success = "Senha atualizada com sucesso!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$lid]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-profile { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .profile-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
    </style>
</head>
<body>

    <header class="hero-profile">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Meu Perfil Institucional</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="profile-card">
            <div class="row g-5">
                <div class="col-md-4 border-end">
                    <h5 class="fw-bold mb-4">Dados de Registo</h5>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block">Nome Completo</label>
                        <div class="fw-bold"><?php echo $user['nome_completo']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block">Número de Cédula</label>
                        <div class="fw-bold"><?php echo $user['numero_registo']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block">E-mail Institucional</label>
                        <div class="fw-bold"><?php echo $user['email']; ?></div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <h5 class="fw-bold mb-4">Alterar Senha de Acesso</h5>
                    
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success border-0 small py-3"><i class="fas fa-check-circle me-1"></i> <?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger border-0 small py-3"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="bg-light p-4 rounded-4 border">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nova Senha</label>
                                <input type="password" name="new_password" class="form-control border-0 p-3" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirmar Nova Senha</label>
                                <input type="password" name="confirm_password" class="form-control border-0 p-3" required>
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-dark px-4 py-3 fw-bold rounded-pill text-uppercase">Atualizar Senha</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
