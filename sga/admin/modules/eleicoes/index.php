<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Elections
try {
    $stmt = $pdo->query("SELECT * FROM gestao_eleicoes ORDER BY data_eleicao DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Atos Eleitorais</h2>
        <div class="text-muted small">Configuração de eleições, cadernos eleitorais e escrutínio institucional.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4 shadow-sm"><i class="fas fa-vote-yea me-2"></i> Nova Eleição</a>
    </div>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Pleito Eleitoral</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Data do Ato</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Estado</th>
                    <th class="border-0 small text-uppercase py-3 text-end pe-4">Ações Estratégicas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="4" class="text-center py-5 opacity-50">Nenhum ato eleitoral registado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $e): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold fs-6 text-dark"><?php echo $e['titulo']; ?></div>
                                <div class="text-muted small opacity-75"><?php echo $e['descricao']; ?></div>
                            </td>
                            <td class="text-center small fw-bold"><?php echo date('d/m/Y', strtotime($e['data_eleicao'])); ?></td>
                            <td class="text-center">
                                <?php if($e['ativa']): ?>
                                    <span class="badge bg-success py-2 px-3 small border-dashed">ATIVA / EM CURSO</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary py-2 px-3 small">FECHADA</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="caderno.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-login-subtle text-login border-login-subtle border p-2 px-3 small fw-bold me-1">
                                   <i class="fas fa-users-check me-1"></i> VER CADERNO
                                </a>
                                <a href="edit.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar ato?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100 bg-light">
            <h6 class="fw-bold text-uppercase small text-muted mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> Critério de Elegibilidade</h6>
            <p class="small text-dark opacity-75">O caderno eleitoral é gerado automaticamente considerando o <b>Regulamento Eleitoral</b> vigente: Apenas advogados ativos e com quotas regularizadas até ao mês anterior ao escrutínio são considerados eleitores.</p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold text-uppercase small text-muted mb-3"><i class="fas fa-lock me-1 text-danger"></i> Segurança do Voto</h6>
            <p class="small text-dark opacity-75">O sistema garante a unicidade do voto através de hashes de segurança e bloqueio de múltipla participação, assegurando a integridade e sigilo total.</p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold text-uppercase small text-muted mb-3"><i class="fas fa-file-export me-1 text-success"></i> Transparência</h6>
            <p class="small text-dark opacity-75">Após o fecho das urnas, a ata de escrutínio é gerada instantaneamente, permitindo a publicação imediata dos resultados oficiais no portal.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
