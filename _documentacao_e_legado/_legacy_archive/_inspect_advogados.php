<?php
require 'connect.php';
$cols = $pdo->query("SHOW COLUMNS FROM advogados_estagiarios")->fetchAll(PDO::FETCH_COLUMN);
foreach($cols as $c) echo $c . "\n";
