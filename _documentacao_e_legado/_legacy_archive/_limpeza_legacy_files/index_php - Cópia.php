<?php
require_once 'connect.php';

// Buscar notícias em destaque
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE destaque = 1 AND ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
$stmt->execute();
$noticias_destaque = $stmt->fetchAll();

// Buscar próximos eventos
$stmt = $pdo->prepare("SELECT * FROM agenda WHERE data_evento >= NOW() AND ativo = 1 ORDER BY data_evento ASC LIMIT 3");
$stmt->execute();
$proximos_eventos = $stmt->fetchAll();

// Estatísticas
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
$stmt->execute();
$total_advogados = $stmt->fetch()->total;

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
$stmt->execute();
$total_estagiarios = $stmt->fetch()->total;

$page_title = "Início";
$breadcrumb = "";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Ordem dos Advogados da Guiné-Bissau, OAGB, Advogados Guinea-Bissau" name="keywords">
    <meta content="Site oficial da Ordem dos Advogados da Guiné-Bissau" name="description">

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

        <?php include 'includes/topbar.php'; ?>

    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <!-- Hero Section -->
        <div class="container-fluid bg-primary py-5 bg-header">
            <div class="row py-5">
                <div class="col-12 text-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <h1 class="display-3 text-white animated slideInDown mb-4" style="font-family: 'Libre Baskerville';">
                                    Ordem dos Advogados da Guiné-Bissau
                                </h1>
                                <p class="fs-5 text-white mb-4" style="font-family: 'Open Sans';">
                                    Instituição que representa e regula a advocacia na Guiné-Bissau, promovendo a justiça e defendendo os direitos dos cidadãos.
                                </p>
                                <div class="row g-3 justify-content-center">
                                    <div class="col-md-4">
                                        <div class="bg-white text-center rounded p-3">
                                            <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo $total_advogados; ?></h3>
                                            <p class="text-dark mb-0" style="font-family: 'Open Sans';">Advogados Ativos</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white text-center rounded p-3">
                                            <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo $total_estagiarios; ?></h3>
                                            <p class="text-dark mb-0" style="font-family: 'Open Sans';">Estagiários</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white text-center rounded p-3">
                                            <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';">8</h3>
                                            <p class="text-dark mb-0" style="font-family: 'Open Sans';">Regiões</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar & Hero End -->

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

    <!-- Services Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Nossos Serviços</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Serviços da Ordem dos Advogados</h1>
            </div>
            <div class="row g-5">
                <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                    <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center h-100 p-4">
                        <div class="bg-primary rounded-circle mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-search text-white"></i>
                        </div>
                        <h4 class="mb-3" style="font-family: 'Libre Baskerville';">Pesquisa de Advogados</h4>
                        <p class="m-0" style="font-family: 'Open Sans';">Encontre advogados qualificados por região, especialidade e área de atuação.</p>
                        <a class="btn btn-lg btn-primary rounded mt-3" href="pesquisa-advogados.php">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                    <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center h-100 p-4">
                        <div class="bg-primary rounded-circle mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-user-plus text-white"></i>
                        </div>
                        <h4 class="mb-3" style="font-family: 'Libre Baskerville';">Inscrição na Ordem</h4>
                        <p class="m-0" style="font-family: 'Open Sans';">Processe a sua inscrição como advogado ou estagiário na Ordem.</p>
                        <a class="btn btn-lg btn-primary rounded mt-3" href="inscricao-ordem.php">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.9s">
                    <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center h-100 p-4">
                        <div class="bg-primary rounded-circle mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-balance-scale text-white"></i>
                        </div>
                        <h4 class="mb-3" style="font-family: 'Libre Baskerville';">Solicitação de Advogados</h4>
                        <p class="m-0" style="font-family: 'Open Sans';">Solicite a indicação de um advogado adequado ao seu caso.</p>
                        <a class="btn btn-lg btn-primary rounded mt-3" href="solicitacao-advogados.php">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->

    <!-- News Start -->
    <?php if ($noticias_destaque): ?>
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Notícias</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Últimas Notícias</h1>
            </div>
            <div class="row g-5">
                <?php foreach ($noticias_destaque as $noticia): ?>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.3s">
                    <div class="blog-item bg-light rounded overflow-hidden h-100">
                        <div class="blog-img position-relative overflow-hidden">
                            <?php if ($noticia->imagem_destaque): ?>
                                <img class="img-fluid w-100" src="img/noticias/<?php echo $noticia->imagem_destaque; ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <img class="img-fluid w-100" src="img/Asset 7-100.jpg" alt="<?php echo htmlspecialchars($noticia->titulo); ?>" style="height: 250px; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <div class="p-4 d-flex flex-column h-100">
                            <h4 class="mb-3" style="color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: normal;">
                                <a href="artigo.php?slug=<?php echo $noticia->slug; ?>" class="text-decoration-none" style="color:#4D1C21;">
                                    <?php echo htmlspecialchars($noticia->titulo); ?>
                                </a>
                            </h4>
                            <div class="d-flex mb-3">
                                <small style="color:#615759;font-family: 'Open Sans'; font-weight: 300;">
                                    <?php echo format_date($noticia->data_publicacao, 'd \d\e F \d\e Y'); ?>
                                </small>
                            </div>
                            <p style="color:#111923;font-family: 'Open Sans'; font-weight: 600;" class="flex-grow-1">
                                <?php echo truncate_text($noticia->resumo, 120); ?>
                            </p>
                            <div class="mt-auto">
                                <a href="artigo.php?slug=<?php echo $noticia->slug; ?>" class="text-uppercase text-decoration-none" style="color:#111923;">
                                    Ler mais <i class="bi bi-arrow-right" style="color:#111923;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="noticias.php" class="btn btn-primary py-3 px-5">Ver Todas as Notícias</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- News End -->

    <!-- Events Start -->
    <?php if ($proximos_eventos): ?>
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Agenda</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Próximos Eventos</h1>
            </div>
            <div class="row g-4">
                <?php foreach ($proximos_eventos as $evento): ?>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-white rounded p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle p-3 me-3">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-family: 'Libre Baskerville';"><?php echo htmlspecialchars($evento->titulo); ?></h5>
                                <small class="text-muted" style="font-family: 'Open Sans';">
                                    <?php echo format_datetime($evento->data_evento, 'd/m/Y \à\s H:i'); ?>
                                </small>
                            </div>
                        </div>
                        <?php if ($evento->local_evento): ?>
                        <p class="text-muted mb-2" style="font-family: 'Open Sans';">
                            <i class="fa fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                        </p>
                        <?php endif; ?>
                        <p style="font-family: 'Open Sans';"><?php echo htmlspecialchars($evento->descricao); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="agenda.php" class="btn btn-outline-primary py-3 px-5">Ver Agenda Completa</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Events End -->

    <!-- CTA Banner -->
    <div class="container-fluid bg-primary py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="text-white mb-2" style="font-family: 'Libre Baskerville';">Precisa de Assistência Jurídica?</h3>
                    <p class="text-white mb-0" style="font-family: 'Open Sans';">Entre em contacto connosco ou solicite a indicação de um advogado qualificado.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="solicitacao-advogados.php" class="btn btn-light py-3 px-5 me-3">Solicitar Advogado</a>
                    <a href="contacto.php" class="btn btn-outline-light py-3 px-5">Contactar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

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
