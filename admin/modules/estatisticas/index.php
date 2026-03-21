<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Stats Queries
try {
    // 1. Gender Distribution
    $gender_stats = $pdo->query("SELECT genero, COUNT(*) as total FROM advogados GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Region Distribution
    $region_stats = $pdo->query("SELECT regiao, COUNT(*) as total FROM (SELECT regiao FROM advogados UNION ALL SELECT regiao FROM advogados_estagiarios) as t GROUP BY regiao ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. New Members Trend (Last 12 Months)
    $trend_stats = $pdo->query("SELECT DATE_FORMAT(created_at, '%M') as mes, COUNT(*) as total FROM advogados WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY MONTH(created_at) ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Totals
    $total_lawyers = $pdo->query("SELECT COUNT(*) FROM advogados WHERE status = 'ativo'")->fetchColumn();
    $total_interns = $pdo->query("SELECT COUNT(*) FROM advogados_estagiarios WHERE status = 'ativo'")->fetchColumn();

} catch (PDOException $e) { $error = $e->getMessage(); }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Iinteligência Institucional</h2>
        <div class="text-muted small">Anàlise demográfica e estatística avançada da Ordem dos Advogados.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <button onclick="window.print()" class="btn btn-login-subtle text-login border-login-subtle border w-auto px-4 shadow-sm"><i class="fas fa-file-pdf me-2"></i> Exportar Relatòrio Anual</button>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 text-center bg-white h-100">
            <h1 class="fw-bold mb-0 text-dark"><?php echo $total_lawyers; ?></h1>
            <div class="small fw-bold text-muted text-uppercase mt-1">Advogados Ativos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 text-center bg-white h-100">
            <h1 class="fw-bold mb-0 text-dark"><?php echo $total_interns; ?></h1>
            <div class="small fw-bold text-muted text-uppercase mt-1">Advogados Estagiários</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 text-center bg-white h-100">
            <h1 class="fw-bold mb-0 text-primary"><?php echo round(($total_lawyers / ($total_lawyers + $total_interns)) * 100); ?>%</h1>
            <div class="small fw-bold text-muted text-uppercase mt-1">Relação Agregados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 text-center bg-dark text-white h-100">
            <h1 class="fw-bold mb-0"><?php echo $total_lawyers + $total_interns; ?></h1>
            <div class="small fw-bold text-white-50 text-uppercase mt-1">Universo Total</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Regional Distribution -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-5 h-100 bg-white">
            <h5 class="fw-bold mb-4">Densidade de Membros por Região</h5>
            <canvas id="regionChart" height="300"></canvas>
        </div>
    </div>

    <!-- Gender Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-5 h-100 bg-white text-center">
            <h5 class="fw-bold mb-4">Distribuição por Gênero</h5>
            <canvas id="genderChart"></canvas>
            <div class="mt-4 small fw-bold text-muted">Apoio a Políticas de Paridade</div>
        </div>
    </div>

    <!-- Growth Trend -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm p-5 bg-white">
            <h5 class="fw-bold mb-4">Crescimento da Classe (Últimos 12 Meses)</h5>
            <canvas id="growthChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Region Chart
    new Chart(document.getElementById('regionChart'), {
        type: 'bar',
        data: {
            labels: [<?php echo "'" . implode("','", array_column($region_stats, 'regiao')) . "'"; ?>],
            datasets: [{
                label: 'Membros',
                data: [<?php echo implode(",", array_column($region_stats, 'total')); ?>],
                backgroundColor: '#B1A276',
                borderRadius: 10
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Gender Chart
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: [<?php 
                    $m = 0; $f = 0; 
                    foreach($gender_stats as $gs) { if($gs['genero']=='M') $m=$gs['total']; else $f=$gs['total']; }
                    echo "$m, $f"; 
                ?>],
                backgroundColor: ['#111923', '#B1A276']
            }]
        }
    });

    // Growth Chart
    new Chart(document.getElementById('growthChart'), {
        type: 'line',
        data: {
            labels: [<?php echo "'" . implode("','", array_column($trend_stats, 'mes')) . "'"; ?>],
            datasets: [{
                label: 'Inscritções',
                data: [<?php echo implode(",", array_column($trend_stats, 'total')); ?>],
                borderColor: '#B1A276',
                fill: true,
                backgroundColor: 'rgba(177, 162, 118, 0.1)',
                tension: 0.4
            }]
        }
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
