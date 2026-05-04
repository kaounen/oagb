<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_fix_encoding')) {
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾', 'experiância', 'C¾digo', 'Bancßrio', 'jurÝdico', 'BasÝlio', 'Janußrio', 'Bastonßrio', 'paÝs', 'JurÝdico', 'CooperaþÒo'];
        $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó', 'experiência', 'Código', 'Bancário', 'jurídico', 'Basílio', 'Januário', 'Bastonário', 'país', 'Jurídico', 'Cooperação'];
        $fixed = str_replace($search, $replace, $text);
        return $fixed;
    }
}

$page_title = "Cooperação Institucional";
$meta_description = "Protocolos e parcerias nacionais e internacionais da Ordem dos Advogados da Guiné-Bissau";
$header_image = 'uploads/justice-symbol-legal-law.jpg';
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
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            line-height: 1; vertical-align: middle;
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        /* Mobile specific breadcrumbs overlaid on bottom of header */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; z-index: 1045 !important; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 30px; border-left: 5px solid var(--primary-gold); padding-left: 20px; }
        
        .coop-card { background: #fff; border-radius: 20px; padding: 40px; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(177, 162, 118, 0.05); transition: .3s; height: 100%; position: relative; overflow: hidden; }
        .coop-card:hover { transform: translateY(-5px); box-shadow: 0 15px 45px rgba(177, 162, 118, 0.12); }
        .coop-card h4 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; margin-bottom: 20px; font-size: 1.25rem; }
        .coop-card p { line-height: 1.8; color: #666; font-size: 0.95rem; }
        .coop-list { list-style: none; padding: 0; margin-top: 25px; }
        .coop-list li { margin-bottom: 12px; font-weight: 600; color: #444; font-size: 0.9rem; display: flex; align-items: center; }
        .coop-list li i { color: var(--primary-gold); margin-right: 12px; font-size: 1.1rem; }

        .cta-box { background: linear-gradient(135deg, var(--primary-maroon) 0%, #7d2e38 100%); border-radius: 24px; padding: 60px; text-align: center; color: #fff; position: relative; overflow: hidden; }
        .cta-box h3 { font-family: 'Libre Baskerville', serif; color: #fff; font-weight: 700; margin-bottom: 20px; }
        .cta-box .btn-white { background: #fff; color: var(--primary-maroon); font-weight: 700; border-radius: 50px; padding: 12px 40px; transition: .3s; border: none; }
        .cta-box .btn-white:hover { background: var(--primary-gold); color: #fff; transform: translateY(-3px); }

        .sidebar-widget { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; position: sticky; top: 120px; }
        .sidebar-link { display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; margin-bottom: 10px; text-decoration: none !important; color: #555; font-weight: 600; transition: all 0.3s; border: 1px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: var(--primary-gold); width: 20px; text-align: center; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }
    </style>
</head>

<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="ordem-dos-advogados.php">A Ordem</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Cooperação Institucional</span>
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

    <!-- Mobile Header -->
    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => 'A Ordem', 'url' => 'ordem-dos-advogados.php'],
        ['label' => 'Cooperação', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row g-5">
                
                <div class="col-lg-8">
                    <span class="section-label">Parcerias</span>
                    <h2 class="section-heading">Cooperação Institucional</h2>
                    
                    <p class="lead mb-5" style="color: #444;">A Ordem dos Advogados da Guiné-Bissau promove o diálogo e a cooperação estratégica com entidades nacionais e internacionais de relevo, fortalecendo a posição jurídica do país e a dignidade da profissão.</p>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-6 wow fadeInUp">
                            <div class="coop-card">
                                <h4>Parcerias Nacionais</h4>
                                <p>Colaboração estreita com as principais instituições do Estado para garantir a melhoria contínua do sistema judicial.</p>
                                <ul class="coop-list">
                                    <li><i class="fas fa-check-circle"></i> Ministério da Justiça</li>
                                    <li><i class="fas fa-check-circle"></i> Conselho Superior da Magistratura</li>
                                    <li><i class="fas fa-check-circle"></i> Instituições Académicas</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 wow fadeInUp">
                            <div class="coop-card">
                                <h4>Cooperação Internacional</h4>
                                <p>Representação ativa em fóruns internacionais de advocacia e intercâmbio de conhecimento jurídico.</p>
                                <ul class="coop-list">
                                    <li><i class="fas fa-check-circle"></i> Advogados da CEDEAO</li>
                                    <li><i class="fas fa-check-circle"></i> União Internacional de Advogados</li>
                                    <li><i class="fas fa-check-circle"></i> Advogados de Língua Portuguesa</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-box shadow-lg wow zoomIn">
                        <i class="fas fa-paper-plane fa-3x mb-4 text-white opacity-75"></i>
                        <h3>Protocolos e Intercâmbios</h3>
                        <p class="mb-4 opacity-75">As nossas portas estão abertas a novas parcerias institucionais que promovam a justiça e o desenvolvimento profissional dos nossos membros.</p>
                        <a href="contacto.php" class="btn btn-white shadow">Contactar Relações Externais</a>
                    </div>
                </div>

                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 120px;">
                        <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px; display: inline-block;">A Ordem</h5>
                        <div class="mt-3">
                            <a href="ordem-dos-advogados.php" class="sidebar-link"><i class="fas fa-history"></i> Apresentação e História</a>
                            <a href="bastonario-ordem.php" class="sidebar-link"><i class="fas fa-user-tie"></i> O Bastonário</a>
                            <a href="orgaos-sociais.php" class="sidebar-link"><i class="fas fa-sitemap"></i> Órgãos Sociais</a>
                            <a href="comissoes-especializadas.php" class="sidebar-link"><i class="fas fa-gavel"></i> Comissões Especializadas</a>
                            <a href="cooperacao-institucional.php" class="sidebar-link active"><i class="fas fa-handshake"></i> Cooperação Institucional</a>
                        </div>
                        

                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top shadow-lg" style="background-color: var(--primary-maroon); border-color: var(--primary-maroon);"><i class="bi bi-arrow-up text-white"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>

