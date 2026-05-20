<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_assignments'])) {
    $assignments = [
        'sig_certidao' => $_POST['sig_certidao'],
        'sig_newsletter' => $_POST['sig_newsletter']
    ];

    try {
        $pdo->beginTransaction();
        
        // Process file uploads first
        $upload_dir = __DIR__ . '/../../../uploads/assinaturas/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        foreach (['sig_certidao', 'sig_newsletter'] as $key) {
            $value = $_POST[$key];
            $file_key = 'upload_' . $key;
            
            if (!empty($value) && isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                list($type, $id) = explode(':', $value);
                $file_ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
                $new_filename = ($type === 'b' ? 'assinatura_bastonario_' : 'assinatura_membro_') . time() . '_' . $key . '.' . $file_ext;
                
                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $new_filename)) {
                    if ($type === 'b') {
                        $st = $pdo->prepare("UPDATE bastonarios SET assinatura_url = ? WHERE id = ?");
                    } else {
                        $st = $pdo->prepare("UPDATE orgaos_sociais SET assinatura = ? WHERE id = ?");
                    }
                    $st->execute([$new_filename, $id]);
                }
            }
        }

        // Save configurations
        foreach ($assignments as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO configuracoes_site (chave, valor, descricao, grupo) 
                                   VALUES (?, ?, ?, 'assinaturas') 
                                   ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
            $desc = ($key === 'sig_certidao') ? 'Responsável pela assinatura nas Certidões' : 'Responsável pela assinatura na Newsletter';
            $stmt->execute([$key, $value, $desc]);
        }
        
        $pdo->commit();
        $success = "Atribuições de assinatura e ficheiros atualizados com sucesso.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erro ao guardar: " . $e->getMessage();
    }
}

// Fetch all potential signers
$bastonarios = $pdo->query("SELECT id, nome_completo as nome, 'bastonario' as tipo FROM bastonarios ORDER BY is_atual DESC, nome_completo ASC")->fetchAll();
$orgaos = $pdo->query("SELECT id, nome, cargo, 'orgao' as tipo FROM orgaos_sociais ORDER BY cargo ASC, nome ASC")->fetchAll();

// Fetch current assignments
$current = [];
$stmt = $pdo->query("SELECT chave, valor FROM configuracoes_site WHERE grupo = 'assinaturas'");
while($row = $stmt->fetch()) {
    $current[$row['chave']] = $row['valor'];
}

// Generate map for JS real-time preview
$sig_map = [];
foreach($bastonarios as $b) {
    $st = $pdo->prepare("SELECT assinatura_url FROM bastonarios WHERE id = ?");
    $st->execute([$b['id']]);
    $sig_url = $st->fetchColumn();
    $sig_map["b:{$b['id']}"] = $sig_url ? "../../../uploads/assinaturas/" . $sig_url : null;
}
foreach($orgaos as $o) {
    $st = $pdo->prepare("SELECT assinatura FROM orgaos_sociais WHERE id = ?");
    $st->execute([$o['id']]);
    $sig_url = $st->fetchColumn();
    $sig_map["o:{$o['id']}"] = $sig_url ? "../../../uploads/assinaturas/" . $sig_url : null;
}
$sig_map_json = json_encode($sig_map);

function get_signer_photo($pdo, $val) {
    if (empty($val)) return null;
    list($type, $id) = explode(':', $val);
    if ($type === 'b') {
        $stmt = $pdo->prepare("SELECT foto_url as foto, assinatura_url as assinatura FROM bastonarios WHERE id = ?");
        $path = 'bastonarios';
    } else {
        $stmt = $pdo->prepare("SELECT foto, assinatura FROM orgaos_sociais WHERE id = ?");
        $path = 'orgaos';
    }
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    return $res ? ['foto' => $res['foto'], 'assinatura' => $res['assinatura'], 'path' => $path] : null;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Atribuição de Assinaturas</h2>
        <div class="text-muted small">Defina quais responsáveis assinam digitalmente os documentos e comunicações.</div>
    </div>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?php echo $success; ?></div>
<?php endif; ?>

<?php if(isset($error)): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- CERTIDÕES -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">Emissão de Certidões</h5>
                            <small class="text-muted">Assinatura principal de "Nada a Declarar"</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Selecionar Responsável</label>
                        <select name="sig_certidao" class="form-select border-0 bg-light p-3 rounded-3 mb-3" onchange="previewSig(this, 'preview_cert')">
                            <option value="">-- Selecione um Responsável --</option>
                            <optgroup label="Bastonários">
                                <?php foreach($bastonarios as $b): ?>
                                    <option value="b:<?php echo $b['id']; ?>" <?php echo ($current['sig_certidao'] ?? '') === "b:{$b['id']}" ? 'selected' : ''; ?>>
                                        <?php echo $b['nome']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Órgãos Sociais">
                                <?php foreach($orgaos as $o): ?>
                                    <option value="o:<?php echo $o['id']; ?>" <?php echo ($current['sig_certidao'] ?? '') === "o:{$o['id']}" ? 'selected' : ''; ?>>
                                        <?php echo $o['nome']; ?> (<?php echo $o['cargo']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="mt-3">
                            <label class="form-label text-uppercase fw-bold text-muted x-small d-block mb-1">Anexar Assinatura Digitalizada</label>
                            <input type="file" name="upload_sig_certidao" class="form-control border-0 bg-light p-2 small" accept="image/*">
                            <div class="x-small text-muted mt-1">PNG com fundo transparente recomendado. O ficheiro atualizará o responsável selecionado.</div>
                        </div>
                    </div>

                    <div id="preview_cert" class="p-3 bg-light rounded-4 text-center">
                        <?php 
                        $sig = get_signer_photo($pdo, $current['sig_certidao'] ?? '');
                        if ($sig && $sig['assinatura']): ?>
                            <img src="../../../uploads/assinaturas/<?php echo $sig['assinatura']; ?>" style="max-height: 100px;">
                            <div class="small text-muted mt-2">Visualização da Assinatura</div>
                        <?php else: ?>
                            <div class="p-4 text-muted small italic">Nenhuma assinatura selecionada ou disponível.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEWSLETTER -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-info-subtle text-info p-3 rounded-circle me-3"><i class="fas fa-paper-plane"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">Newsletter Institucional</h5>
                            <small class="text-muted">Assinatura para o Editorial / Rodapé</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Selecionar Responsável</label>
                        <select name="sig_newsletter" class="form-select border-0 bg-light p-3 rounded-3 mb-3" onchange="previewSig(this, 'preview_news')">
                            <option value="">-- Selecione um Responsável --</option>
                            <optgroup label="Bastonários">
                                <?php foreach($bastonarios as $b): ?>
                                    <option value="b:<?php echo $b['id']; ?>" <?php echo ($current['sig_newsletter'] ?? '') === "b:{$b['id']}" ? 'selected' : ''; ?>>
                                        <?php echo $b['nome']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Órgãos Sociais">
                                <?php foreach($orgaos as $o): ?>
                                    <option value="o:<?php echo $o['id']; ?>" <?php echo ($current['sig_newsletter'] ?? '') === "o:{$o['id']}" ? 'selected' : ''; ?>>
                                        <?php echo $o['nome']; ?> (<?php echo $o['cargo']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="mt-3">
                            <label class="form-label text-uppercase fw-bold text-muted x-small d-block mb-1">Anexar Assinatura Digitalizada</label>
                            <input type="file" name="upload_sig_newsletter" class="form-control border-0 bg-light p-2 small" accept="image/*">
                            <div class="x-small text-muted mt-1">PNG com fundo transparente recomendado. O ficheiro atualizará o responsável selecionado.</div>
                        </div>
                    </div>

                    <div id="preview_news" class="p-3 bg-light rounded-4 text-center">
                        <?php 
                        $sig = get_signer_photo($pdo, $current['sig_newsletter'] ?? '');
                        if ($sig && $sig['assinatura']): ?>
                            <img src="../../../uploads/assinaturas/<?php echo $sig['assinatura']; ?>" style="max-height: 100px;">
                            <div class="small text-muted mt-2">Visualização da Assinatura</div>
                        <?php else: ?>
                            <div class="p-4 text-muted small italic">Nenhuma assinatura selecionada ou disponível.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="submit" name="save_assignments" class="btn btn-dark rounded-pill px-5 py-3 shadow-lg fw-bold">
                <i class="fas fa-save me-2 text-gold"></i> GUARDAR ATRIBUIÇÕES
            </button>
        </div>
    </div>
</form>

<script>
const sigMap = <?php echo $sig_map_json; ?>;

function previewSig(select, containerId) {
    const val = select.value;
    const container = document.getElementById(containerId);
    if (!val) {
        container.innerHTML = '<div class="p-4 text-muted small italic">Nenhuma assinatura selecionada ou disponível.</div>';
        return;
    }
    
    const sigUrl = sigMap[val];
    if (sigUrl) {
        container.innerHTML = `<img src="${sigUrl}" style="max-height: 100px;"><div class="small text-muted mt-2">Visualização da Assinatura</div>`;
    } else {
        container.innerHTML = '<div class="p-4 text-muted small italic text-danger"><i class="fas fa-exclamation-circle me-1"></i> Nenhuma assinatura carregada para este responsável. Pode anexar uma no campo acima!</div>';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
