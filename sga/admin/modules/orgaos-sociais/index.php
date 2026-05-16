<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Members
try {
    $stmt = $pdo->query("SELECT * FROM orgaos_sociais ORDER BY ordem_exibicao ASC, nome ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Órgãos Sociais & Direção</h2>
        <div class="text-muted small">Gestão dos membros que compõem os Órgãos Sociais da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="config.php" class="btn btn-outline-secondary w-auto px-4 shadow-sm py-3 fw-bold text-uppercase me-2"><i class="fas fa-sitemap me-2"></i> Organograma</a>
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-user-plus me-2"></i> Adicionar Membro</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Membro</th>
                    <th class="border-0 small text-uppercase py-3">Cargo / Função</th>
                    <th class="border-0 small text-uppercase py-3">Mandato</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Visibilidade</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum membro registado nos Órgãos Sociais.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                        <?php if($row['foto']): ?>
                                            <img src="/oagb/uploads/orgaos/<?php echo $row['foto']; ?>" class="img-fluid">
                                        <?php else: ?>
                                            <i class="fas fa-user text-muted"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fw-bold small"><?php echo $row['nome']; ?></div>
                                </div>
                            </td>
                            <td><span class="badge bg-login-subtle text-login p-2 px-3 small border-0 fw-bold"><?php echo strtoupper($row['cargo']); ?></span></td>
                            <td class="small">
                                <?php echo date('Y', strtotime($row['mandato_inicio'])); ?> - 
                                <?php echo $row['mandato_fim'] ? date('Y', strtotime($row['mandato_fim'])) : 'Atual'; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">PÚBLICO</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">OCULTO</span>
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
