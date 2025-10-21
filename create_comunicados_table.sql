-- Create comunicados table if it doesn't exist
CREATE TABLE IF NOT EXISTS `comunicados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(300) NOT NULL,
  `descricao` text DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `link_texto` varchar(100) DEFAULT 'Ler mais',
  `imagem` varchar(255) DEFAULT NULL,
  `data_publicacao` date DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ativo` (`ativo`),
  KEY `idx_data` (`data_publicacao`),
  KEY `idx_ordem` (`ordem_exibicao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comunicados da OAGB';

-- Insert sample data
INSERT INTO `comunicados` (`id`, `titulo`, `descricao`, `link_url`, `link_texto`, `data_publicacao`, `ordem_exibicao`, `ativo`) VALUES
(1, 'Comunicado - Renovação de Cédulas 2025', 'Todos os advogados devem proceder à renovação das suas cédulas profissionais até 31 de Dezembro de 2024.', 'comunicados.php?id=1', 'Ver comunicado', '2024-12-01', 1, 1),
(2, 'Comunicado - Assembleia Geral Extraordinária', 'Convocação para Assembleia Geral Extraordinária a realizar-se no próximo dia 15 de Janeiro de 2025.', 'comunicados.php?id=2', 'Ver detalhes', '2024-12-10', 2, 1),
(3, 'Comunicado - Formação Obrigatória', 'Informações sobre o programa de formação contínua obrigatória para o ano de 2025.', 'comunicados.php?id=3', 'Mais informações', '2024-12-05', 3, 1);