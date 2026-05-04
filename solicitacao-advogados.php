<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$success_message = '';
$error_message = '';

// Captcha Logic - Generate if not exists
if (!isset($_SESSION['captcha_a']) || !isset($_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = rand(1, 9);
    $_SESSION['captcha_b'] = rand(1, 9);
    $_SESSION['captcha_sum'] = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_solicitante = clean_input($_POST['nome_solicitante']);
    $email = clean_input($_POST['email']);
    $telefone = clean_input($_POST['telefone']);
    $area_juridica = clean_input($_POST['area_juridica']);
    $regiao_preferencia = clean_input($_POST['regiao_preferencia']);
    $descricao_caso = clean_input($_POST['descricao_caso']);
    $urgencia = clean_input($_POST['urgencia'] ?? 'media');
    $captcha_res = intval($_POST['captcha_res'] ?? 0);
    
    $errors = [];
    if (empty($nome_solicitante) || strlen($nome_solicitante) < 2) $errors[] = "Nome deve ter pelo menos 2 caracteres.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (empty($telefone) || strlen($telefone) < 7) $errors[] = "Telefone inválido.";
    if (empty($area_juridica)) $errors[] = "Selecione uma área jurídica.";
    if (empty($regiao_preferencia)) $errors[] = "Selecione uma região de preferência.";
    if (empty($descricao_caso) || strlen($descricao_caso) < 20) $errors[] = "Descrição do caso deve ter pelo menos 20 caracteres.";
    if ($captcha_res !== $_SESSION['captcha_sum']) $errors[] = "Resposta do Captcha incorreta.";
    
    // Handle File Uploads
    $uploaded_files = [];
    if (!empty($_FILES['ficheiros']['name'][0])) {
        $files = $_FILES['ficheiros'];
        $upload_dir = 'uploads/solicitacoes';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        for ($i = 0; $i < count($files['name']); $i++) {
            $file_error = $files['error'][$i];
            if ($file_error === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $name = basename($files['name'][$i]);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                
                if (in_array($ext, $allowed)) {
                    $new_name = uniqid() . '_' . time() . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($tmp_name, "$upload_dir/$new_name")) {
                        $uploaded_files[] = $new_name;
                    }
                } else {
                    $errors[] = "Ficheiro '$name' tem um formato não permitido.";
                }
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $docs_json = !empty($uploaded_files) ? json_encode($uploaded_files) : null;
            
            $stmt = $pdo->prepare("INSERT INTO solicitacoes_advogados 
                (nome_solicitante, email, telefone, area_juridica, regiao_preferencia, descricao_caso, urgencia, documentos_anexos) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome_solicitante, $email, $telefone, $area_juridica, $regiao_preferencia, $descricao_caso, $urgencia, $docs_json]);
            
            $success_message = "Solicitação enviada com sucesso! A nossa equipa irá analisar o seu caso e entrar em contacto brevemente.";
            
            // Generate new captcha for security
            $_SESSION['captcha_a'] = rand(1, 9);
            $_SESSION['captcha_b'] = rand(1, 9);
            $_SESSION['captcha_sum'] = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
            
            $_POST = [];
        } catch (Exception $e) {
            $error_message = "Ocorreu um erro ao processar a sua solicitação. Por favor, tente novamente mais tarde.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
} else {
    // Generate captcha on initial load
    $_SESSION['captcha_a'] = rand(1, 9);
    $_SESSION['captcha_b'] = rand(1, 9);
    $_SESSION['captcha_sum'] = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
}

$page_title = "Solicitação de Advogados";
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

        .form-card { background: #fff; border-radius: 20px; padding: 40px; border: 1px solid #f0ece4; box-shadow: 0 15px 45px rgba(0,0,0,0.03); position: relative; z-index: 30; }
        @media (max-width: 576px) { .form-card { padding: 25px 20px; } }

        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); margin-bottom: 10px; }
        .form-control, .form-select { border-radius: 12px; border: 1px solid #eee; padding: 12px 18px; font-size: 0.96rem; transition: .3s; background: #fbfbfb; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); background: #fff; box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .btn-submit { background: var(--primary-maroon); color: #fff; border-radius: 50px; height: 55px; font-weight: 700; border: none; transition: .3s; padding: 0 40px; font-size: 1rem; width: 100%; }
        .btn-submit:hover { background: var(--primary-gold); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .info-box { background: var(--primary-maroon); color: #fff; border-radius: 20px; padding: 30px; height: 100%; position: relative; overflow: hidden; }
        .info-box::after { content: '\f24e'; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; bottom: -20px; right: -10px; font-size: 8rem; opacity: 0.05; }
        .step-item { display: flex; gap: 15px; margin-bottom: 25px; }
        .step-num { width: 32px; height: 32px; background: var(--primary-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0; }
        .step-title { font-weight: 700; font-size: 1rem; margin-bottom: 5px; font-family: 'Libre Baskerville'; }
        .step-desc { font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5; }

        .urgent-card { background: #fdfbf7; border: 1px solid #f0ece4; border-radius: 20px; padding: 30px; margin-top: 30px; }
        .urgent-title { color: var(--primary-maroon); font-weight: 700; font-family: 'Libre Baskerville', serif; font-size: 1.3rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .urgent-title i { color: #d9534f; }
        
        .alert { border-radius: 12px; font-size: 0.9rem; padding: 15px 20px; border: none; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        
        .captcha-box { display: flex; align-items: center; gap: 15px; background: #fdfbf7; padding: 15px; border-radius: 12px; border: 1px solid #f0ece4; margin-top: 5px; }
        .captcha-q { font-weight: 700; color: var(--primary-maroon); font-size: 1.1rem; }
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
                        <span class="bc-active">Solicitação de Advogado</span>
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
        ['label' => 'Solicitação', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-4">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="mb-4">
                            <h2 style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.3rem;">Formulário de Solicitação</h2>
                            <p class="text-muted small">Preencha os campos abaixo com os detalhes do seu caso para que possamos indicar o profissional mais adequado.</p>
                        </div>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="solicitacao-advogados.php" enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" name="nome_solicitante" class="form-control" required placeholder="Seu nome completo" value="<?php echo htmlspecialchars($_POST['nome_solicitante'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-mail de Contacto</label>
                                    <input type="email" name="email" class="form-control" required placeholder="exemplo@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefone / WhatsApp</label>
                                    <input type="tel" name="telefone" class="form-control" required placeholder="+245 ..." value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Área de Direito</label>
                                    <select name="area_juridica" class="form-select" required>
                                        <option value="">Selecione a área...</option>
                                        <?php foreach ($areas_juridicas as $val => $label): ?>
                                            <option value="<?php echo $val; ?>" <?php echo (isset($_POST['area_juridica']) && $_POST['area_juridica'] == $val) ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Região para Atendimento</label>
                                    <select name="regiao_preferencia" class="form-select" required>
                                        <option value="">Selecione a região...</option>
                                        <?php foreach ($regioes_gb as $val => $label): ?>
                                            <option value="<?php echo $val; ?>" <?php echo (isset($_POST['regiao_preferencia']) && $_POST['regiao_preferencia'] == $val) ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Urgência do Caso</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="urgencia" id="urg_baixa" value="baixa" <?php echo (!isset($_POST['urgencia']) || $_POST['urgencia'] == 'baixa') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="urg_baixa">Baixa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="urgencia" id="urg_media" value="media" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'media') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="urg_media">Média</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="urgencia" id="urg_alta" value="alta" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'alta') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="urg_alta">Alta</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Anexar Ficheiros (Multi-seleção)</label>
                                    <input type="file" name="ficheiros[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <small class="text-muted">Pode selecionar vários ficheiros (Imagens, PDF, DOC). Máximo 10MB por ficheiro.</small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Breve Descrição do Caso</label>
                                    <textarea name="descricao_caso" class="form-control" rows="5" required placeholder="Descreva os factos de forma clara para ajudar na nossa indicação..."><?php echo htmlspecialchars($_POST['descricao_caso'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Verificação de Segurança (Captcha)</label>
                                    <div class="captcha-box">
                                        <span class="captcha-q">Quanto é <?php echo $_SESSION['captcha_a']; ?> + <?php echo $_SESSION['captcha_b']; ?>?</span>
                                        <input type="number" name="captcha_res" class="form-control" style="max-width: 120px;" required placeholder="Resultado">
                                    </div>
                                </div>

                                <div class="col-md-12 text-end mt-4">
                                    <button type="submit" class="btn-submit">ENVIAR SOLICITAÇÃO <i class="fas fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-box-container" style="position:relative; z-index:20;">
                        <div class="info-box">
                            <h3 class="mb-4 text-white" style="font-family:'Libre Baskerville', serif; font-weight:700; font-size:1.3rem;">Como funciona?</h3>
                            
                            <div class="step-item">
                                <div class="step-num">1</div>
                                <div>
                                    <div class="step-title" style="font-size:1rem; font-family:'Libre Baskerville', serif;">Submissão</div>
                                    <div class="step-desc">Preencha o formulário detalhando o seu problema jurídico e localização.</div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">2</div>
                                <div>
                                    <div class="step-title">Análise</div>
                                    <div class="step-desc">A Comissão de Assistência Judiciária analisa os detalhes para encontrar o melhor especialista.</div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">3</div>
                                <div>
                                    <div class="step-title">Conciliação</div>
                                    <div class="step-desc">Entramos em contacto com a indicação do advogado e os próximos passos.</div>
                                </div>
                            </div>
                        </div>

                        <div class="urgent-card">
                            <div class="urgent-title"><i class="fas fa-exclamation-triangle"></i> Casos Urgentes</div>
                            <p class="text-muted small mb-4">Se necessita de assistência imediata, por favor contacte os nossos serviços centrais diretamente.</p>
                            
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-light rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-phone-alt text-primary"></i></div>
                                <div>
                                    <div class="small fw-bold">Telefone</div>
                                    <div style="color:var(--primary-maroon);">+245 955 475 889</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-envelope text-primary"></i></div>
                                <div>
                                    <div class="small fw-bold">E-mail</div>
                                    <div style="color:var(--primary-maroon);">assistencia@oagb.gw</div>
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
