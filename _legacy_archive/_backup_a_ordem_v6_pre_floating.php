<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_fix_encoding')) {
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        $fixed_text = str_replace(['þÒ', 'þ', 'Ú', 'Ò'], ['ção', 'ç', 'é', 'ã'], $text);
        if (mb_check_encoding($fixed_text, 'UTF-8')) return $fixed_text;
        return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM instituicao_info LIMIT 1");
    $inst_info = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = 'apresentacao-historia' AND ativo = 1");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT * FROM timeline_marcos ORDER BY CAST(ano AS UNSIGNED) ASC");
    $timeline = $stmt->fetchAll();
    
    if (!$pagina) {
        $pagina = (object)[
            'titulo' => 'A Ordem dos Advogados',
            'conteudo' => $inst_info->historia ?? '',
            'imagem' => null
        ];
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
    $stmt->execute();
    $total_advogados = $stmt->fetch()->total;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
    $stmt->execute();
    $total_estagiarios = $stmt->fetch()->total;
    
} catch (Exception $e) {
    error_log("Erro: " . $e->getMessage());
}

$page_title = "A Ordem"; 
$meta_description = "Conheça a história, missão e valores da Ordem dos Advogados da Guiné-Bissau";
$header_image = 'uploads/close-up-scales-justice-original-azul.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
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
        .bg-header { background-attachment: scroll !important; }

        /* === SUBPAGE BREADCRUMB BAR === */
        .subpage-breadcrumb-bar { padding: 10px 0; padding-top: 20px; background: transparent; z-index: 10; }
        .subpage-breadcrumb-bar a { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem; letter-spacing: 0.5px; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 30px; height: 30px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.8); transition: .3s; font-size: 0.7rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: #fff; }

        /* Mobile Breadcrumbs Fix */
        .mobile-breadcrumb-bar {
            background: transparent;
            padding: 10px 0;
            position: absolute;
            bottom: 0; left: 0; right: 0;
            z-index: 999 !important;
            pointer-events: auto !important;
        }
        .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar .bc-active { 
            font-size: 0.7rem; 
            color: rgba(255,255,255,0.73); text-shadow: 0 1px 3px rgba(0,0,0,0.6);
            pointer-events: auto !important;
        }
        .mobile-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; }
        .mobile-breadcrumb-bar .quick-links a { 
            border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); 
            width: 32px; height: 32px; font-size: 0.7rem; 
            pointer-events: auto !important;
        }

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }
        .text-center .section-heading::after { margin-left: auto; margin-right: auto; }

        @media (max-width: 991px) {
            .mobile-navbar-wrapper { z-index: 1000; }
        }

        /* === CLEAN CARDS === */
        .info-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f0ece4;
            padding: 40px;
            transition: all 0.3s ease;
        }
        .info-card:hover {
            box-shadow: 0 12px 40px rgba(177, 162, 118, 0.12);
            transform: translateY(-4px);
        }

        /* === TIMELINE === */
        .tl-container { position: relative; padding: 30px 0; }
        .tl-line {
            position: absolute;
            left: 50%;
            top: 0;
            width: 2px;
            background: var(--primary-gold);
            transform: translateX(-50%);
        }
        .tl-entry { position: relative; width: 47%; margin-bottom: 40px; }
        .tl-entry:nth-child(odd) { margin-right: auto; text-align: right; padding-right: 40px; }
        .tl-entry:nth-child(even) { margin-left: auto; padding-left: 40px; }
        .tl-dot {
            position: absolute;
            top: 10px;
            width: 14px; height: 14px;
            background: var(--primary-gold);
            border-radius: 50%;
            border: 3px solid #fafafa;
            box-shadow: 0 0 0 3px rgba(177, 162, 118, 0.2);
            z-index: 2;
        }
        .tl-entry:nth-child(odd) .tl-dot { right: -7px; }
        .tl-entry:nth-child(even) .tl-dot { left: -7px; }
        .tl-year {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 4px;
        }
        .tl-title { font-weight: 700; color: var(--primary-maroon); font-size: 1rem; margin-bottom: 6px; }
        .tl-desc { color: #666; font-size: 0.9rem; line-height: 1.7; margin: 0; }

        /* === MVV CARDS === */
        .mvv-card {
            background: #fff;
            border: 1px solid #f0ece4;
            border-radius: 14px;
            padding: 24px 26px;
            transition: all 0.3s;
        }
        .mvv-card:hover { box-shadow: 0 8px 28px rgba(177,162,118,0.12); transform: translateY(-2px); }
        .mvv-card .mvv-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }
        .mvv-card .mvv-text {
            color: #666; font-size: 0.9rem; line-height: 1.7; margin: 0;
        }

        /* === NAVBAR ON SCROLL === */
        .navbar-scrolled .nav-link { color: var(--primary-gold) !important; }
        .navbar-scrolled .nav-link.active { color: var(--primary-maroon) !important; font-weight: 700 !important; }
        .bg-header { background-attachment: scroll !important; }

        /* === SUBPAGE BREADCRUMB BAR (fully transparent, over image) === */
        .subpage-breadcrumb-bar {
            padding: 10px 0;
            padding-top: 20px;
            background: transparent;
        }
        .subpage-breadcrumb-bar a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 400; font-size: 0.8rem; letter-spacing: 0.5px; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }
        .quick-links a {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.8);
            transition: .3s; font-size: 0.7rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; }

        /* Mobile breadcrumbs - overlaid on image */
        .mobile-breadcrumb-bar {
            background: transparent;
            padding: 6px 0;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 5;
        }
        .mobile-breadcrumb-bar a { color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.68rem; letter-spacing: 0.3px; text-shadow: 0 1px 3px rgba(0,0,0,0.6); }
        .mobile-breadcrumb-bar a:hover { color: #fff; }
        .mobile-breadcrumb-bar .bc-active { color: #fff; font-weight: 400; font-size: 0.68rem; letter-spacing: 0.3px; text-shadow: 0 1px 3px rgba(0,0,0,0.6); }
        .mobile-breadcrumb-bar .bc-sep { background: var(--primary-gold); opacity: 0.5; width: 4px; height: 4px; }
        .mobile-breadcrumb-bar .quick-links a { border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.7); text-shadow: 0 1px 3px rgba(0,0,0,0.5); font-size: 0.6rem; width: 26px; height: 26px; }
        .mobile-breadcrumb-bar .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; }

        /* Descarregar button hover */
        .btn-descarregar { border-color: var(--primary-maroon) !important; color: var(--primary-maroon) !important; }
        .btn-descarregar:hover { background: var(--primary-maroon) !important; color: #fff !important; border-color: var(--primary-maroon) !important; }

        @media (max-width: 991.98px) {
            .tl-entry { width: 100%; text-align: left !important; padding-left: 35px !important; padding-right: 0 !important; }
            .tl-line { left: 10px; }
            .tl-dot { left: 3px !important; right: auto !important; }
            .section-heading { font-size: 1.6rem; }
            .info-card { padding: 25px; }
            .container { padding-left: 20px; padding-right: 20px; }
            /* Ordem page: push mobile title closer to breadcrumbs but avoid overlap */
            #header-carousel-mobile .carousel-caption.ordem-mobile-caption {
                bottom: 55px !important;
                padding: 0 1rem !important;
            }
        }
    </style>
</head>

<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header + Breadcrumbs over image -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <!-- Breadcrumbs overlaid on bottom of header image, fully transparent -->
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 10px;">
                <div class="container d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.85rem;">Início</span>
                        <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 10px; vertical-align: middle;"></span>
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.85rem;">A Ordem</span>
                        <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 10px; vertical-align: middle;"></span>
                        <span class="bc-active" style="color: #fff; font-weight: 500; font-size: 0.85rem;">Apresentação</span>
                    </div>
                    <div class="quick-links d-flex gap-2">
                        <a href="javascript:history.back()" title="Voltar"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()" title="Imprimir"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="sharePage()" title="Partilhar"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header (identical to index.php) -->
    <div class="d-block d-lg-none">
        <div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-touch="true" style="position: relative;">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="<?php echo htmlspecialchars($header_image, ENT_QUOTES, 'UTF-8'); ?>" alt="OAGB" style="max-height: 250px; object-fit: cover; object-position: top center;">

                    <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                        <div class="row mb-3">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-map-marker-alt text-white-50 me-1"></i>Bissau, Guiné-Bissau</small>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-phone-alt text-white-50 me-1"></i>+245 955 475 889</small>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-envelope-open text-white-50 me-1"></i>info@oagb.gw</small>
                            </div>
                        </div>
                        
                        <div class="row mb-1">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; width: 100%;">
                                <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                                     <i class="fa fa-search" style="font-size: 1rem;"></i>
                                </button>
                                
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="dropdown" data-bs-display="static" title="Mudar Idioma">
                                        <i class="fa fa-globe" style="font-size: 1rem;"></i>
                                    </button>
                                    <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2000; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                        <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                        <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                        <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                        <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                        <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                        <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                        <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                                    </div>
                                </div>
                                
                                <a href="portal/login.php" class="btn btn-sm btn-outline-light px-2 fw-bold text-uppercase d-flex align-items-center mobile-pill-btn">
                                    <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Portal
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">
                        <?php include 'includes/navbar.php'; ?>
                    </div>

                    <!-- Slide Content (mobile spacer) -->
                    <div class="carousel-caption ordem-mobile-caption d-flex flex-column align-items-center justify-content-end" style="padding: 0 1rem; min-height: 80px;">
                        <!-- O título foi removido a pedido do utilizador para esta página mobile -->
                    </div>
                    <!-- Mobile Breadcrumbs (over image) -->
                    <div class="header-overlay-bar mobile-breadcrumb-bar">
                        <div class="container d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span style="color: rgba(255,255,255,0.85); font-size: 0.80rem;">Início</span>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span style="color: rgba(255,255,255,0.85); font-size: 0.80rem;">A Ordem</span>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span class="bc-active" style="color: #fff; font-weight: 500; font-size: 0.80rem;">Apresentação</span>
                            </div>
                            <div class="header-circles d-flex align-items-center">
                                <a href="javascript:history.back()" title="Voltar" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-arrow-left" style="font-size:0.65rem;"></i></a>
                                <a href="javascript:window.print()" title="Imprimir" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-print" style="font-size:0.65rem;"></i></a>
                                <a href="#" onclick="sharePage(); return false;" title="Partilhar" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-share-alt" style="font-size:0.65rem;"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======= MAIN CONTENT ======= -->

    <!-- 1. Introduction Section -->
    <section class="py-4" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="texto-conteudo" style="font-size: 0.85rem; line-height: 1.7; text-align: justify;">
                <?php echo oagb_fix_encoding($pagina->conteudo ?? $inst_info->historia ?? ''); ?>
            </div>
        </div>
    </section>

    <!-- 2. Missão, Visão e Valores -->
    <section class="pt-2 pb-4" style="background: #f7f5f0;">
        <div class="container">
            <div class="text-center mb-4">
                <span class="section-label">Pilares Institucionais</span>
                <h2 class="section-heading" style="font-size: 1.3rem;">Missão, Visão e Valores</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="mvv-card d-flex align-items-start gap-3 h-100">
                        <div class="mvv-icon" style="background: rgba(77,28,33,0.06); color: var(--primary-maroon);">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h6 class="mb-2 fw-bold" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; font-size: 1rem;">Missão</h6>
                            <p class="mvv-text mb-0">
                                <?php echo oagb_fix_encoding($inst_info->missao ?? 'Defender os direitos, liberdades e garantias dos cidadãos.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mvv-card d-flex align-items-start gap-3 h-100">
                        <div class="mvv-icon" style="background: rgba(77,28,33,0.06); color: var(--primary-maroon);">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <h6 class="mb-2 fw-bold" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; font-size: 1rem;">Visão</h6>
                            <p class="mvv-text mb-0">
                                <?php echo oagb_fix_encoding($inst_info->visao ?? 'Ser referência na defesa da legalidade democrática.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mvv-card d-flex align-items-start gap-3 h-100">
                        <div class="mvv-icon" style="background: rgba(77,28,33,0.06); color: var(--primary-maroon);">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div>
                            <h6 class="mb-2 fw-bold" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; font-size: 1rem;">Valores</h6>
                            <p class="mvv-text mb-0">
                                <?php echo oagb_fix_encoding($inst_info->valores ?? 'Independência, Isenção, Sigilo Profissional.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Stats -->
            <div class="row g-3 mt-4 justify-content-center">
                <div class="col-6 col-lg-3">
                    <div class="info-card text-center py-3 px-2">
                        <span style="font-size: 2rem; font-weight: 800; color: var(--primary-maroon); font-family: 'Libre Baskerville', serif;"><?php echo $total_advogados ?: '60+'; ?></span>
                        <small class="d-block text-muted mt-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Advogados Inscritos</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="info-card text-center py-3 px-2">
                        <span style="font-size: 2rem; font-weight: 800; color: var(--primary-gold); font-family: 'Libre Baskerville', serif;"><?php echo $total_estagiarios ?: '15+'; ?></span>
                        <small class="d-block text-muted mt-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Estagiários Ativos</small>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 3. Timeline -->
    <section class="pt-3 pb-3" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="text-center mb-5">
                <span class="section-label">Jornada Institucional</span>
                <h2 class="section-heading">Marcos Históricos</h2>
            </div>
            
            <div class="tl-container">
                <div class="tl-line"></div>
                <?php if (!empty($timeline)): ?>
                    <?php foreach ($timeline as $marco): ?>
                        <div class="tl-entry">
                            <div class="tl-dot"></div>
                            <div class="tl-year"><?php echo htmlspecialchars($marco->ano); ?></div>
                            <div class="tl-title"><?php echo oagb_fix_encoding($marco->titulo); ?></div>
                            <p class="tl-desc"><?php echo oagb_fix_encoding($marco->descricao); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">A preparar os marcos históricos…</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 4. Estatutos CTA -->
    <section class="pb-4" style="background: #f7f5f0;">
        <div class="container py-2">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 text-center">
                    <span class="section-label">Base Legal</span>
                    <h2 class="section-heading">Estatutos da OAGB</h2>
                    <p class="texto-conteudo mb-4" style="max-width: 600px; margin: 0 auto; font-size: 0.85rem; line-height: 1.7;">
                        Os Estatutos definem a organização, os deveres e os direitos dos advogados inscritos na Ordem. Consulte o documento integral aprovado em Assembleia Geral.
                    </p>
                    <a href="estatutos-online.php" class="btn rounded-pill px-5 py-3 shadow-sm me-2" style="background: var(--primary-maroon); color: #fff; font-weight: 600;">
                        <i class="fas fa-book-open me-2"></i>Consultar Online
                    </a>
                    <a href="docsoagb/ESTATUTOS-DA-OAGB.doc" class="btn btn-descarregar rounded-pill px-4 py-3" download>
                        <i class="fas fa-download me-2"></i>Descarregar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top shadow-lg" style="background-color: var(--primary-maroon); border-color: var(--primary-maroon);"><i class="bi bi-arrow-up text-white"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        function sharePage() {
            if (navigator.share) {
                navigator.share({
                    title: 'OAGB - A Ordem',
                    text: 'Conheça a Ordem dos Advogados da Guiné-Bissau',
                    url: window.location.href
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Ligação copiada!');
            }
        }
        // Set timeline line height to match last entry
        document.addEventListener('DOMContentLoaded', function() {
            var entries = document.querySelectorAll('.tl-entry');
            var line = document.querySelector('.tl-line');
            if (entries.length && line) {
                var last = entries[entries.length - 1];
                var container = line.parentElement;
                line.style.height = (last.offsetTop + 20) + 'px';
            }
        });
    </script>
</body>
</html>
