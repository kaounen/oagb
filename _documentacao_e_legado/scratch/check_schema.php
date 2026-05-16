<?php
require_once __DIR__ . '/../connect.php';
$tables = ['advogados_estagiarios', 'gestao_estagio_relatorios', 'sociedades'];
foreach ($tables as $t) {
    echo "--- $t ---\n";
    $stmt = $pdo->query("DESCRIBE $t");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
}
?>
