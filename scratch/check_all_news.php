<?php
require_once '../connect.php';

$stmt = $pdo->query("SELECT id, titulo, imagem_destaque, og_image FROM noticias ORDER BY data_publicacao DESC LIMIT 10");
$news = $stmt->fetchAll();

foreach ($news as $n) {
    echo "ID: {$n->id} | Titulo: {$n->titulo} | Imagem: '{$n->imagem_destaque}' | OG: '{$n->og_image}'\n";
}
?>
