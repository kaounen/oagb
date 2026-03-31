# Documentação Técnica: Base de Dados da Ordem dos Advogados da Guiné-Bissau

## 1. Introdução

Este documento apresenta a arquitetura e o modelo de dados desenvolvido para o novo portal web da **Ordem dos Advogados da Guiné-Bissau (OAGB)**. O sistema foi desenhado para ser uma plataforma moderna, escalável e abrangente, capaz de gerir não apenas o conteúdo público do site (CMS), mas também os processos internos cruciais da Ordem, como a gestão de membros, comissões, estágios e formações.

A base de dados foi estruturada utilizando o modelo relacional (SQL), otimizada para motores como MySQL ou MariaDB, garantindo a integridade referencial e a segurança dos dados institucionais.

## 2. Arquitetura de Módulos

O sistema está dividido em oito módulos lógicos principais, que interagem entre si para fornecer uma visão unificada da instituição.

### 2.1 Módulo Institucional
Este módulo gere a informação estática e histórica da Ordem. A tabela `instituicao_info` centraliza os dados de contacto, missão e visão, facilitando a atualização global no site. A gestão de liderança é feita através das tabelas `bastonarios`, `orgaos_diretivos` e `membros_orgaos`, permitindo manter um histórico completo de mandatos passados e atuais.

### 2.2 Módulo de Comissões Especializadas
As comissões são grupos de trabalho fundamentais na Ordem. A tabela `comissoes` define a estrutura de cada grupo, enquanto a tabela associativa `membros_comissoes` estabelece a relação (N:N) com os advogados registados, definindo os cargos (Presidente, Vogal, etc.) e os períodos de vigência de cada membro na comissão.

### 2.3 Módulo de Cooperação Internacional
Para gerir as relações externas, a tabela `parcerias_internacionais` regista todos os protocolos, acordos e filiações da OAGB com entidades estrangeiras ou internacionais, controlando as datas de validade e o estado de cada parceria.

### 2.4 Módulo de Comunicação (Anúncios)
O sistema de gestão de conteúdos (CMS) para notícias e comunicados é suportado pelas tabelas `categorias_anuncios` e `anuncios`. Este módulo permite a criação de rascunhos, agendamento de publicações e categorização de conteúdo (Editais, Notícias, Eventos), com suporte para URLs amigáveis (slugs) essenciais para SEO.

### 2.5 Módulo de Formação Contínua
A capacitação dos membros é gerida pela tabela `formacoes`, que detalha os cursos, seminários e workshops oferecidos. O modelo suporta diferentes modalidades (Presencial, Online, Híbrido) e controla o estado das inscrições e a emissão de certificados.

### 2.6 Módulo de Estágios (Core Business)
Este é um dos módulos mais complexos e vitais. Acompanha todo o ciclo de vida do advogado estagiário:
1. **Registo**: A tabela `estagiarios` mantém os dados pessoais e académicos.
2. **Acompanhamento**: A tabela `estagios` liga o estagiário ao seu Patrono (um advogado sénior) e controla as fases do estágio.
3. **Avaliação**: A tabela `avaliacoes_estagio` regista as notas parcelares (Deontologia, Prática Processual, etc.) e os pareceres qualitativos dos relatórios e provas, garantindo um histórico transparente da evolução do candidato.

### 2.7 Módulo de Membros (Advogados)
O coração do sistema é a tabela `advogados`, que serve como o registo oficial (Cédula Profissional) de todos os profissionais inscritos na Ordem. Esta tabela relaciona-se com quase todos os outros módulos (Comissões, Estágios como Patronos, Utilizadores do sistema).

### 2.8 Módulo de Administração (Backend)
A segurança e o controlo de acessos são geridos pela tabela `utilizadores`. O sistema suporta diferentes níveis de privilégios (SuperAdmin, Editor, Avaliador) e permite que um utilizador do sistema seja associado a um perfil de advogado real, facilitando portais de auto-serviço no futuro.

## 3. Dicionário de Dados Principal

Abaixo detalhamos as tabelas mais críticas do sistema.

### Tabela: `advogados`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT (PK) | Identificador único |
| `nome_completo` | VARCHAR(150) | Nome do profissional |
| `numero_cedula` | VARCHAR(50) | Número oficial de inscrição (Único) |
| `status` | ENUM | Estado atual (Ativo, Suspenso, Cancelado) |
| `is_patrono` | BOOLEAN | Indica se tem permissão para orientar estagiários |

### Tabela: `estagios`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT (PK) | Identificador único do processo de estágio |
| `estagiario_id` | INT (FK) | Referência ao candidato |
| `patrono_id` | INT (FK) | Referência ao advogado orientador |
| `fase_atual` | ENUM | Fase do estágio (1ª Fase, 2ª Fase, Concluído) |
| `status` | ENUM | Estado do processo (Em Curso, Aprovado, Reprovado) |

### Tabela: `avaliacoes_estagio`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT (PK) | Identificador da avaliação |
| `estagio_id` | INT (FK) | Referência ao processo de estágio |
| `tipo_avaliacao` | ENUM | Relatório Intercalar, Prova Escrita, etc. |
| `nota_final` | DECIMAL | Classificação quantitativa |
| `resultado` | ENUM | Decisão final (Aprovado, Reprovado) |

## 4. Recomendações de Implementação e Acessibilidade

Tendo em conta as melhores práticas de desenvolvimento de plataformas institucionais modernas, recomenda-se que a implementação do frontend e do painel de administração siga os seguintes princípios:

1. **Acessibilidade (State-of-the-Art)**: O portal deve incluir ferramentas nativas para ajuste de tamanho de fonte, contraste (Dark/Light mode) e suporte para leitores de ecrã, garantindo que a justiça e a informação legal sejam acessíveis a todos os cidadãos, incluindo aqueles com deficiências visuais.
2. **Multilinguismo**: Embora o Português seja a língua oficial, a implementação de um sistema de tradução (ou suporte multi-idioma na base de dados no futuro) pode ser benéfica para a cooperação internacional.
3. **Segurança de Dados**: As passwords na tabela `utilizadores` devem ser sempre armazenadas utilizando algoritmos de hash fortes (ex: bcrypt ou Argon2), conforme já previsto no script de dados de exemplo.
4. **Backups**: Dada a natureza sensível dos dados (avaliações, processos disciplinares, registo de advogados), devem ser implementadas rotinas de backup diárias da base de dados.

## 5. Ficheiros Entregues

O projeto inclui os seguintes ficheiros técnicos:
- `oag_schema.sql`: Script DDL com a criação de todas as tabelas, chaves primárias, chaves estrangeiras e restrições.
- `oag_data.sql`: Script DML com dados de exemplo realistas para popular a base de dados e facilitar os testes de desenvolvimento.
- `oag_database_structure.md`: Documento de levantamento inicial com a estrutura em árvore de todos os módulos.

---
*Documento gerado para a Ordem dos Advogados da Guiné-Bissau.*
