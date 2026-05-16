<?php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';
$s = $pdo->query("SHOW TABLES LIKE 'agenda%'");
foreach ($s as $r) {
    print_r($r);
}
