<?php
// Iniciar sess�o e incluir ficheiros necess�rios
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Buscar conte�do da p�gina
try {
    $stmt = $pdo->prepare("
        SELECT * FROM paginas_ordem 
        WHERE slug = 'apresentacao-historia' AND ativo = 1
    ");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    // Se n�o existir na BD, usar conte�do padr�o
    if (!$pagina) {
        $pagina = (object)[
            'titulo' => 'Apresenta��o e Hist�ria',
            'conteudo' => '
                <h3>A Nossa Hist�ria</h3>
                <p>A Ordem dos Advogados da Guin�-Bissau (OAGB) � uma associa��o p�blica representativa dos licenciados em Direito que, em conformidade com os preceitos do presente Estatuto e demais disposi��es legais aplic�veis, exercem a advocacia.</p>
                
                <h3>Miss�o</h3>
                <p>A OAGB tem como miss�o assegurar o acesso dos cidad�os ao Direito e aos tribunais, bem como regular o exerc�cio da profiss�o de advogado, defender a fun��o social, a dignidade e o prest�gio da advocacia e promover o respeito pelos princ�pios do Estado de Direito e dos Direitos Humanos.</p>
                
                <h3>Atribui��es</h3>
                <ul>
                    <li>Defender o Estado de Direito e os direitos, liberdades e garantias dos cidad�os</li>
                    <li>Assegurar o acesso ao direito, nomeadamente o patroc�nio judici�rio</li>
                    <li>Regular o exerc�cio da advocacia</li>
                    <li>Exercer o poder disciplinar sobre os advogados e advogados estagi�rios</li>
                    <li>Defender os interesses, prerrogativas e direitos dos advogados</li>
                    <li>Promover a forma��o profissional e cultural dos advogados</li>
                    <li>Pronunciar-se sobre projectos de diplomas legislativos</li>
                </ul>
                
                <h3>Valores</h3>
                <p>Os nossos valores fundamentais incluem:</p>
                <ul>
                    <li><strong>Independ�ncia</strong> - Atuamos com total independ�ncia</li>
                    <li><strong>Integridade</strong> - Pautamos pela �tica e honestidade</li>
                    <li><strong>Excel�ncia</strong> - Buscamos a excel�ncia profissional</li>
                    <li><strong>Justi�a</strong> - Defendemos a justi�a e equidade</li>
                    <li><strong>Solidariedade</strong> - Promovemos a entreajuda profissional</li>
                </ul>
            ',
            'imagem' => null
        ];
    }
    
    // Buscar estat�sticas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
    $stmt->execute();
    $total_advogados = $stmt->fetch()->total;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
    $stmt->execute();
    $total_estagiarios = $stmt->fetch()->total;
    
} catch (Exception $e) {
    error_log("Erro ao buscar p�gina: " . $e->getMessage());
}

$page_title = htmlspecialchars($pagina->titulo);
$meta_description = "Conhe�a a hist�ria, miss�o e valores da Ordem dos Advogados da Guin�-Bissau";
$apresentacao_header_background_style = "margin-bottom: 90px; background: url('uploads/close-up-scales-justice-original-azul.jpg') center center no-repeat; background-size: cover;";
$apresentacao_header_image = 'uploads/close-up-scales-justice-original-azul.jpg';
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
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* Quick actions */
        .quick-actions .btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .carousel-caption .quick-actions .btn {
            width: 40px !important;
            height: 40px !important;
            min-height: 40px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            line-height: 1 !important;
            border: 2px solid rgba(255,255,255,0.8) !important;
            background-color: transparent !important;
            color: #fff !important;
            box-shadow: none !important;
        }

        .carousel-caption .quick-actions .btn i {
            color: #fff !important;
        }

        .quick-actions .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .quick-actions {
                margin-top: 1rem !important;
            }

            .quick-actions .btn {
                width: 35px;
                height: 35px;
                margin: 0.2rem !important;
            }

            .carousel-caption .quick-actions .btn {
                width: 35px !important;
                height: 35px !important;
                min-height: 35px !important;
            }
        }

        .navbar-dark .btn i.fa-search {
            color: white !important;
        }

        .navbar-dark .btn.text-primary {
            color: white !important;
        }

        .navbar-dark .btn.text-primary i {
            color: inherit !important;
        }

        .navbar-dark .btn-outline-light {
            border-color: rgba(255,255,255,0.85) !important;
            color: #fff !important;
            transition: color 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
        }

        .navbar-dark .btn-outline-light i {
            color: #fff !important;
            transition: color 0.3s ease;
        }

        .navbar-dark .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.12) !important;
            color: #fff !important;
        }

        .navbar-dark .navbar-nav .nav-link,
        .navbar-dark .navbar-nav .nav-link:focus,
        .navbar-dark .navbar-nav .nav-link:active {
            color: white !important;
            transition: color 0.3s ease;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: rgba(255,255,255,0.85) !important;
        }
        /* Slider overlays and layout */
        #header-carousel .carousel-item,
        #header-carousel-mobile .carousel-item {
            position: relative;
            overflow: visible;
        }

        #header-carousel .carousel-item img,
        #header-carousel-mobile .carousel-item img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Overlay para melhorar legibilidade do texto */
        #header-carousel .carousel-item::before,
        #header-carousel-mobile .carousel-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.25) 0%, rgba(0, 0, 0, 0.15) 50%, rgba(0, 0, 0, 0.3) 100%);
            z-index: 1;
            pointer-events: none;
        }

        #header-carousel .carousel-caption,
        #header-carousel-mobile .carousel-caption {
            position: relative;
            z-index: 2000;
            background: none !important;
            pointer-events: auto !important;
        }

        #header-carousel .carousel-caption .btn,
        #header-carousel-mobile .carousel-caption .btn {
            pointer-events: auto !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 3000 !important;
            display: inline-block !important;
            text-decoration: none !important;
            border: 2px solid rgba(255,255,255,0.8) !important;
            background-color: rgba(255,255,255,0.1) !important;
            backdrop-filter: blur(10px) !important;
            transition: all 0.3s ease !important;
            min-height: 45px !important;
            line-height: 1.5 !important;
        }

        #header-carousel-mobile .carousel-caption .btn:hover,
        #header-carousel-mobile .carousel-caption .btn:focus,
        #header-carousel-mobile .carousel-caption .btn:active {
            background-color: rgba(255,255,255,0.9) !important;
            color: #333 !important;
            border-color: rgba(255,255,255,1) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3) !important;
        }

        #header-carousel-mobile .carousel-caption .btn,
        #header-carousel-mobile .carousel-caption a {
            pointer-events: auto !important;
            touch-action: manipulation !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        .mobile-header-contacts {
            position: relative;
            z-index: 1000;
            padding: 1rem 1.5rem;
        }

        .mobile-contacts {
            line-height: 1.2;
        }

        .contact-line {
            margin-bottom: 0.1rem;
            font-size: 0.85rem;
            line-height: 1.1;
        }

        .contact-line:last-child {
            margin-bottom: 0;
        }

        .contact-line strong {
            color: #fff;
        }

        .mobile-navbar-wrapper {
            position: relative;
            left: 0;
            right: 0;
            margin-top: 1rem;
            padding: 0;
            z-index: 5;
            width: 100%;
            pointer-events: none;
        }

        .mobile-navbar-wrapper .navbar-brand,
        .mobile-navbar-wrapper .navbar-toggler {
            pointer-events: auto;
        }

        .mobile-navbar-wrapper .navbar {
            width: 100%;
            flex-direction: column !important;
            align-items: center !important;
            overflow: visible !important;
            padding: 0 !important;
        }
        .mobile-navbar-wrapper .navbar-brand {
            width: 100%;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center;
            margin: 0 auto 1rem !important;
            padding: 0 !important;
            position: relative;
            z-index: 1010;
            line-height: 0;
            min-height: 100px;
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

        @media (max-width: 991.98px) {
            #header-carousel-mobile .carousel-item { min-height: 110vh; }
            #header-carousel-mobile .carousel-caption { bottom: 310px; padding: 0 1.5rem; z-index: 1040; }
            #header-carousel-mobile .carousel-caption .btn { position: relative; z-index: 1050; }
            .mobile-navbar-wrapper .navbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                padding: 1.5rem 1rem 1rem !important;
                background: transparent !important;
            }
            .mobile-navbar-wrapper .navbar-brand {
                margin: 0 auto 1rem !important;
                order: 1;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                min-height: 100px;
            }
            .mobile-navbar-wrapper .navbar-brand .oagb-logo {
                width: 220px !important;
                max-width: 90% !important;
                height: auto !important;
                padding: 0 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            .mobile-navbar-wrapper .navbar-toggler {
                order: 2;
                margin: 3rem auto 2rem !important;
                position: relative !important;
                right: auto !important;
                top: auto !important;
                transform: none !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.5rem !important;
                color: white !important;
                border: 2px solid #B1A276 !important;
                background: transparent !important;
                padding: 0.5rem 1.2rem !important;
                border-radius: 6px !important;
                transition: background-color 0.3s ease-out, border-color 0.3s ease-out, box-shadow 0.3s ease-out;
            }
            .mobile-navbar-wrapper .navbar-toggler i { color: white !important; font-size: 18px !important; }
            .mobile-navbar-wrapper .navbar-toggler::after {
                content: ' MENU';
                margin-left: 0;
                font-family: 'Open Sans', sans-serif;
                font-weight: 600;
                font-size: 14px;
                color: white !important;
            }
            .mobile-navbar-wrapper .navbar-collapse {
                order: 3;
                position: static !important;
                width: 100% !important;
                margin-top: 1.5rem;
                padding: 1.25rem 1.5rem;
                border-top: 1px solid rgba(255,255,255,0.2);
                background: rgba(0, 0, 0, 0.9) !important;
                border-radius: 14px !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 16px 35px rgba(0,0,0,0.4);
                transition: opacity 0.3s ease-out, visibility 0.3s ease-out, max-height 0.3s ease-out;
                pointer-events: auto;
            }
            .mobile-navbar-wrapper .navbar-nav {
                margin-bottom: 2rem;
                text-align: center;
                pointer-events: auto;
            }
            .mobile-navbar-wrapper .navbar-nav .nav-link {
                color: white !important;
                font-size: 1.1rem;
                font-weight: 600;
                padding: 1rem !important;
                margin: 0.5rem 0;
                text-align: center;
                transition: color 0.3s ease-out, opacity 0.3s ease-out;
                pointer-events: auto;
            }
            .mobile-navbar-wrapper .navbar-nav .dropdown-menu {
                background: rgba(255, 255, 255, 0.95);
                border: none;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                text-align: center;
                margin: 0 auto !important;
                position: relative !important;
                left: 0 !important;
                right: 0 !important;
                transition: opacity 0.3s ease-out, visibility 0.3s ease-out, transform 0.3s ease-out;
                pointer-events: auto;
            }
            .mobile-navbar-wrapper .navbar-nav .dropdown-menu .dropdown-item {
                color: #091E3E;
                padding: 0.8rem 1.5rem;
                font-weight: 500;
                transition: background-color 0.3s ease-out, color 0.3s ease-out;
                pointer-events: auto;
            }
            .mobile-navbar-wrapper .navbar-nav .dropdown-menu .dropdown-item:hover {
                background-color: var(--primary);
                color: white;
                transition: background-color 0.3s ease-out, color 0.3s ease-out;
            }
            .mobile-navbar-wrapper .navbar .btn { display: none !important; }
        }

        /* Menu desktop com fundo branco no scroll - barra de endere�os sempre vis�vel */
        @media (min-width: 992px) {
            /* Fazer o navbar fixo no topo com posicionamento consistente */
            .navbar-dark {
                position: fixed !important;
                top: 45px !important; /* Sempre abaixo da barra de endere�os */
                left: 0 !important;
                right: 0 !important;
                z-index: 1030 !important;
                width: 100% !important;
                transition: all 0.3s ease !important;
                background: transparent !important;
                padding: 15px 0 !important; /* Padding padr�o */
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

            /* Logo padr�o - posicionamento consistente */
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
                /* Manter sempre na mesma posi��o mas reduzir altura */
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

            /* Remover o espa�amento do container do navbar */
            .container-fluid.position-relative.p-0.d-none.d-lg-block {
                margin-bottom: 0 !important;
            }

            /* Ajustar o conte�do principal para come�ar logo ap�s o navbar fixo */
            .bg-header {
                margin-top: 0 !important; /* Remover margin para eliminar espa�o branco */
                position: relative;
                top: 0 !important;
            }

            /* Manter a barra de endere�os sempre vis�vel */
            .bg-dark {
                position: fixed !important;
                top: 0 !important;
                width: 100% !important;
                z-index: 1040 !important;
                transition: all 0.3s ease !important;
            }

            /* Mudan�a da barra de endere�os durante scroll - fundo branco */
            .topbar-scrolled {
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            }

            /* Cores douradas #B1A276 para textos da barra de endere�os quando scrolled */
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

            /* For�ar mudan�a de cor em todos os elementos da topbar */
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

            /* Bot�o de pesquisa tamb�m dourado #B1A276 quando scrolled */
            .navbar-scrolled .btn {
                color: #B1A276 !important;
            }

            .navbar-scrolled .btn:hover {
                color: #9d8f64 !important;
            }

            .navbar-scrolled .btn.text-primary {
                color: #B1A276 !important;
            }

            .navbar-scrolled .btn-outline-light {
                border-color: #B1A276 !important;
                color: #B1A276 !important;
                background-color: transparent !important;
            }

            .navbar-scrolled .btn-outline-light:hover {
                background-color: #B1A276 !important;
                border-color: #B1A276 !important;
                color: white !important;
            }

            .navbar-scrolled .btn-outline-light i {
                color: #B1A276 !important;
            }

            .navbar-scrolled .btn.text-primary i {
                color: #B1A276 !important;
            }

            /* Ajustar padding do conte�do para n�o ficar sobreposto pelo navbar fixo */
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

        <?php include 'includes/topbar.php'; ?>

    <!-- Navbar & Header Start -->
    <!-- Desktop Navbar -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary py-5 bg-header" style="<?php echo htmlspecialchars($apresentacao_header_background_style, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">In�cio</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="apresentacao-historia.php" class="h5 text-white"><?php echo $page_title; ?></a>

                    <!-- Quick Action Links -->
                    <div class="quick-actions mt-3">
                        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2" title="Voltar atr�s">
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

                                        <strong class="text-white">Rua 15, Bissau, Guin�-Bissau</strong>

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



                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">

                        <?php include 'includes/navbar.php'; ?>

                    </div>



                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-end" style="padding: 1rem 1.5rem;">

                        <div class="p-3" style="max-width: 900px;">

                            <h1 class="display-5 text-white mb-3 animated zoomIn"><?php echo $page_title; ?></h1>

                            <div class="mt-2">

                                <a href="index.php" class="h6 text-white">In�cio</a>

                                <i class="far fa-circle text-white px-2"></i>

                                <a href="apresentacao-historia.php" class="h6 text-white"><?php echo $page_title; ?></a>

                            </div>

                            <div class="quick-actions mt-3">
                                <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2" title="Voltar atr�s">
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
            <!-- Introdu��o -->
            <div class="row g-5 mb-5">
                <div class="col-lg-7">
                    <div class="content-section">
                        <?php echo $pagina->conteudo; ?>
                    </div>
                </div>
                <div class="col-lg-5">
                    <!-- Estat�sticas -->
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
                                <div class="stats-label">Estagi�rios</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Imagem -->
                    <?php if (!empty($pagina->imagem)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($pagina->imagem); ?>" 
                         alt="OAGB" class="img-fluid rounded">
                    <?php else: ?>
                    <img src="img/close-up-scales-justice.jpg" alt="OAGB" class="img-fluid rounded">
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-divider"></div>

            <!-- Timeline Hist�rico -->
            <div class="content-section">
                <h2 class="text-center mb-5" style="font-family: 'Libre Baskerville', serif; color: #4D1C21;">
                    Marcos Hist�ricos
                </h2>
                <div class="timeline">
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.1s">
                        <div class="timeline-content">
                            <div class="timeline-year">1974</div>
                            <h5>Independ�ncia Nacional</h5>
                            <p>Com a independ�ncia da Guin�-Bissau, surge a necessidade de organizar a classe dos advogados.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="timeline-content">
                            <div class="timeline-year">1991</div>
                            <h5>Cria��o da OAGB</h5>
                            <p>Funda��o oficial da Ordem dos Advogados da Guin�-Bissau como institui��o aut�noma.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.3s">
                        <div class="timeline-content">
                            <div class="timeline-year">2005</div>
                            <h5>Novo Estatuto</h5>
                            <p>Aprova��o do novo estatuto que moderniza a estrutura e funcionamento da Ordem.</p>
                        </div>
                    </div>
                    <div class="timeline-item wow fadeInUp" data-wow-delay="0.4s">
                        <div class="timeline-content">
                            <div class="timeline-year">2020</div>
                            <h5>Digitaliza��o</h5>
                            <p>In�cio do processo de digitaliza��o e moderniza��o dos servi�os da OAGB.</p>
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
                            <h4><i class="fa fa-balance-scale me-2"></i>Justi�a</h4>
                            <p>Defendemos o acesso universal � justi�a e trabalhamos para garantir que todos os cidad�os tenham representa��o legal adequada.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInRight" data-wow-delay="0.2s">
                            <h4><i class="fa fa-shield-alt me-2"></i>Integridade</h4>
                            <p>Mantemos os mais altos padr�es �ticos e profissionais, garantindo a confian�a p�blica na advocacia.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInLeft" data-wow-delay="0.3s">
                            <h4><i class="fa fa-graduation-cap me-2"></i>Excel�ncia</h4>
                            <p>Promovemos a forma��o cont�nua e o desenvolvimento profissional dos nossos membros.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="values-card wow fadeInRight" data-wow-delay="0.4s">
                            <h4><i class="fa fa-handshake me-2"></i>Coopera��o</h4>
                            <p>Fomentamos a colabora��o entre advogados e com outras institui��es jur�dicas nacionais e internacionais.</p>
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
        // Variable to track if speaking
        let isSpeaking = false;
        let utterance = null;

        // Function to share page
        function sharePage() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Confira esta p�gina da OAGB',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copiado para a �rea de transfer�ncia!');
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
                script.onerror = function() {
                    console.error('Erro ao carregar Google Translate');
                    alert('Desculpe, n�o conseguimos carregar a ferramenta de tradu��o. Por favor, tente novamente.');
                };
                document.head.appendChild(script);

                // Initialize Google Translate
                window.googleTranslateElementInit = function() {
                    try {
                        if (window.google && window.google.translate) {
                            const translateElement = document.getElementById('google_translate_element');
                            if (!translateElement) {
                                console.error('Elemento de tradu��o n�o encontrado');
                                return;
                            }
                            
                            new google.translate.TranslateElement({
                                pageLanguage: 'pt',
                                includedLanguages: 'en,fr,es,pt,es',
                                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                            }, 'google_translate_element');
                        }
                    } catch (e) {
                        console.error('Erro ao inicializar Google Translate:', e);
                    }
                };

                // Create translate element container if it doesn't exist
                if (!document.getElementById('google_translate_element')) {
                    const translateDiv = document.createElement('div');
                    translateDiv.id = 'google_translate_element';
                    translateDiv.style.position = 'fixed';
                    translateDiv.style.top = '60px';
                    translateDiv.style.right = '20px';
                    translateDiv.style.zIndex = '9999';
                    translateDiv.style.backgroundColor = 'white';
                    translateDiv.style.padding = '15px';
                    translateDiv.style.borderRadius = '8px';
                    translateDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                    translateDiv.style.border = '1px solid #ddd';
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

        // Function to read aloud the content from container div
        function readAloud() {
            // Stop if already speaking
            if (isSpeaking) {
                speechSynthesis.cancel();
                isSpeaking = false;
                // Update button text back
                const readBtn = document.querySelector('[data-action="read-aloud"]');
                if (readBtn) {
                    readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    readBtn.title = 'Ler em voz alta';
                }
                return;
            }

            // Get container with content
            const container = document.querySelector('.container');
            if (!container) {
                alert('Conte�do n�o encontrado para leitura em voz alta.');
                return;
            }

            // Extract text from container
            let text = '';
            const paragraphs = container.querySelectorAll('p, h3, h4, h5, li');
            paragraphs.forEach(element => {
                if (element.offsetParent !== null) { // Check if element is visible
                    text += element.innerText + ' ';
                }
            });

            if (!text.trim()) {
                alert('Nenhum conte�do dispon�vel para leitura em voz alta.');
                return;
            }

            // Check browser support
            if (!('speechSynthesis' in window)) {
                alert('O seu navegador n�o suporta leitura em voz alta.');
                return;
            }

            // Create utterance
            utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'pt-PT'; // Portuguese (Portugal)
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

            // Update button when speaking
            utterance.onstart = function() {
                isSpeaking = true;
                const readBtn = document.querySelector('[data-action="read-aloud"]');
                if (readBtn) {
                    readBtn.innerHTML = '<i class="fas fa-stop"></i>';
                    readBtn.title = 'Parar leitura';
                }
            };

            // Update button when finished
            utterance.onend = function() {
                isSpeaking = false;
                const readBtn = document.querySelector('[data-action="read-aloud"]');
                if (readBtn) {
                    readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    readBtn.title = 'Ler em voz alta';
                }
            };

            // Handle errors
            utterance.onerror = function(event) {
                console.error('Erro na leitura em voz alta:', event.error);
                isSpeaking = false;
                const readBtn = document.querySelector('[data-action="read-aloud"]');
                if (readBtn) {
                    readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    readBtn.title = 'Ler em voz alta';
                }
            };

            // Start speaking
            speechSynthesis.speak(utterance);
        }
    </script>

    <!-- Desktop Navbar Scroll Effect - manter barra de endere�os vis�vel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar-dark');
            const topbar = document.querySelector('.bg-dark'); // Desktop topbar

            if (navbar && window.innerWidth >= 992) { // S� aplicar em desktop
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

