<?php
require_once 'connect.php';
try {
    $pdo->exec("ALTER TABLE finan_pagamentos ADD COLUMN membro_tipo ENUM('advogado', 'estagiario') DEFAULT 'advogado' AFTER advogado_id");
    echo "Table updated successfully.\n";
} catch (PDOException $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}
?>
