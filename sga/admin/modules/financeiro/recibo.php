<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

// Fetch Payment Data
$stmt = $pdo->prepare("SELECT p.*, tp.nome as tipo_nome 
                       FROM finan_pagamentos p 
                       JOIN finan_tipos_pagamento tp ON p.tipo_pagamento_id = tp.id
                       WHERE p.id = ?");
$stmt->execute([$id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) { exit("Recibo não encontrado."); }

// Fetch Member Data
$table = ($payment['membro_tipo'] == 'estagiario') ? 'advogados_estagiarios' : 'advogados';
$stmt = $pdo->prepare("SELECT nome_completo, numero_registo, email FROM $table WHERE id = ?");
$stmt->execute([$payment['advogado_id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo OAGB #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; color: #333; line-height: 1.6; }
        .receipt-container { width: 800px; margin: 50px auto; padding: 40px; border: 1px solid #ddd; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header img { height: 80px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; margin-top: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .label { font-weight: bold; }
        .amount-box { margin-top: 40px; padding: 20px; background: #f9f9f9; border: 2px dashed #333; text-align: center; font-size: 28px; font-weight: bold; }
        .footer { margin-top: 60px; text-align: center; font-size: 12px; }
        .signature-line { margin-top: 40px; border-top: 1px solid #333; width: 300px; margin-left: auto; margin-right: auto; padding-top: 5px; }
        @media print {
            .no-print { display: none; }
            .receipt-container { border: none; margin: 0; padding: 20px; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; padding: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold;">IMPRIMIR RECIBO</button>
        <a href="index.php" style="margin-left: 10px; text-decoration: none; color: #666;">Voltar ao Financeiro</a>
    </div>

    <div class="receipt-container">
        <div class="header">
            <img src="/oagb/img/logo3.png" alt="OAGB">
            <div class="title">Recibo de Quotas / Taxas</div>
            <div>Ordem dos Advogados da Guiné-Bissau</div>
            <div style="font-size: 12px;">Palácio da Justiça, Bissau | NIF: 500000000</div>
        </div>

        <div class="info-row">
            <div><span class="label">Nº RECIBO:</span> #<?php echo str_pad($id, 6, '0', STR_PAD_LEFT); ?></div>
            <div><span class="label">DATA:</span> <?php echo date('d/m/Y', strtotime($payment['data_pagamento'])); ?></div>
        </div>

        <div class="info-row" style="margin-top: 30px;">
            <div><span class="label">MEMBRO:</span> <?php echo $member['nome_completo']; ?></div>
        </div>
        <div class="info-row">
            <div><span class="label">CÉDULA Nº:</span> <?php echo $member['numero_registo']; ?></div>
            <div><span class="label">CATEGORIA:</span> <?php echo strtoupper($payment['membro_tipo']); ?></div>
        </div>

        <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
            <div class="label">REFERENTE A:</div>
            <div style="font-size: 18px;"><?php echo $payment['tipo_nome']; ?></div>
            <?php if($payment['observacoes']): ?>
                <div style="font-size: 14px; color: #666; margin-top: 5px;">Obs: <?php echo $payment['observacoes']; ?></div>
            <?php endif; ?>
        </div>

        <div class="amount-box">
            TOTAL PAGO: <?php echo number_format($payment['valor_pago'], 0, ',', '.'); ?> CFA
        </div>

        <div class="info-row" style="margin-top: 20px;">
            <div><span class="label">MÉTODO:</span> <?php echo strtoupper($payment['metodo_pagamento']); ?></div>
        </div>

        <div class="footer">
            <div class="signature-line">Assinatura da Tesouraria</div>
            <p style="margin-top: 50px;">Este documento serve como prova de quitação perante a Ordem dos Advogados da Guiné-Bissau para os fins constantes nos estatutos.</p>
        </div>
    </div>

</body>
</html>
