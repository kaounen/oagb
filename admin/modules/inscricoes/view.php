<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM inscricoes_ordem WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if(!$row) { header("Location: index.php"); exit; }
} catch (PDOException $e) { header("Location: index.php"); exit; }

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $obs = $_POST['observations'];
    $registo = $_POST['reg_number'] ?? '';

    try {
        $stmt = $pdo->prepare("UPDATE inscricoes_ordem SET status = ?, observacoes_admin = ?, numero_registo_atribuido = ?, data_analise = NOW() WHERE id = ?");
        $stmt->execute([$status, $obs, $registo, $id]);

        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'INSCRIPTION_UPDATE', "Atualizou status de inscriçao #$id para $status", 'inscricoes_ordem', $id);

        if ($status === 'aprovado') {
            // Logic to move/create lawyer record
            $check = $pdo->prepare("SELECT id FROM advogados WHERE numero_registo = ?");
            $check->execute([$registo]);
            if(!$check->fetch()) {
                $ins = $pdo->prepare("INSERT INTO advogados (numero_registo, nome_completo, genero, data_nascimento, nacionalidade, bi_passaporte, regiao, localidade, morada, telefone, email, status, data_inscricao) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativo', NOW())");
                $ins->execute([
                    $registo,
                    $row['nome_completo'],
                    $row['genero'],
                    $row['data_nascimento'],
                    $row['nacionalidade'],
                    $row['bi_passaporte'],
                    $row['regiao'],
                    $row['localidade'],
                    $row['morada'],
                    $row['telefone'],
                    $row['email']
                ]);
                LogHelper::create($pdo, 'advogados', $pdo->lastInsertId(), "Criado a partir de inscriçao #$id");
            }
        }

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao processar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Análise de Inscrição #<?php echo $id; ?></h2>
        <div class="text-muted small">Processamento técnico e deliberativo da candidatura.</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Dados Pessoais & Contacto</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Nome Completo</label>
                        <div class="fw-bold fs-5"><?php echo $row['nome_completo']; ?></div>
                    </div>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Tipo de Candidatura</label>
                        <div class="badge <?php echo $row['tipo_inscricao'] == 'advogado' ? 'bg-primary' : 'bg-info'; ?> py-2 px-3 small text-uppercase"><?php echo $row['tipo_inscricao']; ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Nacionalidade</label>
                        <div class="fw-bold small"><?php echo $row['nacionalidade']; ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">ID (BI/Passaporte)</label>
                        <div class="fw-bold small"><?php echo $row['bi_passaporte']; ?></div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Email</label>
                        <div class="small fw-bold"><?php echo $row['email']; ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Telefone</label>
                        <div class="small fw-bold"><?php echo $row['telefone']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Habilitações & Localização</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Formação Académica</label>
                    <div class="small bg-light p-3 rounded"><?php echo nl2br($row['formacao_academica']); ?></div>
                </div>
                <div class="mb-4">
                    <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Experiência Profissional</label>
                    <div class="small bg-light p-3 rounded"><?php echo nl2br($row['experiencia_profissional']); ?></div>
                </div>
                <div>
                    <label class="small text-uppercase fw-bold text-muted d-block opacity-50 mb-1">Morada / Residência</label>
                    <div class="small italic text-muted"><?php echo $row['morada']; ?>, <?php echo $row['localidade']; ?> (<?php echo $row['regiao']; ?>)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Decisão Administrativa</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Novo Status</label>
                        <select name="status" class="form-select border-0 bg-light py-2 px-3 fw-bold" required>
                            <option value="pendente" <?php echo $row['status'] == 'pendente'?'selected':''; ?>>Pendente</option>
                            <option value="em_analise" <?php echo $row['status'] == 'em_analise'?'selected':''; ?>>Em Análise</option>
                            <option value="aprovado" <?php echo $row['status'] == 'aprovado'?'selected':''; ?>>Aprovado</option>
                            <option value="rejeitado" <?php echo $row['status'] == 'rejeitado'?'selected':''; ?>>Rejeitado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Número de Registo (Se aprovar)</label>
                        <input type="text" name="reg_number" class="form-control border-0 bg-light py-2 px-3 fw-bold" placeholder="Ex: CP-01/2024" value="<?php echo $row['numero_registo_atribuido']; ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Observações / Justificação</label>
                        <textarea name="observations" class="form-control border-0 bg-light" rows="4" placeholder="Notas sobre a decisão..."><?php echo $row['observacoes_admin']; ?></textarea>
                    </div>

                    <button type="submit" name="update_status" class="btn btn-login w-100 py-3 mb-2 shadow-sm text-uppercase fw-bold">Actualizar Inscricção</button>
                    <a href="index.php" class="btn btn-light w-100 py-3 border">Sair sem Gravar</a>
                </form>
            </div>
        </div>

        <?php if($row['arquivo_comprovativo']): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Comprovativo de Taxa</h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded text-center">
                        <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2 opacity-50"></i>
                        <div class="small mb-3 fw-bold">Anexo disponível para verificação</div>
                        <a href="/oagb/uploads/inscricoes/<?php echo $row['arquivo_comprovativo']; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-4 py-2">Consultar Documento</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
