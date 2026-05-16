<?php
require_once 'connect.php';
$stmt = $pdo->query("DESCRIBE finan_pagamentos");
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
