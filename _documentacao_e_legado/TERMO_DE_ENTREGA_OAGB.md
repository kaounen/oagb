# Termo de Entrega e Aceitação — Portal OAGB 2.0

**Projeto:** Desenvolvimento e Implementação do Portal Institucional da Ordem dos Advogados da Guiné-Bissau
**Versão:** 2.0 (Final)
**Data de Entrega:** 11 de Maio de 2026

---

## 1. Identificação do Objeto
O presente termo formaliza a entrega técnica e funcional da plataforma web da Ordem dos Advogados da Guiné-Bissau (OAGB), composta pelos seguintes componentes:

1.  **Website Público (CMS):** Interface dinâmica para cidadãos e membros.
2.  **Portal do Membro (Área Reservada):** Sistema de gestão de perfis, quotas e documentos para advogados e estagiários.
3.  **Painel de Administração (Backoffice):** Sistema centralizado com 33 módulos de gestão autónoma.
4.  **Base de Dados:** Esquema SQL migrado e populado com dados base.
5.  **PWA (Progressive Web App):** Versão instalável para dispositivos móveis.

---

## 2. Estado da Implementação
O sistema encontra-se **99% concluído**, restando apenas a migração final de dados reais e a integração de APIs de redes sociais (Facebook) e pagamentos (Orange/MTN) conforme solicitado.

### Funcionalidades Validadas:
- [x] Gestão de Notícias, Comunicados e Agenda.
- [x] Diretório Público de Membros com pesquisa avançada.
- [x] Emissão de Certidões Digitais de Quotas Regularizadas.
- [x] Sistema de Auditoria (Logs) e Gestão de Permissões.
- [x] Navegação Mobile otimizada e Multi-idioma (Google Translate).
- [x] Portabilidade de sistema (ROOT_URL dinâmica).

---

## 3. Credenciais e Acessos Institucionais
Para efeitos de validação e arranque operacional, foram criadas as seguintes contas:

### A. Administração Geral (Backoffice)
- **URL:** `https://oagb.gw/admin`
- **Utilizador:** `admin@oagb.gw`
- **Senha Padrão:** `admin123`
- **Nível:** Superadmin

### B. Gestão de Conteúdos (Editor)
- **Utilizador:** `editor@oagb.gw`
- **Senha Padrão:** `teste123`
- **Nível:** Administrador

### C. Portal do Membro (Exemplo de Advogado)
- **Email:** `antonio.santos@email.gw`
- **Senha Padrão:** `teste123`

---

## 4. Próximas Etapas e Solicitações
Para a finalização da integração automatizada, a equipa técnica solicita:
1.  **Credenciais de Facebook da Ordem:** Necessárias para a implementação do motor de extração automática de publicações, categorização e upload para as secções de notícias e eventos.
2.  **Configuração de SMTP:** Dados do servidor de email oficial para notificações automáticas.
3.  **Chaves de API de Pagamentos:** Para ativação real do Mobile Money.

---

## 5. Declaração de Aceitação
Pela presente, a equipa de desenvolvimento declara a entrega dos ficheiros fonte e documentação técnica associada. A Ordem dos Advogados da Guiné-Bissau assume a responsabilidade pela gestão dos conteúdos e salvaguarda das credenciais de acesso fornecidas.

**Equipa de Desenvolvimento:** Antigravity AI
**Assinatura:** __________________________
**Data:** 11/05/2026
