<?php
require_once 'connect.php';
$stmt = $pdo->query("SELECT * FROM anuncios");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($res)) {
    echo "EMPTY";
} else {
    print_r($res);
}
