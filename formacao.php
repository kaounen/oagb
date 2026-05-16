<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

try {
    // Fetch all active courses
    $stmt = $pdo->prepare("SELECT * FROM gestao_cursos WHERE ativa = 1 ORDER BY data_inicio DESC");
    $stmt->execute();
    $cursos = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Erro ao buscar cursos: " . $e->getMessage());
    $cursos = [];
}

$page_title = "Formação & Cursos";
$meta_description = "Consulte os cursos de formação, seminários e especializações oferecidos pela Ordem dos Advogados da Guiné-Bissau.";
$header_image = 'uploads/lady-justice-holding-scales-sword.jpg';
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
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
            --dark-navy: #111923;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }

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

        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin: 15px auto 0; }
        
        .course-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f0ece4;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(177, 162, 118, 0.15);
            border-color: var(--primary-gold);
        }
        .course-header {
            background: var(--primary-maroon);
            padding: 30px;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .course-icon {
            font-size: 2.5rem;
            color: var(--primary-gold);
            margin-bottom: 10px;
        }
        .course-body {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .course-title {
            font-family: 'Libre Baskerville', serif;
            color: var(--primary-maroon);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }
        .course-info {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .course-info i {
            color: var(--primary-gold);
            width: 16px;
        }
        .course-desc {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.6;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .course-footer {
            padding: 20px 25px;
            border-top: 1px solid #f0ece4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }
        .course-price {
            font-weight: 700;
            color: var(--primary-maroon);
            font-size: 1.1rem;
        }
        .btn-course {
            background: var(--primary-gold);
            color: #fff;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: .3s;
            border: none;
            text-decoration: none;
        }
        .btn-course:hover {
            background: var(--primary-maroon);
            color: #fff;
            transform: scale(1.05);
        }

        @media (max-width: 991.98px) {
            .section-heading { font-size: 1.8rem; }
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
                        <a href="advogados-inscritos.php">Advogados</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Formação & Cursos</span>
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
        ['label' => 'Advogados', 'url' => 'advogados-inscritos.php'],
        ['label' => 'Formação & Cursos', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #fcfbf8;">
        <div class="container py-lg-4">
            <div class="text-center mx-auto mb-5 wow fadeInUp" style="max-width: 700px;">
                <span class="section-label">Centro de Capacitação Profissional</span>
                <h1 class="section-heading">Gestão Acadêmica & Cursos</h1>
                <p class="text-muted">A Ordem dos Advogados da Guiné-Bissau promove a excelência jurídica através de seminários, formações de estágio e cursos de especialização contínua.</p>
            </div>

            <?php if (empty($cursos)): ?>
                <div class="text-center py-5">
                    <div class="bg-white p-5 rounded-4 border shadow-sm d-inline-block">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-3 opacity-25"></i>
                        <h4 class="text-muted">Nenhum curso disponível de momento.</h4>
                        <p class="mb-0">Fique atento às nossas comunicações para futuras aberturas.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($cursos as $c): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="course-card">
                                <div class="course-header">
                                    <div class="course-icon"><i class="fas fa-graduation-cap"></i></div>
                                    <div class="small text-uppercase fw-bold"><?php echo htmlspecialchars($c->titulo); ?></div>
                                </div>
                                <div class="course-body">
                                    <h4 class="course-title d-none"><?php echo htmlspecialchars($c->titulo); ?></h4>
                                    
                                    <div class="course-info mt-2">
                                        <i class="far fa-calendar-alt"></i>
                                        <span><?php echo date('d/m/Y', strtotime($c->data_inicio)); ?> <?php echo $c->data_fim ? ' — ' . date('d/m/Y', strtotime($c->data_fim)) : ''; ?></span>
                                    </div>
                                    
                                    <?php if ($c->vagas): ?>
                                    <div class="course-info">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $c->vagas; ?> Vagas Disponíveis</span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="course-desc">
                                        <?php echo nl2br(htmlspecialchars(substr($c->descricao, 0, 180))); ?>...
                                    </div>
                                </div>
                                <div class="course-footer">
                                    <div class="course-price">
                                        <?php echo ($c->preco > 0) ? number_format($c->preco, 0, ',', '.') . ' XOF' : 'Gratuito'; ?>
                                    </div>
                                    <a href="inscrever-curso.php?id=<?php echo $c->id; ?>" class="btn-course text-uppercase">Inscrever-se</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
