<?php
require_once 'c:/xampp/htdocs/oagb/admin/includes/db.php';
$s = $pdo->prepare("SELECT id, ficheiro_anexo, legenda_anexo FROM noticias WHERE id = 9");
$s->execute();
print_r($s->fetch(PDO::FETCH_ASSOC));
