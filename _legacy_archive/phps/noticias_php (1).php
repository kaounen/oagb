<?php
require_once 'connect.php';

// Parâmetros de pesquisa
$categoria_filtro = isset($_GET['categoria']) ? sanitize($_GET['categoria']) : '';
$termo_busca = isset($_GET['busca']) ? sanitize($_GET['busca']) : '';

// Buscar notícias (primeiras 10)
$sql = "SELECT * FROM noticias WHERE ativo = 1";
$params = [];

if ($categoria_filtro) {
    $sql .= " AND categoria = ?";
    $params[] = $categoria_filtro;
}

if ($termo_busca) {
    $sql .= " AND (titulo LIKE ? OR resumo LIKE ? OR conteudo LIKE ? OR tags LIKE ?)";
    $search_term = "%$termo_busca%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
}

$sql .= " ORDER BY data_publicacao DESC LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

// Buscar categorias
$stmt = $pdo->prepare("SELECT categoria, COUNT(*) as total FROM noticias WHERE ativo = 1 AND categoria != '' GROUP BY categoria ORDER BY categoria");
$stmt->execute();
$categorias = $stmt->fetchAll();

// Buscar notícias recentes para sidebar
$stmt = $pdo->prepare("SELECT id, titulo, slug, imagem_destaque, data_publicacao FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 5");
$stmt->execute();
$noticias_recentes = $stmt->fetchAll();

$page_title = "Notícias";
$breadcrumb = "Comunicação";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Notícias - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Notícias OAGB, Ordem dos Advogados Guinea-Bissau" name="keywords">
    <meta content="Últimas notícias da Ordem dos Advogados da Guiné-Bissau" name="description">

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
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo $page_title; ?></a>
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
                <!-- Blog list Start -->
                <div class="col-lg-8">
                    <!-- Filtros -->
                    <?php if ($categoria_filtro || $termo_busca): ?>
                    <div class="mb-4">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-2">Filtros aplicados:</h6>
                            <?php if ($categoria_filtro): ?>
                                <span class="badge bg-primary me-2">Categoria: <?php echo htmlspecialchars($categoria_filtro); ?></span>
                            <?php endif; ?>
                            <?php if ($termo_busca): ?>
                                <span class="badge bg-secondary me-2">Busca: <?php echo htmlspecialchars($termo_busca); ?></span>
                            <?php endif; ?>
                            <a href="noticias.php" class="btn btn-sm btn-outline-secondary">Limpar filtros</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row g-5" id="noticias-container">
                        <?php foreach ($noticias as $noticia): ?>
                        <div class="col-md-6 wow slideInUp" data-wow-delay="0.1s">
                            <div class="blog-item bg-light rounded overflow-hidden h-100">
                                <div class="blog-img position-relative overflow-hidden">
                                    <?php if ($noticia->imagem_destaque): ?>
                                        <img class="img-fluid w-100" src="img/noticias/<?php echo $noticia->imagem_destaque; ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>" style="height: 250px; object-fit: cover;">
                                    <?php else: ?>
                                        <img class="img-fluid w-100" src="img/Asset 7-100.jpg" alt="<?php echo htmlspecialchars($noticia->titulo); ?>" style="height: 250px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                                <div class="p-4 d-flex flex-column h-100">
                                    <h4 class="mb-3" style="margin:0px;color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: normal; font-size:180%;">
                                        <a href="artigo.php?slug=<?php echo $noticia->slug; ?>" class="text-decoration-none" style="color:#4D1C21;">
                                            <?php echo htmlspecialchars($noticia->titulo); ?>
                                        </a>
                                    </h4>
                                    <div class="d-flex mb-3">
                                        <small style="color:#615759;font-family: 'Open Sans'; font-weight: 300; font-style: normal;font-size:90%;">
                                            <?php echo format_date($noticia->data_publicacao, 'd \d\e F \d\e Y'); ?>
                                        </small>
                                    </div>
                                    <p style="color:#111923;font-family: 'Open Sans'; font-weight: 600; font-style: normal;font-size:100%;" class="flex-grow-1">
                                        <?php echo truncate_text($noticia->resumo, 120); ?>
                                    </p>
                                    <div class="mt-auto" style="border-bottom:1px solid #111923;float:left;">
                                        <a class="text-uppercase text-decoration-none" href="artigo.php?slug=<?php echo $noticia->slug; ?>" style="color:#111923;">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="bi bi-arrow-right" style="color:#111923;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Loading indicator -->
                    <div class="text-center py-4" id="loading-indicator" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando mais notícias...</p>
                    </div>

                    <!-- End of content indicator -->
                    <div class="text-center py-4" id="end-indicator" style="display: none;">
                        <p class="text-muted">Não há mais notícias para mostrar.</p>
                    </div>
                </div>
                <!-- Blog list End -->

                <!-- Sidebar Start -->
                <div class="col-lg-4">
                    <!-- Search Form Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <form method="GET" action="noticias.php">
                            <div class="input-group">
                                <input type="text" name="busca" class="form-control p-3" placeholder="Termo de pesquisa" value="<?php echo htmlspecialchars($termo_busca); ?>">
                                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                            <?php if ($categoria_filtro): ?>
                                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoria_filtro); ?>">
                            <?php endif; ?>
                        </form>
                    </div>
                    <!-- Search Form End -->

                    <!-- Category Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0">Categorias</h3>
                        </div>
                        <div class="link-animated d-flex flex-column justify-content-start">
                            <?php foreach ($categorias as $categoria): ?>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none" 
                               href="noticias.php?categoria=<?php echo urlencode($categoria->categoria); ?><?php echo $termo_busca ? '&busca=' . urlencode($termo_busca) : ''; ?>">
                                <i class="bi bi-arrow-right me-2"></i><?php echo htmlspecialchars($categoria->categoria); ?> (<?php echo $categoria->total; ?>)
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Category End -->

                    <!-- Recent Post Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0">Recentes</h3>
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

    <!-- Infinite Scroll -->
    <script>
    let loading = false;
    let currentOffset = 10;
    let hasMoreContent = true;
    const categoria = '<?php echo $categoria_filtro; ?>';
    const busca = '<?php echo $termo_busca; ?>';

    function loadMoreNews() {
        if (loading || !hasMoreContent) return;
        
        loading = true;
        document.getElementById('loading-indicator').style.display = 'block';

        const formData = new FormData();
        formData.append('offset', currentOffset);
        if (categoria) formData.append('categoria', categoria);
        if (busca) formData.append('busca', busca);

        fetch('ajax/carregar-noticias.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading-indicator').style.display = 'none';
            
            if (data.status === 'success' && data.html) {
                document.getElementById('noticias-container').insertAdjacentHTML('beforeend', data.html);
                currentOffset += 10;
            } else {
                hasMoreContent = false;
                document.getElementById('end-indicator').style.display = 'block';
            }
            
            loading = false;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading-indicator').style.display = 'none';
            loading = false;
        });
    }

    // Infinite scroll
    window.addEventListener('scroll', function() {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
            loadMoreNews();
        }
    });
    </script>
</body>
</html>