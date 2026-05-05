<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

$success_message = '';
$error_message = '';

// Captcha Logic - Generate se não existe
if (!isset($_SESSION['captcha_a']) || !isset($_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = rand(1, 9);
    $_SESSION['captcha_b'] = rand(1, 9);
    $_SESSION['captcha_sum'] = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = clean_input($_POST['nome']);
    $email = clean_input($_POST['email']);
    $telefone = clean_input($_POST['telefone']);
    $assunto = clean_input($_POST['assunto']);
    $mensagem = clean_input($_POST['mensagem']);
    $captcha_res = intval($_POST['captcha_res'] ?? 0);
    
    $errors = [];
    if (empty($nome) || strlen($nome) < 2) $errors[] = "Nome deve ter pelo menos 2 caracteres.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (empty($assunto)) $errors[] = "O assunto é obrigatório.";
    if (empty($mensagem) || strlen($mensagem) < 10) $errors[] = "A mensagem deve ter pelo menos 10 caracteres.";
    if ($captcha_res !== $_SESSION['captcha_sum']) $errors[] = "Resposta do Captcha incorreta.";
    
    // Processamento de Ficheiros Anexos
    $uploaded_files = [];
    if (!empty($_FILES['ficheiros']['name'][0])) {
        $files = $_FILES['ficheiros'];
        $upload_dir = 'uploads/contactos';
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
            // Aqui pode adicionar a inserção na base de dados se criar a tabela correspondente.
            // Por enquanto simulamos o envio ou pode ser enviado via e-mail.
            
            // $to = 'info@oagb.gw';
            // $subject_mail = "Novo Contacto Site: " . $assunto;
            // $body = "Nome: $nome\nEmail: $email\nTelefone: $telefone\nMensagem:\n$mensagem";
            // if (!empty($uploaded_files)) {
            //     $body .= "\nFicheiros anexados: " . count($uploaded_files);
            // }
            // send_email($to, $subject_mail, nl2br($body));
            
            $success_message = "Mensagem enviada com sucesso! A nossa equipa irá responder o mais breve possível.";
            
            // Generate new captcha for security
            $_SESSION['captcha_a'] = rand(1, 9);
            $_SESSION['captcha_b'] = rand(1, 9);
            $_SESSION['captcha_sum'] = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
            
            $_POST = [];
        } catch (Exception $e) {
            $error_message = "Ocorreu um erro ao processar a sua mensagem. Por favor, tente novamente mais tarde.";
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

$page_title = "Contacto";
$header_image = 'uploads/justice-velho.jpg'; // Mesma imagem para consistência ou mude se necessário
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
        .info-box::after { content: '\f095'; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; bottom: -20px; right: -10px; font-size: 8rem; opacity: 0.05; }
        .step-item { display: flex; gap: 15px; margin-bottom: 25px; }
        .step-num { width: 32px; height: 32px; background: var(--primary-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0; }
        .step-title { font-weight: 700; font-size: 1rem; margin-bottom: 5px; font-family: 'Libre Baskerville'; }
        .step-desc { font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5; }

        .urgent-card { background: #fdfbf7; border: 1px solid #f0ece4; border-radius: 20px; padding: 30px; margin-top: 30px; }
        .urgent-title { color: var(--primary-maroon); font-weight: 700; font-family: 'Libre Baskerville', serif; font-size: 1.3rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        
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
                        <span class="bc-active">Contacto</span>
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
        ['label' => 'Contacto', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>


    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-4">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="mb-4">
                            <h2 style="font-family:'Libre Baskerville', serif; color:var(--primary-maroon); font-weight:700; font-size:1.3rem;">Fale Connosco</h2>
                            <p class="text-muted small">Preencha o formulário abaixo para enviar uma mensagem. Entraremos em contacto o mais rápido possível.</p>
                        </div>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="contacto.php" enctype="multipart/form-data">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" name="nome" class="form-control" required placeholder="Seu nome completo" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-mail de Contacto</label>
                                    <input type="email" name="email" class="form-control" required placeholder="exemplo@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefone / WhatsApp</label>
                                    <input type="tel" name="telefone" class="form-control" placeholder="+245 ..." value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Assunto</label>
                                    <input type="text" name="assunto" class="form-control" required placeholder="Motivo do seu contacto" value="<?php echo htmlspecialchars($_POST['assunto'] ?? ''); ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Mensagem</label>
                                    <textarea name="mensagem" class="form-control" rows="5" required placeholder="Escreva a sua mensagem..."><?php echo htmlspecialchars($_POST['mensagem'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Anexar Ficheiros (Multi-seleção)</label>
                                    <input type="file" name="ficheiros[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <small class="text-muted">Pode selecionar vários ficheiros (Imagens, PDF, DOC). Máximo 10MB por ficheiro. (Opcional)</small>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Verificação de Segurança (Captcha)</label>
                                    <div class="captcha-box">
                                        <span class="captcha-q">Quanto é <?php echo $_SESSION['captcha_a']; ?> + <?php echo $_SESSION['captcha_b']; ?>?</span>
                                        <input type="number" name="captcha_res" class="form-control" style="max-width: 120px;" required placeholder="Resultado">
                                    </div>
                                </div>

                                <div class="col-md-12 text-end mt-4">
                                    <button type="submit" class="btn-submit">ENVIAR MENSAGEM <i class="fas fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-box-container" style="position:relative; z-index:20;">
                        <?php
                        // Carregar departamentos dinâmicos
                        try {
                            $stmt_dep = $pdo->prepare("SELECT * FROM departamentos_contactos WHERE status = 'ativo' ORDER BY ordem ASC");
                            $stmt_dep->execute();
                            $departamentos = $stmt_dep->fetchAll();
                        } catch (Exception $e) { $departamentos = []; }
                        
                        if (!empty($departamentos)):
                            $first = $departamentos[0];
                        ?>
                        <div class="info-box">
                            <h3 class="mb-4 text-white" style="font-family:'Libre Baskerville', serif; font-weight:700; font-size:1.3rem;">Os Nossos Contactos</h3>
                            
                            <div class="step-item">
                                <div class="step-num"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="step-title" style="font-size:1rem; font-family:'Libre Baskerville', serif;">Sede</div>
                                    <div class="step-desc"><?php echo nl2br(htmlspecialchars($first->morada)); ?></div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num"><i class="fas fa-phone-alt"></i></div>
                                <div>
                                    <div class="step-title">Telefone</div>
                                    <div class="step-desc"><?php echo htmlspecialchars($first->telefone); ?></div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <div class="step-title">E-mail</div>
                                    <div class="step-desc"><?php echo htmlspecialchars($first->email); ?></div>
                                </div>
                            </div>

                            <?php if($first->horario): ?>
                            <div class="step-item">
                                <div class="step-num"><i class="far fa-clock"></i></div>
                                <div>
                                    <div class="step-title">Horário</div>
                                    <div class="step-desc"><?php echo htmlspecialchars($first->horario); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Outros departamentos -->
                        <?php if(count($departamentos) > 1): ?>
                        <div class="urgent-card">
                            <div class="urgent-title"><i class="fas fa-building"></i> Departamentos</div>
                            <?php foreach(array_slice($departamentos, 1) as $dep): ?>
                                <div class="mb-3 pb-3" style="border-bottom: 1px solid #f0ece4;">
                                    <div class="fw-bold small" style="color: var(--primary-maroon);"><?php echo htmlspecialchars($dep->titulo); ?></div>
                                    <?php if($dep->morada): ?><div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-map-marker-alt me-1 opacity-50"></i><?php echo htmlspecialchars($dep->morada); ?></div><?php endif; ?>
                                    <?php if($dep->telefone): ?><div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-phone-alt me-1 opacity-50"></i><?php echo htmlspecialchars($dep->telefone); ?></div><?php endif; ?>
                                    <?php if($dep->email): ?><div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-envelope me-1 opacity-50"></i><?php echo htmlspecialchars($dep->email); ?></div><?php endif; ?>
                                    <?php if($dep->horario): ?><div class="text-muted" style="font-size:0.78rem;"><i class="far fa-clock me-1 opacity-50"></i><?php echo htmlspecialchars($dep->horario); ?></div><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <!-- Fallback estático caso tabela esteja vazia -->
                        <div class="info-box">
                            <h3 class="mb-4 text-white" style="font-family:'Libre Baskerville', serif; font-weight:700; font-size:1.3rem;">Os Nossos Contactos</h3>
                            <div class="step-item">
                                <div class="step-num"><i class="fas fa-map-marker-alt"></i></div>
                                <div><div class="step-title">Endereço</div><div class="step-desc">Bissau, Guiné-Bissau</div></div>
                            </div>
                            <div class="step-item">
                                <div class="step-num"><i class="fas fa-envelope"></i></div>
                                <div><div class="step-title">E-mail</div><div class="step-desc">info@oagb.gw</div></div>
                            </div>
                        </div>
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