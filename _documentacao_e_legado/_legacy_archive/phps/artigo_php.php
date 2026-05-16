<?php
require_once 'connect.php';

// Verificar se o slug foi fornecido
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: noticias.php");
    exit();
}

$slug = sanitize($_GET['slug']);

// Buscar a notícia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header("Location: noticias.php");
    exit();
}

// Incrementar visualizações
$stmt = $pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?");
$stmt->execute([$noticia->id]);

// Buscar notícias relacionadas (mesma categoria)
$stmt = $pdo->prepare("SELECT id, titulo, slug, imagem_destaque, data_publicacao FROM noticias WHERE categoria = ? AND id != ? AND ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
$stmt->execute([$noticia->categoria, $noticia->id]);
$noticias_relacionadas = $stmt->fetchAll();

// Buscar categorias para sidebar
$stmt = $pdo->prepare("SELECT categoria, COUNT(*) as total FROM noticias WHERE ativo = 1 AND categoria != '' GROUP BY categoria ORDER BY categoria");
$stmt->execute();
$categorias = $stmt->fetchAll();

// Buscar notícias recentes para sidebar
$stmt = $pdo->prepare("SELECT id, titulo, slug, imagem_destaque, data_publicacao FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 5");
$stmt->execute();
$noticias_recentes = $stmt->fetchAll();

$page_title = $noticia->titulo;
$breadcrumb = "Notícias";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($noticia->titulo); ?> - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo htmlspecialchars($noticia->titulo); ?>, OAGB, Notícias" name="keywords">
    <meta content="<?php echo htmlspecialchars(truncate_text($noticia->resumo, 160)); ?>" name="description">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($noticia->titulo); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(truncate_text($noticia->resumo, 200)); ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/img/noticias/<?php echo $noticia->imagem_destaque ?: 'default.jpg'; ?>">
    <meta property="og:url" content="<?php echo SITE_URL; ?>/artigo.php?slug=<?php echo $noticia->slug; ?>">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Rua 15, Bissau, Guiné-Bissau</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+245 955 475 889</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>info@oagb.gw</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Header Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Artigo</h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="noticias.php" class="h5 text-white">Notícias</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white">Artigo</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar & Header End -->

    <!-- Full Screen Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(9, 30, 62, .7);">
                <div class="modal-header border-0">
                    <button type="button" class="btn bg-white btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa">
                        <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Blog Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Blog Detail Start -->
                    <div class="mb-5">
                        <?php if ($noticia->imagem_destaque): ?>
                            <img class="img-fluid w-100 rounded mb-5" src="img/noticias/<?php echo $noticia->imagem_destaque; ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <?php if ($noticia->categoria): ?>
                                <span class="badge bg-primary me-2"><?php echo htmlspecialchars($noticia->categoria); ?></span>
                            <?php endif; ?>
                            <small class="text-muted" style="font-family: 'Open Sans';">
                                Publicado em <?php echo format_date($noticia->data_publicacao, 'd \d\e F \d\e Y'); ?>
                                <?php if ($noticia->autor): ?>
                                    por <?php echo htmlspecialchars($noticia->autor); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        
                        <h1 class="mb-4" style="color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: bold;">
                            <?php echo htmlspecialchars($noticia->titulo); ?>
                        </h1>
                        
                        <?php if ($noticia->resumo): ?>
                            <div class="alert alert-light border-start border-primary border-5 mb-4">
                                <p class="mb-0 fw-bold" style="font-family: 'Open Sans'; font-size: 1.1em;">
                                    <?php echo htmlspecialchars($noticia->resumo); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="content" style="font-family: 'Open Sans'; line-height: 1.8;">
                            <?php echo $noticia->conteudo; ?>
                        </div>
                        
                        <!-- Tags -->
                        <?php if ($noticia->tags): ?>
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3" style="font-family: 'Libre Baskerville';">Tags:</h6>
                            <?php
                            $tags = explode(',', $noticia->tags);
                            foreach ($tags as $tag):
                                $tag = trim($tag);
                                if ($tag):
                            ?>
                                <span class="badge bg-light text-dark me-2 mb-2"><?php echo htmlspecialchars($tag); ?></span>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Social Share -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3" style="font-family: 'Libre Baskerville';">Partilhar:</h6>
                            <div class="d-flex">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/artigo.php?slug=' . $noticia->slug); ?>" 
                                   target="_blank" class="btn btn-primary btn-square me-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/artigo.php?slug=' . $noticia->slug); ?>&text=<?php echo urlencode($noticia->titulo); ?>" 
                                   target="_blank" class="btn btn-info btn-square me-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/artigo.php?slug=' . $noticia->slug); ?>" 
                                   target="_blank" class="btn btn-primary btn-square me-2">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="mailto:?subject=<?php echo urlencode($noticia->titulo); ?>&body=<?php echo urlencode($noticia->titulo . ' - ' . SITE_URL . '/artigo.php?slug=' . $noticia->slug); ?>" 
                                   class="btn btn-secondary btn-square">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Blog Detail End -->
                    
                    <!-- Related Articles -->
                    <?php if ($noticias_relacionadas): ?>
                    <div class="mb-5">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Artigos Relacionados</h3>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($noticias_relacionadas as $relacionada): ?>
                            <div class="col-md-4">
                                <div class="blog-item bg-light rounded overflow-hidden h-100">
                                    <div class="blog-img position-relative overflow-hidden">
                                        <?php if ($relacionada->imagem_destaque): ?>
                                            <img class="img-fluid w-100" src="img/noticias/<?php echo $relacionada->imagem_destaque; ?>" style="height: 200px; object-fit: cover;" alt="">
                                        <?php else: ?>
                                            <img class="img-fluid w-100" src="img/Asset 7-100.jpg" style="height: 200px; object-fit: cover;" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-3">
                                        <h6 class="mb-2" style="font-family: 'Libre Baskerville';">
                                            <a href="artigo.php?slug=<?php echo $relacionada->slug; ?>" class="text-decoration-none text-dark">
                                                <?php echo truncate_text($relacionada->titulo, 80); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted" style="font-family: 'Open Sans';">
                                            <?php echo format_date($relacionada->data_publicacao); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between">
                        <a href="noticias.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Voltar às Notícias
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Outras Notícias
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="noticias.php">Todas as Notícias</a></li>
                                <?php if ($noticia->categoria): ?>
                                <li><a class="dropdown-item" href="noticias.php?categoria=<?php echo urlencode($noticia->categoria); ?>">
                                    <?php echo htmlspecialchars($noticia->categoria); ?>
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Start -->
                <div class="col-lg-4">
                    <!-- Search Form Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <form method="GET" action="noticias.php">
                            <div class="input-group">
                                <input type="text" name="busca" class="form-control p-3" placeholder="Pesquisar notícias...">
                                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <!-- Search Form End -->

                    <!-- Category Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Categorias</h3>
                        </div>
                        <div class="link-animated d-flex flex-column justify-content-start">
                            <?php foreach ($categorias as $categoria): ?>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none" 
                               href="noticias.php?categoria=<?php echo urlencode($categoria->categoria); ?>">
                                <i class="bi bi-arrow-right me-2"></i><?php echo htmlspecialchars($categoria->categoria); ?> (<?php echo $categoria->total; ?>)
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Category End -->

                    <!-- Recent Post Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Notícias Recentes</h3>
                        </div>
                        <?php foreach ($noticias_recentes as $recente): ?>
                        <div class="d-flex rounded overflow-hidden mb-3">
                            <?php if ($recente->imagem_destaque): ?>
                                <img class="img-fluid rounded" src="img/noticias/<?php echo $recente->imagem_destaque; ?>" style="width: 100px; height: 100px; object-fit: cover;" alt="">
                            <?php else: ?>
                                <img class="img-fluid rounded" src="img/closed-up-wooden-gavel-generative-ai.jpg" style="width: 100px; height: 100px; object-fit: cover;" alt="">
                            <?php endif; ?>
                            <a href="artigo.php?slug=<?php echo $recente->slug; ?>" class="h6 fw-semi-bold d-flex align-items-center bg-light px-3 mb-0 text-decoration-none">
                                <?php echo truncate_text($recente->titulo, 60); ?>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Recent Post End -->
                </div>
                <!-- Sidebar End -->
            </div>
        </div>
    </div>
    <!-- Blog End -->

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>