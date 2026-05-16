<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Timeline
try {
    $stmt = $pdo->query("SELECT * FROM timeline_marcos ORDER BY ano ASC, ordem ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Linha do Tempo (Timelime)</h2>
        <div class="text-muted small">Marcos históricos fundamentais na trajetória da Ordem.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-history me-2"></i> Adicionar Marco Histórico</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Ano</th>
                    <th class="border-0 small text-uppercase py-3">Evento / Marco</th>
                    <th class="border-0 small text-uppercase py-3">Descrição</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="4" class="text-center py-5">Nenhum marco histórico registado na timeline.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary fs-5"><?php echo $row['ano']; ?></td>
                            <td><div class="fw-bold small"><?php echo $row['titulo']; ?></div></td>
                            <td class="small opacity-75 text-truncate" style="max-width: 400px;"><?php echo strip_tags($row['descricao']); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar este marco da timeline?');"><i class="far fa-trash-alt"></i></a>
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
