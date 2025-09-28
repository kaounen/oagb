<?php
// Incluir configuração
require_once 'connect.php';

try {
    // Buscar notícias em destaque
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE destaque = 1 AND ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
    $stmt->execute();
    $noticias_destaque = $stmt->fetchAll();

    // Buscar próximos eventos
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE data_evento >= NOW() AND ativo = 1 ORDER BY data_evento ASC LIMIT 3");
    $stmt->execute();
    $proximos_eventos = $stmt->fetchAll();

    // Estatísticas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
    $stmt->execute();
    $result = $stmt->fetch();
    $total_advogados = $result ? $result->total : 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
    $stmt->execute();
    $result = $stmt->fetch();
    $total_estagiarios = $result ? $result->total : 0;

} catch (Exception $e) {
    // Em caso de erro, definir valores padrão
    $noticias_destaque = [];
    $proximos_eventos = [];
    $total_advogados = 0;
    $total_estagiarios = 0;
    error_log("Erro na página inicial: " . $e->getMessage());
}

$page_title = "Início";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Ordem dos Advogados da Guiné-Bissau, OAGB, Advogados Guinea-Bissau" name="keywords">
    <meta content="Site oficial da Ordem dos Advogados da Guiné-Bissau" name="description">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

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

    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <img src="img/logo3.png" style="width:70%;height:auto;padding-top:5%;" align="center" border="0" alt="OAGB Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <center><span class="fa fa-bars"></span></center>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link active">INÍCIO</a>
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
                        <div class="dropdown-menu m-0">
                            <a href="agenda.php" class="dropdown-item">Agenda</a>
                            <a href="noticias.php" class="dropdown-item">Notícias</a>
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

        <!-- Carousel -->
        <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/close-up-scales-justice.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="display-1 text-white mb-md-4 animated zoomIn fonText" style="text-decoration:underline;">Bem-vindo à Ordem dos Advogados da Guiné-Bissau</h1>
                            <h5 class="text-white mb-3 animated slideInDown fonText2">A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito, que em conformidade com os preceitos dos respectivos estatutos e demais disposições legais aplicáveis exercem a advocacia.</h5>
                            <a href="apresentacao-historia.php" class="btn btn-outline-light py-md-3 px-md-5 animated slideInRight">Saiba mais</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/brass-scales-justice-close-up-view.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="display-1 text-white mb-md-4 animated zoomIn fonText" style="text-decoration:underline;">Cadastro Nacional de Advogados da Guiné-Bissau</h1>
                            <h5 class="text-white mb-3 animated slideInDown fonText2">O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB, que exerce a função de fiel repositório do cadastro de todos os advogados da Guiné-Bissau.</h5>
                            <a href="advogados-inscritos.php" class="btn btn-outline-light py-md-3 px-md-5 animated slideInRight">Saiba mais</a>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Navbar & Carousel End -->

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

    <!-- Facts Start -->
    <div class="container-fluid facts py-5 pt-lg-0">
        <div class="container py-5 pt-lg-0">
            <div class="row gx-0">
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.1s">
                    <div class="bg-primary shadow d-flex align-items-top justify-content-center p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/pareceresDeliberacoesBox.png" style="width:92%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <a href="pareceres-deliberacoes.php" class="fonText3 linkSublinhado" style="color:#5A463F;">CNEF - Deliberação n.º 8/2023</a><br>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.3s">
                    <div class="bg-light1 shadow d-flex align-items-top justify-content-left p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/pesquisaAdvogadosBox.png" style="width:92%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <a href="advogados-inscritos.php" class="fonText2 linkSublinhado" style="color:#fff;">Advogados Inscritos</a><br>
                            <a href="sociedades-advogados.php" class="fonText2 linkSublinhado" style="color:#fff;">Sociedades de Advogados</a><br>
                            <a href="estagiarios-inscritos.php" class="fonText2 linkSublinhado" style="color:#fff;">Estagiários</a><br>							
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.6s">
                    <div class="bg-primary shadow d-flex align-items-top justify-content-center p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/anunciosBox.png" style="width:52%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <a href="agenda.php" class="fonText3 linkSublinhado" style="color:#5A463F;">IX Congresso dos Advogados Guineenses</a><br>
                            <span class="fonText4 linkSublinhado" style="color:#5A463F;">23-25 de Junho de 2023, 9h às 16h, Hotel Dunia</span><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Facts End -->

    <!-- Blog Start -->
    <?php if (!empty($noticias_destaque)): ?>
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Artigos recentes</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:300%;">Últimas notícias</h1>
            </div>
            <div class="row g-5">
                <?php 
                $delay = 0.3;
                foreach ($noticias_destaque as $noticia): 
                ?>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="<?php echo $delay; ?>s">
                    <div class="blog-item bg-light rounded overflow-hidden">
                        <div class="blog-img position-relative overflow-hidden">
                            <?php if (!empty($noticia->imagem_destaque)): ?>
                                <img class="img-fluid" src="img/noticias/<?php echo htmlspecialchars($noticia->imagem_destaque); ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                            <?php else: ?>
                                <img class="img-fluid" src="img/Asset 7-100.jpg" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="p-4" style="padding:0px">
                            <h4 class="mb-3" style="margin:0px;color:#4D1C21;font-family: 'Libre Baskerville'; font-weight: normal; font-style: normal;font-size:180%;">
                                <a href="artigo.php?slug=<?php echo htmlspecialchars($noticia->slug); ?>" class="linkSublinhado" style="color:#4D1C21;">
                                    <?php echo htmlspecialchars($noticia->titulo); ?>
                                </a>
                            </h4>
                            <div class="d-flex mb-3">
                                <small style="color:#615759;font-family: 'Open Sans'; font-weight: 300; font-style: normal;font-size:90%;">
                                    <?php echo format_date($noticia->data_publicacao, 'd \d\e F \d\e Y'); ?>
                                </small>
                            </div>
                            <p style="color:#111923;font-family: 'Open Sans'; font-weight: 600; font-style: normal;font-size:100%;">
                                <?php echo htmlspecialchars(truncate_text($noticia->resumo, 120)); ?>
                            </p>
                            <span id="" style="border-bottom:1px solid #111923;float:left;">
                                <a class="text-uppercase" href="artigo.php?slug=<?php echo htmlspecialchars($noticia->slug); ?>">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <i class="bi bi-arrow-right" style="color:#111923;"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <?php 
                $delay += 0.3;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Blog End -->

    <!-- Agenda Start -->
    <?php if (!empty($proximos_eventos)): ?>
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Próximos Eventos</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:300%;">Agenda</h1>
            </div>
            <div class="row g-5">
                <?php 
                $delay = 0.3;
                foreach ($proximos_eventos as $evento): 
                ?>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="<?php echo $delay; ?>s">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle p-3 me-3">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="font-family: 'Libre Baskerville';"><?php echo htmlspecialchars($evento->titulo); ?></h5>
                                <small class="text-muted" style="font-family: 'Open Sans';">
                                    <?php echo format_datetime($evento->data_evento, 'd/m/Y \à\s H:i'); ?>
                                </small>
                            </div>
                        </div>
                        <?php if (!empty($evento->local_evento)): ?>
                        <p class="text-muted mb-2" style="font-family: 'Open Sans';">
                            <i class="fa fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($evento->descricao)): ?>
                        <p style="font-family: 'Open Sans';"><?php echo htmlspecialchars(truncate_text($evento->descricao, 120)); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                $delay += 0.3;
                endforeach; 
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="agenda.php" class="btn btn-primary py-3 px-5">Ver Agenda Completa</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Agenda End -->

    <!-- Mídia Social Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Siga-nos</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:300%;">Redes Sociais</h1>
            </div>
            <div class="row g-5">
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-facebook-f text-white fa-2x"></i>
                            </div>
                            <h4 style="font-family: 'Libre Baskerville'; color: #4D1C21;">Facebook</h4>
                        </div>
                        <div class="facebook-embed">
                            <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fprofile.php%3Fid%3D100087015439692&tabs=timeline&width=500&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" 
                                    width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" 
                                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-light rounded p-4 h-100">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-youtube text-white fa-2x"></i>
                            </div>
                            <h4 style="font-family: 'Libre Baskerville'; color: #4D1C21;">YouTube</h4>
                        </div>
                        <div class="youtube-placeholder text-center">
                            <div class="border rounded p-5" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                <div>
                                    <i class="fab fa-youtube text-muted fa-4x mb-3"></i>
                                    <h5 class="text-muted">Canal YouTube</h5>
                                    <p class="text-muted mb-4">Em breve disponível</p>
                                    <a href="#" class="btn btn-outline-primary disabled">
                                        <i class="fab fa-youtube me-2"></i>Inscrever-se
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mídia Social End -->

    <!-- Newsletter Banner -->
    <div id="" style="">
        <a href="#"><img src="img/Asset 6-100.jpg" style="width:100%;height:auto;" border="0" alt="Newsletter Banner"></a>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-light text-light mt-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-4 col-md-6 footer-about">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center h-100 bg-primary p-4">
                        <a href="index.php" class="navbar-brand">
                            <img src="img/logo3.png" style="width:35%;height:auto;padding-top:5%;" align="center" border="0" alt="OAGB Logo">
                        </a>
                        <p class="mt-3 mb-4" style="color:#111923;">Registe o seu e-mail para receber regularmente o nosso boletim informativo com as informações que você deseja.</p>
                        <form action="subscricao.php" method="POST" id="newsletter-form">
                            <div class="input-group">
                                <input type="email" name="email" class="form-control border-white p-3" placeholder="Introduza o seu Email" required>
                                <button class="btn btn-dark" type="submit">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>       
                <div class="col-lg-8 col-md-6">
                    <div class="row gx-5">
                        <div class="col-lg-4 col-md-12 pt-5 mb-5">
                            <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                <h3 class="text-dark mb-0">Fale connosco</h3>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                <p class="mb-0 text-dark">Rua 15, Bissau,<br>Guiné-Bissau</p>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-envelope-open text-primary me-2"></i>
                                <p class="mb-0 text-dark">info@oagb.gw</p>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-telephone text-primary me-2"></i>
                                <p class="mb-0 text-dark">+245 955 475 889</p>
                            </div>
                            <div class="d-flex mt-4">
                                <a class="btn btn-primary btn-square me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                                <a class="btn btn-primary btn-square me-2" href="https://www.facebook.com/profile.php?id=100087015439692" target="_blank"><i class="fab fa-facebook-f fw-normal"></i></a>
                                <a class="btn btn-primary btn-square me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                                <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 pt-0 pt-lg-5 mb-5">
                            <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                <h3 class="text-dark mb-0">Institucional</h3>
                            </div>
                            <div class="link-animated d-flex flex-column justify-content-start">
                                <a class="text-dark mb-2" href="index.php"><i class="bi bi-arrow-right text-primary me-2"></i>Início</a>
                                <a class="text-dark mb-2" href="agenda.php"><i class="bi bi-arrow-right text-primary me-2"></i>Agenda</a>
                                <a class="text-dark mb-2" href="comunicados.php"><i class="bi bi-arrow-right text-primary me-2"></i>Comunicados</a>
                                <a class="text-dark mb-2" href="agenda.php"><i class="bi bi-arrow-right text-primary me-2"></i>Formações e Eventos</a>
                                <a class="text-dark mb-2" href="noticias.php"><i class="bi bi-arrow-right text-primary me-2"></i>Notícias</a>
                                <a class="text-dark" href="contacto.php"><i class="bi bi-arrow-right text-primary me-2"></i>Contacto</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 pt-0 pt-lg-5 mb-5">
                            <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                <h3 class="text-dark mb-0">Advogados</h3>
                            </div>
                            <div class="link-animated d-flex flex-column justify-content-start">
                                <a class="text-dark mb-2" href="pesquisa-advogados.php"><i class="bi bi-arrow-right text-primary me-2"></i>Pesquisa de Advogados</a>
                                <a class="text-dark mb-2" href="advogados-inscritos.php"><i class="bi bi-arrow-right text-primary me-2"></i>Advogados Inscritos</a>
                                <a class="text-dark mb-2" href="estagiarios-inscritos.php"><i class="bi bi-arrow-right text-primary me-2"></i>Advogados Estagiários</a>
                                <a class="text-dark mb-2" href="solicitacao-advogados.php"><i class="bi bi-arrow-right text-primary me-2"></i>Solicitação de Advogados</a>
                                <a class="text-dark mb-2" href="inscricao-ordem.php"><i class="bi bi-arrow-right text-primary me-2"></i>Inscrição na Ordem</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid text-white" style="background: #fff;">
        <div class="container text-center">
            <div class="row justify-content-end">
                <div class="col-lg-8 col-md-6">
                    <div class="d-flex align-items-center justify-content-center" style="height: 75px;">
                        <p class="mb-0 text-dark">&copy; <a class="text-dark border-bottom" href="#">Ordem dos Advogados da Guiné-Bissau</a>. Todos os Direitos Reservados. </p>
                        <p><a class="text-white border-bottom" href="https://ada.gw" target="_blank"><img src="img/LogotipoADA.png" style="width:15%;height:auto;" align="center" border="0" alt="ADA Logo"></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Newsletter subscription -->
    <script>
    document.getElementById('newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('subscricao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Subscrição realizada com sucesso!');
                this.reset();
            } else {
                alert(data.message || 'Erro ao processar subscrição.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao processar subscrição.');
        });
    });
    </script>
</body>
</html>