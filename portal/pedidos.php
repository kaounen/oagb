<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';
$table = ($mtype == 'estagiario') ? 'advogados_estagiarios' : 'advogados';

$success = null;
$error = null;

// Fetch Lawyer Details
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$lid]);
$user = $stmt->fetch();

// Check Regularized Status
$tipo_quota_id = ($mtype == 'estagiario') ? 2 : 1; 
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND membro_tipo = ? AND tipo_pagamento_id = ? 
                       AND status = 'confirmado' AND valid_until >= CURDATE()");
$stmt->execute([$lid, $mtype, $tipo_quota_id]);
$is_regularized = ($stmt->fetchColumn() > 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = clean_input($_POST['tipo_requerimento'] ?? '');
    $detalhes = clean_input($_POST['detalhes'] ?? '');
    
    if (empty($tipo) || empty($detalhes)) {
        $error = "Por favor, selecione o tipo de pedido e preencha a justificação.";
    } else {
        try {
            $status = 'pendente';
            $doc_pdf = null;
            $qr_hash = null;
            
            // Automation: If the lawyer is regularized and requests an active standing declaration or regular quota certificate,
            // we immediately approve and generate the digital credentials!
            if ($is_regularized && ($tipo === 'Declaração de Inscrição' || $tipo === 'Certidão de Regularidade')) {
                $status = 'concluido';
                $qr_hash = strtoupper(substr(md5($lid . $tipo . time()), 0, 12));
                $doc_pdf = "declaração_digital_OAGB_" . $qr_hash . ".pdf";
            }
            
            $stmt = $pdo->prepare("INSERT INTO requerimentos_membros (advogado_id, tipo_requerimento, detalhes, status, qr_code_hash, documento_emitido_pdf) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$lid, $tipo, $detalhes, $status, $qr_hash, $doc_pdf]);
            
            $success = "Requerimento submetido com sucesso! ";
            if ($status === 'concluido') {
                $success .= "O seu documento digital foi emitido e assinado eletronicamente de forma instantânea.";
            } else {
                $success .= "A secretaria da Ordem procederá com a análise regulamentar do seu requerimento.";
            }
        } catch (Exception $e) {
            $error = "Ocorreu um erro ao submeter o seu requerimento. Por favor, tente novamente.";
        }
    }
}

// Fetch member request history
$stmt = $pdo->prepare("SELECT * FROM requerimentos_membros WHERE advogado_id = ? ORDER BY data_pedido DESC");
$stmt->execute([$lid]);
$pedidos = $stmt->fetchAll();

$page_title = "Meus Requerimentos e Declarações";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-requerimentos { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .req-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--sidebar-dark); }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #ddd; padding: 12px 15px; font-size: 0.9rem; }
        
        .badge-status { border-radius: 50px; font-size: 0.7rem; font-weight: 700; padding: 5px 12px; }
    </style>
</head>
<body>

    <header class="hero-requerimentos">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Requerimentos & Declarações</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="req-card">
            
            <?php if($success): ?>
                <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row g-5">
                <!-- Submission Intake Form -->
                <div class="col-lg-5 border-end">
                    <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">
                        <i class="fas fa-file-invoice me-2 text-warning"></i> Novo Requerimento
                    </h5>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Requerimento *</label>
                            <select name="tipo_requerimento" class="form-select" required>
                                <option value="">-- Selecione o Pedido --</option>
                                <option value="Declaração de Inscrição">Declaração de Inscrição Ativa</option>
                                <option value="Certidão de Regularidade">Certidão de Regularidade de Quotas</option>
                                <option value="2ª Via da Carteira">2ª Via da Carteira Profissional</option>
                                <option value="Pedido de Transferência">Transferência de Dados Cadastrais</option>
                                <option value="Apoio Institucional">Pedido de Apoio Institucional / Defesa</option>
                                <option value="Requerimento Geral">Outro Requerimento Geral</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Justificação / Detalhes Adicionais *</label>
                            <textarea name="detalhes" class="form-control" rows="6" placeholder="Justifique o seu pedido ou forneça detalhes adicionais para a secretaria..." required style="resize: none;"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold text-uppercase" style="background: var(--sidebar-dark); border: none;">Submeter Pedido</button>
                    </form>
                </div>
                
                <!-- Request Queue Table -->
                <div class="col-lg-7">
                    <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">
                        <i class="fas fa-list-ul me-2 text-primary"></i> Histórico de Protocolos
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle text-muted small">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 p-3">Protocolo</th>
                                    <th class="border-0 p-3">Tipo</th>
                                    <th class="border-0 p-3">Data</th>
                                    <th class="border-0 p-3 text-center">Estado</th>
                                    <th class="border-0 p-3 text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($pedidos)): ?>
                                    <tr><td colspan="5" class="text-center py-4">Nenhum requerimento registado na sua conta.</td></tr>
                                <?php else: ?>
                                    <?php foreach($pedidos as $req): ?>
                                        <tr>
                                            <td class="p-3 fw-bold text-dark">REQ-#<?php echo $req['id']; ?></td>
                                            <td class="p-3"><?php echo htmlspecialchars($req['tipo_requerimento']); ?></td>
                                            <td class="p-3"><?php echo date('d/m/Y', strtotime($req['data_pedido'])); ?></td>
                                            <td class="p-3 text-center">
                                                <?php if($req['status'] === 'concluido'): ?>
                                                    <span class="badge-status bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check-circle me-1"></i> CONCLUÍDO</span>
                                                <?php elseif($req['status'] === 'pendente'): ?>
                                                    <span class="badge-status bg-warning-subtle text-warning border border-warning-subtle"><i class="fas fa-clock me-1"></i> PENDENTE</span>
                                                <?php elseif($req['status'] === 'rejeitado'): ?>
                                                    <span class="badge-status bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-times-circle me-1"></i> REJEITADO</span>
                                                <?php else: ?>
                                                    <span class="badge-status bg-info-subtle text-info border border-info-subtle"><i class="fas fa-info-circle me-1"></i> <?php echo strtoupper($req['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-3 text-end">
                                                <?php if($req['status'] === 'concluido'): ?>
                                                    <!-- Link to standard digital document verification certificate directly -->
                                                    <a href="certidao.php" class="btn btn-sm btn-outline-success rounded-pill fw-bold" target="_blank"><i class="fas fa-download me-1"></i> Descarregar</a>
                                                <?php else: ?>
                                                    <span class="text-muted italic small">Em análise</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
