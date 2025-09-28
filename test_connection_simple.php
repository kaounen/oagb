<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>OAGB Database Connection Test</h1>";

// Test connection with production credentials
$host = 'localhost';
$dbname = 'korakund_ordem';
$username = 'korakund_advogados';
$password = 'GV@R4ra&rI{4}';

echo "<p><strong>Testing connection with:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Database: $dbname</li>";
echo "<li>User: $username</li>";
echo "<li>Password: " . str_repeat('*', strlen($password)) . "</li>";
echo "</ul>";

try {
    // Test PDO connection
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "<p style='color: green; font-size: 18px;'><strong>✅ PDO Connection: SUCCESS!</strong></p>";
    
    // Test basic query
    $stmt = $pdo->query("SELECT DATABASE() as current_db, NOW() as server_time, @@version as mysql_version");
    $info = $stmt->fetch();
    
    echo "<p><strong>Connection Details:</strong></p>";
    echo "<ul>";
    echo "<li>Current Database: " . $info->current_db . "</li>";
    echo "<li>Server Time: " . $info->server_time . "</li>";
    echo "<li>MySQL Version: " . $info->mysql_version . "</li>";
    echo "</ul>";
    
    // Test if main tables exist
    echo "<h2>Table Structure Check</h2>";
    
    $tables = ['advogados', 'noticias', 'agenda', 'advogados_estagiarios', 'configuracoes_site'];
    $table_status = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            echo "<p style='color: green;'>✅ Table '$table': EXISTS (Records: " . $result->count . ")</p>";
            $table_status[$table] = true;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Table '$table': ERROR - " . $e->getMessage() . "</p>";
            $table_status[$table] = false;
        }
    }
    
    // Test a sample query if main tables exist
    if ($table_status['noticias']) {
        try {
            $stmt = $pdo->query("SELECT titulo, data_publicacao FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
            $noticias = $stmt->fetchAll();
            
            echo "<h2>Sample Data Test</h2>";
            echo "<p style='color: green;'>✅ Found " . count($noticias) . " active news articles:</p>";
            
            foreach ($noticias as $noticia) {
                echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px; background: #f9f9f9;'>";
                echo "<strong>" . htmlspecialchars($noticia->titulo) . "</strong><br>";
                echo "<small>Published: " . $noticia->data_publicacao . "</small>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error querying news: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-size: 18px;'><strong>❌ CONNECTION FAILED!</strong></p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    echo "<h2>Troubleshooting Steps:</h2>";
    echo "<ol>";
    echo "<li>Make sure XAMPP MySQL service is running</li>";
    echo "<li>Check if database 'korakund_ordem' exists</li>";
    echo "<li>Check if user 'korakund_advogados' exists and has correct password</li>";
    echo "<li>Try running: <code>restore_database.bat</code></li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>