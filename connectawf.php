<?php
// Configuração da base de dados
$servername = "localhost";
$username = "korakund_advogados";
$password = "GV@R4ra&rI{4";
$dbname = "korakund_ordem";

// Configurações do site
define('SITE_URL', 'https://oagb.gw');
define('ADMIN_EMAIL', 'info@oagb.gw');

// Conectar à base de dados usando MySQLi
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($mysqli->connect_error) {
    die("Erro de conexão à base de dados: " . $mysqli->connect_error);
}

// Configurar charset
$mysqli->set_charset("utf8mb4");

// Classe simples para compatibilidade com PDO
class SimpleMySQLiWrapper {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    public function prepare($sql) {
        return new SimpleStatementWrapper($this->mysqli, $sql);
    }
    
    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    public function query($sql) {
        return $this->mysqli->query($sql);
    }
}

class SimpleStatementWrapper {
    private $mysqli;
    private $sql;
    private $params = [];
    
    public function __construct($mysqli, $sql) {
        $this->mysqli = $mysqli;
        $this->sql = $sql;
    }
    
    public function execute($params = []) {
        $this->params = $params;
        
        // Construir SQL final substituindo ? pelos parâmetros
        $sql_final = $this->sql;
        
        if (!empty($params)) {
            foreach ($params as $param) {
                // Escapar o parâmetro
                $escaped_param = "'" . $this->mysqli->real_escape_string($param) . "'";
                // Substituir o primeiro ? encontrado
                $sql_final = preg_replace('/\?/', $escaped_param, $sql_final, 1);
            }
        }
        
        $this->result = $this->mysqli->query($sql_final);
        
        if (!$this->result) {
            throw new Exception("Erro na consulta: " . $this->mysqli->error . " | SQL: " . $sql_final);
        }
        
        return true;
    }
    
    public function fetchAll() {
        if (!isset($this->result)) {
            throw new Exception("Query não foi executada");
        }
        
        if ($this->result === true) {
            // Para INSERT, UPDATE, DELETE
            return [];
        }
        
        $rows = [];
        while ($row = $this->result->fetch_assoc()) {
            $rows[] = (object) $row;
        }
        
        return $rows;
    }
    
    public function fetch() {
        if (!isset($this->result)) {
            throw new Exception("Query não foi executada");
        }
        
        if ($this->result === true) {
            // Para INSERT, UPDATE, DELETE
            return false;
        }
        
        $row = $this->result->fetch_assoc();
        return $row ? (object) $row : false;
    }
    
    public function rowCount() {
        return $this->mysqli->affected_rows;
    }
}

// Criar wrapper
$pdo = new SimpleMySQLiWrapper($mysqli);

// Função para sanitizar dados
function sanitize($data) {
    global $mysqli;
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Função para escapar strings para SQL
function escape_string($data) {
    global $mysqli;
    return $mysqli->real_escape_string($data);
}

// Função para formatar datas
function format_date($date, $format = 'd/m/Y') {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    return date($format, $timestamp);
}

// Função para formatar data e hora
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    return format_date($datetime, $format);
}

// Função para truncar texto
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

// Regiões da Guiné-Bissau
$regioes_gb = [
    'SAB' => 'Setor Autónomo de Bissau',
    'Bafatá' => 'Bafatá',
    'Biombo' => 'Biombo',
    'Bolama' => 'Bolama-Bijagós',
    'Cacheu' => 'Cacheu',
    'Gabú' => 'Gabú',
    'Oio' => 'Oio',
    'Quinara' => 'Quinara',
    'Tombali' => 'Tombali'
];

// Áreas jurídicas
$areas_juridicas = [
    'civil' => 'Direito Civil',
    'criminal' => 'Direito Criminal',
    'comercial' => 'Direito Comercial',
    'trabalho' => 'Direito do Trabalho',
    'administrativo' => 'Direito Administrativo',
    'constitucional' => 'Direito Constitucional',
    'internacional' => 'Direito Internacional',
    'familia' => 'Direito da Família',
    'propriedade' => 'Direito de Propriedade',
    'tributario' => 'Direito Tributário',
    'ambiental' => 'Direito Ambiental',
    'outro' => 'Outro'
];

// Configurar fuso horário
date_default_timezone_set('Africa/Bissau');

// Configurar localização para português
if (function_exists('setlocale')) {
    setlocale(LC_TIME, 'pt_PT.UTF-8', 'pt_PT', 'portuguese');
}

// Headers de segurança básicos
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
}
?>
