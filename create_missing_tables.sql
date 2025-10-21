-- Create missing tables for OAGB index.php
-- Execute this if carousel_slides, pareceres_deliberacoes, or comunicados tables are missing

-- Table for carousel slides
CREATE TABLE IF NOT EXISTS `carousel_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` text,
  `imagem` varchar(255) DEFAULT NULL,
  `link_texto` varchar(100) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default carousel slides
INSERT IGNORE INTO `carousel_slides` (`id`, `titulo`, `subtitulo`, `imagem`, `link_texto`, `link_url`, `ordem_exibicao`, `ativo`) VALUES
(1, 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau', 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito.', 'gestao/assets/uploads/files/brass-scales-justice-close-up-view.jpg', 'Saiba mais', 'apresentacao-historia.php', 1, 1),
(2, 'Cadastro Nacional de Advogados', 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB.', 'gestao/assets/uploads/files/close-up-scales-justice-original-azul.jpg', 'Pesquisar Advogados', 'pesquisa-advogados.php', 2, 1),
(3, 'Justiça e Transparência', 'Garantindo a excelência jurídica e a defesa dos direitos dos cidadãos da Guiné-Bissau.', 'gestao/assets/uploads/files/close-up-detail-scales-justice.jpg', 'Nossos Serviços', 'publicacoes.php', 3, 1);

-- Table for pareceres and deliberações
CREATE TABLE IF NOT EXISTS `pareceres_deliberacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('parecer','deliberacao') NOT NULL DEFAULT 'parecer',
  `numero_documento` varchar(50) DEFAULT NULL,
  `data_documento` date DEFAULT NULL,
  `conteudo` text,
  `link_url` varchar(255) DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_data_documento` (`data_documento`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample parecer
INSERT IGNORE INTO `pareceres_deliberacoes` (`id`, `titulo`, `tipo`, `numero_documento`, `data_documento`, `link_url`, `ativo`) VALUES
(1, 'Regulamento de Exercício da Advocacia', 'deliberacao', 'CNEF - Deliberação n.º 8/2023', '2023-12-15', 'pareceres-deliberacoes.php', 1);

-- Table for comunicados
CREATE TABLE IF NOT EXISTS `comunicados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text,
  `conteudo` text,
  `data_publicacao` date DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_data_publicacao` (`data_publicacao`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample comunicado
INSERT IGNORE INTO `comunicados` (`id`, `titulo`, `descricao`, `data_publicacao`, `link_url`, `ativo`) VALUES
(1, 'Comunicado - Assembleia Geral 2024', 'Convocação para Assembleia Geral Ordinária', '2024-01-15', 'comunicados.php', 1);

-- Ensure noticias table has required columns
ALTER TABLE `noticias` 
ADD COLUMN IF NOT EXISTS `destaque` tinyint(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `slug` varchar(255) DEFAULT NULL;

-- Ensure agenda table has required columns  
ALTER TABLE `agenda` 
ADD COLUMN IF NOT EXISTS `ativo` tinyint(1) DEFAULT 1;

-- Update existing news to have some featured articles
UPDATE `noticias` SET `destaque` = 1, `ativo` = 1 WHERE `id` <= 3;

-- Update existing agenda items to be active
UPDATE `agenda` SET `ativo` = 1;