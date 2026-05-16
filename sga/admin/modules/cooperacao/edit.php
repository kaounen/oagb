<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM parcerias_internacionais WHERE id = ?");
    $stmt->execute([$id]);
    $parceria = $stmt->fetch();
    if (!$parceria) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entidade = $_POST['entidade_parceira'];
    $pais = $_POST['pais'];
    $tipo = $_POST['tipo_acordo'];
    $objetivo = $_POST['objetivo'];
    $inicio = $_POST['data_assinatura'] ?: null;
    $fim = $_POST['data_validade'] ?: null;
    $status = $_POST['status'];
    
    // Handle File Upload (PDF Agreement)
    $doc = $parceria['documento_url'];
    if (isset($_FILES['documento_url']) && $_FILES['documento_url']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/cooperacao/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['documento_url']['name'], PATHINFO_EXTENSION);
        $new_filename = 'acordo_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['documento_url']['tmp_name'], $upload_dir . $new_filename)) {
            if ($parceria['documento_url'] && file_exists($upload_dir . $parceria['documento_url'])) {
                unlink($upload_dir . $parceria['documento_url']);
            }
            $doc = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE parcerias_internacionais SET entidade_parceira = ?, pais = ?, tipo_acordo = ?, objetivo = ?, data_assinatura = ?, data_validade = ?, documento_url = ?, status = ? WHERE id = ?");
        $stmt->execute([$entidade, $pais, $tipo, $objetivo, $inicio, $fim, $doc, $status, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Cooperação</h2>
        <div class="text-muted small">Modifique o acordo ou protocolo institucional.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label class="form-label text-uppercase fw-bold text-muted small">Entidade Parceira</label>
                            <input type="text" name="entidade_parceira" class="form-control form-control-lg border-0 bg-light" required value="<?php echo htmlspecialchars($parceria['entidade_parceira']); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-uppercase fw-bold text-muted small">País / Região</label>
                            <input type="text" name="pais" class="form-control form-control-lg border-0 bg-light" required value="<?php echo htmlspecialchars($parceria['pais']); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Objetivo / Âmbito do Acordo</label>
                        <textarea name="objetivo" id="editor" class="form-control bg-light border-0" rows="10"><?php echo htmlspecialchars($parceria['objetivo']); ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Instrumento</label>
                            <input type="text" name="tipo_acordo" class="form-control border-0 py-2 small" required value="<?php echo htmlspecialchars($parceria['tipo_acordo']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Assinatura</label>
                            <input type="date" name="data_assinatura" class="form-control border-0 py-2 small" value="<?php echo $parceria['data_assinatura']; ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Validade</label>
                            <input type="date" name="data_validade" class="form-control border-0 py-2 small" value="<?php echo $parceria['data_validade']; ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Estado Vigente</label>
                            <select name="status" class="form-select border-0 py-2 small">
                                <option value="Ativo" <?php if($parceria['status'] == 'Ativo') echo 'selected'; ?>>Vigente (Ativo)</option>
                                <option value="Expirado" <?php if($parceria['status'] == 'Expirado') echo 'selected'; ?>>Concluído (Expirado)</option>
                                <option value="Em Renovação" <?php if($parceria['status'] == 'Em Renovação') echo 'selected'; ?>>Em Renovação</option>
                            </select>
                        </div>
                        
                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Documento PDF Atual</label>
                            <?php if ($parceria['documento_url']): ?>
                                <a href="/oagb/uploads/cooperacao/<?php echo $parceria['documento_url']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mb-2">Ver Original PDF <i class="fas fa-external-link-alt ms-1"></i></a>
                            <?php else: ?>
                                <span class="small text-muted">Nenhum anexo disponível</span>
                            <?php endif; ?>
                            <div class="mt-3">
                                <label class="x-small text-muted fw-bold">Substituir Arquivo:</label>
                                <input type="file" name="documento_url" class="form-control border-0 bg-white shadow-sm mt-1" accept=".pdf">
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm fw-bold">GRAVAR ALTERAÇÕES</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).catch(e => console.error(e));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
