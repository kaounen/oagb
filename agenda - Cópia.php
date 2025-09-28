<?php
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Agenda";
$meta_title = "Agenda - OAGB";
$meta_description = "Agenda de eventos, congressos, formações e atividades da Ordem dos Advogados da Guiné-Bissau.";

// Parâmetros de filtro
$tipo = clean_input($_GET['tipo'] ?? '');
$periodo = clean_input($_GET['periodo'] ?? 'todos');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

try {
    // Construir query baseado no período
    $where_conditions = ["ativo = 1"];
    $params = [];

    // Filtro por tipo
    if (!empty($tipo)) {
        $where_conditions[] = "tipo_evento = ?";
        $params[] = $tipo;
    }

    // Filtro por período
    switch ($periodo) {
        case 'proximos':
            $where_conditions[] = "data_evento >= NOW()";
            $order_by = "data_evento ASC";
            break;
        case 'passados':
            $where_conditions[] = "data_evento < NOW()";
            $order_by = "data_evento DESC";
            break;
        case 'este_mes':
            $where_conditions[] = "MONTH(data_evento) = MONTH(NOW()) AND YEAR(data_evento) = YEAR(NOW())";
            $order_by = "data_evento ASC";
            break;
        default:
            $order_by = "data_evento DESC";
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Contar total de resultados
    $count_sql = "SELECT COUNT(*) as total FROM agenda WHERE $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_results = $stmt->fetch()->total;

    // Buscar eventos
    $sql = "
        SELECT *, 
               CASE WHEN data_evento >= NOW() THEN 'futuro' ELSE 'passado' END as status_evento
        FROM agenda 
        WHERE $where_clause 
        ORDER BY $order_by
        LIMIT $per_page OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $eventos = $stmt->fetchAll();

    // Próximos eventos para sidebar
    $stmt = $pdo->prepare("
        SELECT * FROM agenda 
        WHERE data_evento >= NOW() AND ativo = 1 
        ORDER BY data_evento ASC 
        LIMIT 5
    ");
    $stmt->execute();
    $proximos_eventos = $stmt->fetchAll();

    // Estatísticas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE data_evento >= NOW() AND ativo = 1");
    $stmt->execute();
    $total_proximos = $stmt->fetch()->total;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE MONTH(data_evento) = MONTH(NOW()) AND YEAR(data_evento) = YEAR(NOW()) AND ativo = 1");
    $stmt->execute();
    $total_este_mes = $stmt->fetch()->total;

} catch (Exception $e) {
    error_log("Erro na página de agenda: " . $e->getMessage());
    $eventos = [];
    $proximos_eventos = [];
    $total_results = 0;
    $total_proximos = 0;
    $total_este_mes = 0;
}

$total_pages = ceil($total_results / $per_page);

// Tipos de eventos disponíveis
$tipos_eventos = [
    'congresso' => 'Congressos',
    'conferencia' => 'Conferências',
    'formacao' => 'Formações',
    'reuniao' => 'Reuniões',
    'workshop' => 'Workshops',
    'palestra' => 'Palestras',
    'outros' => 'Outros'
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    <?php 
    $meta_description = $meta_description;
    include 'includes/meta_tags_include.php'; 
    ?>

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
        /* Header com Background Image PADRÃO */
        .bg-header {
            background: linear-gradient(rgba(9, 30, 62, .7), rgba(9, 30, 62, .7)), url(img/brass-scales-justice-close-up-view.jpg) center center no-repeat;
            background-size: cover;
            margin-bottom: 30px !important;
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
            
            .bg-header {
                margin-bottom: 20px !important;
            }
        }
        
        /* Menu dropdowns no mouseover para desktop */
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
            margin: 0 5px;
        }
        
        .action-buttons .btn:hover {
            background-color: #8B6B47;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Links com underline no hover */
        a.linkSublinhado:hover,
        a.text-decoration-none:hover {
            text-decoration: underline !important;
        }
        
        /* Paleta de cores padrão */
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        .bg-color-5 { background-color: #5a443d; }
        
        /* Tipografia padrão */
        .texto-conteudo {
            color: #111923;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
        }
        
        .titulo-artigo {
            color: #4D1C21;
            font-family: 'Libre Baskerville', serif;
            font-size: 180%;
        }
        
        /* Botões arrow padrão */
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

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Stats -->
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">Estatísticas</h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="bg-color-1 text-white rounded p-3 mb-2">
                                    <h4 class="mb-0"><?php echo $total_proximos; ?></h4>
                                </div>
                                <small>Próximos Eventos</small>
                            </div>
                            <div class="col-6">
                                <div class="bg-color-3 text-white rounded p-3 mb-2">
                                    <h4 class="mb-0"><?php echo $total_este_mes; ?></h4>
                                </div>
                                <small>Este Mês</small>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <?php if (!empty($proximos_eventos)): ?>
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">Próximos Eventos</h5>
                        <?php foreach ($proximos_eventos as $proximo): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="bg-color-1 text-white rounded text-center p-2 me-3" style="min-width: 50px;">
                                <div class="fw-bold"><?php echo format_date_pt($proximo->data_evento, 'd'); ?></div>
                                <small><?php echo format_date_pt($proximo->data_evento, 'M'); ?></small>
                            </div>
                            <div>
                                <h6 class="mb-1">
                                    <a href="evento.php?id=<?php echo $proximo->id; ?>" 
                                       class="text-dark text-decoration-none linkSublinhado">
                                        <?php echo htmlspecialchars(truncate_text($proximo->titulo, 40)); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?php echo format_date_pt($proximo->data_evento, 'H:i'); ?>
                                    <?php if (!empty($proximo->local_evento)): ?>
                                        - <?php echo htmlspecialchars(truncate_text($proximo->local_evento, 30)); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="text-center">
                            <a href="agenda.php?periodo=proximos" class="d-block">
                                <div class="btn-arrow-only">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Filters -->
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">Filtros Rápidos</h5>
                        <div class="d-grid gap-2">
                            <a href="agenda.php?periodo=proximos" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-arrow-right me-1"></i>Próximos Eventos
                            </a>
                            <a href="agenda.php?periodo=este_mes" class="btn btn-outline-success btn-sm">
                                <i class="fa fa-calendar me-1"></i>Este Mês
                            </a>
                            <a href="agenda.php?tipo=formacao" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-graduation-cap me-1"></i>Formações
                            </a>
                            <a href="agenda.php?tipo=congresso" class="btn btn-outline-warning btn-sm">
                                <i class="fa fa-users me-1"></i>Congressos
                            </a>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-color-4 rounded p-4 text-center text-white">
                        <h5 class="text-white mb-3" style="font-family: 'Libre Baskerville', serif;">Sugestão de Evento?</h5>
                        <p class="text-white mb-4" style="font-family: 'Open Sans', sans-serif;">Tem alguma sugestão de evento ou formação? Entre em contacto connosco.</p>
                        <a href="contacto.php" class="btn btn-light">
                            <i class="fa fa-envelope me-2"></i>Contactar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Agenda Content End -->

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
    // Função compartilhar
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: 'Agenda OAGB',
                text: 'Consulte a agenda de eventos da OAGB',
                url: window.location.href
            });
        } else {
            alert('Use o link para compartilhar: ' + window.location.href);
        }
    }
    
    // Função traduzir
    function translatePage() {
        window.open('https://translate.google.com/translate?u=' + encodeURIComponent(window.location.href));
    }
    </script>
</body>
</html>
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
                            <li class="breadcrumb-item active" aria-current="page">Agenda</li>
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
        <button onclick="sharePage()" class="btn btn-sm">
            <i class="bi bi-share"></i> Partilhar
        </button>
        <button onclick="translatePage()" class="btn btn-sm">
            <i class="bi bi-translate"></i> Traduzir
        </button>
    </div>

    <!-- Agenda Content Start -->
    <div class="container-fluid py-3">
        <div class="container">
            <div class="row g-5">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Filter Form -->
                    <div class="bg-light rounded p-4 mb-5">
                        <form method="GET" action="">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label for="periodo" class="form-label">Período</label>
                                    <select class="form-select" id="periodo" name="periodo">
                                        <option value="todos" <?php echo ($periodo == 'todos') ? 'selected' : ''; ?>>Todos os eventos</option>
                                        <option value="proximos" <?php echo ($periodo == 'proximos') ? 'selected' : ''; ?>>Próximos eventos</option>
                                        <option value="este_mes" <?php echo ($periodo == 'este_mes') ? 'selected' : ''; ?>>Este mês</option>
                                        <option value="passados" <?php echo ($periodo == 'passados') ? 'selected' : ''; ?>>Eventos passados</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="tipo" class="form-label">Tipo de Evento</label>
                                    <select class="form-select" id="tipo" name="tipo">
                                        <option value="">Todos os tipos</option>
                                        <?php foreach ($tipos_eventos as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($tipo == $key) ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 d-flex align-items-end">
                                    <button type="submit" class="btn bg-color-1 text-white me-2">
                                        <i class="fa fa-filter me-1"></i>Filtrar
                                    </button>
                                    <a href="agenda.php" class="btn bg-color-4 text-white">
                                        <i class="fa fa-times me-1"></i>Limpar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0" style="color:#5B463F; font-family: 'Libre Baskerville', serif;">
                            <?php 
                            switch ($periodo) {
                                case 'proximos': echo "Próximos Eventos"; break;
                                case 'passados': echo "Eventos Passados"; break;
                                case 'este_mes': echo "Eventos deste Mês"; break;
                                default: echo "Todos os Eventos";
                            }
                            ?>
                            <small class="text-muted">(<?php echo $total_results; ?> encontrado(s))</small>
                        </h5>
                    </div>

                    <!-- Events Grid -->
                    <?php if (!empty($eventos)): ?>
                    <div class="row g-4">
                        <?php foreach ($eventos as $evento): ?>
                        <div class="col-lg-6">
                            <div class="bg-white rounded shadow-sm p-4 h-100 position-relative">
                                <?php if ($evento->status_evento == 'passado'): ?>
                                <div class="position-absolute top-0 end-0 bg-secondary text-white px-2 py-1 m-2 rounded">
                                    <small>Finalizado</small>
                                </div>
                                <?php elseif ($evento->destaque): ?>
                                <div class="position-absolute top-0 end-0 bg-color-1 text-white px-2 py-1 m-2 rounded">
                                    <small>Destaque</small>
                                </div>
                                <?php endif; ?>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-color-1 text-white rounded p-3 me-3 flex-shrink-0">
                                        <div class="text-center">
                                            <div class="fw-bold"><?php echo format_date_pt($evento->data_evento, 'd'); ?></div>
                                            <small><?php echo format_date_pt($evento->data_evento, 'M'); ?></small>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2 titulo-artigo">
                                            <a href="evento.php?id=<?php echo $evento->id; ?>" 
                                               class="text-decoration-none linkSublinhado" style="color:#4D1C21;">
                                                <?php echo htmlspecialchars($evento->titulo); ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-2">
                                            <i class="fa fa-clock me-1"></i>
                                            <?php echo format_date_pt($evento->data_evento, 'd/m/Y \à\s H:i'); ?>
                                            <?php if (!empty($evento->data_fim_evento) && $evento->data_fim_evento != $evento->data_evento): ?>
                                                - <?php echo format_date_pt($evento->data_fim_evento, 'H:i'); ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if (!empty($evento->local_evento)): ?>
                                        <p class="text-muted mb-2">
                                            <i class="fa fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($evento->local_evento); ?>
                                        </p>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-dark text-capitalize">
                                            <?php echo htmlspecialchars($evento->tipo_evento ?? 'Evento'); ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($evento->descricao)): ?>
                                <p class="mb-3 texto-conteudo">
                                    <?php echo htmlspecialchars(truncate_text($evento->descricao, 120)); ?>
                                </p>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="evento.php?id=<?php echo $evento->id; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-info-circle me-1"></i>Mais detalhes
                                    </a>
                                    
                                    <?php if ($evento->status_evento == 'futuro'): ?>
                                    <?php
                                    $start_date = date('Ymd\THis', strtotime($evento->data_evento));
                                    $end_date = !empty($evento->data_fim_evento) ? date('Ymd\THis', strtotime($evento->data_fim_evento)) : date('Ymd\THis', strtotime($evento->data_evento . ' +2 hours'));
                                    $google_cal_url = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode($evento->titulo) . 
                                                     "&dates=" . $start_date . "/" . $end_date . 
                                                     "&details=" . urlencode($evento->descricao) . 
                                                     "&location=" . urlencode($evento->local_evento);
                                    ?>
                                    <a href="<?php echo $google_cal_url; ?>" target="_blank" 
                                       class="btn btn-success btn-sm">
                                        <i class="fa fa-calendar-plus me-1"></i>Adicionar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Navegação de páginas" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page - 1); ?>&periodo=<?php echo urlencode($periodo); ?>&tipo=<?php echo urlencode($tipo); ?>">
                                    Anterior
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&periodo=<?php echo urlencode($periodo); ?>&tipo=<?php echo urlencode($tipo); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page + 1); ?>&periodo=<?php echo urlencode($periodo); ?>&tipo=<?php echo urlencode($tipo); ?>">
                                    Próximo
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <?php else: ?>
                    <!-- No Results -->
                    <div class="text-center py-5">
                        <i class="fa fa-calendar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum evento encontrado</h5>
                        <p class="text-muted">
                            <?php if (!empty($tipo) || $periodo != 'todos'): ?>
                                Tente ajustar os filtros ou <a href="agenda.php">veja todos os eventos</a>.
                            <?php else: ?>
                                Não há eventos disponíveis no momento.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>