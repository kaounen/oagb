<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: forgot_password.php?error=invalid");
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, full_name FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate Token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save Token
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        // Construct Link (Dynamic)
        $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . ADMIN_PATH . "/auth/reset_password.php?token=" . $token;

        // "Send" Email
        $subject = "Recuperação de Acesso - OAGB Administrative Backend";
        $message = "Olá " . $user['full_name'] . ",\n\n";
        $message .= "Recebemos um pedido de recuperação de palavra-passe para a sua conta.\n";
        $message .= "Clique no link abaixo para definir uma nova palavra-passe (válido por 1 hora):\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "Se não solicitou esta alteração, ignore este e-mail.\n\n";
        $message .= "Equipa de Gestão OAGB";

        $headers = "From: " . FROM_EMAIL . "\r\n";
        $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Log the link for debug/demo (since we don't have SMTP)
        $log_entry = "[" . date('Y-m-d H:i:s') . "] Password Reset for $email: $reset_link\n";
        file_put_contents(__DIR__ . '/../../logs/password_resets.log', $log_entry, FILE_APPEND);

        // Attempt real mail sending if configured? 
        // @mail($email, $subject, $message, $headers);
    }

    // Always redirect to success to prevent user enumeration
    header("Location: forgot_password.php?sent=1");
    exit;
}
header("Location: forgot_password.php");
exit;
