<?php
require_once __DIR__ . '/../../includes/db.php';

$id = (int)$_GET['id'];
if ($id) {
    // Delete photo if exists
    $stmt = $pdo->prepare("SELECT foto FROM orgaos_sociais WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row && $row['foto']) {
        @unlink(__DIR__ . '/../../../uploads/orgaos/' . $row['foto']);
    }

    $stmt = $pdo->prepare("DELETE FROM orgaos_sociais WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?success=1");
exit;
