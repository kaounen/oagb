<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Form Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $tipo = $_POST['tipo'];
    $num_doc = $_POST['numero_documento'] ?? '';
    $data_doc = $_POST['data_documento'] ?: date('Y-m-d');
    $descricao = $_POST['descricao'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // PDF Upload handling
    $arquivo = '';
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['arquivo_pdf']['name'], PATHINFO_EXTENSION);
        $new_filename = 'doc_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $upload_dir . $new_filename)) {
            $arquivo = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO documentos_publicos (titulo, tipo, numero_documento, data_documento, descricao, arquivo, ativo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $tipo, $num_doc, $data_doc, $descricao, $arquivo, $ativo]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro no upload: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Carregar Novo Documento</h2>
        <div class="text-muted small">Adicione PDFs, estatutos ou regulamentos ao repositório público.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título do Documento</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Estatutos da Ordem dos Advogados (Revisão 2024)..." required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo / Categoria</label>
                            <select name="tipo" class="form-select border-0 bg-light p-2 small">
                                <option value="publicacao">Publicação Geral</option>
                                <option value="parecer">Parecer Jurídico</option>
                                <option value="deliberacao">Deliberação</option>
                                <option value="comunicado">Comunicado Oficial</option>
                                <option value="orcamento">Orçamento / Relatório</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número de Referência</label>
                            <input type="text" name="numero_documento" class="form-control border-0 bg-light p-2" placeholder="Ex: Reg. 01/2024">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição Breve</label>
                        <textarea name="descricao" class="form-control bg-light border-0" rows="5" placeholder="Sumário do documento para pesquisa..."></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data do Documento</label>
                            <input type="date" name="data_documento" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Arquivo Digital (PDF)</label>
                            <div class="border rounded p-4 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('pdf_input').click();">
                                <i class="fas fa-file-pdf fa-3x text-danger opacity-25 mb-3"></i>
                                <div class="fw-bold small text-muted">Aperte aqui para upload</div>
                                <div id="file-name" class="x-small text-primary mt-2"></div>
                                <input type="file" name="arquivo_pdf" id="pdf_input" class="d-none" accept=".pdf">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Público no site?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" checked>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Confirmar Upload</button>
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
            document.getElementById('file-name').classList.add('fw-bold');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
