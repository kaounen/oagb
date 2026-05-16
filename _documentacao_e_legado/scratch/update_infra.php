<?php
require_once 'connect.php';

// 1. Create Financial Config Table
$sql = "CREATE TABLE IF NOT EXISTS `finan_config` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chave` varchar(100) NOT NULL UNIQUE,
    `valor` text NOT NULL,
    `descricao` varchar(255),
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$pdo->exec($sql);

// 2. Insert Default Values
$configs = [
    ['quota_advogado', '15000', 'Valor da quota mensal para advogados (CFA)'],
    ['quota_estagiario', '5000', 'Valor da quota mensal para estagiários (CFA)'],
    ['orange_money_merchant_id', 'OAGB_MERCH_001', 'Merchant ID Orange Money'],
    ['orange_money_api_key', 'pk_test_oagb_12345', 'Public API Key Orange Money'],
    ['orange_money_secret', 'sk_test_oagb_67890', 'Secret API Key Orange Money'],
    ['orange_money_enabled', '0', 'Ativar pagamentos Orange Money (1=Sim, 0=Não)']
];

foreach ($configs as $c) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO finan_config (chave, valor, descricao) VALUES (?, ?, ?)");
    $stmt->execute($c);
}

// 3. Create Password Recovery Table
$sql = "CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expiry` datetime NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$pdo->exec($sql);

// 4. Update finan_pagamentos to include more metadata if needed
// (Already has enough fields for now: advogado_id, tipo_pagamento_id, valor_pago, metodo_pagamento, status, data_pagamento)

echo "Financial and Security infrastructure updated.\n";
?>
