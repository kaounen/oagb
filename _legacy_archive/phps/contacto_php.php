<?php
require_once 'connect.php';

$success_message = '';
$error_message = '';

// Processar formulário de contacto
if ($_POST) {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $assunto = sanitize($_POST['assunto']);
    $mensagem = sanitize($_POST['mensagem']);
    
    // Validação
    $errors = [];
    
    if (empty($nome) || strlen($nome) < 2) {
        $errors[] = "Nome deve ter pelo menos 2 caracteres.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }
    
    if (empty($assunto) || strlen($assunto) < 3) {
        $errors[] = "Assunto deve ter pelo menos 3 caracteres.";
    }
    
    if (empty($mensagem) || strlen($mensagem) < 10) {
        $errors[] = "Mensagem deve ter pelo menos 10 caracteres.";
    }
    
    if (empty($errors)) {
        try {
            // Inserir na base de dados
            $stmt = $pdo->prepare("INSERT INTO mensagens_contacto (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $assunto, $mensagem]);
            
            // Enviar email
            $to = ADMIN_EMAIL;
            $subject = "Contacto Website OAGB: " . $assunto;
            $message = "
                <h3>Nova mensagem do website OAGB</h3>
                <p><strong>Nome:</strong> $nome</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Assunto:</strong> $assunto</p>
                <p><strong>Mensagem:</strong></p>
                <p>$mensagem</p>
                <hr>
                <p><small>Enviado em " . date('d/m/Y H:i:s') . "</small></p>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@oagb.gw" . "\r\n";
            $headers .= "Reply-To: $email" . "\r\n";
            
            if (mail($to, $subject, $message, $headers)) {
                $success_message = "Mensagem enviada com sucesso! Responderemos em breve.";
            } else {
                $success_message = "Mensagem registada. Entraremos em contacto brevemente.";
            }
            
            // Limpar variáveis POST
            $_POST = [];
            
        } catch (Exception $e) {
            $error_message = "Erro ao enviar mensagem. Tente novamente.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

$page_title = "Contacto";
$breadcrumb = "Contacto";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Contacto - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Contacto OAGB, Ordem dos Advogados Guinea-Bissau" name="keywords">
    <meta content="Entre em contacto com a Ordem dos Advogados da Guiné-Bissau" name="description">

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

    <!-- Contact Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Entre em contacto</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Para qualquer esclarecimento, sinta-se à vontade para entrar em contacto connosco</h1>
            </div>
            
            <!-- Contact Info -->
            <div class="row g-5 mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center wow fadeIn" data-wow-delay="0.1s">
                        <div class="bg-primary d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                            <i class="fa fa-phone-alt text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-2" style="font-family: 'Libre Baskerville';">Telefone</h5>
                            <h4 class="text-primary mb-0" style="font-family: 'Open Sans';">+245 955 475 889</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex align-items-center wow fadeIn" data-wow-delay="0.4s">
                        <div class="bg-primary d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                            <i class="fa fa-envelope-open text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-2" style="font-family: 'Libre Baskerville';">Email</h5>
                            <h4 class="text-primary mb-0" style="font-family: 'Open Sans';">info@oagb.gw</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex align-items-center wow fadeIn" data-wow-delay="0.8s">
                        <div class="bg-primary d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                            <i class="fa fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="ps-4">
                            <h5 class="mb-2" style="font-family: 'Libre Baskerville';">Visite a nossa sede</h5>
                            <h4 class="text-primary mb-0" style="font-family: 'Open Sans';">Rua 15, Bissau<br>Guiné-Bissau</h4>
                        </div>
                    </div>
                </div>
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
            
            <!-- Contact Form & Map -->
            <div class="row g-5">
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.3s">
                    <form method="POST" action="" id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="nome" class="form-control border-0 bg-light px-4" placeholder="Seu Nome" style="height: 55px;" 
                                       value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control border-0 bg-light px-4" placeholder="Seu Email" style="height: 55px;" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="assunto" class="form-control border-0 bg-light px-4" placeholder="Assunto" style="height: 55px;" 
                                       value="<?php echo isset($_POST['assunto']) ? htmlspecialchars($_POST['assunto']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <textarea name="mensagem" class="form-control border-0 bg-light px-4 py-3" rows="4" placeholder="Sua Mensagem" required><?php echo isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">
                                    <i class="fa fa-paper-plane me-2"></i>Enviar Mensagem
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 wow slideInUp" data-wow-delay="0.6s">
                    <iframe class="position-relative rounded w-100 h-100"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d990.3460292641952!2d-15.582842285524569!3d11.863921874901138!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xee6c4d7ad6ccca5%3A0xccc07c46184a1c89!2sOrdem%20de%20Advogados%20da%20Guin%C3%A9-Bissau!5e0!3m2!1spt-PT!2s!4v1691479142763!5m2!1spt-PT!2s" 
                        width="600" height="450" style="border:0;min-height: 350px;" allowfullscreen="" loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade" frameborder="0" aria-hidden="false" tabindex="0">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Additional Info -->
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="bg-white rounded p-4 h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-clock text-white fa-2x"></i>
                            </div>
                            <h5 style="font-family: 'Libre Baskerville'; color: #4D1C21;">Horário de Funcionamento</h5>
                        </div>
                        <ul class="list-unstyled" style="font-family: 'Open Sans';">
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span>Segunda - Sexta:</span>
                                <span class="fw-bold">08:00 - 17:00</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span>Sábado:</span>
                                <span class="fw-bold">08:00 - 12:00</span>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span>Domingo:</span>
                                <span class="fw-bold text-muted">Fechado</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white rounded p-4 h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-question-circle text-white fa-2x"></i>
                            </div>
                            <h5 style="font-family: 'Libre Baskerville'; color: #4D1C21;">Perguntas Frequentes</h5>
                        </div>
                        <div style="font-family: 'Open Sans';">
                            <p class="mb-3"><strong>Como me posso inscrever na Ordem?</strong><br>
                            <small class="text-muted">Consulte a página de <a href="inscricao-ordem.php">Inscrição na Ordem</a> para mais detalhes.</small></p>
                            <p class="mb-3"><strong>Como encontrar um advogado?</strong><br>
                            <small class="text-muted">Use a nossa <a href="pesquisa-advogados.php">ferramenta de pesquisa</a> ou <a href="solicitacao-advogados.php">solicite indicação</a>.</small></p>
                            <p class="mb-0"><strong>Preciso de ajuda urgente?</strong><br>
                            <small class="text-muted">Ligue para +245 955 475 889 durante o horário de expediente.</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white rounded p-4 h-100 shadow-sm">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-users text-white fa-2x"></i>
                            </div>
                            <h5 style="font-family: 'Libre Baskerville'; color: #4D1C21;">Siga-nos nas Redes</h5>
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-outline-primary btn-square me-2 mb-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-square me-2 mb-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-square me-2 mb-2"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-square mb-2"><i class="fab fa-instagram"></i></a>
                        </div>
                        <p class="text-center mt-3 mb-0" style="font-family: 'Open Sans';">
                            <small class="text-muted">Mantenha-se atualizado com as últimas notícias e eventos da OAGB.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const nome = document.querySelector('input[name="nome"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const assunto = document.querySelector('input[name="assunto"]').value.trim();
        const mensagem = document.querySelector('textarea[name="mensagem"]').value.trim();
        
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
        
        if (assunto.length < 3) {
            e.preventDefault();
            alert('Assunto deve ter pelo menos 3 caracteres.');
            return false;
        }
        
        if (mensagem.length < 10) {
            e.preventDefault();
            alert('Mensagem deve ter pelo menos 10 caracteres.');
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