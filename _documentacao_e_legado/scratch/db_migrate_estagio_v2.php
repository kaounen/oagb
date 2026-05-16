<?php
require_once __DIR__ . '/../connect.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS gestao_estagio_interacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        relatorio_id INT,
        autor_id INT,
        autor_tipo ENUM('advogado', 'estagiario', 'admin'),
        tipo ENUM('comentario', 'nota_interna', 'revisao'),
        mensagem TEXT,
        data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Tabela de interações criada.\n";
} catch(Exception $e) { echo "Erro: " . $e->getMessage() . "\n"; }
?>
