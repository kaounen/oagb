# Guia de Implementação: Gateways de Pagamento Reais (OAGB)

Este guia descreve os passos necessários para transitar do **Modo Simulação** para o **Modo Produção** assim que as chaves reais da Orange Money, MTN e Stripe forem obtidas.

## 1. Configuração no Painel Administrativo

O sistema já possui uma interface de configuração em `admin/modules/financeiro/configuracoes.php`.

### Passos:
1. Aceda ao Painel Admin -> Gestão Financeira -> Configurações.
2. Substitua os valores de teste (`pk_test...`, `sk_test...`) pelas chaves de produção fornecidas pelas operadoras.
3. Altere o campo **"Estado do Gateway"** de "Modo Simulação" para **"ATIVADO"**.

---

## 2. Implementação Técnica (Orange Money / MTN)

Para pagamentos móveis na Guiné-Bissau, o fluxo recomendado é o **API Push (USSD)**.

### Integração no `pagamento_ussd.php`:
Em vez da simulação automática no `verificar_status.php`, deve-se realizar uma chamada `cURL` para o endpoint da operadora (ex: Orange Money Web API).

```php
// Exemplo de chamada para iniciar transação USSD
$ch = curl_init("https://api.orange.com/orange-money-webpay/gw/v1/payments");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $fconfig['orange_api_token']]);
// ... configurar payload com $bill['valor_pago'] e $bill['id']
$response = curl_exec($ch);
```

### Webhook (O Passo Mais Importante):
Crie um ficheiro `portal/callback_pagamento.php` para receber a confirmação assíncrona do servidor da operadora.

```php
<?php
// callback_pagamento.php
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data['status'] == 'SUCCESS') {
    $ref = $data['external_reference']; // O ID da fatura OAGB
    // UPDATE finan_pagamentos SET status = 'confirmado' WHERE id = ...
}
?>
```

---

## 3. Implementação Stripe (VISA/Mastercard)

Para o Stripe, recomenda-se o uso do **Stripe Checkout** para evitar lidar com dados sensíveis de cartões no servidor da Ordem (Compliance PCI).

### Alteração no `pagamento_stripe.php`:
Substitua o formulário manual por um redirecionamento para o Stripe:

```php
require_once 'vendor/autoload.php';
\Stripe\Stripe::setApiKey($fconfig['stripe_secret_key']);

$session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price_data' => [
      'currency' => 'xof',
      'product_data' => ['name' => 'Quotas OAGB'],
      'unit_amount' => $bill['valor_pago'],
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => 'https://oagb.gw/portal/financeiro.php?success=paid',
  'cancel_url' => 'https://oagb.gw/portal/pagamento_gateway.php',
]);

header("Location: " . $session->url);
```

---

## 4. Dados Bancários da Ordem

Para pagamentos via **Transferência Bancária**, adicione os dados (IBAN, Banco, Titular) no ficheiro `portal/financeiro.php` na secção de "Notas de Regularização" ou crie uma nova opção "Transferência" no gateway que exiba estes dados e permita o upload do comprovativo.

---

## 5. Checklist de Produção
- [ ] Ativar HTTPS (SSL) em todo o domínio `oagb.gw`.
- [ ] Validar Webhooks com as operadoras (whitelist de IPs).
- [ ] Testar com uma transação real de valor mínimo (ex: 100 CFA).
- [ ] Verificar se os logs de auditoria estão a registar o `provider_ref`.

---
*Documento gerado por Antigravity AI - Especialista em Finanças & DevSecOps*
