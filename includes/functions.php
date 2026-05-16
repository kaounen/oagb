<?php
/**
 * Ficheiro de funções auxiliares do site OAGB
 * includes/functions.php
 */

// Configurar timezone
date_default_timezone_set('Africa/Bissau');

// Configurar locale para português
setlocale(LC_TIME, 'pt_PT.UTF-8', 'pt_PT', 'portuguese');

/**
 * Limpar e validar input do utilizador
 */
function clean_input($data) {
    if (is_null($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Truncar texto mantendo palavras inteiras
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) return $text;
    
    $truncated = substr($text, 0, $length);
    $last_space = strrpos($truncated, ' ');
    
    if ($last_space !== false) {
        $truncated = substr($truncated, 0, $last_space);
    }
    
    return $truncated . $suffix;
}

/**
 * Formatar data para português
 */
function format_date_pt($date, $include_time = false) {
    if (empty($date)) return '';
    
    $timestamp = is_string($date) ? strtotime($date) : $date;
    
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    
    $dia = date('d', $timestamp);
    $mes = $meses[intval(date('n', $timestamp))];
    $ano = date('Y', $timestamp);
    
    $data_formatada = "{$dia} de {$mes} de {$ano}";
    
    if ($include_time) {
        $hora = date('H:i', $timestamp);
        $data_formatada .= " às {$hora}";
    }
    
    return $data_formatada;
}

/**
 * Formatar data e hora
 */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    if (!$datetime) return '';
    try {
        return date($format, strtotime($datetime));
    } catch (Exception $e) {
        return $datetime;
    }
}

/**
 * Formatar data simples
 */
function format_date($date, $format = 'd/m/Y') {
    if (!$date) return '';
    try {
        return date($format, strtotime($date));
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Gerar slug a partir de texto
 */
function generate_slug($text) {
    // Substituir caracteres especiais portugueses
    $text = str_replace(
        ['á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'õ', 'ô', 'ú', 'ç'],
        ['a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'c'],
        mb_strtolower($text, 'UTF-8')
    );
    
    // Remover caracteres especiais
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Substituir espaços por hífens
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Remover hífens no início e fim
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Verificar se é uma URL válida
 */
function is_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Verificar se é um email válido
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Obter IP do visitante
 */
function get_visitor_ip() {
    $ip = '';
    
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Validar IP
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '0.0.0.0';
    }
    
    return $ip;
}

/**
 * Formatar número de telefone
 */
function format_phone($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Formato Guiné-Bissau
    if (preg_match('/^245/', $phone)) {
        return '+245 ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 3) . ' ' . substr($phone, 9);
    }
    
    return $phone;
}

/**
 * Validar número de telefone da Guiné-Bissau
 */
function is_valid_gb_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Números da Guiné-Bissau começam com 245 e têm 9 dígitos após o código do país
    return preg_match('/^245[0-9]{9}$/', $phone);
}

/**
 * Gerar token aleatório
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Encriptar password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Enviar email
 */
function send_email($to, $subject, $message, $headers = []) {
    // Configurações padrão
    $default_headers = [
        'From' => 'noreply@oagb.gw',
        'Reply-To' => 'info@oagb.gw',
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    
    $headers = array_merge($default_headers, $headers);
    
    // Converter array de headers para string
    $header_string = '';
    foreach ($headers as $key => $value) {
        $header_string .= "$key: $value\r\n";
    }
    
    // Template de email
    $html_message = '
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #c18046; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { background: #333; color: white; padding: 10px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Ordem dos Advogados da Guiné-Bissau</h2>
            </div>
            <div class="content">
                ' . $message . '
            </div>
            <div class="footer">
                © ' . date('Y') . ' OAGB - Todos os direitos reservados
            </div>
        </div>
    </body>
    </html>';
    
    return mail($to, $subject, $html_message, $header_string);
}

/**
 * Registar log de atividade
 */
function log_activity($pdo, $action, $description = null, $table = null, $record_id = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs_atividade 
            (usuario_id, usuario_nome, acao, descricao, tabela_afetada, registro_id, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $user_id = $_SESSION['user_id'] ?? null;
        $user_name = $_SESSION['user_name'] ?? 'Sistema';
        $ip = get_visitor_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->execute([
            $user_id,
            $user_name,
            $action,
            $description,
            $table,
            $record_id,
            $ip,
            $user_agent
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log("Erro ao registar log: " . $e->getMessage());
        return false;
    }
}

/**
 * Formatar tamanho de ficheiro
 */
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    
    return $bytes;
}

/**
 * Upload de ficheiro
 */
function upload_file($file, $destination_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    $response = ['success' => false, 'message' => '', 'filename' => ''];
    
    // Verificar erros
    if ($file['error'] != 0) {
        $response['message'] = 'Erro no upload do ficheiro.';
        return $response;
    }
    
    // Verificar tamanho (máximo 10MB)
    if ($file['size'] > 10485760) {
        $response['message'] = 'Ficheiro muito grande. Máximo 10MB.';
        return $response;
    }
    
    // Verificar tipo
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        $response['message'] = 'Tipo de ficheiro não permitido.';
        return $response;
    }
    
    // Gerar nome único
    $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $destination = $destination_dir . '/' . $new_filename;
    
    // Criar diretório se não existir
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    // Mover ficheiro
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $response['success'] = true;
        $response['filename'] = $new_filename;
        $response['message'] = 'Ficheiro enviado com sucesso.';
    } else {
        $response['message'] = 'Erro ao mover ficheiro.';
    }
    
    return $response;
}

/**
 * Redimensionar imagem
 */
function resize_image($source, $destination, $width, $height = null) {
    list($source_width, $source_height, $source_type) = getimagesize($source);
    
    // Calcular altura proporcional se não especificada
    if ($height === null) {
        $height = floor($source_height * ($width / $source_width));
    }
    
    // Criar imagem temporária
    $tmp = imagecreatetruecolor($width, $height);
    
    // Carregar imagem original
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            $source_img = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $source_img = imagecreatefrompng($source);
            // Preservar transparência
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            break;
        case IMAGETYPE_GIF:
            $source_img = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    // Redimensionar
    imagecopyresampled(
        $tmp, $source_img,
        0, 0, 0, 0,
        $width, $height,
        $source_width, $source_height
    );
    
    // Guardar imagem
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($tmp, $destination, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($tmp, $destination, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($tmp, $destination);
            break;
    }
    
    // Limpar memória
    imagedestroy($tmp);
    imagedestroy($source_img);
    
    return true;
}

/**
 * Obter configuração do site
 */
function get_config($pdo, $key, $default = '') {
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
 * Atualizar configuração do site
 */
function update_config($pdo, $key, $value, $description = null, $group = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO configuracoes_site (chave, valor, descricao, grupo) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            valor = VALUES(valor),
            descricao = COALESCE(VALUES(descricao), descricao),
            grupo = COALESCE(VALUES(grupo), grupo)
        ");
        return $stmt->execute([$key, $value, $description, $group]);
    } catch (Exception $e) {
        error_log("Erro ao atualizar configuração: " . $e->getMessage());
        return false;
    }
}

/**
 * Verificar utilizador autenticado
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar administrador
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirecionar com mensagem
 */
function redirect($url, $message = null, $type = 'info') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Mostrar mensagem flash
 */
function show_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        $class_map = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $class_map[$type] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Sanitizar output HTML
 */
function safe_html($html) {
    $allowed_tags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><table><thead><tbody><tr><td><th>';
    return strip_tags($html, $allowed_tags);
}

/**
 * Calcular tempo decorrido
 */
function time_elapsed($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
    if ($diff->m > 0) return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
    if ($diff->d > 0) return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
    if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
    if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
    return 'Agora mesmo';
}

if (!function_exists('oagb_resolve_media_path')) {
    function oagb_resolve_media_path($rawPath, $defaultPath) {
        if (empty($rawPath)) return $defaultPath;
        $normalized = str_replace('\\', '/', trim((string) $rawPath));
        $normalized = preg_replace('#\.\.+#', '', $normalized);
        if ($normalized === '') return $defaultPath;
        if (preg_match('#^https?://#i', $normalized)) return $normalized;
        if ($normalized[0] === '/') $normalized = ltrim($normalized, '/');
        if (strpos($normalized, 'uploads/') === 0 || strpos($normalized, 'img/') === 0) return $normalized;
        return 'uploads/' . $normalized;
    }
}

if (!function_exists('oagb_fix_encoding')) {
    /**
     * Corrige problemas de codificação de caracteres legados da base de dados.
     */
    function oagb_fix_encoding($text) {
        if (empty($text)) return '';
        $map = [
            'þÒ' => 'ção', 'þ' => 'ç', 'Ò' => 'ã', 'Õ' => 'í', 'Ú' => 'é', 'Ó' => 'ê',
            '¡' => 'á', 'à' => 'à', 'È' => 'è', '¿' => 'õ', 'À' => 'à', 'Û' => 'â',
            'ç' => 'ç', 'Ã' => 'ã'
        ];
        $fixed_text = str_replace(array_keys($map), array_values($map), $text);
        if ($fixed_text !== $text && mb_check_encoding($fixed_text, 'UTF-8')) {
             return $fixed_text;
        }
        if (!mb_check_encoding($fixed_text, 'UTF-8')) {
            return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }
        return $fixed_text;
    }
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