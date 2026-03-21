<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $data = $_POST['data_pleito'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("INSERT INTO gestao_eleicoes (titulo, data_pleito, status) VALUES (?, ?, ?)");
    $stmt->execute([$titulo, $data, $status]);
    
    header("Location: index.php?success=created"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Nova Convocatória Eleitoral</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white">
    <form method="POST">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="form-label small fw-bold text-muted">Título do Pleito</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required placeholder="Ex: Eleições Bastonário 2026-2029">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Data do Pleito</label>
                <input type="date" name="data_pleito" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold text-muted">Status de Acesso</label>
                <select name="status" class="form-select border-0 bg-light p-3 fw-bold text-muted">
                    <option value="aberto">ABERTO (Votação Ativa)</option>
                    <option value="fechado" selected>FECHADO (Rascunho)</option>
                    <option value="concluido">CONCLUÍDO (Arquivo)</option>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Criar Pleito Oficial</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
