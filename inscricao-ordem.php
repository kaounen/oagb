<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$success_message = '';
$error_message   = '';
$new_inscricao_id = 0;
$show_payment     = false;

// ── Load payment config ───────────────────────────────────────────────────────
try {
    $pay_cfg = $pdo->query("SELECT chave, valor FROM finan_config WHERE chave IN (
        'global_payments_enabled','stripe_public_key',
        'joia_inscricao_advogado','joia_inscricao_estagiario','pagamento_moeda'
    )")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) { $pay_cfg = []; }

$payments_enabled = ($pay_cfg['global_payments_enabled'] ?? '0') === '1';
$stripe_pk        = $pay_cfg['stripe_public_key'] ?? '';
$joia_adv         = (int)($pay_cfg['joia_inscricao_advogado'] ?? 50000);
$joia_est         = (int)($pay_cfg['joia_inscricao_estagiario'] ?? 25000);
$moeda_display    = $pay_cfg['pagamento_moeda'] ?? 'CFA';

// ── Captcha ───────────────────────────────────────────────────────────────────
if (!isset($_SESSION['captcha_a_ins'])) {
    $_SESSION['captcha_a_ins']   = rand(1, 9);
    $_SESSION['captcha_b_ins']   = rand(1, 9);
    $_SESSION['captcha_sum_ins'] = $_SESSION['captcha_a_ins'] + $_SESSION['captcha_b_ins'];
}

// ── Form Submission ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_inscricao         = clean_input($_POST['tipo_inscricao']);
    $nome_completo          = clean_input($_POST['nome_completo']);
    $genero                 = clean_input($_POST['genero']);
    $data_nascimento        = clean_input($_POST['data_nascimento']);
    $nacionalidade          = clean_input($_POST['nacionalidade']);
    $bi_passaporte          = clean_input($_POST['bi_passaporte']);
    $regiao                 = clean_input($_POST['regiao']);
    $localidade             = clean_input($_POST['localidade']);
    $morada                 = clean_input($_POST['morada']);
    $telefone               = clean_input($_POST['telefone']);
    $email                  = clean_input($_POST['email']);
    $formacao_academica     = clean_input($_POST['formacao_academica']);
    $experiencia_profissional = clean_input($_POST['experiencia_profissional']);
    $captcha_res            = intval($_POST['captcha_res'] ?? 0);

    $errors = [];
    if (empty($nome_completo)) $errors[] = 'Nome completo é obrigatório.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($captcha_res !== $_SESSION['captcha_sum_ins']) $errors[] = 'Resposta do Captcha incorreta.';

    // File Uploads
    $uploaded_docs = [];
    $upload_dir    = 'uploads/inscricoes';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    foreach (['ficheiro_academica', 'ficheiro_experiencia', 'ficheiro_foto', 'ficheiro_criminal', 'ficheiro_patrono'] as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
                $new_name = $field . '_' . uniqid() . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES[$field]['tmp_name'], "$upload_dir/$new_name"))
                    $uploaded_docs[$field] = $new_name;
            }
        }
    }

    if (empty($errors)) {
        try {
            $docs_json = json_encode($uploaded_docs);
            $stmt = $pdo->prepare(
                'INSERT INTO inscricoes_ordem
                (tipo_inscricao, nome_completo, genero, data_nascimento, nacionalidade, bi_passaporte,
                 regiao, localidade, morada, telefone, email, formacao_academica, experiencia_profissional,
                 documentos_anexos, pagamento_status)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                $tipo_inscricao, $nome_completo, $genero, $data_nascimento, $nacionalidade,
                $bi_passaporte, $regiao, $localidade, $morada, $telefone, $email,
                $formacao_academica, $experiencia_profissional, $docs_json, 'nao_pago'
            ]);
            $new_inscricao_id = (int)$pdo->lastInsertId();

            // Renew captcha
            $_SESSION['captcha_a_ins']   = rand(1, 9);
            $_SESSION['captcha_b_ins']   = rand(1, 9);
            $_SESSION['captcha_sum_ins'] = $_SESSION['captcha_a_ins'] + $_SESSION['captcha_b_ins'];

            // Decide next step
            if ($payments_enabled && !str_contains($stripe_pk, 'REPLACE')) {
                $show_payment = true; // show payment step
                $joia_val     = $tipo_inscricao === 'estagiario' ? $joia_est : $joia_adv;
            } else {
                $success_message = 'Inscrição submetida com sucesso! A nossa equipa irá analisar a sua candidatura.';
                $_POST = [];
            }
        } catch (Exception $e) {
            $error_message = 'Erro ao submeter inscrição: ' . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
} else {
    $_SESSION['captcha_a_ins']   = rand(1, 9);
    $_SESSION['captcha_b_ins']   = rand(1, 9);
    $_SESSION['captcha_sum_ins'] = $_SESSION['captcha_a_ins'] + $_SESSION['captcha_b_ins'];
}

$page_title = "Inscrição na Ordem";
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

        .form-card { background: #fff; border-radius: 20px; padding: 40px; border: 1px solid #f0ece4; box-shadow: 0 15px 45px rgba(0,0,0,0.03); position: relative; z-index: 20; }
        @media (max-width: 576px) { .form-card { padding: 25px 20px; } }

        .form-section-title { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.1rem; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f9f6f0; display: flex; align-items: center; gap: 12px; }
        .form-section-title i { color: var(--primary-gold); font-size: 1rem; }
        
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); margin-bottom: 10px; }
        .form-control, .form-select { border-radius: 12px; border: 1px solid #eee; padding: 12px 18px; font-size: 0.96rem; transition: .3s; background: #fbfbfb; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); background: #fff; box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .type-selector { display: flex; gap: 20px; margin-bottom: 40px; }
        .type-option { flex: 1; border: 2px solid #f0ece4; border-radius: 15px; padding: 20px; cursor: pointer; transition: .3s; position: relative; }
        .type-option:hover { border-color: var(--primary-gold); background: #fdfbf7; }
        .type-option.active { border-color: var(--primary-maroon); background: #fdfbf7; box-shadow: 0 10px 25px rgba(77, 28, 33, 0.05); }
        .type-option input { position: absolute; opacity: 0; }
        .type-icon { width: 45px; height: 45px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; transition: .3s; color: #999; }
        .type-option.active .type-icon { background: var(--primary-maroon); color: #fff; }
        .type-title { font-weight: 700; color: var(--primary-maroon); margin-bottom: 5px; }
        .type-desc { font-size: 0.75rem; color: #888; line-height: 1.4; }

        .btn-submit { background: var(--primary-maroon); color: #fff; border-radius: 50px; height: 55px; font-weight: 700; border: none; transition: .3s; padding: 0 40px; font-size: 1rem; width: 100%; }
        .btn-submit:hover { background: var(--primary-gold); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .info-box { background: var(--primary-maroon); color: #fff; border-radius: 20px; padding: 30px; position: relative; z-index: 30; height: auto; overflow: hidden; }
        .info-box::after { content: '\f24e'; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; bottom: -20px; right: -10px; font-size: 8rem; opacity: 0.05; pointer-events: none; }

        .req-list { list-style: none; padding: 0; margin: 0; }
        .req-list li { display: flex; gap: 12px; margin-bottom: 20px; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .req-list li:last-child { border: none; }
        .req-list i { color: var(--primary-gold); margin-top: 4px; }
        
        .admission-steps { background: rgba(255,255,255,0.05); border-radius: 15px; padding: 20px; margin-top: 30px; border: 1px solid rgba(255,255,255,0.1); }
        .admission-steps h5 { font-family: 'Libre Baskerville', serif; font-size: 1rem; color: #fff; margin-bottom: 15px; }
        .step-mini { display: flex; gap: 12px; margin-bottom: 15px; }
        .step-mini:last-child { margin-bottom: 0; }
        .step-mini-num { width: 22px; height: 22px; background: var(--primary-gold); border-radius: 50%; color: #fff; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; }
        .step-mini-text { font-size: 0.8rem; color: rgba(255,255,255,0.8); line-height: 1.4; }

        .sidebar-card { background: #fff; border: 1px solid #f0ece4; border-radius: 20px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .sidebar-title { font-family: 'Libre Baskerville', serif; font-size: 1.1rem; color: var(--primary-maroon); margin-bottom: 20px; font-weight: 700; }
        
        .captcha-box { display: flex; align-items: center; gap: 15px; background: #fdfbf7; padding: 15px; border-radius: 12px; border: 1px solid #f0ece4; margin-top: 5px; }
        .captcha-q { font-weight: 700; color: var(--primary-maroon); font-size: 1.1rem; }
        .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav li { margin-bottom: 12px; }
        .sidebar-nav a { color: #555; text-decoration: none; font-size: 0.85rem; transition: .3s; display: flex; align-items: center; gap: 8px; }
        .sidebar-nav a:hover { color: var(--primary-gold); transform: translateX(5px); }
        .sidebar-nav a i { font-size: 0.7rem; color: var(--primary-gold); opacity: 0.7; }

        .newsletter-sidebar { background: #fdfbf7; border-radius: 15px; padding: 20px; border: 1px solid #f0ece4; }
        .newsletter-input { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 10px 15px; color: #333; font-size: 0.85rem; width: 100%; margin-bottom: 10px; outline: none; transition: .3s; }
        .newsletter-input:focus { border-color: var(--primary-gold); }
        .newsletter-btn { width: 100%; background: var(--primary-maroon); color: #fff; border: none; border-radius: 10px; padding: 10px; font-weight: 700; font-size: 0.85rem; transition: .3s; }
        .newsletter-btn:hover { background: var(--primary-gold); transform: translateY(-2px); }
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
                        <span class="bc-active">Inscrição na Ordem</span>
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
        ['label' => 'Inscrição', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-4">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="mb-5">
                            <h2 style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.3rem;">Formulário de Inscrição</h2>
                            <p class="text-muted small">Inicie o seu processo de inscrição profissional na Ordem dos Advogados da Guiné-Bissau.</p>
                        </div>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="inscricao-ordem.php" enctype="multipart/form-data">
                            <div class="type-selector">
                                <label class="type-option active" for="type_adv" onclick="$('input#type_adv').prop('checked', true); $('.type-option').removeClass('active'); $(this).addClass('active');">
                                    <input type="radio" name="tipo_inscricao" value="advogado" id="type_adv" checked>
                                    <div class="type-icon"><i class="fas fa-user-tie"></i></div>
                                    <div class="type-title">Advogado</div>
                                    <div class="type-desc">Para licenciados em Direito com cédula ativa.</div>
                                </label>
                                <label class="type-option" for="type_est" onclick="$('input#type_est').prop('checked', true); $('.type-option').removeClass('active'); $(this).addClass('active');">
                                    <input type="radio" name="tipo_inscricao" value="estagiario" id="type_est">
                                    <div class="type-icon"><i class="fas fa-user-graduate"></i></div>
                                    <div class="type-title">Estagiário</div>
                                    <div class="type-desc">Para licenciados iniciando estágio profissional.</div>
                                </label>
                            </div>

                            <div class="form-section-title"><i class="fas fa-user"></i> Dados Pessoais</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-12">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" name="nome_completo" class="form-control" required placeholder="Como consta no BI" value="<?php echo htmlspecialchars($_POST['nome_completo'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Género</label>
                                    <select name="genero" class="form-select" required>
                                        <option value="M" <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="F" <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data de Nascimento</label>
                                    <input type="date" name="data_nascimento" class="form-control" required value="<?php echo htmlspecialchars($_POST['data_nascimento'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nacionalidade</label>
                                    <input type="text" name="nacionalidade" class="form-control" required value="<?php echo htmlspecialchars($_POST['nacionalidade'] ?? 'Guineense'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">BI / Passaporte Nº</label>
                                    <input type="text" name="bi_passaporte" class="form-control" required placeholder="Número do documento" value="<?php echo htmlspecialchars($_POST['bi_passaporte'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> Localização e Contactos</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Região</label>
                                    <select name="regiao" class="form-select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($regioes_gb as $val => $label): ?>
                                            <option value="<?php echo $val; ?>" <?php echo (isset($_POST['regiao']) && $_POST['regiao'] == $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Localidade</label>
                                    <input type="text" name="localidade" class="form-control" placeholder="Bairro ou Setor" value="<?php echo htmlspecialchars($_POST['localidade'] ?? ''); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Morada Completa</label>
                                    <input type="text" name="morada" class="form-control" required placeholder="Rua, Bloco, Porta..." value="<?php echo htmlspecialchars($_POST['morada'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefone Prinicpal</label>
                                    <input type="tel" name="telefone" class="form-control" required placeholder="+245 ..." value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-mail Profissional</label>
                                    <input type="email" name="email" class="form-control" required placeholder="exemplo@email.gw" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-section-title"><i class="fas fa-graduation-cap"></i> Formação Académica</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-12">
                                    <label class="form-label">Detalhes da Formação</label>
                                    <textarea name="formacao_academica" class="form-control" rows="4" required placeholder="Indique a universidade, ano de conclusão e especializações..."><?php echo htmlspecialchars($_POST['formacao_academica'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Certificado de Habilitações (PDF/Imagem)</label>
                                    <input type="file" name="ficheiro_academica" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <span class="x-small text-muted"><i class="fas fa-info-circle me-1"></i> Certificado de Licenciatura em Direito.</span>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <label class="form-label">Experiência Profissional Preliminar</label>
                                    <textarea name="experiencia_profissional" class="form-control" rows="3" placeholder="Opcional: estágios, cargos anteriores..."><?php echo htmlspecialchars($_POST['experiencia_profissional'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Comprovativos de Experiência (Opcional)</label>
                                    <input type="file" name="ficheiro_experiencia" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <div class="form-section-title"><i class="fas fa-file-signature"></i> Documentos Adicionais Obrigatórios</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Fotografia Tipo Passe (A cores, fundo liso)</label>
                                    <input type="file" name="ficheiro_foto" class="form-control" accept="image/*" required>
                                    <span class="x-small text-muted"><i class="fas fa-info-circle me-1"></i> Fotografia digital original para emissão da cédula profissional.</span>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Registo Criminal do País de Nacionalidade</label>
                                    <input type="file" name="ficheiro_criminal" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <span class="x-small text-muted"><i class="fas fa-info-circle me-1"></i> Registo Criminal original atualizado.</span>
                                </div>
                                
                                <div class="col-md-6 mb-3" id="patrono_wrapper">
                                     <label class="form-label">Declaração de Patrono</label>
                                     <input type="file" name="ficheiro_patrono" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                     <span class="x-small text-muted"><i class="fas fa-info-circle me-1"></i> Assinada por Advogado Sénior. Recomendado para o estágio profissional.</span>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label class="form-label">Verificação de Segurança (Captcha)</label>
                                    <div class="captcha-box">
                                        <span class="captcha-q">Quanto é <?php echo $_SESSION['captcha_a_ins']; ?> + <?php echo $_SESSION['captcha_b_ins']; ?>?</span>

                                                        <input type="number" name="captcha_res" class="form-control" style="max-width: 120px;" required placeholder="Resultado">
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Option (shown only if payments enabled) -->
                            <?php if ($payments_enabled): ?>
                            <div class="mt-4 mb-2 p-4 rounded-3" style="background:#fdfbf7;border:2px solid #f0ece4;">
                                <div class="form-label mb-3" style="color:var(--primary-maroon);"><i class="fas fa-credit-card me-2" style="color:var(--primary-gold);"></i> Pagamento da Joia de Inscrição</div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="pay-option pay-option--now active" id="opt_pagar" onclick="selectPayOption('agora')">
                                            <div class="fw-bold" style="color:var(--primary-maroon);"><i class="fas fa-bolt me-1" style="color:var(--primary-gold);"></i> Pagar Online</div>
                                            <div class="small text-muted mt-1" id="joia_label">Advogado: <?php echo number_format($joia_adv,0,',','.'); ?> <?php echo $moeda_display; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="pay-option" id="opt_depois" onclick="selectPayOption('depois')">
                                            <div class="fw-bold text-muted"><i class="fas fa-clock me-1"></i> Pagar Mais Tarde</div>
                                            <div class="small text-muted mt-1">Regularize na sede da OAGB</div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="pay_option" id="pay_option_val" value="agora">
                            </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <button type="submit" class="btn-submit" id="btn_submeter">SUBMETER CANDIDATURA <i class="fas fa-check-double ms-2"></i></button>
                                <p class="text-center text-muted small mt-3">Ao submeter, declara que as informações são verdadeiras sob compromisso de honra.</p>
                            </div>
                        </form>

                        <!-- ══ PAYMENT STEP (shown after form submission) ══ -->
                        <?php if ($show_payment): ?>
                        <div id="payment_step" style="display:none; margin-top: 30px;">
                            <div class="text-center mb-4">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;background:linear-gradient(135deg,var(--primary-maroon),#8B1A1A);">
                                    <i class="fas fa-check text-white fa-lg"></i>
                                </div>
                                <h3 style="font-family:'Libre Baskerville',serif;color:var(--primary-maroon);font-size:1.2rem;">Inscrição Submetida!</h3>
                                <p class="text-muted small">Referência <strong>#<?php echo $new_inscricao_id; ?></strong> registada com sucesso. Selecione o método de pagamento preferido para regularizar a joia de inscrição.</p>
                            </div>

                            <!-- Payment Tabs Selection -->
                            <div class="row g-2 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="pay-method-tab active" id="tab_cartao" onclick="switchPayMethod('cartao')">
                                        <i class="fas fa-credit-card d-block mb-1"></i>
                                        <span class="small fw-bold">Cartão Bancário</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="pay-method-tab" id="tab_orange" onclick="switchPayMethod('orange_money')">
                                        <i class="fas fa-mobile-alt d-block mb-1" style="color: #FF6600;"></i>
                                        <span class="small fw-bold">Orange Money</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="pay-method-tab" id="tab_mtn" onclick="switchPayMethod('mtn_momo')">
                                        <i class="fas fa-wallet d-block mb-1" style="color: #FFCC00;"></i>
                                        <span class="small fw-bold">MTN MoMo</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="pay-method-tab" id="tab_sede" onclick="switchPayMethod('sede')">
                                        <i class="fas fa-building d-block mb-1"></i>
                                        <span class="small fw-bold">Pagar na Sede</span>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD PANEL -->
                            <div id="panel_cartao" class="pay-panel active">
                                <div class="p-4 rounded-3 bg-white border border-light shadow-sm">
                                    <div class="form-label mb-3"><i class="fas fa-lock me-2 text-warning"></i> Dados do Cartão de Crédito / Débito</div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="x-small text-muted fw-bold text-uppercase">Nome Impresso no Cartão</label>
                                            <input type="text" id="card_name" class="form-control" placeholder="EX: ANSELMO VARELA" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="x-small text-muted fw-bold text-uppercase">Número do Cartão</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-0 bg-light"><i class="fas fa-credit-card text-muted" id="card_brand_icon"></i></span>
                                                <input type="text" id="card_number" class="form-control border-0 bg-light" placeholder="4000 1234 5678 9010" maxlength="19" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="x-small text-muted fw-bold text-uppercase">Validade</label>
                                            <input type="text" id="card_expiry" class="form-control" placeholder="MM/AA" maxlength="5" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="x-small text-muted fw-bold text-uppercase">CVC / CVV</label>
                                            <input type="password" id="card_cvv" class="form-control" placeholder="123" maxlength="4" required>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-3 mb-3">
                                        <i class="fas fa-shield-alt text-success"></i>
                                        <span class="x-small text-muted">A sua transação é encriptada e processada através de canais bancários seguros.</span>
                                    </div>
                                    <button class="btn-submit" id="btn_pay_cartao" onclick="processLocalPayment('cartao')" style="height:50px; font-size:.9rem;">
                                        <i class="fas fa-lock me-2"></i> Pagar <?php echo number_format($joia_val??$joia_adv,0,',','.'); ?> <?php echo $moeda_display; ?>
                                    </button>
                                </div>
                            </div>

                            <!-- ORANGE MONEY PANEL -->
                            <div id="panel_orange_money" class="pay-panel">
                                <div class="p-4 rounded-3 bg-white border border-light shadow-sm">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge p-2 text-uppercase fw-bold text-white me-3" style="background: #FF6600;">Orange Money</div>
                                        <span class="small text-muted">Disponível para telemóveis Orange na Guiné-Bissau.</span>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Número de Telemóvel Orange</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-light fw-bold text-muted">+245</span>
                                            <input type="tel" id="orange_phone" class="form-control border-0 bg-light p-3" placeholder="95XXXXXXX" maxlength="9">
                                        </div>
                                        <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Digite os 9 algarismos do seu telemóvel Orange (ex: 955475889).</div>
                                    </div>
                                    <button class="btn-submit" id="btn_pay_orange" onclick="processLocalPayment('orange_money')" style="height:50px; font-size:.9rem; background: #FF6600;">
                                        <i class="fas fa-mobile-alt me-2"></i> Solicitar Pagamento Orange Money
                                    </button>
                                </div>
                            </div>

                            <!-- MTN MOMO PANEL -->
                            <div id="panel_mtn_momo" class="pay-panel">
                                <div class="p-4 rounded-3 bg-white border border-light shadow-sm">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge p-2 text-uppercase fw-bold text-dark me-3" style="background: #FFCC00;">MTN Mobile Money</div>
                                        <span class="small text-muted">Disponível para telemóveis MTN na Guiné-Bissau.</span>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Número de Telemóvel MTN</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-light fw-bold text-muted">+245</span>
                                            <input type="tel" id="mtn_phone" class="form-control border-0 bg-light p-3" placeholder="96XXXXXXX" maxlength="9">
                                        </div>
                                        <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Digite os 9 algarismos do seu telemóvel MTN (ex: 966475889).</div>
                                    </div>
                                    <button class="btn-submit" id="btn_pay_mtn" onclick="processLocalPayment('mtn_momo')" style="height:50px; font-size:.9rem; background: #002D62; color: #fff;">
                                        <i class="fas fa-wallet me-2"></i> Solicitar Pagamento MTN MoMo
                                    </button>
                                </div>
                            </div>

                            <!-- SEDE PANEL -->
                            <div id="panel_sede" class="pay-panel">
                                <div class="text-center p-4 rounded-3 bg-white border border-light shadow-sm">
                                    <i class="fas fa-university text-muted mb-3 d-block fa-3x"></i>
                                    <h5 class="fw-bold" style="color:var(--primary-maroon);">Pagamento Presencial ou por Depósito</h5>
                                    <p class="text-muted small mb-4">Pode efectuar o pagamento directamente na sede da Ordem dos Advogados (Bissau), ou realizar um depósito na conta oficial da OAGB e apresentar o respetivo talão para validação da sua inscrição.</p>
                                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-5 py-2">Concluir e Apresentar Comprovativo na Sede</a>
                                </div>
                            </div>

                            <!-- PROCESSING MODAL/STATE -->
                            <div id="payment_processing_state" style="display:none;" class="text-center p-5 rounded-3 bg-white border shadow-sm mt-3">
                                <div class="spinner-border text-warning mb-4" style="width: 3.5rem; height: 3.5rem;" role="status"></div>
                                <h4 class="fw-bold" id="processing_title">A processar transação...</h4>
                                <p class="text-muted small" id="processing_message">Por favor aguarde enquanto contactamos a instituição financeira.</p>
                            </div>

                            <!-- SUCCESS PANEL -->
                            <div id="pay_success" style="display:none;" class="text-center p-5 rounded-3 bg-white border shadow-sm mt-3">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4 bg-success-subtle text-success" style="width: 80px; height: 80px;">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <h3 class="fw-bold text-success">Pagamento Registado com Sucesso!</h3>
                                <p class="text-muted small">O seu pagamento da joia de inscrição foi validado com sucesso na nossa plataforma financeira.</p>
                                <div class="p-3 bg-light rounded-3 d-inline-block text-start mb-4 shadow-sm" style="min-width: 250px;">
                                    <div class="x-small text-muted">Método: <strong id="succ_method">Cartão Bancário</strong></div>
                                    <div class="x-small text-muted">ID Transação: <strong id="succ_ref">TX_123456</strong></div>
                                    <div class="x-small text-muted">Valor Pago: <strong id="succ_val">50.000 CFA</strong></div>
                                </div>
                                <div class="d-block">
                                    <a href="index.php" class="btn-submit d-inline-flex align-items-center justify-content-center shadow-lg" style="width:auto; padding:15px 40px; text-decoration: none;">Concluir e Ir para o Início <i class="fas fa-arrow-right ms-2"></i></a>
                                </div>
                            </div>

                            <!-- ERROR STATE -->
                            <div id="payment_error_state" style="display:none;" class="text-center p-4 rounded-3 bg-danger-subtle text-danger border border-danger-subtle mt-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <div class="fw-bold" id="error_title">Falha no Processamento</div>
                                <div class="small" id="error_message_text">Ocorreu um erro ao autorizar a transação. Verifique os dados e tente novamente.</div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div><div class="col-lg-4">
                    <!-- MAIN INFO CARD (MAROON) -->
                    <div class="info-box shadow-lg mb-4">
                        <h3 class="text-white mb-4" style="font-family:'Libre Baskerville', serif; font-weight:700; font-size:1.3rem;">Documentos Necessários</h3>
                        <p class="text-white-50 small mb-4">Após a submissão digital, deverá entregar presencialmente na sede os seguintes documentos:</p>
                        
                        <ul class="req-list">
                            <li>
                                <i class="fas fa-file-invoice"></i>
                                <div>Cópia autenticada do Diploma de Licenciatura em Direito.</div>
                            </li>
                            <li>
                                <i class="fas fa-id-card"></i>
                                <div>Certificado de Registo Criminal do país de nacionalidade.</div>
                            </li>
                            <li>
                                <i class="fas fa-camera"></i>
                                <div>Duas (2) fotografias originais a cores tipo passe.</div>
                            </li>
                            <li>
                                <i class="fas fa-briefcase"></i>
                                <div>Declaração de patrono (apenas para inscrições).</div>
                            </li>
                        </ul>

                        <div class="admission-steps">
                            <h5>Processo de Admissão</h5>
                            <div class="step-mini text-white">
                                <div class="step-mini-num">1</div>
                                <div class="step-mini-text"><strong>Validação:</strong> Os serviços administrativos verificam a conformidade.</div>
                            </div>
                            <div class="step-mini text-white">
                                <div class="step-mini-num">2</div>
                                <div class="step-mini-text"><strong>Deliberação:</strong> O Conselho Nacional avalia e aprova as novas inscrições.</div>
                            </div>
                            <div class="step-mini text-white">
                                <div class="step-mini-num">3</div>
                                <div class="step-mini-text"><strong>Juramento:</strong> Cerimónia oficial de prestação de compromisso.</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUPPORT CARD (LIGHT) -->
                    <div class="sidebar-card mb-4" style="background: #fdfbf7;">
                        <h5 class="sidebar-title">Apoio à Candidatura</h5>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-light rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="fas fa-phone-alt text-primary"></i></div>
                            <div>
                                <div class="small fw-bold text-muted">Telefone</div>
                                <div class="fw-bold" style="color:var(--primary-maroon);">+245 955 475 889</div>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">Atendimento de Segunda a Sexta, das 08h00 às 15h00.</p>
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
    <?php if ($payments_enabled): ?>
    <script>
    const INSCRICAO_ID      = <?php echo $new_inscricao_id ?: 0; ?>;
    const TIPO_INSCRICAO    = '<?php echo htmlspecialchars($tipo_inscricao ?? "advogado"); ?>';
    const JOIA_ADV          = <?php echo $joia_adv; ?>;
    const JOIA_EST          = <?php echo $joia_est; ?>;
    const MOEDA_DISPLAY     = '<?php echo htmlspecialchars($moeda_display); ?>';

    // ── Pre-form: Update joia label based on tipo selection (with jQuery/Label Click support) ──
    function updateJoiaLabel() {
        const selectedType = jQuery('input[name="tipo_inscricao"]:checked').val();
        const lbl = document.getElementById('joia_label');
        if (!lbl) return;
        const val = selectedType === 'estagiario' ? JOIA_EST : JOIA_ADV;
        lbl.textContent = (selectedType === 'estagiario' ? 'Estagiário: ' : 'Advogado: ') +
            val.toLocaleString('pt-PT') + ' ' + MOEDA_DISPLAY;
    }

    // Bind change event and click on option wrappers
    jQuery('input[name="tipo_inscricao"]').on('change', updateJoiaLabel);
    jQuery('.type-option').on('click', function() {
        setTimeout(updateJoiaLabel, 50); // slight delay to allow jQuery selection to register checked state
    });

    // Run once on load to ensure parity
    jQuery(document).ready(updateJoiaLabel);

    // Pre-form pay option selector
    function selectPayOption(opt) {
        document.getElementById('pay_option_val').value = opt;
        document.querySelectorAll('.pay-option').forEach(el => el.classList.remove('active'));
        document.getElementById(opt === 'agora' ? 'opt_pagar' : 'opt_depois').classList.add('active');
    }

    // ── Post-inscription: Local multi-method checkout ────────────────────────
    <?php if ($show_payment): ?>
    document.getElementById('payment_step').style.display = 'block';

    // Switch Payment Tabs
    function switchPayMethod(method) {
        document.querySelectorAll('.pay-method-tab').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.pay-panel').forEach(el => el.classList.remove('active'));
        
        const tab = document.getElementById('tab_' + (method === 'orange_money' ? 'orange' : method === 'mtn_momo' ? 'mtn' : method));
        const panel = document.getElementById('panel_' + method);
        
        if (tab) tab.classList.add('active');
        if (panel) panel.classList.add('active');

        // Reset state
        document.getElementById('payment_error_state').style.display = 'none';
    }

    // Card Input Auto-formatting & Brand Detection
    const cardInput = document.getElementById('card_number');
    const brandIcon = document.getElementById('card_brand_icon');
    
    if (cardInput) {
        cardInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            // Mask
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted;

            // Simple Brand Detection
            if (value.startsWith('4')) {
                brandIcon.className = 'fab fa-cc-visa text-primary';
            } else if (value.startsWith('5') || value.startsWith('2')) {
                brandIcon.className = 'fab fa-cc-mastercard text-warning';
            } else if (value.startsWith('3')) {
                brandIcon.className = 'fab fa-cc-amex text-info';
            } else {
                brandIcon.className = 'fas fa-credit-card text-muted';
            }
        });
    }

    const expiryInput = document.getElementById('card_expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else {
                e.target.value = value;
            }
        });
    }

    // Process Local Simulated Payments with Real Persistence
    function processLocalPayment(method) {
        const payload = {
            inscricao_id: INSCRICAO_ID,
            tipo_inscricao: TIPO_INSCRICAO,
            metodo: method
        };

        // Form Validation
        if (method === 'cartao') {
            const name = document.getElementById('card_name').value.trim();
            const num = document.getElementById('card_number').value.replace(/\s/g, '');
            const exp = document.getElementById('card_expiry').value.trim();
            const cvv = document.getElementById('card_cvv').value.trim();

            if (!name || num.length < 15 || exp.length < 5 || cvv.length < 3) {
                showPayError('Por favor preencha corretamente todos os dados do cartão.');
                return;
            }
            payload.cartao_numero = num;
        } else if (method === 'orange_money') {
            const phone = document.getElementById('orange_phone').value.trim();
            if (!phone.startsWith('95') || phone.length !== 9) {
                showPayError('Por favor, insira um número Orange válido na Guiné-Bissau (deve começar por 95 e ter 9 algarismos).');
                return;
            }
            payload.telefone = '+245' + phone;
        } else if (method === 'mtn_momo') {
            const phone = document.getElementById('mtn_phone').value.trim();
            if ((!phone.startsWith('96') && !phone.startsWith('97')) || phone.length !== 9) {
                showPayError('Por favor, insira um número MTN válido na Guiné-Bissau (deve começar por 96 ou 97 e ter 9 algarismos).');
                return;
            }
            payload.telefone = '+245' + phone;
        }

        // Hide Panels, Show Processing State
        document.querySelectorAll('.pay-panel').forEach(el => el.classList.remove('active'));
        document.querySelector('.row.g-2.mb-4').style.display = 'none'; // hide tabs
        const procState = document.getElementById('payment_processing_state');
        procState.style.display = 'block';

        const pTitle = document.getElementById('processing_title');
        const pMsg = document.getElementById('processing_message');

        // Simulation flow steps
        setTimeout(() => {
            pTitle.textContent = method === 'cartao' ? 'A validar cartão bancário...' : 'A inicializar ligação GSM...';
            pMsg.textContent = method === 'cartao' ? 'A encriptar comunicação segura SSL...' : 'A enviar pedido push de autorização...';

            setTimeout(() => {
                pTitle.textContent = method === 'cartao' ? 'A autorizar valor...' : 'A aguardar resposta PIN...';
                pMsg.textContent = method === 'cartao' ? 'A comunicar com a rede VISA/Mastercard...' : 'Por favor verifique o seu telemóvel e digite o seu PIN.';

                setTimeout(() => {
                    // Send to backend via AJAX for database synchronization
                    const params = new URLSearchParams();
                    for (const key in payload) {
                        params.append(key, payload[key]);
                    }

                    fetch('processar_pagamento_local.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: params.toString()
                    })
                    .then(r => r.json())
                    .then(data => {
                        procState.style.display = 'none';
                        if (data.success) {
                            document.getElementById('succ_method').textContent = data.metodo_label;
                            document.getElementById('succ_ref').textContent = data.referencia;
                            document.getElementById('succ_val').textContent = data.formatted;
                            document.getElementById('pay_success').style.display = 'block';
                        } else {
                            showPayError(data.error || 'Ocorreu um erro ao registar o pagamento.');
                            restorePanels(method);
                        }
                    })
                    .catch(() => {
                        procState.style.display = 'none';
                        showPayError('Erro de ligação ao servidor da Ordem.');
                        restorePanels(method);
                    });

                }, 2000);
            }, 2500);
        }, 1500);
    }

    function restorePanels(method) {
        document.querySelector('.row.g-2.mb-4').style.display = 'flex';
        switchPayMethod(method);
    }

    function showPayError(msg) {
        const err = document.getElementById('payment_error_state');
        document.getElementById('error_message_text').textContent = msg;
        err.style.display = 'block';
        err.scrollIntoView({ behavior: 'smooth' });
    }
    <?php endif; ?>
    </script>
    <?php endif; ?>

    <style>
    .pay-option { border: 2px solid #f0ece4; border-radius: 15px; padding: 16px 18px; cursor: pointer; transition: .3s; }
    .pay-option:hover { border-color: var(--primary-gold); background: #fdfbf7; }
    .pay-option.active, .pay-option--now.active { border-color: var(--primary-maroon); background: #fdfbf7; box-shadow: 0 6px 20px rgba(77,28,33,.08); }
    
    /* Premium Checkout Styling */
    .pay-method-tab {
        border: 2px solid #f0ece4;
        border-radius: 12px;
        padding: 12px 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
    }
    .pay-method-tab:hover {
        border-color: var(--primary-gold);
        background: #fdfbf7;
    }
    .pay-method-tab.active {
        border-color: var(--primary-maroon);
        background: #fdfbf7;
        box-shadow: 0 4px 15px rgba(139, 26, 26, 0.08);
    }
    .pay-panel {
        display: none;
    }
    .pay-panel.active {
        display: block;
        animation: fadeIn 0.4s ease-in-out;
    }
    .x-small {
        font-size: 0.75rem;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</div>
</body>
</html>
