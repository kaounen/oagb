<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Agreements
try {
    $stmt = $pdo->query("SELECT * FROM parcerias_internacionais ORDER BY data_assinatura DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Cooperação Internacional</h2>
        <div class="text-muted small">Gestão de protocolos, parcerias e acordos internacionais da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-file-contract me-2"></i> Novo Acordo / Protocolo</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Entidade Parceira</th>
                    <th class="border-0 small text-uppercase py-3">País</th>
                    <th class="border-0 small text-uppercase py-3">Tipo de Acordo</th>
                    <th class="border-0 small text-uppercase py-3">Data Assinatura</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="6" class="text-center py-5">Nenhum acordo internacional registado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold small text-primary"><?php echo $row['entidade_parceira']; ?></td>
                            <td><span class="small badge bg-light text-muted border px-2"><?php echo $row['pais']; ?></span></td>
                            <td class="small opacity-75"><?php echo $row['tipo_acordo']; ?></td>
                            <td class="small"><?php echo $row['data_assinatura'] ? date('d/m/Y', strtotime($row['data_assinatura'])) : '-'; ?></td>
                            <td class="text-center">
                                <?php if($row['status'] == 'Ativo'): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small border border-success-subtle">VIGENTE</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small"><?php echo strtoupper($row['status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar acordo?');"><i class="far fa-trash-alt"></i></a>
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
