<?php
// scratch/check_db.php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';

try {
    $stmt = $pdo->query("DESCRIBE ficheiros_anexos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
