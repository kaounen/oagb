<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$advogado_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$advogado_id) {
    header("Location: encontrar-advogado.php");
    exit;
}

// Fetch advocate details and check their quota standing dynamically
$stmt = $pdo->prepare("SELECT a.id, a.numero_registo, a.nome_completo, a.regiao, a.localidade, a.telefone, a.email, a.foto, 
                              a.especialidade, a.linguas, a.atendimento_online, a.atende_diaspora, a.biografia, a.data_inscricao,
                              (SELECT COUNT(1) FROM finan_pagamentos fp 
                               WHERE fp.advogado_id = a.id 
                                 AND fp.membro_tipo = 'advogado' 
                                 AND fp.tipo_pagamento_id = 1 
                                 AND fp.status = 'confirmado' 
                                 AND fp.valid_until >= CURDATE()) as quotas_ok
                       FROM advogados a
                       WHERE a.id = ? AND a.status = 'ativo'");
$stmt->execute([$advogado_id]);
$adv = $stmt->fetch();

if (!$adv) {
    header("Location: encontrar-advogado.php");
    exit;
}

$page_title = "Perfil - " . $adv->nome_completo;
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

        .profile-container { background: #fff; border-radius: 24px; border: 1px solid #f0ece4; overflow: hidden; box-shadow: 0 10px 45px rgba(0,0,0,0.02); }
        .profile-header-banner { height: 120px; background: linear-gradient(135deg, var(--primary-maroon), #270e10); }
        .profile-avatar-wrapper { margin-top: -80px; padding: 0 40px; display: flex; align-items: flex-end; gap: 25px; flex-wrap: wrap; }
        .profile-avatar { width: 160px; height: 160px; border-radius: 20px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.08); background: #eee; }
        .profile-initials { width: 160px; height: 160px; border-radius: 20px; background: var(--primary-maroon); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-family: 'Libre Baskerville'; font-weight: 700; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        .profile-title-block { margin-top: 15px; flex-grow: 1; }
        .profile-name { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.8rem; margin-bottom: 5px; }
        .profile-subtitle { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--primary-gold); letter-spacing: 1.5px; }

        .profile-body { padding: 40px; }
        .info-card { background: #fdfbf7; border: 1px solid #f2edd8; border-radius: 16px; padding: 25px; height: 100%; }
        .info-title { font-family: 'Libre Baskerville', serif; font-size: 1rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px; }
        .info-item { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; font-size: 0.9rem; color: #555; }
        .info-item i { color: var(--primary-gold); font-size: 1.1rem; width: 20px; text-align: center; }

        .verification-seal-box { background: rgba(40, 167, 69, 0.04); border: 2px dashed rgba(40, 167, 69, 0.2); border-radius: 16px; padding: 30px; text-align: center; }
        .verification-badge { background: #28a745; color: #fff; padding: 6px 15px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 15px; text-transform: uppercase; }
        .qr-placeholder { background: #fff; width: 140px; height: 140px; margin: 0 auto 15px; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0,0,0,0.03); position: relative; }
        
        .biography-text { font-size: 1rem; line-height: 1.8; color: #555; text-align: justify; }
        
        .btn-profile-action { border-radius: 50px; font-weight: 700; padding: 12px 30px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 10px; transition: .3s; border: none; text-decoration: none; }
        .btn-action-call { background: var(--primary-gold); color: #fff; }
        .btn-action-call:hover { background: #9e8e63; color: #fff; transform: translateY(-2px); }
        .btn-action-mail { background: var(--primary-maroon); color: #fff; }
        .btn-action-mail:hover { background: #3a1519; color: #fff; transform: translateY(-2px); }

        .spec-badge { background: rgba(77, 28, 33, 0.05); color: var(--primary-maroon); font-size: 0.8rem; font-weight: 700; padding: 6px 16px; border-radius: 50px; display: inline-block; margin-bottom: 20px; }
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
                        <a href="encontrar-advogado.php">Encontrar Advogado</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo htmlspecialchars($adv->nome_completo); ?></span>
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
        ['label' => 'Encontrar', 'url' => 'encontrar-advogado.php'],
        ['label' => 'Perfil', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="profile-container shadow-sm mb-5">
                <div class="profile-header-banner"></div>
                <div class="profile-avatar-wrapper">
                    <?php if ($adv->foto): ?>
                        <img src="uploads/advogados/<?php echo $adv->foto; ?>" class="profile-avatar" alt="<?php echo $adv->nome_completo; ?>">
                    <?php else: ?>
                        <div class="profile-initials">
                            <?php 
                                $names = explode(' ', $adv->nome_completo);
                                echo substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : '');
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="profile-title-block">
                        <span class="profile-subtitle">Cédula nº <?php echo $adv->numero_registo; ?></span>
                        <h1 class="profile-name"><?php echo htmlspecialchars($adv->nome_completo); ?></h1>
                        <span class="spec-badge"><i class="fas fa-balance-scale me-2"></i> <?php echo htmlspecialchars($adv->especialidade ?: 'Advocacia Geral'); ?></span>
                    </div>
                </div>

                <div class="profile-body">
                    <div class="row g-4">
                        <!-- Main Biography and Profile Details -->
                        <div class="col-lg-8">
                            <h3 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville'; font-size: 1.3rem; border-left: 4px solid var(--primary-gold); padding-left: 15px;">Sobre o Profissional</h3>
                            <div class="biography-text mb-5">
                                <?php if ($adv->biografia): ?>
                                    <?php echo nl2br(htmlspecialchars($adv->biografia)); ?>
                                <?php else: ?>
                                    <p class="text-muted italic">O profissional ainda não disponibilizou uma biografia pública. A sua inscrição e regularidade encontram-se plenamente registadas junto da Ordem dos Advogados da Guiné-Bissau.</p>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-3 flex-wrap">
                                <a href="tel:<?php echo $adv->telefone; ?>" class="btn-profile-action btn-action-call"><i class="fas fa-phone-alt"></i> Ligar para Escritório</a>
                                <a href="mailto:<?php echo $adv->email; ?>" class="btn-profile-action btn-action-mail"><i class="fas fa-paper-plane"></i> Enviar Mensagem</a>
                            </div>
                        </div>

                        <!-- Info Card Panel & Verification Seal -->
                        <div class="col-lg-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-card">
                                        <h4 class="info-title">Informações Gerais</h4>
                                        
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <div>
                                                <strong class="d-block" style="font-size: 0.75rem; text-transform: uppercase; color: #888;">Região de Atuação</strong>
                                                <span><?php echo htmlspecialchars($adv->regiao . ($adv->localidade ? ' - ' . $adv->localidade : '')); ?></span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <i class="fas fa-language"></i>
                                            <div>
                                                <strong class="d-block" style="font-size: 0.75rem; text-transform: uppercase; color: #888;">Idiomas</strong>
                                                <span><?php echo htmlspecialchars($adv->linguas ?: 'Português'); ?></span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <i class="fas fa-video"></i>
                                            <div>
                                                <strong class="d-block" style="font-size: 0.75rem; text-transform: uppercase; color: #888;">Consulta Online</strong>
                                                <span><?php echo $adv->atendimento_online ? 'Disponível por Videoconferência' : 'Apenas Presencial'; ?></span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <i class="fas fa-globe"></i>
                                            <div>
                                                <strong class="d-block" style="font-size: 0.75rem; text-transform: uppercase; color: #888;">Apoio à Diáspora</strong>
                                                <span><?php echo $adv->atende_diaspora ? 'Disponível para Clientes Internacionais' : 'Indisponível'; ?></span>
                                            </div>
                                        </div>

                                        <div class="info-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <div>
                                                <strong class="d-block" style="font-size: 0.75rem; text-transform: uppercase; color: #888;">Inscrição na Ordem</strong>
                                                <span><?php echo $adv->data_inscricao ? date('d/m/Y', strtotime($adv->data_inscricao)) : 'Confirmada'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="verification-seal-box">
                                        <?php if ($adv->quotas_ok): ?>
                                            <div class="verification-badge"><i class="fas fa-check-circle"></i> Inscrição Regular</div>
                                            <div class="qr-placeholder">
                                                <!-- Dynamic Verifiable QR code representation using a CSS gradient mock/google chart API for absolute launch fidelity -->
                                                <img src="https://chart.googleapis.com/chart?chs=120&cht=qr&chl=<?php echo urlencode(ROOT_URL . '/advogado-perfil.php?id=' . $adv->id); ?>&choe=UTF-8" style="width: 120px; height: 120px;" alt="QR Code de Verificação">
                                            </div>
                                            <h5 class="fw-bold mb-1" style="font-size: 0.95rem; color: #28a745;">Selo de Segurança OAGB</h5>
                                            <p class="text-muted m-0" style="font-size: 0.75rem; line-height: 1.4;">Este QR Code atesta em tempo real que o profissional está inscrito e habilitado legalmente para exercer a advocacia.</p>
                                        <?php else: ?>
                                            <div class="verification-badge bg-secondary"><i class="fas fa-exclamation-triangle"></i> Status Pendente</div>
                                            <div class="qr-placeholder opacity-50">
                                                <i class="fas fa-lock fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="fw-bold mb-1 text-secondary" style="font-size: 0.95rem;">Aguardando Regularização</h5>
                                            <p class="text-muted m-0" style="font-size: 0.75rem; line-height: 1.4;">Os dados cadastrais deste perfil estão ativos, mas as quotas ou licença estão em análise de atualização.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
