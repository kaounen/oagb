<?php
// orgaos-sociais.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';
require_once 'connect.php';

// Fetch Config
$config = $pdo->query("SELECT * FROM orgaos_config WHERE id = 1")->fetch();

$page_title = 'Órgãos Sociais';
$breadcrumbs = [
    'Início' => 'index.php',
    'A Ordem' => '#',
    $page_title => '#'
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/common_head.php'; ?>
    <style>
        .group-header { border-left: 5px solid #B1A276; padding-left: 15px; margin-bottom: 30px; font-family: 'Libre Baskerville', serif; }
        .member-card { transition: all 0.3s; border: none; border-radius: 12px; overflow: hidden; height: 100%; }
        .member-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .member-img { width: 100%; height: 300px; object-fit: cover; }
        .member-info { padding: 20px; background: #fff; }
        .member-role { color: #B1A276; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .organogram-container { background: #f8f9fa; border-radius: 20px; padding: 40px; margin-bottom: 60px; overflow-x: auto; }
        .btn-toggle-view { border-radius: 50px; padding: 10px 25px; font-weight: bold; }
        
        /* Simple Organogram Tree */
        .tree ul { padding-top: 20px; position: relative; transition: all 0.5s; display: flex; justify-content: center; }
        .tree li { float: left; text-align: center; list-style-type: none; position: relative; padding: 20px 5px 0 5px; transition: all 0.5s; }
        .tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 20px; }
        .tree li::after { right: auto; left: 50%; border-left: 2px solid #ccc; }
        .tree li:only-child::after, .tree li:only-child::before { display: none; }
        .tree li:only-child { padding-top: 0; }
        .tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
        .tree li:last-child::before { border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
        .tree li:first-child::after { border-radius: 5px 0 0 0; }
        .tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 20px; }
        .tree li .node { border: 1px solid #eee; padding: 10px; text-decoration: none; color: #666; font-size: 11px; display: inline-block; border-radius: 8px; transition: all 0.5s; background: #fff; width: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .tree li .node:hover { background: #B1A276; color: #fff; border: 1px solid #B1A276; transform: scale(1.1); }
    </style>
</head>
<body class="bg-white">
    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>

    <!-- Global Unified Header -->
    <?php 
    $header_light = true; // Forçar visual "scrolled" (dark text) em páginas de fundo claro
    include 'includes/header_global.php'; 
    ?>
    
    <!-- Standard Clean Header (Breadcrumbs) -->
    <?php 
    $has_header_image = false; // Clean white header as requested
    include 'includes/page-header.php'; 
    ?>

    <div class="container py-5">
        
        <!-- View Toggle (If enabled) -->
        <div class="text-center mb-5">
            <div class="btn-group shadow-sm p-1 bg-light rounded-pill">
                <button class="btn btn-primary rounded-pill px-4" id="view-list" onclick="toggleView('list')"><i class="fas fa-users me-2"></i> Lista de Membros</button>
                <button class="btn btn-light rounded-pill px-4" id="view-chart" onclick="toggleView('chart')"><i class="fas fa-sitemap me-2"></i> Organograma</button>
            </div>
        </div>

        <!-- Section: Members List -->
        <div id="members-list-view">
            <?php
            $groups = $pdo->query("SELECT * FROM orgaos_diretivos ORDER BY id ASC")->fetchAll();
            foreach($groups as $g):
                $members = $pdo->prepare("SELECT * FROM orgaos_sociais WHERE orgao_diretivo_id = ? AND ativo = 1 ORDER BY ordem_exibicao ASC");
                $members->execute([$g->id]);
                $results = $members->fetchAll();
                
                if (count($results) > 0):
            ?>
                <div class="mb-5 animated fadeInUp">
                    <h3 class="group-header"><?php echo htmlspecialchars($g->nome); ?></h3>
                    <div class="row g-4">
                        <?php foreach($results as $m): ?>
                            <div class="col-lg-3 col-md-6">
                                <div class="member-card shadow-sm border p-0">
                                    <img src="<?php echo $m->foto ? 'uploads/orgaos/'.$m->foto : 'img/user-default.png'; ?>" class="member-img" alt="<?php echo $m->nome; ?>">
                                    <div class="member-info text-center border-top">
                                        <div class="member-role"><?php echo htmlspecialchars($m->cargo); ?></div>
                                        <h5 class="fw-bold my-1"><?php echo htmlspecialchars($m->nome); ?></h5>
                                        <?php if($m->mandato_inicio): ?>
                                            <div class="x-small text-muted mt-2">Mandato: <?php echo date('Y', strtotime($m->mandato_inicio)); ?> - <?php echo $m->mandato_fim ? date('Y', strtotime($m->mandato_fim)) : 'Presente'; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>

        <!-- Section: Organogram -->
        <div id="organogram-view" class="d-none animated fadeIn">
            <h3 class="text-center mb-5 fw-bold">Estrutura Organizacional OAGB</h3>
            <div class="organogram-container border shadow-sm text-center">
                <?php if($config->modo_exibicao == 'imagem' && $config->organograma_path): ?>
                    <img src="uploads/orgaos/<?php echo $config->organograma_path; ?>" class="img-fluid rounded" alt="Organograma OAGB">
                <?php elseif($config->modo_exibicao == 'pdf' && $config->organograma_pdf_path): ?>
                    <div class="py-5">
                        <i class="far fa-file-pdf fa-4x text-danger mb-3"></i>
                        <h5>Organograma em PDF</h5>
                        <p class="text-muted">A estrutura está disponível para consulta em formato PDF de alta qualidade.</p>
                        <a href="uploads/orgaos/<?php echo $config->organograma_pdf_path; ?>" target="_blank" class="btn btn-primary px-5 py-3 rounded-pill mt-2">Descarregar Organograma</a>
                    </div>
                <?php else: ?>
                    <!-- Auto Generated Tree -->
                    <div class="tree">
                        <?php
                        function renderNode($pdo, $superior_id = null) {
                            $stmt = $pdo->prepare("SELECT id, nome, cargo, foto FROM orgaos_sociais WHERE superior_id " . ($superior_id === null ? "IS NULL" : "= ?") . " AND ativo = 1 ORDER BY ordem_exibicao ASC");
                            $stmt->execute($superior_id === null ? [] : [$superior_id]);
                            $nodes = $stmt->fetchAll();
                            
                            if (count($nodes) > 0) {
                                echo "<ul>";
                                foreach($nodes as $node) {
                                    echo "<li>";
                                    echo "<div class='node shadow-sm'><div class='fw-bold' style='color:#091e3e;'>{$node->nome}</div><div class='x-small text-muted'>{$node->cargo}</div></div>";
                                    renderNode($pdo, $node->id);
                                    echo "</li>";
                                }
                                echo "</ul>";
                            }
                        }
                        renderNode($pdo);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>

    <script>
        function toggleView(view) {
            if (view === 'list') {
                document.getElementById('members-list-view').classList.remove('d-none');
                document.getElementById('organogram-view').classList.add('d-none');
                document.getElementById('view-list').classList.replace('btn-light', 'btn-primary');
                document.getElementById('view-chart').classList.replace('btn-primary', 'btn-light');
            } else {
                document.getElementById('members-list-view').classList.add('d-none');
                document.getElementById('organogram-view').classList.remove('d-none');
                document.getElementById('view-chart').classList.replace('btn-light', 'btn-primary');
                document.getElementById('view-list').classList.replace('btn-primary', 'btn-light');
            }
        }
    </script>
</body>
</html>
