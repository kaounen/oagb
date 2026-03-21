<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM pareceres_deliberacoes WHERE id = ?");
    $stmt->execute([$id]);
    $par = $stmt->fetch();
    if(!$par) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assunto = $_POST['assunto'];
    $tipo = $_POST['tipo'] ?? 'parecer';
    $numero = $_POST['numero'] ?? '';
    $relator = $_POST['relator'] ?? '';
    $data_em = $_POST['data_emissao'] ?: date('Y-m-d');
    $resumo = $_POST['resumo'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    $arquivo = $par['arquivo_pdf'];
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['arquivo_pdf']['name'], INFO_EXTENSION);
        $new_filename = 'par_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $upload_dir . $new_filename)) {
            if (!empty($par['arquivo_pdf']) && file_exists($upload_dir . $par['arquivo_pdf'])) {
                unlink($upload_dir . $par['arquivo_pdf']);
            }
            $arquivo = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE pareceres_deliberacoes SET assunto = ?, tipo = ?, numero = ?, relator = ?, data_emissao = ?, resumo = ?, arquivo_pdf = ?, ativo = ? WHERE id = ?");
        $stmt->execute([$assunto, $tipo, $numero, $relator, $data_em, $resumo, $arquivo, $ativo, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Parecer / Deliberação</h2>
        <div class="text-muted small">Altere os detalhes da peça jurídica #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Assunto / Título do Parecer</label>
                        <input type="text" name="assunto" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($par['assunto']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Resumo / Ementa</label>
                        <textarea name="resumo" class="form-control bg-light border-0" rows="10"><?php echo $par['resumo']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número / Ref. Oficial</label>
                            <input type="text" name="numero" class="form-control border-0 py-3" value="<?php echo $par['numero']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Documento</label>
                            <select name="tipo" class="form-select border-0 shadow-sm py-2">
                                <option value="parecer" <?php echo $par['tipo'] == 'parecer'?'selected':''; ?>>Parecer</option>
                                <option value="deliberacao" <?php echo $par['tipo'] == 'deliberacao'?'selected':''; ?>>Deliberação</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Emissão</label>
                            <input type="date" name="data_emissao" class="form-control border-0" value="<?php echo $par['data_emissao']; ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Ficheiro Atual (PDF)</label>
                            <div class="bg-white p-3 border rounded mb-3 text-center">
                                <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                                <div class="small fw-bold text-truncate"><?php echo $par['arquivo_pdf']; ?></div>
                            </div>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('pdf_input').click();">
                                <i class="fas fa-sync-alt text-muted mb-2"></i>
                                <div class="small text-muted">Substituir PDF</div>
                                <input type="file" name="arquivo_pdf" id="pdf_input" class="d-none" accept=".pdf">
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Gravar Alterações</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Descartar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
