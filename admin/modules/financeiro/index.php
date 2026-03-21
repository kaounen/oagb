<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Financial Overview
$total_recebido = $pdo->query("SELECT SUM(valor_pago) FROM finan_pagamentos WHERE status = 'confirmado'")->fetchColumn() ?: 0;
$pendentes_analise = $pdo->query("SELECT COUNT(*) FROM finan_pagamentos WHERE status = 'pendente'")->fetchColumn() ?: 0;

// Recent Payments
try {
    $stmt = $pdo->query("SELECT p.*, a.nome_completo as advogado, t.nome as tipo 
                         FROM finan_pagamentos p 
                         LEFT JOIN advogados a ON p.advogado_id = a.id 
                         LEFT JOIN finan_tipos_pagamento t ON p.tipo_pagamento_id = t.id 
                         ORDER BY p.data_pagamento DESC LIMIT 100");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão Financeira & Pagamentos</h2>
        <div class="text-muted small">Controle de quotas, taxas de inscrição e propinas institucionais.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="certidoes.php" class="btn btn-login-subtle text-login w-auto px-4 me-2 border-login-subtle border"><i class="fas fa-certificate me-2"></i> Emitir Certidões</a>
        <a href="alertas_quotas.php" class="btn btn-danger w-auto px-4 me-2"><i class="fas fa-exclamation-triangle me-2"></i> Verificar Atrasos em Quotas</a>
        <a href="novo_recebimento.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus-circle me-2"></i> Registar Recebimento</a>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white p-4 h-100">
            <div class="small text-uppercase opacity-75 fw-bold mb-2">Total Recebido (Geral)</div>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_recebido, 2, ',', '.'); ?> CFA</h3>
            <div class="mt-3 small"><i class="fas fa-check-circle me-1"></i> Confirmados na base</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark p-4 h-100">
            <div class="small text-uppercase opacity-75 fw-bold mb-2">Pagamentos Pendentes</div>
            <h3 class="fw-bold mb-0"><?php echo $pendentes_analise; ?> Recibos</h3>
            <div class="mt-3 small"><i class="fas fa-clock me-1"></i> Aguardando validação manual</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-dark text-white p-4 h-100">
            <div class="small text-uppercase opacity-75 fw-bold mb-2">Taxas Ativas</div>
            <h3 class="fw-bold mb-0">Quotas & Inscrições</h3>
            <div class="mt-3 small"><i class="fas fa-cog me-1"></i> 4 tipos de taxas configurados</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="card-header bg-white border-0 p-4 pb-0">
        <h5 class="fw-bold mb-0">Últimas Transações</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover mt-3">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Data</th>
                    <th class="border-0 small text-uppercase py-3">Advogado / Beneficiário</th>
                    <th class="border-0 small text-uppercase py-3">Descrição do Tipo</th>
                    <th class="border-0 small text-uppercase py-3">Valor</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="6" class="text-center py-5">Nenhum pagamento registado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $p): ?>
                        <tr>
                            <td class="ps-4 small opacity-75"><?php echo date('d/m/Y', strtotime($p['data_pagamento'])); ?></td>
                            <td>
                                <div class="fw-bold small"><?php echo $p['advogado'] ?: 'Depósito Avulso'; ?></div>
                                <div class="text-muted x-small"><?php echo ucfirst($p['metodo_pagamento']); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border py-2 px-3 small"><?php echo $p['tipo']; ?></span></td>
                            <td class="fw-bold text-success small"><?php echo number_format($p['valor_pago'], 2, ',', '.'); ?> CFA</td>
                            <td class="text-center">
                                <?php if($p['status'] == 'confirmado'): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">Validado</span>
                                <?php elseif($p['status'] == 'pendente'): ?>
                                    <span class="badge bg-warning-subtle text-warning py-2 px-3 small">Pendente</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger py-2 px-3 small">Cancelado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="view_recibo.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="fas fa-receipt"></i></a>
                                    <a href="validar.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-success p-2"><i class="fas fa-check"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
