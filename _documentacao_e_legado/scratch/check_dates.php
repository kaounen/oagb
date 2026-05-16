<?php
require_once '../connect.php';

$stmt = $pdo->query("SELECT id, titulo, data_publicacao FROM noticias WHERE id IN (1, 3)");
$news = $stmt->fetchAll();

foreach ($news as $n) {
    echo "ID: {$n->id} | Titulo: {$n->titulo} | Data: {$n->data_publicacao}\n";
}
?>
