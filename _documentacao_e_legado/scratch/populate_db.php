<?php
require_once 'connect.php';

// 1. Ensure finan_tipos_pagamento has data
$pdo->exec("INSERT IGNORE INTO finan_tipos_pagamento (id, nome) VALUES (1, 'Quota Mensal - Advogado'), (2, 'Quota Mensal - Estagiário')");

// 2. Add some Advocates (Patrons)
$patrons = [
    ['CP-001/24', 'Dr. Carlos Buampé', 'M', 'carlos.buampe@email.gw', 'ativo', '2020-01-01', 'Bissau'],
    ['CP-002/24', 'Dra. Maria Siga', 'F', 'maria.siga@email.gw', 'ativo', '2018-05-15', 'Bafatá']
];

foreach ($patrons as $p) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO advogados (numero_registo, nome_completo, genero, email, status, data_inscricao, regiao) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute($p);
}

// 3. Add some Interns
$interns = [
    ['EST-001/24', 'António Lopes', 'M', 'antonio.lopes@email.gw', 1, '2024-01-10', 'Bissau', 'ativo'],
    ['EST-002/24', 'Binta Camará', 'F', 'binta.camara@email.gw', 1, '2024-02-15', 'Bissau', 'ativo'],
    ['EST-003/24', 'João Djaló', 'M', 'joao.djalo@email.gw', 2, '2024-03-20', 'Bafatá', 'ativo']
];

foreach ($interns as $i) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO advogados_estagiarios (numero_registo, nome_completo, genero, email, orientador_id, data_inicio_estagio, regiao, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute($i);
}

// 4. Add some Payments for "Últimas Movimentações"
$payments = [
    [1, 1, 15000, 'transferencia', 'confirmado', date('Y-m-d', strtotime('-5 days'))],
    [2, 1, 15000, 'deposito', 'pendente', date('Y-m-d', strtotime('-2 days'))],
    [4, 2, 5000, 'transferencia', 'confirmado', date('Y-m-d', strtotime('-10 days'))],
    [5, 2, 5000, 'transferencia', 'confirmado', date('Y-m-d', strtotime('-1 days'))]
];

foreach ($payments as $pay) {
    $stmt = $pdo->prepare("INSERT INTO finan_pagamentos (advogado_id, tipo_pagamento_id, valor_pago, metodo_pagamento, status, data_pagamento) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute($pay);
}

// 5. Add some Reports for Interns
$reports = [
    [4, 1, 'REL_EST_4_1715000000.pdf', 'validado', 'Bom desempenho.', date('Y-m-d', strtotime('-15 days'))],
    [5, 1, 'REL_EST_5_1715100000.pdf', 'pendente', null, date('Y-m-d', strtotime('-3 days'))]
];

foreach ($reports as $r) {
    $stmt = $pdo->prepare("INSERT INTO gestao_estagio_relatorios (estagiario_id, orientador_id, ficheiro_pdf, status, observacoes, data_submissao) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute($r);
}

echo "Database populated successfully!\n";
?>
