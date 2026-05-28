<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM documentos_publicos WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();
    if(!$doc) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $tipo = $_POST['tipo'];
    $num_doc = $_POST['numero_documento'] ?? '';
    $data_doc = $_POST['data_documento'] ?: date('Y-m-d');
    $descricao = $_POST['descricao'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $orgao_socio_id = !empty($_POST['orgao_socio_id']) ? intval($_POST['orgao_socio_id']) : null;
    $subtipo = ($tipo === 'comunicado') ? ($_POST['subtipo'] ?? 'comunicado') : null;
    
    $arquivo = $doc['arquivo'];
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['arquivo_pdf']['name'], PATHINFO_EXTENSION);
        $new_filename = 'doc_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $upload_dir . $new_filename)) {
            // Delete old file if exists
            if (!empty($doc['arquivo']) && file_exists($upload_dir . $doc['arquivo'])) {
                unlink($upload_dir . $doc['arquivo']);
            }
            $arquivo = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE documentos_publicos SET titulo = ?, tipo = ?, subtipo = ?, numero_documento = ?, data_documento = ?, descricao = ?, arquivo = ?, ativo = ?, orgao_socio_id = ? WHERE id = ?");
        $stmt->execute([$titulo, $tipo, $subtipo, $num_doc, $data_doc, $descricao, $arquivo, $ativo, $orgao_socio_id, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Documento</h2>
        <div class="text-muted small">Altere os daddos e anexo do documento #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Título do Documento</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($doc['titulo']); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo / Categoria</label>
                            <select name="tipo" id="tipo_select" class="form-select border-0 bg-light p-2 small">
                                <option value="publicacao" <?php echo $doc['tipo'] == 'publicacao'?'selected':''; ?>>Publicação Geral</option>
                                <option value="parecer" <?php echo $doc['tipo'] == 'parecer'?'selected':''; ?>>Parecer Jurídico</option>
                                <option value="deliberacao" <?php echo $doc['tipo'] == 'deliberacao'?'selected':''; ?>>Deliberação</option>
                                <option value="comunicado" <?php echo $doc['tipo'] == 'comunicado'?'selected':''; ?>>Comunicado Oficial</option>
                                <option value="orcamento" <?php echo $doc['tipo'] == 'orcamento'?'selected':''; ?>>Orçamento / Relatório</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número de Referência</label>
                            <input type="text" name="numero_documento" class="form-control border-0 bg-light p-2" value="<?php echo htmlspecialchars($doc['numero_documento']); ?>">
                        </div>
                    </div>

                    <div class="mb-4" id="subtipo_container" style="display: none;">
                        <label class="form-label text-uppercase fw-bold text-muted small">Subtipo de Comunicado (Obrigatório para Comunicados)</label>
                        <select name="subtipo" class="form-select border-0 bg-light p-2 small">
                            <option value="comunicado" <?php echo $doc['subtipo'] == 'comunicado'?'selected':''; ?>>Comunicado Geral</option>
                            <option value="circular" <?php echo $doc['subtipo'] == 'circular'?'selected':''; ?>>Circular</option>
                            <option value="nota-pesar" <?php echo $doc['subtipo'] == 'nota-pesar'?'selected':''; ?>>Nota de Pesar</option>
                            <option value="comunicado-imprensa" <?php echo $doc['subtipo'] == 'comunicado-imprensa'?'selected':''; ?>>Comunicado de Imprensa</option>
                            <option value="convocatoria-ag" <?php echo $doc['subtipo'] == 'convocatoria-ag'?'selected':''; ?>>Convocatória da AG</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Assinatura / Órgão Responsável (Opcional)</label>
                        <div class="text-muted small mb-2">Associe este documento a um órgão social para exibir o nome, cargo e assinatura digital correspondente.</div>
                        <select name="orgao_socio_id" class="form-select border-0 bg-light p-2 small">
                            <option value="">-- Nenhuma assinatura associada --</option>
                            <?php
                            $orgaos_opt = $pdo->query("SELECT id, nome, cargo FROM orgaos_sociais WHERE ativo = 1 ORDER BY cargo ASC, nome ASC")->fetchAll();
                            foreach ($orgaos_opt as $o) {
                                $selected = ($doc['orgao_socio_id'] == $o['id']) ? 'selected' : '';
                                echo "<option value='{$o['id']}' {$selected}>" . htmlspecialchars("{$o['nome']} ({$o['cargo']})") . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Conteúdo / Descrição do Documento</label>
                        <div class="text-muted small mb-2">Para comunicados, deliberações, pareceres, notas de pesar ou circulares, deve colar o texto completo abaixo para leitura no site. O documento original (PDF) será fornecido como anexo para download.</div>
                        <textarea name="descricao" class="form-control bg-light border-0" rows="12" placeholder="Cole aqui o texto completo do documento..."><?php echo htmlspecialchars($doc['descricao']); ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data do Documento</label>
                            <input type="date" name="data_documento" class="form-control border-0" value="<?php echo $doc['data_documento']; ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Arquivo Digital (Atual PDF)</label>
                            <div class="bg-white p-3 border rounded mb-3 text-center">
                                <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                                <div class="small fw-bold text-truncate"><?php echo $doc['arquivo']; ?></div>
                                <a href="/oagb/uploads/<?php echo $doc['arquivo']; ?>" target="_blank" class="btn btn-sm btn-link text-decoration-none small"><i class="fas fa-eye me-1"></i> Visualizar Atual</a>
                            </div>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('pdf_input').click();">
                                <i class="fas fa-sync-alt text-muted mb-2"></i>
                                <div class="small text-muted">Trocar Anexo PDF</div>
                                <div id="file-name" class="x-small text-primary mt-2"></div>
                                <input type="file" name="arquivo_pdf" id="pdf_input" class="d-none" accept=".pdf">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Público no site?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" <?php echo $doc['ativo'] ? 'checked':''; ?>>
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

<script>
    document.getElementById('pdf_input').onchange = function() {
        if(this.files[0]) {
            document.getElementById('file-name').innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + this.files[0].name;
            document.getElementById('file-name').classList.add('fw-bold');
        }
    }

    const tipoSelect = document.getElementById('tipo_select');
    const subtipoContainer = document.getElementById('subtipo_container');
    if (tipoSelect && subtipoContainer) {
        function checkTipo() {
            if (tipoSelect.value === 'comunicado') {
                subtipoContainer.style.display = 'block';
            } else {
                subtipoContainer.style.display = 'none';
            }
        }
        tipoSelect.addEventListener('change', checkTipo);
        checkTipo();
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
