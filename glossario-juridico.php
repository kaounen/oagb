<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Glossário Jurídico";
$meta_description = "Glossário de termos jurídicos da OAGB — ferramenta de combate à iliteracia jurídica, com definições em linguagem acessível.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

$letra_filter = isset($_GET['letra']) ? strtoupper(trim($_GET['letra'])) : '';
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if ($search_q) {
        $stmt = $pdo->prepare("SELECT * FROM glossario_juridico WHERE status = 'ativo' AND (termo LIKE ? OR definicao LIKE ?) ORDER BY termo ASC");
        $stmt->execute(["%$search_q%", "%$search_q%"]);
    } elseif ($letra_filter) {
        $stmt = $pdo->prepare("SELECT * FROM glossario_juridico WHERE status = 'ativo' AND letra = ? ORDER BY termo ASC");
        $stmt->execute([$letra_filter]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM glossario_juridico WHERE status = 'ativo' ORDER BY letra ASC, termo ASC");
        $stmt->execute();
    }
    $termos = $stmt->fetchAll();
    
    $stmt2 = $pdo->prepare("SELECT DISTINCT letra FROM glossario_juridico WHERE status = 'ativo' ORDER BY letra ASC");
    $stmt2->execute();
    $letras_disponiveis = $stmt2->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) { $termos = []; $letras_disponiveis = []; }

// Agrupar por letra
$por_letra = [];
foreach ($termos as $t) { $por_letra[$t->letra][] = $t; }
$alfabeto = range('A', 'Z');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        .bg-header { background-attachment: scroll !important; }
        html, body { overflow-x: hidden !important; width: 100%; margin: 0; padding: 0; }
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }
        .quick-links a { width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; z-index: 1045 !important; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }
        .sidebar-widget { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; position: sticky; top: 120px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .sidebar-link { display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; margin-bottom: 10px; text-decoration: none !important; color: #555; font-weight: 600; transition: all 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: var(--primary-gold); width: 20px; text-align: center; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }

        /* Glossário styles */
        .alphabet-bar { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 30px; }
        .alpha-btn { width: 38px; height: 38px; border-radius: 10px; border: 1px solid #e0dcd2; background: #fff; color: #555; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; text-decoration: none !important; transition: .3s; }
        .alpha-btn:hover, .alpha-btn.active { background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); }
        .alpha-btn.disabled { opacity: 0.3; pointer-events: none; }
        .letra-heading { font-family: 'Libre Baskerville', serif; color: #fff; font-weight: 700; font-size: 1.8rem; width: 55px; height: 55px; background: var(--primary-maroon); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .termo-card { background: #fff; border: 1px solid #f0ece4; border-radius: 14px; padding: 22px 25px; margin-bottom: 14px; transition: .3s; }
        .termo-card:hover { border-color: var(--primary-gold); box-shadow: 0 8px 25px rgba(0,0,0,0.03); }
        .termo-word { font-family: 'Libre Baskerville', serif; font-weight: 700; color: var(--primary-maroon); font-size: 1.05rem; margin-bottom: 6px; }
        .termo-tipo { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.65rem; font-weight: 700; margin-left: 8px; }
        .tipo-geral { background: rgba(177,162,118,0.15); color: #8a7a4e; }
        .tipo-latinismo { background: rgba(77,28,33,0.1); color: var(--primary-maroon); }
        .tipo-expressao { background: rgba(44,110,73,0.1); color: #2c6e49; }
        .search-box { border: 2px solid #e0dcd2; border-radius: 14px; padding: 12px 20px; font-size: 0.9rem; width: 100%; transition: .3s; background: #fff; }
        .search-box:focus { outline: none; border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(177,162,118,0.15); }
        .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px dashed #dcd8cf; }
    </style>
</head>
<body>
<div style="overflow-x: hidden; width: 100%; position: relative;">
    <?php include 'includes/topbar.php'; ?>
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a><span class="bc-sep"></span><a href="#">Público</a><span class="bc-sep"></span><span class="bc-active"><?php echo $page_title; ?></span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $mobile_breadcrumbs = [['label'=>'Início','url'=>'index.php'],['label'=>'Público','url'=>'#'],['label'=>$page_title,'active'=>true]]; include 'includes/mobile-header-subpage.php'; ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row g-5">
                <div class="col-lg-8">
                    <p class="lead mb-2" style="color: #444;">Glossário da OAGB — ferramenta de combate à iliteracia jurídica.</p>
                    <p class="small text-muted mb-4">Termos e expressões jurídicas explicados em linguagem acessível ao cidadão.</p>

                    <!-- Search -->
                    <form method="GET" class="mb-4">
                        <div class="position-relative">
                            <input type="text" name="q" class="search-box" placeholder="Pesquisar termo..." value="<?php echo htmlspecialchars($search_q); ?>">
                            <i class="fas fa-search position-absolute" style="right: 18px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>
                        </div>
                    </form>

                    <!-- Alphabet bar -->
                    <div class="alphabet-bar">
                        <?php foreach ($alfabeto as $l): ?>
                            <a href="?letra=<?php echo $l; ?>" class="alpha-btn <?php echo $letra_filter === $l ? 'active' : ''; ?> <?php echo !in_array($l, $letras_disponiveis) ? 'disabled' : ''; ?>"><?php echo $l; ?></a>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($por_letra) > 0): ?>
                        <?php foreach ($por_letra as $l => $termos_l): ?>
                            <div class="letra-heading" id="letra-<?php echo $l; ?>"><?php echo $l; ?></div>
                            <?php foreach ($termos_l as $t): ?>
                                <div class="termo-card wow fadeInUp">
                                    <div class="termo-word">
                                        <?php echo htmlspecialchars($t->termo); ?>
                                        <?php 
                                        $tipo_class = 'tipo-geral';
                                        if ($t->categoria === 'Latinismo') $tipo_class = 'tipo-latinismo';
                                        if ($t->categoria === 'Expressao') $tipo_class = 'tipo-expressao';
                                        ?>
                                        <span class="termo-tipo <?php echo $tipo_class; ?>"><?php echo htmlspecialchars($t->categoria); ?></span>
                                    </div>
                                    <p class="text-muted mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($t->definicao); ?></p>
                                    <?php if($t->exemplo_uso): ?>
                                        <p class="mb-0" style="font-size: 0.82rem; color: var(--primary-gold); font-style: italic;"><i class="fas fa-quote-left me-1" style="font-size: 0.65rem;"></i> <?php echo htmlspecialchars($t->exemplo_uso); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open" style="font-size:3rem;color:#dcd8cf;"></i>
                            <h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Nenhum termo encontrado</h5>
                            <p class="text-muted mb-0">Tente outra letra ou pesquise por palavra-chave.</p>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <p class="small text-muted"><strong><?php echo count($termos); ?></strong> termos no glossário</p>
                    </div>
                </div>
                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 120px;">
                        <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px; display: inline-block;">Publicações</h5>
                        <div class="mt-3">
                            <a href="publicacoes.php" class="sidebar-link"><i class="fas fa-book"></i> Publicações</a>
                            <a href="revista-oagb.php" class="sidebar-link"><i class="far fa-newspaper"></i> Revista da OAGB</a>
                            <a href="legislacao-nacional.php" class="sidebar-link"><i class="fas fa-gavel"></i> Legislação Nacional</a>
                            <a href="legislacao-internacional.php" class="sidebar-link"><i class="fas fa-globe-africa"></i> Legislação Internacional</a>
                            <a href="glossario-juridico.php" class="sidebar-link active"><i class="fas fa-book-open"></i> Glossário Jurídico</a>
                            <a href="biblioteca-oagb.php" class="sidebar-link"><i class="fas fa-university"></i> Biblioteca OAGB</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top shadow-lg" style="background-color: var(--primary-maroon); border-color: var(--primary-maroon);"><i class="bi bi-arrow-up text-white"></i></a>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script><script src="lib/easing/easing.min.js"></script><script src="lib/waypoints/waypoints.min.js"></script>
    <script src="js/main.js"></script>
</div>
</body>
</html>
