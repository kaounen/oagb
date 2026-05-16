<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Lawyers to check status
try {
    $stmt = $pdo->query("SELECT a.id, a.nome_completo, a.numero_registo, a.status,
                           (SELECT COUNT(*) FROM finan_pagamentos 
                            WHERE advogado_id = a.id AND tipo_pagamento_id = 1 
                            AND status = 'confirmado' AND MONTH(data_pagamento) = MONTH(NOW()) AND YEAR(data_pagamento) = YEAR(NOW())) as pago_hoje
                           FROM advogados a 
                           WHERE a.status = 'ativo' 
                           ORDER BY a.nome_completo ASC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Emissão de Certidões</h2>
        <div class="text-muted small">Geração automática de "Certidões de Nada a Declarar" para membros com quotas regulares.</div>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 mb-5 bg-white">
    <div class="row g-3 align-items-center mb-4">
        <div class="col-md-8">
            <h5 class="fw-bold mb-0">Pesquisar Membro</h5>
            <div class="text-muted small">Digite o nome ou o número de cédula para localizar o advogado.</div>
        </div>
        <div class="col-md-4">
            <input type="text" id="member_search" class="form-control border-0 bg-light p-3" placeholder="Filtrar por nome...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover" id="member_table">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Nome / Cédula</th>
                    <th class="border-0 small text-uppercase py-3">Estado Financeiro</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ação de Emissão</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($list as $a): ?>
                    <tr class="member-row">
                        <td class="ps-4 name-field">
                            <div class="fw-bold small"><?php echo $a['nome_completo']; ?></div>
                            <div class="text-muted x-small"><?php echo $a['numero_registo']; ?></div>
                        </td>
                        <td>
                            <?php if($a['pago_hoje'] > 0): ?>
                                <span class="badge bg-success-subtle text-success py-1 px-3 small"><i class="fas fa-check-circle me-1"></i> REGULARIZADO</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger py-1 px-3 small"><i class="fas fa-exclamation-triangle me-1"></i> COM DIVIDA</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($a['pago_hoje'] > 0): ?>
                                <a href="view_certidao.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-login w-auto px-4 fw-bold shadow-sm" target="_blank">
                                    <i class="fas fa-file-contract me-2"></i> EMITIR CERTIDÃO
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light w-auto px-4 fw-bold opacity-50" disabled>
                                    <i class="fas fa-lock me-2"></i> ACESSO BLOQUEADO
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('member_search').onkeyup = function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.member-row');
        rows.forEach(row => {
            const name = row.querySelector('.name-field').innerText.toLowerCase();
            row.style.display = name.includes(query) ? '' : 'none';
        });
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
