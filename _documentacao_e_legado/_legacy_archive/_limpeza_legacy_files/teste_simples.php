<?php
/**
 * TESTE SIMPLES - Apenas para verificar se PHP básico funciona
 * REMOVER após confirmação
 */

echo "<h1>Teste de Funcionamento OAGB</h1>";

// Teste 1: PHP básico
echo "<h2>✅ PHP está funcionando!</h2>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";

// Teste 2: Sessões
session_start();
echo "<p>✅ Sessões funcionam!</p>";

// Teste 3: Incluir functions.php
try {
    require_once 'includes/functions.php';
    echo "<p>✅ Functions.php carregado com sucesso!</p>";
    
    // Testar uma função
    $data_teste = format_date(date('Y-m-d'));
    echo "<p>✅ Função format_date: " . $data_teste . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erro no functions.php: " . $e->getMessage() . "</p>";
}

// Teste 4: Conexão com base de dados
try {
    $host = 'localhost';
    $dbname = 'korakund_ordem';
    $username = 'korakund_advogados';
    $password = 'GV@R4ra&rI{4';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p>✅ Conexão com base de dados: <strong>SUCESSO</strong></p>";
    
    // Testar consulta simples
    $stmt = $pdo->query("SELECT 1 as teste");
    $result = $stmt->fetch();
    echo "<p>✅ Consulta teste: " . $result->teste . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erro na base de dados: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Se todos os testes passaram, o index.php deve funcionar!</strong></p>";
echo "<p><a href='index.php'>➡️ Testar Index.php</a></p>";
echo "<p><small>⚠️ Remover este ficheiro após o teste!</small></p>";
?>