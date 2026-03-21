<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $desc = $_POST['descricao'];
    $data = $_POST['data_inicio'];
    $vagas = $_POST['vagas'];
    $preco = $_POST['preco'];
    
    $stmt = $pdo->prepare("INSERT INTO gestao_cursos (titulo, descricao, data_inicio, vagas, preco) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $desc, $data, $vagas, $preco]);
    
    header("Location: index.php?success=created"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Lançar Nova Formação</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="form-label small fw-bold text-muted">Título do Curso / Seminário</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required placeholder="Ex: Reforma do Código Comercial 2026">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Data de Início</label>
                <input type="date" name="data_inicio" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Vagas Disponíveis</label>
                <input type="number" name="vagas" class="form-control border-0 bg-light p-3" value="50" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Preço Unitário (CFA)</label>
                <input type="number" name="preco" class="form-control border-0 bg-light p-3" value="0" required>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted">Ementa / Descrição do Programa</label>
                <textarea name="descricao" class="form-control border-0 bg-light p-4" rows="10" required placeholder="Digite os temas a serem abordados..."></textarea>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Publicar Formação</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
