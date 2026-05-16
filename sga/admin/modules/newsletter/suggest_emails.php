<?php
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$results = [];

if (strlen($query) >= 2) {
    $search = "%$query%";

    // Search in Advogados
    $stmt = $pdo->prepare("SELECT nome_completo as name, email, 'Advogado' as type FROM advogados WHERE (nome_completo LIKE ? OR email LIKE ?) AND status = 'ativo' LIMIT 5");
    $stmt->execute([$search, $search]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in Estagiários
    $stmt = $pdo->prepare("SELECT nome_completo as name, email, 'Estagiário' as type FROM advogados_estagiarios WHERE (nome_completo LIKE ? OR email LIKE ?) AND status = 'ativo' LIMIT 5");
    $stmt->execute([$search, $search]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in Newsletter Subs
    $stmt = $pdo->prepare("SELECT nome as name, email, 'Subscritor' as type FROM newsletter_subscricoes WHERE (nome LIKE ? OR email LIKE ?) AND ativo = 1 LIMIT 5");
    $stmt->execute([$search, $search]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in Bastonarios
    $stmt = $pdo->prepare("SELECT nome_completo as name, email_contacto as email, 'Bastonário' as type FROM bastonarios WHERE (nome_completo LIKE ? OR email_contacto LIKE ?) LIMIT 3");
    $stmt->execute([$search, $search]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in Departments
    $stmt = $pdo->prepare("SELECT titulo as name, email, 'Departamento' as type FROM departamentos_contactos WHERE (titulo LIKE ? OR email LIKE ?) AND status = 'ativo' LIMIT 3");
    $stmt->execute([$search, $search]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Format for Tagify: { value: email, name: name, type: type }
$formatted = array_map(function($item) {
    return [
        'value' => $item['email'],
        'name' => $item['name'] ?: $item['email'],
        'type' => $item['type']
    ];
}, $results);

echo json_encode($formatted);
