<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Departments
try {
    $stmt = $pdo->query("SELECT * FROM departamentos_contactos ORDER BY ordem ASC");
    $list = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Departamentos & Sedes</h2>
        <div class="text-muted small">Gestão de moradas, telefones, e-mails e horários de atendimento da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i> Novo Departamento</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3" style="width: 50px;">Ord.</th>
                    <th class="border-0 small text-uppercase py-3">Departamento / Unidade</th>
                    <th class="border-0 small text-uppercase py-3">Telefone / E-mail</th>
                    <th class="border-0 small text-uppercase py-3">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum departamento registado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold small text-muted"><?php echo $row->ordem; ?></td>
                            <td>
                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($row->titulo); ?></div>
                                <div class="x-small text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($row->morada); ?></div>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?php echo htmlspecialchars($row->telefone); ?></div>
                                <div class="x-small text-muted"><?php echo htmlspecialchars($row->email); ?></div>
                            </td>
                            <td>
                                <?php if($row->status == 'ativo'): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small border border-success-subtle">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">INATIVO</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row->id; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row->id; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar departamento?');"><i class="far fa-trash-alt"></i></a>
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
