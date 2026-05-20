<?php
/**
 * processar_pagamento_local.php
 * Handles simulated card and mobile money payments for inscriptions.
 * Resilient to regional constraints in Guinea-Bissau.
 * Saves audited transactions to the database.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/connect.php';

function json_err(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);

$inscricao_id   = intval($_POST['inscricao_id'] ?? 0);
$tipo_inscricao = $_POST['tipo_inscricao'] ?? 'advogado';
$metodo         = $_POST['metodo'] ?? ''; // cartao, orange_money, mtn_momo
$telefone       = $_POST['telefone'] ?? '';
$cartao_numero  = $_POST['cartao_numero'] ?? '';

if ($inscricao_id <= 0) json_err('ID de inscrição inválido.');
if (!in_array($metodo, ['cartao', 'orange_money', 'mtn_momo'])) json_err('Método de pagamento inválido.');

// Load payment values
try {
    $configs = $pdo->query("SELECT chave, valor FROM finan_config WHERE chave IN (
        'joia_inscricao_advogado','joia_inscricao_estagiario','pagamento_moeda'
    )")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    json_err('Erro de base de dados: ' . $e->getMessage(), 500);
}

$valor = $tipo_inscricao === 'estagiario'
    ? ($configs['joia_inscricao_estagiario'] ?? 25000)
    : ($configs['joia_inscricao_advogado']   ?? 50000);

$moeda = $configs['pagamento_moeda'] ?? 'CFA';

// Generate simulated reference
$ref = 'TX_' . strtoupper($metodo) . '_' . time() . '_' . rand(100, 999);

$payload = [
    'metodo'        => $metodo,
    'telefone'      => $telefone,
    'cartao_masked' => !empty($cartao_numero) ? '**** **** **** ' . substr($cartao_numero, -4) : null,
    'data'          => date('Y-m-d H:i:s'),
    'status'        => 'success',
    'simulado'      => true
];

try {
    $pdo->beginTransaction();

    // 1. Insert into finan_pagamentos_inscricao
    $stmt = $pdo->prepare("INSERT INTO finan_pagamentos_inscricao 
        (inscricao_id, stripe_intent_id, metodo, valor, moeda, status, payload_json) 
        VALUES (?, ?, ?, ?, ?, 'pago', ?)");
    $stmt->execute([
        $inscricao_id,
        $ref,
        $metodo,
        $valor,
        $moeda,
        json_encode($payload)
    ]);

    // 2. Update status in inscricoes_ordem
    $upd = $pdo->prepare("UPDATE inscricoes_ordem SET 
        pagamento_status = 'pago',
        pagamento_referencia = ?,
        pagamento_metodo = ?,
        pagamento_valor = ?,
        pagamento_data = NOW()
        WHERE id = ?");
    $upd->execute([$ref, $metodo, $valor, $inscricao_id]);

    $pdo->commit();

    echo json_encode([
        'success'      => true,
        'referencia'   => $ref,
        'valor'        => $valor,
        'moeda'        => $moeda,
        'formatted'    => number_format((float)$valor, 0, ',', '.') . ' ' . $moeda,
        'metodo_label' => match($metodo) {
            'cartao'       => 'Cartão de Crédito/Débito',
            'orange_money' => 'Orange Money',
            'mtn_momo'     => 'MTN Mobile Money'
        }
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    json_err('Erro ao registar pagamento: ' . $e->getMessage(), 500);
}
?>
