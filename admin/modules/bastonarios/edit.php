<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

// Fetch Bastonario
try {
    $stmt = $pdo->prepare("SELECT * FROM bastonarios WHERE id = ?");
    $stmt->execute([$id]);
    $bastonario = $stmt->fetch();
    if(!$bastonario) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'];
    $biografia = $_POST['biografia'];
    $inicio = $_POST['data_inicio_mandato'];
    $fim = $_POST['data_fim_mandato'] ?: null;
    $email = $_POST['email_contacto'];
    $is_atual = isset($_POST['is_atual']) ? 1 : 0;
    
    // Handle Photo Upload
    $foto = $bastonario['foto_url'];
    if (isset($_FILES['foto_url']) && $_FILES['foto_url']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/bastonarios/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto_url']['name'], PATHINFO_EXTENSION);
        $new_filename = 'bastonario_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['foto_url']['tmp_name'], $upload_dir . $new_filename)) {
            if ($bastonario['foto_url'] && file_exists($upload_dir . $bastonario['foto_url'])) {
                unlink($upload_dir . $bastonario['foto_url']);
            }
            $foto = $new_filename;
        }
    }

    // Handle Signature Upload
    $assinatura = $bastonario['assinatura_url'];
    if (isset($_FILES['assinatura_url']) && $_FILES['assinatura_url']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/assinaturas/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['assinatura_url']['name'], PATHINFO_EXTENSION);
        $new_filename = 'assinatura_bastonario_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['assinatura_url']['tmp_name'], $upload_dir . $new_filename)) {
            if ($bastonario['assinatura_url'] && file_exists($upload_dir . $bastonario['assinatura_url'])) {
                unlink($upload_dir . $bastonario['assinatura_url']);
            }
            $assinatura = $new_filename;
        }
    }

    try {
        if ($is_atual) {
            $pdo->query("UPDATE bastonarios SET is_atual = 0");
        }

        $stmt = $pdo->prepare("UPDATE bastonarios SET nome_completo = ?, biografia = ?, foto_url = ?, assinatura_url = ?, data_inicio_mandato = ?, data_fim_mandato = ?, email_contacto = ?, is_atual = ? WHERE id = ?");
        $stmt->execute([$nome, $biografia, $foto, $assinatura, $inicio, $fim, $email, $is_atual, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Bastonário</h2>
        <div class="text-muted small">Altere os detalhes do registo #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Nome Completo</label>
                        <input type="text" name="nome_completo" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($bastonario['nome_completo']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Biografia / Histórico</label>
                        <textarea name="biografia" id="editor" class="form-control bg-light border-0" rows="10"><?php echo $bastonario['biografia']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data Início Mandato</label>
                            <input type="date" name="data_inicio_mandato" class="form-control border-0 py-2 small" value="<?php echo $bastonario['data_inicio_mandato']; ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data Fim Mandato</label>
                            <input type="date" name="data_fim_mandato" class="form-control border-0 py-2 small" value="<?php echo $bastonario['data_fim_mandato']; ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Email Institucional</label>
                            <input type="email" name="email_contacto" class="form-control border-0 py-2 small" value="<?php echo $bastonario['email_contacto']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Bastonário em Exercício?</span>
                                <input class="form-check-input float-end" type="checkbox" name="is_atual" <?php echo $bastonario['is_atual'] ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Retrato Oficial Atual</label>
                             <?php if($bastonario['foto_url']): ?>
                                <img id="preview" src="/oagb/uploads/bastonarios/<?php echo $bastonario['foto_url']; ?>" class="img-fluid rounded shadow-sm mb-3">
                            <?php else: ?>
                                <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                            <?php endif; ?>
                            
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-sync-alt fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Trocar Retrato</div>
                                <input type="file" name="foto_url" id="img_input" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Assinatura Digitalized</label>
                             <?php if(!empty($bastonario['assinatura_url'])): ?>
                                <img id="preview_sig" src="/oagb/uploads/assinaturas/<?php echo $bastonario['assinatura_url']; ?>" class="img-fluid rounded shadow-sm mb-3 bg-white p-2 border">
                            <?php else: ?>
                                <img id="preview_sig" class="img-fluid mt-3 rounded shadow-sm d-none bg-white p-2 border">
                            <?php endif; ?>
                            
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed" onclick="document.getElementById('sig_input').click();">
                                <i class="fas fa-pen-nib fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Carregar Assinatura</div>
                                <input type="file" name="assinatura_url" id="sig_input" class="d-none" accept="image/*">
                            </div>
                            <div class="x-small text-muted mt-2">Use fundo transparente (PNG) para melhor resultado em certidões.</div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Guardar Alterações</button>
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
    document.getElementById('img_input').onchange = evt => {
        const [file] = document.getElementById('img_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').classList.remove('d-none');
        }
    }
    document.getElementById('sig_input').onchange = evt => {
        const [file] = document.getElementById('sig_input').files;
        if (file) {
            document.getElementById('preview_sig').src = URL.createObjectURL(file);
            document.getElementById('preview_sig').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
