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
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0 50px;
            margin-bottom: 50px;
        }
        
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #c18046;
            transform: translateX(-50%);
        }
        
        .timeline-item {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 30px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #c18046;
            border: 4px solid white;
            transform: translateX(-50%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .timeline-content {
            width: 45%;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .timeline-item:nth-child(odd) .timeline-content {
            margin-left: auto;
        }
        
        .timeline-year {
            font-size: 1.5rem;
            font-weight: bold;
            color: #c18046;
            margin-bottom: 10px;
        }
        
        .stats-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stats-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stats-number {
            font-size: 3rem;
            font-weight: bold;
            color: #c18046;
            margin-bottom: 10px;
        }
        
        .stats-label {
            font-size: 1.1rem;
            color: #666;
        }
        
        .values-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            border-left: 5px solid #c18046;
            transition: all 0.3s ease;
        }
        
        .values-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateX(10px);
        }
        
        .values-card h4 {
            color: #c18046;
            margin-bottom: 15px;
        }
        
        .section-divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #c18046, transparent);
            margin: 50px 0;
        }
        
        .content-section {
            margin-bottom: 50px;
        }
        
        .content-section h3 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .content-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #c18046;
        }
        
        @media (max-width: 768px) {
            .timeline::before {
                left: 30px;
            }
            
            .timeline-item::before {
                left: 30px;
            }
            
            .timeline-content {
                width: calc(100% - 60px);
                margin-left: 60px !important;
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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692" target="_blank"><i class="fab fa-facebook-f fw-normal"></i></a>
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
                    <h1 class="display-4 text-white animated zoomIn"><?php echo htmlspecialchars($pagina->titulo); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white">Ordem</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Apresentação e História</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

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
</body>
</html>