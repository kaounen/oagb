<?php
/**
 * Configuração de conexão com a base de dados
 * Site da Ordem dos Advogados da Guiné-Bissau
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações da base de dados
$host = 'localhost';
$dbname = 'korakund_ordem';
$username = 'korakund_advogados';
$password = 'GV@R4ra&rI{4';

// Configurações para desenvolvimento local (comente quando em produção)
/*
$host = 'localhost';
$dbname = 'oagb_db';
$username = 'root';
$password = '';
*/

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Definir timezone da base de dados
    $pdo->exec("SET time_zone = '+00:00'");
    
} catch (PDOException $e) {
    // Registrar erro no log
    error_log('Erro de conexão BD OAGB: ' . $e->getMessage());
    
    // Verificar se estamos em ambiente de desenvolvimento
    $is_dev = (isset($_SERVER['HTTP_HOST']) && 
               (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
                strpos($_SERVER['HTTP_HOST'], '.test') !== false));
    
    if ($is_dev) {
        // Em desenvolvimento, mostrar detalhes do erro
        die('<h3>Erro de Conexão com a Base de Dados</h3>' .
            '<p><strong>Erro:</strong> ' . $e->getMessage() . '</p>' .
            '<p><strong>Base de dados:</strong> ' . $dbname . '</p>' .
            '<p><strong>Utilizador:</strong> ' . $username . '</p>' .
            '<p><strong>Host:</strong> ' . $host . '</p>' .
            '<p>Verifique se a base de dados existe e as credenciais estão corretas.</p>');
    } else {
        // Em produção, mensagem genérica
        die('Erro interno do servidor. Tente novamente mais tarde.');
    }
}

// Configurações globais do site
define('SITE_URL', 'https://oagb.gw');
define('SITE_NAME', 'Ordem dos Advogados da Guiné-Bissau');
define('UPLOADS_DIR', 'uploads');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// Configurações de email (ajustar conforme necessário)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'info@oagb.gw');
define('SMTP_PASSWORD', 'sua_senha_email');
define('FROM_EMAIL', 'info@oagb.gw');
define('FROM_NAME', 'OAGB - Ordem dos Advogados da Guiné-Bissau');

/**
 * Função para obter configuração do site
 */
function get_site_config($key, $default = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT valor FROM configuracoes_site WHERE chave = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result->valor : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Função para definir configuração do site
 */
function set_site_config($key, $value, $description = null, $group = 'geral') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO configuracoes_site (chave, valor, descricao, grupo) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            valor = VALUES(valor), 
            descricao = VALUES(descricao), 
            grupo = VALUES(grupo)
        ");
        
        return $stmt->execute([$key, $value, $description, $group]);
    } catch (Exception $e) {
        error_log("Erro ao definir configuração: " . $e->getMessage());
        return false;
    }
}

// Configurar cabeçalhos de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Configurar encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>