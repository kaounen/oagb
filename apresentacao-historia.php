<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Buscar conteúdo da página
try {
    $stmt = $pdo->prepare("
        SELECT * FROM paginas_ordem 
        WHERE slug = 'apresentacao-historia' AND ativo = 1
    ");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    // Se não existir na BD, usar conteúdo padrão
    if (!$pagina) {
        $pagina = (object)[
            'titulo' => 'Apresentação e História',
            'conteudo' => '
                <h3>A Nossa História</h3>
                <p>A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública representativa dos licenciados em Direito que, em conformidade com os preceitos do presente Estatuto e demais disposições legais aplicáveis, exercem a advocacia.</p>
                
                <h3>Missão</h3>
                <p>A OAGB tem como missão assegurar o acesso dos cidadãos ao Direito e aos tribunais, bem como regular o exercício da profissão de advogado, defender a função social, a dignidade e o prestígio da advocacia e promover o respeito pelos princípios do Estado de Direito e dos Direitos Humanos.</p>
                
                <h3>Atribuições</h3>
                <ul>
                    <li>Defender o Estado de Direito e os direitos, liberdades e garantias dos cidadãos</li>
                    <li>Assegurar o acesso ao direito, nomeadamente o patrocínio judiciário</li>
                    <li>Regular o exercício da advocacia</li>
                    <li>Exercer o poder disciplinar sobre os advogados e advogados estagiários</li>
                    <li>Defender os interesses, prerrogativas e direitos dos advogados</li>
                    <li>Promover a formação profissional e cultural dos advogados</li>
                    <li>Pronunciar-se sobre projectos de diplomas legislativos</li>
                </ul>
                
                <h3>Valores</h3>
                <p>Os nossos valores fundamentais incluem:</p>
                <ul>
                    <li><strong>Independência</strong> - Atuamos com total independência</li>
                    <li><strong>Integridade</strong> - Pautamos pela ética e honestidade</li>
                    <li><strong>Excelência</strong> - Buscamos a excelência profissional</li>
                    <li><strong>Justiça</strong> - Defendemos a justiça e equidade</li>
                    <li><strong>Solidariedade</strong> - Promovemos a entreajuda profissional</li>
                </ul>
            ',
            'imagem' => null
        ];
    }
    
    // Buscar estatísticas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
    $stmt->execute();
    $total_advogados = $stmt->fetch()->total;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
    $stmt->execute();
    $total_estagiarios = $stmt->fetch()->total;
    
} catch (Exception $e) {
    error_log("Erro ao buscar página: " . $e->getMessage());
}

$page_title = htmlspecialchars($pagina->titulo);
$meta_description = "Conheça a história, missão e valores da Ordem dos Advogados da Guiné-Bissau";
$apresentacao_header_background_style = "margin-bottom: 90px; background: url('gestao/assets/uploads/files/close-up-scales-justice-original-azul.jpg') center center no-repeat; background-size: cover;";
$apresentacao_header_image = 'gestao/assets/uploads/files/close-up-scales-justice-original-azul.jpg';
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
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Header Styles (Componente reutilizável) -->
    <link href="css/header-styles.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

    <?php include 'includes/topbar.php'; ?>

    <!-- Navbar & Header Start -->
    <!-- Desktop Navbar -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary py-5 bg-header" style="<?php echo htmlspecialchars($apresentacao_header_background_style, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="#" class="h5 text-white">A Ordem</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="apresentacao-historia.php" class="h5 text-white"><?php echo $page_title; ?></a>

                    <!-- Quick Action Links -->
                    <div class="quick-actions mt-3">
                        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2" title="Voltar atrás">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <a href="javascript:window.print()" class="btn btn-outline-light btn-sm me-2" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2" title="Partilhar" onclick="sharePage()">
                            <i class="fas fa-share-alt"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm" title="Traduzir" onclick="translatePage()">
                            <i class="fas fa-language"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- Desktop Navbar End -->
            <!-- Mobile Header Start -->

    <div class="d-block d-lg-none">

        <div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="false" style="position: relative;">

            <div class="carousel-inner">

                <div class="carousel-item active">

                    <img class="w-100" src="<?php echo htmlspecialchars($apresentacao_header_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $page_title; ?>">



                    <div class="mobile-header-contacts container-fluid px-3 py-3">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <div class="mobile-contacts">

                                    <div class="contact-line">

                                        <strong class="text-white">Rua 15, Bissau, Guiné-Bissau</strong>

                                    </div>

                                    <div class="contact-line">

                                        <strong class="text-white">+245 955 475 889</strong>

                                    </div>

                                    <div class="contact-line">

                                        <strong class="text-white">info@oagb.gw</strong>

                                    </div>

                                </div>

                            </div>

                            <div class="col-4 text-end">

                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#searchModal">

                                    <i class="fa fa-search me-1"></i>Pesquisar

                                </button>
                                
                                <a href="portal/login.php" class="btn btn-outline-light btn-sm ms-1" title="Área Reservada (Portal)">
                                    <i class="fas fa-user-circle"></i>
                                </a>

                            </div>

                        </div>

                    </div>



                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">

                        <?php include 'includes/navbar.php'; ?>

                    </div>



                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-end" style="padding: 1rem 1.5rem;">

                        <div class="p-3" style="max-width: 900px;">

                            <h1 class="display-5 text-white mb-3 animated zoomIn"><?php echo $page_title; ?></h1>

                            <div class="mt-2">

                                <a href="index.php" class="h6 text-white">Início</a>

                                <i class="far fa-circle text-white px-2"></i>

                                <a href="#" class="h6 text-white">A Ordem</a>

                                <i class="far fa-circle text-white px-2"></i>

                                <a href="apresentacao-historia.php" class="h6 text-white"><?php echo $page_title; ?></a>

                            </div>

                            <div class="quick-actions mt-3">
                                <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2" title="Voltar atrás">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <a href="javascript:window.print()" class="btn btn-outline-light btn-sm me-2" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm me-2" title="Partilhar" onclick="sharePage()">
                                    <i class="fas fa-share-alt"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm" title="Traduzir" onclick="translatePage()">
                                    <i class="fas fa-language"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Mobile Header End -->



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

    <!-- Content Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <!-- Introdução -->
            <div class="row g-5 mb-5">
                <div class="col-lg-7">
                    <div class="content-section">
                        <?php echo $pagina->conteudo; ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <!-- Estatísticas -->
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="stats-number" data-toggle="counter-up"><?php echo $total_advogados; ?></div>
                                <div class="stats-label">Advogados Ativos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="stats-number" data-toggle="counter-up"><?php echo $total_estagiarios; ?></div>
                                <div class="stats-label">Estagiários</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Imagem -->
                    <?php if (!empty($pagina->imagem)): ?>
                    <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($pagina->imagem); ?>" 
                         alt="OAGB" class="img-fluid rounded">
                    <?php else: ?>
                    <img src="img/close-up-scales-justice.jpg" alt="OAGB" class="img-fluid rounded">
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-divider"></div>

            <!-- Timeline Histórico -->
            <div class="content-section">
                <h2 class="text-center mb-5" style="font-family: 'Libre Baskerville', serif; color: #4D1C21;">
                    Marcos Históricos
                </h2>
                <div class="timeline">
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.1s">
                        <div class="timeline-content">
                            <div class="timeline-year">1974</div>
                            <h5>Independência Nacional</h5>
                            <p>Com a independência da Guiné-Bissau, surge a necessidade de organizar a classe dos advogados.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="timeline-content">
                            <div class="timeline-year">1991</div>
                            <h5>Criação da OAGB</h5>
                            <p>Fundação oficial da Ordem dos Advogados da Guiné-Bissau como instituição autónoma.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.3s">
                        <div class="timeline-content">
                            <div class="timeline-year">2005</div>
                            <h5>Novo Estatuto</h5>
                            <p>Aprovação do novo estatuto que moderniza a estrutura e funcionamento da Ordem.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.4s">
                        <div class="timeline-content">
                            <div class="timeline-year">2020</div>
                            <h5>Digitalização</h5>
                            <p>Início do processo de digitalização e modernização dos serviços da OAGB.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            <!-- Valores -->
            <div class="content-section">
                <h2 class="text-center mb-5" style="font-family: 'Libre Baskerville', serif; color: #4D1C21;">
                    Os Nossos Pilares
                </h2>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInLeft" data-wow-delay="0.1s">
                            <h4><i class="fa fa-balance-scale me-2"></i>Justiça</h4>
                            <p>Defendemos o acesso universal à justiça e trabalhamos para garantir que todos os cidadãos tenham representação legal adequada.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInRight" data-wow-delay="0.2s">
                            <h4><i class="fa fa-shield-alt me-2"></i>Integridade</h4>
                            <p>Mantemos os mais altos padrões éticos e profissionais, garantindo a confiança pública na advocacia.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInLeft" data-wow-delay="0.3s">
                            <h4><i class="fa fa-graduation-cap me-2"></i>Excelência</h4>
                            <p>Promovemos a formação contínua e o desenvolvimento profissional dos nossos membros.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInRight" data-wow-delay="0.4s">
                            <h4><i class="fa fa-handshake me-2"></i>Cooperação</h4>
                            <p>Fomentamos a colaboração entre advogados e com outras instituições jurídicas nacionais e internacionais.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-5">
                <a href="inscricao-ordem.php" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">
                    <i class="fa fa-user-plus me-2"></i>Inscrever-se na Ordem
                </a>
                <a href="contacto.php" class="btn btn-outline-primary py-3 px-5 animated fadeIn">
                    <i class="fa fa-envelope me-2"></i>Contactar-nos
                </a>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Header Functions incluído via footer.php -->
</body>
</html>
