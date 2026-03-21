<?php
require_once 'connect.php';

header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

try {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    // Validar email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Email inválido']);
        exit;
    }
    
    // Verificar se já existe
    $stmt = $pdo->prepare("SELECT id FROM subscricoes WHERE email = ?");
    $stmt->execute([$email]);
    $existe = $stmt->fetch();
    
    if ($existe) {
        echo json_encode(['status' => 'info', 'message' => 'Este email já está subscrito na nossa newsletter']);
        exit;
    }
    
    // Inserir nova subscrição
    $stmt = $pdo->prepare("INSERT INTO subscricoes (email, nome, ativo) VALUES (?, ?, 1)");
    $stmt->execute([$email, $nome]);
    
    // Enviar email de confirmação (opcional)
    $subject = "Confirmação de Subscrição - OAGB Newsletter";
    $message = "
        <h3>Bem-vindo(a) à Newsletter da OAGB!</h3>
        <p>Obrigado por subscrever a nossa newsletter.</p>
        <p>Receberá regularmente informações sobre:</p>
        <ul>
            <li>Últimas notícias da Ordem dos Advogados</li>
            <li>Eventos e formações</li>
            <li>Comunicados importantes</li>
            <li>Alterações legislativas relevantes</li>
        </ul>
        <p>Se não deseja mais receber estes emails, pode cancelar a subscrição a qualquer momento.</p>
        <hr>
        <p><small>Ordem dos Advogados da Guiné-Bissau<br>
        Rua 15, Bissau<br>
        Tel: +245 955 475 889<br>
        Email: info@oagb.gw</small></p>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@oagb.gw" . "\r\n";
    
    // Tentar enviar email (não crítico se falhar)
    @mail($email, $subject, $message, $headers);
    
    echo json_encode(['status' => 'success', 'message' => 'Subscrição realizada com sucesso! Obrigado.']);
    
} catch (Exception $e) {
    error_log("Erro na subscrição: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erro interno. Tente novamente.']);
}