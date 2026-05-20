<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/LogHelper.php';

// Repair character encoding issues in finan_config on load
try {
    $pdo->query("UPDATE finan_config SET descricao = 'Ficheiro de Assinatura do Bastonário' WHERE chave = 'bastonario_assinatura'");
    $pdo->query("UPDATE finan_config SET descricao = 'Nome do Bastonário' WHERE chave = 'bastonario_nome'");
} catch (Exception $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Handle Text Fields
    if (isset($_POST['config']) && is_array($_POST['config'])) {
        foreach ($_POST['config'] as $chave => $valor) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_config WHERE chave = ?");
            $stmt->execute([$chave]);
            if ($stmt->fetchColumn() > 0) {
                $stmt = $pdo->prepare("UPDATE finan_config SET valor = ? WHERE chave = ?");
                $stmt->execute([$valor, $chave]);
            } else {
                $descricoes = [
                    'email_smtp_host' => 'Servidor SMTP (Host)',
                    'email_smtp_port' => 'Porta SMTP',
                    'email_smtp_username' => 'Utilizador SMTP (E-mail)',
                    'email_smtp_password' => 'Senha SMTP',
                    'email_from_email' => 'E-mail do Remetente (Sender)',
                    'email_from_name' => 'Nome do Remetente'
                ];
                $desc = $descricoes[$chave] ?? $chave;
                $stmt = $pdo->prepare("INSERT INTO finan_config (chave, valor, descricao) VALUES (?, ?, ?)");
                $stmt->execute([$chave, $valor, $desc]);
            }
        }
    }

    // 2. Handle Bastonário Signature File Upload
    if (isset($_FILES['bastonario_assinatura_file']) && $_FILES['bastonario_assinatura_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['bastonario_assinatura_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg', 'svg'];
        
        if (in_array($ext, $allowed)) {
            $img_dir = __DIR__ . '/../../../img/';
            if (!file_exists($img_dir)) {
                mkdir($img_dir, 0777, true);
            }
            
            $new_name = 'assinatura_bastonario_' . time() . '.' . $ext;
            $destination = $img_dir . $new_name;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update the config in database
                $stmt = $pdo->prepare("UPDATE finan_config SET valor = ? WHERE chave = 'bastonario_assinatura'");
                $stmt->execute([$new_name]);
            }
        }
    }
    
    LogHelper::log($pdo, 'CONFIG_FINAN_UPDATE', "Atualizou as configurações financeiras e de gateway de email.");
    $success = "Configurações atualizadas com sucesso e registadas no log de auditoria.";
}

$stmt = $pdo->query("SELECT * FROM finan_config ORDER BY id ASC");
$configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Populate default email configs on load if they do not exist in database
$email_keys = [
    'email_smtp_host' => ['desc' => 'Servidor SMTP (Host)', 'default' => defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com'],
    'email_smtp_port' => ['desc' => 'Porta SMTP', 'default' => defined('SMTP_PORT') ? SMTP_PORT : '587'],
    'email_smtp_username' => ['desc' => 'Utilizador SMTP (E-mail)', 'default' => defined('SMTP_USERNAME') ? SMTP_USERNAME : 'info@oagb.gw'],
    'email_smtp_password' => ['desc' => 'Senha SMTP', 'default' => defined('SMTP_PASSWORD') ? SMTP_PASSWORD : ''],
    'email_from_email' => ['desc' => 'E-mail do Remetente (Sender)', 'default' => defined('FROM_EMAIL') ? FROM_EMAIL : 'info@oagb.gw'],
    'email_from_name' => ['desc' => 'Nome do Remetente', 'default' => defined('FROM_NAME') ? FROM_NAME : 'OAGB - Ordem dos Advogados da Guiné-Bissau']
];

$existing_keys = array_column($configs, 'chave');
foreach ($email_keys as $key => $data) {
    if (!in_array($key, $existing_keys)) {
        $configs[] = [
            'chave' => $key,
            'valor' => $data['default'],
            'descricao' => $data['desc']
        ];
    }
}
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

<form method="POST" enctype="multipart/form-data">
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
                    <?php 
                    $bastonario_nome_val = '';
                    $bastonario_sig_val = '';
                    foreach ($configs as $c) {
                        if ($c['chave'] === 'bastonario_nome') $bastonario_nome_val = $c['valor'];
                        if ($c['chave'] === 'bastonario_assinatura') $bastonario_sig_val = $c['valor'];
                    }
                    ?>
                    <!-- Nome do Bastonário -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Nome do Bastonário</label>
                        <input type="text" name="config[bastonario_nome]" value="<?php echo htmlspecialchars($bastonario_nome_val); ?>" class="form-control border-0 bg-light p-3">
                    </div>
                    <!-- Ficheiro de Assinatura -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Ficheiro de Assinatura do Bastonário</label>
                        <input type="file" name="bastonario_assinatura_file" class="form-control border-0 bg-light p-3 mb-2" accept="image/*">
                        <input type="hidden" name="config[bastonario_assinatura]" value="<?php echo htmlspecialchars($bastonario_sig_val); ?>">
                        <?php if (!empty($bastonario_sig_val)): ?>
                            <div class="d-flex align-items-center mt-2 p-2 bg-light rounded-3 border">
                                <img src="<?php echo ROOT_URL; ?>/img/<?php echo $bastonario_sig_val; ?>" style="max-height: 40px; margin-right: 15px;" alt="Assinatura Atual">
                                <span class="small text-muted"><i class="fas fa-check-circle text-success me-1"></i> Ficheiro atual: <code><?php echo htmlspecialchars($bastonario_sig_val); ?></code></span>
                            </div>
                        <?php else: ?>
                            <div class="x-small text-muted"><i class="fas fa-info-circle me-1"></i> Selecione um ficheiro de imagem (PNG, JPG) para carregar a assinatura do Bastonário.</div>
                        <?php endif; ?>
                    </div>
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

        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-envelope-open-text me-2 text-danger"></i> Configurações de Servidor de E-mail (SMTP & Sender)</h5>
                <div class="row g-3">
                    <?php foreach($configs as $c): if(strpos($c['chave'], 'email_') === 0): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><?php echo $c['descricao']; ?></label>
                            <?php if($c['chave'] == 'email_smtp_password'): ?>
                                <div class="input-group">
                                    <input type="password" name="config[<?php echo $c['chave']; ?>]" id="smtp_pass_field" value="<?php echo htmlspecialchars($c['valor']); ?>" class="form-control border-0 bg-light p-3">
                                    <button class="btn btn-light border-0 bg-light px-3" type="button" onclick="togglePasswordVisibility()">
                                        <i class="fas fa-eye text-muted" id="smtp_pass_eye"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <input type="text" name="config[<?php echo $c['chave']; ?>]" value="<?php echo htmlspecialchars($c['valor']); ?>" class="form-control border-0 bg-light p-3">
                            <?php endif; ?>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <div class="x-small text-muted mt-2">
                    <i class="fas fa-info-circle me-1 text-danger"></i> Estas configurações controlam o servidor de disparo e o remetente oficial para avisos de pagamento de quotas, newsletters e notificações automáticas do sistema.
                </div>
            </div>
        </div>

        <div class="col-12 text-end mt-4">
            <button type="submit" class="btn btn-login px-5 py-3 fw-bold text-uppercase shadow-lg">Salvar Configurações Ativas</button>
        </div>
    </div>
</form>

<script>
function togglePasswordVisibility() {
    const field = document.getElementById('smtp_pass_field');
    const eye = document.getElementById('smtp_pass_eye');
    if (field.type === 'password') {
        field.type = 'text';
        eye.className = 'fas fa-eye-slash text-muted';
    } else {
        field.type = 'password';
        eye.className = 'fas fa-eye text-muted';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
