<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$resultados = [];
$filtros = [];
$pesquisou = false;

// List of standard specialties
$especialidades_gb = [
    'Direito Civil' => 'Direito Civil',
    'Direito Comercial' => 'Direito Comercial',
    'Direito Laboral' => 'Direito Laboral',
    'Direito Penal' => 'Direito Penal / Criminal',
    'Direito Administrativo' => 'Direito Administrativo',
    'Direito Fiscal' => 'Direito Fiscal / Tributário',
    'Família e Menores' => 'Família e Menores',
    'Propriedade e Terras' => 'Propriedade e Terras',
    'Investimento Estrangeiro' => 'Investimento Estrangeiro',
    'Contratos Internacionais' => 'Contratos Internacionais',
    'Arbitragem e Mediação' => 'Arbitragem e Mediação',
    'Migração e Nacionalidade' => 'Migração e Nacionalidade',
    'Concursos Públicos' => 'Empresas e Concursos'
];

// List of standard languages
$linguas_gb = [
    'Português' => 'Português',
    'Francês' => 'Francês',
    'Inglês' => 'Inglês',
    'Crioulo' => 'Crioulo'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $pesquisou = true;
    
    // Core search query: select advocate details and verify active quota status dynamically
    $sql = "SELECT a.id, a.numero_registo, a.nome_completo, a.regiao, a.localidade, a.telefone, a.email, a.foto, 
                   a.especialidade, a.linguas, a.atendimento_online, a.atende_diaspora, a.biografia,
                   (SELECT COUNT(1) FROM finan_pagamentos fp 
                    WHERE fp.advogado_id = a.id 
                      AND fp.membro_tipo = 'advogado' 
                      AND fp.tipo_pagamento_id = 1 
                      AND fp.status = 'confirmado' 
                      AND fp.valid_until >= CURDATE()) as quotas_ok
            FROM advogados a
            WHERE a.status = 'ativo'";
            
    $params = [];

    $nome = clean_input($_REQUEST['nome'] ?? '');
    if ($nome) {
        $sql .= " AND a.nome_completo LIKE ?";
        $params[] = "%$nome%";
        $filtros['nome'] = $nome;
    }

    $especialidade = clean_input($_REQUEST['especialidade'] ?? '');
    if ($especialidade && $especialidade !== '') {
        $sql .= " AND a.especialidade = ?";
        $params[] = $especialidade;
        $filtros['especialidade'] = $especialidade;
    }

    $regiao = clean_input($_REQUEST['regiao'] ?? '');
    if ($regiao && $regiao !== '') {
        $sql .= " AND a.regiao = ?";
        $params[] = $regiao;
        $filtros['regiao'] = $regiao;
    }

    $lingua = clean_input($_REQUEST['lingua'] ?? '');
    if ($lingua && $lingua !== '') {
        $sql .= " AND a.linguas LIKE ?";
        $params[] = "%$lingua%";
        $filtros['língua'] = $lingua;
    }

    $online = isset($_REQUEST['online']) ? 1 : 0;
    if ($online) {
        $sql .= " AND a.atendimento_online = 1";
        $filtros['atendimento online'] = 'Sim';
    }

    $diaspora = isset($_REQUEST['diaspora']) ? 1 : 0;
    if ($diaspora) {
        $sql .= " AND a.atende_diaspora = 1";
        $filtros['disponível diáspora'] = 'Sim';
    }

    $sql .= " ORDER BY a.nome_completo ASC LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll();
}

$page_title = "Encontrar Advogado";
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
        }

        .filter-sidebar { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #eee; padding: 10px 15px; font-size: 0.85rem; transition: .3s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .btn-filter-submit { background: var(--primary-maroon); color: #fff; border-radius: 50px; font-weight: 700; border: none; padding: 12px; font-size: 0.85rem; transition: .3s; width: 100%; }
        .btn-filter-submit:hover { background: var(--primary-gold); transform: translateY(-2px); }
        
        .lawyer-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0ece4; transition: .3s; height: 100%; display: flex; flex-direction: column; }
        .lawyer-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(177, 162, 118, 0.1); }
        .lawyer-header { padding: 25px; display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #fcfaf6; }
        .lawyer-avatar { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 2px solid #f0ece4; background: #eee; }
        .lawyer-initials { width: 65px; height: 65px; border-radius: 50%; background: var(--primary-maroon); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-family: 'Libre Baskerville'; font-weight: 700; border: 2px solid #f0ece4; }
        .lawyer-name { font-family: 'Libre Baskerville', serif; font-size: 1.05rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 3px; }
        .lawyer-reg { font-size: 0.72rem; color: var(--primary-gold); font-weight: 700; text-transform: uppercase; }
        
        .lawyer-body { padding: 25px; flex-grow: 1; }
        .lawyer-spec-badge { background: rgba(77, 28, 33, 0.05); color: var(--primary-maroon); font-size: 0.72rem; font-weight: 700; padding: 4px 12px; border-radius: 50px; display: inline-block; margin-bottom: 12px; }
        .lawyer-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.82rem; color: #666; }
        .lawyer-item i { color: var(--primary-gold); width: 14px; text-align: center; }
        
        .badge-verified { background: rgba(40, 167, 69, 0.1); color: #28a745; font-size: 0.68rem; font-weight: 700; padding: 3px 8px; border-radius: 50px; display: inline-flex; align-items: center; gap: 4px; }
        .badge-pending { background: rgba(108, 117, 125, 0.1); color: #6c757d; font-size: 0.68rem; font-weight: 700; padding: 3px 8px; border-radius: 50px; display: inline-flex; align-items: center; gap: 4px; }

        .lawyer-footer { padding: 15px 25px; background: #fdfbf7; border-top: 1px solid #f9f6f0; display: flex; gap: 10px; }
        .btn-lawyer { flex: 1; border-radius: 50px; font-size: 0.75rem; font-weight: 700; padding: 8px; transition: .3s; text-decoration: none; text-align: center; }
        
        .btn-lawyer-profile { background: var(--primary-gold); color: #fff; border: 1px solid var(--primary-gold); }
        .btn-lawyer-profile:hover { background: #9e8e63; color: #fff; transform: translateY(-2px); }
        .btn-lawyer-contact { background: var(--primary-maroon); color: #fff; border: 1px solid var(--primary-maroon); }
        .btn-lawyer-contact:hover { background: #3a1519; color: #fff; transform: translateY(-2px); }

        .badge-filter { background: #f8f9fa; border: 1px solid #eee; color: #666; padding: 6px 15px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .badge-filter i { color: var(--primary-gold); font-size: 0.6rem; }
        
        .custom-switch { display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; margin-bottom: 12px; }
        .custom-switch input { display: none; }
        .switch-slider { width: 34px; height: 20px; background-color: #ddd; border-radius: 20px; position: relative; transition: .3s; }
        .switch-slider::before { content: ""; position: absolute; width: 14px; height: 14px; border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: .3s; }
        .custom-switch input:checked + .switch-slider { background-color: var(--primary-gold); }
        .custom-switch input:checked + .switch-slider::before { left: 17px; }
        .switch-label { font-size: 0.8rem; font-weight: 600; color: #444; }
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
                        <a href="#">Público</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo $page_title; ?></span>
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
        ['label' => 'Público', 'url' => '#'],
        ['label' => $page_title, 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="text-center mb-5">
                <span class="section-label" style="font-size:0.7rem; letter-spacing:4px; text-transform:uppercase; font-weight:700; color:var(--primary-gold); display:block; margin-bottom:12px;">Serviço ao Cidadão e Investidor</span>
                <h2 class="section-heading" style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.8rem; line-height: 1.3; margin-bottom:15px; border-left: 5px solid var(--primary-gold); padding-left: 20px; display: inline-block;">Encontrar Advogado por Especialidade</h2>
                <p class="text-muted col-lg-8 mx-auto" style="font-size: 0.95rem;">Pesquise advogados registados e ativos na Ordem dos Advogados da Guiné-Bissau. Filtre por área jurídica, região, línguas ou modalidade de consulta.</p>
            </div>

            <div class="row g-4">
                <!-- Filters Sidebar -->
                <div class="col-lg-4">
                    <div class="filter-sidebar sticky-top" style="top: 110px;">
                        <h5 class="fw-bold mb-4" style="color: var(--primary-maroon); font-family: 'Libre Baskerville'; font-size: 1.15rem; border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px;">
                            <i class="fas fa-sliders-h me-2"></i> Filtros de Pesquisa
                        </h5>
                        <form method="GET" action="encontrar-advogado.php">
                            <div class="mb-3">
                                <label class="form-label">Nome do Profissional</label>
                                <input type="text" name="nome" class="form-control" placeholder="Procurar por nome..." value="<?php echo htmlspecialchars($_GET['nome'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Área de Especialidade</label>
                                <select name="especialidade" class="form-select">
                                    <option value="">Todas as Áreas</option>
                                    <?php foreach ($especialidades_gb as $val => $label): ?>
                                        <option value="<?php echo $val; ?>" <?php echo (isset($_GET['especialidade']) && $_GET['especialidade'] == $val) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Região</label>
                                <select name="regiao" class="form-select">
                                    <option value="">Todas as Regiões</option>
                                    <?php foreach ($regioes_gb as $val => $label): ?>
                                        <option value="<?php echo $val; ?>" <?php echo (isset($_GET['regiao']) && $_GET['regiao'] == $val) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Idioma</label>
                                <select name="lingua" class="form-select">
                                    <option value="">Todos os Idiomas</option>
                                    <?php foreach ($linguas_gb as $val => $label): ?>
                                        <option value="<?php echo $val; ?>" <?php echo (isset($_GET['lingua']) && $_GET['lingua'] == $val) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="custom-switch">
                                    <input type="checkbox" name="online" value="1" <?php echo isset($_GET['online']) ? 'checked' : ''; ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label">Consulta Online / Videoconferência</span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="custom-switch">
                                    <input type="checkbox" name="diaspora" value="1" <?php echo isset($_GET['diaspora']) ? 'checked' : ''; ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label">Atendimento à Diáspora / Internacional</span>
                                </label>
                            </div>

                            <button type="submit" class="btn-filter-submit"><i class="fas fa-search me-2"></i> FILTRAR RESULTADOS</button>
                            
                            <?php if ($pesquisou && count($filtros) > 0): ?>
                                <a href="encontrar-advogado.php" class="btn btn-sm btn-outline-secondary w-100 mt-2 rounded-pill py-2" style="font-size: 0.8rem; font-weight: 600;">Limpar Filtros</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Results Area -->
                <div class="col-lg-8">
                    <?php if ($pesquisou): ?>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                            <div>
                                <h3 class="fw-bold m-0" style="color: var(--primary-maroon); font-family: 'Libre Baskerville', serif; font-size: 1.25rem;">
                                    <?php echo count($resultados); ?> Advogados Encontrados
                                </h3>
                                <div class="mt-2 flex-wrap gap-2 d-flex">
                                    <?php foreach($filtros as $key => $val): ?>
                                        <span class="badge-filter"><i class="fas fa-check"></i> <?php echo htmlspecialchars($key); ?>: <?php echo htmlspecialchars($val); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <?php if (count($resultados) > 0): foreach ($resultados as $adv): ?>
                                <div class="col-md-6 col-12">
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
                                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                    <span class="lawyer-reg">Cédula nº <?php echo $adv->numero_registo; ?></span>
                                                    <?php if ($adv->quotas_ok): ?>
                                                        <span class="badge-verified"><i class="fas fa-check-circle"></i> Regularizado</span>
                                                    <?php else: ?>
                                                        <span class="badge-pending"><i class="fas fa-history"></i> Inativo / Pendente</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 class="lawyer-name"><?php echo htmlspecialchars($adv->nome_completo); ?></h4>
                                            </div>
                                        </div>
                                        <div class="lawyer-body">
                                            <div class="lawyer-spec-badge">
                                                <i class="fas fa-balance-scale me-1"></i> 
                                                <?php echo htmlspecialchars($adv->especialidade ?: 'Advocacia Geral'); ?>
                                            </div>
                                            
                                            <div class="lawyer-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><?php echo htmlspecialchars($adv->regiao . ($adv->localidade ? ' - ' . $adv->localidade : '')); ?></span>
                                            </div>
                                            <div class="lawyer-item">
                                                <i class="fas fa-language"></i>
                                                <span>Idiomas: <?php echo htmlspecialchars($adv->linguas ?: 'Português'); ?></span>
                                            </div>
                                            
                                            <div class="mt-3 d-flex gap-2">
                                                <?php if ($adv->atendimento_online): ?>
                                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem; font-weight: 600;"><i class="fas fa-video text-success me-1"></i> Consulta Online</span>
                                                <?php endif; ?>
                                                <?php if ($adv->atende_diaspora): ?>
                                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem; font-weight: 600;"><i class="fas fa-globe text-primary me-1"></i> Diáspora</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="lawyer-footer">
                                            <a href="advogado-perfil.php?id=<?php echo $adv->id; ?>" class="btn-lawyer btn-lawyer-profile"><i class="far fa-user me-2"></i> Perfil</a>
                                            <a href="mailto:<?php echo $adv->email; ?>" class="btn-lawyer btn-lawyer-contact"><i class="far fa-envelope me-2"></i> Contactar</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <div class="col-12 text-center py-5">
                                    <div class="p-5 bg-white rounded-4 border shadow-sm">
                                        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                        <h4 class="fw-bold" style="color: var(--primary-maroon);">Sem resultados</h4>
                                        <p class="text-muted">Não foram encontrados advogados com os critérios selecionados. Tente alargar os filtros.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
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
