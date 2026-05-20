<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Slides
try {
    $stmt = $pdo->query("SELECT * FROM carousel_slides ORDER BY ordem_exibicao ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão do Carousel (Banners)</h2>
        <div class="text-muted small">Altere os banners rotativos que aparecem no topo da página inicial.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i> Novo Banner</a>
    </div>
</div>

<div class="row mt-4">
    <?php if(empty($list)): ?>
        <div class="col-12 text-center py-5 border rounded bg-white mt-4 text-muted border-dashed border-2">
            Nenhum banner configurado ainda. <a href="add.php" class="fw-bold">Criar o primeiro?</a>
        </div>
    <?php else: ?>
        <?php foreach($list as $slide): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div style="height: 180px; position:relative;">
                        <img src="/oagb/uploads/<?php echo $slide['imagem']; ?>" class="card-img-top h-100 w-100" style="object-fit: cover;">
                        <span class="badge bg-login border border-white position-absolute top-0 end-0 m-3 fw-bold rounded-pill p-2 px-3 shadow-lg">#<?php echo $slide['ordem_exibicao']; ?></span>
                    </div>
                    <div class="card-body p-4">
                        <div class="fw-bold mb-1"><?php echo $slide['titulo']; ?></div>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($slide['subtitulo']); ?></p>
                        <div class="badge bg-light text-muted border x-small mb-3">Link: <?php echo !empty($slide['link_url']) ? $slide['link_url'] : 'Sem destino'; ?></div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <?php if($slide['ativo']): ?>
                                <span class="badge bg-success-subtle text-success py-2 px-3 small">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border py-2 px-3 small">Oculto</span>
                            <?php endif; ?>
                            
                            <div class="btn-group">
                                <a href="edit.php?id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1" title="Editar"><i class="far fa-edit"></i></a>
                                <a href="delete.php?id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar banner?');" title="Eliminar"><i class="far fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
