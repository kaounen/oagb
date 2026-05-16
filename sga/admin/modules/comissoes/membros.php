<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

// Fetch Commission
$stmt = $pdo->prepare("SELECT * FROM gestao_comissoes WHERE id = ?");
$stmt->execute([$id]);
$comissao = $stmt->fetch();
if(!$comissao) { header("Location: index.php"); exit; }

// Fetch Members
$stmt = $pdo->prepare("SELECT m.id, m.cargo, m.data_entrada, a.nome_completo, a.numero_registo, a.email, a.id as lawyer_id 
                       FROM gestao_comissoes_membros m 
                       JOIN advogados a ON m.advogado_id = a.id 
                       WHERE m.comissao_id = ? 
                       ORDER BY FIELD(m.cargo, 'Presidente', 'Vice-Presidente', 'Secretário', 'Vogal') ASC");
$stmt->execute([$id]);
$membros = $stmt->fetchAll();

// Fetch ALL active lawyers for adding
$stmt = $pdo->query("SELECT id, nome_completo, numero_registo FROM advogados WHERE status = 'ativo' ORDER BY nome_completo ASC");
$lawyers = $stmt->fetchAll();

// Handle Add Member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $aid = $_POST['advogado_id'];
    $cargo = $_POST['cargo'];
    $data = $_POST['data_entrada'] ?: date('Y-m-d');

    try {
        $stmt = $pdo->prepare("INSERT INTO gestao_comissoes_membros (comissao_id, advogado_id, cargo, data_entrada) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $aid, $cargo, $data]);
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'COMMISSION_ADD', "Adicionou membro à comissão " . $comissao['nome'], 'gestao_comissoes_membros', $pdo->lastInsertId());

        header("Location: membros.php?id=$id&success=1"); exit;
    } catch (PDOException $e) { $error = "Membro já existe nesta comissão."; }
}

// Handle Remove Member
if (isset($_GET['remove'])) {
    $rid = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM gestao_comissoes_membros WHERE id = ?");
    $stmt->execute([$rid]);
    header("Location: membros.php?id=$id&success=removed"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> <?php echo $comissao['nome']; ?></h2>
        <div class="text-muted small">Estrutura organizacional e gastão de peritos/membros.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-login w-auto px-4 shadow-sm py-3 fw-bold text-uppercase" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="fas fa-user-plus me-2"></i> Adicionar Perito / Membro
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden mb-5 bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Nome / Cédula</th>
                    <th class="border-0 small text-uppercase py-3">Cargo / Função</th>
                    <th class="border-0 small text-uppercase py-3">Entrada na Comissão</th>
                    <th class="border-0 small text-uppercase py-3 text-end pe-4">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($membros)): ?>
                    <tr><td colspan="4" class="text-center py-5 opacity-50">Nenhum membro designado para esta estrutura.</td></tr>
                <?php else: ?>
                    <?php foreach($membros as $m): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $m['nome_completo']; ?></div>
                                <div class="text-muted x-small"><?php echo $m['numero_registo']; ?></div>
                            </td>
                            <td>
                                <span class="badge py-1 px-3 small border <?php echo ($m['cargo'] == 'Presidente' ? 'bg-primary text-white border-primary' : 'bg-light text-dark'); ?>">
                                    <?php echo strtoupper($m['cargo']); ?>
                                </span>
                            </td>
                            <td class="small opacity-75"><?php echo date('d/m/Y', strtotime($m['data_entrada'])); ?></td>
                            <td class="text-end pe-4">
                                <a href="membros.php?id=<?php echo $id; ?>&remove=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger p-2 px-3 fw-bold border-0 shadow-none op-50 hover-op-100" onclick="return confirm('Remover membro da comissão?')">
                                    <i class="fas fa-user-minus me-1"></i> REMOVER
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Novo Membro na Estrutura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4 pt-0">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Selecionar Advogado (Ativo)</label>
                        <select name="advogado_id" class="form-select border-0 bg-light p-3" required>
                            <option value="">Escolha um membro...</option>
                            <?php foreach($lawyers as $la): ?>
                                <option value="<?php echo $la['id']; ?>"><?php echo $la['nome_completo']; ?> (<?php echo $la['numero_registo']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Cargo Designado</label>
                        <select name="cargo" class="form-select border-0 bg-light p-3" required>
                            <option value="Presidente">Presidente</option>
                            <option value="Vice-Presidente">Vice-Presidente</option>
                            <option value="Secretário">Secretário</option>
                            <option value="Relator">Relator</option>
                            <option value="Vogal" selected>Vogal / Perito</option>
                            <option value="Consultor">Consultor Externo</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Data de Designação</label>
                        <input type="date" name="data_entrada" class="form-control border-0 bg-light p-3" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="add_member" class="btn btn-login w-100 py-3 shadow-sm text-uppercase fw-bold">Adicionar à Comissão</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .op-50 { opacity: 0.5; transition: 0.3s; }
    .hover-op-100:hover { opacity: 1 !important; color: #dc3545 !important; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
