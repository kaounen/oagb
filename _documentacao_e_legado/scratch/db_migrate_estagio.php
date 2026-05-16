<?php
require_once __DIR__ . '/../connect.php';

try {
    // 1. Add sociedade_id to interns
    $pdo->exec("ALTER TABLE advogados_estagiarios ADD COLUMN sociedade_id INT(11) AFTER orientador_id");
    echo "Colunma sociedade_id adicionada.\n";
} catch(Exception $e) { echo "Erro ou já existe: " . $e->getMessage() . "\n"; }

try {
    // 2. Add relatorio_firma and revision status to reports
    $pdo->exec("ALTER TABLE gestao_estagio_relatorios ADD COLUMN relatorio_firma TEXT AFTER observacoes");
    // Modify status enum if needed (mysql dependent, but let's try)
    $pdo->exec("ALTER TABLE gestao_estagio_relatorios MODIFY COLUMN status ENUM('pendente','validado','rejeitado','revisao')");
    echo "Colunas de interação adicionadas aos relatórios.\n";
} catch(Exception $e) { echo "Erro ao atualizar relatórios: " . $e->getMessage() . "\n"; }

try {
    // 3. Create Interactions Table for comments and notes
    $pdo->exec("CREATE TABLE IF NOT EXISTS gestao_estagio_interacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        relatorio_id INT,
        autor_id INT,
        autor_tipo ENUM('advogado', 'estagiario', 'admin'),
        tipo ENUM('comentario', 'nota_interna', 'revisao'),
        mensagem TEXT,
        data_registo TIMESTAMP DEFAULT CURRENT__TIMESTAMP
    )");
    echo "Tabela de interações criada.\n";
} catch(Exception $e) { echo "Erro ao criar tabela interações: " . $e->getMessage() . "\n"; }
?>
