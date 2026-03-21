# Instruções Técnicas OAGB 2.0 (Configuração Operacional)

Este guia técnico descreve como ativar e configurar os subsistemas de rede e financeiros da Ordem.

## 1. Ativação de Orange Money & Mobile Money (MTN)

O sistema OAGB 2.0 foi desenhado para ser agnóstico à operadora, utilizando um motor de checkout seguro. Para ativar os pagamentos reais:

### A. Fluxo de Contratação
1.  **Registo Corporativo**: Dirija-se à **Orange Bissau** (Serviço Orange Money) para empresas e solicite as credenciais de **Merchant/Agregador**.
2.  **Chaves de API**: A Orange fornecerá um `API_KEY` (Chave Pública) e um `SECRET_TOKEN` (Chave Privada).
3.  **URL de Resposta**: No painel da Orange/MTN, deverá configurar o nosso URL de notificação: `https://oagb.gw/portal/api/payment_callback.php`.

### B. Funcionamento Técnico do Pagamento
1.  O sistema OAGB cria uma **Referência de Transação** (ex: `OAGB-PAY-12345`).
2.  Quando o advogado confirma no portal, o sistema envia para a Orange o montante e o número de telemóvel do advogado.
3.  O advogado recebe no telemóvel um menu de sistema ("Push USSD") para introduzir o seu **PIN pessoal**.
4.  Após a introdução do PIN com sucesso, a Orange envia um sinal de **CONFIRMAÇÃO (POST)** ao servidor da Ordem.
5.  O servidor da Ordem recebe a confirmação, valida o montante e atualiza instantaneamente a base de dados (`finan_pagamentos`), desbloqueando automaticamente a certidão de quotas de quem pagou.

## 2. Configuração de Notificações (SMS & Email)

*   **SMS**: A classe `NotifyHelper.php` está pronta. Necessita da aquisição de uma conta de envio massivo (ex: Twilio, BulkSMS ou uma operadora local com gateway SMPP).
*   **Email**: O sistema utiliza a função `mail()` do PHP. Recomenda-se a instalação do **PHPMailer** para utilizar o SMTP oficial da Ordem para garantir que os e-mails não caiam no Spam.

## 3. Manutenção Final do Sistema

*   **PWA**: O ícone da App pode ser alterado substituindo o ficheiro `/oagb/img/logo3.png` por um ícone de 192x192px.
*   **Logs**: Recomenda-se que o Diretor de IT consulte periodicamente `/admin/modules/logs/` para verificar se houve tentativas de acesso não autorizado ou alterações críticas de regras de voto.

---
**OAGB 2.0 - Tecnologia ao Serviço da Advocacia.**
