<?php
require_once __DIR__ . '/../../includes/db.php';
$id = intval($_GET['id'] ?? 0);
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
    GalleryHelper::delete($pdo, 'estagio', $_GET['delete_gallery']);
    header("Location: edit.php?id=" . $id . "&gal_deleted=1");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM conteudos_paginas WHERE id = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_OBJ);
if (!$item) { echo '<div class="alert alert-danger">Não encontrado.</div>'; require_once __DIR__.'/../../includes/footer.php'; exit; }
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagina = trim($_POST['pagina'] ?? ''); $secao = trim($_POST['secao'] ?? '');
    $titulo = trim($_POST['titulo'] ?? ''); $icone = trim($_POST['icone'] ?? 'fas fa-info-circle');
    $conteudo = $_POST['conteudo'] ?? ''; $ordem = intval($_POST['ordem'] ?? 0);
    $status = $_POST['status'] ?? 'ativo';
    $resumo = $_POST['resumo'] ?? '';
    $legenda_anexo = $_POST['legenda_anexo'] ?? '';

    $imagem = $item->imagem_destaque;
    if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['foto1']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('cnt_') . '.' . $file_ext;
        if (move_uploaded_file($_FILES['foto1']['tmp_name'], $upload_dir . $new_filename)) {
            if (!empty($item->imagem_destaque) && file_exists($upload_dir . $item->imagem_destaque)) unlink($upload_dir . $item->imagem_destaque);
            $imagem = $new_filename;
        }
    }

    $ficheiro = $item->ficheiro_anexo;
    if (isset($_FILES['pdf_anexo']) && $_FILES['pdf_anexo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/';
        $original_name = basename($_FILES['pdf_anexo']['name']);
        $safe_name = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $original_name);
        $pdf_name = time() . '_' . $safe_name;
        if (move_uploaded_file($_FILES['pdf_anexo']['tmp_name'], $upload_dir . $pdf_name)) {
            if (!empty($item->ficheiro_anexo) && file_exists($upload_dir . $item->ficheiro_anexo)) unlink($upload_dir . $item->ficheiro_anexo);
            $ficheiro = $pdf_name;
        }
    }

    if (empty($titulo) || empty($pagina)) $errors[] = 'Título e Página são obrigatórios.';
    if (empty($errors)) {
        $pdo->prepare("UPDATE conteudos_paginas SET pagina=?,secao=?,titulo=?,icone=?,conteudo=?,ordem=?,status=?,imagem_destaque=?,resumo=?,ficheiro_anexo=?,legenda_anexo=? WHERE id=?")
            ->execute([$pagina,$secao,$titulo,$icone,$conteudo,$ordem,$status,$imagem,$resumo,$ficheiro,$legenda_anexo,$id]);

        $type = $pagina == 'estagio' ? 'estagio' : 'conteudos';

        if (isset($_FILES['attachments'])) AttachmentHelper::save($pdo, $type, $id, $_FILES['attachments'], $_POST['attachment_descriptions'] ?? []);
        if (isset($_POST['att_desc'])) foreach ($_POST['att_desc'] as $att_id => $desc) AttachmentHelper::update($pdo, $att_id, $desc);
        if (isset($_FILES['gallery_files'])) GalleryHelper::save($pdo, $type, $id, $_FILES['gallery_files'], $_POST['new_gal_title'] ?? [], $_POST['new_gal_desc'] ?? []);
        if (isset($_POST['gal_title'])) {
            foreach ($_POST['gal_title'] as $img_id => $title) {
                $desc = $_POST['gal_desc'][$img_id] ?? ''; $order = $_POST['gal_order'][$img_id] ?? 0;
                GalleryHelper::update($pdo, $type, $img_id, $title, $desc, $order);
            }
        }
        header('Location: index.php?msg=updated'); exit;
    }
}
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Editar Conteúdo #<?php echo $id; ?></h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST" enctype="multipart/form-data"><div class="row g-3">
    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold small">Página (slug)</label><input type="text" name="pagina" class="form-control" required value="<?php echo htmlspecialchars($item->pagina); ?>"></div>
            <div class="col-md-6"><label class="form-label fw-bold small">Secção</label><input type="text" name="secao" class="form-control" value="<?php echo htmlspecialchars($item->secao); ?>"></div>
            <div class="col-md-12"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($item->titulo); ?>"></div>
            <div class="col-md-12"><label class="form-label fw-bold small">Conteúdo (HTML)</label><textarea name="conteudo" id="editor" class="form-control" rows="15"><?php echo htmlspecialchars($item->conteudo); ?></textarea></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0"><div class="card-body p-4">
            <div class="mb-4">
                <label class="form-label fw-bold small">Ícone Visual</label>
                <div class="input-group">
                    <span class="input-group-text bg-white" id="icon-preview"><i class="<?php echo htmlspecialchars($item->icone); ?>"></i></span>
                    <select name="icone" class="form-select" onchange="document.querySelector('#icon-preview i').className = this.value">
                        <?php 
                        $icons = ['fas fa-balance-scale'=>'Justiça','fas fa-graduation-cap'=>'Academia','fas fa-users'=>'Cidadãos','fas fa-info-circle'=>'Informação','fas fa-briefcase'=>'Profissional','fas fa-file-contract'=>'Documentos'];
                        foreach ($icons as $class => $label): ?>
                        <option value="<?php echo $class; ?>" <?php echo ($item->icone === $class) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="<?php echo $item->ordem; ?>"></div>
            <div class="mb-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo" <?php echo $item->status==='ativo'?'selected':''; ?>>Ativo</option><option value="inativo" <?php echo $item->status==='inativo'?'selected':''; ?>>Inativo</option></select></div>
            
            <div class="mb-4">
                <label class="form-label text-uppercase fw-bold text-muted small">Imagem de Destaque</label>
                <div class="position-relative mb-3">
                    <img id="preview" src="/oagb/uploads/<?php echo $item->imagem_destaque; ?>" class="img-fluid rounded shadow-sm <?php echo empty($item->imagem_destaque) ? 'd-none' : ''; ?>">
                </div>
                <div class="border rounded p-3 text-center bg-white cursor-pointer" onclick="document.getElementById('foto1_input').click();">
                    <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i><div class="small text-muted">Trocar ou carregar imagem</div>
                    <input type="file" name="foto1" id="foto1_input" class="d-none" accept="image/*">
                </div>
            </div>
            <div class="mb-4"><label class="form-label text-uppercase fw-bold text-muted small">Legenda/Resumo</label><input type="text" name="resumo" class="form-control border-0 bg-white" value="<?php echo htmlspecialchars($item->resumo ?? ''); ?>"></div>
            
            <div class="mb-4">
                <label class="form-label text-uppercase fw-bold text-muted small">Documento PDF Rápido</label>
                <div class="p-3 border rounded bg-white shadow-sm mb-2">
                    <?php if(!empty($item->ficheiro_anexo)): ?>
                        <div class="small fw-bold text-truncate mb-2"><i class="far fa-file-pdf text-danger me-2"></i><?php echo htmlspecialchars($item->ficheiro_anexo); ?></div>
                    <?php endif; ?>
                    <input type="file" name="pdf_anexo" class="form-control form-control-sm border-0 bg-light mb-2" accept=".pdf">
                    <input type="text" name="legenda_anexo" class="form-control form-control-sm border-0 bg-light" placeholder="Título do PDF" value="<?php echo htmlspecialchars($item->legenda_anexo ?? ''); ?>">
                </div>
            </div>

            <?php $type = $item->pagina == 'estagio' ? 'estagio' : 'conteudos'; $gallery = GalleryHelper::get($pdo, $type, $id); require __DIR__ . '/../../includes/partials/gallery_form.php'; ?>
            <?php $entity_type = $type; $entity_id = $id; require __DIR__ . '/../../includes/partials/attachments_form.php'; ?>
            
            <button type="submit" class="btn btn-login w-100 py-3 mt-3 shadow-sm"><i class="fas fa-save me-2"></i> Guardar Alterações</button>
        </div></div>
    </div>
</div></form></div></div>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), { toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','insertTable','mediaEmbed','undo','redo'] }).catch(e=>console.error(e));
    document.getElementById('foto1_input').onchange = evt => { const [file] = document.getElementById('foto1_input').files; if (file) { document.getElementById('preview').src = URL.createObjectURL(file); document.getElementById('preview').classList.remove('d-none'); } }
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
