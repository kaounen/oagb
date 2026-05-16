<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_fix_encoding')) {
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾', 'experiância', 'C¾digo', 'Bancßrio', 'jurÝdico', 'BasÝlio', 'Janußrio', 'Bastonßrio', 'paÝs', 'JurÝdico'];
        $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó', 'experiência', 'Código', 'Bancário', 'jurídico', 'Basílio', 'Januário', 'Bastonário', 'país', 'Jurídico'];
        $fixed = str_replace($search, $replace, $text);
        $bin_search = ["\xDF", "\xDD", "\xBE", "\xDA", "Ã¡", "Ã-", "Ã³", "Ã©", "Ã§", "Ã£"];
        $bin_replace = ["á", "í", "ó", "é", "á", "í", "ó", "é", "ç", "ã"];
        return str_replace($bin_search, $bin_replace, $fixed);
    }
}

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
                background: #f7f5f0 !important; padding: 10px 0;
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
            #mobile-header-simple { background: #f7f5f0 !important; padding-bottom: 10px; width: 100%; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #f7f5f0 !important; }
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
            
            /* Logo adjustment for mobile on cream background */
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }
        .text-center .section-heading::after { margin-left: auto; margin-right: auto; }

        /* === PERFIL DO BASTONÁRIO === */
        .bast-profile-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            border: 1px solid #f0ece4; box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        .bast-profile-card:hover { box-shadow: 0 15px 50px rgba(177, 162, 118, 0.15); transform: translateY(-4px); }
        .bast-img-container { background: #f8f8f8; flex: 0 0 400px; max-width: 100%; }
        .bast-img-container img { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); max-width: 100%; height: auto; }
        .bast-content { padding: 2rem; }
        @media (min-width: 768px) { .bast-content { padding: 3rem 4rem; } }
        .bast-content h2 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; margin-bottom: 1rem; word-wrap: break-word; }
        .bast-content .badge { background: var(--primary-gold); letter-spacing: 1px; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; font-weight: 600; }
        .bast-content .bio-text { line-height: 1.8; color: #555; text-align: justify; font-size: 0.95rem; }
        .bast-content .btn-cv { background: var(--primary-maroon); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 600; transition: .3s; border: none; display: inline-block; }
        .bast-content .btn-cv:hover { background: #3a1519; color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(77,28,33,0.3); }

        /* === GALERIA DE BASTONÁRIOS === */
        .bastonario-card { background: #fff; border-radius: 15px; border: 1px solid #f0ece4; transition: .3s; cursor: pointer; padding: 1.5rem 1rem; }
        .bastonario-card:hover { box-shadow: 0 8px 25px rgba(177, 162, 118, 0.12); transform: translateY(-3px); }
        .bastonario-card img { width: 80px; height: 80px; object-fit: cover; border: 3px solid #f8f8f8; border-radius: 50%; margin-bottom: 1rem; }
        .bastonario-card h6 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-size: 0.9rem; margin-bottom: 0.5rem; word-wrap: break-word; }
        .bastonario-card .periodo { font-size: 0.8rem; color: #999; }
        .bastonario-card .bio-extra { display: none; font-size: 0.8rem; color: #666; text-align: justify; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; }

        /* === RESPONSIVO (breakpoint e padding idênticos a a-ordem-dos-advogados) === */
        @media (max-width: 991.98px) {
            html, body { overflow-x: hidden !important; }
            .section-heading { font-size: 1.6rem; }
            .container { padding-left: 20px; padding-right: 20px; }
            .bast-profile-card { flex-direction: column; }
            .bast-img-container { flex: none; width: 100%; padding: 1rem !important; }
            .bast-img-container img { width: 100%; height: auto; }
            .bast-content { padding: 1.5rem !important; }
        }

        /* === DESKTOP OVERRIDES FOR LIGHT BACKGROUND === */
        @media (min-width: 992px) {
            /* Topbar: Dark text on cream */
            #topbar .topbar-contacts small, 
            #topbar .topbar-contacts small i { color: #333 !important; }
            
            #topbar .topbar-btn { 
                color: #333 !important; 
                border-color: rgba(0,0,0,0.15) !important; 
                background: rgba(0,0,0,0.02) !important; 
            }
            #topbar .topbar-btn i { color: var(--primary-maroon) !important; }
            #topbar .topbar-btn:hover { 
                background: rgba(77,28,33,0.05) !important; 
                border-color: var(--primary-maroon) !important; 
            }

            /* Navbar: Dark links on cream */
            .navbar-dark .navbar-nav .nav-link { color: #333 !important; font-weight: 600; }
            .navbar-dark .navbar-nav .nav-link:hover,
            .navbar-dark .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; }
        }
    </style>
</head>

<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header (fundo creme, sem imagem) -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: #f7f5f0; border-bottom: 1px solid #e0dcd2;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="ordem-dos-advogados.php">A Ordem</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">O Bastonário</span>
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

    <!-- Mobile Header (fundo creme, sem imagem) -->
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
                        <a href="ordem-dos-advogados.php">A Ordem</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">O Bastonário</span>
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

    <!-- Content Area -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp">
                <span class="section-label">Liderança e Visão</span>
                <h1 class="section-heading">O Bastonário da Ordem</h1>
            </div>

            <?php if ($bastonario_atual): ?>
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="bast-profile-card d-flex flex-column flex-md-row">
                        <div class="bast-img-container p-4">
                            <img class="img-fluid rounded-3 shadow" src="<?php echo $bastonario_atual->foto_url ? 'uploads/bastonarios/' . $bastonario_atual->foto_url : 'img/placeholder-staff.jpg'; ?>" alt="Foto Bastonário">
                        </div>
                        <div class="bast-content">
                            <h2><?php echo htmlspecialchars(oagb_fix_encoding($bastonario_atual->nome_completo)); ?></h2>
                            <span class="badge mb-4">BASTONÁRIO DA OAGB</span>
                            <div class="mb-4 bio-text">
                                <?php echo nl2br(htmlspecialchars(oagb_fix_encoding($bastonario_atual->biografia))); ?>
                            </div>
                            <?php if ($bastonario_atual->cv_url): ?>
                            <a href="<?php echo $bastonario_atual->cv_url; ?>" target="_blank" class="btn-cv">
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

            <div class="row g-0 gx-md-4 justify-content-center">
                <?php foreach ($antigos_bastonarios as $antigo): ?>
                <div class="col-6 col-md-3">
                    <div class="bastonario-card text-center p-3 h-100">
                        <?php if(!empty($antigo->foto_url)): ?>
                            <img class="rounded-circle mb-3" src="<?php echo 'uploads/bastonarios/' . $antigo->foto_url; ?>" alt="Antigo Bastonário">
                        <?php endif; ?>
                        <h6><?php echo htmlspecialchars(oagb_fix_encoding($antigo->nome_completo)); ?></h6>
                        <small class="periodo d-block mb-2"><?php echo date('Y', strtotime($antigo->data_inicio_mandato)); ?> — <?php echo (!empty($antigo->data_fim_mandato) && $antigo->data_fim_mandato != '0000-00-00') ? date('Y', strtotime($antigo->data_fim_mandato)) : '—'; ?></small>

                        <?php if(!empty($antigo->biografia)): ?>
                        <div class="bio-extra">
                             <?php echo nl2br(htmlspecialchars(oagb_fix_encoding($antigo->biografia))); ?>
                        </div>
                        <?php endif; ?>
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

    <script>
        $(document).ready(function() {
            // Character encoding fix for Portuguese special characters
            function forceCharacterFix() {
                const replacements = {
                    'Janußrio': 'Januário',
                    'Bastonßrio': 'Bastonário',
                    'BasÝlio': 'Basílio',
                    'jurÝdico': 'jurídico',
                    'JurÝdico': 'Jurídico',
                    'Bancßrio': 'Bancário',
                    'C¾digo': 'Código',
                    'experiância': 'experiência',
                    'paÝs': 'país'
                };
                $('body :not(script)').contents().filter(function() {
                    return this.nodeType === 3;
                }).each(function() {
                    let text = this.nodeValue;
                    let changed = false;
                    for (const [noise, fixed] of Object.entries(replacements)) {
                        if (text.indexOf(noise) !== -1) {
                            text = text.split(noise).join(fixed);
                            changed = true;
                        }
                    }
                    if (changed) this.nodeValue = text;
                });
            }

            forceCharacterFix();

            // Toggle bio extra on click
            $('.bastonario-card').on('click', function() {
                $(this).find('.bio-extra').slideToggle();
            });
        });
    </script>
</body>
</html>

