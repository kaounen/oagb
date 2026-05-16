<?php
require_once 'connect.php';

try {
    // 1. Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `anuncios` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `titulo` varchar(255) NOT NULL,
        `descricao` text,
        `link_url` varchar(255) DEFAULT NULL,
        `imagem` varchar(255) DEFAULT NULL,
        `ativo` tinyint(1) DEFAULT 1,
        `ordem_exibicao` int(11) DEFAULT 0,
        `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Check if there are records
    $stmt = $pdo->query("SELECT COUNT(*) FROM anuncios");
    if ($stmt->fetchColumn() == 0) {
        // 3. Insert sample data
        $stmt = $pdo->prepare("INSERT INTO anuncios (titulo, descricao, link_url) VALUES (?, ?, ?)");
        $stmt->execute(['Processo Seletivo 2026', 'Abertura das inscrições para o novo processo seletivo de advogados estagiários.', 'inscricoes.php']);
        $stmt->execute(['Novo Portal da Transparência', 'Já está disponível o novo módulo de consulta de processos e transparência institucional.', 'portal.php']);
        echo "Table created and sample data inserted successfully.";
    } else {
        echo "Table already exists and has data.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
