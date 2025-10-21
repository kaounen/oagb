<!DOCTYPE html>
<html>
<head>
    <title>Atualizar Terceiro Slide</title>
</head>
<body>
    <h2>Atualização do Terceiro Slide</h2>
    
<?php
require_once 'connect.php';

try {
    echo "<h3>Slides Atuais:</h3>";
    $stmt = $pdo->query('SELECT id, titulo, imagem, ordem FROM carousel_slides ORDER BY ordem ASC');
    $slides = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<table border='1'>";
    echo "<tr><th>Posição</th><th>ID</th><th>Ordem</th><th>Título</th><th>Imagem</th></tr>";
    foreach($slides as $i => $slide) {
        echo "<tr>";
        echo "<td>" . ($i + 1) . "</td>";
        echo "<td>" . $slide->id . "</td>";
        echo "<td>" . $slide->ordem . "</td>";
        echo "<td>" . htmlspecialchars($slide->titulo) . "</td>";
        echo "<td>" . htmlspecialchars($slide->imagem) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Atualizar terceiro slide
    if(isset($slides[2])) { // terceiro slide (índice 2)
        $terceiro_slide = $slides[2];
        
        echo "<h3>Atualizando Terceiro Slide:</h3>";
        echo "<p>ID: " . $terceiro_slide->id . "</p>";
        echo "<p>Título: " . htmlspecialchars($terceiro_slide->titulo) . "</p>";
        echo "<p>Imagem antiga: " . htmlspecialchars($terceiro_slide->imagem) . "</p>";
        
        $stmt = $pdo->prepare('UPDATE carousel_slides SET imagem = ? WHERE id = ?');
        $result = $stmt->execute(['close-up-scales-justice-original-azul.jpg', $terceiro_slide->id]);
        
        if($result) {
            echo "<p style='color: green;'><strong>✅ Terceiro slide atualizado com sucesso!</strong></p>";
            echo "<p>Nova imagem: close-up-scales-justice-original-azul.jpg</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao atualizar slide.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Terceiro slide não encontrado.</p>";
    }
    
    echo "<h3>Slides Após Atualização:</h3>";
    $stmt = $pdo->query('SELECT id, titulo, imagem, ordem FROM carousel_slides ORDER BY ordem ASC');
    $slides_updated = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "<table border='1'>";
    echo "<tr><th>Posição</th><th>ID</th><th>Ordem</th><th>Título</th><th>Imagem</th></tr>";
    foreach($slides_updated as $i => $slide) {
        $style = ($i == 2) ? "style='background-color: #ffffcc;'" : "";
        echo "<tr $style>";
        echo "<td>" . ($i + 1) . "</td>";
        echo "<td>" . $slide->id . "</td>";
        echo "<td>" . $slide->ordem . "</td>";
        echo "<td>" . htmlspecialchars($slide->titulo) . "</td>";
        echo "<td>" . htmlspecialchars($slide->imagem) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch(Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>

<p><a href="index.php">← Voltar ao site</a></p>

</body>
</html>