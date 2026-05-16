<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$curso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$curso_id) { header("Location: index.php"); exit; }

// Fetch Course Details
$stmt = $pdo->prepare("SELECT * FROM gestao_cursos WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch();
if (!$curso) { header("Location: index.php"); exit; }

// Handle Status Change
if (isset($_GET['action']) && isset($_GET['enroll_id'])) {
    $enroll_id = intval($_GET['enroll_id']);
    $action = $_GET['action'];
    $allowed_status = ['confirmado', 'concluido', 'pendente'];
    if (in_array($action, $allowed_status)) {
        $stmt = $pdo->prepare("UPDATE gestao_cursos_inscritos SET status = ? WHERE id = ?");
        $stmt->execute([$action, $enroll_id]);
        echo "<script>window.location='inscritos.php?id=$curso_id';</script>"; exit;
    }
}

// Fetch Enrolled Students
// We try to join with both tables. This is a bit tricky due to potential ID overlap.
// For now, we'll try to find them in 'advogados' first.
$stmt = $pdo->prepare("
    SELECT i.*, 
           COALESCE(a.nome_completo, ae.nome_completo) as nome,
           COALESCE(a.numero_registo, ae.numero_registo) as registo,
           CASE WHEN a.id IS NOT NULL THEN 'Advogado' ELSE 'Estagiário' END as tipo
    FROM gestao_cursos_inscritos i
    LEFT JOIN advogados a ON i.advogado_id = a.id
    LEFT JOIN advogados_estagiarios ae ON i.advogado_id = ae.id
    WHERE i.curso_id = ?
");
$stmt->execute([$curso_id]);
$inscritos = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <h2 class="page-title">Alunos Inscritos</h2>
        <div class="text-muted small">Curso: <strong class="text-dark"><?php echo $curso['titulo']; ?></strong></div>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Voltar aos Cursos</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive" style="overflow: visible;">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Nome do Aluno</th>
                    <th>Cédula / Registo</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($inscritos)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Nenhum aluno inscrito nesta formação.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($inscritos as $i): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo htmlspecialchars($i['nome'] ?? 'N/A'); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($i['registo'] ?? 'N/A'); ?></span></td>
                            <td><?php echo $i['tipo']; ?></td>
                            <td>
                                <?php if($i['status'] == 'pendente'): ?>
                                    <span class="badge bg-warning-subtle text-warning px-3 rounded-pill">Pendente</span>
                                <?php elseif($i['status'] == 'confirmado'): ?>
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">Confirmado</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info px-3 rounded-pill">Concluído</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <?php if($i['status'] == 'pendente'): ?>
                                            <li><a class="dropdown-item small" href="inscritos.php?id=<?php echo $curso_id; ?>&enroll_id=<?php echo $i['id']; ?>&action=confirmado"><i class="fas fa-check me-2 text-success"></i> Confirmar</a></li>
                                        <?php endif; ?>
                                        <?php if($i['status'] == 'confirmado'): ?>
                                            <li><a class="dropdown-item small" href="inscritos.php?id=<?php echo $curso_id; ?>&enroll_id=<?php echo $i['id']; ?>&action=concluido"><i class="fas fa-graduation-cap me-2 text-info"></i> Marcar Concluído</a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item small text-danger" href="inscritos.php?id=<?php echo $curso_id; ?>&enroll_id=<?php echo $i['id']; ?>&action=pendente"><i class="fas fa-undo me-2"></i> Reverter para Pendente</a></li>
                                    </ul>
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
