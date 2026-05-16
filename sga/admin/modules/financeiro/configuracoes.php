<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/LogHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['config'] as $chave => $valor) {
        $stmt = $pdo->prepare("UPDATE finan_config SET valor = ? WHERE chave = ?");
        $stmt->execute([$valor, $chave]);
    }
    
    LogHelper::log($pdo, 'CONFIG_FINAN_UPDATE', "Atualizou as configurações financeiras e de gateway.");
    $success = "Configurações atualizadas com sucesso e registadas no log de auditoria.";
}

$stmt = $pdo->query("SELECT * FROM finan_config ORDER BY id ASC");
$configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Configurações de Tesouraria</h2>
        <div class="text-muted small">Defina valores de quotas e chaves de integração Orange Money.</div>
    </div>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-coins me-2 text-warning"></i> Valores de Quotas (CFA)</h5>
                <?php foreach($configs as $c): if(strpos($c['chave'], 'quota_') === 0): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                        <div class="input-group">
                            <input type="number" name="config[<?php echo $c['chave']; ?>]" value="<?php echo $c['valor']; ?>" class="form-control border-0 bg-light p-3">
                            <span class="input-group-text border-0 bg-light fw-bold text-muted">CFA</span>
                        </div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="fas fa-network-wired me-2 text-primary"></i> Gateway Orange Money</h5>
                <?php foreach($configs as $c): if(strpos($c['chave'], 'orange_') === 0): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                        <?php if($c['chave'] == 'orange_money_enabled'): ?>
                            <select name="config[<?php echo $c['chave']; ?>]" class="form-select border-0 bg-light p-3">
                                <option value="1" <?php echo $c['valor'] == '1' ? 'selected' : ''; ?>>ATIVADO</option>
                                <option value="0" <?php echo $c['valor'] == '0' ? 'selected' : ''; ?>>DESATIVADO (Modo Simulação)</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="config[<?php echo $c['chave']; ?>]" value="<?php echo $c['valor']; ?>" class="form-control border-0 bg-light p-3">
                        <?php endif; ?>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="fas fa-mobile-alt me-2 text-warning"></i> Gateway MTN Mobile Money</h5>
                <?php foreach($configs as $c): if(strpos($c['chave'], 'mtn_') === 0): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                        <?php if($c['chave'] == 'mtn_momo_enabled'): ?>
                            <select name="config[<?php echo $c['chave']; ?>]" class="form-select border-0 bg-light p-3">
                                <option value="1" <?php echo $c['valor'] == '1' ? 'selected' : ''; ?>>ATIVADO</option>
                                <option value="0" <?php echo $c['valor'] == '0' ? 'selected' : ''; ?>>DESATIVADO (Modo Simulação)</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="config[<?php echo $c['chave']; ?>]" value="<?php echo $c['valor']; ?>" class="form-control border-0 bg-light p-3">
                        <?php endif; ?>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-signature me-2 text-dark"></i> Configuração do Bastonário (Certidões)</h5>
                <div class="row">
                    <?php foreach($configs as $c): if(strpos($c['chave'], 'bastonario_') === 0): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                            <input type="text" name="config[<?php echo $c['chave']; ?>]" value="<?php echo $c['valor']; ?>" class="form-control border-0 bg-light p-3">
                            <?php if($c['chave'] == 'bastonario_assinatura'): ?>
                                <div class="x-small text-muted mt-1">Introduza o nome do ficheiro (ex: assinatura.png) presente na pasta <code>img/</code></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-globe me-2 text-info"></i> Pagamentos Internacionais (VISA, Mastercard, PayPal)</h5>
                <div class="row g-3">
                    <?php foreach($configs as $c): if(strpos($c['chave'], 'stripe_') === 0 || strpos($c['chave'], 'paypal_') === 0 || $c['chave'] == 'global_payments_enabled'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                            <?php if($c['chave'] == 'global_payments_enabled'): ?>
                                <select name="config[<?php echo $c['chave']; ?>]" class="form-select border-0 bg-light p-3">
                                    <option value="1" <?php echo $c['valor'] == '1' ? 'selected' : ''; ?>>ATIVADO</option>
                                    <option value="0" <?php echo $c['valor'] == '0' ? 'selected' : ''; ?>>DESATIVADO</option>
                                </select>
                            <?php else: ?>
                                <input type="text" name="config[<?php echo $c['chave']; ?>]" value="<?php echo $c['valor']; ?>" class="form-control border-0 bg-light p-3">
                            <?php endif; ?>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-12 text-end mt-4">
            <button type="submit" class="btn btn-login px-5 py-3 fw-bold text-uppercase shadow-lg">Salvar Configurações Ativas</button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
