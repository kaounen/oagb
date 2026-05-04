<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$success_message = '';
$error_message = '';

// Captcha Logic - Generate if not exists
if (!isset($_SESSION['captcha_a_ins']) || !isset($_SESSION['captcha_b_ins'])) {
    $_SESSION['captcha_a_ins'] = rand(1, 9);
    $_SESSION['captcha_b_ins'] = rand(1, 9);
    $_SESSION['captcha_sum_ins'] = $_SESSION['captcha_a_ins'] + $_SESSION['captcha_b_ins'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_inscricao = clean_input($_POST['tipo_inscricao']);
    $nome_completo = clean_input($_POST['nome_completo']);
    $genero = clean_input($_POST['genero']);
    $data_nascimento = clean_input($_POST['data_nascimento']);
    $nacionalidade = clean_input($_POST['nacionalidade']);
    $bi_passaporte = clean_input($_POST['bi_passaporte']);
    $regiao = clean_input($_POST['regiao']);
    $localidade = clean_input($_POST['localidade']);
    $morada = clean_input($_POST['morada']);
    $telefone = clean_input($_POST['telefone']);
    $email = clean_input($_POST['email']);
    $formacao_academica = clean_input($_POST['formacao_academica']);
    $experiencia_profissional = clean_input($_POST['experiencia_profissional']);
    $captcha_res = intval($_POST['captcha_res'] ?? 0);
    
    $errors = [];
    if (empty($nome_completo)) $errors[] = "Nome completo é obrigatório.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if ($captcha_res !== $_SESSION['captcha_sum_ins']) $errors[] = "Resposta do Captcha incorreta.";
    
    // Process File Uploads
    $uploaded_docs = [];
    $upload_dir = 'uploads/inscricoes';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    foreach (['ficheiro_academica', 'ficheiro_experiencia'] as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $tmp_name = $_FILES[$field]['tmp_name'];
            $name = basename($_FILES[$field]['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
                $new_name = $field . '_' . uniqid() . '_' . time() . '.' . $ext;
                if (move_uploaded_file($tmp_name, "$upload_dir/$new_name")) {
                    $uploaded_docs[$field] = $new_name;
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $docs_json = json_encode($uploaded_docs);
            $stmt = $pdo->prepare("INSERT INTO inscricoes_ordem 
                (tipo_inscricao, nome_completo, genero, data_nascimento, nacionalidade, bi_passaporte, 
                 regiao, localidade, morada, telefone, email, formacao_academica, experiencia_profissional, documentos_anexos) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $tipo_inscricao, $nome_completo, $genero, $data_nascimento, $nacionalidade, 
                $bi_passaporte, $regiao, $localidade, $morada, $telefone, $email, 
                $formacao_academica, $experiencia_profissional, $docs_json
            ]);
            $success_message = "Inscrição submetida com sucesso! A nossa equipa irá analisar a sua candidatura.";
            
            // Generate new captcha for security
            $_SESSION['captcha_a_ins'] = rand(1, 9);
            $_SESSION['captcha_b_ins'] = rand(1, 9);
            $_SESSION['captcha_sum_ins'] = $_SESSION['captcha_a_ins'] + $_SESSION['captcha_b_ins'];
            
            $_POST = [];
        } catch (Exception $e) {
            $error_message = "Erro ao submeter inscrição. Por favor, tente novamente.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
} else {
    // Generate captcha on initial load
    $_SESSION['captcha_a_ins'] = rand(1, 9);
    $_SESSION['captcha_b_ins'] = rand(1, 9);
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
                                    <input type="file" name="ficheiro_academica" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-12 mt-4">
                                    <label class="form-label">Experiência Profissional Preliminar</label>
                                    <textarea name="experiencia_profissional" class="form-control" rows="3" placeholder="Opcional: estágios, cargos anteriores..."><?php echo htmlspecialchars($_POST['experiencia_profissional'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Comprovativos de Experiência (Opcional)</label>
                                    <input type="file" name="ficheiro_experiencia" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label class="form-label">Verificação de Segurança (Captcha)</label>
                                    <div class="captcha-box">
                                        <span class="captcha-q">Quanto é <?php echo $_SESSION['captcha_a_ins']; ?> + <?php echo $_SESSION['captcha_b_ins']; ?>?</span>
                                        <input type="number" name="captcha_res" class="form-control" style="max-width: 120px;" required placeholder="Resultado">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn-submit">SUBMETER CANDIDATURA <i class="fas fa-check-double ms-2"></i></button>
                                <p class="text-center text-muted small mt-3">Ao submeter, declara que as informações são verdadeiras sob compromisso de honra.</p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
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
</div>
</body>
</html>
