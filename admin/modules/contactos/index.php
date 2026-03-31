<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Messages
try {
    $stmt = $pdo->query("SELECT * FROM mensagens_contacto ORDER BY created_at DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Mensagens & Contactos</h2>
        <div class="text-muted small">Inbox oficial de mensagens recebidas pelo formulário de contacto do site.</div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Remetente</th>
                    <th class="border-0 small text-uppercase py-3">Assunto / Mensagem</th>
                    <th class="border-0 small text-uppercase py-3">Data</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhuma mensagem recebida até ao momento.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr class="<?php echo !$row['lida'] ? 'fw-bold bg-light-subtle':''; ?>">
                            <td class="ps-4">
                                <div class="small"><?php echo $row['nome']; ?></div>
                                <div class="text-muted x-small"><?php echo $row['email']; ?></div>
                            </td>
                            <td>
                                <div class="small fw-bold <?php echo !$row['lida'] ? 'text-primary':''; ?>"><?php echo $row['assunto']; ?></div>
                                <div class="text-muted x-small text-truncate" style="max-width: 350px;"><?php echo $row['mensagem']; ?></div>
                            </td>
                            <td class="small text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['respondida']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">REPENDIDO</span>
                                <?php elseif(!$row['lida']): ?>
                                    <span class="badge bg-primary py-2 px-3 small">NOVA</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">LIDA</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info p-2 me-1" title="Ver Mensagem"><i class="far fa-envelope-open"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar mensagem?');"><i class="far fa-trash-alt"></i></a>
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
