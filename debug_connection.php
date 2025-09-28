<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Database Connection</h1>";

// Test different connection methods and credentials
$host = 'localhost';
$dbname = 'korakund_ordem';
$username = 'korakund_advogados';
$password = 'GV@R4ra&rI{4';

echo "<h2>Step 1: Testing Root Connection</h2>";
try {
    $pdo_root = new PDO("mysql:host=localhost", 'root', '');
    echo "<p style='color: green;'>✅ Root connection: SUCCESS</p>";
    
    // Check if database exists
    $stmt = $pdo_root->query("SHOW DATABASES LIKE 'korakund_ordem'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Database 'korakund_ordem' exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Database 'korakund_ordem' does NOT exist</p>";
    }
    
    // Check users
    echo "<h3>Current korakund_advogados users:</h3>";
    $stmt = $pdo_root->query("SELECT User, Host, plugin, authentication_string FROM mysql.user WHERE User = 'korakund_advogados'");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<p style='color: red;'>❌ No korakund_advogados users found!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>User</th><th>Host</th><th>Plugin</th><th>Has Password</th></tr>";
        foreach ($users as $user) {
            $has_pass = !empty($user['authentication_string']) ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$user['User']}</td>";
            echo "<td>{$user['Host']}</td>";
            echo "<td>{$user['plugin']}</td>";
            echo "<td>{$has_pass}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check grants for localhost user
    try {
        $stmt = $pdo_root->query("SHOW GRANTS FOR 'korakund_advogados'@'localhost'");
        echo "<h3>Grants for korakund_advogados@localhost:</h3>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "<li>" . htmlspecialchars($row[0]) . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ No grants found for korakund_advogados@localhost: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Root connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 2: Testing korakund_advogados Connection</h2>";

// Test different connection approaches
$test_credentials = [
    ['host' => 'localhost', 'user' => $username, 'pass' => $password],
    ['host' => '127.0.0.1', 'user' => $username, 'pass' => $password],
    ['host' => 'localhost', 'user' => $username, 'pass' => ''],  // Try empty password
];

foreach ($test_credentials as $i => $cred) {
    echo "<h3>Test " . ($i + 1) . ": {$cred['user']}@{$cred['host']} with password '" . str_repeat('*', strlen($cred['pass'])) . "'</h3>";
    
    try {
        $dsn = "mysql:host={$cred['host']};dbname=$dbname;charset=utf8mb4";
        $pdo_test = new PDO($dsn, $cred['user'], $cred['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        echo "<p style='color: green;'>✅ Connection SUCCESS!</p>";
        
        // Test a simple query
        $stmt = $pdo_test->query("SELECT COUNT(*) as count FROM noticias");
        $result = $stmt->fetch();
        echo "<p style='color: green;'>✅ Query test: Found {$result['count']} news records</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
        echo "<p><strong>Error code:</strong> " . $e->getCode() . "</p>";
    }
}

echo "<h2>Step 3: Manual User Recreation</h2>";
echo "<p>If all tests failed, run this SQL in phpMyAdmin:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
echo "-- Clean slate user creation\n";
echo "DROP USER IF EXISTS 'korakund_advogados'@'localhost';\n";
echo "DROP USER IF EXISTS 'korakund_advogados'@'127.0.0.1';\n";
echo "DROP USER IF EXISTS 'korakund_advogados'@'%';\n\n";
echo "CREATE USER 'korakund_advogados'@'localhost' IDENTIFIED BY 'GV@R4ra&rI{4}';\n";
echo "GRANT ALL PRIVILEGES ON korakund_ordem.* TO 'korakund_advogados'@'localhost';\n";
echo "FLUSH PRIVILEGES;\n";
echo "</pre>";

?>