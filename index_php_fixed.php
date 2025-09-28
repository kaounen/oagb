<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir funções auxiliares e conexão
require_once 'includes/functions.php';
require_once 'connect.php';

try {
    // Buscar slides do carousel da base de dados
    $stmt = $pdo->prepare("
        SELECT * FROM carousel_slides 
        WHERE ativo = 1 
        ORDER BY ordem_exibicao ASC, id DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $carousel_slides = $stmt->fetchAll();
    
    // Se não houver slides na BD, usar padrão
    if (empty($carousel_slides)) {
        $carousel_slides = [
            (object)[
                'titulo' => 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau',
                'subtitulo' => 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito.',
                'imagem' => 'img/close-up-scales-justice.jpg',
                'link_texto' => 'Saiba mais',
                'link_url' => 'apresentacao-historia.php'
            ],
            (object)[
                'titulo' => 'Cadastro Nacional de Advogados',
                'subtitulo' => 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB.',
                'imagem' => 'img/brass-scales-justice-close-up-view.jpg',
                'link_texto' => 'Saiba mais',
                'link_url' => 'advogados-inscritos.php'
            ]
        ];
    }

    // Buscar notícias em destaque
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE destaque = 1 AND ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
    $stmt->execute();
    $noticias_destaque = $stmt->fetchAll();

    // Buscar próximos 2 eventos
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE DATE(data_evento) >= CURDATE() AND ativo = 1 ORDER BY data_evento ASC LIMIT 2");
    $stmt->execute();
    $proximos_eventos = $stmt->fetchAll();
    
    if (empty($proximos_eventos)) {
        $stmt = $pdo->prepare("SELECT * FROM agenda WHERE ativo = 1 ORDER BY data_evento DESC LIMIT 2");
        $stmt->execute();
        $proximos_eventos = $stmt->fetchAll();
    }
    
    // Buscar último parecer/deliberação
    $stmt = $pdo->prepare("
        SELECT titulo, tipo, numero_documento, link_url 
        FROM pareceres_deliberacoes 
        WHERE ativo = 1 
        ORDER BY data_documento DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $ultimo_parecer = $stmt->fetch();
    
    // Buscar último anúncio da base de dados
    $stmt = $pdo->prepare("
        SELECT titulo, descricao, link_url, data_inicio, data_fim
        FROM anuncios 
        WHERE ativo = 1 
        AND (data_fim IS NULL OR DATE(data_fim) >= CURDATE())
        ORDER BY ordem_exibicao ASC, data_inicio DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $ultimo_anuncio = $stmt->fetch();

} catch (Exception $e) {
    $carousel_slides = [];
    $noticias_destaque = [];
    $proximos_eventos = [];
    $ultimo_parecer = null;
    $ultimo_anuncio = null;
    error_log("Erro na página inicial: " . $e->getMessage());
}

$page_title = "Início";
$meta_description = "Site oficial da Ordem dos Advogados da Guiné-Bissau - OAGB";
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
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        /* Classe para texto longo/resumo */
        .texto-conteudo {
            color: #111923;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
        }
        
        /* Títulos de notícias/artigos */
        .titulo-artigo {
            color: #4D1C21;
            font-family: 'Libre Baskerville', serif;
            font-size: 180%;
        }
        
        /* Slider ajustado para desktop maior */
        #header-carousel {
            max-height: 650px;
            overflow: hidden;
        }
        
        #header-carousel .carousel-item img {
            height: 650px;
            object-fit: cover;
        }
        
        /* Ajustes responsivos para mobile */
        @media (max-width: 768px) {
            /* Menu mobile ao lado do logo */
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
            
            /* Slider responsivo */
            #header-carousel {
                max-height: 400px;
            }
            
            #header-carousel .carousel-item img {
                height: 400px;
            }
            
            .carousel-caption h1 {
                font-size: 1.2rem !important;
                line-height: 1.3 !important;
                text-decoration: none !important;
            }
            
            .carousel-caption h5 {
                font-size: 0.85rem !important;
                margin-bottom: 1rem !important;
                line-height: 1.4 !important;
            }
            
            .carousel-caption {
                padding: 1rem !important;
            }
            
            .carousel-caption .btn {
                padding: 0.5rem 1.5rem !important;
                font-size: 0.9rem !important;
            }
        }
        
        /* Desktop slider altura maior */
        @media (min-width: 769px) {
            #header-carousel .carousel-item img {
                height: 600px;
                object-fit: cover;
            }
            
            .carousel-caption h1 {
                font-size: 2.5rem !important;
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
        
        /* Botões arrow corrigidos - arrow em cima */
        .btn-arrow-only {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 250px;
            border-bottom: 1px solid #111923;
            padding-top: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-arrow-only i {
            position: absolute;
            right: 0;
            top: 0;
            color: #111923;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .btn-arrow-only:hover {
            transform: translateX(5px);
        }
        
        .btn-arrow-only:hover i {
            transform: translateX(5px);
        }
        
        /* Links com underline no hover */
        a.linkSublinhado:hover {
            text-decoration: underline !important;
        }
        
        /* Paleta de cores */
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        .bg-color-5 { background-color: #5a443d; }
        
        /* Paddings reduzidos */
        .facts {
            padding-bottom: 2rem !important;
        }
        
        .section-noticias {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        
        /* Agenda sem fundos coloridos */
        .agenda-evento {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            background: white;
            height: auto;
            min-height: 280px;
        }
        
        .agenda-evento .evento-content {
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .agenda-evento .evento-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
            margin-bottom: 1rem;
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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692" target="_blank"><i class="fab fa-facebook-f fw-normal"></i></a>
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
                <span class="fa fa-bars"></span>
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

        <!-- Carousel dinâmico -->
        <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php 
                $first = true;
                foreach ($carousel_slides as $slide): 
                    $img_path = isset($slide->imagem) && !empty($slide->imagem) ? 
                               (strpos($slide->imagem, 'gestao/') === 0 ? $slide->imagem : 
                                'gestao/assets/uploads/files/' . $slide->imagem) : 
                               'img/close-up-scales-justice.jpg';
                ?>
                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <img class="w-100" src="<?php echo htmlspecialchars($img_path); ?>" alt="Slide">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="text-white mb-md-4 animated zoomIn" style="text-decoration:underline; font-family: 'Libre Baskerville', serif;">
                                <?php echo htmlspecialchars($slide->titulo); ?>
                            </h1>
                            <h5 class="text-white mb-3 animated slideInDown" style="font-family: 'Open Sans', sans-serif;">
                                <?php echo htmlspecialchars($slide->subtitulo); ?>
                            </h5>
                            <?php if (!empty($slide->link_url)): ?>
                            <a href="<?php echo htmlspecialchars($slide->link_url); ?>" class="btn btn-outline-light py-md-3 px-md-5 animated slideInRight">
                                <?php echo htmlspecialchars($slide->link_texto ?? 'Saiba mais'); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                $first = false;
                endforeach; 
                ?>
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
                    <form action="pesquisa.php" method="GET" class="input-group" style="max-width: 600px;">
                        <input type="text" name="q" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa" required>
                        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Facts Start -->
    <div class="container-fluid facts py-5 pt-lg-0">
        <div class="container py-3 pt-lg-0">
            <div class="row gx-0">
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.1s">
                    <div class="bg-color-1 shadow d-flex align-items-top justify-content-center p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/pareceresDeliberacoesBox.png" style="width:92%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <?php if($ultimo_parecer): ?>
                            <a href="<?php echo htmlspecialchars($ultimo_parecer->link_url ?? 'pareceres-deliberacoes.php'); ?>" class="linkSublinhado" style="color:#fff; font-family: 'Libre Baskerville', serif;">
                                <?php echo htmlspecialchars($ultimo_parecer->numero_documento ?? ''); ?> - <?php echo htmlspecialchars(truncate_text($ultimo_parecer->titulo, 40)); ?>
                            </a>
                            <?php else: ?>
                            <a href="pareceres-deliberacoes.php" class="linkSublinhado" style="color:#fff; font-family: 'Libre Baskerville', serif;">
                                CNEF - Deliberação n.º 8/2023
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.3s">
                    <div class="bg-color-3 shadow d-flex align-items-top justify-content-left p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/pesquisaAdvogadosBox.png" style="width:92%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <a href="advogados-inscritos.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Advogados Inscritos</a><br>
                            <a href="pesquisa-advogados.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Pesquisa de Advogados</a><br>
                            <a href="estagiarios-inscritos.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Estagiários</a><br>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow zoomIn" data-wow-delay="0.6s">
                    <div class="bg-color-4 shadow d-flex align-items-top justify-content-center p-4" style="height: 210px;">
                        <div class="ps-4">
                            <img src="img/anunciosBox.png" style="width:52%;height:auto;padding-top:10px;" border="0" alt=""><br><br>
                            <?php if($ultimo_anuncio): ?>
                            <a href="<?php echo htmlspecialchars($ultimo_anuncio->link_url ?? 'anuncios.php'); ?>" class="linkSublinhado" style="color:#fff; font-family: 'Libre Baskerville', serif;">
                                <?php echo htmlspecialchars(truncate_text($ultimo_anuncio->titulo, 50)); ?>
                            </a><br>
                            <span class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif; font-size: 90%;">
                                <?php echo htmlspecialchars(truncate_text($ultimo_anuncio->descricao, 60)); ?>
                            </span>
                            <?php else: ?>
                            <a href="agenda.php" class="linkSublinhado" style="color:#fff; font-family: 'Libre Baskerville', serif;">
                                IX Congresso dos Advogados Guineenses
                            </a><br>
                            <span class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif; font-size: 90%;">
                                23-25 de Junho de 2023, 9h às 16h, Hotel Dunia
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Facts End -->

    <!-- Blog Start -->
    <?php if (!empty($noticias_destaque)): ?>
    <div class="container-fluid section-noticias wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans', sans-serif;">Artigos recentes</h5>
                <h1 class="mb-0" style="color:#5B463F; font-family: 'Libre Baskerville', serif; font-weight: bold; font-size:300%;">Últimas notícias</h1>
            </div>
            <div class="row g-5">
                <?php 
                $delay = 0.3;
                foreach ($noticias_destaque as $noticia): 
                ?>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="<?php echo $delay; ?>s">
                    <div class="blog-item bg-light rounded overflow-hidden">
                        <div class="blog-img position-relative overflow-hidden">
                            <?php 
                            $img_noticia = !empty($noticia->imagem_destaque) ? 
                                          'gestao/assets/uploads/files/' . $noticia->imagem_destaque : 
                                          'img/Asset 7-100.jpg';
                            ?>
                            <img class="img-fluid" src="<?php echo htmlspecialchars($img_noticia); ?>" alt="<?php echo htmlspecialchars($noticia->titulo); ?>">
                        </div>
                        <div class="p-4">
                            <h4 class="mb-3 titulo-artigo">
                                <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="linkSublinhado" style="color:#4D1C21;">
                                    <?php echo htmlspecialchars($noticia->titulo); ?>
                                </a>
                            </h4>
                            <div class="d-flex mb-3">
                                <small style="color:#615759; font-family: 'Open Sans', sans-serif; font-weight: 300; font-size:90%;">
                                    <?php echo format_date_pt($noticia->data_publicacao); ?>
                                </small>
                            </div>
                            <p class="texto-conteudo mb-3">
                                <?php echo htmlspecialchars(truncate_text($noticia->resumo, 120)); ?>
                            </p>
                            <a href="artigo.php?id=<?php echo $noticia->id; ?>&slug=<?php echo urlencode($noticia->slug); ?>" class="d-block">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
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
    <div class="container-fluid py-4 wow fadeInUp" data-wow-delay="0.1s" style="background: #f8f9fa;">
        <div class="container py-4">
            <div class="section-title text-center position-relative pb-3 mb-4 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans', sans-serif;">Próximos Eventos</h5>
                <h1 class="mb-0" style="color:#5B463F; font-family: 'Libre Baskerville', serif; font-weight: bold; font-size:280%;">Agenda</h1>
            </div>
            <?php if (!empty($proximos_eventos)): ?>
            <div class="row g-4">
                <?php 
                $delay = 0.3;
                foreach ($proximos_eventos as $evento): 
                ?>
                <div class="col-lg-6 wow slideInUp" data-wow-delay="<?php echo $delay; ?>s">
                    <div class="agenda-evento">
                        <?php if (!empty($evento->imagem_destaque)): ?>
                        <!-- Foto por cima do título quando tem imagem -->
                        <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" 
                             alt="<?php echo htmlspecialchars($evento->titulo); ?>" 
                             class="evento-image">
                        <?php endif; ?>
                        <div class="evento-content">
                            <h4 class="mb-3 titulo-artigo">
                                <a href="evento.php?id=<?php echo $evento->id; ?>" class="linkSublinhado" style="color:#4D1C21;">
                                    <?php echo htmlspecialchars($evento->titulo); ?>
                                </a>
                            </h4>
                            <p class="mb-2" style="color:#615759; font-family: 'Open Sans', sans-serif; font-size: 0.9rem;">
                                <i class="far fa-calendar-alt me-2"></i><?php echo format_date_pt($evento->data_evento); ?>
                            </p>
                            <?php if (!empty($evento->local_evento)): ?>
                            <p class="mb-2" style="color:#615759; font-family: 'Open Sans', sans-serif; font-size: 0.85rem;">
                                <i class="fa fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($evento->descricao)): ?>
                            <p class="texto-conteudo mb-3">
                                <?php echo htmlspecialchars(truncate_text($evento->descricao, 150)); ?>
                            </p>
                            <?php endif; ?>
                            <a href="evento.php?id=<?php echo $evento->id; ?>" class="d-inline-block">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
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
            <?php else: ?>
            <div class="text-center">
                <div class="bg-white rounded shadow-sm p-5">
                    <i class="fa fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted" style="font-family: 'Libre Baskerville', serif;">Nenhum evento agendado</h5>
                    <p class="text-muted" style="font-family: 'Open Sans', sans-serif;">Consulte novamente em breve para ver os próximos eventos da OAGB.</p>
                    <a href="agenda.php" class="btn btn-outline-primary">Ver Agenda</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Agenda End -->

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

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
</body>
</html>