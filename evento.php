<?php
require_once 'connect.php';
require_once 'includes/functions.php';
require_once 'admin/includes/AttachmentHelper.php';

// Obter ID do evento
$evento_id = $_GET['id'] ?? 0;

if (empty($evento_id)) {
    header('Location: agenda.php');
    exit;
}

try {
    // Buscar o evento pelo ID
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ? AND ativo = 1");
    $stmt->execute([$evento_id]);
    $evento = $stmt->fetch();

    if (!$evento) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }
    
    // Buscar imagens adicionais do evento
    $stmt = $pdo->prepare("SELECT * FROM agenda_imagens WHERE agenda_id = ? ORDER BY ordem_exibicao ASC");
    $stmt->execute([$evento->id]);
    $imagens_evento = $stmt->fetchAll();

    $todas_imagens = [];
    $destaque_na_galeria = false;
    if (!empty($evento->imagem_destaque)) {
        foreach ($imagens_evento as $img) {
            if ($img->imagem === $evento->imagem_destaque) {
                $destaque_na_galeria = true;
                break;
            }
        }
    }
    if (!empty($evento->imagem_destaque) && !$destaque_na_galeria) {
        $todas_imagens[] = (object)[
            'imagem' => $evento->imagem_destaque,
            'legenda' => $evento->resumo ?? '',
            'descricao' => ''
        ];
    }
    foreach ($imagens_evento as $img) {
        $todas_imagens[] = (object)[
            'imagem' => $img->imagem,
            'legenda' => $img->legenda ?? '',
            'descricao' => $img->descricao ?? ''
        ];
    }

    $attachments = AttachmentHelper::get($pdo, 'evento', $evento->id);

    // Buscar eventos relacionados
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id != ? AND ativo = 1 ORDER BY data_evento DESC LIMIT 3");
    $stmt->execute([$evento->id]);
    $eventos_relacionados = $stmt->fetchAll();
    
    // Buscar apenas 2 anúncios DA BASE DE DADOS
    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE ativo = 1 ORDER BY ordem_exibicao ASC, id DESC LIMIT 2");
    $stmt->execute();
    $anuncios = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erro ao carregar evento: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    include '500.php';
    exit;
}

// Configurar meta tags
$meta_title = !empty($evento->meta_title) ? $evento->meta_title : $evento->titulo . " - OAGB";
$meta_description = !empty($evento->meta_description) ? $evento->meta_description : $evento->descricao;
$meta_image = !empty($evento->og_image) ? "uploads/" . $evento->og_image : 
              (!empty($evento->imagem_destaque) ? "uploads/" . $evento->imagem_destaque : "img/logo3.png");
$meta_url = "https://oagb.gw/evento.php?id=" . $evento->id;
$meta_type = "event";

$page_title = "Agenda";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    
    <?php include 'includes/meta_tags_include.php'; ?>

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
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
            --dark-navy: #111923;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }

        /* === SUBPAGE BREADCRUMB BAR (fundo creme — cores escuras) === */
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: #666 !important; text-decoration: none !important; font-size: 0.85rem; transition: .3s; }
        .subpage-breadcrumb-bar a:hover { color: var(--primary-maroon) !important; }
        .subpage-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; }
        .bc-sep { display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--primary-maroon);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--primary-maroon) !important; transition: .3s; font-size: 0.8rem;
        }
        .quick-links a:hover { background: rgba(77,28,33,0.08); color: var(--primary-gold) !important; border-color: var(--primary-gold); }
        .quick-links a:hover i { color: var(--primary-gold) !important; }

        /* Mobile breadcrumbs & header (fundo creme) */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar {
                background: #fafafa !important; padding: 10px 0;
                border-bottom: 1px solid #e0dcd2;
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span {
                font-size: 0.72rem; color: #666 !important;
            }
            .mobile-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a {
                border-color: var(--primary-maroon) !important; color: var(--primary-maroon) !important; width: 28px; height: 28px; font-size: 0.65rem;
            }
            .mobile-breadcrumb-bar .quick-links a:hover {
                background: rgba(77,28,33,0.08) !important; border-color: var(--primary-gold) !important;
            }
            #mobile-header-simple { background: #fafafa !important; padding-bottom: 10px; width: 100%; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #fafafa !important; }
            #mobile-header-simple .mobile-header-contacts small { color: var(--primary-maroon) !important; font-size: 0.70rem; }
            #mobile-header-simple .mobile-header-contacts i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; background: transparent !important; }
            #mobile-header-simple .mobile-pill-btn i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn:hover,
            #mobile-header-simple .mobile-pill-btn:active,
            #mobile-header-simple .mobile-pill-btn:focus {
                background: rgba(77,28,33,0.08) !important; border-color: var(--primary-gold) !important;
            }
            #mobile-header-simple .navbar-toggler,
            #mobile-header-simple .navbar-toggler *,
            #mobile-header-simple .navbar-toggler i { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-toggler::after { color: var(--primary-gold) !important; }
            
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        /* === DESKTOP OVERRIDES FOR LIGHT BACKGROUND === */
        @media (min-width: 992px) {
            #topbar .topbar-contacts small, 
            #topbar .topbar-contacts small i { color: #333 !important; }
            
            #topbar .topbar-btn { 
                color: #333 !important; 
                border-color: rgba(0,0,0,0.15) !important; 
                background: rgba(0,0,0,0.02) !important; 
            }
            #topbar .topbar-btn i { color: var(--primary-maroon) !important; }

            /* Navbar: Dark links on cream */
            .navbar-dark .navbar-nav .nav-link { color: #333 !important; font-weight: 600; }
            .navbar-dark .navbar-nav .nav-link:hover,
            .navbar-dark .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; }
        }

        .titulo-evento { color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.5rem; font-weight: 400 !important; margin-bottom: 1rem !important; }
        .texto-conteudo { color: #111923; font-family: 'Open Sans', sans-serif; font-weight: 600; }
        .bg-color-4 { background-color: #a98c78; }

        /* Botões arrow corrigidos */
        .btn-arrow-only { position: relative; display: inline-block; width: 100%; border-bottom: 1px solid #111923; padding-top: 20px; transition: all 0.3s ease; cursor: pointer; }
        .btn-arrow-only i { position: absolute; right: 0; top: 0; color: #111923; font-size: 18px; transition: all 0.3s ease; }
        .btn-arrow-only:hover { transform: translateX(5px); }
        .btn-arrow-only:hover i { transform: translateX(5px); }
        
        .evento-relacionado { padding: 12px 0; border-bottom: 1px solid #f5f2ed; transition: all 0.3s ease; }
        .evento-relacionado:last-child { border-bottom: none; padding-bottom: 0; }
        .evento-relacionado:hover h6 a { color: var(--primary-gold) !important; }
        .evento-relacionado .resumo { font-family: 'Open Sans', sans-serif; font-size: 0.8rem; color: #777; margin-top: 4px; line-height: 1.5; }
        
        .share-icons a { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 5px; transition: all 0.3s ease; }
        .share-icons a:hover { transform: translateY(-3px); }
        .action-buttons { display: flex; gap: 15px; padding: 15px 0; margin-bottom: 20px; justify-content: center; flex-wrap: wrap; }
        .action-buttons .btn { background-color: transparent; color: #8B6B47; border: 1px solid #8B6B47; transition: all 0.3s; }
        .action-buttons .btn:hover { background-color: #8B6B47; color: white; transform: translateY(-2px); }
        
        .announcement-item { margin-bottom: 5px; }
        .announcement-separator { border: 0; height: 1px; background: #f0ece4; margin: 15px 0; }
        .evento-carousel .owl-item img { height: 400px; object-fit: cover; }

        /* Sidebar Cards Premium */
        .sidebar-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #f0ece4;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
        }
        .sidebar-card h4 {
            font-family: 'Libre Baskerville', serif;
            color: var(--primary-maroon);
            font-size: 1.3rem;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .sidebar-card h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--primary-gold);
        }

        /* ======= PREMIUM EVENT SLIDER ======= */
        #eventoCarousel {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        #eventoCarousel .carousel-item {
            position: relative;
        }
        #eventoCarousel .carousel-item img {
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        /* Caption gradient bar */
        .evento-slide-caption {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(0deg, rgba(17,25,35,0.82) 0%, rgba(17,25,35,0.45) 60%, transparent 100%);
            padding: 50px 28px 18px 28px;
            z-index: 3;
        }
        .evento-slide-caption p {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.95);
            margin: 0;
            letter-spacing: 0.2px;
            line-height: 1.5;
        }
        .evento-slide-caption .slide-desc {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
        }
        /* Custom nav arrows */
        .evento-slider-nav {
            position: absolute;
            top: 50%; transform: translateY(-50%);
            width: 48px; height: 48px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.6);
            background: rgba(17,25,35,0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            opacity: 0.7;
            padding: 0;
        }
        .evento-slider-nav:hover {
            background: rgba(77,28,33,0.85);
            border-color: rgba(177,162,118,0.8);
            opacity: 1;
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .evento-slider-nav i {
            font-size: 1.1rem;
            line-height: 1;
        }
        .evento-slider-nav.prev { left: 16px; }
        .evento-slider-nav.next { right: 16px; }
        /* Slide counter badge */
        .evento-slide-counter {
            position: absolute;
            top: 16px; right: 16px;
            background: rgba(17,25,35,0.55);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: rgba(255,255,255,0.9);
            padding: 5px 14px;
            border-radius: 20px;
            font-family: 'Open Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            z-index: 5;
            border: 1px solid rgba(255,255,255,0.15);
        }
        /* Dot indicators */
        #eventoCarousel .carousel-indicators {
            margin-bottom: 10px;
            z-index: 4;
        }
        #eventoCarousel .carousel-indicators button {
            width: 10px; height: 10px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.7);
            background: transparent;
            opacity: 0.6;
            transition: all 0.3s ease;
            margin: 0 4px;
        }
        #eventoCarousel .carousel-indicators button.active {
            background: #B1A276;
            border-color: #B1A276;
            opacity: 1;
            transform: scale(1.15);
        }
        /* Single image container */
        .evento-single-img {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        
        #eventoCarousel .carousel-item img,
        .evento-single-img img {
            height: 500px;
            object-fit: cover;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .evento-slider-nav { width: 40px; height: 40px; }
            .evento-slider-nav.prev { left: 10px; }
            .evento-slider-nav.next { right: 10px; }
            .evento-slide-caption { padding: 40px 18px 14px 18px; }
            .evento-slide-caption p { font-size: 0.85rem; }
            .evento-slide-counter { top: 10px; right: 10px; font-size: 0.7rem; padding: 4px 10px; }
            #eventoCarousel .carousel-item img,
            .evento-single-img img { height: 320px !important; }
        }
    </style>
</head>

<body class="header-light-page">
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

        <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header (EXACTAMENTE IGUAL AO agenda.php) -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: #fafafa; border-bottom: 1px solid #e0dcd2;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a><span class="bc-sep"></span>
                        <a href="agenda.php">Agenda</a><span class="bc-sep"></span>
                        <span class="bc-active"><?php echo htmlspecialchars(truncate_text($evento->titulo, 30)); ?></span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header (fundo creme, sem imagem) - EXACTAMENTE IGUAL AO agenda.php -->
    <div class="d-block d-lg-none">
        <div id="mobile-header-simple" style="position: relative; overflow: hidden;">
            <!-- Contacts -->
            <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                <div class="row g-0 mb-3">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-2 overflow-auto" style="white-space: nowrap;">
                        <small class="text-nowrap"><i class="fa fa-map-marker-alt me-1"></i>Bissau, Guiné-Bissau</small>
                        <small class="text-nowrap"><i class="fa fa-phone-alt me-1"></i>+245 955 475 889</small>
                        <small class="text-nowrap"><i class="fa fa-envelope-open me-1"></i>info@oagb.gw</small>
                    </div>
                </div>

                <div class="row g-0 mb-1">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                             <i class="fa fa-search" style="font-size: 1rem;"></i>
                        </button>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="fa fa-globe" style="font-size: 1rem;"></i>
                            </button>
                            <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                            </div>
                        </div>
                        <a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center">
                            <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navbar -->
            <div class="mobile-navbar-wrapper container-fluid p-0" style="margin-top: 5px;">
                <?php include 'includes/navbar.php'; ?>
            </div>

            <!-- Breadcrumbs -->
            <div class="mobile-breadcrumb-bar">
                <div class="container d-flex align-items-center justify-content-between py-2">
                    <div style="font-size: 0.72rem;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="agenda.php">Agenda</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo htmlspecialchars(truncate_text($evento->titulo, 20)); ?></span>
                    </div>
                    <div class="quick-links d-flex gap-1">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar & Header End -->




    <!-- Event Detail Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8 main-content">
                    <!-- Event Content -->
                    <div class="pe-lg-4">
                        <h1 class="titulo-evento">
                            <?php echo htmlspecialchars($evento->titulo); ?>
                        </h1>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="far fa-calendar-alt text-primary me-2"></i>
                                <span class="text-muted" style="font-family: 'Open Sans', sans-serif;"><?php echo format_date_pt($evento->data_evento); ?></span>
                            </div>
                            
                            <?php if (!empty($evento->local_evento)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-muted" style="font-family: 'Open Sans', sans-serif;"><?php echo htmlspecialchars($evento->local_evento); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Slider / Imagem Premium -->
                        <?php if (count($todas_imagens) > 1): ?>
                            <?php $total_slides = count($todas_imagens); ?>
                            <div id="eventoCarousel" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="6000">
                                <!-- Slide Counter -->
                                <div class="evento-slide-counter">
                                    <i class="far fa-images me-1"></i>
                                    <span id="slideCurrentNum">1</span> / <?php echo $total_slides; ?>
                                </div>
                                <div class="carousel-inner">
                                    <?php foreach ($todas_imagens as $index => $img): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <?php $img_path = oagb_resolve_media_path($img->imagem, 'uploads/OAGB-Placeholder.jpg'); ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" class="d-block w-100" style="object-fit: cover; height: 500px;" alt="<?php echo htmlspecialchars(!empty($img->legenda) ? $img->legenda : $evento->titulo); ?>">
                                        <?php if (!empty($img->legenda)): ?>
                                        <div class="evento-slide-caption">
                                            <p><?php echo htmlspecialchars($img->legenda); ?></p>
                                            <?php if (!empty($img->descricao)): ?>
                                            <p class="slide-desc"><?php echo htmlspecialchars($img->descricao); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Dot Indicators -->
                                <div class="carousel-indicators">
                                    <?php for ($i = 0; $i < $total_slides; $i++): ?>
                                    <button type="button" data-bs-target="#eventoCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?>></button>
                                    <?php endfor; ?>
                                </div>
                                <!-- Custom Arrow Prev -->
                                <button class="evento-slider-nav prev" type="button" data-bs-target="#eventoCarousel" data-bs-slide="prev" aria-label="Anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <!-- Custom Arrow Next -->
                                <button class="evento-slider-nav next" type="button" data-bs-target="#eventoCarousel" data-bs-slide="next" aria-label="Próximo">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <!-- Slide counter JS -->
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var carousel = document.getElementById('eventoCarousel');
                                if (carousel) {
                                    carousel.addEventListener('slid.bs.carousel', function(e) {
                                        var counter = document.getElementById('slideCurrentNum');
                                        if (counter) counter.textContent = e.to + 1;
                                    });
                                }
                            });
                            </script>
                        <?php elseif (count($todas_imagens) === 1): ?>
                            <?php $img_path = oagb_resolve_media_path($todas_imagens[0]->imagem, 'uploads/OAGB-Placeholder.jpg'); ?>
                            <div class="evento-single-img mb-4">
                                <img src="<?php echo htmlspecialchars($img_path); ?>" class="img-fluid w-100" style="height: 500px; object-fit: cover; display: block;" alt="<?php echo htmlspecialchars(!empty($todas_imagens[0]->legenda) ? $todas_imagens[0]->legenda : $evento->titulo); ?>">
                                <?php if (!empty($todas_imagens[0]->legenda)): ?>
                                <div class="evento-slide-caption">
                                    <p><?php echo htmlspecialchars($todas_imagens[0]->legenda); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="content texto-conteudo" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($evento->descricao)); ?>
                        </div>

                    <!-- Dynamic Attachments Logic -->
                    <?php
                    $all_files = [];
                    $added_filenames = [];

                    // 1. Gather files from multiple attachments
                    if (!empty($attachments)) {
                        foreach ($attachments as $att) {
                            $filename = $att['nome_ficheiro'];
                            if (!in_array($filename, $added_filenames)) {
                                $all_files[] = (object)[
                                    'nome_ficheiro' => $filename,
                                    'nome_original' => $att['nome_original'],
                                    'tipo_mime' => $att['tipo_mime'] ?? 'application/pdf',
                                    'tamanho' => $att['tamanho'] ?? 0,
                                    'descricao' => $att['descricao'] ?? ''
                                ];
                                $added_filenames[] = $filename;
                            }
                        }
                    }

                    // 2. Gather single main file (ficheiro_anexo)
                    if (!empty($evento->ficheiro_anexo)) {
                        $main_file = $evento->ficheiro_anexo;
                        if (!in_array($main_file, $added_filenames)) {
                            $orig_name = $main_file;
                            $mime = 'application/pdf';
                            $size = 0;
                            $desc = '';
                            
                            // Try to match metadata from attachments list
                            if (!empty($attachments)) {
                                foreach ($attachments as $att) {
                                    if ($att['nome_ficheiro'] === $main_file) {
                                        $orig_name = $att['nome_original'];
                                        $mime = $att['tipo_mime'] ?? 'application/pdf';
                                        $size = $att['tamanho'] ?? 0;
                                        $desc = $att['descricao'] ?? '';
                                        break;
                                    }
                                }
                            }
                            
                            // If still unpopulated, fallback to legenda_anexo or clean name
                            if ($orig_name === $main_file && !empty($evento->legenda_anexo)) {
                                $orig_name = $evento->legenda_anexo;
                            }

                            // Prepend main file so it appears first in the merged list
                            array_unshift($all_files, (object)[
                                'nome_ficheiro' => $main_file,
                                'nome_original' => $orig_name,
                                'tipo_mime' => $mime,
                                'tamanho' => $size,
                                'descricao' => $desc
                            ]);
                            $added_filenames[] = $main_file;
                        }
                    }

                    $total_attachments = count($all_files);
                    ?>

                    <?php if ($total_attachments > 1): ?>
                        <!-- Multiple Attachments: Grouped beautiful list -->
                        <div class="mt-4">
                            <h6 class="mb-3" style="font-family: 'Open Sans', sans-serif; color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;"><i class="fas fa-paperclip me-2"></i>Documentos Anexos</h6>
                            <?php foreach ($all_files as $att): ?>
                            <a href="uploads/<?php echo htmlspecialchars($att->nome_ficheiro); ?>" class="text-decoration-none d-block mb-2" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                                <div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 p-3 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 10px; flex-shrink: 0;">
                                            <?php if(strpos($att->tipo_mime, 'pdf') !== false): ?>
                                                <i class="far fa-file-pdf" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php elseif(strpos($att->tipo_mime, 'image') !== false): ?>
                                                <i class="far fa-file-image" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php else: ?>
                                                <i class="far fa-file-alt" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php 
                                                $att_display = !empty($att->descricao) ? $att->descricao : $att->nome_original;
                                                // Clean up original filename 
                                                $att_display = str_replace('_', ' ', pathinfo($att_display, PATHINFO_FILENAME));
                                            ?>
                                            <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 0.92rem; color: #333;"><?php echo htmlspecialchars($att_display); ?></div>
                                            <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.72rem;"><?php echo $att->tamanho > 0 ? number_format($att->tamanho / 1024, 0) . ' KB' : 'Clique para descarregar'; ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                        <!-- Share Button -->
                                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); if(navigator.share){navigator.share({title: '<?php echo htmlspecialchars($evento->titulo, ENT_QUOTES); ?>', url: 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'});} else { alert('Link copiado!'); navigator.clipboard.writeText('http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'); }" class="btn btn-sm d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(177,162,118,0.12); color: #B1A276; border: none; transition: 0.3s;" onmouseover="this.style.background='rgba(177,162,118,0.25)'" onmouseout="this.style.background='rgba(177,162,118,0.12)'" title="Partilhar">
                                            <i class="fas fa-share-alt" style="font-size: 0.8rem;"></i>
                                        </button>
                                        <!-- Download Button -->
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(77,28,33,0.1); transition: all 0.3s;">
                                            <i class="fas fa-download" style="color: var(--primary-maroon); font-size: 0.8rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($total_attachments === 1): ?>
                        <!-- Single Attachment: Premium Quick Download card -->
                        <?php $att = $all_files[0]; ?>
                        <div class="mt-5 mb-0">
                            <a href="uploads/<?php echo htmlspecialchars($att->nome_ficheiro); ?>" class="text-decoration-none d-block" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                                <div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 p-4 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 12px; flex-shrink: 0;">
                                            <?php if(strpos($att->tipo_mime, 'pdf') !== false): ?>
                                                <i class="far fa-file-pdf" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php elseif(strpos($att->tipo_mime, 'image') !== false): ?>
                                                <i class="far fa-file-image" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php else: ?>
                                                <i class="far fa-file-alt" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php 
                                                $att_display = !empty($att->descricao) ? $att->descricao : $att->nome_original;
                                                // Clean up original filename 
                                                $att_display = str_replace('_', ' ', pathinfo($att_display, PATHINFO_FILENAME));
                                            ?>
                                            <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 1rem; color: #333;"><?php echo htmlspecialchars($att_display); ?></div>
                                            <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.78rem;">Documento — Clique para abrir</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                        <!-- Share Button -->
                                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); if(navigator.share){navigator.share({title: '<?php echo htmlspecialchars($evento->titulo, ENT_QUOTES); ?>', url: 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'});} else { alert('Link copiado!'); navigator.clipboard.writeText('http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'); }" class="btn btn-sm d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(177,162,118,0.12); color: #B1A276; border: none; transition: 0.3s;" onmouseover="this.style.background='rgba(177,162,118,0.25)'" onmouseout="this.style.background='rgba(177,162,118,0.12)'" title="Partilhar">
                                            <i class="fas fa-share-alt" style="font-size: 0.9rem;"></i>
                                        </button>
                                        <!-- Download Button -->
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(77,28,33,0.1); transition: all 0.3s;">
                                            <i class="fas fa-download" style="color: var(--primary-maroon); font-size: 0.9rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                        
                        <?php if (!empty($evento->contacto_info)): ?>
                        <div class="mt-4 p-3 bg-white rounded">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville', serif;">Informações de Contacto</h5>
                            <p class="texto-conteudo"><?php echo nl2br(htmlspecialchars($evento->contacto_info)); ?></p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <div class="col-lg-4 sidebar-content">
                    <!-- Related Events -->
                    <?php if (!empty($eventos_relacionados)): ?>
                    <div class="sidebar-card">
                        <div class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 500; text-transform: uppercase; position: relative; padding-bottom: 10px; font-size: 1.25rem; letter-spacing: 1px;">
                            OUTROS EVENTOS
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </div>
                        <?php 
                        $count = 0;
                        $total_relacionados = count($eventos_relacionados);
                        foreach ($eventos_relacionados as $relacionado): 
                            $count++;
                        ?>
                        <div class="mb-0 group-card-lidas" style="transition: all 0.3s ease;">
                            <a href="evento.php?id=<?php echo $relacionado->id; ?>" class="text-decoration-none d-block">
                                <?php if (!empty($relacionado->imagem_destaque)): ?>
                                    <?php $img_lida = oagb_resolve_media_path($relacionado->imagem_destaque, 'uploads/OAGB-Placeholder.jpg'); ?>
                                    <div class="rounded-3 overflow-hidden mb-3" style="position: relative;">
                                        <img class="img-fluid w-100" src="<?php echo htmlspecialchars($img_lida); ?>" style="height: 160px; object-fit: cover; transition: transform 0.5s ease;" alt="" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </div>
                                <?php endif; ?>
                                <div class="w-100">
                                    <div class="mb-2" style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo format_date_pt($relacionado->data_evento); ?>
                                    </div>
                                    <div class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 0.95rem; line-height: 1.45; color: #4D1C21; font-weight: 500; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars($relacionado->titulo); ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php if ($count < $total_relacionados): ?>
                        <hr style="border-top: 1px solid #f0ece4; margin: 1.2rem 0; opacity: 1;">
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Event Detail End -->

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg bg-color-1 text-white btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

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
    
    <script>
    // Inicializar carousel de imagens
    $(document).ready(function(){
        if($('.evento-carousel').length) {
            $('.evento-carousel').owlCarousel({
                autoplay: true,
                smartSpeed: 1000,
                loop: true,
                nav: true,
                dots: false,
                items: 1,
                navText : ['<i class="bi bi-chevron-left"></i>','<i class="bi bi-chevron-right"></i>']
            });
        }
    });
    
    // Função compartilhar
    function shareEvent() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($evento->titulo); ?>',
                text: '<?php echo addslashes(truncate_text($evento->descricao, 100)); ?>',
                url: window.location.href
            });
        } else {
            alert('Use os botões de redes sociais abaixo para compartilhar');
        }
    }
    
    // Função traduzir
    function translatePage() {
        window.open('https://translate.google.com/translate?u=' + encodeURIComponent(window.location.href));
    }
    
    // Adicionar ao calendário
    function addToCalendar() {
        const event = {
            title: '<?php echo addslashes($evento->titulo); ?>',
            start: '<?php echo date('Y-m-d\TH:i:s', strtotime($evento->data_evento)); ?>',
            end: '<?php echo date('Y-m-d\TH:i:s', strtotime($evento->data_evento . ' +3 hours')); ?>',
            description: '<?php echo addslashes(truncate_text($evento->descricao, 200)); ?>',
            location: '<?php echo addslashes($evento->local_evento ?? 'OAGB'); ?>'
        };
        
        // Criar link para Google Calendar
        const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.title)}&dates=${event.start.replace(/[-:]/g, '')}/${event.end.replace(/[-:]/g, '')}&details=${encodeURIComponent(event.description)}&location=${encodeURIComponent(event.location)}`;
        
        window.open(googleUrl, '_blank');
    }
                
