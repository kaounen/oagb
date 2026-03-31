<?php
// cooperacao-institucional.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Cooperação Institucional";
$breadcrumbs = [
    'Início' => 'index.php',
    'A Ordem' => '#',
    'Cooperação' => '#'
];

// Content (Hardcoded as a fallback if DB table doesn't exist yet)
$conteudo = "
    <div class='row g-4 mb-5'>
        <div class='col-lg-6'>
            <div class='p-4 bg-light rounded-4 h-100 border'>
                <h4 class='fw-bold mb-3' style='color:#091e3e; font-family: \"Libre Baskerville\";'>Parcerias Nacionais</h4>
                <p>A Ordem dos Advogados da Guiné-Bissau mantém uma estreita colaboração com as principais instituições do Estado e organizações da sociedade civil, visando o fortalecimento do Estado de Direito e a melhoria do acesso à justiça.</p>
                <ul class='list-unstyled'>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> Ministério da Justiça e dos Direitos Humanos</li>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> Conselho Superior da Magistratura</li>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> Universidades e Institutos Superiores</li>
                </ul>
            </div>
        </div>
        <div class='col-lg-6'>
            <div class='p-4 bg-light rounded-4 h-100 border'>
                <h4 class='fw-bold mb-3' style='color:#091e3e; font-family: \"Libre Baskerville\";'>Cooperação Internacional</h4>
                <p>OAGB é membro ativo de diversas organizações internacionais de advocacia, partilhando experiências e adotando as melhores práticas internacionais para o exercício da profissão.</p>
                <ul class='list-unstyled'>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> Ordens de Advogados da CEDEAO</li>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> União Internacional de Advogados (UIA)</li>
                    <li class='mb-2 text-primary fw-bold'><i class='fas fa-check-circle me-2'></i> União dos Advogados de Língua Portuguesa (UALP)</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class='bg-primary p-5 rounded-4 text-white text-center wow fadeInUp'>
        <h3 class='text-white fw-bold mb-4' style='font-family: \"Libre Baskerville\";'>Protocolos e Intercâmbio</h3>
        <p class='lead mb-4'>Estamos abertos a novas parcerias que contribuam para a excelência jurídica e o desenvolvimento profissional dos nossos membros.</p>
        <a href='contacto.php' class='btn btn-light btn-lg rounded-pill px-5 fw-bold'>Contactar Relações Externas</a>
    </div>
";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/common_head.php'; ?>
    <style>
        .page-content { line-height: 1.8; color: #444; }
        .sidebar-widget { background: #f8f9fa; border-radius: 20px; padding: 30px; border: 1px solid #eee; }
        .sidebar-link { display: flex; align-items: center; padding: 12px 20px; border-radius: 50px; background: #fff; margin-bottom: 10px; text-decoration: none; color: #444; font-weight: 600; border: 1px solid #eee; transition: all 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { background: #091e3e; color: #fff; border-color: #091e3e; transform: translateX(5px); }
        .sidebar-link i { margin-right: 15px; color: #B1A276; }
        .sidebar-link:hover i, .sidebar-link.active i { color: #fff; }
    </style>
</head>
<body class="bg-white">
    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>

    <!-- Global Unified Header -->
    <?php 
    $header_light = true; 
    include 'includes/header_global.php'; 
    ?>
    
    <!-- Standard Clean Header (Breadcrumbs) -->
    <?php 
    $has_header_image = false; 
    include 'includes/page-header.php'; 
    ?>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-8 page-content">
                <h2 class="display-6 fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: #091e3e; border-left: 5px solid #B1A276; padding-left: 20px;">
                    Cooperação Institucional
                </h2>
                <p class="lead mb-5">Promovemos o diálogo e a cooperação com entidades nacionais e internacionais para fortalecer o papel da advocacia na sociedade.</p>
                
                <?php echo $conteudo; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar-widget shadow-sm sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4" style="font-family: 'Libre Baskerville', serif; color: #0b1118; border-bottom: 2px solid #B1A276; padding-bottom: 10px; display: inline-block;">A Ordem</h5>
                    <div class="mt-3">
                        <a href="apresentacao-historia.php" class="sidebar-link"><i class="fas fa-history"></i> Apresentação e História</a>
                        <a href="bastonario-ordem.php" class="sidebar-link"><i class="fas fa-user-tie"></i> O Bastonário</a>
                        <a href="orgaos-sociais.php" class="sidebar-link"><i class="fas fa-sitemap"></i> Órgãos Sociais</a>
                        <a href="comissoes-especializadas.php" class="sidebar-link"><i class="fas fa-gavel"></i> Comissões Especializadas</a>
                        <a href="cooperacao-institucional.php" class="sidebar-link active"><i class="fas fa-handshake"></i> Cooperação Institucional</a>
                    </div>
                    
                    <div class="mt-5 p-4 bg-primary rounded-4 text-white text-center">
                        <i class="fas fa-info-circle fa-2x mb-3 text-gold"></i>
                        <h6>Documentos Oficiais</h6>
                        <p class="small opacity-75">Consulte os nossos estatutos e regulamentos na secção pública.</p>
                        <a href="estatutos.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Ver Estatutos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>
</html>
