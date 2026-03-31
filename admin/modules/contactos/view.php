<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

// Fetch Message
try {
    $stmt = $pdo->prepare("SELECT * FROM mensagens_contacto WHERE id = ?");
    $stmt->execute([$id]);
    $msg = $stmt->fetch();
    if(!$msg) { header("Location: index.php"); exit; }

    // Mark as Read
    if(!$msg['lida']) {
        $pdo->prepare("UPDATE mensagens_contacto SET lida = 1 WHERE id = ?")->execute([$id]);
    }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respondida = isset($_POST['respondida']) ? 1 : 0;
    try {
        $pdo->prepare("UPDATE mensagens_contacto SET respondida = ?, data_resposta = NOW() WHERE id = ?")->execute([$respondida, $id]);
        header("Location: view.php?id=$id&updated=1");
        exit;
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Mensagem Recebida</h2>
        <div class="text-muted small">Recebida em <?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?> via formulário de contacto.</div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-user me-2 text-primary"></i> Informações do Remetente</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Nome Completo</label>
                    <div class="fw-bold"><?php echo $msg['nome']; ?></div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Email de Contacto</label>
                    <div class="fw-bold"><a href="mailto:<?php echo $msg['email']; ?>" class="text-decoration-none"><?php echo $msg['email']; ?></a></div>
                </div>
            </div>

            <div class="mb-4">
                <label class="small text-muted text-uppercase fw-bold d-block mb-2">Assunto</label>
                <div class="p-3 bg-light rounded fw-bold text-dark border-start border-primary border-4">
                    <?php echo $msg['assunto']; ?>
                </div>
            </div>

            <div class="mb-4">
                <label class="small text-muted text-uppercase fw-bold d-block mb-2">Mensagem do Utilizador</label>
                <div class="p-4 bg-light rounded text-muted shadow-sm" style="line-height: 1.8; font-size: 0.95rem;">
                    <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <a href="mailto:<?php echo $msg['email']; ?>?subject=Resposta: <?php echo urlencode($msg['assunto']); ?>" class="btn btn-login px-5 py-3 shadow-sm fw-bold"><i class="fas fa-reply me-2"></i> RESPONDER VIA EMAIL</a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-cog me-2 text-primary"></i> Estágio da Mensagem</h5>
            
            <form method="POST">
                <div class="mb-4">
                    <div class="form-check form-switch p-3 border rounded bg-light">
                        <input class="form-check-input ms-0 me-3 float-start" type="checkbox" name="respondida" <?php echo $msg['respondida'] ? 'checked':''; ?>>
                        <span class="small fw-bold <?php echo $msg['respondida'] ? 'text-success':'text-muted'; ?>">Pedência Respondida?</span>
                    </div>
                </div>
                
                <?php if($msg['data_resposta']): ?>
                    <div class="alert bg-success-subtle text-success border-0 small py-2">
                        <i class="fas fa-check-circle me-2"></i> Respondida em <?php echo date('d/m/Y H:i', strtotime($msg['data_resposta'])); ?>.
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-outline-secondary w-100 py-3 mt-3 shadow-sm fw-bold">Guardar Alterações Internas</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
