<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $slug = trim($_POST['slug'] ?? '');
    $icone = trim($_POST['icone'] ?? 'fas fa-info-circle'); $conteudo = $_POST['conteudo'] ?? '';
    $ordem = intval($_POST['ordem'] ?? 0); $status = $_POST['status'] ?? 'ativo';
    if (empty($slug)) $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT',$titulo)));
    if (empty($titulo)) $errors[] = 'Título obrigatório.';
    if (empty($errors)) {
        $pdo->prepare("INSERT INTO info_cidadaos (titulo,slug,icone,conteudo,ordem,status) VALUES (?,?,?,?,?,?)")
            ->execute([$titulo,$slug,$icone,$conteudo,$ordem,$status]);
        $new_id = $pdo->lastInsertId();
        header('Location: edit.php?id=' . $new_id . '&msg=added'); exit;
    }
}
?>
<div class="row mb-4"><div class="col"><h2 class="page-title">Nova Secção — Cidadãos</h2><a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Voltar</a></div></div>
<?php if(!empty($errors)): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST"><div class="row g-3">
    <div class="col-md-6"><label class="form-label fw-bold small">Título</label><input type="text" name="titulo" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Slug</label><input type="text" name="slug" class="form-control" placeholder="auto-gerado"></div>
    <div class="col-md-3">
        <label class="form-label fw-bold small">Ícone Visual</label>
        <div class="input-group">
            <span class="input-group-text bg-white" id="icon-preview"><i class="fas fa-balance-scale"></i></span>
            <select name="icone" class="form-select" onchange="document.querySelector('#icon-preview i').className = this.value">
                <?php 
                $icons = [
                    'fas fa-balance-scale' => 'Justiça (Balança)',
                    'fas fa-gavel' => 'Lei (Martelo)',
                    'fas fa-shield-alt' => 'Defesa (Escudo)',
                    'fas fa-users' => 'Cidadãos (Social)',
                    'fas fa-handshake' => 'Cooperação',
                    'fas fa-university' => 'Institucional (Edifício)',
                    'fas fa-landmark' => 'Estado (Governo)',
                    'fas fa-briefcase' => 'Profissional (Pasta)',
                    'fas fa-file-contract' => 'Documentos (Contrato)',
                    'fas fa-user-graduate' => 'Formação (Estagiário)',
                    'fas fa-graduation-cap' => 'Academia (Estudos)',
                    'fas fa-book' => 'Biblioteca (Livro)',
                    'fas fa-award' => 'Mérito (Prémio)',
                    'fas fa-globe' => 'Internacional',
                    'fas fa-info-circle' => 'Informação',
                    'fas fa-bullhorn' => 'Comunicados (Avisos)',
                    'fas fa-search' => 'Pesquisa (Lupa)',
                    'fas fa-envelope' => 'Mensagem (Email)',
                    'fas fa-phone' => 'Contacto (Telefone)',
                    'fas fa-map-marker-alt' => 'Localização (Mapa)',
                    'fas fa-calendar-alt' => 'Agenda (Calendário)',
                    'fas fa-lock' => 'Segurança (Acesso)',
                    'fas fa-key' => 'Privacidade (Chave)',
                    'fas fa-flag' => 'Bandeira (Nação)',
                    'fas fa-building' => 'Sede (Edifício)',
                    'fas fa-chart-line' => 'Estatísticas',
                    'fas fa-print' => 'Impressão',
                    'fas fa-check-circle' => 'Sucesso',
                    'fas fa-exclamation-triangle' => 'Alerta'
                ];
                foreach ($icons as $class => $label):
                ?>
                <option value="<?php echo $class; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-3"><label class="form-label fw-bold small">Ordem</label><input type="number" name="ordem" class="form-control" value="0"></div>
    <div class="col-md-3"><label class="form-label fw-bold small">Estado</label><select name="status" class="form-select"><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select></div>
    <div class="col-12"><label class="form-label fw-bold small">Conteúdo (HTML)</label><textarea name="conteudo" id="editor" class="form-control" rows="8"></textarea></div>
    <div class="col-12"><button type="submit" class="btn btn-login px-4"><i class="fas fa-save me-2"></i>Guardar</button></div>
</div></form></div></div>
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
