<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Courses
$stmt = $pdo->query("SELECT *, (SELECT COUNT(*) FROM gestao_cursos_inscritos WHERE curso_id = c.id) as total_inscritos FROM gestao_cursos c ORDER BY data_inicio DESC");
$cursos = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Acadêmica & Cursos</h2>
        <div class="text-muted small">Administração de seminários, formações de estágio e cursos de especialização.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-plus-circle me-2"></i> Criar Nova Formação</a>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php if(empty($cursos)): ?>
        <div class="col-12 text-center py-5 opacity-50">Nenhum curso registado no sistema acadêmico.</div>
    <?php else: ?>
        <?php foreach($cursos as $c): ?>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="p-3 rounded-4 bg-login-subtle text-login"><i class="fas fa-graduation-cap fa-2x"></i></div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item small" href="edit.php?id=<?php echo $c['id']; ?>"><i class="fas fa-edit me-1"></i> Editar</a></li>
                                <li><a class="dropdown-item small text-danger" href="delete.php?id=<?php echo $c['id']; ?>"><i class="fas fa-trash-alt me-1"></i> Eliminar</a></li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo $c['titulo']; ?></h5>
                    <div class="small fw-bold text-muted mb-2"><i class="fas fa-calendar-day me-1"></i> <?php echo date('d/m/Y', strtotime($c['data_inicio'])); ?></div>
                    <p class="small text-muted mb-4 opacity-75"><?php echo substr($c['descricao'], 0, 100); ?>...</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge bg-light text-dark fw-bold border p-2 px-3 small"><?php echo $c['total_inscritos']; ?> INSCRITOS</span>
                        <a href="inscritos.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-dark px-3 fw-bold rounded-pill">VER ALUNOS</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
