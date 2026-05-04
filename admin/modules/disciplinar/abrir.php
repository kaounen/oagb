<?php
require_once __DIR__ . '/../../includes/db.php';

// Fetch Lawyers for dropdown
$advogados = $pdo->query("SELECT id, nome_completo, numero_registo FROM advogados ORDER BY nome_completo ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero_processo'];
    $queixoso = $_POST['queixoso_nome'];
    $adv_id = $_POST['advogado_id'];
    $rel_id = $_POST['relator_id'] ?: null;
    $status = $_POST['status'] ?? 'aberto';
    $desc = $_POST['descricao'] ?? '';
    $data_ab = $_POST['data_abertura'] ?: date('Y-m-d');

    try {
        $stmt = $pdo->prepare("INSERT INTO gestao_disciplinar_processos (numero_processo, queixoso_nome, advogado_id, relator_id, status, descricao, data_abertura) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$numero, $queixoso, $adv_id, $rel_id, $status, $desc, $data_ab]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao abrir processo: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Processo Disciplinar</h2>
        <div class="text-muted small">Registe uma nova queixa ou processo de instrução ética.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger border-0 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">Número do Processo</label>
                            <input type="text" name="numero_processo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: PD-001/2024" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">Data de Abertura</label>
                            <input type="date" name="data_abertura" class="form-control form-control-lg border-0 bg-light" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Nome do Queixoso</label>
                        <input type="text" name="queixoso_nome" class="form-control form-control-lg border-0 bg-light" placeholder="Nome do cidadão ou entidade que apresentou a queixa" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição da Queixa / Factos</label>
                        <textarea name="descricao" id="editor" class="form-control bg-light border-0" rows="8"></textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Advogado Visado</label>
                            <select name="advogado_id" class="form-select border-0 shadow-sm py-2" required>
                                <option value="">Selecione o Advogado...</option>
                                <?php foreach($advogados as $a): ?>
                                    <option value="<?php echo $a['id']; ?>"><?php echo $a['nome_completo']; ?> (<?php echo $a['numero_registo']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Relator Designado</label>
                            <select name="relator_id" class="form-select border-0 shadow-sm py-2">
                                <option value="">Aguardando Designação...</option>
                                <?php foreach($advogados as $a): ?>
                                    <option value="<?php echo $a['id']; ?>"><?php echo $a['nome_completo']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Estado Inicial</label>
                            <select name="status" class="form-select border-0 shadow-sm py-2">
                                <option value="aberto">Aberto / Triagem</option>
                                <option value="instrucao">Em Instrução</option>
                            </select>
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm fw-bold">AUTUAR PROCESSO</button>
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
