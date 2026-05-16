<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Processes
$stmt = $pdo->query("SELECT d.*, a.nome_completo as advogado_nome, r.nome_completo as relator_nome 
                       FROM gestao_disciplinar_processos d 
                       JOIN advogados a ON d.advogado_id = a.id 
                       LEFT JOIN advogados r ON d.relator_id = r.id 
                       ORDER BY d.data_abertura DESC");
$processos = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão & Ética Profissional</h2>
        <div class="text-muted small">Controlo de queixas, instrução de processos e aplicação de sanções.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="abrir.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-plus-circle me-2"></i> Abrir Novo Processo</a>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden bg-white mb-5">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr class="small text-uppercase fw-bold text-muted">
                    <th class="ps-4 border-0 py-3">No. Processo / Data</th>
                    <th class="border-0 py-3">Advogado Visado</th>
                    <th class="border-0 py-3">Relator Designado</th>
                    <th class="border-0 py-3 text-center">Estado de Instrução</th>
                    <th class="border-0 py-3 text-end pe-4">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($processos)): ?>
                    <tr><td colspan="5" class="text-center py-5 opacity-50">Nenhum processo disciplinar registado.</td></tr>
                <?php else: ?>
                    <?php foreach($processos as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-dark text-white p-2 px-3 fw-bold mb-1 d-inline-block"><?php echo $p['numero_processo']; ?></span>
                                <div class="x-small text-muted">Aberto em <?php echo date('d/m/Y', strtotime($p['data_abertura'])); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold small"><?php echo $p['advogado_nome']; ?></div>
                                <div class="x-small text-muted">Queixoso: <?php echo $p['queixoso_nome']; ?></div>
                            </td>
                            <td class="small"><?php echo $p['relator_nome'] ?: 'PENDENTE'; ?></td>
                            <td class="text-center">
                                <span class="badge py-2 px-3 <?php echo $p['status'] == 'sancionado' ? 'bg-danger text-white' : ($p['status'] == 'arquivado' ? 'bg-light text-dark' : 'bg-warning-subtle text-warning'); ?>">
                                    <?php echo strtoupper($p['status']); ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="detalhes.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-login-subtle text-login fw-bold border-0 p-2 px-3"><i class="fas fa-search me-1"></i> VER AUTOS</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-login-subtle { background: rgba(177, 162, 118, 0.1); }
    .text-login { color: #B1A276; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
