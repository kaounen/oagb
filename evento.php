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

        .titulo-evento { color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.2rem; font-weight: 400; margin-bottom: 1rem !important; }
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
                        
                        <?php if (!empty($evento->imagem_destaque) || !empty($imagens_evento)): ?>
                        <div class="mb-4">
                            <?php if (count($imagens_evento) > 1 || (!empty($evento->imagem_destaque) && count($imagens_evento) > 0)): ?>
                            <!-- Slider para múltiplas imagens -->
                            <div class="owl-carousel evento-carousel">
                                <?php if (!empty($evento->imagem_destaque)): ?>
                                <div class="item">
                                    <img class="img-fluid rounded" src="uploads/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" alt="">
                                </div>
                                <?php endif; ?>
                                <?php foreach ($imagens_evento as $img): ?>
                                <div class="item position-relative">
                                    <img class="img-fluid rounded" src="uploads/<?php echo htmlspecialchars($img->imagem); ?>" alt="<?php echo htmlspecialchars($img->legenda ?? ''); ?>">
                                    <?php if (!empty($img->legenda) || !empty($img->descricao)): ?>
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: rgba(0,0,0,0.6); border-bottom-left-radius: 0.25rem; border-bottom-right-radius: 0.25rem; text-align: left;">
                                        <?php if (!empty($img->legenda)): ?>
                                        <h6 class="text-white mb-1" style="font-size: 0.95rem; font-weight: 700;"><?php echo htmlspecialchars($img->legenda); ?></h6>
                                        <?php endif; ?>
                                        <?php if (!empty($img->descricao)): ?>
                                        <p class="text-white small mb-0" style="font-size: 0.8rem; opacity: 0.9;"><?php echo htmlspecialchars($img->descricao); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <!-- Imagem única -->
                            <img class="img-fluid w-100 rounded" src="uploads/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" alt="<?php echo htmlspecialchars($evento->titulo); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="content texto-conteudo" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($evento->descricao)); ?>
                        </div>

                        <!-- Documentos Anexos -->
                        <?php if (!empty($attachments)): ?>
                        <div class="mt-5 p-4 rounded-3" style="background: #fcfbf8; border: 1px dashed #B1A276;">
                            <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21;">
                                <i class="fas fa-paperclip me-2" style="color: var(--primary-gold);"></i> Documentos Anexos
                            </h5>
                            <div class="list-group list-group-flush bg-transparent">
                                <?php foreach ($attachments as $att): ?>
                                <a href="uploads/attachments/<?php echo $att['nome_ficheiro']; ?>" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center py-3 px-0 border-bottom" target="_blank">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white p-2 rounded shadow-sm me-3">
                                            <?php if(strpos($att['tipo_mime'], 'pdf') !== false): ?>
                                                <i class="far fa-file-pdf fa-2x text-danger"></i>
                                            <?php elseif(strpos($att['tipo_mime'], 'image') !== false): ?>
                                                <i class="far fa-file-image fa-2x text-primary"></i>
                                            <?php else: ?>
                                                <i class="far fa-file-alt fa-2x text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($att['nome_original']); ?></div>
                                            <div class="small text-muted"><?php echo number_format($att['tamanho'] / 1024, 0); ?> KB</div>
                                        </div>
                                    </div>
                                    <span class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> Descarregar
                                    </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
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
                        <h4>Outros Eventos</h4>
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
                                    <h6 class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 0.95rem; line-height: 1.45; color: #4D1C21; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars($relacionado->titulo); ?>
                                    </h6>
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
                
