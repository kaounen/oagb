<?php
require_once 'connect.php';

try {
    // Buscar o terceiro slide (ordem = 3 ou ID = 3)
    $stmt = $pdo->query('SELECT id, titulo, imagem, ordem FROM carousel_slides ORDER BY ordem ASC');
    $slides = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    foreach($slides as $i => $slide) {
        echo 'Slide ' . ($i + 1) . ' - ID: ' . $slide->id . ' - Ordem: ' . $slide->ordem . ' - Título: ' . $slide->titulo . ' - Imagem: ' . $slide->imagem . PHP_EOL;
    }
    
    echo PHP_EOL . "Atualizando terceiro slide..." . PHP_EOL;
    
    // Buscar especificamente o terceiro slide
    $terceiro_slide = null;
    foreach($slides as $i => $slide) {
        if($i == 2) { // terceiro slide (índice 2)
            $terceiro_slide = $slide;
            break;
        }
    }
    
    if($terceiro_slide) {
        $stmt = $pdo->prepare('UPDATE carousel_slides SET imagem = ? WHERE id = ?');
        $result = $stmt->execute(['close-up-scales-justice-original-azul.jpg', $terceiro_slide->id]);
        
        if($result) {
            echo 'Terceiro slide (ID: ' . $terceiro_slide->id . ') atualizado com sucesso!' . PHP_EOL;
            echo 'Nova imagem: close-up-scales-justice-original-azul.jpg' . PHP_EOL;
        } else {
            echo 'Erro ao atualizar slide.' . PHP_EOL;
        }
    } else {
        echo 'Terceiro slide não encontrado.' . PHP_EOL;
    }
    
} catch(Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
?>