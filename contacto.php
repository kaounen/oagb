<?php
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Contacto";
$meta_title = "Contacto - OAGB";
$meta_description = "Entre em contacto com a Ordem dos Advogados da Guiné-Bissau. Endereço, telefone, email e formulário de contacto.";

// Processar formulário de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = clean_input($_POST['nome'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $telefone = clean_input($_POST['telefone'] ?? '');
        $assunto = clean_input($_POST['assunto'] ?? '');
        $mensagem = clean_input($_POST['mensagem'] ?? '');
        $csrf_token = $_POST['csrf_token'] ?? '';

        // Validações
        $errors = [];

        if (empty($nome)) {
            $errors[] = "Nome é obrigatório";
        }

        if (empty($email) || !is_valid_email($email)) {
            $errors[] = "Email válido é obrigatório";
        }

        if (empty($assunto)) {
            $errors[] = "Assunto é obrigatório";
        }

        if (empty($mensagem)) {
            $errors[] = "Mensagem é obrigatória";
        }

        if (!validate_csrf_token($csrf_token)) {
            $errors[] = "Token de segurança inválido";
        }

        if (empty($errors)) {
            // Salvar na base de dados (criar tabela se necessário)
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO contactos (nome, email, telefone, assunto, mensagem, ip_origem) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
                $stmt->execute([$nome, $email, $telefone, $assunto, $mensagem, $ip]);

                // Opcional: Enviar email de notificação
                // send_contact_notification($nome, $email, $telefone, $assunto, $mensagem);

                $success_message = "Mensagem enviada com sucesso! Entraremos em contacto consigo em breve.";
                
                // Limpar variáveis do formulário
                $nome = $email = $telefone = $assunto = $mensagem = '';
                
            } catch (Exception $e) {
                $errors[] = "Erro ao enviar mensagem. Tente novamente.";
                error_log("Erro ao salvar contacto: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        $errors[] = "Erro interno. Tente novamente.";
        error_log("Erro no formulário de contacto: " . $e->getMessage());
    }
}

// Gerar token CSRF
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    <?php include 'includes/meta-tags.php'; ?>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
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
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="https://www.facebook.com/profile.php?id=100087015439692"><i class="fab fa-facebook-f fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-youtube fw-normal"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>

        <div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 90px;">
            <div class="row py-5">
                <div class="col-12 pt-lg-5 mt-lg-5 text-center">
                    <h1 class="display-4 text-white animated zoomIn">Contacto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Início</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Contacto</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Contact Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Entre em Contacto</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:280%;">Fale Connosco</h1>
            </div>
            
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="bg-primary rounded p-5 text-center">
                        <h4 class="text-white mb-4">Informações de Contacto</h4>
                        
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="bg-white rounded-circle p-3 me-3">
                                <i class="fa fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="text-white mb-1">Endereço</h6>
                                <small class="text-white">Rua 15, Bissau<br>Guiné-Bissau</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="bg-white rounded-circle p-3 me-3">
                                <i class="fa fa-phone-alt text-primary"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="text-white mb-1">Telefone</h6>
                                <small class="text-white">+245 955 475 889</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="bg-white rounded-circle p-3 me-3">
                                <i class="fa fa-envelope text-primary"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="text-white mb-1">Email</h6>
                                <small class="text-white">info@oagb.gw</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="bg-white rounded-circle p-3 me-3">
                                <i class="fa fa-clock text-primary"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="text-white mb-1">Horário</h6>
                                <small class="text-white">Seg-Sex: 8h-17h<br>Sáb: 8h-12h</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            <a class="btn btn-outline-light btn-square me-2" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-square me-2" href="https://www.facebook.com/profile.php?id=100087015439692"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-square me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a class="btn btn-outline-light btn-square" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="bg-light rounded p-5">
                        <h4 class="mb-4" style="color:#5B463F;">Envie-nos uma Mensagem</h4>
                        
                        <!-- Success/Error Messages -->
                        <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" 
                                           value="<?php echo htmlspecialchars($telefone ?? ''); ?>" 
                                           placeholder="+245 xxx xxx xxx">
                                </div>
                                <div class="col-md-6">
                                    <label for="assunto" class="form-label">Assunto *</label>
                                    <select class="form-select" id="assunto" name="assunto" required>
                                        <option value="">Selecione um assunto</option>
                                        <option value="informacoes_gerais" <?php echo (($assunto ?? '') == 'informacoes_gerais') ? 'selected' : ''; ?>>Informações Gerais</option>
                                        <option value="inscricao_ordem" <?php echo (($assunto ?? '') == 'inscricao_ordem') ? 'selected' : ''; ?>>Inscrição na Ordem</option>
                                        <option value="consulta_juridica" <?php echo (($assunto ?? '') == 'consulta_juridica') ? 'selected' : ''; ?>>Consulta Jurídica</option>
                                        <option value="formacao_eventos" <?php echo (($assunto ?? '') == 'formacao_eventos') ? 'selected' : ''; ?>>Formação e Eventos</option>
                                        <option value="reclamacao_sugestao" <?php echo (($assunto ?? '') == 'reclamacao_sugestao') ? 'selected' : ''; ?>>Reclamação/Sugestão</option>
                                        <option value="parcerias" <?php echo (($assunto ?? '') == 'parcerias') ? 'selected' : ''; ?>>Parcerias</option>
                                        <option value="imprensa" <?php echo (($assunto ?? '') == 'imprensa') ? 'selected' : ''; ?>>Imprensa</option>
                                        <option value="outros" <?php echo (($assunto ?? '') == 'outros') ? 'selected' : ''; ?>>Outros</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="mensagem" class="form-label">Mensagem *</label>
                                    <textarea class="form-control" id="mensagem" name="mensagem" rows="6" 
                                              placeholder="Escreva a sua mensagem aqui..." required><?php echo htmlspecialchars($mensagem ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="concordo_politica" required>
                                        <label class="form-check-label" for="concordo_politica">
                                            Concordo com a <a href="politica-privacidade.php" target="_blank">Política de Privacidade</a> *
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary py-3 px-5">
                                        <i class="fa fa-paper-plane me-2"></i>Enviar Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Map Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Localização</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:280%;">Como Chegar</h1>
            </div>
            
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="bg-light rounded p-5">
                        <h4 class="mb-4" style="color:#5B463F;">Indicações</h4>
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">De Carro:</h6>
                            <p class="mb-0">A sede da OAGB localiza-se na Rua 15, no centro de Bissau. Há estacionamento limitado nas proximidades.</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">Transporte Público:</h6>
                            <p class="mb-0">Várias linhas de transporte público passam perto da nossa sede. A paragem mais próxima fica a 2 minutos a pé.</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">Pontos de Referência:</h6>
                            <ul class="mb-0">
                                <li>Próximo ao Tribunal Regional de Bissau</li>
                                <li>A 5 minutos do Ministério da Justiça</li>
                                <li>No centro histórico da cidade</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light rounded p-2">
                        <!-- Google Maps Embed -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3915.7234567890123!2d-15.5985!3d11.8497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDUwJzU5LjAiTiAxNcKwMzUnNTQuNiJX!5e0!3m2!1spt!2sgw!4v1234567890123" 
                                width="100%" height="350" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Map End -->

    <!-- Quick Contact Options -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase">Contacto Rápido</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold; font-style: normal;font-size:280%;">Outras Formas de Contacto</h1>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow slideInUp" data-wow-delay="0.3s">
                    <div class="bg-white rounded shadow p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-phone text-white fa-lg"></i>
                        </div>
                        <h5 class="mb-2">Telefone</h5>
                        <p class="mb-3">Ligue diretamente para esclarecimentos rápidos</p>
                        <a href="tel:+245955475889" class="btn btn-outline-primary btn-sm">
                            +245 955 475 889
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-white rounded shadow p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-envelope text-white fa-lg"></i>
                        </div>
                        <h5 class="mb-2">Email</h5>
                        <p class="mb-3">Envie um email para questões detalhadas</p>
                        <a href="mailto:info@oagb.gw" class="btn btn-outline-primary btn-sm">
                            info@oagb.gw
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 wow slideInUp" data-wow-delay="0.9s">
                    <div class="bg-white rounded shadow p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-whatsapp text-white fa-lg"></i>
                        </div>
                        <h5 class="mb-2">WhatsApp</h5>
                        <p class="mb-3">Contacto rápido via WhatsApp</p>
                        <a href="https://wa.me/245955475889" target="_blank" class="btn btn-outline-primary btn-sm">
                            Enviar Mensagem
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 wow slideInUp" data-wow-delay="1.2s">
                    <div class="bg-white rounded shadow p-4 text-center h-100">
                        <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-facebook-f text-white fa-lg"></i>
                        </div>
                        <h5 class="mb-2">Facebook</h5>
                        <p class="mb-3">Siga-nos e envie mensagem</p>
                        <a href="https://www.facebook.com/profile.php?id=100087015439692" target="_blank" class="btn btn-outline-primary btn-sm">
                            Visitar Página
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Contact Options End -->

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
</body>
</html>