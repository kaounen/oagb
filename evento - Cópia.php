<?php
require_once 'connect.php';
require_once 'includes/functions.php';

// Obter ID do evento
$evento_id = $_GET['id'] ?? 0;

if (empty($evento_id)) {
    header('Location: agenda.php');
    exit;
}

try {
    // Buscar o evento pelo ID
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ? AND ativo = 1");
    $stmt->execute([$evento_id]);
    $evento = $stmt->fetch();

    if (!$evento) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }
    
    // Buscar imagens adicionais do evento
    $stmt = $pdo->prepare("SELECT * FROM agenda_imagens WHERE agenda_id = ? ORDER BY ordem_exibicao ASC");
    $stmt->execute([$evento->id]);
    $imagens_evento = $stmt->fetchAll();

    // Buscar eventos relacionados
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id != ? AND ativo = 1 ORDER BY data_evento DESC LIMIT 3");
    $stmt->execute([$evento->id]);
    $eventos_relacionados = $stmt->fetchAll();
    
    // Buscar apenas 2 anúncios DA BASE DE DADOS
    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE ativo = 1 ORDER BY ordem_exibicao ASC, id DESC LIMIT 2");
    $stmt->execute();
    $anuncios = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erro ao carregar evento: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    include '500.php';
    exit;
}

// Configurar meta tags
$meta_title = !empty($evento->meta_title) ? $evento->meta_title : $evento->titulo . " - OAGB";
$meta_description = !empty($evento->meta_description) ? $evento->meta_description : $evento->descricao;
$meta_image = !empty($evento->og_image) ? "gestao/assets/uploads/files/" . $evento->og_image : 
              (!empty($evento->imagem_destaque) ? "gestao/assets/uploads/files/" . $evento->imagem_destaque : "img/logo3.png");
$meta_url = "https://oagb.gw/evento.php?id=" . $evento->id;
$meta_type = "event";

$page_title = "Agenda";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    
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
    
    <style>
        .texto-conteudo {
            color: #111923;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
        }
        
        .titulo-evento {
            color: #4D1C21;
            font-family: 'Libre Baskerville', serif;
            font-size: 2.2rem;
            font-weight: 400;
            margin-bottom: 1rem !important;
        }
        
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        
        /* Botões arrow corrigidos */
        .btn-arrow-only {
            position: relative;
            display: inline-block;
            width: 100%;
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
        a.linkSublinhado:hover,
        a.text-decoration-none:hover {
            text-decoration: underline !important;
        }
        
        /* Header PADRÃO DESKTOP E MOBILE */
        .bg-header {
            background: linear-gradient(rgba(9, 30, 62, .7), rgba(9, 30, 62, .7)), url(img/brass-scales-justice-close-up-view.jpg) center center no-repeat;
            background-size: cover;
            margin-bottom: 30px !important; /* Reduzido */
        }
        
        /* Ajustes responsivos */
        @media (max-width: 768px) {
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
            
            .main-content,
            .sidebar-content {
                padding: 0 15px;
            }
            
            .bg-header {
                margin-bottom: 20px !important;
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
        
        /* Breadcrumbs com separador circular */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "•";
            color: rgba(255,255,255,0.7);
            padding: 0 8px;
            font-size: 8px;
            vertical-align: middle;
        }
        
        .breadcrumb-item a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        /* Evento relacionado com resumo */
        .evento-relacionado {
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .evento-relacionado:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .evento-relacionado .resumo {
            font-family: 'Open Sans', sans-serif;
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        /* Botões de compartilhar só ícones */
        .share-icons a {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .share-icons a:hover {
            transform: translateY(-3px);
        }
        
        /* Botões de ação com cores castanho/dourado */
        .action-buttons {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            background-color: transparent;
            color: #8B6B47;
            border: 1px solid #8B6B47;
            transition: all 0.3s;
        }
        
        .action-buttons .btn:hover {
            background-color: #8B6B47;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Anúncios sidebar com separador */
        .announcement-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
        }
        
        .announcement-separator {
            border: 0;
            height: 1px;
            background: rgba(255,255,255,0.3);
            margin: 15px 0;
        }
        
        /* Slider de imagens */
        .evento-carousel .owl-item img {
            height: 400px;
            object-fit: cover;
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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692"><i class="fab fa-facebook-f fw-normal"></i></a>
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
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <img src="img/logo3.png" style="width:70%;height:auto;padding-top:5%;" align="center" border="0" alt="OAGB Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link">INÍCIO</a>
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

        <!-- Header com Background Image -->
        <div class="container-fluid bg-primary py-5 bg-header">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                            <li class="breadcrumb-item"><a href="agenda.php">Agenda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Evento</li>
                        </ol>
                    </nav>
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
                    <form action="pesquisa.php" method="GET" class="input-group" style="max-width: 600px;">
                        <input type="text" name="q" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa" required>
                        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Action Buttons -->
    <div class="container action-buttons">
        <button onclick="window.history.back()" class="btn btn-sm">
            <i class="bi bi-arrow-left"></i> Voltar
        </button>
        <button onclick="window.print()" class="btn btn-sm">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <button onclick="shareEvent()" class="btn btn-sm">
            <i class="bi bi-share"></i> Partilhar
        </button>
        <button onclick="translatePage()" class="btn btn-sm">
            <i class="bi bi-translate"></i> Traduzir
        </button>
        <button onclick="addToCalendar()" class="btn btn-sm">
            <i class="bi bi-calendar-plus"></i> Adicionar ao Calendário
        </button>
    </div>

    <!-- Event Detail Start -->
    <div class="container-fluid py-3">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8 main-content">
                    <!-- Event Content -->
                    <div class="bg-light rounded p-4">
                        <h1 class="titulo-evento">
                            <?php echo htmlspecialchars($evento->titulo); ?>
                        </h1>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="far fa-calendar-alt text-primary me-2"></i>
                                <span class="text-muted" style="font-family: 'Open Sans', sans-serif;"><?php echo format_date_pt($evento->data_evento); ?></span>
                            </div>
                            
                            <?php if (!empty($evento->local_evento)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-muted" style="font-family: 'Open Sans', sans-serif;"><?php echo htmlspecialchars($evento->local_evento); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($evento->imagem_destaque) || !empty($imagens_evento)): ?>
                        <div class="mb-4">
                            <?php if (count($imagens_evento) > 1 || (!empty($evento->imagem_destaque) && count($imagens_evento) > 0)): ?>
                            <!-- Slider para múltiplas imagens -->
                            <div class="owl-carousel evento-carousel">
                                <?php if (!empty($evento->imagem_destaque)): ?>
                                <div class="item">
                                    <img class="img-fluid rounded" src="gestao/assets/uploads/files/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" alt="">
                                </div>
                                <?php endif; ?>
                                <?php foreach ($imagens_evento as $img): ?>
                                <div class="item">
                                    <img class="img-fluid rounded" src="gestao/assets/uploads/files/<?php echo htmlspecialchars($img->imagem); ?>" alt="">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <!-- Imagem única -->
                            <img class="img-fluid w-100 rounded" src="gestao/assets/uploads/files/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" alt="<?php echo htmlspecialchars($evento->titulo); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="content texto-conteudo" style="line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($evento->descricao)); ?>
                        </div>
                        
                        <?php if (!empty($evento->contacto_info)): ?>
                        <div class="mt-4 p-3 bg-white rounded">
                            <h5 class="mb-3" style="font-family: 'Libre Baskerville', serif;">Informações de Contacto</h5>
                            <p class="texto-conteudo"><?php echo nl2br(htmlspecialchars($evento->contacto_info)); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Share Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                            <div>
                                <h6 class="mb-0" style="font-family: 'Open Sans', sans-serif;">Compartilhar:</h6>
                            </div>
                            <div class="share-icons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($meta_url); ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($meta_url); ?>&text=<?php echo urlencode($evento->titulo); ?>" 
                                   target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($meta_url); ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="whatsapp://send?text=<?php echo urlencode($evento->titulo . ' - ' . $meta_url); ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 sidebar-content">
                    <!-- Related Events -->
                    <?php if (!empty($eventos_relacionados)): ?>
                    <div class="bg-light rounded p-4 mb-4">
                        <h4 class="mb-4" style="color:#5B463F;font-family: 'Libre Baskerville', serif;">Outros Eventos</h4>
                        <?php 
                        $count = 0;
                        foreach ($eventos_relacionados as $relacionado): 
                            $count++;
                        ?>
                        <div class="evento-relacionado">
                            <h6 style="font-family: 'Libre Baskerville', serif;">
                                <a href="evento.php?id=<?php echo $relacionado->id; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($relacionado->titulo); ?>
                                </a>
                            </h6>
                            <p class="resumo">
                                <?php echo htmlspecialchars(truncate_text($relacionado->descricao, 80)); ?>
                            </p>
                            <small class="text-muted" style="font-family: 'Open Sans', sans-serif;">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?php echo format_date_pt($relacionado->data_evento); ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-4">
                            <a href="agenda.php" class="d-block">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Anúncios DINÂMICOS DA BD -->
                    <?php if (!empty($anuncios)): ?>
                    <div class="bg-color-4 rounded p-4 text-white">
                        <h4 class="mb-4 text-white" style="font-family: 'Libre Baskerville', serif;">Anúncios</h4>
                        <?php 
                        $anuncio_count = 0;
                        foreach ($anuncios as $anuncio): 
                            $anuncio_count++;
                        ?>
                        <div class="announcement-item">
                            <h6 class="text-white" style="font-family: 'Libre Baskerville', serif;">
                                <?php if (!empty($anuncio->link_url)): ?>
                                <a href="<?php echo htmlspecialchars($anuncio->link_url); ?>" class="text-white text-decoration-none">
                                    <?php echo htmlspecialchars($anuncio->titulo); ?>
                                </a>
                                <?php else: ?>
                                <?php echo htmlspecialchars($anuncio->titulo); ?>
                                <?php endif; ?>
                            </h6>
                            <p class="mb-2" style="font-family: 'Open Sans', sans-serif; font-size: 0.9rem;">
                                <?php echo htmlspecialchars(truncate_text($anuncio->descricao, 100)); ?>
                            </p>
                        </div>
                        <?php if($anuncio_count < count($anuncios)): ?>
                        <hr class="announcement-separator">
                        <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="mt-4">
                            <a href="anuncios.php" class="d-block">
                                <div class="btn-arrow-only" style="border-color: white;">
                                    <i class="bi bi-arrow-right" style="color: white;"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Event Detail End -->

    <?php include 'includes/footer.php'; ?>

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
    
    <script>
    // Inicializar carousel de imagens
    $(document).ready(function(){
        if($('.evento-carousel').length) {
            $('.evento-carousel').owlCarousel({
                autoplay: true,
                smartSpeed: 1000,
                loop: true,
                nav: true,
                dots: false,
                items: 1,
                navText : ['<i class="bi bi-chevron-left"></i>','<i class="bi bi-chevron-right"></i>']
            });
        }
    });
    
    // Função compartilhar
    function shareEvent() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($evento->titulo); ?>',
                text: '<?php echo addslashes(truncate_text($evento->descricao, 100)); ?>',
                url: window.location.href
            });
        } else {
            alert('Use os botões de redes sociais abaixo para compartilhar');
        }
    }
    
    // Função traduzir
    function translatePage() {
        window.open('https://translate.google.com/translate?u=' + encodeURIComponent(window.location.href));
    }
    
    // Adicionar ao calendário
    function addToCalendar() {
        const event = {
            title: '<?php echo addslashes($evento->titulo); ?>',
            start: '<?php echo date('Y-m-d\TH:i:s', strtotime($evento->data_evento)); ?>',
            end: '<?php echo date('Y-m-d\TH:i:s', strtotime($evento->data_evento . ' +3 hours')); ?>',
            description: '<?php echo addslashes(truncate_text($evento->descricao, 200)); ?>',
            location: '<?php echo addslashes($evento->local_evento ?? 'OAGB'); ?>'
        };
        
        // Criar link para Google Calendar
        const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.title)}&dates=${event.start.replace(/[-:]/g, '')}/${event.end.replace(/[-:]/g, '')}&details=${encodeURIComponent(event.description)}&location=${encodeURIComponent(event.location)}`;
        
        window.open(googleUrl, '_blank');
    }
                