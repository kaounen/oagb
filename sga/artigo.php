<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_resolve_media_path')) {
    /**
     * Normaliza caminhos de imagens vindos da base de dados.
     */
    function oagb_resolve_media_path($rawPath, $defaultPath)
    {
        if (empty($rawPath)) {
            return $defaultPath;
        }

        $normalized = str_replace('\\', '/', trim((string) $rawPath));
        $normalized = preg_replace('#\.\.+#', '', $normalized);

        if ($normalized === '') {
            return $defaultPath;
        }

        if (preg_match('#^https?://#i', $normalized)) {
            return $normalized;
        }

        if ($normalized[0] === '/') {
            $normalized = ltrim($normalized, '/');
        }

        if (strpos($normalized, 'uploads/') === 0 || strpos($normalized, 'img/') === 0) {
            return $normalized;
        }

        return 'uploads/' . $normalized;
    }
}

// Obter ID ou slug da notícia
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? clean_input($_GET['slug']) : '';

$noticia = null;
$noticias_relacionadas = [];

try {
    // Buscar notícia por ID ou slug
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
    } elseif (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE slug = ? AND ativo = 1");
        $stmt->execute([$slug]);
    }
    
    $noticia = $stmt->fetch();
    
    if (!$noticia) {
        header("Location: noticias.php");
        exit;
    }
    
    // Incrementar visualizações
    $stmt = $pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?");
    $stmt->execute([$noticia->id]);
    
    // Buscar notícias relacionadas (mesma categoria)
    $stmt = $pdo->prepare("
        SELECT id, titulo, slug, resumo, imagem_destaque, data_publicacao 
        FROM noticias 
        WHERE categoria = ? AND id != ? AND ativo = 1 
        ORDER BY data_publicacao DESC 
        LIMIT 3
    ");
    $stmt->execute([$noticia->categoria, $noticia->id]);
    $noticias_relacionadas = $stmt->fetchAll();

    // Buscar notícias mais lidas
    $stmt = $pdo->prepare("
        SELECT id, titulo, slug, resumo, imagem_destaque, data_publicacao, visualizacoes 
        FROM noticias 
        WHERE ativo = 1 
        ORDER BY visualizacoes DESC 
        LIMIT 3
    ");
    $stmt->execute();
    $mais_lidas = $stmt->fetchAll();
    
    // Buscar tags populares
    $stmt = $pdo->prepare("
        SELECT tags FROM noticias 
        WHERE ativo = 1 AND tags IS NOT NULL
    ");
    $stmt->execute();
    $all_tags = $stmt->fetchAll();
    
    $tags_count = [];
    foreach ($all_tags as $row) {
        if (!empty($row->tags)) {
            $tags_array = explode(',', $row->tags);
            foreach ($tags_array as $t) {
                $t = trim($t);
                if (!empty($t)) {
                    $tags_count[$t] = isset($tags_count[$t]) ? $tags_count[$t] + 1 : 1;
                }
            }
        }
    }
    arsort($tags_count);
    $tags_populares = array_slice($tags_count, 0, 15, true);
    
    // Buscar imagens adicionais (slider)
    $stmt = $pdo->prepare("SELECT * FROM noticias_imagens WHERE noticia_id = ? ORDER BY ordem_exibicao ASC, id ASC");
    $stmt->execute([$noticia->id]);
    $noticia_imagens = $stmt->fetchAll();

    $todas_imagens = [];
    // Verificar se a imagem de destaque já existe na galeria
    $destaque_na_galeria = false;
    if (!empty($noticia->imagem_destaque)) {
        foreach ($noticia_imagens as $img) {
            if ($img->imagem === $noticia->imagem_destaque) {
                $destaque_na_galeria = true;
                break;
            }
        }
    }
    // Se a imagem de destaque NÃO está na galeria, adicioná-la como primeira (usando o resumo como legenda)
    if (!empty($noticia->imagem_destaque) && !$destaque_na_galeria) {
        $todas_imagens[] = (object)[
            'imagem' => $noticia->imagem_destaque,
            'legenda' => $noticia->resumo ?? '',
            'descricao' => ''
        ];
    }
    // Adicionar todas as imagens da galeria (incluindo a de destaque se lá estiver)
    foreach ($noticia_imagens as $img) {
        $todas_imagens[] = (object)[
            'imagem' => $img->imagem,
            'legenda' => $img->legenda ?? '',
            'descricao' => $img->descricao ?? ''
        ];
    }
    
    // Buscar ficheiros anexos adicionais (tabela ficheiros_anexos)
    $attachments = [];
    try {
        $stmt = $pdo->prepare("
            SELECT nome_ficheiro, nome_original, tipo_mime, tamanho, descricao 
            FROM ficheiros_anexos 
            WHERE tipo_entidade = 'noticia' AND entidade_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$noticia->id]);
        $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("ficheiros_anexos query: " . $e->getMessage());
    }
    
} catch (Exception $e) {
    error_log("Erro ao buscar notícia: " . $e->getMessage());
    header("Location: noticias.php");
    exit;
}

$page_title = htmlspecialchars($noticia->titulo);
$meta_description = htmlspecialchars($noticia->resumo);
$meta_image = !empty($noticia->imagem_destaque) ? 
              'uploads/' . $noticia->imagem_destaque : 
              'img/Asset 7-100.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    
<?php 
$full_url = SITE_URL . '/artigo.php?id=' . $noticia->id . '&slug=' . urlencode($noticia->slug);
$absolute_meta_image = SITE_URL . '/' . $meta_image;
?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $absolute_meta_image; ?>">
    <meta property="og:url" content="<?php echo $full_url; ?>">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="OAGB - Ordem dos Advogados da Guiné-Bissau">
    <meta property="fb:app_id" content="123456789"> <!-- Opcional: ID da App Facebook da Ordem -->
    
    <!-- Google Web Fonts -->
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

        /* === SUBPAGE BREADCRUMB BAR (fundo creme — cores escuras) === */
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
        .quick-links a:hover i { color: var(--primary-gold) !important; }

        /* Mobile breadcrumbs & header (fundo creme) */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar {
                background: #fafafa !important; padding: 10px 0;
                border-bottom: 1px solid #e0dcd2;
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span {
                font-size: 0.72rem; color: #666 !important;
            }
            .mobile-breadcrumb-bar .bc-active { color: var(--primary-maroon) !important; font-weight: 600; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a {
                border-color: var(--primary-maroon) !important; color: var(--primary-maroon) !important; width: 28px; height: 28px; font-size: 0.65rem;
            }
            .mobile-breadcrumb-bar .quick-links a:hover {
                background: rgba(77,28,33,0.08) !important; border-color: var(--primary-gold) !important;
            }
            #mobile-header-simple { background: #fafafa !important; padding-bottom: 10px; width: 100%; overflow: hidden; }
            #mobile-header-simple .mobile-header-contacts { background: #fafafa !important; }
            #mobile-header-simple .mobile-header-contacts small { color: var(--primary-maroon) !important; font-size: 0.70rem; }
            #mobile-header-simple .mobile-header-contacts i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn { color: var(--primary-maroon) !important; border-color: var(--primary-maroon) !important; background: transparent !important; }
            #mobile-header-simple .mobile-pill-btn i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .mobile-pill-btn:hover,
            #mobile-header-simple .mobile-pill-btn:active,
            #mobile-header-simple .mobile-pill-btn:focus {
                background: rgba(77,28,33,0.08) !important; 
                border-color: var(--primary-gold) !important;
                color: var(--primary-maroon) !important;
            }
            #mobile-header-simple .mobile-pill-btn:hover i { color: var(--primary-maroon) !important; }
            #mobile-header-simple .navbar-toggler,
            #mobile-header-simple .navbar-toggler *,
            #mobile-header-simple .navbar-toggler i { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-toggler::after { color: var(--primary-gold) !important; }
            
            #mobile-header-simple .dropdown-item:hover,
            #mobile-header-simple .dropdown-item:active {
                background: rgba(77,28,33,0.05) !important;
                color: var(--primary-gold) !important;
            }
            
            /* Logo adjustment for mobile on cream background */
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        /* === DESKTOP OVERRIDES FOR LIGHT BACKGROUND === */
        @media (min-width: 992px) {
            /* Topbar: Dark text on cream */
            #topbar .topbar-contacts small, 
            #topbar .topbar-contacts small i { color: #333 !important; }
            
            #topbar .topbar-btn { 
                color: #333 !important; 
                border-color: rgba(0,0,0,0.15) !important; 
                background: rgba(0,0,0,0.02) !important; 
            }
            #topbar .topbar-btn i { color: var(--primary-maroon) !important; }
            #topbar .topbar-btn:hover { 
                background: rgba(77,28,33,0.05) !important; 
                border-color: var(--primary-maroon) !important; 
            }

            /* Navbar: Dark links on cream */
            .navbar-dark .navbar-nav .nav-link { color: #333 !important; font-weight: 600; }
            .navbar-dark .navbar-nav .nav-link:hover,
            .navbar-dark .navbar-nav .nav-link.active { color: var(--primary-maroon) !important; }
        }

        .article-content {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        
        .article-content h2, 
        .article-content h3, 
        .article-content h4 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            margin: 2rem 0;
            border-radius: 10px;
        }
        
        .article-content ul, 
        .article-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }
        
        .article-content blockquote {
            border-left: 4px solid #c18046;
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #666;
        }
        
        .share-buttons {
            padding: 1.5rem 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            margin: 2rem 0;
        }
        
        .share-buttons .btn {
            margin-right: 0.5rem;
        }
        
        .article-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .related-article {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            background: #fff;
        }
        
        .related-article:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .related-article img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        /* ======= PREMIUM ARTICLE SLIDER ======= */
        #artigoCarousel {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        #artigoCarousel .carousel-item {
            position: relative;
        }
        #artigoCarousel .carousel-item img {
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .artigo-slide-caption {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(0deg, rgba(17,25,35,0.82) 0%, rgba(17,25,35,0.45) 60%, transparent 100%);
            padding: 50px 28px 18px 28px;
            z-index: 3;
        }
        .artigo-slide-caption p {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.95);
            margin: 0;
            letter-spacing: 0.2px;
            line-height: 1.5;
        }
        .artigo-slide-caption .slide-desc {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
        }
        .artigo-slider-nav {
            position: absolute;
            top: 50%; transform: translateY(-50%);
            width: 48px; height: 48px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.6);
            background: rgba(17,25,35,0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            opacity: 0.7;
            padding: 0;
        }
        .artigo-slider-nav:hover {
            background: rgba(77,28,33,0.85);
            border-color: rgba(177,162,118,0.8);
            opacity: 1;
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .artigo-slider-nav i { font-size: 1.1rem; line-height: 1; }
        .artigo-slider-nav.prev { left: 16px; }
        .artigo-slider-nav.next { right: 16px; }
        .artigo-slide-counter {
            position: absolute;
            top: 16px; right: 16px;
            background: rgba(17,25,35,0.55);
            backdrop-filter: blur(8px);
            color: rgba(255,255,255,0.9);
            padding: 5px 14px;
            border-radius: 20px;
            font-family: 'Open Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            z-index: 5;
            border: 1px solid rgba(255,255,255,0.15);
        }
        #artigoCarousel .carousel-indicators { margin-bottom: 10px; z-index: 4; }
        #artigoCarousel .carousel-indicators button {
            width: 10px; height: 10px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.7);
            background: transparent;
            opacity: 0.6;
            transition: all 0.3s ease;
            margin: 0 4px;
        }
        #artigoCarousel .carousel-indicators button.active {
            background: #B1A276;
            border-color: #B1A276;
            opacity: 1;
            transform: scale(1.15);
        }
        .artigo-single-img {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        @media (max-width: 768px) {
            .artigo-slider-nav { width: 40px; height: 40px; }
            .artigo-slider-nav.prev { left: 10px; }
            .artigo-slider-nav.next { right: 10px; }
            .artigo-slide-caption { padding: 40px 18px 14px 18px; }
            .artigo-slide-caption p { font-size: 0.85rem; }
            .artigo-slide-counter { top: 10px; right: 10px; font-size: 0.7rem; padding: 4px 10px; }
            #artigoCarousel .carousel-item img,
            .artigo-single-img img { height: 320px !important; }
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
                        <a href="noticias.php">Notícias</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Artigo</span>
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
                        <a href="noticias.php">Notícias</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Artigo</span>
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
    <!-- Navbar End -->

    <!-- Article Content Start -->
    <div class="container-fluid py-5">
        <div class="container">

            
            <div class="row g-5">
                <!-- Article Main Content -->
                <div class="col-lg-8">
                    <!-- Article Header -->
                    <div class="mb-4">
                        <h1 class="mb-4" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.5rem; line-height: 1.3;">
                            <?php echo htmlspecialchars($noticia->titulo); ?>
                        </h1>
                        
                        <!-- Article Meta Information -->
                        <div class="article-meta" style="border-bottom: none; margin-bottom: 1rem; padding-bottom: 0;">
                            <div class="article-meta-item">
                                <span style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo format_date_pt($noticia->data_publicacao); ?>
                                </span>
                            </div>
                            <?php if (!empty($noticia->autor)): ?>
                            <div class="article-meta-item">
                                <i class="far fa-user" style="color: #B1A276;"></i>
                                <span style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($noticia->autor); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Slider / Imagem Premium -->
                        <?php if (count($todas_imagens) > 1): ?>
                            <?php $total_slides = count($todas_imagens); ?>
                            <div id="artigoCarousel" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="6000">
                                <div class="artigo-slide-counter">
                                    <i class="far fa-images me-1"></i>
                                    <span id="slideCurrentNum">1</span> / <?php echo $total_slides; ?>
                                </div>
                                <div class="carousel-inner">
                                    <?php foreach ($todas_imagens as $index => $img): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <?php $img_path = oagb_resolve_media_path($img->imagem, 'uploads/OAGB-Placeholder.jpg'); ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" class="d-block w-100" style="object-fit: cover; height: 500px;" alt="<?php echo htmlspecialchars(!empty($img->legenda) ? $img->legenda : $noticia->titulo); ?>">
                                        <?php if (!empty($img->legenda)): ?>
                                        <div class="artigo-slide-caption">
                                            <p><?php echo htmlspecialchars($img->legenda); ?></p>
                                            <?php if (!empty($img->descricao)): ?>
                                            <p class="slide-desc"><?php echo htmlspecialchars($img->descricao); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="carousel-indicators">
                                    <?php for ($i = 0; $i < $total_slides; $i++): ?>
                                    <button type="button" data-bs-target="#artigoCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?>></button>
                                    <?php endfor; ?>
                                </div>
                                <button class="artigo-slider-nav prev" type="button" data-bs-target="#artigoCarousel" data-bs-slide="prev" aria-label="Anterior">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="artigo-slider-nav next" type="button" data-bs-target="#artigoCarousel" data-bs-slide="next" aria-label="Próximo">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var carousel = document.getElementById('artigoCarousel');
                                if (carousel) {
                                    carousel.addEventListener('slid.bs.carousel', function(e) {
                                        var counter = document.getElementById('slideCurrentNum');
                                        if (counter) counter.textContent = e.to + 1;
                                    });
                                }
                            });
                            </script>
                        <?php elseif (count($todas_imagens) === 1): ?>
                            <?php $img_path = oagb_resolve_media_path($todas_imagens[0]->imagem, 'uploads/OAGB-Placeholder.jpg'); ?>
                            <div class="artigo-single-img mb-4">
                                <img src="<?php echo htmlspecialchars($img_path); ?>" class="img-fluid w-100" style="height: 500px; object-fit: cover; display: block;" alt="<?php echo htmlspecialchars(!empty($todas_imagens[0]->legenda) ? $todas_imagens[0]->legenda : $noticia->titulo); ?>">
                                <?php if (!empty($todas_imagens[0]->legenda)): ?>
                                <div class="artigo-slide-caption">
                                    <p><?php echo htmlspecialchars($todas_imagens[0]->legenda); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Article Content -->
                    <div class="article-content">
                        <?php 
                        // Processar conteúdo - permitir HTML seguro
                        $conteudo = $noticia->conteudo;
                        // Converter quebras de linha em parágrafos se necessário
                        if (!strpos($conteudo, '<p>')) {
                            $paragraphs = explode("\n\n", $conteudo);
                            $conteudo = '<p>' . implode('</p><p>', array_filter($paragraphs)) . '</p>';
                        }
                        echo $conteudo;
                        ?>
                    </div>
                    
                    <!-- Article Tags -->
                    <?php if (!empty($noticia->tags)): ?>
                    <div class="article-tags">
                        <h5 class="mb-3">Tags:</h5>
                        <?php 
                        $tags = explode(',', $noticia->tags);
                        foreach ($tags as $tag): 
                            $tag = trim($tag);
                            if (!empty($tag)):
                        ?>
                        <a href="pesquisa.php?q=<?php echo urlencode($tag); ?>" class="tag-badge">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Documento Principal (Quick Download) -->
                    <?php if (!empty($noticia->ficheiro_anexo)): ?>
                    <div class="mt-5 mb-0">
                        <a href="uploads/<?php echo $noticia->ficheiro_anexo; ?>" class="text-decoration-none d-block" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                            <div class="d-flex align-items-center justify-content-between p-4 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 12px; flex-shrink: 0;">
                                        <i class="far fa-file-pdf" style="font-size: 1.4rem; color: #fff;"></i>
                                    </div>
                                    <div>
                                        <?php 
                                            if (!empty($noticia->legenda_anexo)) {
                                                $display_name = $noticia->legenda_anexo;
                                            } else {
                                                // Extract filename without extension first
                                                $basename = pathinfo($noticia->ficheiro_anexo, PATHINFO_FILENAME);
                                                
                                                // Detect system-generated patterns: doc_1_hash, noticia_1_hash, news_hash
                                                if (preg_match('/^(doc|noticia|news|evento|att)_\d+_[a-f0-9]+$/i', $basename)) {
                                                    $display_name = 'Documento Anexo';
                                                } elseif (preg_match('/^[a-f0-9]{10,}$/i', $basename)) {
                                                    // Pure hash filename
                                                    $display_name = 'Documento Anexo';
                                                } else {
                                                    // Has readable name — clean timestamp prefix and underscores
                                                    $clean = preg_replace('/^\d{6,}_/', '', $basename);
                                                    $display_name = str_replace('_', ' ', $clean);
                                                }
                                                
                                                // Override with original name from ficheiros_anexos if available
                                                if (!empty($attachments)) {
                                                    foreach ($attachments as $a) {
                                                        if ($a['nome_ficheiro'] === $noticia->ficheiro_anexo && !empty($a['nome_original'])) {
                                                            $display_name = str_replace('_', ' ', pathinfo($a['nome_original'], PATHINFO_FILENAME));
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 1rem; color: #333;"><?php echo htmlspecialchars($display_name); ?></div>
                                        <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.78rem;">Documento PDF — Clique para abrir</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 50%; background: rgba(177,162,118,0.12); flex-shrink: 0;">
                                    <i class="fas fa-download" style="color: #B1A276; font-size: 0.9rem;"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Documentos Anexos -->
                    <?php if (!empty($attachments)): ?>
                    <div class="mt-4">
                        <h6 class="mb-3" style="font-family: 'Open Sans', sans-serif; color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;"><i class="fas fa-paperclip me-2"></i>Documentos Anexos</h6>
                        <?php foreach ($attachments as $att): ?>
                        <a href="uploads/<?php echo htmlspecialchars($att['nome_ficheiro']); ?>" class="text-decoration-none d-block mb-2" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 10px; flex-shrink: 0;">
                                        <?php if(strpos($att['tipo_mime'], 'pdf') !== false): ?>
                                            <i class="far fa-file-pdf" style="font-size: 1.2rem; color: #fff;"></i>
                                        <?php elseif(strpos($att['tipo_mime'], 'image') !== false): ?>
                                            <i class="far fa-file-image" style="font-size: 1.2rem; color: #fff;"></i>
                                        <?php else: ?>
                                            <i class="far fa-file-alt" style="font-size: 1.2rem; color: #fff;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php 
                                            $att_display = !empty($att['descricao']) ? $att['descricao'] : $att['nome_original'];
                                            $att_display = str_replace('_', ' ', pathinfo($att_display, PATHINFO_FILENAME));
                                        ?>
                                        <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 0.92rem; color: #333;"><?php echo htmlspecialchars($att_display); ?></div>
                                        <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.72rem;"><?php echo number_format($att['tamanho'] / 1024, 0); ?> KB</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(177,162,118,0.12); flex-shrink: 0;">
                                    <i class="fas fa-download" style="color: #B1A276; font-size: 0.8rem;"></i>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Mais Lidas -->
                    <?php if (!empty($mais_lidas)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 700; position: relative; padding-bottom: 10px;">
                            Mais Lidas
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </h5>
                        <?php $total_lidas = count($mais_lidas); $lida_idx = 0; foreach ($mais_lidas as $lida): $lida_idx++; ?>
                        <div class="mb-0 group-card-lidas" style="transition: all 0.3s ease;">
                            <a href="artigo.php?id=<?php echo $lida->id; ?>&slug=<?php echo urlencode($lida->slug); ?>" class="text-decoration-none d-block">
                                <?php if (!empty($lida->imagem_destaque)): ?>
                                    <?php $img_lida = oagb_resolve_media_path($lida->imagem_destaque, 'uploads/OAGB-Placeholder.jpg'); ?>
                                    <div class="rounded-3 overflow-hidden mb-3" style="position: relative;">
                                        <img class="img-fluid w-100" src="<?php echo htmlspecialchars($img_lida); ?>" style="height: 160px; object-fit: cover; transition: transform 0.5s ease;" alt="" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </div>
                                <?php endif; ?>
                                <div class="w-100">
                                    <div class="mb-2" style="color:#B1A276; font-family: 'Open Sans', sans-serif; font-weight: 700; font-size:0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo format_date_pt($lida->data_publicacao); ?>
                                    </div>
                                    <h6 class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 0.95rem; line-height: 1.45; color: #4D1C21; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
                                        <?php echo htmlspecialchars($lida->titulo); ?>
                                    </h6>
                                </div>
                            </a>
                        </div>
                        <?php if ($lida_idx < $total_lidas): ?>
                        <hr style="border-top: 1px solid #f0ece4; margin: 1.2rem 0; opacity: 1;">
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Tags Populares -->
                    <?php if (!empty($tags_populares)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 700; position: relative; padding-bottom: 10px;">
                            Tags Populares
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </h5>
                        <div class="d-flex flex-wrap m-n1">
                            <?php foreach ($tags_populares as $t => $count): ?>
                            <a href="noticias.php?tag=<?php echo urlencode($t); ?>" class="btn btn-light btn-sm m-1 px-3 rounded-pill" style="font-size: 0.8rem; background: #f8f9fa; color: #666; border: 1px solid #eee;">
                                <?php echo htmlspecialchars($t); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Related Articles -->
            <?php if (!empty($noticias_relacionadas)): ?>
            <div class="related-articles mt-5">
                <h3 class="mb-4" style="color:#4D1C21; font-family: 'Libre Baskerville', serif;">Notícias Relacionadas</h3>
                <div class="row g-4">
                    <?php foreach ($noticias_relacionadas as $relacionada): ?>
                    <div class="col-lg-4">
                        <div class="related-article">
                            <?php 
                            $img_relacionada = oagb_resolve_media_path($relacionada->imagem_destaque, 'uploads/OAGB-Placeholder.jpg');
                            ?>
                            <img src="<?php echo htmlspecialchars($img_relacionada); ?>" alt="<?php echo htmlspecialchars($relacionada->titulo); ?>">
                            <div class="p-4">
                                <h5 class="mb-3">
                                    <a href="artigo.php?id=<?php echo $relacionada->id; ?>&slug=<?php echo urlencode($relacionada->slug); ?>" 
                                       class="text-dark text-decoration-none fw-bold" style="font-size: 1rem;">
                                        <?php echo htmlspecialchars($relacionada->titulo); ?>
                                    </a>
                                </h5>
                                <p class="text-muted mb-3" style="font-size: 0.85rem;"><?php echo htmlspecialchars(truncate_text($relacionada->resumo, 80)); ?></p>
                                <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo format_date_pt($relacionada->data_publicacao); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Article Content End -->

    <?php include 'includes/banner-inscricao.php'; ?>

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>
