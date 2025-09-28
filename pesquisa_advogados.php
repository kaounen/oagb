<?php
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Pesquisa de Advogados";
$meta_title = "Pesquisa de Advogados - OAGB";
$meta_description = "Encontre advogados inscritos na Ordem dos Advogados da Guiné-Bissau. Pesquise por nome, especialidade ou região.";

// Parâmetros de pesquisa
$nome = clean_input($_GET['nome'] ?? '');
$especialidade = clean_input($_GET['especialidade'] ?? '');
$regiao = clean_input($_GET['regiao'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

try {
    // Construir query de pesquisa
    $where_conditions = ["a.status = 'ativo'"];
    $params = [];

    if (!empty($nome)) {
        $where_conditions[] = "(a.nome LIKE ? OR a.nome_completo LIKE ?)";
        $params[] = "%$nome%";
        $params[] = "%$nome%";
    }

    if (!empty($especialidade)) {
        $where_conditions[] = "a.especialidades LIKE ?";
        $params[] = "%$especialidade%";
    }

    if (!empty($regiao)) {
        $where_conditions[] = "a.regiao = ?";
        $params[] = $regiao;
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Contar total de resultados
    $count_sql = "SELECT COUNT(*) as total FROM advogados a WHERE $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_results = $stmt->fetch()->total;

    // Buscar advogados
    $sql = "
        SELECT a.*, 
               COALESCE(a.foto, 'default-avatar.png') as foto_perfil
        FROM advogados a 
        WHERE $where_clause 
        ORDER BY a.nome ASC 
        LIMIT $per_page OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $advogados = $stmt->fetchAll();

    // Buscar especialidades para filtro
    $stmt = $pdo->prepare("
        SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(especialidades, ',', numbers.n), ',', -1)) as especialidade
        FROM advogados
        CROSS JOIN (
            SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
        ) numbers
        WHERE CHAR_LENGTH(especialidades) - CHAR_LENGTH(REPLACE(especialidades, ',', '')) >= numbers.n - 1
        AND especialidades IS NOT NULL AND especialidades != ''
        ORDER BY especialidade
    ");
    $stmt->execute();
    $especialidades = $stmt->fetchAll();

    // Buscar regiões
    $stmt = $pdo->prepare("SELECT DISTINCT regiao FROM advogados WHERE regiao IS NOT NULL AND regiao != '' ORDER BY regiao");
    $stmt->execute();
    $regioes = $stmt->fetchAll();

} catch (Exception $e) {
    error_log("Erro na pesquisa de advogados: " . $e->getMessage());
    $advogados = [];
    $especialidades = [];
    $regioes = [];
    $total_results = 0;
}

$total_pages = ceil($total_results / $per_page);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    <?php include 'includes/meta-tags.php'; ?>

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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Pesquisa de Advogados</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white">Advogados</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Pesquisa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Search Form Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="bg-light rounded p-5 mb-5">
                <h4 class="mb-4" style="color:#5B463F;">Encontre um Advogado</h4>
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label for="nome" class="form-label">Nome do Advogado</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($nome); ?>" 
                                   placeholder="Digite o nome...">
                        </div>
                        <div class="col-lg-4">
                            <label for="especialidade" class="form-label">Especialidade</label>
                            <select class="form-select" id="especialidade" name="especialidade">
                                <option value="">Todas as especialidades</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?php echo htmlspecialchars($esp->especialidade); ?>" 
                                            <?php echo ($especialidade == $esp->especialidade) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($esp->especialidade); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="regiao" class="form-label">Região</label>
                            <select class="form-select" id="regiao" name="regiao">
                                <option value="">Todas as regiões</option>
                                <?php foreach ($regioes as $reg): ?>
                                    <option value="<?php echo htmlspecialchars($reg->regiao); ?>" 
                                            <?php echo ($regiao == $reg->regiao) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($reg->regiao); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="btn btn-primary py-2 px-5 me-3">
                                <i class="fa fa-search me-2"></i>Pesquisar
                            </button>
                            <a href="pesquisa-advogados.php" class="btn btn-outline-secondary py-2 px-5">
                                <i class="fa fa-times me-2"></i>Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Summary -->
            <?php if (!empty($nome) || !empty($especialidade) || !empty($regiao)): ?>
            <div class="alert alert-info">
                <h6 class="mb-2">Resultados da pesquisa:</h6>
                <p class="mb-0">
                    Encontrados <strong><?php echo $total_results; ?></strong> advogado(s)
                    <?php if (!empty($nome)): ?>
                        com o nome "<strong><?php echo htmlspecialchars($nome); ?></strong>"
                    <?php endif; ?>
                    <?php if (!empty($especialidade)): ?>
                        na especialidade "<strong><?php echo htmlspecialchars($especialidade); ?></strong>"
                    <?php endif; ?>
                    <?php if (!empty($regiao)): ?>
                        na região "<strong><?php echo htmlspecialchars($regiao); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Results Grid -->
            <?php if (!empty($advogados)): ?>
            <div class="row g-4">
                <?php foreach ($advogados as $advogado): ?>
                <div class="col-lg-4 col-md-6 wow slideInUp" data-wow-delay="0.3s">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <div class="text-center mb-3">
                            <img src="img/advogados/<?php echo htmlspecialchars($advogado->foto_perfil); ?>" 
                                 alt="<?php echo htmlspecialchars($advogado->nome); ?>" 
                                 class="rounded-circle mb-3" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            <h5 class="mb-1" style="color:#5B463F;">
                                <?php echo htmlspecialchars($advogado->nome); ?>
                            </h5>
                            <small class="text-muted">OAB/GB: <?php echo htmlspecialchars($advogado->numero_oab ?? 'N/A'); ?></small>
                        </div>
                        
                        <?php if (!empty($advogado->especialidades)): ?>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Especialidades:</small>
                            <div>
                                <?php 
                                $especialidades_array = explode(',', $advogado->especialidades);
                                foreach ($especialidades_array as $esp): 
                                ?>
                                    <span class="badge bg-light text-dark me-1 mb-1"><?php echo trim(htmlspecialchars($esp)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <?php if (!empty($advogado->telefone)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-phone text-primary me-2"></i>
                                <small><?php echo htmlspecialchars($advogado->telefone); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($advogado->email)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-envelope text-primary me-2"></i>
                                <small><?php echo htmlspecialchars($advogado->email); ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($advogado->regiao)): ?>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                <small><?php echo htmlspecialchars($advogado->regiao); ?></small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="text-center">
                            <a href="perfil-advogado.php?id=<?php echo $advogado->id; ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-user me-1"></i>Ver Perfil
                            </a>
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
                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>&nome=<?php echo urlencode($nome); ?>&especialidade=<?php echo urlencode($especialidade); ?>&regiao=<?php echo urlencode($regiao); ?>">
                            Anterior
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&nome=<?php echo urlencode($nome); ?>&especialidade=<?php echo urlencode($especialidade); ?>&regiao=<?php echo urlencode($regiao); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>&nome=<?php echo urlencode($nome); ?>&especialidade=<?php echo urlencode($especialidade); ?>&regiao=<?php echo urlencode($regiao); ?>">
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
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum advogado encontrado</h5>
                <p class="text-muted">Tente ajustar os critérios de pesquisa ou <a href="pesquisa-advogados.php">limpe os filtros</a>.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Search Results End -->

    <?php include 'includes/footer.php'; ?>

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
</body>
</html>