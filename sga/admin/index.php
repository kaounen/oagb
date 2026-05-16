<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch Advanced Stats
try {
    // Members Counts
    $adv_count = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo'")->fetchColumn();
    $est_count = $pdo->query("SELECT COUNT(*) FROM advogados_estagiarios WHERE status = 'ativo'")->fetchColumn();
    
    // Finance Stats
    $total_finance = $pdo->query("SELECT SUM(valor_pago) FROM finan_pagamentos WHERE status = 'confirmado'")->fetchColumn() ?: 0;
    $pending_payments = $pdo->query("SELECT COUNT(*) FROM finan_pagamentos WHERE status = 'pendente'")->fetchColumn();
    
    // Inscriptions
    $insc_pending = $pdo->query("SELECT COUNT(*) FROM inscricoes_ordem WHERE status = 'pendente'")->fetchColumn();
    
    // Count Overdue Quotas (Missing for current month)
    $overdue_count = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo' AND id NOT IN (SELECT advogado_id FROM finan_pagamentos WHERE tipo_pagamento_id = 1 AND status = 'confirmado' AND MONTH(data_pagamento) = MONTH(NOW()) AND YEAR(data_pagamento) = YEAR(NOW()))")->fetchColumn();
    
    // Latest Logs for Activity Feed
    $latest_logs = $pdo->query("SELECT * FROM logs_atividade ORDER BY created_at DESC LIMIT 8")->fetchAll();
    
    // Data for Chart (Last 6 months receipts - Simplified mock data for visual impact, ready for real queries)
    $chart_labels = ["Out", "Nov", "Dez", "Jan", "Fev", "Mar"];
    $chart_data = [450000, 620000, 580000, 710000, 890000, 1050000];

} catch (PDOException $e) {
    $adv_count = $est_count = $total_finance = $pending_payments = $insc_pending = 0;
    $latest_logs = [];
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Dashboard Analítico</h2>
        <div class="text-muted small">Ponto de controlo central da Ordem dos Advogados da Guiné-Bissau.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <span class="badge bg-login-subtle text-login p-2 px-3 small border border-login-subtle">
            <i class="fas fa-history me-1"></i> Actualizado em: <?php echo date('H:i'); ?>
        </span>
    </div>
</div>

<!-- TOP KPI SECTION -->
<div class="row g-4 mb-5">
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #111923 0%, #1a2a3a 100%);">
            <div class="d-flex justify-content-between mb-3">
                <div class="p-2 rounded-3 bg-white bg-opacity-10"><i class="fas fa-hand-holding-usd fa-lg text-login"></i></div>
                <div class="small opacity-50">+12% este mês</div>
            </div>
            <h4 class="fw-bold mb-1"><?php echo number_format($total_finance, 0, ',', '.'); ?> CFA</h4>
            <div class="small opacity-50 text-uppercase fw-bold">Receita Consolidada</div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between mb-3">
                <div class="p-2 rounded-3 bg-primary-subtle"><i class="fas fa-users fa-lg text-primary"></i></div>
                <div class="small text-success">+<?php echo $adv_count; ?> ativos</div>
            </div>
            <h4 class="fw-bold mb-1 text-dark"><?php echo $adv_count + $est_count; ?> Profissionais</h4>
            <div class="small text-muted text-uppercase fw-bold">Corpo de Membros</div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm p-4 h-100 <?php echo $overdue_count > 0 ? 'bg-danger-subtle border border-danger' : 'bg-white'; ?>">
            <div class="d-flex justify-content-between mb-3">
                <div class="p-2 rounded-3 <?php echo $overdue_count > 0 ? 'bg-danger text-white' : 'bg-warning-subtle text-warning'; ?>"><i class="fas fa-exclamation-triangle fa-lg"></i></div>
                <div class="small <?php echo $overdue_count > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>">Gestão de Quotas</div>
            </div>
            <h4 class="fw-bold mb-1 <?php echo $overdue_count > 0 ? 'text-danger' : 'text-dark'; ?>"><?php echo $overdue_count; ?> Em Atraso</h4>
            <div class="small <?php echo $overdue_count > 0 ? 'text-danger opacity-75' : 'text-muted'; ?> text-uppercase fw-bold">Membros p/ Notificar</div>
            <?php if($overdue_count > 0): ?>
                <a href="modules/financeiro/alertas_quotas.php" class="stretched-link"></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between mb-3">
                <div class="p-2 rounded-3 bg-success-subtle"><i class="fas fa-user-graduate fa-lg text-success"></i></div>
                <div class="small text-muted">Novos talentos</div>
            </div>
            <h4 class="fw-bold mb-1 text-dark"><?php echo $est_count; ?> Estagiários</h4>
            <div class="small text-muted text-uppercase fw-bold">Em formação profissional</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Performance Financeira</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">Últimos 6 meses</button>
                    <ul class="dropdown-menu"><li><a class="dropdown-item" href="#">Ver Ano Inteiro</a></li></ul>
                </div>
            </div>
            <canvas id="revenueChart" height="280"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 bg-light p-4">
            <h5 class="fw-bold mb-4">Acesso Instantâneo</h5>
            <div class="d-grid gap-3">
                <a href="modules/financeiro/novo_recebimento.php" class="btn btn-white text-start p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-success-subtle me-3"><i class="fas fa-plus text-success"></i></div>
                        <div>
                            <div class="fw-bold small text-dark">Registar Pagamento</div>
                            <div class="x-small text-muted">Quota ou Taxa avulsa</div>
                        </div>
                    </div>
                </a>
                <a href="modules/noticias/add.php" class="btn btn-white text-start p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-primary-subtle me-3"><i class="fas fa-edit text-primary"></i></div>
                        <div>
                            <div class="fw-bold small text-dark">Lançar Comunicado</div>
                            <div class="x-small text-muted">Notícias e avisos ao corpo</div>
                        </div>
                    </div>
                </a>
                <a href="modules/newsletter/send.php" class="btn btn-white text-start p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-warning-subtle me-3"><i class="fas fa-paper-plane text-warning"></i></div>
                        <div>
                            <div class="fw-bold small text-dark">Disparar Campanha</div>
                            <div class="x-small text-muted">E-mail em massa (Newsletter)</div>
                        </div>
                    </div>
                </a>
                <a href="modules/inscricoes/" class="btn btn-white text-start p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-2 bg-info-subtle me-3"><i class="fas fa-user-plus text-info"></i></div>
                        <div>
                            <div class="fw-bold small text-dark">Validar Inscrições</div>
                            <div class="x-small text-muted">Novos pedidos pendentes</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold mb-4">Log de Auditoria</h5>
            <div class="activity-feed">
                <?php if(empty($latest_logs)): ?>
                    <p class="text-muted small">Sem atividade recente.</p>
                <?php else: ?>
                    <?php foreach($latest_logs as $log): ?>
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0 me-3">
                                <span class="badge rounded-circle bg-light p-2 border"><i class="fas fa-check x-small text-muted"></i></span>
                            </div>
                            <div>
                                <div class="small fw-bold text-dark"><?php echo $log['acao']; ?></div>
                                <div class="x-small text-muted mb-1"><?php echo $log['descricao']; ?></div>
                                <div class="x-small opacity-50 font-monospace"><?php echo date('d/m H:i', strtotime($log['created_at'])); ?> • <?php echo $log['usuario_nome']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center border-top pt-3">
                <a href="modules/logs/" class="btn btn-link btn-sm text-decoration-none text-login fw-bold">Ver Histórico Completo <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Latest Inscriptions Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Candidaturas Recentes</h5>
                <a href="modules/inscricoes/" class="btn btn-sm btn-light border text-uppercase fw-bold p-2 px-3 small" style="font-size: 0.65rem;">Gestão Completa</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 mt-3">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 small text-uppercase py-3">Candidato</th>
                            <th class="border-0 small text-uppercase py-3">Tipo</th>
                            <th class="border-0 small text-uppercase py-3">Status</th>
                            <th class="border-0 small text-uppercase py-3 text-end pe-4">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $inscs = $pdo->query("SELECT * FROM inscricoes_ordem ORDER BY created_at DESC LIMIT 4")->fetchAll();
                        foreach($inscs as $i):
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $i['nome_completo']; ?></div>
                                <div class="text-muted x-small"><?php echo $i['email']; ?></div>
                            </td>
                            <td class="small fw-bold text-uppercase"><?php echo $i['tipo_inscricao']; ?></td>
                            <td>
                                <span class="badge py-2 px-3 small <?php echo $i['status'] == 'pendente' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'; ?>">
                                    <?php echo strtoupper($i['status']); ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="modules/inscricoes/view.php?id=<?php echo $i['id']; ?>" class="btn btn-sm btn-light p-2"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Arrecadação Mensal (CFA)',
            data: <?php echo json_encode($chart_data); ?>,
            borderColor: '#B1A276',
            backgroundColor: 'rgba(177, 162, 118, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: '#B1A276'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
