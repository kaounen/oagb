<?php
ob_start();
// admin/includes/db.php

// Reuse the existing main connection
if (!file_exists(__DIR__ . '/../../connect.php')) {
    die('Erro de configuração: connect.php não encontrado.');
}

require_once __DIR__ . '/../../connect.php';

// Ensure $pdo is available and set PDO mode
if (isset($pdo) && $pdo instanceof PDO) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Note: connect.php already sets FETCH_OBJ, but we might prefer FETCH_ASSOC for new admin
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} else {
    // Check for $conn if it was accidentally used
    if (isset($conn) && $conn instanceof PDO) {
        $pdo = $conn;
    } else {
        die('Erro de conexão: Database connection ($pdo) missing.');
    }
}

// Global path constants
if (!defined('ADMIN_PATH')) {
    $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $admin_pos = strpos($script_name, '/admin');
    if ($admin_pos !== false) {
        define('ADMIN_PATH', substr($script_name, 0, $admin_pos + 6));
    } else {
        define('ADMIN_PATH', '/admin');
    }
}
if (!defined('ROOT_URL')) define('ROOT_URL', str_replace('/admin', '', ADMIN_PATH));
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', ROOT_URL . '/uploads/');
?>
