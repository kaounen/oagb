<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("DELETE FROM departamentos_contactos WHERE id = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) { }

header("Location: index.php?deleted=1");
exit;
