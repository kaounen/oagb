<?php
require_once 'connect.php';

// Buscar próximos eventos
$stmt = $pdo->prepare("
    SELECT * FROM agenda 
    WHERE data_evento >= NOW() AND ativo = 1 
    ORDER BY data_evento ASC
");
$stmt->execute();
$proximos_eventos = $stmt->fetchAll();

// Buscar eventos passados (últimos 10)
$stmt = $pdo->prepare("
    SELECT * FROM agenda 
    WHERE data_evento < NOW() AND ativo = 1 
    ORDER BY data_evento DESC 
    LIMIT 10
");
$stmt->execute();
$eventos_passados = $stmt->fetchAll();

$page_title = "Agenda";
$breadcrumb = "Comunicação";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Agenda - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Agenda OAGB, Eventos Ordem Advogados, Formações" name="keywords">
    <meta content="Agenda de eventos, formações e atividades da Ordem dos Advogados da Guiné-Bissau" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
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

    <!-- Próximos Eventos -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Próximos Eventos</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Agenda de Atividades da OAGB</h1>
            </div>

            <?php if ($proximos_eventos): ?>
            <div class="row g-4">
                <?php foreach ($proximos_eventos as $evento): ?>
                <div class="col-lg-6 col-xl-4 wow slideInUp" data-wow-delay="0.1s">
                    <div class="bg-white rounded shadow-sm h-100 p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary text-white rounded p-3 me-3" style="min-width: 60px;">
                                <div class="text-center">
                                    <div style="font-size: 1.2em; font-weight: bold; font-family: 'Libre Baskerville';">
                                        <?php echo date('d', strtotime($evento->data_evento)); ?>
                                    </div>
                                    <div style="font-size: 0.8em; font-family: 'Open Sans';">
                                        <?php echo ucfirst(strftime('%b', strtotime($evento->data_evento))); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                    <?php echo htmlspecialchars($evento->titulo); ?>
                                </h5>
                                <div class="mb-2">
                                    <small class="text-muted" style="font-family: 'Open Sans';">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo format_datetime($evento->data_evento, 'd/m/Y \à\s H:i'); ?>
                                    </small>
                                </div>
                                <?php if ($evento->local_evento): ?>
                                <div class="mb-2">
                                    <small class="text-muted" style="font-family: 'Open Sans';">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php echo htmlspecialchars($evento->local_evento); ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                                <?php if ($evento->tipo_evento): ?>
                                <div class="mb-3">
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($evento->tipo_evento); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($evento->descricao): ?>
                        <p class="text-muted mb-3" style="font-family: 'Open Sans';">
                            <?php echo htmlspecialchars(truncate_text($evento->descricao, 120)); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($evento->organizador): ?>
                        <div class="border-top pt-3">
                            <small class="text-muted" style="font-family: 'Open Sans';">
                                <i class="bi bi-person-badge me-1"></i>
                                Organizador: <?php echo htmlspecialchars($evento->organizador); ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div class="bg-light rounded p-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h4 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                        Nenhum evento agendado
                    </h4>
                    <p class="text-muted mb-0" style="font-family: 'Open Sans';">
                        Não há eventos programados para as próximas datas. 
                        <a href="noticias.php">Consulte as notícias</a> para atualizações.
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Eventos Passados -->
    <?php if ($eventos_passados): ?>
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Eventos Realizados</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Atividades Recentes</h1>
            </div>

            <div class="row g-4">
                <?php foreach ($eventos_passados as $evento): ?>
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.1s">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="bg-light text-center rounded p-3">
                                    <div class="text-primary" style="font-size: 1.5em; font-weight: bold; font-family: 'Libre Baskerville';">
                                        <?php echo date('d', strtotime($evento->data_evento)); ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.9em; font-family: 'Open Sans';">
                                        <?php echo ucfirst(strftime('%b %Y', strtotime($evento->data_evento))); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-9">
                                <h6 class="mb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                    <?php echo htmlspecialchars($evento->titulo); ?>
                                </h6>
                                <?php if ($evento->local_evento): ?>
                                <small class="text-muted mb-1 d-block" style="font-family: 'Open Sans';">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?php echo htmlspecialchars($evento->local_evento); ?>
                                </small>
                                <?php endif; ?>
                                <?php if ($evento->tipo_evento): ?>
                                <span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($evento->tipo_evento); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($evento->descricao): ?>
                        <div class="mt-3 pt-3 border-top">
                            <p class="text-muted mb-0" style="font-family: 'Open Sans'; font-size: 0.9em;">
                                <?php echo htmlspecialchars(truncate_text($evento->descricao, 100)); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tipos de Eventos -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Nossos Eventos</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Tipos de Atividades</h1>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-white rounded shadow-sm p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-graduation-cap text-white fa-2x"></i>
                        </div>
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Formações</h5>
                        <p class="text-muted mb-0" style="font-family: 'Open Sans';">
                            Cursos de formação contínua para advogados e estagiários
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-white rounded shadow-sm p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users text-white fa-2x"></i>
                        </div>
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Conferências</h5>
                        <p class="text-muted mb-0" style="font-family: 'Open Sans';">
                            Conferências sobre temas jurídicos relevantes e atuais
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-white rounded shadow-sm p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-handshake text-white fa-2x"></i>
                        </div>
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Workshops</h5>
                        <p class="text-muted mb-0" style="font-family: 'Open Sans';">
                            Workshops práticos sobre aspetos específicos da advocacia
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="bg-white rounded shadow-sm p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-award text-white fa-2x"></i>
                        </div>
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Cerimónias</h5>
                        <p class="text-muted mb-0" style="font-family: 'Open Sans';">
                            Cerimónias de posse e eventos institucionais da Ordem
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Signup -->
    <div class="container-fluid bg-primary py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="text-white mb-2" style="font-family: 'Libre Baskerville';">Não perca nenhum evento!</h3>
                    <p class="text-white mb-0" style="font-family: 'Open Sans';">
                        Subscreva a nossa newsletter para receber notificações sobre os próximos eventos e formações.
                    </p>
                </div>
                <div class="col-lg-4">
                    <form action="subscricao.php" method="POST" class="d-flex">
                        <input type="email" name="email" class="form-control me-2" placeholder="Seu email" required>
                        <button class="btn btn-light" type="submit">Subscrever</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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