<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Subscriptions
try {
    $stmt = $pdo->query("SELECT * FROM newsletter_subscricoes ORDER BY data_inscricao DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Newsletter & Subscritores</h2>
        <div class="text-muted small">Gestão da base de dados de emails para envio de circulares.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="export.php" class="btn btn-login w-auto px-4"><i class="fas fa-file-csv me-2"></i> Exportar CSV</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">ID</th>
                    <th class="border-0 small text-uppercase py-3">Subscritor (Nome / E-mail)</th>
                    <th class="border-0 small text-uppercase py-3">Data Inscrição</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Confirmado</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="6" class="text-center py-5">Nenhum subscritor registado na base.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 font-monospace small text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="fw-bold small"><?php echo $row['nome'] ?: 'Sem Nome'; ?></div>
                                <div class="text-muted x-small"><?php echo $row['email']; ?></div>
                            </td>
                            <td class="small opacity-75"><?php echo date('d/m/Y H:i', strtotime($row['data_inscricao'])); ?></td>
                            <td class="text-center">
                                <?php if($row['confirmado']): ?>
                                    <i class="fas fa-check-circle text-success" title="E-mail Confirmado"></i>
                                <?php else: ?>
                                    <i class="far fa-circle text-muted" title="Aguarda Confirmação"></i>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">Subscrito</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar subscritor?');"><i class="far fa-trash-alt"></i></a>
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
