<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$success = false;
$ticket_id = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = clean_input($_POST['nome_requerente'] ?? '');
    $email = clean_input($_POST['email_requerente'] ?? '');
    $telefone = clean_input($_POST['telefone_requerente'] ?? '');
    $pais = clean_input($_POST['pais_residencia'] ?? '');
    $categoria = clean_input($_POST['categoria_caso'] ?? '');
    $descricao = clean_input($_POST['descricao_caso'] ?? '');

    if (empty($nome) || empty($email) || empty($pais) || empty($categoria) || empty($descricao)) {
        $error = "Por favor, preencha todos os campos obrigatórios (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, introduza um endereço de email válido.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO helpdesk_pedidos (nome_requerente, email_requerente, telefone_requerente, pais_residencia, categoria_caso, descricao_caso) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $telefone, $pais, $categoria, $descricao]);
            $ticket_id = $pdo->lastInsertId();
            $success = true;
        } catch (Exception $e) {
            $error = "Ocorreu um erro ao registar o seu pedido. Por favor, tente novamente mais tarde.";
        }
    }
}

$page_title = "Help Desk da Diáspora";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';
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

        .intake-container { background: #fff; border-radius: 24px; padding: 40px; border: 1px solid #f0ece4; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 12px; border: 1px solid #eee; padding: 12px 18px; font-size: 0.9rem; transition: .3s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .btn-intake-submit { background: var(--primary-maroon); color: #fff; border-radius: 50px; font-weight: 700; border: none; padding: 14px 30px; font-size: 0.9rem; transition: .3s; width: 100%; }
        .btn-intake-submit:hover { background: var(--primary-gold); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(177, 162, 118, 0.3); }

        .service-feature { background: #fdfbf7; border: 1px solid #f2edd8; border-radius: 16px; padding: 25px; margin-bottom: 20px; }
        .service-feature-icon { width: 50px; height: 50px; background: rgba(77, 28, 33, 0.06); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--primary-maroon); margin-bottom: 15px; }
        .service-feature-title { font-family: 'Libre Baskerville', serif; font-size: 0.95rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 8px; }
        .service-feature-desc { font-size: 0.8rem; color: #666; line-height: 1.5; }

        .success-panel { text-align: center; padding: 40px 20px; }
        .success-icon { width: 80px; height: 80px; background: rgba(40, 167, 69, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #28a745; margin: 0 auto 20px; }
        .ticket-number { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.8rem; letter-spacing: 1px; margin: 15px 0; }
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
                        <a href="#">Serviços</a>
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
        ['label' => 'Serviços', 'url' => '#'],
        ['label' => $page_title, 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="text-center mb-5">
                <span class="section-label" style="font-size:0.7rem; letter-spacing:4px; text-transform:uppercase; font-weight:700; color:var(--primary-gold); display:block; margin-bottom:12px;">Acesso Internacional à Justiça</span>
                <h2 class="section-heading" style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.8rem; line-height: 1.3; margin-bottom:15px; border-left: 5px solid var(--primary-gold); padding-left: 20px; display: inline-block;">Legal Help Desk da Guiné-Bissau</h2>
                <p class="text-muted col-lg-8 mx-auto" style="font-size: 0.95rem;">Canal oficial da OAGB para apoiar guineenses na diáspora, investidores estrangeiros e organizações no encaminhamento seguro para advogados certificados.</p>
            </div>

            <div class="row g-5">
                <!-- Info Left Column -->
                <div class="col-lg-4">
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville'; font-size: 1.25rem;">Como Funciona?</h4>
                        <p class="text-muted small" style="line-height: 1.6;">O Legal Help Desk atua como uma ponte regulada entre o seu problema jurídico e a advocacia guineense, garantindo segurança e transparência.</p>
                    </div>

                    <div class="service-feature">
                        <div class="service-feature-icon"><i class="fas fa-file-signature"></i></div>
                        <h5 class="service-feature-title">1. Submissão Protegida</h5>
                        <p class="service-feature-desc">Descreva as suas necessidades de forma confidencial. Os seus dados são protegidos por rigorosos protocolos de confidencialidade.</p>
                    </div>

                    <div class="service-feature">
                        <div class="service-feature-icon"><i class="fas fa-user-shield"></i></div>
                        <h5 class="service-feature-title">2. Triagem Institucional</h5>
                        <p class="service-feature-desc">A secretaria da Ordem analisa o seu caso e seleciona profissionais qualificados na área jurídica correspondente.</p>
                    </div>

                    <div class="service-feature">
                        <div class="service-feature-icon"><i class="fas fa-handshake"></i></div>
                        <h5 class="service-feature-title">3. Contacto e Conclusão</h5>
                        <p class="service-feature-desc">O advogado indicado contacta-o para prosseguir com a assessoria presencial ou via videoconferência segura.</p>
                    </div>
                </div>

                <!-- Form Right Column -->
                <div class="col-lg-8">
                    <div class="intake-container">
                        <?php if ($success): ?>
                            <div class="success-panel">
                                <div class="success-icon"><i class="fas fa-check"></i></div>
                                <h3 class="fw-bold mb-3" style="color: var(--primary-maroon); font-family: 'Libre Baskerville'; font-size: 1.4rem;">Pedido Registado com Sucesso!</h3>
                                <p class="text-muted">Agradecemos o seu contacto. A Ordem dos Advogados irá efetuar a triagem do seu caso e atribuir um representante legal qualificado brevemente.</p>
                                
                                <div class="ticket-number">Nº DE REGISTO: #<?php echo $ticket_id; ?></div>
                                
                                <p class="small text-muted mb-4">Guarde este número de ticket para futuras referências e acompanhamento com a secretaria.</p>
                                <a href="helpdesk-diaspora.php" class="btn btn-outline-secondary rounded-pill px-4">Submeter Outro Pedido</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <h4 class="fw-bold mb-1" style="color: var(--primary-maroon); font-family: 'Libre Baskerville';"><i class="fas fa-envelope-open-text me-2" style="color: var(--primary-gold);"></i> Formular Requerimento</h4>
                                <p class="text-muted small mb-0">Por favor, descreva de forma clara os seus requisitos jurídicos abaixo.</p>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger rounded-4 py-3 mb-4" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="helpdesk-diaspora.php">
                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label">Nome Completo *</label>
                                        <input type="text" name="nome_requerente" class="form-control" required placeholder="Seu nome completo" value="<?php echo htmlspecialchars($_POST['nome_requerente'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label">Endereço de Email *</label>
                                        <input type="email" name="email_requerente" class="form-control" required placeholder="seuemail@provedor.com" value="<?php echo htmlspecialchars($_POST['email_requerente'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label">Telefone / WhatsApp</label>
                                        <input type="text" name="telefone_requerente" class="form-control" placeholder="+351 912 345 678" value="<?php echo htmlspecialchars($_POST['telefone_requerente'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label">País de Residência *</label>
                                        <input type="text" name="pais_residencia" class="form-control" required placeholder="Ex: Portugal, França, Reino Unido..." value="<?php echo htmlspecialchars($_POST['pais_residencia'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Natureza do Assunto *</label>
                                        <select name="categoria_caso" class="form-select" required>
                                            <option value="">-- Selecione uma Categoria --</option>
                                            <option value="investimento" <?php echo (isset($_POST['categoria_caso']) && $_POST['categoria_caso'] == 'investimento') ? 'selected' : ''; ?>>Constituição de Empresa e Investimento</option>
                                            <option value="terras" <?php echo (isset($_POST['categoria_caso']) && $_POST['categoria_caso'] == 'terras') ? 'selected' : ''; ?>>Propriedades, Litígios e Aquisição de Terras</option>
                                            <option value="familia" <?php echo (isset($_POST['categoria_caso']) && $_POST['categoria_caso'] == 'familia') ? 'selected' : ''; ?>>Assuntos de Família, Sucessão e Herança</option>
                                            <option value="certidao" <?php echo (isset($_POST['categoria_caso']) && $_POST['categoria_caso'] == 'certidao') ? 'selected' : ''; ?>>Autenticação de Documentos e Certidões</option>
                                            <option value="outros" <?php echo (isset($_POST['categoria_caso']) && $_POST['categoria_caso'] == 'outros') ? 'selected' : ''; ?>>Outros Assuntos Jurídicos Gerais</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Descrição Pormenorizada do Caso *</label>
                                        <textarea name="descricao_caso" class="form-control" rows="6" required placeholder="Forneça o máximo de detalhe sobre a sua situação jurídica..." style="resize: none;"><?php echo htmlspecialchars($_POST['descricao_caso'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn-intake-submit"><i class="fas fa-paper-plane me-2"></i> ENVIAR PEDIDO DE APOIO</button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
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
