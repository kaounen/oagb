<?php
$id = $_GET['id'] ?? '';
$token = $_GET['token'] ?? '';
header("Location: comprovativo_vinculo.php?id=$id&token=$token");
exit;
?>
