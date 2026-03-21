<?php
/**
 * Quick test and fix for index.php issues
 */

echo "<h2>OAGB Index.php Quick Diagnostics & Fix</h2>\n";

// Test 1: Database connection
echo "<h3>1. Testando conexão da base de dados...</h3>\n";
try {
    include 'connect.php';
    echo "✅ Conexão estabelecida com sucesso!<br>\n";
    
    // Test existing tables
    $tables = ['noticias', 'agenda', 'advogados'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Tabela '$table' existe ($count registros)<br>\n";
        } catch (Exception $e) {
            echo "❌ Problema com tabela '$table': " . $e->getMessage() . "<br>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>\n";
}

// Test 2: Functions file
echo "<h3>2. Testando ficheiro de funções...</h3>\n";
try {
    include 'includes/functions.php';
    echo "✅ Funções carregadas com sucesso!<br>\n";
} catch (Exception $e) {
    echo "❌ Erro no ficheiro de funções: " . $e->getMessage() . "<br>\n";
}

// Test 3: Create missing tables if needed
if (isset($pdo)) {
    echo "<h3>3. Verificando e criando tabelas em falta...</h3>\n";
    
    // Check carousel_slides table
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'carousel_slides'");
        if ($stmt->rowCount() == 0) {
            echo "Criando tabela carousel_slides...<br>\n";
            $pdo->exec("
                CREATE TABLE `carousel_slides` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `titulo` varchar(255) NOT NULL,
                  `subtitulo` text,
                  `imagem` varchar(255) DEFAULT NULL,
                  `link_texto` varchar(100) DEFAULT NULL,
                  `link_url` varchar(255) DEFAULT NULL,
                  `ordem_exibicao` int(11) DEFAULT 0,
                  `ativo` tinyint(1) DEFAULT 1,
                  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            // Insert default slides
            $pdo->exec("
                INSERT INTO `carousel_slides` (`titulo`, `subtitulo`, `imagem`, `link_texto`, `link_url`, `ordem_exibicao`) VALUES
                ('Bem-vindo à OAGB', 'A Ordem dos Advogados da Guiné-Bissau', 'img/brass-scales-justice-close-up-view.jpg', 'Saiba mais', 'apresentacao-historia.php', 1),
                ('Cadastro Nacional', 'Pesquise advogados registados', 'img/close-up-scales-justice-azul.jpg', 'Pesquisar', 'pesquisa-advogados.php', 2)
            ");
            echo "✅ Tabela carousel_slides criada e populada<br>\n";
        } else {
            echo "✅ Tabela carousel_slides já existe<br>\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao criar carousel_slides: " . $e->getMessage() . "<br>\n";
    }
    
    // Add missing columns to noticias
    try {
        $pdo->exec("ALTER TABLE noticias ADD COLUMN IF NOT EXISTS destaque TINYINT(1) DEFAULT 0");
        $pdo->exec("ALTER TABLE noticias ADD COLUMN IF NOT EXISTS ativo TINYINT(1) DEFAULT 1");
        echo "✅ Campos destaque/ativo adicionados à tabela noticias<br>\n";
    } catch (Exception $e) {
        echo "ℹ️ Campos já existem em noticias<br>\n";
    }
    
    // Add missing column to agenda
    try {
        $pdo->exec("ALTER TABLE agenda ADD COLUMN IF NOT EXISTS ativo TINYINT(1) DEFAULT 1");
        echo "✅ Campo ativo adicionado à tabela agenda<br>\n";
    } catch (Exception $e) {
        echo "ℹ️ Campo já existe em agenda<br>\n";
    }
    
    // Update existing records
    $pdo->exec("UPDATE noticias SET destaque = 1, ativo = 1 WHERE id IN (SELECT * FROM (SELECT id FROM noticias ORDER BY data_publicacao DESC LIMIT 3) as tmp)");
    $pdo->exec("UPDATE agenda SET ativo = 1");
    echo "✅ Registos existentes atualizados<br>\n";
}

echo "<h3>4. Testando index.php...</h3>\n";
try {
    // Test if index.php loads without errors
    ob_start();
    include 'index.php';
    $output = ob_get_contents();
    ob_end_clean();
    
    if (strpos($output, '<!DOCTYPE html') !== false) {
        echo "✅ index.php carrega corretamente!<br>\n";
        echo "ℹ️ Página tem " . strlen($output) . " caracteres de conteúdo<br>\n";
    } else {
        echo "❌ index.php não está a gerar HTML válido<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro no index.php: " . $e->getMessage() . "<br>\n";
}

echo "<h2>Diagnóstico completo!</h2>\n";
echo "<p><a href='index.php' target='_blank'>→ Testar a página principal</a></p>\n";
?>