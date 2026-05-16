<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Commissions
try {
    $stmt = $pdo->query("SELECT * FROM comissoes ORDER BY nome ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Comissões & Órgãos</h2>
        <div class="text-muted small">Administração de comissões de trabalho, grupos de estudo e órgãos deliberativos da Ordem.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm"><i class="fas fa-plus-circle me-2"></i> Nova Comissão</a>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php if(empty($list)): ?>
        <div class="col-12 text-center py-5 opacity-50">Nenhuma comissão registada.</div>
    <?php else: ?>
        <?php foreach($list as $c): ?>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="p-3 rounded-4 bg-login-subtle text-login"><i class="fas fa-users-cog fa-2x"></i></div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item small" href="edit.php?id=<?php echo $c['id']; ?>"><i class="fas fa-edit me-1"></i> Editar Estrutura</a></li>
                                <li><a class="dropdown-item small text-danger" href="delete.php?id=<?php echo $c['id']; ?>"><i class="fas fa-trash-alt me-1"></i> Eliminar</a></li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2"><?php echo $c['nome']; ?></h5>
                    <p class="small text-muted mb-4 opacity-75"><?php echo $c['descricao']; ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <?php 
                        $m_count = !empty($c['membros']) ? count(explode(',', $c['membros'])) : 0;
                        ?>
                        <span class="badge bg-light text-dark fw-bold border p-2 px-3 small"><?php echo $m_count; ?> MEMBROS</span>
                        <a href="edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-dark px-3 fw-bold rounded-pill">EDITAR</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm p-4 bg-primary-subtle text-primary border border-primary">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle fa-2x me-3 opacity-50"></i>
        <div>
            <h6 class="fw-bold mb-1">Nota Estratégica</h6>
            <p class="small mb-0">As comissões são a base da produção técnica e intelectual da Ordem. Garanta a atualização constante dos membros para que a comunicação institucional flua corretamente.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
