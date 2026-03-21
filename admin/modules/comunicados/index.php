<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Announcements
try {
    $stmt = $pdo->query("SELECT * FROM anuncios ORDER BY data_inicio DESC, ordem_exibicao ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Comunicados & Anúncios</h2>
        <div class="text-muted small">Gerencie informativos oficiais e promocionais do portal.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i> Novo Anúncio</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">ID</th>
                    <th class="border-0 small text-uppercase py-3">Título / Comunicado</th>
                    <th class="border-0 small text-uppercase py-3">Data</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum anúncio encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 text-muted small">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="fw-bold small"><?php echo $row['titulo']; ?></div>
                                <div class="text-muted x-small"><?php echo substr(strip_tags($row['descricao']), 0, 100); ?>...</div>
                            </td>
                            <td class="small">
                                <div><i class="far fa-calendar-alt me-1 opacity-50"></i> <?php echo date('d/m/Y', strtotime($row['data_inicio'])); ?></div>
                                <?php if($row['data_fim']): ?>
                                    <div class="text-muted x-small"><i class="fas fa-arrow-right me-1 opacity-50"></i> <?php echo date('d/m/Y', strtotime($row['data_fim'])); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar permanentemente?');"><i class="far fa-trash-alt"></i></a>
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
