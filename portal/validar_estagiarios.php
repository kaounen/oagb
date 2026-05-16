<?php
session_start();
if(!isset($_SESSION['lawyer_id']) || $_SESSION['member_type'] != 'advogado') { header("Location: index.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];

// Handle Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_id'])) {
    $vid = $_POST['validate_id'];
    $status = $_POST['status'];
    $obs = $_POST['observacoes'];
    $rel_firma = $_POST['relatorio_firma'] ?? '';
    $nota_interna = $_POST['nota_interna'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE gestao_estagio_relatorios SET status = ?, observacoes = ?, relatorio_firma = ?, data_validacao = NOW() WHERE id = ? AND orientador_id = ?");
    $stmt->execute([$status, $obs, $rel_firma, $vid, $lid]);
    
    // Se houver nota interna, registar na tabela de interações
    if(!empty($nota_interna)) {
        $stmt = $pdo->prepare("INSERT INTO gestao_estagio_interacoes (relatorio_id, autor_id, autor_tipo, tipo, mensagem) VALUES (?, ?, 'advogado', 'nota_interna', ?)");
        $stmt->execute([$vid, $lid, $nota_interna]);
    }
    
    // Log
    try {
        require_once __DIR__ . '/../admin/includes/LogHelper.php';
        LogHelper::log($pdo, 'INTERN_REPORT_VALIDATE', "Validou relatório ID $vid com estado $status. Notas e Relatório de Firma incluídos.", 'gestao_estagio_relatorios', $vid);
    } catch(Exception $e) {}
    
    header("Location: validar_estagiarios.php?success=1"); exit;
}

// Handle Linkage Acceptance/Rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_link'])) {
    $est_id = $_POST['estagiario_id'];
    $action = $_POST['action_link']; // 'accept' or 'reject'
    $motivo = $_POST['motivo_recusa'] ?? '';
    
    if ($action === 'accept') {
        $stmt = $pdo->prepare("UPDATE advogados_estagiarios SET status = 'ativo', data_resposta_vinculo = NOW() WHERE id = ? AND orientador_id = ?");
        $stmt->execute([$est_id, $lid]);
        
        // Fetch intern info for email
        $est = $pdo->prepare("SELECT nome_completo, email FROM advogados_estagiarios WHERE id = ?");
        $est->execute([$est_id]);
        $intern = $est->fetch();
        
        // Send Email Proof Notification
        require_once __DIR__ . '/../admin/includes/NotifyHelper.php';
        $subject = "Confirmação de Vínculo de Estágio - OAGB";
        $body = "<h2>Olá, {$intern['nome_completo']}</h2>
                 <p>O seu pedido de vinculação de estágio foi <strong>ACEITE</strong> formalmente pelo seu patrono.</p>
                 <p>Pode agora descarregar o seu comprovativo oficial de vínculo através do link abaixo:</p>
                 <p><a href='http://{$_SERVER['HTTP_HOST']}/oagb/portal/comprovativo_vinculo_publico.php?token=" . md5($est_id) . "&id=$est_id' style='background:#B1A276; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>DESCARREGAR COMPROVATIVO</a></p>
                 <br><p>Atenciosamente,<br>Conselho Nacional da OAGB</p>";
        
        NotifyHelper::sendEmail($pdo, $intern['email'], $subject, $body);
        
        // Send Email to Lawyer (Patron)
        $law = $pdo->prepare("SELECT nome_completo, email FROM advogados WHERE id = ?");
        $law->execute([$lid]);
        $lawyer = $law->fetch();
        
        $subject_law = "Cópia de Comprovativo de Vínculo - OAGB";
        $body_law = "<h2>Olá, Dr(a). {$lawyer['nome_completo']}</h2>
                 <p>Este é o seu registo pessoal da vinculação do estagiário <strong>{$intern['nome_completo']}</strong>.</p>
                 <p>O vínculo foi formalizado com sucesso e o estagiário já foi notificado.</p>
                 <p>Pode aceder ao comprovativo oficial sempre que desejar através do link:</p>
                 <p><a href='http://{$_SERVER['HTTP_HOST']}/oagb/portal/comprovativo_vinculo.php?id=$est_id' style='background:#111923; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>VER COMPROVATIVO NO PORTAL</a></p>
                 <br><p>Atenciosamente,<br>Conselho Nacional da OAGB</p>";
        
        NotifyHelper::sendEmail($pdo, $lawyer['email'], $subject_law, $body_law);
        
        $msg = "Aceitou a vinculação do estagiário ID $est_id. Notificações enviadas a ambas as partes.";
    } else {
        $stmt = $pdo->prepare("UPDATE advogados_estagiarios SET status = 'cancelado', data_resposta_vinculo = NOW(), motivo_recusa = ? WHERE id = ? AND orientador_id = ?");
        $stmt->execute([$motivo, $est_id, $lid]);
        $msg = "Rejeitou a vinculação do estagiário ID $est_id. Motivo: $motivo";
    }
    
    try {
        require_once __DIR__ . '/../admin/includes/LogHelper.php';
        LogHelper::log($pdo, 'INTERN_LINK_RESPONSE', $msg, 'advogados_estagiarios', $est_id);
    } catch(Exception $e) {}
    
    header("Location: validar_estagiarios.php?success=link_updated"); exit;
}

// Fetch Pending/All Reports
$stmt = $pdo->prepare("SELECT r.*, e.nome_completo as estagiario_name, e.numero_registo as estagiario_registo 
                       FROM gestao_estagio_relatorios r 
                       JOIN advogados_estagiarios e ON r.estagiario_id = e.id 
                       WHERE r.orientador_id = ? 
                       ORDER BY r.data_submissao DESC");
$stmt->execute([$lid]);
$relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch All Interns linked to this Lawyer
$stmt = $pdo->prepare("SELECT * FROM advogados_estagiarios WHERE orientador_id = ? ORDER BY nome_completo ASC");
$stmt->execute([$lid]);
$meus_estagiarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determinar aba ativa
$active_tab = $_GET['tab'] ?? 'relatorios';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estágios | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; --success-green: #2ecc71; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; }
        .hero-validate { background: var(--sidebar-dark); padding: 60px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .main-card { background: white; border-radius: 24px; padding: 0; border: none; box-shadow: 0 15px 45px rgba(0,0,0,0.06); margin-top: -50px; overflow: hidden; }
        
        .nav-tabs-premium { background: #f1f3f5; padding: 10px 20px 0; border: none; }
        .nav-tabs-premium .nav-link { 
            border: none; color: #666; font-weight: 700; padding: 15px 25px; 
            border-radius: 12px 12px 0 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;
            transition: 0.3s;
        }
        .nav-tabs-premium .nav-link:hover { background: rgba(0,0,0,0.03); }
        .nav-tabs-premium .nav-link.active { background: white; color: var(--sidebar-dark); position: relative; }
        .nav-tabs-premium .nav-link.active::after { 
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--primary-gold); 
        }

        .intern-row { transition: 0.3s; border-radius: 12px; margin-bottom: 10px; }
        .intern-row:hover { background: #fcfcfc; transform: translateX(5px); }
        .avatar-circle { 
            width: 45px; height: 45px; background: #eee; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; font-weight: 700; color: #555;
        }
        .status-badge { font-size: 0.65rem; font-weight: 800; padding: 5px 12px; border-radius: 50px; letter-spacing: 0.5px; }
        .btn-validate { background: var(--sidebar-dark); color: white; border-radius: 50px; font-weight: 700; font-size: 0.75rem; padding: 8px 20px; transition: 0.3s; }
        .btn-validate:hover { background: var(--primary-gold); color: var(--sidebar-dark); }
        
        .empty-state { padding: 80px 0; opacity: 0.6; }
    </style>
</head>
<body>

    <header class="hero-validate">
        <div class="container d-flex justify-content-between align-items-center animate__animated animate__fadeInDown">
            <div>
                <h2 class="fw-bold mb-1">Centro de Monitoria de Estágios</h2>
                <p class="mb-0 opacity-75 small text-uppercase letter-spacing-1">Gestão de Percurso e Validação de Competências</p>
            </div>
            <div class="text-end">
                <div class="mt-2">
                    <a href="index.php" class="text-white text-decoration-none opacity-50 x-small fw-bold"><i class="fas fa-chevron-left me-1"></i> VOLTAR AO PAINEL</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 animate__animated animate__fadeIn">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <div class="fw-bold">Operação realizada com sucesso!</div>
                        <div class="small opacity-75">O vínculo foi atualizado e as notificações foram enviadas.</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="main-card animate__animated animate__fadeInUp">
            <ul class="nav nav-tabs nav-tabs-premium" id="estagioTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?php echo $active_tab == 'relatorios' ? 'active' : ''; ?>" id="relatorios-tab" data-bs-toggle="tab" data-bs-target="#relatorios" type="button">
                        <i class="fas fa-file-signature me-2"></i> Relatórios de Progresso
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo $active_tab == 'estagiarios' ? 'active' : ''; ?>" id="estagiarios-tab" data-bs-toggle="tab" data-bs-target="#estagiarios" type="button">
                        <i class="fas fa-users me-2"></i> Meus Estagiários
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4 p-md-5" id="estagioTabsContent">
                <!-- Tab 1: Relatórios -->
                <div class="tab-pane fade <?php echo $active_tab == 'relatorios' ? 'show active' : ''; ?>" id="relatorios" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 text-dark">Documentos para Validação</h5>
                        <div class="small text-muted">A aguardar revisão técnica</div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="x-small text-uppercase text-muted border-bottom">
                                    <th class="p-3">Data de Entrega</th>
                                    <th class="p-3">Estagiário</th>
                                    <th class="p-3">Conteúdo</th>
                                    <th class="p-3 text-center">Estado</th>
                                    <th class="p-3 text-end">Acção</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($relatorios)): ?>
                                    <tr><td colspan="5" class="empty-state text-center"><i class="fas fa-check-double fa-3x mb-3 d-block opacity-25"></i>Tudo em dia! Não existem relatórios pendentes.</td></tr>
                                <?php else: ?>
                                    <?php foreach($relatorios as $r): ?>
                                        <tr class="intern-row">
                                            <td class="p-3 small"><?php echo date('d/m/Y', strtotime($r['data_submissao'])); ?></td>
                                            <td class="p-3">
                                                <div class="fw-bold"><?php echo $r['estagiario_name']; ?></div>
                                                <div class="x-small opacity-50"><?php echo $r['estagiario_registo']; ?></div>
                                            </td>
                                            <td class="p-3">
                                                <a href="../uploads/estagio/relatorios/<?php echo $r['ficheiro_pdf']; ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold x-small">
                                                    <i class="fas fa-file-pdf me-1"></i> VER RELATÓRIO
                                                </a>
                                            </td>
                                            <td class="p-3 text-center">
                                                <?php if($r['status'] == 'validado'): ?>
                                                    <span class="status-badge bg-success-subtle text-success border border-success-subtle">VALIDADO</span>
                                                <?php elseif($r['status'] == 'pendente'): ?>
                                                    <span class="status-badge bg-warning-subtle text-warning border border-warning-subtle">PENDENTE</span>
                                                <?php elseif($r['status'] == 'revisao'): ?>
                                                    <span class="status-badge bg-info-subtle text-info border border-info-subtle">REVISÃO</span>
                                                <?php else: ?>
                                                    <span class="status-badge bg-danger-subtle text-danger border border-danger-subtle">RECUSADO</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-3 text-end">
                                                <?php if($r['status'] == 'pendente' || $r['status'] == 'revisao'): ?>
                                                    <button class="btn btn-validate" data-bs-toggle="modal" data-bs-target="#valModal<?php echo $r['id']; ?>">AVALIAR AGORA</button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light border rounded-pill x-small px-3" disabled>CONCLUÍDO</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                                          <?php /* Modal moved to end of file for better accessibility */ ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Meus Estagiários -->
                <div class="tab-pane fade <?php echo $active_tab == 'estagiarios' ? 'show active' : ''; ?>" id="estagiarios" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 text-dark">Membros sob Orientação</h5>
                        <div class="small text-muted">Acompanhamento de percurso profissional</div>
                    </div>

                    <div class="row g-4">
                        <?php 
                        $pendentes_vinculo = array_filter($meus_estagiarios, function($e) { return $e['status'] == 'pendente_aceitacao'; });
                        $ativos_vinculo = array_filter($meus_estagiarios, function($e) { return $e['status'] != 'pendente_aceitacao'; });
                        ?>

                        <?php if(!empty($pendentes_vinculo)): ?>
                            <div class="col-12 mb-2">
                                <h6 class="text-warning fw-bold small text-uppercase"><i class="fas fa-exclamation-circle me-1"></i> Pedidos de Vinculação Pendentes</h6>
                                <hr class="mt-1 opacity-10">
                            </div>
                            <?php foreach($pendentes_vinculo as $est): ?>
                                <div class="col-lg-6">
                                    <div class="card border-0 bg-warning-subtle rounded-4 p-4 h-100 shadow-sm border-start border-4 border-warning">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-circle me-3 bg-white shadow-sm"><?php echo substr($est['nome_completo'], 0, 1); ?></div>
                                            <div>
                                                <h6 class="fw-bold mb-0"><?php echo $est['nome_completo']; ?></h6>
                                                <div class="x-small text-muted"><?php echo $est['numero_registo']; ?></div>
                                            </div>
                                        </div>
                                        <p class="small text-dark opacity-75 mb-3">A Ordem iniciou um pedido de vinculação deste estagiário ao seu perfil. Deseja aceitar a orientação?</p>
                                        <form method="POST">
                                            <input type="hidden" name="estagiario_id" value="<?php echo $est['id']; ?>">
                                            <div class="mb-3" id="rejectReason<?php echo $est['id']; ?>" style="display:none;">
                                                <label class="x-small fw-bold text-danger">MOTIVO DA RECUSA</label>
                                                <textarea name="motivo_recusa" class="form-control form-control-sm border-danger-subtle bg-white" rows="2" placeholder="Justifique brevemente para a Ordem..."></textarea>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" name="action_link" value="accept" class="btn btn-success btn-sm rounded-pill px-4 fw-bold flex-grow-1">ACEITAR</button>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold flex-grow-1" onclick="toggleReject(<?php echo $est['id']; ?>)">CANCELAR</button>
                                            </div>
                                            <button type="submit" name="action_link" value="reject" id="btnReject<?php echo $est['id']; ?>" style="display:none;" class="btn btn-danger btn-sm w-100 rounded-pill mt-2 fw-bold">CONFIRMAR RECUSA</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <script>
                            function toggleReject(id) {
                                const area = document.getElementById('rejectReason' + id);
                                const btn = document.getElementById('btnReject' + id);
                                area.style.display = area.style.display === 'none' ? 'block' : 'none';
                                btn.style.display = btn.style.display === 'none' ? 'block' : 'none';
                            }
                            </script>
                            <div class="col-12 mt-4">
                                <h6 class="text-muted fw-bold small text-uppercase">Corpo de Estagiários Ativos</h6>
                                <hr class="mt-1 opacity-10">
                            </div>
                        <?php endif; ?>

                        <?php 
                        $recusados_vinculo = array_filter($meus_estagiarios, function($e) { return $e['status'] == 'cancelado'; });
                        $aceites_historico = array_filter($meus_estagiarios, function($e) { return ($e['status'] == 'ativo' || $e['status'] == 'concluido') && !empty($e['data_resposta_vinculo']); });
                        ?>

                        <?php if(empty($ativos_vinculo) && empty($pendentes_vinculo) && empty($recusados_vinculo)): ?>
                            <div class="col-12 empty-state text-center py-5">
                                <i class="fas fa-user-slash fa-3x mb-3 d-block opacity-25"></i>
                                Não tem estagiários vinculados ao seu perfil.
                            </div>
                        <?php else: ?>
                            <?php foreach($ativos_vinculo as $est): ?>
                                <div class="col-lg-6">
                                    <div class="card border-0 bg-light rounded-4 p-4 h-100 transition-hover shadow-sm border-start border-4 border-gold">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3 bg-white shadow-sm"><?php echo substr($est['nome_completo'], 0, 1); ?></div>
                                                <div>
                                                    <h6 class="fw-bold mb-0"><?php echo $est['nome_completo']; ?></h6>
                                                    <div class="x-small text-muted"><?php echo $est['numero_registo']; ?></div>
                                                </div>
                                            </div>
                                            <span class="status-badge bg-success text-white"><?php echo strtoupper($est['status']); ?></span>
                                        </div>
                                        
                                        <div class="row g-3 small mb-4">
                                            <div class="col-6">
                                                <div class="text-muted x-small text-uppercase">Data de Início</div>
                                                <div class="fw-bold"><?php echo date('d/m/Y', strtotime($est['data_inicio_estagio'])); ?></div>
                                            </div>
                                            <div class="col-6 text-end">
                                                <div class="text-muted x-small text-uppercase">Aceite em</div>
                                                <div class="fw-bold text-success"><?php echo $est['data_resposta_vinculo'] ? date('d/m/Y', strtotime($est['data_resposta_vinculo'])) : 'N/A'; ?></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="text-muted x-small text-uppercase">Email</div>
                                                <div class="fw-bold"><i class="far fa-envelope me-1 opacity-50"></i> <?php echo $est['email']; ?></div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="perfil_estagiario.php?id=<?php echo $est['id']; ?>" class="btn btn-white btn-sm border fw-bold rounded-pill py-2">VER DOSSIER COMPLETO <i class="fas fa-external-link-alt ms-1 x-small"></i></a>
                                            <?php if($est['status'] == 'ativo'): ?>
                                                <a href="comprovativo_vinculo.php?id=<?php echo $est['id']; ?>" target="_blank" class="btn btn-outline-dark btn-sm fw-bold rounded-pill py-2 x-small"><i class="fas fa-certificate me-1"></i> COMPROVATIVO DE VÍNCULO</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="col-12 mt-5">
                                <h6 class="text-muted fw-bold small text-uppercase opacity-75"><i class="fas fa-history me-1"></i> Historial de Decisões Institucionais</h6>
                                <hr class="mt-1 opacity-10">
                            </div>

                            <div class="col-12">
                                <div class="row g-3">
                                    <?php foreach($aceites_historico as $est): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-white border rounded-4 shadow-sm h-100">
                                                <div class="avatar-circle me-3 bg-success-subtle text-success small"><i class="fas fa-check"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold small mb-0"><?php echo $est['nome_completo']; ?></div>
                                                    <div class="x-small text-muted">Vínculo aceite a <?php echo date('d/m/Y', strtotime($est['data_resposta_vinculo'])); ?></div>
                                                </div>
                                                <div class="text-end">
                                                    <a href="comprovativo_vinculo.php?id=<?php echo $est['id']; ?>" target="_blank" class="btn btn-sm btn-light rounded-pill x-small px-3">CERTIFICADO</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php foreach($recusados_vinculo as $est): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-white border rounded-4 shadow-sm h-100 opacity-75">
                                                <div class="avatar-circle me-3 bg-danger-subtle text-danger small"><i class="fas fa-times"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold small mb-0"><?php echo $est['nome_completo']; ?></div>
                                                    <div class="x-small text-muted">Vínculo recusado a <?php echo date('d/m/Y', strtotime($est['data_resposta_vinculo'])); ?></div>
                                                    <div class="x-small italic text-danger mt-1">"<?php echo $est['motivo_recusa']; ?>"</div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals Section (Moved here to avoid z-index/interaction issues) -->
    <?php foreach($relatorios as $r): ?>
        <?php if($r['status'] == 'pendente' || $r['status'] == 'revisao'): ?>
        <div class="modal fade" id="valModal<?php echo $r['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-dark text-white p-4">
                        <h5 class="modal-title fw-bold">Parecer Técnico de Estágio</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="validate_id" value="<?php echo $r['id']; ?>">
                        <div class="modal-body p-4">
                            <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="x-small text-muted text-uppercase fw-bold">Estagiário</div>
                                    <div class="fw-bold"><?php echo $r['estagiario_name']; ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="x-small text-muted text-uppercase fw-bold">Cédula</div>
                                    <div class="fw-bold"><?php echo $r['estagiario_registo']; ?></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-dark">Decisão de Acompanhamento</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="status" id="approve<?php echo $r['id']; ?>" value="validado" checked>
                                        <label class="btn btn-outline-success w-100 py-3 fw-bold rounded-3 x-small" for="approve<?php echo $r['id']; ?>">
                                            <i class="fas fa-check-circle d-block mb-1"></i> APROVAR
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="status" id="rev<?php echo $r['id']; ?>" value="revisao">
                                        <label class="btn btn-outline-warning w-100 py-3 fw-bold rounded-3 x-small" for="rev<?php echo $r['id']; ?>">
                                            <i class="fas fa-sync d-block mb-1"></i> REVISÃO
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" class="btn-check" name="status" id="reject<?php echo $r['id']; ?>" value="rejeitado">
                                        <label class="btn btn-outline-danger w-100 py-3 fw-bold rounded-3 x-small" for="reject<?php echo $r['id']; ?>">
                                            <i class="fas fa-times-circle d-block mb-1"></i> RECUSAR
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Parecer do Patrono (Visível ao Estagiário)</label>
                                <textarea name="observacoes" class="form-control border-0 bg-light p-3" rows="3" placeholder="Feedback para o estagiário..."><?php echo $r['observacoes']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Relatório da Firma / Avaliação de Mérito</label>
                                <textarea name="relatorio_firma" class="form-control border-0 bg-light p-3" rows="3" placeholder="Avaliação formal da sociedade de advogados..."><?php echo $r['relatorio_firma']; ?></textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark">Notas Internas (Apenas para meu controlo)</label>
                                <textarea name="nota_interna" class="form-control border-0 bg-light p-3 border-start border-warning border-4" rows="2" placeholder="Notas privadas sobre este relatório..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer p-4 pt-0 border-0">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">ASSINAR E REGISTAR VALIDAÇÃO</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Essential Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
