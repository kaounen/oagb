<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$mes_atual = date('m');
$ano_atual = date('Y');
$meses_pt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Query for Advogados with NO payment confirmed for this month
try {
    $stmt = $pdo->prepare("SELECT a.id, a.nome_completo, a.numero_registo, a.email, a.telefone,
                           (SELECT MAX(data_pagamento) FROM finan_pagamentos 
                            WHERE advogado_id = a.id AND tipo_pagamento_id = 1 AND status = 'confirmado') as ultimo_pagamento
                           FROM advogados a 
                           WHERE a.status = 'ativo' 
                           AND a.id NOT IN (
                               SELECT advogado_id FROM finan_pagamentos 
                               WHERE tipo_pagamento_id = 1 
                               AND status = 'confirmado' 
                               AND MONTH(data_pagamento) = ? 
                               AND YEAR(data_pagamento) = ?
                           )
                           ORDER BY a.nome_completo ASC");
    $stmt->execute([$mes_atual, $ano_atual]);
    $devedores = $stmt->fetchAll();
} catch (PDOException $e) { $devedores = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Gestão de Quotas em Atraso</h2>
        <div class="text-muted small">Advogados com pagamentos pendentes para <strong><?php echo $meses_pt[(int)$mes_atual]; ?> / <?php echo $ano_atual; ?></strong>.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-danger w-auto px-4 shadow-sm py-3 fw-bold text-uppercase" onclick="sendBulkAlert()">
            <i class="fas fa-paper-plane me-2"></i> Disparar Alerta em Massa (E-mail)
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden mb-5">
    <div class="card-header bg-danger-subtle text-danger-emphasis border-0 p-4 d-flex justify-content-between">
        <h5 class="fw-bold mb-0">Lista de Devedores (<?php echo count($devedores); ?> membros)</h5>
        <div class="small fw-bold text-uppercase opacity-75">Mês de Referência: <?php echo $meses_pt[(int)$mes_atual]; ?></div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Advogado / Cédula</th>
                    <th class="border-0 small text-uppercase py-3">Última Quota Paga</th>
                    <th class="border-0 small text-uppercase py-3">Auditório (Atraso)</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Contactos</th>
                    <th class="border-0 small text-uppercase py-3 text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($devedores)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum devedor encontrado. Excelente gestão de quotas!</td></tr>
                <?php else: ?>
                    <?php foreach($devedores as $d): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $d['nome_completo']; ?></div>
                                <div class="text-muted x-small">Nº Cédula: <?php echo $d['numero_registo']; ?></div>
                            </td>
                            <td>
                                <?php if($d['ultimo_pagamento']): ?>
                                    <span class="badge bg-login-subtle text-login py-1 px-3 small border border-login-subtle">
                                        <?php echo date('d/m/Y', strtotime($d['ultimo_pagamento'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-muted py-1 px-3 small italic">Sem histórico</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    if(!$d['ultimo_pagamento']) {
                                        echo '<span class="text-danger fw-bold small">Indeterminado</span>';
                                    } else {
                                        $last = new DateTime($d['ultimo_pagamento']);
                                        $now = new DateTime();
                                        $diff = $last->diff($now);
                                        $months = ($diff->y * 12) + $diff->m;
                                        if($months > 0) {
                                            echo '<span class="text-danger fw-bold small">'.$months.' Mês(es) de Atraso</span>';
                                        } else {
                                            echo '<span class="fw-bold small opacity-50">Mês corrente apenas</span>';
                                        }
                                    }
                                ?>
                            </td>
                            <td class="text-center small">
                                <a href="mailto:<?php echo $d['email']; ?>" class="text-primary me-2"><i class="fas fa-envelope"></i></a>
                                <a href="tel:<?php echo $d['telefone']; ?>" class="text-success"><i class="fas fa-phone"></i></a>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-danger p-2 px-3 small text-uppercase fw-bold" onclick="sendIndividualAlert('<?php echo $d['id']; ?>', '<?php echo addslashes($d['nome_completo']); ?>')">
                                    <i class="fas fa-bell me-1"></i> Notificar
                                </button>
                                <a href="novo_recebimento.php?adv_id=<?php echo $d['id']; ?>" class="btn btn-sm btn-login p-2 px-3 small text-uppercase fw-bold ms-1">
                                    Registar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function sendBulkAlert() {
        if(confirm('Deseja enviar um e-mail de alerta para todos os <?php echo count($devedores); ?> advogados da lista? Esta acção pode levar alguns minutos.')) {
            alert('Funcionalidade de envio em massa (API Mail/Newsletter) será integrada agora.');
        }
    }

    function sendIndividualAlert(id, nome) {
        alert('Enviando alerta individual para ' + nome + '...');
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
