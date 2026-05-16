<?php
require_once __DIR__ . '/../connect.php';
try {
    $pdo->exec("ALTER TABLE advogados_estagiarios MODIFY COLUMN status ENUM('ativo','concluido','cancelado','pendente_aceitacao') DEFAULT 'pendente_aceitacao'");
    echo "Status 'pendente_aceitacao' adicionado aos estagiários.\n";
} catch(Exception $e) { echo "Erro: " . $e->getMessage() . "\n"; }
?>
