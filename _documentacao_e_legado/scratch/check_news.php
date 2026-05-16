<?php
require_once '../connect.php';

$titulo = "OAGB publica edital para formação de lista sêxtupla ao TRT-8";
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE titulo LIKE ?");
$stmt->execute(['%' . $titulo . '%']);
$noticia = $stmt->fetch();

echo "Dados da notícia:\n";
print_r($noticia);
?>
