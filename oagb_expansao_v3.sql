-- ================================================================
-- OAGB Website Expansion v3.0 — Base de Dados
-- Novas tabelas + dados fictícios contextualizados à Guiné-Bissau
-- SEGURO: Usa CREATE TABLE IF NOT EXISTS (não afeta tabelas existentes)
-- ================================================================

-- 1. DEPARTAMENTOS DE CONTACTO
CREATE TABLE IF NOT EXISTS `departamentos_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `morada` text,
  `telefone` varchar(100),
  `email` varchar(100),
  `horario` varchar(255),
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `departamentos_contactos` (`titulo`, `morada`, `telefone`, `email`, `horario`, `ordem`) VALUES
('Sede — Direção Nacional', 'Bairro de Santa Luzia, Rua Principal, Bissau', '+245 95 123 4567', 'geral@oagb.gw', 'Seg–Sex: 08:30–15:30', 1),
('Conselho de Deontologia e Ética', 'Avenida Pansau Na Isna, Bissau', '+245 96 987 6543', 'deontologia@oagb.gw', 'Seg–Sex: 09:00–14:00', 2),
('Tribunal de Ética e Disciplina', 'Palácio de Justiça, Piso 2, Bissau', '+245 95 222 3344', 'tribunal.etica@oagb.gw', 'Seg–Sex: 09:00–13:00', 3),
('Centro de Estágio e Formação', 'Rua Eduardo Mondlane, Prédio M, Bissau', '+245 95 555 1234', 'formacao@oagb.gw', 'Seg–Sex: 09:00–16:00', 4),
('Gabinete de Acesso ao Direito', 'Palácio de Justiça, R/C, Bissau', '+245 95 111 2233', 'acesso.direito@oagb.gw', 'Seg–Sex: 08:30–14:00', 5),
('Secretariado-Geral', 'Bairro de Santa Luzia, Bissau', '+245 96 444 5566', 'secretariado@oagb.gw', 'Seg–Sex: 08:30–15:00', 6);

-- 2. REVISTAS DA OAGB
CREATE TABLE IF NOT EXISTS `revistas_oagb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `edicao` varchar(50) NOT NULL,
  `ano` int(4) NOT NULL,
  `data_publicacao` date NOT NULL,
  `descricao` text,
  `capa_imagem` varchar(255) DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `revistas_oagb` (`titulo`, `edicao`, `ano`, `data_publicacao`, `descricao`) VALUES
('O Papel da Justiça na Consolidação do Estado de Direito', 'Nº 12', 2024, '2024-06-01', 'Análise do papel dos advogados na construção democrática da Guiné-Bissau.'),
('Advocacia e os Desafios da OHADA na Guiné-Bissau', 'Nº 11', 2023, '2023-10-15', 'Edição especial sobre a harmonização do direito comercial e o impacto dos Atos Uniformes.'),
('Direitos Humanos e Advocacia na África Ocidental', 'Nº 10', 2023, '2023-05-01', 'A Carta de Banjul e a defesa dos direitos humanos pelo advogado guineense.'),
('Os Novos Estatutos da OAGB — Análise e Comentários', 'Nº 9', 2022, '2022-12-01', 'Análise dos 219 artigos dos Estatutos de 2018 e o seu impacto na profissão.'),
('A Independência da Advocacia — 30 Anos de OAGB', 'Nº 8', 2022, '2022-06-15', 'Edição comemorativa dos 30 anos da constituição da Ordem.'),
('Estágio Profissional e Formação Contínua', 'Nº 7', 2021, '2021-11-01', 'Orientações para os cursos de formação de estagiários.');

-- 3. LEGISLAÇÃO NACIONAL
CREATE TABLE IF NOT EXISTS `legislacao_nacional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `diploma_legal` varchar(255) DEFAULT NULL,
  `data_publicacao` date DEFAULT NULL,
  `resumo` text,
  `conteudo` text,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `legislacao_nacional` (`categoria`, `titulo`, `diploma_legal`, `data_publicacao`, `resumo`, `ordem`) VALUES
('Constituição', 'Constituição da República da Guiné-Bissau', 'Aprovada em 16 de Maio de 1984', '1984-05-16', 'Lei fundamental do Estado soberano, democrático, laico e unitário. Estabelece os direitos, liberdades e garantias dos cidadãos. Revista em 1991, 1993 e 1996.', 1),
('Direito Penal', 'Código Penal', 'Decreto-Lei n.º 4/93, de 13 de Outubro', '1993-10-13', 'Modernizou a legislação penal guineense, adaptando-a à realidade de um Estado independente e democrático.', 2),
('Direito Penal', 'Código de Processo Penal', 'Decreto-Lei n.º 5/93, de 13 de Outubro', '1993-10-13', 'Estabelece as normas do procedimento criminal, prazos, competências e garantias processuais.', 3),
('Direito Civil', 'Código Civil', 'Base no Código Civil de 1966', '1966-01-01', 'Ordenamento civil com raízes no direito português, com alterações posteriores ao nível do direito da família e propriedade.', 4),
('Direito do Trabalho', 'Lei Geral do Trabalho', 'Lei n.º 2/86', '1986-04-05', 'Regulamenta as relações laborais, direitos do trabalhador e do empregador na Guiné-Bissau.', 5),
('Direito Fundiário', 'Lei de Terras', 'Lei n.º 5/98', '1998-04-23', 'Regime jurídico dos solos. Define propriedade estatal e comunitária, concessões e uso da terra.', 6),
('Direito Comercial', 'Direito Comercial (OHADA)', 'Atos Uniformes OHADA', '1996-02-20', 'O antigo Código Comercial português foi substituído pelo sistema harmonizado da OHADA, vigente desde 1996.', 7),
('Direito da Família', 'Lei da Família', 'Lei n.º 10/92', '1992-10-06', 'Regula o casamento, filiação, adoção, tutela e responsabilidades parentais.', 8),
('Estatuto da Advocacia', 'Estatutos da OAGB (2018)', 'Aprovados em 2018 — 6 títulos, 219 artigos', '2018-01-01', 'Modernização dos estatutos após ~30 anos. Reforço da independência, internacionalização e ética profissional.', 9),
('Direito Fiscal', 'Código Geral Tributário', 'Aprovado por decreto', '2010-01-01', 'Regime fiscal aplicável a pessoas singulares e coletivas na Guiné-Bissau.', 10);

-- 4. LEGISLAÇÃO INTERNACIONAL
CREATE TABLE IF NOT EXISTS `legislacao_internacional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organizacao` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_adocao` date DEFAULT NULL,
  `data_ratificacao_gb` date DEFAULT NULL,
  `resumo` text,
  `conteudo` text,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `legislacao_internacional` (`organizacao`, `titulo`, `data_adocao`, `data_ratificacao_gb`, `resumo`, `link_externo`, `ordem`) VALUES
('OHADA', 'Tratado de Port-Louis (Tratado OHADA)', '1993-10-17', '1994-01-15', 'Tratado que cria a Organização para a Harmonização em África do Direito dos Negócios. A Guiné-Bissau ratificou em 1994, com entrada em vigor em 20/02/1996.', 'https://www.ohada.org', 1),
('OHADA', 'Ato Uniforme sobre Direito Comercial Geral', '2010-12-15', NULL, 'Regulamenta o estatuto do comerciante, o registo comercial, o fundo de comércio e as obrigações comerciais em todos os Estados OHADA.', 'https://www.ohada.org', 2),
('OHADA', 'Ato Uniforme sobre Sociedades Comerciais e GIE', '2014-01-30', NULL, 'Estabelece regras para constituição, gestão e dissolução de sociedades comerciais e grupos de interesse económico.', 'https://www.ohada.org', 3),
('OHADA', 'Ato Uniforme sobre Garantias', '2010-12-15', NULL, 'Regula as garantias pessoais (fiança, garantia autónoma) e reais (penhor, hipoteca) no espaço OHADA.', 'https://www.ohada.org', 4),
('OHADA', 'Ato Uniforme sobre Arbitragem', '2017-11-23', NULL, 'Promove a arbitragem como método alternativo de resolução de litígios comerciais.', 'https://www.ohada.org', 5),
('CEDEAO', 'Tratado Revisto da CEDEAO', '1993-07-24', NULL, 'Tratado fundador revisto que estabelece a Comunidade Económica dos Estados da África Ocidental, visando integração económica regional.', 'https://www.ecowas.int', 6),
('CEDEAO', 'Protocolo sobre a Livre Circulação de Pessoas', '1979-05-29', NULL, 'Garante o direito de residência e estabelecimento dos cidadãos dos Estados-membros no espaço CEDEAO.', 'https://www.ecowas.int', 7),
('CEDEAO', 'Tribunal de Justiça da CEDEAO', '1991-07-06', NULL, 'Tribunal supranacional competente para julgar violações de direitos humanos e litígios entre Estados-membros da CEDEAO. Sede em Abuja, Nigéria.', 'https://www.courtecowas.org', 8),
('União Africana', 'Carta Africana dos Direitos Humanos e dos Povos (Carta de Banjul)', '1981-06-27', NULL, 'Instrumento regional de proteção dos direitos humanos adotado pela OUA (agora UA). Consagra direitos civis, políticos, económicos e culturais.', 'https://au.int', 9),
('União Africana', 'Ato Constitutivo da União Africana', '2000-07-11', NULL, 'Instrumento fundador da UA, que substitui a OUA. Promove a unidade, a paz, a segurança e o desenvolvimento do continente.', 'https://au.int', 10),
('CPLP', 'Acordos de Cooperação Jurídica da CPLP', '1998-07-17', NULL, 'Cooperação entre as Ordens de Advogados dos países lusófonos para reconhecimento mútuo e troca de jurisprudência.', 'https://www.cplp.org', 11),
('Direitos Humanos', 'Declaração Universal dos Direitos Humanos', '1948-12-10', NULL, 'Instrumento fundamental adotado pela ONU que proclama os direitos inalienáveis de todos os seres humanos.', 'https://www.un.org/pt', 12),
('Direitos Humanos', 'Convenção sobre os Direitos da Criança', '1989-11-20', '1990-08-20', 'Ratificada pela Guiné-Bissau. Protege os direitos das crianças a nível internacional.', 'https://www.unicef.org', 13);

-- 5. GLOSSÁRIO JURÍDICO
CREATE TABLE IF NOT EXISTS `glossario_juridico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termo` varchar(255) NOT NULL,
  `letra` char(1) NOT NULL,
  `definicao` text NOT NULL,
  `exemplo_uso` text DEFAULT NULL,
  `categoria` enum('Geral','Latinismo','Expressao') DEFAULT 'Geral',
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_letra` (`letra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `glossario_juridico` (`termo`, `letra`, `definicao`, `exemplo_uso`, `categoria`) VALUES
('Ação', 'A', 'Meio pelo qual alguém recorre ao tribunal para defender um direito.', 'O advogado propôs uma ação contra o devedor.', 'Geral'),
('Acórdão', 'A', 'Decisão proferida por um tribunal coletivo.', 'O acórdão do Tribunal da Relação foi favorável ao réu.', 'Geral'),
('Advogado', 'A', 'Profissional habilitado a representar e defender cidadãos perante os tribunais.', 'O advogado apresentou as suas alegações finais.', 'Geral'),
('Agravo', 'A', 'Recurso judicial contra decisões interlocutórias.', 'Foi interposto agravo da decisão do juiz.', 'Geral'),
('Bastonário', 'B', 'Presidente da Ordem dos Advogados. Representa a classe e lidera a instituição.', 'O Bastonário convocou uma reunião extraordinária.', 'Geral'),
('Caução', 'C', 'Garantia prestada para assegurar o cumprimento de uma obrigação.', 'O tribunal exigiu caução para a libertação provisória.', 'Geral'),
('Citação', 'C', 'Ato pelo qual se dá conhecimento ao réu de que foi proposta contra ele uma ação.', 'A citação foi entregue por oficial de justiça.', 'Geral'),
('Contumácia', 'C', 'Situação do arguido que se subtrai à justiça, não comparecendo em tribunal.', 'O arguido foi declarado em contumácia.', 'Geral'),
('Dano', 'D', 'Prejuízo causado a alguém, podendo ser material ou moral.', 'O tribunal condenou ao pagamento do dano causado.', 'Geral'),
('Deontologia', 'D', 'Conjunto de regras éticas que regem o exercício de uma profissão.', 'O Código de Deontologia dos Advogados é de cumprimento obrigatório.', 'Geral'),
('Edital', 'E', 'Aviso público afixado ou publicado para dar conhecimento de um ato.', 'O edital foi publicado no Boletim Oficial.', 'Geral'),
('Fiança', 'F', 'Garantia pessoal em que alguém se responsabiliza pelo cumprimento de uma obrigação de outrem.', 'O fiador prestou fiança pelo pagamento da dívida.', 'Geral'),
('Grau de Jurisdição', 'G', 'Nível hierárquico dos tribunais na organização judiciária.', 'O recurso seguiu para o tribunal de segundo grau.', 'Geral'),
('Habeas Corpus', 'H', 'Providência que garante a liberdade individual contra detenções ilegais ou abusivas.', 'O advogado interpôs habeas corpus pela libertação do detido.', 'Latinismo'),
('Inventário', 'I', 'Processo judicial ou administrativo de partilha de bens de uma herança.', 'O inventário dos bens do falecido demorou dois anos.', 'Geral'),
('Juiz', 'J', 'Magistrado judicial com competência para julgar e decidir causas.', 'O juiz proferiu a sentença após ouvir as partes.', 'Geral'),
('Lacuna da Lei', 'L', 'Ausência de regulamentação legal para uma situação concreta.', 'O juiz recorreu à analogia para colmatar a lacuna da lei.', 'Geral'),
('Mandato', 'M', 'Contrato pelo qual alguém confere poderes a outrem para agir em seu nome.', 'O cliente conferiu mandato ao advogado mediante procuração.', 'Geral'),
('Nulidade', 'N', 'Vício que torna um ato jurídico sem efeito.', 'O contrato foi declarado nulo por falta de forma legal.', 'Geral'),
('Oposição', 'O', 'Meio processual pelo qual alguém se opõe a uma pretensão.', 'O réu deduziu oposição à execução.', 'Geral'),
('Procuração', 'P', 'Documento pelo qual se conferem poderes de representação a um advogado.', 'A procuração forense é obrigatória para o advogado agir em juízo.', 'Geral'),
('Queixa', 'Q', 'Participação de um crime feita pelo ofendido às autoridades.', 'A vítima apresentou queixa na polícia judiciária.', 'Geral'),
('Recurso', 'R', 'Meio processual para impugnar uma decisão judicial perante tribunal superior.', 'Foi interposto recurso da sentença de primeira instância.', 'Geral'),
('Sentença', 'S', 'Decisão do juiz que põe termo a um processo.', 'A sentença condenou o réu ao pagamento de indemnização.', 'Geral'),
('Tribunal', 'T', 'Órgão do Estado com poder de julgar e aplicar a lei.', 'O caso foi remetido ao Tribunal Regional de Bissau.', 'Geral'),
('Usucapião', 'U', 'Aquisição de propriedade pela posse prolongada e contínua.', 'Após 20 anos de posse pacífica, adquiriu por usucapião.', 'Geral'),
('Veredito', 'V', 'Decisão de um júri sobre a culpabilidade do arguido.', 'O veredito do júri foi de absolvição.', 'Geral'),
('Ad hoc', 'A', 'Expressão latina que significa "para este efeito" ou "para esta finalidade".', 'Foi nomeado um advogado ad hoc para representar o menor.', 'Latinismo'),
('In dubio pro reo', 'I', 'Em caso de dúvida, a decisão deve favorecer o réu.', 'O tribunal absolveu o arguido com base no princípio in dubio pro reo.', 'Latinismo'),
('Habeas data', 'H', 'Direito de acesso e retificação de dados pessoais em registos públicos.', 'Invocou o habeas data para corrigir os seus dados no registo civil.', 'Latinismo'),
('De facto', 'D', 'Situação que existe na prática, independentemente do reconhecimento legal.', 'A união de facto não confere os mesmos direitos que o casamento.', 'Latinismo');

-- 6. BIBLIOTECA OAGB
CREATE TABLE IF NOT EXISTS `biblioteca_oagb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `subcategoria` varchar(100) DEFAULT NULL,
  `ano_publicacao` int(4) DEFAULT NULL,
  `editora` varchar(255) DEFAULT NULL,
  `resumo` text,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `biblioteca_oagb` (`titulo`, `autor`, `categoria`, `ano_publicacao`, `resumo`, `link_externo`) VALUES
('Manual de Introdução ao Direito OHADA', 'Boris Martor et al.', 'Doutrina', 2015, 'Obra de referência que apresenta os princípios, atos uniformes e a jurisprudência da CCJA.', 'https://www.ohada.org'),
('Guia do Advogado Estagiário — OAGB', 'OAGB', 'Manuais de Formação', 2023, 'Manual prático distribuído nos cursos de formação de estagiários, com orientações e minutas.', NULL),
('Carta Africana dos Direitos Humanos — Texto Anotado', 'Comissão Africana DHP', 'Direitos Humanos', 2020, 'Edição anotada da Carta de Banjul com jurisprudência da Comissão Africana.', 'https://au.int'),
('Código Penal da Guiné-Bissau — Comentado', 'Prof. Doutor J. P. Correia', 'Doutrina', 2019, 'Análise artigo a artigo do Decreto-Lei 4/93 com notas de jurisprudência nacional.', NULL),
('Constituição da República da Guiné-Bissau', 'Assembleia Nacional Popular', 'Legislação', 1984, 'Texto integral da Constituição de 1984 com as revisões de 1991, 1993 e 1996.', 'https://www.parlamento.gw'),
('Relatório sobre o Estado de Direito na Guiné-Bissau', 'UNIOGBIS / ONU', 'Relatórios', 2022, 'Relatório abrangente sobre a situação dos direitos humanos e estado de direito no país.', 'https://unmissions.org'),
('Atos Uniformes OHADA — Compilação Oficial', 'Secretaria Permanente OHADA', 'Legislação', 2023, 'Compilação atualizada dos 10 Atos Uniformes em vigor no espaço OHADA.', 'https://www.ohada.org'),
('Protocolos da CEDEAO — Textos Fundamentais', 'Comissão da CEDEAO', 'Legislação', 2021, 'Compilação dos protocolos-chave: Livre Circulação, Tribunal de Justiça, Democracia e Boa Governação.', 'https://www.ecowas.int');

-- 7. INFORMAÇÃO AO CIDADÃO
CREATE TABLE IF NOT EXISTS `info_cidadaos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icone` varchar(50) DEFAULT 'fas fa-info-circle',
  `conteudo` text NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `info_cidadaos` (`titulo`, `slug`, `icone`, `conteudo`, `ordem`) VALUES
('O que é o Acesso ao Direito?', 'acesso-ao-direito', 'fas fa-balance-scale', '<p>O acesso ao direito e aos tribunais é um direito fundamental consagrado na Constituição da Guiné-Bissau. Os cidadãos com insuficiência económica comprovada têm direito a apoio judiciário, que pode incluir a nomeação gratuita de um advogado.</p><p>A OAGB colabora com o Estado para garantir que ninguém fique sem defesa por razões financeiras.</p>', 1),
('Direitos Fundamentais do Cidadão', 'direitos-fundamentais', 'fas fa-shield-alt', '<p>A Constituição da República garante a todos os cidadãos direitos e liberdades fundamentais, incluindo: direito à vida e integridade física, liberdade de expressão, direito de reunião e associação, direito ao trabalho, à educação e à saúde.</p><p>A Carta Africana dos Direitos Humanos e dos Povos (Carta de Banjul), ratificada pela Guiné-Bissau, reforça estas garantias a nível regional.</p>', 2),
('Como Encontrar um Advogado?', 'encontrar-advogado', 'fas fa-search', '<p>A OAGB mantém uma lista atualizada de advogados inscritos e em exercício. Pode pesquisar por nome, localidade ou especialidade através da nossa <a href="pesquisa-advogados.php">página de pesquisa</a>.</p><p>O acesso a um advogado é um direito fundamental. Nenhum cidadão pode ser julgado sem defesa técnica.</p>', 3),
('O que é a Ordem dos Advogados?', 'o-que-e-a-ordem', 'fas fa-landmark', '<p>A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma pessoa coletiva de direito privado e utilidade pública, constituída por escritura pública em 1991. A sua missão é a defesa do Estado de Direito, a proteção dos direitos humanos e a regulação ética e disciplinar da profissão de advogado.</p>', 4),
('Glossário Jurídico para Cidadãos', 'glossario', 'fas fa-book-open', '<p>Consulte o nosso <a href="glossario-juridico.php">Glossário de Termos Jurídicos</a> — uma ferramenta para combater a iliteracia jurídica, explicando em linguagem acessível os conceitos e expressões usados na prática da advocacia e dos tribunais.</p>', 5);
