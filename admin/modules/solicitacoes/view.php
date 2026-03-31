<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

// Fetch Request + All Advocates for Assignment
try {
    $stmt = $pdo->prepare("SELECT * FROM solicitacoes_advogados WHERE id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch();
    if(!$request) { header("Location: index.php"); exit; }

    $stmt = $pdo->prepare("SELECT id, nome_completo, numero_registo FROM advogados ORDER BY nome_completo ASC");
    $stmt->execute();
    $advogados = $stmt->fetchAll();
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Handle Status/Assignment Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $adv_id = $_POST['advogado_atribuido_id'] ?: null;
    $obs = $_POST['observacoes'];
    $data_atrib = ($adv_id && !$request['advogado_atribuido_id']) ? date('Y-m-d H:i:s') : $request['data_atribuicao'];

    try {
        $stmt = $pdo->prepare("UPDATE solicitacoes_advogados SET status = ?, advogado_atribuido_id = ?, observacoes = ?, data_atribuicao = ? WHERE id = ?");
        $stmt->execute([$status, $adv_id, $obs, $data_atrib, $id]);
        
        header("Location: view.php?id=$id&updated=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao atualizar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Gestão de Pedido</h2>
        <div class="text-muted small">Pedido #<?php echo $id; ?> enviado em <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?>.</div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Details Column -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Detalhes do Caso</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Solicitante</label>
                    <div class="fw-bold"><?php echo $request['nome_solicitante']; ?></div>
                    <div class="text-muted small"><?php echo $request['email']; ?> | <?php echo $request['telefone']; ?></div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Área Jurídica / Especialidade</label>
                    <div class="fw-bold text-primary"><?php echo $request['area_juridica']; ?></div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Região de Preferência</label>
                    <div class="fw-bold"><?php echo $request['regiao_preferencia']; ?></div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted text-uppercase fw-bold">Urgência</label>
                    <div>
                        <?php if($request['urgencia'] == 'alta'): ?>
                            <span class="badge bg-danger text-white px-3 py-2 small">ALTA</span>
                        <?php elseif($request['urgencia'] == 'media'): ?>
                            <span class="badge bg-warning text-dark px-3 py-2 small">MÉDIA</span>
                        <?php else: ?>
                            <span class="badge bg-info text-white px-3 py-2 small">BAIXA</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <label class="small text-muted text-uppercase fw-bold">Descrição do Caso</label>
            <div class="bg-light p-3 rounded small mb-4">
                <?php echo nl2br(htmlspecialchars($request['descricao_caso'])); ?>
            </div>
            
            <?php if($request['data_atribuicao']): ?>
                <div class="alert bg-info-subtle text-info border-0 shadow-sm small py-2">
                    <i class="fas fa-clock-rotate-left me-2"></i> Pedido atribuido em <?php echo date('d/m/Y H:i', strtotime($request['data_atribuicao'])); ?>.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Management Column -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-tasks me-2 text-primary"></i> Acções & Atribuição</h5>
            
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Status do Pedido</label>
                    <select name="status" class="form-select border-0 bg-light p-3">
                        <option value="pendente" <?php echo $request['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente (Aguardando)</option>
                        <option value="atribuido" <?php echo $request['status'] == 'atribuido' ? 'selected' : ''; ?>>Atribuído (Em processamento)</option>
                        <option value="concluido" <?php echo $request['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído (Caso aceite)</option>
                        <option value="cancelado" <?php echo $request['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado (Sem suporte)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Atribuir a Advogado</label>
                    <select name="advogado_atribuido_id" class="form-select border-0 bg-light p-3">
                        <option value="">-- Selecione Profissional da Base OAGB --</option>
                        <?php foreach($advogados as $adv): ?>
                            <option value="<?php echo $adv['id']; ?>" <?php echo $request['advogado_atribuido_id'] == $adv['id'] ? 'selected' : ''; ?>>
                                <?php echo $adv['nome_completo']; ?> (#<?php echo $adv['numero_registo']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Observações Internas</label>
                    <textarea name="observacoes" class="form-control border-0 bg-light p-3" rows="5"><?php echo $request['observacoes']; ?></textarea>
                    <div class="text-muted x-small mt-2">Visíveis apenas para a administração.</div>
                </div>

                <button type="submit" class="btn btn-login w-100 py-3 mt-3 shadow-lg">Actualizar & Notificar</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
