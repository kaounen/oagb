<?php
require_once __DIR__ . '/connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$success = null;
$error = null;

// Fetch active advocates to populate target accused list
try {
    $stmt = $pdo->query("SELECT id, nome_completo, numero_registo FROM advogados ORDER BY nome_completo ASC");
    $advogados = $stmt->fetchAll();
} catch (PDOException $e) {
    $advogados = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $queixoso = clean_input($_POST['queixoso_nome'] ?? '');
    $email = clean_input($_POST['queixoso_email'] ?? '');
    $telefone = clean_input($_POST['queixoso_telefone'] ?? '');
    $advogado_id = (int)($_POST['advogado_id'] ?? 0);
    $assunto = clean_input($_POST['assunto'] ?? '');
    $descricao = clean_input($_POST['descricao'] ?? '');
    
    if (empty($queixoso) || empty($email) || empty($advogado_id) || empty($descricao)) {
        $error = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        try {
            // Generate Process Protocol Number
            $rand = rand(100, 999);
            $num_processo = "PROC-" . date('Y') . "-" . $rand;
            
            // Insert into gestao_disciplinar_processos
            $stmt = $pdo->prepare("INSERT INTO gestao_disciplinar_processos (numero_processo, advogado_id, queixoso_nome, status, descricao, data_abertura) 
                                   VALUES (?, ?, ?, 'instrucao', ?, CURDATE())");
            $stmt->execute([$num_processo, $advogado_id, $queixoso, $descricao . "\n\nContactos Queixoso: Tel " . $telefone . " | E-mail " . $email]);
            
            $success = "Reclamação formal submetida com sucesso! O número do processo disciplinar atribuído é: <strong>" . $num_processo . "</strong>. A comissão de instrução deontológica da OAGB dará início à análise das provas fornecidas.";
        } catch (Exception $e) {
            $error = "Ocorreu um erro ao registar a reclamação. Por favor, tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Apresentar Reclamação Deontológica | OAGB</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@350;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        :root { --primary-maroon: #4D1C21; --primary-gold: #B1A276; }
        .hero-header-custom {
            background: linear-gradient(rgba(9, 30, 62, .85), rgba(9, 30, 62, .85)), url('img/symbol-legal-law.jpg') center center no-repeat;
            background-size: cover;
            padding: 80px 0 60px 0;
            text-align: center;
        }
        .form-label { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #444; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #ced4da; padding: 12px 15px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(177, 162, 118, 0.25); }
    </style>
</head>
<body>

    <!-- Header Start -->
    <?php include 'includes/navbar.php'; ?>
    <!-- Header End -->

    <!-- Hero Header -->
    <div class="container-fluid hero-header-custom mb-5">
        <div class="container py-4">
            <h1 class="display-5 text-white animated slideInDown font-libre fw-bold" style="font-weight: 400 !important; font-size: 2.5rem !important;">Canal de Ética & Reclamações</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="index.php">Início</a></li>
                    <li class="breadcrumb-item"><a class="text-white text-gold" href="cidadaos.php">Cidadãos</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Reclamação</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Content Start -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow p-5 border-top border-5" style="border-top-color: var(--primary-maroon) !important;">
                    <h3 class="font-libre fw-bold text-dark mb-4 text-center">Formulário de Participação Deontológica</h3>
                    <p class="text-muted small text-center mb-5">Este canal serve para participação de comportamentos eventualmente violadores do Estatuto e Regulamento Deontológico por parte de advogados inscritos. O preenchimento com dados reais é obrigatório.</p>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 shadow-sm p-4 mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="apresentar-reclamacao.php">
                        <div class="row g-4">
                            <h5 class="fw-bold text-dark mt-4 mb-2" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">1. Identificação do Comparticipante (Queixoso)</h5>
                            
                            <div class="col-12">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="queixoso_nome" class="form-control" placeholder="Seu nome completo" required>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <label class="form-label">Endereço de E-mail *</label>
                                <input type="email" name="queixoso_email" class="form-control" placeholder="comunicacao@exemplo.com" required>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">Telefone / WhatsApp</label>
                                <input type="text" name="queixoso_telefone" class="form-control" placeholder="+245 XXXXXXX">
                            </div>

                            <h5 class="fw-bold text-dark mt-5 mb-2" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">2. Detalhes da Reclamação</h5>
                            
                            <div class="col-12">
                                <label class="form-label">Advogado Visado *</label>
                                <select name="advogado_id" class="form-select" required>
                                    <option value="">-- Selecione o Advogado inscrito --</option>
                                    <?php foreach ($advogados as $a): ?>
                                        <option value="<?php echo $a['id']; ?>">
                                            <?php echo htmlspecialchars($a['nome_completo']); ?> (Cédula: <?php echo $a['numero_registo']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Assunto Curto *</label>
                                <input type="text" name="assunto" class="form-control" placeholder="Ex: Retenção indevida de valores, falta de representação..." required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descrição Pormenorizada dos Factos *</label>
                                <textarea name="descricao" class="form-control" rows="8" placeholder="Descreva cronologicamente as ações do profissional visado que motivam esta participação, incluindo valores envolvidos e datas..." required style="resize: none;"></textarea>
                            </div>

                            <div class="col-12 mt-5 text-end">
                                <button type="submit" class="btn btn-dark px-5 py-3 fw-bold rounded-pill text-uppercase" style="background: var(--primary-maroon); border: none;">Submeter Participação Deontológica</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Footer Start -->
    <?php include 'includes/footer.php'; ?>
    <!-- Footer End -->

</body>
</html>
