<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Filter and Pagination
$status_filter = $_GET['status'] ?? 'todos';
$where = "WHERE 1=1";
if($status_filter != 'todos') {
    $where .= " AND status = :status";
}

try {
    $stmt = $pdo->prepare("SELECT s.*, a.nome_completo as advogado_nome 
                           FROM solicitacoes_advogados s 
                           LEFT JOIN advogados a ON s.advogado_atribuido_id = a.id 
                           $where 
                           ORDER BY s.created_at DESC");
    if($status_filter != 'todos') {
        $stmt->bindParam(':status', $status_filter);
    }
    $stmt->execute();
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Pedidos de Advogado</h2>
        <div class="text-muted small">Monitoria e gestão de pedidos de assistência jurídica enviados pelo site.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <form class="d-flex justify-content-md-end gap-2">
            <select name="status" class="form-select w-auto border-0 shadow-sm" onchange="this.form.submit()">
                <option value="todos" <?php echo $status_filter == 'todos' ? 'selected' : ''; ?>>Todos Status</option>
                <option value="pendente" <?php echo $status_filter == 'pendente' ? 'selected' : ''; ?>>Pendentes</option>
                <option value="atribuido" <?php echo $status_filter == 'atribuido' ? 'selected' : ''; ?>>Atribuídos</option>
                <option value="concluido" <?php echo $status_filter == 'concluido' ? 'selected' : ''; ?>>Concluídos</option>
                <option value="cancelado" <?php echo $status_filter == 'cancelado' ? 'selected' : ''; ?>>Cancelados</option>
            </select>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Solicitante</th>
                    <th class="border-0 small text-uppercase py-3">Área / Caso</th>
                    <th class="border-0 small text-uppercase py-3">Urgência</th>
                    <th class="border-0 small text-uppercase py-3">Atribuído a</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="6" class="text-center py-5">Nenhum pedido de advogado encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $row['nome_solicitante']; ?></div>
                                <div class="text-muted x-small"><?php echo $row['email']; ?> | <?php echo $row['telefone']; ?></div>
                            </td>
                            <td>
                                <div class="small fw-bold text-primary"><?php echo $row['area_juridica']; ?></div>
                                <div class="text-muted x-small text-truncate" style="max-width: 250px;"><?php echo $row['descricao_caso']; ?></div>
                            </td>
                            <td>
                                <?php if($row['urgencia'] == 'alta'): ?>
                                    <span class="badge bg-danger text-white px-2 py-1 x-small">ALTA</span>
                                <?php elseif($row['urgencia'] == 'media'): ?>
                                    <span class="badge bg-warning text-dark px-2 py-1 x-small">MÉDIA</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-white px-2 py-1 x-small">BAIXA</span>
                                <?php endif; ?>
                            </td>
                            <td class="small">
                                <?php if($row['advogado_nome']): ?>
                                    <div class="fw-bold"><i class="fas fa-user-tie me-1 opacity-50"></i> <?php echo $row['advogado_nome']; ?></div>
                                <?php else: ?>
                                    <span class="text-muted">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $status_class = [
                                    'pendente' => 'bg-warning-subtle text-warning',
                                    'atribuido' => 'bg-info-subtle text-info',
                                    'concluido' => 'bg-success-subtle text-success',
                                    'cancelado' => 'bg-danger-subtle text-danger'
                                ];
                                ?>
                                <span class="badge <?php echo $status_class[$row['status']]; ?> py-2 px-3 small border border-light">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-login p-2 px-3"><i class="fas fa-eye me-1"></i> Ver/Gerir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
