<?php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';
try {
    // Check if column already exists in agenda
    $s = $pdo->query("DESCRIBE agenda");
    $exists = false;
    $has_ficheiro = false;
    foreach ($s as $r) {
        if ($r['Field'] === 'legenda_anexo') $exists = true;
        if ($r['Field'] === 'ficheiro_anexo') $has_ficheiro = true;
    }
    
    if (!$has_ficheiro) {
        $pdo->exec("ALTER TABLE agenda ADD COLUMN ficheiro_anexo VARCHAR(255) NULL AFTER imagem_destaque");
        echo "Column ficheiro_anexo added to agenda.\n";
    }

    if (!$exists) {
        $pdo->exec("ALTER TABLE agenda ADD COLUMN legenda_anexo VARCHAR(255) NULL AFTER ficheiro_anexo");
        echo "Column legenda_anexo added to agenda successfully.";
    } else {
        echo "Columns already exist in agenda.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
