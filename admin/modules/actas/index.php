<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Actas
$stmt = $pdo->query("SELECT * FROM gestao_actas ORDER BY data_reuniao DESC");
$actas = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Livro de Actas Digital</h2>
        <div class="text-muted small">Registo oficial de deliberações, reuniões e assembleias da Ordem.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-file-signature me-2"></i> Lavrar Nova Acta</a>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php if(empty($actas)): ?>
        <div class="col-12 text-center py-5 opacity-50">Nenhuma acta registada no sistema.</div>
    <?php else: ?>
        <?php foreach($actas as $a): ?>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="badge bg-login-subtle text-login p-3 rounded-4"><i class="fas fa-file-alt fa-2x"></i></div>
                        <span class="badge py-2 px-3 small border <?php echo $a['status'] == 'finalizada' ? 'bg-success text-white border-success' : 'bg-warning-subtle text-warning border-warning-subtle'; ?>">
                            <?php echo strtoupper($a['status']); ?>
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo $a['titulo']; ?></h5>
                    <div class="text-muted small mb-3">Data da Reunião: <b><?php echo date('d/m/Y', strtotime($a['data_reuniao'])); ?></b></div>
                    <div class="text-truncate-3 small text-muted opacity-75 mb-4"><?php echo strip_tags($a['conteudo']); ?></div>
                    <div class="d-flex gap-2">
                        <a href="view.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-dark px-3 fw-bold rounded-pill">LER COMPLETA</a>
                        <?php if($a['status'] == 'rascunho'): ?>
                            <a href="edit.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-dark px-3 fw-bold rounded-pill border-0 shadow-none"><i class="fas fa-edit me-1"></i>EDITAR</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
