<?php
require_once 'connect.php';

try {
    $stmt = $pdo->query('SELECT id, titulo, imagem FROM carousel_slides ORDER BY ordem ASC LIMIT 5');
    $slides = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    foreach($slides as $i => $slide) {
        echo 'Slide ' . ($i + 1) . ' - ID: ' . $slide->id . ' - Título: ' . $slide->titulo . ' - Imagem: ' . $slide->imagem . PHP_EOL;
    }
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
?>