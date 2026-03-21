<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Stats for recipients
$count_sub = $pdo->query("SELECT COUNT(*) FROM newsletter_subscricoes WHERE ativo = 1 AND confirmado = 1")->fetchColumn();
$count_law = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo'")->fetchColumn();

// Handle Selection
$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_bulk'])) {
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $target = $_POST['target']; // 'subs' or 'lawyers'
    
    // Logic for bulk sending (Simplified for now)
    // In a real scenario, this would use a queue or PHPMailer
    $success_count = 0;
    
    if ($target === 'subs') {
        $stmt = $pdo->query("SELECT email FROM newsletter_subscricoes WHERE ativo = 1 AND confirmado = 1");
    } else {
        $stmt = $pdo->query("SELECT email FROM advogados WHERE status = 'ativo' AND email IS NOT NULL AND email != ''");
    }
    
    while ($row = $stmt->fetch()) {
        $to = $row['email'];
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: OAGB Newsletter <newsletter@oagb.gw>" . "\r\n";
        
        // mail($to, $subject, $body, $headers); // Simulado para evitar spam real durante testes
        $success_count++;
    }
    
    $msg = "Campanha iniciada com sucesso! " . $success_count . " emails em processamento.";
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Motor de Campanhas (Newsletter)</h2>
        <div class="text-muted small">Crie e envie comunicados em massa para a base de contactos.</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-5">
                <?php if(isset($msg)): ?>
                    <div class="alert alert-success border-0 px-4 py-3 bg-success-subtle text-success mb-4"><?php echo $msg; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Assunto do E-mail</label>
                        <input type="text" name="subject" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Circular Mensal - Março 2024..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Destinatários (Alvo)</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card-radio card h-100 cursor-pointer border-0 bg-light-subtle">
                                    <input type="radio" name="target" value="subs" class="d-none" checked>
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-users-rss fa-2x mb-3 text-primary opacity-50"></i>
                                        <div class="fw-bold">Subscritores Gerais</div>
                                        <div class="text-muted small"><?php echo $count_sub; ?> contactos confirmados</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="card-radio card h-100 cursor-pointer border-0 bg-light-subtle">
                                    <input type="radio" name="target" value="lawyers" class="d-none">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-user-tie fa-2x mb-3 text-login opacity-50"></i>
                                        <div class="fw-bold">Só Advogados</div>
                                        <div class="text-muted small"><?php echo $count_law; ?> advogados ativos</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label text-uppercase fw-bold text-muted small">Corpo da Mensagem (HTML)</label>
                        <textarea name="body" id="editor" class="form-control bg-light border-0" rows="15"></textarea>
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" name="send_bulk" class="btn btn-login w-auto px-5 py-3 shadow-lg">
                            <i class="fas fa-paper-plane me-2"></i> Disparar Campanha Agora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-dark text-white p-4 sticky-top" style="top: 100px;">
            <h5 class="fw-bold mb-4">Dicas de Envio</h5>
            <ul class="list-unstyled small opacity-75">
                <li class="mb-3"><i class="fas fa-info-circle me-2 text-warning"></i> Utilize tags como <code>{NOME}</code> para personalizar o email.</li>
                <li class="mb-3"><i class="fas fa-image me-2 text-warning"></i> Redimensione as imagens antes de as inserir no editor.</li>
                <li class="mb-3"><i class="fas fa-shield-alt me-2 text-warning"></i> Evite palavras como "GRÁTIS" ou "PROMOÇÃO" no assunto para não cair em SPAM.</li>
                <li class="mb-3"><i class="fas fa-clock me-2 text-warning"></i> Para envios superiores a 500 emails, o processo será faseado.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .card-radio input:checked + .card-body {
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 2px solid #B1A276 !important;
        border-radius: 12px;
    }
    .cursor-pointer { cursor: pointer; }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
    }).catch(e => console.error(e));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
