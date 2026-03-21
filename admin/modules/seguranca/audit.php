<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Mock Security Check Data
$checks = [
    ['item' => 'Encriptação de Passwords (Bcrypt/Argon2)', 'status' => 'OK', 'desc' => 'Toda a base de dados de advogados e estagiários migrada para hashing moderno.'],
    ['item' => 'Proteção contra SQL Injection', 'status' => 'OK', 'desc' => 'Implementação sistemática de PDO Prepared Statements em todos os módulos 2.0.'],
    ['item' => 'Gestão de Sessões (OAGB-SEC-ID)', 'status' => 'OK', 'desc' => 'Sessões isoladas entre Portal e Area Administrativa.'],
    ['item' => 'Histórico de Auditoria (Logs)', 'status' => 'OK', 'desc' => 'Registo persistente de ações críticas (Pagamentos, Votos, Alteração de Regras).'],
    ['item' => 'Certificado SSL/TLS', 'status' => 'WARNING', 'desc' => 'Recomendado ativar HTTPS em produção para proteger transações de pagamento.'],
    ['item' => 'Isolamento de Dados Sensíveis', 'status' => 'OK', 'desc' => 'Votos são encriptados e desvinculados nominalmente para garantir o sigilo.'],
];
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Centro de Cibersegurança</h2>
        <div class="text-muted small">Monitorização de integridade, criptografia e auditoria de sistemas OAGB.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-dark w-auto px-4 shadow-sm py-3 fw-bold text-uppercase"><i class="fas fa-shield-virus me-2"></i> Executar Scan Global</button>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php foreach($checks as $c): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge py-2 px-3 <?php echo $c['status'] == 'OK' ? 'bg-success text-white' : 'bg-warning text-dark'; ?>">
                        <i class="fas <?php echo $c['status'] == 'OK' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-1"></i> <?php echo $c['status']; ?>
                    </span>
                    <i class="fas fa-lock text-muted opacity-25 fa-2x"></i>
                </div>
                <h6 class="fw-bold mb-1"><?php echo $c['item']; ?></h6>
                <p class="x-small text-muted mb-0 opacity-75"><?php echo $c['desc']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm p-5 bg-dark text-white">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="fw-bold mb-2">Relatório de Conformidade (Audit-V2)</h5>
            <p class="small text-white-50 mb-0">O ecossistema digital OAGB 2.0 cumpre os requisitos de segurança institucional para gastão de dados profissionais de advogados e processamento de pagamentos digitais. Recomenda-se a limpeza periódica de ficheiros temporários em <code>uploads/estagio/</code>.</p>
        </div>
        <div class="col-md-4 text-md-end mt-4 mt-md-0">
            <h1 class="fw-bold mb-0">100%</h1>
            <div class="small text-uppercase opacity-50 fw-bold">Integridade de Dados</div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
