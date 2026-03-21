<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM comissoes WHERE id = ?");
    $stmt->execute([$id]);
    $comm = $stmt->fetch();
    if(!$comm) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Form Process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $presidente = $_POST['presidente'] ?? '';
    $desc = $_POST['descricao'] ?? '';
    $membros = $_POST['membros'] ?? '';
    $area = $_POST['area_atuacao'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE comissoes SET nome = ?, presidente = ?, descricao = ?, membros = ?, area_atuacao = ?, ativo = ? WHERE id = ?");
        $stmt->execute([$nome, $presidente, $desc, $membros, $area, $ativo, $id]);
        
        header("Location: index.php?updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Comissão / Orgão</h2>
        <div class="text-muted small">Modifique os detatalhes da comissão #<?php echo $id; ?>.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Nome da Comissão</label>
                        <input type="text" name="nome" class="form-control form-control-lg border-0 bg-light" value="<?php echo htmlspecialchars($comm['nome']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Propósito</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="10"><?php echo $comm['descricao']; ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Presidente</label>
                            <input type="text" name="presidente" class="form-control border-0 py-3" value="<?php echo htmlspecialchars($comm['presidente']); ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Área de Atuação</label>
                            <input type="text" name="area_atuacao" class="form-control border-0 py-3" value="<?php echo htmlspecialchars($comm['area_atuacao']); ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Membros (Separados por virgula)</label>
                            <textarea name="membros" class="form-control border-0" rows="5"><?php echo $comm['membros']; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small d-block">Visibilidade</label>
                            <div class="form-check form-switch p-0 pt-2 border-top">
                                <span class="me-3 small text-muted">Exibir na Ordem?</span>
                                <input class="form-check-input float-end" type="checkbox" name="ativo" <?php echo $comm['ativo'] ? 'checked':''; ?>>
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

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
