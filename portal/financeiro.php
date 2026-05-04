<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];

// Fetch All Payments
$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome 
                       FROM finan_pagamentos p 
                       JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id
                       WHERE p.advogado_id = ? 
                       ORDER BY p.data_pagamento DESC");
$stmt->execute([$lid]);
$payments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato de Operações | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-finance { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .finance-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
    </style>
</head>
<body>

    <header class="hero-finance">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Extrato Financeiro</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="finance-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="bg-light text-uppercase small fw-bold text-muted">
                            <th class="border-0 p-3">Data</th>
                            <th class="border-0 p-3">Tipo de Operação</th>
                            <th class="border-0 p-3">Método / Referência</th>
                            <th class="border-0 p-3 text-end">Valor</th>
                            <th class="border-0 p-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($payments)): ?>
                            <tr><td colspan="5" class="text-center py-5">Nenhum pagamento registado.</td></tr>
                        <?php else: ?>
                            <?php foreach($payments as $p): ?>
                                <tr>
                                    <td class="p-3"><?php echo date('d/m/Y', strtotime($p['data_pagamento'])); ?></td>
                                    <td class="p-3 fw-bold text-dark"><?php echo $p['tipo_nome']; ?></td>
                                    <td class="p-3 small text-muted"><?php echo strtoupper($p['metodo_pagamento']); ?> / DEP-<?php echo $p['id']; ?></td>
                                    <td class="p-3 text-end fw-bold"><?php echo number_format($p['valor_pago'], 0, ',', '.'); ?> CFA</td>
                                    <td class="p-3 text-center">
                                        <?php if($p['status'] == 'confirmado'): ?>
                                            <span class="badge py-2 px-3 bg-success-subtle text-success">
                                                CONFIRMADO
                                            </span>
                                        <?php else: ?>
                                            <a href="pagamento_gateway.php?bill=<?php echo $p['id']; ?>" class="btn btn-sm btn-login fw-bold rounded-pill px-3 py-2 shadow-sm animate__animated animate__pulse animate__infinite">
                                                <i class="fas fa-credit-card me-1"></i> PAGAR AGORA
                                            </a>
                                            <div class="x-small text-muted mt-1">Clique para liquidar online</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 p-4 bg-light rounded-4 border-dashed border">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> Notas de Regularização</h6>
                <p class="small text-muted mb-0">Os DEPÓSITOS ou TRANSFERÊNCIAS bancárias levam em média <b>24h a 48h</b> para serem validados pela Tesouraria. Se realizou um pagamento recentemente e este ainda consta como PENDENTE, por favor aguarde pela conferência administrativa.</p>
            </div>
        </div>
    </main>

</body>
</html>
