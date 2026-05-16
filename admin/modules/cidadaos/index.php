<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
try { $items = $pdo->query("SELECT * FROM info_cidadaos ORDER BY ordem ASC")->fetchAll(); } catch (PDOException $e) { $items = []; }
?>
<div class="row mb-5 align-items-center">
    <div class="col-md-6"><h2 class="page-title">Informação ao Cidadão</h2><div class="text-muted small">Gerir secções da página de informação ao cidadão.</div></div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0"><a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i>Nova Secção</a></div>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light"><tr>
            <th class="ps-4 border-0 small text-uppercase" style="width:60px;">Ord.</th>
            <th class="border-0 small text-uppercase">Título</th>
            <th class="border-0 small text-uppercase">Ícone</th>
            <th class="border-0 small text-uppercase">Estado</th>
            <th class="border-0 small text-uppercase text-center">Ações</th>
        </tr></thead>
        <tbody>
            <?php if(empty($items)): ?><tr><td colspan="5" class="text-center py-5 text-muted">Sem secções.</td></tr>
            <?php else: foreach($items as $i): ?>
                <tr>
                    <td class="ps-4"><span class="badge bg-light text-muted border"><?php echo $i['ordem']; ?></span></td>
                    <td><div class="fw-bold small"><?php echo htmlspecialchars($i['titulo']); ?></div></td>
                    <td><i class="<?php echo htmlspecialchars($i['icone']); ?>"></i> <span class="small text-muted"><?php echo htmlspecialchars($i['icone']); ?></span></td>
                    <td><span class="badge <?php echo ($i['status'] ?? 'ativo') === 'ativo' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo ucfirst($i['status'] ?? 'ativo'); ?></span></td>
                    <td class="text-center"><div class="btn-group">
                        <a href="edit.php?id=<?php echo $i['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                        <a href="delete.php?id=<?php echo $i['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar?');"><i class="far fa-trash-alt"></i></a>
                    </div></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
