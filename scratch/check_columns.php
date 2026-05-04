<?php
require_once '../connect.php';

$stmt = $pdo->query("DESCRIBE noticias");
$columns = $stmt->fetchAll();

foreach ($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}
?>
