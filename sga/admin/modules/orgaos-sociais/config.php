<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modo = $_POST['modo_exibicao'];
    
    // Handle Image
    $image_path = $_POST['current_image'] ?? '';
    if (isset($_FILES['organograma_img']) && $_FILES['organograma_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/orgaos/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $new_name = 'organograma_' . time() . '.' . pathinfo($_FILES['organograma_img']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['organograma_img']['tmp_name'], $upload_dir . $new_name)) {
            $image_path = $new_name;
        }
    }

    // Handle PDF
    $pdf_path = $_POST['current_pdf'] ?? '';
    if (isset($_FILES['organograma_pdf']) && $_FILES['organograma_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/orgaos/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $new_pdf = 'organograma_' . time() . '.pdf';
        if (move_uploaded_file($_FILES['organograma_pdf']['tmp_name'], $upload_dir . $new_pdf)) {
            $pdf_path = $new_pdf;
        }
    }

    $stmt = $pdo->prepare("UPDATE orgaos_config SET modo_exibicao = ?, organograma_path = ?, organograma_pdf_path = ? WHERE id = 1");
    $stmt->execute([$modo, $image_path, $pdf_path]);
    $success = "Configurações atualizadas com sucesso.";
}

// Fetch Config
$config = $pdo->query("SELECT * FROM orgaos_config WHERE id = 1")->fetch();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Organograma OAGB</h2>
        <div class="text-muted small">Defina como a estrutura organizacional será exibida no frontend.</div>
    </div>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="current_image" value="<?php echo $config['organograma_path']; ?>">
    <input type="hidden" name="current_pdf" value="<?php echo $config['organograma_pdf_path']; ?>">

    <div class="row">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4">Metodo de Exibição Principal</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-check custom-option text-center p-3 border rounded h-100 <?php echo $config['modo_exibicao'] == 'auto' ? 'border-primary' : ''; ?>">
                            <label class="form-check-label w-100 cursor-pointer">
                                <input class="form-check-input" type="radio" name="modo_exibicao" value="auto" <?php echo $config['modo_exibicao'] == 'auto' ? 'checked' : ''; ?>>
                                <div class="mt-2">
                                    <i class="fas fa-sitemap fa-2x mb-2 text-primary"></i>
                                    <div class="fw-bold">Gerado Automático</div>
                                    <div class="x-small text-muted">Usa a hierarquia dos membros registados.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check custom-option text-center p-3 border rounded h-100 <?php echo $config['modo_exibicao'] == 'imagem' ? 'border-primary' : ''; ?>">
                            <label class="form-check-label w-100 cursor-pointer">
                                <input class="form-check-input" type="radio" name="modo_exibicao" value="imagem" <?php echo $config['modo_exibicao'] == 'imagem' ? 'checked' : ''; ?>>
                                <div class="mt-2">
                                    <i class="far fa-file-image fa-2x mb-2 text-primary"></i>
                                    <div class="fw-bold">Imagem Dinâmica</div>
                                    <div class="x-small text-muted">Exibe uma imagem estática/layout.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">Upload de Arquivos</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Organograma (Imagem - PNG/JPG)</label>
                    <input type="file" name="organograma_img" class="form-control" accept="image/*">
                    <?php if($config['organograma_path']): ?>
                        <div class="mt-3">
                            <img src="../../uploads/orgaos/<?php echo $config['organograma_path']; ?>" class="img-fluid rounded border p-1" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Organograma (PDF de Alta Resolução)</label>
                    <input type="file" name="organograma_pdf" class="form-control" accept="application/pdf">
                    <?php if($config['organograma_pdf_path']): ?>
                        <div class="mt-2">
                            <a href="../../uploads/orgaos/<?php echo $config['organograma_pdf_path']; ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="far fa-file-pdf me-1"></i> Visualizar PDF Atual</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card bg-login-subtle border-0 p-4 sticky-top" style="top: 100px;">
                <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i> Ajuda</h6>
                <p class="small opacity-75">Se escolher <b>Gerado Automático</b>, o frontend irá renderizar um grafo hierárquico baseado nos campos "Superior Direto" de cada membro.</p>
                <p class="small opacity-75">O modo <b>Imagem Dinâmica</b> é ideal se já tiver um organograma desenhado por um estúdio profissional.</p>
                <button type="submit" class="btn btn-login w-100 py-3 mt-4">Gravar Configurações</button>
            </div>
        </div>
    </div>
</form>

<style>
.cursor-pointer { cursor: pointer; }
.custom-option { transition: all 0.2s; }
.custom-option:hover { background: #f8f9fa; }
.custom-option .form-check-input { float: none; margin-bottom: 10px; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
