<?php
/**
 * Script de teste para verificar conexão com a base de dados
 * REMOVER após teste bem-sucedido
 */

echo "<h2>Teste de Conexão - OAGB</h2>";

// Credenciais
$host = 'localhost';
$dbname = 'korakund_ordem';
$username = 'korakund_advogados';
$password = 'GV@R4ra&rI{4';

echo "<p><strong>Testando conexão com:</strong></p>";
echo "<ul>";
echo "<li>Host: " . $host . "</li>";
echo "<li>Base de dados: " . $dbname . "</li>";
echo "<li>Utilizador: " . $username . "</li>";
echo "<li>Password: " . str_repeat('*', strlen($password)) . "</li>";
echo "</ul>";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];
    
    echo "<p>🔄 A tentar conectar...</p>";
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "<p style='color: green;'>✅ <strong>Conexão bem-sucedida!</strong></p>";
    
    // Testar se as tabelas existem
    echo "<h3>Verificando tabelas:</h3>";
    $tables = ['advogados', 'noticias', 'agenda', 'advogados_estagiarios'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "<p style='color: green;'>✅ Tabela '$table': {$result->count} registos</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Tabela '$table': " . $e->getMessage() . "</p>";
        }
    }
    
    // Testar uma consulta simples
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total_noticias FROM noticias");
        $result = $stmt->fetch();
        echo "<p style='color: blue;'>📊 Total de notícias: {$result->total_noticias}</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Erro ao contar notícias: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>Erro de conexão:</strong></p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    
    // Diagnóstico adicional
    echo "<h3>Possíveis soluções:</h3>";
    echo "<ul>";
    echo "<li>Verificar se a base de dados '$dbname' existe</li>";
    echo "<li>Verificar se o utilizador '$username' tem permissões</li>";
    echo "<li>Verificar se a password está correta</li>";
    echo "<li>Verificar se o MySQL está em execução</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>⚠️ <strong>IMPORTANTE:</strong> Remover este ficheiro após o teste!</small></p>;"
?>