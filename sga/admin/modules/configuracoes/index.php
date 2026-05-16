<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Configs
try {
    $stmt = $pdo->query("SELECT * FROM configuracoes_site ORDER BY grupo, chave");
    $configs = $stmt->fetchAll();
} catch (PDOException $e) { $configs = []; }

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    try {
        $pdo->beginTransaction();
        foreach ($_POST['val'] as $id => $val) {
            $update = $pdo->prepare("UPDATE configuracoes_site SET valor = ? WHERE id = ?");
            $update->execute([$val, $id]);
        }
        $pdo->commit();
        echo "<script>window.location.href='index.php?updated=1';</script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Configurações do Sistema</h2>
        <div class="text-muted small">Ajuste os parâmetros globais do portal e informações institucionais.</div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <form method="POST">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 small text-uppercase py-3">Chave / Descrição</th>
                            <th class="border-0 small text-uppercase py-3">Valor / Definição</th>
                            <th class="border-0 small text-uppercase py-3">Grupo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($configs)): ?>
                            <tr><td colspan="3" class="text-center py-5">Nenhuma configuração encontrada.</td></tr>
                        <?php else: ?>
                            <?php foreach($configs as $conf): ?>
                                <tr>
                                    <td class="ps-4" style="width: 35%;">
                                        <div class="fw-bold small"><?php echo $conf['chave']; ?></div>
                                        <div class="text-muted small opacity-75"><?php echo $conf['descricao']; ?></div>
                                    </td>
                                    <td>
                                        <input type="text" name="val[<?php echo $conf['id']; ?>]" class="form-control border-0 bg-light p-2 small" value="<?php echo htmlspecialchars($conf['valor']); ?>">
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary small"><?php echo strtoupper($conf['grupo']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-4 border-0 text-end">
            <button type="submit" name="save_all" class="btn btn-login w-auto px-5">Guardar Alterações</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
