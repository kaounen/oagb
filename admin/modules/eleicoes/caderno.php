<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM gestao_eleicoes WHERE id = ?");
    $stmt->execute([$id]);
    $eleicao = $stmt->fetch();
    if(!$eleicao) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// LOGIC: Eligible Voters = Status 'Ativo' AND Paid Quotas (at least one in the current month)
try {
    $stmt = $pdo->query("SELECT a.id, a.nome_completo, a.numero_registo, a.localidade,
                           (SELECT COUNT(*) FROM finan_pagamentos 
                            WHERE advogado_id = a.id AND tipo_pagamento_id = 1 
                            AND status = 'confirmado' AND (MONTH(data_pagamento) = MONTH(NOW()) OR MONTH(data_pagamento) = MONTH(NOW())-1)) as pago
                           FROM advogados a 
                           WHERE a.status = 'ativo' 
                           ORDER BY a.nome_completo ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Caderno Eleitoral</h2>
        <div class="text-muted small">Membros aptos a exercer o direito de voto para <strong><?php echo $eleicao['titulo']; ?></strong>.</div>
    </div>
    <div class="col-md-4 text-md-end">
        <button onclick="window.print()" class="btn btn-login w-auto px-4"><i class="fas fa-print me-2"></i> Imprimir Caderno Único</button>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden mb-5 p-4">
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-success-subtle border-0 p-3 text-center">
                <h4 class="fw-bold mb-0 text-success"><?php echo count(array_filter($list, function($v){ return $v['pago'] > 0; })); ?></h4>
                <div class="x-small text-success text-uppercase opacity-75">Eleitores Aptos (Em dia)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger-subtle border-0 p-3 text-center">
                <h4 class="fw-bold mb-0 text-danger"><?php echo count(array_filter($list, function($v){ return $v['pago'] == 0; })); ?></h4>
                <div class="x-small text-danger text-uppercase opacity-75">Suspensos (Devedores)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0 p-3 text-center">
                <h4 class="fw-bold mb-0 text-dark"><?php echo count($list); ?></h4>
                <div class="x-small text-dark text-uppercase opacity-75">Total de Inscritos</div>
            </div>
        </div>
        <div class="col-md-3 text-end d-flex align-items-center justify-content-end">
            <input type="text" id="search_voter" class="form-control border-0 bg-light p-3" placeholder="Filtrar por nome...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover" id="voters_table">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Eleitor / Cédula No.</th>
                    <th class="border-0 small text-uppercase py-3">Jurisdição</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Direito de Voto</th>
                    <th class="border-0 small text-uppercase py-3 text-end pe-4">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($list as $v): ?>
                    <tr class="voter-row">
                        <td class="ps-4 voter-name">
                            <div class="fw-bold small"><?php echo $v['nome_completo']; ?></div>
                            <div class="text-muted x-small"><?php echo $v['numero_registo']; ?></div>
                        </td>
                        <td class="small opacity-75"><?php echo $v['localidade']; ?></td>
                        <td class="text-center">
                            <?php if($v['pago'] > 0): ?>
                                <span class="badge bg-success-subtle text-success py-1 px-3 small border border-success-subtle"><i class="fas fa-check-circle me-1"></i> APTO PARA VOTAR</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger py-1 px-3 small border border-danger-subtle"><i class="fas fa-times-circle me-1"></i> IMPEDIDO (EM DIVIDA)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <?php if($v['pago'] == 0): ?>
                                <a href="../financeiro/novo_recebimento.php?adv_id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-danger p-2 px-3 small fw-bold">REGULARIZAR NO ATO</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light p-2 px-3 small fw-bold" disabled>REGULARIZADO</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('search_voter').onkeyup = function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.voter-row');
        rows.forEach(row => {
            const name = row.querySelector('.voter-name').innerText.toLowerCase();
            row.style.display = name.includes(query) ? '' : 'none';
        });
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
