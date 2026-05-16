<?php
require_once __DIR__ . '/../../includes/db.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo->prepare("DELETE FROM conteudos_paginas WHERE id = ?")->execute([$id]);
}
header('Location: index.php?msg=deleted');
exit;
