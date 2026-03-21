-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 07, 2025 at 02:35 PM
-- Server version: 11.4.8-MariaDB
-- PHP Version: 8.3.25

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advogados`
--

INSERT INTO `advogados` (`id`, `numero_registo`, `nome_completo`, `genero`, `data_nascimento`, `nacionalidade`, `bi_passaporte`, `regiao`, `localidade`, `morada`, `telefone`, `email`, `status`, `data_inscricao`, `observacoes`, `ordem_exibicao`, `foto`, `created_at`, `updated_at`) VALUES
(1, '001/2020', 'António Silva Santos', 'M', NULL, 'Guineense', NULL, 'SAB', 'Bissau', NULL, '+245 966 123 456', 'antonio.santos@email.gw', 'ativo', '2020-01-15', NULL, 0, NULL, '2025-06-09 15:31:51', '2025-06-09 15:31:51'),
(2, '002/2020', 'Maria Fernanda Gomes', 'F', NULL, 'Guineense', NULL, 'Bafatá', 'Bafatá', NULL, '+245 966 789 123', 'maria.gomes@email.gw', 'ativo', '2020-02-20', NULL, 0, NULL, '2025-06-09 15:31:51', '2025-06-09 15:31:51'),
(3, '003/2021', 'João Carlos Mendes', 'M', NULL, 'Guineense', NULL, 'Cacheu', 'Cacheu', NULL, '+245 966 456 789', 'joao.mendes@email.gw', 'ativo', '2021-03-10', NULL, 0, NULL, '2025-06-09 15:31:51', '2025-06-09 15:31:51');

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
  `data_inicio_estagio` date NOT NULL,
  `data_fim_estagio` date DEFAULT NULL,
  `status` enum('ativo','concluido','cancelado') DEFAULT 'ativo',
  `observacoes` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_evento` datetime NOT NULL,
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

INSERT INTO `agenda` (`id`, `titulo`, `descricao`, `data_evento`, `data_fim_evento`, `hora_inicio`, `hora_fim`, `local_evento`, `endereco_completo`, `tipo_evento`, `organizador`, `contacto_info`, `email_contacto`, `link_inscricao`, `programa`, `documentos`, `imagem_destaque`, `slug`, `destaque`, `ativo`, `visualizacoes`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `og_image`) VALUES
(1, 'IX Congresso dos Advogados Guineenses', 'Congresso anual da Ordem dos Advogados da Guiné-Bissau com palestras e workshops sobre direito contemporâneo.', '2024-06-23 09:00:00', NULL, NULL, NULL, 'Hotel Dunia, Bissau', NULL, 'congresso', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ix-congresso-advogados-guineenses-2024', 1, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'IX Congresso dos Advogados Guineenses 2024 - OAGB', 'Participe do IX Congresso dos Advogados Guineenses. 23-25 de Junho de 2024 no Hotel Dunia, Bissau.', NULL),
(2, 'Formação sobre Novo Código Civil', 'Workshop intensivo sobre as alterações do novo Código Civil da Guiné-Bissau.', '2024-07-15 14:00:00', NULL, NULL, NULL, 'Sede da OAGB', NULL, 'formacao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'formacao-novo-codigo-civil-2024', 1, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'Formação sobre Novo Código Civil - OAGB', 'Workshop sobre as principais alterações do novo Código Civil da Guiné-Bissau.', NULL),
(3, 'Assembleia Geral Ordinária', 'Assembleia Geral Ordinária da Ordem dos Advogados da Guiné-Bissau para aprovação de contas e eleições.', '2024-08-10 10:00:00', NULL, NULL, NULL, 'Auditório da OAGB', NULL, 'reuniao', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'assembleia-geral-ordinaria-2024', 0, 1, 0, '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'Assembleia Geral Ordinária 2024 - OAGB', 'Assembleia Geral Ordinária da OAGB para aprovação de contas e outras deliberações.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `agenda_imagens`
--

CREATE TABLE `agenda_imagens` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
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

INSERT INTO `anuncios` (`id`, `titulo`, `descricao`, `link_url`, `link_texto`, `imagem`, `data_inicio`, `data_fim`, `ordem_exibicao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Inscrições Abertas para Estágio', 'Estão abertas as inscrições para o programa de estágio profissional da OAGB. Prazo até 30 de Agosto.', 'inscricao-ordem.php', 'Inscrever-se', NULL, '2025-08-01', '2025-08-30', 1, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28'),
(2, 'Renovação de Cédulas Profissionais', 'Todos os advogados devem renovar suas cédulas profissionais até o final do ano.', 'advogados.php', 'Mais informações', NULL, '2025-08-01', '2025-12-31', 2, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28'),
(3, 'Curso de Atualização em Direito Digital', 'Nova turma do curso de Direito Digital e Proteção de Dados. Vagas limitadas.', 'agenda.php', 'Ver detalhes', NULL, '2025-08-01', '2025-09-15', 3, 1, '2025-08-07 02:06:28', '2025-08-07 02:06:28');

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
(1, 'Bem-vindo à Ordem dos Advogados da Guiné-Bissau', 'A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública de licenciados em Direito, que em conformidade com os preceitos dos respectivos estatutos e demais disposições legais aplicáveis exercem a advocacia.', 'brass-scales-justice-close-up-view.jpg', 'Saiba mais', 'apresentacao-historia.php', 1, 1, '2025-08-06 23:52:15', '2025-08-07 00:58:35'),
(2, 'Cadastro Nacional de Advogados da Guiné-Bissau', 'O Cadastro Nacional dos Advogados (CNA) é mantido pelo Conselho de Administração da OAGB, que exerce a função de fiel repositório do cadastro de todos os advogados da Guiné-Bissau.', 'close-up-detail-scales-justice.jpg', 'Saiba mais', 'advogados-inscritos.php', 2, 1, '2025-08-06 23:52:15', '2025-08-07 00:58:22'),
(3, 'Formação Contínua e Desenvolvimento Profissional', 'A OAGB promove regularmente formações e workshops para atualização e desenvolvimento profissional dos advogados, garantindo a excelência na prestação de serviços jurídicos.', 'brass-scales-justice-close-up-view.jpg', 'Ver Formações', 'agenda.php', 3, 1, '2025-08-06 23:52:15', '2025-08-07 00:58:42');

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
-- Table structure for table `documentos_publicos`
--

CREATE TABLE `documentos_publicos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(300) NOT NULL,
  `tipo` enum('parecer','deliberacao','comunicado','publicacao','orcamento') NOT NULL,
  `numero_documento` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `link_externo` varchar(255) DEFAULT NULL,
  `data_documento` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `visualizacoes` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `categoria` varchar(100) DEFAULT NULL,
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
  `canonical_url` varchar(255) DEFAULT NULL COMMENT 'URL canônica do artigo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de notícias e artigos do site';

--
-- Dumping data for table `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `slug`, `resumo`, `conteudo`, `conteudo_formatado`, `imagem_destaque`, `categoria`, `tags`, `autor`, `destaque`, `ordem_destaque`, `ativo`, `visualizacoes`, `data_publicacao`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `og_image`, `canonical_url`) VALUES
(1, 'OAGB publica edital para formação de lista sêxtupla ao TRT-8', 'oagb-publica-edital-trt8', 'O \"FBE International Contract Competition\" organizado pelo \"Federation des Barreaux d\'Europe\" (FBE) conjuntamente com a Ordem dos Advogados.', '<p>A Ordem dos Advogados da Guiné-Bissau publicou recentemente um edital importante para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho...</p>', NULL, NULL, '3º CURSO DE FORMAÇÃO DOS ESTAGIÁRIOS', NULL, 'OAGB', 1, 0, 1, 5, '2025-06-09 15:31:51', '2025-06-09 15:31:51', '2025-08-08 13:07:34', NULL, NULL, NULL, NULL),
(2, 'Conferência sobre o Papel da Ordem e dos Advogados', 'conferencia-papel-ordem-advogados', 'Importante conferência sobre o papel institucional da Ordem dos Advogados na administração da justiça.', '<p>Realizou-se no passado dia uma importante conferência sobre o papel da Ordem dos Advogados e dos profissionais na administração da justiça...</p>', NULL, NULL, 'CONFERÊNCIA', NULL, 'OAGB', 0, 0, 1, 0, '2025-06-09 15:31:51', '2025-06-09 15:31:51', '2025-06-09 15:31:51', NULL, NULL, NULL, NULL),
(3, 'OAGB publica edital para formação de lista sêxtupla ao TRT-8', 'oagb-publica-edital-lista-sextupla-trt8', 'A Ordem dos Advogados da Guiné-Bissau publicou edital para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho.', '<p>A Ordem dos Advogados da Guiné-Bissau (OAGB) publicou no dia de hoje edital para a formação de lista sêxtupla destinada ao preenchimento de vaga de Juiz do Tribunal Regional do Trabalho da 8ª Região.</p><p>O edital estabelece os requisitos e procedimentos para a inscrição de advogados interessados em compor a lista que será encaminhada ao Tribunal Superior do Trabalho.</p>', NULL, 'Asset 7-100.jpg', NULL, NULL, NULL, 1, 0, 1, 13, '2024-06-24 10:00:00', '2025-06-10 13:30:34', '2025-08-08 14:09:45', 'OAGB publica edital para lista sêxtupla ao TRT-8', 'A Ordem dos Advogados da Guiné-Bissau publicou edital para formação de lista sêxtupla ao Tribunal Regional do Trabalho.', NULL, NULL),
(4, 'Competição Internacional de Contratos FBE', 'competicao-internacional-contratos-fbe', 'Jovens advogados participam na competição internacional organizada pela Federação dos Advogados da Europa.', '<p>O \"FBE International Contract Competition\" organizado pelo \"Federation des Barreaux d\'Europe\" (FBE) conjuntamente com a Ordem dos Advogados - através do Instituto de Apoio aos Jovens Advogados (IAJA).</p><p>Esta competição visa promover o conhecimento jurídico e as competências práticas dos jovens advogados na área do direito contratual internacional.</p>', NULL, 'Asset 8-100.jpg', NULL, NULL, NULL, 1, 0, 1, 4, '2024-06-20 15:30:00', '2025-06-10 13:30:34', '2025-08-08 10:27:27', 'Competição Internacional de Contratos FBE - OAGB', 'Jovens advogados da OAGB participam na competição internacional da Federação dos Advogados da Europa.', NULL, NULL),
(5, 'Nova regulamentação para inscrição de estagiários', 'nova-regulamentacao-inscricao-estagiarios', 'Conselho da OAGB aprova nova regulamentação para o processo de inscrição de advogados estagiários.', '<p>O Conselho da Ordem dos Advogados da Guiné-Bissau aprovou nova regulamentação que simplifica e moderniza o processo de inscrição de advogados estagiários.</p><p>As principais alterações incluem a digitalização do processo, redução de prazos e maior transparência nos critérios de avaliação.</p>', NULL, 'Asset 9-100.jpg', NULL, NULL, NULL, 1, 0, 1, 0, '2024-06-18 09:00:00', '2025-06-10 13:30:34', '2025-06-10 13:30:34', 'Nova regulamentação para inscrição de estagiários - OAGB', 'OAGB aprova nova regulamentação que simplifica o processo de inscrição de advogados estagiários.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `noticias_imagens`
--

CREATE TABLE `noticias_imagens` (
  `id` int(11) NOT NULL,
  `noticia_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `ordem_exibicao` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Galeria de imagens das notícias';

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paginas_ordem`
--

INSERT INTO `paginas_ordem` (`id`, `titulo`, `slug`, `conteudo`, `imagem`, `ativo`, `ordem_exibicao`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Apresentação e História', 'apresentacao-historia', '<p>A Ordem dos Advogados da Guiné-Bissau é uma instituição pública de natureza associativa que representa todos os advogados do país...</p>', NULL, 1, 1, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53'),
(2, 'Órgãos Sociais', 'orgaos-sociais', '<p>Os órgãos sociais da OAGB são constituídos por...</p>', NULL, 1, 2, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53'),
(3, 'Comissões Especializadas', 'comissoes-especializadas', '<p>As comissões especializadas desenvolvem trabalhos específicos...</p>', NULL, 1, 3, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53'),
(4, 'Cooperação Institucional', 'cooperacao-institucional', '<p>A cooperação institucional da OAGB...</p>', NULL, 1, 4, NULL, NULL, NULL, '2025-06-03 16:30:53', '2025-06-03 16:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `pareceres_deliberacoes`
--

CREATE TABLE `pareceres_deliberacoes` (
  `id` int(11) NOT NULL,
  `tipo` enum('parecer','deliberacao') NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `titulo` varchar(300) NOT NULL,
  `descricao` text DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `arquivo_pdf` varchar(255) DEFAULT NULL,
  `data_documento` date NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pareceres e Deliberações da OAGB';

--
-- Dumping data for table `pareceres_deliberacoes`
--

INSERT INTO `pareceres_deliberacoes` (`id`, `tipo`, `numero_documento`, `titulo`, `descricao`, `conteudo`, `arquivo_pdf`, `data_documento`, `link_url`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'deliberacao', 'CNEF n.º 8/2023', 'Deliberação sobre Código de Ética', 'Deliberação do Conselho Nacional de Ética e Formação sobre alterações ao Código de Ética dos Advogados', NULL, NULL, '2023-12-15', 'pareceres-deliberacoes.php?id=1', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57'),
(2, 'parecer', 'Parecer n.º 12/2023', 'Parecer sobre Lei de Honorários', 'Parecer técnico sobre a proposta de lei de honorários advocatícios', NULL, NULL, '2023-11-20', 'pareceres-deliberacoes.php?id=2', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57'),
(3, 'deliberacao', 'CNEF n.º 7/2023', 'Deliberação sobre Formação Contínua', 'Novas regras para formação contínua obrigatória dos advogados', NULL, NULL, '2023-10-10', 'pareceres-deliberacoes.php?id=3', 1, '2025-08-07 16:23:57', '2025-08-07 16:23:57');

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

--
-- Indexes for dumped tables
--

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
-- Indexes for table `configuracoes_site`
--
ALTER TABLE `configuracoes_site`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Indexes for table `documentos_publicos`
--
ALTER TABLE `documentos_publicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_documento`),
  ADD KEY `idx_numero` (`numero_documento`);

--
-- Indexes for table `estatisticas_visualizacao`
--
ALTER TABLE `estatisticas_visualizacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conteudo` (`tipo_conteudo`,`conteudo_id`),
  ADD KEY `idx_data` (`data_visualizacao`);

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
-- Indexes for table `inscricoes_ordem`
--
ALTER TABLE `inscricoes_ordem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tipo` (`tipo_inscricao`);

--
-- Indexes for table `logs_atividade`
--
ALTER TABLE `logs_atividade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_acao` (`acao`),
  ADD KEY `idx_data` (`created_at`);

--
-- Indexes for table `mensagens_contacto`
--
ALTER TABLE `mensagens_contacto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lida` (`lida`);

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
-- Indexes for table `pareceres_deliberacoes`
--
ALTER TABLE `pareceres_deliberacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_documento`),
  ADD KEY `idx_ativo` (`ativo`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advogados`
--
ALTER TABLE `advogados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `advogados_estagiarios`
--
ALTER TABLE `advogados_estagiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comissoes`
--
ALTER TABLE `comissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configuracoes_site`
--
ALTER TABLE `configuracoes_site`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `documentos_publicos`
--
ALTER TABLE `documentos_publicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estatisticas_visualizacao`
--
ALTER TABLE `estatisticas_visualizacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `inscricoes_ordem`
--
ALTER TABLE `inscricoes_ordem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs_atividade`
--
ALTER TABLE `logs_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensagens_contacto`
--
ALTER TABLE `mensagens_contacto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscricoes`
--
ALTER TABLE `newsletter_subscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `noticias_imagens`
--
ALTER TABLE `noticias_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orgaos_sociais`
--
ALTER TABLE `orgaos_sociais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paginas_ordem`
--
ALTER TABLE `paginas_ordem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pareceres_deliberacoes`
--
ALTER TABLE `pareceres_deliberacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
