<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entidade = $_POST['entidade_parceira'];
    $pais = $_POST['pais'];
    $tipo = $_POST['tipo_acordo'];
    $objetivo = $_POST['objetivo'];
    $inicio = $_POST['data_assinatura'] ?: null;
    $fim = $_POST['data_validade'] ?: null;
    $status = $_POST['status'];
    
    // Handle File Upload (PDF Agreement)
    $doc = '';
    if (isset($_FILES['documento_url']) && $_FILES['documento_url']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/cooperacao/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['documento_url']['name'], PATHINFO_EXTENSION);
        $new_filename = 'acordo_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['documento_url']['tmp_name'], $upload_dir . $new_filename)) {
            $doc = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO parcerias_internacionais (entidade_parceira, pais, tipo_acordo, objetivo, data_assinatura, data_validade, documento_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$entidade, $pais, $tipo, $objetivo, $inicio, $fim, $doc, $status]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao registar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Registar Cooperação</h2>
        <div class="text-muted small">Adicione um novo acordo ou protocolo de cooperação institucional.</div>
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
                            <input type="text" name="entidade_parceira" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Ordem dos Advogados Portugueses" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-uppercase fw-bold text-muted small">País / Região</label>
                            <input type="text" name="pais" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Portugal, Angola, Internacional..." required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Objetivo / Âmbito do Acordo</label>
                        <textarea name="objetivo" id="editor" class="form-control bg-light border-0" rows="10"></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Instrumento</label>
                            <input type="text" name="tipo_acordo" class="form-control border-0 py-2 small" placeholder="Ex: Protocolo de Formação, Geminação..." required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Assinatura</label>
                            <input type="date" name="data_assinatura" class="form-control border-0 py-2 small">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Validade</label>
                            <input type="date" name="data_validade" class="form-control border-0 py-2 small">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Estado Vigente</label>
                            <select name="status" class="form-select border-0 py-2 small">
                                <option value="Ativo">Vigente (Ativo)</option>
                                <option value="Expirado">Concluído (Expirado)</option>
                                <option value="Em Renovação">Em Renovação</option>
                            </select>
                        </div>
                        
                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Documento PDF (Cópia Digital)</label>
                            <div class="input-group">
                                <input type="file" name="documento_url" class="form-control border-0 bg-white shadow-sm" accept=".pdf">
                            </div>
                            <div class="x-small text-muted mt-2">Apenas ficheiros PDF do protocolo assinado.</div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm fw-bold">PUBLICAR ACORDO</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
