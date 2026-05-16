<?php
require_once __DIR__ . '/../connect.php';
try {
    $pdo->exec("ALTER TABLE advogados_estagiarios ADD COLUMN fase_estagio ENUM('instrucao','pratica','concluido') DEFAULT 'instrucao' AFTER sociedade_id");
    echo "Coluna fase_estagio adicionada com sucesso.\n";
} catch(Exception $e) { echo "Erro: " . $e->getMessage() . "\n"; }
?>
