<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Pareceres
try {
    $stmt = $pdo->query("SELECT * FROM pareceres_deliberacoes ORDER BY data_emissao DESC, id DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Pareceres & Deliberações</h2>
        <div class="text-muted small">Gestão de opiniões legais e decisões oficiais dos órgãos da Ordem.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-file-medical me-2"></i> Novo Parecer</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3" style="width: 100px;">Nº Ref.</th>
                    <th class="border-0 small text-uppercase py-3">Assunto / Título</th>
                    <th class="border-0 small text-uppercase py-3">Relator / Autor</th>
                    <th class="border-0 small text-uppercase py-3">Emissão</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="5" class="text-center py-5">Nenhum parecer ou deliberação registado.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 font-monospace small"><span class="badge bg-light text-dark border border-secondary-subtle py-2 px-3"><?php echo $row['numero'] ?: 'S/N'; ?></span></td>
                            <td>
                                <div class="fw-bold small"><?php echo $row['assunto']; ?></div>
                                <div class="text-muted x-small"><?php echo !empty($row['tipo']) ? strtoupper($row['tipo']) : 'PARECER'; ?></div>
                            </td>
                            <td class="small"><i class="far fa-user-circle me-1 opacity-50"></i> <?php echo $row['relator'] ?: 'Conselho Geral'; ?></td>
                            <td class="small"><?php echo date('d/m/Y', strtotime($row['data_emissao'])); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="/oagb/uploads/<?php echo $row['arquivo_pdf']; ?>" target="_blank" class="btn btn-sm btn-outline-info p-2 me-1" title="Ver PDF"><i class="far fa-eye"></i></a>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar permanentemente do acervo?');"><i class="far fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
