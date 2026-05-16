<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (!$id) { header("Location: index.php"); exit; }

try {
    $stmt = $pdo->prepare("SELECT * FROM gestao_actas WHERE id = ?");
    $stmt->execute([$id]);
    $acta = $stmt->fetch();
    if (!$acta) { header("Location: index.php"); exit; }

    // Security check: if not finalized, only admin or creator can see?
    // For now, if they have the ID and it's finalized, or if they are admin.
    if ($acta['status'] !== 'finalizada' && !isset($_SESSION['admin_id'])) {
        die("Esta acta ainda não foi finalizada para visualização pública.");
    }

} catch (Exception $e) { die("Erro ao carregar acta."); }

$page_title = "Acta Digital: " . $acta['titulo'];
$header_image = 'uploads/lady-justice-holding-scales-sword.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f7f5f0; }
        .acta-paper { background: #fff; padding: 60px; border-radius: 4px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative; min-height: 800px; margin-top: -100px; z-index: 10; border-top: 5px solid var(--primary-gold); }
        .acta-header-info { border-bottom: 1px solid #eee; margin-bottom: 40px; padding-bottom: 20px; }
        .acta-content { line-height: 1.8; font-size: 1.05rem; color: #333; text-align: justify; }
        .acta-code { font-family: monospace; color: var(--primary-gold); font-weight: 700; }
        .attachment-box { background: #f9fafb; border: 1px solid #eee; border-radius: 12px; padding: 25px; margin-top: 50px; }
        @media print {
            .no-print { display: none !important; }
            .acta-paper { margin-top: 0; box-shadow: none; padding: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <?php include 'includes/topbar.php'; ?>
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 350px; background: linear-gradient(rgba(17, 25, 35, 0.6), rgba(17, 25, 35, 0.8)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="container pb-5 mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-gold text-dark mb-2 px-3 py-2 fw-bold text-uppercase"><?php echo $acta['status']; ?></span>
                        <h1 class="text-white fw-bold mb-0" style="font-family: 'Libre Baskerville';">Livro de Actas Digital</h1>
                    </div>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-outline-light rounded-pill px-4"><i class="fas fa-print me-2"></i> Imprimir Acta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="acta-paper">
                    <div class="acta-header-info text-center">
                        <img src="img/logo3.png" alt="OAGB" style="height: 80px; margin-bottom: 20px; filter: grayscale(1);">
                        <h2 class="fw-bold mb-1" style="font-family: 'Libre Baskerville'; color: var(--primary-maroon);"><?php echo htmlspecialchars($acta['titulo']); ?></h2>
                        <div class="text-muted small text-uppercase fw-bold letter-spacing-1">
                            Código: <span class="acta-code"><?php echo $acta['codigo']; ?></span> | 
                            Data da Sessão: <?php echo date('d/m/Y', strtotime($acta['data_reuniao'])); ?>
                        </div>
                    </div>

                    <div class="acta-content">
                        <?php echo $acta['conteudo']; ?>
                    </div>

                    <?php if($acta['ficheiro_url']): ?>
                        <div class="attachment-box no-print">
                            <h6 class="fw-bold text-uppercase mb-3 small" style="color: var(--primary-maroon);"><i class="fas fa-paperclip me-2"></i> Documento Original em Anexo</h6>
                            <div class="d-flex align-items-center justify-content-between bg-white p-3 rounded-3 border">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-file-pdf fa-2x text-danger me-3"></i>
                                    <div>
                                        <div class="fw-bold small">Ficheiro Complementar</div>
                                        <div class="text-muted x-small">Documento oficial digitalizado e assinado.</div>
                                    </div>
                                </div>
                                <a href="<?php echo $acta['ficheiro_url']; ?>" target="_blank" class="btn btn-sm btn-dark px-4 rounded-pill">DESCARREGAR</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 pt-5 border-top text-center opacity-50 small">
                        <p>Esta acta foi lavrada e registrada digitalmente no sistema oficial da OAGB em <?php echo date('d/m/Y H:i', strtotime($acta['created_at'])); ?>.</p>
                        <p>Autenticidade garantida pelo código de referência: <strong><?php echo $acta['codigo']; ?></strong></p>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
