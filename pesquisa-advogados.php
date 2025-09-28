<?php
require_once 'connect.php';

$resultados = [];
$filtros_aplicados = [];
$total_resultados = 0;

// Processar formulário de pesquisa
if ($_POST) {
    $sql = "SELECT numero_registo, nome_completo, regiao, localidade, telefone, email, data_inscricao FROM advogados WHERE status = 'ativo'";
    $params = [];
    
    if (!empty($_POST['nome'])) {
        $sql .= " AND nome_completo LIKE ?";
        $params[] = '%' . sanitize($_POST['nome']) . '%';
        $filtros_aplicados['nome'] = sanitize($_POST['nome']);
    }
    
    if (!empty($_POST['registo'])) {
        $sql .= " AND numero_registo LIKE ?";
        $params[] = '%' . sanitize($_POST['registo']) . '%';
        $filtros_aplicados['registo'] = sanitize($_POST['registo']);
    }
    
    if (!empty($_POST['regiao'])) {
        $sql .= " AND regiao = ?";
        $params[] = sanitize($_POST['regiao']);
        $filtros_aplicados['regiao'] = sanitize($_POST['regiao']);
    }
    
    if (!empty($_POST['localidade'])) {
        $sql .= " AND localidade LIKE ?";
        $params[] = '%' . sanitize($_POST['localidade']) . '%';
        $filtros_aplicados['localidade'] = sanitize($_POST['localidade']);
    }
    
    if (!empty($_POST['morada'])) {
        $sql .= " AND morada LIKE ?";
        $params[] = '%' . sanitize($_POST['morada']) . '%';
        $filtros_aplicados['morada'] = sanitize($_POST['morada']);
    }
    
    $sql .= " ORDER BY nome_completo ASC LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll();
    $total_resultados = count($resultados);
}

$page_title = "Pesquisa de Advogados";
$breadcrumb = "Advogados";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Pesquisa de Advogados - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Pesquisa de Advogados, OAGB, Advogados Guinea-Bissau" name="keywords">
    <meta content="Pesquise advogados qualificados na Guiné-Bissau por região e especialidade" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

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

    <!-- Navbar & Header Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo $page_title; ?></a>
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
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa">
                        <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Search Form Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Encontre um Advogado</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Pesquise por critérios específicos</h1>
            </div>
            
            <div class="row g-5">
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.3s">
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-12">
                                <select class="form-select bg-light border-0" name="regiao" style="height: 55px;">
                                    <option value="">Selecione a Região</option>
                                    <?php foreach ($regioes_gb as $codigo => $nome): ?>
                                        <option value="<?php echo $codigo; ?>" <?php echo (isset($filtros_aplicados['regiao']) && $filtros_aplicados['regiao'] == $codigo) ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="nome" class="form-control border-0 bg-light px-4" placeholder="Nome do Advogado" style="height: 55px;" value="<?php echo isset($filtros_aplicados['nome']) ? htmlspecialchars($filtros_aplicados['nome']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="registo" class="form-control border-0 bg-light px-4" placeholder="Número de Registo" style="height: 55px;" value="<?php echo isset($filtros_aplicados['registo']) ? htmlspecialchars($filtros_aplicados['registo']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="localidade" class="form-control border-0 bg-light px-4" placeholder="Localidade" style="height: 55px;" value="<?php echo isset($filtros_aplicados['localidade']) ? htmlspecialchars($filtros_aplicados['localidade']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="morada" class="form-control border-0 bg-light px-4" placeholder="Morada" style="height: 55px;" value="<?php echo isset($filtros_aplicados['morada']) ? htmlspecialchars($filtros_aplicados['morada']) : ''; ?>">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">
                                    <i class="fa fa-search me-2"></i>Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.6s">
                    <img src="img/old-books-library-table.jpg" style="width:100%;height:auto;" class="img-fluid rounded shadow" alt="Biblioteca Legal">
                    <div class="bg-light rounded p-4 mt-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Informações Úteis</h5>
                        <ul class="list-unstyled" style="font-family: 'Open Sans';">
                            <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Pesquise por região para encontrar advogados próximos</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Use o número de registo para verificar credenciais</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Todos os advogados listados estão em situação regular</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-primary me-2"></i>Para mais informações, consulte o nosso <a href="advogados-inscritos.php">registo completo</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Form End -->

    <!-- Results Section -->
    <?php if ($_POST): ?>
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container">
            <!-- Results Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                            Resultados da Pesquisa
                        </h3>
                        <span class="badge bg-primary fs-6">
                            <?php echo $total_resultados; ?> resultado(s) encontrado(s)
                        </span>
                    </div>
                    
                    <!-- Applied Filters -->
                    <?php if (!empty($filtros_aplicados)): ?>
                    <div class="mt-3">
                        <h6 class="mb-2" style="font-family: 'Open Sans';">Filtros aplicados:</h6>
                        <?php foreach ($filtros_aplicados as $campo => $valor): ?>
                            <span class="badge bg-secondary me-2">
                                <?php 
                                $labels = [
                                    'nome' => 'Nome',
                                    'registo' => 'Registo',
                                    'regiao' => 'Região',
                                    'localidade' => 'Localidade',
                                    'morada' => 'Morada'
                                ];
                                echo $labels[$campo] . ': ' . htmlspecialchars($valor);
                                ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Results -->
            <?php if ($total_resultados > 0): ?>
            <div class="row g-4">
                <?php foreach ($resultados as $advogado): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                        <?php echo htmlspecialchars($advogado->nome_completo); ?>
                                    </h5>
                                    <small class="text-muted" style="font-family: 'Open Sans';">
                                        Registo: <?php echo htmlspecialchars($advogado->numero_registo); ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row text-sm">
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->regiao); ?>
                                            <?php if ($advogado->localidade): ?>
                                                - <?php echo htmlspecialchars($advogado->localidade); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if ($advogado->telefone): ?>
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->telefone); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($advogado->email): ?>
                                    <div class="col-12 mb-2">
                                        <i class="bi bi-envelope text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->email); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <i class="bi bi-calendar text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            Inscrito em <?php echo format_date($advogado->data_inscricao); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <?php if ($advogado->telefone): ?>
                                <a href="tel:<?php echo $advogado->telefone; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-telephone me-2"></i>Ligar
                                </a>
                                <?php endif; ?>
                                <?php if ($advogado->email): ?>
                                <a href="mailto:<?php echo $advogado->email; ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-envelope me-2"></i>Enviar Email
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_resultados >= 50): ?>
            <div class="text-center mt-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Foram mostrados os primeiros 50 resultados. Para uma pesquisa mais específica, use mais filtros.
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center">
                <div class="alert alert-warning">
                    <h5><i class="bi bi-search me-2"></i>Nenhum resultado encontrado</h5>
                    <p class="mb-0">Tente ajustar os critérios de pesquisa ou consulte a nossa <a href="advogados-inscritos.php">lista completa de advogados</a>.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Call to Action -->
    <!-- <div class="container-fluid bg-primary py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="text-white mb-2" style="font-family: 'Libre Baskerville';">Não encontrou o que procura?</h3>
                    <p class="text-white mb-0" style="font-family: 'Open Sans';">Solicite a indicação de um advogado qualificado para o seu caso específico.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="solicitacao-advogados.php" class="btn btn-light py-3 px-5 me-3">Solicitar Advogado</a>
                    <a href="advogados-inscritos.php" class="btn btn-outline-light py-3 px-5">Ver Todos</a>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Footer -->
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