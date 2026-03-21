<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Handle Config Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configs = $_POST['config'];
    foreach ($configs as $key => $val) {
        $stmt = $pdo->prepare("UPDATE gestao_configuracoes SET config_value = ? WHERE config_key = ?");
        $stmt->execute([$val, $key]);
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'VOTE_CONFIG_CHANGE', "Alterou regra de votação: $key para $val", 'gestao_configuracoes', 0);
    }
    header("Location: config.php?success=1"); exit;
}

// Fetch Configs
$stmt = $pdo->query("SELECT * FROM gestao_configuracoes WHERE config_key LIKE 'voto_%'");
$configs = $stmt->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Regras Estratégicas</h2>
        <div class="text-muted small">Configuração de critérios de elegibilidade eleitoral e segurança do pleito.</div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-5 bg-white">
            <h5 class="fw-bold mb-4">Parâmetros de Elegibilidade</h5>
            <form method="POST">
                <?php foreach($configs as $c): ?>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?php echo $c['description']; ?></label>
                        <input type="text" name="config[<?php echo $c['config_key']; ?>]" class="form-control border-0 bg-light p-3 fs-5" value="<?php echo $c['config_value']; ?>">
                        <div class="x-small text-muted mt-2">Última alteração: <?php echo date('d/m/Y H:i', strtotime($c['updated_at'])); ?></div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-dark w-100 p-4 fs-5 fw-bold rounded-pill shadow-lg mt-3">GUARDAR ALTERAÇÕES DO SISTEMA</button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-5 bg-primary-subtle border-primary border h-100">
            <h5 class="fw-bold text-primary mb-4"><i class="fas fa-shield-alt me-2"></i> Regras de Auditoria</h5>
            <p class="small text-primary-emphasis mb-4">Todas as alterações nestes parâmetros são registadas permanentemente nos <b>Logs de Sistema</b> com identificação do utilizador administrativo e carimbo temporal (Timestamp).</p>
            
            <div class="p-4 bg-white rounded-4 shadow-sm small text-muted">
                <i class="fas fa-info-circle me-1"></i> A alteração de regras durante um pleito ativo pode invalidar a integridade democrática do processo e deve ser feita apenas por decisão da <b>Comissão Eleitoral</b>.
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
