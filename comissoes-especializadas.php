<?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Buscar comissões da base de dados
try {
    $stmt = $pdo->prepare("
        SELECT * FROM comissoes 
        WHERE ativo = 1 
        ORDER BY ordem_exibicao ASC, nome ASC
    ");
    $stmt->execute();
    $comissoes = $stmt->fetchAll();
    
    // Se não houver comissões na BD, usar dados exemplo
    if (empty($comissoes)) {
        $comissoes = [
            (object)[
                'nome' => 'Comissão de Direitos Humanos',
                'descricao' => 'Responsável pela defesa e promoção dos direitos humanos, acompanhamento de casos de violação e elaboração de pareceres.',
                'presidente' => 'Dr. João Silva',
                'membros' => 'Dra. Maria Santos, Dr. Pedro Costa, Dra. Ana Gomes',
                'area_atuacao' => 'Direitos Humanos'
            ],
            (object)[
                'nome' => 'Comissão de Formação e Estágio',
                'descricao' => 'Coordena os programas de formação contínua, estágios profissionais e avaliação de candidatos.',
                'presidente' => 'Dra. Isabel Mendes',
                'membros' => 'Dr. Carlos Fernandes, Dra. Sofia Rodrigues, Dr. Manuel Pereira',
                'area_atuacao' => 'Formação'
            ],
            (object)[
                'nome' => 'Comissão de Ética e Deontologia',
                'descricao' => 'Zela pelo cumprimento do código de ética profissional e emite pareceres sobre questões deontológicas.',
                'presidente' => 'Dr. António Martins',
                'membros' => 'Dra. Teresa Oliveira, Dr. Francisco Sousa, Dra. Catarina Lima',
                'area_atuacao' => 'Ética'
            ],
            (object)[
                'nome' => 'Comissão de Legislação',
                'descricao' => 'Analisa propostas legislativas, elabora pareceres técnicos e propõe alterações legislativas.',
                'presidente' => 'Dr. Ricardo Alves',
                'membros' => 'Dra. Beatriz Ferreira, Dr. Luís Cardoso, Dra. Helena Pinto',
                'area_atuacao' => 'Legislação'
            ],
            (object)[
                'nome' => 'Comissão de Apoio Judiciário',
                'descricao' => 'Coordena o sistema de apoio judiciário, garantindo o acesso à justiça aos cidadãos carenciados.',
                'presidente' => 'Dra. Marta Ribeiro',
                'membros' => 'Dr. José Tavares, Dra. Sandra Correia, Dr. Paulo Nunes',
                'area_atuacao' => 'Apoio Judiciário'
            ]
        ];
    }
    
} catch (Exception $e) {
    error_log("Erro ao buscar comissões: " . $e->getMessage());
    $comissoes = [];
}

$page_title = "Comissões Especializadas";
$meta_description = "Conheça as Comissões Especializadas da Ordem dos Advogados da Guiné-Bissau";
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
        .commission-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .commission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .commission-header {
            background: linear-gradient(135deg, #c18046 0%, #a5684e 100%);
            color: white;
            padding: 30px;
            position: relative;
        }
        
        .commission-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        
        .commission-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .commission-body {
            padding: 40px 30px 30px;
        }
        
        .commission-name {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .commission-area {
            display: inline-block;
            padding: 5px 15px;
            background: rgba(255,255,255,0.3);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .commission-description {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .commission-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .commission-info h5 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .commission-info p {
            margin-bottom: 5px;
            color: #666;
        }
        
        .member-list {
            list-style: none;
            padding: 0;
        }
        
        .member-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .member-list li:last-child {
            border-bottom: none;
        }
        
        .member-list li::before {
            content: '▸';
            color: #c18046;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .intro-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 50px;
        }
        
        .intro-section h2 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-bottom: 20px;
        }
        
        .stats-section {
            margin: 50px 0;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #c18046;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        .contact-cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 50px;
        }
        
        .contact-cta h3 {
            color: white;
            font-family: 'Libre Baskerville', serif;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .commission-header {
                padding: 20px;
            }
            
            .commission-body {
                padding: 30px 20px 20px;
            }
            
            .intro-section {
                padding: 30px 20px;
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
                    <h1 class="display-4 text-white animated zoomIn">Comissões Especializadas</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white">Ordem</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Comissões Especializadas</li>
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
            <div class="intro-section">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2>Trabalho Especializado para Melhor Servir</h2>
                        <p class="lead">
                            As Comissões Especializadas da OAGB são órgãos técnicos consultivos que desenvolvem 
                            trabalho especializado em diversas áreas do Direito, contribuindo para a excelência 
                            da advocacia e para o desenvolvimento jurídico na Guiné-Bissau.
                        </p>
                        <p>
                            Cada comissão é composta por advogados com reconhecida competência e experiência 
                            nas respetivas áreas, trabalhando de forma voluntária para o benefício da classe 
                            e da sociedade.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($comissoes); ?></div>
                            <div class="stat-label">Comissões Ativas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Comissões -->
            <div class="row">
                <?php foreach ($comissoes as $comissao): ?>
                <div class="col-lg-6 mb-4">
                    <div class="commission-card">
                        <div class="commission-header">
                            <div class="commission-icon">
                                <?php
                                // Ícone baseado na área
                                $icon = 'fa-gavel';
                                if (stripos($comissao->area_atuacao, 'humanos') !== false) $icon = 'fa-hand-holding-heart';
                                elseif (stripos($comissao->area_atuacao, 'formação') !== false) $icon = 'fa-graduation-cap';
                                elseif (stripos($comissao->area_atuacao, 'ética') !== false) $icon = 'fa-balance-scale';
                                elseif (stripos($comissao->area_atuacao, 'legislação') !== false) $icon = 'fa-book';
                                elseif (stripos($comissao->area_atuacao, 'apoio') !== false) $icon = 'fa-hands-helping';
                                ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <h4 class="commission-name"><?php echo htmlspecialchars($comissao->nome); ?></h4>
                            <?php if (!empty($comissao->area_atuacao)): ?>
                            <span class="commission-area"><?php echo htmlspecialchars($comissao->area_atuacao); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="commission-body">
                            <p class="commission-description">
                                <?php echo htmlspecialchars($comissao->descricao); ?>
                            </p>
                            
                            <?php if (!empty($comissao->presidente)): ?>
                            <div class="commission-info">
                                <h5><i class="fas fa-user-tie me-2"></i>Presidência</h5>
                                <p><strong><?php echo htmlspecialchars($comissao->presidente); ?></strong></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($comissao->membros)): ?>
                            <div class="commission-info">
                                <h5><i class="fas fa-users me-2"></i>Membros</h5>
                                <ul class="member-list">
                                    <?php 
                                    $membros = explode(',', $comissao->membros);
                                    foreach ($membros as $membro):
                                    ?>
                                    <li><?php echo htmlspecialchars(trim($membro)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Estatísticas -->
            <div class="stats-section">
                <h3 class="text-center mb-5" style="font-family: 'Libre Baskerville', serif; color: #4D1C21;">
                    Impacto do Nosso Trabalho
                </h3>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number" data-toggle="counter-up">150+</div>
                            <div class="stat-label">Pareceres Emitidos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number" data-toggle="counter-up">50+</div>
                            <div class="stat-label">Formações Realizadas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number" data-toggle="counter-up">200+</div>
                            <div class="stat-label">Casos Analisados</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number" data-toggle="counter-up">30+</div>
                            <div class="stat-label">Membros Ativos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="contact-cta">
                <h3>Quer Fazer Parte de uma Comissão?</h3>
                <p class="mb-4">
                    Se é advogado inscrito na OAGB e tem interesse em contribuir com o seu conhecimento 
                    e experiência numa das nossas comissões, entre em contacto connosco.
                </p>
                <a href="contacto.php" class="btn btn-light btn-lg">
                    <i class="fas fa-envelope me-2"></i>Contactar-nos
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
