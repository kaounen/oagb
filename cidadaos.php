<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Cidadãos";
$meta_description = "Informação para cidadãos — acesso ao direito, direitos fundamentais, como encontrar advogado e glossário jurídico da OAGB.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

try {
    $stmt = $pdo->prepare("SELECT * FROM info_cidadaos WHERE status = 'ativo' ORDER BY ordem ASC");
    $stmt->execute();
    $seccoes = $stmt->fetchAll();
} catch (Exception $e) { $seccoes = []; }
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
        .info-card { background: #fff; border: 1px solid #f0ece4; border-radius: 20px; padding: 35px; margin-bottom: 25px; transition: .3s; }
        .info-card:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.04); transform: translateY(-2px); }
        .info-icon { width: 60px; height: 60px; background: rgba(77,28,33,0.08); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary-maroon); margin-bottom: 20px; }
        .info-card h3 { font-family: 'Libre Baskerville', serif; font-weight: 700; color: var(--primary-maroon); font-size: 1.2rem; margin-bottom: 15px; }
        .info-card p, .info-card a { font-size: 0.92rem; line-height: 1.7; }
        .info-card a { color: var(--primary-gold); font-weight: 600; }
        .info-card a:hover { color: var(--primary-maroon); }
        .cta-banner { background: linear-gradient(135deg, var(--primary-maroon), #3a1218); border-radius: 20px; padding: 40px; color: #fff; text-align: center; }
        .cta-banner h4 { font-family: 'Libre Baskerville', serif; margin-bottom: 15px; color: #fff; }
        .cta-banner .btn { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 10px 30px; font-weight: 600; border: none; transition: .3s; }
        .cta-banner .btn:hover { background: #fff; color: var(--primary-maroon); }
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
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $mobile_breadcrumbs = [['label'=>'Início','url'=>'index.php'],['label'=>'Público','url'=>'#'],['label'=>$page_title,'active'=>true]]; include 'includes/mobile-header-subpage.php'; ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <span style="font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold);">Informação ao Cidadão</span>
                        <h2 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2rem; margin-top: 10px;">O Direito ao seu alcance</h2>
                        <p class="text-muted mt-3" style="max-width: 600px; margin: 0 auto;">A OAGB trabalha para que todos os cidadãos tenham acesso à informação jurídica e à justiça.</p>
                    </div>

                    <?php if (count($seccoes) > 0): ?>
                        <?php foreach ($seccoes as $sec): ?>
                            <div class="info-card wow fadeInUp" id="<?php echo htmlspecialchars($sec->slug); ?>">
                                <div class="info-icon"><i class="<?php echo htmlspecialchars($sec->icone); ?>"></i></div>
                                <h3><?php echo htmlspecialchars($sec->titulo); ?></h3>
                                <div class="text-muted"><?php echo $sec->conteudo; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users" style="font-size:3rem;color:#dcd8cf;"></i>
                            <h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Conteúdo em preparação</h5>
                        </div>
                    <?php endif; ?>

                    <!-- CTA Banner -->
                    <div class="cta-banner mt-5 wow fadeInUp">
                        <h4>Precisa de um Advogado?</h4>
                        <p class="mb-4 opacity-75">Pesquise a lista de advogados inscritos e em exercício na Ordem dos Advogados da Guiné-Bissau.</p>
                        <a href="pesquisa-advogados.php" class="btn"><i class="fas fa-search me-2"></i>Pesquisar Advogados</a>
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
