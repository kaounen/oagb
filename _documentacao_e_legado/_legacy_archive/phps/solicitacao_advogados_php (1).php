<?php
require_once 'connect.php';

$success_message = '';
$error_message = '';

// Processar formulário
if ($_POST) {
    $nome_solicitante = sanitize($_POST['nome_solicitante']);
    $email = sanitize($_POST['email']);
    $telefone = sanitize($_POST['telefone']);
    $area_juridica = sanitize($_POST['area_juridica']);
    $regiao_preferencia = sanitize($_POST['regiao_preferencia']);
    $descricao_caso = sanitize($_POST['descricao_caso']);
    $urgencia = sanitize($_POST['urgencia']);
    
    // Validação
    $errors = [];
    
    if (empty($nome_solicitante) || strlen($nome_solicitante) < 2) {
        $errors[] = "Nome deve ter pelo menos 2 caracteres.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }
    
    if (empty($telefone) || strlen($telefone) < 7) {
        $errors[] = "Telefone inválido.";
    }
    
    if (empty($area_juridica)) {
        $errors[] = "Selecione uma área jurídica.";
    }
    
    if (empty($regiao_preferencia)) {
        $errors[] = "Selecione uma região de preferência.";
    }
    
    if (empty($descricao_caso) || strlen($descricao_caso) < 20) {
        $errors[] = "Descrição do caso deve ter pelo menos 20 caracteres.";
    }
    
    if (empty($errors)) {
        try {
            // Inserir na base de dados
            $stmt = $pdo->prepare("
                INSERT INTO solicitacoes_advogados 
                (nome_solicitante, email, telefone, area_juridica, regiao_preferencia, descricao_caso, urgencia) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nome_solicitante, $email, $telefone, $area_juridica, 
                $regiao_preferencia, $descricao_caso, $urgencia
            ]);
            
            // Enviar email de notificação
            $to = ADMIN_EMAIL;
            $subject = "Nova Solicitação de Advogado - OAGB";
            $message = "
                <h3>Nova Solicitação de Advogado</h3>
                <p><strong>Nome:</strong> $nome_solicitante</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Telefone:</strong> $telefone</p>
                <p><strong>Área Jurídica:</strong> $area_juridica</p>
                <p><strong>Região de Preferência:</strong> $regiao_preferencia</p>
                <p><strong>Urgência:</strong> " . ucfirst($urgencia) . "</p>
                <p><strong>Descrição do Caso:</strong></p>
                <p>$descricao_caso</p>
                <hr>
                <p><small>Solicitação recebida em " . date('d/m/Y H:i:s') . "</small></p>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@oagb.gw" . "\r\n";
            $headers .= "Reply-To: $email" . "\r\n";
            
            @mail($to, $subject, $message, $headers);
            
            // Email de confirmação ao solicitante
            $subject_confirm = "Confirmação de Solicitação - OAGB";
            $message_confirm = "
                <h3>Solicitação Recebida</h3>
                <p>Caro(a) $nome_solicitante,</p>
                <p>Recebemos a sua solicitação de indicação de advogado.</p>
                <p><strong>Detalhes da solicitação:</strong></p>
                <ul>
                    <li><strong>Área Jurídica:</strong> $area_juridica</li>
                    <li><strong>Região:</strong> $regiao_preferencia</li>
                    <li><strong>Urgência:</strong> " . ucfirst($urgencia) . "</li>
                </ul>
                <p>A sua solicitação será analisada e entraremos em contacto brevemente com a indicação de um advogado adequado ao seu caso.</p>
                <p>Em caso de urgência, pode contactar-nos diretamente pelo telefone +245 955 475 889.</p>
                <hr>
                <p><small>Ordem dos Advogados da Guiné-Bissau<br>
                Rua 15, Bissau<br>
                Tel: +245 955 475 889<br>
                Email: info@oagb.gw</small></p>
            ";
            
            @mail($email, $subject_confirm, $message_confirm, "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@oagb.gw\r\n");
            
            $success_message = "Solicitação enviada com sucesso! Entraremos em contacto brevemente com a indicação de um advogado adequado.";
            
            // Limpar variáveis POST
            $_POST = [];
            
        } catch (Exception $e) {
            $error_message = "Erro ao processar solicitação. Tente novamente.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

$page_title = "Solicitação de Advogados";
$breadcrumb = "Advogados";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Solicitação de Advogados - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Solicitação de Advogados, OAGB, Indicação Advogado" name="keywords">
    <meta content="Solicite a indicação de um advogado qualificado para o seu caso" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Rua 15, Bissau, Guiné-Bissau</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+245 955 475 889</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>info@oagb.gw</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Header Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="h5 text-white">Início</a>
                    <i class="far fa-circle text-white px-2"></i>
                    <a href="" class="h5 text-white"><?php echo $page_title; ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar & Header End -->

    <!-- Full Screen Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(9, 30, 62, .7);">
                <div class="modal-header border-0">
                    <button type="button" class="btn bg-white btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" class="form-control bg-transparent border-primary p-3" placeholder="Digite a palavra de pesquisa">
                        <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Full Screen Search End -->

    <!-- Request Form Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Precisa de Assistência Jurídica?</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Solicite a indicação de um advogado qualificado</h1>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show wow fadeIn" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show wow fadeIn" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="row g-5">
                <!-- Form -->
                <div class="col-lg-8 wow slideInUp" data-wow-delay="0.3s">
                    <form method="POST" action="" id="requestForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Nome Completo *</label>
                                <input type="text" name="nome_solicitante" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['nome_solicitante']) ? htmlspecialchars($_POST['nome_solicitante']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Email *</label>
                                <input type="email" name="email" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Telefone *</label>
                                <input type="tel" name="telefone" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Área Jurídica *</label>
                                <select name="area_juridica" class="form-select bg-light border-0" style="height: 55px;" required>
                                    <option value="">Selecione a área</option>
                                    <?php foreach ($areas_juridicas as $codigo => $nome): ?>
                                        <option value="<?php echo $codigo; ?>" <?php echo (isset($_POST['area_juridica']) && $_POST['area_juridica'] == $codigo) ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Região de Preferência *</label>
                                <select name="regiao_preferencia" class="form-select bg-light border-0" style="height: 55px;" required>
                                    <option value="">Selecione a região</option>
                                    <?php foreach ($regioes_gb as $codigo => $nome): ?>
                                        <option value="<?php echo $codigo; ?>" <?php echo (isset($_POST['regiao_preferencia']) && $_POST['regiao_preferencia'] == $codigo) ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Urgência</label>
                                <select name="urgencia" class="form-select bg-light border-0" style="height: 55px;">
                                    <option value="media" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'media') ? 'selected' : ''; ?>>Média</option>
                                    <option value="baixa" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'baixa') ? 'selected' : ''; ?>>Baixa</option>
                                    <option value="alta" <?php echo (isset($_POST['urgencia']) && $_POST['urgencia'] == 'alta') ? 'selected' : ''; ?>>Alta</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Descrição do Caso *</label>
                                <textarea name="descricao_caso" class="form-control border-0 bg-light px-4 py-3" rows="5" 
                                          placeholder="Descreva detalhadamente o seu caso, incluindo informações relevantes que possam ajudar na indicação do advogado mais adequado..." required><?php echo isset($_POST['descricao_caso']) ? htmlspecialchars($_POST['descricao_caso']) : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termos" required>
                                    <label class="form-check-label" for="termos" style="font-family: 'Open Sans';">
                                        Declaro que as informações fornecidas são verdadeiras e autorizo o contacto por parte da OAGB ou do advogado indicado.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">
                                    <i class="fa fa-paper-plane me-2"></i>Enviar Solicitação
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Info Sidebar -->
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Como Funciona</h5>
                        <div style="font-family: 'Open Sans';">
                            <div class="d-flex mb-3">
                                <div class="bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold">1</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Preencha o formulário</h6>
                                    <small class="text-muted">Forneça detalhes sobre o seu caso</small>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold">2</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Análise da solicitação</h6>
                                    <small class="text-muted">Analisamos o seu caso em 24-48 horas</small>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold">3</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Indicação do advogado</h6>
                                    <small class="text-muted">Contactamos com a indicação adequada</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-primary text-white rounded p-4 mb-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Contacto Urgente</h5>
                        <p style="font-family: 'Open Sans';">Para casos urgentes, contacte-nos diretamente:</p>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa fa-phone me-2"></i>
                            <span>+245 955 475 889</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fa fa-envelope me-2"></i>
                            <span>info@oagb.gw</span>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Informações Importantes</h5>
                        <ul class="list-unstyled" style="font-family: 'Open Sans';">
                            <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>A indicação é gratuita</li>
                            <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Todos os advogados são qualificados</li>
                            <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Confidencialidade garantida</li>
                            <li class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Resposta em 24-48 horas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Request Form End -->

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Form Validation -->
    <script>
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        const nome = document.querySelector('input[name="nome_solicitante"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const telefone = document.querySelector('input[name="telefone"]').value.trim();
        const area = document.querySelector('select[name="area_juridica"]').value;
        const regiao = document.querySelector('select[name="regiao_preferencia"]').value;
        const descricao = document.querySelector('textarea[name="descricao_caso"]').value.trim();
        const termos = document.querySelector('#termos').checked;
        
        if (nome.length < 2) {
            e.preventDefault();
            alert('Nome deve ter pelo menos 2 caracteres.');
            return false;
        }
        
        if (!email || !email.includes('@')) {
            e.preventDefault();
            alert('Por favor, insira um email válido.');
            return false;
        }
        
        if (telefone.length < 7) {
            e.preventDefault();
            alert('Por favor, insira um número de telefone válido.');
            return false;
        }
        
        if (!area) {
            e.preventDefault();
            alert('Por favor, selecione uma área jurídica.');
            return false;
        }
        
        if (!regiao) {
            e.preventDefault();
            alert('Por favor, selecione uma região de preferência.');
            return false;
        }
        
        if (descricao.length < 20) {
            e.preventDefault();
            alert('Descrição do caso deve ter pelo menos 20 caracteres.');
            return false;
        }
        
        if (!termos) {
            e.preventDefault();
            alert('Deve aceitar os termos para continuar.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Enviando...';
        submitBtn.disabled = true;
    });
    </script>
</body>
</html>