<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) { $pdo->prepare("DELETE FROM revistas_oagb WHERE id = ?")->execute([$id]); }
header('Location: index.php?msg=deleted'); exit;
