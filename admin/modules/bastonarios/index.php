<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Bastonarios
try {
    $stmt = $pdo->query("SELECT * FROM bastonarios ORDER BY data_inicio_mandato DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Galeria de Bastonários</h2>
        <div class="text-muted small">Registo histórico de todos os Bastonários da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-user-plus me-2"></i> Adicionar Bastonário</a>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4">Bastonário registado com sucesso!</div>
<?php endif; ?>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">ID</th>
                    <th class="border-0 small text-uppercase py-3">Bastonário</th>
                    <th class="border-0 small text-uppercase py-3">Mandato</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum registo histórico encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 font-monospace small text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                        <?php if($row['foto_url']): ?>
                                            <img src="/oagb/uploads/bastonarios/<?php echo $row['foto_url']; ?>" class="img-fluid">
                                        <?php else: ?>
                                            <i class="fas fa-user text-muted"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fw-bold small"><?php echo $row['nome_completo']; ?></div>
                                </div>
                            </td>
                            <td class="small">
                                <span class="text-muted">De:</span> <?php echo date('Y', strtotime($row['data_inicio_mandato'])); ?> 
                                <span class="text-muted ms-1">Até:</span> <?php echo $row['data_fim_mandato'] ? date('Y', strtotime($row['data_fim_mandato'])) : 'Presente'; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['is_atual']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small border border-success-subtle">ATUAL BASTONÁRIO</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">MANDATO CONCLUÍDO</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar permanentemente do registo histórico?');"><i class="far fa-trash-alt"></i></a>
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
