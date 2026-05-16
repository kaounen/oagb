<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';

// Fetch Actas shared internally
$stmt = $pdo->prepare("SELECT * FROM gestao_actas WHERE status = 'finalizada' AND partilha_interna = 1 ORDER BY data_reuniao DESC");
$stmt->execute();
$actas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livro de Actas Digital | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --bg-main: #f5f6f8; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: var(--bg-main); }
        .portal-header { background: var(--sidebar-dark); padding: 30px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .acta-card { background: white; border-radius: 15px; padding: 25px; border: 1px solid #eee; transition: 0.3s; height: 100%; }
        .acta-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-color: var(--primary-gold); }
    </style>
</head>
<body>
    <header class="portal-header mb-5">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="fw-bold mb-0">Livro de Actas Digital</h4>
            </div>
            <img src="/oagb/img/LogoOA.png" alt="OAGB" style="height: 50px;">
        </div>
    </header>

    <main class="container">
        <div class="row g-4 mb-5">
            <?php if(empty($actas)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-file-invoice fa-4x text-muted mb-3 opacity-25"></i>
                    <h5 class="text-muted">Nenhuma acta disponível para consulta interna de momento.</h5>
                </div>
            <?php else: ?>
                <?php foreach($actas as $a): ?>
                    <div class="col-md-6">
                        <div class="acta-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-muted border small"><?php echo $a['codigo']; ?></span>
                                <span class="small text-muted fw-bold"><?php echo date('d/m/Y', strtotime($a['data_reuniao'])); ?></span>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($a['titulo']); ?></h5>
                            <p class="small text-muted mb-4 text-truncate-2"><?php echo strip_tags($a['conteudo']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="../view-acta.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-dark px-4 rounded-pill fw-bold">LER ACTA</a>
                                <?php if($a['ficheiro_url']): ?>
                                    <a href="../<?php echo $a['ficheiro_url']; ?>" target="_blank" class="text-decoration-none small fw-bold text-primary"><i class="fas fa-download me-1"></i> ANEXO PDF</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <style>
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</body>
</html>
