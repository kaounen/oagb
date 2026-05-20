<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$mes_atual = date('m');
$ano_atual = date('Y');
$meses_pt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Handle Email Dispatch
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_bulk_alerts'])) {
    $subject = $_POST['subject'] ?? '';
    $base_body = $_POST['body'] ?? '';
    $selected_lawyer_ids = $_POST['selected_lawyers'] ?? [];
    $extra_emails_raw = $_POST['extra_emails'] ?? '';
    
    $all_emails_to_send = [];
    
    // Get emails of selected lawyers
    if (!empty($selected_lawyer_ids)) {
        $placeholders = implode(',', array_fill(0, count($selected_lawyer_ids), '?'));
        $stmt = $pdo->prepare("SELECT id, nome_completo, numero_registo, email FROM advogados WHERE id IN ($placeholders)");
        $stmt->execute($selected_lawyer_ids);
        $lawyers_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $lawyers_info = [];
    }
    
    // Parse manual extra emails from Tagify
    if (!empty($extra_emails_raw)) {
        $decoded = json_decode($extra_emails_raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach ($decoded as $item) {
                if (isset($item['value']) && filter_var($item['value'], FILTER_VALIDATE_EMAIL)) {
                    $all_emails_to_send[] = [
                        'type' => 'manual',
                        'email' => $item['value'],
                        'nome' => $item['name'] ?? $item['value'],
                        'cedula' => 'N/A'
                    ];
                }
            }
        } else {
            $parts = preg_split('/[\s,;]+/', $extra_emails_raw);
            foreach ($parts as $part) {
                $part = trim($part);
                if (filter_var($part, FILTER_VALIDATE_EMAIL)) {
                    $all_emails_to_send[] = [
                        'type' => 'manual',
                        'email' => $part,
                        'nome' => $part,
                        'cedula' => 'N/A'
                    ];
                }
            }
        }
    }
    
    // Add lawyers to dispatch list
    foreach ($lawyers_info as $l) {
        if (filter_var($l['email'], FILTER_VALIDATE_EMAIL)) {
            $all_emails_to_send[] = [
                'type' => 'lawyer',
                'email' => $l['email'],
                'nome' => $l['nome_completo'],
                'cedula' => $l['numero_registo']
            ];
        }
    }
    
    // Handle Attachments
    $temp_files = [];
    if (isset($_FILES['attachments'])) {
        $files = $_FILES['attachments'];
        $upload_dir = __DIR__ . '/../../../uploads/temp_alerts/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $name = basename($files['name'][$i]);
                $destination = $upload_dir . time() . '_' . $name;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    $temp_files[] = [
                        'tmp_name' => $destination,
                        'name' => $name,
                        'size' => $files['size'][$i]
                    ];
                }
            }
        }
    }
    
    // Multi-part MIME HTML email sender logic
    function send_debtor_alert($to, $subject, $html_body, $files = []) {
        $from_name = defined('FROM_NAME') ? FROM_NAME : "Ordem dos Advogados da Guiné-Bissau";
        $from_email = defined('FROM_EMAIL') ? FROM_EMAIL : "comunicacao@oagb.gw";
        $headers = "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_email\r\n";
        
        if (empty($files)) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            return @mail($to, $subject, $html_body, $headers);
        } else {
            $boundary = md5(uniqid(time()));
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
            
            // Email body
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $html_body . "\r\n\r\n";
            
            // Attachments
            foreach ($files as $file) {
                if (file_exists($file['tmp_name'])) {
                    $file_name = $file['name'];
                    $file_size = filesize($file['tmp_name']);
                    $handle = fopen($file['tmp_name'], "r");
                    $content = fread($handle, $file_size);
                    fclose($handle);
                    $encoded_content = chunk_split(base64_encode($content));
                    
                    $body .= "--$boundary\r\n";
                    $body .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
                    $body .= "Content-Description: $file_name\r\n";
                    $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
                    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $body .= $encoded_content . "\r\n\r\n";
                }
            }
            $body .= "--$boundary--";
            return @mail($to, $subject, $body, $headers);
        }
    }
    
    // Dispatch Alerts
    $success_count = 0;
    $total_count = count($all_emails_to_send);
    
    foreach ($all_emails_to_send as $recipient) {
        // Customize placeholders
        $personalized_body = str_replace(
            ['{nome}', '{cedula}', '{mes}', '{ano}'],
            [$recipient['nome'], $recipient['cedula'], $meses_pt[(int)$mes_atual], $ano_atual],
            $base_body
        );
        
        if (send_debtor_alert($recipient['email'], $subject, $personalized_body, $temp_files)) {
            $success_count++;
        }
    }
    
    // Cleanup temporary files
    foreach ($temp_files as $f) {
        if (file_exists($f['tmp_name'])) {
            unlink($f['tmp_name']);
        }
    }
    
    if ($success_count > 0) {
        $success_msg = "Alerta disparado com sucesso! Foram enviados $success_count de $total_count e-mails.";
        if ($success_count < $total_count) {
            $success_msg .= " (Falharam " . ($total_count - $success_count) . " envios).";
        }
    } else {
        $error_msg = "Nenhum e-mail pôde ser enviado. Verifique se o servidor SMTP está online ou configure o seu ambiente local.";
    }
}

// Query for Advogados with NO payment confirmed for this month
try {
    $stmt = $pdo->prepare("SELECT a.id, a.nome_completo, a.numero_registo, a.email, a.telefone,
                           (SELECT MAX(data_pagamento) FROM finan_pagamentos 
                            WHERE advogado_id = a.id AND tipo_pagamento_id = 1 AND status = 'confirmado') as ultimo_pagamento
                           FROM advogados a 
                           WHERE a.status = 'ativo' 
                           AND a.id NOT IN (
                               SELECT advogado_id FROM finan_pagamentos 
                               WHERE tipo_pagamento_id = 1 
                               AND status = 'confirmado' 
                               AND MONTH(data_pagamento) = ? 
                               AND YEAR(data_pagamento) = ?
                           )
                           ORDER BY a.nome_completo ASC");
    $stmt->execute([$mes_atual, $ano_atual]);
    $devedores = $stmt->fetchAll();
} catch (PDOException $e) { $devedores = []; }
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>

<!-- Feedback Messages -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 p-4 mb-4" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i> <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 p-4 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2 fs-5"></i> <?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Container 1: Debtors List View -->
<div id="list-view-container">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Gestão de Quotas em Atraso</h2>
            <div class="text-muted small">Advogados com pagamentos pendentes para <strong><?php echo $meses_pt[(int)$mes_atual]; ?> / <?php echo $ano_atual; ?></strong>.</div>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-danger w-auto px-4 shadow-sm py-3 fw-bold text-uppercase" onclick="showEmailPanel()">
                <i class="fas fa-paper-plane me-2"></i> Disparar Alerta em Massa (E-mail)
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-0 overflow-hidden mb-5">
        <div class="card-header bg-danger-subtle text-danger-emphasis border-0 p-4 d-flex justify-content-between">
            <h5 class="fw-bold mb-0">Lista de Devedores (<?php echo count($devedores); ?> membros)</h5>
            <div class="small fw-bold text-uppercase opacity-75">Mês de Referência: <?php echo $meses_pt[(int)$mes_atual]; ?></div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase py-3">Advogado / Cédula</th>
                        <th class="border-0 small text-uppercase py-3">Última Quota Paga</th>
                        <th class="border-0 small text-uppercase py-3">Auditório (Atraso)</th>
                        <th class="border-0 small text-uppercase py-3 text-center">Contactos</th>
                        <th class="border-0 small text-uppercase py-3 text-end pe-4">Acções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($devedores)): ?>
                        <tr><td colspan="5" class="text-center py-5">Nenhum devedor encontrado. Excelente gestão de quotas!</td></tr>
                    <?php else: ?>
                        <?php foreach($devedores as $d): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold small"><?php echo $d['nome_completo']; ?></div>
                                    <div class="text-muted x-small">Nº Cédula: <?php echo $d['numero_registo']; ?></div>
                                </td>
                                <td>
                                    <?php if($d['ultimo_pagamento']): ?>
                                        <span class="badge bg-login-subtle text-login py-1 px-3 small border border-login-subtle">
                                            <?php echo date('d/m/Y', strtotime($d['ultimo_pagamento'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-muted py-1 px-3 small italic">Sem histórico</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        if(!$d['ultimo_pagamento']) {
                                            echo '<span class="text-danger fw-bold small">Indeterminado</span>';
                                        } else {
                                            $last = new DateTime($d['ultimo_pagamento']);
                                            $now = new DateTime();
                                            $diff = $last->diff($now);
                                            $months = ($diff->y * 12) + $diff->m;
                                            if($months > 0) {
                                                echo '<span class="text-danger fw-bold small">'.$months.' Mês(es) de Atraso</span>';
                                            } else {
                                                echo '<span class="fw-bold small opacity-50">Mês corrente apenas</span>';
                                            }
                                        }
                                    ?>
                                </td>
                                <td class="text-center small">
                                    <a href="mailto:<?php echo $d['email']; ?>" class="text-primary me-2"><i class="fas fa-envelope"></i></a>
                                    <a href="tel:<?php echo $d['telefone']; ?>" class="text-success"><i class="fas fa-phone"></i></a>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-danger p-2 px-3 small text-uppercase fw-bold" onclick="showEmailPanel('<?php echo $d['id']; ?>')">
                                        <i class="fas fa-bell me-1"></i> Notificar
                                    </button>
                                    <a href="novo_recebimento.php?adv_id=<?php echo $d['id']; ?>" class="btn btn-sm btn-login p-2 px-3 small text-uppercase fw-bold ms-1">
                                        Registar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Container 2: Premium Mass Email Alert Panel (starts hidden) -->
<div id="email-panel-container" class="d-none">
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h2 class="page-title">
                <button class="btn btn-light border p-2 rounded-circle me-3" onclick="hideEmailPanel()" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left text-muted"></i>
                </button>
                Campanha de Disparo de Alertas
            </h2>
            <div class="text-muted small">Configure o título, corpo do e-mail, envie anexos e filtre os advogados que irão receber o alerta.</div>
        </div>
        <div class="col-md-4 text-md-end">
            <button class="btn btn-light border py-3 px-4 fw-bold text-uppercase rounded-3" onclick="hideEmailPanel()">
                Cancelar e Voltar
            </button>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4 mb-5">
            
            <!-- Left Column: Recipient Selection -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-1">Destinatários</h5>
                        <div class="text-muted small">Selecione quem irá receber esta notificação.</div>
                    </div>
                    <div class="card-body p-4 d-flex flex-column" style="max-height: 650px;">
                        
                        <!-- Search & Toggle All -->
                        <div class="mb-3">
                            <input type="text" id="search-debtor" class="form-control border-light bg-light" placeholder="Procurar advogado..." onkeyup="filterDebtorsList()">
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="master-checkbox" checked onchange="toggleAllDebtors(this)">
                                <label class="form-check-label fw-bold small text-muted text-uppercase cursor-pointer" for="master-checkbox">Selecionar Todos</label>
                            </div>
                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold"><span id="selected-count-badge">0</span> selecionados</span>
                        </div>

                        <!-- Scrollable Debtors List -->
                        <div class="overflow-y-auto flex-grow-1 pe-2" style="max-height: 400px;">
                            <?php foreach ($devedores as $d): ?>
                                <div class="debtor-list-item d-flex align-items-center p-3 border rounded-3 mb-2 bg-white" data-name="<?php echo htmlspecialchars($d['nome_completo']); ?>" data-registry="<?php echo htmlspecialchars($d['numero_registo']); ?>">
                                    <div class="form-check me-3">
                                        <input class="form-check-input debtor-checkbox" type="checkbox" name="selected_lawyers[]" value="<?php echo $d['id']; ?>" id="lawyer_cb_<?php echo $d['id']; ?>" checked onchange="updateSelectedCount()">
                                    </div>
                                    <label class="form-check-label cursor-pointer flex-grow-1" for="lawyer_cb_<?php echo $d['id']; ?>">
                                        <div class="fw-bold small text-dark"><?php echo htmlspecialchars($d['nome_completo']); ?></div>
                                        <div class="text-muted x-small">Cédula: <?php echo htmlspecialchars($d['numero_registo']); ?> | <?php echo htmlspecialchars($d['email']); ?></div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Email Campaign Editor -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-5">
                    
                    <!-- Form Row: Extra Manual Emails -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Adicionar Outros E-mails Manualmente</label>
                        <input name="extra_emails" class="form-control" placeholder="Escreva e-mails adicionais separados por vírgulas...">
                        <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Copie e cole múltiplos e-mails ou escreva manualmente.</div>
                    </div>

                    <!-- Form Row: Subject Title -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Assunto do E-mail</label>
                        <input type="text" name="subject" class="form-control form-control-lg border-light bg-light fw-bold p-3" value="[OAGB] Alerta de Quota de Membro Pendente" required>
                    </div>

                    <!-- Form Row: Email Body WYSIWYG -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Texto / Mensagem do E-mail</label>
                        <textarea name="body" id="email_editor" class="form-control" rows="15">
                            <p>Prezado(a) Colega <strong>{nome}</strong>,</p>
                            <p>Desejamos que este e-mail o(a) encontre bem.</p>
                            <p>Constatamos em nossos registos financeiros que a sua quota regulamentar de membro da Ordem, referente ao mês de <strong>{mes} de {ano}</strong> (Cédula Profissional: <strong>{cedula}</strong>), ainda se encontra pendente de pagamento.</p>
                            <p>A contribuição regular de quotas é um dever deontológico essencial para o correto funcionamento da nossa Ordem, permitindo a continuidade da defesa do Estado de Direito, a melhoria contínua das nossas infraestruturas e a prestação de serviços de apoio e formação aos advogados guineenses.</p>
                            <p>Solicitamos a especial atenção de V. Exa. no sentido de regularizar a situação pendente através dos canais habituais (Secretaria da Ordem ou transferência bancária). Caso já tenha efetuado o respetivo pagamento, solicitamos que nos envie o comprovativo respondendo diretamente a este e-mail.</p>
                            <p>Agradecemos desde já a sua atenção e colaboração.</p>
                            <p>Com os nossos melhores cumprimentos,</p>
                            <p><strong>A Direcção Financeira</strong><br>Ordem dos Advogados da Guiné-Bissau</p>
                        </textarea>
                    </div>

                    <!-- Form Row: Attachments Zone -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Anexar Ficheiros / Documentos (PDF, Imagens, etc.)</label>
                        <input type="file" name="attachments[]" class="form-control border-light bg-light p-3" multiple>
                        <div class="x-small text-muted mt-2"><i class="fas fa-paperclip me-1"></i> Selecione múltiplos ficheiros se desejar anexar faturas, notas de cobrança ou circulares.</div>
                    </div>

                    <!-- Cheat Sheet Box for Placeholders -->
                    <div class="alert bg-light border rounded-3 p-4 mb-4">
                        <h6 class="fw-bold small text-uppercase text-dark mb-3"><i class="fas fa-magic text-gold me-2"></i> Variáveis Dinâmicas Disponíveis</h6>
                        <p class="small text-muted mb-2">Utilize as seguintes variáveis no corpo do e-mail. Elas serão substituídas automaticamente com os dados reais de cada advogado durante o disparo:</p>
                        <div class="row g-2">
                            <div class="col-md-6"><code class="x-small">{nome}</code> <span class="small text-muted">— Nome do Advogado</span></div>
                            <div class="col-md-6"><code class="x-small">{cedula}</code> <span class="small text-muted">— Número de Cédula</span></div>
                            <div class="col-md-6"><code class="x-small">{mes}</code> <span class="small text-muted">— Mês do pagamento pendente</span></div>
                            <div class="col-md-6"><code class="x-small">{ano}</code> <span class="small text-muted">— Ano do pagamento</span></div>
                        </div>
                    </div>

                    <!-- Submit / Send campaign action -->
                    <div class="text-end">
                        <button type="submit" name="send_bulk_alerts" class="btn btn-danger btn-lg px-5 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i> Confirmar e Disparar Alertas<span id="btn-send-count"></span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<script>
    let myEditor;

    // Load CKEditor
    ClassicEditor.create(document.querySelector('#email_editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).then(editor => {
        myEditor = editor;
    }).catch(e => console.error(e));

    // Load Tagify
    const extraInput = document.querySelector('input[name=extra_emails]');
    if (extraInput) {
        new Tagify(extraInput, {
            delimiters: ",| |;|\n",
            transformTag: function(tagData) {
                const regex = /(.+?)\s*<(.+?)>/;
                const match = tagData.value.match(regex);
                if (match) {
                    tagData.name = match[1].trim();
                    tagData.value = match[2].trim();
                }
            }
        });
    }

    // Toggle Panels
    function showEmailPanel(selectedLawyerId = null) {
        document.getElementById('list-view-container').classList.add('d-none');
        document.getElementById('email-panel-container').classList.remove('d-none');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        const checkboxes = document.querySelectorAll('.debtor-checkbox');
        if (selectedLawyerId) {
            checkboxes.forEach(cb => {
                cb.checked = (cb.value == selectedLawyerId);
            });
        } else {
            checkboxes.forEach(cb => {
                cb.checked = true;
            });
        }
        
        // Reset Search field
        document.getElementById('search-debtor').value = '';
        filterDebtorsList();
        
        updateSelectedCount();
    }

    function hideEmailPanel() {
        document.getElementById('email-panel-container').classList.add('d-none');
        document.getElementById('list-view-container').classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // List filtering
    function filterDebtorsList() {
        const query = document.getElementById('search-debtor').value.toLowerCase();
        const items = document.querySelectorAll('.debtor-list-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const registry = item.getAttribute('data-registry').toLowerCase();
            if (name.includes(query) || registry.includes(query)) {
                item.classList.remove('d-none');
                item.classList.add('d-flex');
            } else {
                item.classList.remove('d-flex');
                item.classList.add('d-none');
            }
        });
    }

    // Checkbox toggling
    function toggleAllDebtors(masterCb) {
        const checkboxes = document.querySelectorAll('.debtor-checkbox');
        checkboxes.forEach(cb => {
            const item = cb.closest('.debtor-list-item');
            if (!item.classList.contains('d-none')) {
                cb.checked = masterCb.checked;
            }
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.debtor-checkbox:checked').length;
        document.getElementById('selected-count-badge').textContent = checkedCount;
        document.getElementById('btn-send-count').textContent = ` (${checkedCount})`;
    }

    // Initialize Badge on load
    $(document).ready(function() {
        updateSelectedCount();
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
