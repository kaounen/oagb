<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Legislação Internacional";
$meta_description = "Instrumentos jurídicos internacionais relevantes para a Guiné-Bissau — OHADA, CEDEAO, União Africana, CPLP e Direitos Humanos.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

$org_filter = isset($_GET['org']) ? trim($_GET['org']) : '';

try {
    if ($org_filter) {
        $stmt = $pdo->prepare("SELECT * FROM legislacao_internacional WHERE status = 'ativo' AND organizacao = ? ORDER BY ordem ASC");
        $stmt->execute([$org_filter]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM legislacao_internacional WHERE status = 'ativo' ORDER BY ordem ASC");
        $stmt->execute();
    }
    $instrumentos = $stmt->fetchAll();
    $stmt2 = $pdo->prepare("SELECT DISTINCT organizacao FROM legislacao_internacional WHERE status = 'ativo' ORDER BY organizacao ASC");
    $stmt2->execute();
    $organizacoes = $stmt2->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) { $instrumentos = []; $organizacoes = []; }

$org_colors = ['OHADA'=>'#2c6e49','CEDEAO'=>'#1a5276','União Africana'=>'#7d3c98','CPLP'=>'#b9770e','Direitos Humanos'=>'#c0392b'];
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
        .inst-card { background: #fff; border: 1px solid #f0ece4; border-radius: 16px; padding: 25px; margin-bottom: 20px; transition: .3s; border-left: 4px solid var(--primary-gold); }
        .inst-card:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,0,0,0.04); }
        .inst-card h4 { font-family: 'Libre Baskerville', serif; font-weight: 700; color: var(--primary-maroon); font-size: 1.05rem; margin-bottom: 8px; }
        .org-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .filter-btn { padding: 6px 16px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; border: 1px solid #e0dcd2; background: #fff; color: #555; transition: .3s; margin: 3px; text-decoration: none !important; }
        .filter-btn:hover, .filter-btn.active { background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); }
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
                    <p class="lead mb-4" style="color: #444;">Instrumentos jurídicos internacionais ratificados ou relevantes para a Guiné-Bissau.</p>
                    <div class="mb-4 d-flex flex-wrap">
                        <a href="legislacao-internacional.php" class="filter-btn <?php echo !$org_filter ? 'active' : ''; ?>">Todas</a>
                        <?php foreach ($organizacoes as $org): ?>
                            <a href="?org=<?php echo urlencode($org); ?>" class="filter-btn <?php echo $org_filter === $org ? 'active' : ''; ?>"><?php echo htmlspecialchars($org); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($instrumentos) > 0): ?>
                        <?php foreach ($instrumentos as $inst): ?>
                            <?php $bg = $org_colors[$inst->organizacao] ?? '#555'; ?>
                            <div class="inst-card wow fadeInUp" style="border-left-color: <?php echo $bg; ?>;">
                                <span class="org-badge" style="background: <?php echo $bg; ?>;"><?php echo htmlspecialchars($inst->organizacao); ?></span>
                                <h4><?php echo htmlspecialchars($inst->titulo); ?></h4>
                                <?php if($inst->data_adocao): ?><span class="text-muted" style="font-size:0.78rem;"><i class="far fa-calendar-alt me-1"></i>Adotado: <?php echo date('d/m/Y', strtotime($inst->data_adocao)); ?></span><?php endif; ?>
                                <?php if($inst->data_ratificacao_gb): ?><span class="ms-3 text-muted" style="font-size:0.78rem;"><i class="fas fa-check-circle me-1 text-success"></i>Ratificado GB: <?php echo date('d/m/Y', strtotime($inst->data_ratificacao_gb)); ?></span><?php endif; ?>
                                <?php if($inst->resumo): ?><p class="text-muted mt-2 mb-2" style="font-size:0.88rem;"><?php echo htmlspecialchars($inst->resumo); ?></p><?php endif; ?>
                                <?php if($inst->link_externo): ?><a href="<?php echo htmlspecialchars($inst->link_externo); ?>" target="_blank" class="small fw-bold" style="color: <?php echo $bg; ?>; text-decoration:none;"><i class="fas fa-external-link-alt me-1"></i>Consultar fonte oficial</a><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state"><i class="fas fa-globe-africa" style="font-size:3rem;color:#dcd8cf;"></i><h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Sem instrumentos nesta categoria</h5></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 120px;">
                        <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px; display: inline-block;">Publicações</h5>
                        <div class="mt-3">
                            <a href="publicacoes.php" class="sidebar-link"><i class="fas fa-book"></i> Publicações</a>
                            <a href="revista-oagb.php" class="sidebar-link"><i class="far fa-newspaper"></i> Revista da OAGB</a>
                            <a href="legislacao-nacional.php" class="sidebar-link"><i class="fas fa-gavel"></i> Legislação Nacional</a>
                            <a href="legislacao-internacional.php" class="sidebar-link active"><i class="fas fa-globe-africa"></i> Legislação Internacional</a>
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
