<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$bill_id = $_GET['bill'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome FROM finan_pagamentos p JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id WHERE p.id = ? AND p.advogado_id = ?");
$stmt->execute([$bill_id, $_SESSION['lawyer_id']]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$bill) { header("Location: financeiro.php"); exit; }

// Handle simulated success
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real scenario, here we would validate with Stripe API
    $stmt = $pdo->prepare("UPDATE finan_pagamentos SET status = 'confirmado', metodo_pagamento = 'stripe', data_pagamento = NOW(), valid_until = DATE_ADD(NOW(), INTERVAL meses_pagos MONTH) WHERE id = ?");
    $stmt->execute([$bill_id]);
    header("Location: financeiro.php?success=paid"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento com Cartão | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .checkout-box { max-width: 550px; margin: 60px auto; background: white; border-radius: 30px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .credit-card { background: linear-gradient(135deg, #111923 0%, #2c3e50 100%); border-radius: 20px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
        .credit-card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%); }
        .chip { width: 50px; height: 35px; background: #e0e0e0; border-radius: 5px; margin-bottom: 20px; }
        .card-number { font-size: 22px; letter-spacing: 3px; margin-bottom: 20px; font-family: monospace; }
        .card-holder { text-transform: uppercase; font-size: 14px; opacity: 0.8; }
        .card-expiry { font-size: 14px; opacity: 0.8; }
        .form-control-custom { border: none; background: #f0f2f5; padding: 15px; border-radius: 12px; font-weight: 600; }
        .form-control-custom:focus { box-shadow: 0 0 0 3px rgba(177,162,118,0.2); background: #fff; }
    </style>
</head>
<body>

    <div class="checkout-box">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h4 class="fw-bold mb-0">Pagamento por Cartão</h4>
                <p class="text-muted small mb-0">VISA, Mastercard ou American Express</p>
            </div>
            <img src="/oagb/img/logo3.png" height="35">
        </div>

        <div class="credit-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="chip"></div>
                <div class="fs-2"><i class="fab fa-cc-visa"></i></div>
            </div>
            <div class="card-number">**** **** **** ****</div>
            <div class="d-flex justify-content-between">
                <div>
                    <div class="small opacity-50 mb-1">Titular</div>
                    <div class="card-holder">NOME COMPLETO</div>
                </div>
                <div>
                    <div class="small opacity-50 mb-1">Validade</div>
                    <div class="card-expiry">MM / AA</div>
                </div>
            </div>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Nome no Cartão</label>
                <input type="text" class="form-control form-control-custom" placeholder="Como aparece no cartão" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Número do Cartão</label>
                <input type="text" class="form-control form-control-custom" placeholder="0000 0000 0000 0000" required>
            </div>
            <div class="row mb-4">
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted">Validade</label>
                    <input type="text" class="form-control form-control-custom" placeholder="MM/AA" required>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted">CVC / CVV</label>
                    <input type="password" class="form-control form-control-custom" placeholder="123" required maxlength="4">
                </div>
            </div>

            <div class="p-3 bg-light rounded-4 mb-4 border d-flex justify-content-between align-items-center">
                <div>
                    <div class="small text-muted">Total a Pagar</div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo number_format($bill['valor_pago'], 0, ',', '.'); ?> CFA</div>
                </div>
                <i class="fas fa-lock text-success fs-3"></i>
            </div>

            <button type="submit" class="btn btn-dark w-100 p-4 fs-5 fw-bold rounded-pill shadow-lg text-uppercase">
                Finalizar Pagamento Seguro
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="pagamento_gateway.php?bill=<?php echo $bill_id; ?>" class="text-decoration-none text-muted small fw-bold">
                <i class="fas fa-times me-1"></i> CANCELAR E VOLTAR
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
