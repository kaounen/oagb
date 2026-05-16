<?php
require_once '../connect.php';

$titulo = "OAGB publica edital para formação de lista sêxtupla ao TRT-8";
$stmt = $pdo->prepare("SELECT * FROM carousel_slides WHERE titulo LIKE ?");
$stmt->execute(['%' . $titulo . '%']);
$slides = $stmt->fetchAll();

echo "Slides do carousel:\n";
print_r($slides);
?>
