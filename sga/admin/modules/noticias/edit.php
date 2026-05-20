<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

require_once __DIR__ . '/../../includes/AttachmentHelper.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';

// Handle Attachment Deletion
if (isset($_GET['delete_attachment'])) {
    AttachmentHelper::delete($pdo, $_GET['delete_attachment']);
    header("Location: edit.php?id=" . $id . "&att_deleted=1");
    exit;
}

// Handle Gallery Deletion
if (isset($_GET['delete_gallery'])) {
    GalleryHelper::delete($pdo, 'noticia', $_GET['delete_gallery']);
    header("Location: edit.php?id=" . $id . "&gal_deleted=1");
    exit;
}

// Fetch Existing News
try {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();
    
    if (!$noticia) {
        header("Location: index.php");
        exit;
    }

    $attachments = AttachmentHelper::get($pdo, 'noticia', $id);
    $gallery = GalleryHelper::get($pdo, 'noticia', $id);
} catch (PDOException $e) {
    header("Location: index.php");
    exit;
}

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $data_pub = $_POST['data'] . ' ' . date('H:i:s');
    $conteudo = $_POST['corpo'] ?? '';
    $legenda = $_POST['legendaFoto1'] ?? '';
    $cat_tipo = $_POST['categoria_tipo'] ?? 'Notícia';
    
    $imagem = $noticia['imagem_destaque'];
    
    // Photo upload handling
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('news_') . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $new_filename)) {
            // Delete old file if exists
            if (!empty($noticia['imagem_destaque']) && file_exists($upload_dir . $noticia['imagem_destaque'])) {
                unlink($upload_dir . $noticia['imagem_destaque']);
            }
            $imagem = $new_filename;
        }
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));

    // Handle Quick PDF Attachment
    $legenda_pdf = $_POST['legenda_anexo'] ?? '';
    $pdf_col_sql = "";
    $pdf_params = [];
    if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../uploads/';
        $original_name = basename($_FILES['pdf_anexo']['name']);
        $safe_name = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $original_name);
        $pdf_name = time() . '_' . $safe_name;
        
        if (move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name)) {
            if (!empty($noticia['ficheiro_anexo']) && file_exists($upload_dir . $noticia['ficheiro_anexo'])) {
                unlink($upload_dir . $noticia['ficheiro_anexo']);
            }
            $pdf_col_sql = ", ficheiro_anexo = ?, legenda_anexo = ?";
            $pdf_params = [$pdf_name, $legenda_pdf];
        }
    } else {
        $pdf_col_sql = ", legenda_anexo = ?";
        $pdf_params = [$legenda_pdf];
    }

    try {
        $sql = "UPDATE noticias SET titulo = ?, data_publicacao = ?, conteudo = ?, imagem_destaque = ?, resumo = ?, categoria_tipo = ?, slug = ? $pdf_col_sql WHERE id = ?";
        $all_params = array_merge([$titulo, $data_pub, $conteudo, $imagem, $legenda, $cat_tipo, $slug], $pdf_params, [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($all_params);

        // Handle Multiple Attachments
        if (isset($_FILES['attachments'])) {
            AttachmentHelper::save($pdo, 'noticia', $id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        }

        // Update existing attachments metadata
        if (isset($_POST['att_desc'])) {
            foreach ($_POST['att_desc'] as $att_id => $desc) {
                AttachmentHelper::update($pdo, $att_id, $desc);
            }
        }

        // Handle Gallery Uploads
        if (isset($_FILES['gallery_files'])) {
            GalleryHelper::save($pdo, 'noticia', $id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        }

        // Update Gallery Metadata
        if (isset($_POST['gal_title'])) {
            foreach ($_POST['gal_title'] as $img_id => $title) {
                $desc = $_POST['gal_desc'][$img_id] ?? '';
                $order = $_POST['gal_order'][$img_id] ?? 0;
                GalleryHelper::update($pdo, 'noticia', $img_id, $title, $desc, $order);
            }
        }

        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao atualizar notícia: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Notícia</h2>
        <div class="text-muted small">Modifique os detallhes do artigo #<?php echo $id; ?>.</div>
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
                        <label class="form-label text-uppercase fw-bold text-muted small">Título da Notícia</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Corpo do Artigo / Texto Completo</label>
                        <textarea name="corpo" id="editor" class="form-control bg-light border-0" rows="15"><?php echo $noticia['conteudo']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0">
                        <div class="card-body p-4">
                             <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Tipo de Conteúdo</label>
                                <select name="categoria_tipo" class="form-select border-0 shadow-sm py-2">
                                    <option value="Notícia" <?php echo $noticia['categoria_tipo'] == 'Notícia' ? 'selected' : ''; ?>>Notícia / Artigo</option>
                                    <option value="Anúncio" <?php echo $noticia['categoria_tipo'] == 'Anúncio' ? 'selected' : ''; ?>>Anúncio Oficial</option>
                                    <option value="Aviso" <?php echo $noticia['categoria_tipo'] == 'Aviso' ? 'selected' : ''; ?>>Aviso / Nota</option>
                                    <option value="Edital" <?php echo $noticia['categoria_tipo'] == 'Edital' ? 'selected' : ''; ?>>Edital / Concurso</option>
                                </select>
                             </div>

                             <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Data de Publicação</label>
                                <input type="date" name="data" class="form-control border-0" value="<?php echo date('Y-m-d', strtotime($noticia['data_publicacao'])); ?>" required>
                             </div>

                             <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Imagem de Destaque</label>
                                <div class="position-relative mb-3">
                                    <img id="preview" src="/oagb/uploads/<?php echo $noticia['imagem_destaque']; ?>" class="img-fluid rounded shadow-sm <?php echo empty($noticia['imagem_destaque']) ? 'd-none' : ''; ?>">
                                    <?php if(!empty($noticia['imagem_destaque'])): ?>
                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteMedia(<?php echo $id; ?>, 'highlight_noticia', this);">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('foto1_input').click();">
                                    <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i>
                                    <div class="small text-muted">Trocar ou carregar imagem</div>
                                    <input type="file" name="foto1" id="foto1_input" class="d-none" accept="image/*">
                                </div>
                             </div>

                             <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Legenda da Imagem / Resumo</label>
                                <input type="text" name="legendaFoto1" class="form-control border-0" value="<?php echo htmlspecialchars($noticia['resumo']); ?>">
                             </div>

                             <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF (Quick Download)</label>
                                <div class="p-3 border rounded bg-white shadow-sm mb-2">
                                    <?php if(!empty($noticia['ficheiro_anexo'])): ?>
                                        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded border position-relative">
                                            <div class="me-3">
                                                <i class="far fa-file-pdf fa-2x text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1 min-width-0" style="overflow: hidden;">
                                                <div class="small fw-bold text-truncate" title="<?php echo htmlspecialchars($noticia['ficheiro_anexo']); ?>" style="max-width: 100%;"><?php echo htmlspecialchars(preg_replace('/^[0-9]+_/', '', $noticia['ficheiro_anexo'])); ?></div>
                                                <a href="/oagb/uploads/<?php echo $noticia['ficheiro_anexo']; ?>?v=<?php echo time(); ?>" target="_blank" class="x-small text-primary text-decoration-none">Ver Ficheiro Atual</a>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger border-0" onclick="deleteMedia(<?php echo $id; ?>, 'quick_pdf_noticia', this); document.querySelector('[name=legenda_anexo]').value='';">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Substituir ou Carregar Novo:</label>
                                        <input type="file" name="pdf_anexo" class="form-control form-control-sm border-0 bg-light" accept=".pdf">
                                    </div>
                                    
                                    <div>
                                        <label class="x-small fw-bold text-muted text-uppercase d-block mb-1">Título/Legenda do PDF:</label>
                                        <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light" placeholder="Ex: Baixar Edital Completo" value="<?php echo htmlspecialchars($noticia['legenda_anexo'] ?? ''); ?>">
                                    </div>
                                </div>
                             </div>

                             <!-- Galeria Slider Component -->
                             <?php 
                             $type = 'noticia';
                             $gallery = GalleryHelper::get($pdo, 'noticia', $id);
                             require __DIR__ . '/../../includes/partials/gallery_form.php'; 
                             ?>

                             <!-- Múltiplos Anexos Component -->
                             <?php 
                             $entity_type = 'noticia';
                             $entity_id = $id;
                             require __DIR__ . '/../../includes/partials/attachments_form.php'; 
                             ?>

                             <hr class="my-4">

                             <button type="submit" class="btn btn-login w-100 py-3 shadow-sm">
                                 <i class="fas fa-save me-2"></i> Gravar Alterações
                             </button>
                             <a href="index.php" class="btn btn-light w-100 mt-2 py-3 border">Cancelar</a>
                        </div>
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
    }).catch(error => console.error(error));

    document.getElementById('foto1_input').onchange = evt => {
        const [file] = document.getElementById('foto1_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
