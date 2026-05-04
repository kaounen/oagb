<?php
/**
 * Funções auxiliares para o site da OAGB
 */

/**
 * Formatar data para exibição em português
 */
function format_date_pt($date) {
    if (empty($date)) return '';
    
    $meses = [
        'January' => 'Janeiro',
        'February' => 'Fevereiro', 
        'March' => 'Março',
        'April' => 'Abril',
        'May' => 'Maio',
        'June' => 'Junho',
        'July' => 'Julho',
        'August' => 'Agosto',
        'September' => 'Setembro',
        'October' => 'Outubro',
        'November' => 'Novembro',
        'December' => 'Dezembro'
    ];
    
    try {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        $formatted = date('d \d\e F \d\e Y', $timestamp);
        
        // Substituir nome do mês em inglês para português
        foreach ($meses as $en => $pt) {
            $formatted = str_replace($en, $pt, $formatted);
        }
        
        return $formatted;
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formatar data para exibição
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    
    try {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formatar data e hora para exibição
 */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    return format_date($datetime, $format);
}

/**
 * Truncar texto mantendo palavras completas
 */
function truncate_text($text, $length = 150, $suffix = '...') {
    if (empty($text)) return '';
    
    $text = strip_tags($text);
    
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $truncated = substr($text, 0, $length);
    $last_space = strrpos($truncated, ' ');
    
    if ($last_space !== false) {
        $truncated = substr($truncated, 0, $last_space);
    }
    
    return $truncated . $suffix;
}

/**
 * Gerar slug a partir de string
 */
function generate_slug($string) {
    // Remover acentos e caracteres especiais
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    
    // Converter para minúsculas
    $slug = strtolower($slug);
    
    // Remover caracteres não alfanuméricos exceto hífens
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
    
    // Remover hífens múltiplos
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Remover hífens do início e fim
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Verificar se uma URL é absoluta
 */
function is_absolute_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Converter URL relativa em absoluta
 */
function make_absolute_url($url, $base_url = 'https://oagb.gw') {
    if (is_absolute_url($url)) {
        return $url;
    }
    
    return rtrim($base_url, '/') . '/' . ltrim($url, '/');
}

/**
 * Formatar número de telefone
 */
function format_phone($phone) {
    if (empty($phone)) return '';
    
    // Remover caracteres não numéricos exceto +
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Verificar se começa com +245 ou 245 (compatível PHP 7.4+)
    if (strpos($phone, '+245') !== 0 && strpos($phone, '245') !== 0) {
        if (strlen($phone) == 7) {
            $phone = '+245 ' . $phone;
        }
    }
    
    return $phone;
}

/**
 * Verificar se email é válido
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Escapar HTML de forma segura
 */
function safe_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Limpar entrada de dados
 */
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Formatar tamanho de arquivo
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Gerar breadcrumbs
 */
function generate_breadcrumbs($pages) {
    if (empty($pages)) return '';
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($pages);
    $current = 1;
    
    foreach ($pages as $page) {
        $is_last = ($current == $total);
        
        if ($is_last) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . safe_html($page['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . safe_html($page['url']) . '">' . safe_html($page['title']) . '</a></li>';
        }
        
        $current++;
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * Verificar se usuário está logado (para área administrativa)
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect com mensagem
 */
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit;
}

/**
 * Exibir mensagem flash
 */
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        
        // Compatível com PHP 7.4+
        switch($type) {
            case 'error':
                $class = 'alert-danger';
                break;
            case 'warning':
                $class = 'alert-warning';
                break;
            case 'info':
                $class = 'alert-info';
                break;
            default:
                $class = 'alert-success';
                break;
        }
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo safe_html($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}

/**
 * Validar token CSRF
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Gerar token CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Upload de arquivo com validação
 */
function upload_file($file, $upload_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], $max_size = 5242880) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Parâmetros de upload inválidos.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('Nenhum arquivo foi enviado.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('Arquivo muito grande.');
        default:
            throw new Exception('Erro desconhecido no upload.');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('Arquivo excede o tamanho máximo permitido.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowed_types)) {
        throw new Exception('Tipo de arquivo não permitido.');
    }

    $filename = sprintf('%s.%s', uniqid(), $extension);
    $destination = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Falha ao mover o arquivo.');
    }

    return $filename;
}

/**
 * Configurar configurações regionais
 */
function setup_locale() {
    // Configurar timezone
    date_default_timezone_set('Africa/Bissau');
    
    // Configurar locale para português
    setlocale(LC_TIME, 'pt_PT.UTF-8', 'pt_PT', 'portuguese');
    setlocale(LC_MONETARY, 'pt_PT.UTF-8', 'pt_PT', 'portuguese');
}

// Inicializar configurações regionais
setup_locale();
?>