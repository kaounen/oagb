<?php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';
$s = $pdo->query("SHOW TABLES");
foreach ($s as $r) echo $r[0] . "\n";
