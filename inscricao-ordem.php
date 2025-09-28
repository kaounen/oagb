<?php
require_once 'connect.php';

$success_message = '';
$error_message = '';

// Processar formulário
if ($_POST) {
    $tipo_inscricao = sanitize($_POST['tipo_inscricao']);
    $nome_completo = sanitize($_POST['nome_completo']);
    $genero = sanitize($_POST['genero']);
    $data_nascimento = sanitize($_POST['data_nascimento']);
    $nacionalidade = sanitize($_POST['nacionalidade']);
    $bi_passaporte = sanitize($_POST['bi_passaporte']);
    $regiao = sanitize($_POST['regiao']);
    $localidade = sanitize($_POST['localidade']);
    $morada = sanitize($_POST['morada']);
    $telefone = sanitize($_POST['telefone']);
    $email = sanitize($_POST['email']);
    $formacao_academica = sanitize($_POST['formacao_academica']);
    $experiencia_profissional = sanitize($_POST['experiencia_profissional']);
    
    // Validação
    $errors = [];
    
    if (empty($tipo_inscricao) || !in_array($tipo_inscricao, ['advogado', 'estagiario'])) {
        $errors[] = "Tipo de inscrição inválido.";
    }
    
    if (empty($nome_completo) || strlen($nome_completo) < 5) {
        $errors[] = "Nome completo deve ter pelo menos 5 caracteres.";
    }
    
    if (empty($genero) || !in_array($genero, ['M', 'F'])) {
        $errors[] = "Género inválido.";
    }
    
    if (empty($data_nascimento)) {
        $errors[] = "Data de nascimento é obrigatória.";
    }
    
    if (empty($bi_passaporte)) {
        $errors[] = "BI/Passaporte é obrigatório.";
    }
    
    if (empty($regiao)) {
        $errors[] = "Região é obrigatória.";
    }
    
    if (empty($morada)) {
        $errors[] = "Morada é obrigatória.";
    }
    
    if (empty($telefone) || strlen($telefone) < 7) {
        $errors[] = "Telefone inválido.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }
    
    if (empty($formacao_academica) || strlen($formacao_academica) < 20) {
        $errors[] = "Formação académica deve ter pelo menos 20 caracteres.";
    }
    
    // Verificar se email já existe
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM inscricoes_ordem WHERE email = ? AND status IN ('pendente', 'em_analise', 'aprovado')");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Já existe uma inscrição ativa com este email.";
        }
    }
    
    if (empty($errors)) {
        try {
            // Inserir na base de dados
            $stmt = $pdo->prepare("
                INSERT INTO inscricoes_ordem 
                (tipo_inscricao, nome_completo, genero, data_nascimento, nacionalidade, bi_passaporte, 
                 regiao, localidade, morada, telefone, email, formacao_academica, experiencia_profissional) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $tipo_inscricao, $nome_completo, $genero, $data_nascimento, $nacionalidade, 
                $bi_passaporte, $regiao, $localidade, $morada, $telefone, $email, 
                $formacao_academica, $experiencia_profissional
            ]);
            
            $inscricao_id = $pdo->lastInsertId();
            
            // Enviar email de notificação à administração
            $to = ADMIN_EMAIL;
            $subject = "Nova Inscrição na Ordem - " . ucfirst($tipo_inscricao);
            $message = "
                <h3>Nova Inscrição na Ordem dos Advogados</h3>
                <p><strong>Tipo:</strong> " . ucfirst($tipo_inscricao) . "</p>
                <p><strong>Nome:</strong> $nome_completo</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Telefone:</strong> $telefone</p>
                <p><strong>Região:</strong> $regiao</p>
                <p><strong>Data de Nascimento:</strong> $data_nascimento</p>
                <p><strong>BI/Passaporte:</strong> $bi_passaporte</p>
                <p><strong>Formação Académica:</strong></p>
                <p>$formacao_academica</p>
                <hr>
                <p><small>Inscrição recebida em " . date('d/m/Y H:i:s') . " - ID: $inscricao_id</small></p>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@oagb.gw" . "\r\n";
            $headers .= "Reply-To: $email" . "\r\n";
            
            @mail($to, $subject, $message, $headers);
            
            // Email de confirmação ao requerente
            $subject_confirm = "Confirmação de Inscrição - OAGB";
            $message_confirm = "
                <h3>Inscrição Recebida</h3>
                <p>Caro(a) $nome_completo,</p>
                <p>Recebemos a sua candidatura para inscrição como <strong>" . ($tipo_inscricao == 'advogado' ? 'Advogado' : 'Advogado Estagiário') . "</strong> na Ordem dos Advogados da Guiné-Bissau.</p>
                <p><strong>Número de candidatura:</strong> " . str_pad($inscricao_id, 6, '0', STR_PAD_LEFT) . "</p>
                <p><strong>Próximos passos:</strong></p>
                <ul>
                    <li>A sua candidatura será analisada pelos nossos serviços</li>
                    <li>Poderá ser contactado(a) para esclarecimentos adicionais</li>
                    <li>Será notificado(a) sobre o resultado da análise</li>
                </ul>
                <p><strong>Documentos necessários (enviar por email ou entregar presencialmente):</strong></p>
                <ul>
                    <li>Certificado de habilitações (cópia autenticada)</li>
                    <li>Cópia do BI/Passaporte</li>
                    <li>Certificado de registo criminal</li>
                    <li>2 fotografias tipo passe</li>
                    " . ($tipo_inscricao == 'estagiario' ? '<li>Carta de apresentação de um advogado orientador</li>' : '') . "
                </ul>
                <p>Para questões sobre o processo, contacte-nos pelo telefone +245 955 475 889 ou email info@oagb.gw.</p>
                <hr>
                <p><small>Ordem dos Advogados da Guiné-Bissau<br>
                Rua 15, Bissau<br>
                Tel: +245 955 475 889<br>
                Email: info@oagb.gw</small></p>
            ";
            
            @mail($email, $subject_confirm, $message_confirm, "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: noreply@oagb.gw\r\n");
            
            $success_message = "Inscrição submetida com sucesso! Receberá uma confirmação por email com os próximos passos. Número de candidatura: " . str_pad($inscricao_id, 6, '0', STR_PAD_LEFT);
            
            // Limpar variáveis POST
            $_POST = [];
            
        } catch (Exception $e) {
            $error_message = "Erro ao processar inscrição. Tente novamente.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

$page_title = "Inscrição na Ordem";
$breadcrumb = "Advogados";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Inscrição na Ordem - Ordem dos Advogados da Guiné-Bissau</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Inscrição Ordem Advogados, OAGB, Tornar-se Advogado" name="keywords">
    <meta content="Inscreva-se na Ordem dos Advogados da Guiné-Bissau como Advogado ou Estagiário" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

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

    <!-- Registration Form Start -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 600px;">
                <h5 class="fw-bold text-primary text-uppercase" style="font-family: 'Open Sans';">Junte-se a Nós</h5>
                <h1 class="mb-0" style="color:#5B463F;font-family: 'Libre Baskerville'; font-weight: bold;">Inscrição na Ordem dos Advogados</h1>
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
                    <form method="POST" action="" id="registrationForm">
                        <div class="row g-3">
                            <!-- Tipo de Inscrição -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold" style="font-family: 'Open Sans';">Tipo de Inscrição *</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_inscricao" id="advogado" value="advogado" 
                                                   <?php echo (isset($_POST['tipo_inscricao']) && $_POST['tipo_inscricao'] == 'advogado') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="advogado" style="font-family: 'Open Sans';">
                                                <strong>Advogado</strong><br>
                                                <small class="text-muted">Para licenciados em Direito</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_inscricao" id="estagiario" value="estagiario"
                                                   <?php echo (isset($_POST['tipo_inscricao']) && $_POST['tipo_inscricao'] == 'estagiario') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="estagiario" style="font-family: 'Open Sans';">
                                                <strong>Advogado Estagiário</strong><br>
                                                <small class="text-muted">Para estágio profissional</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dados Pessoais -->
                            <div class="col-12"><h5 class="border-bottom pb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Dados Pessoais</h5></div>
                            
                            <div class="col-md-8">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Nome Completo *</label>
                                <input type="text" name="nome_completo" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Género *</label>
                                <select name="genero" class="form-select bg-light border-0" style="height: 55px;" required>
                                    <option value="">Selecione</option>
                                    <option value="M" <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Data de Nascimento *</label>
                                <input type="date" name="data_nascimento" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['data_nascimento']) ? htmlspecialchars($_POST['data_nascimento']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Nacionalidade</label>
                                <input type="text" name="nacionalidade" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['nacionalidade']) ? htmlspecialchars($_POST['nacionalidade']) : 'Guineense'; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">BI/Passaporte *</label>
                                <input type="text" name="bi_passaporte" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['bi_passaporte']) ? htmlspecialchars($_POST['bi_passaporte']) : ''; ?>" required>
                            </div>
                            
                            <!-- Contactos e Morada -->
                            <div class="col-12 mt-4"><h5 class="border-bottom pb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Contactos e Morada</h5></div>
                            
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Região *</label>
                                <select name="regiao" class="form-select bg-light border-0" style="height: 55px;" required>
                                    <option value="">Selecione a região</option>
                                    <?php foreach ($regioes_gb as $codigo => $nome): ?>
                                        <option value="<?php echo $codigo; ?>" <?php echo (isset($_POST['regiao']) && $_POST['regiao'] == $codigo) ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Localidade</label>
                                <input type="text" name="localidade" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['localidade']) ? htmlspecialchars($_POST['localidade']) : ''; ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Morada Completa *</label>
                                <textarea name="morada" class="form-control border-0 bg-light px-4 py-3" rows="2" 
                                          placeholder="Endereço completo..." required><?php echo isset($_POST['morada']) ? htmlspecialchars($_POST['morada']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Telefone *</label>
                                <input type="tel" name="telefone" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Email *</label>
                                <input type="email" name="email" class="form-control border-0 bg-light px-4" style="height: 55px;" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            
                            <!-- Formação e Experiência -->
                            <div class="col-12 mt-4"><h5 class="border-bottom pb-2" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Formação e Experiência</h5></div>
                            
                            <div class="col-12">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Formação Académica *</label>
                                <textarea name="formacao_academica" class="form-control border-0 bg-light px-4 py-3" rows="4" 
                                          placeholder="Descreva a sua formação académica (curso, instituição, ano de conclusão, etc.)..." required><?php echo isset($_POST['formacao_academica']) ? htmlspecialchars($_POST['formacao_academica']) : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-family: 'Open Sans'; font-weight: 600;">Experiência Profissional</label>
                                <textarea name="experiencia_profissional" class="form-control border-0 bg-light px-4 py-3" rows="3" 
                                          placeholder="Descreva a sua experiência profissional relevante (opcional)..."><?php echo isset($_POST['experiencia_profissional']) ? htmlspecialchars($_POST['experiencia_profissional']) : ''; ?></textarea>
                            </div>
                            
                            <!-- Termos -->
                            <div class="col-12 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termos" required>
                                    <label class="form-check-label" for="termos" style="font-family: 'Open Sans';">
                                        Declaro que as informações fornecidas são verdadeiras e estou ciente dos requisitos para inscrição na Ordem dos Advogados da Guiné-Bissau. 
                                        Autorizo o tratamento dos meus dados pessoais para efeitos de análise da candidatura.
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">
                                    <i class="fa fa-paper-plane me-2"></i>Submeter Inscrição
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Info Sidebar -->
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-light rounded p-4 mb-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Requisitos de Inscrição</h5>
                        <div style="font-family: 'Open Sans';">
                            <h6 class="text-primary">Para Advogado:</h6>
                            <ul class="list-unstyled mb-3">
                                <li><i class="bi bi-check2 text-success me-2"></i>Licenciatura em Direito</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Certificado de habilitações</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Registo criminal limpo</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Documentos de identificação</li>
                            </ul>
                            
                            <h6 class="text-primary">Para Estagiário:</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check2 text-success me-2"></i>Licenciatura em Direito</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Advogado orientador</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Certificado de habilitações</li>
                                <li><i class="bi bi-check2 text-success me-2"></i>Documentos de identificação</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-primary text-white rounded p-4 mb-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville';">Documentos Necessários</h5>
                        <div style="font-family: 'Open Sans';">
                            <p class="mb-2">Após submeter a candidatura, deverá enviar:</p>
                            <ul class="list-unstyled">
                                <li><i class="fa fa-file me-2"></i>Certificado de habilitações (cópia autenticada)</li>
                                <li><i class="fa fa-file me-2"></i>Cópia do BI/Passaporte</li>
                                <li><i class="fa fa-file me-2"></i>Certificado de registo criminal</li>
                                <li><i class="fa fa-file me-2"></i>2 fotografias tipo passe</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3" style="font-family: 'Libre Baskerville'; color: #4D1C21;">Processo de Análise</h5>
                        <div style="font-family: 'Open Sans';">
                            <div class="d-flex mb-2">
                                <div class="bg-primary rounded-circle me-3" style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold small">1</span>
                                </div>
                                <small>Análise documental (5-10 dias)</small>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="bg-primary rounded-circle me-3" style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold small">2</span>
                                </div>
                                <small>Verificação de requisitos</small>
                            </div>
                            <div class="d-flex">
                                <div class="bg-primary rounded-circle me-3" style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold small">3</span>
                                </div>
                                <small>Decisão final e notificação</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration Form End -->

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
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const nome = document.querySelector('input[name="nome_completo"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const telefone = document.querySelector('input[name="telefone"]').value.trim();
        const formacao = document.querySelector('textarea[name="formacao_academica"]').value.trim();
        const termos = document.querySelector('#termos').checked;
        
        if (nome.length < 5) {
            e.preventDefault();
            alert('Nome completo deve ter pelo menos 5 caracteres.');
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
        
        if (formacao.length < 20) {
            e.preventDefault();
            alert('Formação académica deve ter pelo menos 20 caracteres.');
            return false;
        }
        
        if (!termos) {
            e.preventDefault();
            alert('Deve aceitar os termos para continuar.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processando...';
        submitBtn.disabled = true;
    });
    </script>
</body>
</html>