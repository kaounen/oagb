<?php
require_once 'connect.php';
$tables = ['advogados', 'advogados_estagiarios', 'finan_pagamentos', 'finan_tipos_pagamento', 'gestao_estagio_relatorios'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        echo "Table $t exists.\n";
    } catch (Exception $e) {
        echo "Table $t DOES NOT exist.\n";
    }
}
?>
