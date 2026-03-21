<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir funções auxiliares e conexão
require_once 'includes/functions.php';
require_once 'connect.php';

if (!function_exists('oagb_resolve_media_path')) {
    /**
     * Normaliza caminhos de imagens vindos da base de dados.
     *
     * Aceita URLs completas, caminhos relativos ou apenas o nome do ficheiro
     * e devolve uma rota utilizável no frontend.
     */
    function oagb_resolve_media_path($rawPath, $defaultPath)
    {
        if (empty($rawPath)) {
            return $defaultPath;
        }

        $normalized = str_replace('\\', '/', trim((string) $rawPath));
        $normalized = preg_replace('#\.\.+#', '', $normalized);

        if ($normalized === '') {
            return $defaultPath;
        }

        if (preg_match('#^https?://#i', $normalized)) {
            return $normalized;
        }

        if ($normalized[0] === '/') {
            $normalized = ltrim($normalized, '/');
        }

        if (strpos($normalized, 'gestao/assets/uploads/files/') === 0 || strpos($normalized, 'img/') === 0) {
            return $normalized;
        }

        return 'gestao/assets/uploads/files/' . $normalized;
    }
}

// Inicializar variáveis com valores padrão
$carousel_slides = [];
$noticias_destaque = [];
$proximos_eventos = [];
$ultimo_parecer = null;
$ultimo_comunicado = null;

// Verificar se a conexão da base de dados está disponível
if (isset($pdo)) {
    try {
        // Buscar slides do carousel da base de dados (com fallback)
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM carousel_slides 
                WHERE ativo = 1 
                ORDER BY ordem_exibicao ASC, id DESC 
                LIMIT 5
            ");
            $stmt->execute();
            $carousel_slides = $stmt->fetchAll();
        } catch (Exception $e) {
            // Tabela carousel_slides não existe - usar padrão
            error_log("Tabela carousel_slides não encontrada: " . $e->getMessage());
        }
        
        // Se não houver slides na BD, usar padrão
        if (empty($carousel_slides)) {
            $carousel_slides = [
                (object)[
                    'titulo' => 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau',
                    'subtitulo' => 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito.',
                    'imagem' => 'gestao/assets/uploads/files/brass-scales-justice-close-up-view.jpg',
                    'link_texto' => 'Saiba mais',
                    'link_url' => 'apresentacao-historia.php'
                ],
                (object)[
                    'titulo' => 'Cadastro Nacional de Advogados',
                    'subtitulo' => 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB.',
                    'imagem' => 'gestao/assets/uploads/files/close-up-scales-justice-original-azul.jpg',
                    'link_texto' => 'Pesquisar Advogados',
                    'link_url' => 'pesquisa-advogados.php'
                ],
                (object)[
                    'titulo' => 'Justiça e Transparência',
                    'subtitulo' => 'Garantindo a excelência jurídica e a defesa dos direitos dos cidadãos da Guiné-Bissau.',
                    'imagem' => 'gestao/assets/uploads/files/close-up-detail-scales-justice.jpg',
                    'link_texto' => 'Nossos Serviços',
                    'link_url' => 'publicacoes.php'
                ]
            ];
        }

        // Buscar notícias em destaque (com fallback)
        try {
            // Primeiro tentar com campo destaque
            $stmt = $pdo->prepare("SELECT * FROM noticias WHERE destaque = 1 AND ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
            $stmt->execute();
            $noticias_destaque = $stmt->fetchAll();
            
            // Se não encontrar com destaque, buscar as 3 mais recentes
            if (empty($noticias_destaque)) {
                $stmt = $pdo->prepare("SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 3");
                $stmt->execute();
                $noticias_destaque = $stmt->fetchAll();
            }
        } catch (Exception $e) {
            // Campos destaque/ativo podem não existir
            try {
                $stmt = $pdo->prepare("SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 3");
                $stmt->execute();
                $noticias_destaque = $stmt->fetchAll();
            } catch (Exception $e2) {
                error_log("Erro ao buscar notícias: " . $e2->getMessage());
            }
        }

        // Buscar próximos eventos (com fallback)
        try {
            $stmt = $pdo->prepare("SELECT * FROM agenda WHERE DATE(data_evento) >= CURDATE() AND ativo = 1 ORDER BY data_evento ASC LIMIT 2");
            $stmt->execute();
            $proximos_eventos = $stmt->fetchAll();
            
            if (empty($proximos_eventos)) {
                $stmt = $pdo->prepare("SELECT * FROM agenda ORDER BY data_evento DESC LIMIT 2");
                $stmt->execute();
                $proximos_eventos = $stmt->fetchAll();
            }
        } catch (Exception $e) {
            // Campo ativo pode não existir
            try {
                $stmt = $pdo->prepare("SELECT * FROM agenda WHERE DATE(data_evento) >= CURDATE() ORDER BY data_evento ASC LIMIT 2");
                $stmt->execute();
                $proximos_eventos = $stmt->fetchAll();
                
                if (empty($proximos_eventos)) {
                    $stmt = $pdo->prepare("SELECT * FROM agenda ORDER BY data_evento DESC LIMIT 2");
                    $stmt->execute();
                    $proximos_eventos = $stmt->fetchAll();
                }
            } catch (Exception $e2) {
                error_log("Erro ao buscar agenda: " . $e2->getMessage());
            }
        }
        
        // Buscar último parecer/deliberação (com fallback)
        try {
            $stmt = $pdo->prepare("
                SELECT titulo, tipo, numero_documento, link_url, data_documento 
                FROM pareceres_deliberacoes 
                WHERE ativo = 1 
                ORDER BY data_documento DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $ultimo_parecer = $stmt->fetch();
        } catch (Exception $e) {
            error_log("Tabela pareceres_deliberacoes não encontrada: " . $e->getMessage());
        }
        
        // Buscar último comunicado (com fallback)
        try {
            $stmt = $pdo->prepare("
                SELECT titulo, descricao, link_url, data_publicacao 
                FROM comunicados 
                WHERE ativo = 1 
                ORDER BY data_publicacao DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $ultimo_comunicado = $stmt->fetch();
        } catch (Exception $e) {
            error_log("Tabela comunicados não encontrada: " . $e->getMessage());
        }

    } catch (Exception $e) {
        error_log("Erro geral na página inicial: " . $e->getMessage());
    }
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
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- Header Styles (Componente reutilizável) -->
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <!-- Index Specific Styles -->
    <link href="css/index-styles.css" rel="stylesheet">
    
    <!-- Banner Inscrição na Ordem -->
    <link href="css/banner-inscricao.css" rel="stylesheet">
    
    <!-- Footer Styles -->
    <link href="css/footer-styles.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

    <!-- Desktop Header (hidden on mobile) -->
    <div class="d-none d-lg-block">
        <?php include 'includes/topbar.php'; ?>
        <?php include 'includes/navbar.php'; ?>
    </div>

    <!-- Desktop Carousel (only on lg+) -->
    <div class="container-fluid p-0 d-none d-lg-block">
        <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $first = true;
                foreach ($carousel_slides as $slide):
                    $defaultCarouselImage = 'img/close-up-scales-justice.jpg';
                    $img_path = oagb_resolve_media_path($slide->imagem ?? '', $defaultCarouselImage);
                ?>
                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <img class="w-100" src="<?php echo htmlspecialchars($img_path); ?>" alt="Slide">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="padding-top: 5rem;">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="display-1 text-white mb-md-4 animated zoomIn fonText" style="text-decoration:underline;">
                                <?php echo htmlspecialchars($slide->titulo); ?>
                            </h1>
                            <h5 class="text-white mb-3 animated slideInDown fonText2">
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
    <!-- Desktop Carousel End -->

    <!-- Mobile Header Start -->
    <div class="d-block d-lg-none">
        <!-- Mobile Carousel with overlay content -->
        <div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="carousel" style="position: relative;">
            <div class="carousel-inner">
                <?php
                $first = true;
                foreach ($carousel_slides as $slide):
                    $defaultCarouselImage = 'img/close-up-scales-justice.jpg';
                    $img_path = oagb_resolve_media_path($slide->imagem ?? '', $defaultCarouselImage);
                ?>
                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                    <img class="w-100" src="<?php echo htmlspecialchars($img_path); ?>" alt="Slide">

                    <!-- Mobile Contact Info & Buttons -->
                    <div class="mobile-header-contacts container-fluid px-1 pt-0 pb-1">
                        <!-- Primeira Linha: Contactos -->
                        <div class="row mb-2">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-map-marker-alt text-white-50 me-1"></i>Bissau, Guiné-Bissau</small>
                                <div style="width: 1px; height: 10px; background: rgba(255,255,255,0.4);"></div>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-phone-alt text-white-50 me-1"></i>+245 955 475 889</small>
                                <div style="width: 1px; height: 10px; background: rgba(255,255,255,0.4);"></div>
                                <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-envelope-open text-white-50 me-1"></i>info@oagb.gw</small>
                            </div>
                        </div>
                        
                        <!-- Segunda Linha: 3 Botões Principais -->
                        <div class="row mb-1">
                            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; overflow-x: auto; width: 100%;">
                                <!-- Botão Pesquisa -->
                                <button type="button" class="btn btn-link text-white p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#searchModal" onclick="event.stopPropagation();">
                                     <i class="fa fa-search" style="font-size: 1.1rem;"></i>
                                </button>
                                
                                <div style="width: 1px; height: 18px; background: rgba(255,255,255,0.4);"></div>
                                
                                <!-- Botão Tradução -->
                                <div class="dropdown">
                                    <a href="#" class="text-white p-0 text-decoration-none" data-bs-toggle="dropdown" title="Mudar Idioma" onclick="event.stopPropagation();">
                                        <i class="fa fa-globe" style="font-size: 1.2rem;"></i>
                                    </a>
                                    <div class="dropdown-menu border-0 rounded-3 shadow-lg p-2 dropdown-menu-center" style="min-width: 150px; z-index: 2000; background: rgba(255, 255, 255, 0.98); position: absolute; left: 50%; transform: translateX(-50%); margin-top: 10px;">
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                        <a href="#" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="font-size: 0.80rem;"><span class="me-2" style="font-size: 1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                                    </div>
                                </div>
                                
                                <div style="width: 1px; height: 18px; background: rgba(255,255,255,0.4);"></div>
                                
                                <!-- Botão Área Reservada -->
                                <a href="portal/login.php" class="btn btn-sm btn-outline-light px-3 fw-bold text-uppercase d-flex align-items-center" style="border-radius: 20px; border-color: rgba(255,255,255,0.8); font-size: 0.70rem; letter-spacing: 0.5px; background: rgba(255,255,255,0.15);" onclick="event.stopPropagation();">
                                    <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navbar -->
                    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">
                        <?php include 'includes/navbar.php'; ?>
                    </div>

                    <!-- Mobile Slide Content -->
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-end" style="padding: 1rem 1.5rem;">
                        <div class="p-3" style="max-width: 900px;">
                            <h1 class="display-4 text-white mb-3 animated zoomIn fonText" style="text-decoration:underline; font-size: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.9);">
                                <?php echo htmlspecialchars($slide->titulo); ?>
                            </h1>
                            <p class="text-white mb-3 animated slideInDown fonText2" style="font-size: 0.95rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.9);">
                                <?php echo htmlspecialchars($slide->subtitulo); ?>
                            </p>
                            <?php if (!empty($slide->link_url)): ?>
                            <a href="<?php echo htmlspecialchars($slide->link_url); ?>" class="btn btn-outline-light py-2 px-4 animated slideInRight">
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
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel-mobile" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel-mobile" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Mobile Header End -->




    <!-- Facts Start -->
    <div class="container-fluid facts py-5 pt-lg-0">
        <div class="container py-5 pt-lg-0">
            <div class="row gx-0">
                <div class="col-lg-4">
                    <div class="bg-color-1 shadow facts-card">
                        <div class="facts-title">
                            <i class="fas fa-gavel fa-2x me-3" style="color: white;"></i>
                            <h5 style="color: white; font-family: 'Libre Baskerville', serif; font-weight: 600; margin: 0;">
                                Pareceres e Deliberações
                            </h5>
                        </div>
                        <div class="facts-content">
                            <?php if($ultimo_parecer): ?>
                            <small style="color:#fff; font-family: 'Open Sans', sans-serif; opacity: 0.8;">
                                <?php echo !empty($ultimo_parecer->data_documento) ? format_date_pt($ultimo_parecer->data_documento) : '15 de dezembro de 2023'; ?>
                            </small>
                            <a href="<?php echo htmlspecialchars($ultimo_parecer->link_url ?? 'pareceres-deliberacoes.php'); ?>" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">
                                <?php echo htmlspecialchars($ultimo_parecer->numero_documento ?? ''); ?> - <?php echo htmlspecialchars(truncate_text($ultimo_parecer->titulo, 40)); ?>
                            </a>
                            <?php else: ?>
                            <small style="color:#fff; font-family: 'Open Sans', sans-serif; opacity: 0.8;">
                                15 de dezembro de 2023
                            </small>
                            <a href="pareceres-deliberacoes.php" class="linkSublinhado" style="color:#fff; font-family: 'Libre Baskerville', serif;">
                                CNEF - Deliberação n.º 8/2023
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-color-3 shadow facts-card">
                        <div class="facts-title">
                            <i class="fas fa-search fa-2x me-3" style="color: white;"></i>
                            <h5 style="color: white; font-family: 'Libre Baskerville', serif; font-weight: 600; margin: 0;">
                                Pesquisa de Advogados
                            </h5>
                        </div>
                        <div class="facts-content">
                            <a href="advogados-inscritos.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Advogados Inscritos</a>
                            <a href="pesquisa-advogados.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Pesquisa de Advogados</a>
                            <a href="estagiarios-inscritos.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">Estagiários</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-color-4 shadow facts-card">
                        <div class="facts-title">
                            <i class="fas fa-bullhorn fa-2x me-3" style="color: white;"></i>
                            <h5 style="color: white; font-family: 'Libre Baskerville', serif; font-weight: 600; margin: 0;">
                                Comunicados
                            </h5>
                        </div>
                        <div class="facts-content">
                            <?php if($ultimo_comunicado): ?>
                            <?php if(!empty($ultimo_comunicado->data_publicacao)): ?>
                            <small style="color:#fff; font-family: 'Open Sans', sans-serif; opacity: 0.8;">
                                <?php echo format_date_pt($ultimo_comunicado->data_publicacao); ?>
                            </small>
                            <?php endif; ?>
                            <a href="<?php echo htmlspecialchars($ultimo_comunicado->link_url ?? 'comunicados.php'); ?>" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">
                                <?php echo htmlspecialchars(truncate_text($ultimo_comunicado->titulo, 50)); ?>
                            </a>
                            <?php else: ?>
                            <small style="color:#fff; font-family: 'Open Sans', sans-serif; opacity: 0.8;">
                                20 de novembro de 2024
                            </small>
                            <a href="comunicados.php" class="linkSublinhado" style="color:#fff; font-family: 'Open Sans', sans-serif;">
                                Comunicado - Assembleia Geral 2024
                            </a>
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
    <div class="container-fluid section-noticias">
        <div class="container py-4">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="text-primary text-uppercase" style="font-family: 'Open Sans', sans-serif; font-weight: 400;">Artigos recentes</h5>
                <h1 class="mb-0" style="color:#5B463F; font-family: 'Libre Baskerville', serif; font-weight: 400; font-size:280%;">Últimas notícias</h1>
            </div>
            <div class="row g-5">
                <?php foreach ($noticias_destaque as $noticia): ?>
                <div class="col-lg-4">
                    <div class="blog-item bg-light rounded overflow-hidden">
                        <div class="blog-img position-relative overflow-hidden">
                            <?php 
                            $raw_noticia_imagem = $noticia->imagem_destaque ?? '';
                            if (empty($raw_noticia_imagem) && !empty($noticia->imagem)) {
                                $raw_noticia_imagem = $noticia->imagem;
                            }
                            if (empty($raw_noticia_imagem) && !empty($noticia->foto)) {
                                $raw_noticia_imagem = $noticia->foto;
                            }
                            $img_noticia = oagb_resolve_media_path($raw_noticia_imagem, 'img/Asset 7-100.jpg');
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
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Blog End -->

    <!-- Agenda Start -->
    <div class="container-fluid py-5" style="background: white; position: relative; overflow: hidden;">
        <!-- Ícone de agenda no fundo -->
        <div class="agenda-background-icon">
            <i class="far fa-calendar-alt"></i>
        </div>
        
        <div class="container py-4" style="position: relative; z-index: 2;">
            <div class="section-title text-center position-relative pb-3 mb-4 mx-auto" style="max-width: 600px;">
                <h5 class="text-primary text-uppercase" style="font-family: 'Open Sans', sans-serif; font-weight: 400;">Próximos Eventos</h5>
                <h1 class="mb-0" style="color:#5B463F; font-family: 'Libre Baskerville', serif; font-weight: 400; font-size:280%;">Agenda</h1>
            </div>
            
            <?php if (!empty($proximos_eventos)): ?>
            <div class="row g-4">
                <?php foreach ($proximos_eventos as $evento): 
                    // Extrair componentes da data
                    $data_evento = new DateTime($evento->data_evento);
                    $dia = $data_evento->format('d');
                    $mes = $data_evento->format('M');
                    $ano = $data_evento->format('Y');
                    
                    // Traduzir mês para português
                    $meses_pt = [
                        'Jan' => 'JAN', 'Feb' => 'FEV', 'Mar' => 'MAR',
                        'Apr' => 'ABR', 'May' => 'MAI', 'Jun' => 'JUN',
                        'Jul' => 'JUL', 'Aug' => 'AGO', 'Sep' => 'SET',
                        'Oct' => 'OUT', 'Nov' => 'NOV', 'Dec' => 'DEZ'
                    ];
                    $mes_pt = $meses_pt[$mes] ?? $mes;
                ?>
                <div class="col-12 mb-4">
                    <div class="agenda-evento-novo row align-items-center" style="padding: 2rem; min-height: 180px;">
                        <!-- Data à esquerda -->
                        <div class="col-lg-3 col-md-4 text-center agenda-data-container">
                            <div class="agenda-data-display">
                                <div class="agenda-dia" style="font-size: 4rem; font-weight: 700; color: #B1A276; line-height: 1; font-family: 'Libre Baskerville', serif;">
                                    <?php echo $dia; ?>
                                </div>
                                <div class="agenda-mes" style="font-size: 1.5rem; font-weight: 600; color: #5B463F; margin-top: -10px; font-family: 'Open Sans', sans-serif;">
                                    <?php echo $mes_pt; ?>
                                </div>
                                <div class="agenda-ano" style="font-size: 1.2rem; font-weight: 400; color: #888; font-family: 'Open Sans', sans-serif;">
                                    <?php echo $ano; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conteúdo à direita -->
                        <div class="col-lg-9 col-md-8 agenda-conteudo-container">
                            <h4 class="mb-3" style="color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 1.8rem; font-weight: 600;">
                                <a href="evento.php?id=<?php echo $evento->id; ?>" class="linkSublinhado text-decoration-none" style="color: #4D1C21;">
                                    <?php echo htmlspecialchars($evento->titulo); ?>
                                </a>
                            </h4>
                            
                            <?php if (!empty($evento->local_evento)): ?>
                            <p class="mb-2" style="color: #B1A276; font-family: 'Open Sans', sans-serif; font-size: 1rem; font-weight: 500;">
                                <i class="fa fa-map-marker-alt me-2" style="color: #B1A276;"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($evento->descricao)): ?>
                            <p class="texto-conteudo mb-3" style="line-height: 1.6;">
                                <?php echo htmlspecialchars(truncate_text($evento->descricao, 200)); ?>
                            </p>
                            <?php endif; ?>
                            
                            <a href="evento.php?id=<?php echo $evento->id; ?>" class="d-block" style="margin-top: 1rem;">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php else: ?>
            <div class="text-center">
                <div class="bg-white rounded shadow-sm p-5" style="border: 1px solid #f0f0f0;">
                    <i class="far fa-calendar fa-4x text-muted mb-3" style="color: #B1A276 !important;"></i>
                    <h5 class="text-muted mb-3" style="font-family: 'Libre Baskerville', serif; color: #5B463F !important;">Nenhum evento agendado</h5>
                    <p class="text-muted" style="font-family: 'Open Sans', sans-serif;">Consulte novamente em breve para ver os próximos eventos da OAGB.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Agenda End -->

    <!-- Inscrição Banner -->
    <?php include 'includes/banner-inscricao.php'; ?>

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg bg-color-1 text-white btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js?v=<?php echo time(); ?>"></script>

    <!-- Header Functions (Componente reutilizável) -->
    <script src="js/header-functions.js?v=<?php echo time(); ?>"></script>

    </body>
</html>
