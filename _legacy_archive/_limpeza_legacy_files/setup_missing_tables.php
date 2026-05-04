<?php
/**
 * Script para criar tabelas em falta na base de dados OAGB
 * Execute este script uma vez para configurar as tabelas necessárias
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'connect.php';

echo "<h2>Configuração das Tabelas OAGB</h2>\n";

try {
    // 1. Criar tabela carousel_slides
    echo "<h3>1. Criando tabela carousel_slides...</h3>\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `carousel_slides` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `titulo` varchar(255) NOT NULL,
          `subtitulo` text,
          `imagem` varchar(255) DEFAULT NULL,
          `link_texto` varchar(100) DEFAULT NULL,
          `link_url` varchar(255) DEFAULT NULL,
          `ordem_exibicao` int(11) DEFAULT 0,
          `ativo` tinyint(1) DEFAULT 1,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabela carousel_slides criada<br>\n";

    // Inserir slides padrão
    $pdo->exec("
        INSERT IGNORE INTO `carousel_slides` (`id`, `titulo`, `subtitulo`, `imagem`, `link_texto`, `link_url`, `ordem_exibicao`, `ativo`) VALUES
        (1, 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau', 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito.', 'uploads/brass-scales-justice-close-up-view.jpg', 'Saiba mais', 'apresentacao-historia.php', 1, 1),
        (2, 'Cadastro Nacional de Advogados', 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB.', 'uploads/close-up-scales-justice-original-azul.jpg', 'Pesquisar Advogados', 'pesquisa-advogados.php', 2, 1),
        (3, 'Justiça e Transparência', 'Garantindo a excelência jurídica e a defesa dos direitos dos cidadãos da Guiné-Bissau.', 'uploads/close-up-detail-scales-justice.jpg', 'Nossos Serviços', 'publicacoes.php', 3, 1)
    ");
    echo "✅ Slides padrão inseridos<br>\n";

    // 2. Criar tabela pareceres_deliberacoes
    echo "<h3>2. Criando tabela pareceres_deliberacoes...</h3>\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pareceres_deliberacoes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `titulo` varchar(255) NOT NULL,
          `tipo` enum('parecer','deliberacao') NOT NULL DEFAULT 'parecer',
          `numero_documento` varchar(50) DEFAULT NULL,
          `data_documento` date DEFAULT NULL,
          `conteudo` text,
          `link_url` varchar(255) DEFAULT NULL,
          `arquivo_pdf` varchar(255) DEFAULT NULL,
          `ativo` tinyint(1) DEFAULT 1,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_data_documento` (`data_documento`),
          KEY `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabela pareceres_deliberacoes criada<br>\n";

    $pdo->exec("
        INSERT IGNORE INTO `pareceres_deliberacoes` (`id`, `titulo`, `tipo`, `numero_documento`, `data_documento`, `link_url`, `ativo`) VALUES
        (1, 'Regulamento de Exercício da Advocacia', 'deliberacao', 'CNEF - Deliberação n.º 8/2023', '2023-12-15', 'pareceres-deliberacoes.php', 1)
    ");
    echo "✅ Parecer padrão inserido<br>\n";

    // 3. Criar tabela comunicados
    echo "<h3>3. Criando tabela comunicados...</h3>\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `comunicados` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `titulo` varchar(255) NOT NULL,
          `descricao` text,
          `conteudo` text,
          `data_publicacao` date DEFAULT NULL,
          `link_url` varchar(255) DEFAULT NULL,
          `arquivo_pdf` varchar(255) DEFAULT NULL,
          `ativo` tinyint(1) DEFAULT 1,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_data_publicacao` (`data_publicacao`),
          KEY `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabela comunicados criada<br>\n";

    $pdo->exec("
        INSERT IGNORE INTO `comunicados` (`id`, `titulo`, `descricao`, `data_publicacao`, `link_url`, `ativo`) VALUES
        (1, 'Comunicado - Assembleia Geral 2024', 'Convocação para Assembleia Geral Ordinária', '2024-01-15', 'comunicados.php', 1)
    ");
    echo "✅ Comunicado padrão inserido<br>\n";

    // 4. Atualizar tabela noticias se necessário
    echo "<h3>4. Atualizando tabela noticias...</h3>\n";
    try {
        $pdo->exec("ALTER TABLE `noticias` ADD COLUMN IF NOT EXISTS `destaque` tinyint(1) DEFAULT 0");
        echo "✅ Campo destaque adicionado a noticias<br>\n";
    } catch (Exception $e) {
        // Campo já existe
        echo "ℹ️ Campo destaque já existe em noticias<br>\n";
    }

    try {
        $pdo->exec("ALTER TABLE `noticias` ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1");
        echo "✅ Campo ativo adicionado a noticias<br>\n";
    } catch (Exception $e) {
        // Campo já existe
        echo "ℹ️ Campo ativo já existe em noticias<br>\n";
    }

    try {
        $pdo->exec("ALTER TABLE `noticias` ADD COLUMN IF NOT EXISTS `slug` varchar(255) DEFAULT NULL");
        echo "✅ Campo slug adicionado a noticias<br>\n";
    } catch (Exception $e) {
        // Campo já existe
        echo "ℹ️ Campo slug já existe em noticias<br>\n";
    }

    // 5. Atualizar tabela agenda se necessário
    echo "<h3>5. Atualizando tabela agenda...</h3>\n";
    try {
        $pdo->exec("ALTER TABLE `agenda` ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1");
        echo "✅ Campo ativo adicionado a agenda<br>\n";
    } catch (Exception $e) {
        // Campo já existe
        echo "ℹ️ Campo ativo já existe em agenda<br>\n";
    }

    // 6. Atualizar registros existentes
    echo "<h3>6. Atualizando registros existentes...</h3>\n";
    $pdo->exec("UPDATE `noticias` SET `destaque` = 1, `ativo` = 1 WHERE `id` <= 3");
    echo "✅ Primeiras 3 notícias marcadas como destaque<br>\n";

    $pdo->exec("UPDATE `agenda` SET `ativo` = 1");
    echo "✅ Todos os eventos marcados como ativos<br>\n";

    echo "<h2>✅ Configuração completa!</h2>\n";
    echo "<p><a href='index.php'>← Voltar ao site</a></p>\n";

} catch (Exception $e) {
    echo "<h3>❌ Erro: " . $e->getMessage() . "</h3>\n";
    echo "<p>Verifique se a base de dados está configurada corretamente.</p>\n";
}
?>