# Fluxo Governamental: Renovação de Cédula Profissional Digital
## Especialidade: E-Gov & Desburocratização

### 1. Início do Processo (O Cidadão/Advogado)
- **Acesso**: O advogado autentica-se na **Área Reservada** do portal.
- **Trigger**: Detecção automática de cédula prestes a expirar. O sistema exibe um alerta de "Renovação Disponível".
- **Requisito PWA**: Mesmo sem internet constante, o formulário pode ser preenchido offline e submetido assim que houver conexão.

### 2. Formulário Inteligente (Recolha de Dados)
- **Pré-preenchimento**: O sistema puxa os dados atuais da base de dados `advogados` ou `advogados_estagiarios`.
- **Upload de Bio-dados**: Interface otimizada para capturar foto via telemóvel (específico PWA) seguindo padrões oficiais.
- **Validação em Tempo Real**: Verificação de dívidas de quotas anteriores antes de permitir o avanço.

### 3. Módulo de Pagamento (Faturação Segura)
- **Emissão de Referência**: Geração automática de fatura pró-forma.
- **Canais de Pagamento**:
    - **Orange Money**: Gateway direto com confirmação instantânea.
    - **Multicaixa/Banco**: Upload de comprovativo de transferência (OCR inteligente para pré-validar o talão).
- **Status Temporário**: O registo assume o estado "Em Processamento de Pagamento".

### 4. Backoffice (Aprovação Administrativa/Secretaria)
- **Dashboard OAGB**: Notificação para a secretaria validar os documentos e o pagamento.
- **Protocolo de Assinatura**: O Bastonário ou Secretário-Geral assina digitalmente a aprovação.
- **Integração Logística**: Se a cédula for física, o sistema gera uma ordem de impressão.

### 5. Entrega e Notificação (Cidadão Protegido)
- **Cédula Digital (PWA)**: Uma versão digital com **QR-Code de Autenticidade** fica disponível offline na carteira digital do site.
- **Notificação Push**: "Sua renovação foi aprovada. A cédula física pode ser levantada na sede."
- **Atualização de Cadastro**: O estado na "Pesquisa de Advogados" pública é atualizado automaticamente para "Ativo (Válido até 20XX)".

---
*Elaborado por: E-Gov Analyst*
