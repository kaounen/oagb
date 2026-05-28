<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Documents
try {
    $stmt = $pdo->query("SELECT * FROM documentos_publicos ORDER BY data_documento DESC, created_at DESC");
    $list = $stmt->fetchAll();
} catch (PDOException $e) { $list = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Centro de Documentação</h2>
        <div class="text-muted small">Repositório oficial de estatutos, regulamentos e PDFs da OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="upload.php" class="btn btn-login w-auto px-4"><i class="fas fa-file-upload me-2"></i> Carregar Documento</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">ID</th>
                    <th class="border-0 small text-uppercase py-3">Documento / Nome</th>
                    <th class="border-0 small text-uppercase py-3">Tipo / Categ.</th>
                    <th class="border-0 small text-uppercase py-3">Publicação</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Status</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="6" class="text-center py-5">Nenhum documento encontrado no repositório.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $row): ?>
                        <tr>
                            <td class="ps-4 font-monospace small text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-2 bg-danger-subtle rounded rounded-2">
                                        <i class="far fa-file-pdf text-danger fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-truncate" style="max-width: 280px;"><?php echo $row['titulo']; ?></div>
                                        <div class="text-muted x-small"><?php echo !empty($row['numero_documento']) ? 'Ref: ' . $row['numero_documento'] : 'Sem Ref.'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border py-2 px-3 small border-secondary-subtle">
                                    <?php 
                                    $label = strtoupper($row['tipo']);
                                    if ($row['tipo'] == 'comunicado' && !empty($row['subtipo'])) {
                                        $sub_labels = [
                                            'comunicado' => 'COMUNICADO',
                                            'circular' => 'CIRCULAR',
                                            'nota-pesar' => 'NOTA DE PESAR',
                                            'comunicado-imprensa' => 'C. IMPRENSA',
                                            'convocatoria-ag' => 'CONV. AG'
                                        ];
                                        $label .= ' / ' . ($sub_labels[$row['subtipo']] ?? strtoupper($row['subtipo']));
                                    }
                                    echo $label; 
                                    ?>
                                </span>
                            </td>
                            <td class="small">
                                <i class="far fa-calendar-alt me-1 opacity-50"></i> <?php echo date('d/m/Y', strtotime($row['data_documento'])); ?>
                                <div class="text-muted x-small mt-1"><i class="far fa-eye me-1 opacity-50"></i> <?php echo (int)$row['visualizacoes']; ?> vistas</div>
                            </td>
                            <td class="text-center">
                                <?php if($row['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success py-2 px-3 small">Público</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 small">Privado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="/oagb/uploads/<?php echo $row['arquivo']; ?>" target="_blank" class="btn btn-sm btn-outline-info p-2 me-1" title="Ver PDF"><i class="far fa-eye"></i></a>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar permanentemente do repositório?');"><i class="far fa-trash-alt"></i></a>
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
