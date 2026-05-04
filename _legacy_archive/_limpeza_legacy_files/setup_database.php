<?php
// Ativar relatório de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Verificação da Estrutura da Base de Dados</h1>";

// Configuração da base de dados
$servername = "localhost";
$username = "korakund_advogados";
$password = "GV@R4ra&rI{4";
$dbname = "korakund_ordem";

try {
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    
    if ($mysqli->connect_error) {
        throw new Exception("Falha na conexão: " . $mysqli->connect_error);
    }
    
    $mysqli->set_charset("utf8mb4");
    echo "<p style='color: green;'>✓ Conectado à base de dados '$dbname'</p>";
    
    // Verificar se as tabelas principais existem
    $tabelas_necessarias = [
        'advogados' => "CREATE TABLE IF NOT EXISTS advogados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_registo VARCHAR(20) UNIQUE NOT NULL,
            nome_completo VARCHAR(200) NOT NULL,
            genero ENUM('M', 'F') NOT NULL,
            data_nascimento DATE,
            nacionalidade VARCHAR(50) DEFAULT 'Guineense',
            bi_passaporte VARCHAR(50),
            regiao VARCHAR(50) NOT NULL,
            localidade VARCHAR(100),
            morada TEXT,
            telefone VARCHAR(20),
            email VARCHAR(100),
            status ENUM('ativo', 'suspenso', 'inativo') DEFAULT 'ativo',
            data_inscricao DATE NOT NULL,
            observacoes TEXT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_regiao (regiao),
            INDEX idx_nome (nome_completo),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'advogados_estagiarios' => "CREATE TABLE IF NOT EXISTS advogados_estagiarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_registo VARCHAR(20) UNIQUE NOT NULL,
            nome_completo VARCHAR(200) NOT NULL,
            genero ENUM('M', 'F') NOT NULL,
            data_nascimento DATE,
            nacionalidade VARCHAR(50) DEFAULT 'Guineense',
            bi_passaporte VARCHAR(50),
            regiao VARCHAR(50) NOT NULL,
            localidade VARCHAR(100),
            morada TEXT,
            telefone VARCHAR(20),
            email VARCHAR(100),
            orientador_id INT,
            data_inicio_estagio DATE NOT NULL,
            data_fim_estagio DATE,
            status ENUM('ativo', 'concluido', 'cancelado') DEFAULT 'ativo',
            observacoes TEXT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_regiao (regiao),
            INDEX idx_nome (nome_completo),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'noticias' => "CREATE TABLE IF NOT EXISTS noticias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(300) NOT NULL,
            slug VARCHAR(300) UNIQUE NOT NULL,
            resumo TEXT,
            conteudo LONGTEXT NOT NULL,
            imagem_destaque VARCHAR(255),
            categoria VARCHAR(100),
            tags TEXT,
            autor VARCHAR(100),
            destaque BOOLEAN DEFAULT FALSE,
            ativo BOOLEAN DEFAULT TRUE,
            visualizacoes INT DEFAULT 0,
            data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_categoria (categoria),
            INDEX idx_data (data_publicacao),
            INDEX idx_destaque (destaque),
            INDEX idx_ativo (ativo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'agenda' => "CREATE TABLE IF NOT EXISTS agenda (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(300) NOT NULL,
            descricao TEXT,
            data_evento DATETIME NOT NULL,
            local_evento VARCHAR(200),
            organizador VARCHAR(100),
            tipo_evento VARCHAR(50),
            ativo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_data (data_evento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tabelas_necessarias as $nome_tabela => $sql_create) {
        // Verificar se a tabela existe
        $result = $mysqli->query("SHOW TABLES LIKE '$nome_tabela'");
        if ($result->num_rows > 0) {
            echo "<p style='color: blue;'>✓ Tabela '$nome_tabela' já existe</p>";
            
            // Contar registos
            $count_result = $mysqli->query("SELECT COUNT(*) as total FROM $nome_tabela");
            $count = $count_result->fetch_assoc();
            echo "<p style='margin-left: 20px; color: gray;'>→ Registos: {$count['total']}</p>";
            
        } else {
            echo "<p style='color: orange;'>⚠ Tabela '$nome_tabela' não existe. A criar...</p>";
            
            if ($mysqli->query($sql_create)) {
                echo "<p style='color: green; margin-left: 20px;'>✓ Tabela '$nome_tabela' criada com sucesso!</p>";
            } else {
                echo "<p style='color: red; margin-left: 20px;'>✗ Erro ao criar tabela '$nome_tabela': " . $mysqli->error . "</p>";
            }
        }
    }
    
    // Inserir dados de exemplo se as tabelas estiverem vazias
    echo "<hr><h2>Inserção de Dados de Exemplo</h2>";
    
    // Verificar se há notícias
    $result = $mysqli->query("SELECT COUNT(*) as total FROM noticias");
    $count = $result->fetch_assoc();
    
    if ($count['total'] == 0) {
        echo "<p style='color: orange;'>⚠ Tabela noticias vazia. A inserir dados de exemplo...</p>";
        
        $noticias_exemplo = [
            [
                'titulo' => 'OAGB publica edital para formação de lista sêxtupla ao TRT-8',
                'slug' => 'oagb-publica-edital-trt8',
                'resumo' => 'O "FBE International Contract Competition" organizado pelo "Federation des Barreaux d\'Europe" (FBE) conjuntamente com a Ordem dos Advogados.',
                'conteudo' => '<p>A Ordem dos Advogados da Guiné-Bissau publicou recentemente um edital importante para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho...</p>',
                'categoria' => '3º CURSO DE FORMAÇÃO DOS ESTAGIÁRIOS',
                'autor' => 'OAGB',
                'destaque' => 1
            ],
            [
                'titulo' => 'Conferência sobre o Papel da Ordem e dos Advogados',
                'slug' => 'conferencia-papel-ordem-advogados',
                'resumo' => 'Importante conferência sobre o papel institucional da Ordem dos Advogados na administração da justiça.',
                'conteudo' => '<p>Realizou-se no passado dia uma importante conferência sobre o papel da Ordem dos Advogados e dos profissionais na administração da justiça...</p>',
                'categoria' => 'CONFERÊNCIA',
                'autor' => 'OAGB',
                'destaque' => 0
            ]
        ];
        
        foreach ($noticias_exemplo as $noticia) {
            $stmt = $mysqli->prepare("INSERT INTO noticias (titulo, slug, resumo, conteudo, categoria, autor, destaque, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssssi", $noticia['titulo'], $noticia['slug'], $noticia['resumo'], $noticia['conteudo'], $noticia['categoria'], $noticia['autor'], $noticia['destaque']);
            
            if ($stmt->execute()) {
                echo "<p style='color: green; margin-left: 20px;'>✓ Notícia inserida: " . htmlspecialchars($noticia['titulo']) . "</p>";
            } else {
                echo "<p style='color: red; margin-left: 20px;'>✗ Erro ao inserir notícia: " . $stmt->error . "</p>";
            }
        }
    } else {
        echo "<p style='color: blue;'>✓ Tabela noticias já tem dados ($count[total] registos)</p>";
    }
    
    // Verificar se há advogados
    $result = $mysqli->query("SELECT COUNT(*) as total FROM advogados");
    $count = $result->fetch_assoc();
    
    if ($count['total'] == 0) {
        echo "<p style='color: orange;'>⚠ Tabela advogados vazia. A inserir dados de exemplo...</p>";
        
        $advogados_exemplo = [
            ['001/2020', 'António Silva Santos', 'M', 'SAB', 'Bissau', '+245 966 123 456', 'antonio.santos@email.gw', '2020-01-15'],
            ['002/2020', 'Maria Fernanda Gomes', 'F', 'Bafatá', 'Bafatá', '+245 966 789 123', 'maria.gomes@email.gw', '2020-02-20'],
            ['003/2021', 'João Carlos Mendes', 'M', 'Cacheu', 'Cacheu', '+245 966 456 789', 'joao.mendes@email.gw', '2021-03-10']
        ];
        
        foreach ($advogados_exemplo as $advogado) {
            $stmt = $mysqli->prepare("INSERT INTO advogados (numero_registo, nome_completo, genero, regiao, localidade, telefone, email, data_inscricao, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ativo')");
            $stmt->bind_param("ssssssss", $advogado[0], $advogado[1], $advogado[2], $advogado[3], $advogado[4], $advogado[5], $advogado[6], $advogado[7]);
            
            if ($stmt->execute()) {
                echo "<p style='color: green; margin-left: 20px;'>✓ Advogado inserido: " . htmlspecialchars($advogado[1]) . "</p>";
            } else {
                echo "<p style='color: red; margin-left: 20px;'>✗ Erro ao inserir advogado: " . $stmt->error . "</p>";
            }
        }
    } else {
        echo "<p style='color: blue;'>✓ Tabela advogados já tem dados ($count[total] registos)</p>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p style='color: green;'><strong>✓ Verificação concluída!</strong></p>";
echo "<p><em>Agora pode testar o test_mysqli.php e depois o index.php</em></p>";
?>
