<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Get Current User Info
$stmt = $pdo->prepare("SELECT sociedade_id, is_sociedade_gestor FROM advogados WHERE id = ?");
$stmt->execute([$lid]);
$me = $stmt->fetch();

if (!$me['is_sociedade_gestor'] || !$me['sociedade_id']) { header("Location: index.php"); exit; }

// Get Society Info
$stmt = $pdo->prepare("SELECT * FROM gestao_sociedades WHERE id = ?");
$stmt->execute([$me['sociedade_id']]);
$sociedade = $stmt->fetch();

// Fetch Team Members
$stmt = $pdo->prepare("SELECT a.id, a.nome_completo, a.numero_registo, a.email, a.telefone,
           (SELECT COUNT(*) FROM finan_pagamentos 
            WHERE advogado_id = a.id AND tipo_pagamento_id = 1 
            AND status = 'confirmado' AND MONTH(data_pagamento) = MONTH(NOW()) AND YEAR(data_pagamento) = YEAR(NOW())) as regularizado 
           FROM advogados a 
           WHERE a.sociedade_id = ? AND a.id != ? 
           ORDER BY a.nome_completo ASC");
$stmt->execute([$me['sociedade_id'], $lid]);
$membros = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Sociedade | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-soc { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .team-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        .badge-status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
    </style>
</head>
<body>

    <header class="hero-soc">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-building me-2 opacity-50"></i> <?php echo $sociedade['nome']; ?></h4>
                <div class="small opacity-50 text-uppercase fw-bold" style="letter-spacing: 1px;">Área de Gestão de Firmas</div>
            </div>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="team-card">
            <h5 class="fw-bold mb-4">Membros da Sociedade & Conformidade Institucional</h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr class="small text-uppercase fw-bold text-muted">
                            <th class="border-0 p-3">Advogado / Beneficiário</th>
                            <th class="border-0 p-3">Contacto</th>
                            <th class="border-0 p-3 text-center">Estado Financeiro</th>
                            <th class="border-0 p-3 text-end">Ação Recomendada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($membros)): ?>
                            <tr><td colspan="4" class="text-center py-5 opacity-50">Nenhum membro registado na sua sociedade.</td></tr>
                        <?php else: ?>
                            <?php foreach($membros as $m): ?>
                                <tr>
                                    <td class="p-3">
                                        <div class="fw-bold text-dark small mb-0"><?php echo $m['nome_completo']; ?></div>
                                        <div class="x-small text-muted"><?php echo $m['numero_registo']; ?></div>
                                    </td>
                                    <td class="p-3 small">
                                        <div><i class="far fa-envelope me-1 opacity-50"></i> <?php echo $m['email']; ?></div>
                                        <div><i class="fas fa-phone me-1 opacity-50"></i> <?php echo $m['telefone']; ?></div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <?php if($m['regularizado']): ?>
                                            <span class="badge py-2 px-3 bg-success-subtle text-success border border-success-subtle rounded-pill small">
                                                <i class="fas fa-check-circle me-1"></i> REGULARIZADO
                                            </span>
                                        <?php else: ?>
                                            <span class="badge py-2 px-3 bg-danger-subtle text-danger border border-danger-subtle rounded-pill small">
                                                <i class="fas fa-exclamation-triangle me-1"></i> EM ATRASO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-end">
                                        <?php if(!$m['regularizado']): ?>
                                            <a href="pagamento_gateway.php?bill_mock=1&adv=<?php echo $m['id']; ?>" class="btn btn-sm btn-dark px-3 fw-bold rounded-pill">LIQUIDAR PELO MEMBRO</a>
                                        <?php else: ?>
                                            <span class="x-small text-muted fw-bold">CONFORME</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 p-4 bg-primary-subtle rounded-4 border border-primary border-opacity-10">
                <h6 class="fw-bold mb-2 text-primary"><i class="fas fa-info-circle me-1"></i> Conformidade Corporativa</h6>
                <p class="small text-primary-emphasis mb-0">Como gestor da firma, é o seu dever garantir que todos os membros inscritos sob o seu NIF cumpram as obrigações institucionais de forma a manter o exercício profissional sem interrupções.</p>
            </div>
        </div>
    </main>

</body>
</html>
