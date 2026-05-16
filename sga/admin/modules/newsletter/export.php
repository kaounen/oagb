<?php
require_once __DIR__ . '/../../includes/db.php';

// Fetch Subscriptions
$stmt = $pdo->query("SELECT email, nome, data_inscricao FROM newsletter_subscricoes ORDER BY data_inscricao DESC");
$list = $stmt->fetchAll();

// Generate CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="newsletter_list_oagb_'.date('Y-m-d').'.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Email', 'Nome', 'Data de Inscrição']);
foreach ($list as $row) {
    fputcsv($output, [$row['email'], $row['nome'], $row['data_inscricao']]);
}
fclose($output);
exit;
?>
