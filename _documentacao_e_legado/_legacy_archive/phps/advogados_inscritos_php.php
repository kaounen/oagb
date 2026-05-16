<?php
require_once 'connect.php';

// Letra selecionada (padrão: A)
$letra_atual = isset($_GET['letra']) ? strtoupper(sanitize($_GET['letra'])) : 'A';

// Validar letra
if (!preg_match('/^[A-Z]$/', $letra_atual)) {
    $letra_atual = 'A';
}

// Buscar advogados da letra selecionada
$stmt = $pdo->prepare("
    SELECT numero_registo, nome_completo, regiao, localidade, telefone, email, data_inscricao 
    FROM advogados 
    WHERE status = 'ativo' AND nome_completo LIKE ? 
    ORDER BY nome_completo ASC 
    LIMIT 100
");
$stmt->execute([$letra_atual . '%']);
$advogados = $stmt->fetchAll();

// Contar advogados por letra para mostrar quantidades
$alfabeto = range('A', 'Z');
$contagem_por_letra = [];

foreach ($alfabeto as $letra) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo' AND nome_completo LIKE ?");
    $stmt->execute([$letra . '%']);
    $contagem_por_letra[$letra] = $stmt->fetch()->total;
}

// Total geral
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
$stmt->execute();
$total_advogados = $stmt->fetch()->total;

$page_title = "Advogados Inscritos em Vigor";
$breadcrumb = "Advogados";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Advogados Inscritos - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Advogados Inscritos OAGB, Lista Advogados Guinea-Bissau" name="keywords">
    <meta content="Lista completa de advogados inscritos na Ordem dos Advogados da Guiné-Bissau" name="description">

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
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo $page_title; ?></a>
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

    <!-- Alphabet Navigation & List Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <!-- Header Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                    Advogados Inscritos - Letra <?php echo $letra_atual; ?>
                                </h3>
                                <p class="mb-0" style="font-family: 'Open Sans';">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    Mostrando <?php echo count($advogados); ?> advogado(s) da letra <?php echo $letra_atual; ?> de <?php echo $total_advogados; ?> total
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="pesquisa-advogados.php" class="btn btn-primary">
                                    <i class="fa fa-search me-2"></i>Pesquisa Avançada
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alphabet Navigation -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="bg-white rounded shadow-sm p-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Navegação Alfabética</h5>
                        <div class="alphabet-nav">
                            <?php foreach ($alfabeto as $letra): ?>
                                <a href="?letra=<?php echo $letra; ?>" 
                                   class="btn <?php echo ($letra == $letra_atual) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm me-2 mb-2"
                                   style="min-width: 45px;">
                                    <?php echo $letra; ?>
                                    <?php if ($contagem_por_letra[$letra] > 0): ?>
                                        <span class="badge bg-secondary ms-1"><?php echo $contagem_por_letra[$letra]; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lawyers List -->
            <?php if (count($advogados) > 0): ?>
            <div class="row g-4">
                <?php foreach ($advogados as $advogado): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0 wow slideInUp" data-wow-delay="0.1s">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                        <?php echo htmlspecialchars($advogado->nome_completo); ?>
                                    </h5>
                                    <small class="text-muted" style="font-family: 'Open Sans';">
                                        <i class="bi bi-award me-1"></i>Registo: <?php echo htmlspecialchars($advogado->numero_registo); ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row text-sm">
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->regiao); ?>
                                            <?php if ($advogado->localidade): ?>
                                                - <?php echo htmlspecialchars($advogado->localidade); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if ($advogado->telefone): ?>
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->telefone); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($advogado->email): ?>
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-envelope text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->email); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <i class="bi bi-calendar text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            Inscrito em <?php echo format_date($advogado->data_inscricao); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mb-3">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Em Situação Regular
                                </span>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <?php if ($advogado->telefone): ?>
                                <div class="col-6">
                                    <a href="tel:<?php echo $advogado->telefone; ?>" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-telephone me-1"></i>Ligar
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if ($advogado->email): ?>
                                <div class="col-6">
                                    <a href="mailto:<?php echo $advogado->email; ?>" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Navigation Info -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-light rounded p-4 text-center">
                        <p class="mb-2" style="font-family: 'Open Sans';">
                            <strong>Mostrando <?php echo count($advogados); ?> advogado(s) da letra <?php echo $letra_atual; ?></strong>
                        </p>
                        <p class="mb-0 text-muted" style="font-family: 'Open Sans';">
                            Use a navegação alfabética acima para ver advogados de outras letras ou 
                            <a href="pesquisa-advogados.php">faça uma pesquisa específica</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <!-- No Results -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="bg-light rounded p-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                Nenhum advogado encontrado
                            </h4>
                            <p class="text-muted mb-4" style="font-family: 'Open Sans';">
                                Não há advogados inscritos cujo nome comece com a letra <strong><?php echo $letra_atual; ?></strong>.
                            </p>
                            <div>
                                <a href="?letra=A" class="btn btn-primary me-2">
                                    <i class="bi bi-arrow-left me-2"></i>Voltar ao A
                                </a>
                                <a href="pesquisa-advogados.php" class="btn btn-outline-primary">
                                    <i class="fa fa-search me-2"></i>Pesquisa Avançada
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Alphabet Navigation & List End -->

    <!-- Statistics Section -->
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo $total_advogados; ?></h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Advogados Ativos</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marked-alt text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';">8</h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Regiões Cobertas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-balance-scale text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';">100%</h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Em Situação Regular</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-award text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo date('Y') - 1990; ?></h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Anos de Tradição</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="container-fluid bg-primary py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="text-white mb-2" style="font-family: 'Libre Baskerville';">Precisa de Assistência Jurídica?</h3>
                    <p class="text-white mb-0" style="font-family: 'Open Sans';">Todos os advogados listados estão em situação regular e podem prestar serviços jurídicos.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="pesquisa-advogados.php" class="btn btn-light py-3 px-5 me-3">Pesquisar</a>
                    <a href="solicitacao-advogados.php" class="btn btn-outline-light py-3 px-5">Solicitar Indicação</a>
                </div>
            </div>
        </div>
    </div>

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