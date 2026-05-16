<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$bill_id = $_GET['bill'] ?? 0;
$method = $_GET['method'] ?? 'orange_money';
$method_name = ($method == 'orange_money') ? 'Orange Money' : 'Mobile Money (MTN)';
$ussd_code = ($method == 'orange_money') ? '*144#1' : '*155#';

$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome FROM finan_pagamentos p JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id WHERE p.id = ? AND p.advogado_id = ?");
$stmt->execute([$bill_id, $_SESSION['lawyer_id']]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$bill) { header("Location: financeiro.php"); exit; }

// Handle simulated success
if(isset($_POST['confirm_mock'])) {
    $stmt = $pdo->prepare("UPDATE finan_pagamentos SET status = 'confirmado', metodo_pagamento = ?, data_pagamento = NOW(), valid_until = DATE_ADD(NOW(), INTERVAL meses_pagos MONTH) WHERE id = ?");
    $stmt->execute([$method, $bill_id]);
    header("Location: financeiro.php?success=paid"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação USSD | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .checkout-box { max-width: 500px; margin: 60px auto; background: white; border-radius: 30px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); text-align: center; }
        .ussd-circle { width: 120px; height: 120px; background: #fffcf0; border: 4px solid var(--primary-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; font-size: 40px; color: var(--primary-gold); position: relative; }
        .ussd-circle::after { content: ''; position: absolute; width: 100%; height: 100%; border: 4px solid var(--primary-gold); border-radius: 50%; animation: pulse-gold 2s infinite; }
        @keyframes pulse-gold { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
        .code-box { background: #f8f9fa; border: 2px dashed #ddd; padding: 20px; border-radius: 15px; font-family: monospace; font-size: 24px; font-weight: 700; color: #333; margin: 20px 0; }
    </style>
</head>
<body>

    <div class="checkout-box animate__animated animate__fadeIn">
        <div class="mb-4">
            <img src="/oagb/img/logo3.png" height="40" class="mb-3">
            <h4 class="fw-bold mb-1">Aguardando Confirmação</h4>
            <p class="text-muted small">Pagamento via <?php echo $method_name; ?></p>
        </div>

        <div class="ussd-circle">
            <i class="fas fa-mobile-alt"></i>
        </div>

        <h5 class="fw-bold">Instruções no seu telemóvel:</h5>
        <p class="text-muted small px-3">Por favor, marque o código abaixo no seu telemóvel e confirme a transação introduzindo o seu PIN.</p>

        <div class="code-box animate__animated animate__pulse animate__infinite">
            <?php echo $ussd_code; ?>
        </div>

        <div class="alert bg-light border-0 small text-start mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Valor a Pagar:</span>
                <span class="fw-bold"><?php echo number_format($bill['valor_pago'], 0, ',', '.'); ?> CFA</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Referência:</span>
                <span class="fw-bold">OAGB-<?php echo $bill['id']; ?></span>
            </div>
        </div>

        <form method="POST" id="manual-confirm" class="mt-4">
            <button type="submit" name="confirm_mock" class="btn btn-dark w-100 p-3 rounded-pill fw-bold text-uppercase shadow-lg mb-3">
                <i class="fas fa-check-circle me-1"></i> Já Confirmei no Telemóvel
            </button>
        </form>

        <a href="pagamento_gateway.php?bill=<?php echo $bill_id; ?>" class="text-decoration-none text-muted small fw-bold">
            <i class="fas fa-times me-1"></i> CANCELAR E VOLTAR
        </a>

        <div class="mt-5 x-small text-muted opacity-50">
            <i class="fas fa-shield-alt"></i> Processamento seguro OAGB & Gateway Local
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
