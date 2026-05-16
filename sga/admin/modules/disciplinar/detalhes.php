<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

// Fetch Process Details
$stmt = $pdo->prepare("SELECT d.*, a.nome_completo as advogado_nome, r.nome_completo as relator_nome 
                       FROM gestao_disciplinar_processos d 
                       JOIN advogados a ON d.advogado_id = a.id 
                       LEFT JOIN advogados r ON d.relator_id = r.id 
                       WHERE d.id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) { header("Location: index.php"); exit; }

$advogados = $pdo->query("SELECT id, nome_completo FROM advogados ORDER BY nome_completo ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rel_id = $_POST['relator_id'] ?: null;
    $status = $_POST['status'];
    $sancao = $_POST['sancao_tipo'] ?? '';
    $desc = $_POST['descricao'] ?? '';
    $concl = $_POST['conclusao'] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE gestao_disciplinar_processos SET relator_id = ?, status = ?, sancao_tipo = ?, descricao = ?, conclusao = ? WHERE id = ?");
        $stmt->execute([$rel_id, $status, $sancao, $desc, $concl, $id]);
        
        header("Location: detalhes.php?id=$id&updated=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao atualizar: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Autos do Processo <?php echo $p['numero_processo']; ?></h2>
        <div class="text-muted small">Visualização e atualização do estado da instrução.</div>
    </div>
</div>

<?php if(isset($_GET['updated'])): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4">Processo atualizado com sucesso!</div>
<?php endif; ?>

<form method="POST">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Informações Base</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="x-small text-muted text-uppercase fw-bold">Advogado Visado</label>
                            <div class="fw-bold"><?php echo $p['advogado_nome']; ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-muted text-uppercase fw-bold">Queixoso</label>
                            <div class="fw-bold"><?php echo $p['queixoso_nome']; ?></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Descrição / Factos</label>
                        <textarea name="descricao" id="editor_desc" class="form-control bg-light border-0"><?php echo $p['descricao']; ?></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-uppercase fw-bold text-muted small">Conclusão / Acórdão / Decisão Final</label>
                        <textarea name="conclusao" id="editor_concl" class="form-control bg-light border-0"><?php echo $p['conclusao']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Estado & Designação</h5>
                    
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Relator Designado</label>
                        <select name="relator_id" class="form-select border-0 shadow-sm py-2">
                            <option value="">Aguardando Designação...</option>
                            <?php foreach($advogados as $a): ?>
                                <option value="<?php echo $a['id']; ?>" <?php echo $p['relator_id'] == $a['id'] ? 'selected' : ''; ?>><?php echo $a['nome_completo']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Estado Atual</label>
                        <select name="status" class="form-select border-0 shadow-sm py-2">
                            <option value="aberto" <?php echo $p['status'] == 'aberto' ? 'selected' : ''; ?>>Aberto / Triagem</option>
                            <option value="instrucao" <?php echo $p['status'] == 'instrucao' ? 'selected' : ''; ?>>Em Instrução</option>
                            <option value="julgamento" <?php echo $p['status'] == 'julgamento' ? 'selected' : ''; ?>>Em Julgamento</option>
                            <option value="arquivado" <?php echo $p['status'] == 'arquivado' ? 'selected' : ''; ?>>Arquivado</option>
                            <option value="sancionado" <?php echo $p['status'] == 'sancionado' ? 'selected' : ''; ?>>Sancionado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Sanção Aplicada (se houver)</label>
                        <input type="text" name="sancao_tipo" class="form-control border-0 shadow-sm" value="<?php echo $p['sancao_tipo']; ?>" placeholder="Ex: Suspensão 6 meses">
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-login w-100 py-3 mb-2 shadow-sm fw-bold">GUARDAR ALTERAÇÕES</button>
                    <a href="index.php" class="btn btn-light w-100 py-3 border">Voltar à Lista</a>
                </div>
            </div>
            
            <div class="alert bg-white border-0 shadow-sm small text-muted">
                <i class="fas fa-info-circle me-2 text-primary"></i> As alterações nos autos ficam registadas nos logs de auditoria do sistema.
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor_desc')).catch(e => console.error(e));
    ClassicEditor.create(document.querySelector('#editor_concl')).catch(e => console.error(e));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
