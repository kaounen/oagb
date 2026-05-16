<?php
require_once 'connect.php';

$configs = [
    ['stripe_public_key', 'pk_test_oagb_stripe_123', 'Chave Pública Stripe (VISA/Mastercard)'],
    ['stripe_secret_key', 'sk_test_oagb_stripe_456', 'Chave Secreta Stripe'],
    ['paypal_client_id', 'oagb_paypal_id_789', 'PayPal Client ID'],
    ['global_payments_enabled', '0', 'Ativar Pagamentos Internacionais (Stripe/PayPal)']
];

foreach ($configs as $c) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO finan_config (chave, valor, descricao) VALUES (?, ?, ?)");
    $stmt->execute($c);
}

echo "Global payments infrastructure updated.\n";
?>
