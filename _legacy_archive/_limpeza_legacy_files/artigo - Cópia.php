<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

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
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $meta_image; ?>">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="article">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
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
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #999;
        }
        
        /* Tags */
        .article-tags {
            margin-top: 2rem;
        }
        
        .tag-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            margin: 0.2rem;
            background: #f0f0f0;
            color: #666;
            border-radius: 20px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .tag-badge:hover {
            background: #c18046;
            color: white;
        }
        
        /* Print Styles */
        @media print {
            .navbar, .footer, .share-buttons, .related-articles, .back-to-top {
                display: none !important;
            }
            
            .article-content {
                font-size: 12pt;
            }
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .article-content {
                font-size: 1rem;
            }
            
            .article-meta {
                gap: 1rem;
            }
            
            .share-buttons .btn {
                margin-bottom: 0.5rem;
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
    </div>
    <!-- Navbar End -->

    <!-- Article Content Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                    <li class="breadcrumb-item"><a href="noticias.php">Notícias</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(truncate_text($noticia->titulo, 50)); ?></li>
                </ol>
            </nav>
            
            <div class="row g-5">
                <!-- Article Main Content -->
                <div class="col-lg-8">
                    <!-- Article Header -->
                    <div class="mb-4">
                        <h1 class="mb-4" style="color:#4D1C21; font-family: 'Libre Baskerville', serif; font-size: 2.5rem; line-height: 1.3;">
                            <?php echo htmlspecialchars($noticia->titulo); ?>
                        </h1>
                        
                        <!-- Article Meta Information -->
                        <div class="article-meta">
                            <div class="article-meta-item">
                                <i class="far fa-calendar-alt text-primary"></i>
                                <span><?php echo format_date_pt($noticia->data_publicacao); ?></span>
                            </div>
                            <?php if (!empty($noticia->autor)): ?>
                            <div class="article-meta-item">
                                <i class="far fa-user text-primary"></i>
                                <span><?php echo htmlspecialchars($noticia->autor); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="article-meta-item">
                                <i class="far fa-eye text-primary"></i>
                                <span><?php echo number_format($noticia->visualizacoes); ?> visualizações</span>
                            </div>
                            <?php if (!empty($noticia->categoria)): ?>
                            <div class="article-meta-item">
                                <i class="far fa-folder text-primary"></i>
                                <span><?php echo htmlspecialchars(ucfirst($noticia->categoria)); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Featured Image -->
                        <?php if (!empty($noticia->imagem_destaque)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($noticia->imagem_destaque); ?>" 
                             alt="<?php echo htmlspecialchars($noticia->titulo); ?>" 
                             class="img-fluid rounded mb-4 w-100">
                        <?php endif; ?>
                        
                        <!-- Article Summary/Lead -->
                        <?php if (!empty($noticia->resumo)): ?>
                        <div class="lead mb-4" style="font-size: 1.25rem; color: #666; font-style: italic;">
                            <?php echo nl2br(htmlspecialchars($noticia->resumo)); ?>
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
                    
                    <!-- Share Buttons -->
                    <div class="share-buttons">
                        <h5 class="mb-3">Partilhar:</h5>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn btn-primary">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($noticia->titulo); ?>" 
                           target="_blank" class="btn btn-info text-white">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($noticia->titulo . ' - ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:?subject=<?php echo urlencode($noticia->titulo); ?>&body=<?php echo urlencode('Veja esta notícia: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           class="btn btn-secondary">
                            <i class="far fa-envelope"></i> Email
                        </a>
                        <button onclick="window.print();" class="btn btn-dark">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Search Widget -->
                    <div class="mb-5">
                        <div class="bg-light rounded p-4">
                            <h4 class="mb-4" style="font-family: 'Libre Baskerville', serif;">Pesquisar</h4>
                            <form action="pesquisa.php" method="GET">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control p-3" placeholder="Palavra-chave..." required>
                                    <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Categories Widget -->
                    <div class="mb-5">
                        <div class="bg-light rounded p-4">
                            <h4 class="mb-4" style="font-family: 'Libre Baskerville', serif;">Categorias</h4>
                            <div class="d-flex flex-column">
                                <a href="noticias.php?categoria=comunicados" class="mb-2">
                                    <i class="bi bi-arrow-right text-primary me-2"></i>Comunicados
                                </a>
                                <a href="noticias.php?categoria=formacao" class="mb-2">
                                    <i class="bi bi-arrow-right text-primary me-2"></i>Formação
                                </a>
                                <a href="noticias.php?categoria=eventos" class="mb-2">
                                    <i class="bi bi-arrow-right text-primary me-2"></i>Eventos
                                </a>
                                <a href="noticias.php?categoria=institucional" class="mb-2">
                                    <i class="bi bi-arrow-right text-primary me-2"></i>Institucional
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Newsletter Widget -->
                    <div class="mb-5">
                        <div class="bg-primary rounded p-4">
                            <h4 class="mb-4 text-white" style="font-family: 'Libre Baskerville', serif;">Newsletter</h4>
                            <p class="text-white mb-3">Subscreva a nossa newsletter para receber as últimas notícias e atualizações.</p>
                            <form action="subscricao.php" method="POST">
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control p-3" placeholder="Seu email" required>
                                    <button class="btn btn-dark px-4" type="submit">Subscrever</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Articles -->
            <?php if (!empty($noticias_relacionadas)): ?>
            <div class="related-articles mt-5">
                <h3 class="mb-4" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">Notícias Relacionadas</h3>
                <div class="row g-4">
                    <?php foreach ($noticias_relacionadas as $relacionada): ?>
                    <div class="col-lg-4">
                        <div class="related-article">
                            <?php if (!empty($relacionada->imagem_destaque)): ?>
                            <img src="uploads/<?php echo htmlspecialchars($relacionada->imagem_destaque); ?>" 
                                 alt="<?php echo htmlspecialchars($relacionada->titulo); ?>">
                            <?php else: ?>
                            <img src="img/Asset 7-100.jpg" alt="<?php echo htmlspecialchars($relacionada->titulo); ?>">
                            <?php endif; ?>
                            <div class="p-4">
                                <h5 class="mb-3">
                                    <a href="artigo.php?id=<?php echo $relacionada->id; ?>&slug=<?php echo urlencode($relacionada->slug); ?>" 
                                       class="text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($relacionada->titulo); ?>
                                    </a>
                                </h5>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars(truncate_text($relacionada->resumo, 100)); ?></p>
                                <small class="text-muted">
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
