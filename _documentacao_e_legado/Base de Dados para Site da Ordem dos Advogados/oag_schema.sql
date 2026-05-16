-- ==============================================================================
-- ESQUEMA DE BASE DE DADOS - ORDEM DOS ADVOGADOS DA GUINÉ-BISSAU (OAGB)
-- ==============================================================================
-- Motor: MySQL / MariaDB
-- Codificação: utf8mb4
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ==============================================================================
-- 1. MÓDULO: INFORMAÇÕES INSTITUCIONAIS
-- ==============================================================================

-- Tabela: instituicao_info
-- Armazena informações gerais sobre a Ordem (Sobre a Ordem)
CREATE TABLE `instituicao_info` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `missao` TEXT NOT NULL,
  `visao` TEXT NOT NULL,
  `valores` TEXT NOT NULL,
  `historia` TEXT,
  `estatutos_url` VARCHAR(255),
  `email_geral` VARCHAR(100) NOT NULL,
  `telefone_geral` VARCHAR(50) NOT NULL,
  `endereco` VARCHAR(255) NOT NULL,
  `horario_funcionamento` VARCHAR(255),
  `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: bastonarios
-- Armazena informações sobre o Bastonário atual e anteriores
CREATE TABLE `bastonarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome_completo` VARCHAR(150) NOT NULL,
  `biografia` TEXT,
  `foto_url` VARCHAR(255),
  `data_inicio_mandato` DATE NOT NULL,
  `data_fim_mandato` DATE,
  `email_contacto` VARCHAR(100),
  `is_atual` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: orgaos_diretivos
-- Armazena os diferentes órgãos (Conselho Diretivo, Assembleia Geral, etc.)
CREATE TABLE `orgaos_diretivos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: membros_orgaos
-- Relaciona pessoas aos órgãos diretivos
CREATE TABLE `membros_orgaos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `orgao_id` INT NOT NULL,
  `nome_completo` VARCHAR(150) NOT NULL,
  `cargo` VARCHAR(100) NOT NULL,
  `foto_url` VARCHAR(255),
  `data_inicio_mandato` DATE NOT NULL,
  `data_fim_mandato` DATE,
  `ordem_exibicao` INT DEFAULT 0,
  FOREIGN KEY (`orgao_id`) REFERENCES `orgaos_diretivos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 2. MÓDULO: COMISSÕES ESPECIALIZADAS
-- ==============================================================================

-- Tabela: comissoes
CREATE TABLE `comissoes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(150) NOT NULL,
  `descricao` TEXT,
  `objetivos` TEXT,
  `data_criacao` DATE,
  `email_contacto` VARCHAR(100),
  `status` ENUM('Ativa', 'Inativa') DEFAULT 'Ativa',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: membros_comissoes
-- Relaciona advogados/membros às comissões
CREATE TABLE `membros_comissoes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `comissao_id` INT NOT NULL,
  `advogado_id` INT NOT NULL, -- Referência à tabela de advogados (criada mais abaixo)
  `cargo` VARCHAR(100) DEFAULT 'Membro', -- Ex: Presidente, Vice-Presidente, Vogal, Membro
  `data_entrada` DATE NOT NULL,
  `data_saida` DATE,
  FOREIGN KEY (`comissao_id`) REFERENCES `comissoes`(`id`) ON DELETE CASCADE
  -- A FK para advogado_id será adicionada depois
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 3. MÓDULO: COOPERAÇÃO INTERNACIONAL
-- ==============================================================================

-- Tabela: parcerias_internacionais
CREATE TABLE `parcerias_internacionais` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `entidade_parceira` VARCHAR(150) NOT NULL,
  `pais` VARCHAR(100) NOT NULL,
  `tipo_acordo` VARCHAR(100),
  `objetivo` TEXT,
  `data_assinatura` DATE,
  `data_validade` DATE,
  `documento_url` VARCHAR(255),
  `status` ENUM('Ativo', 'Expirado', 'Em Renovação') DEFAULT 'Ativo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 4. MÓDULO: ANÚNCIOS E NOTÍCIAS
-- ==============================================================================

-- Tabela: categorias_anuncios
CREATE TABLE `categorias_anuncios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(50) NOT NULL, -- Ex: Notícia, Aviso, Comunicado, Edital
  `slug` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: anuncios
CREATE TABLE `anuncios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `categoria_id` INT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `resumo` TEXT,
  `conteudo` LONGTEXT NOT NULL,
  `imagem_destaque_url` VARCHAR(255),
  `data_publicacao` DATETIME NOT NULL,
  `data_expiracao` DATETIME,
  `is_destaque` BOOLEAN DEFAULT FALSE,
  `status` ENUM('Rascunho', 'Publicado', 'Arquivado') DEFAULT 'Rascunho',
  `autor_id` INT, -- Referência ao utilizador do backend
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias_anuncios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 5. MÓDULO: FORMAÇÕES
-- ==============================================================================

-- Tabela: formacoes
CREATE TABLE `formacoes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(200) NOT NULL,
  `descricao` TEXT NOT NULL,
  `objetivos` TEXT,
  `conteudo_programatico` TEXT,
  `publico_alvo` VARCHAR(150),
  `formador` VARCHAR(150),
  `carga_horaria` INT, -- em horas
  `data_inicio` DATE NOT NULL,
  `data_fim` DATE NOT NULL,
  `local` VARCHAR(200),
  `modalidade` ENUM('Presencial', 'Online', 'Híbrido') DEFAULT 'Presencial',
  `vagas_totais` INT,
  `custo` DECIMAL(10,2) DEFAULT 0.00,
  `tem_certificado` BOOLEAN DEFAULT TRUE,
  `status` ENUM('Planeada', 'Inscrições Abertas', 'Em Curso', 'Concluída', 'Cancelada') DEFAULT 'Planeada',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 6. MÓDULO: ESTÁGIOS E AVALIAÇÕES
-- ==============================================================================

-- Tabela: estagiarios
CREATE TABLE `estagiarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome_completo` VARCHAR(150) NOT NULL,
  `numero_processo` VARCHAR(50) UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `telefone` VARCHAR(50),
  `data_nascimento` DATE,
  `universidade_origem` VARCHAR(150),
  `ano_conclusao_licenciatura` INT,
  `data_inscricao_ordem` DATE NOT NULL,
  `status` ENUM('Ativo', 'Suspenso', 'Concluído', 'Desistente') DEFAULT 'Ativo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: estagios
CREATE TABLE `estagios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estagiario_id` INT NOT NULL,
  `patrono_id` INT NOT NULL, -- Referência ao advogado orientador
  `data_inicio` DATE NOT NULL,
  `data_fim_prevista` DATE NOT NULL,
  `data_fim_efetiva` DATE,
  `fase_atual` ENUM('1ª Fase', '2ª Fase', 'Concluído') DEFAULT '1ª Fase',
  `status` ENUM('Em Curso', 'Suspenso', 'Aprovado', 'Reprovado') DEFAULT 'Em Curso',
  `observacoes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`estagiario_id`) REFERENCES `estagiarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela: avaliacoes_estagio
CREATE TABLE `avaliacoes_estagio` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estagio_id` INT NOT NULL,
  `avaliador_id` INT NOT NULL, -- Pode ser o patrono ou membro do júri
  `data_avaliacao` DATE NOT NULL,
  `tipo_avaliacao` ENUM('Relatório Intercalar', 'Relatório Final', 'Prova Escrita', 'Prova Oral') NOT NULL,
  
  -- Critérios de avaliação (notas de 0 a 20, por exemplo)
  `nota_conhecimento_juridico` DECIMAL(4,2),
  `nota_deontologia` DECIMAL(4,2),
  `nota_pratica_processual` DECIMAL(4,2),
  `nota_assiduidade` DECIMAL(4,2),
  
  `nota_final` DECIMAL(4,2) NOT NULL,
  `parecer_qualitativo` TEXT,
  `resultado` ENUM('Aprovado', 'Reprovado', 'Necessita Revisão') NOT NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`estagio_id`) REFERENCES `estagios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 7. MÓDULO: ADVOGADOS (MEMBROS)
-- ==============================================================================

-- Tabela: advogados
CREATE TABLE `advogados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome_completo` VARCHAR(150) NOT NULL,
  `numero_cedula` VARCHAR(50) NOT NULL UNIQUE,
  `data_inscricao` DATE NOT NULL,
  `email_profissional` VARCHAR(100),
  `telefone_profissional` VARCHAR(50),
  `endereco_escritorio` VARCHAR(255),
  `cidade` VARCHAR(100),
  `foto_url` VARCHAR(255),
  `status` ENUM('Ativo', 'Suspenso', 'Cancelado', 'Falecido') DEFAULT 'Ativo',
  `is_patrono` BOOLEAN DEFAULT FALSE, -- Indica se pode receber estagiários
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Adicionar as chaves estrangeiras pendentes
ALTER TABLE `membros_comissoes` 
ADD CONSTRAINT `fk_membro_advogado` FOREIGN KEY (`advogado_id`) REFERENCES `advogados`(`id`) ON DELETE CASCADE;

ALTER TABLE `estagios` 
ADD CONSTRAINT `fk_estagio_patrono` FOREIGN KEY (`patrono_id`) REFERENCES `advogados`(`id`);

ALTER TABLE `avaliacoes_estagio` 
ADD CONSTRAINT `fk_avaliacao_avaliador` FOREIGN KEY (`avaliador_id`) REFERENCES `advogados`(`id`);

-- ==============================================================================
-- 8. MÓDULO: UTILIZADORES DO SISTEMA (BACKEND)
-- ==============================================================================

-- Tabela: utilizadores
CREATE TABLE `utilizadores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('SuperAdmin', 'Admin', 'Editor', 'Avaliador') DEFAULT 'Editor',
  `advogado_id` INT NULL, -- Se o utilizador for também um advogado registado
  `ultimo_acesso` DATETIME,
  `status` ENUM('Ativo', 'Inativo') DEFAULT 'Ativo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`advogado_id`) REFERENCES `advogados`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `anuncios` 
ADD CONSTRAINT `fk_anuncio_autor` FOREIGN KEY (`autor_id`) REFERENCES `utilizadores`(`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
