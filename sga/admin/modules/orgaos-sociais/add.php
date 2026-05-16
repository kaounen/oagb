<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cargo = $_POST['cargo'];
    $biografia = $_POST['biografia'];
    $inicio = $_POST['mandato_inicio'];
    $fim = $_POST['mandato_fim'] ?: null;
    $ordem = (int)$_POST['ordem_exibicao'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $superior_id = $_POST['superior_id'] ?: null;
    $diretivo_id = (int)$_POST['orgao_diretivo_id'];
    
    // Handle Photo Upload
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir_photo = __DIR__ . '/../../../uploads/orgaos/';
        if (!file_exists($upload_dir_photo)) mkdir($upload_dir_photo, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = 'membro_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir_photo . $new_filename)) {
            $foto = $new_filename;
        }
    }

    // Handle Signature Upload
    $assinatura = '';
    if (isset($_FILES['assinatura']) && $_FILES['assinatura']['error'] === UPLOAD_ERR_OK) {
        $upload_dir_sig = __DIR__ . '/../../../uploads/assinaturas/';
        if (!file_exists($upload_dir_sig)) mkdir($upload_dir_sig, 0777, true);
        
        $file_ext = pathinfo($_FILES['assinatura']['name'], PATHINFO_EXTENSION);
        $new_filename = 'assinatura_membro_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['assinatura']['tmp_name'], $upload_dir_sig . $new_filename)) {
            $assinatura = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO orgaos_sociais (nome, cargo, biografia, foto, assinatura, mandato_inicio, mandato_fim, ordem_exibicao, ativo, superior_id, orgao_diretivo_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $cargo, $biografia, $foto, $assinatura, $inicio, $fim, $ordem, $ativo, $superior_id, $diretivo_id]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao registar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Registar Membro</h2>
        <div class="text-muted small">Adicione um novo membro aos Órgãos Sociais / Direção.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-uppercase fw-bold text-muted small">Nome Completo</label>
                            <input type="text" name="nome" class="form-control form-control-lg border-0 bg-light" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Cargo / Função</label>
                            <input type="text" name="cargo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Bastonário, Vogal..." required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">Órgão / Grupo Administrativo</label>
                            <select name="orgao_diretivo_id" class="form-select form-control-lg border-0 bg-light">
                                <?php 
                                $grupos = $pdo->query("SELECT * FROM orgaos_diretivos ORDER BY nome ASC");
                                while($g = $grupos->fetch()) {
                                    echo "<option value=\"{$g['id']}\">{$g['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">Superior Direto (Hierarquia)</label>
                            <select name="superior_id" class="form-select form-control-lg border-0 bg-light">
                                <option value="">Nenhum (Topo da Hierarquia)</option>
                                <?php 
                                $potenciais = $pdo->query("SELECT id, nome, cargo FROM orgaos_sociais ORDER BY nome ASC");
                                while($p = $potenciais->fetch()) {
                                    echo "<option value=\"{$p['id']}\">{$p['nome']} ({$p['cargo']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Biografia / Nota Curricular</label>
                        <textarea name="biografia" id="editor" class="form-control bg-light border-0" rows="10"></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small text-primary">Início Mandato</label>
                            <input type="date" name="mandato_inicio" class="form-control border-0 py-2 small" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Fim Mandato</label>
                            <input type="date" name="mandato_fim" class="form-control border-0 py-2 small" placeholder="Opcional se em exercíco">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Ordem de Exibição</label>
                            <input type="number" name="ordem_exibicao" class="form-control border-0 py-2 small" value="0">
                            <div class="x-small text-muted mt-1">Números menores aparecem primeiro.</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir publicamente?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" checked>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Fotografia Oficial</label>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed shadow-sm" onclick="document.getElementById('img_input').click();">
                                <i class="fas fa-user-circle fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Aperte aqui para upload</div>
                                <input type="file" name="foto" id="img_input" class="d-none" accept="image/*">
                            </div>
                            <img id="preview" class="img-fluid mt-3 rounded shadow-sm d-none">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Assinatura Digitalized</label>
                            <div class="border rounded p-3 text-center bg-white cursor-pointer border-dashed shadow-sm" onclick="document.getElementById('sig_input').click();">
                                <i class="fas fa-pen-nib fa-2x text-muted mb-2"></i>
                                <div class="small text-muted">Aperte aqui para upload</div>
                                <input type="file" name="assinatura" id="sig_input" class="d-none" accept="image/*">
                            </div>
                            <img id="preview_sig" class="img-fluid mt-3 rounded shadow-sm d-none bg-white p-1 border">
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm">Adicionar Membro</button>
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
    document.getElementById('sig_input').onchange = evt => {
        const [file] = document.getElementById('sig_input').files;
        if (file) {
            document.getElementById('preview_sig').src = URL.createObjectURL(file);
            document.getElementById('preview_sig').classList.remove('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
