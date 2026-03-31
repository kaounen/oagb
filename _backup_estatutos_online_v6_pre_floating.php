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
    <style>
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
        .subpage-breadcrumb-bar { padding: 10px 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 10px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar .bc-active {
            font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s;
            color: <?php echo $has_header_image ? 'rgba(255,255,255,0.85)' : '#777'; ?>;
            <?php if($has_header_image): ?> text-shadow: 0 1px 4px rgba(0,0,0,0.6); <?php endif; ?>
        }
        .subpage-breadcrumb-bar a:hover { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-gold)'; ?>; }
        .subpage-breadcrumb-bar .bc-active { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-maroon)'; ?>; font-weight: 600; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid <?php echo $has_header_image ? 'rgba(255,255,255,0.3)' : 'var(--primary-maroon)'; ?>;
            display: inline-flex; align-items: center; justify-content: center;
            color: <?php echo $has_header_image ? 'rgba(255,255,255,0.9)' : 'var(--primary-maroon)'; ?>;
            transition: .3s; font-size: 0.8rem;
        }
        .quick-links a:hover { background: <?php echo $has_header_image ? 'rgba(255,255,255,0.15)' : 'rgba(77,28,33,0.1)'; ?>; color: <?php echo $has_header_image ? '#fff' : 'var(--primary-gold)'; ?>; border-color: var(--primary-gold); }

        /* Topbar Darkness if No Header Image - ONLY FOR DESKTOP */
        @media (min-width: 992px) {
            <?php if (!$has_header_image): ?>
            .topbar-contacts small, .topbar-contacts small i { color: #333 !important; }
            .topbar-btn { color: #333 !important; border-color: #ccc !important; background: rgba(0,0,0,0.03) !important; }
            .topbar-btn i { color: var(--primary-maroon) !important; }
            .navbar-dark:not(.navbar-scrolled) .nav-link { color: #333 !important; }
            <?php endif; ?>
        }

        /* === MOBILE HEADER SYNC === */
        @media (max-width: 991px) {
            .mobile-header-contacts i { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-maroon)'; ?> !important; opacity: 0.85; }
            .mobile-header-contacts small { color: <?php echo $has_header_image ? '#fff' : '#333'; ?> !important; font-size: 0.70rem !important; }
            .mobile-pill-btn { border-radius: 50px !important; border: 1px solid <?php echo $has_header_image ? 'rgba(255,255,255,0.4)' : 'var(--primary-maroon)'; ?> !important; color: <?php echo $has_header_image ? '#fff' : '#333'; ?> !important; background: <?php echo $has_header_image ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.03)'; ?> !important; }
            
            .mobile-navbar-wrapper .navbar-toggler { border-color: var(--primary-gold) !important; }
            
            .mobile-breadcrumb-bar { background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; z-index: 999 !important; pointer-events: auto !important; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar .bc-active { font-size: 0.7rem; color: <?php echo $has_header_image ? 'rgba(255,255,255,0.73)' : '#666'; ?>; text-shadow: <?php echo $has_header_image ? '0 1px 3px rgba(0,0,0,0.6)' : 'none'; ?>; pointer-events: auto !important; }
            .mobile-breadcrumb-bar .bc-active { color: <?php echo $has_header_image ? '#fff' : 'var(--primary-maroon)'; ?>; font-weight: 600; }
            
            .mobile-breadcrumb-bar .quick-links a { 
                width: 32px; height: 32px; font-size: 0.7rem; 
                color: <?php echo $has_header_image ? 'rgba(255,255,255,0.7)' : 'var(--primary-maroon)'; ?>; 
                border-color: <?php echo $has_header_image ? 'rgba(255,255,255,0.3)' : 'var(--primary-maroon)'; ?> !important; 
                pointer-events: auto !important; 
            }
            .ordem-mobile-caption { bottom: 55px !important; padding: 0 1rem !important; }
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
        .artigo-title { font-family: 'Open Sans', sans-serif; font-size: 1.05rem !important; color: #B1A276; font-weight: 600 !important; margin-bottom: 8px; }
        .texto-conteudo { font-family: 'Open Sans', sans-serif; font-size: 0.85rem !important; line-height: 1.7; color: #111923 !important; text-align: justify; font-weight: 600; }
        
        /* Thematic section dividers */
        .tema-section-title { font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 500; font-size: 1.4rem; padding-bottom: 10px; border-bottom: 1px solid rgba(77, 28, 33, 0.2); margin-top: 40px; margin-bottom: 25px; scroll-margin-top: 140px; }
        
        @media (max-width: 991px) {
            .mobile-sidebar-grid { display: flex; flex-direction: column; gap: 4px; margin-bottom: 20px; }
            .sidebar-link { font-size: 0.85rem; padding: 10px 12px; border-radius: 8px; border-left: 3px solid transparent; flex-direction: row; text-align: left; gap: 10px; justify-content: flex-start; align-items: center; }
            .sidebar-link:hover, .sidebar-link.active { border-left-color: var(--primary-gold); border-radius: 8px; }
            .sidebar-badge { margin-left: auto; }
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
                <div class="container d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.85rem;">Início</span>
                        <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 10px; vertical-align: middle;"></span>
                        <span style="color: rgba(255,255,255,0.85); font-size: 0.85rem;">A Ordem</span>
                        <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 10px; vertical-align: middle;"></span>
                        <span class="bc-active" style="color: #fff; font-weight: 500; font-size: 0.85rem;">Estatutos</span>
                    </div>
                    <div class="quick-links d-flex gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="d-block d-lg-none">
        <div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-touch="true" style="position: relative; <?php echo !$has_header_image ? 'background: var(--light-grey-bg); min-height: 180px;' : ''; ?>">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <?php if ($has_header_image): ?>
                        <img class="w-100" src="<?php echo htmlspecialchars($header_image); ?>" alt="Estatutos OAGB" style="max-height: 250px; object-fit: cover; object-position: top center;">
                    <?php endif; ?>

                    <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                        <div class="row mb-3 mx-0">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                                <small class="text-nowrap"><i class="fa fa-map-marker-alt me-1"></i>Av. Amílcar Cabral</small>
                                <small class="text-nowrap"><i class="fa fa-phone-alt me-1"></i>+245 955475889</small>
                                <small class="text-nowrap"><i class="fa fa-envelope-open me-1"></i>info@oagb.gw</small>
                            </div>
                        </div>
                        
                        <div class="row mb-1 mx-0">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; width: 100%;">
                                <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                                     <i class="fa fa-search" style="font-size: 1rem;"></i>
                                </button>
                                
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static">
                                        <i class="fa fa-globe" style="font-size: 1rem;"></i>
                                    </button>
                                    <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1" style="min-width: 150px; z-index: 2000; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                        <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1" style="font-size: 0.8rem;"><span class="me-2">🇵🇹</span> Português</a>
                                        <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1" style="font-size: 0.8rem;"><span class="me-2">🇺🇸</span> English</a>
                                    </div>
                                </div>
                                
                                <a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center">
                                    <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Portal
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0" style="margin-top: <?php echo $has_header_image ? '0' : '5px'; ?>;">
                        <?php include 'includes/navbar.php'; ?>
                    </div>

                    <div class="header-overlay-bar mobile-breadcrumb-bar">
                        <div class="container d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span style="color: rgba(255,255,255,0.85); font-size: 0.80rem;">Início</span>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span style="color: rgba(255,255,255,0.85); font-size: 0.80rem;">A Ordem</span>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: rgba(255,255,255,0.7); display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span class="bc-active" style="color: #fff; font-weight: 500; font-size: 0.80rem;">Estatutos</span>
                            </div>
                            <div class="header-circles d-flex align-items-center">
                                <a href="javascript:history.back()" title="Voltar" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-arrow-left" style="font-size:0.65rem;"></i></a>
                                <a href="javascript:window.print()" title="Imprimir" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-print" style="font-size:0.65rem;"></i></a>
                                <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});} else {sharePage(); return false;}" title="Partilhar" style="width: 25px; height: 25px; border-radius: 50%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; margin-left: 6px;"><i class="fas fa-share-alt" style="font-size:0.65rem;"></i></a>
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

                <!-- Articles Content Area -->
                <div class="col-lg-9" id="containerTop">
                    <!-- Title, Search, and DOC Button -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end border-bottom pb-3 mb-3">
                        <div class="mb-3 mb-md-0" style="flex: 1; padding-right: 15px;">
                            <h2 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 600; font-size: 1.4rem; margin-bottom: 3px;">Estatutos da Ordem dos Advogados da Guiné-Bissau</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Aprovado na Assembleia Geral de 28 de Julho de 2018 • <strong id="artigoCounter" style="color: var(--primary-gold);"><?php echo $total_artigos; ?></strong> Artigos</p>
                        </div>
                        <div class="d-flex align-items-center mt-3 mt-md-0 w-100 justify-content-md-end" style="gap: 10px; max-width: 350px;">
                            <div class="search-wrapper flex-grow-1" style="border-radius: 8px; background: #fff; border: 1px solid rgba(177,162,118,0.4); display: flex; align-items: center; padding: 0 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); height: 38px;">
                                <i class="fa fa-search text-muted me-2" style="font-size: 0.95rem; opacity: 0.6;"></i>
                                <input type="text" id="searchEstatutos" placeholder="Pesquisar..." style="border: none; background: transparent; width: 100%; font-size: 0.9rem; outline: none; color: #333; padding: 0;">
                                <i class="fas fa-times" id="clearSearch" style="cursor: pointer; color: #ccc; display: none; margin-left: 5px; font-size: 1rem;"></i>
                            </div>
                            <a href="docsoagb/oagb_estatutos.pdf" target="_blank" class="btn btn-sm d-flex align-items-center justify-content-center flex-shrink-0" style="background: var(--primary-maroon); color: #fff; border-radius: 8px; padding: 0 16px; font-weight: 600; font-size: 0.8rem; letter-spacing: 0.5px; white-space: nowrap; height: 38px;">
                                <i class="fas fa-download me-1"></i> DOC
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
            $sidebarLinks.on('click', function(e) {
                e.preventDefault();
                const targetId = $(this).data('target');
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
                
                $sidebarLinks.removeClass('active');
                $('[data-target="' + currentId + '"]').addClass('active');
            });
        });
    </script>
</body>
</html>
