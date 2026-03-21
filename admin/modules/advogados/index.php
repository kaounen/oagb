<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Lawyers
try {
    $stmt = $pdo->query("SELECT * FROM advogados ORDER BY nome_completo ASC");
    $advogados = $stmt->fetchAll();
} catch (PDOException $e) {
    $advogados = [];
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Diretório de Profissionais</h2>
        <div class="text-muted small">Pesquisa e gestão da base de dados oficial de advogados da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-user-plus me-2"></i> Adicionar Profissional</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small text-uppercase">Cédula Prof.</th>
                        <th class="border-0 small text-uppercase">Nome Completo</th>
                        <th class="border-0 small text-uppercase">Região / Localidade</th>
                        <th class="border-0 small text-uppercase">Telefone / E-mail</th>
                        <th class="border-0 small text-uppercase text-center">Status</th>
                        <th class="border-0 small text-uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($advogados)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Não foram encontrados registos de advogados.</td></tr>
                    <?php else: ?>
                        <?php foreach($advogados as $row): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted border">#<?php echo $row['numero_registo']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($row['foto'])): ?>
                                            <img src="/oagb/gestao/assets/uploads/files/<?php echo $row['foto']; ?>" class="rounded-circle me-3 border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle me-3 border bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="far fa-user text-muted opacity-50"></i></div>
                                        <?php endif; ?>
                                        <div class="fw-bold small"><?php echo $row['nome_completo']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold"><?php echo $row['regiao']; ?></div>
                                    <?php if(!empty($row['localidade'])): ?><div class="text-muted x-small"><?php echo $row['localidade']; ?></div><?php endif; ?>
                                </td>
                                <td>
                                    <div class="small"><i class="fas fa-phone-alt me-2 text-muted x-small"></i> <?php echo $row['telefone']; ?></div>
                                    <?php if(!empty($row['email'])): ?><div class="text-muted x-small"><i class="far fa-envelope me-2 text-muted"></i> <?php echo $row['email']; ?></div><?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success py-2 px-3">Ativo</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1" title="Editar"><i class="far fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Apagar este registo profissional permanentemente?');" title="Eliminar"><i class="far fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
