<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION['lawyer_id'])) { echo json_encode(['status' => 'error']); exit; }
require_once __DIR__ . '/../connect.php';

$bill_id = $_GET['bill'] ?? 0;
$stmt = $pdo->prepare("SELECT status FROM finan_pagamentos WHERE id = ? AND advogado_id = ?");
$stmt->execute([$bill_id, $_SESSION['lawyer_id']]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bill) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

// SIMULATION LOGIC: If the user has been on the USSD page for more than 15 seconds, 
// we "auto-confirm" it for this demo.
// In reality, this would only return the current status from the DB updated by a webhook.
if ($bill['status'] == 'pendente') {
    // Check if we should auto-confirm for the demo
    $stmt_time = $pdo->prepare("SELECT data_pagamento FROM finan_pagamentos WHERE id = ?");
    $stmt_time->execute([$bill_id]);
    $created_at = strtotime($stmt_time->fetchColumn());
    
    if (time() - $created_at > 12) { // 12 seconds wait for demo
        $stmt_upd = $pdo->prepare("UPDATE finan_pagamentos SET status = 'confirmado', data_pagamento = NOW(), valid_until = DATE_ADD(NOW(), INTERVAL meses_pagos MONTH) WHERE id = ?");
        $stmt_upd->execute([$bill_id]);
        $bill['status'] = 'confirmado';
    }
}

echo json_encode(['status' => $bill['status']]);
