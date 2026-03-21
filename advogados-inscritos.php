<?php
require_once 'connect.php';
require_once 'includes/functions.php';

// Letra selecionada (padrão: A)
$letra_atual = isset($_GET['letra']) ? strtoupper(clean_input($_GET['letra'])) : 'A';

// Validar letra
if (!preg_match('/^[A-Z]$/', $letra_atual)) {
    $letra_atual = 'A';
}

// Buscar advogados da letra selecionada
$stmt = $pdo->prepare("
    SELECT numero_registo, nome_completo, regiao, localidade, telefone, email, data_inscricao
    FROM advogados
    WHERE status = 'ativo' AND nome_completo LIKE ?
    ORDER BY nome_completo ASC
    LIMIT 100
");
$stmt->execute([$letra_atual . '%']);
$advogados = $stmt->fetchAll();

// Contar advogados por letra para mostrar quantidades
$alfabeto = range('A', 'Z');
$contagem_por_letra = [];

foreach ($alfabeto as $letra) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo' AND nome_completo LIKE ?");
    $stmt->execute([$letra . '%']);
    $contagem_por_letra[$letra] = $stmt->fetch()->total;
}

// Total geral
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
$stmt->execute();
$total_advogados = $stmt->fetch()->total;

$page_title = "Advogados Inscritos em Vigor";
$breadcrumb = "Advogados";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Advogados Inscritos - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Advogados Inscritos OAGB, Lista Advogados Guinea-Bissau" name="keywords">
    <meta content="Lista completa de advogados inscritos na Ordem dos Advogados da Guiné-Bissau" name="description">

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

    <!-- Custom Styles for this page -->
    <style>
        /* Colors from index.php */
        .bg-color-1 { background-color: #c18046; }
        .bg-color-2 { background-color: #f37263; }
        .bg-color-3 { background-color: #a5684e; }
        .bg-color-4 { background-color: #a98c78; }
        .bg-color-5 { background-color: #5a443d; }

        .text-color-1 { color: #c18046; }
        .text-color-2 { color: #f37263; }
        .text-color-3 { color: #a5684e; }
        .text-color-4 { color: #a98c78; }
        .text-color-5 { color: #5a443d; }
        .text-main { color: #4D1C21; }
        .text-secondary { color: #111923; }

        /* Alphabet navigation */
        .alphabet-nav .btn {
            transition: all 0.2s ease;
        }

        .alphabet-nav .btn:hover {
            transform: scale(1.05);
        }

        /* Lawyer cards */
        .lawyer-card {
            transition: all 0.3s ease;
        }

        .lawyer-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

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
                border-radius: 50% !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
                line-height: 1 !important;
            }
        }

        /* Mobile navbar inside header styling */
        .bg-header .navbar {
            background: transparent !important;
        }

        .bg-header .navbar-brand img {
            filter: brightness(1.1);
        }

        /* Desktop menu colors */
        @media (min-width: 992px) {
            .navbar-dark .navbar-nav .nav-link {
                color: #c18046 !important;
            }

            .navbar-dark .navbar-nav .nav-link:hover,
            .navbar-dark .navbar-nav .nav-link:focus {
                color: #a5684e !important;
            }

            .navbar-dark .navbar-nav .nav-link.active {
                color: #c18046 !important;
            }
        }

        /* Mobile menu text is white */
        @media (max-width: 991.98px) {
            .navbar-dark .navbar-nav .nav-link {
                color: white !important;
            }

            .navbar-dark .navbar-nav .nav-link:hover,
            .navbar-dark .navbar-nav .nav-link:focus {
                color: rgba(255,255,255,0.8) !important;
            }

            .navbar-dark .navbar-nav .nav-link.active {
                color: white !important;
            }
        }

        /* Mobile contact info */
        .mobile-contacts {
            line-height: 1.2;
        }

        .contact-line {
            margin-bottom: 0.2rem;
            font-size: 0.85rem;
        }

        .contact-line:last-child {
            margin-bottom: 0;
        }

        /* Mobile navbar adjustments */
        @media (max-width: 991.98px) {
            .bg-header .navbar {
                padding: 1rem 1.5rem;
                margin-bottom: 2rem;
            }

            .bg-header .navbar-brand {
                margin-bottom: 1.5rem;
            }

            .bg-header .navbar-brand img {
                width: 220px !important;
                max-width: 90% !important;
            }

            .bg-header .navbar-collapse {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255,255,255,0.2);
            }

            .bg-header .navbar-nav {
                margin-bottom: 2rem;
            }

            .bg-header .navbar-nav .nav-link {
                padding: 0.8rem 1rem !important;
                margin: 0.3rem 0;
                border-radius: 5px;
                transition: all 0.3s ease;
            }

            .bg-header .navbar-nav .nav-link:hover {
                background-color: rgba(255,255,255,0.1);
            }

            .bg-header .navbar-nav .dropdown-menu {
                background: rgba(255,255,255,0.95) !important;
                border: none;
                border-radius: 8px;
                margin-top: 0.5rem;
                padding: 0.5rem;
            }

            .bg-header .navbar-nav .dropdown-item {
                padding: 0.7rem 1rem;
                margin: 0.2rem 0;
                border-radius: 5px;
                transition: all 0.3s ease;
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


    <!-- Desktop Navbar -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
    </div>

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-3 bg-header" style="margin-bottom: 90px;">
        <!-- Mobile Contact Info -->
        <div class="container-fluid d-block d-lg-none px-4 py-3">
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
                    <a href="portal/login.php" class="btn btn-outline-light btn-sm ms-1" title="Área Reservada (Portal)">
                        <i class="fas fa-user-circle"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Navbar inside header -->
        <div class="container-fluid position-relative p-0 d-block d-lg-none">
            <?php include 'includes/navbar.php'; ?>
        </div>

        <div class="row py-5">
            <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>

                <!-- Breadcrumbs -->
                <div class="mb-3">
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo $page_title; ?></a>
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
    <!-- Header End -->

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

    <!-- Alphabet Navigation & List Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <!-- Header Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                    <i class="fas fa-users text-primary me-3"></i>Advogados Inscritos - Letra <?php echo $letra_atual; ?>
                                </h3>
                                <p class="mb-0" style="font-family: 'Open Sans';">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Mostrando <?php echo count($advogados); ?> advogado(s) da letra <?php echo $letra_atual; ?> de <?php echo $total_advogados; ?> total
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="pesquisa-advogados.php" class="btn btn-primary">
                                    <i class="fa fa-search me-2"></i>Pesquisa Avançada
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alphabet Navigation -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="bg-white rounded shadow-sm p-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                            <i class="fas fa-sort-alpha-down text-primary me-2"></i>Navegação Alfabética
                        </h5>
                        <div class="alphabet-nav">
                            <?php foreach ($alfabeto as $letra): ?>
                                <a href="?letra=<?php echo $letra; ?>"
                                   class="btn <?php echo ($letra == $letra_atual) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm me-2 mb-2"
                                   style="min-width: 45px;">
                                    <?php echo $letra; ?>
                                    <?php if ($contagem_por_letra[$letra] > 0): ?>
                                        <span class="badge bg-secondary ms-1"><?php echo $contagem_por_letra[$letra]; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lawyers List -->
            <?php if (count($advogados) > 0): ?>
            <div class="row g-4">
                <?php foreach ($advogados as $advogado): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0 lawyer-card wow slideInUp" data-wow-delay="0.1s">
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
                                        <i class="fas fa-id-card text-primary me-1"></i>Registo: <?php echo htmlspecialchars($advogado->numero_registo); ?>
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row text-sm">
                                    <div class="col-12 mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->regiao); ?>
                                            <?php if ($advogado->localidade): ?>
                                                - <?php echo htmlspecialchars($advogado->localidade); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if ($advogado->telefone): ?>
                                    <div class="col-12 mb-2">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->telefone); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($advogado->email): ?>
                                    <div class="col-12 mb-2">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            <?php echo htmlspecialchars($advogado->email); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <span style="font-family: 'Open Sans';">
                                            Inscrito em <?php echo format_date($advogado->data_inscricao); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mb-3">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Em Situação Regular
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <?php if ($advogado->telefone): ?>
                                <div class="col-6">
                                    <a href="tel:<?php echo $advogado->telefone; ?>" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-phone me-1"></i>Ligar
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if ($advogado->email): ?>
                                <div class="col-6">
                                    <a href="mailto:<?php echo $advogado->email; ?>" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Info -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-light rounded p-4 text-center">
                        <p class="mb-2" style="font-family: 'Open Sans';">
                            <strong>Mostrando <?php echo count($advogados); ?> advogado(s) da letra <?php echo $letra_atual; ?></strong>
                        </p>
                        <p class="mb-0 text-muted" style="font-family: 'Open Sans';">
                            Use a navegação alfabética acima para ver advogados de outras letras ou
                            <a href="pesquisa-advogados.php">faça uma pesquisa específica</a>
                        </p>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- No Results -->
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="bg-light rounded p-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Nenhum advogado encontrado
                            </h4>
                            <p class="text-muted mb-4" style="font-family: 'Open Sans';">
                                Não há advogados inscritos cujo nome comece com a letra <strong><?php echo $letra_atual; ?></strong>.
                            </p>
                            <div>
                                <a href="?letra=A" class="btn btn-primary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>Voltar ao A
                                </a>
                                <a href="pesquisa-advogados.php" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-2"></i>Pesquisa Avançada
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Alphabet Navigation & List End -->

    <!-- Statistics Section -->
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo $total_advogados; ?></h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Advogados Ativos</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marked-alt text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';">8</h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Regiões Cobertas</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-balance-scale text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';">100%</h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Em Situação Regular</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="bg-white rounded p-4 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-award text-white"></i>
                        </div>
                        <h3 class="text-primary mb-1" style="font-family: 'Libre Baskerville';"><?php echo date('Y') - 1990; ?></h3>
                        <p class="mb-0" style="font-family: 'Open Sans';">Anos de Tradição</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


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
</body>
</html>