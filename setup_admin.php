<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/admin/includes/db.php';

$username = 'admin@oagb.gw';
// Password hashed for production use
$passwd = 'admin123';
$password_hashed = password_hash($passwd, PASSWORD_DEFAULT);
$full_name = 'Administrador OAGB';
$role = 'superadmin';

try {
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, role) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute([$username, $password_hashed, $full_name, $role, $password_hashed]);

    echo "Sucesso! Administrador pré-configurado.\n";
    echo "Login: $username\n";
    echo "Senha: $passwd\n";
} catch (PDOException $e) {
    die("Erro ao inserir admin: " . $e->getMessage());
}
?>
