<?php
require_once __DIR__ . '/admin/includes/db.php';

echo "Iniciando Sincronização de Dados...\n";

// 1. Map instituicao_info to paginas_ordem
try {
    $pdo->exec("INSERT INTO paginas_ordem (titulo, slug, conteudo) 
                VALUES ('História da Ordem', 'historia', 'A Ordem dos Advogados da Guiné-Bissau foi criada para regular a profissão e defender os interesses dos seus membros e da sociedade.')
                ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo)");
    echo "História sincronizada.\n";
} catch (Exception $e) {}

// 2. Import Bastonarios (if not exists)
try {
    $pdo->exec("INSERT IGNORE INTO bastonarios (nome_completo, biografia, data_inicio_mandato, data_fim_mandato, email_contacto, is_atual) VALUES
                ('Dr. Januário Pedro', 'Advogado com mais de 20 anos de experiência em Direito Penal e Direitos Humanos.', '2022-01-15', '2025-01-14', 'bastonario@oagb.gw', 1),
                ('Dra. Maria Silva', 'Especialista em Direito Comercial e ex-Bastonária da OAGB.', '2018-01-15', '2022-01-14', 'maria.silva@exemplo.gw', 0)");
    echo "Bastonários sincronizados.\n";
} catch (Exception $e) {}

// 3. Import Advogados
try {
    $pdo->exec("INSERT IGNORE INTO advogados (nome_completo, numero_registo, data_inscricao, email, telefone, regiao) VALUES
                ('Dr. Carlos Mendes', 'OAGB-001/2005', '2005-03-10', 'carlos.mendes@advogados.gw', '+245 96 111 22 33', 'Bissau'),
                ('Dra. Fátima Gomes', 'OAGB-045/2010', '2010-07-22', 'fatima.gomes@advogados.gw', '+245 95 444 55 66', 'Bissau'),
                ('Dr. João Tavares', 'OAGB-112/2018', '2018-11-05', 'joao.tavares@advogados.gw', '+245 96 777 88 99', 'Bafatá')");
    echo "Advogados sincronizados.\n";
} catch (Exception $e) {}

// 4. Import Noticias (from Anuncios)
try {
    $pdo->exec("INSERT IGNORE INTO noticias (titulo, slug, resumo, conteudo, data_publicacao, categoria_tipo, ativo) VALUES
                ('Abertura do Ano Judicial 2024', 'abertura-ano-judicial-2024', 'Cerimónia solene de abertura do ano judicial.', '<p>A Ordem dos Advogados informa que a cerimónia de abertura do ano judicial terá lugar no dia...</p>', '2024-01-10 09:00:00', 'Notícia', 1),
                ('Comunicado sobre as Custas Judiciais', 'comunicado-custas-judiciais', 'Posição da OAGB sobre a nova tabela de custas.', '<p>O Conselho Diretivo da OAGB vem por este meio comunicar a sua posição oficial sobre...</p>', '2024-02-15 14:30:00', 'Anúncio', 1)");
    echo "Notícias sincronizadas.\n";
} catch (Exception $e) {}

echo "Sincronização Concluída.\n";
?>
