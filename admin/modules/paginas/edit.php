<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;
$slug_get = $_GET['slug'] ?? '';

if ($slug_get) {
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = ?");
    $stmt->execute([$slug_get]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$p) {
        // Auto-create missing page
        $stmt = $pdo->prepare("INSERT INTO paginas_ordem (titulo, slug, conteudo) VALUES (?, ?, ?)");
        $stmt->execute([ucfirst($slug_get), $slug_get, 'Conteúdo a ser definido...']);
        $id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $id = $p['id'];
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(!$p) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $slug = $_POST['slug'];
    $conteudo = $_POST['conteudo'];
    $ordem = $_POST['ordem_exibicao'];
    
    $stmt = $pdo->prepare("UPDATE paginas_ordem SET titulo = ?, slug = ?, conteudo = ?, ordem_exibicao = ? WHERE id = ?");
    $stmt->execute([$titulo, $slug, $conteudo, $ordem, $id]);
    
    require_once __DIR__ . '/../../includes/LogHelper.php';
    LogHelper::log($pdo, 'PAGE_UPDATE', "Editou a página institucional: $titulo", 'paginas_ordem', $id);

    header("Location: index.php?success=1"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Conteúdo</h2>
        <div class="text-muted small">Registo oficial de deliberações e reuniões deliberativas da Ordem.</div>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="form-label small fw-bold text-muted text-uppercase">Tìtulo da Página</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo $p['titulo']; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Slug URL</label>
                <input type="text" name="slug" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo $p['slug']; ?>">
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Conteúdo Institucional</label>
            <textarea name="conteudo" class="form-control border-0 bg-light p-4" rows="15" required><?php echo $p['conteudo']; ?></textarea>
        </div>

        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Ordem de Exibição</label>
                <input type="number" name="ordem_exibicao" class="form-control border-0 bg-light p-3" value="<?php echo $p['ordem_exibicao']; ?>">
            </div>
            <div class="col-md-8 text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">Guardar Alterações Site</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
