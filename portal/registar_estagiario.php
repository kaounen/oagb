<?php
session_start();
if(!isset($_SESSION['lawyer_id']) || $_SESSION['member_type'] != 'advogado') { header("Location: index.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_intern'])) {
    $nome = $_POST['nome_completo'];
    $reg = $_POST['numero_registo'];
    $email = $_POST['email'];
    $fase = $_POST['fase_estagio'];
    
    $stmt = $pdo->prepare("INSERT INTO advogados_estagiarios (nome_completo, numero_registo, email, orientador_id, fase_estagio, status, data_inicio_estagio) VALUES (?, ?, ?, ?, ?, 'ativo', NOW())");
    $stmt->execute([$nome, $reg, $email, $lid, $fase]);
    
    header("Location: validar_estagiarios.php?success=created"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar Estagiário | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-reg { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .form-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        .btn-gold { background: var(--primary-gold); color: #111923; font-weight: 700; border: none; }
        .btn-gold:hover { background: #111923; color: white; }
    </style>
</head>
<body>

    <header class="hero-reg">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Inscrição de Novo Estagiário</h2>
            <a href="validar_estagiarios.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> CANCELAR E VOLTAR</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="form-card">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="fw-bold mb-1">Dados Institucionais do Estagiário</h5>
                    <p class="text-muted small">Preencha os dados abaixo para vincular um novo estagiário à sua orientação profissional.</p>
                </div>
            </div>

            <form method="POST">
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted">Nome Completo</label>
                        <input type="text" name="nome_completo" class="form-control border-0 bg-light p-3" placeholder="Ex: João Manuel Galissa" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Cédula No. (Provisória)</label>
                        <input type="text" name="numero_registo" class="form-control border-0 bg-light p-3" placeholder="Ex: EST-2024-001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Email Profissional</label>
                        <input type="email" name="email" class="form-control border-0 bg-light p-3" placeholder="estagiario@email.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Fase de Estágio</label>
                        <select name="fase_estagio" class="form-select border-0 bg-light p-3">
                            <option value="instrucao">Fase de Instrução</option>
                            <option value="pratica">Fase Prática</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-5">
                        <div class="alert alert-info border-0 rounded-3 small">
                            <i class="fas fa-info-circle me-2"></i> Ao confirmar, o estagiário será automaticamente vinculado ao seu perfil como orientador/patrono.
                        </div>
                        <div class="text-end">
                            <button type="submit" name="register_intern" class="btn btn-gold px-5 py-3 rounded-3 text-uppercase shadow-sm">
                                Concluir Registo e Vincular
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
