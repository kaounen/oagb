<?php
// Minimal working version of index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to include functions - if it fails, define basic ones
try {
    include_once 'includes/functions.php';
} catch (Exception $e) {
    // Define essential functions inline
    function truncate_text($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) return $text;
        $truncated = substr($text, 0, $length);
        $last_space = strrpos($truncated, ' ');
        if ($last_space !== false) {
            $truncated = substr($truncated, 0, $last_space);
        }
        return $truncated . $suffix;
    }
    
    function format_date_pt($date) {
        if (empty($date)) return '';
        $timestamp = is_string($date) ? strtotime($date) : $date;
        return date('d/m/Y', $timestamp);
    }
}

// Try database connection with fallback
$pdo = null;
$noticias_destaque = [];
$proximos_eventos = [];

try {
    include 'connect.php';
    
    // Get news
    $stmt = $pdo->prepare("SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 3");
    $stmt->execute();
    $noticias_destaque = $stmt->fetchAll();
    
    // Get events
    $stmt = $pdo->prepare("SELECT * FROM agenda ORDER BY data_evento DESC LIMIT 2");
    $stmt->execute();
    $proximos_eventos = $stmt->fetchAll();
    
} catch (Exception $e) {
    // Continue with empty arrays
}

// Default carousel slides
$carousel_slides = [
    (object)[
        'titulo' => 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau',
        'subtitulo' => 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito.',
        'imagem' => 'img/brass-scales-justice-close-up-view.jpg',
        'link_texto' => 'Saiba mais',
        'link_url' => 'apresentacao-historia.php'
    ],
    (object)[
        'titulo' => 'Cadastro Nacional de Advogados',
        'subtitulo' => 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB.',
        'imagem' => 'img/close-up-scales-justice-azul.jpg',
        'link_texto' => 'Pesquisar Advogados',
        'link_url' => 'pesquisa-advogados.php'
    ]
];

$page_title = "Início";
$meta_description = "Site oficial da Ordem dos Advogados da Guiné-Bissau - OAGB";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?> - OAGB</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo $meta_description; ?>" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

        <?php include 'includes/topbar.php'; ?>

    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <?php 
        try {
            include 'includes/navbar.php'; 
        } catch (Exception $e) {
            echo '<nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
                    <a href="index.php" class="navbar-brand p-0">
                        <img src="img/logo3.png" style="width:200px;" alt="OAGB">
                    </a>
                  </nav>';
        }
        ?>

        <!-- Carousel -->
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php 
                $first = true;
                foreach ($carousel_slides as $slide): 
                ?>
                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <img class="w-100" src="<?php echo htmlspecialchars($slide->imagem); ?>" alt="Slide" style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="text-white mb-4"><?php echo htmlspecialchars($slide->titulo); ?></h1>
                            <h5 class="text-white mb-3"><?php echo htmlspecialchars($slide->subtitulo); ?></h5>
                            <a href="<?php echo htmlspecialchars($slide->link_url); ?>" class="btn btn-outline-light py-md-3 px-md-5">
                                <?php echo htmlspecialchars($slide->link_texto); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                $first = false;
                endforeach; 
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    <!-- Navbar & Carousel End -->

    <!-- News Section -->
    <?php if (!empty($noticias_destaque)): ?>
    <div class="container-fluid py-5">
        <div class="container py-4">
            <div class="text-center pb-3 mb-5">
                <h5 class="text-primary text-uppercase">Artigos recentes</h5>
                <h1 class="mb-0">Últimas notícias</h1>
            </div>
            <div class="row g-5">
                <?php foreach ($noticias_destaque as $noticia): ?>
                <div class="col-lg-4">
                    <div class="blog-item bg-light rounded overflow-hidden">
                        <div class="p-4">
                            <h4 class="mb-3">
                                <a href="artigo.php?id=<?php echo $noticia->id; ?>"><?php echo htmlspecialchars($noticia->titulo); ?></a>
                            </h4>
                            <small><?php echo format_date_pt($noticia->data_publicacao); ?></small>
                            <p class="mt-3"><?php echo htmlspecialchars(truncate_text($noticia->resumo ?? '', 120)); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php 
    try {
        include 'includes/footer.php'; 
    } catch (Exception $e) {
        echo '<footer class="bg-dark text-light py-5">
                <div class="container text-center">
                    <p>&copy; 2024 OAGB - Todos os direitos reservados</p>
                </div>
              </footer>';
    }
    ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
