<?php
// ajax/agenda_load_more.php
require_once '../includes/functions.php';
require_once '../connect.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$ano = isset($_GET['ano']) ? $_GET['ano'] : '';
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'proximos';
$limit = 10;
$offset = ($pagina - 1) * $limit;

$params = [];
$sql = "SELECT * FROM agenda WHERE ativo = 1";

if ($mode == 'proximos') {
    $sql .= " AND data_evento >= CURDATE()";
    $order = "ASC";
} else {
    $order = "DESC";
}

if (!empty($ano)) {
    $sql .= " AND YEAR(data_evento) = :ano";
    $params['ano'] = $ano;
}

if (!empty($mes)) {
    $sql .= " AND MONTH(data_evento) = :mes";
    $params['mes'] = $mes;
}

$sql .= " ORDER BY data_evento $order LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll();

if ($eventos) {
    // Meses PT Helper
    $meses_pt = [
        'Jan' => 'JAN', 'Feb' => 'FEV', 'Mar' => 'MAR',
        'Apr' => 'ABR', 'May' => 'MAI', 'Jun' => 'JUN',
        'Jul' => 'JUL', 'Aug' => 'AGO', 'Sep' => 'SET',
        'Oct' => 'OUT', 'Nov' => 'NOV', 'Dec' => 'DEZ'
    ];

    foreach ($eventos as $evento) {
        $data_evento = new DateTime($evento->data_evento);
        $dia = $data_evento->format('d');
        $mes_en = $data_evento->format('M');
        $ano_val = $data_evento->format('Y');
        $mes_pt = $meses_pt[$mes_en] ?? $mes_en;
        $link = "evento.php?id=" . $evento->id;
        ?>
        <div class="col-12 mb-4 animated fadeInUp" style="animation-duration: 0.6s;">
            <div class="agenda-evento-novo row align-items-center bg-white shadow-sm transition-all border-0" style="padding: 2rem; min-height: 180px; border-radius: 15px;">
                <!-- Data à esquerda (Estilo Index) -->
                <div class="col-lg-3 col-md-4 text-center agenda-data-container border-end">
                    <div class="agenda-data-display">
                        <div class="agenda-dia" style="font-size: 4rem; font-weight: 700; color: #B1A276; line-height: 1; font-family: 'Libre Baskerville', serif;">
                            <?php echo $dia; ?>
                        </div>
                        <div class="agenda-mes" style="font-size: 1.5rem; font-weight: 600; color: #5B463F; margin-top: -10px; font-family: 'Open Sans', sans-serif;">
                            <?php echo $mes_pt; ?>
                        </div>
                        <div class="agenda-ano" style="font-size: 1.2rem; font-weight: 400; color: #888; font-family: 'Open Sans', sans-serif;">
                            <?php echo $ano_val; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo à direita (Estilo Index) -->
                <div class="col-lg-9 col-md-8 agenda-conteudo-container ps-lg-5">
                    <h4 class="mb-2" style="color: #4D1C21; font-family: 'Libre Baskerville', serif; font-size: 1.3rem; font-weight: 600; line-height: 1.3;">
                        <a href="<?php echo $link; ?>" class="text-decoration-none" style="color: #4D1C21; transition: color 0.3s;">
                            <?php echo htmlspecialchars($evento->titulo); ?>
                        </a>
                    </h4>
                    
                    <?php if (!empty($evento->local_evento)): ?>
                    <p class="mb-2" style="color: #B1A276; font-family: 'Open Sans', sans-serif; font-size: 1rem; font-weight: 500;">
                        <i class="fa fa-map-marker-alt me-2" style="color: #B1A276;"></i><?php echo htmlspecialchars($evento->local_evento); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($evento->descricao)): ?>
                    <p class="texto-conteudo mb-3 text-muted" style="line-height: 1.6; font-size: 0.95rem; color: #5B463F !important;">
                        <?php echo htmlspecialchars(truncate_text($evento->descricao, 200)); ?>
                    </p>
                    <?php endif; ?>
                    
                    <a href="<?php echo $link; ?>" class="d-block" style="margin-top: 1rem;">
                        <div class="btn-arrow-only">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
