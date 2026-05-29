<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/functions.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$success = null;
$error = null;
$tracked_process = null;
$search_query = '';

// Fetch active advocates to populate accused list
try {
    $stmt = $pdo->query("SELECT id, nome_completo, numero_registo FROM advogados ORDER BY nome_completo ASC");
    $advogados = $stmt->fetchAll();
} catch (PDOException $e) {
    $advogados = [];
}

// ----------------------------------------------------
// PROCESS QUEIXA SUBMISSION
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {
    $queixoso = clean_input($_POST['queixoso_nome'] ?? '');
    $email = clean_input($_POST['queixoso_email'] ?? '');
    $telefone = clean_input($_POST['queixoso_telefone'] ?? '');
    $advogado_id = (int)($_POST['advogado_id'] ?? 0);
    $assunto = clean_input($_POST['assunto'] ?? '');
    $descricao = clean_input($_POST['descricao'] ?? '');
    
    if (empty($queixoso) || empty($email) || empty($advogado_id) || empty($descricao)) {
        $error = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        // Handle Multiple Proof Document Uploads
        $uploaded_files = [];
        if (!empty($_FILES['provas']['name'][0])) {
            $files = $_FILES['provas'];
            $upload_dir = 'uploads/reclamacoes';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            for ($i = 0; $i < count($files['name']); $i++) {
                $file_error = $files['error'][$i];
                if ($file_error === UPLOAD_ERR_OK) {
                    $tmp_name = $files['tmp_name'][$i];
                    $name = basename($files['name'][$i]);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                    
                    if (in_array($ext, $allowed)) {
                        $new_name = 'reclamacao_' . uniqid() . '_' . time() . '_' . $i . '.' . $ext;
                        if (move_uploaded_file($tmp_name, "$upload_dir/$new_name")) {
                            $uploaded_files[] = $new_name;
                        }
                    } else {
                        $error = "Ficheiro '$name' possui extensão não permitida.";
                    }
                }
            }
        }
        
        if (!$error) {
            try {
                // Generate Protocol ID
                $rand = rand(100, 999);
                $num_processo = "PROC-" . date('Y') . "-" . $rand;
                
                // Seed initial tracking history
                $initial_history = json_encode([
                    [
                        'data' => date('Y-m-d H:i:s'),
                        'evento' => 'Reclamação formal submetida pelo cidadão.',
                        'operador' => 'Sistema'
                    ]
                ]);
                
                $docs_json = !empty($uploaded_files) ? json_encode($uploaded_files) : null;
                $full_desc = "Assunto: $assunto\n\nFactos:\n$descricao\n\nContactos: Tel $telefone | Email $email";
                
                // Insert into gestao_disciplinar_processos
                $stmt = $pdo->prepare("INSERT INTO gestao_disciplinar_processos 
                    (numero_processo, advogado_id, queixoso_nome, status, descricao, documentos, historico_interno, resposta_ordem, data_abertura) 
                    VALUES (?, ?, ?, 'aberto', ?, ?, ?, ?, CURDATE())");
                
                $stmt->execute([
                    $num_processo, 
                    $advogado_id, 
                    $queixoso, 
                    $full_desc, 
                    $docs_json, 
                    $initial_history, 
                    'A sua reclamação foi registada no sistema e encontra-se em triagem inicial pela secretaria geral.'
                ]);
                
                $success = "Reclamação registada com sucesso! Guarde o seu número de processo para acompanhamento online: <strong style='font-size: 1.2rem; color: #fff; background: #000; padding: 2px 8px; border-radius: 4px;'>$num_processo</strong>";
            } catch (Exception $e) {
                $error = "Erro ao registar processo: " . $e->getMessage();
            }
        }
    }
}

// ----------------------------------------------------
// PROCESS TRACK PROCESS QUERY
// ----------------------------------------------------
if (isset($_GET['track_process_id'])) {
    $search_query = clean_input($_GET['track_process_id']);
    
    if (!empty($search_query)) {
        $stmt = $pdo->prepare("SELECT d.*, a.nome_completo as advogado_nome 
                               FROM gestao_disciplinar_processos d 
                               JOIN advogados a ON d.advogado_id = a.id 
                               WHERE d.numero_processo = ?");
        $stmt->execute([$search_query]);
        $tracked_process = $stmt->fetch();
        
        if (!$tracked_process) {
            $error = "Nenhum processo disciplinar encontrado com o número '$search_query'. Verifique o código e tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <title>Canal de Ética & Reclamações | OAGB</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-maroon: #4D1C21;
            --primary-gold: #B1A276;
            --dark-navy: #111923;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #111923; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #dcdfe3; padding: 12px 18px; font-size: 0.92rem; background: #fff; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.15); }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            line-height: 1; vertical-align: middle;
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 30px; border-left: 5px solid var(--primary-gold); padding-left: 20px; }

        .tab-btn { font-weight: 700; text-transform: uppercase; padding: 15px 30px; border-radius: 50px; font-size: 0.85rem; border: 2px solid transparent; transition: .3s; }
        .tab-btn.active { background-color: var(--primary-maroon); color: white !important; }
        .tab-btn:not(.active) { background: #fff; color: var(--primary-maroon); border-color: #eef0f2; }
        .tab-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .timeline-item { position: relative; padding-left: 30px; border-left: 2px solid #eef0f2; padding-bottom: 25px; }
        .timeline-item::after { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; background: var(--primary-gold); border-radius: 50%; }
        .timeline-item:last-child { border-left-color: transparent; }
    </style>
</head>
<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('img/symbol-legal-law.jpg') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="cidadaos.php">Cidadãos</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Ética & Reclamações</span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => 'Cidadãos', 'url' => 'cidadaos.php'],
        ['label' => 'Reclamações', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            
            <div class="text-center mb-4">
                <span class="section-label">Conselho Deontológico</span>
                <h2 class="section-heading border-0 p-0 text-center">Ética & Deontologia</h2>
            </div>
            
            <!-- Toggle Tabs -->
            <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
                <a href="apresentar-reclamacao.php?tab=submit" class="tab-btn <?php echo (!isset($_GET['track_process_id']) && ($_GET['tab'] ?? '') !== 'track') ? 'active' : ''; ?> text-decoration-none"><i class="fas fa-edit me-1"></i> Apresentar Reclamação</a>
                <a href="apresentar-reclamacao.php?tab=track" class="tab-btn <?php echo (isset($_GET['track_process_id']) || ($_GET['tab'] ?? '') === 'track') ? 'active' : ''; ?> text-decoration-none"><i class="fas fa-search me-1"></i> Acompanhar Processo</a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 shadow p-4 mb-4 rounded-4 text-white" style="background: #198754;"><i class="fas fa-check-circle fa-lg me-2"></i> <?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 shadow p-4 mb-4 rounded-4"><i class="fas fa-exclamation-triangle fa-lg me-2"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- TAB 1: SUBMIT FORM -->
                    <?php if (!isset($_GET['track_process_id']) && ($_GET['tab'] ?? '') !== 'track'): ?>
                        <div class="card card-custom bg-white p-5 border-top border-4" style="border-top-color: var(--primary-maroon) !important;">
                            <h3 class="font-libre fw-bold text-dark mb-3 text-center">Participação Deontológica</h3>
                            <p class="text-muted small text-center mb-5" style="max-width: 600px; margin: 0 auto;">
                                Este formulário permite expor infrações ou condutas impróprias praticadas por advogados no exercício das suas funções. Todos os anexos de provas documentais devem ser submetidos no final do formulário.
                            </p>

                            <form method="POST" enctype="multipart/form-data">
                                <div class="row g-4">
                                    <h5 class="fw-bold text-dark mt-4 mb-1" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;"><i class="fas fa-user-shield me-2 text-warning"></i> 1. Identificação do Comparticipante (Queixoso)</h5>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Nome Completo *</label>
                                        <input type="text" name="queixoso_nome" class="form-control" placeholder="Indique o seu nome completo" required>
                                    </div>
                                    
                                    <div class="col-md-6 col-12">
                                        <label class="form-label">Endereço de E-mail *</label>
                                        <input type="email" name="queixoso_email" class="form-control" placeholder="exemplo@email.com" required>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label">Telefone / WhatsApp</label>
                                        <input type="tel" name="queixoso_telefone" class="form-control" placeholder="+245 ...">
                                    </div>

                                    <h5 class="fw-bold text-dark mt-5 mb-1" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;"><i class="fas fa-user-ninja me-2 text-warning"></i> 2. Detalhes da Reclamação</h5>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Advogado Visado *</label>
                                        <select name="advogado_id" class="form-select" required>
                                            <option value="">-- Selecione o Advogado inscrito --</option>
                                            <?php foreach ($advogados as $a): ?>
                                                <option value="<?php echo $a['id']; ?>">
                                                    <?php echo htmlspecialchars($a['nome_completo']); ?> (Cédula Profissional: <?php echo $a['numero_registo']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Assunto Sumário *</label>
                                        <input type="text" name="assunto" class="form-control" placeholder="Ex: Retenção indevida de valores, falta de comparência a julgamento" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Descrição Pormenorizada dos Factos *</label>
                                        <textarea name="descricao" class="form-control" rows="8" placeholder="Descreva os acontecimentos de forma clara, indicando datas, quantias e testemunhas..." required style="resize: none;"></textarea>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <label class="form-label">Documentos de Prova (Imagens / PDFs)</label>
                                        <input type="file" name="provas[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <small class="text-muted">Pode selecionar múltiplos ficheiros de evidências para análise preliminar da Comissão.</small>
                                    </div>

                                    <div class="col-12 mt-5 text-end">
                                        <button type="submit" name="submit_complaint" class="btn btn-dark px-5 py-3 fw-bold rounded-pill text-uppercase" style="background: var(--primary-maroon); border: none;"><i class="fas fa-paper-plane me-1"></i> Submeter Processo</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <!-- TAB 2: TRACK STATUS -->
                    <?php else: ?>
                        <div class="card card-custom bg-white p-5 border-top border-4 mb-4" style="border-top-color: var(--primary-gold) !important;">
                            <h3 class="font-libre fw-bold text-dark mb-4 text-center"><i class="fas fa-search-location text-warning me-2"></i> Acompanhamento Online de Queixas</h3>
                            
                            <form method="GET" action="apresentar-reclamacao.php" class="mb-4">
                                <input type="hidden" name="tab" value="track">
                                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                                    <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-file-invoice text-muted"></i></span>
                                    <input type="text" name="track_process_id" class="form-control border-0 p-3" placeholder="Insira o número do seu processo (Ex: PROC-2026-881)" value="<?php echo htmlspecialchars($search_query); ?>" required>
                                    <button type="submit" class="btn btn-dark px-4 font-bold text-uppercase fw-bold" style="background: var(--primary-maroon); border:0;">Pesquisar</button>
                                </div>
                            </form>

                            <?php if ($tracked_process): ?>
                                <!-- Process details -->
                                <div class="border rounded-4 p-4 mt-5 bg-light">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4 flex-wrap gap-2">
                                        <div>
                                            <div class="x-small text-muted text-uppercase fw-bold">Número de Autos</div>
                                            <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($tracked_process['numero_processo']); ?></h4>
                                        </div>
                                        <div class="text-end">
                                            <div class="x-small text-muted text-uppercase fw-bold">Estado Deontológico</div>
                                            
                                            <?php 
                                            $status_labels = [
                                                'aberto' => ['label' => 'Aberto / Triagem da Secretaria', 'class' => 'bg-warning text-dark'],
                                                'instrucao' => ['label' => 'Em Instrução Deontológica', 'class' => 'bg-info text-white'],
                                                'julgamento' => ['label' => 'Em Fase de Julgamento', 'class' => 'bg-primary text-white'],
                                                'arquivado' => ['label' => 'Processo Arquivado', 'class' => 'bg-secondary text-white'],
                                                'sancionado' => ['label' => 'Procedimento Sancionatório Aplicado', 'class' => 'bg-danger text-white']
                                            ];
                                            $st = $status_labels[$tracked_process['status']] ?? ['label' => 'Aberto', 'class' => 'bg-warning'];
                                            ?>
                                            <span class="badge <?php echo $st['class']; ?> px-3 py-2 small"><?php echo strtoupper($st['label']); ?></span>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6 col-12">
                                            <div class="x-small text-muted text-uppercase fw-bold">Advogado Visado</div>
                                            <div class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($tracked_process['advogado_nome']); ?></div>
                                        </div>
                                        
                                        <div class="col-md-6 col-12">
                                            <div class="x-small text-muted text-uppercase fw-bold">Data de Participação</div>
                                            <div class="fw-bold text-dark"><i class="far fa-calendar-alt text-muted me-1"></i> <?php echo date('d/m/Y', strtotime($tracked_process['data_abertura'])); ?></div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="x-small text-muted text-uppercase fw-bold mb-2">Resposta Oficial da Ordem</div>
                                        <div class="bg-white p-3 rounded-3 border text-dark shadow-sm small" style="line-height: 1.6; border-left: 5px solid var(--primary-gold) !important;">
                                            <?php echo nl2br(htmlspecialchars($tracked_process['resposta_ordem'] ?: 'A comissão de instrução está a analisar a reclamação submetida. Brevemente haverá novos desenvolvimentos.')); ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($tracked_process['sancao_tipo'])): ?>
                                        <div class="alert alert-danger border-0 p-3 rounded-3 small"><i class="fas fa-balance-scale me-1"></i> <strong>Sanção Aplicada:</strong> <?php echo htmlspecialchars($tracked_process['sancao_tipo']); ?></div>
                                    <?php endif; ?>

                                    <?php if (!empty($tracked_process['historico_interno'])): ?>
                                        <?php $hist = json_decode($tracked_process['historico_interno'], true); ?>
                                        <?php if (is_array($hist)): ?>
                                            <div class="mt-4">
                                                <div class="x-small text-muted text-uppercase fw-bold mb-3">Histórico Deontológico do Processo</div>
                                                <div class="ps-2">
                                                    <?php foreach ($hist as $h): ?>
                                                        <div class="timeline-item">
                                                            <div class="x-small text-muted fw-bold"><?php echo date('d/m/Y H:i', strtotime($h['data'])); ?></div>
                                                            <div class="small text-dark fw-bold"><?php echo htmlspecialchars($h['evento']); ?></div>
                                                            <div class="x-small text-muted opacity-75">Operador: <?php echo htmlspecialchars($h['operador']); ?></div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
