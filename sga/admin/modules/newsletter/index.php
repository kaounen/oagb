<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Editions (Builder)
$edicoes = $pdo->query("SELECT * FROM newsletter_edicoes ORDER BY created_at DESC")->fetchAll();

// Fetch Subscriptions
try {
    $stmt = $pdo->query("SELECT * FROM newsletter_subscricoes ORDER BY data_inscricao DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Newsletter & Comunicação</h2>
        <div class="text-muted small">Crie edições modulares e gira a sua base de contactos.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="d-flex gap-2 justify-content-end">
            <a href="builder.php" class="btn btn-dark w-auto px-4 shadow-sm"><i class="fas fa-magic me-2 text-warning"></i> Criar Nova Edição</a>
            <a href="send.php" class="btn btn-login w-auto px-4 shadow-sm"><i class="fas fa-paper-plane me-2"></i> Disparo Rápido</a>
        </div>
    </div>
</div>

<!-- Editions Section -->
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0"><i class="fas fa-history me-2 text-gold"></i> Edições Recentes (Builder)</h5>
    </div>
    <div class="card border-0 shadow-sm overflow-hidden p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase py-3">ID</th>
                        <th class="border-0 small text-uppercase py-3">Título da Edição</th>
                        <th class="border-0 small text-uppercase py-3">Data de Criação</th>
                        <th class="border-0 small text-uppercase py-3 text-center">Estado</th>
                        <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($edicoes)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Nenhuma edição criada no builder ainda.</td></tr>
                    <?php else: ?>
                        <?php foreach($edicoes as $ed): ?>
                            <tr>
                                <td class="ps-4 small text-muted">#<?php echo $ed['id']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($ed['titulo']); ?></div>
                                    <div class="text-muted x-small">Template: <?php echo $ed['design_template']; ?></div>
                                </td>
                                <td class="small"><?php echo date('d/m/Y H:i', strtotime($ed['created_at'])); ?></td>
                                <td class="text-center">
                                    <?php if($ed['status'] === 'rascunho'): ?>
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">Rascunho</span>
                                    <?php elseif($ed['status'] === 'aprovado'): ?>
                                        <span class="badge bg-info-subtle text-info px-3 py-2">Aprovado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success px-3 py-2">Enviado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="builder.php?id=<?php echo $ed['id']; ?>" class="btn btn-sm btn-outline-dark px-3" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="send.php?edition=<?php echo $ed['id']; ?>" class="btn btn-sm btn-login px-3" title="Enviar"><i class="fas fa-paper-plane"></i></a>
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

<hr class="my-5 opacity-10">

<!-- Subscribers Section -->
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold m-0"><i class="fas fa-users me-2 text-gold"></i> Subscritores da Newsletter</h5>
    <a href="export.php" class="btn btn-sm btn-outline-secondary px-3"><i class="fas fa-file-csv me-1"></i> Exportar CSV</a>
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
