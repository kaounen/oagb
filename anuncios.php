<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$busca = isset($_GET['busca']) ? clean_input($_GET['busca']) : '';
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 9;
$offset = ($pagina - 1) * $por_pagina;

$where = ['ativo = 1'];
$params = [];

if ($ano > 2020 && $ano <= date('Y')) {
    $where[] = 'YEAR(data_inicio) = ?';
    $params[] = $ano;
}
if ($mes > 0 && $mes <= 12) {
    $where[] = 'MONTH(data_inicio) = ?';
    $params[] = $mes;
}
if (!empty($busca)) {
    $where[] = '(titulo LIKE ? OR descricao LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

$where_clause = implode(' AND ', $where);

if (!function_exists('oagb_resolve_media_path')) {
    function oagb_resolve_media_path($rawPath, $defaultPath) {
        if (empty($rawPath)) return $defaultPath;
        $normalized = str_replace('\\', '/', trim((string) $rawPath));
        $normalized = preg_replace('#\.\.+#', '', $normalized);
        if ($normalized === '') return $defaultPath;
        if (preg_match('#^https?://#i', $normalized)) return $normalized;
        if ($normalized[0] === '/') $normalized = ltrim($normalized, '/');
        if (strpos($normalized, 'uploads/') === 0 || strpos($normalized, 'img/') === 0) return $normalized;
        return 'uploads/' . $normalized;
    }
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anuncios WHERE $where_clause");
    $stmt->execute($params);
    $total_anuncios = $stmt->fetch()->total;
    $total_paginas = ceil($total_anuncios / $por_pagina);
    
    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE $where_clause ORDER BY ordem_exibicao DESC, data_inicio DESC LIMIT $por_pagina OFFSET $offset");
    $stmt->execute($params);
    $anuncios = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, titulo, imagem, data_inicio FROM anuncios WHERE ativo = 1 ORDER BY data_inicio DESC LIMIT 3");
    $stmt->execute();
    $ultimos = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erro ao buscar anuncios: " . $e->getMessage());
    $anuncios = [];
    $ultimos = [];
    $total_anuncios = 0;
    $total_paginas = 0;
}

$page_title = "Anúncios";
$meta_description = "Acompanhe os anúncios e comunicados da Ordem dos Advogados da Guiné-Bissau.";

if (isset($_GET['ajax'])) {
    if (!empty($anuncios)) {
        foreach ($anuncios as $anuncio) {
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item rounded overflow-hidden d-flex flex-column" style="background: #fff; border: 1px solid #f0ece4; transition: .3s;">
                    <?php if (!empty($anuncio->imagem)): ?>
                    <div class="blog-img position-relative overflow-hidden">
                        <?php $img_anuncio = oagb_resolve_media_path($anuncio->imagem, ''); ?>
                        <img class="img-fluid w-100" style="height:200px; object-fit:cover;" src="<?php echo htmlspecialchars($img_anuncio); ?>" alt="<?php echo htmlspecialchars($anuncio->titulo); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h5 class="mb-3 titulo-artigo">
                            <a href="anuncio.php?id=<?php echo $anuncio->id; ?>" class="linkSublinhado text-decoration-none" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-weight: 700; font-size: 1.1rem;">
                                <?php echo htmlspecialchars($anuncio->titulo); ?>
                            </a>
                        </h5>
                        <div class="d-flex mb-3">
                            <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                <i class="fas fa-bullhorn me-2 text-warning"></i><?php echo format_date_pt($anuncio->data_inicio); ?>
                            </small>
                        </div>
                        <p class="texto-conteudo mb-3 flex-grow-1" style="font-size: 0.9rem; color: #666; line-height: 1.6;">
                            <?php echo htmlspecialchars(truncate_text(strip_tags($anuncio->descricao), 100)); ?>
                        </p>
                        <a href="anuncio.php?id=<?php echo $anuncio->id; ?>" class="d-block mt-auto pt-2">
                            <div class="btn-arrow-only">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    exit;
}
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
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
            --dark-navy: #111923;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }

        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; text-align:left;}
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: #666 !important; text-decoration: none !important; font-size: 0.85rem; transition: .3s; }
        .subpage-breadcrumb-bar a:hover { color: var(--primary-maroon) !important; }
        .subpage-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; }
        .bc-sep { display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--primary-maroon);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--primary-maroon) !important; transition: .3s; font-size: 0.8rem;
        }
        .quick-links a:hover { background: rgba(77,28,33,0.08); color: var(--primary-gold) !important; border-color: var(--primary-gold); }

        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: #fafafa !important; padding: 10px 0; border-bottom: 1px solid #e0dcd2; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #666 !important; }
            .mobile-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { border-color: var(--primary-maroon) !important; color: var(--primary-maroon) !important; width: 28px; height: 28px; font-size: 0.65rem; }
            #mobile-header-simple { background: #fafafa !important; padding-bottom: 10px; width: 100%; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #fafafa !important; }
            #mobile-header-simple .mobile-header-contacts small { color: var(--primary-maroon) !important; font-size: 0.70rem; }
            #mobile-header-simple .mobile-pill-btn { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; background: transparent !important; }
            #mobile-header-simple .navbar-toggler, #mobile-header-simple .navbar-toggler * { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .dropdown-item:hover { background: rgba(77,28,33,0.05) !important; color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }

        @media (max-width: 991.98px) {
            html, body { overflow-x: hidden !important; }
            .section-heading { font-size: 1.6rem; }
            .container { padding-left: 20px; padding-right: 20px; }
        }

        @media (min-width: 992px) {
            #topbar .topbar-contacts small { color: #333 !important; }
            #topbar .topbar-btn { color: #333 !important; border-color: rgba(0,0,0,0.15) !important; background: rgba(0,0,0,0.02) !important; }
            .navbar-dark .navbar-nav .nav-link { color: #333 !important; font-weight: 600; }
            .navbar-dark .navbar-nav .nav-link:hover, .navbar-dark .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; }
        }
    </style>
</head>

<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: #fafafa; border-bottom: 1px solid #e0dcd2;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="#">Comunicação</a>
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
    <div class="d-block d-lg-none">
        <div id="mobile-header-simple" style="position: relative; overflow: hidden;">
            <div class="mobile-navbar-wrapper container-fluid p-0" style="margin-top: 5px;">
                <?php include 'includes/navbar.php'; ?>
            </div>
            <div class="mobile-breadcrumb-bar">
                <div class="container d-flex align-items-center justify-content-between py-2">
                    <div style="font-size: 0.72rem;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo $page_title; ?></span>
                    </div>
                    <div class="quick-links d-flex gap-1">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Start -->
    <div class="container-fluid py-5 section-noticias" style="margin-top: 40px;">
        <div class="container">
            <div class="row g-5">
                <!-- Anuncios List -->
                <div class="col-lg-8">
                    <div class="row g-4" id="news-container">
                        <?php if (!empty($anuncios)): ?>
                            <?php foreach ($anuncios as $anuncio): ?>
                            <div class="col-md-6 mb-4">
                                <div class="blog-item rounded overflow-hidden d-flex flex-column" style="background: #fff; border: 1px solid #f0ece4; transition: .3s;">
                                    <?php if (!empty($anuncio->imagem)): ?>
                                    <div class="blog-img position-relative overflow-hidden">
                                        <?php $img_anuncio = oagb_resolve_media_path($anuncio->imagem, ''); ?>
                                        <img class="img-fluid w-100" style="height:200px; object-fit:cover;" src="<?php echo htmlspecialchars($img_anuncio); ?>" alt="<?php echo htmlspecialchars($anuncio->titulo); ?>">
                                    </div>
                                    <?php endif; ?>
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        <h5 class="mb-3 titulo-artigo">
                                            <a href="anuncio.php?id=<?php echo $anuncio->id; ?>" class="linkSublinhado text-decoration-none" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-weight: 700; font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($anuncio->titulo); ?>
                                            </a>
                                        </h5>
                                        <div class="d-flex mb-3">
                                            <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                                <i class="fas fa-bullhorn me-2 text-warning"></i><?php echo format_date_pt($anuncio->data_inicio); ?>
                                            </small>
                                        </div>
                                        <p class="texto-conteudo mb-3 flex-grow-1" style="font-size: 0.9rem; color: #666; line-height: 1.6;">
                                            <?php echo htmlspecialchars(truncate_text(strip_tags($anuncio->descricao), 100)); ?>
                                        </p>
                                        <a href="anuncio.php?id=<?php echo $anuncio->id; ?>" class="d-block mt-auto pt-2">
                                            <div class="btn-arrow-only">
                                                <i class="bi bi-arrow-right"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted" style="font-family: 'Libre Baskerville', serif;">Nenhum anúncio encontrado</h5>
                            <p class="text-muted" style="font-family: 'Open Sans', sans-serif;">Não existem comunicados disponíveis no momento.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Ultimos Anuncios -->
                    <?php if (!empty($ultimos)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 700; position: relative; padding-bottom: 10px;">
                            Últimos Anúncios
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </h5>
                        <?php $total_ultimos = count($ultimos); $ultimo_idx = 0; foreach ($ultimos as $lida): $ultimo_idx++; ?>
                        <div class="d-flex align-items-center mb-0">
                            <?php if (!empty($lida->imagem)): ?>
                                <?php $img_lida = oagb_resolve_media_path($lida->imagem, ''); ?>
                                <img class="img-fluid rounded" src="<?php echo htmlspecialchars($img_lida); ?>" style="width: 80px; height: 80px; object-fit: cover;" alt="">
                                <div class="ps-3">
                            <?php else: ?>
                                <div class="w-100">
                            <?php endif; ?>
                                <h6 class="mb-1" style="font-family: 'Libre Baskerville', serif; font-size: 0.95rem; line-height: 1.4;">
                                    <a href="anuncio.php?id=<?php echo $lida->id; ?>" class="text-decoration-none fw-bold" style="color: #4D1C21; transition: 0.3s;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars(truncate_text($lida->titulo, 50)); ?>
                                    </a>
                                </h6>
                                <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;"><i class="fas fa-bullhorn text-warning me-1"></i> <?php echo format_date_pt($lida->data_inicio); ?></small>
                            </div>
                        </div>
                        <?php if ($ultimo_idx < $total_ultimos): ?>
                        <hr style="border-top: 1px solid #f0ece4; margin: 1.2rem 0; opacity: 1;">
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loading-spinner" class="text-center my-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">A carregar...</span>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            let page = 1;
            let loading = false;
            let hasMore = <?php echo ($total_paginas > 1) ? 'true' : 'false'; ?>;
            let totalPages = <?php echo $total_paginas; ?>;

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                    if (hasMore && !loading) {
                        loading = true;
                        page++;
                        $('#loading-spinner').show();

                        $.ajax({
                            url: 'anuncios.php',
                            type: 'GET',
                            data: {
                                ajax: 1,
                                pagina: page,
                                busca: '<?php echo $busca; ?>',
                                ano: '<?php echo $ano; ?>'
                            },
                            success: function(data) {
                                if (data.trim() === '') {
                                    hasMore = false;
                                } else {
                                    $('#news-container').append(data);
                                }
                                loading = false;
                                $('#loading-spinner').hide();
                                
                                if (page >= totalPages) {
                                    hasMore = false;
                                }
                            },
                            error: function() {
                                loading = false;
                                $('#loading-spinner').hide();
                            }
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
