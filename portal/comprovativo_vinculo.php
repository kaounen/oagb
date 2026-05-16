<?php
session_start();
require_once __DIR__ . '/../connect.php';

// Configuração de meses em português
$meses = [
    'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
    'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
    'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
    'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
];
$mes_atual = $meses[date('F')];

$est_id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;

// Validação de acesso público via token
$public_access = ($est_id && $token && $token === md5($est_id));

// Se não for acesso público, exige login do advogado
if (!$public_access && !isset($_SESSION['lawyer_id'])) {
    die("Acesso negado.");
}

$lid = $_SESSION['lawyer_id'] ?? null;

try {
    // Busca dados do estagiário com info do patrono
    if ($public_access) {
        // Acesso público: valida apenas pelo ID do estagiário
        $stmt = $pdo->prepare("SELECT e.*, a.nome_completo as patrono_nome, a.numero_registo as patrono_cedula, s.nome as sociedade_nome 
                               FROM advogados_estagiarios e 
                               JOIN advogados a ON e.orientador_id = a.id 
                               LEFT JOIN gestao_sociedades s ON e.sociedade_id = s.id 
                               WHERE e.id = ?");
        $stmt->execute([$est_id]);
    } else {
        // Acesso via Portal: garante que o advogado logado é o orientador
        $stmt = $pdo->prepare("SELECT e.*, a.nome_completo as patrono_nome, a.numero_registo as patrono_cedula, s.nome as sociedade_nome 
                               FROM advogados_estagiarios e 
                               JOIN advogados a ON e.orientador_id = a.id 
                               LEFT JOIN gestao_sociedades s ON e.sociedade_id = s.id 
                               WHERE e.id = ? AND e.orientador_id = ?");
        $stmt->execute([$est_id, $lid]);
    }

    // Usar PDO::FETCH_ASSOC para garantir que trabalhamos com array
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao processar solicitação: " . $e->getMessage());
}

if (!$data) {
    die("Comprovativo não disponível ou vínculo não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comprovativo de Vínculo de Estágio | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', sans-serif; background: #f0f0f0; padding: 50px 0; }
        .certificate {
            background: white;
            width: 800px;
            margin: 0 auto;
            padding: 80px;
            border: 1px solid #ddd;
            position: relative;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 20px solid #f8f8f8;
            pointer-events: none;
        }
        .header-logo { text-align: center; margin-bottom: 50px; }
        .header-logo img { width: 140px; opacity: 1; }
        
        h1 { font-family: 'Libre Baskerville', serif; color: #1a1a1a; text-align: center; font-size: 1.8rem; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px; }
        .content { font-size: 1.1rem; line-height: 1.8; color: #333; text-align: justify; }
        .highlight { font-weight: 700; color: #000; border-bottom: 1px solid #eee; }
        
        .footer-sig { margin-top: 80px; text-align: center; }
        .signature-line { width: 300px; border-top: 1px solid #333; margin: 0 auto 10px; }
        
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 6rem;
            color: rgba(0,0,0,0.03);
            font-weight: 900;
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
        }
        
        .meta-info { margin-top: 50px; font-size: 0.8rem; color: #999; border-top: 1px dashed #eee; padding-top: 20px; }

        @media print {
            body { background: white; padding: 0; }
            .certificate { box-shadow: none; border: none; width: 100%; padding: 40px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="container text-center no-print mb-4">
        <button onclick="window.print()" class="btn btn-dark px-5 py-2 fw-bold shadow-sm">
            <i class="fas fa-print me-2"></i> IMPRIMIR COMPROVATIVO (PDF)
        </button>
    </div>

    <div class="certificate">
        <div class="watermark">CERTIFICADO</div>
        
        <div class="header">
            <img src="<?php echo ROOT_URL; ?>/img/logo3.png" alt="OAGB Logo" class="logo-img">
            <h1 class="org-name">Ordem dos Advogados da Guiné-Bissau</h1>
            <div class="x-small opacity-50">Conselho Nacional</div>
        </div>

        <h1>Comprovativo de Vínculo de Estágio</h1>

        <div class="content">
            Pelo presente documento, a <span class="highlight">Ordem dos Advogados da Guiné-Bissau (OAGB)</span> certifica, para os devidos efeitos, que o(a) Sr(a). <span class="highlight"><?php echo $data['patrono_nome']; ?></span>, Advogado(a) com a Cédula Profissional nº <span class="highlight"><?php echo $data['patrono_cedula']; ?></span>, aceitou formalmente no dia <span class="highlight"><?php echo ($data['data_resposta_vinculo'] && $data['data_resposta_vinculo'] != '0000-00-00 00:00:00') ? date('d/m/Y \à\s H:i', strtotime($data['data_resposta_vinculo'])) : '---'; ?></span> a orientação jurídica e patrocínio do estágio profissional do(a) estagiário(a) <span class="highlight"><?php echo $data['nome_completo']; ?></span>, portador(a) da cédula de estagiário nº <span class="highlight"><?php echo $data['numero_registo']; ?></span>.
            <br><br>
            O estágio decorrerá sob a responsabilidade direta do orientador supracitado<?php echo $data['sociedade_nome'] ? ", integrado na firma/sociedade <span class='highlight'>".$data['sociedade_nome']."</span>," : ""; ?> em conformidade com o Estatuto da Ordem dos Advogados e demais regulamentação de estágio vigente.
        </div>

        <div class="footer-sig">
            <div class="mb-4 small opacity-75">Bissau, <?php echo date('d'); ?> de <?php echo $mes_atual; ?> de <?php echo date('Y'); ?></div>
            <div class="signature-line"></div>
            <div class="small fw-bold">ORCID / CONSELHO NACIONAL</div>
            <div class="x-small text-muted">Validado Digitalmente via Portal OAGB</div>
        </div>

        <div class="meta-info d-flex justify-content-between">
            <div>HASH: <?php echo md5($data['id'] . $data['data_resposta_vinculo']); ?></div>
            <div>Ref: OAGB-EST-<?php echo $data['id']; ?>-<?php echo date('Y'); ?></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/js/all.min.js"></script>
</body>
</html>
