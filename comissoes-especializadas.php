<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_fix_encoding')) {
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾', 'experiância', 'C¾digo', 'Bancßrio', 'jurÝdico', 'BasÝlio', 'Janußrio', 'Bastonßrio', 'paÝs', 'JurÝdico', 'Direitos Humanos', 'ComissÒo'];
        $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó', 'experiência', 'Código', 'Bancário', 'jurídico', 'Basílio', 'Januário', 'Bastonário', 'país', 'Jurídico', 'Direitos Humanos', 'Comissão'];
        $fixed = str_replace($search, $replace, $text);
        
        $bin_search = ["\xDF", "\xDD", "\xBE", "\xDA", "Ã¡", "Ã-", "Ã³", "Ã©", "Ã§", "Ã£"];
        $bin_replace = ["á", "í", "ó", "é", "á", "í", "ó", "é", "ç", "ã"];
        return str_replace($bin_search, $bin_replace, $fixed);
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM comissoes WHERE ativo = 1 ORDER BY nome ASC");
    $stmt->execute();
    $comissoes = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (Exception $e) {
    error_log("Erro ao buscar comissões: " . $e->getMessage());
    $comissoes = [];
}

$page_title = "Comissões Especializadas";
$meta_description = "Conheça as Comissões Especializadas da Ordem dos Advogados da Guiné-Bissau";
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
            .mobile-breadcrumb-bar { 
                background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; 
                z-index: 1045 !important; pointer-events: auto !important; 
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { 
                font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            }
            .mobile-breadcrumb-bar .bc-active { font-weight: 500; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { 
                border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; 
            }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }
        .text-center .section-heading::after { margin-left: auto; margin-right: auto; }

        /* === COMISSÕES CARDS === */
        .commission-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; transition: all 0.3s ease; border: 1px solid #f0ece4; }
        .commission-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(177, 162, 118, 0.15); }
        .commission-header { background: linear-gradient(135deg, var(--primary-maroon) 0%, #7d2e38 100%); color: white; padding: 40px 30px; position: relative; }
        .commission-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 20px; backdrop-filter: blur(5px); color: white; }
        .commission-name { font-family: 'Libre Baskerville', serif; font-size: 1.3rem; margin-bottom: 15px; font-weight: 700; color: #fff !important; }
        .commission-area { display: inline-block; padding: 4px 14px; background: rgba(255, 255, 255, 0.15); color: #fff; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.2); }
        .commission-body { padding: 40px 30px 40px; }
        .commission-description { color: #666; line-height: 1.8; margin-bottom: 30px; font-size: 0.95rem; }
        .commission-info { background: #fdfbf7; border-radius: 16px; padding: 25px; margin-bottom: 20px; border: 1px solid #f9f6f0; }
        .commission-info h5 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-size: 1rem; margin-bottom: 12px; font-weight: 700; border-bottom: 1px solid rgba(177, 162, 118, 0.2); padding-bottom: 8px; }
        .commission-info p { margin-bottom: 5px; color: #444; font-weight: 600; font-size: 0.9rem; }
        .member-list { list-style: none; padding: 0; margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px; }
        .member-list li { background: #fff; border: 1px solid #eee; padding: 4px 12px; border-radius: 6px; font-size: 0.8rem; color: #666; }
        
        .intro-section { background: #fff; border-radius: 24px; padding: 50px; margin-bottom: 60px; border: 1px solid #f0ece4; box-shadow: 0 15px 45px rgba(0,0,0,0.03); }
        .intro-section h2 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; }
        
        .stat-card { background: #fff; border-radius: 20px; padding: 40px; text-align: center; border: 1px solid #f0ece4; transition: .3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(177, 162, 118, 0.1); }
        .stat-number { font-size: 3rem; font-weight: 800; color: var(--primary-gold); margin-bottom: 10px; font-family: 'Libre Baskerville', serif; }
        .stat-label { color: #888; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        .btn-cta-gold { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 700; transition: .3s; border: none; }
        .btn-cta-gold:hover { background: var(--primary-maroon); color: #fff; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
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
                        <span class="bc-active">Comissões Especializadas</span>
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
        ['label' => 'Comissões Especializadas', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            
            <div class="intro-section">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="section-label">Órgãos Técnicos</span>
                        <h2 class="section-heading">Especialização e Excelência</h2>
                        <p class="lead mb-4">
                            As Comissões Especializadas da OAGB são órgãos técnicos consultivos que desenvolvem trabalho altamente especializado, contribuindo para a modernização da justiça e defesa da classe.
                        </p>
                        <p class="text-muted mb-0">
                            Compostas por advogados de mérito em diversas áreas do Direito, estas comissões garantem que a Ordem esteja presente nos grandes debates jurídicos nacionais e internacionais.
                        </p>
                    </div>
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="stat-card shadow-sm">
                            <div class="stat-number" data-toggle="counter-up"><?php echo count($comissoes); ?></div>
                            <div class="stat-label">Comissões Ativas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Comissões -->
            <div class="row">
                <?php if (!empty($comissoes)): foreach ($comissoes as $c): ?>
                <div class="col-lg-6 mb-4 wow fadeInUp">
                    <div class="commission-card">
                        <div class="commission-header">
                            <div class="commission-icon">
                                <?php
                                $icon = 'fa-gavel';
                                $area = strtolower($c->area_atuacao ?? '');
                                if (strpos($area, 'humanos') !== false) $icon = 'fa-user-shield';
                                elseif (strpos($area, 'formação') !== false || strpos($area, 'estágio') !== false) $icon = 'fa-graduation-cap';
                                elseif (strpos($area, 'ética') !== false || strpos($area, 'deontologia') !== false) $icon = 'fa-balance-scale';
                                elseif (strpos($area, 'legislação') !== false) $icon = 'fa-book-reader';
                                elseif (strpos($area, 'apoio') !== false) $icon = 'fa-hands-helping';
                                ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <h4 class="commission-name"><?php echo oagb_fix_encoding($c->nome); ?></h4>
                            <?php if (!empty($c->area_atuacao)): ?>
                                <span class="commission-area"><?php echo oagb_fix_encoding($c->area_atuacao); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="commission-body">
                            <p class="commission-description">
                                <?php echo oagb_fix_encoding($c->descricao); ?>
                            </p>
                            
                            <?php if (!empty($c->presidente)): ?>
                            <div class="commission-info border-start border-4" style="border-color: var(--primary-gold) !important;">
                                <h5>Presidência</h5>
                                <p><?php echo oagb_fix_encoding($c->presidente); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($c->membros)): ?>
                            <div class="commission-info">
                                <h5>Membros da Comissão</h5>
                                <ul class="member-list">
                                    <?php 
                                    $membros = explode(',', $c->membros);
                                    foreach ($membros as $m):
                                    ?>
                                    <li><?php echo trim(oagb_fix_encoding($m)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Nenhuma comissão ativa encontrada.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- CTA -->
            <div class="bg-white p-5 rounded-4 shadow-sm border text-center mt-5">
                <h3 class="section-heading" style="font-size: 1.3rem;">Deseja participar?</h3>
                <p class="mx-auto mb-4" style="max-width: 600px;">Se é advogado inscrito e deseja contribuir com o seu conhecimento técnico, contacte-nos para saber como integrar uma das nossas comissões.</p>
                <a href="contacto.php" class="btn btn-cta-gold btn-lg px-5">Contactar Relações Externas</a>
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

