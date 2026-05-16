-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 09:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `korakund_ordem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role` enum('superadmin','admin','editor') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin@oagb.gw', '$2y$10$ZswfyQEheGJLvbPLet7Z1OkMdiijX9eXj9f/pYwaXjKA1u3Gx0Ou6', 'Administrador OAGB', NULL, 'superadmin', '2026-05-11 17:01:23', '2026-03-20 10:56:20'),
(2, 'editor@oagb.gw', '$2y$10$0Eztb9dFmGnZZm0Whu639O3XM0IkdmO8ofsXm7Jzv0hpQiStNZJ6q', 'Editor de Teste OAGB', 'editor@oagb.gw', 'editor', NULL, '2026-05-04 23:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `advogados`
--

CREATE TABLE `advogados` (
  `id` int(11) NOT NULL,
  `numero_registo` varchar(20) NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `genero` enum('M','F') NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nacionalidade` varchar(50) DEFAULT 'Guineense',
  `bi_passaporte` varchar(50) DEFAULT NULL,
  `regiao` varchar(50) NOT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('ativo','suspenso','inativo') DEFAULT 'ativo',
  `data_inscricao` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `last_portal_login` datetime DEFAULT NULL,
  `sociedade_id` int(11) DEFAULT NULL,
  `is_sociedade_gestor` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advogados`
--

INSERT INTO `advogados` (`id`, `numero_registo`, `nome_completo`, `genero`, `data_nascimento`, `nacionalidade`, `bi_passaporte`, `regiao`, `localidade`, `morada`, `telefone`, `email`, `status`, `data_inscricao`, `observacoes`, `ordem_exibicao`, `foto`, `created_at`, `updated_at`, `password`, `last_portal_login`, `sociedade_id`, `is_sociedade_gestor`) VALUES
(1, '001/2020', 'António Silva Santosewr', 'M', NULL, 'Guineense', NULL, 'SAB', 'Bissau', NULL, '+245 966 123 456', 'antonio.santos@email.gw', 'ativo', '2020-01-15', NULL, 0, NULL, '2025-06-09 15:31:51', '2026-05-04 23:16:45', '$2y$10$0Eztb9dFmGnZZm0Whu639O3XM0IkdmO8ofsXm7Jzv0hpQiStNZJ6q', NULL, NULL, 0),
(2, '002/2020', 'Maria Fernanda Gomes', 'F', NULL, 'Guineense', NULL, 'Bafatá', 'Bafatá', NULL, '+245 966 789 123', 'maria.gomes@email.gw', 'ativo', '2020-02-20', NULL, 0, NULL, '2025-06-09 15:31:51', '2025-06-09 15:31:51', NULL, NULL, NULL, 0),
(3, '003/2021', 'João Carlos Mendes', 'M', NULL, 'Guineense', NULL, 'Cacheu', 'Cacheu', NULL, '+245 966 456 789', 'joao.mendes@email.gw', 'ativo', '2021-03-10', NULL, 0, NULL, '2025-06-09 15:31:51', '2025-06-09 15:31:51', NULL, NULL, NULL, 0),
(4, 'OAGB-001/2005', 'Dr. Carlos Mendes', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, '+245 96 111 22 33', 'carlos.mendes@advogados.gw', 'ativo', '2005-03-10', NULL, 0, NULL, '2026-03-24 19:18:51', '2026-03-24 19:18:51', NULL, NULL, NULL, 0),
(5, 'OAGB-045/2010', 'Dra. Fátima Gomes', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, '+245 95 444 55 66', 'fatima.gomes@advogados.gw', 'ativo', '2010-07-22', NULL, 0, NULL, '2026-03-24 19:18:51', '2026-03-24 19:18:51', NULL, NULL, NULL, 0),
(6, 'OAGB-112/2018', 'Dr. João Tavares', 'M', NULL, 'Guineense', NULL, 'Bafatá', NULL, NULL, '+245 96 777 88 99', 'joao.tavares@advogados.gw', 'ativo', '2018-11-05', NULL, 0, NULL, '2026-03-24 19:18:51', '2026-03-24 19:18:51', NULL, NULL, NULL, 0),
(7, 'CP-001/24', 'Dr. Carlos Buampé', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, NULL, 'carlos.buampe@email.gw', 'ativo', '2020-01-01', NULL, 0, NULL, '2026-05-06 20:16:02', '2026-05-06 20:16:02', NULL, NULL, NULL, 0),
(8, 'CP-002/24', 'Dra. Maria Siga', 'F', NULL, 'Guineense', NULL, 'Bafatá', NULL, NULL, NULL, 'maria.siga@email.gw', 'ativo', '2018-05-15', NULL, 0, NULL, '2026-05-06 20:16:02', '2026-05-06 20:16:02', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `advogados_estagiarios`
--

CREATE TABLE `advogados_estagiarios` (
  `id` int(11) NOT NULL,
  `numero_registo` varchar(20) NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `genero` enum('M','F') NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nacionalidade` varchar(50) DEFAULT 'Guineense',
  `bi_passaporte` varchar(50) DEFAULT NULL,
  `regiao` varchar(50) NOT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `orientador_id` int(11) DEFAULT NULL,
  `sociedade_id` int(11) DEFAULT NULL,
  `fase_estagio` enum('instrucao','pratica','concluido') DEFAULT 'instrucao',
  `data_inicio_estagio` date NOT NULL,
  `data_fim_estagio` date DEFAULT NULL,
  `status` enum('ativo','concluido','cancelado','pendente_aceitacao') DEFAULT 'pendente_aceitacao',
  `data_resposta_vinculo` datetime DEFAULT NULL,
  `motivo_recusa` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `last_portal_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advogados_estagiarios`
--

INSERT INTO `advogados_estagiarios` (`id`, `numero_registo`, `nome_completo`, `genero`, `data_nascimento`, `nacionalidade`, `bi_passaporte`, `regiao`, `localidade`, `morada`, `telefone`, `email`, `orientador_id`, `sociedade_id`, `fase_estagio`, `data_inicio_estagio`, `data_fim_estagio`, `status`, `data_resposta_vinculo`, `motivo_recusa`, `observacoes`, `foto`, `created_at`, `updated_at`, `password`, `last_portal_login`) VALUES
(1, 'EST-001/24', 'António Lopes', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, NULL, 'antonio.lopes@email.gw', 1, 0, 'instrucao', '2024-01-10', NULL, 'ativo', '2026-05-08 20:21:43', NULL, NULL, NULL, '2026-05-06 20:16:02', '2026-05-08 20:21:43', NULL, NULL),
(2, 'EST-002/24', 'Binta Camará', 'F', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, NULL, 'binta.camara@email.gw', 1, NULL, 'instrucao', '2024-02-15', NULL, 'ativo', NULL, NULL, NULL, NULL, '2026-05-06 20:16:02', '2026-05-06 20:16:02', NULL, NULL),
(3, 'EST-003/24', 'João Djaló', 'M', NULL, 'Guineense', NULL, 'Bafatá', NULL, NULL, NULL, 'joao.djalo@email.gw', 2, NULL, 'instrucao', '2024-03-20', NULL, 'ativo', NULL, NULL, NULL, NULL, '2026-05-06 20:16:02', '2026-05-06 20:16:02', NULL, NULL),
(4, 'EST-MOCK-6482', 'Estagiário de Teste 6482', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, NULL, NULL, 1, NULL, 'instrucao', '2026-05-08', NULL, 'ativo', NULL, NULL, NULL, NULL, '2026-05-08 09:58:55', '2026-05-08 09:58:55', NULL, NULL),
(5, 'EST-MOCK-7463', 'Estagiário de Teste 7463', 'M', NULL, 'Guineense', NULL, 'Bissau', NULL, NULL, NULL, NULL, 1, NULL, 'instrucao', '2026-05-08', NULL, 'ativo', NULL, NULL, NULL, NULL, '2026-05-08 15:48:23', '2026-05-08 15:48:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_evento` datetime NOT NULL,
  `data_fim` date DEFAULT NULL,
  `data_fim_evento` datetime DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `local_evento` varchar(255) DEFAULT NULL,
  `endereco_completo` text DEFAULT NULL,
  `tipo_evento` enum('congresso','conferencia','formacao','reuniao','workshop','palestra','outros') DEFAULT 'outros',
  `organizador` varchar(255) DEFAULT NULL,
  `contacto_info` text DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `link_inscricao` varchar(255) DEFAULT NULL,
  `programa` text DEFAULT NULL,
  `documentos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documentos`)),
  `ficheiro_anexo` varchar(255) DEFAULT NULL,
  `imagem_destaque` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `visualizacoes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'Título para SEO/Facebook',
  `meta_description` text DEFAULT NULL COMMENT 'Descrição para SEO/Facebook',
  `og_image` varchar(255) DEFAULT NULL COMMENT 'Imagem específica para Facebook/Open Graph'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela de eventos e agenda da OAGB';

--
-- Dumping data for table `agenda`
--

INSERT INTO `agenda` (`id`, `titulo`, `descricao`, `data_evento`, `data_fim`, `data_fim_evento`, `hora_inicio`, `hora_fim`, `local_evento`, `endereco_completo`, `tipo_evento`, `organizador`, `contacto_info`, `email_contacto`, `link_inscricao`, `programa`, `documentos`, `ficheiro_anexo`, `imagem_destaque`, `slug`, `destaque`, `ativo`, `visualizacoes`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `og_image`) VALUES
(1, 'IX Congresso dos Advogados Guineenses', 'Congresso anual da Ordem dos Advogados da Guiné-Bissau com palestras e workshops sobre direito contemporâneo.', '2024-06-23 09:00:00', NULL, NULL, NULL, NULL, 'Hotel Dunia, Bissau', NULL, 'congresso', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ix-congresso-advogados-guineenses-2024', 1, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'IX Congresso dos Advogados Guineenses 2024 - OAGB', 'Participe do IX Congresso dos Advogados Guineenses. 23-25 de Junho de 2024 no Hotel Dunia, Bissau.', NULL),
(2, 'Formação sobre Novo Código Civil', 'Workshop intensivo sobre as alterações do novo Código Civil da Guiné-Bissau.', '2024-07-15 14:00:00', NULL, NULL, NULL, NULL, 'Sede da OAGB', NULL, 'formacao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'formacao-novo-codigo-civil-2024', 1, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'Formação sobre Novo Código Civil - OAGB', 'Workshop sobre as principais alterações do novo Código Civil da Guiné-Bissau.', NULL),
(3, 'Assembleia Geral Ordinária', 'Assembleia Geral Ordinária da Ordem dos Advogados da Guiné-Bissau para aprovação de contas e eleições.', '2024-08-10 10:00:00', NULL, NULL, NULL, NULL, 'Auditório da OAGB', NULL, 'reuniao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'assembleia-geral-ordinaria-2024', 0, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'Assembleia Geral Ordinária 2024 - OAGB', 'Assembleia Geral Ordinária da OAGB para aprovação de contas e outras deliberações.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `agenda_imagens`
--

CREATE TABLE `agenda_imagens` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Imagens adicionais para eventos da agenda';

-- --------------------------------------------------------

--
-- Table structure for table `anuncios`
--

CREATE TABLE `anuncios` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `link_texto` varchar(100) DEFAULT 'Saiba mais',
  `imagem` varchar(255) DEFAULT NULL,
  `ficheiro_anexo` varchar(255) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anúncios e avisos da OAGB';

--
-- Dumping data for table `anuncios`
--

INSERT INTO `anuncios` (`id`, `titulo`, `descricao`, `link_url`, `link_texto`, `imagem`, `ficheiro_anexo`, `data_inicio`, `data_fim`, `ordem_exibicao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Inscrições Abertas para Estágio', 'Estão abertas as inscrições para o programa de estágio profissional da OAGB. Prazo até 30 de Agosto.', 'inscricao-ordem.php', 'Inscrever-se', NULL, NULL, '2025-08-01', '2025-08-30', 1, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28'),
(2, 'Renovação de Cédulas Profissionais', 'Todos os advogados devem renovar suas cédulas profissionais até o final do ano.', 'advogados.php', 'Mais informações', NULL, NULL, '2025-08-01', '2025-12-31', 2, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28'),
(3, 'Curso de Atualização em Direito Digital', 'Nova turma do curso de Direito Digital e Proteção de Dados. Vagas limitadas.', 'agenda.php', 'Ver detalhes', NULL, NULL, '2025-08-01', '2025-09-15', 3, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes_estagio`
--

CREATE TABLE `avaliacoes_estagio` (
  `id` int(11) NOT NULL,
  `estagio_id` int(11) NOT NULL,
  `avaliador_id` int(11) NOT NULL,
  `data_avaliacao` date NOT NULL,
  `tipo_avaliacao` enum('Relatório Intercalar','Relatório Final','Prova Escrita','Prova Oral') NOT NULL,
  `nota_final` decimal(4,2) NOT NULL,
  `parecer_qualitativo` text DEFAULT NULL,
  `resultado` enum('Aprovado','Reprovado','Necessita Revisão') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bastonarios`
--

CREATE TABLE `bastonarios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `biografia` text DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `cv_url` varchar(255) DEFAULT NULL,
  `data_inicio_mandato` date NOT NULL,
  `data_fim_mandato` date DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `is_atual` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assinatura_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bastonarios`
--

INSERT INTO `bastonarios` (`id`, `nome_completo`, `biografia`, `foto_url`, `cv_url`, `data_inicio_mandato`, `data_fim_mandato`, `email_contacto`, `is_atual`, `created_at`, `assinatura_url`) VALUES
(1, 'Dr. Januário Pedro Correia', 'Doutor em Direito pela prestigiada Faculdade de Direito da Universidade de Lisboa, o Dr. Januário Pedro Correia é uma das figuras mais proeminentes do panorama jurídico da Guiné-Bissau. Com uma carreira marcada pela excelência académica e pelo rigor profissional, exerce atualmente as funções de Bastonário da Ordem dos Advogados, liderando a classe com uma visão de modernização e integridade.\n\nEspecialista em Direito Bancário e Direito da OHADA, acumulou uma vasta experiência como Diretor Jurídico do Banco da África Ocidental (BAO) e como consultor internacional para instituições de relevo como o Banco Mundial e o FMI. A sua dedicação à causa pública é evidenciada pela coordenação das reformas estruturantes do Código Civil e do Código de Processo Civil da Guiné-Bissau.\n\nAlém da sua atuação institucional, é um académico dedicado, lecionando na Faculdade de Direito de Bissau desde 2004 e contribuindo com diversas obras de referência para a doutrina jurídica lusófona.', 'Foto_Bastonario_Januario_Correia.jpg', 'gestao/assets/uploads/files/Curriculo_Bastonario_Januario_Correia.pdf', '2022-01-15', '2025-01-14', 'bastonario@oagb.gw', 1, '2026-03-24 19:18:51', NULL),
(2, 'Dra. Maria Silva', 'Especialista em Direito Comercial e ex-Bastonária da OAGB.', NULL, NULL, '2018-01-15', '2022-01-14', 'maria.silva@exemplo.gw', 0, '2026-03-24 19:18:51', NULL),
(3, 'Dr. BasÝlio Mancuro Sanca', 'Eleito Bastonßrio da OAGB em janeiro de 2026 para o triÚnio 2026-2028.', NULL, NULL, '2026-01-01', NULL, NULL, 0, '2026-03-30 21:43:31', NULL),
(4, 'Dr. BasÝlio Sanca', 'Exerceu o cargo de Bastonßrio entre 2020 e 2022.', NULL, NULL, '2020-01-01', '2022-01-01', NULL, 0, '2026-03-30 21:43:31', NULL),
(5, 'Dr. Domingos QuadÚ', '<p>Ilustre advogado e antigo Bastonário da OAGB (2018-2020). Faleceu em novembro de 2024.</p>', NULL, NULL, '2018-01-01', '2020-01-01', '', 0, '2026-03-30 21:43:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `biblioteca_oagb`
--

CREATE TABLE `biblioteca_oagb` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL,
  `capa` varchar(255) DEFAULT NULL,
  `ficheiro` varchar(255) DEFAULT NULL,
  `subcategoria` varchar(100) DEFAULT NULL,
  `ano_publicacao` int(4) DEFAULT NULL,
  `editora` varchar(255) DEFAULT NULL,
  `resumo` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `biblioteca_oagb`
--

INSERT INTO `biblioteca_oagb` (`id`, `titulo`, `autor`, `categoria`, `capa`, `ficheiro`, `subcategoria`, `ano_publicacao`, `editora`, `resumo`, `imagem`, `ficheiro_pdf`, `link_externo`, `destaque`, `status`, `created_at`) VALUES
(1, 'Manual de Introdução ao Direito OHADA', 'Boris Martor et al.', 'Doutrina', NULL, NULL, NULL, 2015, NULL, 'Obra de referência que apresenta os princípios, atos uniformes e a jurisprudência da CCJA.', NULL, NULL, 'https://www.ohada.org', 0, 'ativo', '2026-05-05 10:44:41'),
(2, 'Guia do Advogado Estagiário — OAGB', 'OAGB', 'Manuais de Formação', NULL, NULL, NULL, 2023, NULL, 'Manual prático distribuído nos cursos de formação de estagiários, com orientações e minutas.', NULL, NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(3, 'Carta Africana dos Direitos Humanos — Texto Anotado', 'Comissão Africana DHP', 'Direitos Humanos', NULL, NULL, NULL, 2020, NULL, 'Edição anotada da Carta de Banjul com jurisprudência da Comissão Africana.', NULL, NULL, 'https://au.int', 0, 'ativo', '2026-05-05 10:44:41'),
(4, 'Código Penal da Guiné-Bissau — Comentado', 'Prof. Doutor J. P. Correia', 'Doutrina', NULL, NULL, NULL, 2019, NULL, 'Análise artigo a artigo do Decreto-Lei 4/93 com notas de jurisprudência nacional.', NULL, NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(5, 'Constituição da República da Guiné-Bissau', 'Assembleia Nacional Popular', 'Legislação', NULL, NULL, NULL, 1984, NULL, 'Texto integral da Constituição de 1984 com as revisões de 1991, 1993 e 1996.', NULL, NULL, 'https://www.parlamento.gw', 0, 'ativo', '2026-05-05 10:44:41'),
(6, 'Relatório sobre o Estado de Direito na Guiné-Bissau', 'UNIOGBIS / ONU', 'Relatórios', NULL, NULL, NULL, 2022, NULL, 'Relatório abrangente sobre a situação dos direitos humanos e estado de direito no país.', NULL, NULL, 'https://unmissions.org', 0, 'ativo', '2026-05-05 10:44:41'),
(7, 'Atos Uniformes OHADA — Compilação Oficial', 'Secretaria Permanente OHADA', 'Legislação', NULL, NULL, NULL, 2023, NULL, 'Compilação atualizada dos 10 Atos Uniformes em vigor no espaço OHADA.', NULL, NULL, 'https://www.ohada.org', 0, 'ativo', '2026-05-05 10:44:41'),
(8, 'Protocolos da CEDEAO — Textos Fundamentais', 'Comissão da CEDEAO', 'Legislação', NULL, NULL, NULL, 2021, NULL, 'Compilação dos protocolos-chave: Livre Circulação, Tribunal de Justiça, Democracia e Boa Governação.', NULL, NULL, 'https://www.ecowas.int', 0, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `carousel_slides`
--

CREATE TABLE `carousel_slides` (
  `id` int(11) NOT NULL,
  `titulo` varchar(300) NOT NULL,
  `subtitulo` text DEFAULT NULL,
  `imagem` varchar(255) NOT NULL COMMENT 'Caminho da imagem',
  `link_texto` varchar(100) DEFAULT 'Saiba mais',
  `link_url` varchar(255) DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Slides do carousel da página inicial';

--
-- Dumping data for table `carousel_slides`
--

INSERT INTO `carousel_slides` (`id`, `titulo`, `subtitulo`, `imagem`, `link_texto`, `link_url`, `ordem_exibicao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Defesa dos Direitos e Liberdades', 'A Ordem dos Advogados da Guiné-Bissau assegura a excelência jurídica e a proteção dos direitos fundamentais dos cidadãos.', 'close-up-scales-justice-original-azul.jpg', 'Apresentação', 'ordem-dos-advogados.php', 3, 1, '2025-08-06 23:52:15', '2026-05-04 22:01:34'),
(2, 'Cadastro Nacional de Advogados', 'Consulte a lista oficial e atualizada dos advogados e estagiários habilitados ao exercício profissional na Guiné-Bissau.', 'close-up-detail-scales-justice.jpg', 'Pesquisar Advogados', 'pesquisa-advogados.php', 2, 1, '2025-08-06 23:52:15', '2026-03-19 15:41:29'),
(3, 'Transparência e Deontologia', 'Acompanhe as últimas notícias, comunicados, pareceres e deliberações oficiais da Ordem dos Advogados.', 'brass-scales-justice-close-up-view.jpg', 'Ver Publicações', 'noticias.php', 1, 1, '2025-08-06 23:52:15', '2026-05-03 20:14:00');

-- --------------------------------------------------------

--
-- Table structure for table `comissoes`
--

CREATE TABLE `comissoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `presidente` varchar(200) DEFAULT NULL,
  `membros` text DEFAULT NULL,
  `area_atuacao` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comissoes`
--

INSERT INTO `comissoes` (`id`, `nome`, `descricao`, `presidente`, `membros`, `area_atuacao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Comissão de Direitos Humanos', 'Promove o acompanhamento da situação dos direitos humanos na Guiné-Bissau e emite pareceres sobre violações e reformas legislativas.', 'Dr. Serifo Jaló', 'Dra. Maria da Conceição, Dr. Pedro Mendes, Dra. Fatumata Baldé', 'Direitos Humanos', 1, '2026-04-05 16:58:43', '2026-04-05 16:58:43'),
(2, 'Comissão de Ética e Deontologia', 'Zela pelo cumprimento do Código Deontológico dos Advogados e analisa questões de ética profissional.', 'Dra. Aminata Sila', 'Dr. Bakary Camará, Dra. Isabel Gomes, Dr. Umaro Sissoco', 'Ética Profissional', 1, '2026-04-05 16:58:43', '2026-04-05 16:58:43'),
(3, 'Comissão de Formação e Estágio', 'Responsável pela organização de cursos de formação contínua e pelo acompanhamento de advogados estagiários.', 'Dr. João António Lopes', 'Dra. Sónia Viana, Dr. Domingos Simões, Dra. Carla Rodrigues', 'Formação', 1, '2026-04-05 16:58:43', '2026-04-05 16:58:43'),
(4, 'Comissão de Reforma Legislativa', 'Analisa projetos de lei e propõe revisões aos códigos vigentes no país para a sua modernização.', 'Dr. Paulo Kassamá', 'Dra. Rosa Silva, Dr. Nelson Gomes, Dra. Beatriz Tavares', 'Legislação', 1, '2026-04-05 16:58:43', '2026-04-05 16:58:43'),
(5, 'Comissão de Apoio Judiciário', 'Coordena os mecanismos de acesso ao direito e à justiça para os cidadãos economicamente desfavorecidos.', 'Dra. Helena Mendonça', 'Dr. Jorge Biai, Dra. Luísa Furtado, Dr. Mamadu Serifo', 'Apoio Judiciário', 1, '2026-04-05 16:58:43', '2026-04-05 16:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `comissoes_especializadas`
--

CREATE TABLE `comissoes_especializadas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `objetivos` text DEFAULT NULL,
  `data_criacao` date DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `status` enum('Ativa','Inativa') DEFAULT 'Ativa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes_site`
--

CREATE TABLE `configuracoes_site` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `grupo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configurações gerais do site';

--
-- Dumping data for table `configuracoes_site`
--

INSERT INTO `configuracoes_site` (`id`, `chave`, `valor`, `descricao`, `grupo`, `created_at`, `updated_at`) VALUES
(1, 'site_url', 'https://oagb.gw', 'URL principal do site', 'geral', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(2, 'facebook_url', 'https://www.facebook.com/profile.php?id=100087015439692', 'URL da página do Facebook', 'social', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(3, 'youtube_url', '', 'URL do canal do YouTube', 'social', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(4, 'instagram_url', '', 'URL do Instagram', 'social', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(5, 'linkedin_url', '', 'URL do LinkedIn', 'social', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(6, 'twitter_url', '', 'URL do Twitter', 'social', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(7, 'og_default_image', 'img/logo3.png', 'Imagem padrão para compartilhamento', 'seo', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(8, 'site_description', 'Site oficial da Ordem dos Advogados da Guiné-Bissau', 'Descrição padrão do site', 'seo', '2025-06-10 13:30:34', '2025-06-10 13:30:34'),
(9, 'contact_phone', '+245 955 475 889', 'Telefone de contacto principal', 'contacto', '2025-06-10 13:38:48', '2025-06-10 13:38:48'),
(10, 'contact_email', 'info@oagb.gw', 'Email de contacto principal', 'contacto', '2025-06-10 13:38:48', '2025-06-10 13:38:48'),
(11, 'contact_address', 'Rua 15, Bissau, Guiné-Bissau', 'Endereço da sede', 'contacto', '2025-06-10 13:38:48', '2025-06-10 13:38:48'),
(12, 'google_analytics_id', '', 'ID do Google Analytics', 'analytics', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(13, 'facebook_pixel_id', '', 'ID do Facebook Pixel', 'analytics', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(14, 'smtp_host', '', 'Servidor SMTP para envio de emails', 'email', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(15, 'smtp_port', '587', 'Porta SMTP', 'email', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(16, 'smtp_user', '', 'Utilizador SMTP', 'email', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(17, 'smtp_pass', '', 'Password SMTP', 'email', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(18, 'email_from', 'noreply@oagb.gw', 'Email remetente padrão', 'email', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(19, 'manutencao_ativa', '0', 'Site em manutenção (0=não, 1=sim)', 'sistema', '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(20, 'mensagem_manutencao', 'Site em manutenção. Voltamos em breve.', 'Mensagem de manutenção', 'sistema', '2025-09-07 12:55:48', '2025-09-07 12:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `conteudos_paginas`
--

CREATE TABLE `conteudos_paginas` (
  `id` int(11) NOT NULL,
  `pagina` varchar(100) NOT NULL,
  `secao` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `icone` varchar(100) DEFAULT 'fas fa-info-circle',
  `imagem` varchar(255) DEFAULT NULL,
  `conteudo` text DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conteudos_paginas`
--

INSERT INTO `conteudos_paginas` (`id`, `pagina`, `secao`, `titulo`, `icone`, `imagem`, `conteudo`, `arquivo`, `ordem`, `status`, `criado_em`) VALUES
(1, 'deontologia', 'intro', 'Código Deontológico dos Advogados', 'fas fa-balance-scale', NULL, '<p>O exercício da advocacia na Guiné-Bissau rege-se por um rigoroso conjunto de normas deontológicas, consagradas nos <strong>Estatutos da OAGB de 2018</strong> (Título III — Direitos e Deveres dos Advogados). Estas regras visam garantir a dignidade da profissão, a confiança do público e a independência do advogado.</p><p>O Conselho de Deontologia e Ética é o órgão estatutário responsável pela vigilância e promoção das boas práticas profissionais.</p>', NULL, 1, 'ativo', '2026-05-05 11:35:10'),
(2, 'deontologia', 'principios', 'Princípios Fundamentais', 'fas fa-shield-alt', NULL, '<ul><li><strong>Independência:</strong> O advogado exerce a sua profissão com total independência técnica, não podendo receber instruções contrárias à sua consciência profissional.</li><li><strong>Sigilo Profissional:</strong> O dever de sigilo é absoluto e abrange tudo o que o cliente confiar ao advogado no exercício das suas funções (Art. 78.º dos Estatutos).</li><li><strong>Lealdade:</strong> Dever de lealdade para com o cliente, os tribunais, os colegas e a Ordem.</li><li><strong>Diligência:</strong> O advogado deve zelar pelos interesses do cliente com competência e prontidão.</li><li><strong>Dignidade:</strong> Manter comportamento digno dentro e fora do exercício profissional.</li></ul>', NULL, 2, 'ativo', '2026-05-05 11:35:10'),
(3, 'deontologia', 'honorarios', 'Honorários e Tabela de Emolumentos', 'fas fa-file-invoice-dollar', NULL, '<p>A fixação de honorários deve respeitar os princípios da razoabilidade e proporcionalidade. Os Estatutos de 2018 preveem:</p><ul><li>Tabela mínima de honorários aprovada pelo Conselho Nacional</li><li>Proibição do <em>pactum de quota litis</em> (pagamento exclusivamente em função do resultado)</li><li>Direito de retenção sobre documentos para garantia de honorários devidos</li><li>Obrigação de celebrar contrato de mandato por escrito, sempre que possível</li></ul><p>Em caso de litígio sobre honorários, o Bastonário pode intervir como mediador antes de recurso ao tribunal.</p>', NULL, 3, 'ativo', '2026-05-05 11:35:10'),
(4, 'deontologia', 'sancoes', 'Regime Disciplinar e Sanções', 'fas fa-gavel', NULL, '<p>As infrações disciplinares são apreciadas pelo <strong>Tribunal de Ética e Disciplina</strong>, com recurso ao Conselho Nacional. As sanções previstas são:</p><ul><li><strong>Advertência:</strong> Para infrações leves, sem registo público.</li><li><strong>Censura:</strong> Registo no cadastro disciplinar do advogado.</li><li><strong>Multa:</strong> De valor fixado pelo Tribunal de Ética.</li><li><strong>Suspensão:</strong> Até 3 anos, com proibição de exercício.</li><li><strong>Expulsão:</strong> Para infrações gravíssimas, com cancelamento definitivo da inscrição.</li></ul>', NULL, 4, 'ativo', '2026-05-05 11:35:10'),
(5, 'deontologia', 'conflitos', 'Conflito de Interesses', 'fas fa-exclamation-triangle', NULL, '<p>O advogado está proibido de representar interesses conflitantes, ainda que com consentimento das partes. Está igualmente vedado:</p><ul><li>Patrocinar causa contra antigo cliente sobre assunto que lhe tenha sido confiado</li><li>Aceitar mandato quando tenha participado na matéria como magistrado, funcionário ou perito</li><li>Exercer advocacia em causa própria perante tribunais superiores, salvo quando não haja outro advogado disponível</li></ul>', NULL, 5, 'ativo', '2026-05-05 11:35:10'),
(6, 'deontologia', 'orgao', 'Conselho de Deontologia e Ética', 'fas fa-users-cog', NULL, '<p>Órgão colegial previsto no Art. 11.º dos Estatutos de 2018. Composição:</p><ul><li>Presidente eleito pelo Congresso dos Advogados</li><li>Quatro vogais, entre advogados de reconhecida idoneidade</li></ul><p><strong>Competências:</strong> Emitir pareceres sobre questões deontológicas, propor alterações ao código ético, colaborar na formação de estagiários sobre ética profissional e instruir processos disciplinares para apreciação do Tribunal de Ética.</p>', NULL, 6, 'ativo', '2026-05-05 11:35:10'),
(7, 'estagio', 'intro', 'Centro de Estágio e Formação', 'fas fa-graduation-cap', NULL, '<p>O Centro de Estágio da OAGB é responsável pela organização e supervisão do estágio profissional dos candidatos à inscrição como advogados. O estágio é regulado pelo <strong>Título IV dos Estatutos de 2018</strong> (Arts. 86.º a 120.º) e pelo Regulamento de Estágio aprovado pelo Conselho Nacional.</p><p>A conclusão com aproveitamento do estágio é condição obrigatória para a inscrição como advogado na OAGB.</p>', NULL, 1, 'ativo', '2026-05-05 11:35:10'),
(8, 'estagio', 'requisitos', 'Requisitos de Inscrição no Estágio', 'fas fa-clipboard-check', NULL, '<ul><li>Licenciatura em Direito por instituição de ensino reconhecida</li><li>Nacionalidade guineense ou cidadão da CEDEAO/CPLP com título de residência</li><li>Registo criminal sem antecedentes incompatíveis</li><li>Pagamento da taxa de inscrição no estágio</li><li>Requerimento dirigido ao Bastonário, acompanhado de documentação completa</li><li>Indicação de patrono (advogado com mais de 5 anos de inscrição efetiva)</li></ul>', NULL, 2, 'ativo', '2026-05-05 11:35:10'),
(9, 'estagio', 'fases', 'Fases do Estágio', 'fas fa-list-ol', NULL, '<p>O estágio profissional tem a duração mínima de <strong>18 meses</strong> e compreende:</p><ol><li><strong>Fase Teórica (6 meses):</strong> Curso de formação intensivo ministrado pelo Centro de Estágio, com módulos de Deontologia, Prática Processual, Direito OHADA, Direito Penal Prático, Redação Jurídica e Oratória Forense.</li><li><strong>Fase Prática (12 meses):</strong> Exercício supervisionado sob orientação do patrono, com participação obrigatória em audiências, redação de peças processuais e atendimento ao público.</li><li><strong>Avaliação Final:</strong> Prova escrita e oral perante o júri do Centro de Estágio. Aprovação mínima: 14/20 valores.</li></ol>', NULL, 3, 'ativo', '2026-05-05 11:35:10'),
(10, 'estagio', 'patrono', 'O Papel do Patrono', 'fas fa-user-tie', NULL, '<p>O patrono é o advogado responsável pela orientação prática do estagiário. Deve:</p><ul><li>Ter pelo menos 5 anos de exercício efetivo da advocacia</li><li>Estar em situação regular perante a OAGB (quotas em dia)</li><li>Não orientar mais de 2 estagiários em simultâneo</li><li>Elaborar relatório semestral sobre o progresso do estagiário</li><li>Assegurar a participação do estagiário em pelo menos 10 audiências judiciais</li></ul>', NULL, 4, 'ativo', '2026-05-05 11:35:10'),
(11, 'estagio', 'direitos', 'Direitos e Deveres do Estagiário', 'fas fa-balance-scale-left', NULL, '<p><strong>Direitos:</strong></p><ul><li>Participar em todos os atos do escritório do patrono</li><li>Aceder às sessões de formação do Centro de Estágio</li><li>Exercer o patrocínio forense em processos de menor complexidade, sob supervisão</li><li>Receber cédula provisória de estagiário</li></ul><p><strong>Deveres:</strong></p><ul><li>Assiduidade obrigatória (mínimo de 80%) nas sessões de formação</li><li>Cumprimento das normas deontológicas desde o primeiro dia</li><li>Elaboração de relatório de atividades a cada trimestre</li><li>Sigilo profissional sobre os assuntos do escritório</li></ul>', NULL, 5, 'ativo', '2026-05-05 11:35:10'),
(12, 'estagio', 'formacao', 'Cursos de Formação Contínua', 'fas fa-chalkboard-teacher', NULL, '<p>Para além do estágio inicial, a OAGB promove regularmente:</p><ul><li><strong>Cursos de Atualização:</strong> Sobre alterações legislativas e jurisprudência relevante</li><li><strong>Seminários OHADA:</strong> Aprofundamento dos Atos Uniformes em parceria com a ERSUMA (Escola Regional Superior da Magistratura)</li><li><strong>Workshops de Direitos Humanos:</strong> Em colaboração com a CPLP e organizações internacionais</li><li><strong>Programa de Mentoria:</strong> Emparelhamento de advogados juniores com advogados seniores para acompanhamento profissional</li></ul>', NULL, 6, 'ativo', '2026-05-05 11:35:10'),
(13, 'estagio', 'calendario', 'Calendário e Inscrições', 'fas fa-calendar-alt', NULL, '<p>As inscrições para o estágio são abertas <strong>anualmente</strong>, normalmente entre Janeiro e Março. O curso de formação inicia-se em Abril.</p><p>Para mais informações ou para requerer a inscrição, dirija-se à sede da OAGB ou contacte o Centro de Estágio e Formação:</p><p><strong>Email:</strong> formacao@oagb.gw<br><strong>Telefone:</strong> +245 95 555 1234<br><strong>Horário:</strong> Seg–Sex, 09:00–16:00</p>', NULL, 7, 'ativo', '2026-05-05 11:35:10'),
(14, 'cooperacao', 'nacionais', 'Parcerias Nacionais', 'fas fa-handshake', NULL, '<p>Colabora??o estreita com as principais institui??es do Estado para garantir a melhoria cont?nua do sistema judicial.</p><ul><li>Minist?rio da Justi?a</li><li>Conselho Superior da Magistratura</li><li>Institui??es Acad?micas</li></ul>', NULL, 1, 'ativo', '2026-05-05 13:53:46'),
(15, 'cooperacao', 'internacionais', 'Cooperação Internacional', 'fas fa-globe', NULL, '<p>Representa??o ativa em f?runs internacionais de advocacia e interc?mbio de conhecimento jur?dico.</p><ul><li>Advogados da CEDEAO</li><li>Uni?o Internacional de Advogados</li><li>Advogados de L?ngua Portuguesa</li></ul>', NULL, 2, 'ativo', '2026-05-05 13:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `departamentos_contactos`
--

CREATE TABLE `departamentos_contactos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `morada` text DEFAULT NULL,
  `telefone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departamentos_contactos`
--

INSERT INTO `departamentos_contactos` (`id`, `titulo`, `morada`, `telefone`, `email`, `horario`, `ordem`, `status`) VALUES
(1, 'Sede — Direção Nacional', 'Bairro de Santa Luzia, Rua Principal, Bissau', '+245 95 123 4567', 'geral@oagb.gw', 'Seg–Sex: 08:30–15:30', 1, 'ativo'),
(2, 'Conselho de Deontologia e Ética', 'Avenida Pansau Na Isna, Bissau', '+245 96 987 6543', 'deontologia@oagb.gw', 'Seg–Sex: 09:00–14:00', 2, 'ativo'),
(3, 'Tribunal de Ética e Disciplina', 'Palácio de Justiça, Piso 2, Bissau', '+245 95 222 3344', 'tribunal.etica@oagb.gw', 'Seg–Sex: 09:00–13:00', 3, 'ativo'),
(4, 'Centro de Estágio e Formação', 'Rua Eduardo Mondlane, Prédio M, Bissau', '+245 95 555 1234', 'formacao@oagb.gw', 'Seg–Sex: 09:00–16:00', 4, 'ativo'),
(5, 'Gabinete de Acesso ao Direito', 'Palácio de Justiça, R/C, Bissau', '+245 95 111 2233', 'acesso.direito@oagb.gw', 'Seg–Sex: 08:30–14:00', 5, 'ativo'),
(6, 'Secretariado-Geral', 'Bairro de Santa Luzia, Bissau', '+245 96 444 5566', 'secretariado@oagb.gw', 'Seg–Sex: 08:30–15:00', 6, 'ativo');

-- --------------------------------------------------------

--
-- Table structure for table `documentos_publicos`
--

CREATE TABLE `documentos_publicos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(300) NOT NULL,
  `tipo` enum('parecer','deliberacao','comunicado','publicacao','orcamento') NOT NULL,
  `numero_documento` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `link_externo` varchar(255) DEFAULT NULL,
  `data_documento` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `visualizacoes` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documentos_publicos`
--

INSERT INTO `documentos_publicos` (`id`, `titulo`, `tipo`, `numero_documento`, `descricao`, `arquivo`, `imagem`, `link_externo`, `data_documento`, `ativo`, `visualizacoes`, `created_at`, `updated_at`) VALUES
(1, 'Parecer Jurídico sobre Novo CPC', 'parecer', 'PAR-2023-01', 'Análise detalhada sobre as implicações do novo Código de Processo Civil na advocacia nacional.', '#', NULL, NULL, '2023-11-15', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(2, 'Deliberação do Conselho Geral nº 4/2023', 'deliberacao', 'DEL-2023-04', 'Aprovação do regulamento interno de estágios e quotas anuais.', '#', NULL, NULL, '2023-10-20', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(3, 'Parecer Técnico - Reforma Fiscal', 'parecer', 'PAR-2023-02', 'Posição oficial da OAGB perante a proposta de alteração da matriz fiscal.', '#', NULL, NULL, '2023-09-05', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(4, 'Comunicado aos Membros - Pagamento de Quotas', 'comunicado', 'COM-2024-01', 'Informação sobre o processo de regularização de quotas para o ano em curso.', '#', NULL, NULL, '2024-01-10', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(5, 'Encerramento da Secretaria', 'comunicado', 'COM-2023-12', 'Aviso sobre o período de encerramento da secretaria para balanço anual e manutenções.', '#', NULL, NULL, '2023-12-20', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(6, 'Abertura do Ano Judicial', 'comunicado', 'COM-2023-03', 'Convite e orientações para a cerimónia de Abertura Solene do Ano Judicial.', '#', NULL, NULL, '2023-03-01', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(7, 'Revista da Ordem - 1º Semestre', 'publicacao', 'REV-2023-01', 'Publicação semestral com artigos jurídicos, jurisprudência relevante e crónicas.', '#', NULL, NULL, '2023-06-30', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(8, 'Manual de Práticas Processuais', 'publicacao', 'PUB-2023-02', 'Guia de bolso para advogados estagiários com resumos processuais essenciais.', '#', NULL, NULL, '2023-08-15', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(9, 'Código Deontológico Comentado', 'publicacao', 'PUB-2023-01', 'Edição especial com comentários e exemplos práticos da aplicação do código de ética.', '#', NULL, NULL, '2023-02-10', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(10, 'Orçamento Anual Aprovado 2024', 'orcamento', 'ORC-2024', 'Demonstrativo do plano financeiro, receitas e despesas estimadas para o ano civil de 2024.', '#', NULL, NULL, '2023-12-28', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27'),
(11, 'Relatório e Contas 2023', 'orcamento', 'REL-2023', 'Balanço financeiro e transparência da execução orçamental do ano anterior.', '#', NULL, NULL, '2024-02-15', 1, 0, '2026-04-21 11:39:27', '2026-04-21 11:39:27');

-- --------------------------------------------------------

--
-- Table structure for table `estagios_processos`
--

CREATE TABLE `estagios_processos` (
  `id` int(11) NOT NULL,
  `estagiario_id` int(11) NOT NULL,
  `patrono_id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim_prevista` date NOT NULL,
  `data_fim_efetiva` date DEFAULT NULL,
  `fase_atual` enum('1ª Fase','2ª Fase','Concluído') DEFAULT '1ª Fase',
  `status` enum('Em Curso','Suspenso','Aprovado','Reprovado') DEFAULT 'Em Curso',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estatisticas_visualizacao`
--

CREATE TABLE `estatisticas_visualizacao` (
  `id` int(11) NOT NULL,
  `tipo_conteudo` enum('noticia','evento','pagina','documento') NOT NULL,
  `conteudo_id` int(11) NOT NULL,
  `ip_visitante` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `data_visualizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estatutos_artigos`
--

CREATE TABLE `estatutos_artigos` (
  `id` int(11) NOT NULL,
  `numero_artigo` int(11) NOT NULL,
  `titulo_artigo` varchar(500) DEFAULT NULL,
  `conteudo` mediumtext DEFAULT NULL,
  `tema` varchar(100) DEFAULT NULL COMMENT 'Thematic category for sidebar filtering',
  `capitulo` varchar(100) DEFAULT NULL COMMENT 'For future manual assignment',
  `titulo_capitulo` varchar(255) DEFAULT NULL COMMENT 'For future manual assignment',
  `seccao` varchar(100) DEFAULT NULL COMMENT 'For future manual assignment',
  `titulo_seccao` varchar(255) DEFAULT NULL COMMENT 'For future manual assignment',
  `titulo_doc` varchar(100) DEFAULT NULL COMMENT 'For future manual assignment: TITULO I, II, etc.',
  `titulo_doc_nome` varchar(255) DEFAULT NULL COMMENT 'For future manual assignment',
  `ordem` int(11) DEFAULT 0 COMMENT 'Display order (defaults to article number)',
  `ativo` tinyint(1) DEFAULT 1 COMMENT '1=visible, 0=hidden',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `estatutos_artigos`
--

INSERT INTO `estatutos_artigos` (`id`, `numero_artigo`, `titulo_artigo`, `conteudo`, `tema`, `capitulo`, `titulo_capitulo`, `seccao`, `titulo_seccao`, `titulo_doc`, `titulo_doc_nome`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Denominação, Natureza Sede', '1. Denomina-se Ordem dos Advogados da Guiné-Bissau, a associação pública, com personalidade jurídica própria, representativa dos operadores do direito, que exercem profissionalmente a advocacia.\n2. A denominação de advogado é reservada exclusivamente aos profissionais do direito inscrito na Ordem dos Advogados.\n3. A Ordem dos Advogados tem a sua sede em Bissau, podendo criar representações e delegacias no interior do país.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(2, 2, 'Regime jurídico', '1. Os advogados inscritos na OAGB exercem as suas atividades de advocacia no território da Guiné-Bissau, subordinados à Constituição da República, as demais leis aplicáveis e aos preceitos deste estatuto e regulamentos.\n2. As normas relativas ao exercício da advocacia e as liberdades fundamentais que os presentes estatutos reconhecem, são interpretadas de conformidade com a constituição.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(3, 3, 'Âmbito', 'A Ordem dos Advogados exerce as atribuições e competências que lhe conferidas pelos presentes e pela lei em todo o território nacional.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 3, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(4, 4, 'Relação com as outras organizações congéneres', 'A Ordem dos Advogados da Guiné-Bissau rege-se nas relações internacionais pelos princípios da Independência, do respeito dos direitos do homem, Independência do poder judicial e da reciprocidade.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 4, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(5, 5, 'Direito internacional', 'As normas e os princípios do direito internacional aceites pelo Estado da Guiné-Bissau relativa a proteção dos direitos do homem e proteção da independência dos advogados e dos magistrados fazem parte integrante dos presentes estatutos, podendo ser invocadas diretamente perante os poderes públicos.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 5, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(6, 6, 'A independência da OAGB, dos advogados e valores', '1. A Ordem dos Advogados é independente face aos órgãos do Estado, pugnando pelo Estado Democrático de Direito e social, e como valores superiores do seu ordenamento jurídico, a independência da justiça, a igualdade e o pluralismo de pensamento jurídico.\n2. O direito ao livre exercício da profissão, o respeito pela lei, dos direitos, liberdades e garantias fundamentais das pessoas e aos princípios éticos inerentes ao bom exercício profissional da atividade forense, bem como a independência dos Magistrados e dos advogados\n3. Não há hierarquia nem subordinação entre advogados, magistrados judiciais e do Ministério Publico, devendo todos tratar-se com consideração e respeito recíprocos.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 6, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(7, 7, 'Representação da Ordem dos Advogados', '1. A Ordem dos Advogados é representada em juízo e fora dele pelo Bastonário.\n2. Para defesa de todos os seus membros em todos os assuntos relativos ao exercício da profissão ou ao desempenho das funções nos órgãos sociais, a OAGB, poderá exercer o direito de assistente ou conceder patrocínio em processo de qualquer natureza.\n3. A Ordem dos Advogados, quando intervenha como assistente em processo penal, pode ser representada por advogado diferente do constituído pelos restantes assistentes, havendo-os.', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 7, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(8, 8, 'Trato oficial, honras, títulos honoríficos e patronos', '1. O Bastonário da Ordem dos Advogados é tratado de \"Excelentíssimo Senhor Bastonário\" e os presidentes dos restantes membros dos órgãos sociais de \"Ilustríssimo senhor\"\n2. Os advogados são tratados de \"ilustres\".\n3. A denominação de Bastonário é vitalícia.\n4. Nos atos oficiais, o Bastonário da Ordem fica imediatamente à esquerda do Procurador-Geral da República.\n5. Durante o seu mandato e no estrito desempenho das suas funções, o Bastonário não pode ser censurado por opiniões que emitir nessa qualidade, contando que não as profira em violação da Lei.\n6. O Advogado que exerça ou haja exercido cargos nos órgãos da Ordem tem direito a usar a insígnia correspondente, nos termos do respetivo regulamento.\n7. O Advogado que desempenha ou tenha desempenhado funções no Conselho Nacional da Ordem, Comissão de deontologia e ética e tribunal de ética e disciplina da Ordem enquanto se encontre no exercício dos cargos e nos 2 anos subsequentes, fica isento de prestar quaisquer serviços de nomeação oficiosa.\n8. Em caso de justificada necessidade, ao Conselho Nacional da Ordem poderá fazer cessar a isenção prevista no número anterior.\n9. O Advogado que tenha exercido com mérito e distinção cargo nos órgãos da Ordem conserva a designação correspondente ao cargo mais elevado que haja ocupado.\n10. A Ordem dos advogados da Guiné-Bissau, fazendo honra as suas origens, ela poderá adoptar patronos e comemorações que tenham acompanhado sua trajetória histórica desde a fundação.\n\nTITULO II\nRegime Economico da OAGB', 'Disposições Gerais', NULL, NULL, NULL, NULL, NULL, NULL, 8, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(9, 9, 'O exercício económico', 'O exercício económico da OAGB corresponde ao ano civil.', 'Regime Económico', NULL, NULL, NULL, NULL, NULL, NULL, 9, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(10, 10, 'Dos recursos da OAGB', '1. Constituem recursos da OAGB, os seguintes:\na) Os rendimentos de qualquer natureza resultante das atividades da OAGB, bens, serviços ou direitos que integram o património da OAGB, bem como os rendimentos depositados em contas;\nb) As joias de inscrição e quotas;\nc) As taxas fixadas pela Direção Nacional para emissão de certificações, boletins, modelos de contratos de honorários, faturas e recibos dos mesmos, bem como de outros modelos de formulários;\nd) Taxas para pedidos de informação, consultas, acesso a biblioteca, bem como pela prestação de outros serviços;\na) As subvenções ou donativos concedidos a OAGB por Organismos Internacionais, o Estado, o poder local, entidades públicas e privadas, ou particulares;\nb) Os bens e direitos de toda classe que por doação, herança, legado ou outro título integram o património da OAGB;\nc) Quaisquer outros bens móveis e imoveis adquiridos legalmente, bem como Produto de venda das publicações, organização de atividades de caracter científico, cursos, congressos, atividades desportivas ou recreativas, etc.,\n2. A OAGB goza de imunidade tributária total sobre os seus bens, rendimentos e serviços.', 'Regime Económico', NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(11, 11, 'Despesas', 'As receitas da Ordem serão afetadas as suas despesas devidamente orçamentadas.', 'Regime Económico', NULL, NULL, NULL, NULL, NULL, NULL, 11, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(12, 12, 'Contabilidade e Gestão Financeira', '1. O exercício da vida económica da Ordem coincide com o ano civil.\n2. As contas da Ordem são encerradas em 31 de Dezembro de cada ano.\n3. Constituem instrumentos de controlo de gestão o orçamento, o relatório e as contas do exercício com referência a 31 de Dezembro.', 'Regime Económico', NULL, NULL, NULL, NULL, NULL, NULL, 12, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(13, 13, 'Processo e Papéis da Ordem, Selos, Custas e Impostos de Justiça', '1. Não dão lugar às custas ou impostos de justiça e não estão sujeitos a impostos de selo as certidões expedidas pela Ordem, os requerimentos e petições a ela dirigidos e os processos que nela corram ou em que tenha intervenção.\n2. A Ordem pode requerer e alegar em papel não selado e está isenta de custas, preparos e impostos de justiça em qualquer processo em que intervenha.', 'Regime Económico', NULL, NULL, NULL, NULL, NULL, NULL, 13, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(14, 14, 'Providência Social, económica e jurídica', 'A Ordem dos Advogados assegura a proteção social, económica e jurídica dos Advogados, nos termos das disposições legais e regulamentais aplicáveis.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 14, 1, '2026-03-29 22:51:52', '2026-03-29 22:51:52'),
(15, 15, 'Obrigatoriedade de inscrição na OAGB', '1. É obrigatória a inscrição na Ordem dos advogados da Guiné-Bissau para o exercício da advocacia no território nacional da Guiné-Bissau.\n2. A qualidade de advogado se adquire, se conserva e se perde de acordo com o estabelecido nos presentes estatutos e regulamente em vigor.\n3. Podem inscrever-se na Ordem dos Advogados da Guiné-Bissau para o exercício da profissão de advogado, os indivíduos com pelo menos, licenciatura em direito.\n4. A inscrição e atuação de advogados estrangeiros serão regidas pelos Estatutos, legislação nacional, comunitária ou internacional que o Estado da Guiné-Bissau é parte.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 15, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(16, 16, 'Requisitos de inscrição', '1. Os requisitos para a inscrição na OAGB são os seguintes:\na) Ter a nacionalidade guineense ou de algum Estado membro da UEMOA, salvo disposição em tratado, convenção internacional ou disposição legal especifica;\nb) Ser maior de idade e não estar abrangido por qualquer das causas de incapacidade;\nc) Ter licenciatura em direito ou titular de um diploma estrangeiros que, conforme a legislação nacional aplicável, sejam homologados pelo departamento governamental competente, designadamente Ministério da Educação Nacional;\nd) Pagar a joia e a quota de ingresso e demais direitos de inscrição que tenham sido fixados pela OAGB;\ne) Formalizar a inscrição na caixa de assistência geral dos advogados sedeada no Instituto Nacional da Providencia Social;\nf) Estar livre de qualquer antecedente criminal com moldura penal abstrata superior a três anos de prisão;\ng) Prestar prova de aptidão na OAGB.\nh) Não estar abrangido por qualquer situação de incompatibilidade ou proibição para o exercício da advocacia;\n2. Não obstante, são admitidos à inscrição na OAGB, como advogados, exercendo a profissão de advogado, os indivíduos que reúnam as condições previstas nos presentes Estatuto e no Regulamento para o acesso a profissão.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 16, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(17, 17, 'Direitos humanos', '1. Excetuam-se do disposto nos artigos 17° e 18° a proteção dos direitos humanos relativos ao estatuto da criança, mutilação genital feminina, tráfico de órgãos humanos, desde que o advogado ou funcionário internacional é contratado, designado ou nomeado para a defesa da vítima ou dos familiares.\n2. O advogado ou funcionário internacional que pretende intervir nos tribunais do território da Guiné-Bissau para a proteção dos direitos humanos, nos termos do número anterior, deve solicitar a sua inscrição na OAGB para o efeito.\n3. A inscrição efetuada nos termos e para os efeitos do número anterior caducada com a execução efetiva e completa da decisão com trânsito em julgado.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 17, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(18, 18, 'Inscrição', '1. A inscrição deve ser feita na sede da OAGB.\n2. O requerimento deve ser acompanhado de certidão do registo de nascimento, certificado de licenciatura, certificado de registo criminal e boletins preenchidos nos termos regulamentares assinados pelos interessados e acompanhados de três fotografias.\n3. O pedido de inscrição deve ser formulado, em formulário próprio perante a Direção Nacional e só poderá ser recusado ou denegado, mediante deliberação devidamente fundamentada, contra a qual cabe recurso nos termos gerais.\n4. Para a inscrição como advogado será dispensado o diploma de licenciatura ou documento que o substitua, quando o mesmo já conste dos arquivos da OAGB.\n5. No requerimento pode o interessado indicar, para uso no exercício da profissão, nome abreviado, que não é admitido se suscetível de provocar confusão com outro anteriormente requerido ou inscrito, exceto se o possuidor deste com isso tenha concordado.\n6. Todas as comunicações previstas no presente Estatuto e nos regulamentos da OAGB devem ser feitas, salvo disposição expressa em contrário, para o domicílio profissional indicado no formulário do pedido de inscrição.\n7. O domicílio profissional do advogado estagiário é o do seu patrono.\n8. O pedido de inscrição deve ser decido no prazo máximo de quinze dias.\n9. No caso de recusa de inscrição, pode o interessado recorrer para a plenária do Conselho Nacional da Ordem.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 18, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(19, 19, 'Inscrição como advogado. Requisitos', '1. Sem prejuízo das excepçoes previstas no estatuto, a inscrição como advogado é precedida de um estágio de dezoito meses com boa informação.\n2. A inscrição como advogado, nas respetivas ordens, de cidadãos oriundos dos Países membros da CPLP, é reconhecida para efeito de inscrição na Ordem dos Advogados, observado o princípio da reciprocidade.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 19, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(20, 20, 'Inscrição de Advogados Guineenses Regressados do Estrangeiro', '1. Ao advogado guineense inscrito como advogado numa Ordem dos Advogados membro da União dos Advogados de Língua Portuguesa (\"UALP\") que requerer a sua inscrição na Ordem dos Advogados Guineense, será inscrito como estagiário por um período de três meses.\n2. Ao advogado guineense proveniente de outras Ordens de Advogados diferente do referido no número anterior, é fixado um tempo de estágio que pode ir de seis a doze meses, conforme o grau de dificuldade na adaptação à prática do foro, tendo sempre em conta as diferenças de sistemas judiciais, aplicando-se com as necessárias adaptações o regime da fase complementar do estágio.\n3. O advogado requerente nos termos dos números anteriores deve juntar no acto da inscrição os documentos comprovativos da frequência do estágio e da sua inscrição como advogado efetivo, nomeadamente o certificado para o efeito passado pela respetiva Ordem dos Advogados ou entidade similar.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 20, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(21, 21, 'Restrições ao Direito de Inscrição', '1. Não podem ser inscritos:\na) Os que não possuam idoneidade moral para o exercício da profissão e, nos termos do estatuto;\nb) Os que não estejam no pleno goza dos direitos civis;\nc) Os declarados incapazes de administrar as suas pessoas e bens por sentença transitada em julgado;\nd) Os que estejam em situação de incompatibilidade ou inibição do exercício da advocacia:\ne) Os magistrados e funcionários que, mediante processo disciplinar, hajam sido demitidos, aposentados ou colocados na inatividade por falta de idoneidade moral.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 21, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(22, 22, 'Exercício da Advocacia por Estrangeiros', '1. Os estrangeiros diplomados em Faculdades de Direito estrangeiras, com residência permanente no território da República da Guiné-Bissau, desde o seu início até a data de inscrição, não inferior a três anos, podem inscrever-se na Ordem dos Advogados da Guiné-Bissau, nos mesmos termos dos guineenses, se a estes o seu país conceder reciprocidade.\n2. Os Advogados diplomados por qualquer Faculdade de Direito dos Países membros da CPLP podem inscrever-se na Ordem dos Advogados em regime de reciprocidade.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 22, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(23, 23, 'Inscrição como Estagiário. Requisitos', '1. A inscrição para o estágio profissional é precedida de concurso documental e prova de aptidão com a classificação de regular.\n1. O interessado deve preencher os requisitos previstos no artigo 15°.\n2. O estágio tem a duração de um período de 18 meses, durante o qual, sob a direção de um patrono, com pelo menos três anos de efetivo exercício da advocacia, o advogado estagiário efetuará consulta jurídica e prática forense.\n1. As disposições deste Estatuto, com as necessárias adaptações, aplicam-se aos advogados estagiários, à exceção das que se referem ao exercício do direito de voto.\n2. A organização geral do estágio cabe á OAGB.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 23, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(24, 24, 'Inscrição dos Magistrados. Requisitos', 'Podem solicitar inscrição na OAGB como advogados, os Magistrados que tenham exercício efetivo de funções de forma ininterrupta, com pelo menos classificação de bom, por período de tempo igual ou superior a cinco anos, com quebra total do vínculo com o estado, salvo os direitos inerentes à reforma.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(25, 25, 'Dispensa de estágio', 'São dispensados do estágio, os magistrados que preencham os requisitos previstos no artigo anterior, os mestres docentes nas Faculdades de Direito, com um mínimo de 5 anos de docência e os Doutores em Direito.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 25, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(26, 26, 'Do juramento do compromisso profissional', '1. O advogado, no início do seu exercício profissional, prestará juramento de compromisso de respeito à constituição da República e Estado de Direito, bem como do fiel cumprimento das obrigações e normas deontológicas da profissão.\n2. O juramento ou compromisso será prestado perante o Conselho Nacional da Ordem na forma que a OAGB estabelecer.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 26, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(27, 27, 'Título profissional de Advogado', '1. O título de Advogado está exclusivamente reservado aos licenciados que obtiverem aprovação no curso obrigatório de acesso à profissão, com inscrição em vigor na OAGB.\n2. Os candidatos a título profissional de Advogado são denominados de advogados estagiários.\n3. Os advogados honorários podem usar a denominação de advogado desde que façam a indicação dessa qualidade.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 27, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(28, 28, 'Traje Profissional', '1. A toga é o traje profissional do advogado e do estagiário.\n2. É obrigatório para os advogados e advogados estagiários, quando pleiteiem oralmente, o uso da toga.\n3. O modelo da toga, bem como qualquer outro acessório do traje profissional, é o fixado pelo Conselho Nacional da Ordem.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 28, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(29, 29, 'Da identidade profissional', '1. Constitui documento de identidade profissional a carteira profissional emitida pela OAGB, de uso obrigatório pelos advogados e estagiários inscritos, para o exercício de suas atividades.\n2. A carteira profissional, tem as seguintes dimensões (...) x (...) centímetros e observa os seguintes critérios:\na) A frente, em fundo (indicar as cores ou cor verde, um símbolo nacional, nome do advogado, escritório, n° de inscrição, e cidade e a expressão \" Ordem dos Advogados da Guiné-Bissau)\nb) Verso registo da lei que concede proteção aos advogados\"\n3. O cartão do estagiário tem a cor (.....) com a indicação de \"identidade de estagiário\" , n° de inscrição, escritório onde frequenta o estagio, o nome do patrono e o registo da lei que protege os advogados\n4. O advogado tem direito a receber o cartão de identidade profissional imediatamente apos a prestação do juramento do compromisso com a constituição, a lei e aos estatutos da OAGB perante o presidente da República.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 29, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(30, 30, 'Cancelamento e suspensão da inscrição', '1. A inscrição do advogado pode ser cancelada na OAGB:\na) Quando requerida pelo interessado;\nb) Por óbito;\nc) Por efeitos da sanção\nd) Quando o advogado passar, com caracter definitivo, a exercer atividade incompatível com a advocacia;\ne) Perder qualquer dos requisitos necessários para inscrição;\n2. Suspende-se, a inscrição na OAGB:\na) Quando requerida pelo interessado;\nb) Quando o advogado passar a exercer, com caracter temporário, atividade incompatível com o exercício da advocacia;\nc) Por motivo de doença curável, que torna o advogado incapacitado de exercer a atividade durante dois anos.', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 30, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(31, 31, 'Direitos Perante a OAGB', 'Os advogados têm o direito a requerer a intervenção da OAGB para defesa dos seus direitos ou dos legítimos interesses da classe, nos termos previstos neste Estatuto.\nSecção II\nFormas de exercício da advocacia', 'Membros e Inscrição', NULL, NULL, NULL, NULL, NULL, NULL, 31, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(32, 32, 'Liberdade de organização', 'O exercício da advocacia profissional pode assumir a forma individual ou coletiva ou sociedade de advogados, por conta própria, sob a forma de contrato de prestação de serviços ou contrato individual de trabalho.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 32, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(33, 33, 'Exercício individual', '1. O advogado individual poderá partilhar o seu escritório, instalações, serviços ou outros meios de trabalho, sem identificação coletiva com outros advogados perante o cliente.\n2. O advogado individual pode celebrar acordos para determinados assuntos ou categorias de assuntos com outros advogados, ou coletivo de advogados, nacionais ou estrangeiros, qualquer que seja sua forma.\n3. O advogado titular do escritório individual responde profissionalmente perante o seu cliente pelos gastos e atuações que efetue com os seus colaboradores, sem prejuízo do direito de regresso contra estes;\n4. Ficam excluídos do âmbito da responsabilidade previsto no número anterior, a violação das regras de deontologia e disciplina;\n5. Os honorários devidos pelo cliente ao advogado titular do escritório, devem ser pagos diretamente a este, mesmo nos casos em que as intervenções no processo tenham sido realizadas por outro advogado, substabelecido com mãos conjuntas ou substituição total;\n6. O titular do escritório responde pessoalmente pelos honorários devidos aos advogados intervenientes, mesmo nos casos em que o cliente tenha abona-los outras vantagens ou dinheiro, salvo pacto escrito em contrário.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 33, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(34, 34, 'Exercício coletivo', '1. Os advogados podem exercer coletivamente a advocacia, através da constituição de sociedades de advogados em qualquer das formas permitidas por lei.\n2. Não são admitidos, nem podem funcionar, as sociedades de advogados que representam formas ou características comerciais, que realizem atividades estranhas à advocacia, que incluem sócios não inscritos na Ordem ou proibidos de exercer a atividade de advocacia.\n3. A organização deve ter como objecto exclusivo, o exercício profissional da advocacia e estar integrada maioritariamente por advogados em exercício na percentagem exigido pela lei aplicável, sem limitação de número.\n4. Os advogados agrupados não podem atuar de forma independentes nem autónomos da sociedade.\n5. Nas intervenções profissionais ou documentos que o grupo elabora deve constar a identificação da sociedade ou indicação expressa que o interveniente age em nome da sociedade.\n6. Os advogados associados gozam de plena liberdade individual para aceitar ou recusar qualquer cliente ou assunto solicitado o patrocínio da sociedade e, plena independência no patrocínio da causa que tenha sido nomeado.\n7. A atuação profissional dos sócios subordina-se a disciplina da OAGB, em matéria de deontologia e disciplina, respondendo pessoalmente cada um dos advogados perante a OAGB.\n8. Nenhum advogado pode integrar mais de uma sociedade de advogados.\n9. Os advogados sócios de uma sociedade profissional não podem representar em juízo ou fora de juízo, clientes de interesses opostos', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 34, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(35, 35, 'Contrato de trabalho', '1. O contrato de trabalho celebrado com o advogado, não lhe retira a isenção técnica, nem diminui a independência profissional a que esteja adstrito, inerente à profissão\n2. Cabe exclusivamente à Ordem dos Advogados a apreciação da conformidade com os princípios deontológicos das cláusulas de contrato celebrado com o advogado, por via do qual o seu exercício profissional se encontra sujeito a subordinação jurídica.\n3. O advogado trabalhador não está obrigado à prestação de serviços profissionais de interesse pessoal dos empregadores, bem como os serviços não compreendidos no objecto do contrato de trabalho.\n4. São nulas as cláusulas de contrato celerado com o advogado que violem os princípios referidos no n° 2, bem como quaisquer orientações ou instruções da entidade empregadora que restrinjam a isenção e independência do advogado ou que, de algum modo, violem os princípios deontológicos da profissão.\n5. O Conselho Nacional da Ordem dos Advogados pode solicitar às entidades públicas empregadoras, que hajam intervindo em tais contratos, entrega de cópia dos mesmos, a fim de aferir da legalidade do respetivo clausulado, atentos os critérios enunciados nos números anteriores.\n6.  Quando a entidade empregadora seja pessoa de direito privado, qualquer dos contraentes pode solicitar ao Conselho Nacional parecer sobre a validade das cláusulas ou de actos praticados na execução do contrato, o qual tem caracter vinculativo.\n7. Em caso de litígio, o parecer referido no número anterior é obrigatório.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 35, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(36, 36, 'Escritório de procuradoria ou consulta jurídica', '1. Denomina-se escritório o domicílio profissional do advogado, o lugar onde o advogado exerce a sua atividade principal de advocacia.\n2. O advogado deve promover a inscrição do seu escritório na Ordem dos Advogados. Se exercer a profissão no interior do país ou em lugares diversos, deve promover a inscrição suplementar dos mesmos, na ordem e nos tribunais em cuja jurisdição territorial exerce a atividade.\n3. No caso de transferência ou mudança efetiva para outra localidade, deve o advogado requere a modificação do registo na Ordem, bem como nos tribunais, nos termos do número anterior.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 36, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(37, 37, 'Proibições', '1. É proibido a constituição de escritório de procuradoria, designadamente judicial, administrativa, fiscal e laboral, ou de escritório que preste, de forma regular e remunerada, consulta jurídica a terceiros, ainda que, em qualquer dos casos, sob a direção efetiva de pessoa não habilitada para exercer o mandato judicial.\n2. Não são abrangidos pelo disposto no número anterior, os serviços de contencioso e consulta jurídica mantidos pelos sindicatos, associações patronais ou outras associações legalmente constituídos, sem fim lucrativo e de reconhecido interesse público, destinados a facilitar a defesa exclusivamente dos interesses dos respetivos associados, desde que estes sejam praticados por gabinetes criados para o efeito, exercidos por advogados, advogados estagiários ou solicitador\n3. A violação da proibição estabelecida no neste artigo confere à Ordem dos advogados o direito de requerer junto das autoridades judiciais competentes o encerramento do escritório ou gabinete.\n\nSecção III\nHonorários', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 37, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(38, 38, 'Honorários Limites e Forma de Pagamento', '1. O advogado tem direito a receber os honorários convencionados, fixados por arbitramento, por conciliação e aos de sucumbência correspondente aos serviços prestados.\n2. Na fixação dos honorários deve o advogado proceder com moderação, atendendo ao tempo gasto, à dificuldade do assunto, à importância do serviço prestado, às posses dos interessados, aos resultados obtidos e à praxe do foro e estilo da área judicial.\n3. Os honorários devem ser saldados em dinheiro.\n4. É lícito ao advogado exigir, a título de provisão, quantias por conta dos honorários, o que, a não ser satisfeito, dá ao advogado direito a renunciar ao mandato.\n5. Salvo estipulação em contrário, pelo menos um terço dos honorários é devido no início do serviço, outro terço até a decisão da primeira instância, se houver recurso, e o restante no final.\n6. A cobrança dos honorários pode promover-se nos mesmos autos ou processo da acçao em que tenha atuado o advogado, se assim lhe convier.\n7.  É nula qualquer cláusula, ou convenção que retire o advogado o direito aos honorários de sucumbência.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 38, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(39, 39, 'Repartição de Honorários', 'É proibido ao advogado repartir honorários, ainda que a título de comissão ou outra forma de compensação, exceto com advogados, advogados estagiários e solicitadores com quem colabore ou que lhe tenham prestado colaboração.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 39, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(40, 40, 'Honorários do advogado em regime do trabalho subordinado', 'Independentemente da retribuição ou convenção sobre os honorários, nas causas em que for parte o empregador, ou pessoa por este representada, os honorários de sucumbência são sempre devidos aos advogados trabalhadores.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 40, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(41, 41, 'Preparos e Custas do Advogado pelo seu não Pagamento', 'O advogado não pode ser responsabilizado pela falta de pagamento de custas ou quaisquer despesas se, tendo pedido ao cliente as importâncias para tal necessárias, as não tiver recebido, e não é obrigado a dispor, para aquele efeito, das provisões que tenha recebido para honorários.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 41, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(42, 42, 'Da colaboração interprofissional', 'Os advogados podem estabelecer protocolos ou formas de colaboração técnica com outros profissionais liberais para assistência ou peritagem aos assuntos que lhes são entregues para patrocínio.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 42, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(43, 43, 'Constituição e registo dos estatutos e escritórios profissionais', '1. Compete a direção nacional da OAGB aprovar previamente, mediante emissão da certidão negativa, a constituição, suspensão ou dissolução de associação de advogados para o exercício coletivo da advocacia, bem como o registo dos estatutos e escritórios de advogados;\n2. As associações de advogados para o exercício coletivo da advocacia será objecto de inscrição no registo central das pessoas coletivas\n\nCAPITULO II\nA Ordem dos Advogados e a participação da sociedade\nSECÇÃO I\nCongresso Nacional da Advocacia', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 43, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(44, 44, 'Do congresso da advocacia', '1. A Ordem dos Advogados organizará, pelo menos em cada três anos, um congresso da Advocacia, cujas conclusões terão caracter de parecer para o Governo para o sector da Justiça, Estado de Direito, Democracia e direitos humanos.\n2. Sem prejuízo da autonomia da Ordem dos Advogados na escolha e seleção dos temas, o Governo poderá propor a Ordem dos Advogados para os trabalhos do congresso os temas de interesse para a boa governação ou outros de interesse geral para o aperfeiçoamento do ordenamento jurídico.\n3. O Congresso é aberto a todos os advogados e ao público em geral, nos termos e nas condições previstas no regulamento do congresso.\n4. Podem ser convidados ao congresso, juristas nacionais e estrangeiras de reconhecido mérito profissional ou académico, advogados de outros países, professores universitários e funcionários dos organismos internacionais que atuam nas áreas da justiça, Estado de direito, Democracia e Direitos Humanos.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 44, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(45, 45, 'Organização', '1. O Congresso é organizado por uma comissão nomeada para o efeito, sob proposta do Bastonário, pela Direção Nacional que deverá elaborar o regulamento do Congresso e a proposta do programa.\n2. A comissão organizadora do Congresso é composta pelo Bastonário, que preside, antigos bastonários, mais dez advogados indicados por este e aprovados pelo Conselho nacional\n3. O secretariado do Congresso é o órgão executivo da comissão organizadora.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 45, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(46, 46, 'Convocação e Preparação', '1. O Congresso é convocado pelo Bastonário com uma antecedência mínima de quatro meses, por meio de edital e anúncio no jornal mais lido, donde conste a ordem dos trabalhos.\n2. Nos dois meses seguintes à convocação, o Bastonário promoverá a constituição da comissão organizadora do Congresso, que procede à elaboração do regulamento, tendo em conta as sugestões feitas pelos advogados e órgãos da Ordem, estabelecer o respetivo programa, do qual devem constar temas a debater.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 46, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(47, 47, 'Conclusões', '1. O último dia do congresso é reservado exclusivamente aos advogados e convidados destacados para a adoção das conclusões.\n2. Os convidados assistem os trabalhos como observadores sem direito de voto, gozando do direito de voto apenas os advogados com inscrição em vigor.\n\nCAPITULO III\nOAGB\nSecção I\nDisposições Gerais', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 47, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(48, 48, 'Órgãos', '1. A organização democrática do Ordem dos advogados compreende a existência dos órgãos sociais.\n2. A OAGB é dotada de órgãos próprios representativos, que visam a prossecução de interesses próprios dos advogados.\n3. Os órgãos principais da ordem dos advogados são; o congresso dos Advogados Guineense, a Assembleia Geral, o Bastonário, a Direção Nacional, o Conselho da Ordem, o Secretário-geral, o Conselho de deontologia e ética e o Tribunal de ética e Disciplina.\n4. Poderão ser criados, de acordo com os presentes estatutos, os órgãos subsidiários ou comissões consideradas necessárias.\n5. O Bastonário é por inerência o Presidente do Congresso, da Assembleia Geral, do Conselho da Ordem e da Direção Nacional.\n6. É a seguinte a hierarquia dos titulares dos órgãos da OAGB:\na) O Bastonário;\nb) O Presidente da Assembleia-geral;\nc) O presidente do Conselho de deontologia e ética;\nd) Os membros da Direção nacional;\n7. O Presidente do tribunal é independente em relação aos restantes órgãos da OAGB.\nSecção IV\nCarácter Eletivo e Temporário do Exercício dos Cargos Sociais', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 48, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(49, 49, 'Princípios eleitorais', '1. A escolha dos advogados para ocuparem cargos nos órgãos sociais da OAGB é regida pelo princípio da democracia, autonomia e livre participação dos advogados.\n2. O Bastonário e os restantes órgãos da ordem são eleitos em votação direta e secreta, democrática, justa, livre e transparente, por único mandato de três anos, em que poderão participar como eleitores todos os advogados com inscrição em vigor, com pelo menos três meses anteriores a data da Assembleia Geral para a eleição dos órgãos sociais,\n3. Não é admitida a reeleição do Bastonário para um segundo mandato consecutivo nem a apresentação da sua candidatura no triénio subsequente ao termo do seu mandato.\n4. Com exclusão do Bastonário, são reelegíveis em mandatos consecutivos todos os membros dos outros órgãos colegiais.\n5. Não são elegíveis para os órgãos sociais os membros que se encontram em qualquer das seguintes situações:\na) Punido disciplinarmente com trânsito em julgado com pena superior à multa;\nb) Funcionário publico qualquer que seja o título da sua ligação com o Estado;\nc) Afastamento prolongado da profissão por mais de dois anos consecutivos ou cinco alternados por razoes de incompatibilidade ou voluntariamente até o ano imediatamente à data das eleições;\nd) Os solicitadores;\ne) Os membros dos órgãos de direção dos partidos políticos, associações civis ou organizações não-governamentais até o ano subsequente ao termo do respetivo mandato.\nf)  Os membros oriundos da Magistratura até cinco anos de exercício efetivo', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 49, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(50, 50, 'Convocação das eleições', 'Devem ser convocadas as eleições, nos seguintes casos:\na) Quando caduca o período do mandato;\nb) Quando a moção de censura contra o Bastonário for aprovada;\nc) Quando por qualquer motivo, a maioria dos membros da direção nacional, deixam de participar nesse órgão.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 50, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(51, 51, 'Da Comissão eleitoral', '1. A comissão eleitoral será integrada por cinco advogados em efetividade do exercício da atividade de advocacia, com uma antiguidade de inscrição regular não inferior a sete anos.\n2. Os membros da comissão eleitoral serão nomeados, com os seus suplentes, sob proposta da direção nacional pelo Conselho nacional.\n3. O mandato da comissão de eleição permanecerá vigente até a finalização do processo eleitoral para que fora designada.\n4. Não poderão ser membros da comissão eleitoral os membros da Direção nacional, nem os advogados subescritores da lista dos candidatos à eleição para os órgãos sociais\n5. A comissão eleitoral será presidida pelo membro mais antigo e secretário, o membro menos antigo na profissão.\n6. Em caso de empate na distribuição de funções a que se refere o número anterior far-se-á mediante sorteio.\n7. A comissão eleitoral deve velar pela observância de um processo eleitoral democrático e limpo, baseado nos princípios de igualdade de trato, transparência, bem como na aplicação correta das regras eleitorais vigentes aprovadas para cada processo eleitoral.\n8.   Compete a comissão eleitoral desempenhar as seguintes funções:\na) Supervisionar o processo eleitoral\nb) Resolver as reclamações emergentes do processo eleitoral;\nc) Declarar candidaturas, excluir os candidatos em relação aos quais se verifique as causas de ilegibilidade;\nd) Nomear os elementos da mesa de assembleias de voto;\ne) Resolver dúvidas e omissões verificadas no decurso do processo eleitoral;\nf) Velar para que o processo se decorre num clima de paz, entendimento e que todos os seus actos se ajustem ao regime dos actos eleitorais e aos princípios da publicidade, transparência e democracia.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 51, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(52, 52, 'Apresentação de candidaturas', '1. As eleições para os diversos órgãos da OAGB dependem da apresentação de propostas de candidatura, subscritas por pelo menos 20 advogados, que devem ser efetuadas perante a comissão eleitoral, até trinta e um de outubro do ano imediatamente anterior ao início do triénio subsequente.\n2. A apresentação de propostas de candidatura não pode efetuar-se nunca em período de tempo inferior a 15 dias anteriores ao acto de votação.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 52, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(53, 53, 'Data das eleições', '1. Compete ao Bastonário marcar a data das eleições, nos termos do presente estatuto.\n2. Haverá três espécies de boletins de voto, devidamente identificados no canto superior: Bastonários, Advogados e advogados estagiários.\n3. Considera-se eleito a lista que obtiver a maioria dos votos validos.\n4. Em caso de empate, se considera eleito a lista que obtiver mais votos dos advogados, se persistir, aquela que obtiver mais votos dos bastonários, se persistir, aquela que tiver maior número dos advogados com maior tempo de exercício, e se persistir ainda, a lista do candidato a Bastonário com maior idade.\n5. O mandato nos órgãos da OAGB é de quarto anos, com início do mandato em primeiro de janeiro do ano seguinte ao da eleição, salvo os membros da mesa da Assembleia-geral.\n6. Os membros da mesa da Assembleia-geral iniciam seus mandatos em 1 de Fevereiro do ano seguinte ao da eleição.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 53, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(54, 54, 'Direito ao Voto', '1. Apenas têm direito a voto os advogados e os advogados estagiários, nos termos previstos nos presentes estatutos.\n2. O voto nas eleições dos órgãos sociais é presencial e indelegável.\n3. Os advogados votantes que se preveem ausentar para o estrangeiro no dia da votação, poderão ser autorizados a votar 15 dias antes da data marcada pra a votação, mediante apresentação do título de viagem.\n4. Tratando-se de ausências não previsíveis para o estrangeiro, os interessados deverão logo que tenham a certeza do dia, apresentar-se perante a comissão eleitoral acompanhado do título de viagem para que sejam autorizados a votar.\n5. O advogado que deixar de votar sem motivo justificado pagará multa de montante igual a 2 vezes o valor da quota mensal.\n6. A justificação da falta deve ser apresentada pelo interessado, independentemente de qualquer notificação, no prazo de quinze dias a contar da data da votação, por carta dirigida ao Bastonário.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 54, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(55, 55, 'Apresentação de Candidatura', '1. A eleição para os órgãos da OAGB depende da apresentação de propostas de candidatura, que devem ser efetuadas perante o Bastonário em exercício até 31 de Outubro do ano imediatamente anterior ao início do quadriénio subsequente.\n2. As propostas são subscritas por um mínimo de quinze advogados com inscrição em vigor.\n3. As propostas de candidaturas para o cargo de Bastonário e para membros das Direções deverão ser apresentadas em conjunto, acompanhadas das linhas gerais dos respetivos programas.', 'Direitos e Deveres', NULL, NULL, NULL, NULL, NULL, NULL, 55, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(56, 56, 'Requisitos para o Cargo de Bastonário', 'São requisitos para se apresentar como candidato ao cargo de Bastonário da OAGB, os seguintes:\na) Ter 8 anos de exercício como advogado;\nb) Não estar ligado por qualquer título permanente com o Estado;\nc) Não ocupar cargo de direção em outras organizações civis, ou órgãos dos partidos políticos, enquanto este vínculo se mantiver.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 56, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(57, 57, 'Obrigatoriedade de Exercício de Funções', 'É dever do Advogado exercer nos órgãos da OAGB função para que tenha sido eleito, constituindo falta disciplinar a recusa de tomada de posse, salvo escusa fundamentada, aceite pelo Conselho Nacional da OAGB.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 57, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(58, 58, 'Renúncia ao Cargo, Suspensão Temporária do Exercício de Funções', 'Quando sobrevenham motivos relevantes, pode o advogado titular de cargo nos órgãos da Ordem, solicitar, mediante motivo devidamente fundamentado, ao Conselho Nacional a aceitação da sua renúncia ou a suspensão temporária do exercício de funções.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 58, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(59, 59, 'Perda de Cargo', '1. Perde o cargo, o advogado que, sem motivo justificado, não exerça as suas funções com assiduidade e diligência ou dificulte o funcionamento do órgão a que pertença.\n2. A perda do cargo nos termos deste artigo será determinada pelo próprio órgão, mediante deliberação tomada por maioria dos votos dos respetivos membros, com base nos resultados do processo disciplinar instaurado nos termos do presente Estatuto.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 59, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(60, 60, 'Efeitos das Penas Disciplinares no Exercício de Cargos', '1. O mandato para exercício de qualquer cargo cessa quando o respetivo titular seja punido disciplinarmente com pena superior à de advertência e por efeito do trânsito em julgado da respetiva decisão.\n2. Em caso de suspensão preventiva ou decisão de que seja interposto recurso, o titular punido fica suspenso do exercício de funções até decisão com trânsito em julgado.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 60, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(61, 61, 'Substituição do Bastonário', '1. No caso de escusa, renúncia, perda ou caducidade do mandato, do titular ou ainda nos casos de impedimento permanente será convocada para os quinze dias posteriores uma reunião conjunta do Conselho Nacional e Conselho de deontologia e ética a qual deliberará previamente sobre a verificação do facto e em seguida sobre a substituição.\n2. Até à tomada de posse do novo Bastonário e em todos os casos de impedimento temporário, exerce as funções o membro escolhido para o efeito pelo Conselho Nacional.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 61, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(62, 62, 'Substituição dos Membros dos órgãos Colegiais', 'A substituição dos membros dos órgãos colegiais será efetuada pelos membros suplentes dos respetivos órgãos, conforme a ordem de precedência das respetivas listas eleitorais.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 62, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(63, 63, 'Mandato dos Substitutos', 'Nos casos previstos nos artigos anteriores os membros designados exercem funções até ao termo do mandato do respetivo antecessor, sendo pelo tempo de impedimento em caso de impedimento temporário.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 63, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53');
INSERT INTO `estatutos_artigos` (`id`, `numero_artigo`, `titulo_artigo`, `conteudo`, `tema`, `capitulo`, `titulo_capitulo`, `seccao`, `titulo_seccao`, `titulo_doc`, `titulo_doc_nome`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(64, 64, 'Atribuições', 'Constituem atribuições da OAGB, designadamente:\n1) Defender o Estado de Direito Social e Democrático, os interesses próprios, específicos e comuns, nomeadamente, os direitos, liberdades e garantias\n2) as acçoes legais, relacionadas com a defesa da classe e da profissão, bem como o direito de petição para a defesa da constituição da república e das leis;\n3) Defender a independência do poder judicial e colaborar na administração da justiça e na realização do direito;\n4) Organizar o exercício da profissão de advocacia e exercer a jurisdição disciplinar exclusiva sobre os seus membros;\n5) Assegurar o acesso aos tribunais e ao direito a todos os interessados, independentemente das suas posses, nos termos da constituição e das leis;\n6) Atribuir o título profissional de Advogado e de advogado estagiário, bem como regulamentar o exercício da respetiva profissão;\n7) Zelar pela função social, dignidade e prestígio da profissão de advogado, promovendo a formação inicial e permanente dos advogados e o respeito pelos valores e princípios deontológicos;\n8) Defender os interesses, direitos, prerrogativas e imunidades dos seus membros;\n9) Promover a solidariedade entre os advogados;\n10) Exercer, em exclusivo, jurisdição disciplinar sobre os advogados e advogados estagiários;\n11) Promover o acesso ao conhecimento e aplicação do direito;\n12) Contribuir para o desenvolvimento da cultura jurídica e aperfeiçoamento da elaboração do direito;\n13) Ser ouvida sobre os projetos de diplomas legislativos que interessem ao exercício da advocacia e ao patrocínio judiciário em geral e propor as alterações legislativas que se entendam convenientes;\n14) Contribuir para o estreitamento das ligações com organismos congéneres estrangeiros;\n15) Cooperar na melhoria dos estudos que conduzem a obtenção dos títulos habilitantes para o exercício da profissão de advogado;\n16) Promover e defender os valores relacionados com a deontologia profissional e a aplicação rigorosa do regime disciplinar para a defesa da sociedade;\n17) Colaborar com a administração pública no exercício das suas competências nos termos previstos na lei;\n18) Participar nos organismos consultivos do Estado, nos termos da lei, assim como nas organizações interprofissionais;\n19)  Assegurar a representação da advocacia nos conselhos sociais e nas universidades, nos termos regulados pelas normas vigentes naquelas instituições académicas;\n20) Promover a harmonia e colaboração entre os advogados, fomentar a solidariedade e evitar a concorrência desleal entre os mesmos;\n21) Intervir, previamente a solicitação dos interessados, como mediador nos conflitos profissionais que surjam entre os advogados ou entre estes e seus clientes;\n22) Exercer a função de árbitro nos assuntos que o sejam submetidos conforme a legislação de arbitragem, assim como promover ou participar nas instituições de arbitragem;\n23) Estabelecer critérios orientadores sobre os honorários profissionais, informar e determinar os honorários profissionais em processos judiciais e administrativos, ou a solicitação dos advogados;\n24) Promover, organizar e colaborar, dentro da função social da advocacia, as atividades ou serviços de interesse da sociedade\n25) Promover a imagem da profissão desde a perspetiva dos direitos, deveres, e princípios, e sua inserção na sociedade Guineense;\n26) Dedicar especial atenção aos advogados nos seus primeiros anos de exercício facilitando, o cumprimento dos seus encargos associativos e sua formação profissional;\n27) Exercer as demais funções que resultem das disposições deste Estatuto ou de outros preceitos legislativo.\n28) As demais que venham a ser atribuídas pela legislação nacional;\n29) O disposto no número anterior concretiza-se pelo respeito ao estatuto, e pelo regime estatutariamente definido dos órgãos em matéria de funcionamento e competência.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 64, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(65, 65, 'Dever de colaboração', '1. No cumprimento das suas atribuições e competências legais e estatutárias, todas as entidades públicas, autoridades judiciárias e policiais, bem como os órgãos administração pública em geral, têm o especial dever de prestar total colaboração aos órgãos da ordem dos Advogados, no exercício das suas funções.\n2. Os particulares sejam pessoas singulares ou coletivas, os organismos nacionais ou estrangeiras, sejam elas as Organizações não-governamentais ou internacionais estaduais com sede ou domicílio profissional no território da Guiné-Bissau têm o dever de colaboração com os órgãos da Ordem dos Advogados no exercício das suas atribuições.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 65, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(66, 66, 'Garantia Geral de comunicação e requisição oficial de Documentos', 'Na prossecução das suas atribuições podem os órgãos da Ordem corresponder-se com as entidades referidas no artigo anterior e tribunais e, bem assim, requisitar, sem pagamento de despesas, cópias, certidões, informações e esclarecimentos, incluindo a remessa de processo em confiança, nos termos em que os organismos oficiais devem satisfazer as requisições dos tribunais judiciais.\n\n\nSecção V', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 66, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(67, 67, 'Representação', '1. A OAGB é representada em juízo e fora dele pelo Bastonário que poderá delegar os seus poderes em qualquer dos seus Vice-presidentes.\n2. Sempre que se trate de assuntos ou questões no interior do país, a Ordem poderá fazer-se representar, em juízo ou fora dele, pelos delegados das regiões judiciais.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 67, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(68, 68, 'Representação da OAGB nas regiões judiciais', '1. A OAGB é representada nas regiões judiciais pelas delegações\n2. As deleções são compostas por um presidente, que dirige, um vice-presidente, um secretário e dois vogais, nomeados pela direção Nacional, sob proposta do Bastonário.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 68, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(69, 69, 'Competência das delegações', '3. Compete as delegações da OAGB nas regiões judiciais:\na) Assegurar as relações da OAGB com os órgãos da administração local nas regiões e tribunais.\nb) Zelar pelo cumprimento, na respetiva região judicial, das normas que rege a ordem e o exercício da profissão;\nc) Registar e controlar os escritórios dos advogados nas regiões;\nd) Executar e fazer executar as deliberações da Direção nacional\ne) Coordenar a atividade dos membros da OAGB com domicílio profissional na respetiva região judicial;\nf) Elaborar e aprova o seu regulamento;\ng) Solicitar e receber informações sobre assuntos de interesse para a OAGB nas regiões\nh) Assegurar o patrocínio oficioso nas respetivas regiões, sob a coordenação e orientação Direção Nacional', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 69, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(70, 70, 'Competência do presidente da delegação', 'Compete ao presidente da Delegação:\na) Representar o Bastonário na Região;\nb) Convocar as reuniões da delegacia;\nc) Dirigir e coordenar as atividades da Ordem na Região;\nd) Exercer os demais poderes que lhe sejam atribuídos por lei, pelo regulamento ou por delegação...\n\n\nSecção VI\nCongresso dos Advogados Guineenses', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 70, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(71, 71, 'Constituição e periodicidade', '1. O congresso é o plenário de todos os advogados com inscrição em vigor.\n2. Poderão participar no Congresso os advogados honorários e os antigos advogados cuja inscrição tenha sido cancelada por efeito de reforma.\n3. O congresso realiza-se ordinariamente de três em três anos para debruçar-se sobre os temas relacionados com o exercício da advocacia, seu estatuto e garantias dos advogados no estado de Direito Democrático, bem como as garantias de defesa e aperfeiçoamento do regime geral do exercício da Advocacias.\n4. Podem ser convidados ao congresso, juristas nacionais e estrangeiros de reconhecido mérito profissional ou académico de outros países, instituições representativas dos profissionais que operam no domínio da advocacia ou domínios a fins.\n5. As conclusões do congresso terão caracter orientador para os órgãos social da OAGB.', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 71, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(72, 72, 'Convocação, Organização e preparação,', 'O congresso é convocado pelo Bastonário com antecedência mínima de quatro meses, aplicando com as necessárias adaptações as normas relativas a organização e preparação do Congresso Nacional da advocacia.\nSECÇÃO III\nAssembleia Geral', 'Assembleias e Congressos', NULL, NULL, NULL, NULL, NULL, NULL, 72, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(73, 73, 'Composição e Competência', '1. A Assembleia Geral é composta por todos os membros da OAGB, com inscrição em vigor e sem quotas atrasadas, pelo menos três meses anteriores à data da realização da Assembleia respetiva, em pleno gozo dos seus direitos.\n2. Compete a Assembleia Geral discutir, apreciar e deliberar sobre todos os assuntos relativos à OAGB, que não estejam compreendidos nas competências específicas dos outros órgãos da Ordem, nomeadamente:\na) Aprovar o seu regulamento interno;\nb) Eleger e destituir a respetiva mesa, bem como os outros órgãos da OAGB.\nc) (mantem-se) Aprovar o relatório e plano de atividade, as contas e orçamento geral da OAGB;\nd) Apreciar a atividade dos demais órgãos da OAGB, podendo modificar, revogar ou ratificar quaisquer actos dos mesmos, sem prejuízo dos direitos de terceiros, nos termos da lei\n3. Compete a Assembleia-geral em especial, na primeira sessão ordinária, do primeiro trimestre do ano, deliberar sobre a seguinte ordem do dia:\na) Apreciar e votar o relatório do Bastonário sobre os acontecimentos mais importantes ocorridos durante o ano anterior relacionados com a justiça, o Estado de Direito e o exercício da profissão;\nb) Apreciação e aprovação das contas anuais do exercício anterior;\nc) A presentação, discussão e votação dos assuntos que o Conselho Nacional tenha inscrito na convocatória;\nd) Propostas;\ne) Perguntas e respostas;\n4. Compete a Assembleia-geral na segunda sessão, dentro do último trimestre do ano, deliberar sobre a seguinte ordem do dia:\na) Exame e aprovação do orçamento geral da OAGB para o exercício seguinte;\nb) Apresentação, discussão e aprovação dos assuntos que o Conselho Nacional tenha inscrito na ordem do dia;\nc) Propostas;\nd) Perguntas e respostas.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 73, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(74, 74, 'Mesa da Assembleia Geral', 'A mesa da Assembleia Geral é composta pelo Bastonário que preside, um vice-presidente, um secretário e um vogal, eleitos pela Assembleia-geral.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 74, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(75, 75, 'Reuniões da Assembleia Geral', '1. As reuniões ordinárias da Assembleia Geral realizar-se-ão, salvo justo impedimento, em Dezembro de cada ano.\n2. As reuniões da Assembleia Geral são convocadas pelo Bastonário, que por inerência é presidente da mesa, competindo-lhe dirigir as sessões, velar para um bom desenrolar dos trabalhos, designadamente, moderação, uso da palavra e oportunidade para submeter a votação um assunto quando suficientemente debatido.\n5. As reuniões extraordinárias da Assembleia Geral realizar-se-ão sempre que os interesses superiores da OAGB o aconselhem, convocadas pelo Bastonário, a pedido Conselho da Ordem, Direção Nacional, pela mesa da Assembleia Geral, por um terço de advogados com a inscrição em vigor, desde que seja legal o objeto da convocação e relacionado com os interesses da profissão.\n6. As reuniões da Assembleia Geral destinadas à discussão e votação do relatório de atividade e contas da OAGB realiza-se até ao final do mês de Abril do ano imediato ao do exercício respetivo.\n7. A Assembleia Geral destinada à discussão e aprovação do orçamento da Ordem reúne até ao final do mês de Novembro do ano anterior ao exercício a que diz respeito.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 75, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(76, 76, 'Convocatória', '1. As convocatórias das Assembleias-gerais assumem a forma de edital afixada na sede da ordem em lugar bem visível, correio eletrónico dirigido aos escritórios dos advogados e anúncio num dos jornais nacionais mais lido, donde conste a ordem dos trabalhos com pelo menos trinta dias de antecedência em relação a data designada para a sua realização, constando na convocatória o local, dia e a hora.\n2. Até vinte dias antes da realização das Assembleias a que se referem os números 6 e 7 do artigo anterior, é enviado aos advogados com inscrição em vigor os projetos de orçamento e do relatório e contas.\n3. Com os avisos convocatórios de Assembleias Gerais cuja ordem dos trabalhos compreenda a realização de eleições são enviados os boletins com informação de todos os candidatos admitidos.\n4. Para efeito de validade das deliberações da Assembleia Geral, só são consideradas essenciais as formalidades da convocatória referida no número 3.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 76, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(77, 77, 'Constituição e quórum', '1. A Assembleia Geral é constituída validamente em primeira convocatória com a presença de pelo menos mais de metade dos seus membros, incluindo os representados e em segunda convocatória, um terço., não podendo nenhum advogado representar mais de uma pessoa em cada reunião.\n2. A assembleia geral só poderá deliberar validamente com a presença de pelo menos, mais de metade dos seus membros.\n3. Para efeito do disposto no número anterior, o número de representações não poderá exceder um quarto do número exigido no número no n° 1 deste artigo.\nSubsecção I\nAssembleia Geral Eletiva', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 77, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(78, 78, 'Reuniões da Assembleia Geral Eletiva', 'A Assembleia-geral eletiva reúne para a eleição dos órgãos sociais da Ordem., no período compreendido entre 1 e 15 de Dezembro, do último ano do mandato, em data a ser designada pelo Bastonário.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 78, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(79, 79, 'Não convocação ou impossibilidade da assembleia-geral eletiva', '1. Quando não tenha sido convocada a Assembleia Geral eletiva no prazo fixado no estatuto por denegação do direito de renovação democrática dos mandatos, impossibilidade de constituição da Assembleia, ou ainda por falta de apresentação de lista de candidaturas ou por estas terem sido rejeitadas, proceder-se-á da seguinte forma:\na)  Nomeação pelo Conselho da Ordem de uma comissão diretiva nacional, no caso de falta de apresentação de listas de candidaturas;\nb) Marcação pelo Conselho Nacional de eleições, a realizar no prazo máximo de noventa dias, no caso de rejeição da totalidade das listas apresentadas;\nc) Marcação das eleições pelo Conselho Nacional, no caso de não convocação, no prazo de 90 dias, contados a partir da data de caducidade do mandato.\n2. Na nomeação prevista na alínea a) do número anterior, o Conselho Nacional deverá ter em consideração o resultado dos antigos bastonários eleitos, em caso de empate, o bastonário com melhor desempenho.\n3. Na ponderação dos resultados a que se refere o número anterior, deve observar-se o seguinte.\na) Verificação do número de eleitores com capacidade eleitoral ativa nas respetiva eleições;\nb) O número de votantes;\nc) O número de abstenções registadas;\n1. Feita o apuramento nos termos previstos no numero anterior, aplica-se a regra de proporção sobre os resultados obtidos, nomeado direção Nacional que será pelos elementos escolhidos pelo Bastonário vencedor.,', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 79, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(80, 80, 'Direito de Voto', '1. O voto nas Assembleias Gerais é facultativo, salvo se para fins eletivos e para os previstos nos números 3 e 4 do artigo 49.º.\n2. O voto, quando é facultativo, não pode ser exercido por correspondência, sendo, no entanto, admissível o voto por procuração a favor de outro advogado com inscrição em vigor.\n\n\nSecção IV\nConselho da Ordem', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 80, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(81, 81, 'Composição', 'O Conselho Nacional é presidido pelo Bastonário, composto por três vice-presidentes e 9 vogais, eleitos diretamente pela assembleia-geral, todos os presidentes dos outros órgãos, antigos bastonários e as Delegações regionais judiciais', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 81, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(82, 82, 'Estrutura e funcionamento', '1. O Conselho da Ordem terá um Plenário, uma Comissão Permanente e Comissão permanente restrito.\n2. A Comissão Permanente integra todos os antigos Bastonários e os membros da Direção Nacional.\n3. A Comissão permanente restrito é o órgão facultativo de consulta do Bastonário sobre as matérias da sua competência reservada.\n4. Na primeira sessão o conselho da Ordem elege, de entre os seus vogais, as delegações regionais e o seu secretário\n3. As delegações regionais, os presidentes das comissões especializadas, o presidente do gabinete de acesso ao direito, estudos e documentação da OAGB, têm assento próprio nas reuniões do Conselho da Ordem.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 82, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(83, 83, 'Competência', '1. Compete ao Conselho da Ordem reunido em plenário:\na) Definir a posição da ordem perante os órgãos de soberania e da administração pública no que se relacione com a defesa do Estado de direito, dos direitos, liberdades e garantias e com a administração da justiça;\nb) Deliberar e votar a petição de fiscalização concreta da constituição, bem como o direito de petição para a defesa dos direitos, garantias e prerrogativas relativas ao exercício de atividade de advogados.\nc) Aprovar os pareceres sobre os projetos de diploma legislativos que interessam ao exercício e ao patrocínio judiciário em geral;\nd) Aprovar as propostas de alterações legislativas que respeitem ao exercício da profissão, aos interesses dos advogados e à gestão da ordem dos advogados que não sejam especialmente cometidos a outros órgãos da Ordem;\ne) Aprovar prémios ou distinções para os advogados\nf) Confirmar a inscrição dos advogados e advogados estagiários efetuadas preparatoriamente pela Direção Nacional e manter atualizado o respetivo quadro nacional, bem como os dos advogados honorários;\ng) Julgar os recursos interpostos das deliberações da Direção Nacional;\nh) Deliberar sobre pedidos de recusa, renúncia ou de suspensão temporária de cargos;\ni) Conhecer oficiosamente ou mediante petição de qualquer advogado dos vícios das deliberações da Assembleia Geral;\nj) Elaborar e aprovar o seu próprio regulamento;\nk) Deliberar sobre o impedimento, substituição e perda do cargo dos seus membros e suspendê-los preventivamente, em caso de falta disciplinar, no decurso do respetivo processo;\nl) Definir a posição da Ordem perante os órgãos de soberania e da administração no que se relaciona com a defesa do estado de direito, dos direitos e garantias individuais e com a administração da justiça;\nm) Emitir pareceres sobre os projetos de diplomas legislativos que interessem ao exercício da advocacia e ao patrocínio judiciário em geral e propor alterações legislativas convenientes;\nn) Deliberar sobre todos os assuntos que respeitem ao exercício da profissão, aos interesses dos advogados e à gestão da Ordem.\no) Elaborar e aprovar o regulamento da inscrição dos advogados, advogados estagiários bacharéis e solicitadores, o regulamento do estágio, dos laudos do trajo e da insígnia profissional;\np) Deliberar sobre os impedimentos de exercício da profissão;\nq) Elaborar e aprovar outros regulamentos, designadamente os dos diversos serviços da Ordem, os relativos às atribuições e competência do seu pessoal e os relativos à contratação e despedimento de todo o pessoal;\nr) Formular recomendações de modo a procurar uniformizar, tanto quanto possível, a atuação das diversas Delegacias;\ns) Discutir e aprovar os pareceres dos seus membros e os solicitados pelo Bastonário a outros advogados;\nt) Fixar o valor das cotas a pagar pelos membros da Ordem dos Advogados, nomeadamente os advogados e os solicitadores;\nu) Fixar emolumentos devidos pela emissão de documentos ou prática de atos no âmbito dos serviços da Ordem, designadamente pela inscrição dos advogados, dos advogados estagiários dos bacharéis e dos solicitadores;\nv) Arrecadar e distribuir receitas da Ordem, satisfazer despesas, aceitar doações e legados feitos à Ordem e administrá-los, alienar ou obrigar bens e contrair empréstimos;\nw) Prestar patrocínio aos advogados que hajam sido ofendidos no exercício das suas funções ou por causa dele;\nx) Dar laudos sobre honorários, quando solicitados pelos tribunais, pelo Conselho Jurisdicional ou em relação às respetivas contas, por qualquer advogado ou seu representante;\ny) Deliberar sobre a instauração ou defesa de quaisquer procedimentos judiciais relativos a Ordem e sobre confissão, desistência ou transação dos mesmos;\nz) Deliberar sobre a realização do Congresso;\naa) Exercer as demais funções que as leis e os regulamentos lhe confiram.\n4. Compete a Comissão permanente do Conselho da Ordem intervir mediante nomeação de quatro dos seus membros para a constituição do plenário do tribunal de ética e disciplina, na decisão das seguintes matérias:\na) Julgamento dos processos disciplinares em que sejam arguidos o Bastonário, os antigos Bastonários e os membros das Delegacias;\nb) Julgamento dos recursos das deliberações sobre perda do cargo e exoneração dos membros das Direções;\nc) Deliberação sobre a renúncia do cargo de Bastonário;\nd) Substituição do Bastonário no caso de impedimento permanente;\ne) Atribuição da medalha de honra aos advogados a cidadãos nacionais ou estrangeiros que tenham prestado serviços relevantes na defesa do Estado de direito ou à causa da advocacia;\nf) Conferir título de advogado honorário a advogados que tenham deixado a advocacia depois de a haverem exercido distintamente durante 15 anos, pelo menos, e se tenham assinalado como juristas eminentes.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 83, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(84, 84, 'Reuniões', '1. O Conselho da Ordem reúne-se, ordinariamente, pelo menos uma vez por mês e extraordinariamente por iniciativa do Bastonário ou mediante solicitação da maioria absoluta dos seus membros.\n2. Sempre que o Presidente do Conselho da Ordem não esteja presente, o voto de qualidade assiste ao vice-presidente que presida à respetiva reunião\nSECÇÃO IV\nBastonário', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 84, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(85, 85, 'Bastonário', '1. O Bastonário é por inerência, presidente do Congresso, da Assembleia Geral e do Conselho da Ordem.\n2. Sempre que o Bastonário julgar conveniente, poderá convocar as reuniões das comissões especializadas, competindo-lhe dirigir os trabalhos e votações.\n3. O Bastonário tem voto de qualidade em caso de empate.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 85, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(86, 86, 'Competência', '1. Compete ao Bastonário:\na) Representar a Ordem em juízo e fora dele, designadamente perante os órgãos de soberania;\nb) Proferir discursos em representação da OAGB nas cerimónias oficiais;\nc) Nomear o Secretario Geral da OAGB;\nd) Supervisionar os serviços da OAGB\ne) Velar pelo cumprimento da legislação respeitante à Ordem dos advogados, e respetivos regulamentos e zelar pela realização das suas atribuições;\nf) Fazer executar as deliberações do Congresso, do Conselho da Ordem, do tribunal de deontologia e disciplina e dar seguimento às recomendações do congresso\ng) Promover a cobrança das receitas da Ordem, autorizar despesas e promover a abertura de créditos extraordinários, quando necessários;\nh) Apresentar anualmente à Assembleia Geral o projeto de orçamento para o ano civil seguinte, as contas do ano civil anterior e o relatório sobre as atividades anuais;\ni) Promover, por iniciativa própria ou a solicitação das Direções, os atos necessários ao patrocínio dos Advogados ou para que a Ordem se constitua assistente;\nj) Cometer a quaisquer órgãos da Ordem ou aos respetivos membros a elaboração de pareceres sobre quaisquer matérias que interessem às suas atribuições;\nk) Assistir, querendo, às reuniões de todos os órgãos colegiais da Ordem, não tendo direito de voto nas reuniões do Conselho Jurisdicional;\nl) Usar o voto de qualidade, em caso de empate, nos órgãos colegiais que preside;\nm) Encaminhar para o Conselho Jurisdicional as deliberações de todos os órgãos que julgue contrárias as leis e regulamentos ou aos interesses da Ordem ou dos membros\nn) Exercer as demais atribuições que as leis e regulamentos lhe confiram;\no) Manter atento sobre as eventuais omissões e a necessidade de ajustar os estatutos a dinâmica de defesa do exercício da atividade da advocacia e promover por sua iniciativa a revisão dos mesmos;\n2. O Bastonário pode delegar em qualquer membro do Conselho da Ordem alguma ou algumas das suas atribuições.\n3. O Bastonário pode também, com o acordo do Conselho da Ordem delegar a representação da Ordem ou atribuir funções específicas a qualquer advogado que integre o Conselho da Ordem.\n4. O Bastonário pode ainda consultar os antigos Bastonários ou em reuniões por ele presididas, e delegar neles a sua representação, contanto que nenhum outro membro do Conselho da Ordem esteja disponível para tanto.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 86, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(87, 87, 'Moção de censura', '1. Qualquer advogado ou grupo de advogados com inscrição em vigor e em pleno exercício dos seus direitos, sem penalização anterior superior a repreensão registada poderá liderar a iniciativa de apresentação de moção de censura contra o Bastonário mediante proposta sobescrita por um numero de advogados, não inferior à 20%, com regular inscrição, em efetividade de exercício, com pelo menos 1 ano de exercício anterior a data da apresentação da moção, com indicação expressa e descriminada das razoes em que se funda a censura.\n2. Não é permitido apresentar a moção de censura nos seis meses seguintes a da data da tomada de posse.\n3. Cumpridos os requisitos previstos nos números anteriores, o presidente do Conselho da Ordem deverá convocar a Assembleia geral extraordinária no prazo na superior a quinze dias contados desde a data da apresentação da moção.\n4. A convocatória da Assembleia geral, para o debate da moção de censura, deverá efetuar-se com antecedência mínima de vinte dias e máxima de trinta dias.\n5. A Assembleia geral convocada para a finalidade deste artigo, considera-se validamente constituída na primeira convocatória se estiverem presentes mais de metade dos subscritores, mais pelo menos 30% dos advogados com direito de voto.\n6. Se não se atingir o quórum exigido no úmero anterior, poderá em segundo convocatória, realizar a assembleia com a presença de mais de metade dos subscritores e 20% dos advogados com direito de voto, podendo as duas convocatórias ser simultâneas numa única convocatória.\n7. O debate iniciará com a apresentação da moção pelo proponente ou proponentes seguindo a defesa do censurado, podendo o Bastonário, querendo e com o acordo de nomeado, contestar a moção.\n8. A Assembleia-geral extraordinária poderá desenrolar-se em uma ou mais secções para debate e votação.\n9. Para a aprovação da moção de censura será necessário o voto favorável de mais de metade dos advogados presentes na assembleia.\n10. Se a moção não obtiver aprovação, não poderá ser apresentada uma segunda dentro de um ano, contado a partir da data da apresentação da primeira moção.\n11. Aprovada a moção cessará de imediato o mandata de todos os órgãos sociais eleitos, devendo ser convocada e marcada a data de eleições na mesma Assembleia.\nSECÇÃO V\nDireção Nacional', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 87, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(88, 88, 'Natureza e Composição', '1. A Direção Nacional é o órgão executivo da Ordem.\n2. A Direção Nacional é constituída pelo Bastonário que preside, com voto de qualidade, três vice-presidentes, dois vogais, eleitos pela Assembleia Geral, um Secretario Geral, um tesoureiro e um bibliotecário, nomeados pela direção Nacional sob proposta do Bastonário.\n3. A direção Nacional reúne sempre que convocado pelo Bastonário ou a pedido de pelo menos três dos seus membros eleitos na Assembleia-geral.\n4. O Secretario Geral é o principal funcionário Administrativo da Ordem.\n5. O Direção nacional tem a seguinte estrutura\na) Direção executiva, e\nb) Conselho diretivo:\n5. A direção executiva integra unicamente os membros eleitos e o Secretario Geral,\n6. Nas reuniões da direção executiva o Secretario Geral não tem direito a voto.\n7. Compete a direção executiva:\na) Elaborar o seu próprio regulamento;\nb) Elaborar e submeter para aprovação ao Conselho da Ordem a proposta do regulamento de inscrição dos advogados e dos advogados estagiários;\nc) Elaborar e submeter para aprovação ao Conselho Nacional a proposta do regulamento do estágio, da formação continua e da formação especializada, com inerente atribuição do título de advogado especialista;\nd) Homologar as inscrições dos advogados e advogados estagiários;\ne) Aprovar a proposta de orçamento e do relatório de atividades e contas;\nf) Propor ao Conselho da Ordem a criação de institutos, observatórios, departamentos ou comissões especializadas,\ng) Propor o valor das quotas a pagar pelos advogados;\nh) Propor os valores de emolumentos devidos pela emissão de documentos ou prática de atos no âmbito dos serviços da Ordem dos Advogados, designadamente pela inscrição dos advogados e advogados estagiários;\n6. O Conselho técnico integra a direção nacional, o secretario Geral, presidentes das comissões especializadas e o presidente do gabinete de acesso ao direito, estudos e documentação da OAGB.\n7. Compete ao Conselho técnico:\na) Preparar as reuniões do conselho da Ordem;\nb) Deliberar em recurso das decisões do Bastonário sem recurso;\nc) A supervisão técnica e científica das atividades da OAGB;\nd) A definição da proposta da ordem do dia do Conselho Nacional;\ne) A aprovação preliminar do relatório de atividade e contas da direção nacional\nf) Opinar sempre que solicitado pelo Bastonário nos assuntos da sua competência própria;\n8. O Bastonário poderá convidar os antigos Bastonários para as reuniões do Conselho Diretivo', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 88, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(89, 89, 'Secretário-geral da OAGB', '1. A Secretário-geral é o principal funcionário Administrativo da OAGB, o qual é assistido pelo número de pessoal indispensável para assegurar a boa Administração da OAGB, a quem cabe elaborar o relatório anual sobre as atividades da OAGB.\n2. O Secretario Geral é quem por inerência assegura o secretariado de todas as reuniões da Direção Nacional, e fiel depositário de todas as actas e relatórios do Conselho Nacional, Assembleia Geral, Congresso Nacional da Advocacia e do Congresso nacional dos Advogados Guineenses.\n3. No exercício das suas competências, compete ao Secretário-geral fazer propostas de contração do pessoal administrativo da OAGB, sob a sua direção e disciplina.\n4. Compete em especial ao Secretário-geral no exercício da sua função administrativa, superintender, nomeadamente:\na) Os actos gerais da secretária-geral da OAGB\nb) A realização de inscrições dos advogados e advogados estagiários na OAGB;\nc) Zelar pela boa administração geral da ordem.\nd) A boa organização do expediente geral da Ordem;\ne) A Recepçao e expedição das correspondências da Ordem;\nf) A boa conservação e desenvolvimento do património geral da Ordem;\n\n\nSECÇÃO VI\nTribunal de ética e disciplina', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 89, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(90, 90, 'Composição', '1. O tribunal de ética e disciplina é o órgão jurisdicional da Ordem dos Advogados, composto por um presidente, um vice-presidente e mais três conselheiros.\n2. O Presidente e vice-presidente são escolhidos de entre os antigos bastonários, incluindo o bastonário cessante.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 90, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(91, 91, 'Função jurisdicional', '1. Na administração da justiça incumbe ao tribunal de ética e disciplina assegurar a defesa dos estatutos da OAGB, regulamentos internos, a deontologia própria da profissão e disciplina.\n2. O regulamento interno do tribunal é aprovado pela Assembleia Geral da OAGB', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 91, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(92, 92, 'Decisões do tribunal', '1. As decisões do tribunal são fundamentadas nos casos e nos termos previstos na lei substantiva da OAGB e no processo disciplinar.\n2. As decisões do tribunal com trânsito em julgado são obrigatórias, vinculando todas as entidades públicas e privadas, incluindo os tribunais judiciais.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 92, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(93, 93, 'Competência', '1. Compete ao tribunal de ética e disciplina, reunido em sessão plenária:\na) Julgar os processos disciplinares instaurados pelo Conselho de deontologia e ética;\nb) A fiscalização concreta dos estatutos e dos regulamentos da OAGB, declarando contrários aos estatutos actos ou normas que infrinja o disposto neles ou princípios neles consignados;\nc) Verificar o pedido de escusa, de renúncia e de suspensão temporária de cargo, procedendo a sua homologação;\nd) Julgar os recursos das decisões dos órgãos da Ordem dos Advogados que determinarem a perda de cargo de qualquer dos seus membros;\ne) Verificar impedimentos e incompatibilidades e declara-los a pedido do Conselho de deontologia e ética\nf) Determinar a suspensão preventiva, em caso de falta disciplinar, no decurso do respetivo processo;\n2. Compete ao tribunal de ética e disciplina e os membros permanentes do conselho Nacional, em reunião conjunta:\na) Verificar em recurso a perda do cargo e exoneração dos membros do conselho Nacional e do conselho de deontologia e ética;\nb) Verificar a renúncia ao cargo de bastonário;\nc) Julgar os processos em que sejam arguidos o bastonário, antigos bastonários e os membros atuais do conselho nacional e do conselho de deontologia e ética;\n3. Ficam sujeitos ao regime de impugnação das deliberações sociais, regulado no código do processo civil, todas as deliberações do Conselho Nacional e da Assembleia Geral da OAGB, não mencionadas neste artigo;\nSECÇÃO VI\nConselho de deontologia e ética', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 93, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(94, 94, 'Composição', '1. O Conselho de deontologia e ética é órgão de fiscalização da observância das regras de deontologia profissional, composto por um antigo bastonário que o preside, com voto de qualidade, por dois vice-presidentes e dois vogais eleitos pela Assembleia Geral.\n2.  Na primeira sessão, o Conselho elege, de entre os dois vogais, um secretário, preferencialmente mais jovem.\n3. Sempre que o Presidente do Conselho não esteja presente, o voto de qualidade assiste ao vice-presidente que presida à respetiva reunião.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 94, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(95, 95, 'Competências', '1. Compete ao Conselho de deontologia e ética:\ni) Instruir os processos de mediação em que sejam partes os advogados ou advogados e cidadãos;\nj) Julgar em primeira instância os conflitos de honorários mediante processo de arbitragem\nk)  Apreciar e deliberar sobre a verificação de impedimentos e incompatibilidades;\nl) Fiscalizar o cumprimento das normas estatutárias e regulamentares, relativos ao exercício da profissão, cumprimento das recomendações da Assembleia Geral e Conselho Nacional relativos a observância das regras de deontologia profissional, emitindo sobre os mesmos os respetivos pareceres;\nm) Submeter os pareceres, conforme os casos, à Assembleia Geral e o tribunal de deontologia e disciplina', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 95, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(96, 96, 'Reuniões', 'O Conselho de deontologia e ética reúne sempre que for convocado para o exercício das suas competências e, ordinariamente, uma vez em cada trimestre, ou ainda por iniciativa do Presidente ou mediante solicitação por escrito da maioria absoluta dos seus membros.', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 96, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(97, 97, 'Distinção e permeios', '1. O Conselho da Ordem poderá conferir distinções de honra, nos termos do estatuto, aos advogados que se destacaram pelos serviços relevantes prestados à Ordem ou a causa da advocacia em geral.\n2. O Conselho da Ordem poderá igualmente conferir tal distinção aos advogados com antiguidade superior a cinquenta anos, sempre que durante a sua experiencia profissional não conste qualquer sanção disciplinar no seu curriculum.\nTÍTULO III\nDO EXERCÍCIO DA ADVOCACIA\nCAPITULO I\nDisposições gerais\nSecção I', 'Órgãos de Governação', NULL, NULL, NULL, NULL, NULL, NULL, 97, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(98, 98, 'Atividade da advocacia', '1. O exercício da atividade de advocacia no território da Guiné-Bissau é reservado exclusivamente aos Advogados e Advogados estagiários com inscrição em vigor na OAGB\n2. Os advogados estagiários são obrigados a indicação dessa qualidade em todas as suas intervenções oficiais.\n3. Só os advogados e advogados estagiários, a que se refere o número anterior, podem em todo o território nacional, exercer a atividade de advocacia perante qualquer jurisdição, instância, autoridade ou entidade pública ou privada, praticar actos próprios da profissão e, designadamente, exercer o mandato judicial ou funções de consulta jurídica em regime de profissão liberal remunerada.\n4. A profissão de advogado é exercida em regime de profissão liberal, independência e rege-se pelo presente estatuto, as regras de deontologia e ética e pelas demais disposições legais aplicáveis.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 98, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(99, 99, 'Liberdade de exercício', '1. O exercício da atividade da advocacia é livre e independente, sendo inviolável o advogado no seu exercício profissional, seus actos e manifestações, salvo disposição expressa da lei.\n2.  Não é permitido as autoridades públicas ou privada, entidade, instância ou jurisdição, conjunta ou separadamente impedir o livre exercício do mandato forense por qualquer forma, salvo nos casos previstos na lei.\n3. Aos advogados, quando no exercício profissional, devem ser dispensados tratamento compatível com a dignidade da profissão e condições adequadas para o cabal desempenho da função, de acordo com as garantias consignadas no Estatuto da Ordem dos Advogados e demais legislação aplicável.\n4. O advogado que for vítima de atentado ou agressão contra as suas garantias ao livre exercício da profissão, deve denunciar o facto ao Bastonário, a quem compete tomar diligências que se revelarem adequadas para restaurar a dignidade e o respeito da profissão.\n5. Para o cumprimento dos fins previstos no número anterior, o Bastonário apos recebido a participação, em articulação com a Comissão de acesso a justiça, julgando necessário, designará o advogado ou advogados, investidos em poderes bastantes para o efeito.\n6. Os advogados devem ser assegurados, em todos os tribunais, juízos, cartórios do Ministério publico, esquadras de polícia ou órgãos de polícia de Ordem Publica, polícia judiciária, salas especiais permanentes para a prática de actos urgentes indispensáveis a boa administração da justiça, ficando sob a disponibilidade e responsabilidade da OAGB.\n7. Quando um advogado for agredido, no exercício da profissão ou em desempenho do cargo da OAGB, o Conselho Nacional deve fazer um pronunciamento publico a favor do agredido, sem prejuízo da responsabilidade criminal do infrator.\n8. Correndo inquérito contra um advogado por haver fortes indícios materiais da prática de um crime, mediante despacho motivado do juiz, poderá este determinar a suspensão do direito previsto no n° 4 al. b) do artigo 6° dos presentes estatutos, e emitir o mandado de busca e apreensão, específico e pormenorizado, que será cumprido na presença de um representante da OAGB.\n9. Não obstante, o mandado de busca não pode abranger os documentos de clientes, os objetos pertencentes aos clientes do advogado, bem como dos demais instrumentos de trabalho com informações dos clientes.\n10.   Fica excluído do âmbito da aplicação do número anterior, os abjetos e documentos do cliente formalmente constituído suspeito pela prática do crime que deu causa a suspensão da inviolabilidade do escritório do advogado.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 99, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(100, 100, 'Função forense', '1. A função forense, consiste no exercício da atividade por lei reservada aos advogados, incumbindo-lhes, nos termos da lei, a defesa dos direitos, interesses e garantias individuais dos cidadãos, requerendo, em caso de necessidade, a intervenção dos órgãos policiais e jurisdicionais competentes.\n2. Os advogados estagiários praticam os atos próprios dos advogados, nos termos regulados no presente diploma e no regulamento do estágio.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 100, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(101, 101, 'Estatuto legal do Advogado', '1. O advogado goza, em todas as instituições, autoridades, órgãos, instâncias, na administração da justiça e na administração pública em geral, de imunidade para cumprir o mandato e desempenhar as suas funções cabalmente.\n2. As Autoridades, os servidores públicos e os funcionários da justiça devem dispensar ao advogado, no exercício da profissão, tratamento compatível com a dignidade da advocacia e condições adequadas ao seu desempenho profissional.\n3. Os advogados são independentes face aos órgãos da Administração da justiça, sendo inviolável no exercício de sua atividade, dentro do respeito à Constituição da república e às leis.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 101, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(102, 102, 'Mandato forense', '1. O mandato forense é documento através do qual o advogado é conferido pela parte interessada, nos termos da legislação em vigor, poderes para o representar ou intervir em juízo ou fora dele nos assuntos do seu interesse próprio, agindo por si livremente, nos termos permitidos por lei.\n2. Em caso de urgência, o advogado pode atuar sem mandato forense, obrigando-se a apresentá-lo no prazo de cinco dias, prorrogável por igual período.\n3. O advogado que renunciar ao mandato continuará, durante os dez dias seguintes à notificação da renúncia, a representar o mandante, salvo se for substituído antes do término desse prazo, comunicando, após, o juiz.\n4. No exercício do mandato, os advogados devem agir com total independência e autonomia técnica e de forma isenta e responsável, encontrando-se apenas vinculados a critérios de legalidade e às regras deontológicas próprias da profissão.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 102, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(103, 103, 'Deveres para com a Comunidade', 'Constituem deveres do advogado para com a comunidade:\na) Pugnar pela boa aplicação das leis, pela rápida administração da justiça e pelo aperfeiçoamento das instituições jurídicas;\nb) Não advogar contra lei expressa, não usar de meios ou expedientes ilegais, nem promover diligências reconhecidamente dilatórias, inúteis ou prejudiciais para a correta aplicação da lei ou a descoberta da verdade;\nc) Recusar o patrocínio a questões que considere injustas;\nd) Colaborar no acesso ao direito e aceitar nomeações oficiosas nas condições fixadas na lei e pela Ordem;\ne) Protestar contra as violações dos direitos humanos e combater as arbitrariedades de que tiver conhecimento no exercício da profissão;\nf) Não solicitar nem angariar clientes, por si nem por interposta pessoa;\ng) Não aceitar mandato ou prestação de serviços profissionais que, em qualquer circunstância, não resulte de escolha direta e livre pelo mandante ou interessado.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 103, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(104, 104, 'Deveres para com a OAGB', 'Constituem Deveres do Advogado para com a Ordem:\na) Não prejudicar os fins e prestígio da Ordem e da advocacia;\nb) Exercer os cargos para que tenha sido eleito ou nomeado e desempenhar com zelo e dedicação os mandatos que lhe forem confiados;\nc) Colaborar na prossecução das atribuições da Ordem;\nd) Observar os costumes e praxes profissionais;\ne) Declarar, ao requerer a inscrição, para efeito de verificação de incompatibilidade, qualquer cargo ou atividade profissional que exerça;\nf) Suspender imediatamente o exercício da profissão e requerer, no prazo máximo de trinta dias, a suspensão da inscrição na Ordem quando ocorrer incompatibilidade ou impedimento superveniente;\ng) Pagar pontualmente as quotas e outros encargos devidos à Ordem, estabelecidos neste Estatuto e nos regulamentos, suspendendo-se o direito de votar e de ser eleito para os órgãos da Ordem se houver atraso superior a três meses;\nh) Dirigir com empenho o estágio dos advogados estagiários e elaborar a respetiva informação final;\ni) Comunicar, no prazo de trinta dias, qualquer mudança de escritório.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 104, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(105, 105, 'Documentos e Valores do Cliente', '1. Quando cessar a representação confiada ao advogado, deve este, restituir os documentos, valores ou objetos que lhe hajam sido entregues e que sejam necessários para prova do direito do cliente ou cuja retenção possa trazer a este prejuízo graves.\n2. Com relação aos demais valores e objetos em seu poder, goza o advogado do direito de retenção para garantia do pagamento dos seus honorários e reembolso de despesas.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 105, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(106, 106, 'Informação e Publicidade', '1. O Advogado deve divulgar a sua atividade profissional de forma objetiva, verdadeira e digna, no rigoroso respeito dos deveres deontológicos e segredo profissional.\n2. É vedada ao advogado toda a espécie de reclamo por circulares, anúncios, meios de comunicação social ou por qualquer outra forma, direta ou indireta, de publicidade profissional designadamente divulgando o nome dos seus clientes.\n3. Os advogados não devem fomentar, nem autorizar, notícias referentes a causas judiciais ou outras questões profissionais a si confiadas.\n4. Não constituem formas de publicidade a indicação de títulos académicos, a menção de cargos exercidos na Ordem ou a referência à sociedade civil profissional de que o advogado seja sócio, devendo qualquer outra menção ser previamente autorizada pelo Conselho Nacional.\n5. Não constitui também publicidade o uso de tabuletas afixadas no exterior dos escritórios, a inserção de meros anúncios nos jornais, a utilização de cartão-de-visita ou papel de carta, desde que com simples menção do nome do advogado, endereço do escritório e horas do expediente.\n6. Nas publicações especializadas de advogados pode ainda inserir-se curriculum vitae académico e profissional do advogado e eventual referência à sua especialização, se previamente reconhecida pela Ordem.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 106, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(107, 107, 'Direito de coadjuvação pelas entidades públicas', 'No exercício da profissão os advogados têm direito à coadjuvação das entidades públicas.\n\nSecção II', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 107, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53');
INSERT INTO `estatutos_artigos` (`id`, `numero_artigo`, `titulo_artigo`, `conteudo`, `tema`, `capitulo`, `titulo_capitulo`, `seccao`, `titulo_seccao`, `titulo_doc`, `titulo_doc_nome`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(108, 108, 'Início de Atividade', 'a) Estudar e dar parecer sobre pedidos de inscrições nos quadros de advogados e estagiários, examinando e verificando o preenchimento dos requisitos legais;\nb) Apreciar as impugnações aos pedidos de inscrição, emitindo parecer fundamentado, para posterior apreciação e julgamento pela Primeira Câmara;\nc) Verificar o efetivo exercício profissional por parte dos inscritos, bem como os casos de impedimento, incompatibilidade, licenciamento ou cancelamento da inscrição;\nd) Determinar, quando for o caso, exame de saúde, a ser realizado pela Caixa de Assistência dos Advogados do Acre, visando a promover eventual licenciamento do profissional;\ne) Examinar pedidos de transferência e de inscrição suplementar;\nf) Promover a representação prevista no art. 10, § 4º, da Lei nº. 8906/94, em caso de transferência ou inscrição suplementar, desde que verificado vício ou possível ilegalidade na inscrição principal;\ng) Deferir a expedição de carteiras profissionais e cédulas de identidade, bem como vias suplementares em casos de extravio, perda ou mau estado de conservação;\nh) Recolher as carteiras e cédulas dos advogados, ou profissionais excluídos, suspensos ou impedidos do exercício da advocacia, assim como daqueles que tiverem suas inscrições canceladas;\ni) Em caso de recusa de entrega da carteira profissional, na forma prevista no dispositivo anterior, propor a tomada das medidas cabíveis, inclusive de natureza judicial, para obter a restituição do documento;\nj) Autorizar, de imediato, a alteração do nome da profissional em virtude de casamento, separação judicial ou divórcio, desde que comprovado por documento hábil a mudança;\nk) Anotar nas carteiras o cancelamento das inscrições, assim como os licenciamentos e impedimentos.\nO inicio de atividade de advocacia depende da verificaçao dos seguintes requisitos comulativos:\na) Aquisição de instalações para manter escritório ou associar-se a um escritório já existente em espaço condigno, adequado e afeto exclusivamente a atividade de advocacia, constituindo este o seu domicílio profissional;\nb) Registo do escritório na Ordem dos Advogados;\nc) Registo do Carimbo profissional na Ordem dos advogados;\nd) Registo do carimbo do escritório da Ordem dos Advogados\ne) Aquisição do uniforme profissional;', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 108, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(109, 109, 'Regime jurídico do acto próprio do advogado', '1. O acto do advogado faz prova plena da data, assinatura do autor e da qualidade em que o assina.\n2. Os advogados são obrigados a redigir de forma clara, precisa e legal os atos dos advogados que praticam, assegurando aos seus clientes a garantia legal de produção de efeitos para os quais contrataram os seus serviços.\n3.  São menções obrigatórias no acto do advogado:\na) Assinatura do autor;\nb) Data\nc) N° de inscrição na Ordem;\nd)  Carimbo profissional, donde consta a morada do escritório;\ne) Vinhete nos casos em que é obrigatória a sua aposição;', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 109, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(110, 110, 'Atos próprios dos advogados', '1. São atos próprios dos advogados:\na) O mandato forense\nb) Elaboração e Impugnação dos atos judiciais, civis, administrativos, tributários e afins;\nc) A atividade de consultoria;\nd) A elaboração de contratos e a prática dos atos preparatórios tendentes à constituição, alteração ou extinção de negócios jurídicos, seja aqueles para os quais a lei exige a escritura pública, designadamente os praticados perante as conservatórias e cartórios notariais;\ne) A negociação tendente à cobrança de créditos;\n1. Consideram-se atos próprios dos advogados e dos solicitadores os atos que, nos termos do número anterior, forem exercidos no interesse de terceiros e no âmbito de atividade profissional, sem prejuízo das competências próprias atribuídas às demais profissões ou atividades cujo acesso ou exercício é regulado por lei.\n2. São também atos próprios dos advogados todos aqueles que resultem do exercício do direito dos cidadãos a fazer acompanhar por advogado perante qualquer autoridade.\n3. Nos casos em que o processo penal determinar que o suspeito seja assistido por defensor, esta função é obrigatoriamente exercida por advogado, nos termos da lei.\n4. Os advogados estagiários, regularmente inscrito, pode praticar os atos previstos no n° 1, nos termos estabelecidos no regulamento do estágio, em conjunto com advogado e sob responsabilidade deste.\n5. Os atos próprios dos advogados e solicitadores relativos a contratos constitutivos de pessoas jurídicas, intervenções judiciais, exercício do mandato no âmbito de reclamação ou impugnação de atos administrativos ou tributários, sob pena de ineficazes ou não admitidos a registo, nos órgãos competentes, devem ser visados por carimbos próprios dos advogados.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 110, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(111, 111, 'Atos próprios dos solicitadores', '1. São atos dos Solicitadores:\na) O exercício do mandato forense nos termos previsto nas leis do processo;\nb) A elaboração de contratos e a prática dos atos preparatórios tendentes à constituição, alteração ou extinção de negócios jurídicos, seja aqueles para os quais a lei exige a escritura pública, designadamente os praticados perante as conservatórias e cartórios notariais;\n2. Para os efeitos do disposto no artigo anterior e no número anterior, não se consideram atos próprios dos advogados e solicitadores os praticados no interesse de terceiros, pelos representantes legais, empregados, funcionários ou agentes de pessoas singulares ou coletivas, publicas ou privadas, nesta qualidade, salvo se, no caso de cobrança de dívidas, esta constituir o objeto ou atividade principal destas pessoas.\n3. Não se inclui nos atos próprios dos advogados e solicitadores o requerimento de habeas corpus em qualquer instância ou tribunal.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 111, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(112, 112, 'Prova do acto próprio do advogado e solicitador', '1. A prova do acto próprio do Advogado e solicitador é feita mediante aposição da assinatura do advogado no acto, carimbo pessoal e n° de inscrição na OAGB\n2. Os elementos da prova do acto próprio do advogado e solicitador constante do n° anterior, são obrigatórios em actos constitutivos de pessoas coletivas, indispensáveis ao seu registo e arquivamento nos órgãos competentes.\n3. O funcionário publico, no acto de recepçao para efeitos legais, registo e arquivamento dos documentos de competência do advogado e solicitador, deve verificar e confirmar a constatação efetiva de que os respetivos documentos as exigências legais profissional.\n4. A prática de actos próprios de advocacia, por profissionais e sociedades não inscritos na OAGB, constitui exercício ilegal da profissão.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 112, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(113, 113, 'Atos próprios dos advogados nulos', '1. São nulos os atos próprios dos advogados e solicitadores praticados por pessoas sem inscrição na OAGB.\n2. São também nulos os atos próprios dos advogados praticados por advogados e solicitadores impedidos, no âmbito do impedimento, suspensos ou que passar a exercer atividade incompatível com a advocacia.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 113, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(114, 114, 'Competência dos Estagiários', '1. Durante o período do estágio, o estagiário não pode:\na) Praticar actos próprios da profissão de advogado ou de solicitador judicial senão em causa própria ou do seu cônjuge, ascendentes ou descendentes;\nb) Exercer a advocacia em processos penais de competência do Tribunal de Sector;\nc) Exercer a advocacia em processos cíveis cujo valor não caiba a alçada Tribunal de Primeira Instância;\nd) Exercer a advocacia em processos de divórcio por mútuo consentimento;\ne) Exercer consulta jurídica.\n2. Pode, no entanto, o advogado estagiário praticar actos próprios da advocacia em todos os demais processos, independentemente da sua natureza e do seu valor, desde que efetivamente acompanhado de advogado que assegure a tutela do seu tirocínio.\n3. O estagiário deve indicar sempre a sua qualidade quando intervenha em qualquer acto de natureza profissional.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 114, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(115, 115, 'Advogados em serviço público do Estado', '1. Exercem a advocacia em serviço público, os advogados inscritos na OAGB, e livremente, pessoal ou através do escritório solicitem a inscrição no sistema da justiça gratuita, para patrocínio oficioso das pessoas carenciadas, nos termos definidos por lei.\n2. Os advogados que prestam serviço público da advocacia são elegíveis e podem integrar qualquer órgão da OAGB.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 115, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(116, 116, 'Jurisconsultos', '1. Podem exercer consulta jurídica, para além dos advogados com inscrição em vigor na Ordem dos Advogados, juristas cujo grau de licenciatura seja reconhecida por órgãos competentes do Estado da Guiné-Bissau, solicita a sua inscrição na Ordem dos Advogados para o efeito e aceite.\n2. Excetua-se do disposto no número anterior a elaboração de pareceres escritos por docentes das faculdades de direito, no âmbito da função académica, sobre matérias que atua e professores doutores em direito.\n3. Não se inclui no n° 2, os pareceres jurídicos emitidos pelos funcionários ou agentes e empregados de pessoas singulares ou coletivas, públicas ou privadas, nesta qualidade para os fins das entidades onde prestam as suas atividades.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 116, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(117, 117, 'A lista dos advogados', 'Deve a Comissão de Acesso à Justiça, em cada três meses, enviar aos tribunais e aos departamentos do Governo, a lista atualizada dos Advogados e solicitadores com inscrição e quotas regulares, e zelar pelo seu cumprimento e fiscalização.\nCAPITULO II\nDEONTOLOGIA PROFISSIONAL\nSecção I\nPrincípios gerais', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 117, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(118, 118, 'DEVER DE INTEGRIDADE PROFISSIONAL', '1. No exercício da profissão, o advogado deve proceder com correção indispensável de forma que o seu comportamento público e profissional o torne merecedor de respeito e responsabilidades de funções que exerce, contribuindo para o prestígio da classe e da advocacia.\n2. No exercício da profissão, o advogado obriga a abster-se, de qualquer contacto ilícito com os órgãos da administração da justiça, nomeadamente magistrados, ou seus auxiliares, nomeadamente, as testemunhas, peritos, ou outros, capaz de influenciar os seus comportamentos e prejudicar a descoberta a boa decisão da causa, não negociar ou entrar em negociação sobre a lei ou deontologia profissional com os órgãos da administração da justiça.\n3. São obrigações profissionais do advogado a honestidade, lealdade, probidade, retidão, lealdade, cortesia e sinceridade.\n4. A relação entre os advogados no exercício da profissão, impõem nomeadamente, os seguintes deveres:\na) A relação de confiança e cooperação entre os advogados em benefício dos clientes;\nb) Evitar litígios inúteis, conciliando, tanto quanto possível, os interesses da profissão com os da justiça ou daqueles que a procuram;\nc) Proceder com maior correção e urbanidade, abstendo-se de qualquer ataque pessoal, alusão deprimente ou crítica desprimorosa, de fundo ou de forma;\nd) Responder; em prazo razoável, às solicitações orais ou escritas do colega;\ne) Não emitir publicamente opinião sobre questão que saiba confiada à outro advogado, salvo na presença deste ou com o seu prévio acordo;\nf) Atuar com maior lealdade, procurando não obter vantagens ilegítimas ou indevidas para o seu cliente;\ng) Não contactar a parte contraria que esteja representada por advogado, salvo se previamente autorizado por este, ou se tal for indispensável, por imposição legal ou contratual;\nh) Não assinar pareceres, peças processuais ou outros escritos profissionais que não sejam da sua autoria ou em que não tenha colaborado;\ni) Comunicar, atempadamente, a impossibilidade de comparecer a qualquer diligência aos outros advogados que nela devam intervir;\nj) Não receber ou não iniciar a sua atuação num assunto anteriormente confiado a outro advogado, sem antes diligenciar no sentido de a este ser pago os seus honorários e demais quantias que a este sejam devidas, devendo expor ao colega, oralmente ou por escrito, as razoes da aceitação do mandato e dar-lhe conta dos esforços que tenha desenvolvido para aquele efeito;\nk) Que, sempre que um advogado deseja que a sua comunicação dirigida a um outro advogado seja confidencia, deve exprimir claramente essa preocupação;\n5. As comunicações confidenciais não podem, em qualquer caso, constituir meio de prova, não lhes sendo aplicável o regime do segredo profissional.\n6. O advogado destinatário da comunicação que não tenha condições para garantir a confidencialidade da mesma deve devolvê-la ao remetente sem revelar à terceiros o respetivo conteúdo.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 118, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(119, 119, 'Independência', '1. O advogado, no exercício da profissão, deve manter-se sempre independente em quaisquer circunstâncias, subordinando-se apenas a lei, devendo agir livre de qualquer pressão, atuar com diligência e lealdade devida aos interesses do seu cliente, abster-se especialmente de influência que resulte dos seus próprios interesses ou de influências exteriores, quer sejam eles políticos ou económicos, cumprindo pontual e escrupulosamente os deveres consignados no presente Estatuto e todos aqueles que a lei, os usos, costumes e tradições profissionais lhe impõem.\n2. O advogado deve obstar a que os seus clientes exerçam quaisquer represálias contra o adversário e sejam menos corretos para com os advogados da parte contrária, magistrados, árbitros, funcionários, órgãos de polícia ou quaisquer outros intervenientes no processo da justiça.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 119, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(120, 120, 'Incompatibilidade', 'São incompatíveis com o exercício da advocacia as seguintes funções:\na) Membro do Governo;\nb) Magistrado Judicial ou do Ministério Público;\nc) Diretor de serviço e diretor Geral;\nd) Notário ou Conservador dos Registos e Funcionários ou agentes dos Serviços do notariado e registo;\ne) Polícia ou Guarda Fiscal;\nf) Funcionários, Agentes ou assessores em qualquer Tribunal, Polícia ou Organismo especializado de Fiscalização, Prevenção ou Segurança;\ng) Outras que por lei sejam declaradas como tal.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 120, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(121, 121, 'Impedimentos', '1. O advogado está impedido de exercer o patrocínio:\na) Nos processos em que cônjuge ou algum ascendente, descendente, irmão ou afim nos mesmos graus, for juiz ou magistrado do Ministério Público;\nb) Nos processos em que tenha intervindo como testemunhas, declarantes ou peritos;\nc) Nos processos em que tenha intervindo ou seus incidentes, processos conexos, em qualquer veste, nomeadamente representante ou auxiliar da parte contrária, tenha prestado parecer jurídico sobre a questão controvertida, Juiz assessor, agente do Ministério Público ou funcionário judicial, testemunha, declarante ou perito;\nd) A parte contrária noutra causa pendente seja patrono;\ne) A questão seja contra a entidade patronal a que se encontra vinculado por contrato de trabalho;\n2. Para além dos impedimentos referidos no número anterior, está igualmente impedido de exercer o patrocínio contra ou a favor do Estado:\na) Os Deputados da Assembleia Nacional Popular;\nb) Funcionários e agentes da administração pública; independentemente do título de ligação;\n3. Cessa imediatamente o impedimento, as razoes de impedimento previsto no número anterior logo que o impedido deixar de exercer as funções públicas, comunicando o facto por escrito a OAGB', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 121, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(122, 122, 'Segredo Profissional', '1. O advogado tem direito a proteção do segredo profissional contra o Estado, as entidades públicas e privadas.\n2. O advogado é obrigado ao segredo profissional no que respeite:\na) Os factos referentes a assuntos profissionais que lhe tenham sido revelados pelo cliente ou por sua ordem ou conhecidos no exercício da profissão;\nb) Os factos que, em virtude de cargo desempenhado na Ordem, qualquer colega obrigado quanto aos mesmos factos ao segredo profissional, lhe tenha comunicado;\nc) Os factos comunicados por coautor, corréu ou cointeressado do cliente ou pelo respetivo representante;\nd) Os factos de que a parte contrária do cliente ou respetivos representantes lhe tenham dado conhecimento durante negociações para acordo amigável e que sejam relativos à pendência.\n3. A obrigação do segredo profissional existe, quer o serviço solicitado ou cometido ao advogado envolva ou não representação judicial ou extrajudicial quer deva ou não ser remunerado, quer o advogado haja ou não chegado a aceitar e a desempenhar a representação ou serviços, o mesmo acontecendo para todos os advogados que, direta ou indiretamente, tenham qualquer intervenção no serviço.\n3. O segredo profissional abrange ainda os documentos ou outras coisas que se relacionem, direta ou indiretamente, com os factos sujeitos a sigilo.\n4. Cessa a obrigação de segredo profissional em tudo quanto seja absolutamente necessário para a defesa da dignidade, direitos e interesses legítimos do próprio advogado ou do cliente ou seus representantes, mediante prévia autorização do Presidente do tribunal de deontologia e disciplina com recurso nos termos gerais de direito,\n5. Não podem fazer prova em juízo as declarações feitas pelo advogado com violação de segredo profissional.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 122, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(123, 123, 'Imposição de Selos, Arrolamentos e Buscas em Escritórios de Advogados', '1. A imposição de selos, arrolamentos, buscas e diligências semelhantes no escritório de advogado ou em qualquer outro lugar onde faça arquivo só pode ser decretado e presidido pelo juiz competente.\n2. Com a necessária antecedência, o juiz deve convocar para assistir a diligência o advogado a ele sujeito, bem como o Presidente do Conselho deontológico podendo este delegar em outro advogado.\n3. Na falta de comparência do advogado, representante da Ordem ou havendo urgência incompatível com os trâmites previstos no número anterior, o juiz deve nomear qualquer advogado que possa comparecer imediatamente, de preferência de entre os que hajam feito parte dos órgãos da Ordem ou, quando não seja possível, o que for indicado pelo advogado a quem o escritório ou arquivo pertencer.\n4. Às diligências são admitidos também, quando se apresentem ou o juiz os convoque, os familiares ou empregados do advogado interessado.\n5. Até a comparência do advogado que represente a Ordem podem ser tomadas as providências indispensáveis para que se não inutilizem ou desencaminhem quaisquer papéis ou objecto,\n6. O auto de diligência faz expressa menção das pessoas presentes, bem como de quaisquer ocorrências que tenham lugar no seu decurso.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 123, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(124, 124, 'Apreensão de Documentos', '1. Não pode ser apreendida a correspondência que respeite ao exercício da profissão.\n2. A proibição estende-se às correspondências trocadas entre o advogado e aquele que lhe tenha cometido ou pretendido cometer mandato ou lhe haja solicitado parecer, embora ainda não dado ou é recusado.\n3. Compreende-se na correspondência as instruções e informações inscritas sobre o assunto da nomeação, mandato ou do parecer solicitado.\n4. Excetua-se o caso de a correspondência respeitar o facto criminoso relativamente ao qual o advogado seja arguido.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 124, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(125, 125, 'Reclamação', '1. No decurso das diligências previstas nos artigos anteriores, pode o advogado interessado ou, na sua falta, qualquer dos familiares ou empregado presente, bem como o representante da Ordem apresentar qualquer reclamação.\n2. Sendo a reclamação feita para preservar o segredo profissional, o juiz deve logo sobrestar na diligência relativamente aos documentos ou objetos que forem postos em causa fazendo-os acondicionar, sem os ler ou examinar, em volume selado no mesmo momento.\n3. As reclamações serão fundamentadas no prazo de cinco dias e entregues no Tribunal onde corre o processo devendo remetê-las em igual prazo, ao Presidente do Supremo Tribunal de Justiça com o seu parecer e, sendo caso disso, com o volume a que se refere o número anterior.\n4. O Presidente do Supremo Tribunal de Justiça pode, com reserva de segredo, proceder à descolagem do mesmo volume, devolvendo-o novamente selado com a sua decisão.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 125, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(126, 126, 'Direito de Comunicação com Suspeitos Presos', 'Os advogados têm direito, nos termos da lei, de comunicar, pessoal e reservadamente, com os seus patrocinados, mesmo que estes se achem presas ou detidos em estabelecimento civil ou militar.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 126, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(127, 127, 'Obrigação de Prestar Informações', '1. O Conselho deontológico pode solicitar dos advogados e advogados estagiários as informações que entenda necessárias para verificação de existência ou não de incompatibilidade.\n2. Não sendo tais informações prestadas no prazo de quinze dias, poderá o Conselho Jurisdicional requerer a suspensão da inscrição.\n3. A aplicação do disposto nos números anteriores não é prejudicada pela circunstância de o advogado ou advogado estagiário ter mudado o seu escritório desde que da mudança não tenha sido dado oportuno conhecimento.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 127, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(128, 128, 'Dever Geral de Urbanidade', 'No exercício da profissão deve o advogado proceder com urbanidade, nomeadamente, para com os outros advogados, magistrados, funcionários das secretarias, peritos, interpretes, testemunhas e outros intervenientes nos processos.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 128, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(129, 129, 'Informação, Exame de Processo e Pedido de Certidões', '1. No exercício da sua profissão, o advogado pode solicitar em qualquer tribunal ou repartição pública o exame de processos, livros ou documentos que não tenham carácter reservado ou secreto, bem como requerer verbalmente ou por escrito a passagem de certidões, sem necessidade de exibir procuração.\n2. Os advogados, quando no exercício da sua profissão, têm preferência para ser atendidos por quaisquer funcionários a quem devam dirigir-se e têm o direito de ingresso nas secretarias judiciais.', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 129, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(130, 130, 'Direito de Protesto', '1. No decorrer de audiência ou de qualquer outro acto ou diligência em que intervenha o advogado deve ser admitido a requerer oralmente ou por escrito, no momento que considerar oportuno, o que julgar conveniente ao dever do patrocínio.\n2. Quando, por qualquer razão, lhe não seja concedido a palavra ou o requerimento não for exarado em acta pode o advogado exercer o direito de protesto, indicando a matéria do requerimento e o objecto que tinha em vista.\n3. O protesto não pode deixar de constar da acta e é havido para todos os efeitos como arguição de nulidade, nos termos da lei', 'Exercício da Advocacia', NULL, NULL, NULL, NULL, NULL, NULL, 130, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(131, 131, 'Patrocínio Contra Advogados e Magistrados', 'O advogado, antes de promover quaisquer diligências judiciais contra outros advogados ou magistrados, comunicar-lhes-á por escrito a sua intenção, com as explicações que entenda necessárias, salvo tratando-se de diligência ou auto de natureza secreta ou urgente.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 131, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(132, 132, 'Discussão Pública de Questões Profissionais', '1. O advogado não deve discutir ou contribuir para a discussão em público ou nos meios de comunicação social, questões pendentes ou a instaurar perante os tribunais ou outros órgãos do Estado, salvo se o Conselho Jurisdicional concordar fundamentalmente com a necessidade de uma explicação pública e, nesse caso nos precisos termos autorizado pelo Conselho.\n2. O advogado não deve tentar influir de forma maliciosa ou censurável na resolução de pleitos judiciais ou outras questões pendentes em órgãos do Estado.\n\n\nTITULO VI\nDever de colaboração da Administração publica com os advogados', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 132, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(133, 133, 'Correspondências e requisições de documentos', '1. No exercício da sua atividade, as entidades públicas estão obrigadas a prestar a devida colaboração aos advogados, devendo, nos limites da lei, corresponder-se com estes, designadamente para fornecimento de cópias, certidões, informações e esclarecimentos, incluindo a consulta dos autos e confiança de processo, sempre que para tal, o interessado fundamentar o pedido.\n2. Havendo recusa de colaboração, poderá o advogado requerer a intervenção do Bastonário, através da carta dirigida a Ordem dos Advogados, instruído com a cópia do pedido de colaboração e documentos que achar conveniente.\n3. O pedido deve, se possível identificar detalhadamente o comportamento da entidade recusante.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 133, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(134, 134, 'Direito a informação e de acesso aos documentos', '1. Os advogados têm direito à informação indispensável para a boa administração da justiça, nomeadamente o direito de acesso a documentos administrativos de carácter não nominativo.\n2. O direito de acesso aos documentos administrativos compreende não só o direito de obter a sua reprodução, bem como o direito de ser informado sobre a sua existência e conteúdo.\n3. O depósito dos documentos administrativos em arquivos não prejudica o exercício, a todo o tempo, do direito de acesso aos referidos documentos.\n4. O direito de acesso aos documentos, abrange os documentos notariais e registrais, aos documentos de identificação civil e criminal, aos documentos referentes a dados pessoais com tratamento automatizado e aos documentos depositados em arquivos históricos, salvo disposição legal contrária', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 134, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(135, 135, 'Âmbito', '1. Os documentos a que se reporta o artigo anterior são os que têm origem ou são detidos por órgãos do Estado que exerçam funções administrativas, órgãos dos institutos públicos e das associações públicas, órgãos das autarquias locais, e outras entidades no exercício de poderes de autoridade, nos termos da lei.\n2. Os direitos abrangidos por este regime, incluem o direito de pedir esclarecimentos e explicações sobre a demora da administração, relativamente as situações administrativas cujos prazos de resposta está previsto na lei', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 135, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(136, 136, 'Documentos administrativos', '1. Para efeito do disposto nos presentes estatutos, são considerados documentos administrativos, os documentos detidos pela Administração Pública, nomeadamente, processos, relatórios, estudos, pareceres, atas, autos, circulares, ofícios-circulares, ordens de serviço, despachos normativos internos, instruções e orientações de interpretação legal ou de enquadramento da atividade ou outros elementos de informação;\n2. Não se consideram documentos administrativos, para efeitos do presente estatuto:\na) As notas pessoais, esboços, apontamentos e outros registos de natureza semelhante;\nb) Os documentos cuja elaboração não releve da atividade administrativa, designadamente referentes à reunião do Conselho de Ministros e /ou outras reuniões de natureza politica do Estado, bem como à sua preparação.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 136, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(137, 137, 'Forma do pedido', 'O acesso aos documentos deve ser solicitado por escrito através do requerimento, onde conste os elementos essenciais à sua identificação, bem como a identificação do escritório, do advogado, número de inscrição e assinatura.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 137, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(138, 138, 'Resposta da Administração', '1. A entidade a quem foi dirigido o requerimento de acesso a um documento deve, no prazo de 5 dias:\na) Comunicar a data, local e modo para se efetivar a consulta, efetuar a reprodução ou obter a certidão;\nb) Indicar, nos termos da presente lei, as razões da recusa, total ou parcial, do acesso ao documento pretendido;\nc) Informar que não possui o documento e, se for do seu conhecimento, qual a entidade que o detém ou remeter o requerimento a esta, comunicando o facto ao interessado;', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 138, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(139, 139, 'Formas e meios de acesso', '1. O acesso aos documentos exerce-se através de:\na) Consulta gratuita, efeituada nos serviços que os detêm;\nb)  Reprodução por fotocópia ou por qualquer meio técnico, designadamente visual ou sonora;\nc) Passagem de certidão pelos serviços da Administração.\n2. A reprodução nos termos da alínea b) do número anterior far-se-á num exemplar, sujeito a pagamento, pela pessoa que a solicitar, do encargo financeiro estritamente correspondente ao custo dos materiais usados e do serviço prestado, a fixar por despacho do Ministro.\n3. Os documentos informatizados são transmitidos em forma inteligível para qualquer pessoa e em termos rigorosamente correspondentes ao do conteúdo do registo, sem prejuízo da opção prevista na alínea b) do n.º 1.\n4. Quando a reprodução prevista no n.º 1 puder causar dano ao documento visado, o interessado, a expensas suas e sob a direção do serviço detentor, pode promover a cópia manual ou a reprodução por qualquer outro meio que não prejudique a sua conservação.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 139, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(140, 140, 'Recusa legítima de fornecer documentos e informações', '1. A Administração pode recusar o acesso a documentos cuja comunicação ponha em causa segredos comerciais, industriais ou sobre a vida interna das empresas.\n2. É vedada a utilização de informações com desrespeito dos direitos de autor e dos direitos de propriedade industrial, assim como a reprodução, difusão e utilização destes documentos e respetivas informações que possam configurar práticas de concorrência desleal.\n3. Os dados pessoais comunicados a terceiros não podem ser utilizados para fins diversos dos que determinaram o acesso, sob pena de responsabilidade por perdas e danos, nos termos legais.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 140, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(141, 141, 'Direito de queixa', '1. O advogado denegado o acesso à informação ou documento administrativo pode dirigir uma queixa ao presidente do Supremo Tribunal de Justiça, no prazo de 20 dias, contra o indeferimento expresso, a falta de decisão ou decisão limitadora do direito de acesso.\n2. O presidente do Supremo Tribunal de Justiça tem o prazo de 10 dias para se pronunciar, mediante relatório de apreciação da situação, enviando-o, com as devidas conclusões, ao interessado e ao departamento administrativo visado.\n3. Recebido o relatório referido no número anterior, a Administração deve comunicar ao interessado a sua decisão final, fundamentada, no prazo de 5 dias.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 141, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(142, 142, 'Recurso', 'A decisão ou falta de decisão podem ser impugnadas pelo interessado junto dos tribunais administrativos, aplicando-se, com as devidas adaptações, as regras do processo administrativo urgentes.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 142, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(143, 143, 'Direito a informação e de acesso aos documentos', '5. Os advogados têm direito à informação indispensável para a boa administração da justiça, nomeadamente o direito de acesso a documentos administrativos de carácter não nominativo.\n6. O direito de acesso aos documentos administrativos compreende não só o direito de obter a sua reprodução, bem como o direito de ser informado sobre a sua existência e conteúdo.\n7. O depósito dos documentos administrativos em arquivos não prejudica o exercício, a todo o tempo, do direito de acesso aos referidos documentos.\n8. O direito de acesso aos documentos, abrange os documentos notariais e registrais, aos documentos de identificação civil e criminal, aos documentos referentes a dados pessoais com tratamento automatizado e aos documentos depositados em arquivos históricos, salvo disposição legal contraria', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 143, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(144, 144, 'Âmbito', '3. Os documentos a que se reporta o artigo anterior são os que têm origem ou são detidos por órgãos do Estado que exerçam funções administrativas, órgãos dos institutos públicos e das associações públicas, órgãos das autarquias locais, e outras entidades no exercício de poderes de autoridade, nos termos da lei.\n4. Os direitos abrangidos por este regime, incluem o direito de pedir esclarecimentos e explicações sobre a demora da administração, relativamente as situações administrativas cujos prazos de resposta está previsto na lei', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 144, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(145, 145, 'Documentos administrativos', '3. Para efeito do disposto nos presentes estatutos, são considerados documentos administrativos, os documentos detidos pela Administração Pública, nomeadamente, processos, relatórios, estudos, pareceres, atas, autos, circulares, ofícios-circulares, ordens de serviço, despachos normativos internos, instruções e orientações de interpretação legal ou de enquadramento da atividade ou outros elementos de informação;\n4. Não se consideram documentos administrativos, para efeitos do presente estatuto:\nc) As notas pessoais, esboços, apontamentos e outros registos de natureza semelhante;\nd) Os documentos cuja elaboração não releve da atividade administrativa, designadamente referentes à reunião do Conselho de Ministros e /ou outras reuniões de natureza política do Estado, bem como à sua preparação.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 145, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(146, 146, 'Forma do pedido', 'O acesso aos documentos deve ser solicitado por escrito através do requerimento, onde conste os elementos essenciais à sua identificação, bem como a identificação do escritório, do advogado, número de inscrição e assinatura.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 146, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(147, 147, 'Resposta da Administração', '2. A entidade a quem foi dirigido o requerimento de acesso a um documento deve, no prazo de 5 dias:\nd) Comunicar a data, local e modo para se efetivar a consulta, efetuar a reprodução ou obter a certidão;\ne) Indicar, nos termos da presente lei, as razões da recusa, total ou parcial, do acesso ao documento pretendido;\nf) Informar que não possui o documento e, se for do seu conhecimento, qual a entidade que o detém ou remeter o requerimento a esta, comunicando o facto ao interessado;', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 147, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(148, 148, 'Formas e meios de acesso', '5. O acesso aos documentos exerce-se através de:\nd) Consulta gratuita, efeituada nos serviços que os detêm;\ne)  Reprodução por fotocópia ou por qualquer meio técnico, designadamente visual ou sonora;\nf) Passagem de certidão pelos serviços da Administração.\n6. A reprodução nos termos da alínea b) do número anterior far-se-á num exemplar, sujeito a pagamento, pela pessoa que a solicitar, do encargo financeiro estritamente correspondente ao custo dos materiais usados e do serviço prestado, a fixar por despacho do Ministro.\n7. Os documentos informatizados são transmitidos em forma inteligível para qualquer pessoa e em termos rigorosamente correspondentes ao do conteúdo do registo, sem prejuízo da opção prevista na alínea b) do n.º 1.\n8. Quando a reprodução prevista no n.º 1 puder causar dano ao documento visado, o interessado, a expensas suas e sob a direção do serviço detentor, pode promover a cópia manual ou a reprodução por qualquer outro meio que não prejudique a sua conservação.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 148, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(149, 149, 'Recusa legítima de fornecer documentos e informações', '4. A Administração pode recusar o acesso a documentos cuja comunicação ponha em causa segredos comerciais, industriais ou sobre a vida interna das empresas.\n5. É vedada a utilização de informações com desrespeito dos direitos de autor e dos direitos de propriedade industrial, assim como a reprodução, difusão e utilização destes documentos e respetivas informações que possam configurar práticas de concorrência desleal.\n6. Os dados pessoais comunicados a terceiros não podem ser utilizados para fins diversos dos que determinaram o acesso, sob pena de responsabilidade por perdas e danos, nos termos legais.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 149, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(150, 150, 'Direito de queixa', '4. O advogado denegado o acesso à informação ou documento administrativo pode dirigir uma queixa ao presidente do Supremo Tribunal de Justiça, no prazo de 20 dias, contra o indeferimento expresso, a falta de decisão ou decisão limitadora do direito de acesso.\n5. O presidente do Supremo Tribunal de Justiça tem o prazo de 10 dias para se pronunciar, mediante relatório de apreciação da situação, enviando-o, com as devidas conclusões, ao interessado e ao departamento administrativo visado.\n6. Recebido o relatório referido no número anterior, a Administração deve comunicar ao interessado a sua decisão final, fundamentada, no prazo de 5 dias.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 150, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(151, 151, 'Recurso', 'A decisão ou falta de decisão podem ser impugnadas pelo interessado junto dos tribunais administrativos, aplicando-se, com as devidas adaptações, as regras do processo administrativo urgentes.\n\nSECÇÃO II\nDireitos e deveres dos advogados e solicitadores', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 151, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(152, 152, 'Direitos deveres dos advogados', 'Os direitos e deveres dos advogados, tanto os de caracter geral como em relação a OAGB, ou entre advogados, ou na relação com magistrados e tribunais, na relação com os clientes, bem como em matéria dos honorários profissionais, assistência jurídica gratuita e assistência aos detidos são os estabelecidos nos presentes estatutos e demais deposições legais aplicáveis.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 152, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(153, 153, 'Direitos dos advogados', '1. São direitos profissionais dos advogados, os seguintes:\na) Intervir livremente nos tribunais, administração pública em geral para a defesa dos direitos, interesse, liberdades e garantias individuais dos cidadãos;\nb) Gestão racional do seu tempo, não lhe sendo exigido a permanência em qualquer serviço judicial, policial ou da administração pública em geral, em que pratica um ato ou participa numa diligencia, depois de meia hora sobre a hora marcada para o início do acto ou diligência, querendo, findo do qual, depositar a participação registada no tribunal, dando conhecimento do facto a direção nacional da Ordem para tomar as iniciativas que julgar pertinente ao caso.\nc) Não ser restringido na sua liberdade ou independência por qualquer forma no exercício da sua função;\nd) Protestar os obstáculos ou restrições que impedem a boa administração da justiça ou correta aplicação da lei;\ne) A Colaboração necessária das autoridades judiciais e dos órgãos policiais\nf) Exercer com liberdade, a profissão em todo o território nacional;\ng) A inviolabilidade do escritório ou local de trabalho, bem como dos instrumentos de trabalho, correspondência escrita, eletrónica e telefónica desde que relativa ao exercício da advocacia;\nh) Comunicar-se com o seu cliente, pessoal e reservadamente, mesmo sem procuração, quando estes se encontram presos, detidos ou em lugar reservado, ainda que inibidos o direito de incomunicação;\ni) Ser assistido por um representante da OAGB, quando detido em flagrante delito, por motivo ligado ao exercício da advocacia, para a audição, sob pena de nulidade e, nos demais casos, a comunicação expressa à direção nacional da OAGB.\nj) Não ser preso, antes da sentença transitada em julgado, passiveis de caução;\nk) Não ser preso no estabelecimento prisional comum ou cela, com os reclusos de delitos comum;\nl) Apresentar alegações sentado ou em pé e abandonar qualquer momento a sala ou serviço mediante mero conhecimento do órgão que dirige o acto;\nm) Usar da palavra, pela ordem, em qualquer juízo ou tribunal, mediante intervenção sumaria, para esclarecer equívoco ou duvida surgida em relação a factos, documentos ou afirmações que influam no julgamento, bem como para replicar acusação ou censura que lhe forem feitas;\nn) Ter acesso em qualquer órgão dos poderes judicial e legislativo, ou da administração pública em geral, dos outos de processos findos ou em andamento, mesmo sem procuração, quando não estejam sujeitos a sigilo, assegurada a obtenção de copiais, podendo tomar notas\no) Acesso aos serviços dos órgãos policiais, mesmo sem procuração, outos de flagrante delito, e de inquérito findo ou em andamento, ainda que conclusos para autoridade judicial, podendo copiar peças e tomar notas;\np) Acesso aos processos judiciais ou administrativos de qualquer natureza, em cartório ou repartição competente, ou devolvê-los dentro dos prazos previstos na lei;\nq) Não depor como testemunha em processo no qual é advogado ou deva patrocinar, ou sobre factos relacionado com a pessoa de quem foi advogado ou testemunha no processo em que foi advogado, mesmo quando autorizado ou solicitado pelo constituinte, bem como sobre facto que constitua sigilo profissional;\nr) Usar de palavra com liberdade e sob a imunidade profissional, não constituindo injúria penal, qualquer intervenção oral da sua parte, no exercício da sua atividade, em juízo ou fara dele, sem prejuízo das sanções disciplinares perante a Ordem, pelos excessos que cometer;\ns) Entrar livremente e sem oposição nos serviços dos tribunais, incluindo nas zonas reservados para os magistrados; nos serviços e dependências das secretarias, cartórios, serviços notariais, de registo e nas prisões sem dependência do horário de expediente;\na) Em qualquer edifício ou espeço em que funciona serviço judicial ou outro serviço publico onde o advogado deve ter acesso por razoes profissionais\nb) Em qualquer assembleia ou reunião de que participa ou participar o seu cliente ou perante a qual este deve comparecer, desde que munido de poderes especiais;\n2. Para o efeito do disposto nas alíneas, c), d), e e), participando o facto o interessado à ordem, poderá adotar as medidas que achar convenientes e oportunas tendo em vista assegurar a independência e prestígio profissional', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 153, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(154, 154, 'Dever do Advogado para com o Cliente', '1. Nas relações com o cliente os advogados têm os seguintes deveres:\na) Recusar mandato, nomeação oficiosa ou prestação de serviços em questão em que já tenha intervindo em qualquer outra qualidade ou seja conexa com outra em que é representante ou tenha representado a parte contrária;\nb) Recusar mandato contra quem noutra causa seja seu mandante;\nc) Dar ao cliente a sua opinião conscienciosa sobre o merecimento do direito ou pretensão que este invoca, assim como prestar, sempre que lhe for pedido, informação sobre o andamento das questões que lhe forem confiadas;\nd) Estudar com cuidado e tratar com zelo a questão de que seja incumbido, utilizando, para o efeito, todos os recursos da sua experiência, saber e atividade;\ne)  Guardar segredo profissional;\nf) Aconselhar toda a composição do litígio que ache justa e equitativa;\ng) Dar conta ao cliente de todos os dinheiros deste que tenha recebido, qualquer que seja sua proveniência e apresentar nota de honorários e despesas, quando solicitada;\nh) Dar aplicação devida a valores, documentos ou objetos que lhe tenham sido confiados;\ni) Não celebrar, em proveito próprio, Contratos sobre o objeto das questões confiadas ou, por qualquer forma, solicitar ou aceitar participação nos resultados das causas;\nj) Não abandonar o patrocínio do constituinte ou o acompanhamento das questões que lhe estão cometidas sem motivo justificado.\n2. O advogado deve empregar os esforços a fim de evitar que seu cliente exerça quaisquer represálias contra o adversário e seja menos correto para com os advogados da parte contrária, juízes ou quaisquer outros intervenientes no processo.', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 154, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(155, 155, 'Efeitos da cessação do contrato de mandato forense', '1. Cessando o contrato de mandato forense por qualquer forma, o cliente tem direito a restituição de documentos, objetos ou valores que haja sido confiado ao advogado, que sejam necessários para prova do direito do cliente ou cuja retenção possa trazer a este prejuízo graves.\n2. Com relação aos demais valores e objetos em seu poder, goza o advogado do direito de retenção para garantia do pagamento dos seus honorários e reembolso de despesas.\nSecção III\nDas infrações disciplinares', 'Estágio e Formação', NULL, NULL, NULL, NULL, NULL, NULL, 155, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53');
INSERT INTO `estatutos_artigos` (`id`, `numero_artigo`, `titulo_artigo`, `conteudo`, `tema`, `capitulo`, `titulo_capitulo`, `seccao`, `titulo_seccao`, `titulo_doc`, `titulo_doc_nome`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(156, 156, 'Infração Disciplinar', '1. Comete infração disciplinar o advogado que, por ação ou omissão, violar dolosa ou culposamente algum dos deveres decorrentes deste Estatuto, dos regulamentos internos ou demais disposições aplicáveis.\n2. Nenhum advogado poderá ser sancionado por ação ou omissões que não estejam tipificados como infração nos estatutos da OAGB ou nas normas deontológicas aprovadas pelo Conselho nacional.\n3. Constitui infração disciplinar, nomeadamente a violação culposa dos alguns dos deveres seguintes:\na) Exercício da profissão, quando impedido de o fazer, ou facilitar, por qualquer meio, o seu exercício aos não inscritos na OAGB, proibidos ou impedidos.\nb) Manter a sociedade profissional fora das normas e preceitos estabelecidos nos estatutos ou na lei;\nc) Atuar como agenciador de causas, mediante participação nos honorários a receber;\nd) Angariar ou captar causas, com intervenção de terceiros;\ne) Assinar qualquer escrito destinado a processo judicial ou para fim extrajudicial sem mandato, ou em que não tenha intervenções permitidas por lei por qualquer forma ou colaboração;\nf) Advogar contra disposição legal expressa, presumindo-se de boa-fé quando fundamentado na inconstitucionalidade, na injustiça da lei ou em decisão judicial anterior;\ng) Querar, sem justa causa, sigilo profissional;\nh) Estabelecer contactos com a parte adversa sem autorização do cliente e conhecimento do advogado contrário;\ni)  Prejudicar, culposamente e de forma grave, interesses confiados ao seu patrocínio;\nj) Abandonar o patrocínio da causa sem justa causa ou antes de decorrido o prazo para comunicação da renúncia com eficácia dentro dos autos;\nk) Recursar-se a prestar, sem justo motivo, assistência jurídica, quando nomeado regularmente;\nl) Fazer juízo público na imprensa, conteúdo dos actos processuais relativo aos processos pendentes;\nm) Citar publicamente o nome do magistrado na imprensa ou por outro meio idóneo por razoes relacionadas com o exercício das suas funções, bem como depoimentos, documentos e alegações de parte contrária;\nn) Fazer em nome do constituinte sem autorização escrita deste, declarações públicas sobre um facto do seu interesse;\no) Não cumprir, no prazo estabelecido, notificações, citações ou outras ordens judiciais ou judiciaria emitidas dentro do âmbito da competência própria ou delegada do órgão ou autoridade, depois de regularmente notificado ou citado;\np) Facilitar, consentir ou auxiliar os clientes ou a terceiros para a realização do acto contrário a lei ou destinado a fraudá-la;\nq) Solicitar ou receber de constituinte qualquer importância para aplicação ilícita ou desonesta no processo ou entregar adversário;\nr) Receber valores, de parte contraria ou de terceiros, relacionados com o objecto do mandato, sem expressa vontade do cliente;\ns) Locupletar-se, por qualquer forma, à custa do cliente ou da parte adversaria, por si ou interposta pessoa;\nt) Recusar-se, injustificadamente, a prestar contas ao cliente de quantias recebidas dele ou de terceiros por conta dele,\nu) Reter, abusivamente, ou extraviar autos recebidos com vista ou em confiança;\nv) Deixar de pagar as contribuições, multas e preços de serviços devidos à OAGB, depois de regularmente notificado a fazê-lo;\nw) Reincidência reiterada em erros reiterados que evidenciem inépcia profissional;\nx) Fazer falsa prova de qualquer dos requisitos para a inscrição na OAGB;\ny) Utilizar documentos falsos, relações especiais, expedientes tipificados como crime para o exercício do mandato ou advocacia;\nz) Praticar, o estagiário, acto não abrangido na sua competência profissional;\nTITULO III\nDO PROCESSO NA OAGB\nCAPITULO I\nDisposições Gerais', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 156, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(157, 157, 'Processo', '1. O processo é o meio obrigatório para a tomada de decisão na OAGB.\n2. Salvo disposição em contrário, aplicam-se subsidiariamente ao processo na Ordem, incluindo ao processo disciplinar, as regras da legislação processual penal comum, as regras gerais do procedimento administrativo, e as regras da legislação processual civil, na ordem aqui estabelecida\n3. O prazo para reagir, impugnar, requerer diligencia, arguir nulidade ou exercer qualquer outro poder contra os actos praticados pelos órgãos sociais ou agentes da OAGB é de 15 dias.\n4. No caso do recurso contra as decisões do tribunal de ética e disciplina para os tribunais administrativos o prazo é de trinta dias\n5. Excetuam-se do disposto nos números anteriores os prazos especiais previstos neste estatuto\n6. Salvo disposição expressa, os atos praticados pelos Órgãos da Ordem, admitem recurso\nCAPITULO I\nDa Responsabilidade disciplinar dos advogados\nSEC9AO I\nDisposições Gerais', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 157, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(158, 158, 'Da responsabilidade Disciplinar', '1. Os advogados estão sujeitos a responsabilidade disciplinar em caso de violação dos seus deveres profissionais.\n2. A jurisdição disciplinar é exclusiva dos órgãos da Ordem, nos termos regulado neste Estatuto.\n3. As sanções disciplinares impostas aos advogados devem ser registadas no cadastro disciplinar dos membros da OAGB e disponível para consulta profissional, não podendo ser utilizado para fins diversos.\n4. Durante o tempo de cumprimento da sanção disciplinar de suspensão, o advogado continua sujeito à jurisdição disciplinar da Ordem.\n5. O pedido de cancelamento ou suspensão da inscrição não faz cessar a responsabilidade disciplinar por infrações anteriormente praticadas.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 158, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(159, 159, 'Dever de colaboração pelos Tribunais e outras Entidades', '1. Os tribunais e todas as autoridades públicas devem dar conhecimento à Ordem da prática por advogados de actos suscetíveis de constituir infrações disciplinares.\n2. O Ministério Público, os órgãos de Policia e as demais entidades públicas devem remeter à Ordem certidão das participações apresentadas contra advogados.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 159, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(160, 160, 'Responsabilidade Disciplinar, Civil e Criminal', '1. A responsabilidade disciplinar é independente da responsabilidade criminal ou civil.\n2. Pode, porém, ser ordenada a suspensão de processo disciplinar até decisão final noutros processos.\n3. Sempre que, em processo contra advogado, seja designado dia para julgamento, o Tribunal deve ordenar a remessa à Ordem de cópias do processo, bem como quaisquer outros elementos solicitados.\nSecção II\nPenas, sua Medida, Graduação e Execução', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 160, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(161, 161, 'Penas Disciplinar e sua graduação', '1. As penas disciplinares são as seguintes:\na) Advertência;\nb) Repreensão escrita;\nc) Pena de formação obrigatória;\nd) Multa,\ne) Pena de suspensão de exercício;\nf) Pena de proibição definitiva do exercício de profissão;\ng) Pena de restituição;\n2. As penas aplicadas devem constar no processo individual do sancionado, após trânsito em julgado da decisão, sem publicidade e censura, salvo nos casos previstos nestes estatutos.\n3. Nenhum advogado poderá ser sancionado por ações ou omissões praticadas no exercício da profissão, sem devida e fundada justificação da pena aplicada.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 161, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(162, 162, 'Aplicação das penas', 'A pena de advertência é aplicável a infrações leves que não devem passar sem reparo.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 162, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(163, 163, 'Pena de formação obrigatória', 'A pena de formação obrigatória é aplicável a infrações que revelem a inaptidão profissional, em termos de exigências técnicas da profissão.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 163, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(164, 164, 'Pena de multa', 'A pena de multa é aplicável a caso de negligência ou desinteresse pelo cumprimento dos deveres da profissão.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 164, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(165, 165, 'Pena de suspensão de exercício', 'A pena de suspensão de exercício é aplicável aos casos de desinteresse grave pelo cumprimento das regras do processo com danos irreparáveis para o cliente, falta de honestidade ou conduta imoral ou desonrosa, suscetível de abalar a confiança da sociedade nos advogados.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 165, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(166, 166, 'Pena de proibição definitiva do exercício de profissão', 'A pena de proibição definitiva do exercício de profissão é aplicável aos casos de infrações praticados em flagrante e grave abuso de profissão, com manifesta e grave violação dos deveres a ela inerentes, que impliquem a quebra de prestígio exigível ao advogado para que possa manter-se no seio dos seus colegas.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 166, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(167, 167, 'Critérios de Graduação da Pena', '1. Na aplicação das penas deve atender-se aos antecedentes profissionais e disciplinares do suspeito, ao grau de culpabilidade, às consequências da infração e às demais circunstâncias agravantes ou atenuantes.\n2.  Constituem, entre outras, circunstâncias agravantes:\na) A verificação de dolo;\nb) A premeditação;\nc) O conluio;\nd) A reincidência;\ne) A acumulação de infrações;\nf) A prática de infração disciplinar durante o cumprimento de pena disciplinar ou de suspensão de respetiva execução;\ng) A produção de prejuízo de valor igual ou superior a alçada do Tribunal de Primeira Instância.\n3. Considera-se reincidente o advogado que cometa uma infração disciplinar que deva ser punida com pena igual ou superior à de multa, antes de decorridos o prazo de dois anos sobre o termo do cumprimento de pena efetiva de igual ou superior gravidade que lhe tenha sido aplicada pela prática da infração anterior.\n4. Constituem, entre outras, circunstâncias atenuantes:\na) O exercício efetivo da advocacia por um período superior a 3 anos, sem qualquer sanção disciplinar;\nb) A confissão;\nc) A colaboração do suspeito para a descoberta da verdade;\nd) A reparação espontânea, pelo suspeito, dos danos causados pela sua conduta.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 167, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(168, 168, 'Graduação das penas de multa e suspensão', '1. Na aplicação das penas de multa e de suspensão, observar-se- a o seguinte:\n2. Multa: conforme a gravidade da infração, de Xof. 300.000,00 (trezentos mil francos da comunidade financeira africana) até Xof. 3.000.000,00 (três milhões de francos da comunidade financeira africana);\n3. Suspensão: de um mês até três anos;', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 168, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(169, 169, 'Restituição de Quantias e Documentos e Perda de Honorários', '1. Cumulativamente ou não com qualquer das penas pode ser imposta a de restituição de quantias, documentos ou objetos e, conjunta ou separadamente, a perda dos honorários.\n2. Independentemente da decisão final do processo, pode ser imposta a restituição de quantias, documentos ou objetos que hajam sido confiados ao advogado.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 169, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(170, 170, 'Aplicação da Pena de Suspensão Superior a um Ano ou proibição definitivo do exercício da profissão', '1. As penas previstas de duração superior a um ano e proibição do exercício definitivo da proibição só podem ser aplicadas por infração disciplinar que afete gravemente a dignidade e o prestígio profissional, mediante decisão que obtenha dois terços dos votos dos membros do Conselho deontológico e do tribunal de disciplina e ética.\n2. Ao Bastonário é remetido o processo em seis dias para exame, participando na votação do acórdão.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 170, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(171, 171, 'Publicidade das Penas', '1. As penas de suspensão e de expulsão têm sempre publicidade.\n2. A publicidade das penas é feita por meio de edital, com referência aos preceitos infringidos, afixado nas instalações da sede e publicado num dos jornais mais lidos do país e, no caso de suspensão ou expulsão, comunicada a todos os tribunais, conservatórias e notários.\nSecção II\nDos Órgãos Disciplinares da OAGB', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 171, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(172, 172, 'Do exercício da competência disciplinar', '1. O exercício da competência disciplinar é constituído por duas fases destintas com intervenção de dois órgãos igualmente distintos: a conciliatória dirigida pelo conselho de deontologia e ética e a contenciosa dirigida pelo tribunal de ética e disciplina.\n2. Os titulares dos órgãos disciplinares da Ordem são independentes no exercício da sua competência jurisdicional.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 172, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(173, 173, 'Instauração de Processo Disciplinar', '1. O procedimento disciplinar é instaurado mediante deliberação do Conselho de deontologia e ética com base em participação dirigida ao Bastonário por qualquer pessoa, devidamente identificada, que tenha conhecimento de factos suscetíveis de integrar a infração disciplinar.\n2. Por despacho meramente administrativo, o Bastonário dispõe de dois dias uteis para remeter a participação ao Conselho de deontologia e ética, que poderá indeferir liminarmente, ou após diligências preliminares e por deliberação fundamentada, a participação, quando a julga manifestamente inviável, podendo o interessado interpor o recurso nos termos do presente Estatuto para o tribunal da deontologia e disciplina.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 173, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(174, 174, 'Processos contra Titulares de Cargos da Ordem', 'Têm caracter urgente, com prioridade sobre quaisquer outros, os processos disciplinares em que sejam arguidos os titulares de algum dos órgãos da Ordem em exercício de funções.\nSECÇÃO II\nDistribuição', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 174, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(175, 175, 'Processo', '1. Instaurado o processo disciplinar, é efetuada a distribuição.\n2. Procede-se à nova distribuição no impedimento permanente do relator ou nos seus impedimentos temporários, sempre que as circunstâncias o justifiquem.\n3. Procede-se ainda à nova distribuição sempre que o infrator opõe suspeição contra o relator ou escusa do relator.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 175, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(176, 176, 'Natureza Secreta do Processo', '1. O processo é de natureza secreta até ao despacho de acusação.\n2. O relator pode, contudo, autorizar a consulta do processo pelo interessado ou pelo arguido quando não haja inconveniente para a instrução.\n3. O relator pode ainda, no interesse da instrução, dar a conhecer ao interessado ou ao arguido cópia de peças do processo, a fim de os mesmos sobre elas se pronunciar.\n4. Mediante requerimento em que se indique o fim a que se destina, pode ser autorizada passagem de certidão em qualquer fase do processo, mesmo depois de findo, para defesa de interesses legítimos dos requerentes podendo condicionar a sua utilização sob a pena de o arguido incorrer no crime de desobediência.\n5. O arguido e o interessado, quando advogados, que não respeitem a natureza secreta do processo incorrem em responsabilidade disciplinar.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 176, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(177, 177, 'Legitimidade Procedimental', 'As pessoas com interesse direto relativamente aos factos participados podem intervir no processo, requerendo e alegando o que tiverem por conveniente.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 177, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(178, 178, 'Prescrição', '1. O procedimento disciplinar prescreve no prazo de três anos.\n2. As infrações disciplinares que constituam simultaneamente ilícito penal prescrevem no mesmo prazo que o procedimento criminal, quando este for superior.\n3. A prescrição é de conhecimento oficioso, podendo o advogado suspeito, no entanto, requerer a continuação do processo.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 178, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(179, 179, 'Desistência', 'A desistência pelo interessado, pode ser requerida em qualquer fase do processo e extingue a responsabilidade disciplinar, salvo se a falta imputada afetar a dignidade do advogado visado ou o prestígio da Ordem ou da profissão.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 179, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(180, 180, 'Apensação do Processo', 'Estando pendentes vários processos disciplinares contra o mesmo suspeito, são todos apensados ao antigo e proferida uma só decisão, exceto se da apensação resultar manifesto inconveniente.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 180, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(181, 181, 'Instrução', '1. A instrução do processo disciplinar deve ultimar-se no prazo de 30 dias e aplica-se com as necessárias adaptações ao disposto no código do processo penal.\n2. Salvo por razoes de complexidade da participação, poderá ser solicitado ao tribunal a prorrogação do prazo previsto no número um para mais 20 dias improrrogáveis, com indicação dos motivos.\n3. Sem prejuízo de excepçoes previstas no presente estatuto, o tribunal de deontologia e disciplina compete apenas conhecer e julgar os processos disciplinares instruídos pelo conselho de deontologia e ética, nos termos dos presentes estatutos.\n4. Compete ao relator assegurar o regular andamento da instrução do processo e manter a disciplina nos respetivos autos.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 181, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(182, 182, 'Meios de Prova', '1. Na instrução do processo são admissíveis todos os meios de prova em direito permitido.\n2. O relator deve notificar sempre o advogado do suspeito, querendo, responder sobre a matéria da participação.\n3. O interessado e o suspeito podem requerer ao relator as diligências de prova que considerem necessárias ao apuramento da verdade.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 182, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(183, 183, 'Termo da Instrução', '1. Finda a instrução, o relator profere despacho de acusação no prazo de 15 dias.\n2. Se não se apurar suficientemente factos constitutivos da infração, não sendo proferido, por isso, o despacho de acusação, o relator apresenta o parecer na primeira sessão do Conselho, a fim de ser deliberado o arquivamento do processo, que este fique a aguardar a produção de melhor prova ou determinado que o mesmo prossiga com a realização de diligências complementares ou com o despacho de acusação, podendo ser designado novo relator de entre os membros do Conselho que tenham votado a continuação do processo.\nSECÇÃO II\nAcusação e Defesa', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 183, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(184, 184, 'Despacho de Acusação', '1. O despacho de acusação deve especificar a identificação do arguido, articulando discriminadamente os factos constitutivos da infração disciplinar; as circunstâncias em que os mesmos foram praticados, as circunstâncias agravantes ou atenuantes, indicando as normas estatutárias infringidas ou os preceitos no caso aplicáveis e o prazo para o exercício do direito à defesa.\n2. É simultaneamente ordenada a junção aos autos do extrato do registo disciplinar do suspeito.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 184, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(185, 185, 'Suspensão Preventiva', '1. Após o despacho de acusação pode ser requerida suspensão preventiva do suspeito perante o Tribunal de deontologia e disciplina, nos termos seguintes:\na) Existência de fortes possibilidades da prática de novas e graves infrações disciplinares ou tentativa de perturbar o andamento da instrução do processo;\nb) Se o suspeito tiver sido pronunciado criminalmente por crime cometido no exercício da profissão ou por crime a que corresponda pena maior.\nc) Se houver fortes indícios de que a continuação na efetividade da atividade da advocacia seja prejudicial ao prestígio e a dignidade da Ordem ou da função do Advogado na sociedade.\n2. O requerimento da suspensão preventiva não pode exceder dois meses, no caso das a) e b) e seis meses no caso da al. c) e deve ser deliberada por dois terços dos membros do Conselho.\n3. O Bastonário pode, mediante proposta igualmente aprovada por dois terços dos membros do Conselho, prorrogar por mais dois meses a suspensão.\n4. A suspensão preventiva é sempre descontada nas penas de suspensão.\n5. Os processos disciplinares com suspeito suspenso preventivamente preferem para o seu julgamento a todos os demais.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 185, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(186, 186, 'Notificação da Acusação', '1. O suspeito é notificado da acusação pessoalmente ou remetida para o seu domicílio profissional ou para a sua residência, consoante a sua inscrição esteja ou não em vigor, com entrega da respetiva cópia.\n2. Se o arguido se tiver ausentado do País e for desconhecida a sua residência, é notificado por edital, com o resumo da acusação, a afixar nas instalações da sede, na porta do seu domicílio profissional ou da última residência.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 186, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(187, 187, 'Exercício do Direito de Defesa', '3. O prazo para a defesa é de vinte dias, a contar da notificação da acusação.\n4. Se o arguido for notificado no estrangeiro ou por edital, o prazo para a defesa não pode ser inferior a trinta dias nem superior a sessenta dias.\n5. O relator pode ainda, em caso de justo impedimento, admitir a defesa apresentada extemporaneamente.\n6. O suspeito pode nomear em sua defesa um representante especialmente mandatado para esse efeito.\n7. No caso de o suspeito não poder exercer esse direito o relator nomeia um curador, preferindo para o cargo a pessoa a quem competiria à tutela no caso de interdição.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 187, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(188, 188, 'Apresentação da Defesa', '1. A defesa expõe clara e concisamente os factos e as razões que a fundamenta.\n2. Com a defesa deve o arguido apresentar o rol de testemunhas, juntar documentos e requerer quaisquer diligências, que julgar pertinente, podendo ser recusados, quando manifestamente impertinentes ou desnecessárias para o apuramento dos factos.\n3. O suspeito deve indicar os factos sobre os quais incidirá a prova ou quando convidado a fazê-lo, sob pena de indeferimento por falta de indicação.\n4. Não podem ser indicados mais de 3 testemunhas por cada facto e o seu total não pode exceder, sem prejuízo do disposto no artigo seguinte, o número de 25.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 188, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(189, 189, 'Realização de novas Diligências', '1. O relator pode ordenar a realização de novas diligências que considere necessárias para o apuramento da verdade.\n2. O disposto no número anterior, é feita mediante despacho favorável do requerimento dirigido ao tribunal, e o prazo não deve ultrapassar sessenta dias, podendo ser elevado por mais 20 dias, ocorrendo motivo justificado, nomeadamente em razão da excecional complexidade do processo.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 189, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(190, 190, 'Exame do Processo na Secretaria', '1. Durante o prazo para apresentação da defesa, o processo pode ser consultado na secretaria ou confiado arguido ou o advogado constituído para exame no seu domicílio ou escritório.\n2. Seguidamente, no prazo máximo de dez dias, o processo é entregue ao tribunal para julgamento.\nSECÇÃO III\nJulgamento', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 190, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(191, 191, 'Prazo para Julgamento', '1. Os processos devem ser apresentados a julgamento no prazo de 30 dias a contar da data de distribuição.\n2. Não sendo cumprido o prazo mencionado no número anterior, sem justificação, o processo é redistribuído a outro relator, devendo o facto ser obrigatoriamente comunicado ao Conselho Nacional para efeitos de promoção da acçao disciplinar contra o inadimplente.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 191, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(192, 192, 'Nulidades insupríveis', '1. Constituição nulidade insuprível a falta de audiência do arguido com possibilidade de defesa e a omissão de diligências essenciais para a descoberta da verdade que ainda possam ultimamente realizar-se.\n2. As restantes nulidades e irregularidades consideram-se sanadas se não forem arguidas na defesa ou, a ocorrerem posteriormente, no prazo de cinco dias, contados da data do seu conhecimento.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 192, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(193, 193, 'Produção da prova', '1. Os factos apurados durante a produção da prova, cuja existência se considere essencial a boa decisão do processo, as circunstâncias agravantes e atenuantes, devem constar discriminadamente na acta.\n2. A acta deverá ser assinada por todos os membros do tribunal, o arguido, o interessado e os respetivos advogados constituídos', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 193, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(194, 194, 'Projeto do Acórdão', 'Realizada a produção da prova a que se refere o artigo anterior, o relator elabora no prazo de quinze dias, o projeto do acórdão, do qual devem constar os factos e circunstâncias que se considere provada, qualificação jurídica, as normas infringidas e a pena aplicável.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 194, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(195, 195, 'Acórdão', '3. O processo é dado para vista, por cinco dias, a cada membro do tribunal, findo o prazo de vista, o processo é presente em sessão para julgamento.\n4. Se todos os membros do tribunal considerarem legal e justo o projeto na conferencia, é votado o e assinado o acórdão final.\n5. Os votos de vencido devem ser fundamentados.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 195, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(196, 196, 'Notificação', '1. Os acórdãos finais são notificados ao suspeito, aos interessados e ao Bastonário.\n2. Se a participação tiver sido feita por Magistrado Judicial ou do Ministério Público, o acórdão final é igualmente notificado ao participante, ainda que sem interesse direto no processo e ao Presidente do Supremo Tribunal de Justiça e ao Procurador-Geral da Republica respetivamente.\n3. A notificação do suspeito deve ser efetuada nos termos gerais da lei do processo penal.\n\n\n\nSECÇÃO IV\nRecursos Ordinários', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 196, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(197, 197, 'Deliberações Recorríveis', '1. Das deliberações do tribunal de deontologia e disciplina cabe recurso para o Conselho Nacional.\n2. Das deliberações do Conselho Nacional cabe recurso para o tribunal de deontologia e disciplina, nos termos previstos nos presentes estatutos.\n3. Das deliberações finais e executórias cabe recurso contencioso nos termos gerais de direito.\n4. Não admitem recurso em qualquer instância as decisões de mero expediente ou de disciplina dos trabalhos.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 197, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(198, 198, 'Legitimidade e Irrenunciabilidade', '1. Têm legitimidade para interpor recurso o suspeito, os interessados e o Bastonário.\n2. Não é permitida a renúncia a recurso antes do conhecimento da decisão.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 198, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(199, 199, 'Subida e Efeitos do Recurso', '1. Os recursos interpostos de despachos ou acórdãos interlocutórios sobem com o da decisão final.\n2. Têm efeito suspensivo os recursos interpostos pelo Bastonário e os das decisões finais.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 199, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(200, 200, 'Alegações', 'Admitido o recurso que subir imediatamente, são notificados o recorrente e o recorrido para apresentar alegações em prazo sucessivos de vinte dias, sendo-lhe para tanto facultada a consulta do processo.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 200, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(201, 201, 'Baixa do Processo', 'Julgado definitivamente qualquer recurso, o processo baixa a Direção Nacional, que dará conhecimento ao órgão interessado.\n\n\n\n\nSECÇÃO V\nRecurso de Revisão', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 201, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(202, 202, 'Competência', 'A revisão das decisões com trânsito em julgado é da competência da plenária do tribunal da Ordem dos Advogados.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 202, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(203, 203, 'Legitimidade', '1. O pedido de revisão das decisões deve ser formulado em requerimento fundamentado pelo interessado ou pelo suspeito condenado e, tendo este falecido, pelos seus descendentes, ascendentes, cônjuges ou irmãos.\n2. O Bastonário pode apresentar ao Conselho da Ordem proposta fundamentada de revisão de decisões.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 203, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(204, 204, 'Fundamento de Admissibilidade', 'A decisão com trânsito em julgado apenas pode ser revista nos seguintes casos:\na) Quando se tenham descoberto novos factos, novas provas documentais suscetíveis de alterar a decisão proferida;\nb) Quando outra decisão transitada em julgado declarar falsos quaisquer elementos de prova suscetíveis de terem determinado a decisão revidenda;\nc) Quando se mostrar, por exame psiquiátrico ou outras diligências, que a falta de integridade mental do suspeito condenado poderia ter determinado a sua inimputabilidade.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 204, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(205, 205, 'Tramitação', '1. O pedido de revisão é submetido ao tribunal acompanhado das alegações do recorrente e dos meios probatórios que a este se oferecerem.\n2. Recebido o pedido é efetuada a distribuição e requisição, destruído o processo ao mesmo relatório que proferiu a decisão revidenda.\n3. Tratando-se de pedido do Bastonário, é notificado o suspeito condenado ou absolvido, consoante os casos, para alegar no prazo de vinte dias, apresentando simultaneamente as provas.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 205, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(206, 206, 'Julgamento', '1. Realizadas as diligências requeridas, quando a elas houver lugar, o relator elabora o seu parecer, seguindo depois o processo para vista, por cinco dias, a cada um dos membros do tribunal.\n2. Findo o prazo de vista, o processo é submetido à deliberação do coletivo que antes de decidir, pode ainda ordenar diligências.\n3. Sendo ordenadas novas diligências, é efetuada a redistribuição do processo a um membro do Coletivo que tenha votado nesse sentido.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 206, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(207, 207, 'Maioria Qualificada', 'A concessão da revisão tem de ser votada pela maioria de pelo menos dois terços dos membros do tribunal e da respetiva deliberação não cabe recurso.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 207, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(208, 208, 'Baixa do Processo, Averbamento e Publicidade', '1. O processo, depois de julgado o pedido ou proposta de revisão, baixa ao Conselho respetiva, que o instrui e julga de novo, se a revisão tiver sido admitida.\n2. No caso de absolvição, serão cancelados os averbamentos das decisões condenatórias.\n3. Será dada publicidade ao acórdão de revisão quando resulte a absolvição e a decisão condenatória revista tenha sido publicitada.\nSECÇÃO VI\nExecução de Penas', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 208, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(209, 209, 'Competência do Bastonário', 'Compete a Direção Nacional dar execução a todas as decisões proferidas nos processos em que sejam suspeitos os advogados.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 209, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(210, 210, 'Consequência da Falta de Cumprimento de', 'Decisões Disciplinares\nÉ suspensa a inscrição do advogado punido até o cumprimento das decisões disciplinares.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 210, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(211, 211, 'Início do Cumprimento da Pena de Suspensão', '1. O cumprimento das penas de suspensão ou proibição de exercício da atividade tem início a partir do dia imediato ao da publicação da respetiva decisão.\n2. Se à data da publicação estiver suspensa ou cancelada a inscrição do suspeito, o cumprimento da pena de suspensão tem início a partir do dia imediato àquele em que tiver lugar o levantamento da suspensão da inscrição ou da reinscrição ou a partir do termo de anterior pena de suspensão.\nCAPÍTULO V\nReabilitação do Advogado Expulso', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 211, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(212, 212, 'Regime', '1. O advogado punido com proibição do exercício da atividade pode ser reabilitado desde que cumulativamente tenham decorrido mais de 5 anos sobre a data em que se tornou definitiva a decisão que aplicou a pena de proibição e o reabilitado tenha revelado boa conduta, podendo, para o demostrar, utilizar meios de prova admitidos em direito.\n2. Ao pedido de reabilitação é aplicável, com as necessárias adaptações, o disposto no artigo 306° CPP.\n3. Concedida a reabilitação, nos termos do número anterior, o advogado reabilitado recupera plenamente os seus direitos e é dada a publicidade devida, nos termos previstos nos estatutos\nTÍTULO VI\nDISPOSIÇÕES FINAIS TRANSITÓRIAS', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 212, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(213, 213, 'Aplicação no Tempo das Incompatibilidades e Impedimentos', 'As incompatibilidades e impedimentos criados pelo presente Estatuto não prejudicam os direitos legalmente adquiridos.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 213, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(214, 214, 'Exercício Ilegal da Advocacia', '1. O exercício da advocacia realizado de forma diversa do estabelecido no presente estatuto será considerado ilegal.\n2. Os juízes, magistrados do Ministério Público, conservadores, notários e os responsáveis de repartições públicas têm a obrigação de comunicar à Ordem o exercício ilegal do patrocínio judiciário.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 214, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(215, 215, 'Funcionários públicos', 'Os funcionários e agentes da administração pública, central e local, dos serviços personalizados do Estado e institutos públicos, com inscrição em vigor na Ordem, nos termos do anterior Estatuto, dispõem de um prazo de seis meses para declararem a sua situação de funcionário público.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 215, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(216, 216, 'Período de transição', 'Fica estabelecido um período de transição de seis meses, para a preparação de eleições de novos órgãos da Ordem, nos termos do presente estatuto.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 216, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(217, 217, 'Entrada em vigor do estatuto', 'As normas do presente estatuto entram em vigor com a eleição de novos órgãos da Ordem.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 217, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(218, 218, 'Revisão', 'O presente estatuto pode ser revisto ao fim do quarto ano subsequente a sua entrada em vigor.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 218, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53'),
(219, 219, 'Revogação', 'É Revogado os artigos contrários do antigo Estatuto da Ordem dos Advogados da Guiné-Bissau publicado no Boletim Oficial, n.º 52, de 28 de Dezembro de 1992.', 'Disciplina e Processo', NULL, NULL, NULL, NULL, NULL, NULL, 219, 1, '2026-03-29 22:51:53', '2026-03-29 22:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `pergunta` varchar(500) NOT NULL,
  `resposta` text NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `visualizacoes` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `pergunta`, `resposta`, `categoria`, `ordem_exibicao`, `visualizacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Como posso inscrever-me na Ordem dos Advogados?', 'Para se inscrever na OAGB, deve preencher o formulário de inscrição disponível no nosso site, anexar os documentos necessários e aguardar a análise do seu pedido.', 'Inscrições', 1, 0, 1, '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(2, 'Quais são os documentos necessários para a inscrição?', 'Os documentos necessários incluem: cópia do diploma de licenciatura em Direito, BI ou passaporte, certificado de registo criminal, duas fotografias tipo passe e comprovativo de pagamento da taxa de inscrição.', 'Inscrições', 2, 0, 1, '2025-09-07 12:55:48', '2025-09-07 12:55:48'),
(3, 'Como posso solicitar um advogado?', 'Pode solicitar um advogado através do formulário disponível na secção \"Solicitação de Advogados\" do nosso site, indicando a área jurídica e região de preferência.', 'Serviços', 3, 0, 1, '2025-09-07 12:55:48', '2025-09-07 12:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `ficheiros_anexos`
--

CREATE TABLE `ficheiros_anexos` (
  `id` int(11) NOT NULL,
  `tipo_entidade` enum('noticia','evento','documento','parecer') NOT NULL,
  `entidade_id` int(11) NOT NULL,
  `nome_ficheiro` varchar(255) NOT NULL,
  `nome_original` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamanho` int(11) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `downloads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finan_config`
--

CREATE TABLE `finan_config` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finan_config`
--

INSERT INTO `finan_config` (`id`, `chave`, `valor`, `descricao`, `updated_at`) VALUES
(1, 'quota_advogado', '15000', 'Valor da quota mensal para advogados (CFA)', '2026-05-06 20:43:03'),
(2, 'quota_estagiario', '5000', 'Valor da quota mensal para estagiários (CFA)', '2026-05-06 20:43:03'),
(3, 'orange_money_merchant_id', 'OAGB_MERCH_001', 'Merchant ID Orange Money', '2026-05-06 20:43:03'),
(4, 'orange_money_api_key', 'pk_test_oagb_12345', 'Public API Key Orange Money', '2026-05-06 20:43:03'),
(5, 'orange_money_secret', 'sk_test_oagb_67890', 'Secret API Key Orange Money', '2026-05-06 20:43:03'),
(6, 'orange_money_enabled', '1', 'Ativar pagamentos Orange Money (1=Sim, 0=Não)', '2026-05-06 22:23:08'),
(7, 'stripe_public_key', 'pk_test_oagb_stripe_123', 'Chave Pública Stripe (VISA/Mastercard)', '2026-05-06 20:57:25'),
(8, 'stripe_secret_key', 'sk_test_oagb_stripe_456', 'Chave Secreta Stripe', '2026-05-06 20:57:25'),
(9, 'paypal_client_id', 'oagb_paypal_id_789', 'PayPal Client ID', '2026-05-06 20:57:25'),
(10, 'global_payments_enabled', '0', 'Ativar Pagamentos Internacionais (Stripe/PayPal)', '2026-05-06 20:57:25'),
(11, 'mtn_momo_api_key', '', 'API Key MTN Mobile Money', '2026-05-07 18:41:17'),
(12, 'mtn_momo_secret', '', 'Secret Key MTN Mobile Money', '2026-05-07 18:41:17'),
(13, 'mtn_momo_enabled', '0', 'Ativar MTN Mobile Money (1=Sim, 0=No)', '2026-05-07 18:41:17'),
(14, 'bastonario_nome', 'Nome do Bastonßrio', 'Nome do Bastonßrio para Certid§es', '2026-05-08 10:19:21'),
(15, 'bastonario_assinatura', '', 'Ficheiro de Assinatura do Bastonßrio', '2026-05-08 10:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `finan_pagamentos`
--

CREATE TABLE `finan_pagamentos` (
  `id` int(11) NOT NULL,
  `advogado_id` int(11) DEFAULT NULL,
  `membro_tipo` enum('advogado','estagiario') DEFAULT 'advogado',
  `inscricao_id` int(11) DEFAULT NULL,
  `tipo_pagamento_id` int(11) DEFAULT NULL,
  `meses_pagos` int(11) DEFAULT 1,
  `valor_pago` decimal(10,2) NOT NULL,
  `data_pagamento` datetime NOT NULL,
  `valid_until` date DEFAULT NULL,
  `comprovativo_arquivo` varchar(255) DEFAULT NULL,
  `metodo_pagamento` enum('transferencia','dinheiro','deposito','outro') DEFAULT 'transferencia',
  `observacoes` text DEFAULT NULL,
  `validado_por` int(11) DEFAULT NULL,
  `status` enum('pendente','confirmado','cancelado') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finan_pagamentos`
--

INSERT INTO `finan_pagamentos` (`id`, `advogado_id`, `membro_tipo`, `inscricao_id`, `tipo_pagamento_id`, `meses_pagos`, `valor_pago`, `data_pagamento`, `valid_until`, `comprovativo_arquivo`, `metodo_pagamento`, `observacoes`, `validado_por`, `status`, `created_at`) VALUES
(1, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-06 20:16:02', '2026-05-31', NULL, 'transferencia', NULL, NULL, 'confirmado', '2026-05-06 20:16:02'),
(2, 2, 'advogado', NULL, 1, 1, 15000.00, '2026-05-06 20:16:02', NULL, NULL, 'deposito', NULL, NULL, 'pendente', '2026-05-06 20:16:02'),
(3, 4, 'advogado', NULL, 2, 1, 5000.00, '2026-05-06 20:16:02', '2026-05-26', NULL, 'transferencia', NULL, NULL, 'confirmado', '2026-05-06 20:16:02'),
(4, 5, 'advogado', NULL, 2, 1, 5000.00, '2026-05-06 20:16:02', '2026-06-04', NULL, 'transferencia', NULL, NULL, 'confirmado', '2026-05-06 20:16:02'),
(5, 1, 'advogado', NULL, NULL, 1, 15000.00, '2026-05-06 22:35:09', NULL, NULL, '', NULL, NULL, 'pendente', '2026-05-06 22:35:09'),
(6, 1, 'advogado', NULL, NULL, 3, 45000.00, '2026-05-06 22:37:26', NULL, NULL, '', NULL, NULL, 'pendente', '2026-05-06 22:37:26'),
(7, 1, 'advogado', NULL, NULL, 1, 15000.00, '2026-05-07 18:00:50', NULL, NULL, '', NULL, NULL, 'pendente', '2026-05-07 18:00:50'),
(8, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-07 18:10:15', '2026-06-07', NULL, '', NULL, NULL, 'confirmado', '2026-05-07 18:10:15'),
(9, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-07 18:12:12', '2026-06-07', NULL, '', NULL, NULL, 'confirmado', '2026-05-07 18:12:12'),
(10, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-07 18:16:05', '2026-06-07', NULL, '', NULL, NULL, 'confirmado', '2026-05-07 18:16:05'),
(11, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-07 18:35:57', '2026-06-08', NULL, '', NULL, NULL, 'confirmado', '2026-05-07 18:35:57'),
(12, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-08 09:58:27', '2026-06-08', NULL, '', NULL, NULL, 'confirmado', '2026-05-08 09:58:27'),
(13, 1, 'advogado', NULL, 1, 3, 45000.00, '2026-05-08 10:05:52', '2026-08-08', NULL, '', NULL, NULL, 'confirmado', '2026-05-08 10:05:52'),
(14, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-08 20:11:55', NULL, NULL, '', NULL, NULL, 'pendente', '2026-05-08 20:11:55'),
(15, 1, 'advogado', NULL, 1, 1, 15000.00, '2026-05-09 10:59:22', NULL, NULL, '', NULL, NULL, 'pendente', '2026-05-09 10:59:22');

-- --------------------------------------------------------

--
-- Table structure for table `finan_tipos_pagamento`
--

CREATE TABLE `finan_tipos_pagamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor_padrao` decimal(10,2) DEFAULT 0.00,
  `periodicidade` enum('unico','mensal','anual') DEFAULT 'unico',
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finan_tipos_pagamento`
--

INSERT INTO `finan_tipos_pagamento` (`id`, `nome`, `descricao`, `valor_padrao`, `periodicidade`, `ativo`, `created_at`) VALUES
(1, 'Quota Mensal (Advogado)', NULL, 5000.00, 'mensal', 1, '2026-03-20 11:57:45'),
(2, 'Quota Mensal (Estagißrio)', NULL, 2500.00, 'mensal', 1, '2026-03-20 11:57:45'),
(3, 'Taxa de InscriþÒo', NULL, 50000.00, 'unico', 1, '2026-03-20 11:57:45'),
(4, 'EmissÒo de CÚdula Profissional', NULL, 10000.00, 'unico', 1, '2026-03-20 11:57:45');

-- --------------------------------------------------------

--
-- Table structure for table `gestao_actas`
--

CREATE TABLE `gestao_actas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_reuniao` date DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `ficheiro_url` varchar(255) DEFAULT NULL,
  `status` enum('rascunho','finalizada') DEFAULT 'rascunho',
  `partilha_interna` tinyint(1) DEFAULT 1,
  `partilha_ordem` tinyint(1) DEFAULT 0,
  `criada_por` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_biblioteca`
--

CREATE TABLE `gestao_biblioteca` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('Lei','Regulamento','Acordao','Parecer','Outro') DEFAULT 'Lei',
  `ficheiro_url` varchar(255) NOT NULL,
  `data_publicacao` date DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_comissoes`
--

CREATE TABLE `gestao_comissoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_comissoes_membros`
--

CREATE TABLE `gestao_comissoes_membros` (
  `id` int(11) NOT NULL,
  `comissao_id` int(11) NOT NULL,
  `advogado_id` int(11) NOT NULL,
  `cargo` varchar(100) DEFAULT 'Vogal',
  `data_entrada` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_configuracoes`
--

CREATE TABLE `gestao_configuracoes` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) DEFAULT NULL,
  `config_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestao_configuracoes`
--

INSERT INTO `gestao_configuracoes` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'voto_min_quotas', '1', 'Meses de quota em dia necessarios para votar', '2026-03-20 12:45:02'),
(2, 'voto_n_meses_antiguidade', '0', 'Meses de inscricao na Ordem para votar', '2026-03-20 12:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `gestao_cursos`
--

CREATE TABLE `gestao_cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `vagas` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestao_cursos`
--

INSERT INTO `gestao_cursos` (`id`, `titulo`, `descricao`, `data_inicio`, `data_fim`, `vagas`, `preco`, `ativa`, `created_at`) VALUES
(1, 'Seminário de Ética e Deontologia Profissional', 'Um mergulho profundo nos princípios éticos que regem a advocacia na Guiné-Bissau. Essencial para estagiários e advogados em início de carreira.', '2024-06-15', '2024-06-17', 50, 15000.00, 1, '2026-05-09 12:23:26'),
(2, 'Curso de Especialização em Direito Administrativo', 'Análise detalhada dos procedimentos administrativos, contencioso e a relação entre o cidadão e o Estado guineense.', '2024-07-01', '2024-07-30', 30, 75000.00, 1, '2026-05-09 12:23:26'),
(3, 'Workshop: Prática Processual Penal', 'Treino prático sobre as fases do processo penal, desde a instrução até ao julgamento. Focado em competências de tribunal.', '2024-08-10', '2024-08-12', 25, 25000.00, 1, '2026-05-09 12:23:26'),
(4, 'Formação em Mediação e Resolução de Conflitos', 'Desenvolvimento de competências em métodos alternativos de resolução de litígios. Uma abordagem moderna para a justiça célere.', '2024-09-05', '2024-09-20', 20, 45000.00, 1, '2026-05-09 12:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `gestao_cursos_inscritos`
--

CREATE TABLE `gestao_cursos_inscritos` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `advogado_id` int(11) NOT NULL,
  `status` enum('pendente','confirmado','concluido') DEFAULT 'pendente',
  `nota` varchar(50) DEFAULT NULL,
  `certificado_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestao_cursos_inscritos`
--

INSERT INTO `gestao_cursos_inscritos` (`id`, `curso_id`, `advogado_id`, `status`, `nota`, `certificado_url`) VALUES
(1, 3, 1, 'confirmado', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gestao_disciplinar_processos`
--

CREATE TABLE `gestao_disciplinar_processos` (
  `id` int(11) NOT NULL,
  `numero_processo` varchar(50) DEFAULT NULL,
  `queixoso_nome` varchar(255) DEFAULT NULL,
  `advogado_id` int(11) NOT NULL,
  `relator_id` int(11) DEFAULT NULL,
  `status` enum('aberto','instrucao','julgamento','arquivado','sancionado') DEFAULT 'aberto',
  `sancao_tipo` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `conclusao` text DEFAULT NULL,
  `data_abertura` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_eleicoes`
--

CREATE TABLE `gestao_eleicoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_eleicao` date NOT NULL,
  `ativa` tinyint(1) DEFAULT 0,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_estagio_interacoes`
--

CREATE TABLE `gestao_estagio_interacoes` (
  `id` int(11) NOT NULL,
  `relatorio_id` int(11) DEFAULT NULL,
  `autor_id` int(11) DEFAULT NULL,
  `autor_tipo` enum('advogado','estagiario','admin') DEFAULT NULL,
  `tipo` enum('comentario','nota_interna','revisao') DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_estagio_relatorios`
--

CREATE TABLE `gestao_estagio_relatorios` (
  `id` int(11) NOT NULL,
  `estagiario_id` int(11) NOT NULL,
  `orientador_id` int(11) NOT NULL,
  `tipo_documento` varchar(50) DEFAULT 'Relat¾rio Mensal',
  `ficheiro_pdf` varchar(255) NOT NULL,
  `status` enum('pendente','validado','rejeitado','revisao') DEFAULT NULL,
  `data_submissao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_validacao` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `relatorio_firma` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestao_estagio_relatorios`
--

INSERT INTO `gestao_estagio_relatorios` (`id`, `estagiario_id`, `orientador_id`, `tipo_documento`, `ficheiro_pdf`, `status`, `data_submissao`, `data_validacao`, `observacoes`, `relatorio_firma`) VALUES
(1, 4, 1, 'Relat¾rio Mensal', 'REL_EST_4_1715000000.pdf', 'validado', '2026-04-21 00:00:00', NULL, 'Bom desempenho.', NULL),
(2, 5, 1, 'Relat¾rio Mensal', 'REL_EST_5_1715100000.pdf', 'pendente', '2026-05-03 00:00:00', NULL, NULL, NULL),
(3, 4, 1, 'Relat¾rio Mensal', 'dummy_report.pdf', 'validado', '2026-05-08 09:58:55', '2026-05-09 10:58:51', '', ''),
(4, 5, 1, 'Relat¾rio Mensal', 'dummy_report.pdf', 'validado', '2026-05-08 15:48:23', '2026-05-08 16:26:17', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `gestao_notificacoes`
--

CREATE TABLE `gestao_notificacoes` (
  `id` int(11) NOT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `tipo` enum('sms','push','email') NOT NULL,
  `mensagem` text DEFAULT NULL,
  `status` enum('pendente','enviado','erro') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gestao_notificacoes`
--

INSERT INTO `gestao_notificacoes` (`id`, `destinatario_id`, `tipo`, `mensagem`, `status`, `created_at`) VALUES
(1, 1, 'sms', 'OAGB: O seu vínculo com o estagiário António Lopes foi atualizado.', 'pendente', '2026-05-08 16:29:49'),
(2, 1, 'sms', 'OAGB: Novo pedido de vinculação do estagiário António Lopes. Por favor, aceda ao portal para aceitar.', 'pendente', '2026-05-08 20:06:03'),
(3, 1, 'sms', 'OAGB: Novo pedido de vinculação do estagiário António Lopes. Por favor, aceda ao portal para aceitar.', 'pendente', '2026-05-08 20:21:35'),
(4, 0, 'email', 'Para: antonio.lopes@email.gw | Assunto: Confirmação de Vínculo de Estágio - OAGB', 'pendente', '2026-05-08 20:21:43'),
(5, 0, 'email', 'Para: antonio.santos@email.gw | Assunto: Cópia de Comprovativo de Vínculo - OAGB', 'pendente', '2026-05-08 20:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `gestao_opcoes`
--

CREATE TABLE `gestao_opcoes` (
  `id` int(11) NOT NULL,
  `eleicao_id` int(11) NOT NULL,
  `nome_lista` varchar(255) NOT NULL,
  `cor` varchar(50) DEFAULT '#B1A276'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_sociedades`
--

CREATE TABLE `gestao_sociedades` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `nif` varchar(50) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestao_votos`
--

CREATE TABLE `gestao_votos` (
  `id` int(11) NOT NULL,
  `eleicao_id` int(11) NOT NULL,
  `advogado_id` int(11) NOT NULL,
  `hash_voto` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `glossario_juridico`
--

CREATE TABLE `glossario_juridico` (
  `id` int(11) NOT NULL,
  `termo` varchar(255) NOT NULL,
  `letra` char(1) NOT NULL,
  `definicao` text NOT NULL,
  `exemplo_uso` text DEFAULT NULL,
  `categoria` enum('Geral','Latinismo','Expressao') DEFAULT 'Geral',
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glossario_juridico`
--

INSERT INTO `glossario_juridico` (`id`, `termo`, `letra`, `definicao`, `exemplo_uso`, `categoria`, `ordem`, `status`, `created_at`) VALUES
(1, 'Ação', 'A', 'Meio pelo qual alguém recorre ao tribunal para defender um direito.', 'O advogado propôs uma ação contra o devedor.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(2, 'Acórdão', 'A', 'Decisão proferida por um tribunal coletivo.', 'O acórdão do Tribunal da Relação foi favorável ao réu.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(3, 'Advogado', 'A', 'Profissional habilitado a representar e defender cidadãos perante os tribunais.', 'O advogado apresentou as suas alegações finais.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(4, 'Agravo', 'A', 'Recurso judicial contra decisões interlocutórias.', 'Foi interposto agravo da decisão do juiz.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(5, 'Bastonário', 'B', 'Presidente da Ordem dos Advogados. Representa a classe e lidera a instituição.', 'O Bastonário convocou uma reunião extraordinária.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(6, 'Caução', 'C', 'Garantia prestada para assegurar o cumprimento de uma obrigação.', 'O tribunal exigiu caução para a libertação provisória.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(7, 'Citação', 'C', 'Ato pelo qual se dá conhecimento ao réu de que foi proposta contra ele uma ação.', 'A citação foi entregue por oficial de justiça.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(8, 'Contumácia', 'C', 'Situação do arguido que se subtrai à justiça, não comparecendo em tribunal.', 'O arguido foi declarado em contumácia.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(9, 'Dano', 'D', 'Prejuízo causado a alguém, podendo ser material ou moral.', 'O tribunal condenou ao pagamento do dano causado.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(10, 'Deontologia', 'D', 'Conjunto de regras éticas que regem o exercício de uma profissão.', 'O Código de Deontologia dos Advogados é de cumprimento obrigatório.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(11, 'Edital', 'E', 'Aviso público afixado ou publicado para dar conhecimento de um ato.', 'O edital foi publicado no Boletim Oficial.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(12, 'Fiança', 'F', 'Garantia pessoal em que alguém se responsabiliza pelo cumprimento de uma obrigação de outrem.', 'O fiador prestou fiança pelo pagamento da dívida.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(13, 'Grau de Jurisdição', 'G', 'Nível hierárquico dos tribunais na organização judiciária.', 'O recurso seguiu para o tribunal de segundo grau.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(14, 'Habeas Corpus', 'H', 'Providência que garante a liberdade individual contra detenções ilegais ou abusivas.', 'O advogado interpôs habeas corpus pela libertação do detido.', 'Latinismo', 0, 'ativo', '2026-05-05 10:44:41'),
(15, 'Inventário', 'I', 'Processo judicial ou administrativo de partilha de bens de uma herança.', 'O inventário dos bens do falecido demorou dois anos.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(16, 'Juiz', 'J', 'Magistrado judicial com competência para julgar e decidir causas.', 'O juiz proferiu a sentença após ouvir as partes.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(17, 'Lacuna da Lei', 'L', 'Ausência de regulamentação legal para uma situação concreta.', 'O juiz recorreu à analogia para colmatar a lacuna da lei.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(18, 'Mandato', 'M', 'Contrato pelo qual alguém confere poderes a outrem para agir em seu nome.', 'O cliente conferiu mandato ao advogado mediante procuração.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(19, 'Nulidade', 'N', 'Vício que torna um ato jurídico sem efeito.', 'O contrato foi declarado nulo por falta de forma legal.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(20, 'Oposição', 'O', 'Meio processual pelo qual alguém se opõe a uma pretensão.', 'O réu deduziu oposição à execução.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(21, 'Procuração', 'P', 'Documento pelo qual se conferem poderes de representação a um advogado.', 'A procuração forense é obrigatória para o advogado agir em juízo.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(22, 'Queixa', 'Q', 'Participação de um crime feita pelo ofendido às autoridades.', 'A vítima apresentou queixa na polícia judiciária.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(23, 'Recurso', 'R', 'Meio processual para impugnar uma decisão judicial perante tribunal superior.', 'Foi interposto recurso da sentença de primeira instância.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(24, 'Sentença', 'S', 'Decisão do juiz que põe termo a um processo.', 'A sentença condenou o réu ao pagamento de indemnização.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(25, 'Tribunal', 'T', 'Órgão do Estado com poder de julgar e aplicar a lei.', 'O caso foi remetido ao Tribunal Regional de Bissau.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(26, 'Usucapião', 'U', 'Aquisição de propriedade pela posse prolongada e contínua.', 'Após 20 anos de posse pacífica, adquiriu por usucapião.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(27, 'Veredito', 'V', 'Decisão de um júri sobre a culpabilidade do arguido.', 'O veredito do júri foi de absolvição.', 'Geral', 0, 'ativo', '2026-05-05 10:44:41'),
(28, 'Ad hoc', 'A', 'Expressão latina que significa para este efeito ou para esta finalidade.', 'Foi nomeado um advogado ad hoc para representar o menor.', 'Latinismo', 0, 'ativo', '2026-05-05 10:44:41'),
(29, 'In dubio pro reo', 'I', 'Em caso de dúvida, a decisão deve favorecer o réu.', 'O tribunal absolveu o arguido com base no princípio in dubio pro reo.', 'Latinismo', 0, 'ativo', '2026-05-05 10:44:41'),
(30, 'Habeas data', 'H', 'Direito de acesso e retificação de dados pessoais em registos públicos.', 'Invocou o habeas data para corrigir os seus dados no registo civil.', 'Latinismo', 0, 'ativo', '2026-05-05 10:44:41'),
(31, 'De facto', 'D', 'Situação que existe na prática, independentemente do reconhecimento legal.', 'A união de facto não confere os mesmos direitos que o casamento.', 'Latinismo', 0, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `info_cidadaos`
--

CREATE TABLE `info_cidadaos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icone` varchar(50) DEFAULT 'fas fa-info-circle',
  `imagem` varchar(255) DEFAULT NULL,
  `conteudo` text NOT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info_cidadaos`
--

INSERT INTO `info_cidadaos` (`id`, `titulo`, `slug`, `icone`, `imagem`, `conteudo`, `arquivo`, `ordem`, `status`, `created_at`) VALUES
(1, 'O que é o Acesso ao Direito?', 'acesso-ao-direito', 'fas fa-balance-scale', NULL, '<p>O acesso ao direito e aos tribunais é um direito fundamental consagrado na Constituição da Guiné-Bissau. Os cidadãos com insuficiência económica comprovada têm direito a apoio judiciário, que pode incluir a nomeação gratuita de um advogado.</p><p>A OAGB colabora com o Estado para garantir que ninguém fique sem defesa por razões financeiras.</p>', NULL, 1, 'ativo', '2026-05-05 10:44:41'),
(2, 'Direitos Fundamentais do Cidadão', 'direitos-fundamentais', 'fas fa-shield-alt', NULL, '<p>A Constituição da República garante a todos os cidadãos direitos e liberdades fundamentais, incluindo: direito à vida e integridade física, liberdade de expressão, direito de reunião e associação, direito ao trabalho, à educação e à saúde.</p><p>A Carta Africana dos Direitos Humanos e dos Povos (Carta de Banjul), ratificada pela Guiné-Bissau, reforça estas garantias a nível regional.</p>', NULL, 2, 'ativo', '2026-05-05 10:44:41'),
(3, 'Como Encontrar um Advogado?', 'encontrar-advogado', 'fas fa-search', NULL, '<p>A OAGB mantém uma lista atualizada de advogados inscritos e em exercício. Pode pesquisar por nome, localidade ou especialidade através da nossa <a href=\"pesquisa-advogados.php\">página de pesquisa</a>.</p><p>O acesso a um advogado é um direito fundamental. Nenhum cidadão pode ser julgado sem defesa técnica.</p>', NULL, 3, 'ativo', '2026-05-05 10:44:41'),
(4, 'O que é a Ordem dos Advogados?', 'o-que-e-a-ordem', 'fas fa-landmark', NULL, '<p>A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma pessoa coletiva de direito privado e utilidade pública, constituída por escritura pública em 1991. A sua missão é a defesa do Estado de Direito, a proteção dos direitos humanos e a regulação ética e disciplinar da profissão de advogado.</p>', NULL, 4, 'ativo', '2026-05-05 10:44:41'),
(5, 'Glossário Jurídico para Cidadãos', 'glossario', 'fas fa-book-open', NULL, '<p>Consulte o nosso <a href=\"glossario-juridico.php\">Glossário de Termos Jurídicos</a> — uma ferramenta para combater a iliteracia jurídica, explicando em linguagem acessível os conceitos e expressões usados na prática da advocacia e dos tribunais.</p>', NULL, 5, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `inscricoes_ordem`
--

CREATE TABLE `inscricoes_ordem` (
  `id` int(11) NOT NULL,
  `tipo_inscricao` enum('advogado','estagiario') NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `genero` enum('M','F') NOT NULL,
  `data_nascimento` date NOT NULL,
  `nacionalidade` varchar(50) DEFAULT 'Guineense',
  `bi_passaporte` varchar(50) NOT NULL,
  `regiao` varchar(50) NOT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `morada` text NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `formacao_academica` text NOT NULL,
  `experiencia_profissional` text DEFAULT NULL,
  `documentos_anexos` text DEFAULT NULL,
  `status` enum('pendente','em_analise','aprovado','rejeitado') DEFAULT 'pendente',
  `observacoes_admin` text DEFAULT NULL,
  `data_aprovacao` date DEFAULT NULL,
  `numero_registo_atribuido` varchar(20) DEFAULT NULL,
  `ip_inscricao` varchar(45) DEFAULT NULL,
  `arquivo_comprovativo` varchar(255) DEFAULT NULL,
  `data_analise` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instituicao_info`
--

CREATE TABLE `instituicao_info` (
  `id` int(11) NOT NULL,
  `missao` text NOT NULL,
  `visao` text NOT NULL,
  `valores` text NOT NULL,
  `historia` text DEFAULT NULL,
  `estatutos_url` varchar(255) DEFAULT NULL,
  `email_geral` varchar(100) NOT NULL,
  `telefone_geral` varchar(50) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `horario_funcionamento` varchar(255) DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instituicao_info`
--

INSERT INTO `instituicao_info` (`id`, `missao`, `visao`, `valores`, `historia`, `estatutos_url`, `email_geral`, `telefone_geral`, `endereco`, `horario_funcionamento`, `data_atualizacao`) VALUES
(1, 'Defender os direitos, liberdades e garantias dos cidadãos, promover a boa administração da justiça e o aperfeiçoamento da cultura jurídica, contribuindo para o desenvolvimento de uma sociedade mais justa e equitativa na Guiné-Bissau.', 'Ser reconhecida como a instituição de referência na defesa da legalidade democrática, da independência dos tribunais e do acesso à justiça em toda a África Ocidental lusófona.', 'Independência profissional, Isenção e imparcialidade, Sigilo profissional, Solidariedade entre colegas, Dignidade e decoro no exercício da profissão, Compromisso com a justiça social.', 'A Ordem dos Advogados da Guiné-Bissau (OAGB) foi instituída pela Lei nº 4/91, de 3 de Outubro, como uma associação pública representativa dos licenciados em Direito que exercem a profissão de advogado. Desde a sua fundação em 1991, a OAGB tem desempenhado um papel fundamental na defesa do Estado de Direito, na promoção dos direitos humanos e na regulação da profissão de advocacia na Guiné-Bissau. A instituição é regida pelos seus Estatutos, aprovados em Assembleia Geral Constituinte, e tem a sua sede em Bissau, podendo criar delegações regionais em todo o território nacional.', NULL, '', '', '', NULL, '2026-03-26 23:43:18');

-- --------------------------------------------------------

--
-- Table structure for table `legislacao_internacional`
--

CREATE TABLE `legislacao_internacional` (
  `id` int(11) NOT NULL,
  `organizacao` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_adocao` date DEFAULT NULL,
  `data_ratificacao_gb` date DEFAULT NULL,
  `resumo` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `conteudo` text DEFAULT NULL,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legislacao_internacional`
--

INSERT INTO `legislacao_internacional` (`id`, `organizacao`, `titulo`, `data_adocao`, `data_ratificacao_gb`, `resumo`, `imagem`, `conteudo`, `ficheiro_pdf`, `link_externo`, `ordem`, `status`, `created_at`) VALUES
(1, 'OHADA', 'Tratado de Port-Louis (Tratado OHADA)', '1993-10-17', '1994-01-15', 'Tratado que cria a Organização para a Harmonização em África do Direito dos Negócios. A Guiné-Bissau ratificou em 1994, com entrada em vigor em 20/02/1996.', NULL, NULL, NULL, 'https://www.ohada.org', 1, 'ativo', '2026-05-05 10:44:41'),
(2, 'OHADA', 'Ato Uniforme sobre Direito Comercial Geral', '2010-12-15', NULL, 'Regulamenta o estatuto do comerciante, o registo comercial, o fundo de comércio e as obrigações comerciais em todos os Estados OHADA.', NULL, NULL, NULL, 'https://www.ohada.org', 2, 'ativo', '2026-05-05 10:44:41'),
(3, 'OHADA', 'Ato Uniforme sobre Sociedades Comerciais e GIE', '2014-01-30', NULL, 'Estabelece regras para constituição, gestão e dissolução de sociedades comerciais e grupos de interesse económico.', NULL, NULL, NULL, 'https://www.ohada.org', 3, 'ativo', '2026-05-05 10:44:41'),
(4, 'OHADA', 'Ato Uniforme sobre Garantias', '2010-12-15', NULL, 'Regula as garantias pessoais (fiança, garantia autónoma) e reais (penhor, hipoteca) no espaço OHADA.', NULL, NULL, NULL, 'https://www.ohada.org', 4, 'ativo', '2026-05-05 10:44:41'),
(5, 'OHADA', 'Ato Uniforme sobre Arbitragem', '2017-11-23', NULL, 'Promove a arbitragem como método alternativo de resolução de litígios comerciais.', NULL, NULL, NULL, 'https://www.ohada.org', 5, 'ativo', '2026-05-05 10:44:41'),
(6, 'CEDEAO', 'Tratado Revisto da CEDEAO', '1993-07-24', NULL, 'Tratado fundador revisto que estabelece a Comunidade Económica dos Estados da África Ocidental, visando integração económica regional.', NULL, NULL, NULL, 'https://www.ecowas.int', 6, 'ativo', '2026-05-05 10:44:41'),
(7, 'CEDEAO', 'Protocolo sobre a Livre Circulação de Pessoas', '1979-05-29', NULL, 'Garante o direito de residência e estabelecimento dos cidadãos dos Estados-membros no espaço CEDEAO.', NULL, NULL, NULL, 'https://www.ecowas.int', 7, 'ativo', '2026-05-05 10:44:41'),
(8, 'CEDEAO', 'Tribunal de Justiça da CEDEAO', '1991-07-06', NULL, 'Tribunal supranacional competente para julgar violações de direitos humanos e litígios entre Estados-membros da CEDEAO. Sede em Abuja, Nigéria.', NULL, NULL, NULL, 'https://www.courtecowas.org', 8, 'ativo', '2026-05-05 10:44:41'),
(9, 'União Africana', 'Carta Africana dos Direitos Humanos e dos Povos (Carta de Banjul)', '1981-06-27', NULL, 'Instrumento regional de proteção dos direitos humanos adotado pela OUA (agora UA). Consagra direitos civis, políticos, económicos e culturais.', NULL, NULL, NULL, 'https://au.int', 9, 'ativo', '2026-05-05 10:44:41'),
(10, 'União Africana', 'Ato Constitutivo da União Africana', '2000-07-11', NULL, 'Instrumento fundador da UA, que substitui a OUA. Promove a unidade, a paz, a segurança e o desenvolvimento do continente.', NULL, NULL, NULL, 'https://au.int', 10, 'ativo', '2026-05-05 10:44:41'),
(11, 'CPLP', 'Acordos de Cooperação Jurídica da CPLP', '1998-07-17', NULL, 'Cooperação entre as Ordens de Advogados dos países lusófonos para reconhecimento mútuo e troca de jurisprudência.', NULL, NULL, NULL, 'https://www.cplp.org', 11, 'ativo', '2026-05-05 10:44:41'),
(12, 'Direitos Humanos', 'Declaração Universal dos Direitos Humanos', '1948-12-10', NULL, 'Instrumento fundamental adotado pela ONU que proclama os direitos inalienáveis de todos os seres humanos.', NULL, NULL, NULL, 'https://www.un.org/pt', 12, 'ativo', '2026-05-05 10:44:41'),
(13, 'Direitos Humanos', 'Convenção sobre os Direitos da Criança', '1989-11-20', '1990-08-20', 'Ratificada pela Guiné-Bissau. Protege os direitos das crianças a nível internacional.', NULL, NULL, NULL, 'https://www.unicef.org', 13, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `legislacao_nacional`
--

CREATE TABLE `legislacao_nacional` (
  `id` int(11) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `diploma_legal` varchar(255) DEFAULT NULL,
  `data_publicacao` date DEFAULT NULL,
  `resumo` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `conteudo` text DEFAULT NULL,
  `ficheiro_pdf` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legislacao_nacional`
--

INSERT INTO `legislacao_nacional` (`id`, `categoria`, `titulo`, `diploma_legal`, `data_publicacao`, `resumo`, `imagem`, `conteudo`, `ficheiro_pdf`, `ordem`, `status`, `created_at`) VALUES
(1, 'Constituição', 'Constituição da República da Guiné-Bissau', 'Aprovada em 16 de Maio de 1984', '1984-05-16', 'Lei fundamental do Estado soberano, democrático, laico e unitário. Estabelece os direitos, liberdades e garantias dos cidadãos. Revista em 1991, 1993 e 1996.', NULL, NULL, NULL, 1, 'ativo', '2026-05-05 10:44:41'),
(2, 'Direito Penal', 'Código Penal', 'Decreto-Lei n.º 4/93, de 13 de Outubro', '1993-10-13', 'Modernizou a legislação penal guineense, adaptando-a à realidade de um Estado independente e democrático.', NULL, NULL, NULL, 2, 'ativo', '2026-05-05 10:44:41'),
(3, 'Direito Penal', 'Código de Processo Penal', 'Decreto-Lei n.º 5/93, de 13 de Outubro', '1993-10-13', 'Estabelece as normas do procedimento criminal, prazos, competências e garantias processuais.', NULL, NULL, NULL, 3, 'ativo', '2026-05-05 10:44:41'),
(4, 'Direito Civil', 'Código Civil', 'Base no Código Civil de 1966', '1966-01-01', 'Ordenamento civil com raízes no direito português, com alterações posteriores ao nível do direito da família e propriedade.', NULL, NULL, NULL, 4, 'ativo', '2026-05-05 10:44:41'),
(5, 'Direito do Trabalho', 'Lei Geral do Trabalho', 'Lei n.º 2/86', '1986-04-05', 'Regulamenta as relações laborais, direitos do trabalhador e do empregador na Guiné-Bissau.', NULL, NULL, NULL, 5, 'ativo', '2026-05-05 10:44:41'),
(6, 'Direito Fundiário', 'Lei de Terras', 'Lei n.º 5/98', '1998-04-23', 'Regime jurídico dos solos. Define propriedade estatal e comunitária, concessões e uso da terra.', NULL, NULL, NULL, 6, 'ativo', '2026-05-05 10:44:41'),
(7, 'Direito Comercial', 'Direito Comercial (OHADA)', 'Atos Uniformes OHADA', '1996-02-20', 'O antigo Código Comercial português foi substituído pelo sistema harmonizado da OHADA, vigente desde 1996.', NULL, NULL, NULL, 7, 'ativo', '2026-05-05 10:44:41'),
(8, 'Direito da Família', 'Lei da Família', 'Lei n.º 10/92', '1992-10-06', 'Regula o casamento, filiação, adoção, tutela e responsabilidades parentais.', NULL, NULL, NULL, 8, 'ativo', '2026-05-05 10:44:41'),
(9, 'Estatuto da Advocacia', 'Estatutos da OAGB (2018)', 'Aprovados em 2018 — 6 títulos, 219 artigos', '2018-01-01', 'Modernização dos estatutos após ~30 anos. Reforço da independência, internacionalização e ética profissional.', NULL, NULL, NULL, 9, 'ativo', '2026-05-05 10:44:41'),
(10, 'Direito Fiscal', 'Código Geral Tributário', 'Aprovado por decreto', '2010-01-01', 'Regime fiscal aplicável a pessoas singulares e coletivas na Guiné-Bissau.', NULL, NULL, NULL, 10, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `logs_atividade`
--

CREATE TABLE `logs_atividade` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nome` varchar(100) DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tabela_afetada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs_atividade`
--

INSERT INTO `logs_atividade` (`id`, `usuario_id`, `usuario_nome`, `acao`, `descricao`, `tabela_afetada`, `registro_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 16:04:50'),
(2, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-16 20:33:47'),
(3, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 15:53:02'),
(4, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 16:41:35'),
(5, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 16:48:42'),
(6, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-05 13:44:17'),
(7, 1, 'Administrador OAGB', 'PAGE_CREATE', 'Criou uma nova página institucional: testBast', 'paginas_ordem', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 18:15:27'),
(8, 1, 'Administrador OAGB', 'PAGE_CREATE', 'Criou uma nova página institucional: teste', 'paginas_ordem', 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 18:19:09'),
(9, 1, 'Administrador OAGB', 'PAGE_CREATE', 'Criou uma nova página institucional: urgente', 'paginas_ordem', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 18:58:13'),
(10, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 19:48:49'),
(11, 1, 'Administrador OAGB', 'PAGE_UPDATE', 'Editou a página institucional: urgente', 'paginas_ordem', 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 19:49:49'),
(12, 1, 'Administrador OAGB', 'PAGE_UPDATE', 'Editou a página institucional: testBast', 'paginas_ordem', 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 19:50:22'),
(13, 1, 'Administrador OAGB', 'PAGE_UPDATE', 'Editou a página institucional: teste', 'paginas_ordem', 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 19:50:39'),
(14, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 22:19:37'),
(15, 1, 'Administrador OAGB', 'CONFIG_FINAN_UPDATE', 'Atualizou as configurações financeiras e de gateway.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-06 22:23:08'),
(16, 1, 'Administrador OAGB', 'INTERN_REPORT_VALIDATE', 'Validou relatório ID 4 com estado validado. Notas e Relatório de Firma incluídos.', 'gestao_estagio_relatorios', 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-08 16:26:17'),
(17, 1, 'Administrador OAGB', 'INTERN_LINK_RESPONSE', 'Rejeitou a vinculação do estagiário ID 1.', 'advogados_estagiarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-08 20:06:53'),
(18, 1, 'Administrador OAGB', 'INTERN_LINK_RESPONSE', 'Aceitou a vinculação do estagiário ID 1. Notificações enviadas a ambas as partes.', 'advogados_estagiarios', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-08 20:21:47'),
(19, 1, 'Administrador OAGB', 'INTERN_REPORT_VALIDATE', 'Validou relatório ID 3 com estado validado. Notas e Relatório de Firma incluídos.', 'gestao_estagio_relatorios', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-09 10:58:51'),
(20, 1, 'Administrador OAGB', 'LOGIN', 'Utilizador entrou no sistema.', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 17:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `membros_comissoes_novo`
--

CREATE TABLE `membros_comissoes_novo` (
  `id` int(11) NOT NULL,
  `comissao_id` int(11) NOT NULL,
  `advogado_id` int(11) NOT NULL,
  `cargo` varchar(100) DEFAULT 'Membro',
  `data_entrada` date NOT NULL,
  `data_saida` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membros_orgaos`
--

CREATE TABLE `membros_orgaos` (
  `id` int(11) NOT NULL,
  `orgao_id` int(11) NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `data_inicio_mandato` date NOT NULL,
  `data_fim_mandato` date DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mensagens_contacto`
--

CREATE TABLE `mensagens_contacto` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(300) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `respondida` tinyint(1) DEFAULT 0,
  `data_resposta` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_edicoes`
--

CREATE TABLE `newsletter_edicoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo_json` longtext DEFAULT NULL,
  `editorial` text DEFAULT NULL,
  `noticias_ids` text DEFAULT NULL,
  `anuncios_ids` text DEFAULT NULL,
  `publicidade_html` text DEFAULT NULL,
  `design_template` varchar(50) DEFAULT 'padrao',
  `status` enum('rascunho','aprovado','enviado') DEFAULT 'rascunho',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_edicoes`
--

INSERT INTO `newsletter_edicoes` (`id`, `titulo`, `conteudo_json`, `editorial`, `noticias_ids`, `anuncios_ids`, `publicidade_html`, `design_template`, `status`, `created_at`, `updated_at`) VALUES
(1, 'cC', NULL, '<p>CascCSCCSA</p>', '[\"1\"]', '[\"2\"]', 'AaD', 'padrao', 'rascunho', '2026-05-08 11:36:25', '2026-05-08 11:36:25'),
(2, 'News 2', '[{\"type\":\"editorial\",\"title\":\"Editorial\",\"bg_color\":\"#ffffff\",\"text_color\":\"#333333\",\"content\":\"<p>edit<\\/p>\",\"icon\":\"\",\"link\":\"\",\"link_text\":\"\",\"items\":[],\"image\":\"\"},{\"type\":\"generic\",\"title\":\"bloco 1\",\"bg_color\":\"#ffffff\",\"text_color\":\"#333333\",\"content\":\"<p>safafa<\\/p>\",\"icon\":\"\",\"link\":\"\",\"link_text\":\"\",\"items\":[],\"image\":\"\"},{\"type\":\"site_content\",\"title\":\"O Que H\\u00e1 de Novo?\",\"bg_color\":\"#ffffff\",\"text_color\":\"#333333\",\"content\":\"\",\"icon\":\"\",\"link\":\"\",\"link_text\":\"\",\"items\":[\"noticia:4\",\"legislacao:5\"],\"image\":\"\"},{\"type\":\"generic\",\"title\":\"Novo Bloco\",\"bg_color\":\"#ffffff\",\"text_color\":\"#333333\",\"content\":\"\",\"icon\":\"\",\"link\":\"\",\"link_text\":\"\",\"items\":[],\"image\":\"\"}]', NULL, NULL, NULL, NULL, 'padrao', 'rascunho', '2026-05-08 11:50:20', '2026-05-08 12:32:46');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscricoes`
--

CREATE TABLE `newsletter_subscricoes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `token_confirmacao` varchar(255) DEFAULT NULL,
  `confirmado` tinyint(1) DEFAULT 0,
  `ip_inscricao` varchar(45) DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_confirmacao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Subscrições da newsletter';

--
-- Dumping data for table `newsletter_subscricoes`
--

INSERT INTO `newsletter_subscricoes` (`id`, `email`, `nome`, `ativo`, `token_confirmacao`, `confirmado`, `ip_inscricao`, `data_inscricao`, `data_confirmacao`) VALUES
(1, 'kaounengalissa@gmail.com', '', 1, '0d740a6d0104973f2526f127492b05be3eb7408075b74da22fa6318b07a0c6d6', 1, '::1', '2026-05-09 14:15:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(300) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `resumo` text DEFAULT NULL,
  `conteudo` longtext NOT NULL,
  `conteudo_formatado` longtext DEFAULT NULL,
  `imagem_destaque` varchar(255) DEFAULT NULL,
  `ficheiro_anexo` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `categoria_tipo` enum('Notícia','Anúncio','Aviso','Edital') DEFAULT 'Notícia',
  `tags` text DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `ordem_destaque` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `visualizacoes` int(11) DEFAULT 0,
  `data_publicacao` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'Título para SEO/Facebook',
  `meta_description` text DEFAULT NULL COMMENT 'Descrição para SEO/Facebook',
  `og_image` varchar(255) DEFAULT NULL COMMENT 'Imagem específica para Facebook/Open Graph',
  `canonical_url` varchar(255) DEFAULT NULL COMMENT 'URL canônica do artigo',
  `audio_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de notícias e artigos do site';

--
-- Dumping data for table `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `slug`, `resumo`, `conteudo`, `conteudo_formatado`, `imagem_destaque`, `ficheiro_anexo`, `categoria`, `categoria_tipo`, `tags`, `autor`, `destaque`, `ordem_destaque`, `ativo`, `visualizacoes`, `data_publicacao`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `og_image`, `canonical_url`, `audio_url`, `video_url`) VALUES
(1, 'OAGB publica edital para formação de lista sêxtupla ao TRT-8', 'oagb-publica-edital-trt8', 'O \"FBE International Contract Competition\" organizado pelo \"Federation des Barreaux d\'Europe\" (FBE) conjuntamente com a Ordem dos Advogados.', '<p>A Ordem dos Advogados da Guiné-Bissau publicou recentemente um edital importante para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho...</p>', NULL, 'Asset 7-1001.jpg', NULL, '3º CURSO DE FORMAÇÃO DOS ESTAGIÁRIOS', 'Notícia', NULL, 'OAGB', 1, 0, 1, 9, '2025-06-09 15:31:51', '2025-06-09 15:31:51', '2026-05-03 15:50:17', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Conferência sobre o Papel da Ordem e dos Advogados', 'conferencia-papel-ordem-advogados', 'Importante conferência sobre o papel institucional da Ordem dos Advogados na administração da justiça.', '<p>Realizou-se no passado dia uma importante conferência sobre o papel da Ordem dos Advogados e dos profissionais na administração da justiça...</p>', NULL, 'Asset 7-100.jpg', NULL, 'CONFERÊNCIA', 'Notícia', NULL, 'OAGB', 0, 0, 1, 13, '2025-06-09 15:31:51', '2025-06-09 15:31:51', '2026-05-05 14:43:45', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'OAGB publica edital para formação de lista sêxtupla ao TRT-8', 'oagb-publica-edital-lista-sextupla-trt8', 'A Ordem dos Advogados da Guiné-Bissau publicou edital para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho.', '<p>A Ordem dos Advogados da Guiné-Bissau (OAGB) publicou no dia de hoje edital para a formação de lista sêxtupla destinada ao preenchimento de vaga de Juiz do Tribunal Regional do Trabalho da 8ª Região.</p><p>O edital estabelece os requisitos e procedimentos para a inscrição de advogados interessados em compor a lista que será encaminhada ao Tribunal Superior do Trabalho.</p>', NULL, '', NULL, NULL, 'Notícia', NULL, NULL, 1, 0, 1, 30, '2024-06-24 10:00:00', '2025-06-10 13:30:34', '2026-05-09 12:49:04', 'OAGB publica edital para lista sêxtupla ao TRT-8', 'A Ordem dos Advogados da Guiné-Bissau publicou edital para formação de lista sêxtupla ao Tribunal Regional do Trabalho.', NULL, NULL, NULL, NULL),
(4, 'Competição Internacional de Contratos FBE', 'competicao-internacional-contratos-fbe', 'Jovens advogados participam na competição internacional organizada pela Federação dos Advogados da Europa.', '<p>O \"FBE International Contract Competition\" organizado pelo \"Federation des Barreaux d\'Europe\" (FBE) conjuntamente com a Ordem dos Advogados - através do Instituto de Apoio aos Jovens Advogados (IAJA).</p><p>Esta competição visa promover o conhecimento jurídico e as competências práticas dos jovens advogados na área do direito contratual internacional.</p>', NULL, 'Asset 8-100.jpg', NULL, NULL, 'Notícia', NULL, NULL, 1, 0, 1, 26, '2024-06-20 15:30:00', '2025-06-10 13:30:34', '2026-05-06 19:47:31', 'Competição Internacional de Contratos FBE - OAGB', 'Jovens advogados da OAGB participam na competição internacional da Federação dos Advogados da Europa.', NULL, NULL, NULL, NULL),
(5, 'Nova regulamentação para inscrição de estagiários', 'nova-regulamentacao-inscricao-estagiarios', 'Conselho da OAGB aprova nova regulamentação para o processo de inscrição de advogados estagiários.', '<p>O Conselho da Ordem dos Advogados da Guiné-Bissau aprovou nova regulamentação que simplifica e moderniza o processo de inscrição de advogados estagiários.</p><p>As principais alterações incluem a digitalização do processo, redução de prazos e maior transparência nos critérios de avaliação.</p>', NULL, 'Asset 9-100.jpg', NULL, NULL, 'Notícia', NULL, NULL, 1, 0, 1, 2, '2024-06-18 09:00:00', '2025-06-10 13:30:34', '2026-05-06 20:05:31', 'Nova regulamentação para inscrição de estagiários - OAGB', 'OAGB aprova nova regulamentação que simplifica o processo de inscrição de advogados estagiários.', NULL, NULL, NULL, NULL),
(7, 'Abertura do Ano Judicial 2024', 'abertura-ano-judicial-2024', 'Cerimónia solene de abertura do ano judicial.', '<p>A Ordem dos Advogados informa que a cerimónia de abertura do ano judicial terá lugar no dia...</p>', NULL, '', NULL, NULL, 'Notícia', NULL, NULL, 0, 0, 1, 1, '2024-01-10 09:00:00', '2026-03-24 19:18:51', '2026-05-06 20:04:49', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Comunicado sobre as Custas Judiciais', 'comunicado-custas-judiciais', 'Posição da OAGB sobre a nova tabela de custas.', '<p>O Conselho Diretivo da OAGB vem por este meio comunicar a sua posição oficial sobre...</p>', NULL, '', NULL, NULL, 'Anúncio', NULL, NULL, 0, 0, 1, 1, '2024-02-15 14:30:00', '2026-03-24 19:18:51', '2026-05-06 19:47:23', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `noticias_imagens`
--

CREATE TABLE `noticias_imagens` (
  `id` int(11) NOT NULL,
  `noticia_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Galeria de imagens das notícias';

-- --------------------------------------------------------

--
-- Table structure for table `orgaos_config`
--

CREATE TABLE `orgaos_config` (
  `id` int(11) NOT NULL DEFAULT 1,
  `organograma_path` varchar(255) DEFAULT NULL,
  `organograma_pdf_path` varchar(255) DEFAULT NULL,
  `modo_exibicao` enum('info','imagem','pdf','auto') DEFAULT 'info'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orgaos_config`
--

INSERT INTO `orgaos_config` (`id`, `organograma_path`, `organograma_pdf_path`, `modo_exibicao`) VALUES
(1, NULL, NULL, 'info');

-- --------------------------------------------------------

--
-- Table structure for table `orgaos_diretivos`
--

CREATE TABLE `orgaos_diretivos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orgaos_diretivos`
--

INSERT INTO `orgaos_diretivos` (`id`, `nome`, `descricao`, `created_at`) VALUES
(1, 'Conselho Nacional', 'Órgão executivo superior', '2026-03-24 23:03:57'),
(2, 'Conselho Regional', 'Representação regional', '2026-03-24 23:03:57'),
(3, 'Conselho Fiscal', 'Fiscalização de contas', '2026-03-24 23:03:57'),
(4, 'Conselho de Jurisdição', 'Assuntos disciplinares e jurídicos', '2026-03-24 23:03:57'),
(5, 'Congresso dos Advogados', '?rg?o deliberativo supremo da OAGB (Art. 12.??17.? dos Estatutos 2018). Re?ne ordinariamente de 3 em 3 anos.', '2026-05-05 11:58:20'),
(6, 'Tribunal de Ética e Disciplina', '?rg?o jurisdicional respons?vel pela aprecia??o de processos disciplinares (Art. 35.??42.?).', '2026-05-05 11:58:20'),
(7, 'Conselho de Deontologia e Ética', '?rg?o consultivo que vela pela observ?ncia das regras deontol?gicas (Art. 43.??48.?).', '2026-05-05 11:58:20'),
(8, 'Assembleia Geral', '?rg?o deliberativo dos advogados inscritos, re?ne anualmente para aprovar relat?rios e contas.', '2026-05-05 11:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `orgaos_sociais`
--

CREATE TABLE `orgaos_sociais` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `mandato_inicio` date DEFAULT NULL,
  `mandato_fim` date DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `orgao_diretivo_id` int(11) NOT NULL DEFAULT 1,
  `superior_id` int(11) DEFAULT NULL,
  `assinatura` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orgaos_sociais`
--

INSERT INTO `orgaos_sociais` (`id`, `nome`, `cargo`, `mandato_inicio`, `mandato_fim`, `foto`, `biografia`, `ordem_exibicao`, `ativo`, `created_at`, `updated_at`, `orgao_diretivo_id`, `superior_id`, `assinatura`) VALUES
(1, 'Dr. Januário Pedro Correia', 'Bastonário / Presidente', NULL, NULL, NULL, 'Professor Doutor em Direito. Bastonário da OAGB desde 2019. Especialista em Direito Constitucional e docente universitário. Liderou a reforma dos Estatutos de 2018.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 1, NULL, NULL),
(2, 'Dr. Serifo Jaló', 'Vice-Presidente', NULL, NULL, NULL, 'Advogado com vasta experiência em Direito Comercial e Societário. Vice-Presidente do Conselho Nacional desde 2019.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 1, 1, NULL),
(3, 'Dra. Maria da Conceição', 'Tesoureira', NULL, NULL, NULL, 'Advogada especializada em Direito da Família e Sucessões. Responsável pela gestão financeira da Ordem.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 1, 1, NULL),
(4, 'Dr. Nelson Morgado', 'Vogal', NULL, NULL, NULL, 'Advogado com prática consolidada em contencioso cível. Membro ativo em comissões de reforma legislativa.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 1, 1, NULL),
(5, 'Dr. Exemplo Nome', 'Membro Representante', NULL, NULL, NULL, 'Representante regional com experiência em Direito Penal e assessoria jurídica comunitária.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 2, 2, NULL),
(6, 'Dr. Exemplo Nome', 'Membro Representante', NULL, NULL, NULL, 'Membro do Conselho Fiscal com formação em auditoria jurídica e controlo financeiro institucional.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 3, 2, NULL),
(7, 'Dr. Exemplo Nome', 'Membro Representante', NULL, NULL, NULL, 'Jurista com experiência em Direito Processual Civil. Membro do Conselho de Jurisdição.', 0, 1, '2026-04-05 17:17:59', '2026-05-05 12:33:57', 4, 2, NULL),
(8, 'Prof. Dr. Januário Pedro Correia', 'Presidente do Congresso', '2022-01-01', NULL, NULL, 'Presidente do Congresso. Professor Catedrático e autor de diversas publicações sobre Direito Constitucional guineense.', 1, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 5, NULL, NULL),
(9, 'Dr.ª Mariana Gomes Pereira', 'Vice-Presidente', '2022-01-01', NULL, NULL, 'Vice-Presidente do Congresso. Advogada com prática em Direito do Trabalho e consultoria a organizações internacionais.', 2, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 5, NULL, NULL),
(10, 'Dr. Fausto Dias Sanhá', 'Secretário', '2022-01-01', NULL, NULL, 'Secretário do Congresso. Advogado com experiência em Direito Administrativo e contratação pública.', 3, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 5, NULL, NULL),
(11, 'Dr. Augusto Soares da Costa', 'Presidente', '2022-01-01', NULL, NULL, 'Presidente do Tribunal de Ética. Advogado sénior com mais de 20 anos de exercício e reputação ilibada.', 1, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 6, NULL, NULL),
(12, 'Dr.ª Iracema Cumba Vieira', 'Juiz Efetivo', '2022-01-01', NULL, NULL, 'Juiz Efetivo do Tribunal de Ética. Especialista em Direito Deontológico e formação de estagiários.', 2, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 6, NULL, NULL),
(13, 'Dr. Mamadú Baldé', 'Juiz Efetivo', '2022-01-01', NULL, NULL, 'Juiz Efetivo. Advogado com experiência em mediação de conflitos e arbitragem.', 3, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 6, NULL, NULL),
(14, 'Dr.ª Ana Luísa Ndjai', 'Juiz Suplente', '2022-01-01', NULL, NULL, 'Juiz Suplente. Jovem advogada com formação complementar em Direitos Humanos pela CPLP.', 4, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 6, NULL, NULL),
(15, 'Dr. Caetano Natcham Intchama', 'Presidente', '2022-01-01', NULL, NULL, 'Presidente do Conselho de Deontologia. Advogado de referência em ética profissional e formação contínua.', 1, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 7, NULL, NULL),
(16, 'Dr.ª Domingas Mendes da Silva', 'Vogal', '2022-01-01', NULL, NULL, 'Vogal do Conselho de Deontologia. Advogada com prática em Direito da Família e proteção de menores.', 2, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 7, NULL, NULL),
(17, 'Dr. Seco Cassamá', 'Vogal', '2022-01-01', NULL, NULL, 'Vogal. Advogado com experiência em litígios comerciais e Direito OHADA.', 3, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 7, NULL, NULL),
(18, 'Dr. Fernando Lopes Correia', 'Vogal', '2022-01-01', NULL, NULL, 'Vogal. Advogado com formação em Direito Internacional Público e experiência em cooperação jurídica.', 4, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 7, NULL, NULL),
(19, 'Dr.ª Raquel Lopes Semedo', 'Presidente da Mesa', '2022-01-01', NULL, NULL, 'Presidente da Mesa da Assembleia Geral. Advogada com larga experiência em governação associativa.', 1, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 8, NULL, NULL),
(20, 'Dr. Abdulai Djaló', 'Vice-Presidente da Mesa', '2022-01-01', NULL, NULL, 'Vice-Presidente da Mesa. Advogado com prática em Direito Fiscal e consultoria empresarial.', 2, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 8, NULL, NULL),
(21, 'Dr.ª Luísa Nancassa Có', 'Secretária', '2022-01-01', NULL, NULL, 'Secretária da Assembleia. Advogada com experiência em Direito Civil e redação de atos normativos.', 3, 1, '2026-05-05 11:59:10', '2026-05-05 12:33:57', 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `paginas_ordem`
--

CREATE TABLE `paginas_ordem` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `conteudo` longtext DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `ordem_exibicao` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exibir_menu` tinyint(1) DEFAULT 0,
  `menu_categoria` varchar(50) DEFAULT 'NENHUM',
  `ordem_menu` int(11) DEFAULT 0,
  `layout_tipo` varchar(20) DEFAULT '2col_right',
  `imagem_posicao` varchar(20) DEFAULT 'topo',
  `mostrar_sidebar` tinyint(1) DEFAULT 1,
  `mostrar_botoes` tinyint(1) DEFAULT 0,
  `card_bg` tinyint(1) DEFAULT 1,
  `parallax` tinyint(1) DEFAULT 0,
  `titulo_cor` varchar(20) DEFAULT '#4D1C21',
  `titulo_tamanho` varchar(20) DEFAULT '2.5rem',
  `texto_cor` varchar(20) DEFAULT '#444444',
  `texto_tamanho` varchar(20) DEFAULT '1rem',
  `fonte_familia` varchar(50) DEFAULT 'Open Sans',
  `imagem_card` varchar(255) DEFAULT NULL,
  `sidebar_conteudo` text DEFAULT NULL,
  `botao1_texto` varchar(100) DEFAULT NULL,
  `botao1_link` varchar(255) DEFAULT NULL,
  `botao2_texto` varchar(100) DEFAULT NULL,
  `botao2_link` varchar(255) DEFAULT NULL,
  `botao1_file` varchar(255) DEFAULT NULL,
  `botao2_file` varchar(255) DEFAULT NULL,
  `sidebar_widget` varchar(50) DEFAULT 'default',
  `sidebar_menu_categoria` varchar(50) DEFAULT NULL,
  `sidebar_titulo` varchar(100) DEFAULT NULL,
  `sidebar_icon` varchar(50) DEFAULT 'fas fa-info-circle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paginas_ordem`
--

INSERT INTO `paginas_ordem` (`id`, `titulo`, `slug`, `conteudo`, `imagem`, `ativo`, `ordem_exibicao`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`, `exibir_menu`, `menu_categoria`, `ordem_menu`, `layout_tipo`, `imagem_posicao`, `mostrar_sidebar`, `mostrar_botoes`, `card_bg`, `parallax`, `titulo_cor`, `titulo_tamanho`, `texto_cor`, `texto_tamanho`, `fonte_familia`, `imagem_card`, `sidebar_conteudo`, `botao1_texto`, `botao1_link`, `botao2_texto`, `botao2_link`, `botao1_file`, `botao2_file`, `sidebar_widget`, `sidebar_menu_categoria`, `sidebar_titulo`, `sidebar_icon`) VALUES
(1, 'Apresentação e História', 'apresentacao-historia', '<p>A Ordem dos Advogados da Guiné-Bissau (OAGB) foi instituída pela <strong>Lei nº 4/91, de 3 de Outubro</strong>, como uma associação pública representativa dos licenciados em Direito que, em conformidade com os preceitos dos Estatutos e demais disposições legais aplicáveis, exercem a profissão de advogado.</p>\n\n<p>A OAGB goza de <strong>personalidade jurídica</strong> e é independente dos órgãos do Estado no que respeita à sua organização e funcionamento. A Ordem tem a sua sede em Bissau e pode criar delegações regionais em todo o território nacional, conforme deliberação da Assembleia Geral.</p>\n\n<h5 class=\"mt-4 mb-3\" style=\"color: var(--primary-maroon); font-family: Libre Baskerville, serif;\">Atribuições Fundamentais</h5>\n\n<ul style=\"list-style: none; padding-left: 0;\">\n<li style=\"padding: 8px 0; border-bottom: 1px solid rgba(177,162,118,0.1);\"><i class=\"fas fa-gavel me-2\" style=\"color: var(--primary-gold);\"></i> Defender os direitos, liberdades e garantias individuais</li>\n<li style=\"padding: 8px 0; border-bottom: 1px solid rgba(177,162,118,0.1);\"><i class=\"fas fa-balance-scale me-2\" style=\"color: var(--primary-gold);\"></i> Promover a boa administração da justiça e o aperfeiçoamento da cultura jurídica</li>\n<li style=\"padding: 8px 0; border-bottom: 1px solid rgba(177,162,118,0.1);\"><i class=\"fas fa-university me-2\" style=\"color: var(--primary-gold);\"></i> Colaborar na administração da justiça e defender a legalidade democrática</li>\n<li style=\"padding: 8px 0; border-bottom: 1px solid rgba(177,162,118,0.1);\"><i class=\"fas fa-users me-2\" style=\"color: var(--primary-gold);\"></i> Conferir, verificar e fazer respeitar as condições de exercício da profissão</li>\n<li style=\"padding: 8px 0;\"><i class=\"fas fa-handshake me-2\" style=\"color: var(--primary-gold);\"></i> Exercer o poder disciplinar sobre os seus membros</li>\n</ul>\n\n<h5 class=\"mt-4 mb-3\" style=\"color: var(--primary-maroon); font-family: Libre Baskerville, serif;\">Estrutura Orgânica</h5>\n\n<p>A OAGB é composta pelos seguintes órgãos: a <strong>Assembleia Geral</strong>, o <strong>Conselho da Ordem</strong> (Bastonário, Vice-Bastonário e Conselheiros), o <strong>Conselho de Jurisdição</strong> e o <strong>Conselho Fiscal</strong>. Cada órgão desempenha funções específicas na governação, disciplina e fiscalização da atividade da Ordem.</p>\n\n<p><a href=\"orgaos-sociais.php\" style=\"color: var(--primary-maroon); font-weight: 600; text-decoration: none; border-bottom: 2px solid var(--primary-gold); padding-bottom: 2px;\"><i class=\"fas fa-sitemap me-2\"></i>Conhecer os Órgãos Sociais da OAGB</a></p>', NULL, 1, 1, NULL, NULL, NULL, '2025-06-03 16:30:53', '2026-03-27 18:00:34', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(2, 'Órgãos Sociais', 'orgaos-sociais', '<p>Os órgãos sociais da OAGB são constituídos por...</p>', NULL, 1, 2, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(3, 'Comissões Especializadas', 'comissoes-especializadas', '<p>As comissões especializadas desenvolvem trabalhos específicos...</p>', NULL, 1, 3, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(4, 'Cooperação Institucional', 'cooperacao-institucional', '<p>A cooperação institucional da OAGB...</p>', NULL, 1, 4, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(5, 'Historia', 'historia', 'A Ordem dos Advogados da Guiné-Bissau foi criada para regular a profissão e defender os interesses dos seus membros e da sociedade.', NULL, 1, 0, NULL, NULL, NULL, '2026-03-24 19:09:11', '2026-03-24 19:18:51', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(6, 'Apresentacao', 'apresentacao', 'Conteúdo a ser definido...', NULL, 1, 0, NULL, NULL, NULL, '2026-03-24 19:09:13', '2026-03-24 19:09:13', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4D1C21', '2.5rem', '#444444', '1rem', 'Open Sans', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'default', NULL, NULL, 'fas fa-info-circle'),
(8, 'testBast', 'testbast', '<p>ascafafa</p>', NULL, 0, 0, NULL, NULL, NULL, '2026-05-05 18:15:27', '2026-05-06 19:50:22', 0, 'ORDEM', 3, '1col', 'meio', 0, 1, 0, 0, '#111923', '3rem', '#222222', '1.1rem', '\'Libre Baskerville\', serif', NULL, '', '', '', '', '', NULL, NULL, 'default', '', NULL, NULL),
(9, 'teste', 'teste', '<p>igfghkjk</p>', 'page_1778005149.jpg', 0, 4, NULL, NULL, NULL, '2026-05-05 18:19:09', '2026-05-06 19:50:39', 0, 'ORDEM', 4, '2col_right', 'topo', 1, 0, 1, 0, '#4d1c21', '2.5rem', '#444444', '1.05rem', '\'Open Sans\', sans-serif', NULL, '', '', '', '', '', NULL, NULL, 'default', '', NULL, NULL),
(10, 'urgente', 'urgente', '<p>jhv</p>', '', 0, 0, NULL, NULL, NULL, '2026-05-05 18:58:13', '2026-05-06 19:49:49', 0, 'NENHUM', 0, '2col_right', 'topo', 1, 0, 1, 0, '#4d1c21', '2.5rem', '#444444', '1.05rem', '\'Open Sans\', sans-serif', '', '<p>Teste<br>rest<br>&nbsp;</p>', '', '', '', '', '', '', 'default', 'ADVOGADOS', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parcerias_internacionais`
--

CREATE TABLE `parcerias_internacionais` (
  `id` int(11) NOT NULL,
  `entidade_parceira` varchar(150) NOT NULL,
  `pais` varchar(100) NOT NULL,
  `tipo_acordo` varchar(100) DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `data_assinatura` date DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `documento_url` varchar(255) DEFAULT NULL,
  `status` enum('Ativo','Expirado','Em Renovação') DEFAULT 'Ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parcerias_internacionais`
--

INSERT INTO `parcerias_internacionais` (`id`, `entidade_parceira`, `pais`, `tipo_acordo`, `objetivo`, `data_assinatura`, `data_validade`, `documento_url`, `status`, `created_at`) VALUES
(1, 'hs', 'finlandia', 'efef', '<p>sasv</p>', NULL, NULL, '', 'Ativo', '2026-05-02 15:58:06');

-- --------------------------------------------------------

--
-- Table structure for table `pareceres_deliberacoes`
--

CREATE TABLE `pareceres_deliberacoes` (
  `id` int(11) NOT NULL,
  `tipo` enum('parecer','deliberacao','comunicado','anuncio','edital') NOT NULL,
  `numero` varchar(50) NOT NULL,
  `assunto` varchar(300) NOT NULL,
  `relator` varchar(255) DEFAULT NULL,
  `resumo` text DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_emissao` date NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pareceres e Deliberações da OAGB';

--
-- Dumping data for table `pareceres_deliberacoes`
--

INSERT INTO `pareceres_deliberacoes` (`id`, `tipo`, `numero`, `assunto`, `relator`, `resumo`, `conteudo`, `arquivo_pdf`, `imagem`, `data_emissao`, `link_url`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'deliberacao', 'CNEF n.º 8/2023', 'Deliberação sobre Código de Ética', NULL, 'Deliberação do Conselho Nacional de Ética e Formação sobre alterações ao Código de Ética dos Advogados', NULL, NULL, NULL, '2023-12-15', 'pareceres-deliberacoes.php?id=1', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57'),
(2, 'parecer', 'Parecer n.º 12/2023', 'Parecer sobre Lei de Honorários', NULL, 'Parecer técnico sobre a proposta de lei de honorários advocatícios', NULL, NULL, NULL, '2023-11-20', 'pareceres-deliberacoes.php?id=2', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57'),
(3, 'deliberacao', 'CNEF n.º 7/2023', 'Deliberação sobre Formação Contínua', NULL, 'Novas regras para formação contínua obrigatória dos advogados', NULL, NULL, NULL, '2023-10-10', 'pareceres-deliberacoes.php?id=3', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revistas_oagb`
--

CREATE TABLE `revistas_oagb` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `edicao` varchar(50) NOT NULL,
  `ano` int(4) NOT NULL,
  `data_publicacao` date NOT NULL,
  `descricao` text DEFAULT NULL,
  `capa_imagem` varchar(255) DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revistas_oagb`
--

INSERT INTO `revistas_oagb` (`id`, `titulo`, `edicao`, `ano`, `data_publicacao`, `descricao`, `capa_imagem`, `arquivo_pdf`, `destaque`, `status`, `created_at`) VALUES
(1, 'O Papel da Justiça na Consolidação do Estado de Direito', 'Nº 12', 2024, '2024-06-01', 'Análise do papel dos advogados na construção democrática da Guiné-Bissau.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(2, 'Advocacia e os Desafios da OHADA na Guiné-Bissau', 'Nº 11', 2023, '2023-10-15', 'Edição especial sobre a harmonização do direito comercial e o impacto dos Atos Uniformes.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(3, 'Direitos Humanos e Advocacia na África Ocidental', 'Nº 10', 2023, '2023-05-01', 'A Carta de Banjul e a defesa dos direitos humanos pelo advogado guineense.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(4, 'Os Novos Estatutos da OAGB — Análise e Comentários', 'Nº 9', 2022, '2022-12-01', 'Análise dos 219 artigos dos Estatutos de 2018 e o seu impacto na profissão.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(5, 'A Independência da Advocacia — 30 Anos de OAGB', 'Nº 8', 2022, '2022-06-15', 'Edição comemorativa dos 30 anos da constituição da Ordem.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41'),
(6, 'Estágio Profissional e Formação Contínua', 'Nº 7', 2021, '2021-11-01', 'Orientações para os cursos de formação de estagiários.', NULL, NULL, 0, 'ativo', '2026-05-05 10:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `solicitacoes_advogados`
--

CREATE TABLE `solicitacoes_advogados` (
  `id` int(11) NOT NULL,
  `nome_solicitante` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `area_juridica` varchar(100) DEFAULT NULL,
  `regiao_preferencia` varchar(50) DEFAULT NULL,
  `descricao_caso` text DEFAULT NULL,
  `documentos_anexos` text DEFAULT NULL,
  `urgencia` enum('baixa','media','alta') DEFAULT 'media',
  `status` enum('pendente','atribuido','concluido','cancelado') DEFAULT 'pendente',
  `advogado_atribuido_id` int(11) DEFAULT NULL,
  `data_atribuicao` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscricoes`
--

CREATE TABLE `subscricoes` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timeline_marcos`
--

CREATE TABLE `timeline_marcos` (
  `id` int(11) NOT NULL,
  `ano` varchar(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeline_marcos`
--

INSERT INTO `timeline_marcos` (`id`, `ano`, `titulo`, `descricao`, `imagem`, `ordem`, `created_at`) VALUES
(2, '1991', 'Fundação da OAGB', '<p>Criação oficial da Ordem dos Advogados da Guiné-Bissau pela Lei nº 4/91, de 3 de Outubro, como associação pública representativa dos advogados.</p>', NULL, 0, '2026-03-26 23:43:18'),
(3, '1992', 'Aprovação dos Estatutos', 'Aprovação dos primeiros Estatutos da OAGB em Assembleia Geral Constituinte, definindo a estrutura orgânica e os princípios deontológicos da profissão.', NULL, 0, '2026-03-26 23:43:18'),
(4, '1998', 'Resiliência Institucional', 'Manutenção da atividade da Ordem durante o conflito político-militar, assegurando o mínimo de funcionamento dos serviços jurídicos ao cidadão.', NULL, 0, '2026-03-26 23:43:18'),
(5, '2004', 'Consolidação Democrática', 'Participação ativa da OAGB nos processos de reconciliação nacional e no reforço das instituições democráticas do país.', NULL, 0, '2026-03-26 23:43:18'),
(6, '2010', 'Reforma dos Estatutos', 'Revisão profunda dos Estatutos para adequação às novas realidades jurídicas e sociais, reforçando os mecanismos disciplinares e de formação contínua.', NULL, 0, '2026-03-26 23:43:18'),
(7, '2015', 'Cooperação Internacional', 'Estabelecimento de protocolos de cooperação com Ordens de Advogados de países lusófonos, nomeadamente Portugal, Brasil, Cabo Verde e Moçambique.', NULL, 0, '2026-03-26 23:43:18'),
(8, '2020', 'Modernização Digital', 'Início do processo de digitalização dos serviços da Ordem, incluindo a gestão de inscrições e a comunicação institucional através de plataformas eletrónicas.', NULL, 0, '2026-03-26 23:43:18'),
(9, '2024', 'Nova Era Institucional', 'Lançamento do portal digital da OAGB com funcionalidades de gestão online, marcando o compromisso com a transparência e a proximidade aos advogados e cidadãos.', NULL, 0, '2026-03-26 23:43:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `advogados`
--
ALTER TABLE `advogados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_registo` (`numero_registo`),
  ADD KEY `idx_regiao` (`regiao`),
  ADD KEY `idx_nome` (`nome_completo`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ordem` (`ordem_exibicao`);

--
-- Indexes for table `advogados_estagiarios`
--
ALTER TABLE `advogados_estagiarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_registo` (`numero_registo`),
  ADD KEY `orientador_id` (`orientador_id`),
  ADD KEY `idx_regiao` (`regiao`),
  ADD KEY `idx_nome` (`nome_completo`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `data_evento` (`data_evento`),
  ADD KEY `destaque` (`destaque`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `idx_visualizacoes` (`visualizacoes`);
ALTER TABLE `agenda` ADD FULLTEXT KEY `ft_titulo_desc` (`titulo`,`descricao`);

--
-- Indexes for table `agenda_imagens`
--
ALTER TABLE `agenda_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agenda_id` (`agenda_id`),
  ADD KEY `idx_ordem` (`ordem_exibicao`);

--
-- Indexes for table `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_datas` (`data_inicio`,`data_fim`),
  ADD KEY `idx_ordem` (`ordem_exibicao`);

--
-- Indexes for table `avaliacoes_estagio`
--
ALTER TABLE `avaliacoes_estagio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estagio_id` (`estagio_id`),
  ADD KEY `avaliador_id` (`avaliador_id`);

--
-- Indexes for table `bastonarios`
--
ALTER TABLE `bastonarios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biblioteca_oagb`
--
ALTER TABLE `biblioteca_oagb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ordem` (`ordem_exibicao`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Indexes for table `comissoes`
--
ALTER TABLE `comissoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comissoes_especializadas`
--
ALTER TABLE `comissoes_especializadas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `configuracoes_site`
--
ALTER TABLE `configuracoes_site`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Indexes for table `conteudos_paginas`
--
ALTER TABLE `conteudos_paginas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departamentos_contactos`
--
ALTER TABLE `departamentos_contactos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documentos_publicos`
--
ALTER TABLE `documentos_publicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_documento`),
  ADD KEY `idx_numero` (`numero_documento`);

--
-- Indexes for table `estagios_processos`
--
ALTER TABLE `estagios_processos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estagiario_id` (`estagiario_id`),
  ADD KEY `patrono_id` (`patrono_id`);

--
-- Indexes for table `estatisticas_visualizacao`
--
ALTER TABLE `estatisticas_visualizacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conteudo` (`tipo_conteudo`,`conteudo_id`),
  ADD KEY `idx_data` (`data_visualizacao`);

--
-- Indexes for table `estatutos_artigos`
--
ALTER TABLE `estatutos_artigos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_numero` (`numero_artigo`),
  ADD KEY `idx_tema` (`tema`),
  ADD KEY `idx_capitulo` (`capitulo`),
  ADD KEY `idx_ativo` (`ativo`);
ALTER TABLE `estatutos_artigos` ADD FULLTEXT KEY `idx_fulltext` (`titulo_artigo`,`conteudo`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_ordem` (`ordem_exibicao`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Indexes for table `ficheiros_anexos`
--
ALTER TABLE `ficheiros_anexos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entidade` (`tipo_entidade`,`entidade_id`),
  ADD KEY `idx_downloads` (`downloads`);

--
-- Indexes for table `finan_config`
--
ALTER TABLE `finan_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Indexes for table `finan_pagamentos`
--
ALTER TABLE `finan_pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advogado_id` (`advogado_id`),
  ADD KEY `tipo_pagamento_id` (`tipo_pagamento_id`);

--
-- Indexes for table `finan_tipos_pagamento`
--
ALTER TABLE `finan_tipos_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_actas`
--
ALTER TABLE `gestao_actas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_biblioteca`
--
ALTER TABLE `gestao_biblioteca`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_comissoes`
--
ALTER TABLE `gestao_comissoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_comissoes_membros`
--
ALTER TABLE `gestao_comissoes_membros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comissao_id` (`comissao_id`);

--
-- Indexes for table `gestao_configuracoes`
--
ALTER TABLE `gestao_configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `gestao_cursos`
--
ALTER TABLE `gestao_cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_cursos_inscritos`
--
ALTER TABLE `gestao_cursos_inscritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indexes for table `gestao_disciplinar_processos`
--
ALTER TABLE `gestao_disciplinar_processos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_processo` (`numero_processo`);

--
-- Indexes for table `gestao_eleicoes`
--
ALTER TABLE `gestao_eleicoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_estagio_interacoes`
--
ALTER TABLE `gestao_estagio_interacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_estagio_relatorios`
--
ALTER TABLE `gestao_estagio_relatorios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_notificacoes`
--
ALTER TABLE `gestao_notificacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_opcoes`
--
ALTER TABLE `gestao_opcoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_sociedades`
--
ALTER TABLE `gestao_sociedades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gestao_votos`
--
ALTER TABLE `gestao_votos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`eleicao_id`,`advogado_id`);

--
-- Indexes for table `glossario_juridico`
--
ALTER TABLE `glossario_juridico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_letra` (`letra`);

--
-- Indexes for table `info_cidadaos`
--
ALTER TABLE `info_cidadaos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inscricoes_ordem`
--
ALTER TABLE `inscricoes_ordem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tipo` (`tipo_inscricao`);

--
-- Indexes for table `instituicao_info`
--
ALTER TABLE `instituicao_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `legislacao_internacional`
--
ALTER TABLE `legislacao_internacional`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `legislacao_nacional`
--
ALTER TABLE `legislacao_nacional`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_atividade`
--
ALTER TABLE `logs_atividade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_acao` (`acao`),
  ADD KEY `idx_data` (`created_at`);

--
-- Indexes for table `membros_comissoes_novo`
--
ALTER TABLE `membros_comissoes_novo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comissao_id` (`comissao_id`),
  ADD KEY `advogado_id` (`advogado_id`);

--
-- Indexes for table `membros_orgaos`
--
ALTER TABLE `membros_orgaos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orgao_id` (`orgao_id`);

--
-- Indexes for table `mensagens_contacto`
--
ALTER TABLE `mensagens_contacto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lida` (`lida`);

--
-- Indexes for table `newsletter_edicoes`
--
ALTER TABLE `newsletter_edicoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscricoes`
--
ALTER TABLE `newsletter_subscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `confirmado` (`confirmado`);

--
-- Indexes for table `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_data` (`data_publicacao`),
  ADD KEY `idx_destaque` (`destaque`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_data_publicacao` (`data_publicacao`),
  ADD KEY `idx_destaque_ativo` (`destaque`,`ativo`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_ordem_destaque` (`ordem_destaque`);
ALTER TABLE `noticias` ADD FULLTEXT KEY `ft_titulo` (`titulo`);
ALTER TABLE `noticias` ADD FULLTEXT KEY `ft_resumo` (`resumo`);
ALTER TABLE `noticias` ADD FULLTEXT KEY `ft_conteudo` (`conteudo`);

--
-- Indexes for table `noticias_imagens`
--
ALTER TABLE `noticias_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `noticia_id` (`noticia_id`),
  ADD KEY `idx_ordem` (`ordem_exibicao`);

--
-- Indexes for table `orgaos_config`
--
ALTER TABLE `orgaos_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orgaos_diretivos`
--
ALTER TABLE `orgaos_diretivos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orgaos_sociais`
--
ALTER TABLE `orgaos_sociais`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paginas_ordem`
--
ALTER TABLE `paginas_ordem`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `parcerias_internacionais`
--
ALTER TABLE `parcerias_internacionais`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pareceres_deliberacoes`
--
ALTER TABLE `pareceres_deliberacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_emissao`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `revistas_oagb`
--
ALTER TABLE `revistas_oagb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `solicitacoes_advogados`
--
ALTER TABLE `solicitacoes_advogados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advogado_atribuido_id` (`advogado_atribuido_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_regiao` (`regiao_preferencia`);

--
-- Indexes for table `subscricoes`
--
ALTER TABLE `subscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `timeline_marcos`
--
ALTER TABLE `timeline_marcos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `advogados`
--
ALTER TABLE `advogados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `advogados_estagiarios`
--
ALTER TABLE `advogados_estagiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `agenda_imagens`
--
ALTER TABLE `agenda_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `avaliacoes_estagio`
--
ALTER TABLE `avaliacoes_estagio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bastonarios`
--
ALTER TABLE `bastonarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `biblioteca_oagb`
--
ALTER TABLE `biblioteca_oagb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comissoes`
--
ALTER TABLE `comissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comissoes_especializadas`
--
ALTER TABLE `comissoes_especializadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configuracoes_site`
--
ALTER TABLE `configuracoes_site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `conteudos_paginas`
--
ALTER TABLE `conteudos_paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `departamentos_contactos`
--
ALTER TABLE `departamentos_contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `documentos_publicos`
--
ALTER TABLE `documentos_publicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `estagios_processos`
--
ALTER TABLE `estagios_processos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estatisticas_visualizacao`
--
ALTER TABLE `estatisticas_visualizacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estatutos_artigos`
--
ALTER TABLE `estatutos_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ficheiros_anexos`
--
ALTER TABLE `ficheiros_anexos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finan_config`
--
ALTER TABLE `finan_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `finan_pagamentos`
--
ALTER TABLE `finan_pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `finan_tipos_pagamento`
--
ALTER TABLE `finan_tipos_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gestao_actas`
--
ALTER TABLE `gestao_actas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_biblioteca`
--
ALTER TABLE `gestao_biblioteca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_comissoes`
--
ALTER TABLE `gestao_comissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_comissoes_membros`
--
ALTER TABLE `gestao_comissoes_membros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_configuracoes`
--
ALTER TABLE `gestao_configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gestao_cursos`
--
ALTER TABLE `gestao_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gestao_cursos_inscritos`
--
ALTER TABLE `gestao_cursos_inscritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gestao_disciplinar_processos`
--
ALTER TABLE `gestao_disciplinar_processos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_eleicoes`
--
ALTER TABLE `gestao_eleicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_estagio_interacoes`
--
ALTER TABLE `gestao_estagio_interacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_estagio_relatorios`
--
ALTER TABLE `gestao_estagio_relatorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gestao_notificacoes`
--
ALTER TABLE `gestao_notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gestao_opcoes`
--
ALTER TABLE `gestao_opcoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_sociedades`
--
ALTER TABLE `gestao_sociedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gestao_votos`
--
ALTER TABLE `gestao_votos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glossario_juridico`
--
ALTER TABLE `glossario_juridico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `info_cidadaos`
--
ALTER TABLE `info_cidadaos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inscricoes_ordem`
--
ALTER TABLE `inscricoes_ordem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instituicao_info`
--
ALTER TABLE `instituicao_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `legislacao_internacional`
--
ALTER TABLE `legislacao_internacional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `legislacao_nacional`
--
ALTER TABLE `legislacao_nacional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `logs_atividade`
--
ALTER TABLE `logs_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `membros_comissoes_novo`
--
ALTER TABLE `membros_comissoes_novo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membros_orgaos`
--
ALTER TABLE `membros_orgaos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensagens_contacto`
--
ALTER TABLE `mensagens_contacto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_edicoes`
--
ALTER TABLE `newsletter_edicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `newsletter_subscricoes`
--
ALTER TABLE `newsletter_subscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `noticias_imagens`
--
ALTER TABLE `noticias_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orgaos_diretivos`
--
ALTER TABLE `orgaos_diretivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orgaos_sociais`
--
ALTER TABLE `orgaos_sociais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `paginas_ordem`
--
ALTER TABLE `paginas_ordem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `parcerias_internacionais`
--
ALTER TABLE `parcerias_internacionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pareceres_deliberacoes`
--
ALTER TABLE `pareceres_deliberacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revistas_oagb`
--
ALTER TABLE `revistas_oagb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `solicitacoes_advogados`
--
ALTER TABLE `solicitacoes_advogados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscricoes`
--
ALTER TABLE `subscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeline_marcos`
--
ALTER TABLE `timeline_marcos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advogados_estagiarios`
--
ALTER TABLE `advogados_estagiarios`
  ADD CONSTRAINT `advogados_estagiarios_ibfk_1` FOREIGN KEY (`orientador_id`) REFERENCES `advogados` (`id`);

--
-- Constraints for table `agenda_imagens`
--
ALTER TABLE `agenda_imagens`
  ADD CONSTRAINT `agenda_imagens_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `avaliacoes_estagio`
--
ALTER TABLE `avaliacoes_estagio`
  ADD CONSTRAINT `avaliacoes_estagio_ibfk_1` FOREIGN KEY (`estagio_id`) REFERENCES `estagios_processos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_estagio_ibfk_2` FOREIGN KEY (`avaliador_id`) REFERENCES `advogados` (`id`);

--
-- Constraints for table `estagios_processos`
--
ALTER TABLE `estagios_processos`
  ADD CONSTRAINT `estagios_processos_ibfk_1` FOREIGN KEY (`estagiario_id`) REFERENCES `advogados_estagiarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estagios_processos_ibfk_2` FOREIGN KEY (`patrono_id`) REFERENCES `advogados` (`id`);

--
-- Constraints for table `finan_pagamentos`
--
ALTER TABLE `finan_pagamentos`
  ADD CONSTRAINT `finan_pagamentos_ibfk_1` FOREIGN KEY (`advogado_id`) REFERENCES `advogados` (`id`),
  ADD CONSTRAINT `finan_pagamentos_ibfk_2` FOREIGN KEY (`tipo_pagamento_id`) REFERENCES `finan_tipos_pagamento` (`id`);

--
-- Constraints for table `gestao_comissoes_membros`
--
ALTER TABLE `gestao_comissoes_membros`
  ADD CONSTRAINT `gestao_comissoes_membros_ibfk_1` FOREIGN KEY (`comissao_id`) REFERENCES `gestao_comissoes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gestao_cursos_inscritos`
--
ALTER TABLE `gestao_cursos_inscritos`
  ADD CONSTRAINT `gestao_cursos_inscritos_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `gestao_cursos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `membros_comissoes_novo`
--
ALTER TABLE `membros_comissoes_novo`
  ADD CONSTRAINT `membros_comissoes_novo_ibfk_1` FOREIGN KEY (`comissao_id`) REFERENCES `comissoes_especializadas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membros_comissoes_novo_ibfk_2` FOREIGN KEY (`advogado_id`) REFERENCES `advogados` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `membros_orgaos`
--
ALTER TABLE `membros_orgaos`
  ADD CONSTRAINT `membros_orgaos_ibfk_1` FOREIGN KEY (`orgao_id`) REFERENCES `orgaos_diretivos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `noticias_imagens`
--
ALTER TABLE `noticias_imagens`
  ADD CONSTRAINT `noticias_imagens_ibfk_1` FOREIGN KEY (`noticia_id`) REFERENCES `noticias` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `solicitacoes_advogados`
--
ALTER TABLE `solicitacoes_advogados`
  ADD CONSTRAINT `solicitacoes_advogados_ibfk_1` FOREIGN KEY (`advogado_atribuido_id`) REFERENCES `advogados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
