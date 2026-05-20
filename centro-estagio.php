<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Centro de Estágio e Formação";
$meta_description = "Centro de Estágio da OAGB — requisitos de inscrição, fases do estágio, papel do patrono e formação contínua para advogados na Guiné-Bissau.";
$header_image = 'uploads/justice-symbol-legal-law.jpg';

try {
    $stmt = $pdo->prepare("SELECT * FROM conteudos_paginas WHERE pagina = 'estagio' AND status = 'ativo' ORDER BY ordem ASC");
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
        .sidebar-widget { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; position: sticky; top: 120px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .sidebar-link { display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; margin-bottom: 10px; text-decoration: none !important; color: #555; font-weight: 600; transition: all 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: var(--primary-gold); width: 20px; text-align: center; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }
        .info-card { background: #fff; border: 1px solid #f0ece4; border-radius: 20px; padding: 35px; margin-bottom: 25px; transition: .3s; }
        .info-card:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.04); transform: translateY(-2px); }
        .info-icon { width: 55px; height: 55px; background: rgba(77,28,33,0.08); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--primary-maroon); margin-bottom: 18px; }
        .info-card h3 { font-family: 'Libre Baskerville', serif; font-weight: 700; color: var(--primary-maroon); font-size: 1.15rem; margin-bottom: 15px; }
        .info-card p, .info-card li { font-size: 0.92rem; line-height: 1.7; color: #555; }
        .info-card ul, .info-card ol { padding-left: 20px; }
        .info-card li { margin-bottom: 6px; }
        .info-card strong { color: var(--primary-maroon); }
        .cta-banner { background: linear-gradient(135deg, var(--primary-maroon), #3a1218); border-radius: 20px; padding: 40px; color: #fff; text-align: center; margin-top: 20px; }
        .cta-banner h4 { font-family: 'Libre Baskerville', serif; margin-bottom: 15px; }
        .cta-banner .btn { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 10px 30px; font-weight: 600; border: none; transition: .3s; }
        .cta-banner .btn:hover { background: #fff; color: var(--primary-maroon); }
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
                        <a href="index.php">Início</a><span class="bc-sep"></span><a href="#">A Ordem</a><span class="bc-sep"></span><span class="bc-active"><?php echo $page_title; ?></span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $mobile_breadcrumbs = [['label'=>'Início','url'=>'index.php'],['label'=>'A Ordem','url'=>'#'],['label'=>$page_title,'active'=>true]]; include 'includes/mobile-header-subpage.php'; ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row g-5">
                <div class="col-lg-8">
                    <?php if (count($seccoes) > 0): ?>
                        <?php foreach ($seccoes as $sec): ?>
                            <div class="info-card wow fadeInUp" id="<?php echo htmlspecialchars($sec->secao); ?>">
                                <div class="info-icon"><i class="<?php echo htmlspecialchars($sec->icone); ?>"></i></div>
                                <h3><?php echo htmlspecialchars($sec->titulo); ?></h3>
                                <div><?php echo $sec->conteudo; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5"><i class="fas fa-graduation-cap" style="font-size:3rem;color:#dcd8cf;"></i><h5 style="color:var(--primary-maroon);font-family:'Libre Baskerville';">Conteúdo em preparação</h5></div>
                    <?php endif; ?>

                    <div class="cta-banner wow fadeInUp">
                        <h4>Quer inscrever-se no Estágio?</h4>
                        <p class="mb-4 opacity-75">Consulte os requisitos e faça o download do formulário de candidatura.</p>
                        <a href="contacto.php" class="btn"><i class="fas fa-envelope me-2"></i>Contactar o Centro de Estágio</a>
                    </div>
                </div>
                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm">
                        <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px; display: inline-block;">A Ordem</h5>
                        <div class="mt-3">
                            <a href="ordem-dos-advogados.php" class="sidebar-link"><i class="fas fa-landmark"></i> Apresentação</a>
                            <a href="orgaos-sociais.php" class="sidebar-link"><i class="fas fa-sitemap"></i> Órgãos Sociais</a>
                            <a href="deontologia-etica.php" class="sidebar-link"><i class="fas fa-balance-scale"></i> Deontologia e Ética</a>
                            <a href="centro-estagio.php" class="sidebar-link active"><i class="fas fa-graduation-cap"></i> Centro de Estágio</a>
                            <a href="cooperacao-institucional.php" class="sidebar-link"><i class="fas fa-handshake"></i> Cooperação</a>
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
