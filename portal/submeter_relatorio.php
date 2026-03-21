<?php
session_start();
if(!isset($_SESSION['lawyer_id']) || $_SESSION['member_type'] != 'estagiario') { header("Location: index.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Get Orientador
$stmt = $pdo->prepare("SELECT orientador_id FROM advogados_estagiarios WHERE id = ?");
$stmt->execute([$lid]);
$orientador_id = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['relatorio'])) {
    if (!$orientador_id) { $error = "Nenhum patrono/orientador designado. Contacte a secretaria."; }
    else {
        $file = $_FILES['relatorio'];
        if ($file['type'] == 'application/pdf') {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = "REL_EST_" . $lid . "_" . time() . "." . $ext;
            $uploadDir = "../uploads/estagio/relatorios/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $stmt = $pdo->prepare("INSERT INTO gestao_estagio_relatorios (estagiario_id, orientador_id, ficheiro_pdf) VALUES (?, ?, ?)");
                $stmt->execute([$lid, $orientador_id, $newName]);
                $success = "Relatório submetido com sucesso para validação do seu patrono.";
            } else { $error = "Erro no upload do ficheiro."; }
        } else { $error = "Apenas ficheiros PDF são permitidos."; }
    }
}

// Fetch History
$stmt = $pdo->prepare("SELECT * FROM gestao_estagio_relatorios WHERE estagiario_id = ? ORDER BY data_submissao DESC");
$stmt->execute([$lid]);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submeter Relatório | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-relatorio { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .relatorio-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; max-width: 800px; margin-left: auto; margin-right: auto; }
    </style>
</head>
<body>

    <header class="hero-relatorio">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Submissão de Relatórios</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="relatorio-card">
            <h5 class="fw-bold mb-4">Novo Envios (Fase de Estágio)</h5>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success border-0 small px-4 py-3 mb-4"><i class="fas fa-check-circle me-1"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 small px-4 py-3 mb-4"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="p-4 bg-light rounded-4 border-dashed border mb-5">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Ficheiro do Relatório (PDF)</label>
                    <input type="file" name="relatorio" class="form-control border-0 p-3" required accept=".pdf">
                    <div class="x-small text-muted mt-2">Certifique-se que o documento está assinado e legível.</div>
                </div>
                <button type="submit" class="btn btn-dark w-100 p-3 fw-bold rounded-3 text-uppercase">Enviar para o Patrono</button>
            </form>
            
            <h5 class="fw-bold mb-4 mt-5">Histórico de Submissões</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-muted small">
                    <thead>
                        <tr class="bg-light">
                            <th class="border-0 p-3">Data Envio</th>
                            <th class="border-0 p-3">Documento</th>
                            <th class="border-0 p-3 text-center">Estado de Validação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($history)): ?>
                            <tr><td colspan="3" class="text-center py-4 opacity-50">Nenhum relatório enviado.</td></tr>
                        <?php else: ?>
                            <?php foreach($history as $h): ?>
                                <tr>
                                    <td class="p-3"><?php echo date('d/m/Y', strtotime($h['data_submissao'])); ?></td>
                                    <td class="p-3 fw-bold text-dark"><i class="far fa-file-pdf me-2 text-danger"></i> <?php echo $h['ficheiro_pdf']; ?></td>
                                    <td class="p-3 text-center">
                                        <span class="badge py-2 px-3 <?php echo $h['status'] == 'validado' ? 'bg-success text-white' : ($h['status'] == 'pendente' ? 'bg-warning-subtle text-warning' : 'bg-danger text-white'); ?>">
                                            <?php echo strtoupper($h['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
