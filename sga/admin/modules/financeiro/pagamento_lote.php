<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Lawyers grouped by Firm (if any) or Alphabetical
$stmt = $pdo->query("SELECT a.id, a.nome_completo, a.numero_registo, s.nome as sociedade_nome 
                       FROM advogados a 
                       LEFT JOIN gestao_sociedades s ON a.sociedade_id = s.id 
                       WHERE a.status = 'ativo' 
                       ORDER BY s.nome, a.nome_completo ASC");
$lawyers = $stmt->fetchAll();

// Handle Bulk Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_bulk'])) {
    $selected = $_POST['adv_ids'] ?? [];
    $mes = $_POST['mes'] ?: date('m');
    $ano = $_POST['ano'] ?: date('Y');
    
    foreach ($selected as $aid) {
        $stmt = $pdo->prepare("INSERT INTO finan_pagamentos (advogado_id, tipo_pagamento_id, valor_pago, metodo_pagamento, status, data_pagamento) 
                               VALUES (?, 1, 10000, 'sociedade', 'confirmado', ?)");
        $stmt->execute([$aid, "$ano-$mes-01"]);
    }
    
    require_once __DIR__ . '/../../includes/LogHelper.php';
    LogHelper::log($pdo, 'FINANCE_BULK_PAY', "Liquidação em lote para " . count($selected) . " advogados.", 'finan_pagamentos', 0);
    
    header("Location: pagamento_lote.php?success=1&count=" . count($selected)); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Recebimento em Lote</h2>
        <div class="text-muted small">Liquidação massiva de quotas para Sociedades de Advogados ou grupos específicos.</div>
    </div>
</div>

<form method="POST">
    <div class="card border-0 shadow-sm p-4 bg-white mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Mês de Referência</label>
                <select name="mes" class="form-select border-0 bg-light p-3">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if($i==date('m')) echo 'selected'; ?>>Mês <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Ano</label>
                <input type="number" name="ano" class="form-control border-0 bg-light p-3" value="<?php echo date('Y'); ?>">
            </div>
            <div class="col-md-7 text-md-end">
                <button type="submit" name="pay_bulk" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase" onclick="return confirm('Confirmar liquidação em lote para os advogados selecionados?')">CONSOLIDAR PAGAMENTOS EM MASSA</button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-0 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 py-3" style="width: 50px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                        <th class="border-0 small text-uppercase py-3">Advogado / Beneficiário</th>
                        <th class="border-0 small text-uppercase py-3">Sociedade / Firma</th>
                        <th class="border-0 small text-uppercase py-3 text-end pe-4">Valor Base</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lawyers as $l): ?>
                        <tr>
                            <td class="ps-4"><input type="checkbox" name="adv_ids[]" value="<?php echo $l['id']; ?>" class="adv-check"></td>
                            <td>
                                <div class="fw-bold small"><?php echo $l['nome_completo']; ?></div>
                                <div class="x-small text-muted"><?php echo $l['numero_registo']; ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?php echo $l['sociedade_nome'] ?: 'Independente'; ?></span></td>
                            <td class="text-end pe-4 fw-bold">10.000 CFA</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    function toggleAll(el) {
        document.querySelectorAll('.adv-check').forEach(c => c.checked = el.checked);
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
