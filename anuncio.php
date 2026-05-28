<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';
require_once 'admin/includes/AttachmentHelper.php';

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
$attachments = [];

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
    
    // Fetch attachments via AttachmentHelper
    $attachments = AttachmentHelper::get($pdo, 'anuncio', $anuncio->id);
    
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
            #mobile-header-simple { background: #fafafa !important; padding-bottom: 10px; width: 100%; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #fafafa !important; }
            #mobile-header-simple .mobile-header-contacts small { color: var(--primary-maroon) !important; font-size: 0.70rem; }
            #mobile-header-simple .mobile-header-contacts i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; background: transparent !important; }
            #mobile-header-simple .mobile-pill-btn i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn:hover, #mobile-header-simple .mobile-pill-btn:focus { background: rgba(77,28,33,0.08) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-toggler, #mobile-header-simple .navbar-toggler * { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-toggler::after { color: var(--primary-gold) !important; }
            #mobile-header-simple .dropdown-item:hover { background: rgba(77,28,33,0.05) !important; color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        @media (min-width: 992px) {
            #topbar .topbar-contacts small, #topbar .topbar-contacts small i { color: #333 !important; }
            #topbar .topbar-btn { color: #333 !important; border-color: rgba(0,0,0,0.15) !important; background: rgba(0,0,0,0.02) !important; }
            #topbar .topbar-btn i { color: var(--primary-maroon) !important; }
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
<body class="header-light-page">
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
            <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                <div class="row g-0 mb-3">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-2 overflow-auto" style="white-space: nowrap;">
                        <small class="text-nowrap"><i class="fa fa-map-marker-alt me-1"></i>Bissau, Guiné-Bissau</small>
                        <small class="text-nowrap"><i class="fa fa-phone-alt me-1"></i>+245 955 475 889</small>
                        <small class="text-nowrap"><i class="fa fa-envelope-open me-1"></i>info@oagb.gw</small>
                    </div>
                </div>

                <div class="row g-0 mb-1">
                    <div class="col-12 d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                             <i class="fa fa-search" style="font-size: 1rem;"></i>
                        </button>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm mobile-pill-btn px-2 fw-bold d-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="static">
                                <i class="fa fa-globe" style="font-size: 1rem;"></i>
                            </button>
                            <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                            </div>
                        </div>
                        <a href="portal/login.php" class="btn btn-sm mobile-pill-btn px-2 fw-bold text-uppercase d-flex align-items-center">
                            <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                        </a>
                    </div>
                </div>
            </div>

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
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
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
                        <h1 class="mb-4" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.5rem; font-weight: 400 !important; line-height: 1.3;">
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

                    <!-- Dynamic Attachments Logic -->
                    <?php
                    $all_files = [];
                    $added_filenames = [];

                    // Gather files from multiple attachments
                    if (!empty($attachments)) {
                        foreach ($attachments as $att) {
                            $filename = $att['nome_ficheiro'];
                            if (!in_array($filename, $added_filenames)) {
                                $all_files[] = (object)[
                                    'nome_ficheiro' => $filename,
                                    'nome_original' => $att['nome_original'],
                                    'tipo_mime' => $att['tipo_mime'] ?? 'application/pdf',
                                    'tamanho' => $att['tamanho'] ?? 0,
                                    'descricao' => $att['descricao'] ?? ''
                                ];
                                $added_filenames[] = $filename;
                            }
                        }
                    }

                    $total_attachments = count($all_files);
                    ?>

                    <?php if ($total_attachments > 1): ?>
                        <!-- Multiple Attachments: Grouped beautiful list -->
                        <div class="mt-4 mb-4">
                            <h6 class="mb-3" style="font-family: 'Open Sans', sans-serif; color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;"><i class="fas fa-paperclip me-2"></i>Documentos Anexos</h6>
                            <?php foreach ($all_files as $att): ?>
                            <a href="uploads/<?php echo htmlspecialchars($att->nome_ficheiro); ?>" class="text-decoration-none d-block mb-2" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                                <div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 p-3 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 10px; flex-shrink: 0;">
                                            <?php if(strpos($att->tipo_mime, 'pdf') !== false): ?>
                                                <i class="far fa-file-pdf" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php elseif(strpos($att->tipo_mime, 'image') !== false): ?>
                                                <i class="far fa-file-image" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php else: ?>
                                                <i class="far fa-file-alt" style="font-size: 1.2rem; color: #fff;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php 
                                                $att_display = !empty($att->descricao) ? $att->descricao : $att->nome_original;
                                                // Clean up original filename 
                                                $att_display = str_replace('_', ' ', pathinfo($att_display, PATHINFO_FILENAME));
                                            ?>
                                            <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 0.92rem; color: #333;"><?php echo htmlspecialchars($att_display); ?></div>
                                            <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.72rem;"><?php echo $att->tamanho > 0 ? number_format($att->tamanho / 1024, 0) . ' KB' : 'Clique para descarregar'; ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                        <!-- Share Button -->
                                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); if(navigator.share){navigator.share({title: '<?php echo htmlspecialchars($anuncio->titulo, ENT_QUOTES); ?>', url: 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'});} else { alert('Link copiado!'); navigator.clipboard.writeText('http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'); }" class="btn btn-sm d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(177,162,118,0.12); color: #B1A276; border: none; transition: 0.3s;" onmouseover="this.style.background='rgba(177,162,118,0.25)'" onmouseout="this.style.background='rgba(177,162,118,0.12)'" title="Partilhar">
                                            <i class="fas fa-share-alt" style="font-size: 0.8rem;"></i>
                                        </button>
                                        <!-- Download Button -->
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(77,28,33,0.1); transition: all 0.3s;">
                                            <i class="fas fa-download" style="color: var(--primary-maroon); font-size: 0.8rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($total_attachments === 1): ?>
                        <!-- Single Attachment: Premium Quick Download card -->
                        <?php $att = $all_files[0]; ?>
                        <div class="mt-5 mb-4">
                            <a href="uploads/<?php echo htmlspecialchars($att->nome_ficheiro); ?>" class="text-decoration-none d-block" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                                <div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 p-4 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 12px; flex-shrink: 0;">
                                            <?php if(strpos($att->tipo_mime, 'pdf') !== false): ?>
                                                <i class="far fa-file-pdf" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php elseif(strpos($att->tipo_mime, 'image') !== false): ?>
                                                <i class="far fa-file-image" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php else: ?>
                                                <i class="far fa-file-alt" style="font-size: 1.4rem; color: #fff;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php 
                                                $att_display = !empty($att->descricao) ? $att->descricao : $att->nome_original;
                                                // Clean up original filename 
                                                $att_display = str_replace('_', ' ', pathinfo($att_display, PATHINFO_FILENAME));
                                            ?>
                                            <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 1rem; color: #333;"><?php echo htmlspecialchars($att_display); ?></div>
                                            <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.78rem;">Documento — Clique para abrir</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                        <!-- Share Button -->
                                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); if(navigator.share){navigator.share({title: '<?php echo htmlspecialchars($anuncio->titulo, ENT_QUOTES); ?>', url: 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'});} else { alert('Link copiado!'); navigator.clipboard.writeText('http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>'); }" class="btn btn-sm d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(177,162,118,0.12); color: #B1A276; border: none; transition: 0.3s;" onmouseover="this.style.background='rgba(177,162,118,0.25)'" onmouseout="this.style.background='rgba(177,162,118,0.12)'" title="Partilhar">
                                            <i class="fas fa-share-alt" style="font-size: 0.9rem;"></i>
                                        </button>
                                        <!-- Download Button -->
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background: rgba(77,28,33,0.1); transition: all 0.3s;">
                                            <i class="fas fa-download" style="color: var(--primary-maroon); font-size: 0.9rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($anuncio->link_url)): ?>
                    <div class="mt-4">
                        <a href="<?php echo htmlspecialchars($anuncio->link_url); ?>" class="btn btn-outline-primary px-4 py-2" style="border-radius: 50px;">
                            <?php echo htmlspecialchars($anuncio->link_texto ?: 'Saiba mais'); ?> <i class="fas fa-external-link-alt ms-2"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <?php if (!empty($ultimos)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <div class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 500; text-transform: uppercase; position: relative; padding-bottom: 10px; font-size: 1.25rem; letter-spacing: 1px;">
                            ÚLTIMOS ANÚNCIOS
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </div>
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
                                    <div class="mb-2" style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                        <i class="fas fa-bullhorn me-1"></i> <?php echo format_date_pt($lida->data_inicio); ?>
                                    </div>
                                    <div class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 0.95rem; line-height: 1.45; color: #4D1C21; font-weight: 400; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars(truncate_text($lida->titulo, 50)); ?>
                                    </div>
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
