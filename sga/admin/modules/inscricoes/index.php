<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Pending Inscricoes
try {
    $stmt = $pdo->query("SELECT * FROM inscricoes_ordem WHERE status = 'pendente' ORDER BY created_at DESC");
    $pendentes = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT * FROM inscricoes_ordem WHERE status != 'pendente' ORDER BY updated_at DESC LIMIT 50");
    $historico = $stmt->fetchAll();
} catch (PDOException $e) { $pendentes = []; $historico = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Novas Inscrições</h2>
        <div class="text-muted small">Processamento de candidaturas para Advogados e Estagiários.</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5 p-0 overflow-hidden">
    <div class="card-header bg-warning-subtle text-warning-emphasis border-0 p-4">
        <h5 class="fw-bold mb-0 text-uppercase small"><i class="fas fa-user-plus me-2"></i> Aguardando Analise (Novos Pedidos)</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Candidato</th>
                    <th class="border-0 small text-uppercase py-3">Tipo</th>
                    <th class="border-0 small text-uppercase py-3">Contacto</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Data Pedido</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($pendentes)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhuma inscrição pendente de análise.</td></tr>
                <?php else: ?>
                    <?php foreach($pendentes as $i): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small"><?php echo $i['nome_completo']; ?></div>
                                <div class="text-muted x-small"><?php echo $i['localidade']; ?></div>
                            </td>
                            <td><span class="badge <?php echo $i['tipo_inscricao'] == 'advogado' ? 'bg-primary' : 'bg-info'; ?> py-1 px-3 small text-uppercase fw-bold"><?php echo $i['tipo_inscricao']; ?></span></td>
                            <td class="small opacity-75">
                                <div><i class="fas fa-phone-alt me-1 x-small opacity-50"></i> <?php echo $i['telefone']; ?></div>
                                <div><i class="fas fa-envelope me-1 x-small opacity-50"></i> <?php echo $i['email']; ?></div>
                            </td>
                            <td class="text-center small opacity-75"><?php echo date('d/m/Y', strtotime($i['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="view.php?id=<?php echo $i['id']; ?>" class="btn btn-login w-auto px-4 btn-sm shadow-sm py-2">Analisar Pedido</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden mb-5">
    <div class="card-header bg-white border-0 p-4">
        <h5 class="fw-bold mb-0 text-uppercase small"><i class="fas fa-history me-2"></i> Histórico Decidido</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Nome</th>
                    <th class="border-0 small text-uppercase py-3">Decisão</th>
                    <th class="border-0 small text-uppercase py-3">Data</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($historico as $h): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold small"><?php echo $h['nome_completo']; ?></div>
                            <div class="text-muted x-small"><?php echo $h['tipo_inscricao']; ?></div>
                        </td>
                        <td>
                            <?php if($h['status'] == 'aprovado'): ?>
                                <span class="badge bg-success-subtle text-success py-1 px-3 small">APROVADO</span>
                            <?php elseif($h['status'] == 'rejeitado'): ?>
                                <span class="badge bg-danger-subtle text-danger py-1 px-3 small">REJEITADO</span>
                            <?php else: ?>
                                <span class="badge bg-secondary py-1 px-3 small"><?php echo strtoupper($h['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="small opacity-75"><?php echo date('d/m/Y', strtotime($h['updated_at'])); ?></td>
                        <td class="text-center">
                            <a href="view.php?id=<?php echo $h['id']; ?>" class="btn btn-sm btn-outline-secondary p-2"><i class="fas fa-eye"></i> Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
