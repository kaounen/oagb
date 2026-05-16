<?php
require_once 'connect.php';
try {
    $pdo->exec("ALTER TABLE finan_pagamentos ADD COLUMN meses_pagos INT DEFAULT 1 AFTER tipo_pagamento_id");
    echo "Added meses_pagos column.\n";
} catch (PDOException $e) {
    echo "Column exists or error: " . $e->getMessage() . "\n";
}
?>
