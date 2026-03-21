<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Lawyers for dropdown
$lawyers = $pdo->query("SELECT id, nome_completo, numero_registo FROM advogados ORDER BY nome_completo ASC")->fetchAll();
$tipos = $pdo->query("SELECT id, nome, valor_padrao FROM finan_tipos_pagamento WHERE ativo = 1")->fetchAll();

// Handle Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adv_id = $_POST['advogado_id'];
    $tipo_id = $_POST['tipo_pagamento_id'];
    $valor = $_POST['valor_pago'];
    $data = $_POST['data_pagamento'] ?: date('Y-m-d');
    $metodo = $_POST['metodo_pagamento'];
    $obs = $_POST['observacoes'];
    
    // File handling
    $comprovativo = '';
    if (isset($_FILES['comprovativo']) && $_FILES['comprovativo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../uploads/financeiro/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $new_filename = 'rec_' . time() . '.' . pathinfo($_FILES['comprovativo']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['comprovativo']['tmp_name'], $upload_dir . $new_filename)) {
            $comprovativo = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO finan_pagamentos (advogado_id, tipo_pagamento_id, valor_pago, data_pagamento, metodo_pagamento, observacoes, comprovativo_arquivo, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmado')");
        $stmt->execute([$adv_id, $tipo_id, $valor, $data, $metodo, $obs, $comprovativo]);
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'FINANCE', "Registou pagamento de " . $valor . " para advogado ID " . $adv_id, 'finan_pagamentos', $pdo->lastInsertId());

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao registar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Recebimento</h2>
        <div class="text-muted small">Registe uma entrada de valores na tesouraria da Ordem.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger px-4 py-3 border-0 bg-danger-subtle text-danger small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Beneficiário (Advogado)</label>
                        <select name="advogado_id" class="form-select border-0 bg-light p-3 select2-advogado" required>
                            <option value="">Selecione o Advogado...</option>
                            <?php foreach($lawyers as $l): ?>
                                <option value="<?php echo $l['id']; ?>"><?php echo $l['nome_completo']; ?> (<?php echo $l['numero_registo']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Taxa / Quota</label>
                        <select name="tipo_pagamento_id" id="tipo_select" class="form-select border-0 bg-light p-3" required>
                            <option value="">Selecione o tipo...</option>
                            <?php foreach($tipos as $t): ?>
                                <option value="<?php echo $t['id']; ?>" data-valor="<?php echo $t['valor_padrao']; ?>"><?php echo $t['nome']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Observações Internas</label>
                        <textarea name="observacoes" class="form-control bg-light border-0" rows="5" placeholder="Ex: Pagamento referente ao semestre 2024.1..."></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Valor Pago (CFA)</label>
                            <input type="number" name="valor_pago" id="valor_input" step="0.01" class="form-control border-0 fw-bold text-success fs-4" placeholder="0.00" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data do Pagamento</label>
                            <input type="date" name="data_pagamento" class="form-control border-0 py-2" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Método</label>
                            <select name="metodo_pagamento" class="form-select border-0 py-2">
                                <option value="transferencia">Transf. Bancária</option>
                                <option value="deposito">Depósito</option>
                                <option value="dinheiro">Númerário (Caixa)</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Comprovativo (Scan/Foto)</label>
                            <input type="file" name="comprovativo" class="form-control border-0 bg-white" accept="image/*,application/pdf">
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm text-uppercase fw-bold">Confirmar Recebimento</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('tipo_select').onchange = function() {
        const valor = this.options[this.selectedIndex].getAttribute('data-valor');
        if(valor) document.getElementById('valor_input').value = valor;
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
