<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM inscricoes_ordem WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// ─── Handle Full Activation Workflow ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_inscription'])) {
    $status      = $_POST['status'];
    $obs         = trim($_POST['observations']);
    $registo     = trim($_POST['reg_number'] ?? '');
    $init_pass   = trim($_POST['initial_password'] ?? '');
    $send_email  = isset($_POST['send_email_notification']);

    try {
        $pdo->beginTransaction();

        // 1. Update inscription status
        $stmt = $pdo->prepare("UPDATE inscricoes_ordem SET status = ?, observacoes_admin = ?, numero_registo_atribuido = ?, data_analise = NOW() WHERE id = ?");
        $stmt->execute([$status, $obs, $registo, $id]);

        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'INSCRIPTION_UPDATE', "Atualizou inscri\u00e7\u00e3o #$id para status: $status", 'inscricoes_ordem', $id);

        $new_adv_id = null;

        if ($status === 'aprovado') {
            // 2. Create advogado record if not exists
            $check = $pdo->prepare("SELECT id FROM advogados WHERE numero_registo = ?");
            $check->execute([$registo]);
            $existing = $check->fetch();

            if (!$existing) {
                $ins = $pdo->prepare("INSERT INTO advogados (numero_registo, nome_completo, genero, data_nascimento, nacionalidade, bi_passaporte, regiao, localidade, morada, telefone, email, status, data_inscricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativo', NOW())");
                $ins->execute([
                    $registo, $row['nome_completo'], $row['genero'], $row['data_nascimento'],
                    $row['nacionalidade'], $row['bi_passaporte'], $row['regiao'],
                    $row['localidade'], $row['morada'], $row['telefone'], $row['email']
                ]);
                $new_adv_id = $pdo->lastInsertId();
                LogHelper::log($pdo, 'ADVOGADO_CREATE', "Criado advogado #$new_adv_id a partir de inscri\u00e7\u00e3o #$id", 'advogados', $new_adv_id);
            } else {
                $new_adv_id = $existing['id'];
            }

            // 3. Create portal access with initial password
            if (!empty($init_pass) && $new_adv_id) {
                $username    = strtolower(explode(' ', $row['nome_completo'])[0]) . '.' . $new_adv_id;
                $pass_hashed = password_hash($init_pass, PASSWORD_DEFAULT);

                // Check if admin_users already has this email
                $chk = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
                $chk->execute([$row['email']]);
                if (!$chk->fetch()) {
                    $ins_user = $pdo->prepare("INSERT INTO admin_users (full_name, username, password, email, role) VALUES (?, ?, ?, ?, 'membro')");
                    $ins_user->execute([$row['nome_completo'], $username, $pass_hashed, $row['email']]);
                    LogHelper::log($pdo, 'USER_CREATE', "Criado acesso ao portal para {$row['nome_completo']} ($username)", 'admin_users', $pdo->lastInsertId());
                }
            }

            // 4. Simulate email notification
            if ($send_email) {
                $email_log = date('Y-m-d H:i:s') . " | EMAIL ENVIADO para {$row['email']}: Aprovação de Inscrição OAGB";
                // In production: use PHPMailer/SMTP here
                // mail($row['email'], 'OAGB - Inscrição Aprovada', $body, $headers);
                LogHelper::log($pdo, 'EMAIL_SENT', "Notifica\u00e7\u00e3o de aprova\u00e7\u00e3o enviada para {$row['email']}", 'inscricoes_ordem', $id);
            }

        } elseif ($status === 'rejeitado' && $send_email) {
            LogHelper::log($pdo, 'EMAIL_SENT', "Notifica\u00e7\u00e3o de rejei\u00e7\u00e3o enviada para {$row['email']}", 'inscricoes_ordem', $id);
        }

        $pdo->commit();

        // Re-fetch updated row
        $stmt = $pdo->prepare("SELECT * FROM inscricoes_ordem WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        $success = [
            'status'     => $status,
            'reg'        => $registo,
            'pass'       => $init_pass,
            'email_sent' => $send_email,
            'adv_id'     => $new_adv_id,
        ];

    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Erro ao processar: " . $e->getMessage();
    }
}

// Generate a suggested strong password
function generate_password($length = 10) {
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#';
    return substr(str_shuffle(str_repeat($chars, ceil($length/strlen($chars)))), 0, $length);
}

$suggested_pass = generate_password();
$status_color = ['pendente' => 'warning', 'em_analise' => 'info', 'aprovado' => 'success', 'rejeitado' => 'danger'];
$sc = $status_color[$row['status']] ?? 'secondary';
?>

<style>
    .timeline-step { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 20px; }
    .timeline-step .step-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.85rem; }
    .info-row { display: flex; flex-direction: column; margin-bottom: 18px; }
    .info-row .info-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #B1A276; font-weight: 800; margin-bottom: 4px; }
    .info-row .info-value { font-size: 0.9rem; font-weight: 600; color: #1a1a2e; }
    .pass-box { background: #1a1a2e; color: #B1A276; padding: 14px 20px; border-radius: 12px; font-family: monospace; font-size: 1.1rem; letter-spacing: 3px; font-weight: bold; display: flex; align-items: center; justify-content: space-between; }
    .activation-card { background: linear-gradient(135deg, #1a1a2e 0%, #111923 100%); border-radius: 20px; padding: 30px; color: white; }
    .status-badge-lg { font-size: 0.75rem; padding: 6px 16px; border-radius: 30px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
    .success-overlay { background: linear-gradient(135deg, #0f5132, #198754); border-radius: 20px; padding: 30px; color: white; text-align: center; }
</style>

<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-circle p-2"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="page-title mb-0">Análise de Candidatura <span class="text-gold">#<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></span></h2>
                <div class="text-muted small">Processamento deliberativo e ativação de acesso ao portal.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-end">
        <span class="status-badge-lg badge bg-<?php echo $sc; ?>-subtle text-<?php echo $sc; ?>-emphasis border border-<?php echo $sc; ?>-subtle">
            <i class="fas fa-circle me-1" style="font-size:0.5rem;vertical-align:middle"></i>
            <?php echo strtoupper($row['status']); ?>
        </span>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="success-overlay mb-5 shadow-lg">
        <div class="mb-3"><i class="fas fa-check-circle fa-3x opacity-75"></i></div>
        <h4 class="fw-bold">
            <?php echo $success['status'] === 'aprovado' ? '🎉 Inscrição Aprovada com Sucesso!' : '⚠️ Candidatura Processada'; ?>
        </h4>
        <?php if ($success['status'] === 'aprovado'): ?>
            <p class="mb-2 opacity-75">O advogado foi registado no sistema com o nº <strong><?php echo $success['reg']; ?></strong></p>
            <?php if ($success['pass']): ?>
                <div class="d-inline-flex align-items-center gap-3 bg-black bg-opacity-25 rounded-pill px-4 py-2 mb-3">
                    <span class="opacity-75 small">Password inicial gerada:</span>
                    <code class="text-warning fw-bold fs-6"><?php echo htmlspecialchars($success['pass']); ?></code>
                </div>
            <?php endif; ?>
            <?php if ($success['email_sent']): ?>
                <p class="mb-0 small opacity-60"><i class="fas fa-paper-plane me-1"></i> Email de notificação enviado para <strong><?php echo $row['email']; ?></strong></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="opacity-75 mb-0">A candidatura foi marcada como <strong><?php echo strtoupper($success['status']); ?></strong>.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- ─── LEFT: Candidate Profile ─────────────────────── -->
    <div class="col-lg-7">

        <!-- Personal Info -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                         style="width:60px;height:60px;background:linear-gradient(135deg,#B1A276,#8c7b4e);font-size:1.4rem;flex-shrink:0">
                        <?php echo mb_substr($row['nome_completo'], 0, 1); ?>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo $row['nome_completo']; ?></h5>
                        <small class="text-muted">
                            <span class="badge bg-<?php echo $row['tipo_inscricao'] === 'advogado' ? 'primary' : 'info'; ?> py-1 px-2 me-2 small"><?php echo ucfirst($row['tipo_inscricao']); ?></span>
                            <i class="fas fa-map-marker-alt me-1 opacity-50"></i><?php echo $row['localidade']; ?>, <?php echo $row['regiao']; ?>
                        </small>
                    </div>
                    <div class="ms-auto text-muted small">
                        Submetido a <strong><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></strong>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo $row['email']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Telefone</span>
                            <span class="info-value"><?php echo $row['telefone']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Género</span>
                            <span class="info-value"><?php echo $row['genero'] === 'M' ? 'Masculino' : 'Feminino'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Nascimento</span>
                            <span class="info-value"><?php echo $row['data_nascimento'] ? date('d/m/Y', strtotime($row['data_nascimento'])) : '—'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Documento ID</span>
                            <span class="info-value"><?php echo $row['bi_passaporte']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="info-row">
                            <span class="info-label">Morada</span>
                            <span class="info-value"><?php echo $row['morada']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic & Experience -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase small mb-4" style="color:#B1A276;letter-spacing:1px">
                    <i class="fas fa-graduation-cap me-2"></i>Habilitações & Experiência
                </h6>
                <div class="mb-4">
                    <div class="info-label mb-2">Formação Académica</div>
                    <div class="bg-light p-3 rounded-3 small" style="white-space:pre-line;line-height:1.7"><?php echo htmlspecialchars($row['formacao_academica']); ?></div>
                </div>
                <?php if ($row['experiencia_profissional']): ?>
                    <div>
                        <div class="info-label mb-2">Experiência Profissional</div>
                        <div class="bg-light p-3 rounded-3 small" style="white-space:pre-line;line-height:1.7"><?php echo htmlspecialchars($row['experiencia_profissional']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($row['arquivo_comprovativo']): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="mb-3"><i class="fas fa-file-invoice-dollar fa-2x text-primary opacity-50"></i></div>
                    <div class="small fw-bold mb-3">Comprovativo de Taxa de Inscrição</div>
                    <a href="/oagb/uploads/inscricoes/<?php echo $row['arquivo_comprovativo']; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-4 py-2 rounded-pill">
                        <i class="fas fa-external-link-alt me-2"></i>Abrir Documento
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ─── RIGHT: Decision Panel ────────────────────────── -->
    <div class="col-lg-5">
        <div class="activation-card shadow-lg mb-4">
            <h5 class="fw-bold mb-1 text-white">
                <i class="fas fa-gavel me-2" style="color:#B1A276"></i>Painel de Decisão
            </h5>
            <p class="text-white opacity-50 small mb-4">Delibere, atribua credenciais e notifique o candidato.</p>

            <form method="POST" id="activationForm">

                <!-- Status Decision -->
                <div class="mb-4">
                    <label class="d-block mb-2" style="font-size:0.65rem;text-transform:uppercase;letter-spacing:1px;color:#B1A276;font-weight:800">Decisão</label>
                    <div class="d-flex gap-2">
                        <?php
                        $statuses = [
                            'pendente'   => ['label' => 'Pendente',   'icon' => 'fa-hourglass-half', 'color' => 'warning'],
                            'em_analise' => ['label' => 'Em Análise', 'icon' => 'fa-search',         'color' => 'info'],
                            'aprovado'   => ['label' => 'Aprovar',    'icon' => 'fa-check',          'color' => 'success'],
                            'rejeitado'  => ['label' => 'Rejeitar',   'icon' => 'fa-times',          'color' => 'danger'],
                        ];
                        foreach ($statuses as $val => $s):
                            $sel = $row['status'] === $val;
                        ?>
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="status" id="st_<?php echo $val; ?>" value="<?php echo $val; ?>" <?php echo $sel ? 'checked' : ''; ?> onchange="toggleApprovalFields(this.value)">
                                <label class="btn btn-outline-<?php echo $s['color']; ?> w-100 d-flex flex-column align-items-center py-2 px-1 border-2 small fw-bold" for="st_<?php echo $val; ?>" style="font-size:0.7rem;gap:4px">
                                    <i class="fas <?php echo $s['icon']; ?>"></i><?php echo $s['label']; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Fields shown only on Approve -->
                <div id="approval_fields" style="display: <?php echo $row['status'] === 'aprovado' ? 'block' : 'none'; ?>">
                    <div class="mb-4">
                        <label class="d-block mb-2" style="font-size:0.65rem;text-transform:uppercase;letter-spacing:1px;color:#B1A276;font-weight:800">Nº de Registo a Atribuir</label>
                        <input type="text" name="reg_number" id="reg_number"
                               class="form-control border-0 bg-white bg-opacity-10 text-white fw-bold"
                               style="background:rgba(255,255,255,0.08) !important;border-radius:10px;color:white!important"
                               placeholder="Ex: ADV-001/2026"
                               value="<?php echo htmlspecialchars($row['numero_registo_atribuido'] ?? ''); ?>">
                    </div>

                    <!-- Password Generator -->
                    <div class="mb-4">
                        <label class="d-block mb-2" style="font-size:0.65rem;text-transform:uppercase;letter-spacing:1px;color:#B1A276;font-weight:800">
                            <i class="fas fa-key me-1"></i>Password Inicial do Portal
                        </label>
                        <div class="pass-box mb-2" id="passDisplay">
                            <span id="passValue"><?php echo $suggested_pass; ?></span>
                            <button type="button" class="btn btn-sm" style="color:#B1A276;background:transparent;border:none" onclick="regeneratePass()" title="Gerar nova password">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <input type="hidden" name="initial_password" id="initial_password" value="<?php echo $suggested_pass; ?>">
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.5)">
                            <i class="fas fa-info-circle me-1"></i>Password gerada automaticamente. O candidato deverá alterá-la no primeiro acesso.
                        </div>
                    </div>
                </div>

                <!-- Observations -->
                <div class="mb-4">
                    <label class="d-block mb-2" style="font-size:0.65rem;text-transform:uppercase;letter-spacing:1px;color:#B1A276;font-weight:800">Observações / Justificação</label>
                    <textarea name="observations" rows="3"
                              class="form-control border-0 text-white"
                              style="background:rgba(255,255,255,0.08)!important;border-radius:10px;resize:none;color:white!important"
                              placeholder="Notas internas sobre a decisão..."><?php echo htmlspecialchars($row['observacoes_admin'] ?? ''); ?></textarea>
                </div>

                <!-- Email Notification -->
                <div class="mb-4 p-3 rounded-3" style="background:rgba(177,162,118,0.1);border:1px solid rgba(177,162,118,0.2)">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="send_email_notification" id="sendEmail" checked>
                        <label class="form-check-label text-white small" for="sendEmail">
                            <i class="fas fa-envelope me-2" style="color:#B1A276"></i>
                            Enviar email de notificação ao candidato
                        </label>
                    </div>
                    <div class="small mt-2" style="color:rgba(255,255,255,0.4);padding-left:2.5rem">
                        Para: <strong style="color:rgba(255,255,255,0.7)"><?php echo $row['email']; ?></strong>
                    </div>
                </div>

                <button type="submit" name="activate_inscription" class="btn w-100 py-3 fw-bold rounded-pill shadow-sm"
                        style="background:linear-gradient(135deg,#B1A276,#8c7b4e);color:#111923;letter-spacing:1px;font-size:0.85rem">
                    <i class="fas fa-bolt me-2"></i>PROCESSAR DECISÃO & NOTIFICAR
                </button>
                <a href="index.php" class="btn btn-light btn-sm w-100 py-2 mt-2 border opacity-50">Cancelar</a>
            </form>
        </div>

        <!-- Admin History / Timeline -->
        <?php if ($row['data_analise']): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold small text-uppercase mb-4" style="color:#B1A276;letter-spacing:1px">
                    <i class="fas fa-history me-2"></i>Histórico
                </h6>
                <div class="timeline-step">
                    <div class="step-icon bg-success-subtle text-success"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="fw-bold small">Decisão Registada</div>
                        <div class="text-muted x-small"><?php echo date('d/m/Y H:i', strtotime($row['data_analise'])); ?></div>
                        <?php if ($row['numero_registo_atribuido']): ?>
                            <div class="mt-1"><span class="badge bg-success-subtle text-success py-1 px-2 small">Nº <?php echo $row['numero_registo_atribuido']; ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($row['observacoes_admin']): ?>
                <div class="timeline-step">
                    <div class="step-icon bg-info-subtle text-info"><i class="fas fa-sticky-note"></i></div>
                    <div>
                        <div class="fw-bold small">Observações Admin</div>
                        <div class="text-muted small mt-1"><?php echo nl2br(htmlspecialchars($row['observacoes_admin'])); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="timeline-step">
                    <div class="step-icon bg-warning-subtle text-warning"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="fw-bold small">Candidatura Submetida</div>
                        <div class="text-muted x-small"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
                        <div class="text-muted x-small">IP: <?php echo $row['ip_inscricao']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const PASS_CHARS = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#';

function generatePass(len = 10) {
    let pass = '';
    for (let i = 0; i < len; i++) {
        pass += PASS_CHARS.charAt(Math.floor(Math.random() * PASS_CHARS.length));
    }
    return pass;
}

function regeneratePass() {
    const p = generatePass();
    document.getElementById('passValue').textContent = p;
    document.getElementById('initial_password').value = p;
    document.getElementById('passDisplay').style.animation = 'none';
    setTimeout(() => { document.getElementById('passDisplay').style.animation = ''; }, 10);
}

function toggleApprovalFields(val) {
    const af = document.getElementById('approval_fields');
    af.style.display = val === 'aprovado' ? 'block' : 'none';
    if (val === 'aprovado') {
        document.getElementById('reg_number').required = true;
    } else {
        document.getElementById('reg_number').required = false;
    }
}

// Init
toggleApprovalFields(document.querySelector('input[name="status"]:checked')?.value || 'pendente');
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
