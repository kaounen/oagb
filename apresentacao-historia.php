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
    
    <style>
        /* Styles for page consistency */
        .quick-actions .btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .quick-actions .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .bg-header .navbar { background: transparent !important; }

        /* Mobile contacts styling */
        .mobile-contacts { line-height: 1.2; }
        .contact-line { margin-bottom: 0.1rem; font-size: 0.85rem; line-height: 1.1; }
        .contact-line:last-child { margin-bottom: 0; }

        /* Mobile header carousel styling */
        .mobile-header-slide {
            position: relative;
            overflow: hidden;
            background-color: #091E3E;
        }

        /* Mobile navbar adjustments - matching index.php */
        @media (max-width: 991.98px) {
            .mobile-header-slide .navbar {
                padding: 1rem 1rem;
                margin-bottom: 2rem;
                background: transparent !important;
            }

            /* Logo centralizado e menu com texto MENU na linha seguinte */
            .mobile-header-slide .navbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                padding: 1rem !important;
            }

            .mobile-header-slide .navbar-brand {
                margin-bottom: 1.5rem;
                order: 1;
            }

            .mobile-header-slide .navbar-brand img {
                width: 220px !important;
                max-width: 90% !important;
            }

            /* Botão menu com texto MENU em linha separada */
            .mobile-header-slide .navbar-toggler {
                order: 2;
                margin: 0 auto !important;
                position: relative !important;
                right: auto !important;
                top: auto !important;
                transform: none !important;
                display: block !important;
            }

            /* Adicionar texto MENU ao botão */
            .mobile-header-slide .navbar-toggler::after {
                content: ' MENU';
                margin-left: 8px;
                font-family: 'Open Sans', sans-serif;
                font-weight: 600;
                font-size: 14px;
            }

  
            .mobile-header-slide .navbar-collapse {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.2);
            }

            .mobile-header-slide .navbar-nav {
                margin-bottom: 2rem;
                text-align: center;
            }

            .mobile-header-slide .navbar-nav .nav-link {
                color: white !important;
                font-size: 1.1rem;
                font-weight: 600;
                padding: 1rem !important;
                margin: 0.5rem 0;
                text-align: center;
            }

            .mobile-header-slide .navbar-nav .dropdown-menu {
                background: rgba(255, 255, 255, 0.95);
                border: none;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            }

            .mobile-header-slide .navbar-nav .dropdown-menu .dropdown-item {
                color: #091E3E;
                padding: 0.8rem 1.5rem;
                font-weight: 500;
            }

            .mobile-header-slide .navbar-nav .dropdown-menu .dropdown-item:hover {
                background-color: var(--primary);
                color: white;
            }

            /* Hide search button in mobile navbar - already exists in header */
            .mobile-header-slide .navbar .btn {
                display: none !important;
            }

            /* Mobile slide content positioning and interactions */
            .mobile-slide-content {
                position: absolute !important;
                bottom: 140px !important;
                z-index: 600 !important;
                transition: all 0.3s ease !important;
                pointer-events: auto !important;
            }

            /* When navbar is expanded, move content up even more */
            .navbar-collapse.show ~ .mobile-slide-content,
            .navbar-collapse.collapsing ~ .mobile-slide-content {
                bottom: 200px !important;
                z-index: 500 !important;
            }

            /* Ensure menu content is above slide content when expanded */
            .mobile-header-slide .navbar-collapse {
                z-index: 1001 !important;
                background: rgba(0,0,0,0.9) !important;
                border-radius: 10px !important;
                margin-top: 1rem !important;
                backdrop-filter: blur(10px) !important;
            }

            /* Force menu to be above indicators when expanded */
            .mobile-header-slide .navbar-collapse.show,
            .mobile-header-slide .navbar-collapse.collapsing {
                z-index: 1100 !important;
            }

            /* Ensure menu links are always clickable */
            .mobile-header-slide .navbar-nav .nav-link,
            .mobile-header-slide .navbar-nav .dropdown-item {
                position: relative;
                z-index: 1101 !important;
                pointer-events: auto !important;
            }

            /* Make content fully interactive */
            .mobile-slide-content h1,
            .mobile-slide-content p,
            .mobile-slide-content a {
                pointer-events: auto !important;
                user-select: text !important;
            }

            /* Remove any blocking elements */
            .mobile-header-slide::before,
            .mobile-header-slide::after {
                display: none !important;
            }

            /* Ensure sufficient height for content */
            .mobile-header-slide {
                min-height: 110vh !important;
            }

            /* Mobile contact info styling */
            .contact-line strong {
                text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
                font-weight: 600;
            }

            /* Ensure proper z-index layering */
            .mobile-header-slide .contact-line,
            .mobile-header-slide .navbar-brand,
            .mobile-header-slide .navbar-toggler,
            .mobile-header-slide .navbar-nav {
                position: relative;
                z-index: 1050;
            }

            /* Search modal z-index fixes */
            #searchModal {
                z-index: 2050 !important;
            }

            #searchModal .modal-dialog {
                z-index: 2051 !important;
            }

            .modal-backdrop {
                z-index: 2049 !important;
            }

          }

        /* Original page styles */
        .timeline { position: relative; padding: 20px 0; }
        .timeline::before { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: #c18046; transform: translateX(-50%); }
        .timeline-item { position: relative; padding: 20px 0; }
        .timeline-item::before { content: ''; position: absolute; left: 50%; top: 30px; width: 20px; height: 20px; border-radius: 50%; background: #c18046; border: 4px solid white; transform: translateX(-50%); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .timeline-content { width: 45%; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .timeline-item:nth-child(odd) .timeline-content { margin-left: auto; }
        .timeline-year { font-size: 1.5rem; font-weight: bold; color: #c18046; margin-bottom: 10px; }
        .stats-box { background: white; border-radius: 15px; padding: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .stats-box:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .stats-number { font-size: 3rem; font-weight: bold; color: #c18046; margin-bottom: 10px; }
        .stats-label { font-size: 1.1rem; color: #666; }
        .values-card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 20px; border-left: 5px solid #c18046; transition: all 0.3s ease; }
        .values-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.1); transform: translateX(10px); }
        .values-card h4 { color: #c18046; margin-bottom: 15px; }
        .section-divider { height: 2px; background: linear-gradient(to right, transparent, #c18046, transparent); margin: 50px 0; }
        .content-section { margin-bottom: 50px; }
        .content-section h3 { font-family: 'Libre Baskerville', serif; color: #4D1C21; margin-bottom: 30px; position: relative; padding-bottom: 15px; }
        .content-section h3::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 3px; background: #c18046; }
        @media (max-width: 768px) {
            .timeline::before { left: 30px; }
            .timeline-item::before { left: 30px; }
            .timeline-content { width: calc(100% - 60px); margin-left: 60px !important; }
        }

        /* Menu desktop com fundo branco no scroll - barra de endereços sempre visível */
        @media (min-width: 992px) {
            /* Fazer o navbar fixo no topo com posicionamento consistente */
            .navbar-dark {
                position: fixed !important;
                top: 45px !important; /* Sempre abaixo da barra de endereços */
                left: 0 !important;
                right: 0 !important;
                z-index: 1030 !important;
                width: 100% !important;
                transition: all 0.3s ease !important;
                background: transparent !important;
                padding: 15px 0 !important; /* Padding padrão */
            }

            /* Garantir estrutura Bootstrap consistente - SEMPRE */
            .navbar-dark.navbar-expand-lg {
                flex-wrap: nowrap !important;
            }

            .navbar-dark .navbar-collapse {
                flex-basis: auto !important;
            }

            /* Manter padding lateral consistente - SEMPRE */
            .navbar-dark.px-5 {
                padding-left: 3rem !important;
                padding-right: 3rem !important;
            }

            /* Logo padrão - posicionamento consistente */
            .navbar-dark .navbar-brand {
                padding: 0 !important; /* Sem padding extra */
            }

            .navbar-dark .navbar-brand img {
                width: 70% !important; /* Tamanho original */
                height: auto !important;
                padding-top: 5% !important; /* Padding-top original */
                transition: all 0.3s ease !important;
            }

            /* Navbar mais compacto durante scroll - manter posicionamentos */
            .navbar-scrolled {
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
                /* Manter sempre na mesma posição mas reduzir altura */
                top: 45px !important;
                padding: 8px 0 !important; /* Padding reduzido para navbar mais compacto */
            }

            /* Logo menor durante scroll - manter posicionamento horizontal */
            .navbar-scrolled .navbar-brand {
                padding: 0 !important; /* Manter o mesmo padding do estado normal */
            }

            .navbar-scrolled .navbar-brand img {
                width: 50% !important; /* Logo menor durante scroll */
                height: auto !important;
                padding-top: 2% !important; /* Reduzir apenas padding-top para compactar */
                transition: all 0.3s ease !important;
                filter: none !important;
            }

            /* Remover o espaçamento do container do navbar */
            .container-fluid.position-relative.p-0.d-none.d-lg-block {
                margin-bottom: 0 !important;
            }

            /* Ajustar o conteúdo principal para começar logo após o navbar fixo */
            .bg-header {
                margin-top: 0 !important; /* Remover margin para eliminar espaço branco */
                position: relative;
                top: 0 !important;
            }

            /* Manter a barra de endereços sempre visível */
            .bg-dark {
                position: fixed !important;
                top: 0 !important;
                width: 100% !important;
                z-index: 1040 !important;
                transition: all 0.3s ease !important;
            }

            /* Mudança da barra de endereços durante scroll - fundo branco */
            .topbar-scrolled {
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            }

            /* Cores douradas #B1A276 para textos da barra de endereços quando scrolled */
            .topbar-scrolled .text-light {
                color: #B1A276 !important;
            }

            .topbar-scrolled .btn-outline-light {
                border-color: #B1A276 !important;
                color: #B1A276 !important;
            }

            .topbar-scrolled .btn-outline-light:hover {
                background-color: #B1A276 !important;
                border-color: #B1A276 !important;
                color: white !important;
            }

            /* Forçar mudança de cor em todos os elementos da topbar */
            .topbar-scrolled small {
                color: #B1A276 !important;
            }

            .topbar-scrolled i {
                color: #B1A276 !important;
            }

            /* Cores douradas #B1A276 para links do navbar quando scrolled */
            .navbar-scrolled .navbar-nav .nav-link {
                color: #B1A276 !important;
            }

            .navbar-scrolled .navbar-nav .nav-link:hover {
                color: #9d8f64 !important; /* Tom mais escuro de #B1A276 */
            }

            .navbar-scrolled .navbar-nav .nav-link.active {
                color: #B1A276 !important;
                font-weight: 600;
            }

            /* Garantir que todos os itens do menu principal tenham a cor dourada */
            .navbar-scrolled .navbar-nav .nav-item .nav-link {
                color: #B1A276 !important;
            }

            .navbar-scrolled .navbar-nav .dropdown .nav-link {
                color: #B1A276 !important;
            }

            .navbar-scrolled .navbar-nav .dropdown-toggle {
                color: #B1A276 !important;
            }

            /* Botão de pesquisa também dourado #B1A276 quando scrolled */
            .navbar-scrolled .btn {
                color: #B1A276 !important;
            }

            .navbar-scrolled .btn:hover {
                color: #9d8f64 !important;
            }

            /* Ajustar padding do conteúdo para não ficar sobreposto pelo navbar fixo */
            .bg-header {
                padding-top: 8rem !important;
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

    <!-- Desktop Topbar Start -->
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
    <!-- Desktop Topbar End -->

    <!-- Desktop Navbar & Header Start -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>

        <!-- Header Content -->
        <div class="container-fluid bg-primary pb-5 pt-lg-7 bg-header">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>

                    <!-- Breadcrumbs -->
                    <div class="mb-3">
                        <a href="index.php" class="h5 text-white">Início</a>
                        <i class="far fa-circle text-white px-2"></i>
                        <a href="#" class="h5 text-white">Ordem</a>
                        <i class="far fa-circle text-white px-2"></i>
                        <a href="apresentacao-historia.php" class="h5 text-white"><?php echo $page_title; ?></a>
                    </div>

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
    <!-- Desktop Navbar & Header End -->

    <!-- Mobile Header Start -->
    <div class="d-lg-none">
        <!-- Mobile Background -->
        <div class="mobile-header-slide" style="background-image: url('img/close-up-scales-justice.jpg'); background-size: cover; background-position: center; min-height: 110vh; position: relative;">

            <!-- Mobile Contact Info -->
            <div class="container-fluid px-3 py-3" style="position: absolute; top: 0; left: 0; right: 0; z-index: 1000;">
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
                    </div>
                </div>
            </div>

            <!-- Mobile Navbar -->
            <div class="container-fluid position-relative p-0" style="position: absolute; top: 80px; left: 0; right: 0; z-index: 1000;">
                <?php include 'includes/navbar.php'; ?>
            </div>

            <!-- Mobile Header Content -->
            <div class="mobile-slide-content" style="position: absolute; bottom: 140px; left: 0; right: 0; z-index: 500; padding: 1rem 1.5rem;">
                <div class="text-center" style="padding: 0;">
                    <h1 class="text-white mb-2" style="font-family: 'Open Sans', sans-serif; font-size: 1.25rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.9);">
                        <?php echo $page_title; ?>
                    </h1>

                    <!-- Breadcrumbs -->
                    <div class="mb-3">
                        <a href="index.php" class="h5 text-white">Início</a>
                        <i class="far fa-circle text-white px-2"></i>
                        <a href="#" class="h5 text-white">Ordem</a>
                        <i class="far fa-circle text-white px-2"></i>
                        <a href="apresentacao-historia.php" class="h5 text-white"><?php echo $page_title; ?></a>
                    </div>

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
    <!-- Mobile Header End -->

    <!-- Full Screen Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" style="z-index: 2050;">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(9, 30, 62, .7); z-index: 2051;">
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

    <!-- Quick Actions Functions -->
    <script>
        // Function to share page
        function sharePage() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Confira esta página da OAGB',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copiado para a área de transferência!');
                }).catch(() => {
                    // Further fallback: show URL in prompt
                    prompt('Copie este link:', window.location.href);
                });
            }
        }

        // Function to translate page (Google Translate)
        function translatePage() {
            const googleTranslateScript = document.getElementById('google-translate-script');

            if (!googleTranslateScript) {
                // Add Google Translate script
                const script = document.createElement('script');
                script.id = 'google-translate-script';
                script.type = 'text/javascript';
                script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                document.head.appendChild(script);

                // Initialize Google Translate
                window.googleTranslateElementInit = function() {
                    new google.translate.TranslateElement({
                        pageLanguage: 'pt',
                        includedLanguages: 'en,fr,es,pt',
                        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                    }, 'google_translate_element');
                };

                // Create translate element container if it doesn't exist
                if (!document.getElementById('google_translate_element')) {
                    const translateDiv = document.createElement('div');
                    translateDiv.id = 'google_translate_element';
                    translateDiv.style.position = 'fixed';
                    translateDiv.style.top = '10px';
                    translateDiv.style.right = '10px';
                    translateDiv.style.zIndex = '9999';
                    translateDiv.style.backgroundColor = 'white';
                    translateDiv.style.padding = '10px';
                    translateDiv.style.borderRadius = '5px';
                    translateDiv.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
                    document.body.appendChild(translateDiv);
                }
            } else {
                // Toggle translate element visibility
                const translateElement = document.getElementById('google_translate_element');
                if (translateElement) {
                    translateElement.style.display = translateElement.style.display === 'none' ? 'block' : 'none';
                }
            }
        }
    </script>

    <!-- Desktop Navbar Scroll Effect - manter barra de endereços visível -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar-dark');
            const topbar = document.querySelector('.bg-dark'); // Desktop topbar

            if (navbar && window.innerWidth >= 992) { // Só aplicar em desktop
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 100) {
                        navbar.classList.add('navbar-scrolled');
                        if (topbar) {
                            topbar.classList.add('topbar-scrolled');
                        }
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                        if (topbar) {
                            topbar.classList.remove('topbar-scrolled');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>