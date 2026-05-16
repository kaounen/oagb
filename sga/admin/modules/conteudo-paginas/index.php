<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$pagina_filter = $_GET['pagina'] ?? '';
$where = $pagina_filter ? "WHERE pagina = ?" : "";
$params = $pagina_filter ? [$pagina_filter] : [];
$stmt = $pdo->prepare("SELECT * FROM conteudos_paginas $where ORDER BY pagina ASC, ordem ASC");
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<div class="row mb-5 align-items-center">
    <div class="col-md-6"><h2 class="page-title">Gestão de Conteúdos por Página</h2><div class="text-muted small">Gerir secções dinâmicas e multimédia de páginas institucionais.</div></div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0"><a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-plus me-2"></i>Nova Secção</a></div>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light"><tr>
            <th class="ps-4 border-0 small text-uppercase">Página</th>
            <th class="border-0 small text-uppercase">Título</th>
            <th class="border-0 small text-uppercase">Secção</th>
            <th class="border-0 small text-uppercase">Multimédia</th>
            <th class="border-0 small text-uppercase">Estado</th>
            <th class="border-0 small text-uppercase text-center">Ações</th>
        </tr></thead>
        <tbody>
            <?php if(empty($items)): ?><tr><td colspan="6" class="text-center py-5 text-muted">Sem conteúdos registados.</td></tr>
            <?php else: foreach($items as $i): ?>
                <tr>
                    <td class="ps-4"><span class="badge bg-login-subtle text-login"><?php echo htmlspecialchars($i->pagina); ?></span></td>
                    <td><div class="fw-bold small"><?php echo htmlspecialchars($i->titulo); ?></div></td>
                    <td><span class="small text-muted"><?php echo htmlspecialchars($i->secao); ?></span></td>
                    <td>
                        <?php if($i->imagem): ?><i class="fas fa-image text-success me-2" title="Imagem"></i><?php endif; ?>
                        <?php if($i->arquivo): ?><i class="fas fa-file-pdf text-danger" title="Documento"></i><?php endif; ?>
                    </td>
                    <td><span class="badge <?php echo $i->status==='ativo'?'bg-success':'bg-secondary'; ?>"><?php echo ucfirst($i->status); ?></span></td>
                    <td class="text-center"><div class="btn-group">
                        <a href="edit.php?id=<?php echo $i->id; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="far fa-edit"></i></a>
                        <a href="delete.php?id=<?php echo $i->id; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar?');"><i class="far fa-trash-alt"></i></a>
                    </div></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
