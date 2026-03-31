<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        header("Location: reset_password.php?token=$token&error=mismatch");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: reset_password.php?token=$token&error=short");
        exit;
    }

    try {
        // Verify token again
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if ($reset) {
            $email = $reset['email'];
            $new_password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Update password in admin_users
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE email = ?");
            $stmt->execute([$new_password_hash, $email]);

            // Delete used tokens for this email
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);

            // Success redirect to login
            header("Location: ../login.php?reset_success=1");
            exit;
        } else {
            header("Location: reset_password.php?error=expired");
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: reset_password.php?token=$token&error=db");
        exit;
    }
}
header("Location: ../login.php");
exit;
