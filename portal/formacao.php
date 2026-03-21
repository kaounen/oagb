<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Fetch Courses
$stmt = $pdo->query("SELECT * FROM gestao_cursos WHERE ativa = 1 AND data_inicio >= NOW() ORDER BY data_inicio ASC");
$cursos = $stmt->fetchAll();

// Fetch Enrollments
$stmt = $pdo->prepare("SELECT i.*, c.titulo, c.data_inicio FROM gestao_cursos_inscritos i JOIN gestao_cursos c ON i.curso_id = c.id WHERE i.advogado_id = ?");
$stmt->execute([$lid]);
$inscritos = $stmt->fetchAll();

// Handle Enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curso_id'])) {
    $cid = $_POST['curso_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO gestao_cursos_inscritos (curso_id, advogado_id) VALUES (?, ?)");
        $stmt->execute([$cid, $lid]);
        header("Location: formacao.php?success=1"); exit;
    } catch (PDOException $e) { $error = "Já está inscrito neste curso."; }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formação Contínua | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-formacao { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .course-card { background: white; border-radius: 20px; padding: 30px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; border: 1px solid #eee; }
        .course-card:hover { transform: translateY(-5px); border-color: var(--primary-gold); }
    </style>
</head>
<body>

    <header class="hero-formacao">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Centro de Formação</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container my-5">
        <div class="row g-4 mb-5">
            <div class="col-12"><h4 class="fw-bold"><i class="fas fa-graduation-cap me-2 text-primary"></i> Próximos Seminários & Cursos</h4></div>
            <?php if(empty($cursos)): ?>
                <div class="col-12 text-center py-5 opacity-50">Nenhum curso com inscrições abertas no momento.</div>
            <?php else: ?>
                <?php foreach($cursos as $c): ?>
                    <div class="col-lg-4">
                        <div class="course-card">
                            <div class="badge bg-login-subtle text-login mb-3 p-2 px-3 fw-bold small"><?php echo date('d/m/Y', strtotime($c['data_inicio'])); ?></div>
                            <h5 class="fw-bold"><?php echo $c['titulo']; ?></h5>
                            <p class="small text-muted mb-4"><?php echo substr($c['descricao'], 0, 150); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="fw-bold"><?php echo number_format($c['preco'], 0, ',', '.'); ?> CFA</span>
                                <form method="POST"><input type="hidden" name="curso_id" value="<?php echo $c['id']; ?>"><button type="submit" class="btn btn-dark btn-sm px-4 fw-bold rounded-pill">INSCREVER-ME</button></form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card border-0 shadow-sm p-4 bg-white">
            <h5 class="fw-bold mb-4">Minhas Inscrições & Certificados</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr class="small text-uppercase fw-bold text-muted">
                            <th class="border-0 p-3">Curso</th>
                            <th class="border-0 p-3">Data Inìcio</th>
                            <th class="border-0 p-3 text-center">Estado</th>
                            <th class="border-0 p-3 text-end">Certificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($inscritos)): ?>
                            <tr><td colspan="4" class="text-center py-4 opacity-50">Ainda não se inscreveu em nenhum curso.</td></tr>
                        <?php else: ?>
                            <?php foreach($inscritos as $i): ?>
                                <tr>
                                    <td class="p-3 fw-bold text-dark"><?php echo $i['titulo']; ?></td>
                                    <td class="p-3 small text-muted"><?php echo date('d/m/Y', strtotime($i['data_inicio'])); ?></td>
                                    <td class="p-3 text-center">
                                        <span class="badge py-2 px-3 <?php echo $i['status'] == 'concluido' ? 'bg-success text-white' : 'bg-warning-subtle text-warning'; ?>">
                                            <?php echo strtoupper($i['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <?php if($i['status'] == 'concluido' && $i['certificado_url']): ?>
                                            <a href="../uploads/certificados/<?php echo $i['certificado_url']; ?>" target="_blank" class="btn btn-sm btn-login-subtle text-login border-0 fw-bold"><i class="fas fa-download me-1"></i> PDF</a>
                                        <?php else: ?>
                                            <span class="x-small text-muted">Indisponível</span>
                                        <?php endif; ?>
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
