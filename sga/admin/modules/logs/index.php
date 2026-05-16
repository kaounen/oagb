<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Logs
try {
    $stmt = $pdo->query("SELECT * FROM logs_atividade ORDER BY created_at DESC LIMIT 500");
    $logs = $stmt->fetchAll();
} catch (PDOException $e) { $logs = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Registo de Atividades (Auditoria)</h2>
        <div class="text-muted small">Monitorização em tempo real de todas as acções administrativas.</div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Utilizador / IP</th>
                    <th class="border-0 small text-uppercase py-3">Ação / Descrição</th>
                    <th class="border-0 small text-uppercase py-3">Tabela / ID</th>
                    <th class="border-0 small text-uppercase py-3">Horário</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logs)): ?>
                    <tr><td colspan="4" class="text-center py-5">Nenhum registo de atividade encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($logs as $log): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $log['usuario_nome']; ?></div>
                                <div class="text-muted x-small"><?php echo $log['ip_address']; ?></div>
                            </td>
                            <td>
                                <div class="badge <?php 
                                    echo strpos($log['acao'], 'CREATE') !== false ? 'bg-success' : 
                                         (strpos($log['acao'], 'DELETE') !== false ? 'bg-danger' : 
                                         (strpos($log['acao'], 'UPDATE') !== false ? 'bg-primary' : 'bg-secondary')); 
                                ?> py-1 px-2 small mb-1"><?php echo $log['acao']; ?></div>
                                <div class="small opacity-75"><?php echo $log['descricao']; ?></div>
                            </td>
                            <td>
                                <div class="fw-bold small"><?php echo strtoupper($log['tabela_afetada'] ?: '-'); ?></div>
                                <div class="text-muted x-small">ID: <?php echo $log['registro_id'] ?: '-'; ?></div>
                            </td>
                            <td class="small opacity-75">
                                <i class="far fa-clock me-1 opacity-50"></i> <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
