<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM bastonarios WHERE is_atual = 1 LIMIT 1");
    $stmt->execute();
    $bastonario_atual = $stmt->fetch();
    if (!$bastonario_atual) {
        $stmt = $pdo->query("SELECT * FROM bastonarios ORDER BY data_inicio_mandato DESC LIMIT 1");
        $bastonario_atual = $stmt->fetch();
    }
    $stmt = $pdo->prepare("SELECT * FROM bastonarios WHERE id != ? ORDER BY data_inicio_mandato DESC");
    $stmt->execute([$bastonario_atual->id ?? 0]);
    $antigos_bastonarios = $stmt->fetchAll();
} catch (Exception $e) { error_log("Erro: " . $e->getMessage()); }

$page_title = "O Bastonário";
$meta_description = "Conheça o Bastonário da Ordem dos Advogados da Guiné-Bissau.";
$header_image = ''; // Propositadamente vazio conforme pedido
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
        body { font-family: 'Open Sans', sans-serif; background-color: var(--light-grey-bg); }
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

        .section-label { font-size: 0.72rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 10px; }
    </style>
</head>

<body>
    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar_modern.php'; ?>
        <div class="container-fluid bg-header-custom">
            <div class="subpage-breadcrumb-bar">
                <div class="container d-flex align-items-center justify-content-between">
                    <div>
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="a-ordem-dos-advogados.php">A Ordem</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">O Bastonário</span>
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
        <div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-touch="true" style="position: relative; <?php echo !$has_header_image ? 'background: var(--light-grey-bg); min-height: 250px;' : ''; ?>">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <?php if ($has_header_image): ?>
                        <img class="w-100" src="<?php echo htmlspecialchars($header_image); ?>" alt="O Bastonário">
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
                        <?php include 'includes/navbar_modern.php'; ?>
                    </div>

                    <div class="mobile-breadcrumb-bar">
                        <div class="container d-flex align-items-center justify-content-between py-1">
                            <div>
                                <a href="index.php">Início</a>
                                <span class="bc-sep" style="width:4px; height:4px; margin:0 6px;"></span>
                                <span class="bc-active">O Bastonário</span>
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
        </div>
    </div>

    <!-- Content Area -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp">
                <span class="section-label">Liderança e Visão</span>
                <h1 class="section-heading">O Bastonário da Ordem</h1>
            </div>

            <?php if ($bastonario_atual): ?>
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="bast-profile-card d-flex flex-column flex-md-row" style="background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0ece4; box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
                        <div class="bast-img-container p-4" style="background: #f8f8f8; flex: 0 0 400px; max-width: 100%;">
                            <img class="img-fluid rounded-3 shadow" src="<?php echo $bastonario_atual->foto_url ?: 'img/placeholder-staff.jpg'; ?>" alt="Foto Bastonário">
                        </div>
                        <div class="bast-content p-4 p-lg-5">
                            <h2 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700;"><?php echo htmlspecialchars(oagb_fix_encoding($bastonario_atual->nome_completo)); ?></h2>
                            <span class="badge mb-4" style="background: var(--primary-gold); letter-spacing: 1px;">BASTONÁRIO DA OAGB</span>
                            <div class="mb-4" style="line-height: 1.8; color: #555; text-align: justify;">
                                <?php echo nl2br(htmlspecialchars(oagb_fix_encoding($bastonario_atual->biografia))); ?>
                            </div>
                            <?php if ($bastonario_atual->cv_url): ?>
                            <a href="<?php echo $bastonario_atual->cv_url; ?>" target="_blank" class="btn" style="background: var(--primary-maroon); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 600;">
                                <i class="fas fa-file-download me-2"></i> Descarregar Curriculum Vitae
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-center mx-auto mt-5 mb-5 pt-4">
                <span class="section-label">Legado</span>
                <h2 class="section-heading">Galeria de Bastonários</h2>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach ($antigos_bastonarios as $antigo): ?>
                <div class="col-6 col-md-3">
                    <div class="text-center p-3 h-100" style="background: #fff; border-radius: 15px; border: 1px solid #f0ece4; transition: .3s;">
                        <img class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f8f8f8;" src="<?php echo $antigo->foto_url ?: 'img/placeholder-staff.jpg'; ?>" alt="Antigo Bastonário">
                        <h6 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon);"><?php echo htmlspecialchars(oagb_fix_encoding($antigo->nome_completo)); ?></h6>
                        <small class="text-muted"><?php echo date('Y', strtotime($antigo->data_inicio_mandato)); ?> — <?php echo (!empty($antigo->data_fim_mandato) && $antigo->data_fim_mandato != '0000-00-00') ? date('Y', strtotime($antigo->data_fim_mandato)) : 'Presente'; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
