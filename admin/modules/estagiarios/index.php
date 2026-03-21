<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Estagiarios with Orientador
try {
    $stmt = $pdo->query("SELECT e.*, a.nome_completo as orientador_nome 
                         FROM advogados_estagiarios e 
                         LEFT JOIN advogados a ON e.orientador_id = a.id 
                         ORDER BY e.nome_completo ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Corpo de Estagiários</h2>
        <div class="text-muted small">Gestão de novos talentos e acompanhamento de estágios profissionais.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-user-graduate me-2"></i> Novo Estagiário</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Estagiário / Cédula</th>
                    <th class="border-0 small text-uppercase py-3">Orientador Responsável</th>
                    <th class="border-0 small text-uppercase py-3">Período</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Estado</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum estagiário registado na base de dados.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $e): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3"><i class="fas fa-user-graduate text-muted opacity-50"></i></div>
                                    <div>
                                        <div class="fw-bold small"><?php echo $e['nome_completo']; ?></div>
                                        <div class="text-muted x-small"><?php echo $e['numero_registo']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold"><?php echo $e['orientador_nome'] ?: 'Sem Orientador'; ?></div>
                                <div class="text-muted x-small">Patrono atribuído</div>
                            </td>
                            <td class="small opacity-75">
                                <div class="fw-bold"><?php echo date('d/m/Y', strtotime($e['data_inicio_estagio'])); ?></div>
                                <div class="x-small">Início do estágio</div>
                            </td>
                            <td class="text-center">
                                <span class="badge py-2 px-3 small <?php 
                                    echo $e['status'] == 'ativo' ? 'bg-success-subtle text-success' : 
                                         ($e['status'] == 'concluido' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger'); 
                                ?>"> <?php echo strtoupper($e['status']); ?> </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="fas fa-user-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar estagiário?')"><i class="fas fa-trash"></i></a>
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
