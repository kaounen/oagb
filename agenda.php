<?php
// Iniciar sessão e incluir ficheiros necess├írios
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Parâmetros de filtro e paginação
$tipo = isset($_GET['tipo']) ? clean_input($_GET['tipo']) : 'todos';

// Definir mês e ano padrão como 0 (Mostrar Tudo) se não forem especificados
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$busca = isset($_GET['busca']) ? clean_input($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 9;
$offset = ($pagina - 1) * $por_pagina;

// Construir query
$where = ['ativo = 1'];
$params = [];

// Filtro por tipo
if ($tipo != 'todos' && in_array($tipo, ['congresso', 'conferencia', 'formacao', 'reuniao', 'workshop', 'palestra', 'outros'])) {
    $where[] = 'tipo_evento = ?';
    $params[] = $tipo;
}

// Filtro por mês/ano
if ($mes > 0 && $mes <= 12) {
    $where[] = 'MONTH(data_evento) = ?';
    $params[] = $mes;
}
if ($ano > 2020 && $ano <= date('Y') + 2) {
    $where[] = 'YEAR(data_evento) = ?';
    $params[] = $ano;
}

// Busca
if (!empty($busca)) {
    $where[] = '(titulo LIKE ? OR descricao LIKE ? OR local_evento LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

$where_clause = implode(' AND ', $where);

try {
    // Contar total de eventos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE $where_clause");
    $stmt->execute($params);
    $total_eventos = $stmt->fetch()->total;
    $total_paginas = ceil($total_eventos / $por_pagina);
    
    // Buscar eventos
    $stmt = $pdo->prepare("
        SELECT * FROM agenda 
        WHERE $where_clause 
        ORDER BY data_evento DESC 
        LIMIT $por_pagina OFFSET $offset
    ");
    $stmt->execute($params);
    $eventos = $stmt->fetchAll();

    // Se não houver eventos, mostrar os últimos carregados
    if (empty($eventos) && $tipo == 'todos' && $mes == 0 && $ano == 0 && empty($busca)) {
        $stmt = $pdo->prepare("
            SELECT * FROM agenda 
            WHERE ativo = 1 
            ORDER BY id DESC 
            LIMIT $por_pagina OFFSET $offset
        ");
        $stmt->execute();
        $eventos = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE ativo = 1");
        $stmt->execute();
        $total_eventos = $stmt->fetch()->total;
        $total_paginas = ceil($total_eventos / $por_pagina);
    }

    
    // Buscar eventos em destaque (próximos 3 eventos importantes)
    $stmt = $pdo->prepare("
        SELECT * FROM agenda 
        WHERE ativo = 1 AND destaque = 1 AND DATE(data_evento) >= CURDATE()
        ORDER BY data_evento ASC 
        LIMIT 3
    ");
    $stmt->execute();
    $eventos_destaque = $stmt->fetchAll();
    
    // Estatísticas para sidebar
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN DATE(data_evento) >= CURDATE() THEN 1 END) as futuros,
            COUNT(CASE WHEN DATE(data_evento) < CURDATE() THEN 1 END) as passados,
            COUNT(CASE WHEN MONTH(data_evento) = MONTH(CURDATE()) AND YEAR(data_evento) = YEAR(CURDATE()) THEN 1 END) as este_mes
        FROM agenda 
        WHERE ativo = 1
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // Buscar todos os destaques da Bastonária para a card lateral
    $stmt = $pdo->prepare("
        SELECT id, titulo, slug FROM agenda 
        WHERE ativo = 1 AND destaque = 1 
        ORDER BY data_evento DESC
    ");
    $stmt->execute();
    $destaques_bastonaria = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao buscar eventos: " . $e->getMessage());
    $eventos = [];
    $eventos_destaque = [];
    $total_eventos = 0;
    $total_paginas = 0;
}

$page_title = "Agenda de Eventos";
$meta_description = "Agenda de eventos, formações, congressos e atividades da Ordem dos Advogados da Guiné-Bissau";

// AJAX response for infinite scroll
if (isset($_GET['ajax'])) {
    if (!empty($eventos)) {
        foreach ($eventos as $evento) {
            $data_evento = new DateTime($evento->data_evento);
            $dia = $data_evento->format('d');
            $mes = $data_evento->format('M');
            $ano = $data_evento->format('Y');
            
            $meses_pt = [
                'Jan' => 'JAN', 'Feb' => 'FEV', 'Mar' => 'MAR',
                'Apr' => 'ABR', 'May' => 'MAI', 'Jun' => 'JUN',
                'Jul' => 'JUL', 'Aug' => 'AGO', 'Sep' => 'SET',
                'Oct' => 'OUT', 'Nov' => 'NOV', 'Dec' => 'DEZ'
            ];
            $mes_pt = $meses_pt[$mes] ?? $mes;
            ?>
            <div class="col-12 mb-3">
                <div class="agenda-evento-novo row align-items-center" style="padding: 2rem; min-height: 180px;">
                    <div class="col-lg-3 col-md-4 text-center agenda-data-container">
                        <div class="agenda-data-display">
                            <div class="agenda-dia" style="font-size: 4rem; font-weight: 700; color: #B1A276; line-height: 1; font-family: 'Libre Baskerville', serif;">
                                <?php echo $dia; ?>
                            </div>
                            <div class="agenda-mes" style="font-size: 1.5rem; font-weight: 600; color: #5B463F; margin-top: -10px; font-family: 'Open Sans', sans-serif;">
                                <?php echo $mes_pt; ?>
                            </div>
                            <div class="agenda-ano" style="font-size: 1.2rem; font-weight: 400; color: #888; font-family: 'Open Sans', sans-serif;">
                                <?php echo $ano; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-9 col-md-8 agenda-conteudo-container">
                         <h4 class="mb-2" style="color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 1.15rem; font-weight: 600; line-height: 1.3;">
                             <a href="evento.php?id=<?php echo $evento->id; ?>" class="linkSublinhado text-decoration-none" style="color: #4D1C21;">
                                <?php echo htmlspecialchars($evento->titulo); ?>
                            </a>
                        </h4>
                        
                        <?php if (!empty($evento->local_evento)): ?>
                        <p class="mb-2" style="color: #B1A276; font-family: 'Open Sans', sans-serif; font-size: 1rem; font-weight: 500;">
                            <i class="fa fa-map-marker-alt me-2" style="color: #B1A276;"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($evento->descricao)): ?>
                        <p class="texto-conteudo mb-3" style="line-height: 1.6;">
                            <?php echo htmlspecialchars(truncate_text($evento->descricao, 200)); ?>
                        </p>
                        <?php endif; ?>
                        
                        <a href="evento.php?id=<?php echo $evento->id; ?>" class="d-block" style="margin-top: 1rem;">
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
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
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
                background: rgba(77,28,33,0.08) !important; border-color: var(--primary-gold) !important;
            }
            #mobile-header-simple .navbar-toggler,
            #mobile-header-simple .navbar-toggler *,
            #mobile-header-simple .navbar-toggler i { color: var(--primary-gold) !important; border-color: var(--primary-gold) !important; }
            #mobile-header-simple .navbar-toggler::after { color: var(--primary-gold) !important; }
            
            /* Logo adjustment for mobile on cream background */
            #mobile-header-simple .navbar-brand { margin: 10px auto !important; display: block; filter: brightness(0.95); }
        }

        /* === PREMIUM TITLES === */
        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 20px; }
        .section-heading::after { content: ''; display: block; width: 50px; height: 3px; background: var(--primary-gold); margin-top: 15px; }
        .text-center .section-heading::after { margin-left: auto; margin-right: auto; }

        /* === PERFIL DO BASTONÁRIO === */
        .bast-profile-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            border: 1px solid #f0ece4; box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        .bast-profile-card:hover { box-shadow: 0 15px 50px rgba(177, 162, 118, 0.15); transform: translateY(-4px); }
        .bast-img-container { background: #f8f8f8; flex: 0 0 400px; max-width: 100%; }
        .bast-img-container img { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); max-width: 100%; height: auto; }
        .bast-content { padding: 2rem; }
        @media (min-width: 768px) { .bast-content { padding: 3rem 4rem; } }
        .bast-content h2 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; margin-bottom: 1rem; word-wrap: break-word; }
        .bast-content .badge { background: var(--primary-gold); letter-spacing: 1px; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; font-weight: 600; }
        .bast-content .bio-text { line-height: 1.8; color: #555; text-align: justify; font-size: 0.95rem; }
        .bast-content .btn-cv { background: var(--primary-maroon); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 600; transition: .3s; border: none; display: inline-block; }
        .bast-content .btn-cv:hover { background: #3a1519; color: #fff; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(77,28,33,0.3); }

        /* === GALERIA DE BASTONÁRIOS === */
        .bastonario-card { background: #fff; border-radius: 15px; border: 1px solid #f0ece4; transition: .3s; cursor: pointer; padding: 1.5rem 1rem; }
        .bastonario-card:hover { box-shadow: 0 8px 25px rgba(177, 162, 118, 0.12); transform: translateY(-3px); }
        .bastonario-card img { width: 80px; height: 80px; object-fit: cover; border: 3px solid #f8f8f8; border-radius: 50%; margin-bottom: 1rem; }
        .bastonario-card h6 { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-size: 0.9rem; margin-bottom: 0.5rem; word-wrap: break-word; }
        .bastonario-card .periodo { font-size: 0.8rem; color: #999; }
        .bastonario-card .bio-extra { display: none; font-size: 0.8rem; color: #666; text-align: justify; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; }

        /* === RESPONSIVO (breakpoint e padding idênticos a a-ordem-dos-advogados) === */
        @media (max-width: 991.98px) {
            html, body { overflow-x: hidden !important; }
            .section-heading { font-size: 1.6rem; }
            .container { padding-left: 20px; padding-right: 20px; }
            .bast-profile-card { flex-direction: column; }
            .bast-img-container { flex: none; width: 100%; padding: 1rem !important; }
            .bast-img-container img { width: 100%; height: auto; }
            .bast-content { padding: 1.5rem !important; }
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
        .premium-search-item input::placeholder {
            color: #ccc;
            font-weight: 400;
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
        
        /* Dropdowns mais bonitos */
        .premium-search-item select, .premium-search-item input {
            padding: 8px 12px !important;
            border-radius: 12px !important;
            transition: all 0.2s ease;
        }
        .premium-search-item select:hover, .premium-search-item input:hover {
            background: rgba(177, 162, 118, 0.08) !important;
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

    <!-- Desktop Header (fundo creme, sem imagem) -->
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

    <!-- Mobile Header (fundo creme, sem imagem) -->
    <div class="d-block d-lg-none">
        <div id="mobile-header-simple" style="position: relative; overflow: hidden;">
            <!-- Contacts -->
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

            <!-- Navbar -->
            <div class="mobile-navbar-wrapper container-fluid p-0" style="margin-top: 5px;">
                <?php include 'includes/navbar.php'; ?>
            </div>

            <!-- Breadcrumbs -->
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

    
<!-- Agenda Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Filtros -->
            <div class="mb-5">
                <form method="GET" action="agenda.php" id="agendaSearchForm" autocomplete="off">
                    <div class="premium-search-wrapper">
                        <!-- Tipo -->
                        <div class="premium-search-item" style="flex: 2;">
                            <select name="tipo" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="todos">Todos</option>
                                <option value="congresso" <?php echo $tipo == 'congresso' ? 'selected' : ''; ?>>Congresso</option>
                                <option value="conferencia" <?php echo $tipo == 'conferencia' ? 'selected' : ''; ?>>Conferência</option>
                                <option value="formacao" <?php echo $tipo == 'formacao' ? 'selected' : ''; ?>>Formação</option>
                                <option value="reuniao" <?php echo $tipo == 'reuniao' ? 'selected' : ''; ?>>Reunião</option>
                                <option value="workshop" <?php echo $tipo == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                                <option value="palestra" <?php echo $tipo == 'palestra' ? 'selected' : ''; ?>>Palestra</option>
                                <option value="outros" <?php echo $tipo == 'outros' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Mês -->
                        <div class="premium-search-item" style="flex: 1.5;">
                            <select name="mes" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="0">Mês</option>
                                <?php 
                                $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                                         'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                foreach ($meses as $i => $nome_mes): 
                                ?>
                                <option value="<?php echo $i + 1; ?>" <?php echo $mes == ($i + 1) ? 'selected' : ''; ?>>
                                    <?php echo $nome_mes; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Ano -->
                        <div class="premium-search-item" style="flex: 1;">
                            <select name="ano" class="form-select form-select-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; cursor: pointer; box-shadow: none;">
                                <option value="0">Ano</option>
                                <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo $ano == $y ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="premium-search-divider d-none d-lg-block"></div>
                        
                        <!-- Pesquisa -->
                        <div class="premium-search-item" style="flex: 2;">
                            <input type="text" name="busca" class="form-control form-control-sm border-0 bg-transparent px-0 fw-bold" style="color: #4D1C21; box-shadow: none;" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($busca); ?>">
                        </div>
                        
                        <!-- Botão -->
                        <button type="submit" class="premium-search-btn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="row g-5">
                <!-- Lista de Eventos -->
                <div class="col-lg-8">

                    <!-- Lista de Eventos -->
                    <?php if (!empty($eventos)): ?>
                    <div class="row g-0 justify-content-center" id="agenda-container">
                <?php foreach ($eventos as $evento): 
                    // Extrair componentes da data
                    $data_evento = new DateTime($evento->data_evento);
                    $dia = $data_evento->format('d');
                    $mes = $data_evento->format('M');
                    $ano = $data_evento->format('Y');
                    
                    // Traduzir mês para português
                    $meses_pt = [
                        'Jan' => 'JAN', 'Feb' => 'FEV', 'Mar' => 'MAR',
                        'Apr' => 'ABR', 'May' => 'MAI', 'Jun' => 'JUN',
                        'Jul' => 'JUL', 'Aug' => 'AGO', 'Sep' => 'SET',
                        'Oct' => 'OUT', 'Nov' => 'NOV', 'Dec' => 'DEZ'
                    ];
                    $mes_pt = $meses_pt[$mes] ?? $mes;
                ?>
                <div class="col-12 mb-3"> <!-- mb-1 para diminuir distância entre blocos de eventos -->
                    <div class="agenda-evento-novo row align-items-center" style="padding: 2rem; min-height: 180px;">
                        <!-- Data à esquerda -->
                        <div class="col-lg-3 col-md-4 text-center agenda-data-container">
                            <div class="agenda-data-display">
                                <div class="agenda-dia" style="font-size: 4rem; font-weight: 700; color: #B1A276; line-height: 1; font-family: 'Libre Baskerville', serif;">
                                    <?php echo $dia; ?>
                                </div>
                                <div class="agenda-mes" style="font-size: 1.5rem; font-weight: 600; color: #5B463F; margin-top: -10px; font-family: 'Open Sans', sans-serif;">
                                    <?php echo $mes_pt; ?>
                                </div>
                                <div class="agenda-ano" style="font-size: 1.2rem; font-weight: 400; color: #888; font-family: 'Open Sans', sans-serif;">
                                    <?php echo $ano; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conteúdo à direita -->
                        <div class="col-lg-9 col-md-8 agenda-conteudo-container">
                             <h4 class="mb-2" style="color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 1.15rem; font-weight: 600; line-height: 1.3;">
                                 <a href="evento.php?id=<?php echo $evento->id; ?>" class="linkSublinhado text-decoration-none" style="color: #4D1C21;">
                                    <?php echo htmlspecialchars($evento->titulo); ?>
                                </a>
                            </h4>
                            
                            <?php if (!empty($evento->local_evento)): ?>
                            <p class="mb-2" style="color: #B1A276; font-family: 'Open Sans', sans-serif; font-size: 1rem; font-weight: 500;">
                                <i class="fa fa-map-marker-alt me-2" style="color: #B1A276;"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($evento->descricao)): ?>
                            <p class="texto-conteudo mb-3" style="line-height: 1.6;">
                                <?php echo htmlspecialchars(truncate_text($evento->descricao, 200)); ?>
                            </p>
                            <?php endif; ?>
                            
                            <a href="evento.php?id=<?php echo $evento->id; ?>" class="d-block" style="margin-top: 1rem;">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                    
                    <!-- Loading Spinner for Lazy Load -->
                    <div id="loading-spinner" class="text-center my-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="color: #B1A276 !important;">
                            <span class="visually-hidden">A carregar...</span>
                        </div>
                    </div>

                    <!-- Sentinel for Infinite Scroll (Lazy Load) -->
                    <div id="scroll-sentinel" style="height: 10px; margin: 10px 0;"></div>
                    
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum evento encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros ou volte mais tarde.</p>
                        <a href="agenda.php" class="btn btn-primary mt-3">
                            <i class="fa fa-refresh me-2"></i>Ver Todos os Eventos
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Destaques da Bastonária -->
                <div class="col-lg-4 mt-5 mt-lg-4">
                    <div class="sidebar-widget shadow-sm sticky-top" style="top: 120px; background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                        <div class="mt-3">
                            <?php if (!empty($destaques_bastonaria)): ?>
                                <?php foreach ($destaques_bastonaria as $dest): ?>
                                    <a href="evento.php?id=<?php echo $dest->id; ?>" class="sidebar-link d-flex align-items-center mb-3 text-decoration-none" style="display: flex; align-items: center; padding: 14px 20px; border-radius: 12px; background: #fafafa; color: #555; font-weight: 600; transition: all 0.3s; border: 1px solid transparent;">
                                        <i class="fas fa-star me-3" style="color: var(--primary-gold); margin-right: 15px; width: 20px; text-align: center;"></i>
                                        <span><?php echo htmlspecialchars($dest->titulo); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">Sem eventos em destaque no momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                            </div>
        </div>
    </div>
    <!-- Agenda End -->

<?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Character encoding fix for Portuguese special characters
            function forceCharacterFix() {
                const replacements = {
                    'Janußrio': 'Januário',
                    'Bastonßrio': 'Bastonário',
                    'BasÝlio': 'Basílio',
                    'jurÝdico': 'jurídico',
                    'JurÝdico': 'Jurídico',
                    'Bancßrio': 'Bancário',
                    'C¾digo': 'Código',
                    'experiância': 'experiência',
                    'paÝs': 'país'
                };
                $('body :not(script)').contents().filter(function() {
                    return this.nodeType === 3;
                }).each(function() {
                    let text = this.nodeValue;
                    let changed = false;
                    for (const [noise, fixed] of Object.entries(replacements)) {
                        if (text.indexOf(noise) !== -1) {
                            text = text.split(noise).join(fixed);
                            changed = true;
                        }
                    }
                    if (changed) this.nodeValue = text;
                });
            }

            // Limpar formulário se for um carregamento "limpo" (sem query strings)
            if (!window.location.search) {
                document.getElementById('agendaSearchForm').reset();
            }

            forceCharacterFix();

            // Toggle bio extra on click
            $('.bastonario-card').on('click', function() {
                $(this).find('.bio-extra').slideToggle();
            });

            // Infinite Scroll (Lazy Load) logic
            let page = 1;
            let loading = false;
            let hasMore = <?php echo ($total_paginas > 1) ? 'true' : 'false'; ?>;
            let totalPages = <?php echo $total_paginas; ?>;

            function loadNextPage() {
                loading = true;
                page++;
                $('#loading-spinner').show();

                $.ajax({
                    url: 'agenda.php',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        pagina: page,
                        tipo: '<?php echo $tipo; ?>',
                        busca: '<?php echo $busca; ?>',
                        mes: '<?php echo $mes; ?>',
                        ano: '<?php echo $ano; ?>'
                    },
                    success: function(data) {
                        if (data.trim() === '') {
                            hasMore = false;
                        } else {
                            $('#agenda-container').append(data);
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

            // Use IntersectionObserver for modern scroll detection
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    if (entries[0].isIntersecting && hasMore && !loading) {
                        loadNextPage();
                    }
                }, { rootMargin: '150px', threshold: 0.1 });

                const sentinel = document.getElementById('scroll-sentinel');
                if (sentinel) observer.observe(sentinel);
            } else {
                $(window).scroll(function() {
                    var scrollTop = $(window).scrollTop() || $("html").scrollTop() || $("body").scrollTop();
                    var docHeight = $(document).height();
                    var winHeight = $(window).height();
                    if (scrollTop + winHeight > docHeight - 200) {
                        if (hasMore && !loading) {
                            loadNextPage();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
