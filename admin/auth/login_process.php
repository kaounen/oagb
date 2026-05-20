<?php
// admin/auth/login_process.php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: ../login.php?error=1");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role'];
            
            // Remember Me Cookie & Session Persistence
            if (isset($_POST['remember'])) {
                // Set cookie for 30 days
                setcookie('oagb_admin_remember_user', $username, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                
                // Set session cookie parameters to last 30 days
                if (session_id() !== '') {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), session_id(), time() + (30 * 24 * 60 * 60), $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
            } else {
                // Clear cookie
                setcookie('oagb_admin_remember_user', '', time() - 3600, '/', '', false, true);
            }
            
            // Update last login
            $update = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $update->execute([$user['id']]);

            // Import LogHelper
            require_once __DIR__ . '/../includes/LogHelper.php';
            LogHelper::login($pdo);

            header("Location: ../index.php");
            exit;
        } else {
            // Login failed
            header("Location: ../login.php?error=1");
            exit;
        }
    } catch (PDOException $e) {
        // Log error and redirect
        error_log($e->getMessage());
        header("Location: ../login.php?error=1");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
