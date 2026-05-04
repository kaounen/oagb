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
    $config = $pdo->query("SELECT * FROM orgaos_config WHERE id = 1")->fetch();
    $groups = $pdo->query("SELECT * FROM orgaos_diretivos ORDER BY id ASC")->fetchAll();
} catch (Exception $e) {
    error_log("Erro: " . $e->getMessage());
}

$page_title = "Órgãos Sociais"; 
$meta_description = "Estrutura organizacional e órgãos diretivos da Ordem dos Advogados da Guiné-Bissau";
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
        .quick-links a i { line-height: 1; vertical-align: middle; }
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

        /* === ÓRGÃOS CARDS === */
        .group-header { border-left: 5px solid var(--primary-gold); padding-left: 15px; margin-bottom: 30px; font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; }
        .member-card { transition: all 0.3s; border: 1px solid #f0ece4; border-radius: 16px; overflow: hidden; height: 100%; background: #fff; }
        .member-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(177, 162, 118, 0.12) !important; }
        .member-img { width: 100%; height: 280px; object-fit: cover; }
        .member-info { padding: 24px; background: #fff; }
        .member-role { color: var(--primary-gold); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; margin-bottom: 8px; display: block; }
        .member-name { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; }
        .member-mandato { font-size: 0.75rem; color: #888; margin-top: 10px; }

        .organogram-container { background: #fff; border-radius: 20px; padding: 40px; margin-bottom: 40px; border: 1px solid #f0ece4; overflow-x: auto; }
        .btn-toggle-view { border-radius: 50px; padding: 10px 30px; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px; transition: .3s; }
        
        /* Tree Adjustments */
        /* === ADVANCED ORGANOGRAM CSS === */
        .tree { position: relative; padding: 20px 0; }
        .tree ul {
            padding-top: 20px; position: relative;
            transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s;
            display: flex; justify-content: center;
        }
        .tree li {
            text-align: center; list-style-type: none; position: relative;
            padding: 20px 10px 0 10px;
            transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s;
        }

        /* We will use ::before and ::after to draw the connector lines */
        .tree li::before, .tree li::after {
            content: ''; position: absolute; top: 0; right: 50%;
            border-top: 1px solid #ccc; width: 50%; height: 20px;
        }
        .tree li::after {
            right: auto; left: 50%; border-left: 1px solid #ccc;
        }

        /* We need to remove left-right connectors from elements without any siblings */
        .tree li:only-child::after, .tree li:only-child::before { display: none; }
        
        /* Remove "outer" connectors from single child */
        .tree li:only-child { padding-top: 0; }
        
        /* Remove left connector from first child and right connector from last child */
        .tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
        
        /* Adding back the vertical connector to last nodes */
        .tree li:last-child::before { border-right: 1px solid #ccc; border-radius: 0 5px 0 0; }
        .tree li:first-child::after { border-radius: 5px 0 0 0; }

        /* Time to draw vertical connectors from parents */
        .tree ul ul::before {
            content: ''; position: absolute; top: 0; left: 50%;
            border-left: 1px solid #ccc; width: 0; height: 20px;
        }

        .tree li .node {
            border: 1px solid #f0ece4; padding: 12px 18px; text-decoration: none; color: #444; 
            font-size: 0.8rem; display: inline-block; border-radius: 12px; transition: all 0.3s; 
            background: #fff; width: 180px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            position: relative; z-index: 10;
        }
        .tree li .node:hover { 
            background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); 
            transform: translateY(-5px); 
        }
        .tree li .node .n-name { font-weight: 700; margin-bottom: 2px; font-family: 'Libre Baskerville', serif; font-size: 0.85rem; }
        .tree li .node .n-role { font-size: 11px; opacity: 0.7; font-weight: 600; text-transform: uppercase; color: var(--primary-gold); }
        /* === RESPONSIVE ORGANOGRAM (MOBILE VERTICAL TREE) === */
        @media screen and (max-width: 767px) {
            .organogram-container { padding: 20px 5px; overflow-x: hidden !important; }
            .tree ul { display: block; padding-top: 0; }
            .tree li { display: block; padding: 12px 0 0 30px !important; text-align: left; position: relative; }
            .tree li::before, .tree li::after { display: none !important; }
            
            /* Vertical connectors with better distance */
            .tree li::before {
                content: ''; position: absolute; top: -12px; left: 10px;
                width: 1px; height: 100%; background: #ccc; display: block !important;
            }
            .tree li:last-child::before { height: 32px; }
            
            /* Horizontal connector to the node adjusted */
            .tree li .node::before {
                content: ''; position: absolute; top: 50%; left: -20px;
                width: 20px; height: 1px; background: #ccc;
            }
            
            .tree ul ul::before { display: none !important; }
            .tree li .node { width: calc(100% - 10px); max-width: 250px; text-align: left; padding: 10px 14px; }
            .tree li:only-child { padding-top: 12px !important; }
            .tree li:first-child::before { top: 0; }
        }
        
        /* Desktop Scroll (Only if needed) */
        @media screen and (min-width: 768px) {
            .organogram-container::-webkit-scrollbar { height: 8px; }
            .organogram-container::-webkit-scrollbar-thumb { background: var(--primary-gold); border-radius: 10px; }
            .organogram-container::-webkit-scrollbar-track { background: #f0f0f0; }
        }
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
                        <span class="bc-active">Órgãos Sociais</span>
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
        ['label' => 'Órgãos Sociais', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <!-- ======= MAIN CONTENT ======= -->


    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            
            <div class="text-center mb-5">
                <span class="section-label">Governação</span>
                <h2 class="section-heading" style="font-size: 1.3rem;">Estrutura dos Órgãos Sociais</h2>
                
                <div class="btn-group shadow-sm p-1 bg-white rounded-pill mt-4 border">
                    <button class="btn btn-primary rounded-pill px-4 btn-toggle-view" id="view-list-btn" onclick="toggleView('list')"><i class="fas fa-users me-2"></i> Lista de Membros</button>
                    <button class="btn btn-light rounded-pill px-4 btn-toggle-view" id="view-chart-btn" onclick="toggleView('chart')"><i class="fas fa-sitemap me-2"></i> Organograma</button>
                </div>
            </div>

            <!-- View: Members List -->
            <div id="members-list-view" class="animated fadeIn">
                <?php if(!empty($groups)): foreach($groups as $g): ?>
                    <?php
                        $stmt = $pdo->prepare("SELECT * FROM orgaos_sociais WHERE orgao_diretivo_id = ? AND ativo = 1 ORDER BY ordem_exibicao ASC");
                        $stmt->execute([$g->id]);
                        $members = $stmt->fetchAll();
                        if (count($members) > 0):
                    ?>
                        <div class="mb-5">
                            <h3 class="group-header" style="font-size: 1.1rem;"><?php echo htmlspecialchars($g->nome); ?></h3>
                            <div class="row g-4">
                                <?php foreach($members as $m): ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="member-card shadow-sm">
                                            <div class="position-relative overflow-hidden" style="height: 280px;">
                                                <img src="<?php echo $m->foto ? 'uploads/orgaos/'.$m->foto : 'img/user-default.png'; ?>" class="member-img w-100 h-100" style="object-fit:cover;" alt="<?php echo $m->nome; ?>">
                                            </div>
                                            <div class="member-info text-center border-top">
                                                <span class="member-role"><?php echo oagb_fix_encoding($m->cargo); ?></span>
                                                <h5 class="member-name"><?php echo oagb_fix_encoding($m->nome); ?></h5>
                                                <?php if($m->mandato_inicio): ?>
                                                    <div class="member-mandato">Mandato: <?php echo date('Y', strtotime($m->mandato_inicio)); ?> - <?php echo $m->mandato_fim ? date('Y', strtotime($m->mandato_fim)) : 'Presente'; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            </div>

            <!-- View: Organogram -->
            <div id="organogram-view" class="d-none animated fadeIn">
                <div class="organogram-container text-center">
                    <?php if($config && $config->modo_exibicao == 'imagem' && $config->organograma_path): ?>
                        <img src="uploads/orgaos/<?php echo $config->organograma_path; ?>" class="img-fluid rounded-3 shadow-sm border" alt="Organograma OAGB">
                    <?php elseif($config && $config->modo_exibicao == 'pdf' && $config->organograma_pdf_path): ?>
                        <div class="py-5 text-center">
                            <div class="mb-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(77,28,33,0.06); border-radius: 50%; color: var(--primary-maroon);">
                                <i class="far fa-file-pdf fa-2x"></i>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif;">Organograma em PDF</h4>
                            <p class="text-muted mx-auto mb-4" style="max-width: 500px; font-size: 0.9rem;">Consulte a estrutura hierárquica completa da Ordem dos Advogados da Guiné-Bissau em formato PDF.</p>
                            <a href="uploads/orgaos/<?php echo $config->organograma_pdf_path; ?>" target="_blank" class="btn rounded-pill px-5 py-3 shadow-sm" style="background: var(--primary-maroon); color: #fff; font-weight:600;">
                                <i class="fas fa-download me-2"></i>Descarregar PDF
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Auto Generated Tree (Simplified for premium look) -->
                        <div class="tree py-4">
                            <?php
                            function renderNode($pdo, $superior_id = null) {
                                $stmt = $pdo->prepare("SELECT id, nome, cargo FROM orgaos_sociais WHERE superior_id " . ($superior_id === null ? "IS NULL" : "= ?") . " AND ativo = 1 ORDER BY ordem_exibicao ASC");
                                $stmt->execute($superior_id === null ? [] : [$superior_id]);
                                $nodes = $stmt->fetchAll();
                                
                                if (count($nodes) > 0) {
                                    echo "<ul>";
                                    foreach($nodes as $node) {
                                        $nome = oagb_fix_encoding($node->nome);
                                        $cargo = oagb_fix_encoding($node->cargo);
                                        echo "<li>";
                                        echo "<div class='node shadow-sm'><div class='n-name'>{$nome}</div><div class='n-role'>{$cargo}</div></div>";
                                        renderNode($pdo, $node->id);
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                }
                            }
                            renderNode($pdo);
                            ?>
                        </div>
                    <?php endif; ?>
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
    <script>
        function toggleView(view) {
            const listBtn = document.getElementById('view-list-btn');
            const chartBtn = document.getElementById('view-chart-btn');
            const listView = document.getElementById('members-list-view');
            const chartView = document.getElementById('organogram-view');

            if (view === 'list') {
                listView.classList.remove('d-none');
                chartView.classList.add('d-none');
                listBtn.classList.replace('btn-light', 'btn-primary');
                chartBtn.classList.replace('btn-primary', 'btn-light');
            } else {
                listView.classList.add('d-none');
                chartView.classList.remove('d-none');
                chartBtn.classList.replace('btn-light', 'btn-primary');
                listBtn.classList.replace('btn-primary', 'btn-light');
            }
        }
    </script>
</body>
</html>

