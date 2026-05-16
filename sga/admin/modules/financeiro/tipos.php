<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Types
$types = $pdo->query("SELECT * FROM finan_tipos_pagamento ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_taxa'])) {
    $id = $_POST['taxa_id'] ?? 0;
    $nome = $_POST['nome'];
    $valor = $_POST['valor_padrao'];
    $periodo = $_POST['periodicidade'];

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE finan_tipos_pagamento SET nome = ?, valor_padrao = ?, periodicidade = ? WHERE id = ?");
        $stmt->execute([$nome, $valor, $periodo, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO finan_tipos_pagamento (nome, valor_padrao, periodicidade) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $valor, $periodo]);
    }
    header("Location: tipos.php?success=1");
    exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Configuração de Taxas & Quotas</h2>
        <div class="text-muted small">Defina os valores padrão para cada tipo de contribuição.</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
            <h5 class="fw-bold mb-4">Nova Taxa / Quota</h5>
            <form method="POST">
                <input type="hidden" name="taxa_id" id="taxa_id" value="0">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nome da Taxa</label>
                    <input type="text" name="nome" id="taxa_nome" class="form-control border-0 bg-light" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Valor em CFA</label>
                    <input type="number" step="0.01" name="valor_padrao" id="taxa_valor" class="form-control border-0 bg-light" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Periodicidade</label>
                    <select name="periodicidade" id="taxa_periodo" class="form-select border-0 bg-light">
                        <option value="unico">Pagamento Único</option>
                        <option value="mensal">Mensalidade</option>
                        <option value="anual">Anualidade</option>
                    </select>
                </div>
                <button type="submit" name="save_taxa" class="btn btn-login w-100 py-3 shadow-sm">Gravar Configuração</button>
            </form>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-0 overflow-hidden">
            <table class="table align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4 border-0 small py-3">Nome do Item</th>
                        <th class="border-0 small py-3">Valor Padrão</th>
                        <th class="border-0 small py-3">Ciclo</th>
                        <th class="border-0 small py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($types as $t): ?>
                        <tr>
                            <td class="ps-4 fw-bold small"><?php echo $t['nome']; ?></td>
                            <td class="small"><?php echo number_format($t['valor_padrao'], 2, ',', '.'); ?> CFA</td>
                            <td><span class="badge bg-light text-dark border px-3 py-2 small"><?php echo ucfirst($t['periodicidade']); ?></span></td>
                            <td class="text-center">
                                <button onclick="editTaxa(<?php echo $t['id']; ?>, '<?php echo addslashes($t['nome']); ?>', <?php echo $t['valor_padrao']; ?>, '<?php echo $t['periodicidade']; ?>')" class="btn btn-sm btn-outline-primary p-2 me-1"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editTaxa(id, nome, valor, periodo) {
        document.getElementById('taxa_id').value = id;
        document.getElementById('taxa_nome').value = nome;
        document.getElementById('taxa_valor').value = valor;
        document.getElementById('taxa_periodo').value = periodo;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
