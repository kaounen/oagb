<?php
/**
 * Processamento de subscrições da newsletter
 */

require_once 'connect.php';

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

try {
    // Obter dados do formulário
    $email = clean_input($_POST['email'] ?? '');
    $nome = clean_input($_POST['nome'] ?? '');
    
    // Validar email
    if (empty($email)) {
        throw new Exception('Email é obrigatório');
    }
    
    if (!is_valid_email($email)) {
        throw new Exception('Email inválido');
    }
    
    // Verificar se já existe subscrição para este email
    $stmt = $pdo->prepare("SELECT id, ativo FROM newsletter_subscricoes WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        if ($existing->ativo) {
            echo json_encode([
                'status' => 'info', 
                'message' => 'Este email já está subscrito na nossa newsletter'
            ]);
            exit;
        } else {
            // Reativar subscrição existente
            $stmt = $pdo->prepare("UPDATE newsletter_subscricoes SET ativo = 1, data_inscricao = NOW() WHERE id = ?");
            $stmt->execute([$existing->id]);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Subscrição reativada com sucesso!'
            ]);
            exit;
        }
    }
    
    // Gerar token de confirmação
    $token = bin2hex(random_bytes(32));
    
    // Obter IP do visitante
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Inserir nova subscrição
    $stmt = $pdo->prepare("
        INSERT INTO newsletter_subscricoes (email, nome, token_confirmacao, ip_inscricao) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([$email, $nome, $token, $ip]);
    
    if ($success) {
        // Opcional: Enviar email de confirmação
        // send_confirmation_email($email, $nome, $token);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Subscrição realizada com sucesso! Obrigado por se juntar à nossa newsletter.'
        ]);
    } else {
        throw new Exception('Erro ao processar subscrição');
    }
    
} catch (Exception $e) {
    error_log("Erro na subscrição newsletter: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

/**
 * Função para enviar email de confirmação (opcional)
 */
function send_confirmation_email($email, $nome, $token) {
    // Implementar envio de email de confirmação
    // Pode usar PHPMailer, SwiftMailer ou função mail() do PHP
    
    $confirmation_url = SITE_URL . "/confirmar-subscricao.php?token=" . $token;
    
    $subject = "Confirme a sua subscrição - OAGB Newsletter";
    $message = "
    <html>
    <body>
        <h2>Bem-vindo à Newsletter da OAGB!</h2>
        <p>Olá " . htmlspecialchars($nome ?: 'Subscritor') . ",</p>
        <p>Obrigado por se subscrever à nossa newsletter. Para confirmar a sua subscrição, clique no link abaixo:</p>
        <p><a href='{$confirmation_url}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Confirmar Subscrição</a></p>
        <p>Se não solicitou esta subscrição, pode ignorar este email.</p>
        <hr>
        <p><small>Ordem dos Advogados da Guiné-Bissau<br>
        Rua 15, Bissau, Guiné-Bissau<br>
        +245 955 475 889</small></p>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($email, $subject, $message, implode("\r\n", $headers));
}
