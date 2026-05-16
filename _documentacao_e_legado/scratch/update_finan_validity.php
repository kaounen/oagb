<?php
require_once 'connect.php';
try {
    $pdo->exec("ALTER TABLE finan_pagamentos ADD COLUMN valid_until DATE AFTER data_pagamento");
    // Update existing confirmed payments to be valid for at least 30 days from payment date
    $pdo->exec("UPDATE finan_pagamentos SET valid_until = DATE_ADD(data_pagamento, INTERVAL 30 DAY) WHERE status = 'confirmado' AND valid_until IS NULL");
    echo "Table updated with valid_until.\n";
} catch (PDOException $e) {
    echo "Column might already exist: " . $e->getMessage() . "\n";
}
?>
