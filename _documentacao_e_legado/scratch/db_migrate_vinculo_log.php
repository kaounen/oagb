<?php
require_once __DIR__ . '/../connect.php';
try {
    $pdo->exec("ALTER TABLE advogados_estagiarios 
                ADD COLUMN data_resposta_vinculo DATETIME NULL AFTER status,
                ADD COLUMN motivo_recusa TEXT NULL AFTER data_resposta_vinculo");
    echo "Colunas de rastreio de aceitação adicionadas.\n";
} catch(Exception $e) { echo "Erro: " . $e->getMessage() . "\n"; }
?>
