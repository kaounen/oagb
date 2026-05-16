<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }

// Prevent caching to avoid "state change" issues when going back
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';

// Fetch Member Info
$table = ($mtype == 'estagiario') ? 'advogados_estagiarios' : 'advogados';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$lid]);
$lawyer = $stmt->fetch();

// Fetch Financial Config
$stmt = $pdo->query("SELECT chave, valor FROM finan_config");
$fconfig = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$quota_valor = ($mtype == 'estagiario') ? ($fconfig['quota_estagiario'] ?? 5000) : ($fconfig['quota_advogado'] ?? 15000);

// Fetch Commissions
$stmt = $pdo->prepare("SELECT c.nome, m.cargo 
                       FROM gestao_comissoes_membros m 
                       JOIN gestao_comissoes c ON m.comissao_id = c.id 
                       WHERE m.advogado_id = ? AND ? = 'advogado'");
$stmt->execute([$lid, $mtype]);
$my_commissions = $stmt->fetchAll();

// Check Regularized Status (Using valid_until)
$tipo_quota_id = ($mtype == 'estagiario') ? 2 : 1; 
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND membro_tipo = ? AND tipo_pagamento_id = ? 
                       AND status = 'confirmado' AND valid_until >= CURDATE()");
$stmt->execute([$lid, $mtype, $tipo_quota_id]);
$is_regularized = ($stmt->fetchColumn() > 0);
$regularizado = $is_regularized;

// Fetch History
$stmt = $pdo->prepare("SELECT * FROM finan_pagamentos WHERE advogado_id = ? ORDER BY data_pagamento DESC LIMIT 5");
$stmt->execute([$lid]);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Reservada | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#B1A276">
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>
    <style>
        :root { --primary-gold: #B1A276; --bg-main: #f5f6f8; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: var(--bg-main); overflow-x: hidden; }
        .portal-header { background: var(--sidebar-dark); padding: 40px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .lawyer-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 40px; }
        .avatar-box { width: 100px; height: 100px; background: var(--primary-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 2.5rem; color: #111923; margin-bottom: 20px; }
        .badge-status { border-radius: 50px; padding: 8px 25px; font-weight: 700; font-size: 0.75rem; letter-spacing: 1px; }
        .quick-box { background: white; border-radius: 16px; padding: 25px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: all 0.3s; height: 100%; border: 1px solid #f0f0f0; }
        .quick-box:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: var(--primary-gold); }
        .btn-portal-action { background: var(--primary-gold); color: #111923; font-weight: 700; padding: 12px 25px; border-radius: 10px; border: none; transition: 0.3s; text-decoration: none; display: inline-block; width: 100%; text-align: center; }
        .btn-portal-action:hover { background: #111923; color: white; }
        .footer-portal { padding: 40px 0; font-size: 0.8rem; opacity: 0.5; color: #111923; }
    </style>
</head>
<body>

    <header class="portal-header">
        <div class="container d-flex justify-content-between align-items-center">
            <img src="<?php echo ROOT_URL; ?>/img/LogoOA.png" alt="OAGB" style="height: 75px;">
            <div class="d-flex gap-4">
                <a href="logout.php" class="text-white text-decoration-none opacity-50 small fw-bold">SAIR DO PORTAL <i class="fas fa-sign-out-alt ms-1"></i></a>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="lawyer-card text-center mb-4">
                    <div class="avatar-box mx-auto"><?php echo substr($lawyer['nome_completo'], 0, 1); ?></div>
                    <div class="badge bg-dark text-white mb-2 x-small border-dashed px-3"><?php echo strtoupper($mtype); ?></div>
                    <h4 class="fw-bold mb-1"><?php echo $lawyer['nome_completo']; ?></h4>
                    <div class="text-muted small mb-4">Cédula Profissional: <strong><?php echo $lawyer['numero_registo']; ?></strong></div>
                    
                    <?php if($lawyer['password'] === null): ?>
                        <div class="alert alert-warning border-0 small p-3 text-start mb-4">
                            <i class="fas fa-shield-alt me-2"></i> <strong>Segurança:</strong> Está a usar uma senha temporária. Recomendamos que defina uma senha segura.
                            <a href="perfil.php" class="btn btn-sm btn-dark w-100 mt-2 fw-bold">DEFINIR SENHA</a>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2 mb-4">
                        <a href="financeiro.php" class="text-decoration-none">
                            <?php if($regularizado): ?>
                                <span class="badge-status bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check-circle me-1"></i> QUOTAS REGULARIZADAS</span>
                            <?php else: ?>
                                <span class="badge-status bg-danger-subtle text-danger border border-danger-subtle animate__animated animate__pulse animate__infinite"><i class="fas fa-exclamation-triangle me-1"></i> QUOTAS EM ATRASO</span>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <hr class="my-4 opacity-50">
                    
                    <div class="row text-start g-3 small">
                        <div class="col-12"><i class="fas fa-envelope text-muted me-2 opacity-50"></i> <?php echo $lawyer['email']; ?></div>
                        <div class="col-12"><i class="fas fa-phone text-muted me-2 opacity-50"></i> <?php echo $lawyer['telefone']; ?></div>
                        <div class="col-12"><i class="fas fa-map-marker-alt text-muted me-2 opacity-50"></i> <?php echo $lawyer['localidade']; ?></div>
                    </div>
                    
                    <a href="perfil.php" class="btn btn-outline-secondary w-100 mt-4 rounded-pill small fw-bold py-2"><i class="fas fa-user-cog me-1"></i> CONFIGURAR PERFIL</a>
                </div>
            </div>

            <div class="col-lg-8" style="margin-top: 40px;">
                <!-- Welcome Section -->
                <div class="mb-5 animate__animated animate__fadeIn">
                    <div class="badge bg-primary mb-2">v2.1-TESOURARIA</div>
                    <h2 class="fw-bold text-dark">Olá, <?php echo explode(' ', $lawyer['nome_completo'])[0]; ?>! 👋</h2>
                    <p class="text-muted">Bem-vindo ao seu painel digital. Hoje é <?php 
                        $d = date('d \d\e F \d\e Y');
                        $m_en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                        $m_pt = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                        echo str_replace($m_en, $m_pt, $d);
                    ?>.</p>
                </div>

                <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Atualização do Portal (v2.1)</h6>
                            <p class="small mb-0 opacity-75">O novo sistema de pagamentos e tesouraria já está ativo abaixo.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- NEW: Payments Card -->
                    <div class="col-md-6">
                        <div class="quick-box border-warning-subtle shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <i class="fas fa-wallet fa-2x text-warning"></i>
                                <?php if(!$regularizado): ?>
                                    <span class="badge bg-danger animate__animated animate__flash animate__infinite">PENDENTE</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold">Tesouraria & Quotas</h5>
                            <p class="text-muted small">Consulte o seu histórico, gerencie as suas quotas e efetue pagamentos via Mobile Money.</p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="financeiro.php" class="btn btn-portal-action bg-warning text-dark border-0 flex-grow-1">PAGAR / VER EXTRATO</a>
                                <?php if(!$regularizado): ?>
                                    <form method="POST" action="financeiro.php" class="d-flex gap-2 w-100 mt-2">
                                        <input type="hidden" name="pay_advance" value="1">
                                        <button type="submit" name="num_months" value="3" class="btn btn-sm btn-outline-warning text-dark fw-bold border-warning-subtle flex-grow-1">PAGAR TRIMESTRE</button>
                                        <button type="submit" name="num_months" value="12" class="btn btn-sm btn-outline-warning text-dark fw-bold border-warning-subtle flex-grow-1">PAGAR ANUAL</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-box">
                            <i class="fas fa-certificate fa-2x text-success mb-3"></i>
                            <h5 class="fw-bold">Emissão de Certidões</h5>
                            <p class="text-muted small">Descarregue a sua Certidão de Nada a Declarar assinada digitalmente de forma imediata.</p>
                            <?php if($regularizado): ?>
                                <a href="certidao.php" class="btn btn-portal-action">DESCARREGAR CERTIDÃO</a>
                            <?php else: ?>
                                <button class="btn btn-portal-action bg-light text-muted border" disabled><i class="fas fa-lock me-2"></i> ACESSO BLOQUEADO</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-box">
                            <?php if($mtype == 'estagiario'): ?>
                                <i class="fas fa-file-upload fa-2x text-primary mb-3"></i>
                                <h5 class="fw-bold">Documentos / Trabalhos</h5>
                                <p class="text-muted small">Submeta os seus relatórios mensais, trabalhos científicos ou artigos para validação.</p>
                                <a href="submeter_relatorio.php" class="btn btn-portal-action">SUBMETER AGORA</a>
                            <?php else: ?>
                                <i class="fas fa-user-graduate fa-2x text-primary mb-3"></i>
                                <h5 class="fw-bold">Processo de Estágio</h5>
                                <p class="text-muted small">Acompanhe as suas etapas, horas de formação e submeta relatórios de progresso.</p>
                                <a href="validar_estagiarios.php" class="btn btn-portal-action">ABRIR MONITORIA</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-box">
                            <div class="small fw-bold text-muted opacity-50 text-uppercase mb-3">Minhas Comissões</div>
                            <?php if(empty($my_commissions)): ?>
                                <div class="text-muted small py-2">Não está designado para nenhuma comissão.</div>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach($my_commissions as $comm): ?>
                                        <li class="mb-2 d-flex justify-content-between align-items-center">
                                            <span class="small fw-bold text-dark"><?php echo $comm['nome']; ?></span>
                                            <span class="badge bg-light text-muted border x-small"><?php echo strtoupper($comm['cargo']); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="quick-box">
                            <i class="fas fa-file-signature fa-2x text-danger mb-3"></i>
                            <h5 class="fw-bold">Livro de Actas Digital</h5>
                            <p class="text-muted small">Consulte as actas oficiais, deliberações e registros de assembleias da Ordem partilhadas consigo.</p>
                            <a href="actas.php" class="btn btn-portal-action bg-danger-subtle text-danger border-danger-subtle">ACEDER ÀS ACTAS</a>
                        </div>
                    </div>

                    <?php if($mtype == 'estagiario'): ?>
                    <div class="col-md-6">
                        <div class="quick-box">
                            <div class="small fw-bold text-muted opacity-50 text-uppercase mb-3">Estado do Estágio</div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold">Fase: <?php echo strtoupper($lawyer['fase_estagio'] ?? 'Instrução'); ?></span>
                                <span class="small fw-bold text-primary">Ativo</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 50%"></div>
                            </div>
                            <a href="estagio.php" class="btn btn-sm btn-outline-primary w-100 fw-bold py-2 rounded-3 text-uppercase">Ver Processo de Estágio</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card border-0 shadow-sm p-5 mt-5 bg-white">
                    <h5 class="fw-bold mb-4">Últimas Movimentações</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 text-muted small">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 p-3">Data</th>
                                    <th class="border-0 p-3">Descrição / Referência</th>
                                    <th class="border-0 p-3">Valor</th>
                                    <th class="border-0 p-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($history)): ?>
                                    <tr><td colspan="4" class="text-center py-4">Nenhum pagamento registado no portal.</td></tr>
                                <?php else: ?>
                                    <?php foreach($history as $h): ?>
                                        <tr>
                                            <td class="p-3"><?php echo date('d/m/Y H:i', strtotime($h['data_pagamento'])); ?></td>
                                            <td class="p-3 fw-bold text-dark"><?php echo $h['metodo_pagamento'] == 'transferencia' ? 'TRF' : 'DEP'; ?> #<?php echo $h['id']; ?></td>
                                            <td class="p-3"><?php echo number_format($h['valor_pago'], 0, ',', '.'); ?> CFA</td>
                                            <td class="p-3 text-center">
                                                <span class="badge py-2 px-3 <?php echo $h['status'] == 'confirmado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>">
                                                    <?php echo strtoupper($h['status']); ?>
                                                </span>
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

    <footer class="container text-center footer-portal">
        &copy; <?php echo date('Y'); ?> Ordem dos Advogados da Guiné-Bissau. Todos os direitos reservados. | Desenvolvido pelo Departamento de TI.
    </footer>

</body>
</html>
