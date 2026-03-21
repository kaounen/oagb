<?php
session_start();
if(!isset($_SESSION['lawyer_id']) || $_SESSION['member_type'] != 'advogado') { header("Location: index.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Handle Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_id'])) {
    $vid = $_POST['validate_id'];
    $status = $_POST['status'];
    $obs = $_POST['observacoes'];
    
    $stmt = $pdo->prepare("UPDATE gestao_estagio_relatorios SET status = ?, observacoes = ?, data_validacao = NOW() WHERE id = ? AND orientador_id = ?");
    $stmt->execute([$status, $obs, $vid, $lid]);
    
    require_once __DIR__ . '/../admin/includes/LogHelper.php';
    LogHelper::log($pdo, 'INTERN_REPORT_VALIDATE', "Validou relatório ID $vid com estado $status", 'gestao_estagio_relatorios', $vid);
    
    header("Location: validar_estagiarios.php?success=1"); exit;
}

// Fetch Pending Reports
$stmt = $pdo->prepare("SELECT r.*, e.nome_completo as estagiario_name, e.numero_registo as estagiario_registo 
                       FROM gestao_estagio_relatorios r 
                       JOIN advogados_estagiarios e ON r.estagiario_id = e.id 
                       WHERE r.orientador_id = ? 
                       ORDER BY r.data_submissao DESC");
$stmt->execute([$lid]);
$pendentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Estagiários | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-validate { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .list-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
    </style>
</head>
<body>

    <header class="hero-validate">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Monitorização de Estagiários</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="list-card">
            <h5 class="fw-bold mb-4">Relatòrios Submetidos (Sob minha Orientação)</h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr class="small text-uppercase fw-bold text-muted">
                            <th class="border-0 p-3">Data</th>
                            <th class="border-0 p-3">Estagiário / Cédula No.</th>
                            <th class="border-0 p-3">Ficheiro</th>
                            <th class="border-0 p-3 text-center">Estado Atual</th>
                            <th class="border-0 p-3 text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pendentes)): ?>
                            <tr><td colspan="5" class="text-center py-5 opacity-50">Nenhum relatório pendente para validação.</td></tr>
                        <?php else: ?>
                            <?php foreach($pendentes as $p): ?>
                                <tr>
                                    <td class="p-3"><?php echo date('d/m/Y', strtotime($p['data_submissao'])); ?></td>
                                    <td class="p-3">
                                        <div class="fw-bold text-dark mb-0"><?php echo $p['estagiario_name']; ?></div>
                                        <div class="x-small opacity-50"><?php echo $p['estagiario_registo']; ?></div>
                                    </td>
                                    <td class="p-3 small fw-bold">
                                        <a href="../uploads/estagio/relatorios/<?php echo $p['ficheiro_pdf']; ?>" target="_blank" class="text-danger text-decoration-none">
                                            <i class="far fa-file-pdf me-1"></i> VER PDF
                                        </a>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="badge py-2 px-3 <?php echo $p['status'] == 'validado' ? 'bg-success text-white' : ($p['status'] == 'pendente' ? 'bg-warning-subtle text-warning' : 'bg-danger text-white'); ?>">
                                            <?php echo strtoupper($p['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <?php if($p['status'] == 'pendente'): ?>
                                            <button class="btn btn-dark btn-sm px-3 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#valModal<?php echo $p['id']; ?>">VALIDAR</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="valModal<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-0 p-4">
                                                <h5 class="modal-title fw-bold">Validação Profissional</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <input type="hidden" name="validate_id" value="<?php echo $p['id']; ?>">
                                                <div class="modal-body p-4 pt-0">
                                                    <p class="small text-muted mb-4">Confirma a exatidão das atividades descritas pelo estagiário <b><?php echo $p['estagiario_name']; ?></b>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Apreção Técnica / Decisao</label>
                                                        <select name="status" class="form-select border-0 bg-light p-3" required>
                                                            <option value="validado">VALIDAR (Apto)</option>
                                                            <option value="rejeitado">REJEITAR (Necessita Alterações)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Observações / Justificação</label>
                                                        <textarea name="observacoes" class="form-control border-0 bg-light p-3" rows="3" placeholder="Informações adicionais..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="submit" class="btn btn-login w-100 py-3 fw-bold text-uppercase">Assinar e Registar Validação</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
