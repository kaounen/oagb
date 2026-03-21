<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Pages
$stmt = $pdo->query("SELECT * FROM paginas_ordem ORDER BY ordem_exibicao ASC");
$paginas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Páginas Institucionais</h2>
        <div class="text-muted small">Gestão de conteúdos fixos como História, Missão, Organograma, etc.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-plus-circle me-2"></i> Criar Nova Página</a>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php if(empty($paginas)): ?>
        <div class="col-12 text-center py-5 opacity-50">Nenhuma página institucional configurada.</div>
    <?php else: ?>
        <?php foreach($paginas as $p): ?>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between mb-3 align-items-start">
                        <div class="p-3 rounded-4 bg-login-subtle text-login"><i class="fas fa-file-invoice fa-2x"></i></div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle border-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item small" href="edit.php?id=<?php echo $p['id']; ?>"><i class="fas fa-edit me-1"></i> Editar</a></li>
                                <li><a class="dropdown-item small text-danger" href="delete.php?id=<?php echo $p['id']; ?>"><i class="fas fa-trash-alt me-1"></i> Eliminar</a></li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo $p['titulo']; ?></h5>
                    <div class="small fw-bold text-muted mb-2"><i class="fas fa-link me-1"></i> /<?php echo $p['slug']; ?></div>
                    <p class="small text-muted mb-4 opacity-75"><?php echo substr(strip_tags($p['conteudo']), 0, 80); ?>...</p>
                    <div class="d-flex justify-content-between align-items-baseline mt-auto border-top pt-3">
                        <span class="x-small text-muted text-uppercase fw-bold">Ordem: <?php echo $p['ordem_exibicao']; ?></span>
                        <a href="/oagb/pagina.php?s=<?php echo $p['slug']; ?>" target="_blank" class="btn btn-sm btn-outline-dark px-3 fw-bold rounded-pill border-0">VER SITE</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
