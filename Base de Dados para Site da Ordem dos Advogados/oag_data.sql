-- ==============================================================================
-- DADOS DE EXEMPLO - ORDEM DOS ADVOGADOS DA GUINÉ-BISSAU (OAGB)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Informações Institucionais
INSERT INTO `instituicao_info` (`missao`, `visao`, `valores`, `historia`, `email_geral`, `telefone_geral`, `endereco`, `horario_funcionamento`) VALUES
('Defender o Estado de Direito, os direitos, liberdades e garantias dos cidadãos e colaborar na administração da justiça.', 
 'Ser uma instituição de referência na promoção da justiça e na defesa intransigente dos direitos humanos na Guiné-Bissau.', 
 'Independência, Ética, Transparência, Integridade, Competência.', 
 'A Ordem dos Advogados da Guiné-Bissau foi criada para regular a profissão e defender os interesses dos seus membros e da sociedade.', 
 'geral@oagb.gw', '+245 95 123 45 67', 'Avenida Amílcar Cabral, Bissau, Guiné-Bissau', 'Segunda a Sexta: 08:00 - 16:00');

-- 2. Bastonários
INSERT INTO `bastonarios` (`nome_completo`, `biografia`, `data_inicio_mandato`, `data_fim_mandato`, `email_contacto`, `is_atual`) VALUES
('Dr. Januário Pedro', 'Advogado com mais de 20 anos de experiência em Direito Penal e Direitos Humanos.', '2022-01-15', '2025-01-14', 'bastonario@oagb.gw', TRUE),
('Dra. Maria Silva', 'Especialista em Direito Comercial e ex-Bastonária da OAGB.', '2018-01-15', '2022-01-14', 'maria.silva@exemplo.gw', FALSE);

-- 3. Órgãos Diretivos
INSERT INTO `orgaos_diretivos` (`nome`, `descricao`) VALUES
('Conselho Diretivo', 'Órgão executivo da Ordem dos Advogados.'),
('Conselho Jurisdicional', 'Órgão disciplinar da Ordem dos Advogados.'),
('Assembleia Geral', 'Órgão deliberativo máximo da Ordem.');

-- 4. Advogados (Membros)
INSERT INTO `advogados` (`nome_completo`, `numero_cedula`, `data_inscricao`, `email_profissional`, `telefone_profissional`, `cidade`, `is_patrono`) VALUES
('Dr. Carlos Mendes', 'OAGB-001/2005', '2005-03-10', 'carlos.mendes@advogados.gw', '+245 96 111 22 33', 'Bissau', TRUE),
('Dra. Fátima Gomes', 'OAGB-045/2010', '2010-07-22', 'fatima.gomes@advogados.gw', '+245 95 444 55 66', 'Bissau', TRUE),
('Dr. João Tavares', 'OAGB-112/2018', '2018-11-05', 'joao.tavares@advogados.gw', '+245 96 777 88 99', 'Bafatá', FALSE);

-- 5. Membros dos Órgãos
INSERT INTO `membros_orgaos` (`orgao_id`, `nome_completo`, `cargo`, `data_inicio_mandato`, `data_fim_mandato`) VALUES
(1, 'Dr. Januário Pedro', 'Bastonário / Presidente', '2022-01-15', '2025-01-14'),
(1, 'Dra. Fátima Gomes', 'Vice-Presidente', '2022-01-15', '2025-01-14'),
(2, 'Dr. Carlos Mendes', 'Presidente do Conselho Jurisdicional', '2022-01-15', '2025-01-14');

-- 6. Comissões Especializadas
INSERT INTO `comissoes` (`nome`, `descricao`, `objetivos`, `data_criacao`, `email_contacto`) VALUES
('Comissão de Direitos Humanos', 'Acompanha e denuncia violações de direitos humanos.', 'Promover a defesa dos direitos fundamentais na Guiné-Bissau.', '2015-05-20', 'direitoshumanos@oagb.gw'),
('Comissão de Direito Comercial', 'Analisa legislação comercial e empresarial.', 'Apoiar o desenvolvimento do ambiente de negócios.', '2018-09-10', 'comercial@oagb.gw');

-- 7. Membros das Comissões
INSERT INTO `membros_comissoes` (`comissao_id`, `advogado_id`, `cargo`, `data_entrada`) VALUES
(1, 1, 'Presidente', '2022-02-01'),
(1, 3, 'Vogal', '2022-02-01'),
(2, 2, 'Presidente', '2022-03-15');

-- 8. Cooperação Internacional
INSERT INTO `parcerias_internacionais` (`entidade_parceira`, `pais`, `tipo_acordo`, `objetivo`, `data_assinatura`, `data_validade`) VALUES
('Ordem dos Advogados de Portugal', 'Portugal', 'Protocolo de Cooperação', 'Intercâmbio de conhecimentos e formação contínua.', '2019-10-12', '2024-10-12'),
('União Internacional dos Advogados (UIA)', 'Internacional', 'Filiação', 'Participação em fóruns internacionais de advocacia.', '2016-04-05', NULL);

-- 9. Categorias de Anúncios
INSERT INTO `categorias_anuncios` (`nome`, `slug`) VALUES
('Notícias', 'noticias'),
('Comunicados', 'comunicados'),
('Editais', 'editais'),
('Eventos', 'eventos');

-- 10. Utilizadores (Backend)
INSERT INTO `utilizadores` (`nome`, `email`, `password_hash`, `role`, `advogado_id`) VALUES
('Admin Sistema', 'admin@oagb.gw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SuperAdmin', NULL),
('Secretaria OAGB', 'secretaria@oagb.gw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', NULL);

-- 11. Anúncios
INSERT INTO `anuncios` (`categoria_id`, `titulo`, `slug`, `resumo`, `conteudo`, `data_publicacao`, `status`, `autor_id`) VALUES
(1, 'Abertura do Ano Judicial 2024', 'abertura-ano-judicial-2024', 'Cerimónia solene de abertura do ano judicial.', '<p>A Ordem dos Advogados informa que a cerimónia de abertura do ano judicial terá lugar no dia...</p>', '2024-01-10 09:00:00', 'Publicado', 2),
(2, 'Comunicado sobre as Custas Judiciais', 'comunicado-custas-judiciais', 'Posição da OAGB sobre a nova tabela de custas.', '<p>O Conselho Diretivo da OAGB vem por este meio comunicar a sua posição oficial sobre...</p>', '2024-02-15 14:30:00', 'Publicado', 1);

-- 12. Formações
INSERT INTO `formacoes` (`titulo`, `descricao`, `objetivos`, `publico_alvo`, `formador`, `carga_horaria`, `data_inicio`, `data_fim`, `local`, `modalidade`, `vagas_totais`, `status`) VALUES
('Curso Prático de Processo Penal', 'Abordagem prática aos trâmites do processo penal guineense.', 'Capacitar advogados e estagiários na prática processual penal.', 'Advogados e Estagiários', 'Dr. Carlos Mendes', 20, '2024-05-10', '2024-05-14', 'Sede da OAGB', 'Presencial', 30, 'Inscrições Abertas'),
('Deontologia Profissional', 'Princípios éticos e regras da profissão.', 'Conhecer o estatuto e regulamentos disciplinares.', 'Estagiários (Obrigatório)', 'Dra. Fátima Gomes', 15, '2024-06-01', '2024-06-05', 'Online (Zoom)', 'Online', 50, 'Planeada');

-- 13. Estagiários
INSERT INTO `estagiarios` (`nome_completo`, `numero_processo`, `email`, `telefone`, `universidade_origem`, `ano_conclusao_licenciatura`, `data_inscricao_ordem`, `status`) VALUES
('Ana Silva Fernandes', 'EST-001/2023', 'ana.fernandes@email.com', '+245 95 111 22 33', 'Faculdade de Direito de Bissau', 2022, '2023-09-01', 'Ativo'),
('Mário Costa', 'EST-002/2023', 'mario.costa@email.com', '+245 96 444 55 66', 'Universidade Amílcar Cabral', 2022, '2023-09-05', 'Ativo');

-- 14. Estágios
INSERT INTO `estagios` (`estagiario_id`, `patrono_id`, `data_inicio`, `data_fim_prevista`, `fase_atual`, `status`) VALUES
(1, 1, '2023-10-01', '2025-04-01', '1ª Fase', 'Em Curso'),
(2, 2, '2023-10-15', '2025-04-15', '1ª Fase', 'Em Curso');

-- 15. Avaliações de Estágio
INSERT INTO `avaliacoes_estagio` (`estagio_id`, `avaliador_id`, `data_avaliacao`, `tipo_avaliacao`, `nota_conhecimento_juridico`, `nota_deontologia`, `nota_pratica_processual`, `nota_assiduidade`, `nota_final`, `parecer_qualitativo`, `resultado`) VALUES
(1, 1, '2024-04-01', 'Relatório Intercalar', 16.5, 18.0, 15.0, 19.0, 17.1, 'A estagiária tem demonstrado grande empenho e evolução técnica.', 'Aprovado');

SET FOREIGN_KEY_CHECKS = 1;
