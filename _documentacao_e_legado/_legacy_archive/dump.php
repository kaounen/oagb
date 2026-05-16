<?php
require 'connect.php';

$pdo->query("TRUNCATE TABLE documentos_publicos");

$dummy_data = [
    // Pareceres
    ['Parecer Jurídico sobre Novo CPC', 'parecer', 'PAR-2023-01', 'Análise detalhada sobre as implicações do novo Código de Processo Civil na advocacia nacional.', '2023-11-15'],
    ['Deliberação do Conselho Geral nº 4/2023', 'deliberacao', 'DEL-2023-04', 'Aprovação do regulamento interno de estágios e quotas anuais.', '2023-10-20'],
    ['Parecer Técnico - Reforma Fiscal', 'parecer', 'PAR-2023-02', 'Posição oficial da OAGB perante a proposta de alteração da matriz fiscal.', '2023-09-05'],
    
    // Comunicados
    ['Comunicado aos Membros - Pagamento de Quotas', 'comunicado', 'COM-2024-01', 'Informação sobre o processo de regularização de quotas para o ano em curso.', '2024-01-10'],
    ['Encerramento da Secretaria', 'comunicado', 'COM-2023-12', 'Aviso sobre o período de encerramento da secretaria para balanço anual e manutenções.', '2023-12-20'],
    ['Abertura do Ano Judicial', 'comunicado', 'COM-2023-03', 'Convite e orientações para a cerimónia de Abertura Solene do Ano Judicial.', '2023-03-01'],

    // Publicações
    ['Revista da Ordem - 1º Semestre', 'publicacao', 'REV-2023-01', 'Publicação semestral com artigos jurídicos, jurisprudência relevante e crónicas.', '2023-06-30'],
    ['Manual de Práticas Processuais', 'publicacao', 'PUB-2023-02', 'Guia de bolso para advogados estagiários com resumos processuais essenciais.', '2023-08-15'],
    ['Código Deontológico Comentado', 'publicacao', 'PUB-2023-01', 'Edição especial com comentários e exemplos práticos da aplicação do código de ética.', '2023-02-10'],

    // Orçamento
    ['Orçamento Anual Aprovado 2024', 'orcamento', 'ORC-2024', 'Demonstrativo do plano financeiro, receitas e despesas estimadas para o ano civil de 2024.', '2023-12-28'],
    ['Relatório e Contas 2023', 'orcamento', 'REL-2023', 'Balanço financeiro e transparência da execução orçamental do ano anterior.', '2024-02-15']
];

$stmt = $pdo->prepare("INSERT INTO documentos_publicos (titulo, tipo, numero_documento, descricao, data_documento, ativo, arquivo) VALUES (?, ?, ?, ?, ?, 1, '#')");

foreach ($dummy_data as $row) {
    if (!$stmt->execute($row)) {
        print_r($stmt->errorInfo());
    }
}
echo "Dummy data populated!";
?>
