<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Events
try {
    $stmt = $pdo->query("SELECT * FROM agenda ORDER BY data_evento DESC, id DESC");
    $agenda = $stmt->fetchAll();
} catch (PDOException $e) {
    $agenda = [];
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Agenda & Eventos</h2>
        <div class="text-muted small">Organize o calendário institucional e divulgue eventos.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-calendar-plus me-2"></i> Criar Evento</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase" style="width: 80px;">ID</th>
                        <th class="border-0 small text-uppercase">Evento / Título</th>
                        <th class="border-0 small text-uppercase">Data / Hora</th>
                        <th class="border-0 small text-uppercase">Localização</th>
                        <th class="border-0 small text-uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($agenda)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Ainda não existem eventos registados na agenda.</td></tr>
                    <?php else: ?>
                        <?php foreach($agenda as $event): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted border">#<?php echo $event['id']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($event['imagem_destaque'])): ?>
                                            <img src="/oagb/gestao/assets/uploads/files/<?php echo $event['imagem_destaque']; ?>" class="rounded me-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded me-3 border bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="far fa-calendar text-muted opacity-25"></i></div>
                                        <?php endif; ?>
                                        <div class="fw-bold small"><?php echo $event['titulo']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold"><?php echo date('d/m/Y', strtotime($event['data_evento'])); ?></div>
                                    <div class="text-muted x-small"><?php echo !empty($event['hora_evento']) ? $event['hora_evento'] : '--:--'; ?></div>
                                </td>
                                <td class="small opacity-75"><?php echo $event['local_evento']; ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1" title="Editar"><i class="far fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Apagar este evento da agenda?');" title="Eliminar"><i class="far fa-trash-alt"></i></a>
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
