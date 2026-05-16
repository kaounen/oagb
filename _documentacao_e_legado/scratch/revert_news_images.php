<?php
require_once '../connect.php';

// Deixar apenas a notícia do TRT-8 (ID 1 e 3) com a imagem
$stmt = $pdo->prepare("UPDATE noticias SET imagem_destaque = '' WHERE id NOT IN (1, 3) AND imagem_destaque = 'Asset 7-100.jpg'");
$stmt->execute();

echo "Linhas revertidas: " . $stmt->rowCount() . "\n";
?>
