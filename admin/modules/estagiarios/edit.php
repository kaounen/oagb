<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'];
    $reg = $_POST['numero_registo'];
    $email = $_POST['email'];
    $orientador = $_POST['orientador_id'];
    $sociedade = $_POST['sociedade_id'];
    $fase = $_POST['fase_estagio'];
    $status = $_POST['status'];

    // Se o orientador mudou, resetar para pendente de aceitação
    $current = $pdo->prepare("SELECT orientador_id FROM advogados_estagiarios WHERE id = ?");
    $current->execute([$id]);
    $old_orientador = $current->fetchColumn();
    
    if($orientador != $old_orientador && !empty($orientador)) {
        $status = 'pendente_aceitacao';
    }
    
    $stmt = $pdo->prepare("UPDATE advogados_estagiarios SET nome_completo = ?, numero_registo = ?, email = ?, orientador_id = ?, sociedade_id = ?, fase_estagio = ?, status = ? WHERE id = ?");
    $stmt->execute([$nome, $reg, $email, $orientador, $sociedade, $fase, $status, $id]);
    
    // Notificar
    require_once __DIR__ . '/../../includes/NotifyHelper.php';
    if(!empty($orientador)) {
        NotifyHelper::sendSMS($pdo, $orientador, "OAGB: Novo pedido de vinculação do estagiário $nome. Por favor, aceda ao portal para aceitar.");
    }

    header("Location: index.php?success=updated"); exit;
}

$stmt = $pdo->prepare("SELECT * FROM advogados_estagiarios WHERE id = ?");
$stmt->execute([$id]);
$e = $stmt->fetch();

$orientadores = $pdo->query("SELECT id, nome_completo FROM advogados WHERE status = 'ativo' ORDER BY nome_completo")->fetchAll();
$sociedades = $pdo->query("SELECT id, nome FROM gestao_sociedades ORDER BY nome")->fetchAll();
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Perfil de Estagiário</h2>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white">
    <form method="POST">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nome Completo</label>
                <input type="text" name="nome_completo" class="form-control border-0 bg-light p-3" value="<?php echo $e['nome_completo']; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Cédula No.</label>
                <input type="text" name="numero_registo" class="form-control border-0 bg-light p-3" value="<?php echo $e['numero_registo']; ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Estado do Estágio</label>
                <select name="status" class="form-select border-0 bg-light p-3">
                    <option value="pendente_aceitacao" <?php echo $e['status'] == 'pendente_aceitacao' ? 'selected' : ''; ?>>PENDENTE ACEITAÇÃO</option>
                    <option value="ativo" <?php echo $e['status'] == 'ativo' ? 'selected' : ''; ?>>ATIVO</option>
                    <option value="concluido" <?php echo $e['status'] == 'concluido' ? 'selected' : ''; ?>>CONCLUÍDO</option>
                    <option value="cancelado" <?php echo $e['status'] == 'cancelado' ? 'selected' : ''; ?>>CANCELADO</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Email</label>
                <input type="email" name="email" class="form-control border-0 bg-light p-3" value="<?php echo $e['email']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Fase de Estágio</label>
                <select name="fase_estagio" class="form-select border-0 bg-light p-3">
                    <option value="instrucao" <?php echo $e['fase_estagio'] == 'instrucao' ? 'selected' : ''; ?>>INSTRUÇÃO</option>
                    <option value="pratica" <?php echo $e['fase_estagio'] == 'pratica' ? 'selected' : ''; ?>>PRÁTICA</option>
                    <option value="concluido" <?php echo $e['fase_estagio'] == 'concluido' ? 'selected' : ''; ?>>CONCLUÍDO</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Patrono Orientador</label>
                <select name="orientador_id" class="form-select border-0 bg-light p-3">
                    <option value="">-- Sem Patrono --</option>
                    <?php foreach($orientadores as $o): ?>
                        <option value="<?php echo $o['id']; ?>" <?php echo $e['orientador_id'] == $o['id'] ? 'selected' : ''; ?>><?php echo $o['nome_completo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Sociedade de Advogados / Firma</label>
                <select name="sociedade_id" class="form-select border-0 bg-light p-3">
                    <option value="">-- Sem Sociedade --</option>
                    <?php foreach($sociedades as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo $e['sociedade_id'] == $s['id'] ? 'selected' : ''; ?>><?php echo $s['nome']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 text-end mt-5">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Atualizar Registo e Notificar</button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
