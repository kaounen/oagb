<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';

// Only fully qualified lawyers can claim public consultation requests
if ($mtype === 'estagiario') {
    exit("Acesso Restrito: Advogados Estagiários não podem aceitar consultas autónomas de mediação pública.");
}

$success = null;
$error = null;

// Check Regularized Status
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND membro_tipo = 'advogado' AND tipo_pagamento_id = 1 
                       AND status = 'confirmado' AND valid_until >= CURDATE()");
$stmt->execute([$lid]);
$is_regularized = ($stmt->fetchColumn() > 0);

if (!$is_regularized) {
    $error = "Acesso Suspenso: Para poder receber encaminhamentos de clientes e consultas públicas, a sua situação contributiva de quotas deve estar totalmente regularizada.";
}

// Handle Claiming/Accepting a Ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_ticket_id']) && $is_regularized) {
    $tid = (int)$_POST['claim_ticket_id'];
    
    // Double check that the ticket is still pending
    $stmt = $pdo->prepare("SELECT status FROM helpdesk_pedidos WHERE id = ?");
    $stmt->execute([$tid]);
    $t_status = $stmt->fetchColumn();
    
    if ($t_status === 'pendente') {
        $stmt = $pdo->prepare("UPDATE helpdesk_pedidos SET status = 'em_atendimento', advogado_designado_id = ? WHERE id = ?");
        $stmt->execute([$lid, $tid]);
        $success = "Consulta aceita com sucesso! Os contactos do cliente foram desbloqueados abaixo.";
    } else {
        $error = "Desculpe, este pedido de consulta já foi aceito por outro colega.";
    }
}

// Fetch all pending public consultation requests (Available for anyone regularized)
$stmt = $pdo->query("SELECT * FROM helpdesk_pedidos WHERE status = 'pendente' ORDER BY data_submissao DESC");
$pending_tickets = $stmt->fetchAll();

// Fetch all consultation tickets currently claimed by this specific lawyer
$stmt = $pdo->prepare("SELECT * FROM helpdesk_pedidos WHERE advogado_designado_id = ? ORDER BY data_submissao DESC");
$stmt->execute([$lid]);
$my_tickets = $stmt->fetchAll();

$page_title = "Mediação de Consultas e Clientes";
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
        .hero-consultas { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .con-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        
        .client-box { background: #fff; border-radius: 12px; border: 1px solid #eee; padding: 20px; transition: .3s; }
        .client-box:hover { border-color: var(--primary-gold); transform: translateY(-3px); }
        .badge-method { font-size: 0.65rem; font-weight: 700; padding: 4px 10px; border-radius: 50px; }
    </style>
</head>
<body>

    <header class="hero-consultas">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0"><i class="fas fa-handshake me-2"></i> Mediação de Clientes & Consultas</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="con-card">
            
            <?php if($success): ?>
                <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row g-5">
                <!-- Available incoming client matches -->
                <div class="col-lg-7 border-end">
                    <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">
                        <i class="fas fa-user-plus me-2 text-warning"></i> Consultas Públicas Disponíveis
                    </h5>
                    
                    <?php if(!$is_regularized): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                            <h6 class="fw-bold text-muted">Acesso Bloqueado</h6>
                            <p class="small text-muted px-4">Por favor, regularize as suas quotas mensais na secção de Tesouraria para visualizar pedidos de consultas de clientes.</p>
                        </div>
                    <?php elseif(empty($pending_tickets)): ?>
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h6 class="fw-bold">Nenhum pedido pendente</h6>
                            <p class="small">Todos os pedidos de consulta de cidadãos e investidores foram atendidos.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($pending_tickets as $ticket): ?>
                                <div class="col-12">
                                    <div class="client-box shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-dark text-white text-uppercase x-small px-3"><?php echo htmlspecialchars($ticket['categoria_caso']); ?></span>
                                            <span class="badge-method <?php echo ($ticket['tipo_atendimento'] === 'Online') ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle'; ?>">
                                                <i class="<?php echo ($ticket['tipo_atendimento'] === 'Online') ? 'fas fa-video' : 'fas fa-map-marker-alt'; ?> me-1"></i> <?php echo htmlspecialchars($ticket['tipo_atendimento']); ?>
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($ticket['nome_completo']); ?> (<?php echo htmlspecialchars($ticket['pais_residencia']); ?>)</h6>
                                        <p class="small text-muted mb-3"><?php echo htmlspecialchars(substr($ticket['descricao_caso'], 0, 180)); ?>...</p>
                                        
                                        <form method="POST" class="text-end">
                                            <input type="hidden" name="claim_ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill fw-bold px-4 py-2"><i class="fas fa-check me-1"></i> Aceitar e Contactar</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- My Claimed Consultations -->
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">
                        <i class="fas fa-folder-open me-2 text-primary"></i> As Minhas Mediações Activas
                    </h5>
                    
                    <?php if(empty($my_tickets)): ?>
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="far fa-folder fa-3x mb-3"></i>
                            <h6 class="fw-bold">Nenhum atendimento activo</h6>
                            <p class="small">Os clientes que aceitar atender serão listados aqui com os respectivos contactos desbloqueados.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($my_tickets as $my): ?>
                                <div class="col-12">
                                    <div class="p-3 border rounded-3 bg-light shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-success text-white x-small">EM ATENDIMENTO</span>
                                            <span class="x-small text-muted"><?php echo date('d/m/Y', strtotime($my['data_submissao'])); ?></span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($my['nome_completo']); ?></h6>
                                        <div class="x-small text-muted mb-3"><i class="fas fa-globe me-1"></i> País: <?php echo htmlspecialchars($my['pais_residencia']); ?></div>
                                        
                                        <div class="bg-white p-3 rounded border small mb-2">
                                            <div class="fw-bold text-dark mb-1">Descrição do Caso:</div>
                                            <p class="text-muted m-0" style="font-size: 0.8rem;"><?php echo htmlspecialchars($my['descricao_caso']); ?></p>
                                        </div>
                                        
                                        <div class="row g-2 pt-2 border-top border-light mt-2 text-start small">
                                            <div class="col-12"><i class="fas fa-phone text-muted me-2"></i> <a href="tel:<?php echo htmlspecialchars($my['telefone']); ?>" class="text-decoration-none fw-bold"><?php echo htmlspecialchars($my['telefone']); ?></a></div>
                                            <div class="col-12"><i class="fas fa-envelope text-muted me-2"></i> <a href="mailto:<?php echo htmlspecialchars($my['email']); ?>" class="text-decoration-none fw-bold"><?php echo htmlspecialchars($my['email']); ?></a></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
