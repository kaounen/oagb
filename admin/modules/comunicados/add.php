<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Form Process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $link_url = $_POST['link_url'] ?? '';
    $link_txt = $_POST['link_texto'] ?? 'Saiba mais';
    $data_ini = $_POST['data_inicio'] ?: date('Y-m-d');
    $data_fim = $_POST['data_fim'] ?: NULL;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Photo handling
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $new_filename = 'anun_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $new_filename)) {
            $imagem = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO anuncios (titulo, descricao, link_url, link_texto, imagem, data_inicio, data_fim, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descricao, $link_url, $link_txt, $imagem, $data_ini, $data_fim, $ativo]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro no registo: " . $e->getMessage();
    }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Anúncio / Comunicado</h2>
        <div class="text-muted small">Crie informativos oficiais para destaque no portal da Ordem.</div>
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
                        <label class="form-label text-uppercase fw-bold text-muted small">Título do Comunicado</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Aviso Importante aos Advogados Estagiários..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Conteúdo Completo</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="10"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Link de Destino (URL)</label>
                            <input type="text" name="link_url" class="form-control bg-light border-0" placeholder="https://oagb.gw/anexo.pdf ou pagina.php">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Texto do Link</label>
                            <input type="text" name="link_texto" class="form-control bg-light border-0" placeholder="Ex: Ler Comunicado Completo">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Iniciio</label>
                            <input type="date" name="data_inicio" class="form-control border-0" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Término (Opcional)</label>
                            <input type="date" name="data_fim" class="form-control border-0">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Ativo no portal?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" checked>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Imagem Ilustrativa</label>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Carregar Imagem</div>
                                <input type="file" name="imagem" id="img_input" class="d-none" accept="image/*">
                            </div>
                            <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Publicar Anúncio</button>
                        <a href="index.php" class="btn btn-light w-100 py-3 border">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
    document.getElementById('img_input').onchange = evt => {
        const [file] = document.getElementById('img_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
