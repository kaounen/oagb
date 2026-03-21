<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'];
    $reg = $_POST['numero_registo'];
    $email = $_POST['email'];
    $orientador = $_POST['orientador_id'];
    $fase = $_POST['fase_estagio'];
    
    $stmt = $pdo->prepare("INSERT INTO advogados_estagiarios (nome_completo, numero_registo, email, orientador_id, fase_estagio, status) VALUES (?, ?, ?, ?, ?, 'ativo')");
    $stmt->execute([$nome, $reg, $email, $orientador, $fase]);
    
    header("Location: index.php?success=created"); exit;
}

$orientadores = $pdo->query("SELECT id, nome_completo FROM advogados WHERE status = 'ativo' ORDER BY nome_completo")->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Inscrição de Estagiário</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white">
    <form method="POST">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nome Completo</label>
                <input type="text" name="nome_completo" class="form-control border-0 bg-light p-3" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Cédula No. (EST-XXXX)</label>
                <input type="text" name="numero_registo" class="form-control border-0 bg-light p-3" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Fase Inicial</label>
                <select name="fase_estagio" class="form-select border-0 bg-light p-3">
                    <option value="instrucao">INSTRUÇÃO</option>
                    <option value="pratica">PRÁTICA</option>
                    <option value="concluido">CONCLUÍDO</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Email Profissional</label>
                <input type="email" name="email" class="form-control border-0 bg-light p-3" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Patrono Orientador</label>
                <select name="orientador_id" class="form-select border-0 bg-light p-3" required>
                    <option value="">-- Selecione o Patrono --</option>
                    <?php foreach($orientadores as $o): ?>
                        <option value="<?php echo $o['id']; ?>"><?php echo $o['nome_completo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 text-end mt-5">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Concluir Inscrição Institucional</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
