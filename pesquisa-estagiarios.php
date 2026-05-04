<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$resultados = [];
$filtros = [];
$pesquisou = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['q'])) {
    $pesquisou = true;
    $sql = "SELECT id, numero_registo, nome_completo, regiao, localidade, telefone, email, data_inicio_estagio, foto 
            FROM advogados_estagiarios 
            WHERE status = 'ativo'";
    $params = [];

    $nome = clean_input($_POST['nome'] ?? $_GET['nome'] ?? $_GET['q'] ?? '');
    if ($nome) {
        $sql .= " AND nome_completo LIKE ?";
        $params[] = "%$nome%";
        $filtros['nome'] = $nome;
    }

    $registo = clean_input($_POST['registo'] ?? $_GET['registo'] ?? '');
    if ($registo) {
        $sql .= " AND numero_registo LIKE ?";
        $params[] = "%$registo%";
        $filtros['registo'] = $registo;
    }

    $regiao = clean_input($_POST['regiao'] ?? $_GET['regiao'] ?? '');
    if ($regiao && $regiao !== '') {
        $sql .= " AND regiao = ?";
        $params[] = $regiao;
        $filtros['regiao'] = $regiao;
    }

    $sql .= " ORDER BY nome_completo ASC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll();
}

$page_title = "Pesquisa de Advogados Estagiários";
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


        .search-container { background: #fff; border-radius: 20px; padding: 35px 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); position: relative; margin-top: 0; margin-bottom: 40px; border: 1px solid rgba(0,0,0,0.02); }
        @media (max-width: 576px) { .search-container { padding: 25px 20px; } }

        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); margin-bottom: 10px; }
        .form-control, .form-select { border-radius: 12px; border: 1px solid #eee; padding: 12px 18px; font-size: 0.9rem; transition: .3s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .btn-search { background: var(--primary-maroon); color: #fff; border-radius: 12px; height: 50px; font-weight: 700; border: none; transition: .3s; width: 100%; margin-top: 31px; }
        .btn-search:hover { background: var(--primary-gold); transform: translateY(-2px); }

        .lawyer-card { background: #fff; border-radius: 24px; overflow: hidden; border: 1px solid #f0ece4; transition: .3s; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .lawyer-card:hover { transform: translateY(-5px); box-shadow: 0 15px 45px rgba(177, 162, 118, 0.12); }
        .lawyer-header { padding: 25px; display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #f9f6f0; }
        .lawyer-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #f0ece4; background: #eee; }
        .lawyer-initials { width: 60px; height: 60px; border-radius: 50%; background: var(--primary-gold); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-family: 'Libre Baskerville'; font-weight: 700; border: 2px solid #ccc; }
        .lawyer-name { font-family: 'Libre Baskerville', serif; font-size: 1rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 2px; }
        .lawyer-reg { font-size: 0.7rem; color: var(--primary-gold); font-weight: 700; text-transform: uppercase; }
        .lawyer-body { padding: 25px; min-height: 140px; }
        .lawyer-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.85rem; color: #666; }
        .lawyer-item i { color: var(--primary-gold); width: 14px; text-align: center; }
        .lawyer-footer { padding: 15px 25px; background: #fdfbf7; border-top: 1px solid #f9f6f0; display: flex; gap: 10px; }
        .btn-lawyer { flex: 1; border-radius: 50px; font-size: 0.75rem; font-weight: 700; padding: 8px; transition: .3s; text-decoration: none; text-align: center; }
        
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
        
        .badge-filter { background: #f8f9fa; border: 1px solid #eee; color: #666; padding: 6px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .badge-filter i { color: var(--primary-gold); font-size: 0.6rem; }
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
                        <a href="estagiarios-inscritos.php">Estagiários</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Pesquisa</span>
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
        ['label' => 'Estagiários', 'url' => 'estagiarios-inscritos.php'],
        ['label' => 'Pesquisa', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <section class="pb-5 pt-5" style="background: #f7f5f0;">
        <div class="container">
            
            <div class="search-container">
                <div class="mb-4">
                    <h5 class="fw-bold mb-1" style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">
                        <i class="fas fa-search me-2" style="color: var(--primary-gold);"></i> Encontrar Estagiário
                    </h5>
                    <p class="text-muted small mb-0">Preencha os campos abaixo para localizar um profissional em estágio.</p>
                </div>
                <form method="POST" action="pesquisa-estagiarios.php">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Nome do Estagiário</label>
                            <input type="text" name="nome" class="form-control" placeholder="Digite o nome..." value="<?php echo htmlspecialchars($filtros['nome'] ?? ''); ?>">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Registo Nº</label>
                            <input type="text" name="registo" class="form-control" placeholder="Nº de Registo" value="<?php echo htmlspecialchars($filtros['registo'] ?? ''); ?>">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Região</label>
                            <select name="regiao" class="form-select">
                                <option value="">Todas as Regiões</option>
                                <?php foreach ($regioes_gb as $val => $label): ?>
                                    <option value="<?php echo $val; ?>" <?php echo (isset($filtros['regiao']) && $filtros['regiao'] == $val) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn-search"><i class="fas fa-search me-2"></i> PESQUISAR</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if ($pesquisou): ?>
                <div class="mt-4 pt-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h3 class="fw-bold m-0" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; font-size: 1.3rem;">
                                <?php echo count($resultados); ?> Estagiários Encontrados
                            </h3>
                            <div class="mt-2">
                                <?php foreach($filtros as $key => $val): ?>
                                    <span class="badge-filter"><i class="fas fa-check"></i> <?php echo ucfirst($key); ?>: <?php echo htmlspecialchars($val); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="pesquisa-estagiarios.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Limpar Filtros</a>
                    </div>

                    <div class="row g-4">
                        <?php if (count($resultados) > 0): foreach ($resultados as $est): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="lawyer-card">
                                    <div class="lawyer-header">
                                        <?php if ($est->foto): ?>
                                            <img src="uploads/estagiarios/<?php echo $est->foto; ?>" class="lawyer-avatar" alt="<?php echo $est->nome_completo; ?>">
                                        <?php else: ?>
                                            <div class="lawyer-initials">
                                                <?php 
                                                    $names = explode(' ', $est->nome_completo);
                                                    echo substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : '');
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="lawyer-reg">Registo nº <?php echo $est->numero_registo; ?></div>
                                            <h4 class="lawyer-name"><?php echo htmlspecialchars($est->nome_completo); ?></h4>
                                        </div>
                                    </div>
                                    <div class="lawyer-body">
                                        <div class="lawyer-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($est->regiao . ($est->localidade ? ' - ' . $est->localidade : '')); ?></span>
                                        </div>
                                        <?php if($est->telefone): ?>
                                            <div class="lawyer-item">
                                                <i class="fas fa-phone-alt"></i>
                                                <span><?php echo htmlspecialchars($est->telefone); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($est->email): ?>
                                            <div class="lawyer-item">
                                                <i class="fas fa-envelope"></i>
                                                <span class="text-truncate" style="max-width: 100%;"><?php echo htmlspecialchars($est->email); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lawyer-footer">
                                        <a href="tel:<?php echo $est->telefone; ?>" class="btn btn-lawyer-call btn-lawyer"> Ligar</a>
                                        <a href="mailto:<?php echo $est->email; ?>" class="btn btn-lawyer-email btn-lawyer"> Email</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="col-12 text-center py-5">
                                <div class="p-5 bg-white rounded-4 border shadow-sm">
                                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                    <h4 class="fw-bold" style="color: var(--primary-maroon);">Nenhum resultado</h4>
                                    <p class="text-muted">Não foram encontrados estagiários com estes critérios.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Pre-search Content -->
                <div class="row mt-5 pt-4 g-lg-5 g-3 z-1 position-relative overflow-hidden">
                    <div class="col-lg-4 text-center px-4">
                        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(177, 162, 118, 0.1); transition: .3s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                            <i class="fas fa-user-graduate fs-2" style="color: var(--primary-gold);"></i>
                        </div>
                        <h5 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Futuros Profissionais</h5>
                        <p class="text-muted small m-0" style="line-height: 1.6;">Os advogados estagiários estão em fase de formação prática, sob orientação de patronos qualificados.</p>
                    </div>
                    <div class="col-lg-4 text-center px-4">
                        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(177, 162, 118, 0.1); transition: .3s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                            <i class="fas fa-map-marked-alt fs-2" style="color: var(--primary-gold);"></i>
                        </div>
                        <h5 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Filtro Regional</h5>
                        <p class="text-muted small m-0" style="line-height: 1.6;">Localize estagiários por região para facilitar a colaboração em processos locais.</p>
                    </div>
                    <div class="col-lg-4 text-center px-4">
                        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: rgba(177, 162, 118, 0.1); transition: .3s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                            <i class="fas fa-balance-scale fs-2" style="color: var(--primary-gold);"></i>
                        </div>
                        <h5 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Transparência OAGB</h5>
                        <p class="text-muted small m-0" style="line-height: 1.6;">Mantenha-se informado sobre quem são os novos ingressos na classe jurídica guineense.</p>
                    </div>
                </div>
            <?php endif; ?>
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
