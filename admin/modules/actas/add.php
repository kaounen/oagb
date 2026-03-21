<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $data = $_POST['data_reuniao'];
    $conteudo = $_POST['conteudo'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("INSERT INTO gestao_actas (titulo, data_reuniao, conteudo, status, criada_por) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $data, $conteudo, $status, 1]); // Mock user 1
    
    require_once __DIR__ . '/../../includes/LogHelper.php';
    LogHelper::log($pdo, 'MINUTES_ADD', "Lavrou a acta: $titulo", 'gestao_actas', $pdo->lastInsertId());

    header("Location: index.php?success=1"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Nova Acta</h2>
        <div class="text-muted small">Registo oficial de deliberações e reuniões deliberativas da Ordem.</div>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="form-label small fw-bold text-muted text-uppercase">Tìtulo da Reunião / Assembleia</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required placeholder="Ex: Assembleia Geral Extraordinária - Março 2026">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Data do Pleito / Reunião</label>
                <input type="date" name="data_reuniao" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Conteúdo Integrando Deliberações</label>
            <textarea name="conteudo" class="form-control border-0 bg-light p-4" rows="15" required placeholder="Digite o conteudo oficial da acta..."></textarea>
            <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Utilize este espaço para transcrever todas as decisões tomadas em assembleia oficial da Ordem.</div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-4">
                <select name="status" class="form-select border-0 bg-light p-3 fw-bold text-uppercase small text-muted">
                    <option value="rascunho">MANTER COMO RASCUNHO</option>
                    <option value="finalizada">FINALIZAR E ASSINAR DIGITALMENTE</option>
                </select>
            </div>
            <div class="col-md-8 text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">Registrar em Livro Digital</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
