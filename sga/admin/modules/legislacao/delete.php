<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';
$tipo = $_GET['tipo'] ?? 'nacional'; $id = intval($_GET['id'] ?? 0);
$table = $tipo === 'internacional' ? 'legislacao_internacional' : 'legislacao_nacional';
if ($id > 0) { $pdo->prepare("DELETE FROM $table WHERE id = ?")->execute([$id]); }
header("Location: index.php?tipo=$tipo&msg=deleted"); exit;
