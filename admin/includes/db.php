<?php
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
if (!defined('ADMIN_PATH')) define('ADMIN_PATH', '/oagb/admin');
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', '/oagb/gestao/assets/uploads/files/');
?>
