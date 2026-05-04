<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$letra_atual = isset($_GET['letra']) ? strtoupper(substr($_GET['letra'], 0, 1)) : 'A';
if (!preg_match('/^[A-Z]$/', $letra_atual)) { $letra_atual = 'A'; }

// Fetch lawyers for selected letter
$stmt = $pdo->prepare("SELECT numero_registo, nome_completo, regiao, localidade, telefone, email, data_inscricao, foto 
                       FROM advogados 
                       WHERE status = 'ativo' AND nome_completo LIKE ? 
                       ORDER BY nome_completo ASC");
$stmt->execute([$letra_atual . '%']);
$advogados = $stmt->fetchAll();

// Counts per letter
$alfabeto = range('A', 'Z');
$contagem = [];
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM advogados WHERE status = 'ativo' AND nome_completo LIKE ?");
foreach ($alfabeto as $l) {
    $stmt_count->execute([$l . '%']);
    $contagem[$l] = $stmt_count->fetchColumn();
}

$total_geral = array_sum($contagem);

$page_title = "Advogados Inscritos em Vigor";
$header_image = 'uploads/lady-justice-holding-scales-sword.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        html, body { overflow-x: hidden !important; width: 100%; margin: 0; padding: 0; }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        
        /* === SUBPAGE BREADCRUMB BAR === */
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        /* Mobile specific breadcrumbs overlaid on bottom of header */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { 
                background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; 
                z-index: 1045 !important; pointer-events: auto !important; 
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { 
                font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            }
            .mobile-breadcrumb-bar .bc-active { font-weight: 500; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { 
                border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; 
            }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }


        /* Alphabet Nav */
        .alphabet-nav { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; margin-top: 30px; }
        .alpha-btn { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; color: #444; font-weight: 700; transition: .3s; text-decoration: none !important; position: relative; }
        .alpha-btn:hover, .alpha-btn.active { background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); transform: translateY(-3px); }
        .alpha-count { position: absolute; top: -8px; right: -8px; background: var(--primary-gold); color: #fff; font-size: 0.6rem; padding: 2px 6px; border-radius: 50px; border: 2px solid #fff; font-weight: 800; }

        /* Lawyer Cards */
        .lawyer-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0ece4; transition: .3s; height: 100%; }
        .lawyer-card:hover { transform: translateY(-5px); box-shadow: 0 15px 45px rgba(177, 162, 118, 0.12); }
        .lawyer-header { padding: 30px; display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #f9f6f0; }
        .lawyer-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #f0ece4; background: #eee; }
        .lawyer-initials { width: 80px; height: 80px; border-radius: 50%; background: var(--primary-maroon); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-family: 'Libre Baskerville'; font-weight: 700; border: 3px solid #f0ece4; }
        .lawyer-name { font-family: 'Libre Baskerville', serif; font-size: 1.1rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 4px; }
        .lawyer-reg { font-size: 0.75rem; color: var(--primary-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        
        .lawyer-body { padding: 30px; }
        .lawyer-item { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; font-size: 0.9rem; color: #555; }
        .lawyer-item i { color: var(--primary-gold); width: 16px; text-align: center; }
        .lawyer-footer { padding: 20px 30px; background: #fdfbf7; border-top: 1px solid #f9f6f0; display: flex; gap: 10px; }
        .btn-lawyer { flex: 1; border-radius: 50px; font-size: 0.8rem; font-weight: 700; padding: 10px; transition: .3s; }
        
        .btn-lawyer-call {
            color: var(--primary-gold);
            border: 1px solid var(--primary-gold);
            background: transparent;
        }
        .btn-lawyer-call:hover {
            background: var(--primary-gold);
            color: #fff;
        }
        
        .btn-lawyer-email {
            background: var(--primary-maroon);
            color: #fff;
            border: 1px solid var(--primary-maroon);
        }
        .btn-lawyer-email:hover {
            background: #3a1519;
            color: #fff;
            border-color: #3a1519;
        }
        
        .results-summary { background: #fff; border-radius: 50px; padding: 15px 30px; display: inline-flex; align-items: center; gap: 15px; border: 1px solid #f0ece4; margin-bottom: 40px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .summary-dot { width: 8px; height: 8px; background: var(--primary-gold); border-radius: 50%; }

        .search-cta { background: var(--primary-maroon); border-radius: 20px; padding: 40px; color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 30px; margin-top: 60px; }
        .btn-gold { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 700; border: none; transition: .3s; }
        .btn-gold:hover { background: #fff; color: var(--primary-maroon); transform: translateY(-3px); }
        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }
    </style>
</head>

<body>
<div style="overflow-x: hidden; width: 100%; position: relative;">

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="pesquisa-advogados.php">Advogados</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Inscritos em Vigor</span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => 'Advogados', 'url' => 'pesquisa-advogados.php'],
        ['label' => 'Inscritos', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            
            <div class="text-center">
                <span class="section-label" style="font-size:0.7rem; letter-spacing:4px; text-transform:uppercase; font-weight:700; color:var(--primary-gold); display:block; margin-bottom:12px;">Quadro Geral</span>
                <h2 class="section-heading" style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.3rem; margin-bottom:40px;">Lista de Advogados Inscritos</h2>

                <div class="alphabet-nav shadow-sm">
                    <?php foreach ($alfabeto as $l): ?>
                        <a href="?letra=<?php echo $l; ?>" class="alpha-btn <?php echo ($l == $letra_atual) ? 'active' : ''; ?>">
                            <?php echo $l; ?>
                            <?php if ($contagem[$l] > 0): ?>
                                <span class="alpha-count"><?php echo $contagem[$l]; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="results-summary">
                    <div class="summary-dot"></div>
                    <span style="font-weight:700; color:var(--primary-maroon); font-size:0.9rem;">
                        Letra <?php echo $letra_atual; ?>: <?php echo count($advogados); ?> advogados encontrados
                    </span>
                    <span class="text-muted" style="font-size:0.8rem; margin-left:10px;">(Total em vigor: <?php echo $total_geral; ?>)</span>
                </div>
            </div>

            <div class="row g-4">
                <?php if (count($advogados) > 0): foreach ($advogados as $adv): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="lawyer-card shadow-sm">
                            <div class="lawyer-header">
                                <?php if ($adv->foto): ?>
                                    <img src="uploads/advogados/<?php echo $adv->foto; ?>" class="lawyer-avatar" alt="<?php echo $adv->nome_completo; ?>">
                                <?php else: ?>
                                    <div class="lawyer-initials">
                                        <?php 
                                            $names = explode(' ', $adv->nome_completo);
                                            echo substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : '');
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="lawyer-reg">Cédula nº <?php echo $adv->numero_registo; ?></div>
                                    <h4 class="lawyer-name"><?php echo htmlspecialchars($adv->nome_completo); ?></h4>
                                </div>
                            </div>
                            <div class="lawyer-body">
                                <div class="lawyer-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($adv->regiao . ($adv->localidade ? ' - ' . $adv->localidade : '')); ?></span>
                                </div>
                                <?php if ($adv->telefone): ?>
                                    <div class="lawyer-item">
                                        <i class="fas fa-phone-alt"></i>
                                        <span><?php echo htmlspecialchars($adv->telefone); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($adv->email): ?>
                                    <div class="lawyer-item">
                                        <i class="fas fa-envelope"></i>
                                        <a href="mailto:<?php echo $adv->email; ?>" class="text-decoration-none color-inherit"><?php echo htmlspecialchars($adv->email); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="lawyer-footer">
                                <a href="tel:<?php echo $adv->telefone; ?>" class="btn btn-lawyer-call btn-lawyer"><i class="fas fa-phone me-2"></i> Ligar</a>
                                <a href="mailto:<?php echo $adv->email; ?>" class="btn btn-lawyer-email btn-lawyer"><i class="fas fa-paper-plane me-2"></i> Email</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="p-5 bg-white rounded-4 border shadow-sm">
                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                            <h4 class="fw-bold" style="color: var(--primary-maroon);">Sem resultados</h4>
                            <p class="text-muted">Não foram encontrados advogados ativos cujo nome comece pela letra <?php echo $letra_atual; ?>.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="search-cta wow fadeInUp">
                <div>
                    <h5 class="fw-bold mb-2 text-white">Procura um advogado específico?</h5>
                    <p class="mb-0 opacity-75">Utilize a nossa pesquisa avançada para filtrar por região, nome ou número de cédula.</p>
                </div>
                <a href="pesquisa-advogados.php" class="btn btn-gold flex-shrink-0">Pesquisa Avançada <i class="fas fa-search ms-2"></i></a>
            </div>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
</div>
</body>
</html>