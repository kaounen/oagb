<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Parâmetros de filtro e paginação
$tipo = isset($_GET['tipo']) ? clean_input($_GET['tipo']) : 'todos';
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$busca = isset($_GET['busca']) ? clean_input($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 9;
$offset = ($pagina - 1) * $por_pagina;

// Construir query
$where = ['ativo = 1'];
$params = [];

// Filtro por tipo
if ($tipo != 'todos' && in_array($tipo, ['congresso', 'conferencia', 'formacao', 'reuniao', 'workshop', 'palestra', 'outros'])) {
    $where[] = 'tipo_evento = ?';
    $params[] = $tipo;
}

// Filtro por mês/ano
if ($mes > 0 && $mes <= 12) {
    $where[] = 'MONTH(data_evento) = ?';
    $params[] = $mes;
}
if ($ano > 2020 && $ano <= date('Y') + 2) {
    $where[] = 'YEAR(data_evento) = ?';
    $params[] = $ano;
}

// Busca
if (!empty($busca)) {
    $where[] = '(titulo LIKE ? OR descricao LIKE ? OR local_evento LIKE ?)';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
    $params[] = '%' . $busca . '%';
}

$where_clause = implode(' AND ', $where);

try {
    // Contar total de eventos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE $where_clause");
    $stmt->execute($params);
    $total_eventos = $stmt->fetch()->total;
    $total_paginas = ceil($total_eventos / $por_pagina);
    
    // Buscar eventos
    $stmt = $pdo->prepare("
        SELECT * FROM agenda 
        WHERE $where_clause 
        ORDER BY data_evento DESC 
        LIMIT $por_pagina OFFSET $offset
    ");
    $stmt->execute($params);
    $eventos = $stmt->fetchAll();
    
    // Buscar eventos em destaque (próximos 3 eventos importantes)
    $stmt = $pdo->prepare("
        SELECT * FROM agenda 
        WHERE ativo = 1 AND destaque = 1 AND DATE(data_evento) >= CURDATE()
        ORDER BY data_evento ASC 
        LIMIT 3
    ");
    $stmt->execute();
    $eventos_destaque = $stmt->fetchAll();
    
    // Estatísticas para sidebar
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN DATE(data_evento) >= CURDATE() THEN 1 END) as futuros,
            COUNT(CASE WHEN DATE(data_evento) < CURDATE() THEN 1 END) as passados,
            COUNT(CASE WHEN MONTH(data_evento) = MONTH(CURDATE()) AND YEAR(data_evento) = YEAR(CURDATE()) THEN 1 END) as este_mes
        FROM agenda 
        WHERE ativo = 1
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
} catch (Exception $e) {
    error_log("Erro ao buscar eventos: " . $e->getMessage());
    $eventos = [];
    $eventos_destaque = [];
    $total_eventos = 0;
    $total_paginas = 0;
}

$page_title = "Agenda de Eventos";
$meta_description = "Agenda de eventos, formações, congressos e atividades da Ordem dos Advogados da Guiné-Bissau";
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .event-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .event-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .event-card .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .event-card .event-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .event-date-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 60px;
        }
        
        .event-date-badge .day {
            font-size: 1.5rem;
            font-weight: bold;
            color: #c18046;
            line-height: 1;
        }
        
        .event-date-badge .month {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #666;
        }
        
        .event-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        
        .type-congresso { background: #e3f2fd; color: #1976d2; }
        .type-formacao { background: #f3e5f5; color: #7b1fa2; }
        .type-reuniao { background: #e8f5e9; color: #388e3c; }
        .type-workshop { background: #fff3e0; color: #f57c00; }
        .type-palestra { background: #fce4ec; color: #c2185b; }
        .type-conferencia { background: #e0f2f1; color: #00796b; }
        .type-outros { background: #f5f5f5; color: #616161; }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .calendar-widget {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .calendar-widget .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #c18046;
        }
        
        .stats-card .stats-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
        
        .highlight-event {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .highlight-event h3 {
            color: white;
        }
        
        .highlight-event .btn {
            background: white;
            color: #667eea;
        }
        
        .timeline-view {
            position: relative;
            padding-left: 40px;
        }
        
        .timeline-view::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -29px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #c18046;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .event-card .event-image {
                height: 150px;
            }
            
            .filter-section {
                padding: 1rem;
            }
            
            .timeline-view {
                padding-left: 30px;
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

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Agenda de Eventos</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Agenda</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Agenda Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- Eventos em Destaque -->
            <?php if (!empty($eventos_destaque)): ?>
            <div class="highlight-event">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="mb-3">
                            <i class="fa fa-star me-2"></i>
                            <?php echo htmlspecialchars($eventos_destaque[0]->titulo); ?>
                        </h3>
                        <p class="mb-3"><?php echo htmlspecialchars(truncate_text($eventos_destaque[0]->descricao, 150)); ?></p>
                        <div class="d-flex flex-wrap gap-3">
                            <span><i class="far fa-calendar me-2"></i><?php echo format_date_pt($eventos_destaque[0]->data_evento); ?></span>
                            <?php if (!empty($eventos_destaque[0]->local_evento)): ?>
                            <span><i class="fa fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($eventos_destaque[0]->local_evento); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 text-end">
                        <a href="evento.php?id=<?php echo $eventos_destaque[0]->id; ?>" class="btn btn-light btn-lg">
                            Ver Detalhes <i class="fa fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Filtros -->
            <div class="filter-section">
                <form method="GET" action="agenda.php" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Evento</label>
                        <select name="tipo" class="form-select">
                            <option value="todos">Todos</option>
                            <option value="congresso" <?php echo $tipo == 'congresso' ? 'selected' : ''; ?>>Congresso</option>
                            <option value="conferencia" <?php echo $tipo == 'conferencia' ? 'selected' : ''; ?>>Conferência</option>
                            <option value="formacao" <?php echo $tipo == 'formacao' ? 'selected' : ''; ?>>Formação</option>
                            <option value="reuniao" <?php echo $tipo == 'reuniao' ? 'selected' : ''; ?>>Reunião</option>
                            <option value="workshop" <?php echo $tipo == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                            <option value="palestra" <?php echo $tipo == 'palestra' ? 'selected' : ''; ?>>Palestra</option>
                            <option value="outros" <?php echo $tipo == 'outros' ? 'selected' : ''; ?>>Outros</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mês</label>
                        <select name="mes" class="form-select">
                            <option value="0">Todos</option>
                            <?php 
                            $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                                     'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                            foreach ($meses as $i => $nome_mes): 
                            ?>
                            <option value="<?php echo $i + 1; ?>" <?php echo $mes == ($i + 1) ? 'selected' : ''; ?>>
                                <?php echo $nome_mes; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ano</label>
                        <select name="ano" class="form-select">
                            <option value="0">Todos</option>
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $ano == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pesquisar</label>
                        <input type="text" name="busca" class="form-control" placeholder="Buscar evento..." value="<?php echo htmlspecialchars($busca); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="row g-5">
                <!-- Lista de Eventos -->
                <div class="col-lg-8">
                    <?php if (!empty($eventos)): ?>
                    <div class="row g-4">
                        <?php foreach ($eventos as $evento): ?>
                        <div class="col-md-6">
                            <div class="event-card">
                                <div class="position-relative">
                                    <?php if (!empty($evento->imagem_destaque)): ?>
                                    <img src="gestao/assets/uploads/files/<?php echo htmlspecialchars($evento->imagem_destaque); ?>" 
                                         alt="<?php echo htmlspecialchars($evento->titulo); ?>" 
                                         class="event-image">
                                    <?php else: ?>
                                    <div class="event-image"></div>
                                    <?php endif; ?>
                                    
                                    <div class="event-date-badge">
                                        <div class="day"><?php echo date('d', strtotime($evento->data_evento)); ?></div>
                                        <div class="month"><?php echo substr($meses[date('n', strtotime($evento->data_evento)) - 1], 0, 3); ?></div>
                                    </div>
                                </div>
                                
                                <div class="event-body">
                                    <span class="event-type-badge type-<?php echo $evento->tipo_evento; ?>">
                                        <?php echo ucfirst($evento->tipo_evento); ?>
                                    </span>
                                    
                                    <h5 class="mb-3">
                                        <a href="evento.php?id=<?php echo $evento->id; ?>" class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($evento->titulo); ?>
                                        </a>
                                    </h5>
                                    
                                    <?php if (!empty($evento->local_evento)): ?>
                                    <p class="text-muted mb-2">
                                        <i class="fa fa-map-marker-alt me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($evento->local_evento); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <p class="text-muted mb-3">
                                        <?php echo htmlspecialchars(truncate_text($evento->descricao, 100)); ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <a href="evento.php?id=<?php echo $evento->id; ?>" class="btn btn-outline-primary btn-sm">
                                            Ver Detalhes <i class="fa fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($total_paginas > 1): ?>
                    <nav aria-label="Paginação" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])); ?>">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])); ?>">
                                    Próximo <i class="bi bi-arrow-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum evento encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros ou volte mais tarde.</p>
                        <a href="agenda.php" class="btn btn-primary mt-3">
                            <i class="fa fa-refresh me-2"></i>Ver Todos os Eventos
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Estatísticas -->
                    <div class="mb-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville', serif;">Estatísticas</h5>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo $stats->futuros ?? 0; ?></div>
                                    <div class="stats-label">Próximos Eventos</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo $stats->este_mes ?? 0; ?></div>
                                    <div class="stats-label">Este Mês</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calendário -->
                    <div class="calendar-widget mb-4">
                        <div class="calendar-header">
                            <h5 style="font-family: 'Libre Baskerville', serif;">Calendário</h5>
                            <span class="text-primary"><?php echo strftime('%B %Y'); ?></span>
                        </div>
                        <div id="mini-calendar"></div>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="bg-primary rounded p-4 text-white">
                        <h5 class="mb-3 text-white">Não Perca Nenhum Evento</h5>
                        <p class="mb-3">Subscreva a nossa newsletter e receba notificações sobre novos eventos.</p>
                        <form action="subscricao.php" method="POST">
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="Seu email" required>
                                <button class="btn btn-dark" type="submit">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Agenda End -->

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>
