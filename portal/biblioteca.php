<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Fetch Categories
$stmt = $pdo->query("SELECT tipo, COUNT(*) as total FROM gestao_biblioteca GROUP BY tipo");
$cats = $stmt->fetchAll();

// Handle Search
$search = $_GET['q'] ?? '';
$tipo = $_GET['tipo'] ?? '';

$sql = "SELECT * FROM gestao_biblioteca WHERE 1=1";
$params = [];
if ($search) { $sql .= " AND (titulo LIKE ? OR tags LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($tipo) { $sql .= " AND tipo = ?"; $params[] = $tipo; }
$sql .= " ORDER BY data_publicacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$docs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Juràdica | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-lib { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .doc-card { background: white; border-radius: 16px; padding: 25px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; border-bottom: 4px solid #eee; }
        .doc-card:hover { transform: translateY(-5px); border-bottom-color: var(--primary-gold); }
    </style>
</head>
<body>

    <header class="hero-lib">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Repositòrio de Saber</h2>
                <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
            </div>
            <form class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="q" class="form-control border-0 bg-white bg-opacity-10 text-white p-3 px-4 rounded-pill shadow-none" placeholder="Pesquisar por tìtulo, palavra-chave ou No. do acórdão..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <select name="tipo" class="form-select border-0 bg-white bg-opacity-10 text-white p-3 px-4 rounded-pill shadow-none">
                        <option value="" class="text-dark">Todas as Categorias</option>
                        <option value="Lei" <?php if($tipo=='Lei') echo 'selected'; ?> class="text-dark">Legislação Nacional</option>
                        <option value="Acordao" <?php if($tipo=='Acordao') echo 'selected'; ?> class="text-dark">Jurisprudência (Acórdãos)</option>
                        <option value="Regulamento" <?php if($tipo=='Regulamento') echo 'selected'; ?> class="text-dark">Regulamentos Internos</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-login w-100 p-3 rounded-pill bg-white text-dark border-0 fw-bold"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </header>

    <main class="container my-5">
        <div class="row g-4">
            <?php if(empty($docs)): ?>
                <div class="col-12 text-center py-5 opacity-40">Nenhum documento jurídico encontrado para esta pesquisa.</div>
            <?php else: ?>
                <?php foreach($docs as $d): ?>
                    <div class="col-lg-4">
                        <div class="doc-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <i class="far fa-file-pdf fa-2x text-danger opacity-75"></i>
                                <span class="badge py-1 px-3 bg-light text-dark border small fw-bold text-uppercase"><?php echo $d['tipo']; ?></span>
                            </div>
                            <h6 class="fw-bold mb-2"><?php echo $d['titulo']; ?></h6>
                            <div class="x-small text-muted mb-4"><i class="far fa-calendar-alt me-1"></i> Publicado em <?php echo date('d/m/Y', strtotime($d['data_publicacao'])); ?></div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="x-small text-muted opacity-50"><i class="fas fa-tags me-1"></i> <?php echo $d['tags']; ?></div>
                                <a href="../uploads/biblioteca/<?php echo $d['ficheiro_url']; ?>" target="_blank" class="btn btn-sm btn-dark px-3 rounded-pill fw-bold">CONSULTAR</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
