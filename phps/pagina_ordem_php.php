<?php
require_once 'connect.php';

// Mapear URLs para slugs
$url_to_slug = [
    'orgaos-sociais' => 'orgaos-sociais',
    'comissoes-especializadas' => 'comissoes-especializadas', 
    'cooperacao-institucional' => 'cooperacao-institucional'
];

// Obter página atual baseada na URL
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$slug = isset($url_to_slug[$current_page]) ? $url_to_slug[$current_page] : $current_page;

// Buscar conteúdo da página
$stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = ? AND ativo = 1");
$stmt->execute([$slug]);
$pagina = $stmt->fetch();

// Conteúdo padrão caso não exista na BD
$conteudo_padrao = [
    'orgaos-sociais' => [
        'titulo' => 'Órgãos Sociais',
        'conteudo' => '
            <h3>Conselho Geral</h3>
            <p>O Conselho Geral é o órgão supremo da Ordem dos Advogados da Guiné-Bissau, responsável pela definição das políticas gerais e pela supervisão das atividades da instituição.</p>
            
            <h3>Conselho Diretivo</h3>
            <p>O Conselho Diretivo é o órgão executivo da Ordem, responsável pela gestão corrente e pela implementação das decisões do Conselho Geral.</p>
            
            <h3>Conselho Disciplinar</h3>
            <p>O Conselho Disciplinar tem competência para apreciar e decidir sobre questões disciplinares relativas aos membros da Ordem.</p>
            
            <h3>Assembleia Geral</h3>
            <p>A Assembleia Geral é constituída por todos os advogados inscritos na Ordem e é o órgão deliberativo máximo.</p>'
    ],
    'comissoes-especializadas' => [
        'titulo' => 'Comissões Especializadas',
        'conteudo' => '
            <h3>Comissão de Direito Civil</h3>
            <p>Especializada em questões relacionadas com o direito civil, contratos, propriedade e direitos reais.</p>
            
            <h3>Comissão de Direito Criminal</h3>
            <p>Dedicada ao estudo e desenvolvimento do direito criminal e processual penal.</p>
            
            <h3>Comissão de Direito Comercial</h3>
            <p>Focada nas questões empresariais, societárias e de direito comercial.</p>
            
            <h3>Comissão de Direito do Trabalho</h3>
            <p>Especializada em relações laborais e direito do trabalho.</p>
            
            <h3>Comissão de Direitos Humanos</h3>
            <p>Dedicada à promoção e defesa dos direitos humanos fundamentais.</p>'
    ],
    'cooperacao-institucional' => [
        'titulo' => 'Cooperação Institucional',
        'conteudo' => '
            <h3>Parcerias Nacionais</h3>
            <p>A OAGB mantém parcerias com diversas instituições nacionais, incluindo tribunais, universidades e organizações da sociedade civil.</p>
            
            <h3>Cooperação Internacional</h3>
            <p>Colaboramos com ordens de advogados de outros países da CEDEAO e organizações internacionais de advocacia.</p>
            
            <h3>Protocolos de Cooperação</h3>
            <p>Temos protocolos estabelecidos com várias entidades para facilitar o exercício da advocacia e a formação profissional.</p>
            
            <h3>Projetos em Curso</h3>
            <p>Participamos em diversos projetos de cooperação focados no fortalecimento do sistema judicial e na capacitação profissional.</p>'
    ]
];

// Se não existir na BD, usar conteúdo padrão
if (!$pagina && isset($conteudo_padrao[$slug])) {
    $pagina = (object)$conteudo_padrao[$slug];
} elseif (!$pagina) {
    // Página não encontrada
    header("HTTP/1.0 404 Not Found");
    $pagina = (object)[
        'titulo' => 'Página não encontrada',
        'conteudo' => '<p>A página solicitada não foi encontrada.</p>'
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
    <meta content="<?php echo htmlspecialchars($pagina->titulo); ?>, OAGB, Ordem Advogados Guinea-Bissau" name="keywords">
    <meta content="<?php echo htmlspecialchars($pagina->titulo); ?> da Ordem dos Advogados da Guiné-Bissau" name="description">

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
                    <div class="mb-5">
                        <h1 class="mb-4" style="color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: bold;">
                            <?php echo htmlspecialchars($pagina->titulo); ?>
                        </h1>
                        
                        <div class="content" style="font-family: 'Open Sans'; line-height: 1.8;">
                            <?php echo $pagina->conteudo; ?>
                        </div>

                        <!-- Seção específica baseada no tipo de página -->
                        <?php if ($slug == 'orgaos-sociais'): ?>
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="mb-4" style="color:#4D1C21;font-family: 'Libre Baskerville';">Membros dos Órgãos Sociais</h3>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Para informações atualizadas sobre os membros dos órgãos sociais, contacte os nossos serviços pelo telefone +245 955 475 889 ou email info@oagb.gw.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($slug == 'comissoes-especializadas'): ?>
                        <div class="bg-light rounded p-4 mt-5">
                            <h4 class="mb-3" style="color:#4D1C21;font-family: 'Libre Baskerville';">Como Participar</h4>
                            <p>Os advogados interessados em participar nas comissões especializadas podem:</p>
                            <ul>
                                <li>Manifestar interesse junto dos serviços da Ordem</li>
                                <li>Participar nas reuniões e atividades das comissões</li>
                                <li>Contribuir com conhecimentos especializados</li>
                                <li>Propor temas de estudo e desenvolvimento</li>
                            </ul>
                            <a href="contacto.php" class="btn btn-primary">Manifestar Interesse</a>
                        </div>
                        <?php endif; ?>

                        <?php if ($slug == 'cooperacao-institucional'): ?>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="bg-primary text-white rounded p-4">
                                    <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Parcerias Ativas</h5>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Tribunais da Guiné-Bissau</li>
                                        <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Faculdade de Direito</li>
                                        <li class="mb-2"><i class="bi bi-check-circle me-2"></i>OAB - Brasil</li>
                                        <li class="mb-0"><i class="bi bi-check-circle me-2"></i>Organizações da CEDEAO</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded p-4">
                                    <h5 class="mb-3" style="color:#4D1C21;font-family: 'Libre Baskerville';">Oportunidades</h5>
                                    <ul>
                                        <li>Programas de intercâmbio</li>
                                        <li>Formações especializadas</li>
                                        <li>Eventos internacionais</li>
                                        <li>Projetos de cooperação</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Navigation Menu -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">A Ordem</h3>
                        </div>
                        <div class="link-animated d-flex flex-column justify-content-start">
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($slug == 'apresentacao-historia') ? 'bg-primary text-white' : ''; ?>" href="apresentacao-historia.php">
                                <i class="bi bi-arrow-right me-2"></i>Apresentação e História
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($slug == 'orgaos-sociais') ? 'bg-primary text-white' : ''; ?>" href="orgaos-sociais.php">
                                <i class="bi bi-arrow-right me-2"></i>Órgãos Sociais
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($slug == 'comissoes-especializadas') ? 'bg-primary text-white' : ''; ?>" href="comissoes-especializadas.php">
                                <i class="bi bi-arrow-right me-2"></i>Comissões Especializadas
                            </a>
                            <a class="h6 fw-semi-bold bg-light rounded py-2 px-3 mb-2 text-decoration-none <?php echo ($slug == 'cooperacao-institucional') ? 'bg-primary text-white' : ''; ?>" href="cooperacao-institucional.php">
                                <i class="bi bi-arrow-right me-2"></i>Cooperação Institucional
                            </a>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-light rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">A OAGB em Números</h5>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Advogados Ativos</span>
                                <span class="fw-bold text-primary">200+</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Estagiários</span>
                                <span class="fw-bold text-primary">50+</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-family: 'Open Sans';">Regiões</span>
                                <span class="fw-bold text-primary">8</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-family: 'Open Sans';">Anos de Tradição</span>
                                <span class="fw-bold text-primary">30+</span>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="bg-primary text-white rounded p-4">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Serviços da Ordem</h5>
                            <ul class="list-unstyled mb-3" style="font-family: 'Open Sans';">
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Inscrição de advogados</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Formação profissional</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Indicação de advogados</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Supervisão disciplinar</li>
                                <li class="mb-0"><i class="bi bi-check-circle me-2"></i>Apoio institucional</li>
                            </ul>
                            <a href="contacto.php" class="btn btn-light">Contactar</a>
                        </div>
                    </div>

                    <!-- Latest News -->
                    <div class="wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0" style="font-family: 'Libre Baskerville';">Últimas Notícias</h3>
                        </div>
                        <?php
                        // Buscar últimas 3 notícias
                        $stmt = $pdo->prepare("SELECT titulo, slug, data_publicacao FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
                        $stmt->execute();
                        $noticias_recentes = $stmt->fetchAll();
                        
                        foreach ($noticias_recentes as $noticia): ?>
                        <div class="d-flex rounded overflow-hidden mb-3">
                            <img class="img-fluid rounded" src="img/Asset 7-100.jpg" style="width: 80px; height: 80px; object-fit: cover;" alt="">
                            <div class="ps-3">
                                <h6 class="mb-1">
                                    <a href="artigo.php?slug=<?php echo $noticia->slug; ?>" class="text-decoration-none" style="font-family: 'Libre Baskerville';">
                                        <?php echo truncate_text($noticia->titulo, 50); ?>
                                    </a>
                                </h6>
                                <small class="text-muted" style="font-family: 'Open Sans';">
                                    <?php echo format_date($noticia->data_publicacao); ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center mt-3">
                            <a href="noticias.php" class="btn btn-outline-primary btn-sm">Ver Todas</a>
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