# Manual de Utilizador e Guia de Gestão
## Portal Institucional da Ordem dos Advogados da Guiné-Bissau (OAGB)

> **Versão 1.0 — Maio 2026**
> Documento elaborado para uso interno e entrega à Direção da OAGB.

---

## Índice

1. [Visão Global da Plataforma](#1-visão-global-da-plataforma)
2. [Credenciais de Acesso e Perfis de Utilizador](#2-credenciais-de-acesso-e-perfis-de-utilizador)
3. [Painel de Administração (Backoffice) — Módulo a Módulo](#3-painel-de-administração-backoffice--módulo-a-módulo)
4. [Área Reservada de Membros](#4-área-reservada-de-membros)
5. [Guia de Dados Reais — Atributos de Cada Tabela](#5-guia-de-dados-reais--atributos-de-cada-tabela)
6. [Tarefas do Dia-a-Dia (Fluxos Práticos)](#6-tarefas-do-dia-a-dia-fluxos-práticos)
7. [Notas de Segurança e Boas Práticas](#7-notas-de-segurança-e-boas-práticas)

---

## 1. Visão Global da Plataforma

O portal da OAGB é composto por **três camadas distintas** que comunicam entre si:

### 1.1 Website Público (`oagb.gw`)
É a face visível para todos os cidadãos, advogados e parceiros internacionais. Qualquer pessoa pode aceder sem precisar de login. Aqui estão disponíveis:
- Informação institucional (História, Bastonário, Órgãos Sociais, Comissões)
- Notícias e comunicados oficiais
- Agenda de eventos e formações
- Diretório público de Advogados e Estagiários inscritos
- Pareceres, Deliberações e Legislação para download
- Formulário de pedido de inscrição na Ordem
- Formulário de solicitação de advogado por cidadãos
- Glossário Jurídico e Biblioteca Online

### 1.2 Painel de Administração / Backoffice (`oagb.gw/admin`)
Centro de comando da plataforma. Restrito ao staff da OAGB (Administradores, Editores e Superadmin). A partir daqui gere-se **tudo o que aparece no website público**. Não é necessário qualquer conhecimento técnico de programação. O sistema tem 33 módulos de gestão.

### 1.3 Área Reservada de Membros (`oagb.gw` > "Área Reservada")
Espaço privado para Advogados e Estagiários autenticados verificarem o seu estado na Ordem, atualizarem contactos e, futuramente, consultarem situação financeira.

---

## 2. Credenciais de Acesso e Perfis de Utilizador

### 2.1 Perfis Existentes no Sistema

O sistema tem **três níveis de acesso** para a área de administração:

| Perfil | Quem usa | O que pode fazer |
|---|---|---|
| **Superadmin** | Responsável técnico máximo | Tudo — incluindo gerir outros utilizadores, configurações avançadas e logs de auditoria |
| **Administrador** | Secretária-Geral, Direção | Gerir todo o conteúdo e membros. Pode criar e editar Editores. Sem acesso a configurações técnicas |
| **Editor** | Staff de comunicação, secretariado | Publicar notícias, eventos, anúncios e comunicados. Não pode apagar membros ou alterar configurações |

### 2.2 Credenciais de Teste (Conta de Demonstração)

> ⚠️ **Atenção:** Altere as senhas abaixo imediatamente após a validação do sistema.

**Painel de Administração**
- **URL:** `https://oagb.gw/admin` (ou `http://localhost/oagb/admin` em ambiente local)
- **Email Master (Superadmin):** `admin@oagb.gw` / **Senha:** `admin123`
- **Email Operacional (Administrador):** `editor@oagb.gw` / **Senha:** `teste123`
- **Nível:** Administrador / Superadmin

**Área Reservada (simulação de Advogado)**
- **URL:** Menu principal do site > "Área Reservada"
- **Email:** `antonio.santos@email.gw`
- **Senha:** `teste123`

### 2.3 Como Criar Novos Utilizadores do Backoffice

1. Entre no Backoffice com conta de Administrador ou Superadmin.
2. No menu lateral, vá a **Utilizadores** → **Adicionar Novo**.
3. Preencha: Nome Completo, Username, Email Profissional (ex: `secretaria@oagb.gw`), Senha e **Nível de Permissão**.
4. Clique em **"Criar Acesso ao Sistema"**.
5. A pessoa receberá os dados de acesso e poderá entrar imediatamente.

---

## 3. Painel de Administração (Backoffice) — Módulo a Módulo

Ao entrar no backoffice, o menu lateral apresenta todos os módulos disponíveis. Aqui está o que cada um faz:

### Módulo: Advogados
Gestão do **Cadastro Nacional de Advogados**. A partir daqui pode:
- **Ver a lista** de todos os advogados inscritos e pesquisar por nome, região ou número de cédula.
- **Adicionar um novo** advogado (preenchendo o formulário com os dados do membro).
- **Editar** os dados de qualquer advogado existente (foto, contacto, morada, estado).
- **Suspender ou inativar** um advogado sem o eliminar (o registo fica guardado mas sai do diretório público).
- A lista pública em `advogados-inscritos.php` atualiza-se automaticamente.

### Módulo: Estagiários
Idêntico ao módulo de Advogados, mas para **Estagiários**. Inclui campos adicionais para o **advogado orientador**, **data de início** e **data de fim** do estágio.

### Módulo: Notícias
Central de publicação de artigos e notícias institucionais.
- Crie artigos com editor de texto formatado (negrito, listas, links, tabelas).
- Defina uma **imagem de destaque** para aparecer na listagem e nas partilhas em redes sociais.
- Marque como **Destaque** para o artigo aparecer em grande plano na homepage.
- Agende a publicação para uma data futura.
- O campo **Categoria** agrupa os artigos (ex: "Formação", "Institucional", "Internacional").

### Módulo: Anúncios / Comunicados
Para avisos urgentes e comunicados institucionais que precisam de visibilidade imediata.
- Defina uma **data de início e fim** de publicação — o anúncio desaparece automaticamente fora desse período.
- Pode associar um link para uma página interna ou externa.

### Módulo: Agenda (Eventos)
Registo de todos os eventos futuros da Ordem.
- Preencha o **tipo de evento** (Congresso, Formação, Assembleia, Workshop, etc.).
- Defina **hora de início e fim**, **local completo** e dados de contacto para inscrição.
- Pode fazer upload do **programa do evento** em PDF.
- Eventos com **Destaque** ativado aparecem na secção "Próximos Eventos" da homepage.
- Eventos cuja data já passou são automaticamente arquivados.

### Módulo: Pareceres e Deliberações
Repositório legal e documental oficial da OAGB.
- Escolha o tipo de documento: **Parecer** ou **Deliberação**.
- Preencha o número de referência (ex: `CNEF n.º 8/2023`) e a data.
- Faça upload do ficheiro PDF original.
- Os documentos ficam disponíveis publicamente na página "Pareceres e Deliberações".

### Módulo: Carousel (Slider da Homepage)
Controla as imagens e mensagens no topo animado da página inicial.
- Altere o **título** e o **subtítulo** sobrepostos à imagem.
- Faça upload de uma nova imagem de fundo.
- Defina o **texto e o link do botão** de chamada à ação (ex: "Ver Formações" → agenda.php).
- Arraste para reordenar os slides. Ative ou desative cada slide individualmente.

### Módulo: Órgãos Sociais
Atualiza a composição do Conselho, Assembleia Geral e órgãos eleitos da OAGB.
- Adicione cada membro com **nome**, **cargo**, **foto** e **datas de mandato**.
- Define a **ordem de exibição** para controlar a sequência na página pública.

### Módulo: Comissões Especializadas
Gere as comissões permanentes ou temporárias da Ordem.
- Registe o nome da comissão, o **presidente** e os membros.
- Descreva a área de atuação e objetivos.

### Módulo: Galeria de Bastonários
Linha do tempo visual dos anteriores Bastonários da OAGB. Adicione cada mandatário com foto, período de mandato e nota biográfica.

### Módulo: Biblioteca Online
Gestão do repositório de publicações jurídicas disponíveis para consulta ou download pelo público.
- Adicione títulos com autor, data, descrição e ficheiro para download.

### Módulo: Revista OAGB
Gestão das edições da Revista Jurídica da Ordem. Cada edição pode ter capa, data e PDF para download.

### Módulo: Legislação
Repositório de legislação nacional e internacional referenciada pela OAGB. Divida por tipo (Nacional / Internacional) com links externos ou ficheiros.

### Módulo: Inscrições
Aqui chegam todos os **pedidos de inscrição** submetidos através do formulário público do site.
- O estado padrão é **"Pendente"**. O administrador analisa e aprova, rejeita ou pede mais informação.
- Após aprovação, pode criar o registo oficial do advogado ou estagiário diretamente a partir do pedido.

### Módulo: Solicitações de Advogado
Pedidos feitos por **cidadãos** que necessitam de ser contactados por um advogado.
- Visualize a área jurídica, região e urgência declarada pelo cidadão.
- Atribua um advogado do sistema ao pedido e marque-o como resolvido.

### Módulo: Contactos
Caixa de entrada das mensagens enviadas pelo formulário de contacto do site. Marque como "lida" ou "respondida" para controlo interno.

### Módulo: Financeiro
Gestão básica de quotas e situação financeira dos membros. Permite registar pagamentos e consultar históricos.

### Módulo: Utilizadores (Staff do Backoffice)
Crie e gira os acessos de toda a equipa administrativa. Defina permissões por nível (Editor, Administrador, Superadmin).

### Módulo: Configurações do Site
Parâmetros globais do portal (sem necessidade de editar código):
- **Contactos:** Telefone, email e morada da sede (aparecem no rodapé e página de contactos)
- **Redes Sociais:** Links para Facebook, Instagram, LinkedIn, YouTube, Twitter
- **SEO:** Descrição global e imagem padrão para partilha
- **Email (SMTP):** Configuração do servidor de envio de emails automáticos (confirmações, notificações)
- **Modo Manutenção:** Ative para colocar o site temporariamente offline com mensagem customizada

### Módulo: Logs de Atividade
Registo automático de todas as ações realizadas no sistema (quem fez o quê e quando). Essencial para auditoria interna.

---

## 4. Área Reservada de Membros

O acesso é feito a partir do menu "Área Reservada" no site principal. Após autenticação, o advogado ou estagiário tem acesso a:

- **Cartão Digital:** Visualização do perfil profissional com número de cédula e situação na Ordem.
- **Atualização de Contactos:** O membro pode atualizar o seu telefone, email e morada do escritório — estes dados ficam visíveis no diretório público.
- **Situação Financeira:** Consulta do histórico de quotas e eventual regularização online.
- **Submissão de Relatórios** (Estagiários): Upload de relatórios de estágio em PDF diretamente pelo portal.

---

## 5. Guia de Dados Reais — Atributos de Cada Tabela

Esta secção é um guia técnico para a equipa da OAGB preparar a informação real a inserir no sistema, seja manualmente no backoffice ou através de importação a partir de folhas de cálculo (Excel/CSV).

### 5.1 Advogados (`tabela: advogados`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `numero_registo` | ✅ Sim | Número de cédula único (ex: `001/2020`) |
| `nome_completo` | ✅ Sim | Nome completo (ex: `António Silva Santos`) |
| `genero` | ✅ Sim | `M` (Masculino) ou `F` (Feminino) |
| `data_nascimento` | Não | Data no formato `AAAA-MM-DD` |
| `nacionalidade` | Não | Por defeito: `Guineense` |
| `bi_passaporte` | Não | Número do documento de identidade |
| `regiao` | ✅ Sim | Região de atividade (ex: `SAB`, `Bafatá`, `Cacheu`) |
| `localidade` | Não | Cidade ou localidade (ex: `Bissau`) |
| `morada` | Não | Endereço completo do escritório |
| `telefone` | Não | Contacto direto (ex: `+245 966 123 456`) |
| `email` | Não | Email profissional visível no diretório |
| `data_inscricao` | ✅ Sim | Data de admissão na Ordem |
| `status` | ✅ Sim | `ativo`, `suspenso`, ou `inativo` |
| `foto` | Não | Fotografia de perfil (upload de imagem) |
| `observacoes` | Não | Notas internas (não visíveis ao público) |

### 5.2 Estagiários (`tabela: advogados_estagiarios`)

Tem todos os campos dos Advogados, com os seguintes campos adicionais:

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `orientador_id` | Não | ID do advogado orientador do estágio |
| `data_inicio_estagio` | ✅ Sim | Data de início do estágio |
| `data_fim_estagio` | Não | Data de conclusão (vazio se ainda decorrer) |
| `status` | ✅ Sim | `ativo`, `concluido`, ou `cancelado` |

### 5.3 Notícias (`tabela: noticias`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `titulo` | ✅ Sim | Título do artigo |
| `resumo` | Não | 1–2 frases de resumo (aparece nas listagens) |
| `conteudo` | ✅ Sim | Corpo completo do artigo (aceita HTML) |
| `imagem_destaque` | Não | Imagem principal do artigo |
| `categoria` | Não | Ex: `Formação`, `Institucional`, `Internacional` |
| `data_publicacao` | Não | Data de publicação (por defeito: data atual) |
| `destaque` | Não | `1` = aparece em destaque na homepage |
| `ativo` | Não | `1` = visível, `0` = rascunho/oculto |
| `meta_title` | Não | Título alternativo para SEO e redes sociais |
| `meta_description` | Não | Descrição curta para partilha em Facebook |

### 5.4 Eventos / Agenda (`tabela: agenda`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `titulo` | ✅ Sim | Nome do evento |
| `descricao` | Não | Descrição completa do evento |
| `data_evento` | ✅ Sim | Data e hora de início (ex: `2026-06-15 09:00:00`) |
| `data_fim_evento` | Não | Data e hora de fim (para eventos multi-dia) |
| `local_evento` | Não | Nome do local (ex: `Hotel Dunia, Bissau`) |
| `tipo_evento` | Não | `congresso`, `formacao`, `reuniao`, `workshop`, etc. |
| `organizador` | Não | Entidade organizadora |
| `imagem_destaque` | Não | Imagem promocional do evento |
| `destaque` | Não | `1` = aparece no bloco "Próximos Eventos" |
| `ativo` | Não | `1` = visível publicamente |

### 5.5 Slides da Página Inicial (`tabela: carousel_slides`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `titulo` | ✅ Sim | Título grande sobreposto à imagem |
| `subtitulo` | Não | Texto de apoio (parágrafo abaixo do título) |
| `imagem` | ✅ Sim | Ficheiro de imagem de fundo (upload) |
| `link_texto` | Não | Texto do botão (ex: `Saber mais`, `Ver Formações`) |
| `link_url` | Não | Página de destino do botão (ex: `agenda.php`) |
| `ordem_exibicao` | Não | Posição do slide: `1`, `2`, `3`... |
| `ativo` | Não | `1` = visível, `0` = oculto |

### 5.6 Pareceres e Deliberações (`tabela: pareceres_deliberacoes`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `tipo` | ✅ Sim | `parecer` ou `deliberacao` |
| `numero_documento` | ✅ Sim | Referência oficial (ex: `Parecer n.º 12/2023`) |
| `titulo` | ✅ Sim | Título descritivo do documento |
| `descricao` | Não | Resumo do conteúdo |
| `data_documento` | ✅ Sim | Data de emissão do documento |
| `arquivo_pdf` | Não | Ficheiro PDF para download público |
| `ativo` | Não | `1` = visível, `0` = oculto |

### 5.7 Anúncios (`tabela: anuncios`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `titulo` | ✅ Sim | Título do anúncio |
| `descricao` | Não | Texto explicativo |
| `link_url` | Não | Link para mais informação |
| `link_texto` | Não | Texto do link (ex: `Saiba mais`) |
| `data_inicio` | Não | Data a partir da qual o anúncio fica visível |
| `data_fim` | Não | Data em que o anúncio desaparece automaticamente |
| `ordem_exibicao` | Não | Define a ordem de apresentação dos anúncios |

### 5.8 Órgãos Sociais (`tabela: orgaos_sociais`)

| Campo | Obrigatório | Descrição / Exemplo |
|---|---|---|
| `nome` | ✅ Sim | Nome completo do membro |
| `cargo` | ✅ Sim | Ex: `Bastonário`, `Vice-Bastonário`, `Tesoureiro` |
| `mandato_inicio` | Não | Data de início do mandato |
| `mandato_fim` | Não | Data de fim do mandato |
| `foto` | Não | Fotografia do rosto |
| `biografia` | Não | Nota biográfica curta |
| `ordem_exibicao` | Não | Posição na listagem pública |

### 5.9 Configurações Globais do Site (`tabela: configuracoes_site`)

Estes valores são editáveis diretamente no Backoffice em **Configurações**:

| Chave | Grupo | Valor atual | O que é |
|---|---|---|---|
| `contact_phone` | contacto | `+245 955 475 889` | Telefone no rodapé e página de contactos |
| `contact_email` | contacto | `info@oagb.gw` | Email principal visível no site |
| `contact_address` | contacto | `Rua 15, Bissau` | Endereço da sede |
| `facebook_url` | social | Facebook da OAGB | Link para o Facebook |
| `site_description` | seo | Descrição padrão | Frase de apresentação para motores de busca |
| `manutencao_ativa` | sistema | `0` | Coloca o site em manutenção (`1` = sim) |

---

## 6. Tarefas do Dia-a-Dia (Fluxos Práticos)

### "Quero publicar uma notícia sobre uma formação"
1. Backoffice → **Notícias** → **Adicionar Nova**
2. Preencha título, texto, data e imagem de destaque.
3. Selecione categoria `Formação`. Ative "Destaque" se for a peça principal do momento.
4. Clique **Guardar**. A notícia fica imediatamente visível no site.

### "Um novo advogado foi admitido na Ordem"
1. Backoffice → **Advogados** → **Adicionar Novo**
2. Preencha os dados do advogado (cédula, nome, região, data de inscrição).
3. Guarde. O advogado fica automaticamente no diretório público.

### "Recebi um pedido de inscrição pelo site"
1. Backoffice → **Inscrições**
2. Veja os pedidos com estado "Pendente".
3. Clique em "Ver Detalhes" para analisar a documentação submetida.
4. Aprove (cria registo de membro) ou Rejeite (com nota explicativa).

### "Quero mudar as imagens do slider da página inicial"
1. Backoffice → **Carousel**
2. Edite um slide existente ou adicione um novo.
3. Faça upload da nova imagem e ajuste o título e o botão.
4. Guarde. A alteração é imediata no site público.

---

## 7. Notas de Segurança e Boas Práticas

- **Altere as senhas de demonstração** imediatamente após o primeiro acesso real.
- **Crie um utilizador nominativo** para cada membro do staff (não partilhe logins).
- **O nível "Editor"** é adequado para quem só gere notícias e eventos — nunca dê acesso de "Superadmin" a funções operacionais correntes.
- **O sistema regista automaticamente** todas as ações no módulo "Logs de Atividade" — se houver dúvida sobre quem fez uma alteração, consulte o registo.
- **Faça backups regulares** da base de dados (via cPanel > phpMyAdmin > Exportar).
- **Modo Manutenção:** Ative antes de fazer grandes atualizações de conteúdo ou alterações técnicas. Desative quando terminar.
