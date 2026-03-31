-- OAGB Estatutos - Database Schema & Data
-- Generated: 2026-03-30 00:51:44
-- Encoding: UTF-8

SET NAMES utf8mb4;

DROP TABLE IF EXISTS estatutos_artigos;

CREATE TABLE estatutos_artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_artigo INT NOT NULL,
    titulo_artigo VARCHAR(500) DEFAULT NULL,
    conteudo MEDIUMTEXT,
    tema VARCHAR(100) DEFAULT NULL COMMENT 'Thematic category for sidebar filtering',
    capitulo VARCHAR(100) DEFAULT NULL COMMENT 'For future manual assignment',
    titulo_capitulo VARCHAR(255) DEFAULT NULL COMMENT 'For future manual assignment',
    seccao VARCHAR(100) DEFAULT NULL COMMENT 'For future manual assignment',
    titulo_seccao VARCHAR(255) DEFAULT NULL COMMENT 'For future manual assignment',
    titulo_doc VARCHAR(100) DEFAULT NULL COMMENT 'For future manual assignment: TITULO I, II, etc.',
    titulo_doc_nome VARCHAR(255) DEFAULT NULL COMMENT 'For future manual assignment',
    ordem INT DEFAULT 0 COMMENT 'Display order (defaults to article number)',
    ativo TINYINT(1) DEFAULT 1 COMMENT '1=visible, 0=hidden',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_numero (numero_artigo),
    KEY idx_tema (tema),
    KEY idx_capitulo (capitulo),
    KEY idx_ativo (ativo),
    FULLTEXT KEY idx_fulltext (titulo_artigo, conteudo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (1, 'Denominação, Natureza Sede', '1. Denomina-se Ordem dos Advogados da Guiné-Bissau, a associação pública, com personalidade jurídica própria, representativa dos operadores do direito, que exercem profissionalmente a advocacia.
2. A denominação de advogado é reservada exclusivamente aos profissionais do direito inscrito na Ordem dos Advogados.
3. A Ordem dos Advogados tem a sua sede em Bissau, podendo criar representações e delegacias no interior do país.', 'Disposições Gerais', 1);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (2, 'Regime jurídico', '1. Os advogados inscritos na OAGB exercem as suas atividades de advocacia no território da Guiné-Bissau, subordinados à Constituição da República, as demais leis aplicáveis e aos preceitos deste estatuto e regulamentos.
2. As normas relativas ao exercício da advocacia e as liberdades fundamentais que os presentes estatutos reconhecem, são interpretadas de conformidade com a constituição.', 'Disposições Gerais', 2);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (3, 'Âmbito', 'A Ordem dos Advogados exerce as atribuições e competências que lhe conferidas pelos presentes e pela lei em todo o território nacional.', 'Disposições Gerais', 3);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (4, 'Relação com as outras organizações congéneres', 'A Ordem dos Advogados da Guiné-Bissau rege-se nas relações internacionais pelos princípios da Independência, do respeito dos direitos do homem, Independência do poder judicial e da reciprocidade.', 'Disposições Gerais', 4);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (5, 'Direito internacional', 'As normas e os princípios do direito internacional aceites pelo Estado da Guiné-Bissau relativa a proteção dos direitos do homem e proteção da independência dos advogados e dos magistrados fazem parte integrante dos presentes estatutos, podendo ser invocadas diretamente perante os poderes públicos.', 'Disposições Gerais', 5);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (6, 'A independência da OAGB, dos advogados e valores', '1. A Ordem dos Advogados é independente face aos órgãos do Estado, pugnando pelo Estado Democrático de Direito e social, e como valores superiores do seu ordenamento jurídico, a independência da justiça, a igualdade e o pluralismo de pensamento jurídico.
2. O direito ao livre exercício da profissão, o respeito pela lei, dos direitos, liberdades e garantias fundamentais das pessoas e aos princípios éticos inerentes ao bom exercício profissional da atividade forense, bem como a independência dos Magistrados e dos advogados
3. Não há hierarquia nem subordinação entre advogados, magistrados judiciais e do Ministério Publico, devendo todos tratar-se com consideração e respeito recíprocos.', 'Disposições Gerais', 6);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (7, 'Representação da Ordem dos Advogados', '1. A Ordem dos Advogados é representada em juízo e fora dele pelo Bastonário.
2. Para defesa de todos os seus membros em todos os assuntos relativos ao exercício da profissão ou ao desempenho das funções nos órgãos sociais, a OAGB, poderá exercer o direito de assistente ou conceder patrocínio em processo de qualquer natureza.
3. A Ordem dos Advogados, quando intervenha como assistente em processo penal, pode ser representada por advogado diferente do constituído pelos restantes assistentes, havendo-os.', 'Disposições Gerais', 7);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (8, 'Trato oficial, honras, títulos honoríficos e patronos', '1. O Bastonário da Ordem dos Advogados é tratado de \"Excelentíssimo Senhor Bastonário\" e os presidentes dos restantes membros dos órgãos sociais de \"Ilustríssimo senhor\"
2. Os advogados são tratados de \"ilustres\".
3. A denominação de Bastonário é vitalícia.
4. Nos atos oficiais, o Bastonário da Ordem fica imediatamente à esquerda do Procurador-Geral da República.
5. Durante o seu mandato e no estrito desempenho das suas funções, o Bastonário não pode ser censurado por opiniões que emitir nessa qualidade, contando que não as profira em violação da Lei.
6. O Advogado que exerça ou haja exercido cargos nos órgãos da Ordem tem direito a usar a insígnia correspondente, nos termos do respetivo regulamento.
7. O Advogado que desempenha ou tenha desempenhado funções no Conselho Nacional da Ordem, Comissão de deontologia e ética e tribunal de ética e disciplina da Ordem enquanto se encontre no exercício dos cargos e nos 2 anos subsequentes, fica isento de prestar quaisquer serviços de nomeação oficiosa.
8. Em caso de justificada necessidade, ao Conselho Nacional da Ordem poderá fazer cessar a isenção prevista no número anterior.
9. O Advogado que tenha exercido com mérito e distinção cargo nos órgãos da Ordem conserva a designação correspondente ao cargo mais elevado que haja ocupado.
10. A Ordem dos advogados da Guiné-Bissau, fazendo honra as suas origens, ela poderá adoptar patronos e comemorações que tenham acompanhado sua trajetória histórica desde a fundação.

TITULO II
Regime Economico da OAGB', 'Disposições Gerais', 8);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (9, 'O exercício económico', 'O exercício económico da OAGB corresponde ao ano civil.', 'Regime Económico', 9);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (10, 'Dos recursos da OAGB', '1. Constituem recursos da OAGB, os seguintes:
a) Os rendimentos de qualquer natureza resultante das atividades da OAGB, bens, serviços ou direitos que integram o património da OAGB, bem como os rendimentos depositados em contas;
b) As joias de inscrição e quotas;
c) As taxas fixadas pela Direção Nacional para emissão de certificações, boletins, modelos de contratos de honorários, faturas e recibos dos mesmos, bem como de outros modelos de formulários;
d) Taxas para pedidos de informação, consultas, acesso a biblioteca, bem como pela prestação de outros serviços;
a) As subvenções ou donativos concedidos a OAGB por Organismos Internacionais, o Estado, o poder local, entidades públicas e privadas, ou particulares;
b) Os bens e direitos de toda classe que por doação, herança, legado ou outro título integram o património da OAGB;
c) Quaisquer outros bens móveis e imoveis adquiridos legalmente, bem como Produto de venda das publicações, organização de atividades de caracter científico, cursos, congressos, atividades desportivas ou recreativas, etc.,
2. A OAGB goza de imunidade tributária total sobre os seus bens, rendimentos e serviços.', 'Regime Económico', 10);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (11, 'Despesas', 'As receitas da Ordem serão afetadas as suas despesas devidamente orçamentadas.', 'Regime Económico', 11);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (12, 'Contabilidade e Gestão Financeira', '1. O exercício da vida económica da Ordem coincide com o ano civil.
2. As contas da Ordem são encerradas em 31 de Dezembro de cada ano.
3. Constituem instrumentos de controlo de gestão o orçamento, o relatório e as contas do exercício com referência a 31 de Dezembro.', 'Regime Económico', 12);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (13, 'Processo e Papéis da Ordem, Selos, Custas e Impostos de Justiça', '1. Não dão lugar às custas ou impostos de justiça e não estão sujeitos a impostos de selo as certidões expedidas pela Ordem, os requerimentos e petições a ela dirigidos e os processos que nela corram ou em que tenha intervenção.
2. A Ordem pode requerer e alegar em papel não selado e está isenta de custas, preparos e impostos de justiça em qualquer processo em que intervenha.', 'Regime Económico', 13);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (14, 'Providência Social, económica e jurídica', 'A Ordem dos Advogados assegura a proteção social, económica e jurídica dos Advogados, nos termos das disposições legais e regulamentais aplicáveis.', 'Membros e Inscrição', 14);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (15, 'Obrigatoriedade de inscrição na OAGB', '1. É obrigatória a inscrição na Ordem dos advogados da Guiné-Bissau para o exercício da advocacia no território nacional da Guiné-Bissau.
2. A qualidade de advogado se adquire, se conserva e se perde de acordo com o estabelecido nos presentes estatutos e regulamente em vigor.
3. Podem inscrever-se na Ordem dos Advogados da Guiné-Bissau para o exercício da profissão de advogado, os indivíduos com pelo menos, licenciatura em direito.
4. A inscrição e atuação de advogados estrangeiros serão regidas pelos Estatutos, legislação nacional, comunitária ou internacional que o Estado da Guiné-Bissau é parte.', 'Membros e Inscrição', 15);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (16, 'Requisitos de inscrição', '1. Os requisitos para a inscrição na OAGB são os seguintes:
a) Ter a nacionalidade guineense ou de algum Estado membro da UEMOA, salvo disposição em tratado, convenção internacional ou disposição legal especifica;
b) Ser maior de idade e não estar abrangido por qualquer das causas de incapacidade;
c) Ter licenciatura em direito ou titular de um diploma estrangeiros que, conforme a legislação nacional aplicável, sejam homologados pelo departamento governamental competente, designadamente Ministério da Educação Nacional;
d) Pagar a joia e a quota de ingresso e demais direitos de inscrição que tenham sido fixados pela OAGB;
e) Formalizar a inscrição na caixa de assistência geral dos advogados sedeada no Instituto Nacional da Providencia Social;
f) Estar livre de qualquer antecedente criminal com moldura penal abstrata superior a três anos de prisão;
g) Prestar prova de aptidão na OAGB.
h) Não estar abrangido por qualquer situação de incompatibilidade ou proibição para o exercício da advocacia;
2. Não obstante, são admitidos à inscrição na OAGB, como advogados, exercendo a profissão de advogado, os indivíduos que reúnam as condições previstas nos presentes Estatuto e no Regulamento para o acesso a profissão.', 'Membros e Inscrição', 16);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (17, 'Direitos humanos', '1. Excetuam-se do disposto nos artigos 17° e 18° a proteção dos direitos humanos relativos ao estatuto da criança, mutilação genital feminina, tráfico de órgãos humanos, desde que o advogado ou funcionário internacional é contratado, designado ou nomeado para a defesa da vítima ou dos familiares.
2. O advogado ou funcionário internacional que pretende intervir nos tribunais do território da Guiné-Bissau para a proteção dos direitos humanos, nos termos do número anterior, deve solicitar a sua inscrição na OAGB para o efeito.
3. A inscrição efetuada nos termos e para os efeitos do número anterior caducada com a execução efetiva e completa da decisão com trânsito em julgado.', 'Membros e Inscrição', 17);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (18, 'Inscrição', '1. A inscrição deve ser feita na sede da OAGB.
2. O requerimento deve ser acompanhado de certidão do registo de nascimento, certificado de licenciatura, certificado de registo criminal e boletins preenchidos nos termos regulamentares assinados pelos interessados e acompanhados de três fotografias.
3. O pedido de inscrição deve ser formulado, em formulário próprio perante a Direção Nacional e só poderá ser recusado ou denegado, mediante deliberação devidamente fundamentada, contra a qual cabe recurso nos termos gerais.
4. Para a inscrição como advogado será dispensado o diploma de licenciatura ou documento que o substitua, quando o mesmo já conste dos arquivos da OAGB.
5. No requerimento pode o interessado indicar, para uso no exercício da profissão, nome abreviado, que não é admitido se suscetível de provocar confusão com outro anteriormente requerido ou inscrito, exceto se o possuidor deste com isso tenha concordado.
6. Todas as comunicações previstas no presente Estatuto e nos regulamentos da OAGB devem ser feitas, salvo disposição expressa em contrário, para o domicílio profissional indicado no formulário do pedido de inscrição.
7. O domicílio profissional do advogado estagiário é o do seu patrono.
8. O pedido de inscrição deve ser decido no prazo máximo de quinze dias.
9. No caso de recusa de inscrição, pode o interessado recorrer para a plenária do Conselho Nacional da Ordem.', 'Membros e Inscrição', 18);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (19, 'Inscrição como advogado. Requisitos', '1. Sem prejuízo das excepçoes previstas no estatuto, a inscrição como advogado é precedida de um estágio de dezoito meses com boa informação.
2. A inscrição como advogado, nas respetivas ordens, de cidadãos oriundos dos Países membros da CPLP, é reconhecida para efeito de inscrição na Ordem dos Advogados, observado o princípio da reciprocidade.', 'Membros e Inscrição', 19);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (20, 'Inscrição de Advogados Guineenses Regressados do Estrangeiro', '1. Ao advogado guineense inscrito como advogado numa Ordem dos Advogados membro da União dos Advogados de Língua Portuguesa (\"UALP\") que requerer a sua inscrição na Ordem dos Advogados Guineense, será inscrito como estagiário por um período de três meses.
2. Ao advogado guineense proveniente de outras Ordens de Advogados diferente do referido no número anterior, é fixado um tempo de estágio que pode ir de seis a doze meses, conforme o grau de dificuldade na adaptação à prática do foro, tendo sempre em conta as diferenças de sistemas judiciais, aplicando-se com as necessárias adaptações o regime da fase complementar do estágio.
3. O advogado requerente nos termos dos números anteriores deve juntar no acto da inscrição os documentos comprovativos da frequência do estágio e da sua inscrição como advogado efetivo, nomeadamente o certificado para o efeito passado pela respetiva Ordem dos Advogados ou entidade similar.', 'Membros e Inscrição', 20);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (21, 'Restrições ao Direito de Inscrição', '1. Não podem ser inscritos:
a) Os que não possuam idoneidade moral para o exercício da profissão e, nos termos do estatuto;
b) Os que não estejam no pleno goza dos direitos civis;
c) Os declarados incapazes de administrar as suas pessoas e bens por sentença transitada em julgado;
d) Os que estejam em situação de incompatibilidade ou inibição do exercício da advocacia:
e) Os magistrados e funcionários que, mediante processo disciplinar, hajam sido demitidos, aposentados ou colocados na inatividade por falta de idoneidade moral.', 'Membros e Inscrição', 21);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (22, 'Exercício da Advocacia por Estrangeiros', '1. Os estrangeiros diplomados em Faculdades de Direito estrangeiras, com residência permanente no território da República da Guiné-Bissau, desde o seu início até a data de inscrição, não inferior a três anos, podem inscrever-se na Ordem dos Advogados da Guiné-Bissau, nos mesmos termos dos guineenses, se a estes o seu país conceder reciprocidade.
2. Os Advogados diplomados por qualquer Faculdade de Direito dos Países membros da CPLP podem inscrever-se na Ordem dos Advogados em regime de reciprocidade.', 'Membros e Inscrição', 22);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (23, 'Inscrição como Estagiário. Requisitos', '1. A inscrição para o estágio profissional é precedida de concurso documental e prova de aptidão com a classificação de regular.
1. O interessado deve preencher os requisitos previstos no artigo 15°.
2. O estágio tem a duração de um período de 18 meses, durante o qual, sob a direção de um patrono, com pelo menos três anos de efetivo exercício da advocacia, o advogado estagiário efetuará consulta jurídica e prática forense.
1. As disposições deste Estatuto, com as necessárias adaptações, aplicam-se aos advogados estagiários, à exceção das que se referem ao exercício do direito de voto.
2. A organização geral do estágio cabe á OAGB.', 'Membros e Inscrição', 23);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (24, 'Inscrição dos Magistrados. Requisitos', 'Podem solicitar inscrição na OAGB como advogados, os Magistrados que tenham exercício efetivo de funções de forma ininterrupta, com pelo menos classificação de bom, por período de tempo igual ou superior a cinco anos, com quebra total do vínculo com o estado, salvo os direitos inerentes à reforma.', 'Membros e Inscrição', 24);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (25, 'Dispensa de estágio', 'São dispensados do estágio, os magistrados que preencham os requisitos previstos no artigo anterior, os mestres docentes nas Faculdades de Direito, com um mínimo de 5 anos de docência e os Doutores em Direito.', 'Membros e Inscrição', 25);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (26, 'Do juramento do compromisso profissional', '1. O advogado, no início do seu exercício profissional, prestará juramento de compromisso de respeito à constituição da República e Estado de Direito, bem como do fiel cumprimento das obrigações e normas deontológicas da profissão.
2. O juramento ou compromisso será prestado perante o Conselho Nacional da Ordem na forma que a OAGB estabelecer.', 'Membros e Inscrição', 26);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (27, 'Título profissional de Advogado', '1. O título de Advogado está exclusivamente reservado aos licenciados que obtiverem aprovação no curso obrigatório de acesso à profissão, com inscrição em vigor na OAGB.
2. Os candidatos a título profissional de Advogado são denominados de advogados estagiários.
3. Os advogados honorários podem usar a denominação de advogado desde que façam a indicação dessa qualidade.', 'Membros e Inscrição', 27);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (28, 'Traje Profissional', '1. A toga é o traje profissional do advogado e do estagiário.
2. É obrigatório para os advogados e advogados estagiários, quando pleiteiem oralmente, o uso da toga.
3. O modelo da toga, bem como qualquer outro acessório do traje profissional, é o fixado pelo Conselho Nacional da Ordem.', 'Membros e Inscrição', 28);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (29, 'Da identidade profissional', '1. Constitui documento de identidade profissional a carteira profissional emitida pela OAGB, de uso obrigatório pelos advogados e estagiários inscritos, para o exercício de suas atividades.
2. A carteira profissional, tem as seguintes dimensões (...) x (...) centímetros e observa os seguintes critérios:
a) A frente, em fundo (indicar as cores ou cor verde, um símbolo nacional, nome do advogado, escritório, n° de inscrição, e cidade e a expressão \" Ordem dos Advogados da Guiné-Bissau)
b) Verso registo da lei que concede proteção aos advogados\"
3. O cartão do estagiário tem a cor (.....) com a indicação de \"identidade de estagiário\" , n° de inscrição, escritório onde frequenta o estagio, o nome do patrono e o registo da lei que protege os advogados
4. O advogado tem direito a receber o cartão de identidade profissional imediatamente apos a prestação do juramento do compromisso com a constituição, a lei e aos estatutos da OAGB perante o presidente da República.', 'Membros e Inscrição', 29);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (30, 'Cancelamento e suspensão da inscrição', '1. A inscrição do advogado pode ser cancelada na OAGB:
a) Quando requerida pelo interessado;
b) Por óbito;
c) Por efeitos da sanção
d) Quando o advogado passar, com caracter definitivo, a exercer atividade incompatível com a advocacia;
e) Perder qualquer dos requisitos necessários para inscrição;
2. Suspende-se, a inscrição na OAGB:
a) Quando requerida pelo interessado;
b) Quando o advogado passar a exercer, com caracter temporário, atividade incompatível com o exercício da advocacia;
c) Por motivo de doença curável, que torna o advogado incapacitado de exercer a atividade durante dois anos.', 'Membros e Inscrição', 30);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (31, 'Direitos Perante a OAGB', 'Os advogados têm o direito a requerer a intervenção da OAGB para defesa dos seus direitos ou dos legítimos interesses da classe, nos termos previstos neste Estatuto.
Secção II
Formas de exercício da advocacia', 'Membros e Inscrição', 31);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (32, 'Liberdade de organização', 'O exercício da advocacia profissional pode assumir a forma individual ou coletiva ou sociedade de advogados, por conta própria, sob a forma de contrato de prestação de serviços ou contrato individual de trabalho.', 'Direitos e Deveres', 32);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (33, 'Exercício individual', '1. O advogado individual poderá partilhar o seu escritório, instalações, serviços ou outros meios de trabalho, sem identificação coletiva com outros advogados perante o cliente.
2. O advogado individual pode celebrar acordos para determinados assuntos ou categorias de assuntos com outros advogados, ou coletivo de advogados, nacionais ou estrangeiros, qualquer que seja sua forma.
3. O advogado titular do escritório individual responde profissionalmente perante o seu cliente pelos gastos e atuações que efetue com os seus colaboradores, sem prejuízo do direito de regresso contra estes;
4. Ficam excluídos do âmbito da responsabilidade previsto no número anterior, a violação das regras de deontologia e disciplina;
5. Os honorários devidos pelo cliente ao advogado titular do escritório, devem ser pagos diretamente a este, mesmo nos casos em que as intervenções no processo tenham sido realizadas por outro advogado, substabelecido com mãos conjuntas ou substituição total;
6. O titular do escritório responde pessoalmente pelos honorários devidos aos advogados intervenientes, mesmo nos casos em que o cliente tenha abona-los outras vantagens ou dinheiro, salvo pacto escrito em contrário.', 'Direitos e Deveres', 33);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (34, 'Exercício coletivo', '1. Os advogados podem exercer coletivamente a advocacia, através da constituição de sociedades de advogados em qualquer das formas permitidas por lei.
2. Não são admitidos, nem podem funcionar, as sociedades de advogados que representam formas ou características comerciais, que realizem atividades estranhas à advocacia, que incluem sócios não inscritos na Ordem ou proibidos de exercer a atividade de advocacia.
3. A organização deve ter como objecto exclusivo, o exercício profissional da advocacia e estar integrada maioritariamente por advogados em exercício na percentagem exigido pela lei aplicável, sem limitação de número.
4. Os advogados agrupados não podem atuar de forma independentes nem autónomos da sociedade.
5. Nas intervenções profissionais ou documentos que o grupo elabora deve constar a identificação da sociedade ou indicação expressa que o interveniente age em nome da sociedade.
6. Os advogados associados gozam de plena liberdade individual para aceitar ou recusar qualquer cliente ou assunto solicitado o patrocínio da sociedade e, plena independência no patrocínio da causa que tenha sido nomeado.
7. A atuação profissional dos sócios subordina-se a disciplina da OAGB, em matéria de deontologia e disciplina, respondendo pessoalmente cada um dos advogados perante a OAGB.
8. Nenhum advogado pode integrar mais de uma sociedade de advogados.
9. Os advogados sócios de uma sociedade profissional não podem representar em juízo ou fora de juízo, clientes de interesses opostos', 'Direitos e Deveres', 34);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (35, 'Contrato de trabalho', '1. O contrato de trabalho celebrado com o advogado, não lhe retira a isenção técnica, nem diminui a independência profissional a que esteja adstrito, inerente à profissão
2. Cabe exclusivamente à Ordem dos Advogados a apreciação da conformidade com os princípios deontológicos das cláusulas de contrato celebrado com o advogado, por via do qual o seu exercício profissional se encontra sujeito a subordinação jurídica.
3. O advogado trabalhador não está obrigado à prestação de serviços profissionais de interesse pessoal dos empregadores, bem como os serviços não compreendidos no objecto do contrato de trabalho.
4. São nulas as cláusulas de contrato celerado com o advogado que violem os princípios referidos no n° 2, bem como quaisquer orientações ou instruções da entidade empregadora que restrinjam a isenção e independência do advogado ou que, de algum modo, violem os princípios deontológicos da profissão.
5. O Conselho Nacional da Ordem dos Advogados pode solicitar às entidades públicas empregadoras, que hajam intervindo em tais contratos, entrega de cópia dos mesmos, a fim de aferir da legalidade do respetivo clausulado, atentos os critérios enunciados nos números anteriores.
6.  Quando a entidade empregadora seja pessoa de direito privado, qualquer dos contraentes pode solicitar ao Conselho Nacional parecer sobre a validade das cláusulas ou de actos praticados na execução do contrato, o qual tem caracter vinculativo.
7. Em caso de litígio, o parecer referido no número anterior é obrigatório.', 'Direitos e Deveres', 35);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (36, 'Escritório de procuradoria ou consulta jurídica', '1. Denomina-se escritório o domicílio profissional do advogado, o lugar onde o advogado exerce a sua atividade principal de advocacia.
2. O advogado deve promover a inscrição do seu escritório na Ordem dos Advogados. Se exercer a profissão no interior do país ou em lugares diversos, deve promover a inscrição suplementar dos mesmos, na ordem e nos tribunais em cuja jurisdição territorial exerce a atividade.
3. No caso de transferência ou mudança efetiva para outra localidade, deve o advogado requere a modificação do registo na Ordem, bem como nos tribunais, nos termos do número anterior.', 'Direitos e Deveres', 36);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (37, 'Proibições', '1. É proibido a constituição de escritório de procuradoria, designadamente judicial, administrativa, fiscal e laboral, ou de escritório que preste, de forma regular e remunerada, consulta jurídica a terceiros, ainda que, em qualquer dos casos, sob a direção efetiva de pessoa não habilitada para exercer o mandato judicial.
2. Não são abrangidos pelo disposto no número anterior, os serviços de contencioso e consulta jurídica mantidos pelos sindicatos, associações patronais ou outras associações legalmente constituídos, sem fim lucrativo e de reconhecido interesse público, destinados a facilitar a defesa exclusivamente dos interesses dos respetivos associados, desde que estes sejam praticados por gabinetes criados para o efeito, exercidos por advogados, advogados estagiários ou solicitador
3. A violação da proibição estabelecida no neste artigo confere à Ordem dos advogados o direito de requerer junto das autoridades judiciais competentes o encerramento do escritório ou gabinete.

Secção III
Honorários', 'Direitos e Deveres', 37);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (38, 'Honorários Limites e Forma de Pagamento', '1. O advogado tem direito a receber os honorários convencionados, fixados por arbitramento, por conciliação e aos de sucumbência correspondente aos serviços prestados.
2. Na fixação dos honorários deve o advogado proceder com moderação, atendendo ao tempo gasto, à dificuldade do assunto, à importância do serviço prestado, às posses dos interessados, aos resultados obtidos e à praxe do foro e estilo da área judicial.
3. Os honorários devem ser saldados em dinheiro.
4. É lícito ao advogado exigir, a título de provisão, quantias por conta dos honorários, o que, a não ser satisfeito, dá ao advogado direito a renunciar ao mandato.
5. Salvo estipulação em contrário, pelo menos um terço dos honorários é devido no início do serviço, outro terço até a decisão da primeira instância, se houver recurso, e o restante no final.
6. A cobrança dos honorários pode promover-se nos mesmos autos ou processo da acçao em que tenha atuado o advogado, se assim lhe convier.
7.  É nula qualquer cláusula, ou convenção que retire o advogado o direito aos honorários de sucumbência.', 'Direitos e Deveres', 38);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (39, 'Repartição de Honorários', 'É proibido ao advogado repartir honorários, ainda que a título de comissão ou outra forma de compensação, exceto com advogados, advogados estagiários e solicitadores com quem colabore ou que lhe tenham prestado colaboração.', 'Direitos e Deveres', 39);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (40, 'Honorários do advogado em regime do trabalho subordinado', 'Independentemente da retribuição ou convenção sobre os honorários, nas causas em que for parte o empregador, ou pessoa por este representada, os honorários de sucumbência são sempre devidos aos advogados trabalhadores.', 'Direitos e Deveres', 40);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (41, 'Preparos e Custas do Advogado pelo seu não Pagamento', 'O advogado não pode ser responsabilizado pela falta de pagamento de custas ou quaisquer despesas se, tendo pedido ao cliente as importâncias para tal necessárias, as não tiver recebido, e não é obrigado a dispor, para aquele efeito, das provisões que tenha recebido para honorários.', 'Direitos e Deveres', 41);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (42, 'Da colaboração interprofissional', 'Os advogados podem estabelecer protocolos ou formas de colaboração técnica com outros profissionais liberais para assistência ou peritagem aos assuntos que lhes são entregues para patrocínio.', 'Direitos e Deveres', 42);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (43, 'Constituição e registo dos estatutos e escritórios profissionais', '1. Compete a direção nacional da OAGB aprovar previamente, mediante emissão da certidão negativa, a constituição, suspensão ou dissolução de associação de advogados para o exercício coletivo da advocacia, bem como o registo dos estatutos e escritórios de advogados;
2. As associações de advogados para o exercício coletivo da advocacia será objecto de inscrição no registo central das pessoas coletivas

CAPITULO II
A Ordem dos Advogados e a participação da sociedade
SECÇÃO I
Congresso Nacional da Advocacia', 'Direitos e Deveres', 43);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (44, 'Do congresso da advocacia', '1. A Ordem dos Advogados organizará, pelo menos em cada três anos, um congresso da Advocacia, cujas conclusões terão caracter de parecer para o Governo para o sector da Justiça, Estado de Direito, Democracia e direitos humanos.
2. Sem prejuízo da autonomia da Ordem dos Advogados na escolha e seleção dos temas, o Governo poderá propor a Ordem dos Advogados para os trabalhos do congresso os temas de interesse para a boa governação ou outros de interesse geral para o aperfeiçoamento do ordenamento jurídico.
3. O Congresso é aberto a todos os advogados e ao público em geral, nos termos e nas condições previstas no regulamento do congresso.
4. Podem ser convidados ao congresso, juristas nacionais e estrangeiras de reconhecido mérito profissional ou académico, advogados de outros países, professores universitários e funcionários dos organismos internacionais que atuam nas áreas da justiça, Estado de direito, Democracia e Direitos Humanos.', 'Direitos e Deveres', 44);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (45, 'Organização', '1. O Congresso é organizado por uma comissão nomeada para o efeito, sob proposta do Bastonário, pela Direção Nacional que deverá elaborar o regulamento do Congresso e a proposta do programa.
2. A comissão organizadora do Congresso é composta pelo Bastonário, que preside, antigos bastonários, mais dez advogados indicados por este e aprovados pelo Conselho nacional
3. O secretariado do Congresso é o órgão executivo da comissão organizadora.', 'Direitos e Deveres', 45);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (46, 'Convocação e Preparação', '1. O Congresso é convocado pelo Bastonário com uma antecedência mínima de quatro meses, por meio de edital e anúncio no jornal mais lido, donde conste a ordem dos trabalhos.
2. Nos dois meses seguintes à convocação, o Bastonário promoverá a constituição da comissão organizadora do Congresso, que procede à elaboração do regulamento, tendo em conta as sugestões feitas pelos advogados e órgãos da Ordem, estabelecer o respetivo programa, do qual devem constar temas a debater.', 'Direitos e Deveres', 46);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (47, 'Conclusões', '1. O último dia do congresso é reservado exclusivamente aos advogados e convidados destacados para a adoção das conclusões.
2. Os convidados assistem os trabalhos como observadores sem direito de voto, gozando do direito de voto apenas os advogados com inscrição em vigor.

CAPITULO III
OAGB
Secção I
Disposições Gerais', 'Direitos e Deveres', 47);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (48, 'Órgãos', '1. A organização democrática do Ordem dos advogados compreende a existência dos órgãos sociais.
2. A OAGB é dotada de órgãos próprios representativos, que visam a prossecução de interesses próprios dos advogados.
3. Os órgãos principais da ordem dos advogados são; o congresso dos Advogados Guineense, a Assembleia Geral, o Bastonário, a Direção Nacional, o Conselho da Ordem, o Secretário-geral, o Conselho de deontologia e ética e o Tribunal de ética e Disciplina.
4. Poderão ser criados, de acordo com os presentes estatutos, os órgãos subsidiários ou comissões consideradas necessárias.
5. O Bastonário é por inerência o Presidente do Congresso, da Assembleia Geral, do Conselho da Ordem e da Direção Nacional.
6. É a seguinte a hierarquia dos titulares dos órgãos da OAGB:
a) O Bastonário;
b) O Presidente da Assembleia-geral;
c) O presidente do Conselho de deontologia e ética;
d) Os membros da Direção nacional;
7. O Presidente do tribunal é independente em relação aos restantes órgãos da OAGB.
Secção IV
Carácter Eletivo e Temporário do Exercício dos Cargos Sociais', 'Direitos e Deveres', 48);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (49, 'Princípios eleitorais', '1. A escolha dos advogados para ocuparem cargos nos órgãos sociais da OAGB é regida pelo princípio da democracia, autonomia e livre participação dos advogados.
2. O Bastonário e os restantes órgãos da ordem são eleitos em votação direta e secreta, democrática, justa, livre e transparente, por único mandato de três anos, em que poderão participar como eleitores todos os advogados com inscrição em vigor, com pelo menos três meses anteriores a data da Assembleia Geral para a eleição dos órgãos sociais,
3. Não é admitida a reeleição do Bastonário para um segundo mandato consecutivo nem a apresentação da sua candidatura no triénio subsequente ao termo do seu mandato.
4. Com exclusão do Bastonário, são reelegíveis em mandatos consecutivos todos os membros dos outros órgãos colegiais.
5. Não são elegíveis para os órgãos sociais os membros que se encontram em qualquer das seguintes situações:
a) Punido disciplinarmente com trânsito em julgado com pena superior à multa;
b) Funcionário publico qualquer que seja o título da sua ligação com o Estado;
c) Afastamento prolongado da profissão por mais de dois anos consecutivos ou cinco alternados por razoes de incompatibilidade ou voluntariamente até o ano imediatamente à data das eleições;
d) Os solicitadores;
e) Os membros dos órgãos de direção dos partidos políticos, associações civis ou organizações não-governamentais até o ano subsequente ao termo do respetivo mandato.
f)  Os membros oriundos da Magistratura até cinco anos de exercício efetivo', 'Direitos e Deveres', 49);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (50, 'Convocação das eleições', 'Devem ser convocadas as eleições, nos seguintes casos:
a) Quando caduca o período do mandato;
b) Quando a moção de censura contra o Bastonário for aprovada;
c) Quando por qualquer motivo, a maioria dos membros da direção nacional, deixam de participar nesse órgão.', 'Direitos e Deveres', 50);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (51, 'Da Comissão eleitoral', '1. A comissão eleitoral será integrada por cinco advogados em efetividade do exercício da atividade de advocacia, com uma antiguidade de inscrição regular não inferior a sete anos.
2. Os membros da comissão eleitoral serão nomeados, com os seus suplentes, sob proposta da direção nacional pelo Conselho nacional.
3. O mandato da comissão de eleição permanecerá vigente até a finalização do processo eleitoral para que fora designada.
4. Não poderão ser membros da comissão eleitoral os membros da Direção nacional, nem os advogados subescritores da lista dos candidatos à eleição para os órgãos sociais
5. A comissão eleitoral será presidida pelo membro mais antigo e secretário, o membro menos antigo na profissão.
6. Em caso de empate na distribuição de funções a que se refere o número anterior far-se-á mediante sorteio.
7. A comissão eleitoral deve velar pela observância de um processo eleitoral democrático e limpo, baseado nos princípios de igualdade de trato, transparência, bem como na aplicação correta das regras eleitorais vigentes aprovadas para cada processo eleitoral.
8.   Compete a comissão eleitoral desempenhar as seguintes funções:
a) Supervisionar o processo eleitoral
b) Resolver as reclamações emergentes do processo eleitoral;
c) Declarar candidaturas, excluir os candidatos em relação aos quais se verifique as causas de ilegibilidade;
d) Nomear os elementos da mesa de assembleias de voto;
e) Resolver dúvidas e omissões verificadas no decurso do processo eleitoral;
f) Velar para que o processo se decorre num clima de paz, entendimento e que todos os seus actos se ajustem ao regime dos actos eleitorais e aos princípios da publicidade, transparência e democracia.', 'Direitos e Deveres', 51);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (52, 'Apresentação de candidaturas', '1. As eleições para os diversos órgãos da OAGB dependem da apresentação de propostas de candidatura, subscritas por pelo menos 20 advogados, que devem ser efetuadas perante a comissão eleitoral, até trinta e um de outubro do ano imediatamente anterior ao início do triénio subsequente.
2. A apresentação de propostas de candidatura não pode efetuar-se nunca em período de tempo inferior a 15 dias anteriores ao acto de votação.', 'Direitos e Deveres', 52);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (53, 'Data das eleições', '1. Compete ao Bastonário marcar a data das eleições, nos termos do presente estatuto.
2. Haverá três espécies de boletins de voto, devidamente identificados no canto superior: Bastonários, Advogados e advogados estagiários.
3. Considera-se eleito a lista que obtiver a maioria dos votos validos.
4. Em caso de empate, se considera eleito a lista que obtiver mais votos dos advogados, se persistir, aquela que obtiver mais votos dos bastonários, se persistir, aquela que tiver maior número dos advogados com maior tempo de exercício, e se persistir ainda, a lista do candidato a Bastonário com maior idade.
5. O mandato nos órgãos da OAGB é de quarto anos, com início do mandato em primeiro de janeiro do ano seguinte ao da eleição, salvo os membros da mesa da Assembleia-geral.
6. Os membros da mesa da Assembleia-geral iniciam seus mandatos em 1 de Fevereiro do ano seguinte ao da eleição.', 'Direitos e Deveres', 53);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (54, 'Direito ao Voto', '1. Apenas têm direito a voto os advogados e os advogados estagiários, nos termos previstos nos presentes estatutos.
2. O voto nas eleições dos órgãos sociais é presencial e indelegável.
3. Os advogados votantes que se preveem ausentar para o estrangeiro no dia da votação, poderão ser autorizados a votar 15 dias antes da data marcada pra a votação, mediante apresentação do título de viagem.
4. Tratando-se de ausências não previsíveis para o estrangeiro, os interessados deverão logo que tenham a certeza do dia, apresentar-se perante a comissão eleitoral acompanhado do título de viagem para que sejam autorizados a votar.
5. O advogado que deixar de votar sem motivo justificado pagará multa de montante igual a 2 vezes o valor da quota mensal.
6. A justificação da falta deve ser apresentada pelo interessado, independentemente de qualquer notificação, no prazo de quinze dias a contar da data da votação, por carta dirigida ao Bastonário.', 'Direitos e Deveres', 54);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (55, 'Apresentação de Candidatura', '1. A eleição para os órgãos da OAGB depende da apresentação de propostas de candidatura, que devem ser efetuadas perante o Bastonário em exercício até 31 de Outubro do ano imediatamente anterior ao início do quadriénio subsequente.
2. As propostas são subscritas por um mínimo de quinze advogados com inscrição em vigor.
3. As propostas de candidaturas para o cargo de Bastonário e para membros das Direções deverão ser apresentadas em conjunto, acompanhadas das linhas gerais dos respetivos programas.', 'Direitos e Deveres', 55);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (56, 'Requisitos para o Cargo de Bastonário', 'São requisitos para se apresentar como candidato ao cargo de Bastonário da OAGB, os seguintes:
a) Ter 8 anos de exercício como advogado;
b) Não estar ligado por qualquer título permanente com o Estado;
c) Não ocupar cargo de direção em outras organizações civis, ou órgãos dos partidos políticos, enquanto este vínculo se mantiver.', 'Assembleias e Congressos', 56);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (57, 'Obrigatoriedade de Exercício de Funções', 'É dever do Advogado exercer nos órgãos da OAGB função para que tenha sido eleito, constituindo falta disciplinar a recusa de tomada de posse, salvo escusa fundamentada, aceite pelo Conselho Nacional da OAGB.', 'Assembleias e Congressos', 57);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (58, 'Renúncia ao Cargo, Suspensão Temporária do Exercício de Funções', 'Quando sobrevenham motivos relevantes, pode o advogado titular de cargo nos órgãos da Ordem, solicitar, mediante motivo devidamente fundamentado, ao Conselho Nacional a aceitação da sua renúncia ou a suspensão temporária do exercício de funções.', 'Assembleias e Congressos', 58);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (59, 'Perda de Cargo', '1. Perde o cargo, o advogado que, sem motivo justificado, não exerça as suas funções com assiduidade e diligência ou dificulte o funcionamento do órgão a que pertença.
2. A perda do cargo nos termos deste artigo será determinada pelo próprio órgão, mediante deliberação tomada por maioria dos votos dos respetivos membros, com base nos resultados do processo disciplinar instaurado nos termos do presente Estatuto.', 'Assembleias e Congressos', 59);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (60, 'Efeitos das Penas Disciplinares no Exercício de Cargos', '1. O mandato para exercício de qualquer cargo cessa quando o respetivo titular seja punido disciplinarmente com pena superior à de advertência e por efeito do trânsito em julgado da respetiva decisão.
2. Em caso de suspensão preventiva ou decisão de que seja interposto recurso, o titular punido fica suspenso do exercício de funções até decisão com trânsito em julgado.', 'Assembleias e Congressos', 60);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (61, 'Substituição do Bastonário', '1. No caso de escusa, renúncia, perda ou caducidade do mandato, do titular ou ainda nos casos de impedimento permanente será convocada para os quinze dias posteriores uma reunião conjunta do Conselho Nacional e Conselho de deontologia e ética a qual deliberará previamente sobre a verificação do facto e em seguida sobre a substituição.
2. Até à tomada de posse do novo Bastonário e em todos os casos de impedimento temporário, exerce as funções o membro escolhido para o efeito pelo Conselho Nacional.', 'Assembleias e Congressos', 61);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (62, 'Substituição dos Membros dos órgãos Colegiais', 'A substituição dos membros dos órgãos colegiais será efetuada pelos membros suplentes dos respetivos órgãos, conforme a ordem de precedência das respetivas listas eleitorais.', 'Assembleias e Congressos', 62);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (63, 'Mandato dos Substitutos', 'Nos casos previstos nos artigos anteriores os membros designados exercem funções até ao termo do mandato do respetivo antecessor, sendo pelo tempo de impedimento em caso de impedimento temporário.', 'Assembleias e Congressos', 63);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (64, 'Atribuições', 'Constituem atribuições da OAGB, designadamente:
1) Defender o Estado de Direito Social e Democrático, os interesses próprios, específicos e comuns, nomeadamente, os direitos, liberdades e garantias
2) as acçoes legais, relacionadas com a defesa da classe e da profissão, bem como o direito de petição para a defesa da constituição da república e das leis;
3) Defender a independência do poder judicial e colaborar na administração da justiça e na realização do direito;
4) Organizar o exercício da profissão de advocacia e exercer a jurisdição disciplinar exclusiva sobre os seus membros;
5) Assegurar o acesso aos tribunais e ao direito a todos os interessados, independentemente das suas posses, nos termos da constituição e das leis;
6) Atribuir o título profissional de Advogado e de advogado estagiário, bem como regulamentar o exercício da respetiva profissão;
7) Zelar pela função social, dignidade e prestígio da profissão de advogado, promovendo a formação inicial e permanente dos advogados e o respeito pelos valores e princípios deontológicos;
8) Defender os interesses, direitos, prerrogativas e imunidades dos seus membros;
9) Promover a solidariedade entre os advogados;
10) Exercer, em exclusivo, jurisdição disciplinar sobre os advogados e advogados estagiários;
11) Promover o acesso ao conhecimento e aplicação do direito;
12) Contribuir para o desenvolvimento da cultura jurídica e aperfeiçoamento da elaboração do direito;
13) Ser ouvida sobre os projetos de diplomas legislativos que interessem ao exercício da advocacia e ao patrocínio judiciário em geral e propor as alterações legislativas que se entendam convenientes;
14) Contribuir para o estreitamento das ligações com organismos congéneres estrangeiros;
15) Cooperar na melhoria dos estudos que conduzem a obtenção dos títulos habilitantes para o exercício da profissão de advogado;
16) Promover e defender os valores relacionados com a deontologia profissional e a aplicação rigorosa do regime disciplinar para a defesa da sociedade;
17) Colaborar com a administração pública no exercício das suas competências nos termos previstos na lei;
18) Participar nos organismos consultivos do Estado, nos termos da lei, assim como nas organizações interprofissionais;
19)  Assegurar a representação da advocacia nos conselhos sociais e nas universidades, nos termos regulados pelas normas vigentes naquelas instituições académicas;
20) Promover a harmonia e colaboração entre os advogados, fomentar a solidariedade e evitar a concorrência desleal entre os mesmos;
21) Intervir, previamente a solicitação dos interessados, como mediador nos conflitos profissionais que surjam entre os advogados ou entre estes e seus clientes;
22) Exercer a função de árbitro nos assuntos que o sejam submetidos conforme a legislação de arbitragem, assim como promover ou participar nas instituições de arbitragem;
23) Estabelecer critérios orientadores sobre os honorários profissionais, informar e determinar os honorários profissionais em processos judiciais e administrativos, ou a solicitação dos advogados;
24) Promover, organizar e colaborar, dentro da função social da advocacia, as atividades ou serviços de interesse da sociedade
25) Promover a imagem da profissão desde a perspetiva dos direitos, deveres, e princípios, e sua inserção na sociedade Guineense;
26) Dedicar especial atenção aos advogados nos seus primeiros anos de exercício facilitando, o cumprimento dos seus encargos associativos e sua formação profissional;
27) Exercer as demais funções que resultem das disposições deste Estatuto ou de outros preceitos legislativo.
28) As demais que venham a ser atribuídas pela legislação nacional;
29) O disposto no número anterior concretiza-se pelo respeito ao estatuto, e pelo regime estatutariamente definido dos órgãos em matéria de funcionamento e competência.', 'Assembleias e Congressos', 64);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (65, 'Dever de colaboração', '1. No cumprimento das suas atribuições e competências legais e estatutárias, todas as entidades públicas, autoridades judiciárias e policiais, bem como os órgãos administração pública em geral, têm o especial dever de prestar total colaboração aos órgãos da ordem dos Advogados, no exercício das suas funções.
2. Os particulares sejam pessoas singulares ou coletivas, os organismos nacionais ou estrangeiras, sejam elas as Organizações não-governamentais ou internacionais estaduais com sede ou domicílio profissional no território da Guiné-Bissau têm o dever de colaboração com os órgãos da Ordem dos Advogados no exercício das suas atribuições.', 'Assembleias e Congressos', 65);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (66, 'Garantia Geral de comunicação e requisição oficial de Documentos', 'Na prossecução das suas atribuições podem os órgãos da Ordem corresponder-se com as entidades referidas no artigo anterior e tribunais e, bem assim, requisitar, sem pagamento de despesas, cópias, certidões, informações e esclarecimentos, incluindo a remessa de processo em confiança, nos termos em que os organismos oficiais devem satisfazer as requisições dos tribunais judiciais.


Secção V', 'Assembleias e Congressos', 66);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (67, 'Representação', '1. A OAGB é representada em juízo e fora dele pelo Bastonário que poderá delegar os seus poderes em qualquer dos seus Vice-presidentes.
2. Sempre que se trate de assuntos ou questões no interior do país, a Ordem poderá fazer-se representar, em juízo ou fora dele, pelos delegados das regiões judiciais.', 'Assembleias e Congressos', 67);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (68, 'Representação da OAGB nas regiões judiciais', '1. A OAGB é representada nas regiões judiciais pelas delegações
2. As deleções são compostas por um presidente, que dirige, um vice-presidente, um secretário e dois vogais, nomeados pela direção Nacional, sob proposta do Bastonário.', 'Assembleias e Congressos', 68);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (69, 'Competência das delegações', '3. Compete as delegações da OAGB nas regiões judiciais:
a) Assegurar as relações da OAGB com os órgãos da administração local nas regiões e tribunais.
b) Zelar pelo cumprimento, na respetiva região judicial, das normas que rege a ordem e o exercício da profissão;
c) Registar e controlar os escritórios dos advogados nas regiões;
d) Executar e fazer executar as deliberações da Direção nacional
e) Coordenar a atividade dos membros da OAGB com domicílio profissional na respetiva região judicial;
f) Elaborar e aprova o seu regulamento;
g) Solicitar e receber informações sobre assuntos de interesse para a OAGB nas regiões
h) Assegurar o patrocínio oficioso nas respetivas regiões, sob a coordenação e orientação Direção Nacional', 'Assembleias e Congressos', 69);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (70, 'Competência do presidente da delegação', 'Compete ao presidente da Delegação:
a) Representar o Bastonário na Região;
b) Convocar as reuniões da delegacia;
c) Dirigir e coordenar as atividades da Ordem na Região;
d) Exercer os demais poderes que lhe sejam atribuídos por lei, pelo regulamento ou por delegação...


Secção VI
Congresso dos Advogados Guineenses', 'Assembleias e Congressos', 70);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (71, 'Constituição e periodicidade', '1. O congresso é o plenário de todos os advogados com inscrição em vigor.
2. Poderão participar no Congresso os advogados honorários e os antigos advogados cuja inscrição tenha sido cancelada por efeito de reforma.
3. O congresso realiza-se ordinariamente de três em três anos para debruçar-se sobre os temas relacionados com o exercício da advocacia, seu estatuto e garantias dos advogados no estado de Direito Democrático, bem como as garantias de defesa e aperfeiçoamento do regime geral do exercício da Advocacias.
4. Podem ser convidados ao congresso, juristas nacionais e estrangeiros de reconhecido mérito profissional ou académico de outros países, instituições representativas dos profissionais que operam no domínio da advocacia ou domínios a fins.
5. As conclusões do congresso terão caracter orientador para os órgãos social da OAGB.', 'Assembleias e Congressos', 71);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (72, 'Convocação, Organização e preparação,', 'O congresso é convocado pelo Bastonário com antecedência mínima de quatro meses, aplicando com as necessárias adaptações as normas relativas a organização e preparação do Congresso Nacional da advocacia.
SECÇÃO III
Assembleia Geral', 'Assembleias e Congressos', 72);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (73, 'Composição e Competência', '1. A Assembleia Geral é composta por todos os membros da OAGB, com inscrição em vigor e sem quotas atrasadas, pelo menos três meses anteriores à data da realização da Assembleia respetiva, em pleno gozo dos seus direitos.
2. Compete a Assembleia Geral discutir, apreciar e deliberar sobre todos os assuntos relativos à OAGB, que não estejam compreendidos nas competências específicas dos outros órgãos da Ordem, nomeadamente:
a) Aprovar o seu regulamento interno;
b) Eleger e destituir a respetiva mesa, bem como os outros órgãos da OAGB.
c) (mantem-se) Aprovar o relatório e plano de atividade, as contas e orçamento geral da OAGB;
d) Apreciar a atividade dos demais órgãos da OAGB, podendo modificar, revogar ou ratificar quaisquer actos dos mesmos, sem prejuízo dos direitos de terceiros, nos termos da lei
3. Compete a Assembleia-geral em especial, na primeira sessão ordinária, do primeiro trimestre do ano, deliberar sobre a seguinte ordem do dia:
a) Apreciar e votar o relatório do Bastonário sobre os acontecimentos mais importantes ocorridos durante o ano anterior relacionados com a justiça, o Estado de Direito e o exercício da profissão;
b) Apreciação e aprovação das contas anuais do exercício anterior;
c) A presentação, discussão e votação dos assuntos que o Conselho Nacional tenha inscrito na convocatória;
d) Propostas;
e) Perguntas e respostas;
4. Compete a Assembleia-geral na segunda sessão, dentro do último trimestre do ano, deliberar sobre a seguinte ordem do dia:
a) Exame e aprovação do orçamento geral da OAGB para o exercício seguinte;
b) Apresentação, discussão e aprovação dos assuntos que o Conselho Nacional tenha inscrito na ordem do dia;
c) Propostas;
d) Perguntas e respostas.', 'Órgãos de Governação', 73);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (74, 'Mesa da Assembleia Geral', 'A mesa da Assembleia Geral é composta pelo Bastonário que preside, um vice-presidente, um secretário e um vogal, eleitos pela Assembleia-geral.', 'Órgãos de Governação', 74);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (75, 'Reuniões da Assembleia Geral', '1. As reuniões ordinárias da Assembleia Geral realizar-se-ão, salvo justo impedimento, em Dezembro de cada ano.
2. As reuniões da Assembleia Geral são convocadas pelo Bastonário, que por inerência é presidente da mesa, competindo-lhe dirigir as sessões, velar para um bom desenrolar dos trabalhos, designadamente, moderação, uso da palavra e oportunidade para submeter a votação um assunto quando suficientemente debatido.
5. As reuniões extraordinárias da Assembleia Geral realizar-se-ão sempre que os interesses superiores da OAGB o aconselhem, convocadas pelo Bastonário, a pedido Conselho da Ordem, Direção Nacional, pela mesa da Assembleia Geral, por um terço de advogados com a inscrição em vigor, desde que seja legal o objeto da convocação e relacionado com os interesses da profissão.
6. As reuniões da Assembleia Geral destinadas à discussão e votação do relatório de atividade e contas da OAGB realiza-se até ao final do mês de Abril do ano imediato ao do exercício respetivo.
7. A Assembleia Geral destinada à discussão e aprovação do orçamento da Ordem reúne até ao final do mês de Novembro do ano anterior ao exercício a que diz respeito.', 'Órgãos de Governação', 75);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (76, 'Convocatória', '1. As convocatórias das Assembleias-gerais assumem a forma de edital afixada na sede da ordem em lugar bem visível, correio eletrónico dirigido aos escritórios dos advogados e anúncio num dos jornais nacionais mais lido, donde conste a ordem dos trabalhos com pelo menos trinta dias de antecedência em relação a data designada para a sua realização, constando na convocatória o local, dia e a hora.
2. Até vinte dias antes da realização das Assembleias a que se referem os números 6 e 7 do artigo anterior, é enviado aos advogados com inscrição em vigor os projetos de orçamento e do relatório e contas.
3. Com os avisos convocatórios de Assembleias Gerais cuja ordem dos trabalhos compreenda a realização de eleições são enviados os boletins com informação de todos os candidatos admitidos.
4. Para efeito de validade das deliberações da Assembleia Geral, só são consideradas essenciais as formalidades da convocatória referida no número 3.', 'Órgãos de Governação', 76);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (77, 'Constituição e quórum', '1. A Assembleia Geral é constituída validamente em primeira convocatória com a presença de pelo menos mais de metade dos seus membros, incluindo os representados e em segunda convocatória, um terço., não podendo nenhum advogado representar mais de uma pessoa em cada reunião.
2. A assembleia geral só poderá deliberar validamente com a presença de pelo menos, mais de metade dos seus membros.
3. Para efeito do disposto no número anterior, o número de representações não poderá exceder um quarto do número exigido no número no n° 1 deste artigo.
Subsecção I
Assembleia Geral Eletiva', 'Órgãos de Governação', 77);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (78, 'Reuniões da Assembleia Geral Eletiva', 'A Assembleia-geral eletiva reúne para a eleição dos órgãos sociais da Ordem., no período compreendido entre 1 e 15 de Dezembro, do último ano do mandato, em data a ser designada pelo Bastonário.', 'Órgãos de Governação', 78);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (79, 'Não convocação ou impossibilidade da assembleia-geral eletiva', '1. Quando não tenha sido convocada a Assembleia Geral eletiva no prazo fixado no estatuto por denegação do direito de renovação democrática dos mandatos, impossibilidade de constituição da Assembleia, ou ainda por falta de apresentação de lista de candidaturas ou por estas terem sido rejeitadas, proceder-se-á da seguinte forma:
a)  Nomeação pelo Conselho da Ordem de uma comissão diretiva nacional, no caso de falta de apresentação de listas de candidaturas;
b) Marcação pelo Conselho Nacional de eleições, a realizar no prazo máximo de noventa dias, no caso de rejeição da totalidade das listas apresentadas;
c) Marcação das eleições pelo Conselho Nacional, no caso de não convocação, no prazo de 90 dias, contados a partir da data de caducidade do mandato.
2. Na nomeação prevista na alínea a) do número anterior, o Conselho Nacional deverá ter em consideração o resultado dos antigos bastonários eleitos, em caso de empate, o bastonário com melhor desempenho.
3. Na ponderação dos resultados a que se refere o número anterior, deve observar-se o seguinte.
a) Verificação do número de eleitores com capacidade eleitoral ativa nas respetiva eleições;
b) O número de votantes;
c) O número de abstenções registadas;
1. Feita o apuramento nos termos previstos no numero anterior, aplica-se a regra de proporção sobre os resultados obtidos, nomeado direção Nacional que será pelos elementos escolhidos pelo Bastonário vencedor.,', 'Órgãos de Governação', 79);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (80, 'Direito de Voto', '1. O voto nas Assembleias Gerais é facultativo, salvo se para fins eletivos e para os previstos nos números 3 e 4 do artigo 49.º.
2. O voto, quando é facultativo, não pode ser exercido por correspondência, sendo, no entanto, admissível o voto por procuração a favor de outro advogado com inscrição em vigor.


Secção IV
Conselho da Ordem', 'Órgãos de Governação', 80);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (81, 'Composição', 'O Conselho Nacional é presidido pelo Bastonário, composto por três vice-presidentes e 9 vogais, eleitos diretamente pela assembleia-geral, todos os presidentes dos outros órgãos, antigos bastonários e as Delegações regionais judiciais', 'Órgãos de Governação', 81);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (82, 'Estrutura e funcionamento', '1. O Conselho da Ordem terá um Plenário, uma Comissão Permanente e Comissão permanente restrito.
2. A Comissão Permanente integra todos os antigos Bastonários e os membros da Direção Nacional.
3. A Comissão permanente restrito é o órgão facultativo de consulta do Bastonário sobre as matérias da sua competência reservada.
4. Na primeira sessão o conselho da Ordem elege, de entre os seus vogais, as delegações regionais e o seu secretário
3. As delegações regionais, os presidentes das comissões especializadas, o presidente do gabinete de acesso ao direito, estudos e documentação da OAGB, têm assento próprio nas reuniões do Conselho da Ordem.', 'Órgãos de Governação', 82);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (83, 'Competência', '1. Compete ao Conselho da Ordem reunido em plenário:
a) Definir a posição da ordem perante os órgãos de soberania e da administração pública no que se relacione com a defesa do Estado de direito, dos direitos, liberdades e garantias e com a administração da justiça;
b) Deliberar e votar a petição de fiscalização concreta da constituição, bem como o direito de petição para a defesa dos direitos, garantias e prerrogativas relativas ao exercício de atividade de advogados.
c) Aprovar os pareceres sobre os projetos de diploma legislativos que interessam ao exercício e ao patrocínio judiciário em geral;
d) Aprovar as propostas de alterações legislativas que respeitem ao exercício da profissão, aos interesses dos advogados e à gestão da ordem dos advogados que não sejam especialmente cometidos a outros órgãos da Ordem;
e) Aprovar prémios ou distinções para os advogados
f) Confirmar a inscrição dos advogados e advogados estagiários efetuadas preparatoriamente pela Direção Nacional e manter atualizado o respetivo quadro nacional, bem como os dos advogados honorários;
g) Julgar os recursos interpostos das deliberações da Direção Nacional;
h) Deliberar sobre pedidos de recusa, renúncia ou de suspensão temporária de cargos;
i) Conhecer oficiosamente ou mediante petição de qualquer advogado dos vícios das deliberações da Assembleia Geral;
j) Elaborar e aprovar o seu próprio regulamento;
k) Deliberar sobre o impedimento, substituição e perda do cargo dos seus membros e suspendê-los preventivamente, em caso de falta disciplinar, no decurso do respetivo processo;
l) Definir a posição da Ordem perante os órgãos de soberania e da administração no que se relaciona com a defesa do estado de direito, dos direitos e garantias individuais e com a administração da justiça;
m) Emitir pareceres sobre os projetos de diplomas legislativos que interessem ao exercício da advocacia e ao patrocínio judiciário em geral e propor alterações legislativas convenientes;
n) Deliberar sobre todos os assuntos que respeitem ao exercício da profissão, aos interesses dos advogados e à gestão da Ordem.
o) Elaborar e aprovar o regulamento da inscrição dos advogados, advogados estagiários bacharéis e solicitadores, o regulamento do estágio, dos laudos do trajo e da insígnia profissional;
p) Deliberar sobre os impedimentos de exercício da profissão;
q) Elaborar e aprovar outros regulamentos, designadamente os dos diversos serviços da Ordem, os relativos às atribuições e competência do seu pessoal e os relativos à contratação e despedimento de todo o pessoal;
r) Formular recomendações de modo a procurar uniformizar, tanto quanto possível, a atuação das diversas Delegacias;
s) Discutir e aprovar os pareceres dos seus membros e os solicitados pelo Bastonário a outros advogados;
t) Fixar o valor das cotas a pagar pelos membros da Ordem dos Advogados, nomeadamente os advogados e os solicitadores;
u) Fixar emolumentos devidos pela emissão de documentos ou prática de atos no âmbito dos serviços da Ordem, designadamente pela inscrição dos advogados, dos advogados estagiários dos bacharéis e dos solicitadores;
v) Arrecadar e distribuir receitas da Ordem, satisfazer despesas, aceitar doações e legados feitos à Ordem e administrá-los, alienar ou obrigar bens e contrair empréstimos;
w) Prestar patrocínio aos advogados que hajam sido ofendidos no exercício das suas funções ou por causa dele;
x) Dar laudos sobre honorários, quando solicitados pelos tribunais, pelo Conselho Jurisdicional ou em relação às respetivas contas, por qualquer advogado ou seu representante;
y) Deliberar sobre a instauração ou defesa de quaisquer procedimentos judiciais relativos a Ordem e sobre confissão, desistência ou transação dos mesmos;
z) Deliberar sobre a realização do Congresso;
aa) Exercer as demais funções que as leis e os regulamentos lhe confiram.
4. Compete a Comissão permanente do Conselho da Ordem intervir mediante nomeação de quatro dos seus membros para a constituição do plenário do tribunal de ética e disciplina, na decisão das seguintes matérias:
a) Julgamento dos processos disciplinares em que sejam arguidos o Bastonário, os antigos Bastonários e os membros das Delegacias;
b) Julgamento dos recursos das deliberações sobre perda do cargo e exoneração dos membros das Direções;
c) Deliberação sobre a renúncia do cargo de Bastonário;
d) Substituição do Bastonário no caso de impedimento permanente;
e) Atribuição da medalha de honra aos advogados a cidadãos nacionais ou estrangeiros que tenham prestado serviços relevantes na defesa do Estado de direito ou à causa da advocacia;
f) Conferir título de advogado honorário a advogados que tenham deixado a advocacia depois de a haverem exercido distintamente durante 15 anos, pelo menos, e se tenham assinalado como juristas eminentes.', 'Órgãos de Governação', 83);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (84, 'Reuniões', '1. O Conselho da Ordem reúne-se, ordinariamente, pelo menos uma vez por mês e extraordinariamente por iniciativa do Bastonário ou mediante solicitação da maioria absoluta dos seus membros.
2. Sempre que o Presidente do Conselho da Ordem não esteja presente, o voto de qualidade assiste ao vice-presidente que presida à respetiva reunião
SECÇÃO IV
Bastonário', 'Órgãos de Governação', 84);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (85, 'Bastonário', '1. O Bastonário é por inerência, presidente do Congresso, da Assembleia Geral e do Conselho da Ordem.
2. Sempre que o Bastonário julgar conveniente, poderá convocar as reuniões das comissões especializadas, competindo-lhe dirigir os trabalhos e votações.
3. O Bastonário tem voto de qualidade em caso de empate.', 'Órgãos de Governação', 85);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (86, 'Competência', '1. Compete ao Bastonário:
a) Representar a Ordem em juízo e fora dele, designadamente perante os órgãos de soberania;
b) Proferir discursos em representação da OAGB nas cerimónias oficiais;
c) Nomear o Secretario Geral da OAGB;
d) Supervisionar os serviços da OAGB
e) Velar pelo cumprimento da legislação respeitante à Ordem dos advogados, e respetivos regulamentos e zelar pela realização das suas atribuições;
f) Fazer executar as deliberações do Congresso, do Conselho da Ordem, do tribunal de deontologia e disciplina e dar seguimento às recomendações do congresso
g) Promover a cobrança das receitas da Ordem, autorizar despesas e promover a abertura de créditos extraordinários, quando necessários;
h) Apresentar anualmente à Assembleia Geral o projeto de orçamento para o ano civil seguinte, as contas do ano civil anterior e o relatório sobre as atividades anuais;
i) Promover, por iniciativa própria ou a solicitação das Direções, os atos necessários ao patrocínio dos Advogados ou para que a Ordem se constitua assistente;
j) Cometer a quaisquer órgãos da Ordem ou aos respetivos membros a elaboração de pareceres sobre quaisquer matérias que interessem às suas atribuições;
k) Assistir, querendo, às reuniões de todos os órgãos colegiais da Ordem, não tendo direito de voto nas reuniões do Conselho Jurisdicional;
l) Usar o voto de qualidade, em caso de empate, nos órgãos colegiais que preside;
m) Encaminhar para o Conselho Jurisdicional as deliberações de todos os órgãos que julgue contrárias as leis e regulamentos ou aos interesses da Ordem ou dos membros
n) Exercer as demais atribuições que as leis e regulamentos lhe confiram;
o) Manter atento sobre as eventuais omissões e a necessidade de ajustar os estatutos a dinâmica de defesa do exercício da atividade da advocacia e promover por sua iniciativa a revisão dos mesmos;
2. O Bastonário pode delegar em qualquer membro do Conselho da Ordem alguma ou algumas das suas atribuições.
3. O Bastonário pode também, com o acordo do Conselho da Ordem delegar a representação da Ordem ou atribuir funções específicas a qualquer advogado que integre o Conselho da Ordem.
4. O Bastonário pode ainda consultar os antigos Bastonários ou em reuniões por ele presididas, e delegar neles a sua representação, contanto que nenhum outro membro do Conselho da Ordem esteja disponível para tanto.', 'Órgãos de Governação', 86);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (87, 'Moção de censura', '1. Qualquer advogado ou grupo de advogados com inscrição em vigor e em pleno exercício dos seus direitos, sem penalização anterior superior a repreensão registada poderá liderar a iniciativa de apresentação de moção de censura contra o Bastonário mediante proposta sobescrita por um numero de advogados, não inferior à 20%, com regular inscrição, em efetividade de exercício, com pelo menos 1 ano de exercício anterior a data da apresentação da moção, com indicação expressa e descriminada das razoes em que se funda a censura.
2. Não é permitido apresentar a moção de censura nos seis meses seguintes a da data da tomada de posse.
3. Cumpridos os requisitos previstos nos números anteriores, o presidente do Conselho da Ordem deverá convocar a Assembleia geral extraordinária no prazo na superior a quinze dias contados desde a data da apresentação da moção.
4. A convocatória da Assembleia geral, para o debate da moção de censura, deverá efetuar-se com antecedência mínima de vinte dias e máxima de trinta dias.
5. A Assembleia geral convocada para a finalidade deste artigo, considera-se validamente constituída na primeira convocatória se estiverem presentes mais de metade dos subscritores, mais pelo menos 30% dos advogados com direito de voto.
6. Se não se atingir o quórum exigido no úmero anterior, poderá em segundo convocatória, realizar a assembleia com a presença de mais de metade dos subscritores e 20% dos advogados com direito de voto, podendo as duas convocatórias ser simultâneas numa única convocatória.
7. O debate iniciará com a apresentação da moção pelo proponente ou proponentes seguindo a defesa do censurado, podendo o Bastonário, querendo e com o acordo de nomeado, contestar a moção.
8. A Assembleia-geral extraordinária poderá desenrolar-se em uma ou mais secções para debate e votação.
9. Para a aprovação da moção de censura será necessário o voto favorável de mais de metade dos advogados presentes na assembleia.
10. Se a moção não obtiver aprovação, não poderá ser apresentada uma segunda dentro de um ano, contado a partir da data da apresentação da primeira moção.
11. Aprovada a moção cessará de imediato o mandata de todos os órgãos sociais eleitos, devendo ser convocada e marcada a data de eleições na mesma Assembleia.
SECÇÃO V
Direção Nacional', 'Órgãos de Governação', 87);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (88, 'Natureza e Composição', '1. A Direção Nacional é o órgão executivo da Ordem.
2. A Direção Nacional é constituída pelo Bastonário que preside, com voto de qualidade, três vice-presidentes, dois vogais, eleitos pela Assembleia Geral, um Secretario Geral, um tesoureiro e um bibliotecário, nomeados pela direção Nacional sob proposta do Bastonário.
3. A direção Nacional reúne sempre que convocado pelo Bastonário ou a pedido de pelo menos três dos seus membros eleitos na Assembleia-geral.
4. O Secretario Geral é o principal funcionário Administrativo da Ordem.
5. O Direção nacional tem a seguinte estrutura
a) Direção executiva, e
b) Conselho diretivo:
5. A direção executiva integra unicamente os membros eleitos e o Secretario Geral,
6. Nas reuniões da direção executiva o Secretario Geral não tem direito a voto.
7. Compete a direção executiva:
a) Elaborar o seu próprio regulamento;
b) Elaborar e submeter para aprovação ao Conselho da Ordem a proposta do regulamento de inscrição dos advogados e dos advogados estagiários;
c) Elaborar e submeter para aprovação ao Conselho Nacional a proposta do regulamento do estágio, da formação continua e da formação especializada, com inerente atribuição do título de advogado especialista;
d) Homologar as inscrições dos advogados e advogados estagiários;
e) Aprovar a proposta de orçamento e do relatório de atividades e contas;
f) Propor ao Conselho da Ordem a criação de institutos, observatórios, departamentos ou comissões especializadas,
g) Propor o valor das quotas a pagar pelos advogados;
h) Propor os valores de emolumentos devidos pela emissão de documentos ou prática de atos no âmbito dos serviços da Ordem dos Advogados, designadamente pela inscrição dos advogados e advogados estagiários;
6. O Conselho técnico integra a direção nacional, o secretario Geral, presidentes das comissões especializadas e o presidente do gabinete de acesso ao direito, estudos e documentação da OAGB.
7. Compete ao Conselho técnico:
a) Preparar as reuniões do conselho da Ordem;
b) Deliberar em recurso das decisões do Bastonário sem recurso;
c) A supervisão técnica e científica das atividades da OAGB;
d) A definição da proposta da ordem do dia do Conselho Nacional;
e) A aprovação preliminar do relatório de atividade e contas da direção nacional
f) Opinar sempre que solicitado pelo Bastonário nos assuntos da sua competência própria;
8. O Bastonário poderá convidar os antigos Bastonários para as reuniões do Conselho Diretivo', 'Órgãos de Governação', 88);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (89, 'Secretário-geral da OAGB', '1. A Secretário-geral é o principal funcionário Administrativo da OAGB, o qual é assistido pelo número de pessoal indispensável para assegurar a boa Administração da OAGB, a quem cabe elaborar o relatório anual sobre as atividades da OAGB.
2. O Secretario Geral é quem por inerência assegura o secretariado de todas as reuniões da Direção Nacional, e fiel depositário de todas as actas e relatórios do Conselho Nacional, Assembleia Geral, Congresso Nacional da Advocacia e do Congresso nacional dos Advogados Guineenses.
3. No exercício das suas competências, compete ao Secretário-geral fazer propostas de contração do pessoal administrativo da OAGB, sob a sua direção e disciplina.
4. Compete em especial ao Secretário-geral no exercício da sua função administrativa, superintender, nomeadamente:
a) Os actos gerais da secretária-geral da OAGB
b) A realização de inscrições dos advogados e advogados estagiários na OAGB;
c) Zelar pela boa administração geral da ordem.
d) A boa organização do expediente geral da Ordem;
e) A Recepçao e expedição das correspondências da Ordem;
f) A boa conservação e desenvolvimento do património geral da Ordem;


SECÇÃO VI
Tribunal de ética e disciplina', 'Órgãos de Governação', 89);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (90, 'Composição', '1. O tribunal de ética e disciplina é o órgão jurisdicional da Ordem dos Advogados, composto por um presidente, um vice-presidente e mais três conselheiros.
2. O Presidente e vice-presidente são escolhidos de entre os antigos bastonários, incluindo o bastonário cessante.', 'Órgãos de Governação', 90);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (91, 'Função jurisdicional', '1. Na administração da justiça incumbe ao tribunal de ética e disciplina assegurar a defesa dos estatutos da OAGB, regulamentos internos, a deontologia própria da profissão e disciplina.
2. O regulamento interno do tribunal é aprovado pela Assembleia Geral da OAGB', 'Órgãos de Governação', 91);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (92, 'Decisões do tribunal', '1. As decisões do tribunal são fundamentadas nos casos e nos termos previstos na lei substantiva da OAGB e no processo disciplinar.
2. As decisões do tribunal com trânsito em julgado são obrigatórias, vinculando todas as entidades públicas e privadas, incluindo os tribunais judiciais.', 'Órgãos de Governação', 92);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (93, 'Competência', '1. Compete ao tribunal de ética e disciplina, reunido em sessão plenária:
a) Julgar os processos disciplinares instaurados pelo Conselho de deontologia e ética;
b) A fiscalização concreta dos estatutos e dos regulamentos da OAGB, declarando contrários aos estatutos actos ou normas que infrinja o disposto neles ou princípios neles consignados;
c) Verificar o pedido de escusa, de renúncia e de suspensão temporária de cargo, procedendo a sua homologação;
d) Julgar os recursos das decisões dos órgãos da Ordem dos Advogados que determinarem a perda de cargo de qualquer dos seus membros;
e) Verificar impedimentos e incompatibilidades e declara-los a pedido do Conselho de deontologia e ética
f) Determinar a suspensão preventiva, em caso de falta disciplinar, no decurso do respetivo processo;
2. Compete ao tribunal de ética e disciplina e os membros permanentes do conselho Nacional, em reunião conjunta:
a) Verificar em recurso a perda do cargo e exoneração dos membros do conselho Nacional e do conselho de deontologia e ética;
b) Verificar a renúncia ao cargo de bastonário;
c) Julgar os processos em que sejam arguidos o bastonário, antigos bastonários e os membros atuais do conselho nacional e do conselho de deontologia e ética;
3. Ficam sujeitos ao regime de impugnação das deliberações sociais, regulado no código do processo civil, todas as deliberações do Conselho Nacional e da Assembleia Geral da OAGB, não mencionadas neste artigo;
SECÇÃO VI
Conselho de deontologia e ética', 'Órgãos de Governação', 93);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (94, 'Composição', '1. O Conselho de deontologia e ética é órgão de fiscalização da observância das regras de deontologia profissional, composto por um antigo bastonário que o preside, com voto de qualidade, por dois vice-presidentes e dois vogais eleitos pela Assembleia Geral.
2.  Na primeira sessão, o Conselho elege, de entre os dois vogais, um secretário, preferencialmente mais jovem.
3. Sempre que o Presidente do Conselho não esteja presente, o voto de qualidade assiste ao vice-presidente que presida à respetiva reunião.', 'Órgãos de Governação', 94);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (95, 'Competências', '1. Compete ao Conselho de deontologia e ética:
i) Instruir os processos de mediação em que sejam partes os advogados ou advogados e cidadãos;
j) Julgar em primeira instância os conflitos de honorários mediante processo de arbitragem
k)  Apreciar e deliberar sobre a verificação de impedimentos e incompatibilidades;
l) Fiscalizar o cumprimento das normas estatutárias e regulamentares, relativos ao exercício da profissão, cumprimento das recomendações da Assembleia Geral e Conselho Nacional relativos a observância das regras de deontologia profissional, emitindo sobre os mesmos os respetivos pareceres;
m) Submeter os pareceres, conforme os casos, à Assembleia Geral e o tribunal de deontologia e disciplina', 'Órgãos de Governação', 95);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (96, 'Reuniões', 'O Conselho de deontologia e ética reúne sempre que for convocado para o exercício das suas competências e, ordinariamente, uma vez em cada trimestre, ou ainda por iniciativa do Presidente ou mediante solicitação por escrito da maioria absoluta dos seus membros.', 'Órgãos de Governação', 96);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (97, 'Distinção e permeios', '1. O Conselho da Ordem poderá conferir distinções de honra, nos termos do estatuto, aos advogados que se destacaram pelos serviços relevantes prestados à Ordem ou a causa da advocacia em geral.
2. O Conselho da Ordem poderá igualmente conferir tal distinção aos advogados com antiguidade superior a cinquenta anos, sempre que durante a sua experiencia profissional não conste qualquer sanção disciplinar no seu curriculum.
TÍTULO III
DO EXERCÍCIO DA ADVOCACIA
CAPITULO I
Disposições gerais
Secção I', 'Órgãos de Governação', 97);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (98, 'Atividade da advocacia', '1. O exercício da atividade de advocacia no território da Guiné-Bissau é reservado exclusivamente aos Advogados e Advogados estagiários com inscrição em vigor na OAGB
2. Os advogados estagiários são obrigados a indicação dessa qualidade em todas as suas intervenções oficiais.
3. Só os advogados e advogados estagiários, a que se refere o número anterior, podem em todo o território nacional, exercer a atividade de advocacia perante qualquer jurisdição, instância, autoridade ou entidade pública ou privada, praticar actos próprios da profissão e, designadamente, exercer o mandato judicial ou funções de consulta jurídica em regime de profissão liberal remunerada.
4. A profissão de advogado é exercida em regime de profissão liberal, independência e rege-se pelo presente estatuto, as regras de deontologia e ética e pelas demais disposições legais aplicáveis.', 'Exercício da Advocacia', 98);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (99, 'Liberdade de exercício', '1. O exercício da atividade da advocacia é livre e independente, sendo inviolável o advogado no seu exercício profissional, seus actos e manifestações, salvo disposição expressa da lei.
2.  Não é permitido as autoridades públicas ou privada, entidade, instância ou jurisdição, conjunta ou separadamente impedir o livre exercício do mandato forense por qualquer forma, salvo nos casos previstos na lei.
3. Aos advogados, quando no exercício profissional, devem ser dispensados tratamento compatível com a dignidade da profissão e condições adequadas para o cabal desempenho da função, de acordo com as garantias consignadas no Estatuto da Ordem dos Advogados e demais legislação aplicável.
4. O advogado que for vítima de atentado ou agressão contra as suas garantias ao livre exercício da profissão, deve denunciar o facto ao Bastonário, a quem compete tomar diligências que se revelarem adequadas para restaurar a dignidade e o respeito da profissão.
5. Para o cumprimento dos fins previstos no número anterior, o Bastonário apos recebido a participação, em articulação com a Comissão de acesso a justiça, julgando necessário, designará o advogado ou advogados, investidos em poderes bastantes para o efeito.
6. Os advogados devem ser assegurados, em todos os tribunais, juízos, cartórios do Ministério publico, esquadras de polícia ou órgãos de polícia de Ordem Publica, polícia judiciária, salas especiais permanentes para a prática de actos urgentes indispensáveis a boa administração da justiça, ficando sob a disponibilidade e responsabilidade da OAGB.
7. Quando um advogado for agredido, no exercício da profissão ou em desempenho do cargo da OAGB, o Conselho Nacional deve fazer um pronunciamento publico a favor do agredido, sem prejuízo da responsabilidade criminal do infrator.
8. Correndo inquérito contra um advogado por haver fortes indícios materiais da prática de um crime, mediante despacho motivado do juiz, poderá este determinar a suspensão do direito previsto no n° 4 al. b) do artigo 6° dos presentes estatutos, e emitir o mandado de busca e apreensão, específico e pormenorizado, que será cumprido na presença de um representante da OAGB.
9. Não obstante, o mandado de busca não pode abranger os documentos de clientes, os objetos pertencentes aos clientes do advogado, bem como dos demais instrumentos de trabalho com informações dos clientes.
10.   Fica excluído do âmbito da aplicação do número anterior, os abjetos e documentos do cliente formalmente constituído suspeito pela prática do crime que deu causa a suspensão da inviolabilidade do escritório do advogado.', 'Exercício da Advocacia', 99);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (100, 'Função forense', '1. A função forense, consiste no exercício da atividade por lei reservada aos advogados, incumbindo-lhes, nos termos da lei, a defesa dos direitos, interesses e garantias individuais dos cidadãos, requerendo, em caso de necessidade, a intervenção dos órgãos policiais e jurisdicionais competentes.
2. Os advogados estagiários praticam os atos próprios dos advogados, nos termos regulados no presente diploma e no regulamento do estágio.', 'Exercício da Advocacia', 100);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (101, 'Estatuto legal do Advogado', '1. O advogado goza, em todas as instituições, autoridades, órgãos, instâncias, na administração da justiça e na administração pública em geral, de imunidade para cumprir o mandato e desempenhar as suas funções cabalmente.
2. As Autoridades, os servidores públicos e os funcionários da justiça devem dispensar ao advogado, no exercício da profissão, tratamento compatível com a dignidade da advocacia e condições adequadas ao seu desempenho profissional.
3. Os advogados são independentes face aos órgãos da Administração da justiça, sendo inviolável no exercício de sua atividade, dentro do respeito à Constituição da república e às leis.', 'Exercício da Advocacia', 101);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (102, 'Mandato forense', '1. O mandato forense é documento através do qual o advogado é conferido pela parte interessada, nos termos da legislação em vigor, poderes para o representar ou intervir em juízo ou fora dele nos assuntos do seu interesse próprio, agindo por si livremente, nos termos permitidos por lei.
2. Em caso de urgência, o advogado pode atuar sem mandato forense, obrigando-se a apresentá-lo no prazo de cinco dias, prorrogável por igual período.
3. O advogado que renunciar ao mandato continuará, durante os dez dias seguintes à notificação da renúncia, a representar o mandante, salvo se for substituído antes do término desse prazo, comunicando, após, o juiz.
4. No exercício do mandato, os advogados devem agir com total independência e autonomia técnica e de forma isenta e responsável, encontrando-se apenas vinculados a critérios de legalidade e às regras deontológicas próprias da profissão.', 'Exercício da Advocacia', 102);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (103, 'Deveres para com a Comunidade', 'Constituem deveres do advogado para com a comunidade:
a) Pugnar pela boa aplicação das leis, pela rápida administração da justiça e pelo aperfeiçoamento das instituições jurídicas;
b) Não advogar contra lei expressa, não usar de meios ou expedientes ilegais, nem promover diligências reconhecidamente dilatórias, inúteis ou prejudiciais para a correta aplicação da lei ou a descoberta da verdade;
c) Recusar o patrocínio a questões que considere injustas;
d) Colaborar no acesso ao direito e aceitar nomeações oficiosas nas condições fixadas na lei e pela Ordem;
e) Protestar contra as violações dos direitos humanos e combater as arbitrariedades de que tiver conhecimento no exercício da profissão;
f) Não solicitar nem angariar clientes, por si nem por interposta pessoa;
g) Não aceitar mandato ou prestação de serviços profissionais que, em qualquer circunstância, não resulte de escolha direta e livre pelo mandante ou interessado.', 'Exercício da Advocacia', 103);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (104, 'Deveres para com a OAGB', 'Constituem Deveres do Advogado para com a Ordem:
a) Não prejudicar os fins e prestígio da Ordem e da advocacia;
b) Exercer os cargos para que tenha sido eleito ou nomeado e desempenhar com zelo e dedicação os mandatos que lhe forem confiados;
c) Colaborar na prossecução das atribuições da Ordem;
d) Observar os costumes e praxes profissionais;
e) Declarar, ao requerer a inscrição, para efeito de verificação de incompatibilidade, qualquer cargo ou atividade profissional que exerça;
f) Suspender imediatamente o exercício da profissão e requerer, no prazo máximo de trinta dias, a suspensão da inscrição na Ordem quando ocorrer incompatibilidade ou impedimento superveniente;
g) Pagar pontualmente as quotas e outros encargos devidos à Ordem, estabelecidos neste Estatuto e nos regulamentos, suspendendo-se o direito de votar e de ser eleito para os órgãos da Ordem se houver atraso superior a três meses;
h) Dirigir com empenho o estágio dos advogados estagiários e elaborar a respetiva informação final;
i) Comunicar, no prazo de trinta dias, qualquer mudança de escritório.', 'Exercício da Advocacia', 104);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (105, 'Documentos e Valores do Cliente', '1. Quando cessar a representação confiada ao advogado, deve este, restituir os documentos, valores ou objetos que lhe hajam sido entregues e que sejam necessários para prova do direito do cliente ou cuja retenção possa trazer a este prejuízo graves.
2. Com relação aos demais valores e objetos em seu poder, goza o advogado do direito de retenção para garantia do pagamento dos seus honorários e reembolso de despesas.', 'Exercício da Advocacia', 105);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (106, 'Informação e Publicidade', '1. O Advogado deve divulgar a sua atividade profissional de forma objetiva, verdadeira e digna, no rigoroso respeito dos deveres deontológicos e segredo profissional.
2. É vedada ao advogado toda a espécie de reclamo por circulares, anúncios, meios de comunicação social ou por qualquer outra forma, direta ou indireta, de publicidade profissional designadamente divulgando o nome dos seus clientes.
3. Os advogados não devem fomentar, nem autorizar, notícias referentes a causas judiciais ou outras questões profissionais a si confiadas.
4. Não constituem formas de publicidade a indicação de títulos académicos, a menção de cargos exercidos na Ordem ou a referência à sociedade civil profissional de que o advogado seja sócio, devendo qualquer outra menção ser previamente autorizada pelo Conselho Nacional.
5. Não constitui também publicidade o uso de tabuletas afixadas no exterior dos escritórios, a inserção de meros anúncios nos jornais, a utilização de cartão-de-visita ou papel de carta, desde que com simples menção do nome do advogado, endereço do escritório e horas do expediente.
6. Nas publicações especializadas de advogados pode ainda inserir-se curriculum vitae académico e profissional do advogado e eventual referência à sua especialização, se previamente reconhecida pela Ordem.', 'Exercício da Advocacia', 106);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (107, 'Direito de coadjuvação pelas entidades públicas', 'No exercício da profissão os advogados têm direito à coadjuvação das entidades públicas.

Secção II', 'Exercício da Advocacia', 107);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (108, 'Início de Atividade', 'a) Estudar e dar parecer sobre pedidos de inscrições nos quadros de advogados e estagiários, examinando e verificando o preenchimento dos requisitos legais;
b) Apreciar as impugnações aos pedidos de inscrição, emitindo parecer fundamentado, para posterior apreciação e julgamento pela Primeira Câmara;
c) Verificar o efetivo exercício profissional por parte dos inscritos, bem como os casos de impedimento, incompatibilidade, licenciamento ou cancelamento da inscrição;
d) Determinar, quando for o caso, exame de saúde, a ser realizado pela Caixa de Assistência dos Advogados do Acre, visando a promover eventual licenciamento do profissional;
e) Examinar pedidos de transferência e de inscrição suplementar;
f) Promover a representação prevista no art. 10, § 4º, da Lei nº. 8906/94, em caso de transferência ou inscrição suplementar, desde que verificado vício ou possível ilegalidade na inscrição principal;
g) Deferir a expedição de carteiras profissionais e cédulas de identidade, bem como vias suplementares em casos de extravio, perda ou mau estado de conservação;
h) Recolher as carteiras e cédulas dos advogados, ou profissionais excluídos, suspensos ou impedidos do exercício da advocacia, assim como daqueles que tiverem suas inscrições canceladas;
i) Em caso de recusa de entrega da carteira profissional, na forma prevista no dispositivo anterior, propor a tomada das medidas cabíveis, inclusive de natureza judicial, para obter a restituição do documento;
j) Autorizar, de imediato, a alteração do nome da profissional em virtude de casamento, separação judicial ou divórcio, desde que comprovado por documento hábil a mudança;
k) Anotar nas carteiras o cancelamento das inscrições, assim como os licenciamentos e impedimentos.
O inicio de atividade de advocacia depende da verificaçao dos seguintes requisitos comulativos:
a) Aquisição de instalações para manter escritório ou associar-se a um escritório já existente em espaço condigno, adequado e afeto exclusivamente a atividade de advocacia, constituindo este o seu domicílio profissional;
b) Registo do escritório na Ordem dos Advogados;
c) Registo do Carimbo profissional na Ordem dos advogados;
d) Registo do carimbo do escritório da Ordem dos Advogados
e) Aquisição do uniforme profissional;', 'Exercício da Advocacia', 108);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (109, 'Regime jurídico do acto próprio do advogado', '1. O acto do advogado faz prova plena da data, assinatura do autor e da qualidade em que o assina.
2. Os advogados são obrigados a redigir de forma clara, precisa e legal os atos dos advogados que praticam, assegurando aos seus clientes a garantia legal de produção de efeitos para os quais contrataram os seus serviços.
3.  São menções obrigatórias no acto do advogado:
a) Assinatura do autor;
b) Data
c) N° de inscrição na Ordem;
d)  Carimbo profissional, donde consta a morada do escritório;
e) Vinhete nos casos em que é obrigatória a sua aposição;', 'Exercício da Advocacia', 109);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (110, 'Atos próprios dos advogados', '1. São atos próprios dos advogados:
a) O mandato forense
b) Elaboração e Impugnação dos atos judiciais, civis, administrativos, tributários e afins;
c) A atividade de consultoria;
d) A elaboração de contratos e a prática dos atos preparatórios tendentes à constituição, alteração ou extinção de negócios jurídicos, seja aqueles para os quais a lei exige a escritura pública, designadamente os praticados perante as conservatórias e cartórios notariais;
e) A negociação tendente à cobrança de créditos;
1. Consideram-se atos próprios dos advogados e dos solicitadores os atos que, nos termos do número anterior, forem exercidos no interesse de terceiros e no âmbito de atividade profissional, sem prejuízo das competências próprias atribuídas às demais profissões ou atividades cujo acesso ou exercício é regulado por lei.
2. São também atos próprios dos advogados todos aqueles que resultem do exercício do direito dos cidadãos a fazer acompanhar por advogado perante qualquer autoridade.
3. Nos casos em que o processo penal determinar que o suspeito seja assistido por defensor, esta função é obrigatoriamente exercida por advogado, nos termos da lei.
4. Os advogados estagiários, regularmente inscrito, pode praticar os atos previstos no n° 1, nos termos estabelecidos no regulamento do estágio, em conjunto com advogado e sob responsabilidade deste.
5. Os atos próprios dos advogados e solicitadores relativos a contratos constitutivos de pessoas jurídicas, intervenções judiciais, exercício do mandato no âmbito de reclamação ou impugnação de atos administrativos ou tributários, sob pena de ineficazes ou não admitidos a registo, nos órgãos competentes, devem ser visados por carimbos próprios dos advogados.', 'Exercício da Advocacia', 110);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (111, 'Atos próprios dos solicitadores', '1. São atos dos Solicitadores:
a) O exercício do mandato forense nos termos previsto nas leis do processo;
b) A elaboração de contratos e a prática dos atos preparatórios tendentes à constituição, alteração ou extinção de negócios jurídicos, seja aqueles para os quais a lei exige a escritura pública, designadamente os praticados perante as conservatórias e cartórios notariais;
2. Para os efeitos do disposto no artigo anterior e no número anterior, não se consideram atos próprios dos advogados e solicitadores os praticados no interesse de terceiros, pelos representantes legais, empregados, funcionários ou agentes de pessoas singulares ou coletivas, publicas ou privadas, nesta qualidade, salvo se, no caso de cobrança de dívidas, esta constituir o objeto ou atividade principal destas pessoas.
3. Não se inclui nos atos próprios dos advogados e solicitadores o requerimento de habeas corpus em qualquer instância ou tribunal.', 'Exercício da Advocacia', 111);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (112, 'Prova do acto próprio do advogado e solicitador', '1. A prova do acto próprio do Advogado e solicitador é feita mediante aposição da assinatura do advogado no acto, carimbo pessoal e n° de inscrição na OAGB
2. Os elementos da prova do acto próprio do advogado e solicitador constante do n° anterior, são obrigatórios em actos constitutivos de pessoas coletivas, indispensáveis ao seu registo e arquivamento nos órgãos competentes.
3. O funcionário publico, no acto de recepçao para efeitos legais, registo e arquivamento dos documentos de competência do advogado e solicitador, deve verificar e confirmar a constatação efetiva de que os respetivos documentos as exigências legais profissional.
4. A prática de actos próprios de advocacia, por profissionais e sociedades não inscritos na OAGB, constitui exercício ilegal da profissão.', 'Exercício da Advocacia', 112);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (113, 'Atos próprios dos advogados nulos', '1. São nulos os atos próprios dos advogados e solicitadores praticados por pessoas sem inscrição na OAGB.
2. São também nulos os atos próprios dos advogados praticados por advogados e solicitadores impedidos, no âmbito do impedimento, suspensos ou que passar a exercer atividade incompatível com a advocacia.', 'Exercício da Advocacia', 113);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (114, 'Competência dos Estagiários', '1. Durante o período do estágio, o estagiário não pode:
a) Praticar actos próprios da profissão de advogado ou de solicitador judicial senão em causa própria ou do seu cônjuge, ascendentes ou descendentes;
b) Exercer a advocacia em processos penais de competência do Tribunal de Sector;
c) Exercer a advocacia em processos cíveis cujo valor não caiba a alçada Tribunal de Primeira Instância;
d) Exercer a advocacia em processos de divórcio por mútuo consentimento;
e) Exercer consulta jurídica.
2. Pode, no entanto, o advogado estagiário praticar actos próprios da advocacia em todos os demais processos, independentemente da sua natureza e do seu valor, desde que efetivamente acompanhado de advogado que assegure a tutela do seu tirocínio.
3. O estagiário deve indicar sempre a sua qualidade quando intervenha em qualquer acto de natureza profissional.', 'Exercício da Advocacia', 114);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (115, 'Advogados em serviço público do Estado', '1. Exercem a advocacia em serviço público, os advogados inscritos na OAGB, e livremente, pessoal ou através do escritório solicitem a inscrição no sistema da justiça gratuita, para patrocínio oficioso das pessoas carenciadas, nos termos definidos por lei.
2. Os advogados que prestam serviço público da advocacia são elegíveis e podem integrar qualquer órgão da OAGB.', 'Exercício da Advocacia', 115);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (116, 'Jurisconsultos', '1. Podem exercer consulta jurídica, para além dos advogados com inscrição em vigor na Ordem dos Advogados, juristas cujo grau de licenciatura seja reconhecida por órgãos competentes do Estado da Guiné-Bissau, solicita a sua inscrição na Ordem dos Advogados para o efeito e aceite.
2. Excetua-se do disposto no número anterior a elaboração de pareceres escritos por docentes das faculdades de direito, no âmbito da função académica, sobre matérias que atua e professores doutores em direito.
3. Não se inclui no n° 2, os pareceres jurídicos emitidos pelos funcionários ou agentes e empregados de pessoas singulares ou coletivas, públicas ou privadas, nesta qualidade para os fins das entidades onde prestam as suas atividades.', 'Exercício da Advocacia', 116);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (117, 'A lista dos advogados', 'Deve a Comissão de Acesso à Justiça, em cada três meses, enviar aos tribunais e aos departamentos do Governo, a lista atualizada dos Advogados e solicitadores com inscrição e quotas regulares, e zelar pelo seu cumprimento e fiscalização.
CAPITULO II
DEONTOLOGIA PROFISSIONAL
Secção I
Princípios gerais', 'Exercício da Advocacia', 117);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (118, 'DEVER DE INTEGRIDADE PROFISSIONAL', '1. No exercício da profissão, o advogado deve proceder com correção indispensável de forma que o seu comportamento público e profissional o torne merecedor de respeito e responsabilidades de funções que exerce, contribuindo para o prestígio da classe e da advocacia.
2. No exercício da profissão, o advogado obriga a abster-se, de qualquer contacto ilícito com os órgãos da administração da justiça, nomeadamente magistrados, ou seus auxiliares, nomeadamente, as testemunhas, peritos, ou outros, capaz de influenciar os seus comportamentos e prejudicar a descoberta a boa decisão da causa, não negociar ou entrar em negociação sobre a lei ou deontologia profissional com os órgãos da administração da justiça.
3. São obrigações profissionais do advogado a honestidade, lealdade, probidade, retidão, lealdade, cortesia e sinceridade.
4. A relação entre os advogados no exercício da profissão, impõem nomeadamente, os seguintes deveres:
a) A relação de confiança e cooperação entre os advogados em benefício dos clientes;
b) Evitar litígios inúteis, conciliando, tanto quanto possível, os interesses da profissão com os da justiça ou daqueles que a procuram;
c) Proceder com maior correção e urbanidade, abstendo-se de qualquer ataque pessoal, alusão deprimente ou crítica desprimorosa, de fundo ou de forma;
d) Responder; em prazo razoável, às solicitações orais ou escritas do colega;
e) Não emitir publicamente opinião sobre questão que saiba confiada à outro advogado, salvo na presença deste ou com o seu prévio acordo;
f) Atuar com maior lealdade, procurando não obter vantagens ilegítimas ou indevidas para o seu cliente;
g) Não contactar a parte contraria que esteja representada por advogado, salvo se previamente autorizado por este, ou se tal for indispensável, por imposição legal ou contratual;
h) Não assinar pareceres, peças processuais ou outros escritos profissionais que não sejam da sua autoria ou em que não tenha colaborado;
i) Comunicar, atempadamente, a impossibilidade de comparecer a qualquer diligência aos outros advogados que nela devam intervir;
j) Não receber ou não iniciar a sua atuação num assunto anteriormente confiado a outro advogado, sem antes diligenciar no sentido de a este ser pago os seus honorários e demais quantias que a este sejam devidas, devendo expor ao colega, oralmente ou por escrito, as razoes da aceitação do mandato e dar-lhe conta dos esforços que tenha desenvolvido para aquele efeito;
k) Que, sempre que um advogado deseja que a sua comunicação dirigida a um outro advogado seja confidencia, deve exprimir claramente essa preocupação;
5. As comunicações confidenciais não podem, em qualquer caso, constituir meio de prova, não lhes sendo aplicável o regime do segredo profissional.
6. O advogado destinatário da comunicação que não tenha condições para garantir a confidencialidade da mesma deve devolvê-la ao remetente sem revelar à terceiros o respetivo conteúdo.', 'Exercício da Advocacia', 118);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (119, 'Independência', '1. O advogado, no exercício da profissão, deve manter-se sempre independente em quaisquer circunstâncias, subordinando-se apenas a lei, devendo agir livre de qualquer pressão, atuar com diligência e lealdade devida aos interesses do seu cliente, abster-se especialmente de influência que resulte dos seus próprios interesses ou de influências exteriores, quer sejam eles políticos ou económicos, cumprindo pontual e escrupulosamente os deveres consignados no presente Estatuto e todos aqueles que a lei, os usos, costumes e tradições profissionais lhe impõem.
2. O advogado deve obstar a que os seus clientes exerçam quaisquer represálias contra o adversário e sejam menos corretos para com os advogados da parte contrária, magistrados, árbitros, funcionários, órgãos de polícia ou quaisquer outros intervenientes no processo da justiça.', 'Exercício da Advocacia', 119);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (120, 'Incompatibilidade', 'São incompatíveis com o exercício da advocacia as seguintes funções:
a) Membro do Governo;
b) Magistrado Judicial ou do Ministério Público;
c) Diretor de serviço e diretor Geral;
d) Notário ou Conservador dos Registos e Funcionários ou agentes dos Serviços do notariado e registo;
e) Polícia ou Guarda Fiscal;
f) Funcionários, Agentes ou assessores em qualquer Tribunal, Polícia ou Organismo especializado de Fiscalização, Prevenção ou Segurança;
g) Outras que por lei sejam declaradas como tal.', 'Exercício da Advocacia', 120);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (121, 'Impedimentos', '1. O advogado está impedido de exercer o patrocínio:
a) Nos processos em que cônjuge ou algum ascendente, descendente, irmão ou afim nos mesmos graus, for juiz ou magistrado do Ministério Público;
b) Nos processos em que tenha intervindo como testemunhas, declarantes ou peritos;
c) Nos processos em que tenha intervindo ou seus incidentes, processos conexos, em qualquer veste, nomeadamente representante ou auxiliar da parte contrária, tenha prestado parecer jurídico sobre a questão controvertida, Juiz assessor, agente do Ministério Público ou funcionário judicial, testemunha, declarante ou perito;
d) A parte contrária noutra causa pendente seja patrono;
e) A questão seja contra a entidade patronal a que se encontra vinculado por contrato de trabalho;
2. Para além dos impedimentos referidos no número anterior, está igualmente impedido de exercer o patrocínio contra ou a favor do Estado:
a) Os Deputados da Assembleia Nacional Popular;
b) Funcionários e agentes da administração pública; independentemente do título de ligação;
3. Cessa imediatamente o impedimento, as razoes de impedimento previsto no número anterior logo que o impedido deixar de exercer as funções públicas, comunicando o facto por escrito a OAGB', 'Exercício da Advocacia', 121);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (122, 'Segredo Profissional', '1. O advogado tem direito a proteção do segredo profissional contra o Estado, as entidades públicas e privadas.
2. O advogado é obrigado ao segredo profissional no que respeite:
a) Os factos referentes a assuntos profissionais que lhe tenham sido revelados pelo cliente ou por sua ordem ou conhecidos no exercício da profissão;
b) Os factos que, em virtude de cargo desempenhado na Ordem, qualquer colega obrigado quanto aos mesmos factos ao segredo profissional, lhe tenha comunicado;
c) Os factos comunicados por coautor, corréu ou cointeressado do cliente ou pelo respetivo representante;
d) Os factos de que a parte contrária do cliente ou respetivos representantes lhe tenham dado conhecimento durante negociações para acordo amigável e que sejam relativos à pendência.
3. A obrigação do segredo profissional existe, quer o serviço solicitado ou cometido ao advogado envolva ou não representação judicial ou extrajudicial quer deva ou não ser remunerado, quer o advogado haja ou não chegado a aceitar e a desempenhar a representação ou serviços, o mesmo acontecendo para todos os advogados que, direta ou indiretamente, tenham qualquer intervenção no serviço.
3. O segredo profissional abrange ainda os documentos ou outras coisas que se relacionem, direta ou indiretamente, com os factos sujeitos a sigilo.
4. Cessa a obrigação de segredo profissional em tudo quanto seja absolutamente necessário para a defesa da dignidade, direitos e interesses legítimos do próprio advogado ou do cliente ou seus representantes, mediante prévia autorização do Presidente do tribunal de deontologia e disciplina com recurso nos termos gerais de direito,
5. Não podem fazer prova em juízo as declarações feitas pelo advogado com violação de segredo profissional.', 'Exercício da Advocacia', 122);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (123, 'Imposição de Selos, Arrolamentos e Buscas em Escritórios de Advogados', '1. A imposição de selos, arrolamentos, buscas e diligências semelhantes no escritório de advogado ou em qualquer outro lugar onde faça arquivo só pode ser decretado e presidido pelo juiz competente.
2. Com a necessária antecedência, o juiz deve convocar para assistir a diligência o advogado a ele sujeito, bem como o Presidente do Conselho deontológico podendo este delegar em outro advogado.
3. Na falta de comparência do advogado, representante da Ordem ou havendo urgência incompatível com os trâmites previstos no número anterior, o juiz deve nomear qualquer advogado que possa comparecer imediatamente, de preferência de entre os que hajam feito parte dos órgãos da Ordem ou, quando não seja possível, o que for indicado pelo advogado a quem o escritório ou arquivo pertencer.
4. Às diligências são admitidos também, quando se apresentem ou o juiz os convoque, os familiares ou empregados do advogado interessado.
5. Até a comparência do advogado que represente a Ordem podem ser tomadas as providências indispensáveis para que se não inutilizem ou desencaminhem quaisquer papéis ou objecto,
6. O auto de diligência faz expressa menção das pessoas presentes, bem como de quaisquer ocorrências que tenham lugar no seu decurso.', 'Exercício da Advocacia', 123);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (124, 'Apreensão de Documentos', '1. Não pode ser apreendida a correspondência que respeite ao exercício da profissão.
2. A proibição estende-se às correspondências trocadas entre o advogado e aquele que lhe tenha cometido ou pretendido cometer mandato ou lhe haja solicitado parecer, embora ainda não dado ou é recusado.
3. Compreende-se na correspondência as instruções e informações inscritas sobre o assunto da nomeação, mandato ou do parecer solicitado.
4. Excetua-se o caso de a correspondência respeitar o facto criminoso relativamente ao qual o advogado seja arguido.', 'Exercício da Advocacia', 124);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (125, 'Reclamação', '1. No decurso das diligências previstas nos artigos anteriores, pode o advogado interessado ou, na sua falta, qualquer dos familiares ou empregado presente, bem como o representante da Ordem apresentar qualquer reclamação.
2. Sendo a reclamação feita para preservar o segredo profissional, o juiz deve logo sobrestar na diligência relativamente aos documentos ou objetos que forem postos em causa fazendo-os acondicionar, sem os ler ou examinar, em volume selado no mesmo momento.
3. As reclamações serão fundamentadas no prazo de cinco dias e entregues no Tribunal onde corre o processo devendo remetê-las em igual prazo, ao Presidente do Supremo Tribunal de Justiça com o seu parecer e, sendo caso disso, com o volume a que se refere o número anterior.
4. O Presidente do Supremo Tribunal de Justiça pode, com reserva de segredo, proceder à descolagem do mesmo volume, devolvendo-o novamente selado com a sua decisão.', 'Exercício da Advocacia', 125);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (126, 'Direito de Comunicação com Suspeitos Presos', 'Os advogados têm direito, nos termos da lei, de comunicar, pessoal e reservadamente, com os seus patrocinados, mesmo que estes se achem presas ou detidos em estabelecimento civil ou militar.', 'Exercício da Advocacia', 126);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (127, 'Obrigação de Prestar Informações', '1. O Conselho deontológico pode solicitar dos advogados e advogados estagiários as informações que entenda necessárias para verificação de existência ou não de incompatibilidade.
2. Não sendo tais informações prestadas no prazo de quinze dias, poderá o Conselho Jurisdicional requerer a suspensão da inscrição.
3. A aplicação do disposto nos números anteriores não é prejudicada pela circunstância de o advogado ou advogado estagiário ter mudado o seu escritório desde que da mudança não tenha sido dado oportuno conhecimento.', 'Exercício da Advocacia', 127);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (128, 'Dever Geral de Urbanidade', 'No exercício da profissão deve o advogado proceder com urbanidade, nomeadamente, para com os outros advogados, magistrados, funcionários das secretarias, peritos, interpretes, testemunhas e outros intervenientes nos processos.', 'Exercício da Advocacia', 128);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (129, 'Informação, Exame de Processo e Pedido de Certidões', '1. No exercício da sua profissão, o advogado pode solicitar em qualquer tribunal ou repartição pública o exame de processos, livros ou documentos que não tenham carácter reservado ou secreto, bem como requerer verbalmente ou por escrito a passagem de certidões, sem necessidade de exibir procuração.
2. Os advogados, quando no exercício da sua profissão, têm preferência para ser atendidos por quaisquer funcionários a quem devam dirigir-se e têm o direito de ingresso nas secretarias judiciais.', 'Exercício da Advocacia', 129);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (130, 'Direito de Protesto', '1. No decorrer de audiência ou de qualquer outro acto ou diligência em que intervenha o advogado deve ser admitido a requerer oralmente ou por escrito, no momento que considerar oportuno, o que julgar conveniente ao dever do patrocínio.
2. Quando, por qualquer razão, lhe não seja concedido a palavra ou o requerimento não for exarado em acta pode o advogado exercer o direito de protesto, indicando a matéria do requerimento e o objecto que tinha em vista.
3. O protesto não pode deixar de constar da acta e é havido para todos os efeitos como arguição de nulidade, nos termos da lei', 'Exercício da Advocacia', 130);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (131, 'Patrocínio Contra Advogados e Magistrados', 'O advogado, antes de promover quaisquer diligências judiciais contra outros advogados ou magistrados, comunicar-lhes-á por escrito a sua intenção, com as explicações que entenda necessárias, salvo tratando-se de diligência ou auto de natureza secreta ou urgente.', 'Estágio e Formação', 131);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (132, 'Discussão Pública de Questões Profissionais', '1. O advogado não deve discutir ou contribuir para a discussão em público ou nos meios de comunicação social, questões pendentes ou a instaurar perante os tribunais ou outros órgãos do Estado, salvo se o Conselho Jurisdicional concordar fundamentalmente com a necessidade de uma explicação pública e, nesse caso nos precisos termos autorizado pelo Conselho.
2. O advogado não deve tentar influir de forma maliciosa ou censurável na resolução de pleitos judiciais ou outras questões pendentes em órgãos do Estado.


TITULO VI
Dever de colaboração da Administração publica com os advogados', 'Estágio e Formação', 132);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (133, 'Correspondências e requisições de documentos', '1. No exercício da sua atividade, as entidades públicas estão obrigadas a prestar a devida colaboração aos advogados, devendo, nos limites da lei, corresponder-se com estes, designadamente para fornecimento de cópias, certidões, informações e esclarecimentos, incluindo a consulta dos autos e confiança de processo, sempre que para tal, o interessado fundamentar o pedido.
2. Havendo recusa de colaboração, poderá o advogado requerer a intervenção do Bastonário, através da carta dirigida a Ordem dos Advogados, instruído com a cópia do pedido de colaboração e documentos que achar conveniente.
3. O pedido deve, se possível identificar detalhadamente o comportamento da entidade recusante.', 'Estágio e Formação', 133);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (134, 'Direito a informação e de acesso aos documentos', '1. Os advogados têm direito à informação indispensável para a boa administração da justiça, nomeadamente o direito de acesso a documentos administrativos de carácter não nominativo.
2. O direito de acesso aos documentos administrativos compreende não só o direito de obter a sua reprodução, bem como o direito de ser informado sobre a sua existência e conteúdo.
3. O depósito dos documentos administrativos em arquivos não prejudica o exercício, a todo o tempo, do direito de acesso aos referidos documentos.
4. O direito de acesso aos documentos, abrange os documentos notariais e registrais, aos documentos de identificação civil e criminal, aos documentos referentes a dados pessoais com tratamento automatizado e aos documentos depositados em arquivos históricos, salvo disposição legal contrária', 'Estágio e Formação', 134);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (135, 'Âmbito', '1. Os documentos a que se reporta o artigo anterior são os que têm origem ou são detidos por órgãos do Estado que exerçam funções administrativas, órgãos dos institutos públicos e das associações públicas, órgãos das autarquias locais, e outras entidades no exercício de poderes de autoridade, nos termos da lei.
2. Os direitos abrangidos por este regime, incluem o direito de pedir esclarecimentos e explicações sobre a demora da administração, relativamente as situações administrativas cujos prazos de resposta está previsto na lei', 'Estágio e Formação', 135);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (136, 'Documentos administrativos', '1. Para efeito do disposto nos presentes estatutos, são considerados documentos administrativos, os documentos detidos pela Administração Pública, nomeadamente, processos, relatórios, estudos, pareceres, atas, autos, circulares, ofícios-circulares, ordens de serviço, despachos normativos internos, instruções e orientações de interpretação legal ou de enquadramento da atividade ou outros elementos de informação;
2. Não se consideram documentos administrativos, para efeitos do presente estatuto:
a) As notas pessoais, esboços, apontamentos e outros registos de natureza semelhante;
b) Os documentos cuja elaboração não releve da atividade administrativa, designadamente referentes à reunião do Conselho de Ministros e /ou outras reuniões de natureza politica do Estado, bem como à sua preparação.', 'Estágio e Formação', 136);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (137, 'Forma do pedido', 'O acesso aos documentos deve ser solicitado por escrito através do requerimento, onde conste os elementos essenciais à sua identificação, bem como a identificação do escritório, do advogado, número de inscrição e assinatura.', 'Estágio e Formação', 137);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (138, 'Resposta da Administração', '1. A entidade a quem foi dirigido o requerimento de acesso a um documento deve, no prazo de 5 dias:
a) Comunicar a data, local e modo para se efetivar a consulta, efetuar a reprodução ou obter a certidão;
b) Indicar, nos termos da presente lei, as razões da recusa, total ou parcial, do acesso ao documento pretendido;
c) Informar que não possui o documento e, se for do seu conhecimento, qual a entidade que o detém ou remeter o requerimento a esta, comunicando o facto ao interessado;', 'Estágio e Formação', 138);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (139, 'Formas e meios de acesso', '1. O acesso aos documentos exerce-se através de:
a) Consulta gratuita, efeituada nos serviços que os detêm;
b)  Reprodução por fotocópia ou por qualquer meio técnico, designadamente visual ou sonora;
c) Passagem de certidão pelos serviços da Administração.
2. A reprodução nos termos da alínea b) do número anterior far-se-á num exemplar, sujeito a pagamento, pela pessoa que a solicitar, do encargo financeiro estritamente correspondente ao custo dos materiais usados e do serviço prestado, a fixar por despacho do Ministro.
3. Os documentos informatizados são transmitidos em forma inteligível para qualquer pessoa e em termos rigorosamente correspondentes ao do conteúdo do registo, sem prejuízo da opção prevista na alínea b) do n.º 1.
4. Quando a reprodução prevista no n.º 1 puder causar dano ao documento visado, o interessado, a expensas suas e sob a direção do serviço detentor, pode promover a cópia manual ou a reprodução por qualquer outro meio que não prejudique a sua conservação.', 'Estágio e Formação', 139);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (140, 'Recusa legítima de fornecer documentos e informações', '1. A Administração pode recusar o acesso a documentos cuja comunicação ponha em causa segredos comerciais, industriais ou sobre a vida interna das empresas.
2. É vedada a utilização de informações com desrespeito dos direitos de autor e dos direitos de propriedade industrial, assim como a reprodução, difusão e utilização destes documentos e respetivas informações que possam configurar práticas de concorrência desleal.
3. Os dados pessoais comunicados a terceiros não podem ser utilizados para fins diversos dos que determinaram o acesso, sob pena de responsabilidade por perdas e danos, nos termos legais.', 'Estágio e Formação', 140);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (141, 'Direito de queixa', '1. O advogado denegado o acesso à informação ou documento administrativo pode dirigir uma queixa ao presidente do Supremo Tribunal de Justiça, no prazo de 20 dias, contra o indeferimento expresso, a falta de decisão ou decisão limitadora do direito de acesso.
2. O presidente do Supremo Tribunal de Justiça tem o prazo de 10 dias para se pronunciar, mediante relatório de apreciação da situação, enviando-o, com as devidas conclusões, ao interessado e ao departamento administrativo visado.
3. Recebido o relatório referido no número anterior, a Administração deve comunicar ao interessado a sua decisão final, fundamentada, no prazo de 5 dias.', 'Estágio e Formação', 141);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (142, 'Recurso', 'A decisão ou falta de decisão podem ser impugnadas pelo interessado junto dos tribunais administrativos, aplicando-se, com as devidas adaptações, as regras do processo administrativo urgentes.', 'Estágio e Formação', 142);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (143, 'Direito a informação e de acesso aos documentos', '5. Os advogados têm direito à informação indispensável para a boa administração da justiça, nomeadamente o direito de acesso a documentos administrativos de carácter não nominativo.
6. O direito de acesso aos documentos administrativos compreende não só o direito de obter a sua reprodução, bem como o direito de ser informado sobre a sua existência e conteúdo.
7. O depósito dos documentos administrativos em arquivos não prejudica o exercício, a todo o tempo, do direito de acesso aos referidos documentos.
8. O direito de acesso aos documentos, abrange os documentos notariais e registrais, aos documentos de identificação civil e criminal, aos documentos referentes a dados pessoais com tratamento automatizado e aos documentos depositados em arquivos históricos, salvo disposição legal contraria', 'Estágio e Formação', 143);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (144, 'Âmbito', '3. Os documentos a que se reporta o artigo anterior são os que têm origem ou são detidos por órgãos do Estado que exerçam funções administrativas, órgãos dos institutos públicos e das associações públicas, órgãos das autarquias locais, e outras entidades no exercício de poderes de autoridade, nos termos da lei.
4. Os direitos abrangidos por este regime, incluem o direito de pedir esclarecimentos e explicações sobre a demora da administração, relativamente as situações administrativas cujos prazos de resposta está previsto na lei', 'Estágio e Formação', 144);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (145, 'Documentos administrativos', '3. Para efeito do disposto nos presentes estatutos, são considerados documentos administrativos, os documentos detidos pela Administração Pública, nomeadamente, processos, relatórios, estudos, pareceres, atas, autos, circulares, ofícios-circulares, ordens de serviço, despachos normativos internos, instruções e orientações de interpretação legal ou de enquadramento da atividade ou outros elementos de informação;
4. Não se consideram documentos administrativos, para efeitos do presente estatuto:
c) As notas pessoais, esboços, apontamentos e outros registos de natureza semelhante;
d) Os documentos cuja elaboração não releve da atividade administrativa, designadamente referentes à reunião do Conselho de Ministros e /ou outras reuniões de natureza política do Estado, bem como à sua preparação.', 'Estágio e Formação', 145);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (146, 'Forma do pedido', 'O acesso aos documentos deve ser solicitado por escrito através do requerimento, onde conste os elementos essenciais à sua identificação, bem como a identificação do escritório, do advogado, número de inscrição e assinatura.', 'Estágio e Formação', 146);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (147, 'Resposta da Administração', '2. A entidade a quem foi dirigido o requerimento de acesso a um documento deve, no prazo de 5 dias:
d) Comunicar a data, local e modo para se efetivar a consulta, efetuar a reprodução ou obter a certidão;
e) Indicar, nos termos da presente lei, as razões da recusa, total ou parcial, do acesso ao documento pretendido;
f) Informar que não possui o documento e, se for do seu conhecimento, qual a entidade que o detém ou remeter o requerimento a esta, comunicando o facto ao interessado;', 'Estágio e Formação', 147);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (148, 'Formas e meios de acesso', '5. O acesso aos documentos exerce-se através de:
d) Consulta gratuita, efeituada nos serviços que os detêm;
e)  Reprodução por fotocópia ou por qualquer meio técnico, designadamente visual ou sonora;
f) Passagem de certidão pelos serviços da Administração.
6. A reprodução nos termos da alínea b) do número anterior far-se-á num exemplar, sujeito a pagamento, pela pessoa que a solicitar, do encargo financeiro estritamente correspondente ao custo dos materiais usados e do serviço prestado, a fixar por despacho do Ministro.
7. Os documentos informatizados são transmitidos em forma inteligível para qualquer pessoa e em termos rigorosamente correspondentes ao do conteúdo do registo, sem prejuízo da opção prevista na alínea b) do n.º 1.
8. Quando a reprodução prevista no n.º 1 puder causar dano ao documento visado, o interessado, a expensas suas e sob a direção do serviço detentor, pode promover a cópia manual ou a reprodução por qualquer outro meio que não prejudique a sua conservação.', 'Estágio e Formação', 148);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (149, 'Recusa legítima de fornecer documentos e informações', '4. A Administração pode recusar o acesso a documentos cuja comunicação ponha em causa segredos comerciais, industriais ou sobre a vida interna das empresas.
5. É vedada a utilização de informações com desrespeito dos direitos de autor e dos direitos de propriedade industrial, assim como a reprodução, difusão e utilização destes documentos e respetivas informações que possam configurar práticas de concorrência desleal.
6. Os dados pessoais comunicados a terceiros não podem ser utilizados para fins diversos dos que determinaram o acesso, sob pena de responsabilidade por perdas e danos, nos termos legais.', 'Estágio e Formação', 149);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (150, 'Direito de queixa', '4. O advogado denegado o acesso à informação ou documento administrativo pode dirigir uma queixa ao presidente do Supremo Tribunal de Justiça, no prazo de 20 dias, contra o indeferimento expresso, a falta de decisão ou decisão limitadora do direito de acesso.
5. O presidente do Supremo Tribunal de Justiça tem o prazo de 10 dias para se pronunciar, mediante relatório de apreciação da situação, enviando-o, com as devidas conclusões, ao interessado e ao departamento administrativo visado.
6. Recebido o relatório referido no número anterior, a Administração deve comunicar ao interessado a sua decisão final, fundamentada, no prazo de 5 dias.', 'Estágio e Formação', 150);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (151, 'Recurso', 'A decisão ou falta de decisão podem ser impugnadas pelo interessado junto dos tribunais administrativos, aplicando-se, com as devidas adaptações, as regras do processo administrativo urgentes.

SECÇÃO II
Direitos e deveres dos advogados e solicitadores', 'Estágio e Formação', 151);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (152, 'Direitos deveres dos advogados', 'Os direitos e deveres dos advogados, tanto os de caracter geral como em relação a OAGB, ou entre advogados, ou na relação com magistrados e tribunais, na relação com os clientes, bem como em matéria dos honorários profissionais, assistência jurídica gratuita e assistência aos detidos são os estabelecidos nos presentes estatutos e demais deposições legais aplicáveis.', 'Estágio e Formação', 152);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (153, 'Direitos dos advogados', '1. São direitos profissionais dos advogados, os seguintes:
a) Intervir livremente nos tribunais, administração pública em geral para a defesa dos direitos, interesse, liberdades e garantias individuais dos cidadãos;
b) Gestão racional do seu tempo, não lhe sendo exigido a permanência em qualquer serviço judicial, policial ou da administração pública em geral, em que pratica um ato ou participa numa diligencia, depois de meia hora sobre a hora marcada para o início do acto ou diligência, querendo, findo do qual, depositar a participação registada no tribunal, dando conhecimento do facto a direção nacional da Ordem para tomar as iniciativas que julgar pertinente ao caso.
c) Não ser restringido na sua liberdade ou independência por qualquer forma no exercício da sua função;
d) Protestar os obstáculos ou restrições que impedem a boa administração da justiça ou correta aplicação da lei;
e) A Colaboração necessária das autoridades judiciais e dos órgãos policiais
f) Exercer com liberdade, a profissão em todo o território nacional;
g) A inviolabilidade do escritório ou local de trabalho, bem como dos instrumentos de trabalho, correspondência escrita, eletrónica e telefónica desde que relativa ao exercício da advocacia;
h) Comunicar-se com o seu cliente, pessoal e reservadamente, mesmo sem procuração, quando estes se encontram presos, detidos ou em lugar reservado, ainda que inibidos o direito de incomunicação;
i) Ser assistido por um representante da OAGB, quando detido em flagrante delito, por motivo ligado ao exercício da advocacia, para a audição, sob pena de nulidade e, nos demais casos, a comunicação expressa à direção nacional da OAGB.
j) Não ser preso, antes da sentença transitada em julgado, passiveis de caução;
k) Não ser preso no estabelecimento prisional comum ou cela, com os reclusos de delitos comum;
l) Apresentar alegações sentado ou em pé e abandonar qualquer momento a sala ou serviço mediante mero conhecimento do órgão que dirige o acto;
m) Usar da palavra, pela ordem, em qualquer juízo ou tribunal, mediante intervenção sumaria, para esclarecer equívoco ou duvida surgida em relação a factos, documentos ou afirmações que influam no julgamento, bem como para replicar acusação ou censura que lhe forem feitas;
n) Ter acesso em qualquer órgão dos poderes judicial e legislativo, ou da administração pública em geral, dos outos de processos findos ou em andamento, mesmo sem procuração, quando não estejam sujeitos a sigilo, assegurada a obtenção de copiais, podendo tomar notas
o) Acesso aos serviços dos órgãos policiais, mesmo sem procuração, outos de flagrante delito, e de inquérito findo ou em andamento, ainda que conclusos para autoridade judicial, podendo copiar peças e tomar notas;
p) Acesso aos processos judiciais ou administrativos de qualquer natureza, em cartório ou repartição competente, ou devolvê-los dentro dos prazos previstos na lei;
q) Não depor como testemunha em processo no qual é advogado ou deva patrocinar, ou sobre factos relacionado com a pessoa de quem foi advogado ou testemunha no processo em que foi advogado, mesmo quando autorizado ou solicitado pelo constituinte, bem como sobre facto que constitua sigilo profissional;
r) Usar de palavra com liberdade e sob a imunidade profissional, não constituindo injúria penal, qualquer intervenção oral da sua parte, no exercício da sua atividade, em juízo ou fara dele, sem prejuízo das sanções disciplinares perante a Ordem, pelos excessos que cometer;
s) Entrar livremente e sem oposição nos serviços dos tribunais, incluindo nas zonas reservados para os magistrados; nos serviços e dependências das secretarias, cartórios, serviços notariais, de registo e nas prisões sem dependência do horário de expediente;
a) Em qualquer edifício ou espeço em que funciona serviço judicial ou outro serviço publico onde o advogado deve ter acesso por razoes profissionais
b) Em qualquer assembleia ou reunião de que participa ou participar o seu cliente ou perante a qual este deve comparecer, desde que munido de poderes especiais;
2. Para o efeito do disposto nas alíneas, c), d), e e), participando o facto o interessado à ordem, poderá adotar as medidas que achar convenientes e oportunas tendo em vista assegurar a independência e prestígio profissional', 'Estágio e Formação', 153);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (154, 'Dever do Advogado para com o Cliente', '1. Nas relações com o cliente os advogados têm os seguintes deveres:
a) Recusar mandato, nomeação oficiosa ou prestação de serviços em questão em que já tenha intervindo em qualquer outra qualidade ou seja conexa com outra em que é representante ou tenha representado a parte contrária;
b) Recusar mandato contra quem noutra causa seja seu mandante;
c) Dar ao cliente a sua opinião conscienciosa sobre o merecimento do direito ou pretensão que este invoca, assim como prestar, sempre que lhe for pedido, informação sobre o andamento das questões que lhe forem confiadas;
d) Estudar com cuidado e tratar com zelo a questão de que seja incumbido, utilizando, para o efeito, todos os recursos da sua experiência, saber e atividade;
e)  Guardar segredo profissional;
f) Aconselhar toda a composição do litígio que ache justa e equitativa;
g) Dar conta ao cliente de todos os dinheiros deste que tenha recebido, qualquer que seja sua proveniência e apresentar nota de honorários e despesas, quando solicitada;
h) Dar aplicação devida a valores, documentos ou objetos que lhe tenham sido confiados;
i) Não celebrar, em proveito próprio, Contratos sobre o objeto das questões confiadas ou, por qualquer forma, solicitar ou aceitar participação nos resultados das causas;
j) Não abandonar o patrocínio do constituinte ou o acompanhamento das questões que lhe estão cometidas sem motivo justificado.
2. O advogado deve empregar os esforços a fim de evitar que seu cliente exerça quaisquer represálias contra o adversário e seja menos correto para com os advogados da parte contrária, juízes ou quaisquer outros intervenientes no processo.', 'Estágio e Formação', 154);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (155, 'Efeitos da cessação do contrato de mandato forense', '1. Cessando o contrato de mandato forense por qualquer forma, o cliente tem direito a restituição de documentos, objetos ou valores que haja sido confiado ao advogado, que sejam necessários para prova do direito do cliente ou cuja retenção possa trazer a este prejuízo graves.
2. Com relação aos demais valores e objetos em seu poder, goza o advogado do direito de retenção para garantia do pagamento dos seus honorários e reembolso de despesas.
Secção III
Das infrações disciplinares', 'Estágio e Formação', 155);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (156, 'Infração Disciplinar', '1. Comete infração disciplinar o advogado que, por ação ou omissão, violar dolosa ou culposamente algum dos deveres decorrentes deste Estatuto, dos regulamentos internos ou demais disposições aplicáveis.
2. Nenhum advogado poderá ser sancionado por ação ou omissões que não estejam tipificados como infração nos estatutos da OAGB ou nas normas deontológicas aprovadas pelo Conselho nacional.
3. Constitui infração disciplinar, nomeadamente a violação culposa dos alguns dos deveres seguintes:
a) Exercício da profissão, quando impedido de o fazer, ou facilitar, por qualquer meio, o seu exercício aos não inscritos na OAGB, proibidos ou impedidos.
b) Manter a sociedade profissional fora das normas e preceitos estabelecidos nos estatutos ou na lei;
c) Atuar como agenciador de causas, mediante participação nos honorários a receber;
d) Angariar ou captar causas, com intervenção de terceiros;
e) Assinar qualquer escrito destinado a processo judicial ou para fim extrajudicial sem mandato, ou em que não tenha intervenções permitidas por lei por qualquer forma ou colaboração;
f) Advogar contra disposição legal expressa, presumindo-se de boa-fé quando fundamentado na inconstitucionalidade, na injustiça da lei ou em decisão judicial anterior;
g) Querar, sem justa causa, sigilo profissional;
h) Estabelecer contactos com a parte adversa sem autorização do cliente e conhecimento do advogado contrário;
i)  Prejudicar, culposamente e de forma grave, interesses confiados ao seu patrocínio;
j) Abandonar o patrocínio da causa sem justa causa ou antes de decorrido o prazo para comunicação da renúncia com eficácia dentro dos autos;
k) Recursar-se a prestar, sem justo motivo, assistência jurídica, quando nomeado regularmente;
l) Fazer juízo público na imprensa, conteúdo dos actos processuais relativo aos processos pendentes;
m) Citar publicamente o nome do magistrado na imprensa ou por outro meio idóneo por razoes relacionadas com o exercício das suas funções, bem como depoimentos, documentos e alegações de parte contrária;
n) Fazer em nome do constituinte sem autorização escrita deste, declarações públicas sobre um facto do seu interesse;
o) Não cumprir, no prazo estabelecido, notificações, citações ou outras ordens judiciais ou judiciaria emitidas dentro do âmbito da competência própria ou delegada do órgão ou autoridade, depois de regularmente notificado ou citado;
p) Facilitar, consentir ou auxiliar os clientes ou a terceiros para a realização do acto contrário a lei ou destinado a fraudá-la;
q) Solicitar ou receber de constituinte qualquer importância para aplicação ilícita ou desonesta no processo ou entregar adversário;
r) Receber valores, de parte contraria ou de terceiros, relacionados com o objecto do mandato, sem expressa vontade do cliente;
s) Locupletar-se, por qualquer forma, à custa do cliente ou da parte adversaria, por si ou interposta pessoa;
t) Recusar-se, injustificadamente, a prestar contas ao cliente de quantias recebidas dele ou de terceiros por conta dele,
u) Reter, abusivamente, ou extraviar autos recebidos com vista ou em confiança;
v) Deixar de pagar as contribuições, multas e preços de serviços devidos à OAGB, depois de regularmente notificado a fazê-lo;
w) Reincidência reiterada em erros reiterados que evidenciem inépcia profissional;
x) Fazer falsa prova de qualquer dos requisitos para a inscrição na OAGB;
y) Utilizar documentos falsos, relações especiais, expedientes tipificados como crime para o exercício do mandato ou advocacia;
z) Praticar, o estagiário, acto não abrangido na sua competência profissional;
TITULO III
DO PROCESSO NA OAGB
CAPITULO I
Disposições Gerais', 'Disciplina e Processo', 156);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (157, 'Processo', '1. O processo é o meio obrigatório para a tomada de decisão na OAGB.
2. Salvo disposição em contrário, aplicam-se subsidiariamente ao processo na Ordem, incluindo ao processo disciplinar, as regras da legislação processual penal comum, as regras gerais do procedimento administrativo, e as regras da legislação processual civil, na ordem aqui estabelecida
3. O prazo para reagir, impugnar, requerer diligencia, arguir nulidade ou exercer qualquer outro poder contra os actos praticados pelos órgãos sociais ou agentes da OAGB é de 15 dias.
4. No caso do recurso contra as decisões do tribunal de ética e disciplina para os tribunais administrativos o prazo é de trinta dias
5. Excetuam-se do disposto nos números anteriores os prazos especiais previstos neste estatuto
6. Salvo disposição expressa, os atos praticados pelos Órgãos da Ordem, admitem recurso
CAPITULO I
Da Responsabilidade disciplinar dos advogados
SEC9AO I
Disposições Gerais', 'Disciplina e Processo', 157);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (158, 'Da responsabilidade Disciplinar', '1. Os advogados estão sujeitos a responsabilidade disciplinar em caso de violação dos seus deveres profissionais.
2. A jurisdição disciplinar é exclusiva dos órgãos da Ordem, nos termos regulado neste Estatuto.
3. As sanções disciplinares impostas aos advogados devem ser registadas no cadastro disciplinar dos membros da OAGB e disponível para consulta profissional, não podendo ser utilizado para fins diversos.
4. Durante o tempo de cumprimento da sanção disciplinar de suspensão, o advogado continua sujeito à jurisdição disciplinar da Ordem.
5. O pedido de cancelamento ou suspensão da inscrição não faz cessar a responsabilidade disciplinar por infrações anteriormente praticadas.', 'Disciplina e Processo', 158);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (159, 'Dever de colaboração pelos Tribunais e outras Entidades', '1. Os tribunais e todas as autoridades públicas devem dar conhecimento à Ordem da prática por advogados de actos suscetíveis de constituir infrações disciplinares.
2. O Ministério Público, os órgãos de Policia e as demais entidades públicas devem remeter à Ordem certidão das participações apresentadas contra advogados.', 'Disciplina e Processo', 159);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (160, 'Responsabilidade Disciplinar, Civil e Criminal', '1. A responsabilidade disciplinar é independente da responsabilidade criminal ou civil.
2. Pode, porém, ser ordenada a suspensão de processo disciplinar até decisão final noutros processos.
3. Sempre que, em processo contra advogado, seja designado dia para julgamento, o Tribunal deve ordenar a remessa à Ordem de cópias do processo, bem como quaisquer outros elementos solicitados.
Secção II
Penas, sua Medida, Graduação e Execução', 'Disciplina e Processo', 160);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (161, 'Penas Disciplinar e sua graduação', '1. As penas disciplinares são as seguintes:
a) Advertência;
b) Repreensão escrita;
c) Pena de formação obrigatória;
d) Multa,
e) Pena de suspensão de exercício;
f) Pena de proibição definitiva do exercício de profissão;
g) Pena de restituição;
2. As penas aplicadas devem constar no processo individual do sancionado, após trânsito em julgado da decisão, sem publicidade e censura, salvo nos casos previstos nestes estatutos.
3. Nenhum advogado poderá ser sancionado por ações ou omissões praticadas no exercício da profissão, sem devida e fundada justificação da pena aplicada.', 'Disciplina e Processo', 161);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (162, 'Aplicação das penas', 'A pena de advertência é aplicável a infrações leves que não devem passar sem reparo.', 'Disciplina e Processo', 162);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (163, 'Pena de formação obrigatória', 'A pena de formação obrigatória é aplicável a infrações que revelem a inaptidão profissional, em termos de exigências técnicas da profissão.', 'Disciplina e Processo', 163);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (164, 'Pena de multa', 'A pena de multa é aplicável a caso de negligência ou desinteresse pelo cumprimento dos deveres da profissão.', 'Disciplina e Processo', 164);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (165, 'Pena de suspensão de exercício', 'A pena de suspensão de exercício é aplicável aos casos de desinteresse grave pelo cumprimento das regras do processo com danos irreparáveis para o cliente, falta de honestidade ou conduta imoral ou desonrosa, suscetível de abalar a confiança da sociedade nos advogados.', 'Disciplina e Processo', 165);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (166, 'Pena de proibição definitiva do exercício de profissão', 'A pena de proibição definitiva do exercício de profissão é aplicável aos casos de infrações praticados em flagrante e grave abuso de profissão, com manifesta e grave violação dos deveres a ela inerentes, que impliquem a quebra de prestígio exigível ao advogado para que possa manter-se no seio dos seus colegas.', 'Disciplina e Processo', 166);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (167, 'Critérios de Graduação da Pena', '1. Na aplicação das penas deve atender-se aos antecedentes profissionais e disciplinares do suspeito, ao grau de culpabilidade, às consequências da infração e às demais circunstâncias agravantes ou atenuantes.
2.  Constituem, entre outras, circunstâncias agravantes:
a) A verificação de dolo;
b) A premeditação;
c) O conluio;
d) A reincidência;
e) A acumulação de infrações;
f) A prática de infração disciplinar durante o cumprimento de pena disciplinar ou de suspensão de respetiva execução;
g) A produção de prejuízo de valor igual ou superior a alçada do Tribunal de Primeira Instância.
3. Considera-se reincidente o advogado que cometa uma infração disciplinar que deva ser punida com pena igual ou superior à de multa, antes de decorridos o prazo de dois anos sobre o termo do cumprimento de pena efetiva de igual ou superior gravidade que lhe tenha sido aplicada pela prática da infração anterior.
4. Constituem, entre outras, circunstâncias atenuantes:
a) O exercício efetivo da advocacia por um período superior a 3 anos, sem qualquer sanção disciplinar;
b) A confissão;
c) A colaboração do suspeito para a descoberta da verdade;
d) A reparação espontânea, pelo suspeito, dos danos causados pela sua conduta.', 'Disciplina e Processo', 167);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (168, 'Graduação das penas de multa e suspensão', '1. Na aplicação das penas de multa e de suspensão, observar-se- a o seguinte:
2. Multa: conforme a gravidade da infração, de Xof. 300.000,00 (trezentos mil francos da comunidade financeira africana) até Xof. 3.000.000,00 (três milhões de francos da comunidade financeira africana);
3. Suspensão: de um mês até três anos;', 'Disciplina e Processo', 168);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (169, 'Restituição de Quantias e Documentos e Perda de Honorários', '1. Cumulativamente ou não com qualquer das penas pode ser imposta a de restituição de quantias, documentos ou objetos e, conjunta ou separadamente, a perda dos honorários.
2. Independentemente da decisão final do processo, pode ser imposta a restituição de quantias, documentos ou objetos que hajam sido confiados ao advogado.', 'Disciplina e Processo', 169);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (170, 'Aplicação da Pena de Suspensão Superior a um Ano ou proibição definitivo do exercício da profissão', '1. As penas previstas de duração superior a um ano e proibição do exercício definitivo da proibição só podem ser aplicadas por infração disciplinar que afete gravemente a dignidade e o prestígio profissional, mediante decisão que obtenha dois terços dos votos dos membros do Conselho deontológico e do tribunal de disciplina e ética.
2. Ao Bastonário é remetido o processo em seis dias para exame, participando na votação do acórdão.', 'Disciplina e Processo', 170);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (171, 'Publicidade das Penas', '1. As penas de suspensão e de expulsão têm sempre publicidade.
2. A publicidade das penas é feita por meio de edital, com referência aos preceitos infringidos, afixado nas instalações da sede e publicado num dos jornais mais lidos do país e, no caso de suspensão ou expulsão, comunicada a todos os tribunais, conservatórias e notários.
Secção II
Dos Órgãos Disciplinares da OAGB', 'Disciplina e Processo', 171);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (172, 'Do exercício da competência disciplinar', '1. O exercício da competência disciplinar é constituído por duas fases destintas com intervenção de dois órgãos igualmente distintos: a conciliatória dirigida pelo conselho de deontologia e ética e a contenciosa dirigida pelo tribunal de ética e disciplina.
2. Os titulares dos órgãos disciplinares da Ordem são independentes no exercício da sua competência jurisdicional.', 'Disciplina e Processo', 172);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (173, 'Instauração de Processo Disciplinar', '1. O procedimento disciplinar é instaurado mediante deliberação do Conselho de deontologia e ética com base em participação dirigida ao Bastonário por qualquer pessoa, devidamente identificada, que tenha conhecimento de factos suscetíveis de integrar a infração disciplinar.
2. Por despacho meramente administrativo, o Bastonário dispõe de dois dias uteis para remeter a participação ao Conselho de deontologia e ética, que poderá indeferir liminarmente, ou após diligências preliminares e por deliberação fundamentada, a participação, quando a julga manifestamente inviável, podendo o interessado interpor o recurso nos termos do presente Estatuto para o tribunal da deontologia e disciplina.', 'Disciplina e Processo', 173);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (174, 'Processos contra Titulares de Cargos da Ordem', 'Têm caracter urgente, com prioridade sobre quaisquer outros, os processos disciplinares em que sejam arguidos os titulares de algum dos órgãos da Ordem em exercício de funções.
SECÇÃO II
Distribuição', 'Disciplina e Processo', 174);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (175, 'Processo', '1. Instaurado o processo disciplinar, é efetuada a distribuição.
2. Procede-se à nova distribuição no impedimento permanente do relator ou nos seus impedimentos temporários, sempre que as circunstâncias o justifiquem.
3. Procede-se ainda à nova distribuição sempre que o infrator opõe suspeição contra o relator ou escusa do relator.', 'Disciplina e Processo', 175);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (176, 'Natureza Secreta do Processo', '1. O processo é de natureza secreta até ao despacho de acusação.
2. O relator pode, contudo, autorizar a consulta do processo pelo interessado ou pelo arguido quando não haja inconveniente para a instrução.
3. O relator pode ainda, no interesse da instrução, dar a conhecer ao interessado ou ao arguido cópia de peças do processo, a fim de os mesmos sobre elas se pronunciar.
4. Mediante requerimento em que se indique o fim a que se destina, pode ser autorizada passagem de certidão em qualquer fase do processo, mesmo depois de findo, para defesa de interesses legítimos dos requerentes podendo condicionar a sua utilização sob a pena de o arguido incorrer no crime de desobediência.
5. O arguido e o interessado, quando advogados, que não respeitem a natureza secreta do processo incorrem em responsabilidade disciplinar.', 'Disciplina e Processo', 176);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (177, 'Legitimidade Procedimental', 'As pessoas com interesse direto relativamente aos factos participados podem intervir no processo, requerendo e alegando o que tiverem por conveniente.', 'Disciplina e Processo', 177);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (178, 'Prescrição', '1. O procedimento disciplinar prescreve no prazo de três anos.
2. As infrações disciplinares que constituam simultaneamente ilícito penal prescrevem no mesmo prazo que o procedimento criminal, quando este for superior.
3. A prescrição é de conhecimento oficioso, podendo o advogado suspeito, no entanto, requerer a continuação do processo.', 'Disciplina e Processo', 178);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (179, 'Desistência', 'A desistência pelo interessado, pode ser requerida em qualquer fase do processo e extingue a responsabilidade disciplinar, salvo se a falta imputada afetar a dignidade do advogado visado ou o prestígio da Ordem ou da profissão.', 'Disciplina e Processo', 179);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (180, 'Apensação do Processo', 'Estando pendentes vários processos disciplinares contra o mesmo suspeito, são todos apensados ao antigo e proferida uma só decisão, exceto se da apensação resultar manifesto inconveniente.', 'Disciplina e Processo', 180);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (181, 'Instrução', '1. A instrução do processo disciplinar deve ultimar-se no prazo de 30 dias e aplica-se com as necessárias adaptações ao disposto no código do processo penal.
2. Salvo por razoes de complexidade da participação, poderá ser solicitado ao tribunal a prorrogação do prazo previsto no número um para mais 20 dias improrrogáveis, com indicação dos motivos.
3. Sem prejuízo de excepçoes previstas no presente estatuto, o tribunal de deontologia e disciplina compete apenas conhecer e julgar os processos disciplinares instruídos pelo conselho de deontologia e ética, nos termos dos presentes estatutos.
4. Compete ao relator assegurar o regular andamento da instrução do processo e manter a disciplina nos respetivos autos.', 'Disciplina e Processo', 181);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (182, 'Meios de Prova', '1. Na instrução do processo são admissíveis todos os meios de prova em direito permitido.
2. O relator deve notificar sempre o advogado do suspeito, querendo, responder sobre a matéria da participação.
3. O interessado e o suspeito podem requerer ao relator as diligências de prova que considerem necessárias ao apuramento da verdade.', 'Disciplina e Processo', 182);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (183, 'Termo da Instrução', '1. Finda a instrução, o relator profere despacho de acusação no prazo de 15 dias.
2. Se não se apurar suficientemente factos constitutivos da infração, não sendo proferido, por isso, o despacho de acusação, o relator apresenta o parecer na primeira sessão do Conselho, a fim de ser deliberado o arquivamento do processo, que este fique a aguardar a produção de melhor prova ou determinado que o mesmo prossiga com a realização de diligências complementares ou com o despacho de acusação, podendo ser designado novo relator de entre os membros do Conselho que tenham votado a continuação do processo.
SECÇÃO II
Acusação e Defesa', 'Disciplina e Processo', 183);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (184, 'Despacho de Acusação', '1. O despacho de acusação deve especificar a identificação do arguido, articulando discriminadamente os factos constitutivos da infração disciplinar; as circunstâncias em que os mesmos foram praticados, as circunstâncias agravantes ou atenuantes, indicando as normas estatutárias infringidas ou os preceitos no caso aplicáveis e o prazo para o exercício do direito à defesa.
2. É simultaneamente ordenada a junção aos autos do extrato do registo disciplinar do suspeito.', 'Disciplina e Processo', 184);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (185, 'Suspensão Preventiva', '1. Após o despacho de acusação pode ser requerida suspensão preventiva do suspeito perante o Tribunal de deontologia e disciplina, nos termos seguintes:
a) Existência de fortes possibilidades da prática de novas e graves infrações disciplinares ou tentativa de perturbar o andamento da instrução do processo;
b) Se o suspeito tiver sido pronunciado criminalmente por crime cometido no exercício da profissão ou por crime a que corresponda pena maior.
c) Se houver fortes indícios de que a continuação na efetividade da atividade da advocacia seja prejudicial ao prestígio e a dignidade da Ordem ou da função do Advogado na sociedade.
2. O requerimento da suspensão preventiva não pode exceder dois meses, no caso das a) e b) e seis meses no caso da al. c) e deve ser deliberada por dois terços dos membros do Conselho.
3. O Bastonário pode, mediante proposta igualmente aprovada por dois terços dos membros do Conselho, prorrogar por mais dois meses a suspensão.
4. A suspensão preventiva é sempre descontada nas penas de suspensão.
5. Os processos disciplinares com suspeito suspenso preventivamente preferem para o seu julgamento a todos os demais.', 'Disciplina e Processo', 185);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (186, 'Notificação da Acusação', '1. O suspeito é notificado da acusação pessoalmente ou remetida para o seu domicílio profissional ou para a sua residência, consoante a sua inscrição esteja ou não em vigor, com entrega da respetiva cópia.
2. Se o arguido se tiver ausentado do País e for desconhecida a sua residência, é notificado por edital, com o resumo da acusação, a afixar nas instalações da sede, na porta do seu domicílio profissional ou da última residência.', 'Disciplina e Processo', 186);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (187, 'Exercício do Direito de Defesa', '3. O prazo para a defesa é de vinte dias, a contar da notificação da acusação.
4. Se o arguido for notificado no estrangeiro ou por edital, o prazo para a defesa não pode ser inferior a trinta dias nem superior a sessenta dias.
5. O relator pode ainda, em caso de justo impedimento, admitir a defesa apresentada extemporaneamente.
6. O suspeito pode nomear em sua defesa um representante especialmente mandatado para esse efeito.
7. No caso de o suspeito não poder exercer esse direito o relator nomeia um curador, preferindo para o cargo a pessoa a quem competiria à tutela no caso de interdição.', 'Disciplina e Processo', 187);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (188, 'Apresentação da Defesa', '1. A defesa expõe clara e concisamente os factos e as razões que a fundamenta.
2. Com a defesa deve o arguido apresentar o rol de testemunhas, juntar documentos e requerer quaisquer diligências, que julgar pertinente, podendo ser recusados, quando manifestamente impertinentes ou desnecessárias para o apuramento dos factos.
3. O suspeito deve indicar os factos sobre os quais incidirá a prova ou quando convidado a fazê-lo, sob pena de indeferimento por falta de indicação.
4. Não podem ser indicados mais de 3 testemunhas por cada facto e o seu total não pode exceder, sem prejuízo do disposto no artigo seguinte, o número de 25.', 'Disciplina e Processo', 188);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (189, 'Realização de novas Diligências', '1. O relator pode ordenar a realização de novas diligências que considere necessárias para o apuramento da verdade.
2. O disposto no número anterior, é feita mediante despacho favorável do requerimento dirigido ao tribunal, e o prazo não deve ultrapassar sessenta dias, podendo ser elevado por mais 20 dias, ocorrendo motivo justificado, nomeadamente em razão da excecional complexidade do processo.', 'Disciplina e Processo', 189);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (190, 'Exame do Processo na Secretaria', '1. Durante o prazo para apresentação da defesa, o processo pode ser consultado na secretaria ou confiado arguido ou o advogado constituído para exame no seu domicílio ou escritório.
2. Seguidamente, no prazo máximo de dez dias, o processo é entregue ao tribunal para julgamento.
SECÇÃO III
Julgamento', 'Disciplina e Processo', 190);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (191, 'Prazo para Julgamento', '1. Os processos devem ser apresentados a julgamento no prazo de 30 dias a contar da data de distribuição.
2. Não sendo cumprido o prazo mencionado no número anterior, sem justificação, o processo é redistribuído a outro relator, devendo o facto ser obrigatoriamente comunicado ao Conselho Nacional para efeitos de promoção da acçao disciplinar contra o inadimplente.', 'Disciplina e Processo', 191);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (192, 'Nulidades insupríveis', '1. Constituição nulidade insuprível a falta de audiência do arguido com possibilidade de defesa e a omissão de diligências essenciais para a descoberta da verdade que ainda possam ultimamente realizar-se.
2. As restantes nulidades e irregularidades consideram-se sanadas se não forem arguidas na defesa ou, a ocorrerem posteriormente, no prazo de cinco dias, contados da data do seu conhecimento.', 'Disciplina e Processo', 192);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (193, 'Produção da prova', '1. Os factos apurados durante a produção da prova, cuja existência se considere essencial a boa decisão do processo, as circunstâncias agravantes e atenuantes, devem constar discriminadamente na acta.
2. A acta deverá ser assinada por todos os membros do tribunal, o arguido, o interessado e os respetivos advogados constituídos', 'Disciplina e Processo', 193);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (194, 'Projeto do Acórdão', 'Realizada a produção da prova a que se refere o artigo anterior, o relator elabora no prazo de quinze dias, o projeto do acórdão, do qual devem constar os factos e circunstâncias que se considere provada, qualificação jurídica, as normas infringidas e a pena aplicável.', 'Disciplina e Processo', 194);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (195, 'Acórdão', '3. O processo é dado para vista, por cinco dias, a cada membro do tribunal, findo o prazo de vista, o processo é presente em sessão para julgamento.
4. Se todos os membros do tribunal considerarem legal e justo o projeto na conferencia, é votado o e assinado o acórdão final.
5. Os votos de vencido devem ser fundamentados.', 'Disciplina e Processo', 195);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (196, 'Notificação', '1. Os acórdãos finais são notificados ao suspeito, aos interessados e ao Bastonário.
2. Se a participação tiver sido feita por Magistrado Judicial ou do Ministério Público, o acórdão final é igualmente notificado ao participante, ainda que sem interesse direto no processo e ao Presidente do Supremo Tribunal de Justiça e ao Procurador-Geral da Republica respetivamente.
3. A notificação do suspeito deve ser efetuada nos termos gerais da lei do processo penal.



SECÇÃO IV
Recursos Ordinários', 'Disciplina e Processo', 196);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (197, 'Deliberações Recorríveis', '1. Das deliberações do tribunal de deontologia e disciplina cabe recurso para o Conselho Nacional.
2. Das deliberações do Conselho Nacional cabe recurso para o tribunal de deontologia e disciplina, nos termos previstos nos presentes estatutos.
3. Das deliberações finais e executórias cabe recurso contencioso nos termos gerais de direito.
4. Não admitem recurso em qualquer instância as decisões de mero expediente ou de disciplina dos trabalhos.', 'Disciplina e Processo', 197);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (198, 'Legitimidade e Irrenunciabilidade', '1. Têm legitimidade para interpor recurso o suspeito, os interessados e o Bastonário.
2. Não é permitida a renúncia a recurso antes do conhecimento da decisão.', 'Disciplina e Processo', 198);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (199, 'Subida e Efeitos do Recurso', '1. Os recursos interpostos de despachos ou acórdãos interlocutórios sobem com o da decisão final.
2. Têm efeito suspensivo os recursos interpostos pelo Bastonário e os das decisões finais.', 'Disciplina e Processo', 199);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (200, 'Alegações', 'Admitido o recurso que subir imediatamente, são notificados o recorrente e o recorrido para apresentar alegações em prazo sucessivos de vinte dias, sendo-lhe para tanto facultada a consulta do processo.', 'Disciplina e Processo', 200);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (201, 'Baixa do Processo', 'Julgado definitivamente qualquer recurso, o processo baixa a Direção Nacional, que dará conhecimento ao órgão interessado.




SECÇÃO V
Recurso de Revisão', 'Disciplina e Processo', 201);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (202, 'Competência', 'A revisão das decisões com trânsito em julgado é da competência da plenária do tribunal da Ordem dos Advogados.', 'Disciplina e Processo', 202);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (203, 'Legitimidade', '1. O pedido de revisão das decisões deve ser formulado em requerimento fundamentado pelo interessado ou pelo suspeito condenado e, tendo este falecido, pelos seus descendentes, ascendentes, cônjuges ou irmãos.
2. O Bastonário pode apresentar ao Conselho da Ordem proposta fundamentada de revisão de decisões.', 'Disciplina e Processo', 203);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (204, 'Fundamento de Admissibilidade', 'A decisão com trânsito em julgado apenas pode ser revista nos seguintes casos:
a) Quando se tenham descoberto novos factos, novas provas documentais suscetíveis de alterar a decisão proferida;
b) Quando outra decisão transitada em julgado declarar falsos quaisquer elementos de prova suscetíveis de terem determinado a decisão revidenda;
c) Quando se mostrar, por exame psiquiátrico ou outras diligências, que a falta de integridade mental do suspeito condenado poderia ter determinado a sua inimputabilidade.', 'Disciplina e Processo', 204);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (205, 'Tramitação', '1. O pedido de revisão é submetido ao tribunal acompanhado das alegações do recorrente e dos meios probatórios que a este se oferecerem.
2. Recebido o pedido é efetuada a distribuição e requisição, destruído o processo ao mesmo relatório que proferiu a decisão revidenda.
3. Tratando-se de pedido do Bastonário, é notificado o suspeito condenado ou absolvido, consoante os casos, para alegar no prazo de vinte dias, apresentando simultaneamente as provas.', 'Disciplina e Processo', 205);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (206, 'Julgamento', '1. Realizadas as diligências requeridas, quando a elas houver lugar, o relator elabora o seu parecer, seguindo depois o processo para vista, por cinco dias, a cada um dos membros do tribunal.
2. Findo o prazo de vista, o processo é submetido à deliberação do coletivo que antes de decidir, pode ainda ordenar diligências.
3. Sendo ordenadas novas diligências, é efetuada a redistribuição do processo a um membro do Coletivo que tenha votado nesse sentido.', 'Disciplina e Processo', 206);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (207, 'Maioria Qualificada', 'A concessão da revisão tem de ser votada pela maioria de pelo menos dois terços dos membros do tribunal e da respetiva deliberação não cabe recurso.', 'Disciplina e Processo', 207);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (208, 'Baixa do Processo, Averbamento e Publicidade', '1. O processo, depois de julgado o pedido ou proposta de revisão, baixa ao Conselho respetiva, que o instrui e julga de novo, se a revisão tiver sido admitida.
2. No caso de absolvição, serão cancelados os averbamentos das decisões condenatórias.
3. Será dada publicidade ao acórdão de revisão quando resulte a absolvição e a decisão condenatória revista tenha sido publicitada.
SECÇÃO VI
Execução de Penas', 'Disciplina e Processo', 208);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (209, 'Competência do Bastonário', 'Compete a Direção Nacional dar execução a todas as decisões proferidas nos processos em que sejam suspeitos os advogados.', 'Disciplina e Processo', 209);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (210, 'Consequência da Falta de Cumprimento de', 'Decisões Disciplinares
É suspensa a inscrição do advogado punido até o cumprimento das decisões disciplinares.', 'Disciplina e Processo', 210);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (211, 'Início do Cumprimento da Pena de Suspensão', '1. O cumprimento das penas de suspensão ou proibição de exercício da atividade tem início a partir do dia imediato ao da publicação da respetiva decisão.
2. Se à data da publicação estiver suspensa ou cancelada a inscrição do suspeito, o cumprimento da pena de suspensão tem início a partir do dia imediato àquele em que tiver lugar o levantamento da suspensão da inscrição ou da reinscrição ou a partir do termo de anterior pena de suspensão.
CAPÍTULO V
Reabilitação do Advogado Expulso', 'Disciplina e Processo', 211);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (212, 'Regime', '1. O advogado punido com proibição do exercício da atividade pode ser reabilitado desde que cumulativamente tenham decorrido mais de 5 anos sobre a data em que se tornou definitiva a decisão que aplicou a pena de proibição e o reabilitado tenha revelado boa conduta, podendo, para o demostrar, utilizar meios de prova admitidos em direito.
2. Ao pedido de reabilitação é aplicável, com as necessárias adaptações, o disposto no artigo 306° CPP.
3. Concedida a reabilitação, nos termos do número anterior, o advogado reabilitado recupera plenamente os seus direitos e é dada a publicidade devida, nos termos previstos nos estatutos
TÍTULO VI
DISPOSIÇÕES FINAIS TRANSITÓRIAS', 'Disciplina e Processo', 212);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (213, 'Aplicação no Tempo das Incompatibilidades e Impedimentos', 'As incompatibilidades e impedimentos criados pelo presente Estatuto não prejudicam os direitos legalmente adquiridos.', 'Disciplina e Processo', 213);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (214, 'Exercício Ilegal da Advocacia', '1. O exercício da advocacia realizado de forma diversa do estabelecido no presente estatuto será considerado ilegal.
2. Os juízes, magistrados do Ministério Público, conservadores, notários e os responsáveis de repartições públicas têm a obrigação de comunicar à Ordem o exercício ilegal do patrocínio judiciário.', 'Disciplina e Processo', 214);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (215, 'Funcionários públicos', 'Os funcionários e agentes da administração pública, central e local, dos serviços personalizados do Estado e institutos públicos, com inscrição em vigor na Ordem, nos termos do anterior Estatuto, dispõem de um prazo de seis meses para declararem a sua situação de funcionário público.', 'Disciplina e Processo', 215);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (216, 'Período de transição', 'Fica estabelecido um período de transição de seis meses, para a preparação de eleições de novos órgãos da Ordem, nos termos do presente estatuto.', 'Disciplina e Processo', 216);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (217, 'Entrada em vigor do estatuto', 'As normas do presente estatuto entram em vigor com a eleição de novos órgãos da Ordem.', 'Disciplina e Processo', 217);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (218, 'Revisão', 'O presente estatuto pode ser revisto ao fim do quarto ano subsequente a sua entrada em vigor.', 'Disciplina e Processo', 218);
INSERT INTO estatutos_artigos (numero_artigo, titulo_artigo, conteudo, tema, ordem) VALUES (219, 'Revogação', 'É Revogado os artigos contrários do antigo Estatuto da Ordem dos Advogados da Guiné-Bissau publicado no Boletim Oficial, n.º 52, de 28 de Dezembro de 1992.', 'Disciplina e Processo', 219);
