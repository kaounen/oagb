<?php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';
try {
    // Check if column already exists
    $s = $pdo->query("DESCRIBE noticias");
    $exists = false;
    foreach ($s as $r) {
        if ($r['Field'] === 'legenda_anexo') $exists = true;
    }
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE noticias ADD COLUMN legenda_anexo VARCHAR(255) NULL AFTER ficheiro_anexo");
        echo "Column added successfully.";
    } else {
        echo "Column already exists.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
