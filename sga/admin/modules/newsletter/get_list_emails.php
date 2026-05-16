<?php
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

$target = $_GET['target'] ?? '';
$results = [];

if ($target === 'lawyers') {
    $stmt = $pdo->query("SELECT nome_completo as name, email, 'Advogado' as type FROM advogados WHERE status = 'ativo' AND email != ''");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($target === 'interns') {
    $stmt = $pdo->query("SELECT nome_completo as name, email, 'Estagiário' as type FROM advogados_estagiarios WHERE status = 'ativo' AND email != ''");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($target === 'subs') {
    $stmt = $pdo->query("SELECT nome as name, email, 'Subscritor' as type FROM newsletter_subscricoes WHERE ativo = 1 AND confirmado = 1 AND email != ''");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($target === 'ordem') {
    // Fetch Bastonarios
    $stmt = $pdo->query("SELECT nome_completo as name, email_contacto as email, 'Bastonário' as type FROM bastonarios WHERE email_contacto != ''");
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
    // Fetch Departments
    $stmt = $pdo->query("SELECT titulo as name, email, 'Departamento' as type FROM departamentos_contactos WHERE status = 'ativo' AND email != ''");
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Format for Tagify
$formatted = array_map(function($item) {
    return [
        'value' => $item['email'],
        'name' => $item['name'] ?: $item['email'],
        'type' => $item['type']
    ];
}, $results);

echo json_encode($formatted);
