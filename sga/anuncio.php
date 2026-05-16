<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$anuncio = null;

try {
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
        $anuncio = $stmt->fetch();
    }
    
    if (!$anuncio) {
        header("Location: anuncios.php");
        exit;
    }
    
    // Ultimos Anuncios para sidebar
    $stmt = $pdo->prepare("SELECT id, titulo, imagem, data_inicio FROM anuncios WHERE id != ? AND ativo = 1 ORDER BY data_inicio DESC LIMIT 3");
    $stmt->execute([$anuncio->id]);
    $ultimos = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erro ao buscar anuncio: " . $e->getMessage());
    header("Location: anuncios.php");
    exit;
}

$page_title = htmlspecialchars($anuncio->titulo);
$meta_description = htmlspecialchars(truncate_text(strip_tags($anuncio->descricao), 150));
$meta_image = !empty($anuncio->imagem) ? 'uploads/' . $anuncio->imagem : 'img/Asset 7-100.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $meta_image; ?>">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="article">
    
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

    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; text-align:left;}
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: #666 !important; text-decoration: none !important; font-size: 0.85rem; transition: .3s; }
        .subpage-breadcrumb-bar a:hover { color: var(--primary-maroon) !important; }
        .subpage-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; }
        .bc-sep { display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; }

        .quick-links a { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--primary-maroon); display: inline-flex; align-items: center; justify-content: center; color: var(--primary-maroon) !important; transition: .3s; font-size: 0.8rem; }
        .quick-links a:hover { background: rgba(77,28,33,0.08); color: var(--primary-gold) !important; border-color: var(--primary-gold); }

        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: #fafafa !important; padding: 10px 0; border-bottom: 1px solid #e0dcd2; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #666 !important; }
            .mobile-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { border-color: var(--primary-maroon) !important; color: var(--primary-maroon) !important; width: 28px; height: 28px; font-size: 0.65rem; }
            #mobile-header-simple { background: #fafafa !important; padding-bottom: 10px; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #fafafa !important; }
            #mobile-header-simple .mobile-header-contacts small { color: var(--primary-maroon) !important; font-size: 0.70rem; }
            #mobile-header-simple .mobile-pill-btn { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; background: transparent !important; }
            #mobile-header-simple .navbar-toggler, #mobile-header-simple .navbar-toggler * { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .dropdown-item:hover { background: rgba(77,28,33,0.05) !important; color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        @media (min-width: 992px) {
            #topbar .topbar-contacts small { color: #333 !important; }
            #topbar .topbar-btn { color: #333 !important; border-color: rgba(0,0,0,0.15) !important; background: rgba(0,0,0,0.02) !important; }
            .navbar-dark .navbar-nav .nav-link { color: #333 !important; font-weight: 600; }
            .navbar-dark .navbar-nav .nav-link:hover, .navbar-dark .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; }
        }

        .article-content { font-family: 'Open Sans', sans-serif; font-size: 1.1rem; line-height: 1.8; color: #333; }
        .article-content h2, .article-content h3 { font-family: 'Libre Baskerville', serif; color: #4D1C21; margin-top: 2rem; margin-bottom: 1rem; }
        .article-content p { margin-bottom: 1.5rem; }
        .article-content img { max-width: 100%; height: auto; margin: 2rem 0; border-radius: 10px; }
        
        .article-meta { display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0; }
        .article-meta-item { display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.95rem; }
    </style>
</head>
<body>
    <?php include 'includes/topbar.php'; ?>

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
                        <a href="anuncios.php">Anúncios</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Comunicado</span>
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
                        <a href="anuncios.php">Anúncios</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Comunicado</span>
                    </div>
                    <div class="quick-links d-flex gap-1">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h1 class="mb-4" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.5rem; line-height: 1.3;">
                            <?php echo htmlspecialchars($anuncio->titulo); ?>
                        </h1>
                        
                        <div class="article-meta" style="border-bottom: none; margin-bottom: 1rem; padding-bottom: 0;">
                            <div class="article-meta-item">
                                <span style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                    <i class="fas fa-bullhorn me-2 text-warning"></i> <?php echo format_date_pt($anuncio->data_inicio); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($anuncio->imagem)): ?>
                            <?php $img_path = oagb_resolve_media_path($anuncio->imagem, ''); ?>
                            <div class="mb-4 position-relative" style="border-radius: 14px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
                                <img src="<?php echo htmlspecialchars($img_path); ?>" class="img-fluid w-100" style="height: 500px; object-fit: cover; display: block;" alt="<?php echo htmlspecialchars($anuncio->titulo); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="article-content">
                        <?php 
                        $conteudo = $anuncio->descricao;
                        if (!strpos($conteudo, '<p>')) {
                            $paragraphs = explode("\n\n", $conteudo);
                            $conteudo = '<p>' . implode('</p><p>', array_filter($paragraphs)) . '</p>';
                        }
                        echo $conteudo;
                        ?>
                    </div>
                    
                    <?php if (!empty($anuncio->link_url)): ?>
                    <div class="mt-4">
                        <a href="<?php echo htmlspecialchars($anuncio->link_url); ?>" target="_blank" class="btn btn-outline-primary px-4 py-2" style="border-radius: 50px;">
                            <?php echo htmlspecialchars($anuncio->link_texto ?: 'Saiba mais'); ?> <i class="fas fa-external-link-alt ms-2"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <?php if (!empty($ultimos)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 700; position: relative; padding-bottom: 10px;">
                            Últimos Anúncios
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </h5>
                        <?php $total_ultimos = count($ultimos); $ultimo_idx = 0; foreach ($ultimos as $lida): $ultimo_idx++; ?>
                        <div class="mb-0 group-card-lidas" style="transition: all 0.3s ease;">
                            <a href="anuncio.php?id=<?php echo $lida->id; ?>" class="text-decoration-none d-block">
                                <?php if (!empty($lida->imagem)): ?>
                                    <?php $img_lida = oagb_resolve_media_path($lida->imagem, 'uploads/OAGB-Placeholder.jpg'); ?>
                                    <div class="rounded-3 overflow-hidden mb-3" style="position: relative;">
                                        <img class="img-fluid w-100" src="<?php echo htmlspecialchars($img_lida); ?>" style="height: 160px; object-fit: cover; transition: transform 0.5s ease;" alt="" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </div>
                                <?php endif; ?>
                                <div class="w-100">
                                    <div class="mb-2" style="color:#B1A276; font-family: 'Open Sans', sans-serif; font-weight: 700; font-size:0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                        <i class="fas fa-bullhorn me-1"></i> <?php echo format_date_pt($lida->data_inicio); ?>
                                    </div>
                                    <h6 class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 1.05rem; line-height: 1.45; color: #4D1C21; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars(truncate_text($lida->titulo, 50)); ?>
                                    </h6>
                                </div>
                            </a>
                        </div>
                        <?php if ($ultimo_idx < $total_ultimos): ?>
                        <hr style="border-top: 1px solid #f0ece4; margin: 1.2rem 0; opacity: 1;">
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
