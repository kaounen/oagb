<?php
require_once 'connect.php';

// Buscar conteúdo da página
$stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = 'apresentacao-historia' AND ativo = 1");
$stmt->execute();
$pagina = $stmt->fetch();

// Se não existir conteúdo na BD, usar conteúdo padrão
if (!$pagina) {
    $pagina = (object)[
        'titulo' => 'Apresentação e História',
        'conteudo' => '<p>Conteúdo em construção...</p>',
        'imagem' => null
    ];
}

$page_title = $pagina->titulo;
$breadcrumb = "Ordem";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pagina->titulo); ?> - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="História OAGB, Apresentação Ordem Advogados, Guinea-Bissau" name="keywords">
    <meta content="Conheça a história e apresentação da Ordem dos Advogados da Guiné-Bissau" name="description">

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

    <!-- Content Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Main Content -->
                    <div class="mb-5">
                        <?php if ($pagina->imagem): ?>
                            <img class="img-fluid w-100 rounded mb-5" src="img/ordem/<?php echo $pagina->imagem; ?>" alt="<?php echo htmlspecialchars($pagina->titulo); ?>">
                        <?php endif; ?>
                        
                        <h1 class="mb-4" style="color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: bold;">
                            <?php echo htmlspecialchars($pagina->titulo); ?>
                        </h1>
                        
                        <div class="content" style="font-family: 'Open Sans'; line-height: 1.8;">
                            <!-- Conteúdo padrão expandido para demonstração -->
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <h3 style="color:#4D1C21;font-family: 'Libre Baskerville';">Nossa Missão</h3>
                                    <p>A Ordem dos Advogados da Guiné-Bissau tem como missão representar, regular e promover a advocacia no país, defendendo os direitos e interesses dos advogados e contribuindo para o fortalecimento do Estado de Direito.</p>
                                    <p>Estabelecida como instituição de utilidade pública, a OAGB zela pela dignidade e independência da advocacia, promovendo a formação contínua dos seus membros e assegurando o cumprimento dos mais altos padrões éticos e profissionais.</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 style="color:#4D1C21;font-family: 'Libre Baskerville';">Nossa Visão</h3>
                                    <p>Ser uma instituição de referência na promoção da justiça e na defesa dos direitos fundamentais, contribuindo para o desenvolvimento de um sistema judicial eficiente e acessível a todos os cidadãos da Guiné-Bissau.</p>
                                    <p>Aspiramos a ser reconhecidos pela excelência na formação jurídica e pela qualidade dos serviços prestados pelos nossos membros à sociedade guineense.</p>
                                </div>
                            </div>

                            <div class="bg-light rounded p-4 mb-5">
                                <h3 style="color:#4D1C21;font-family: 'Libre Baskerville';" class="mb-3">História da Ordem</h3>
                                <p>A Ordem dos Advogados da Guiné-Bissau foi criada com o objetivo de organizar e disciplinar a profissão de advogado no país. Desde a sua fundação, tem desempenhado um papel fundamental na construção e consolidação do sistema judicial guineense.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 style="color:#4D1C21;">Marcos Históricos</h5>
                                        <ul>
                                            <li>Criação da primeira legislação sobre a advocacia</li>
                                            <li>Estabelecimento do estatuto da Ordem</li>
                                            <li>Implementação do sistema de estágio profissional</li>
                                            <li>Criação de programas de formação contínua</li>
                                            <li>Desenvolvimento de parcerias institucionais</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 style="color:#4D1C21;">Conquistas Recentes</h5>
                                        <ul>
                                            <li>Modernização dos serviços da Ordem</li>
                                            <li>Implementação de sistemas digitais</li>
                                            <li>Expansão da rede de advogados</li>
                                            <li>Fortalecimento da cooperação internacional</li>
                                            <li>Melhoria dos processos internos</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <h3 style="color:#4D1C21;font-family: 'Libre Baskerville';">Valores Fundamentais</h3>
                            <div class="row g-4 mb-5">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-balance-scale text-white fa-2x"></i>
                                        </div>
                                        <h5 style="color:#4D1C21;">Justiça</h5>
                                        <p>Compromisso com a promoção da justiça e equidade em todas as nossas ações e decisões.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-shield-alt text-white fa-2x"></i>
                                        </div>
                                        <h5 style="color:#4D1C21;">Integridade</h5>
                                        <p>Manutenção dos mais altos padrões éticos e morais na prática profissional.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-graduation-cap text-white fa-2x"></i>
                                        </div>
                                        <h5 style="color:#4D1C21;">Excelência</h5>
                                        <p>Busca constante pela excelência na formação e no exercício da advocacia.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Incluir o conteúdo da base de dados se existir -->
                            <?php if ($pagina && $pagina->conteudo && strlen(trim(strip_tags($pagina->conteudo))) > 20): ?>
                            <div class="mt-5">
                                <?php echo $pagina->conteudo; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Links -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Outras Páginas</h3>
                        </div>
                        <div class="link-animated d-flex flex-column justify-content-start">
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none" href="orgaos-sociais.php">
                                <i class="bi bi-arrow-right me-2"></i>Órgãos Sociais
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none" href="comissoes-especializadas.php">
                                <i class="bi bi-arrow-right me-2"></i>Comissões Especializadas
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none" href="cooperacao-institucional.php">
                                <i class="bi bi-arrow-right me-2"></i>Cooperação Institucional
                            </a>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-primary text-white rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Contacte-nos</h5>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-map-marker-alt me-2"></i>
                                <span style="font-family: 'Open Sans';">Rua 15, Bissau<br>Guiné-Bissau</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-phone me-2"></i>
                                <span style="font-family: 'Open Sans';">+245 955 475 889</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-envelope me-2"></i>
                                <span style="font-family: 'Open Sans';">info@oagb.gw</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-light rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Em Números</h5>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Advogados Ativos</span>
                                <span class="fw-bold text-primary">200+</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Estagiários</span>
                                <span class="fw-bold text-primary">50+</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Regiões Cobertas</span>
                                <span class="fw-bold text-primary">8</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-family: 'Open Sans';">Anos de Experiência</span>
                                <span class="fw-bold text-primary">30+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

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