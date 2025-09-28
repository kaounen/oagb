<?php
require_once 'connect.php';

// Determinar tipo de documento baseado na URL
$tipos_validos = [
    'pareceres-deliberacoes' => ['parecer', 'deliberacao'],
    'comunicados' => ['comunicado'],
    'publicacoes' => ['publicacao'],
    'orcamento' => ['orcamento']
];

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$tipo_filtro = '';
$tipos_query = [];

if (isset($tipos_validos[$current_page])) {
    $tipos_query = $tipos_validos[$current_page];
    $tipo_filtro = $current_page;
}

// Buscar documentos
$sql = "SELECT * FROM documentos_publicos WHERE ativo = 1";
$params = [];

if (!empty($tipos_query)) {
    $placeholders = str_repeat('?,', count($tipos_query) - 1) . '?';
    $sql .= " AND tipo IN ($placeholders)";
    $params = $tipos_query;
}

$sql .= " ORDER BY data_documento DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documentos = $stmt->fetchAll();

// Títulos das páginas
$page_titles = [
    'pareceres-deliberacoes' => 'Pareceres e Deliberações',
    'comunicados' => 'Comunicados',
    'publicacoes' => 'Publicações',
    'orcamento' => 'Orçamento'
];

$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Documentos Públicos';
$breadcrumb = "Público";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title); ?> - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo htmlspecialchars($page_title); ?>, OAGB, Documentos Públicos" name="keywords">
    <meta content="<?php echo htmlspecialchars($page_title); ?> da Ordem dos Advogados da Guiné-Bissau" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f fw-normal"></i></a>
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
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo htmlspecialchars($page_title); ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo htmlspecialchars($page_title); ?></a>
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
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa">
                        <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Documents Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Header Info -->
                    <div class="mb-5">
                        <h1 class="mb-3" style="color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: bold;">
                            <?php echo htmlspecialchars($page_title); ?>
                        </h1>
                        <p class="lead" style="font-family: 'Open Sans';">
                            Consulte os documentos oficiais da Ordem dos Advogados da Guiné-Bissau.
                        </p>
                    </div>

                    <!-- Documents List -->
                    <?php if ($documentos): ?>
                    <div class="row g-4">
                        <?php foreach ($documentos as $documento): ?>
                        <div class="col-12 wow slideInUp" data-wow-delay="0.1s">
                            <div class="bg-white rounded shadow-sm p-4 h-100">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <div class="bg-primary text-white rounded p-3">
                                            <i class="fas fa-file-pdf fa-2x"></i>
                                        </div>
                                        <span class="badge bg-secondary mt-2">
                                            <?php echo ucfirst($documento->tipo); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-7">
                                        <h5 class="mb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                            <?php echo htmlspecialchars($documento->titulo); ?>
                                        </h5>
                                        <?php if ($documento->descricao): ?>
                                        <p class="text-muted mb-2" style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars(truncate_text($documento->descricao, 120)); ?>
                                        </p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center">
                                            <?php if ($documento->data_documento): ?>
                                            <small class="text-muted me-3" style="font-family: 'Open Sans';">
                                                <i class="bi bi-calendar me-1"></i>
                                                <?php echo format_date($documento->data_documento); ?>
                                            </small>
                                            <?php endif; ?>
                                            <small class="text-muted" style="font-family: 'Open Sans';">
                                                <i class="bi bi-clock me-1"></i>
                                                Publicado em <?php echo format_date($documento->created_at); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <?php if ($documento->arquivo): ?>
                                        <a href="img/documentos/<?php echo $documento->arquivo; ?>" 
                                           class="btn btn-primary btn-sm mb-2" target="_blank">
                                            <i class="bi bi-download me-1"></i>Baixar PDF
                                        </a>
                                        <?php endif; ?>
                                        <div>
                                            <small class="text-muted" style="font-family: 'Open Sans';">
                                                <i class="bi bi-eye me-1"></i>Visualizar online
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php else: ?>
                    <!-- No Documents -->
                    <div class="text-center py-5">
                        <div class="bg-light rounded p-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h4 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                Nenhum documento disponível
                            </h4>
                            <p class="text-muted mb-4" style="font-family: 'Open Sans';">
                                Não há documentos disponíveis nesta categoria no momento.
                            </p>
                            <a href="contacto.php" class="btn btn-primary">
                                <i class="bi bi-envelope me-2"></i>Solicitar Informação
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Categories -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Categorias</h3>
                        </div>
                        <div class="link-animated d-flex flex-column justify-content-start">
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($current_page == 'pareceres-deliberacoes') ? 'bg-primary text-white' : ''; ?>" 
                               href="pareceres-deliberacoes.php">
                                <i class="bi bi-arrow-right me-2"></i>Pareceres e Deliberações
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($current_page == 'comunicados') ? 'bg-primary text-white' : ''; ?>" 
                               href="comunicados.php">
                                <i class="bi bi-arrow-right me-2"></i>Comunicados
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($current_page == 'publicacoes') ? 'bg-primary text-white' : ''; ?>" 
                               href="publicacoes.php">
                                <i class="bi bi-arrow-right me-2"></i>Publicações
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($current_page == 'orcamento') ? 'bg-primary text-white' : ''; ?>" 
                               href="orcamento.php">
                                <i class="bi bi-arrow-right me-2"></i>Orçamento
                            </a>
                        </div>
                    </div>

                    <!-- Document Info -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-light rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Formatos Disponíveis</h5>
                            <ul class="list-unstyled" style="font-family: 'Open Sans';">
                                <li class="mb-2">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>PDF para download
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-eye text-primary me-2"></i>Visualização online
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-mobile-alt text-success me-2"></i>Compatível com mobile
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Help -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-primary text-white rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Precisa de Ajuda?</h5>
                            <p class="mb-3" style="font-family: 'Open Sans';">
                                Se não encontrar o documento que procura ou tiver dificuldades, contacte-nos.
                            </p>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-phone me-2"></i>
                                <span>+245 955 475 889</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-envelope me-2"></i>
                                <span>info@oagb.gw</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Documentos Recentes</h3>
                        </div>
                        
                        <?php
                        // Buscar documentos recentes de todas as categorias
                        $stmt = $pdo->prepare("
                            SELECT titulo, tipo, created_at 
                            FROM documentos_publicos 
                            WHERE ativo = 1 
                            ORDER BY created_at DESC 
                            LIMIT 5
                        ");
                        $stmt->execute();
                        $documentos_recentes = $stmt->fetchAll();
                        
                        foreach ($documentos_recentes as $doc_recente): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-light rounded p-2 me-3">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" style="font-family: 'Libre Baskerville';">
                                    <?php echo truncate_text($doc_recente->titulo, 40); ?>
                                </h6>
                                <small class="text-muted" style="font-family: 'Open Sans';">
                                    <?php echo ucfirst($doc_recente->tipo); ?> • 
                                    <?php echo format_date($doc_recente->created_at); ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Documents End -->

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

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
</body>
</html>