<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];

// Initializing Favorites Session array
if (!isset($_SESSION['library_favorites'])) {
    $_SESSION['library_favorites'] = [];
}

// Handle Favorite Toggle Action
if (isset($_GET['toggle_fav'])) {
    $fid = (int)$_GET['toggle_fav'];
    if (in_array($fid, $_SESSION['library_favorites'])) {
        $_SESSION['library_favorites'] = array_diff($_SESSION['library_favorites'], [$fid]);
    } else {
        $_SESSION['library_favorites'][] = $fid;
    }
    header("Location: biblioteca.php" . (isset($_GET['only_fav']) ? "?only_fav=1" : ""));
    exit;
}

// Auto-seed table if empty
try {
    $count = $pdo->query("SELECT COUNT(*) FROM gestao_biblioteca")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO gestao_biblioteca (titulo, tipo, data_publicacao, tags, ficheiro_url) VALUES 
            ('Código Civil da Guiné-Bissau', 'Lei', '2020-01-15', 'Código, Civil, Família', 'codigo_civil_gb.pdf'),
            ('Código de Processo Penal', 'Lei', '2021-06-10', 'Processo, Penal, Tribunal', 'codigo_processo_penal.pdf'),
            ('Lei de Terras e Propriedade', 'Lei', '2018-09-05', 'Terras, Concessões, Imóveis', 'lei_de_terras.pdf'),
            ('Regulamento das Sociedades de Advogados', 'Regulamento', '2023-11-20', 'Sociedades, Estatutos, Ética', 'regulamento_sociedades.pdf'),
            ('Acórdão sobre Prerrogativas de Defesa da OAGB', 'Acordao', '2024-03-12', 'Jurisprudência, Supremo, Defesa', 'acordao_prerrogativas.pdf')
        ");
    }
} catch (PDOException $e) {
    // Fail silently if table schema differs
}

// Handle Search and Filters
$search = $_GET['q'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$only_fav = isset($_GET['only_fav']) ? true : false;

$sql = "SELECT * FROM gestao_biblioteca WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (titulo LIKE ? OR tags LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($tipo) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo;
}
if ($only_fav && !empty($_SESSION['library_favorites'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['library_favorites']), '?'));
    $sql .= " AND id IN ($placeholders)";
    foreach ($_SESSION['library_favorites'] as $fav_id) {
        $params[] = $fav_id;
    }
} elseif ($only_fav) {
    $sql .= " AND 1=0"; // Return empty if filtering by favorites but none are marked
}

$sql .= " ORDER BY data_publicacao DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$docs = $stmt->fetchAll();

// Mock summaries database for the "IA Summary Assistant"
$mock_summaries = [
    'Código Civil da Guiné-Bissau' => [
        'resumo' => 'Este diploma fundamental rege a totalidade das relações civis, contratos e disposições familiares na República da Guiné-Bissau.',
        'artigos' => 'Artigos 1301.º a 1500.º (Relações de Família), Artigos 405.º e seguintes (Regime Geral das Obrigações e Validade Contratual).',
        'impacto' => 'Regula contratos civis, transmissões de propriedade, regimes de bens matrimoniais e sucessões nacionais.'
    ],
    'Código de Processo Penal' => [
        'resumo' => 'Rege o rito processual criminal, garantias de defesa dos cidadãos detidos e as competências instrutórias das forças policiais e magistrados.',
        'artigos' => 'Artigo 80.º (Garantias Fundamentais da Defesa e Acesso a Patrocínio Judiciário), Artigos 250.º e seguintes (Regime e Prazos de Prisão Preventiva).',
        'impacto' => 'Essencial para a actuação criminalística de defensores oficiosos e advogados nos tribunais de primeira instância e superiores.'
    ],
    'Lei de Terras e Propriedade' => [
        'resumo' => 'Dispõe sobre o estatuto jurídico do solo, concessões estatais, direitos comunitários de uso e posse de parcelas rurais e urbanas.',
        'artigos' => 'Artigo 4.º (Propriedade Originária do Estado sobre a Terra), Artigo 12.º (Reconhecimento das Concessões e Usos Tradicionais Colectivos).',
        'impacto' => 'Crucial para o aconselhamento de investimentos agro-industriais e resolução de litígios fundiários e heranças territoriais.'
    ],
    'Regulamento das Sociedades de Advogados' => [
        'resumo' => 'Regula o regime de constituição, regras deontológicas, limitações de responsabilidade e regras comerciais para sociedades em território nacional.',
        'artigos' => 'Artigo 5.º (Exclusividade de Objecto Social na Advocacia), Artigo 12.º (Regime de Incompatibilidades e Publicidade Proibida).',
        'impacto' => 'Rege a criação de novas bancas de advocacia corporativa na Guiné-Bissau e previne conflitos de interesse éticos.'
    ],
    'Acórdão sobre Prerrogativas de Defesa da OAGB' => [
        'resumo' => 'Decisão suprema que confirma a inviolabilidade de arquivos de advogados e impede a buscas judiciais em escritórios sem presença do Bastonário.',
        'artigos' => 'Decisão da Câmara Cível (Seção de Recursos), com referência expressa ao Estatuto da Ordem dos Advogados (Artigo 65.º).',
        'impacto' => 'Garante a imunidade funcional no livre exercício profissional dos defensores e sigilo profissional absoluto do cliente.'
    ]
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Jurídica Inteligente | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-lib { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .doc-card { background: white; border-radius: 16px; padding: 25px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; display: flex; flex-direction: column; border-bottom: 4px solid #eee; }
        .doc-card:hover { transform: translateY(-5px); border-bottom-color: var(--primary-gold); }
        .btn-fav { color: #ccc; transition: .2s; }
        .btn-fav.active { color: #e74c3c; }
        .bg-gold-accent { background: var(--primary-gold); color: #111923; }
    </style>
</head>
<body>

    <header class="hero-lib">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0"><i class="fas fa-brain me-2"></i> Biblioteca Inteligente</h2>
                <div class="d-flex gap-2">
                    <a href="biblioteca.php?only_fav=1" class="btn btn-outline-light rounded-pill px-3 fw-bold small"><i class="fas fa-heart text-danger me-1"></i> Favoritos</a>
                    <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold d-flex align-items-center"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
                </div>
            </div>
            <form class="row g-3" method="GET">
                <div class="col-md-7">
                    <input type="text" name="q" class="form-control border-0 bg-white bg-opacity-10 text-white p-3 px-4 rounded-pill shadow-none" placeholder="Pesquisar por título, palavra-chave ou código..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="tipo" class="form-select border-0 bg-white bg-opacity-10 text-white p-3 px-4 rounded-pill shadow-none">
                        <option value="" class="text-dark">Todas as Categorias</option>
                        <option value="Lei" <?php if($tipo=='Lei') echo 'selected'; ?> class="text-dark">Legislação Nacional</option>
                        <option value="Acordao" <?php if($tipo=='Acordao') echo 'selected'; ?> class="text-dark">Jurisprudência (Acórdãos)</option>
                        <option value="Regulamento" <?php if($tipo=='Regulamento') echo 'selected'; ?> class="text-dark">Regulamentos Internos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn w-100 p-3 rounded-pill bg-white text-dark border-0 fw-bold"><i class="fas fa-search me-1"></i> Procurar</button>
                </div>
            </form>
        </div>
    </header>

    <main class="container my-5">
        <div class="row g-4">
            <?php if(empty($docs)): ?>
                <div class="col-12 text-center py-5 opacity-40">Nenhum documento jurídico encontrado para esta pesquisa.</div>
            <?php else: ?>
                <?php foreach($docs as $d): 
                    $is_fav = in_array($d['id'], $_SESSION['library_favorites']);
                    $summary_data = $mock_summaries[$d['titulo']] ?? null;
                ?>
                    <div class="col-lg-4">
                        <div class="doc-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <a href="biblioteca.php?toggle_fav=<?php echo $d['id']; ?><?php echo $only_fav ? '&only_fav=1' : ''; ?>" class="btn-fav <?php echo $is_fav ? 'active' : ''; ?>" title="Adicionar aos Favoritos">
                                    <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart fa-lg"></i>
                                </a>
                                <span class="badge py-1 px-3 bg-light text-dark border small fw-bold text-uppercase"><?php echo $d['tipo']; ?></span>
                            </div>
                            <h6 class="fw-bold mb-2"><?php echo $d['titulo']; ?></h6>
                            <div class="x-small text-muted mb-4"><i class="far fa-calendar-alt me-1"></i> Publicado em <?php echo date('d/m/Y', strtotime($d['data_publicacao'])); ?></div>
                            
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <?php if($summary_data): ?>
                                    <!-- Dynamic IA Assistant Trigger -->
                                    <button class="btn btn-sm bg-gold-accent px-3 rounded-pill fw-bold" onclick="showIASummary(<?php echo htmlspecialchars(json_encode($d['titulo'])); ?>, <?php echo htmlspecialchars(json_encode($summary_data['resumo'])); ?>, <?php echo htmlspecialchars(json_encode($summary_data['artigos'])); ?>, <?php echo htmlspecialchars(json_encode($summary_data['impacto'])); ?>)"><i class="fas fa-robot me-1"></i> IA Resumo</button>
                                <?php endif; ?>
                                <a href="../uploads/biblioteca/<?php echo $d['ficheiro_url']; ?>" class="btn btn-sm btn-dark px-3 rounded-pill fw-bold" onclick="alert('A descarregar o documento oficial da Ordem...');"><i class="fas fa-download me-1"></i> PDF</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- IA Legal Assistant Modal -->
    <div class="modal fade" id="iaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0 mt-2 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-robot text-warning me-2"></i> IA Assistant Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted" id="iaDocTitle">TÍTULO DO DOCUMENTO</h6>
                    
                    <div class="bg-light p-3 rounded-3 border-start border-warning border-3 mb-3">
                        <div class="fw-bold small text-dark mb-1">Resumo Síntese da IA:</div>
                        <p class="small text-muted m-0" id="iaResumo">Este é o resumo automático gerado pela inteligência jurídica artificial da OAGB.</p>
                    </div>

                    <div class="mb-3">
                        <div class="fw-bold small text-dark mb-1"><i class="fas fa-book me-1 text-muted"></i> Artigos Chave / Relevantes:</div>
                        <div class="small text-muted" id="iaArtigos">Artigos de maior impacto regulatório.</div>
                    </div>

                    <div>
                        <div class="fw-bold small text-dark mb-1"><i class="fas fa-gavel me-1 text-muted"></i> Impacto Deontológico / Prático:</div>
                        <div class="small text-muted" id="iaImpacto">Implicações práticas para a sua advocacia em vigor.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showIASummary(title, resumo, artigos, impacto) {
            document.getElementById('iaDocTitle').innerText = title;
            document.getElementById('iaResumo').innerText = resumo;
            document.getElementById('iaArtigos').innerText = artigos;
            document.getElementById('iaImpacto').innerText = impacto;
            
            var modal = new bootstrap.Modal(document.getElementById('iaModal'));
            modal.show();
        }
    </script>
</body>
</html>
