<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];

// Create table and seed if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS gestao_oportunidades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        instituicao VARCHAR(255) NOT NULL,
        tipo VARCHAR(100) NOT NULL,
        data_limite DATE NOT NULL,
        descricao TEXT,
        link_edital VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $count = $pdo->query("SELECT COUNT(*) FROM gestao_oportunidades")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO gestao_oportunidades (titulo, instituicao, tipo, data_limite, descricao, link_edital) VALUES 
            ('Consultor Nacional para Apoio Jurídico à Reforma Legislativa', 'PNUD Guiné-Bissau', 'Consultoria', DATE_ADD(NOW(), INTERVAL 20 DAY), 'O Programa das Nações Unidas para o Desenvolvimento (PNUD) recruta consultor sénior nacional para assessorar a elaboração dos anteprojectos da nova legislação penal.', 'https://procurement-notices.undp.org'),
            ('Assessor Jurídico Sénior para Apoio ao Investimento Privado', 'Ministério da Economia e Finanças', 'Vaga Contratual', DATE_ADD(NOW(), INTERVAL 15 DAY), 'Contratação de advogado inscrito com mais de 5 anos de experiência para atuar na revisão de pareceres e concessões de licenças industriais de pesca.', '#'),
            ('Painel de Árbitros Oficiais para Resolução Aduaneira', 'Tribunal de Arbitragem Comercial (CCI-GB)', 'Arbitragem', DATE_ADD(NOW(), INTERVAL 35 DAY), 'Chamada pública para advogados especializados em Direito Comercial e Fiscal para integrarem a lista oficial de mediadores aduaneiros da Bacia do Rio Geba.', '#')
        ");
    }
} catch (PDOException $e) {
    // Fail silently if DB issues
}

// Fetch active opportunities
$stmt = $pdo->query("SELECT * FROM gestao_oportunidades WHERE data_limite >= CURDATE() ORDER BY data_limite ASC");
$oportunidades = $stmt->fetchAll();

$page_title = "Oportunidades Profissionais";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-oportunidades { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .op-card { background: white; border-radius: 16px; padding: 30px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; border: 1px solid #eee; display: flex; flex-direction: column; }
        .op-card:hover { transform: translateY(-5px); border-color: var(--primary-gold); }
    </style>
</head>
<body>

    <header class="hero-oportunidades">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0"><i class="fas fa-briefcase me-2"></i> Oportunidades Profissionais</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container my-5">
        <div class="row g-4">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm p-4 rounded-4">
                    <h6 class="fw-bold"><i class="fas fa-hand-holding-usd me-1 text-primary"></i> Retorno ao seu Investimento</h6>
                    <p class="small mb-0 opacity-75">Como membro ativo da Ordem com quotas regularizadas, tem acesso prioritário a anúncios de consultorias, concursos públicos nacionais e vagas internacionais para peritos jurídicos.</p>
                </div>
            </div>
            
            <?php if(empty($oportunidades)): ?>
                <div class="col-12 text-center py-5 opacity-50">Não há editais ou oportunidades profissionais ativas listadas nesta data.</div>
            <?php else: ?>
                <?php foreach($oportunidades as $op): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="op-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-dark text-white text-uppercase px-3 py-2 small"><?php echo htmlspecialchars($op['tipo']); ?></span>
                                <div class="x-small text-danger fw-bold"><i class="far fa-clock me-1"></i> Até <?php echo date('d/m/Y', strtotime($op['data_limite'])); ?></div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($op['titulo']); ?></h5>
                            <div class="small fw-bold text-muted mb-3"><i class="fas fa-university me-1"></i> <?php echo htmlspecialchars($op['instituicao']); ?></div>
                            <p class="small text-muted mb-4"><?php echo htmlspecialchars($op['descricao']); ?></p>
                            
                            <div class="mt-auto pt-2">
                                <a href="<?php echo htmlspecialchars($op['link_edital']); ?>" class="btn btn-dark w-100 rounded-pill fw-bold text-uppercase py-2" target="_blank">Consultar Edital <i class="fas fa-external-link-alt ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
