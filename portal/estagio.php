<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';

if($mtype != 'estagiario') { header("Location: index.php"); exit; }

// Fetch Intern Info
$stmt = $pdo->prepare("SELECT e.*, a.nome_completo as orientador_nome 
                       FROM advogados_estagiarios e 
                       LEFT JOIN advogados a ON e.orientador_id = a.id 
                       WHERE e.id = ?");
$stmt->execute([$lid]);
$estagio = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Estágio | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-estagio { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .estagio-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        .timeline-step { position: relative; padding-left: 40px; border-left: 2px solid #eee; margin-bottom: 30px; }
        .timeline-step::before { content: ''; position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: white; border: 3px solid var(--primary-gold); border-radius: 50%; }
        .step-done::before { background: var(--primary-gold); }
    </style>
</head>
<body>

    <header class="hero-estagio">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Meu Percurso de Estágio</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="estagio-card">
            <div class="row g-5">
                <div class="col-md-7">
                    <h5 class="fw-bold mb-4">Cronograma Profissional</h5>
                    
                    <div class="timeline-step step-done">
                        <h6 class="fw-bold mb-1">Início do Estágio</h6>
                        <div class="small text-muted">Ato registado em <?php echo date('d/m/Y', strtotime($estagio['data_inicio_estagio'])); ?></div>
                        <p class="small mt-2">Inscrição formalizada perante o Conselho Nacional da Ordem.</p>
                    </div>

                    <div class="timeline-step">
                        <h6 class="fw-bold mb-1">Fase de Formação Teórica</h6>
                        <div class="small text-muted">A decorrer...</div>
                        <p class="small mt-2">Participação obrigatória em seminários e sessões de deontologia profissional.</p>
                    </div>

                    <div class="timeline-step">
                        <h6 class="fw-bold mb-1">Relatórios Periódicos</h6>
                        <div class="small text-muted">Pendente de submissão (Semestre 1)</div>
                        <p class="small mt-2">O seu relatório deve ser validado pelo seu patrono/orientador até ao final do quadrimestre.</p>
                    </div>

                    <div class="timeline-step">
                        <h6 class="fw-bold mb-1">Agregação à Ordem</h6>
                        <div class="small text-muted">Data prevista: <?php echo date('d/m/Y', strtotime($estagio['data_inicio_estagio'] . '+ 18 months')); ?></div>
                        <p class="small mt-2">Conclusão do período probatório e juramento institucional.</p>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card bg-light border-0 p-4 rounded-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user-tie me-2 text-primary"></i> Patrono / Orientador</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <?php echo substr($estagio['orientador_nome'] ?? 'P', 0, 1); ?>
                            </div>
                            <div>
                                <div class="fw-bold small"><?php echo $estagio['orientador_nome'] ?? 'Nenhum designado'; ?></div>
                                <div class="x-small text-muted">Patrono Principal</div>
                            </div>
                        </div>
                        <hr class="opacity-50">
                        <div class="x-small text-muted">
                            <p class="mb-2"><i class="fas fa-info-circle me-1"></i> O seu orientador é o responsável pela validação da sua prática profissional diária.</p>
                            <a href="#" class="text-primary fw-bold text-decoration-none">CONTACTAR PATRONO <i class="fas fa-external-link-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
