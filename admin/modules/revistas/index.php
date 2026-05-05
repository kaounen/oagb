<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM revistas_oagb ORDER BY ano DESC, data_publicacao DESC");
    $items = $stmt->fetchAll();
} catch (PDOException $e) { $items = []; }
?>
<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Revista da OAGB</h2>
        <div class="text-muted small">Gerir edições da Revista da Ordem.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i>Nova Edição</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase" style="width:60px;">ID</th>
                        <th class="border-0 small text-uppercase">Título</th>
                        <th class="border-0 small text-uppercase">Edição</th>
                        <th class="border-0 small text-uppercase">Ano</th>
                        <th class="border-0 small text-uppercase">Estado</th>
                        <th class="border-0 small text-uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($items)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Sem edições registadas.</td></tr>
                    <?php else: ?>
                        <?php foreach($items as $item): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted border">#<?php echo $item->id; ?></span></td>
                                <td><div class="fw-bold small"><?php echo htmlspecialchars($item->titulo); ?></div></td>
                                <td class="small"><?php echo htmlspecialchars($item->edicao); ?></td>
                                <td class="small"><?php echo $item->ano; ?></td>
                                <td><span class="badge <?php echo $item->status === 'ativo' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo ucfirst($item->status); ?></span></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1" title="Editar"><i class="far fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar esta edição?');" title="Eliminar"><i class="far fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
