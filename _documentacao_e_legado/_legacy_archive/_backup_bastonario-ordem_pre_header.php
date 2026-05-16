<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_fix_encoding')) {
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        // Mapeamento radical e binário para caracteres corrompidos comuns
        $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾', 'experiância', '¾', 'C¾digo', 'Bancßrio', 'jurÝdico', 'BasÝlio', 'Janußrio', 'Bastonßrio', 'paÝs', 'JurÝdico'];
        $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó', 'experiência', 'ó', 'Código', 'Bancário', 'jurídico', 'Basílio', 'Januário', 'Bastonário', 'país', 'Jurídico'];
        
        $fixed = str_replace($search, $replace, $text);
        
        // Reforço binário manual
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
$header_image = 'uploads/close-up-scales-justice-original-azul.jpg'; 
$has_header_image = true; // Sincronizado com o padrão institucional d'A Ordem
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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">

</head>

<body>
    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
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

    <!-- Mobile Header Radical Rewrite (No Carousel/Image ressidues) -->
    <div class="d-block d-lg-none">
        <div id="mobile-header-simple" style="position: relative; background: #f7f5f0; padding-bottom: 5px; width: 100%; overflow: hidden;">
            <!-- Contacts - Dark version for light background -->
            <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                <div class="row g-0 mb-3 justify-content-center align-items-center" style="width: 100%; margin: 0;">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-2 overflow-auto" style="white-space: nowrap;">
                        <small class="text-nowrap"><i class="fa fa-map-marker-alt me-1"></i>Av. Amílcar Cabral</small>
                        <small class="text-nowrap"><i class="fa fa-phone-alt me-1"></i>+245 955475889</small>
                        <small class="text-nowrap"><i class="fa fa-envelope-open me-1"></i>info@oagb.gw</small>
                    </div>
                </div>
                
                <div class="row g-0 mb-1 justify-content-center align-items-center" style="width: 100%; margin: 0;">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                             <i class="fa fa-search" style="font-size: 1rem; color: var(--primary-maroon) !important;"></i>
                        </button>
                        
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="fa fa-globe" style="font-size: 1rem; color: var(--primary-maroon) !important;"></i>
                            </button>
                            <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1" style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                            </div>
                        </div>
                        
                        <a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center" style="color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important;">
                            <i class="fas fa-user-circle me-1" style="font-size: 1rem; color: var(--primary-maroon) !important;"></i> Área Reservada
                        </a>
                    </div>
                </div>
            </div>

            <div class="mobile-navbar-wrapper container-fluid p-0" style="margin-top: 5px; width: 100%; overflow: hidden;">
                <?php include 'includes/navbar.php'; ?>
            </div>

            <div class="mobile-breadcrumb-bar" style="background: #f7f5f0 !important; width: 100%; overflow: hidden;">
                <div class="container d-flex align-items-center justify-content-between py-2">
                    <div style="font-size: 0.72rem; color: #666;">
                        <a href="index.php" style="color: #666; text-decoration: none;">Início</a>
                        <span class="bc-sep" style="width:4px; height:4px; margin:0 6px; background: var(--primary-gold); display: inline-block; border-radius: 50%;"></span>
                        <a href="a-ordem-dos-advogados.php" style="color: #666; text-decoration: none;">A Ordem</a>
                        <span class="bc-sep" style="width:4px; height:4px; margin:0 6px; background: var(--primary-gold); display: inline-block; border-radius: 50%;"></span>
                        <span style="color: var(--primary-maroon); font-weight: 600;">O Bastonário</span>
                    </div>
                    <div class="quick-links d-flex gap-2">
                        <a href="javascript:history.back()" style="color: var(--primary-maroon) !important; border: 1px solid var(--primary-maroon); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()" style="color: var(--primary-maroon) !important; border: 1px solid var(--primary-maroon); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}" style="color: var(--primary-maroon) !important; border: 1px solid var(--primary-maroon); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-share-alt"></i></a>
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

            <div class="row g-0 gx-md-4 justify-content-center" style="margin: 0 !important;">
                <?php foreach ($antigos_bastonarios as $antigo): ?>
                <div class="col-6 col-md-3">
                    <div class="text-center p-3 h-100" style="background: #fff; border-radius: 15px; border: 1px solid #f0ece4; transition: .3s; cursor: pointer;" onclick="$(this).find('.bio-extra').slideToggle();">
                        <?php if(!empty($antigo->foto_url)): ?>
                            <img class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #f8f8f8;" src="<?php echo $antigo->foto_url; ?>" alt="Antigo Bastonário">
                        <?php endif; ?>
                        <h6 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-size: 0.9rem;"><?php echo htmlspecialchars(oagb_fix_encoding($antigo->nome_completo)); ?></h6>
                        <small class="text-muted d-block mb-2"><?php echo date('Y', strtotime($antigo->data_inicio_mandato)); ?> — <?php echo (!empty($antigo->data_fim_mandato) && $antigo->data_fim_mandato != '0000-00-00') ? date('Y', strtotime($antigo->data_fim_mandato)) : '—'; ?></small>
                        
                        <?php if(!empty($antigo->biografia)): ?>
                        <div class="bio-extra" style="display: none; font-size: 0.75rem; color: #666; text-align: justify; margin-top: 10px; border-top: 1px solid #eee; pt-2">
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

    <style>
        :root {
            --primary-maroon: #4D1C21;
            --primary-gold: #B1A276;
            --light-grey-bg: #f7f5f0;
        }

        /* === SINCRONIZAÇÃO INSTITUCIONAL PREMIUM (FOOTER INJECTION) === */
        /* Estas regras carregam por último para anular os ficheiros incluídos */
        
        .bg-header-custom {
            min-height: 400px; display: flex; align-items: flex-end; position: relative;
            background: <?php echo $has_header_image ? "linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('$header_image') center center / cover" : "var(--light-grey-bg)"; ?>;
            border-bottom: none !important;
        }
        .subpage-breadcrumb-bar { padding: 10px 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 10px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar .bc-active {
            color: #666 !important; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; 
        }
        .subpage-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; }
        .subpage-breadcrumb-bar .bc-sep { width: 5px; height: 5px; background: var(--primary-gold); border-radius: 50%; margin: 0 10px; display: inline-block; }
        
        .section-label { font-size: 0.72rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 600; font-size: 1.5rem; line-height: 1.3; margin-bottom: 10px; }
        
        /* Forçar cores escuras nos botões - Mobile & Desktop */
        .mobile-pill-btn, .mobile-pill-btn i, .mobile-pill-btn span { color: var(--primary-maroon) !important; }
        .mobile-pill-btn:hover, .mobile-pill-btn:active, .mobile-pill-btn:focus { color: var(--primary-maroon) !important; background: rgba(0,0,0,0.08) !important; border-color: var(--primary-gold) !important; }
        .mobile-navbar-wrapper .navbar-toggler, 
        .mobile-navbar-wrapper .navbar-toggler i, 
        .mobile-navbar-wrapper .navbar-toggler span { color: var(--primary-gold) !important; font-weight: 700 !important; border-color: var(--primary-gold) !important; }

        .quick-links a, .subpage-breadcrumb-bar .quick-links a { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; }
        .quick-links a:hover, .subpage-breadcrumb-bar .quick-links a:hover,
        .mobile-pill-btn:hover, .mobile-pill-btn:active, .mobile-pill-btn:focus { 
            background: rgba(77,28,33,0.08) !important; 
            color: var(--primary-maroon) !important; 
            border-color: var(--primary-gold) !important; 
        }
        .quick-links a:hover i, .mobile-pill-btn:hover i, .mobile-pill-btn span:hover { color: var(--primary-maroon) !important; }

        #mobile-header-simple, .mobile-navbar-wrapper, .mobile-navbar-wrapper .navbar, .mobile-header-contacts, .mobile-breadcrumb-bar { background: #f7f5f0 !important; border: none !important; box-shadow: none !important; }
        
        .navbar-toggler, .navbar-toggler * { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; font-weight: 700 !important; text-transform: uppercase !important; }
        
        /* Corrigir Hover de Botões de Ação (Desktop & Mobile) */
        .quick-links a, .subpage-breadcrumb-bar .quick-links a { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.85rem; }
        .quick-links a:hover, .subpage-breadcrumb-bar .quick-links a:hover,
        .mobile-pill-btn:hover, .mobile-pill-btn:active, .mobile-pill-btn:focus { 
            background: rgba(77,28,33,0.08) !important; 
            color: var(--primary-maroon) !important; 
            border-color: var(--primary-gold) !important; 
        }
        .quick-links a:hover i, .mobile-pill-btn:hover i, .mobile-pill-btn span:hover { color: var(--primary-maroon) !important; }
        
        /* Estilos específicos para Desktop (Min-Width: 992px) */
        @media (min-width: 992px) {
            /* Forçar cor bordeaux no menu sobre fundo creme no desktop */
            .navbar-nav .nav-link, 
            .navbar-nav .nav-link:hover, 
            .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; opacity: 1 !important; }
        }

        /* Botões de Ação (Quick Links) */
        .quick-links a, .subpage-breadcrumb-bar .quick-links a { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; }
        .quick-links a:hover, .subpage-breadcrumb-bar .quick-links a:hover { background: rgba(77,28,33,0.08) !important; color: var(--primary-maroon) !important; border-color: var(--primary-gold) !important; }
        .quick-links a:hover i, .subpage-breadcrumb-bar .quick-links a:hover i { color: var(--primary-maroon) !important; }

        /* Bloqueio de Scroll Horizontal - Apenas para Mobile */
        @media (max-width: 991px) {
            html, body { overflow-x: hidden !important; max-width: 100vw !important; }
            #mobile-header-simple .row { margin-left: 0 !important; margin-right: 0 !important; }
            .mobile-breadcrumb-bar { border-bottom: 1px solid #e0dcd2 !important; }
            .mobile-breadcrumb-bar .container { padding-left: 15px !important; padding-right: 15px !important; }
            #mobile-header-simple .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
        }
        body { position: relative !important; background-color: #f7f5f0 !important; }
        
        /* Corrigir Hover de Botões para não ficarem Brancos (Top Bar & Action Buttons) */
        .quick-links a:hover, .mobile-pill-btn:hover,
        .btn-link:hover, a.btn-link:hover, .top-bar a:hover { 
            background: rgba(77,28,33,0.08) !important; 
            color: var(--primary-maroon) !important; 
            border-color: var(--primary-gold) !important; 
        }
        .quick-links a:hover i, .mobile-pill-btn:hover i, .top-bar a:hover i { color: var(--primary-maroon) !important; }
    </style>

    <script>
        // JS-Turbo-Sync (Specialist Agent Nuclear Force) 
        $(document).ready(function() {
            function forceMenuGold() {
                $('.navbar-toggler, .navbar-toggler i, .navbar-toggler span, .navbar-toggler-text').each(function() {
                    $(this).css({'color': '#B1A276', 'border-color': '#B1A276'}).attr('style', function(i,s) { return (s || '') + 'color: #B1A276 !important; border-color: #B1A276 !important;'; });
                });
            }
            
            function forceCleanLayout() {
                $('body').css({'overflow-x': 'hidden', 'width': '100vw'});
                $('#mobile-header-simple, .mobile-navbar-wrapper, .mobile-navbar-wrapper .navbar, .mobile-header-contacts, .mobile-breadcrumb-bar').each(function() {
                     this.style.setProperty('background-color', '#f7f5f0', 'important');
                     this.style.setProperty('border', 'none', 'important');
                     this.style.setProperty('box-shadow', 'none', 'important');
                });
                $('.row').css({'margin-left': '0', 'margin-right': '0', 'max-width': '100%'});
            }

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

            // Persistence Loop - Repeat force every 500ms for 3 seconds
            let syncCount = 0;
            const syncInterval = setInterval(function() {
                forceMenuGold();
                forceCleanLayout();
                forceCharacterFix();
                syncCount++;
                if (syncCount > 6) clearInterval(syncInterval);
            }, 500);
            
            $(window).on('scroll resize', forceMenuGold);
            $('.navbar-toggler').on('click', function() {
                setTimeout(forceMenuGold, 50);
            });
        });
    </script>
</body>
</html>
