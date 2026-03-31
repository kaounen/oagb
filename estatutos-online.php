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

        /* Search Form Premium */
        .search-container { position: relative; width: 100%; margin-bottom: 25px; }
        .search-wrapper { position: relative; max-width: 500px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 30px; overflow: hidden; background: #fff; }
        .search-wrapper input { border-radius: 30px; padding: 15px 50px 15px 25px; border: 1px solid #eee; font-size: 1rem; width: 100%; transition: .3s; }
        .search-wrapper input:focus { border-color: var(--primary-gold); outline: none; box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        #clearSearch { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #ccc; display: none; transition: .3s; z-index: 5; }

        .section-label { font-size: 0.72rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .sidebar-link { display: flex; align-items: center; gap: 15px; padding: 12px 15px; border-radius: 12px; color: #555; text-decoration: none; transition: .3s; margin-bottom: 5px; font-size: 0.9rem; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(177,162,118,0.08); color: var(--primary-maroon); font-weight: 600; }
        .artigo-block { background: #fff; padding: 35px; border-radius: 20px; border: 1px solid #f0ece4; margin-bottom: 25px; transition: .3s; }
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
                        <span class="bc-active">Estatutos Online</span>
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
                        <img class="w-100" src="<?php echo htmlspecialchars($header_image); ?>" alt="Estatutos OAGB">
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
                                <span class="bc-active">Estatutos</span>
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
                <span class="section-label">Legislação e Normas</span>
                <h2 class="section-heading">Estatutos da Ordem</h2>
            </div>
            
            <div class="search-container mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <div class="search-wrapper">
                    <input type="text" id="searchEstatutos" placeholder="Pesquisar por palavra-chave ou nº artigo...">
                    <i class="fas fa-times" id="clearSearch"></i>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="sticky-top" style="top: 100px; z-index: 10;">
                        <h5 class="mb-4" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px;">Temas Principais</h5>
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                            <?php foreach ($temas as $tema_nome => $tema_info): ?>
                            <a class="sidebar-link" href="#<?php echo $tema_info['slug']; ?>">
                                <i class="<?php echo $tema_info['icon']; ?> text-primary" style="width: 20px;"></i>
                                <?php echo htmlspecialchars(oagb_fix_encoding($tema_nome)); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php if (empty($temas)): ?>
                        <div class="alert alert-info py-4 text-center">Nenhum artigo encontrado.</div>
                    <?php else: ?>
                        <?php foreach ($temas as $tema_nome => $tema_info): ?>
                        <div id="<?php echo $tema_info['slug']; ?>" class="tema-section mb-5">
                            <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="<?php echo $tema_info['icon']; ?>"></i>
                                </span>
                                <h3 class="m-0" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif;"><?php echo htmlspecialchars(oagb_fix_encoding($tema_nome)); ?></h3>
                            </div>

                            <?php foreach ($tema_info['artigos'] as $artigo): ?>
                            <div class="artigo-block wow fadeInUp" data-artigo-num="<?php echo $artigo['numero_artigo']; ?>" data-tema="<?php echo htmlspecialchars($tema_nome); ?>">
                                <h5 class="mb-3" style="color: var(--primary-maroon); font-weight: 700; border-left: 4px solid var(--primary-gold); padding-left: 15px;">
                                    Artigo <?php echo $artigo['numero_artigo']; ?>º (<?php echo htmlspecialchars(oagb_fix_encoding($artigo['titulo_artigo'])); ?>)
                                </h5>
                                <div class="artigo-content" style="line-height: 1.8; color: #444; font-size: 0.95rem; text-align: justify;">
                                    <?php echo oagb_fix_encoding($artigo['conteudo_artigo']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            const $temas = $('.tema-section');

            function performSearch() {
                const rawValue = $searchInput.val();
                const searchTerm = normalizeString(rawValue);

                if (rawValue.length > 0) {
                    $clearSearch.fadeIn();
                } else {
                    $clearSearch.fadeOut();
                }

                $artigos.each(function() {
                    const $artigo = $(this);
                    const $content = $artigo.find('.artigo-content');
                    const $title = $artigo.find('h5');
                    
                    // Reset highlights first
                    if ($artigo.data('original-content')) {
                        $content.html($artigo.data('original-content'));
                        $title.html($artigo.data('original-title'));
                    } else {
                        // Store original for first time
                        $artigo.data('original-content', $content.html());
                        $artigo.data('original-title', $title.html());
                    }

                    if (!searchTerm) {
                        $artigo.show();
                        return;
                    }

                    const normalizedText = normalizeString($artigo.text());
                    const isMatch = normalizedText.indexOf(searchTerm) > -1;

                    if (isMatch) {
                        $artigo.show();
                        
                        // Highlight logic
                        highlightMatch($title, rawValue);
                        highlightMatch($content, rawValue);
                    } else {
                        $artigo.hide();
                    }
                });

                // Show/Hide sections
                $temas.each(function() {
                    const visibleCount = $(this).find('.artigo-block:visible').length;
                    $(this).toggle(visibleCount > 0);
                });
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

            $searchInput.on('keyup', performSearch);
            $clearSearch.on('click', function() {
                $searchInput.val('');
                performSearch();
            });

            // Handle sidebar thematic jump
            $('.sidebar-link').on('click', function(e) {
                e.preventDefault();
                const target = $(this).attr('href');
                
                // Clear search if navigating themes
                if ($searchInput.val()) {
                    $searchInput.val('');
                    performSearch();
                }

                const offset = 140; // Desktop offset for fixed header
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = document.querySelector(target).getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update active link
                $('.sidebar-link').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
</body>
</html>
