<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT p.*, a.nome_completo as advogado, a.numero_registo, t.nome as tipo_taxa 
                         FROM finan_pagamentos p 
                         LEFT JOIN advogados a ON p.advogado_id = a.id 
                         LEFT JOIN finan_tipos_pagamento t ON p.tipo_pagamento_id = t.id 
                         WHERE p.id = ?");
    $stmt->execute([$id]);
    $pay = $stmt->fetch();
    if(!$pay) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Handle Validation
if (isset($_POST['confirm_payment'])) {
    $status = $_POST['status'];
    $obs = $_POST['admin_obs'];
    $validado_por = $_SESSION['admin_id'] ?? 1;

    try {
        $stmt = $pdo->prepare("UPDATE finan_pagamentos SET status = ?, observacoes = CONCAT(observacoes, '\n---\nValidação: ', ?), validado_por = ? WHERE id = ?");
        $stmt->execute([$status, $obs, $validado_por, $id]);
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'FINANCE_VALIDATE', "Validou pagamento #$id como $status", 'finan_pagamentos', $id);

        header("Location: index.php?validated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao validar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Validar Pagamento #<?php echo $id; ?></h2>
        <div class="text-muted small">Verifique o comprovativo e confirme a entrada de fundos.</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Comprovativo de Depósito / Transf.</h5>
            </div>
            <div class="card-body p-4">
                <?php if($pay['comprovativo_arquivo']): ?>
                    <?php $ext = pathinfo($pay['comprovativo_arquivo'], PATHINFO_EXTENSION); ?>
                    <?php if(in_array(strtolower($ext), ['jpg','jpeg','png','gif'])): ?>
                        <img src="/oagb/uploads/financeiro/<?php echo $pay['comprovativo_arquivo']; ?>" class="img-fluid rounded border shadow-sm w-100" alt="Recibo">
                    <?php else: ?>
                        <div class="p-5 text-center bg-light border rounded">
                            <i class="fas fa-file-pdf fa-4x text-danger mb-4 opacity-50"></i>
                            <div class="fw-bold fs-5 mb-3">Documento PDF</div>
                            <a href="/oagb/uploads/financeiro/<?php echo $pay['comprovativo_arquivo']; ?>" target="_blank" class="btn btn-login w-auto px-5">Abrir Ficheiro em Nova Aba</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="p-5 text-center bg-light border rounded">
                        <i class="fas fa-image fa-4x text-muted mb-4 opacity-25"></i>
                        <div class="text-muted small">Nenhum ficheiro anexado a este registo.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Detalhes da Transação</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block">Advogado</label>
                        <div class="fw-bold"><?php echo $pay['advogado']; ?></div>
                        <div class="text-muted small">Cédula: <?php echo $pay['numero_registo']; ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block">Tipo de Taxa</label>
                        <div class="badge bg-login-subtle text-login px-3 py-2 small fw-bold mt-1"><?php echo $pay['tipo_taxa']; ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block">Valor Liquidado</label>
                        <div class="fw-bold fs-4 text-success"><?php echo number_format($pay['valor_pago'], 2, ',', '.'); ?> CFA</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block">Data do Pagamento</label>
                        <div class="fw-bold"><?php echo date('d/m/Y', strtotime($pay['data_pagamento'])); ?></div>
                        <div class="text-muted small">Método: <?php echo ucfirst($pay['metodo_pagamento']); ?></div>
                    </div>
                    <?php if($pay['observacoes']): ?>
                        <div class="col-12 border-top pt-3">
                            <label class="small text-uppercase fw-bold text-muted d-block">Notas do Operador</label>
                            <div class="p-3 bg-light rounded small mt-1 italic"><?php echo nl2br($pay['observacoes']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-5">

                <h5 class="fw-bold mb-4">Ação Administrativa</h5>
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Parecer de Validação</label>
                        <textarea name="admin_obs" class="form-control border-0 bg-light" rows="4" placeholder="Ex: Valor confirmado no extracto bancário em 20/03..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Novo Estado do Pagamento</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card-radio card h-100 cursor-pointer border-0 bg-success-subtle text-success">
                                    <input type="radio" name="status" value="confirmado" class="d-none" checked>
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-check-circle mb-2"></i>
                                        <div class="fw-bold small">CONFIRMAR</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="card-radio card h-100 cursor-pointer border-0 bg-danger-subtle text-danger">
                                    <input type="radio" name="status" value="cancelado" class="d-none">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-times-circle mb-2"></i>
                                        <div class="fw-bold small">REJEITAR</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="confirm_payment" class="btn btn-login w-100 py-3 mt-4 fw-bold shadow-sm">FINALIZAR VALIDAÇÃO</button>
                    <a href="index.php" class="btn btn-light w-100 py-3 mt-2 border">DESISTIR E VOLTAR</a>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .card-radio input:checked + .card-body {
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 2px solid currentColor !important;
        border-radius: 12px;
    }
    .cursor-pointer { cursor: pointer; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
