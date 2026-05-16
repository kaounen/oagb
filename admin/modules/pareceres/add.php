<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Form Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assunto = $_POST['assunto'];
    $tipo = $_POST['tipo'] ?? 'parecer';
    $numero = $_POST['numero'] ?? '';
    $relator = $_POST['relator'] ?? '';
    $data_em = $_POST['data_emissao'] ?: date('Y-m-d');
    $resumo = $_POST['resumo'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // PDF Upload handling
    $arquivo = '';
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['arquivo_pdf']['name'], INFO_EXTENSION);
        $new_filename = 'par_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $upload_dir . $new_filename)) {
            $arquivo = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO pareceres_deliberacoes (assunto, tipo, numero, relator, data_emissao, resumo, arquivo_pdf, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$assunto, $tipo, $numero, $relator, $data_em, $resumo, $arquivo, $ativo]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro no upload: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Ato / Comunicado Oficial</h2>
        <div class="text-muted small">Adicione um novo documento ao acervo oficial da Ordem.</div>
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
                        <label class="form-label text-uppercase fw-bold text-muted small">Assunto / Título do Parecer</label>
                        <input type="text" name="assunto" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Incompatibilidade de Exercício Profissional..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Resumo / Ementa</label>
                        <textarea name="resumo" class="form-control bg-light border-0" rows="10" placeholder="Sumário jurídico da decisão..."></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número / Ref. Oficial</label>
                            <input type="text" name="numero" class="form-control border-0 py-3" placeholder="Ex: Par. 04/2023">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Documento</label>
                            <select name="tipo" class="form-select border-0 shadow-sm py-2">
                                <option value="comunicado">Comunicado</option>
                                <option value="parecer">Parecer Jurídico</option>
                                <option value="deliberacao">Deliberação Oficial</option>
                                <option value="anuncio">Anúncio / Aviso</option>
                                <option value="edital">Edital Público</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Relator Responsável</label>
                            <input type="text" name="relator" class="form-control border-0 py-2 small" placeholder="Ex: Dr. António Lopes">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Emissão</label>
                            <input type="date" name="data_emissao" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Arquivo PDF (Oficial)</label>
                            <div class="border rounded p-4 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('pdf_input').click();">
                                <i class="fas fa-file-pdf fa-2x text-danger opacity-25 mb-3"></i>
                                <div class="fw-bold small text-muted">Aperte aqui para upload</div>
                                <div id="file-name" class="x-small text-primary mt-2"></div>
                                <input type="file" name="arquivo_pdf" id="pdf_input" class="d-none" accept=".pdf">
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Confirmar Parecer</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('pdf_input').onchange = function() {
        if(this.files[0]) {
            document.getElementById('file-name').innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + this.files[0].name;
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
