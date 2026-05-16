<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Parâmetros de filtro e paginação
$categoria = isset($_GET['categoria']) ? clean_input($_GET['categoria']) : 'todas';
$busca = isset($_GET['busca']) ? clean_input($_GET['busca']) : '';
$tag = isset($_GET['tag']) ? clean_input($_GET['tag']) : '';
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 9;
$offset = ($pagina - 1) * $por_pagina;
$view_mode = isset($_GET['view']) ? clean_input($_GET['view']) : 'grid'; // grid ou list

// Construir query
$where = ['ativo = 1'];
$params = [];

// Filtro por categoria
if ($categoria != 'todas' && !empty($categoria)) {
    $where[] = 'categoria = ?';
    $params[] = $categoria;
}

// Filtro por tag
if (!empty($tag)) {
    $where[] = 'tags LIKE ?';
    $params[] = '%' . $tag . '%';
}

// Filtro por ano/mês
if ($ano > 2020 && $ano <= date('Y')) {
    $where[] = 'YEAR(data_publicacao) = ?';
    $params[] = $ano;
}
if ($mes > 0 && $mes <= 12) {
    $where[] = 'MONTH(data_publicacao) = ?';
    $params[] = $mes;
}

// Busca
if (!empty($busca)) {
    $where[] = '(titulo LIKE ? OR resumo LIKE ? OR conteudo LIKE ? OR tags LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

$where_clause = implode(' AND ', $where);

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

try {
    // Contar total de notícias
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM noticias WHERE $where_clause");
    $stmt->execute($params);
    $total_noticias = $stmt->fetch()->total;
    $total_paginas = ceil($total_noticias / $por_pagina);
    
    // Buscar notícias
    $stmt = $pdo->prepare("
        SELECT * FROM noticias 
        WHERE $where_clause 
        ORDER BY data_publicacao DESC 
        LIMIT $por_pagina OFFSET $offset
    ");
    $stmt->execute($params);
    $noticias = $stmt->fetchAll();

    // Buscar notícias em destaque
    $stmt = $pdo->prepare("
        SELECT * FROM noticias 
        WHERE ativo = 1 AND destaque = 1 
        ORDER BY data_publicacao DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $noticias_destaque = $stmt->fetchAll();
    
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
    
    // Buscar categorias disponíveis com contagem
    $stmt = $pdo->prepare("
        SELECT categoria, COUNT(*) as total 
        FROM noticias 
        WHERE ativo = 1 AND categoria IS NOT NULL 
        GROUP BY categoria 
        ORDER BY total DESC
    ");
    $stmt->execute();
    $categorias_disponiveis = $stmt->fetchAll();
    
    // Buscar tags populares
    $stmt = $pdo->prepare("
        SELECT tags FROM noticias 
        WHERE ativo = 1 AND tags IS NOT NULL
    ");
    $stmt->execute();
    $all_tags = $stmt->fetchAll();
    
    // Processar tags
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
    
    // Buscar arquivo de notícias (anos disponíveis)
    $stmt = $pdo->prepare("
        SELECT DISTINCT YEAR(data_publicacao) as ano, COUNT(*) as total 
        FROM noticias 
        WHERE ativo = 1 
        GROUP BY YEAR(data_publicacao) 
        ORDER BY ano DESC
    ");
    $stmt->execute();
    $arquivo_anos = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao buscar notícias: " . $e->getMessage());
    $noticias = [];
    $noticias_destaque = [];
    $mais_lidas = [];
    $total_noticias = 0;
    $total_paginas = 0;
}

$page_title = "Notícias";
$meta_description = "Últimas notícias, comunicados e informações da Ordem dos Advogados da Guiné-Bissau";

// AJAX response for infinite scroll
if (isset($_GET['ajax'])) {
    if (!empty($noticias)) {
        foreach ($noticias as $noticia) {
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item rounded overflow-hidden d-flex flex-column" style="background: #fff; border: 1px solid #f0ece4; transition: .3s;">
                    <?php 
                    $raw_noticia_imagem = $noticia->imagem_destaque ?? '';
                    if (empty($raw_noticia_imagem) && !empty($noticia->imagem)) {
                        $raw_noticia_imagem = $noticia->imagem;
                    }
                    if (empty($raw_noticia_imagem) && !empty($noticia->foto)) {
                        $raw_noticia_imagem = $noticia->foto;
                    }
                    ?>
                    <?php if (!empty($raw_noticia_imagem)): ?>
                    <div class="blog-img position-relative overflow-hidden">
                        <?php $img_noticia = oagb_resolve_media_path($raw_noticia_imagem, ''); ?>
                        <img class="img-fluid w-100" style="height:220px; object-fit:cover;" src="<?php echo htmlspecialchars($img_noticia); ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h4 class="mb-3 titulo-artigo">
                            <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="linkSublinhado text-decoration-none" style="color:#4D1C21;">
                                <?php echo htmlspecialchars($noticia->titulo); ?>
                            </a>
                        </h4>
                        <div class="d-flex mb-3">
                            <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                <?php echo format_date_pt($noticia->data_publicacao); ?>
                            </small>
                        </div>
                        <p class="texto-conteudo mb-3 flex-grow-1">
                            <?php echo htmlspecialchars(truncate_text($noticia->resumo, 120)); ?>
                        </p>
                        <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="d-block mt-auto pt-3">
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

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }
        .text-center .section-heading::after { margin-left: auto; margin-right: auto; }

        /* === RESPONSIVO === */
        @media (max-width: 991.98px) {
            html, body { overflow-x: hidden !important; }
            .section-heading { font-size: 1.6rem; }
            .container { padding-left: 20px; padding-right: 20px; }
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

        /* === PREMIUM SEARCH BAR === */
        .premium-search-wrapper {
            background: #fff;
            border-radius: 50px;
            padding: 10px 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #f0ece4;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .premium-search-wrapper:hover {
            box-shadow: 0 15px 40px rgba(177, 162, 118, 0.15);
        }
        .premium-search-item {
            position: relative;
            padding: 5px 20px;
        }
        .premium-search-divider {
            width: 1px;
            height: 40px;
            background: #e0dcd2;
        }
        .premium-search-item label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 2px;
            display: block;
        }
        .premium-search-btn {
            background: var(--primary-maroon);
            color: #fff;
            border-radius: 50px;
            height: 50px;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: .3s;
            flex-shrink: 0;
            margin-left: 10px;
        }
        .premium-search-btn:hover {
            background: #3a1519;
            transform: scale(1.05);
            color: #fff;
        }
        
        @media (max-width: 991px) {
            .premium-search-wrapper {
                flex-direction: column;
                border-radius: 20px;
                padding: 20px;
                align-items: stretch;
            }
            .premium-search-item {
                padding: 10px 0;
            }
            .premium-search-divider {
                width: 100%;
                height: 1px;
                margin: 5px 0;
                background: #f0ece4;
            }
            .premium-search-btn {
                width: 100%;
                margin-top: 15px;
                margin-left: 0;
                border-radius: 10px;
            }
        }
    </style>
</head>

<body class="header-light-page">

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
                        <a href="#">Comunicação</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo $page_title; ?></span>
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

    <!-- News Start -->
    <div class="container-fluid py-5 section-noticias" style="margin-top: 40px;">
        <div class="container">
            


            <div class="row g-5">
                <!-- Notícias List -->
                <div class="col-lg-8">
                    <div class="row g-4" id="news-container">
                        <?php if (!empty($noticias)): ?>
                            <?php foreach ($noticias as $noticia): ?>
                            <div class="col-md-6 mb-4">
                                <div class="blog-item rounded overflow-hidden d-flex flex-column" style="background: #fff; border: 1px solid #f0ece4; transition: .3s;">
                                    <?php 
                                    $raw_noticia_imagem = $noticia->imagem_destaque ?? '';
                                    if (empty($raw_noticia_imagem) && !empty($noticia->imagem)) {
                                        $raw_noticia_imagem = $noticia->imagem;
                                    }
                                    if (empty($raw_noticia_imagem) && !empty($noticia->foto)) {
                                        $raw_noticia_imagem = $noticia->foto;
                                    }
                                    ?>
                                    <?php if (!empty($raw_noticia_imagem)): ?>
                                    <div class="blog-img position-relative overflow-hidden">
                                        <?php $img_noticia = oagb_resolve_media_path($raw_noticia_imagem, ''); ?>
                                        <img class="img-fluid w-100" style="height:200px; object-fit:cover;" src="<?php echo htmlspecialchars($img_noticia); ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                                    </div>
                                    <?php endif; ?>
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        <h5 class="mb-3 titulo-artigo">
                                            <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="linkSublinhado text-decoration-none" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-weight: 700; font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($noticia->titulo); ?>
                                            </a>
                                        </h5>
                                        <div class="d-flex mb-3">
                                            <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                                <i class="far fa-calendar-alt me-2"></i><?php echo format_date_pt($noticia->data_publicacao); ?>
                                            </small>
                                        </div>
                                        <p class="texto-conteudo mb-3 flex-grow-1" style="font-size: 0.9rem; color: #666; line-height: 1.6;">
                                            <?php echo htmlspecialchars(truncate_text($noticia->resumo, 100)); ?>
                                        </p>
                                        <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="d-block mt-auto pt-2">
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
                            <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted" style="font-family: 'Libre Baskerville', serif;">Nenhuma notícia encontrada</h5>
                            <p class="text-muted" style="font-family: 'Open Sans', sans-serif;">Tente ajustar os filtros ou volte mais tarde.</p>
                            <a href="noticias.php" class="btn btn-outline-primary mt-3 px-4 py-2" style="border-radius: 50px;">
                                <i class="fa fa-sync me-2"></i>Ver Todas as Notícias
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
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
                                    <h6 class="mb-0" style="font-family: 'Libre Baskerville', serif; font-size: 1.05rem; line-height: 1.45; color: #4D1C21; transition: color 0.3s ease;" onmouseover="this.style.color='#B1A276'" onmouseout="this.style.color='#4D1C21'">
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

                    <!-- Tags -->
                    <?php if (!empty($tags_populares)): ?>
                    <div class="mb-5 p-4 rounded-3" style="background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <h5 class="mb-4" style="font-family: 'Libre Baskerville', serif; color: #4D1C21; font-weight: 700; position: relative; padding-bottom: 10px;">
                            Tags Populares
                            <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #B1A276;"></span>
                        </h5>
                        <div class="d-flex flex-wrap m-n1">
                            <?php foreach ($tags_populares as $t => $count): ?>
                            <a href="?tag=<?php echo urlencode($t); ?>" class="btn btn-light btn-sm m-1 px-3 rounded-pill" style="font-size: 0.8rem; background: #f8f9fa; color: #666; border: 1px solid #eee;">
                                <?php echo htmlspecialchars($t); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Loading Spinner for Lazy Load -->
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


            // Infinite Scroll (Lazy Load)
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
                            url: 'noticias.php',
                            type: 'GET',
                            data: {
                                ajax: 1,
                                pagina: page,
                                categoria: '<?php echo $categoria; ?>',
                                busca: '<?php echo $busca; ?>',
                                tag: '<?php echo $tag; ?>',
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
