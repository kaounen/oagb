-- --------------------------------------------------------
-- Script de Expansão da Base de Dados da OAGB
-- --------------------------------------------------------

-- 1. Tabela para os Departamentos da Página de Contactos
CREATE TABLE IF NOT EXISTS `departamentos_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `morada` text,
  `telefone` varchar(100),
  `email` varchar(100),
  `horario` varchar(255),
  `ordem` int(11) DEFAULT '0',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados Fictícios para Departamentos
INSERT INTO `departamentos_contactos` (`titulo`, `morada`, `telefone`, `email`, `horario`, `ordem`) VALUES
('Sede - Conselho Nacional', 'Bairro de Santa Luzia, Rua Principal, Bissau, Guiné-Bissau', '+245 95 123 4567', 'geral@oagb.gw', 'Segunda a Sexta: 08:30 - 15:30', 1),
('Conselho Regional de Bissau', 'Avenida Pansau Na Isna, Bissau', '+245 96 987 6543', 'regional.bissau@oagb.gw', 'Segunda a Sexta: 08:30 - 15:00', 2),
('Centro de Estágio e Formação', 'Rua Eduardo Mondlane, Prédio M, Bissau', '+245 95 555 1234', 'formacao@oagb.gw', 'Segunda a Sexta: 09:00 - 16:00', 3),
('Gabinete de Acesso ao Direito', 'Palácio de Justiça, Bissau', '+245 95 111 2233', 'acesso.direito@oagb.gw', 'Segunda a Sexta: 08:30 - 14:00', 4);

-- 2. Tabela para Informação Jurídica (OHADA, CEDEAO, etc.)
CREATE TABLE IF NOT EXISTS `informacao_juridica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL, -- Ex: 'OHADA', 'CEDEAO', 'União Africana', 'Direitos Humanos'
  `titulo` varchar(255) NOT NULL,
  `conteudo` text,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `data_publicacao` date NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados Fictícios para Informação Jurídica
INSERT INTO `informacao_juridica` (`categoria`, `titulo`, `conteudo`, `data_publicacao`) VALUES
('OHADA', 'Tratado de Port-Louis e a sua Aplicação', 'O Tratado relativo à Harmonização do Direito dos Negócios em África (OHADA) foi assinado em Port-Louis e tem um impacto direto nas transações comerciais...', '2024-01-15'),
('OHADA', 'Atos Uniformes da OHADA - Versão Atualizada', 'Documentação sobre os atos uniformes referentes ao direito comercial geral, sociedades comerciais e agrupamentos de interesse económico...', '2024-02-20'),
('CEDEAO', 'Protocolo sobre a Livre Circulação de Pessoas', 'Análise jurídica sobre o protocolo da Comunidade Económica dos Estados da África Ocidental relativo ao direito de residência e estabelecimento...', '2023-11-10'),
('União Africana', 'Carta Africana dos Direitos Humanos e dos Povos', 'Instrumento regional de proteção dos direitos humanos adotado no quadro da Organização de Unidade Africana (agora União Africana)...', '2023-08-05'),
('Direitos Humanos', 'Relatório Anual sobre Direitos Humanos na África Ocidental', 'Resumo e conclusões do relatório anual, destacando o papel dos advogados na defesa e promoção das liberdades fundamentais...', '2024-03-01');

-- 3. Tabela para Revistas da Ordem
CREATE TABLE IF NOT EXISTS `revistas_oagb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `edicao` varchar(50) NOT NULL,
  `data_publicacao` date NOT NULL,
  `capa_imagem` varchar(255) DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados Fictícios para Revistas
INSERT INTO `revistas_oagb` (`titulo`, `edicao`, `data_publicacao`) VALUES
('Revista da Ordem - O Papel da Justiça em Bissau', 'Edição Nº 12', '2024-04-01'),
('Revista da Ordem - Advocacia e os Desafios da OHADA', 'Edição Nº 11', '2023-10-15'),
('Revista da Ordem - Especial Direitos Humanos', 'Edição Nº 10', '2023-05-10');

-- 4. Tabela para Informação ao Cidadão (Acesso ao Direito, etc.)
CREATE TABLE IF NOT EXISTS `info_cidadaos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `ordem` int(11) DEFAULT '0',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados Fictícios para Info Cidadãos
INSERT INTO `info_cidadaos` (`titulo`, `slug`, `conteudo`, `ordem`) VALUES
('O que é o Acesso ao Direito?', 'acesso-ao-direito', '<p>O acesso ao direito e aos tribunais é um direito fundamental. Na Guiné-Bissau, os cidadãos com insuficiência económica têm direito a apoio judiciário...</p>', 1),
('Direitos e Deveres do Cidadão Perante a Justiça', 'direitos-deveres', '<p>Informação prática sobre o que esperar ao recorrer aos tribunais e o papel imprescindível do advogado na defesa dos seus direitos...</p>', 2),
('Glossário Jurídico para Cidadãos', 'glossario-juridico', '<p><strong>Habeas Corpus:</strong> Providência que visa garantir a liberdade individual contra detenções ilegais.<br><strong>Procuração:</strong> Documento através do qual alguém confere poderes a outra pessoa (ex: advogado)...</p>', 3);
