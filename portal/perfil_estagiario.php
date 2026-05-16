<?php
session_start();
if(!isset($_SESSION['lawyer_id']) || $_SESSION['member_type'] != 'advogado') { header("Location: index.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$est_id = $_GET['id'] ?? null;

if(!$est_id) { header("Location: validar_estagiarios.php"); exit; }

// Fetch Intern Info (Must belong to this lawyer)
$stmt = $pdo->prepare("SELECT * FROM advogados_estagiarios WHERE id = ? AND orientador_id = ?");
$stmt->execute([$est_id, $lid]);
$est = $stmt->fetch();

if(!$est) { die("Estagiário não encontrado ou sem permissão de acesso."); }

// Fetch Financial Status of Intern
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND membro_tipo = 'estagiario' AND status = 'confirmado' AND valid_until >= CURDATE()");
$stmt->execute([$est_id]);
$is_regularized = ($stmt->fetchColumn() > 0);

// Fetch Reports History
$stmt = $pdo->prepare("SELECT * FROM gestao_estagio_relatorios WHERE estagiario_id = ? ORDER BY data_submissao DESC");
$stmt->execute([$est_id]);
$relatorios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossier do Estagiário | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-dossier { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .profile-header { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        .avatar-lg { width: 80px; height: 80px; background: var(--primary-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 2rem; color: #111923; }
        .card-stat { background: white; border-radius: 15px; padding: 20px; border: 1px solid #eee; }
    </style>
</head>
<body>

    <header class="hero-dossier">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Dossier Profissional</h2>
            <a href="validar_estagiarios.php?tab=estagiarios" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="profile-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <div class="avatar-lg"><?php echo substr($est['nome_completo'], 0, 1); ?></div>
                </div>
                <div class="col-md">
                    <div class="badge bg-primary text-uppercase x-small mb-2">Estagiário Sob Orientação</div>
                    <h3 class="fw-bold mb-1"><?php echo $est['nome_completo']; ?></h3>
                    <div class="text-muted small">Cédula No: <strong><?php echo $est['numero_registo']; ?></strong> | Inscrito em <?php echo date('d/m/Y', strtotime($est['data_inicio_estagio'])); ?></div>
                </div>
                <div class="col-md-auto text-end">
                    <?php if($is_regularized): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-bold"><i class="fas fa-check-circle me-1"></i> QUOTAS EM DIA</span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> QUOTAS PENDENTES</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Informação de Contacto</h6>
                    <div class="small mb-3"><i class="fas fa-envelope text-muted me-2"></i> <?php echo $est['email']; ?></div>
                    <div class="small mb-3"><i class="fas fa-phone text-muted me-2"></i> <?php echo $est['telefone'] ?? 'Não registado'; ?></div>
                    <div class="small"><i class="fas fa-map-marker-alt text-muted me-2"></i> <?php echo $est['localidade'] ?? 'Bissau'; ?></div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3">Resumo de Percurso</h6>
                    <div class="card-stat mb-3">
                        <div class="x-small text-muted text-uppercase fw-bold">Fase Atual</div>
                        <div class="fw-bold text-primary"><?php echo strtoupper($est['fase_estagio'] ?? 'Instrução'); ?></div>
                    </div>
                    <div class="card-stat">
                        <div class="x-small text-muted text-uppercase fw-bold">Relatórios Validados</div>
                        <div class="fw-bold"><?php 
                            $validados = array_filter($relatorios, function($r) { return $r['status'] == 'validado'; });
                            echo count($validados);
                        ?> / <?php echo count($relatorios); ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Histórico de Submissões e Atividade</h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr class="x-small text-uppercase fw-bold">
                                    <th class="p-3 border-0">Data</th>
                                    <th class="p-3 border-0">Documento</th>
                                    <th class="p-3 border-0 text-center">Avaliação</th>
                                    <th class="p-3 border-0">Notas do Patrono</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($relatorios)): ?>
                                    <tr><td colspan="5" class="text-center py-5 opacity-50">Nenhuma atividade registada para este estagiário.</td></tr>
                                <?php else: ?>
                                    <?php foreach($relatorios as $r): ?>
                                        <tr>
                                            <td class="p-3 small"><?php echo date('d/m/Y', strtotime($r['data_submissao'])); ?></td>
                                            <td class="p-3">
                                                <a href="../uploads/estagio/relatorios/<?php echo $r['ficheiro_pdf']; ?>" target="_blank" class="text-danger small fw-bold text-decoration-none">
                                                    <i class="far fa-file-pdf me-1"></i> VER PDF
                                                </a>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="badge <?php 
                                                    echo $r['status'] == 'validado' ? 'bg-success' : 
                                                         ($r['status'] == 'revisao' ? 'bg-info' : 
                                                         ($r['status'] == 'pendente' ? 'bg-warning' : 'bg-danger')); 
                                                ?> small">
                                                    <?php echo strtoupper($r['status']); ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <div class="x-small fw-bold text-dark">Parecer do Patrono:</div>
                                                <div class="x-small text-muted mb-2"><?php echo $r['observacoes'] ?: 'Pendente...'; ?></div>
                                                
                                                <?php if($r['relatorio_firma']): ?>
                                                    <div class="x-small fw-bold text-primary">Relatório da Firma:</div>
                                                    <div class="x-small text-muted"><?php echo $r['relatorio_firma']; ?></div>
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
