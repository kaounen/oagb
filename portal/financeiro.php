<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';

// Fetch Financial Config
$stmt = $pdo->query("SELECT chave, valor FROM finan_config");
$fconfig = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$quota_valor = ($mtype == 'estagiario') ? ($fconfig['quota_estagiario'] ?? 5000) : ($fconfig['quota_advogado'] ?? 15000);
$tipo_quota_id = ($mtype == 'estagiario') ? 2 : 1; 

// Handle Advance Payment Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_advance'])) {
    $months = (int)$_POST['num_months'];
    $total = $quota_valor * $months;
    
    // Create a pending record for the multi-month payment
    $stmt = $pdo->prepare("INSERT INTO finan_pagamentos (advogado_id, membro_tipo, tipo_pagamento_id, meses_pagos, valor_pago, status, data_pagamento, metodo_pagamento) 
                           VALUES (?, ?, ?, ?, ?, 'pendente', NOW(), 'mobile_money')");
    $stmt->execute([$lid, $mtype, $tipo_quota_id, $months, $total]);
    $pay_id = $pdo->lastInsertId();
    
    header("Location: pagamento_gateway.php?bill=$pay_id"); exit;
}

// Fetch All Payments
$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome 
                       FROM finan_pagamentos p 
                       JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id
                       WHERE p.advogado_id = ? 
                       ORDER BY p.data_pagamento DESC, p.id DESC");
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-finance { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .finance-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        .btn-login { background: var(--primary-gold); color: #111923; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; transition: 0.3s; }
        .btn-login:hover { background: #111923; color: white; transform: translateY(-2px); }
        
        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--primary-gold) !important; border: none !important; color: white !important; border-radius: 10px; }
        .dataTables_filter input { border-radius: 10px; border: 1px solid #ddd; padding: 8px 15px; }
        .table thead th { border-bottom: 2px solid #eee !important; }
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
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i> Pagamento Antecipado / Novo</h5>
                        <div class="badge bg-light text-dark border p-2 px-3 small">Valor Unitário: <?php echo number_format($quota_valor, 0, ',', '.'); ?> CFA</div>
                    </div>
                    <form method="POST" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted text-uppercase">Selecionar Período de Quotas</label>
                            <select name="num_months" class="form-select border-0 bg-light p-3 rounded-3" required>
                                <option value="1">1 Mês (<?php echo number_format($quota_valor, 0, ',', '.'); ?> CFA)</option>
                                <option value="3">Trimestre - 3 Meses (<?php echo number_format($quota_valor * 3, 0, ',', '.'); ?> CFA)</option>
                                <option value="6">Semestre - 6 Meses (<?php echo number_format($quota_valor * 6, 0, ',', '.'); ?> CFA)</option>
                                <option value="12">Anual - 12 Meses (<?php echo number_format($quota_valor * 12, 0, ',', '.'); ?> CFA)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="pay_advance" class="btn btn-login w-100 py-3 shadow-sm fw-bold text-uppercase">
                                <i class="fas fa-wallet me-2"></i> Gerar Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="finance-card">
            <div class="table-responsive">
                <table class="table align-middle" id="financeTable">
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
                        <?php if(!empty($payments)): ?>
                            <?php foreach($payments as $p): ?>
                                <tr>
                                    <td class="p-3" data-order="<?php echo strtotime($p['data_pagamento']) . str_pad($p['id'], 10, '0', STR_PAD_LEFT); ?>">
                                        <?php echo date('d/m/Y H:i', strtotime($p['data_pagamento'])); ?>
                                    </td>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#financeTable').DataTable({
                "order": [[ 0, "desc" ]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json"
                },
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50]
            });
        });
    </script>
</body>
</html>
