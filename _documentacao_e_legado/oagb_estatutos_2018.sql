-- --------------------------------------------------------
-- Script Base de Dados - Atualização OAGB Estatutos 2018
-- --------------------------------------------------------

-- 1. Órgãos Sociais (Estrutura 2018)
CREATE TABLE IF NOT EXISTS `orgaos_sociais_2018` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orgao_nome` varchar(255) NOT NULL,
  `descricao` text,
  `competencias` text,
  `ordem` int(11) DEFAULT '0',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orgaos_sociais_2018` (`orgao_nome`, `descricao`, `competencias`, `ordem`) VALUES
('Congresso dos Advogados', 'Órgão máximo de reflexão e debate.', 'Debater os grandes temas da justiça e advocacia na Guiné-Bissau.', 1),
('Assembleia Geral', 'Órgão deliberativo supremo.', 'Aprovar orçamento, relatórios e regulamentos internos.', 2),
('Bastonário', 'Presidente e representante máximo da OAGB.', 'Representar a Ordem, liderar a Direção Nacional e o Conselho Nacional.', 3),
('Direção Nacional', 'Órgão executivo.', 'Gerir o dia a dia da Ordem, executar as deliberações da AG.', 4),
('Conselho Nacional', 'Órgão consultivo e estratégico.', 'Pronunciar-se sobre assuntos de interesse nacional e da profissão.', 5),
('Secretário-Geral', 'Responsável administrativo.', 'Garantir o funcionamento administrativo da Ordem.', 6),
('Conselho de Deontologia e Ética', 'Órgão fiscalizador da ética.', 'Zelar pelo cumprimento das regras deontológicas.', 7),
('Tribunal de Ética e Disciplina', 'Órgão jurisdicional.', 'Julgar infrações disciplinares e aplicar sanções.', 8);


-- 2. Guia de Estágio Profissional
CREATE TABLE IF NOT EXISTS `guia_estagios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `categoria` varchar(100) NOT NULL, -- Ex: 'Requisitos', 'Dispensas', 'Deveres'
  `conteudo` text NOT NULL,
  `ordem` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `guia_estagios` (`titulo`, `categoria`, `conteudo`, `ordem`) VALUES
('Requisitos Básicos para o Estágio', 'Requisitos', '<p>Inscrição obrigatória na Ordem, prestação de Juramento profissional, registo do Escritório patrono e uso de toga obrigatório em tribunal.</p>', 1),
('A Prática do Advogado Estagiário', 'Deveres', '<p>O estagiário trabalha obrigatoriamente sob supervisão de um Patrono. Não lhe é permitido atuar plenamente de forma isolada em determinados processos complexos.</p>', 2),
('Dispensas de Estágio', 'Dispensas', '<p>A dispensa de estágio aplica-se a: Magistrados de carreira, Doutores em Direito e Professores de Direito com vasta experiência comprovada.</p>', 3);


-- 3. Deontologia e Honorários
CREATE TABLE IF NOT EXISTS `regras_deontologia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('Honorarios','Atos Proprios','Deveres','Sancoes') NOT NULL,
  `conteudo` text NOT NULL,
  `ordem` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `regras_deontologia` (`titulo`, `tipo`, `conteudo`, `ordem`) VALUES
('Regra dos Honorários Justos', 'Honorarios', '<p>Os honorários devem ser justos, moderados e baseados no tempo gasto, na complexidade do assunto e nos resultados obtidos. <strong>É estritamente proibida a partilha de honorários com não advogados.</strong></p>', 1),
('O Modelo 1/3 - 1/3 - 1/3', 'Honorarios', '<p>O pagamento típico e recomendado na OAGB consiste em 1/3 do valor no início do mandato, 1/3 durante o andamento do processo e o remanescente 1/3 no final.</p>', 2),
('Atos Próprios do Advogado', 'Atos Proprios', '<p>Incluem, em exclusividade, a representação judicial, a consultoria jurídica, elaboração de contratos, negociação de créditos e a defesa em processos penais.</p>', 3),
('Regime Sancionatório', 'Sancoes', '<p>As infrações implicam sanções como: Advertência, Repreensão, Multa, Formação obrigatória, Suspensão ou Expulsão. A responsabilidade disciplinar é independente das responsabilidades civil e criminal.</p>', 4),
('Princípio da Independência Técnica', 'Deveres', '<p>Ainda que o advogado exerça a profissão através de Contrato de Trabalho para uma entidade, o mesmo mantém total independência técnica e de isenção.</p>', 5);

-- 4. Contexto Internacional e Acesso à Justiça
-- Reaproveitando a estrutura previamente criada "informacao_juridica" e "info_cidadaos", 
-- inserindo mais dados precisos.

INSERT INTO `informacao_juridica` (`categoria`, `titulo`, `conteudo`, `data_publicacao`) VALUES
('CPLP', 'Advocacia no Espaço Lusófono', 'O papel das Ordens de Advogados da CPLP na uniformização e na cooperação jurídica transnacional, facilitando a troca de jurisprudência e o reconhecimento mútuo...', '2024-05-01');

INSERT INTO `info_cidadaos` (`titulo`, `slug`, `conteudo`, `ordem`) VALUES
('Como Encontrar um Advogado?', 'encontrar-advogado', '<p>A OAGB mantém uma lista atualizada de advogados inscritos. O acesso a um profissional capacitado é um direito fundamental de todos os cidadãos da Guiné-Bissau para garantir a administração justa da lei.</p>', 4),
('Acesso à Informação Pública', 'acesso-informacao-publica', '<p>No âmbito da defesa dos cidadãos, os advogados detêm o direito consagrado de solicitar documentos administrativos e de contestar ativamente eventuais recusas do Estado no acesso à informação.</p>', 5);
