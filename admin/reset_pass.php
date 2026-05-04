<?php
require_once 'connect.php';

// Configure the user and desired password
$username = 'admin@oagb.gw'; // Adivinhado, vamos ver
$nova_password = 'admin'; // A senha temporária pretendida

$hash = password_hash($nova_password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        $update = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $update->execute([$hash, $username]);
        echo "Sucesso! A palavra-passe para $username foi alterada para '$nova_password'. Pode entrar agora.";
    } else {
        // Find the first admin user
        $stmt = $pdo->query("SELECT username FROM admin_users LIMIT 1");
        $user = $stmt->fetch();
        if ($user) {
            $username = $user['username'];
            $update = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $update->execute([$hash, $username]);
            echo "Aviso: O utilizador 'admin@oagb.gw' não foi encontrado, mas a palavra-passe do utilizador principal '$username' foi alterada para '$nova_password'. Pode entrar agora.";
        } else {
            echo "Erro: Não existem utilizadores na tabela admin_users.";
        }
    }
} catch (Exception $e) {
    echo "Erro na base de dados: " . $e->getMessage();
}
