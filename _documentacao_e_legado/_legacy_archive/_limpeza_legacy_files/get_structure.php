<?php
require_once 'connect.php';
$stmt = $pdo->query("DESCRIBE noticias");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
?>
