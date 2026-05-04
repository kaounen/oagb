<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Get Pending Bill
$bill_id = $_GET['bill'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome FROM finan_pagamentos p JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id WHERE p.id = ? AND p.advogado_id = ? AND p.status = 'pendente'");
$stmt->execute([$bill_id, $lid]);
$bill = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bill) {
    $method = $_POST['metodo_selecionado'] ?? 'orange_money';
    // MOCK: In a real scenario, this would call the API (Hub2, PayDunya, etc)
    // For now, we simulate success
    $stmt = $pdo->prepare("UPDATE finan_pagamentos SET status = 'confirmado', metodo_pagamento = ?, data_pagamento = NOW() WHERE id = ?");
    $stmt->execute([$method, $bill_id]);
    
    header("Location: financeiro.php?success=paid"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Seguro | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .checkout-box { max-width: 500px; margin: 80px auto; background: white; border-radius: 24px; padding: 40px; box-shadow: 0 15px 50px rgba(0,0,0,0.1); }
        .method-card { border: 2px solid #eee; padding: 20px; border-radius: 16px; margin-bottom: 15px; cursor: pointer; transition: 0.3s; }
        .method-card:hover { border-color: var(--primary-gold); background: #fffcf5; }
        .method-card input { display: none; }
        .method-card.active { border-color: var(--primary-gold); background: #fffcf5; box-shadow: 0 0 15px rgba(177,162,118,0.2); }
    </style>
</head>
<body>

    <div class="checkout-box">
        <div class="text-center mb-5">
            <img src="/oagb/img/logo3.png" height="45" class="mb-4">
            <h4 class="fw-bold">Pagamento Institucional</h4>
            <p class="text-muted small">Ambiente Seguro de Tesouraria Digital</p>
        </div>

        <?php if(!$bill): ?>
            <div class="alert alert-danger shadow-sm text-center">Referência de pagamento inválida ou já liquidada.</div>
            <a href="financeiro.php" class="btn btn-dark w-100 p-3 rounded-pill mt-3">VOLTAR AO EXTRATO</a>
        <?php else: ?>
            <div class="p-4 bg-light rounded-4 mb-5">
                <div class="small fw-bold text-muted text-uppercase mb-1"><?php echo $bill['tipo_nome']; ?></div>
                <div class="h2 fw-bold mb-0"><?php echo number_format($bill['valor_pago'], 0, ',', '.'); ?> CFA</div>
                <div class="x-small text-muted mt-2">ID#<?php echo $bill['id']; ?> | Referencia OAGB-<?php echo date('Y'); ?></div>
            </div>

            <form method="POST">
                <label class="method-card w-100 d-flex align-items-center active" onclick="sel(this)">
                    <input type="radio" name="metodo_selecionado" value="orange_money" checked>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/1024px-Orange_logo.svg.png" height="30" class="me-3">
                    <div class="flex-grow-1"><div class="fw-bold">Orange Money</div><div class="x-small text-muted">Pago instantâneo</div></div>
                    <i class="fas fa-check-circle text-success fs-4"></i>
                </label>

                <label class="method-card w-100 d-flex align-items-center" onclick="sel(this)">
                    <input type="radio" name="metodo_selecionado" value="mobile_money">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/03/MTN_Logo.svg" height="30" class="me-3">
                    <div class="flex-grow-1"><div class="fw-bold">Mobile Money (MTN)</div><div class="x-small text-muted">Aprovação via telemóvel</div></div>
                </label>

                <label class="method-card w-100 d-flex align-items-center" onclick="sel(this)">
                    <input type="radio" name="metodo_selecionado" value="visa">
                    <i class="fab fa-cc-visa fs-2 text-primary me-3"></i>
                    <div class="flex-grow-1"><div class="fw-bold">Cartão Bancário / MB</div><div class="x-small text-muted">Processamento Internacional</div></div>
                </label>

                <button type="submit" class="btn btn-dark w-100 p-4 fs-5 fw-bold rounded-pill shadow-lg mt-4 text-uppercase">
                    Pagar CFA <?php echo number_format($bill['valor_pago'], 0, ',', '.'); ?>
                </button>
            </form>
            
            <div class="text-center mt-4 small opacity-50">
                <i class="fas fa-lock me-1"></i> Transação encriptada e segura (SSL)
            </div>
        <?php endif; ?>
    </div>

    <script>
        function sel(el) {
            document.querySelectorAll('.method-card').forEach(m => m.classList.remove('active'));
            el.classList.add('active');
            el.querySelector('input').checked = true;
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
