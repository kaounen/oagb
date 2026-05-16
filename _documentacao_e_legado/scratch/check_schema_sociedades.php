<?php
require_once __DIR__ . '/../connect.php';
$t = 'gestao_sociedades';
echo "--- $t ---\n";
$stmt = $pdo->query("DESCRIBE $t");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['Field']} - {$row['Type']}\n";
}
?>
