<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch News
try {
    $stmt = $pdo->query("SELECT * FROM noticias ORDER BY data_publicacao DESC, id DESC");
    $noticias = $stmt->fetchAll();
} catch (PDOException $e) {
    $noticias = [];
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Notícias</h2>
        <div class="text-muted small">Crie, edite e organize os artigos do portal.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i> Nova Notícia</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase" style="width: 80px;">ID</th>
                        <th class="border-0 small text-uppercase">Título da Notícia / Artigo</th>
                        <th class="border-0 small text-uppercase">Data</th>
                        <th class="border-0 small text-uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($noticias)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">Ainda não existem notícias publicadas.</td></tr>
                    <?php else: ?>
                        <?php foreach($noticias as $item): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted border">#<?php echo $item['id']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($item['imagem_destaque'])): ?>
                                            <img src="/oagb/uploads/<?php echo $item['imagem_destaque']; ?>" class="rounded me-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded me-3 border bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="far fa-image text-muted opacity-25"></i></div>
                                        <?php endif; ?>
                                        <div class="fw-bold small"><?php echo $item['titulo']; ?></div>
                                    </div>
                                </td>
                                <td class="small"><?php echo !empty($item['data_publicacao']) ? date('d/m/Y', strtotime($item['data_publicacao'])) : '--/--/----'; ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1" title="Editar"><i class="far fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Tem a certeza que deseja eliminar esta notícia?');" title="Eliminar"><i class="far fa-trash-alt"></i></a>
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
