<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$course_id) { header("Location: formacao.php"); exit; }

try {
    $stmt = $pdo->prepare("SELECT * FROM gestao_cursos WHERE id = ? AND ativa = 1");
    $stmt->execute([$course_id]);
    $curso = $stmt->fetch();
    if (!$curso) { header("Location: formacao.php"); exit; }
} catch (Exception $e) { header("Location: formacao.php"); exit; }

$user_id = $_SESSION['lawyer_id'] ?? null;
$msg = null;
$error = null;

// Handle Enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if (!$user_id) {
        $error = "Deve estar autenticado para se inscrever.";
    } else {
        try {
            // Check if already enrolled
            $stmt = $pdo->prepare("SELECT id FROM gestao_cursos_inscritos WHERE curso_id = ? AND advogado_id = ?");
            $stmt->execute([$course_id, $user_id]);
            if ($stmt->fetch()) {
                $error = "Já se encontra inscrito nesta formação.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO gestao_cursos_inscritos (curso_id, advogado_id, status) VALUES (?, ?, 'pendente')");
                $stmt->execute([$course_id, $user_id]);
                $msg = "Inscrição realizada com sucesso! Aguarde a confirmação por parte da secretaria.";
            }
        } catch (Exception $e) {
            $error = "Erro ao processar inscrição. Tente novamente mais tarde.";
        }
    }
}

// Handle embedded login if not logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $registo = $_POST['registo'] ?? '';
    $pass = $_POST['password'] ?? '';
    try {
        $stmt = $pdo->prepare("SELECT *, 'advogado' as mtype FROM advogados WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
        $stmt->execute([$registo, $registo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $stmt = $pdo->prepare("SELECT *, 'estagiario' as mtype FROM advogados_estagiarios WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
            $stmt->execute([$registo, $registo]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if ($user) {
            if (($user['password'] === null && $pass === $user['numero_registo']) || ($user['password'] !== null && password_verify($pass, $user['password']))) {
                $_SESSION['lawyer_id'] = $user['id'];
                $_SESSION['lawyer_name'] = $user['nome_completo'];
                $_SESSION['member_type'] = $user['mtype'];
                $user_id = $user['id'];
                // Redirect to avoid resubmission
                header("Location: inscrever-curso.php?id=$course_id&login_success=1"); exit;
            } else { $error = "Credenciais inválidas."; }
        } else { $error = "Membro não encontrado ou inactivo."; }
    } catch (Exception $e) { $error = "Erro ao autenticar."; }
}

$page_title = "Inscrição em Formação";
$header_image = 'uploads/lady-justice-holding-scales-sword.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
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

        .enroll-card { background: #fff; border-radius: 24px; border: 1px solid #f0ece4; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.05); }
        .enroll-header { background: var(--primary-maroon); color: #fff; padding: 40px; text-align: center; }
        .enroll-body { padding: 40px; }
        .info-pill { background: #f8f9fa; border-radius: 12px; padding: 15px 20px; margin-bottom: 15px; border-left: 4px solid var(--primary-gold); }
        .form-label { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--primary-maroon); letter-spacing: 1px; }
        .btn-confirm { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 12px 40px; font-weight: 700; border: none; transition: .3s; }
        .btn-confirm:hover { background: var(--primary-maroon); transform: translateY(-2px); }
        .login-box { background: rgba(177, 162, 118, 0.05); border: 1px dashed var(--primary-gold); border-radius: 16px; padding: 25px; margin-top: 20px; }
        .text-maroon { color: var(--primary-maroon) !important; }
        .text-gold { color: var(--primary-gold) !important; }
    </style>
</head>
<body>
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
                        <a href="advogados-inscritos.php">Advogados</a>
                        <span class="bc-sep"></span>
                        <a href="formacao.php">Formação & Cursos</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Inscrição</span>
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
        ['label' => 'Advogados', 'url' => 'advogados-inscritos.php'],
        ['label' => 'Formação', 'url' => 'formacao.php'],
        ['label' => 'Inscrição', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="enroll-card wow fadeInUp">
                        <div class="enroll-header">
                            <i class="fas fa-graduation-cap fa-3x mb-3" style="color: var(--primary-gold);"></i>
                            <h2 class="text-white fw-bold mb-0" style="font-family: 'Libre Baskerville';"><?php echo htmlspecialchars($curso->titulo); ?></h2>
                        </div>
                        <div class="enroll-body">
                            <?php if ($msg): ?>
                                <div class="alert alert-success p-4 rounded-4 shadow-sm">
                                    <i class="fas fa-check-circle fa-2x me-3 align-middle"></i>
                                    <span class="align-middle fw-bold"><?php echo $msg; ?></span>
                                    <div class="mt-3"><a href="formacao.php" class="btn btn-sm btn-success rounded-pill px-4">Voltar à Listagem</a></div>
                                </div>
                            <?php else: ?>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="info-pill">
                                            <div class="small text-muted text-uppercase fw-bold">Data da Formação</div>
                                            <div class="fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-gold"></i> <?php echo date('d/m/Y', strtotime($curso->data_inicio)); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-pill">
                                            <div class="small text-muted text-uppercase fw-bold">Investimento / Preço</div>
                                            <div class="fw-bold text-dark"><i class="fas fa-tag me-2 text-gold"></i> <?php echo ($curso->preco > 0) ? number_format($curso->preco, 0, ',', '.') . ' XOF' : 'Gratuito'; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="fw-bold" style="color: var(--primary-maroon);">Descrição da Formação</h5>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($curso->descricao)); ?></p>
                                </div>

                                <hr class="my-5 opacity-10">

                                <?php if ($error): ?>
                                    <div class="alert alert-danger mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
                                <?php endif; ?>

                                <?php if ($user_id): ?>
                                    <div class="text-center">
                                        <div class="p-4 bg-light rounded-4 border mb-4">
                                            <div class="small text-muted text-uppercase fw-bold mb-2">Identificado como:</div>
                                            <div class="h5 fw-bold mb-0" style="color: var(--primary-maroon);"><?php echo htmlspecialchars($_SESSION['lawyer_name']); ?></div>
                                            <div class="small text-muted"><?php echo strtoupper($_SESSION['member_type']); ?></div>
                                        </div>
                                        <form method="POST">
                                            <button type="submit" name="enroll" class="btn-confirm shadow-lg"><i class="fas fa-check-circle me-2"></i> CONFIRMAR INSCRIÇÃO AGORA</button>
                                        </form>
                                        <p class="small text-muted mt-3">Ao clicar em confirmar, os seus dados de membro serão associados a esta formação.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center mb-4">
                                        <div class="alert alert-warning rounded-4 border-warning">
                                            <i class="fas fa-lock me-2"></i> <strong>Acesso Reservado:</strong> Esta formação é exclusiva a membros da OAGB. Autentique-se para continuar.
                                        </div>
                                    </div>
                                    <div class="login-box shadow-sm">
                                        <h5 class="fw-bold mb-4" style="color: var(--primary-maroon);">Autenticação de Membro</h5>
                                        <form method="POST">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">E-mail ou Nº Cédula</label>
                                                    <input type="text" name="registo" class="form-control" required placeholder="Ex: CP-001/24">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Senha</label>
                                                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                                                </div>
                                                <div class="col-12 mt-4">
                                                    <button type="submit" name="login" class="btn btn-confirm w-100">LOGAR E CONTINUAR INSCRIÇÃO</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="mt-3 text-center small text-muted">
                                            Ainda não tem conta? <a href="inscricao-ordem.php" class="fw-bold text-maroon">Inscreva-se na Ordem</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
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
    <script src="js/main.js"></script>
</body>
</html>
