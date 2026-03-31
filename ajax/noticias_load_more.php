<?php
// ajax/noticias_load_more.php
require_once '../includes/functions.php';
require_once '../connect.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'todas';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$limit = 10;
$offset = ($pagina - 1) * $limit;

$params = [];
$sql = "SELECT * FROM noticias WHERE ativo = 1";

if ($categoria !== 'todas') {
    $sql .= " AND categoria = :categoria";
    $params['categoria'] = $categoria;
}

if (!empty($busca)) {
    $sql .= " AND (titulo LIKE :busca OR resumo LIKE :busca OR conteudo LIKE :busca)";
    $params['busca'] = "%$busca%";
}

$sql .= " ORDER BY data_publicacao DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

if ($noticias) {
    foreach ($noticias as $not) {
        $img_path = oagb_resolve_media_path($not->imagem_destaque ?? '', 'img/close-up-scales-justice.jpg');
        $data_not = date('d/m/Y', strtotime($not->data_publicacao));
        $link = "artigo.php?id=" . $not->id . "&slug=" . urlencode($not->slug);
        ?>
        <div class="col-lg-4 col-md-6 mb-4 animated fadeInUp" style="animation-duration: 0.6s;">
            <div class="card h-100 border-0 shadow-sm transition-all news-card-item overflow-hidden" style="border-radius: 15px;">
                <div class="position-relative overflow-hidden" style="aspect-ratio: 16/9;">
                    <img src="<?php echo htmlspecialchars($img_path); ?>" class="card-img-top object-fit-cover w-100 h-100 transition-all" alt="<?php echo htmlspecialchars($not->titulo); ?>">
                    <?php if (!empty($not->categoria)): ?>
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge rounded-pill bg-primary px-3 py-2 shadow" style="font-size: 0.75rem; font-weight: 600;"><?php echo strtoupper($not->categoria); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <small class="text-primary fw-bold mb-2 d-block" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                        <i class="far fa-calendar-alt me-1"></i> <?php echo $data_not; ?>
                    </small>
                    <h4 class="card-title mb-3 title-font" style="color: #4D1C21; font-size: 1.25rem; font-weight: 600; line-height: 1.4;">
                        <a href="<?php echo $link; ?>" class="text-decoration-none" style="color: #4D1C21; transition: color 0.3s;">
                            <?php echo htmlspecialchars($not->titulo); ?>
                        </a>
                    </h4>
                    <p class="card-text text-muted mb-4" style="font-family: 'Open Sans', sans-serif; font-size: 0.95rem; line-height: 1.6; color: #5B463F !important;">
                        <?php echo htmlspecialchars(truncate_text($not->resumo, 120)); ?>
                    </p>
                    <div class="mt-auto">
                        <a href="<?php echo $link; ?>" class="d-inline-block text-decoration-none">
                            <div class="btn-arrow-only">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
