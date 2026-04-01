<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$stmt = $pdo->query("SELECT * FROM estatutos_artigos WHERE ativo = 1 ORDER BY ordem ASC, numero_artigo ASC");
$art_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$temas = [];
$tema_icons = [
    'Disposições Gerais'       => 'fas fa-landmark',
    'Regime Económico'         => 'fas fa-coins',
    'Membros e Inscrição'      => 'fas fa-user-plus',
    'Direitos e Deveres'       => 'fas fa-balance-scale',
    'Assembleias e Congressos' => 'fas fa-users',
    'Órgãos de Governação'     => 'fas fa-university',
    'Exercício da Advocacia'   => 'fas fa-gavel',
    'Estágio e Formação'       => 'fas fa-graduation-cap',
    'Disciplina e Processo'    => 'fas fa-book',
];

$total_artigos = 0;
foreach ($art_rows as $artigo) {
    $tema = $artigo['tema'] ?: 'Outros';
    if (!isset($temas[$tema])) {
        $temas[$tema] = [
            'slug' => 'tema-' . preg_replace('/[^a-z0-9]+/', '-', strtolower(str_replace(['ã', 'á', 'â', 'à', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç'], ['a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c'], $tema))),
            'icon' => $tema_icons[$tema] ?? 'fas fa-file-alt',
            'artigos' => []
        ];
    }
    $temas[$tema]['artigos'][] = $artigo;
    $total_artigos++;
}

$page_title = "Estatutos da OAGB";
$meta_description = "Consulte online os Estatutos da Ordem dos Advogados da Guiné-Bissau.";
$header_image = 'gestao/assets/uploads/files/close-up-scales-justice-original-azul.jpg';
$has_header_image = !empty($header_image);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        html, body { overflow-x: hidden !important; width: 100%; position: relative; scroll-behavior: smooth; }
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
            --dark-navy: #111923;
            --light-grey-bg: #fafafa;
        }
        body { font-family: 'Open Sans', sans-serif; background: var(--light-grey-bg); }
        .bg-header { background-attachment: scroll !important; }

        /* === DESKTOP HEADER BAR === */
        .bg-header-custom {
            min-height: 400px; display: flex; align-items: flex-end; position: relative;
            background: <?php echo $has_header_image ? "linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('$header_image') center center / cover" : "var(--light-grey-bg)"; ?>;
            border-bottom: <?php echo $has_header_image ? 'none' : '1px solid #eee'; ?>;
        }
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span, .subpage-breadcrumb-bar .bc-active {
            font-size: 0.8rem !important; letter-spacing: 0.5px; transition: .3s;
            color: <?php echo $has_header_image ? 'rgba(255,255,255,0.85)' : '#777'; ?> !important;
            text-decoration: none !important;
            <?php if($has_header_image): ?> text-shadow: 0 1px 4px rgba(0,0,0,0.6); <?php endif; ?>
        }
        .subpage-breadcrumb-bar a:hover { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-gold)'; ?>; }
        .subpage-breadcrumb-bar .bc-active { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-maroon)'; ?>; font-weight: 600; opacity: 1 !important; font-size: 0.8rem !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid <?php echo $has_header_image ? 'rgba(255,255,255,0.3)' : 'var(--primary-maroon)'; ?>;
            display: inline-flex; align-items: center; justify-content: center;
            color: <?php echo $has_header_image ? 'rgba(255,255,255,0.9)' : 'var(--primary-maroon)'; ?>;
            transition: .3s; font-size: 0.8rem;
        }
        .quick-links a:hover { background: <?php echo $has_header_image ? 'rgba(255,255,255,0.15)' : 'rgba(77,28,33,0.1)'; ?>; color: <?php echo $has_header_image ? '#fff' : 'var(--primary-gold)'; ?>; border-color: var(--primary-gold); }

        /* Mobile specific breadcrumbs overlaid on image */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { 
                background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; 
                z-index: 1045 !important; pointer-events: auto !important; 
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span, .mobile-breadcrumb-bar .bc-active { 
                font-size: 0.72rem !important; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            }
            .mobile-breadcrumb-bar .bc-active { font-weight: 500; opacity: 1 !important; }
            .mobile-breadcrumb-bar .quick-links a { 
                border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; 
            }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }

        /* Search Form Premium (Anexo 2) */
        .search-container { position: relative; width: 100%; margin-bottom: 30px; max-width: 800px; margin-left: auto; margin-right: auto; }
        .search-wrapper { position: relative; width: 100%; border-radius: 50px; border: 1px solid rgba(177,162,118, 0.4); background: #fff; display: flex; align-items: center; padding: 4px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: .3s; }
        .search-wrapper:focus-within { border-color: var(--primary-gold); box-shadow: 0 6px 20px rgba(177,162,118, 0.15); }
        .search-wrapper input { border: none; padding: 12px 10px; font-size: 1rem; width: 100%; color: #333; outline: none; background: transparent; }
        .search-wrapper input::placeholder { color: #aaa; font-weight: 300; }
        #clearSearch { cursor: pointer; color: #ccc; transition: .3s; display: none; margin-left: 10px; font-size: 1.2rem; }
        #clearSearch:hover { color: var(--primary-maroon); }

        /* Sidebar Anexo 2 */
        .sidebar-link { display: flex; align-items: center; justify-content: space-between; padding: 6px 12px; border-radius: 8px; color: #555; text-decoration: none; transition: .3s; margin-bottom: 2px; font-size: 0.8rem; border-left: 3px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(177,162,118,0.1); color: var(--primary-maroon); font-weight: 600; border-left-color: var(--primary-gold); border-radius: 0 8px 8px 0; }
        .sidebar-badge { background: #fdfbf7; color: var(--primary-maroon); font-size: 0.65rem; padding: 1px 6px; border-radius: 8px; font-weight: 700; border: 1px solid rgba(177,162,118,0.3); }
        .sidebar-link.active .sidebar-badge { background: #fff; border-color: var(--primary-gold); }
        
        .artigo-block { background: transparent; padding: 0 0 20px 0; border: none; border-bottom: 1px dashed #e0dcd2; margin-bottom: 20px; }
        .artigo-title { font-family: 'Open Sans', sans-serif; font-size: 1rem !important; color: #B1A276; font-weight: 600 !important; margin-bottom: 8px; }
        .texto-conteudo { font-family: 'Open Sans', sans-serif; font-size: 0.85rem !important; line-height: 1.7; color: #111923 !important; text-align: justify; font-weight: 600; }
        
        /* Thematic section dividers */
        .tema-section-title { font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 600; font-size: 1.3rem; padding-bottom: 10px; border-bottom: 1px solid rgba(77, 28, 33, 0.2); margin-top: 40px; margin-bottom: 25px; scroll-margin-top: 140px; }
        
        @media (max-width: 991px) {
            .mobile-sidebar-grid { display: flex; flex-direction: column; gap: 4px; margin-bottom: 20px; }
            .sidebar-link { font-size: 0.85rem; padding: 10px 12px; border-radius: 8px; border-left: 3px solid transparent; flex-direction: row; text-align: left; gap: 10px; justify-content: flex-start; align-items: center; }
            .sidebar-link:hover, .sidebar-link.active { border-left-color: var(--primary-gold); border-radius: 8px; }
            .sidebar-badge { margin-left: auto; }
            .container { padding-left: 20px; padding-right: 20px; }
            .mobile-quick-filters { position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%) translateY(30px); z-index: 2100; width: 95%; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border-radius: 50px; padding: 5px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: flex !important; align-items: center; justify-content: center; overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid rgba(255,255,255,0.4); scrollbar-width: none; opacity: 0; visibility: hidden; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); pointer-events: none; }
            .mobile-quick-filters.active-bar { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); pointer-events: auto; }
            body:has(.navbar-collapse.show) .mobile-quick-filters { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; transform: translateX(-50%) translateY(30px) !important; }
            .mobile-quick-filters::-webkit-scrollbar { display: none; }
            .filter-bubble { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(0,0,0,0.05); color: var(--primary-maroon); opacity: 0.5; transition: .3s; text-decoration: none; margin-right: 4px; -webkit-tap-highlight-color: transparent; outline: none; }
            .filter-bubble:last-child { margin-right: 0; }
            .filter-bubble.active { opacity: 1; background: var(--primary-maroon); color: #fff; transform: scale(1.15); box-shadow: 0 4px 10px rgba(77,28,33,0.3); }
            .filter-bubble:active, .filter-bubble:focus { background: rgba(77, 28, 33, 0.15); color: var(--primary-maroon); outline: none; box-shadow: none; }
            .filter-bubble:hover { opacity: 0.95; transform: scale(1.05); }
            .filter-bubble i { font-size: 0.8rem !important; }
        }
    </style>
</head>
<body>
    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-header-custom">
            <div class="subpage-breadcrumb-bar">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="a-ordem-dos-advogados.php">A Ordem</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Estatutos</span>
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

    <!-- Mobile Header (Identical to Index in structure and size) -->
    <div class="d-block d-lg-none" style="overflow: hidden !important; width: 100vw; position: relative;">
        <div id="header-carousel-mobile" class="carousel slide" data-bs-ride="false" style="position: relative; overflow: hidden !important;">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="<?php echo htmlspecialchars($header_image); ?>" alt="OAGB Mobile Header">
                    
                    <!-- Contacts (Identical to Index) -->
                    <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                        <div class="row mb-3 mx-0">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-map-marker-alt text-white-50 me-1"></i>Av. Amílcar Cabral</small>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-phone-alt text-white-50 me-1"></i>+245 955475889</small>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-envelope-open text-white-50 me-1"></i>info@oagb.gw</small>
                            </div>
                        </div>
                        
                        <div class="row mb-1 mx-0">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; width: 100%;">
                                <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                                     <i class="fa fa-search" style="font-size: 1rem;"></i>
                                </button>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="dropdown" data-bs-display="static">
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
                                <a href="portal/login.php" class="btn btn-sm btn-outline-light px-2 fw-bold text-uppercase d-flex align-items-center mobile-pill-btn">
                                    <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Navbar (Identical to Index) -->
                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">
                        <?php include 'includes/navbar.php'; ?>
                    </div>

                    <!-- Breadcrumbs (Positioned at bottom of Index-sized header) -->
                    <div class="mobile-breadcrumb-bar">
                        <div class="container d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <a href="index.php" class="text-white opacity-75">Início</a>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: #B1A276; display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <a href="a-ordem-dos-advogados.php" class="text-white opacity-75">A Ordem</a>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: #B1A276; display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span class="bc-active">Estatutos</span>
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
        </div>
    </div>

    <!-- Content Area -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container pb-lg-5">
            <div class="row g-5">
                <!-- Temas Filter Sidebar -->
                <div class="col-lg-3">
                    <div class="sticky-top" style="top: 100px; z-index: 10;">
                        <div class="nav flex-column nav-pills mobile-sidebar-grid" id="v-pills-tab" role="tablist">
                            <a class="sidebar-link active" href="#containerTop" data-target="containerTop">
                                <span><i class="fas fa-list me-2" style="color: #ccc;"></i> Todos os Artigos</span>
                                <span class="sidebar-badge"><?php echo $total_artigos; ?></span>
                            </a>
                            <?php foreach ($temas as $tema_nome => $tema_info): ?>
                            <a class="sidebar-link" href="#<?php echo $tema_info['slug']; ?>" data-target="<?php echo $tema_info['slug']; ?>">
                                <span><i class="<?php echo $tema_info['icon']; ?> me-2" style="color: var(--primary-gold);"></i> <?php echo htmlspecialchars(oagb_fix_encoding($tema_nome)); ?></span>
                                <span class="sidebar-badge"><?php echo count($tema_info['artigos']); ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Floating Mobile Filters -->
                <div class="d-lg-none mobile-quick-filters">
                    <a href="#containerTop" class="filter-bubble active p-0" title="Topo">
                        <i class="fas fa-arrow-up" style="font-size: 0.9rem;"></i>
                    </a>
                    <?php foreach ($temas as $tema_nome => $tema_info): ?>
                    <a href="#<?php echo $tema_info['slug']; ?>" class="filter-bubble p-0" title="<?php echo htmlspecialchars($tema_nome); ?>">
                        <i class="<?php echo $tema_info['icon']; ?>" style="font-size: 0.9rem;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Articles Content Area -->
                <div class="col-lg-9" id="containerTop">
                    <!-- Title, Search, and DOC Button -->
                    <!-- Title, Search, and DOC Button -->
                    <div class="border-bottom pb-4 mb-4">
                        <div class="mb-4">
                            <h2 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 600; font-size: 1.3rem; margin-bottom: 5px;">Estatutos da Ordem dos Advogados da Guiné-Bissau</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Aprovado na Assembleia Geral de 28 de Julho de 2018 • <strong id="artigoCounter" style="color: var(--primary-gold);"><?php echo $total_artigos; ?></strong> Artigos</p>
                        </div>
                        <div class="d-flex align-items-center w-100" style="gap: 12px;">
                            <div class="search-wrapper flex-grow-1" style="border-radius: 50px; background: #fff; border: 1px solid rgba(177,162,118,0.4); display: flex; align-items: center; padding: 0 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.05); height: 46px;">
                                <i class="fa fa-search text-muted me-2" style="font-size: 1rem; opacity: 0.6;"></i>
                                <input type="text" id="searchEstatutos" placeholder="Pesquisar nos estatutos..." style="border: none; background: transparent; width: 100%; font-size: 0.95rem; outline: none; color: #333; padding: 0;">
                                <i class="fas fa-times" id="clearSearch" style="cursor: pointer; color: #ccc !important; display: none !important; margin-left: 8px; font-size: 1rem;"></i>
                            </div>
                            <a href="docsoagb/oagb_estatutos.pdf" target="_blank" class="btn d-flex align-items-center justify-content-center flex-shrink-0 rounded-circle shadow-sm" style="background: var(--primary-maroon); color: #fff; width: 46px; height: 46px; transition: .3s; border: none;" title="Descarregar Estatutos">
                                <i class="fas fa-download" style="font-size: 1.1rem;"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div id="noResultsMsg" class="text-center py-5 my-5" style="display: none;">
                        <i class="fa fa-search fa-3x mb-3 text-muted" style="opacity: 0.2;"></i>
                        <h5 class="text-muted" style="font-family: 'Libre Baskerville', serif;">Nenhum artigo encontrado para a sua pesquisa.</h5>
                    </div>

                    <div id="artigosContainer">
                        <?php foreach ($temas as $tema_nome => $tema_info): ?>
                            <!-- Group Title dividing sections -->
                            <h3 id="<?php echo $tema_info['slug']; ?>" class="tema-section-title scrollspy-target">
                                <i class="<?php echo $tema_info['icon']; ?> me-2"></i> <?php echo htmlspecialchars(oagb_fix_encoding($tema_nome)); ?>
                            </h3>
                            
                            <?php foreach ($tema_info['artigos'] as $artigo): ?>
                            <div class="artigo-block wow fadeInUp" data-artigo-num="<?php echo $artigo['numero_artigo']; ?>" data-tema="<?php echo htmlspecialchars($tema_nome); ?>">
                                <h4 class="artigo-title">
                                    Artigo <?php echo $artigo['numero_artigo']; ?>º (<?php echo htmlspecialchars(oagb_fix_encoding($artigo['titulo_artigo'])); ?>)
                                </h4>
                                <div class="texto-conteudo">
                                    <?php 
                                        $texto = oagb_fix_encoding($artigo['conteudo']);
                                        // Inject line breaks before numbers "1." or letters "a)" that follow sentence-ending punctuation.
                                        $texto = preg_replace('/(?<=[.\;:!])\s+([0-9]{1,2}\.|[a-z]\))\s/u', '<br><br>$1 ', $texto);
                                        // Also respect actual database newlines
                                        echo nl2br($texto); 
                                    ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        /**
         * Normalização de texto para pesquisa insensível a acentos
         */
        function normalizeString(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        }

        $(document).ready(function() {
            const $searchInput = $('#searchEstatutos');
            const $clearSearch = $('#clearSearch');
            const $artigos = $('.artigo-block');
            const $dividers = $('.tema-section-title');
            const $sidebarLinks = $('.sidebar-link');
            const $counter = $('#artigoCounter');
            const $noResults = $('#noResultsMsg');

            function performSearch() {
                const rawValue = $searchInput.val();
                const searchTerm = normalizeString(rawValue);
                let visibleCount = 0;

                if (rawValue.length > 0) {
                    $clearSearch.fadeIn();
                } else {
                    $clearSearch.fadeOut();
                }

                $artigos.each(function() {
                    const $artigo = $(this);
                    const $content = $artigo.find('.texto-conteudo');
                    const $title = $artigo.find('.artigo-title');
                    
                    // Reset highlights first
                    if ($artigo.data('original-content')) {
                        $content.html($artigo.data('original-content'));
                        $title.html($artigo.data('original-title'));
                    } else {
                        // Store original for first time
                        $artigo.data('original-content', $content.html());
                        $artigo.data('original-title', $title.html());
                    }

                    // Check Search
                    if (!searchTerm) {
                        $artigo.show();
                        visibleCount++;
                        return;
                    }

                    const normalizedText = normalizeString($artigo.text());
                    const isMatch = normalizedText.indexOf(searchTerm) > -1;

                    if (isMatch) {
                        $artigo.show();
                        highlightMatch($title, rawValue);
                        highlightMatch($content, rawValue);
                        visibleCount++;
                    } else {
                        $artigo.hide();
                    }
                });
                
                // Hide empty dividers during search
                $dividers.each(function() {
                    if (searchTerm) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });

                $counter.text(visibleCount);
                if (visibleCount === 0) {
                    $noResults.show();
                } else {
                    $noResults.hide();
                }
            }

            function highlightMatch($element, term) {
                if (!term) return;
                const innerHTML = $element.html();
                const index = normalizeString(innerHTML).indexOf(normalizeString(term));
                if (index >= 0) {
                   const originalTextPart = innerHTML.substr(index, term.length);
                   $element.html(innerHTML.replace(new RegExp(originalTextPart, 'gi'), 
                       match => `<span style="background: rgba(177, 162, 118, 0.2); color: #4D1C21; font-weight: 700; padding: 2px 4px; border-radius: 4px;">${match}</span>`
                   ));
                }
            }

            // Click Themes -> Scroll (No filtering)
            const $mobileBubbles = $('.filter-bubble');
            
            $sidebarLinks.add($mobileBubbles).on('click', function(e) {
                e.preventDefault();
                const targetId = $(this).attr('href').substring(1) || $(this).data('target');
                const $targetElement = $('#' + targetId);
                
                if($targetElement.length) {
                    // Clear search on navigation
                    if($searchInput.val()) {
                        $searchInput.val('');
                        performSearch();
                    }
                    
                    const offset = 140; 
                    const bodyRect = document.body.getBoundingClientRect().top;
                    const elementRect = $targetElement[0].getBoundingClientRect().top;
                    window.scrollTo({ top: elementRect - bodyRect - offset, behavior: 'smooth' });
                }
            });

            // Input Searching
            $searchInput.on('keyup', performSearch);

            $clearSearch.on('click', function() {
                $searchInput.val('');
                performSearch();
            });
            
            // ScrollSpy System for Lateral Menu Highlight
            const spyTargets = $('.scrollspy-target').toArray().reverse();
            $(window).on('scroll', function() {
                // If there is an active search query, do not spy
                if($searchInput.val().length > 0) return;
                
                const scrollPos = $(window).scrollTop() + 160; 
                let currentId = 'containerTop'; // Default fallback
                
                // Find the highest division that we've scrolled past
                for (let i = 0; i < spyTargets.length; i++) {
                    if ($(spyTargets[i]).offset().top <= scrollPos) {
                        currentId = $(spyTargets[i]).attr('id');
                        break;
                    }
                }
                
                const $bar = $('.mobile-quick-filters');
                if (scrollPos > 400 && window.innerWidth < 992) {
                    $bar.addClass('active-bar');
                } else {
                    $bar.removeClass('active-bar');
                }
                
                $sidebarLinks.removeClass('active');
                $mobileBubbles.removeClass('active');

                $('[data-target="' + currentId + '"]').addClass('active');
                $mobileBubbles.filter('[href="#' + currentId + '"]').addClass('active');
                
                // Auto-scroll the filter bar to keep the active bubble visible
                const $activeBubble = $mobileBubbles.filter('.active');
                if ($activeBubble.length && window.innerWidth < 992) {
                    const $bar = $('.mobile-quick-filters');
                    const scrollLeft = $activeBubble.position().left + $bar.scrollLeft() - ($bar.width() / 2) + ($activeBubble.width() / 2);
                    $bar.stop().animate({ scrollLeft: scrollLeft }, 200);
                }
            });
        });
    </script>
</body>
</html>
