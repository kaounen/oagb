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
                SELECT titulo, tipo, numero_documento, link_url 
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
                SELECT titulo, descricao, link_url 
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
                SELECT titulo, tipo, numero_documento, link_url 
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
                SELECT titulo, descricao, link_url 
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
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

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

        /* Classes de texto do slider (do index2.html) */
        .fonText {
            font-family: 'Open Sans', sans-serif;
            font-weight: bold;
            font-style: normal;
        }

        .fonText2 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .fonText3 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-style: normal;
        }

        .fonText4 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 300;
            font-style: normal;
            font-size: 90%;
        }

        /* Paleta de cores */
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        .bg-color-5 { background-color: #5a443d; }

        /* Botões arrow */
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
        a.linkSublinhado {
            font-weight: 500 !important;
        }

        a.linkSublinhado:hover {
            text-decoration: underline !important;
        }

        /* Paddings reduzidos */
        .facts {
            padding-bottom: 2rem !important;
            margin-top: -105px !important; /* Metade dos cards sobre o slider */
            position: relative;
            z-index: 10;
        }

        .section-noticias {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
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

        /* Garantir que o botão seja clicável em todas as situações */
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

        .mobile-navbar-wrapper .navbar-toggler {
            align-self: center;
        }

        /* News article images - same height */
        .blog-item .blog-img {
            height: 200px;
            overflow: hidden;
        }

        .blog-item .blog-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Agenda redesenhada */
        .agenda-background-icon {
            position: absolute;
            top: 20px;
            right: 50px;
            z-index: 1;
            opacity: 0.05;
            font-size: 15rem;
            color: #B1A276;
            pointer-events: none;
        }

        .agenda-evento-novo {
            /* Sem efeitos card */
        }

        .agenda-data-container {
            border-right: 2px solid #f0f0f0;
            padding-right: 2rem;
        }

        .agenda-conteudo-container {
            padding-left: 2rem;
        }

        /* Responsivo para agenda */
        @media (max-width: 991.98px) {
            .agenda-data-container {
                border-right: none;
                border-bottom: 2px solid #f0f0f0;
                padding-right: 1rem;
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .agenda-conteudo-container {
                padding-left: 1rem;
            }

            .agenda-background-icon {
                font-size: 8rem;
                top: 10px;
                right: 20px;
            }
        }

        /* Facts cards */
        .facts-card {
            display: flex;
            flex-direction: column;
            height: 210px;
            padding: 2rem;
            padding-top: 30px;
        }

        .facts-title {
            display: flex;
            align-items: center;
            height: 50px;
            margin-bottom: 20px;
        }

        .facts-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* Mobile contact info */
        .mobile-contacts {
            line-height: 1.2;
        }

        .contact-line {
            margin-bottom: 0.1rem;
            font-size: 0.75rem;
            line-height: 1.1;
        }

        .contact-line:last-child {
            margin-bottom: 0;
        }

        /* Mobile navbar - corrigir layout do botão menu */
        @media (max-width: 991.98px) {
            #header-carousel-mobile .carousel-item {
                min-height: 110vh;
            }

            #header-carousel-mobile .carousel-caption {
                bottom: 310px;
                padding: 0 1.5rem;
            }

            /* Corrigir z-index do botão para aparecer acima do conteúdo e fact-cards */
            #header-carousel-mobile .carousel-caption .btn {
                position: relative;
                z-index: 1050;
            }

            /* Garantir que o carousel-caption tenha prioridade sobre fact-cards */
            #header-carousel-mobile .carousel-caption {
                z-index: 1040;
            }

            /* Garantir que a secção Facts não interfira with o carousel */
            .container-fluid.facts {
                position: relative;
                z-index: 5;
            }

            /* Reduzir padding-bottom da secção Facts para diminuir espaço até Artigos */
            .container-fluid.facts {
                padding-bottom: 0rem !important;
            }

            /* Reduzir também o padding-bottom do container interno da secção Facts */
            .container-fluid.facts .container {
                padding-bottom: 0rem !important;
            }

            /* Reduzir espaço no topo da secção Artigos recentes / Últimas notícias */
            .container-fluid.section-noticias .container {
                padding-top: 0rem !important;
            }

            /* Usar margin negativo para forçar secção Artigos a subir */
            .container-fluid.section-noticias {
                margin-top: -4.25rem !important;
            }

            /* Reduzir margin-bottom da última fact-card se existir */
            .container-fluid.facts .facts-card:last-child {
                margin-bottom: 0 !important;
            }

            /* Reduzir espaço entre final dos artigos e início da secção Eventos */
            .container-fluid.section-noticias .container {
                padding-bottom: 0rem !important;
            }

            /* Atacar TODOS os níveis da secção eventos para eliminar espaço */
            
            /* 1. Container-fluid da secção eventos - adicionar espaço controlado */
            .container-fluid[style*="background: white"] {
                padding-top: 6.25rem !important;
            }

            /* 2. Container interno da secção eventos - eliminar padding-top */
            .container-fluid[style*="background: white"] .container {
                padding-top: 0rem !important;
            }

            /* 3. Título da secção eventos - manter barra horizontal intacta */
            .container-fluid[style*="background: white"] .section-title {
                margin-top: 0rem !important;
                padding-top: 0rem !important;
                padding-bottom: 0.5rem !important;
                margin-bottom: 1rem !important;
            }

            /* 4. Garantir que secção artigos não tem padding-bottom excessivo */
            .container-fluid.section-noticias {
                padding-bottom: 0rem !important;
            }

            /* 5. Usar margin negativo para subir os eventos sem afetar o título */
            .container-fluid[style*="background: white"] .row.g-4 {
                margin-top: -6.25rem !important;
                row-gap: 0rem !important;
            }

            /* 7. REDUZIR ESPAÇO ENTRE 1º E 2º EVENTO - Seletor CORRETO para Agenda */
            .container-fluid[style*="background: white"] .row.g-4 > .col-12:nth-child(2) {
                margin-top: -150px !important;
                margin-bottom: 0 !important;
            }
            
            /* 7b. Reduzir padding interno do 2º evento */
            .container-fluid[style*="background: white"] .row.g-4 > .col-12:nth-child(2) .agenda-evento-novo {
                padding: 1rem 2rem !important;
                min-height: auto !important;
            }
            
            /* 8. Compactar cards de eventos internamente para proximidade máxima */
            .container-fluid[style*="background: white"] .card {
                margin-bottom: 0.25rem !important;
                padding: 0.3rem !important;
            }
            
            .container-fluid[style*="background: white"] .card-body {
                padding: 0.4rem !important;
            }
            
            .container-fluid[style*="background: white"] .card-title {
                margin-bottom: 0.2rem !important;
                line-height: 1.1 !important;
                font-size: 0.95rem !important;
            }
            
            .container-fluid[style*="background: white"] .card-text {
                margin-bottom: 0.15rem !important;
                line-height: 1.2 !important;
                font-size: 0.85rem !important;
            }

            /* 6. Reduzir margens dos cards de eventos individuais */
            .container-fluid[style*="background: white"] .col-lg-4 {
                margin-bottom: 0.5rem !important;
            }

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

            .mobile-navbar-wrapper .navbar-toggler i {
                color: white !important;
                font-size: 18px !important;
            }

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

            .mobile-navbar-wrapper .navbar .btn {
                display: none !important;
            }
        }

        /* Desktop navbar scroll effect */
        @media (min-width: 992px) {
            /* Navbar fixo */
            .navbar-dark {
                position: fixed !important;
                top: 45px !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 1030 !important;
                width: 100% !important;
                transition: all 0.3s ease !important;
                background: transparent !important;
                padding: 15px 0 !important;
            }

            /* Garantir estrutura Bootstrap consistente */
            .navbar-dark.navbar-expand-lg {
                flex-wrap: nowrap !important;
            }

            .navbar-dark .navbar-collapse {
                flex-basis: auto !important;
            }

            /* Manter padding lateral consistente */
            .navbar-dark.px-5 {
                padding-left: 3rem !important;
                padding-right: 3rem !important;
            }

            /* Logo padrão - posicionamento consistente */
            .navbar-dark .navbar-brand {
                padding: 0 !important;
            }

            .navbar-dark .navbar-brand img {
                width: 70% !important;
                height: auto !important;
                padding-top: 5% !important;
                transition: all 0.3s ease !important;
            }

            /* Links brancos no estado inicial */
            .navbar-dark .navbar-nav .nav-link {
                color: white !important;
                transition: color 0.3s ease;
            }

            .navbar-dark .btn {
                color: white !important;
                transition: color 0.3s ease;
            }

            /* Estado scrolled - fundo branco, navbar mais compacto */
            .navbar-scrolled {
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
                top: 45px !important;
                padding: 8px 0 !important;
            }

            /* Logo menor durante scroll - manter posicionamento */
            .navbar-scrolled .navbar-brand {
                padding: 0 !important;
            }

            .navbar-scrolled .navbar-brand img {
                width: 50% !important;
                height: auto !important;
                padding-top: 2% !important;
                transition: all 0.3s ease !important;
            }

            /* Links dourados quando scrolled */
            .navbar-scrolled .navbar-nav .nav-link {
                color: #B1A276 !important;
            }

            .navbar-scrolled .navbar-nav .nav-link:hover {
                color: #9d8f64 !important;
            }

            .navbar-scrolled .btn {
                color: #B1A276 !important;
            }

            /* Topbar fixa */
            .bg-dark {
                position: fixed !important;
                top: 0 !important;
                width: 100% !important;
                z-index: 1040 !important;
                transition: all 0.3s ease !important;
            }

            /* Topbar scrolled - fundo branco */
            .topbar-scrolled {
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            }

            /* Textos dourados quando topbar scrolled */
            .topbar-scrolled .text-light {
                color: #B1A276 !important;
            }

            .topbar-scrolled small {
                color: #B1A276 !important;
            }

            .topbar-scrolled i {
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

            #header-carousel {
                margin-top: 0;
                position: relative;
                top: 0;
            }

            #header-carousel .carousel-item {
                min-height: 650px;
                height: 650px;
            }

            /* Baixar o conteúdo do carousel */
            #header-carousel .carousel-caption {
                top: 55% !important;
                bottom: auto !important;
                transform: translateY(-50%);
            }
        }

        @media (max-width: 991.98px) {
            .navbar-dark .navbar-nav .nav-link { color: white !important; }

            /* Adicionar espaçamento no título e breadcrumbs mobile */
            .text-center {
                padding-top: 8rem !important;
            }
        }
        
        /* ========== FOOTER - Títulos com Underline ========== */
        .section-title-sm h3 {
            font-size: 1.3rem !important;
            font-weight: 600 !important;
            letter-spacing: 0px !important;
            display: block;
        }
        
        /* Restaurar a barra castanha curta (::after) debaixo dos títulos */
        .section-title-sm {
            padding-bottom: 0.75rem !important;
            border: none !important;
            border-bottom: none !important;
            margin-bottom: 1rem !important;
            position: relative;
            overflow: visible !important;
        }
        
        /* Remover qualquer ::before e ::after conflitantes */
        .section-title-sm::before {
            display: none !important;
        }
        
        .section-title-sm::after {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 45% !important;
            height: 3px !important;
            background-color: #c18046 !important;
            display: block !important;
            z-index: 10 !important;
            opacity: 1 !important;
        }
        
        /* Garantir que h3 no footer não tem border */
        .col-lg-8 .section-title-sm h3 {
            border: none !important;
            border-bottom: none !important;
            padding-bottom: 0.5rem !important;
        }
        
        /* ========== FOOTER - Formulário Newsletter ========== */
        /* Reduzir altura do formulário (Desktop e Mobile) */
        #newsletter-form .input-group {
            gap: 0;
        }
        
        #newsletter-form .form-control {
            padding: 0.5rem 1rem !important;
            height: 36px !important;
            min-height: 36px !important;
            font-size: 0.95rem;
        }
        
        #newsletter-form .btn {
            padding: 0.5rem 1rem !important;
            height: 36px !important;
            min-height: 36px !important;
        }
        
        /* ========== FOOTER - Desktop ========== */
        @media (min-width: 992px) {
            /* Alinhar logo do footer verticalmente com títulos */
            .col-lg-4.col-md-6.footer-about {
                padding-top: 3rem;
            }
            
            /* Aumentar largura do formulário de inscrição */
            #newsletter-form {
                max-width: 100%;
                width: 100%;
            }
            
            #newsletter-form .input-group {
                max-width: 100%;
                width: 100%;
            }
        }
        
        /* ========== FOOTER - Versão Mobile Centralizada ========== */
        @media (max-width: 991.98px) {
            /* Traço centrado no mobile */
            .section-title-sm::after {
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            /* Centralizar logo do footer - reduzir 30px do topo */
            .col-lg-4.col-md-6.footer-about {
                text-align: center;
                margin-bottom: 2rem;
                margin-top: -30px;
                padding-top: 0 !important;
            }
            
            .col-lg-4.col-md-6.footer-about img {
                margin-top: -40px;
            }
            
            /* Centralizar coluna de menus no mobile */
            .col-lg-8.col-md-6 {
                width: 100%;
            }
            
            /* Centralizar cada coluna de menu */
            .col-lg-4.col-md-12 {
                text-align: center;
                margin-bottom: 1.5rem;
                padding-top: 0 !important;
            }
            
            /* Aumentar distância dos títulos dos menus do topo */
            .col-lg-4.col-md-12 .section-title-sm {
                margin-top: 2rem !important;
            }
            .col-lg-4.col-md-12 .d-flex {
                flex-direction: column;
                align-items: center !important;
                justify-content: center !important;
                text-align: center;
            }
            
            /* Centralizar links do menu */
            .link-animated {
                justify-content: center !important;
                align-items: center !important;
            }
            
            /* Centralizar icons sociais */
            .col-lg-4.col-md-12 .d-flex.mt-4 {
                justify-content: center !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 0.5rem !important;
            }
            
            .col-lg-4.col-md-12 .d-flex.mt-4 a {
                flex-shrink: 0 !important;
            }
            
            /* Formulário newsletter - Mobile: aumentar largura, deixar paddings left/right */
            #newsletter-form {
                width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
                margin: 0 auto;
            }
            
            #newsletter-form .input-group {
                width: 100%;
            }
            
            #newsletter-form .form-control {
                flex: 1;
            }
            
            /* Centralizar secção de copyright - Reduzir distância do topo */
            .row.justify-content-end {
                justify-content: center !important;
                margin-top: -4rem !important;
            }
            
            /* Centralizar texto e logo da empresa */
            .d-flex.align-items-center.justify-content-center {
                flex-direction: column;
                text-align: center;
                height: auto !important;
                padding-bottom: 1.5rem;
            }
            
            /* Garantir que o logo ADA fica centralizado e com mais espaço */
            .d-flex.align-items-center.justify-content-center p {
                margin: 1rem 0 0 0 !important;
                padding-bottom: 0.5rem;
            }
        }
        
        .mobile-contacts { line-height: 1.2; }
        .contact-line { margin-bottom: 0.1rem; font-size: 0.85rem; line-height: 1.1; }
        .contact-line:last-child { margin-bottom: 0; }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

        <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Navbar -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        
        <!-- Desktop Carousel -->
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
    <!-- Desktop Navbar & Carousel End -->

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
                    
                    <!-- Mobile Contact Info -->
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
    <script src="js/main.js"></script>

    <!-- Desktop Navbar Scroll Effect -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar-dark');
            const topbar = document.querySelector('.container-fluid.bg-dark.px-5.d-none.d-lg-block');

            if (navbar && window.innerWidth >= 992) { // Only apply on desktop
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 45) { // Use a small threshold for inner pages
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

