<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$page_title = "Publicações";
$meta_description = "Publicações, manuais e documentação produzida pela Ordem dos Advogados da Guiné-Bissau.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

try {
    $stmt = $pdo->prepare("SELECT * FROM documentos_publicos WHERE tipo = 'publicacao' AND ativo = 1 ORDER BY data_documento DESC");
    $stmt->execute();
    $documentos = $stmt->fetchAll();
} catch (Exception $e) {
    $documentos = [];
}

// Group documents by year
$documentos_agrupados = [];
foreach ($documentos as $doc) {
    $ano = !empty($doc->data_documento) ? date('Y', strtotime($doc->data_documento)) : 'Outros';
    $documentos_agrupados[$ano][] = $doc;
}
krsort($documentos_agrupados);
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
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        .bg-header { background-attachment: scroll !important; }

        html, body { overflow-x: hidden !important; width: 100%; margin: 0; padding: 0; }
        
        /* === SUBPAGE BREADCRUMB BAR === */
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.85rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.85rem !important; opacity: 1 !important; }
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
            .mobile-breadcrumb-bar .bc-active { font-weight: 600; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { 
                border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; 
            }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }

        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 30px; border-left: 5px solid var(--primary-gold); padding-left: 20px; }

        .sidebar-widget { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; position: sticky; top: 150px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .sidebar-link { display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; margin-bottom: 10px; text-decoration: none !important; color: #555; font-weight: 600; transition: all 0.3s; border: 1px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: var(--primary-gold); width: 20px; text-align: center; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }

        .doc-card { background: #fff; border: 1px solid #f0ece4; border-radius: 12px; padding: 25px; margin-bottom: 20px; transition: .3s; display: flex; align-items: flex-start; gap: 20px; }
        .doc-card:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,0,0,0.04); border-color: #e0dcd2; }
        .doc-icon { width: 60px; height: 60px; background: rgba(77, 28, 33, 0.08); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary-maroon); flex-shrink: 0; }
        .doc-content { flex: 1; }
        .doc-title { font-family: 'Libre Baskerville', serif; font-weight: 500; color: var(--primary-maroon); font-size: 1.15rem; margin-bottom: 8px; line-height: 1.3; }
        .doc-title a { color: var(--primary-maroon) !important; text-decoration: none; transition: color 0.3s; font-weight: 500; }
        .doc-title a:hover { color: var(--primary-gold) !important; }
        .doc-meta { font-size: 0.8rem; color: #888; margin-bottom: 15px; display: flex; gap: 15px; font-weight: 600;}
        .doc-meta span { display: flex; align-items: center; gap: 5px; }
        .btn-download { background: var(--primary-maroon); color: #fff; border-radius: 50px; padding: 8px 20px; font-size: 0.85rem; font-weight: 600; transition: .3s; border: none; text-decoration:none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-download:hover { background: var(--primary-gold); color: #fff; transform: translateY(-2px); }
        
        .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px dashed #dcd8cf; }
        .empty-state i { font-size: 3rem; color: #dcd8cf; margin-bottom: 20px; }
        .btn-contact-sidebar { border: 1px solid var(--primary-maroon); color: var(--primary-maroon); font-weight: 700; border-radius: 50px; transition: .3s; }
        .btn-contact-sidebar:hover { background: var(--primary-maroon); color: #fff !important; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(77, 28, 33, 0.2); }
    </style>
</head>
<body>
<div style="overflow-x: hidden; width: 100%; position: relative;">
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
                        <a href="#">Público</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo $page_title; ?></span>
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
        ['label' => 'Público', 'url' => '#'],
        ['label' => $page_title, 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row g-5">
                <div class="col-lg-8">

                    <p class="lead mb-5" style="color: #444;">Consulte as publicações, manuais e literatura jurídica chanceladas ou produzidas pela Ordem dos Advogados da Guiné-Bissau.</p>

                    <div class="doc-list-grouped">
                        <?php if (count($documentos_agrupados) > 0): ?>
                            <?php foreach ($documentos_agrupados as $ano => $docs_do_ano): ?>
                                <h3 class="year-heading my-4" style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.8rem; border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px; margin-top: 40px !important;"><?php echo $ano; ?></h3>
                                <div class="doc-list mb-5">
                                    <?php foreach ($docs_do_ano as $doc): ?>
                                        <?php 
                                            $file_link = (!empty($doc->arquivo) && $doc->arquivo != '#') ? 'uploads/documentos/' . htmlspecialchars($doc->arquivo) : '#';
                                        ?>
                                        <div class="doc-card wow fadeInUp">
                                            <div class="doc-icon"><i class="fas fa-book"></i></div>
                                            <div class="doc-content">
                                                <h4 class="doc-title">
                                                    <a href="<?php echo $file_link; ?>" target="_blank">
                                                        <?php echo htmlspecialchars($doc->titulo); ?>
                                                    </a>
                                                </h4>
                                                <div class="doc-meta">
                                                    <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($doc->data_documento)); ?></span>
                                                    <?php if($doc->numero_documento): ?>
                                                        <span><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($doc->numero_documento); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($doc->descricao): ?>
                                                    <p class="text-muted" style="font-size:0.9rem; margin-bottom:15px;"><?php echo nl2br(htmlspecialchars($doc->descricao)); ?></p>
                                                <?php endif; ?>
                                                <a href="<?php echo $file_link; ?>" class="btn-download" target="_blank">
                                                    <i class="fas fa-download"></i> Descarregar Publicação
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="far fa-folder-open"></i>
                                <h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Sem publicações no momento</h5>
                                <p class="text-muted mb-0">Esta secção encontra-se em atualização. Novos documentos serão disponibilizados brevemente.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4 mt-5 mt-lg-0 pt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 150px;">
                        <div class="mt-0">
                            <a href="pareceres-deliberacoes.php" class="sidebar-link"><i class="fas fa-gavel"></i> Pareceres e Deliberações</a>
                            <a href="comunicados.php" class="sidebar-link"><i class="fas fa-bullhorn"></i> Comunicados</a>
                            <a href="publicacoes.php" class="sidebar-link active"><i class="fas fa-book"></i> Publicações</a>
                            <a href="orcamento.php" class="sidebar-link"><i class="fas fa-chart-pie"></i> Orçamento</a>
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
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="js/main.js"></script>
</div>
</body>
</html>
