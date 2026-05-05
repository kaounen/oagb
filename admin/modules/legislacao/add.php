<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$tipo = $_GET['tipo'] ?? 'nacional'; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($tipo === 'internacional') {
        $org = trim($_POST['organizacao'] ?? ''); $titulo = trim($_POST['titulo'] ?? '');
        $data_adocao = $_POST['data_adocao'] ?? null; $data_rat = $_POST['data_ratificacao_gb'] ?? null;
        $resumo = trim($_POST['resumo'] ?? ''); $link = trim($_POST['link_externo'] ?? '');
        $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
        if (empty($titulo)) $errors[] = 'Título obrigatório.';
        if (empty($errors)) {
            $pdo->prepare("INSERT INTO legislacao_internacional (organizacao,titulo,data_adocao,data_ratificacao_gb,resumo,link_externo,ordem,status) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$org,$titulo,$data_adocao?:null,$data_rat?:null,$resumo,$link,$ordem,$status]);
            header("Location: index.php?tipo=internacional&msg=added"); exit;
        }
    } else {
        $cat = trim($_POST['categoria'] ?? ''); $titulo = trim($_POST['titulo'] ?? '');
        $diploma = trim($_POST['diploma_legal'] ?? ''); $data_pub = $_POST['data_publicacao'] ?? null;
        $resumo = trim($_POST['resumo'] ?? ''); $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
        if (empty($titulo)) $errors[] = 'Título obrigatório.';
        if (empty($errors)) {
            $pdo->prepare("INSERT INTO legislacao_nacional (categoria,titulo,diploma_legal,data_publicacao,resumo,ordem,status) VALUES (?,?,?,?,?,?,?)")
                ->execute([$cat,$titulo,$diploma,$data_pub?:null,$resumo,$ordem,$status]);
            header("Location: index.php?tipo=nacional&msg=added"); exit;
        }
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Novo Diploma (<?php echo $tipo==='internacional'?'Internacional':'Nacional'; ?>)</h2><a href="index.php?tipo=<?php echo $tipo; ?>" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <?php if($tipo==='internacional'): ?>
        <div class="col-md-4"><label class="form-label fw-bold small">Organização</label><select name="organizacao" class="form-select"><option>OHADA</option><option>CEDEAO</option><option>União Africana</option><option>CPLP</option><option>Direitos Humanos</option></select></div>
        <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Data Adoção</label><input type="date" name="data_adocao" class="form-control"></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Data Ratificação GB</label><input type="date" name="data_ratificacao_gb" class="form-control"></div>
        <div class="col-md-4"><label class="form-label fw-bold small">Link Externo</label><input type="url" name="link_externo" class="form-control"></div>
    <?php else: ?>
        <div class="col-md-4"><label class="form-label fw-bold small">Categoria</label><input type="text" name="categoria" class="form-control" required placeholder="Ex: Direito Penal"></div>
        <div class="col-md-8"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-bold small">Diploma Legal</label><input type="text" name="diploma_legal" class="form-control" placeholder="Ex: Decreto-Lei n.º 4/93"></div>
        <div class="col-md-6"><label class="form-label fw-bold small">Data</label><input type="date" name="data_publicacao" class="form-control"></div>
    <?php endif; ?>
    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="0"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Resumo</label><textarea name="resumo" class="form-control" rows="3"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
