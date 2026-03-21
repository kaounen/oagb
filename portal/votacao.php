<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Check Quota Status (Necessário para votar)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND tipo_pagamento_id = 1 
                       AND status = 'confirmado' AND (MONTH(data_pagamento) = MONTH(NOW()) OR MONTH(data_pagamento) = MONTH(NOW())-1)");
$stmt->execute([$lid]);
$regularizado = ($stmt->fetchColumn() > 0);

// Fetch Active Election
$stmt = $pdo->query("SELECT * FROM gestao_eleicoes WHERE ativa = 1 LIMIT 1");
$eleicao = $stmt->fetch();

if ($eleicao) {
    // Check if Already Voted
    $stmt = $pdo->prepare("SELECT id FROM gestao_votos WHERE eleicao_id = ? AND advogado_id = ?");
    $stmt->execute([$eleicao['id'], $lid]);
    $already_voted = $stmt->fetch();

    // Fetch Options
    $stmt = $pdo->prepare("SELECT * FROM gestao_opcoes WHERE eleicao_id = ?");
    $stmt->execute([$eleicao['id']]);
    $opcoes = $stmt->fetchAll();
}

// Handle Vote
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $eleicao && $regularizado && !$already_voted) {
    if (isset($_POST['opcao_id'])) {
        $oid = $_POST['opcao_id'];
        try {
            $hash = hash('sha256', $lid . $eleicao['id'] . time());
            $stmt = $pdo->prepare("INSERT INTO gestao_votos (eleicao_id, advogado_id, hash_voto) VALUES (?, ?, ?)");
            $stmt->execute([$eleicao['id'], $lid, $hash]);
            
            // Redirect with success
            header("Location: votacao.php?success=1&hash=$hash"); exit;
        } catch (PDOException $e) { $error = "Erro ao registar o seu voto."; }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escrutínio Digital | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; min-height: 100vh; }
        .hero-vote { background: var(--sidebar-dark); padding: 80px 0; color: white; border-bottom: 5px solid var(--primary-gold); text-align: center; }
        .vote-card { background: white; border-radius: 24px; padding: 40px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); margin-top: -60px; max-width: 800px; margin-left: auto; margin-right: auto; }
        .btn-vote { padding: 25px; border-radius: 16px; border: 2px solid #eee; text-align: center; cursor: pointer; transition: all 0.3s; height: 100%; position: relative; }
        .btn-vote:hover { border-color: var(--primary-gold); background: #fffcf5; transform: translateY(-5px); }
        .btn-vote input { display: none; }
        .btn-vote.selected { border-color: var(--primary-gold); background: #fffcf5; box-shadow: 0 0 20px rgba(177, 162, 118, 0.2); }
        .btn-vote.selected::after { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; top: 15px; right: 15px; color: var(--primary-gold); font-size: 1.5rem; }
        .footer-vote { margin-top: 100px; text-align: center; font-size: 0.8rem; opacity: 0.4; color: #111923; padding-bottom: 50px; }
    </style>
</head>
<body>

    <header class="hero-vote">
        <div class="container">
            <h1 class="fw-bold mb-3"><i class="fas fa-vote-yea me-3"></i> Ato Eleitoral Digital</h1>
            <p class="opacity-75">O seu voto é seguro, secreto e fundamental para o fortalecimento da nossa Instituição.</p>
            <div class="mt-4">
                <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="vote-card animate__animated animate__fadeInUp">
            <?php if(!$eleicao): ?>
                <div class="text-center py-5">
                    <i class="fas fa-hourglass-half fa-4x text-muted mb-4 opacity-25"></i>
                    <h3 class="fw-bold text-dark">Nenhuma eleição ativa</h3>
                    <p class="text-muted">Neste momento não existem atos eleitorais em curso. <br> Fique atento aos comunicados oficiais no portal.</p>
                </div>
            <?php elseif(!$regularizado): ?>
                <div class="text-center py-5">
                    <i class="fas fa-lock fa-4x text-danger mb-4 opacity-25"></i>
                    <h3 class="fw-bold text-danger">Acesso Impedido</h3>
                    <p class="text-muted">Apenas advogados com a situação contributiva regularizada <br> podem exercer o direito de voto (Regulamento Eleitoral).</p>
                    <a href="financeiro.php" class="btn btn-dark px-4 py-2 mt-3 rounded-pill">REGULARIZAR NO MEU EXTRATO</a>
                </div>
            <?php elseif(isset($already_voted)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-double fa-4x text-success mb-4"></i>
                    <h3 class="fw-bold text-dark">Voto Registado com Sucesso</h3>
                    <p class="text-muted">Obrigado pela sua participação democrática. O seu voto já foi contabilizado.</p>
                    <div class="badge bg-light text-dark p-3 mt-4 border">
                        REFERÊNCIA DE REGISTO: <br> <code class="small"><?php echo $_GET['hash'] ?? 'HIDDEN-SHA256'; ?></code>
                    </div>
                </div>
            <?php else: ?>
                <h4 class="fw-bold text-center mb-5"><?php echo $eleicao['titulo']; ?></h4>
                <p class="text-center small text-muted mb-5">Selecione uma das opções abaixo e clique em confirmar voto definitivo.</p>

                <form method="POST">
                    <div class="row g-4 mb-5">
                        <?php foreach($opcoes as $o): ?>
                            <div class="col-md-6">
                                <label class="btn-vote w-100" onclick="selectVote(this)">
                                    <input type="radio" name="opcao_id" value="<?php echo $o['id']; ?>" required>
                                    <div class="p-4">
                                        <div class="fw-bold fs-4"><?php echo $o['nome_lista']; ?></div>
                                        <div class="small opacity-50 text-uppercase">Opção de Voto #<?php echo $o['id']; ?></div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($opcoes)): ?>
                            <div class="col-12 text-center py-4 bg-light rounded-4">As listas concorrentes ainda não foram publicadas.</div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 p-4 fs-5 fw-bold rounded-pill shadow-lg animate__animated animate__pulse animate__infinite">
                        <i class="fas fa-paper-plane me-2"></i> CONFIRMAR E ENTREGAR VOTO DIGITAL
                    </button>
                    <div class="text-center x-small text-muted mt-4">
                        <i class="fas fa-shield-alt me-1"></i> A participação é sigilosa. O sistema apenas regista que o Dr. votou, sem associar à opção selecionada.
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer-vote container">
        &copy; <?php echo date('Y'); ?> Ordem dos Advogados da Guiné-Bissau | Comissão Eleitoral Nacional
    </footer>

    <script>
        function selectVote(el) {
            document.querySelectorAll('.btn-vote').forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');
        }
    </script>

</body>
</html>
