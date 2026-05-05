<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Legislação Nacional";
$meta_description = "Consulte a legislação fundamental da Guiné-Bissau — Constituição, Código Penal, Código Civil, Lei de Terras, Estatutos da OAGB e mais.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

$cat_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';

try {
    if ($cat_filter) {
        $stmt = $pdo->prepare("SELECT * FROM legislacao_nacional WHERE status = 'ativo' AND categoria = ? ORDER BY ordem ASC");
        $stmt->execute([$cat_filter]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM legislacao_nacional WHERE status = 'ativo' ORDER BY ordem ASC");
        $stmt->execute();
    }
    $leis = $stmt->fetchAll();
    
    $stmt2 = $pdo->prepare("SELECT DISTINCT categoria FROM legislacao_nacional WHERE status = 'ativo' ORDER BY categoria ASC");
    $stmt2->execute();
    $categorias = $stmt2->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $leis = [];
    $categorias = [];
}
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
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 30px; border-left: 5px solid var(--primary-gold); padding-left: 20px; }
        .sidebar-widget { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; position: sticky; top: 120px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .sidebar-link { display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; margin-bottom: 10px; text-decoration: none !important; color: #555; font-weight: 600; transition: all 0.3s; border: 1px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: var(--primary-gold); width: 20px; text-align: center; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }
        .lei-card { background: #fff; border: 1px solid #f0ece4; border-radius: 16px; padding: 25px; margin-bottom: 20px; transition: .3s; display: flex; gap: 20px; align-items: flex-start; }
        .lei-card:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,0,0,0.04); }
        .lei-icon { width: 55px; height: 55px; background: rgba(77, 28, 33, 0.08); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary-maroon); flex-shrink: 0; }
        .lei-card h4 { font-family: 'Libre Baskerville', serif; font-weight: 700; color: var(--primary-maroon); font-size: 1.05rem; margin-bottom: 5px; }
        .lei-diploma { font-size: 0.78rem; color: var(--primary-gold); font-weight: 700; margin-bottom: 8px; }
        .cat-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; background: rgba(177,162,118,0.12); color: var(--primary-maroon); margin-bottom: 10px; }
        .filter-btn { padding: 6px 16px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #e0dcd2; background: #fff; color: #555; transition: .3s; margin: 3px; text-decoration: none !important; }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); }
        .btn-download { background: var(--primary-maroon); color: #fff; border-radius: 50px; padding: 6px 16px; font-size: 0.8rem; font-weight: 600; transition: .3s; border: none; text-decoration:none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-download:hover { background: var(--primary-gold); color: #fff; }
        .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px dashed #dcd8cf; }
        .empty-state i { font-size: 3rem; color: #dcd8cf; margin-bottom: 20px; }
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
                    <p class="lead mb-4" style="color: #444;">Legislação fundamental da República da Guiné-Bissau, organizada por áreas de referência.</p>
                    
                    <div class="mb-4 d-flex flex-wrap">
                        <a href="legislacao-nacional.php" class="filter-btn <?php echo !$cat_filter ? 'active' : ''; ?>">Todas</a>
                        <?php foreach ($categorias as $cat): ?>
                            <a href="?cat=<?php echo urlencode($cat); ?>" class="filter-btn <?php echo $cat_filter === $cat ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat); ?></a>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($leis) > 0): ?>
                        <?php foreach ($leis as $lei): ?>
                            <div class="lei-card wow fadeInUp">
                                <div class="lei-icon"><i class="fas fa-scroll"></i></div>
                                <div style="flex:1;">
                                    <span class="cat-badge"><?php echo htmlspecialchars($lei->categoria); ?></span>
                                    <h4><?php echo htmlspecialchars($lei->titulo); ?></h4>
                                    <?php if($lei->diploma_legal): ?><div class="lei-diploma"><?php echo htmlspecialchars($lei->diploma_legal); ?></div><?php endif; ?>
                                    <?php if($lei->resumo): ?><p class="text-muted mb-2" style="font-size:0.88rem;"><?php echo htmlspecialchars($lei->resumo); ?></p><?php endif; ?>
                                    <?php if($lei->ficheiro_pdf): ?>
                                        <a href="uploads/legislacao/<?php echo htmlspecialchars($lei->ficheiro_pdf); ?>" target="_blank" class="btn-download"><i class="fas fa-download"></i> PDF</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state"><i class="fas fa-gavel"></i><h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Sem diplomas nesta categoria</h5></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 120px;">
                        <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px; display: inline-block;">Publicações</h5>
                        <div class="mt-3">
                            <a href="publicacoes.php" class="sidebar-link"><i class="fas fa-book"></i> Publicações</a>
                            <a href="revista-oagb.php" class="sidebar-link"><i class="far fa-newspaper"></i> Revista da OAGB</a>
                            <a href="legislacao-nacional.php" class="sidebar-link active"><i class="fas fa-gavel"></i> Legislação Nacional</a>
                            <a href="legislacao-internacional.php" class="sidebar-link"><i class="fas fa-globe-africa"></i> Legislação Internacional</a>
                            <a href="glossario-juridico.php" class="sidebar-link"><i class="fas fa-book-open"></i> Glossário Jurídico</a>
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
