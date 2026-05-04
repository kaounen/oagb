-- Base de dados para Ordem dos Advogados da Guiné-Bissau
CREATE DATABASE oagb_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE oagb_db;

-- Tabela de advogados
CREATE TABLE advogados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_registo VARCHAR(20) UNIQUE NOT NULL,
    nome_completo VARCHAR(200) NOT NULL,
    genero ENUM('M', 'F') NOT NULL,
    data_nascimento DATE,
    nacionalidade VARCHAR(50) DEFAULT 'Guineense',
    bi_passaporte VARCHAR(50),
    regiao VARCHAR(50) NOT NULL,
    localidade VARCHAR(100),
    morada TEXT,
    telefone VARCHAR(20),
    email VARCHAR(100),
    status ENUM('ativo', 'suspenso', 'inativo') DEFAULT 'ativo',
    data_inscricao DATE NOT NULL,
    observacoes TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_regiao (regiao),
    INDEX idx_nome (nome_completo),
    INDEX idx_status (status)
);

-- Tabela de advogados estagiários
CREATE TABLE advogados_estagiarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_registo VARCHAR(20) UNIQUE NOT NULL,
    nome_completo VARCHAR(200) NOT NULL,
    genero ENUM('M', 'F') NOT NULL,
    data_nascimento DATE,
    nacionalidade VARCHAR(50) DEFAULT 'Guineense',
    bi_passaporte VARCHAR(50),
    regiao VARCHAR(50) NOT NULL,
    localidade VARCHAR(100),
    morada TEXT,
    telefone VARCHAR(20),
    email VARCHAR(100),
    orientador_id INT,
    data_inicio_estagio DATE NOT NULL,
    data_fim_estagio DATE,
    status ENUM('ativo', 'concluido', 'cancelado') DEFAULT 'ativo',
    observacoes TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (orientador_id) REFERENCES advogados(id),
    INDEX idx_regiao (regiao),
    INDEX idx_nome (nome_completo),
    INDEX idx_status (status)
);

-- Tabela de notícias
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(300) NOT NULL,
    slug VARCHAR(300) UNIQUE NOT NULL,
    resumo TEXT,
    conteudo LONGTEXT NOT NULL,
    imagem_destaque VARCHAR(255),
    categoria VARCHAR(100),
    tags TEXT,
    autor VARCHAR(100),
    destaque BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE,
    visualizacoes INT DEFAULT 0,
    data_publicacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_data (data_publicacao),
    INDEX idx_destaque (destaque),
    INDEX idx_ativo (ativo)
);

-- Tabela de agenda/eventos
CREATE TABLE agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(300) NOT NULL,
    descricao TEXT,
    data_evento DATETIME NOT NULL,
    local_evento VARCHAR(200),
    organizador VARCHAR(100),
    tipo_evento VARCHAR(50),
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_data (data_evento)
);

-- Tabela de documentos públicos
CREATE TABLE documentos_publicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(300) NOT NULL,
    tipo ENUM('parecer', 'deliberacao', 'comunicado', 'publicacao', 'orcamento') NOT NULL,
    descricao TEXT,
    arquivo VARCHAR(255),
    data_documento DATE,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_data (data_documento)
);

-- Tabela de orgãos sociais
CREATE TABLE orgaos_sociais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    cargo VARCHAR(100) NOT NULL,
    mandato_inicio DATE,
    mandato_fim DATE,
    foto VARCHAR(255),
    biografia TEXT,
    ordem_exibicao INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de comissões especializadas
CREATE TABLE comissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    presidente VARCHAR(200),
    membros TEXT,
    area_atuacao VARCHAR(100),
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de solicitações de advogados
CREATE TABLE solicitacoes_advogados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_solicitante VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    area_juridica VARCHAR(100),
    regiao_preferencia VARCHAR(50),
    descricao_caso TEXT,
    urgencia ENUM('baixa', 'media', 'alta') DEFAULT 'media',
    status ENUM('pendente', 'atribuido', 'concluido', 'cancelado') DEFAULT 'pendente',
    advogado_atribuido_id INT NULL,
    data_atribuicao DATETIME NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (advogado_atribuido_id) REFERENCES advogados(id),
    INDEX idx_status (status),
    INDEX idx_regiao (regiao_preferencia)
);

-- Tabela de inscrições na Ordem
CREATE TABLE inscricoes_ordem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_inscricao ENUM('advogado', 'estagiario') NOT NULL,
    nome_completo VARCHAR(200) NOT NULL,
    genero ENUM('M', 'F') NOT NULL,
    data_nascimento DATE NOT NULL,
    nacionalidade VARCHAR(50) DEFAULT 'Guineense',
    bi_passaporte VARCHAR(50) NOT NULL,
    regiao VARCHAR(50) NOT NULL,
    localidade VARCHAR(100),
    morada TEXT NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    formacao_academica TEXT NOT NULL,
    experiencia_profissional TEXT,
    documentos_anexos TEXT,
    status ENUM('pendente', 'em_analise', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    observacoes_admin TEXT,
    data_aprovacao DATE NULL,
    numero_registo_atribuido VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_tipo (tipo_inscricao)
);

-- Tabela de subscrições de newsletter
CREATE TABLE subscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    nome VARCHAR(200),
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de mensagens de contacto
CREATE TABLE mensagens_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    assunto VARCHAR(300) NOT NULL,
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    respondida BOOLEAN DEFAULT FALSE,
    data_resposta DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lida (lida)
);

-- Tabela de páginas institucionais
CREATE TABLE paginas_ordem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    conteudo LONGTEXT,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE,
    ordem_exibicao INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir dados iniciais
INSERT INTO paginas_ordem (titulo, slug, conteudo, ordem_exibicao) VALUES 
('Apresentação e História', 'apresentacao-historia', '<p>A Ordem dos Advogados da Guiné-Bissau é uma instituição pública de natureza associativa que representa todos os advogados do país...</p>', 1),
('Órgãos Sociais', 'orgaos-sociais', '<p>Os órgãos sociais da OAGB são constituídos por...</p>', 2),
('Comissões Especializadas', 'comissoes-especializadas', '<p>As comissões especializadas desenvolvem trabalhos específicos...</p>', 3),
('Cooperação Institucional', 'cooperacao-institucional', '<p>A cooperação institucional da OAGB...</p>', 4);

INSERT INTO noticias (titulo, slug, resumo, conteudo, categoria, autor, destaque) VALUES 
('OAGB publica edital para formação de lista sêxtupla ao TRT-8', 'oagb-publica-edital-trt8', 'O "FBE International Contract Competition" organizado pelo "Federation des Barreaux d\'Europe" (FBE) conjuntamente com a Ordem dos Advogados - através do Instituto de Apoio aos Jovens Advogados (IAJA).', '<p>A Ordem dos Advogados da Guiné-Bissau publicou recentemente um edital importante para a formação de lista sêxtupla destinada ao Tribunal Regional do Trabalho...</p>', '3º CURSO DE FORMAÇÃO DOS ESTAGIÁRIOS', 'OAGB', TRUE),
('Conferência sobre o Papel da Ordem e dos Advogados', 'conferencia-papel-ordem-advogados', 'Importante conferência sobre o papel institucional da Ordem dos Advogados na administração da justiça.', '<p>Realizou-se no passado dia uma importante conferência sobre o papel da Ordem dos Advogados e dos profissionais na administração da justiça...</p>', 'CONFERÊNCIA "PAPEL DA ORDEM E DOS ADVOGADOS NA ADMINISTRAÇÃO DA JUSTIÇA"', 'OAGB', FALSE),
('Novo Estatuto da OAGB', 'novo-estatuto-oagb', 'Aprovação e entrada em vigor do novo estatuto da Ordem dos Advogados da Guiné-Bissau.', '<p>Foi aprovado o novo estatuto da Ordem dos Advogados da Guiné-Bissau, que entrará em vigor...</p>', 'NOVO ESTATUTO DA OAGB', 'OAGB', TRUE);

-- Inserir dados de exemplo para advogados (mascarados)
INSERT INTO advogados (numero_registo, nome_completo, genero, regiao, localidade, telefone, email, data_inscricao, status) VALUES
('001/2020', 'António Silva Santos', 'M', 'SAB', 'Bissau', '+245 966 123 456', 'antonio.santos@email.gw', '2020-01-15', 'ativo'),
('002/2020', 'Maria Fernanda Gomes', 'F', 'Bafatá', 'Bafatá', '+245 966 789 123', 'maria.gomes@email.gw', '2020-02-20', 'ativo'),
('003/2021', 'João Carlos Mendes', 'M', 'Cacheu', 'Cacheu', '+245 966 456 789', 'joao.mendes@email.gw', '2021-03-10', 'ativo'),
('004/2021', 'Ana Paula Rodrigues', 'F', 'SAB', 'Bissau', '+245 966 321 654', 'ana.rodrigues@email.gw', '2021-05-08', 'ativo'),
('005/2022', 'Carlos Alberto Pereira', 'M', 'Gabú', 'Gabú', '+245 966 987 321', 'carlos.pereira@email.gw', '2022-01-25', 'ativo');

-- Inserir dados de exemplo para estagiários
INSERT INTO advogados_estagiarios (numero_registo, nome_completo, genero, regiao, localidade, telefone, email, data_inicio_estagio, status, orientador_id) VALUES
('EST001/2024', 'Pedro Miguel Tavares', 'M', 'SAB', 'Bissau', '+245 966 111 222', 'pedro.tavares@email.gw', '2024-01-15', 'ativo', 1),
('EST002/2024', 'Luisa Fernanda Costa', 'F', 'Oio', 'Farim', '+245 966 333 444', 'luisa.costa@email.gw', '2024-02-01', 'ativo', 2),
('EST003/2024', 'Manuel José Correia', 'M', 'Bolama', 'Bolama', '+245 966 555 666', 'manuel.correia@email.gw', '2024-03-01', 'ativo', 3);