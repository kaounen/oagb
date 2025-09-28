<?php
// Ativar relatório de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Teste de Conexão MySQLi</h1>";

// Incluir o novo connect.php
require_once 'connect.php';

echo "<p style='color: green;'><strong>✓ Ficheiro connect.php carregado com sucesso!</strong></p>";

// Testar as funções básicas
try {
    // Testar se as tabelas existem
    $tabelas = ['advogados', 'advogados_estagiarios', 'noticias', 'agenda'];
    
    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM $tabela");
            $stmt->execute();
            $result = $stmt->fetch();
            echo "<p style='color: blue;'>✓ Tabela '$tabela': {$result->total} registos</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Erro na tabela '$tabela': " . $e->getMessage() . "</p>";
        }
    }
    
    // Testar busca de notícias
    echo "<hr><h2>Teste de Consultas</h2>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
        $stmt->execute();
        $noticias = $stmt->fetchAll();
        
        echo "<p style='color: green;'>✓ Consulta de notícias bem-sucedida!</p>";
        echo "<p><strong>Notícias encontradas:</strong> " . count($noticias) . "</p>";
        
        foreach ($noticias as $noticia) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
            echo "<h4>" . htmlspecialchars($noticia->titulo) . "</h4>";
            echo "<p><strong>Data:</strong> " . format_date($noticia->data_publicacao) . "</p>";
            echo "<p><strong>Resumo:</strong> " . htmlspecialchars(truncate_text($noticia->resumo, 100)) . "</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro ao buscar notícias: " . $e->getMessage() . "</p>";
    }
    
    // Testar busca de advogados
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados WHERE status = 'ativo'");
        $stmt->execute();
        $result = $stmt->fetch();
        $total_advogados = $result ? $result->total : 0;
        
        echo "<p style='color: green;'>✓ Total de advogados ativos: <strong>$total_advogados</strong></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro ao contar advogados: " . $e->getMessage() . "</p>";
    }
    
    // Testar busca de estagiários
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM advogados_estagiarios WHERE status = 'ativo'");
        $stmt->execute();
        $result = $stmt->fetch();
        $total_estagiarios = $result ? $result->total : 0;
        
        echo "<p style='color: green;'>✓ Total de estagiários ativos: <strong>$total_estagiarios</strong></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro ao contar estagiários: " . $e->getMessage() . "</p>";
    }
    
    // Testar funções auxiliares
    echo "<hr><h2>Teste de Funções Auxiliares</h2>";
    
    $data_teste = '2024-03-15 14:30:00';
    echo "<p>Data original: $data_teste</p>";
    echo "<p>format_date(): " . format_date($data_teste) . "</p>";
    echo "<p>format_datetime(): " . format_datetime($data_teste) . "</p>";
    
    $texto_teste = "Este é um texto muito longo que será truncado para testar a função truncate_text";
    echo "<p>Texto original: $texto_teste</p>";
    echo "<p>truncate_text(50): " . truncate_text($texto_teste, 50) . "</p>";
    
    $input_teste = "<script>alert('test')</script>Texto normal";
    echo "<p>Input original: $input_teste</p>";
    echo "<p>sanitize(): " . sanitize($input_teste) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Erro geral:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p style='color: green;'><strong>✓ Teste concluído!</strong></p>";
echo "<p><em>Se todos os testes passaram, pode testar agora a página index.php</em></p>";
?>
