<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Configs
try {
    $stmt = $pdo->query("SELECT * FROM configuracoes_site ORDER BY grupo, chave");
    $configs = $stmt->fetchAll();
} catch (PDOException $e) { $configs = []; }

// Fetch Institutional Info (Missão, Visão, Valores)
try {
    $stmt_inst = $pdo->query("SELECT missao, visao, valores FROM instituicao_info LIMIT 1");
    $inst_info = $stmt_inst->fetch(PDO::FETCH_OBJ);
} catch (PDOException $e) { $inst_info = null; }

// Handle Global Config Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all'])) {
    try {
        $pdo->beginTransaction();
        foreach ($_POST['val'] as $id => $val) {
            $update = $pdo->prepare("UPDATE configuracoes_site SET valor = ? WHERE id = ?");
            $update->execute([$val, $id]);
        }
        $pdo->commit();
        echo "<script>window.location.href='index.php?t=portal&updated=1';</script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Handle MVV Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_mvv'])) {
    try {
        $missao = trim($_POST['missao'] ?? '');
        $visao = trim($_POST['visao'] ?? '');
        $valores = trim($_POST['valores'] ?? '');
        
        $update_inst = $pdo->prepare("UPDATE instituicao_info SET missao = ?, visao = ?, valores = ? WHERE id = 1");
        $update_inst->execute([$missao, $visao, $valores]);
        echo "<script>window.location.href='index.php?t=pilares&updated=1';</script>";
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php
$tab = $_GET['t'] ?? 'portal';
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <?php if ($tab === 'pilares'): ?>
            <h2 class="page-title">Pilares Institucionais</h2>
            <div class="text-muted small">Altere as informações de Missão, Visão e Valores exibidas na página institucional "A Ordem".</div>
        <?php else: ?>
            <h2 class="page-title">Configurações do Sistema</h2>
            <div class="text-muted small">Ajuste os parâmetros globais do portal.</div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> Configurações atualizadas com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($tab === 'pilares'): ?>
<!-- CARD 1: Missão, Visão e Valores (Pilares Institucionais) -->
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white border-0 py-3 ps-4">
        <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Open Sans', sans-serif;"><i class="fas fa-eye text-login me-2"></i>Pilares Institucionais (Missão, Visão e Valores)</h5>
        <span class="text-muted small">Altere as informações de Missão, Visão e Valores exibidas na página institucional "A Ordem".</span>
    </div>
    <form method="POST">
        <div class="card-body p-4 pt-2">
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.5px; color: var(--primary-maroon);"><i class="fas fa-rocket me-2"></i>Missão</label>
                    <textarea name="missao" class="form-control border-0 bg-light p-3 small" rows="3" placeholder="Insira a missão da Ordem..." required><?php echo htmlspecialchars($inst_info->missao ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.5px; color: var(--primary-maroon);"><i class="fas fa-eye me-2"></i>Visão</label>
                    <textarea name="visao" class="form-control border-0 bg-light p-3 small" rows="3" placeholder="Insira a visão da Ordem..." required><?php echo htmlspecialchars($inst_info->visao ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 0.5px; color: var(--primary-maroon);"><i class="fas fa-balance-scale me-2"></i>Valores</label>
                    <textarea name="valores" class="form-control border-0 bg-light p-3 small" rows="3" placeholder="Insira os valores da Ordem (separados por vírgula)..." required><?php echo htmlspecialchars($inst_info->valores ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white p-4 border-0 text-end">
            <button type="submit" name="save_mvv" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase"><i class="fas fa-save me-2"></i>Guardar Pilares</button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php if ($tab === 'portal'): ?>
<!-- CARD 2: Parâmetros Globais do Sistema -->
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 py-3 ps-4">
        <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Open Sans', sans-serif;"><i class="fas fa-cogs text-login me-2"></i>Parâmetros Globais do Portal</h5>
        <span class="text-muted small">Gerir variáveis e configurações técnicas globais do sistema.</span>
    </div>
    <form method="POST">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 small text-uppercase py-3">Chave / Descrição</th>
                            <th class="border-0 small text-uppercase py-3">Valor / Definição</th>
                            <th class="border-0 small text-uppercase py-3">Grupo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($configs)): ?>
                            <tr><td colspan="3" class="text-center py-5">Nenhuma configuração encontrada.</td></tr>
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
            <button type="submit" name="save_all" class="btn btn-login w-auto px-5 py-3 fw-bold text-uppercase">Guardar Parâmetros</button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
