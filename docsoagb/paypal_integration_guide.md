# Guia Técnico de Integração PayPal para a OAGB

Este guia detalha o processo de ativação técnica e integração do PayPal como gateway de pagamento alternativo para inscrições e quotas na Guiné-Bissau.

---

## 1. Viabilidade do PayPal na Guiné-Bissau
Como identificado na documentação de cobertura mundial do PayPal, a **Guiné-Bissau** é listada oficialmente como país elegível para a criação de contas PayPal. 

### Benefícios desta Solução para a OAGB:
1. **Dispensa Gateway Regional**: Permite à OAGB receber pagamentos internacionais via cartão de crédito e contas PayPal sem necessitar de intermediários ou do Stripe.
2. **Segurança de Alto Nível**: Todas as transações com cartão são processadas de forma encriptada diretamente nos servidores do PayPal.
3. **Conversão de Moeda Automática**: O PayPal processa em USD ou EUR, e faz a conversão de taxas cambiais de forma automática (mesmo que a joia da OAGB esteja fixada em XOF).

---

## 2. Como Criar e Configurar a Conta Business da OAGB
Para poder aceitar pagamentos com cartão de crédito via portal, a OAGB precisa de criar uma **Conta PayPal Comercial (Business)**.

1. **Aceder ao Portal**: Ir a https://www.paypal.com/ e clicar em "Criar Conta" -> Selecionar **Conta Comercial / Business**.
2. **Dados Institucionais**:
   * Introduzir o e-mail institucional (ex: `financeiro@oagb.gw`).
   * Associar o Número de Identificação Fiscal (NIF) e detalhes de registo da Ordem.
3. **Associação Bancária**: Associar uma conta bancária internacional ou cartão de débito/crédito internacional que receba os fundos recolhidos no portal.

---

## 3. Integração Técnica via PayPal Smart Buttons
A forma mais moderna e recomendada de integrar o PayPal no formulário `inscricao-ordem.php` é através dos **Smart Payment Buttons** do PayPal, que disponibilizam um fluxo pop-up limpo dentro do próprio portal OAGB.

### Passo 1: Obter o Client ID do PayPal
1. Iniciar sessão no painel de programador em https://developer.paypal.com/.
2. Aceder a **Apps & Credentials** e criar uma App em ambiente **Live**.
3. Copiar o **Client ID** gerado.

### Passo 2: Implementação em `inscricao-ordem.php`
Podemos injetar o seguinte snippet JavaScript nas opções de pagamento do formulário:

```html
<!-- Incluir o SDK JavaScript do PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=SEU_CLIENT_ID_LIVE&currency=EUR"></script>

<!-- Contentor onde o botão do PayPal será renderizado -->
<div id="paypal-button-container" class="mt-3"></div>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        // Obter o valor da joia dinamicamente (ex: convertido para EUR)
        const valorJoiaXof = TIPO_INSCRICAO === 'estagiario' ? JOIA_EST : JOIA_ADV;
        
        // Conversão aproximada para EUR (ex: 1 EUR = ~656 XOF)
        const valorEur = (valorJoiaXof / 655.957).toFixed(2);

        return actions.order.create({
            purchase_units: [{
                amount: {
                    currency_code: 'EUR',
                    value: valorEur
                },
                description: 'Joia de Inscrição na OAGB - Ref #' + INSCRICAO_ID
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Pagamento aprovado pelo PayPal
            // Enviar os dados via AJAX para o controlador de pagamento da OAGB
            jQuery.ajax({
                url: 'processar_pagamento_local.php',
                method: 'POST',
                data: {
                    inscricao_id: INSCRICAO_ID,
                    metodo: 'paypal',
                    transacao_id: details.id,
                    valor: details.purchase_units[0].amount.value,
                    status: 'pago'
                },
                success: function(response) {
                    // Mostrar painel de sucesso no frontend
                    switchPayMethod('sucesso');
                    document.getElementById('succ_method').textContent = 'PayPal / Cartão';
                    document.getElementById('succ_ref').textContent = details.id;
                    document.getElementById('succ_val').textContent = details.purchase_units[0].amount.value + ' EUR';
                    document.getElementById('pay_success').style.display = 'block';
                }
            });
        });
    },
    onError: function(err) {
        console.error(err);
        showPayError('Ocorreu um erro ao processar o pagamento com o PayPal.');
    }
}).render('#paypal-button-container');
</script>
```

---

## 4. Conclusão e Próximos Passos
O uso do PayPal é uma **alternativa excelente e viável** para a OAGB receber pagamentos internacionais por Cartão de Crédito de forma legal e segura. 

Recomendamos que, assim que a OAGB decida avançar com esta solução, nos forneçam o seu **Client ID** para que possamos ativá-lo diretamente no portal público.
