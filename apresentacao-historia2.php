<?php
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Apresentação e História da OAGB";
$meta_title = "Apresentação e História - Ordem dos Advogados da Guiné-Bissau";
$meta_description = "Conheça a história, missão e visão da Ordem dos Advogados da Guiné-Bissau desde a sua criação em 1991.";
$meta_image = "img/logo3.png";
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

        <?php include 'includes/topbar.php'; ?>

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Apresentação da Ordem</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white">Ordem</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Apresentação</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

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

    <!-- About Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="section-title position-relative pb-3 mb-5" style="margin-top:0%;padding-top:0%;">
                        <h5 class="fw-bold text-primary text-uppercase">Apresentação da Ordem</h5>
                        <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:300%;">Criação e ideal da OAGB</h1>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="text-primary mb-3">Departamento de Advocacia Popular</h4>
                        <p class="mb-4 fl fonText6">Despacho do Comissário de Estado de Justiça, publicado no Boletim Oficial, n.º 11, de 18 de 1978, regulou pela primeira vez atividade da advocacia e da procuradoria em geral, exigindo para o exercício das mesmas à inscrição.</p>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-primary mb-3">Liberdade de Constituição de Associações</h4>
                        <p class="mb-4 fl fonText6">Art. 55° CRGB, e novo horizonte de liberdade associativa.</p>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-primary mb-3">Constituição da OAGB</h4>
                        <p class="mb-4 fl fonText6">Escritura pública de 8/08/1991, por um grupo de juristas, publicada no Boletim Oficial, n.º 52, de 28 de Dezembro de 1992.</p>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-primary mb-3">Natureza Jurídica</h4>
                        <p class="mb-4 fl fonText6">Pessoa coletiva de direito privado de utilidade pública dotada de personalidade jurídica e autonomia administrativa e financeira (Decreto n.º 13/94, de 7 de Abril) - promoção e defesa dos valores do Estado de Direito Democrático, direitos, liberdades e garantias dos cidadãos.</p>
                    </div>

                    <div class="d-flex align-items-center mb-4 wow fadeIn" data-wow-delay="0.6s">
                        <div class="bg-primary d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                            <i class="fa fa-phone-alt text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-2">Ligue para tirar qualquer dúvida</h5>
                            <h4 class="text-primary mb-0">+245 955 475 889</h4>
                        </div>
                    </div>
                    <a href="contacto.php" class="btn btn-primary py-3 px-5 mt-3 wow zoomIn" data-wow-delay="0.9s">Fale connosco</a>
                </div>
                <div class="col-lg-5" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100 rounded wow zoomIn" data-wow-delay="0.5s" src="img/law-scales-wooden-gavel.jpg" style="object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Mission Vision Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Valores e Princípios</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:280%;">Missão e Visão</h1>
            </div>
            <div class="row g-5">
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.3s">
                    <div class="bg-white rounded shadow p-5 h-100 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-eye text-white fa-2x"></i>
                        </div>
                        <h4 class="mb-3" style="color:#5B463F;">Visão</h4>
                        <p class="mb-0" style="color:#111923;">Ser uma instituição de referência na promoção da justiça, defesa do Estado de Direito e na formação de advogados íntegros e competentes na Guiné-Bissau.</p>
                    </div>
                </div>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-white rounded shadow p-5 h-100 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-bullseye text-white fa-2x"></i>
                        </div>
                        <h4 class="mb-3" style="color:#5B463F;">Missão</h4>
                        <p class="mb-0" style="color:#111923;">Promover e defender os valores do Estado de Direito Democrático, os direitos, liberdades e garantias dos cidadãos, regulamentando e fiscalizando o exercício da advocacia.</p>
                    </div>
                </div>
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.9s">
                    <div class="bg-white rounded shadow p-5 h-100 text-center">
                        <div class="bg-primary rounded-circle mx-auto mb-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-heart text-white fa-2x"></i>
                        </div>
                        <h4 class="mb-3" style="color:#5B463F;">Valores</h4>
                        <p class="mb-0" style="color:#111923;">Integridade, independência, competência profissional, ética, transparência e compromisso com a justiça e o bem comum.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mission Vision End -->

    <!-- Timeline Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Nossa História</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:280%;">Marcos Históricos</h1>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="timeline">
                        <div class="timeline-item mb-5">
                            <div class="row">
                                <div class="col-lg-6 order-2 order-lg-1">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="text-primary">1978</h4>
                                        <h5>Departamento de Advocacia Popular</h5>
                                        <p>Primeira regulamentação da atividade da advocacia através do Despacho do Comissário de Estado de Justiça, publicado no Boletim Oficial n.º 11.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 order-1 order-lg-2">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-gavel text-white fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-5">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-users text-white fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="text-primary">1991</h4>
                                        <h5>Constituição da OAGB</h5>
                                        <p>Escritura pública de 8 de agosto de 1991, por um grupo de juristas pioneiros, marcando o nascimento oficial da Ordem dos Advogados da Guiné-Bissau.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-5">
                            <div class="row">
                                <div class="col-lg-6 order-2 order-lg-1">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="text-primary">1992</h4>
                                        <h5>Publicação Oficial</h5>
                                        <p>Publicação da constituição da OAGB no Boletim Oficial n.º 52, de 28 de Dezembro, conferindo reconhecimento legal à instituição.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 order-1 order-lg-2">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-file-alt text-white fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="text-center">
                                        <div class="bg-primary rounded-circle mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-certificate text-white fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="bg-light rounded p-4">
                                        <h4 class="text-primary">1994</h4>
                                        <h5>Reconhecimento de Utilidade Pública</h5>
                                        <p>Decreto n.º 13/94, de 7 de Abril, confere à OAGB o estatuto de pessoa coletiva de direito privado de utilidade pública com autonomia administrativa e financeira.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Timeline End -->

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
