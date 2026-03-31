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
    
    // Buscar notícias em destaque para carousel
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
        LIMIT 5
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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .news-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .news-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .news-card .news-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .news-card .news-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .news-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #c18046;
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        
        .news-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .list-view .news-card {
            flex-direction: row;
            align-items: center;
        }
        
        .list-view .news-card .news-image {
            width: 300px;
            height: 200px;
            flex-shrink: 0;
        }
        
        .carousel-news {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 3rem;
            color: white;
        }
        
        .carousel-news h2 {
            color: white;
            font-family: 'Libre Baskerville', serif;
        }
        
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .tag-cloud a {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            background: #f0f0f0;
            color: #666;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .tag-cloud a:hover {
            background: #c18046;
            color: white;
            transform: translateY(-2px);
        }
        
        .tag-cloud a.active {
            background: #c18046;
            color: white;
        }
        
        .sidebar-widget {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .sidebar-widget h5 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #c18046;
        }
        
        .popular-news-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .popular-news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .popular-news-item img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .popular-news-item .popular-news-content {
            flex: 1;
        }
        
        .popular-news-item h6 {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        
        .popular-news-item .views {
            font-size: 0.75rem;
            color: #999;
        }
        
        .filter-bar {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .view-switcher {
            display: flex;
            gap: 0.5rem;
        }
        
        .view-switcher button {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .view-switcher button.active {
            background: #c18046;
            color: white;
            border-color: #c18046;
        }
        
        .archive-list {
            list-style: none;
            padding: 0;
        }
        
        .archive-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .archive-list li:last-child {
            border-bottom: none;
        }
        
        .archive-list a {
            color: #666;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .archive-list a:hover {
            color: #c18046;
        }
        
        .archive-list .count {
            background: #f0f0f0;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .list-view .news-card {
                flex-direction: column;
            }
            
            .list-view .news-card .news-image {
                width: 100%;
                height: 200px;
            }
            
            .carousel-news {
                padding: 2rem 1.5rem;
            }
            
            .filter-bar {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

        <?php include 'includes/topbar.php'; ?>

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Notícias</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Notícias</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- News Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Carousel de Destaques -->
            <?php if (!empty($noticias_destaque)): ?>
            <div class="carousel-news">
                <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php 
                        $first = true;
                        foreach ($noticias_destaque as $destaque): 
                        ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h2 class="mb-3"><?php echo htmlspecialchars($destaque->titulo); ?></h2>
                                    <p class="mb-4"><?php echo htmlspecialchars(truncate_text($destaque->resumo, 200)); ?></p>
                                    <a href="artigo.php?id=<?php echo $destaque->id; ?>&slug=<?php echo urlencode($destaque->slug); ?>" 
                                       class="btn btn-light btn-lg">
                                        Ler Mais <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <?php if (!empty($destaque->imagem_destaque)): ?>
                                <div class="col-lg-4">
                                    <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($destaque->imagem_destaque); ?>" 
                                         alt="<?php echo htmlspecialchars($destaque->titulo); ?>" 
                                         class="img-fluid rounded">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                        $first = false;
                        endforeach; 
                        ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Barra de Filtros -->
            <div class="filter-bar">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <form method="GET" action="noticias.php" class="row g-2">
                            <div class="col-md-4">
                                <select name="categoria" class="form-select" onchange="this.form.submit()">
                                    <option value="todas">Todas as Categorias</option>
                                    <?php foreach ($categorias_disponiveis as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat->categoria); ?>" 
                                            <?php echo $categoria == $cat->categoria ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($cat->categoria); ?> (<?php echo $cat->total; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="busca" class="form-control" 
                                       placeholder="Pesquisar notícias..." 
                                       value="<?php echo htmlspecialchars($busca); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search me-2"></i>Pesquisar
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="view-switcher">
                            <button class="<?php echo $view_mode == 'grid' ? 'active' : ''; ?>" 
                                    onclick="changeView('grid')">
                                <i class="fa fa-th"></i> Grelha
                            </button>
                            <button class="<?php echo $view_mode == 'list' ? 'active' : ''; ?>" 
                                    onclick="changeView('list')">
                                <i class="fa fa-list"></i> Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-5">
                <!-- Lista de Notícias -->
                <div class="col-lg-8">
                    <?php if (!empty($noticias)): ?>
                    <div class="row g-4 <?php echo $view_mode == 'list' ? 'list-view' : ''; ?>">
                        <?php foreach ($noticias as $noticia): ?>
                        <div class="<?php echo $view_mode == 'grid' ? 'col-md-6' : 'col-12'; ?>">
                            <div class="news-card">
                                <?php if (!empty($noticia->imagem_destaque)): ?>
                                <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($noticia->imagem_destaque); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia->titulo); ?>" 
                                     class="news-image">
                                <?php else: ?>
                                <div class="news-image"></div>
                                <?php endif; ?>
                                
                                <div class="news-body">
                                    <?php if (!empty($noticia->categoria)): ?>
                                    <span class="news-category"><?php echo htmlspecialchars($noticia->categoria); ?></span>
                                    <?php endif; ?>
                                    
                                    <h5 class="mb-3">
                                        <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" 
                                           class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($noticia->titulo); ?>
                                        </a>
                                    </h5>
                                    
                                    <div class="news-meta">
                                        <span><i class="far fa-calendar me-1"></i><?php echo format_date_pt($noticia->data_publicacao); ?></span>
                                        <?php if (!empty($noticia->autor)): ?>
                                        <span><i class="far fa-user me-1"></i><?php echo htmlspecialchars($noticia->autor); ?></span>
                                        <?php endif; ?>
                                        <span><i class="far fa-eye me-1"></i><?php echo number_format($noticia->visualizacoes); ?></span>
                                    </div>
                                    
                                    <p class="text-muted mb-3">
                                        <?php echo htmlspecialchars(truncate_text($noticia->resumo, 150)); ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            Ler Mais <i class="fa fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($total_paginas > 1): ?>
                    <nav aria-label="Paginação" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])); ?>">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])); ?>">
                                    Próximo <i class="bi bi-arrow-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma notícia encontrada</h5>
                        <p class="text-muted">Tente ajustar os filtros ou volte mais tarde.</p>
                        <a href="noticias.php" class="btn btn-primary mt-3">
                            <i class="fa fa-refresh me-2"></i>Ver Todas as Notícias
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Pesquisa -->
                    <div class="sidebar-widget">
                        <h5>Pesquisar</h5>
                        <form action="noticias.php" method="GET">
                            <div class="input-group">
                                <input type="text" name="busca" class="form-control" 
                                       placeholder="Pesquisar..." value="<?php echo htmlspecialchars($busca); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Mais Lidas -->
                    <?php if (!empty($mais_lidas)): ?>
                    <div class="sidebar-widget">
                        <h5>Mais Lidas</h5>
                        <?php foreach ($mais_lidas as $lida): ?>
                        <div class="popular-news-item">
                            <?php if (!empty($lida->imagem_destaque)): ?>
                            <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($lida->imagem_destaque); ?>" 
                                 alt="<?php echo htmlspecialchars($lida->titulo); ?>">
                            <?php else: ?>
                            <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 8px;"></div>
                            <?php endif; ?>
                            <div class="popular-news-content">
                                <h6>
                                    <a href="artigo.php?id=<?php echo $lida->id; ?>&slug=<?php echo urlencode($lida->slug); ?>" 
                                       class="text-dark text-decoration-none">
                                        <?php echo htmlspecialchars(truncate_text($lida->titulo, 60)); ?>
                                    </a>
                                </h6>
                                <span class="views">
                                    <i class="far fa-eye me-1"></i><?php echo number_format($lida->visualizacoes); ?> visualizações
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Tags Populares -->
                    <?php if (!empty($tags_populares)): ?>
                    <div class="sidebar-widget">
                        <h5>Tags Populares</h5>
                        <div class="tag-cloud">
                            <?php foreach ($tags_populares as $tag_name => $count): ?>
                            <a href="?tag=<?php echo urlencode($tag_name); ?>" 
                               class="<?php echo $tag == $tag_name ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($tag_name); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Arquivo -->
                    <?php if (!empty($arquivo_anos)): ?>
                    <div class="sidebar-widget">
                        <h5>Arquivo</h5>
                        <ul class="archive-list">
                            <?php foreach ($arquivo_anos as $arquivo): ?>
                            <li>
                                <a href="?ano=<?php echo $arquivo->ano; ?>">
                                    <span><?php echo $arquivo->ano; ?></span>
                                    <span class="count"><?php echo $arquivo->total; ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Newsletter -->
                    <div class="sidebar-widget bg-primary text-white">
                        <h5 class="text-white">Newsletter</h5>
                        <p class="mb-3">Subscreva para receber as últimas notícias diretamente no seu email.</p>
                        <form action="subscricao.php" method="POST">
                            <input type="email" name="email" class="form-control mb-3" 
                                   placeholder="Seu email" required>
                            <button class="btn btn-light w-100" type="submit">
                                <i class="fa fa-paper-plane me-2"></i>Subscrever
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- News End -->

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
