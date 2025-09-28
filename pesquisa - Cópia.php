<?php
require_once 'connect.php';
require_once 'includes/functions.php';

// Obter parâmetros de pesquisa
$query = clean_input($_GET['q'] ?? '');
$tipo = clean_input($_GET['tipo'] ?? 'todos');
$categoria = clean_input($_GET['categoria'] ?? '');
$data_inicio = clean_input($_GET['data_inicio'] ?? '');
$data_fim = clean_input($_GET['data_fim'] ?? '');
$pagina = max(1, intval($_GET['page'] ?? 1));
$ajax = isset($_GET['ajax']) && $_GET['ajax'] == '1';
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$resultados = [];
$total_resultados = 0;

if (!empty($query)) {
    try {
        $search_term = '%' . $query . '%';
        $all_results = [];
        
        // Pesquisa em notícias
        if ($tipo == 'todos' || $tipo == 'noticias') {
            $sql = "SELECT 
                    'noticia' as tipo,
                    id,
                    titulo,
                    resumo as descricao,
                    slug,
                    data_publicacao as data,
                    imagem_destaque as imagem,
                    NULL as local_evento
                FROM noticias 
                WHERE ativo = 1 
                AND (titulo LIKE :search1 OR resumo LIKE :search2 OR conteudo LIKE :search3)";
            
            $params = [
                ':search1' => $search_term,
                ':search2' => $search_term,
                ':search3' => $search_term
            ];
            
            if (!empty($categoria)) {
                $sql .= " AND categoria = :categoria";
                $params[':categoria'] = $categoria;
            }
            
            if (!empty($data_inicio) && !empty($data_fim)) {
                $sql .= " AND DATE(data_publicacao) BETWEEN :data_inicio AND :data_fim";
                $params[':data_inicio'] = $data_inicio;
                $params[':data_fim'] = $data_fim;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $all_results = array_merge($all_results, $noticias);
        }
        
        // Pesquisa em agenda
        if ($tipo == 'todos' || $tipo == 'agenda') {
            $sql = "SELECT 
                    'evento' as tipo,
                    id,
                    titulo,
                    descricao,
                    slug,
                    data_evento as data,
                    imagem_destaque as imagem,
                    local_evento
                FROM agenda 
                WHERE ativo = 1 
                AND (titulo LIKE :search1 OR descricao LIKE :search2 OR local_evento LIKE :search3)";
            
            $params = [
                ':search1' => $search_term,
                ':search2' => $search_term,
                ':search3' => $search_term
            ];
            
            if (!empty($data_inicio) && !empty($data_fim)) {
                $sql .= " AND DATE(data_evento) BETWEEN :data_inicio AND :data_fim";
                $params[':data_inicio'] = $data_inicio;
                $params[':data_fim'] = $data_fim;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $all_results = array_merge($all_results, $eventos);
        }
        
        // Pesquisa em advogados
        if ($tipo == 'todos' || $tipo == 'advogados') {
            $sql = "SELECT 
                    'advogado' as tipo,
                    id,
                    nome_completo as titulo,
                    CONCAT('Nº Registro: ', numero_registo, ' - Região: ', regiao) as descricao,
                    numero_registo as slug,
                    data_inscricao as data,
                    foto as imagem,
                    localidade as local_evento
                FROM advogados 
                WHERE status = 'ativo' 
                AND (nome_completo LIKE :search1 OR numero_registo LIKE :search2 OR regiao LIKE :search3)";
            
            $params = [
                ':search1' => $search_term,
                ':search2' => $search_term,
                ':search3' => $search_term
            ];
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $advogados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $all_results = array_merge($all_results, $advogados);
        }
        
        // Ordenar por data
        usort($all_results, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        
        // Total de resultados
        $total_resultados = count($all_results);
        
        // Paginar resultados
        $resultados = array_slice($all_results, $offset, $por_pagina);
        
        // Converter para objetos
        $resultados = array_map(function($item) {
            return (object) $item;
        }, $resultados);
        
    } catch (Exception $e) {
        error_log("Erro na pesquisa: " . $e->getMessage());
    }
}

// Se for requisição AJAX, retornar apenas os resultados
if ($ajax) {
    foreach ($resultados as $resultado) {
        $link = '';
        switch($resultado->tipo) {
            case 'noticia':
                $link = "artigo.php?id={$resultado->id}&slug=" . urlencode($resultado->slug);
                break;
            case 'evento':
                $link = "evento.php?id={$resultado->id}";
                break;
            case 'advogado':
                $link = "advogado-detalhe.php?id={$resultado->id}";
                break;
        }
        
        $badge_color = $resultado->tipo == 'noticia' ? '1' : ($resultado->tipo == 'evento' ? '2' : '3');
        ?>
        <div class="result-item">
            <div class="row">
                <?php if (!empty($resultado->imagem)): ?>
                <div class="col-md-3">
                    <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($resultado->imagem); ?>" 
                         class="img-fluid rounded" alt="<?php echo htmlspecialchars($resultado->titulo); ?>">
                </div>
                <div class="col-md-9">
                <?php else: ?>
                <div class="col-md-12">
                <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h4 class="titulo-resultado">
                            <a href="<?php echo $link; ?>" class="linkSublinhado" style="color:#4D1C21;">
                                <?php echo htmlspecialchars($resultado->titulo); ?>
                            </a>
                        </h4>
                        <span class="badge-tipo bg-color-<?php echo $badge_color; ?> text-white">
                            <?php echo ucfirst($resultado->tipo); ?>
                        </span>
                    </div>
                    
                    <p class="texto-conteudo mb-2">
                        <?php echo htmlspecialchars(truncate_text($resultado->descricao, 200)); ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo format_date_pt($resultado->data); ?>
                            
                            <?php if (!empty($resultado->local_evento)): ?>
                            <span class="ms-3">
                                <i class="fa fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($resultado->local_evento); ?>
                            </span>
                            <?php endif; ?>
                        </small>
                        <a href="<?php echo $link; ?>" class="btn-arrow-sm">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    exit;
}

// Calcular paginação
$total_paginas = ceil($total_resultados / $por_pagina);

$page_title = "Pesquisa";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Pesquisa de Advogados - OAGB</title>
    
    <?php 
    $meta_description = "Resultados de pesquisa para: " . htmlspecialchars($query);
    include 'includes/meta_tags_include.php'; 
    ?>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .texto-conteudo {
            color: #111923;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
        }
        
        .titulo-resultado {
            color: #4D1C21;
            font-family: 'Libre Baskerville', serif;
            font-size: 140%;
        }
        
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .result-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .result-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .badge-tipo {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: 600;
        }
        
        .btn-arrow-sm {
            display: inline-block;
            color: #111923;
            transition: all 0.3s ease;
        }
        
        .btn-arrow-sm:hover {
            transform: translateX(5px);
            color: #c18046;
        }
        
        /* Header PADRÃO COM BACKGROUND */
        .bg-header {
            background: linear-gradient(rgba(9, 30, 62, .7), rgba(9, 30, 62, .7)), url(img/brass-scales-justice-close-up-view.jpg) center center no-repeat;
            background-size: cover;
            margin-bottom: 30px !important;
        }
        
        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .navbar-brand img {
                width: 150px !important;
                max-width: 60% !important;
            }
            
            .navbar-toggler {
                position: relative !important;
                right: auto !important;
                top: auto !important;
                transform: none !important;
            }
            
            .bg-header {
                margin-bottom: 20px !important;
            }
        }
        
        /* Menu dropdowns no mouseover */
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
            
            .navbar-nav .dropdown .dropdown-menu {
                margin-top: 0;
            }
        }
        
        /* Breadcrumbs com separador circular */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "•";
            color: rgba(255,255,255,0.7);
            padding: 0 8px;
            font-size: 8px;
            vertical-align: middle;
        }
        
        .breadcrumb-item a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        /* Botões de ação com cores castanho/dourado */
        .action-buttons {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            background-color: transparent;
            color: #8B6B47;
            border: 1px solid #8B6B47;
            transition: all 0.3s;
            margin: 0 5px;
        }
        
        .action-buttons .btn:hover {
            background-color: #8B6B47;
            color: white;
            transform: translateY(-2px);
        }
        
        .loading-spinner {
            text-align: center;
            padding: 20px;
            display: none;
        }
        
        .loading-spinner.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Rua 15, Bissau, Guiné-Bissau</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+245 955 475 889</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>info@oagb.gw</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Header Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <img src="img/logo3.png" style="width:70%;height:auto;padding-top:5%;" align="center" border="0" alt="OAGB Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link">INÍCIO</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ORDEM</a>
                        <div class="dropdown-menu m-0">
                            <a href="apresentacao-historia.php" class="dropdown-item">Apresentação e História</a>
                            <a href="orgaos-sociais.php" class="dropdown-item">Órgãos Sociais</a>                            
                            <a href="comissoes-especializadas.php" class="dropdown-item">Comissões Especializadas</a>
                            <a href="cooperacao-institucional.php" class="dropdown-item">Cooperação Institucional</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ADVOGADOS</a>
                        <div class="dropdown-menu m-0">
                            <a href="advogados.php" class="dropdown-item">Advogados</a>
                            <a href="pesquisa-advogados.php" class="dropdown-item">Pesquisa de Advogados</a>                            
                            <a href="advogados-inscritos.php" class="dropdown-item">Advogados Inscritos em vigor</a>
                            <a href="pesquisa-estagiarios.php" class="dropdown-item">Pesquisa de Advogados Estagiários</a>                            
                            <a href="estagiarios-inscritos.php" class="dropdown-item">Advogados Estagiários Inscritos em vigor</a>
                            <a href="solicitacao-advogados.php" class="dropdown-item">Solicitação de Advogados</a>                           
                            <a href="inscricao-ordem.php" class="dropdown-item">Inscrição na Ordem</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">PÚBLICO</a>
                        <div class="dropdown-menu m-0">
                            <a href="pareceres-deliberacoes.php" class="dropdown-item">Pareceres e Deliberações</a>
                            <a href="comunicados.php" class="dropdown-item">Comunicados</a>
                            <a href="publicacoes.php" class="dropdown-item">Publicações</a>
                            <a href="orcamento.php" class="dropdown-item">Orçamento</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">COMUNICAÇÃO</a>
                        <div class="dropdown-menu m-0" style="left: auto; right: 0;">
                            <a href="agenda.php" class="dropdown-item">Agenda</a>
                            <a href="noticias.php" class="dropdown-item">Notícias</a>
                            <a href="anuncios.php" class="dropdown-item">Anúncios</a>
                        </div>
                    </div>
                    <a href="contacto.php" class="nav-item nav-link">CONTACTO</a>
                </div>
                <button type="button" class="btn text-primary ms-3" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="fa fa-search"></i>
                </button>
                <div id="" class="">&nbsp;</div>
            </div>
        </nav>

        <!-- Header com Background Image -->
        <div class="container-fluid bg-primary py-5 bg-header">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pesquisa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar & Header End -->

    <!-- Full Screen Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(9, 30, 62, .7);">
                <div class="modal-header border-0">
                    <button type="button" class="btn bg-white btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <form action="pesquisa.php" method="GET" class="input-group" style="max-width: 600px;">
                        <input type="text" name="q" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa" required>
                        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Action Buttons -->
    <div class="container action-buttons">
        <button onclick="window.history.back()" class="btn btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </button>
        <button onclick="window.print()" class="btn btn-sm">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <button onclick="sharePage()" class="btn btn-sm">
            <i class="bi bi-share"></i> Partilhar
        </button>
        <button onclick="translatePage()" class="btn btn-sm">
            <i class="bi bi-translate"></i> Traduzir
        </button>
    </div>

    <!-- Search Results Start -->
    <div class="container-fluid py-3">
        <div class="container">
            <!-- Filtros no topo -->
            <div class="filter-section">
                <h5 class="mb-3" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">Filtros de Pesquisa</h5>
                
                <form method="GET" action="pesquisa.php" id="filter-form">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Pesquisar</label>
                            <input type="text" name="q" class="form-control" placeholder="Digite sua pesquisa..." value="<?php echo htmlspecialchars($query); ?>">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="todos" <?php echo $tipo == 'todos' ? 'selected' : ''; ?>>Todos</option>
                                <option value="noticias" <?php echo $tipo == 'noticias' ? 'selected' : ''; ?>>Notícias</option>
                                <option value="agenda" <?php echo $tipo == 'agenda' ? 'selected' : ''; ?>>Agenda</option>
                                <option value="advogados" <?php echo $tipo == 'advogados' ? 'selected' : ''; ?>>Advogados</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="categoria" class="form-select" <?php echo $tipo == 'agenda' || $tipo == 'advogados' ? 'disabled' : ''; ?>>
                                <option value="">Todas</option>
                                <option value="comunicados" <?php echo $categoria == 'comunicados' ? 'selected' : ''; ?>>Comunicados</option>
                                <option value="formacao" <?php echo $categoria == 'formacao' ? 'selected' : ''; ?>>Formação</option>
                                <option value="eventos" <?php echo $categoria == 'eventos' ? 'selected' : ''; ?>>Eventos</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control" value="<?php echo htmlspecialchars($data_inicio); ?>">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control" value="<?php echo htmlspecialchars($data_fim); ?>">
                        </div>
                        
                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <div class="d-grid gap-2 w-100">
                                <button type="submit" class="btn bg-color-1 text-white">Filtrar</button>
                                <a href="pesquisa.php" class="btn bg-color-4 text-white">Limpar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Resultados -->
            <?php if (!empty($query)): ?>
                <div class="mb-4">
                    <h3 style="color:#5B463F; font-family: 'Libre Baskerville', serif;">
                        Resultados para: <span class="text-primary">"<?php echo htmlspecialchars($query); ?>"</span>
                    </h3>
                    <p class="text-muted">Encontrados <?php echo $total_resultados; ?> resultados</p>
                </div>
                
                <div id="results-container">
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $resultado): ?>
                            <div class="result-item">
                                <div class="row">
                                    <?php if (!empty($resultado->imagem)): ?>
                                    <div class="col-md-3">
                                        <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($resultado->imagem); ?>" 
                                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($resultado->titulo); ?>">
                                    </div>
                                    <div class="col-md-9">
                                    <?php else: ?>
                                    <div class="col-md-12">
                                    <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="titulo-resultado">
                                                <?php 
                                                $link = '';
                                                switch($resultado->tipo) {
                                                    case 'noticia':
                                                        $link = "artigo.php?id={$resultado->id}&slug=" . urlencode($resultado->slug);
                                                        break;
                                                    case 'evento':
                                                        $link = "evento.php?id={$resultado->id}";
                                                        break;
                                                    case 'advogado':
                                                        $link = "advogado-detalhe.php?id={$resultado->id}";
                                                        break;
                                                }
                                                ?>
                                                <a href="<?php echo $link; ?>" class="linkSublinhado" style="color:#4D1C21;">
                                                    <?php echo htmlspecialchars($resultado->titulo); ?>
                                                </a>
                                            </h4>
                                            <span class="badge-tipo bg-color-<?php echo $resultado->tipo == 'noticia' ? '1' : ($resultado->tipo == 'evento' ? '2' : '3'); ?> text-white">
                                                <?php echo ucfirst($resultado->tipo); ?>
                                            </span>
                                        </div>
                                        
                                        <p class="texto-conteudo mb-2">
                                            <?php echo htmlspecialchars(truncate_text($resultado->descricao, 200)); ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?php echo format_date_pt($resultado->data); ?>
                                                
                                                <?php if (!empty($resultado->local_evento)): ?>
                                                <span class="ms-3">
                                                    <i class="fa fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($resultado->local_evento); ?>
                                                </span>
                                                <?php endif; ?>
                                            </small>
                                            <a href="<?php echo $link; ?>" class="btn-arrow-sm">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum resultado encontrado</h5>
                            <p class="text-muted">Tente usar palavras-chave diferentes ou ajustar os filtros de pesquisa.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Loading spinner for infinite scroll -->
                <div class="loading-spinner" id="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Digite algo para pesquisar</h5>
                    <p class="text-muted">Use o campo de pesquisa para encontrar notícias, eventos ou advogados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Search Results End -->

    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg bg-color-1 text-white btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <script>
    // Controle de filtro de categoria
    document.querySelector('select[name="tipo"]').addEventListener('change', function() {
        const categoriaSelect = document.querySelector('select[name="categoria"]');
        if (this.value === 'noticias' || this.value === 'todos') {
            categoriaSelect.disabled = false;
        } else {
            categoriaSelect.disabled = true;
            categoriaSelect.value = '';
        }
    });
    
    // Infinite scroll
    <?php if (!empty($query) && $total_paginas > 1): ?>
    let currentPage = 1;
    let isLoading = false;
    let hasMore = <?php echo $pagina < $total_paginas ? 'true' : 'false'; ?>;
    
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMore) return;
        
        const scrollPosition = window.innerHeight + window.scrollY;
        const documentHeight = document.documentElement.offsetHeight;
        
        if (scrollPosition >= documentHeight - 200) {
            loadMoreResults();
        }
    });
    
    function loadMoreResults() {
        isLoading = true;
        currentPage++;
        
        document.getElementById('loading-spinner').classList.add('active');
        
        const params = new URLSearchParams(window.location.search);
        params.set('page', currentPage);
        params.set('ajax', '1');
        
        fetch('pesquisa.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                if (html.trim()) {
                    document.getElementById('results-container').insertAdjacentHTML('beforeend', html);
                    
                    if (currentPage >= <?php echo $total_paginas; ?>) {
                        hasMore = false;
                    }
                } else {
                    hasMore = false;
                }
                
                document.getElementById('loading-spinner').classList.remove('active');
                isLoading = false;
            })
            .catch(error => {
                console.error('Erro ao carregar mais resultados:', error);
                document.getElementById('loading-spinner').classList.remove('active');
                isLoading = false;
            });
    }
    <?php endif; ?>
    
    // Função compartilhar
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: 'Pesquisa OAGB',
                text: 'Resultados de pesquisa',
                url: window.location.href
            });
        } else {
            alert('Use o link para compartilhar: ' + window.location.href);
        }
    }
    
    // Função traduzir
    function translatePage() {
        window.open('https://translate.google.com/translate?u=' + encodeURIComponent(window.location.href));
    }
    </script>
</body>
</html>