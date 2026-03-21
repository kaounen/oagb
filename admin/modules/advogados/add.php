<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Form Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'];
    $num_registo = $_POST['numero_registo'];
    $genero = $_POST['genero'];
    $data_insc = $_POST['data_inscricao'];
    $regiao = $_POST['regiao'];
    $localidade = $_POST['localidade'] ?? '';
    $telefone = $_POST['telefone'];
    $email = $_POST['email'] ?? '';
    
    // Photo handling
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../../gestao/assets/uploads/files/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = 'adv_' . time() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $new_filename)) {
            $foto = $new_filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO advogados (numero_registo, nome_completo, genero, data_inscricao, regiao, localidade, telefone, email, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$num_registo, $nome, $genero, $data_insc, $regiao, $localidade, $telefone, $email, $foto]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro no registo: " . $e->getMessage();
    }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Registar Profissional</h2>
        <div class="text-muted small">Adicionar novo advogado ao diretório oficial da OAGB.</div>
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
                    <!-- Bio Info -->
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Nome Completo do Advogado</label>
                            <input type="text" name="nome_completo" class="form-control bg-light border-0 py-3" placeholder="Ex: Dr. António Lopes Barreto" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número de Cédula / Registo</label>
                            <input type="text" name="numero_registo" class="form-control bg-light border-0 py-3" placeholder="Ex: 125/2023" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Género</label>
                            <select name="genero" class="form-select bg-light border-0 py-3">
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Inscrição</label>
                            <input type="date" name="data_inscricao" class="form-control bg-light border-0 py-3" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-4 mb-4 border-bottom pb-2">Localização & Contactos</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Região de Atuação</label>
                            <input type="text" name="regiao" class="form-control bg-light border-0 py-3" placeholder="Ex: Bissau" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Localidade / Morada</label>
                            <input type="text" name="localidade" class="form-control bg-light border-0 py-3" placeholder="Ex: Brada, Bissau">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Nº Identificação / BI</label>
                            <input type="text" name="bi_numero" class="form-control bg-light border-0 py-3" placeholder="Opcional...">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Telefone Principal</label>
                            <input type="text" name="telefone" class="form-control bg-light border-0 py-3" placeholder="Ex: 955 475 889" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Utilizador / E-mail Profissional</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-3" placeholder="exemplo@oagb.gw">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-3">
                        <div class="card-body">
                            <div class="mb-4 text-center">
                                <label class="form-label text-uppercase fw-bold text-muted small d-block">Fotografia Profissional</label>
                                <div class="bg-white rounded-circle p-2 shadow-sm d-inline-block position-relative mb-3 border">
                                    <img id="preview" src="/oagb/img/placeholder_user.png" class="rounded-circle" style="width: 151px; height: 151px; object-fit: cover; opacity: 0.1;">
                                    <div class="position-absolute translate-middle-x start-50 top-50" id="placeholder-icon">
                                        <i class="fas fa-camera fa-2x text-muted"></i>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle" onclick="document.getElementById('foto_input').click();">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                                <input type="file" name="foto" id="foto_input" class="d-none" accept="image/*">
                                <p class="x-small text-muted mt-2">Clique no ícone para carregar foto em formato Retrato/Tipo Passe (máx 2MB).</p>
                            </div>

                            <hr class="my-4">

                            <button type="submit" class="btn btn-login w-100 py-3 shadow-sm">
                                <i class="fas fa-save me-2"></i> Concluir Registo
                            </button>
                            <a href="index.php" class="btn btn-light w-100 mt-2 py-3 border">Descartar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('foto_input').onchange = evt => {
        const [file] = document.getElementById('foto_input').files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
            document.getElementById('preview').style.opacity = '1';
            document.getElementById('placeholder-icon').classList.add('d-none');
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
