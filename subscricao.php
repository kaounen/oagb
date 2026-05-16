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
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    // Validar email
    if (empty($email)) {
        throw new Exception('O email é obrigatório');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }
    
    // Verificar se já existe subscrição para este email
    $stmt = $pdo->prepare("SELECT id, ativo FROM newsletter_subscricoes WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        if ($existing['ativo']) {
            echo json_encode([
                'status' => 'info', 
                'message' => 'Este email já está subscrito na nossa newsletter'
            ]);
            exit;
        } else {
            // Reativar subscrição existente e já marcar como confirmado
            $stmt = $pdo->prepare("UPDATE newsletter_subscricoes SET ativo = 1, confirmado = 1, data_inscricao = NOW() WHERE id = ?");
            $stmt->execute([$existing['id']]);
            
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
    
    // Inserir nova subscrição - Marcando como confirmado=1 para visibilidade imediata no admin (sistema simplificado)
    $stmt = $pdo->prepare("
        INSERT INTO newsletter_subscricoes (email, nome, token_confirmacao, ip_inscricao, ativo, confirmado) 
        VALUES (?, ?, ?, ?, 1, 1)
    ");
    
    $success = $stmt->execute([$email, $nome, $token, $ip]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Subscrição realizada com sucesso! Obrigado por se juntar à nossa newsletter.'
        ]);
    } else {
        throw new Exception('Erro ao processar subscrição');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
