<?php
require_once '../connect.php';

$stmt = $pdo->prepare("UPDATE noticias SET imagem_destaque = 'Asset 7-100.jpg' WHERE imagem_destaque = '' OR imagem_destaque IS NULL");
$stmt->execute();

echo "Linhas afetadas: " . $stmt->rowCount() . "\n";
?>
