<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];

// Check Quota Status
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND tipo_pagamento_id = 1 
                       AND status = 'confirmado' AND MONTH(data_pagamento) = MONTH(NOW()) AND YEAR(data_pagamento) = YEAR(NOW())");
$stmt->execute([$lid]);
if ($stmt->fetchColumn() == 0) { exit("Acesso Bloqueado: Quotas em atraso."); }

// Fetch Lawyer Info
$stmt = $pdo->prepare("SELECT * FROM advogados WHERE id = ?");
$stmt->execute([$lid]);
$row = $stmt->fetch();

$data_emissao = date('d \d\e F \d\e Y');
$meses_pt = [
    'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março', 'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
    'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro', 'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
];
$data_emissao = strtr($data_emissao, $meses_pt);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>CERTIDÃO DIGITAL - <?php echo $row['numero_registo']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Libre Baskerville', serif; margin: 0; padding: 0; background: #e0e0e0; }
        .cert-page { 
            width: 210mm; height: 297mm; padding: 25mm; 
            margin: 20mm auto; background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative; box-sizing: border-box;
            background-image: url('https://oagb.gw/img/watermark.png'); 
            background-repeat: no-repeat; background-position: center; background-size: 50%;
        }
        .header { text-align: center; margin-bottom: 50px; }
        .header img { height: 75px; margin-bottom: 10px; }
        .header .org-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; color: #111923; text-transform: uppercase; letter-spacing: 2px; }
        
        .title { text-align: center; margin-bottom: 60px; }
        .title h1 { border-bottom: 2px solid #B1A276; display: inline-block; padding-bottom: 5px; font-size: 24px; font-weight: 700; color: #111923;  text-transform: uppercase; }
        
        .content { line-height: 2; text-align: justify; font-size: 16px; margin-bottom: 80px; }
        .content b { text-transform: uppercase; }
        
        .footer-sig { text-align: center; margin-top: 100px; margin-bottom: 40px; }
        .sig-line { border-top: 1px solid #111923; width: 300px; margin: 0 auto 5px; }
        .sig-name { font-weight: 700; text-transform: uppercase; font-size: 14px; }
        .sig-role { font-size: 12px; font-style: italic; opacity: 0.7; }
        
        .meta-info { position: absolute; bottom: 25mm; width: calc(100% - 50mm); display: flex; justify-content: space-between; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; font-family: sans-serif; }
        
        @media print {
            body { background: white; padding: 0; }
            .cert-page { margin: 0; box-shadow: none; border: none; }
            .btn-print { display: none; }
        }
        
        .btn-print { position: fixed; top: 20px; right: 20px; background: #B1A276; color: white; border: none; padding: 15px 30px; border-radius: 50px; cursor: pointer; font-weight: 700; shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 999; display: flex; align-items: center; gap: 10px; }
        .btn-print:hover { transform: scale(1.05); background: #9a8c63; }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">
        <svg fill="currentColor" width="20" height="20" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> 
        IMPRIMIR / GUARDAR PDF
    </button>

    <div class="cert-page">
        <div class="header">
            <img src="/oagb/img/logo3.png" alt="OAGB LOGO">
            <div class="org-name">Ordem dos Advogados da Guiné-Bissau</div>
        </div>
        
        <div class="title">
            <h1>Certidão de Nada a Declarar</h1>
        </div>
        
        <div class="content">
            Certifica-se, para os devidos efeitos e a pedido do interessado, que o(a) <b>Dr(a). <?php echo $row['nome_completo']; ?></b>, 
            portador(a) da Cédula Profissional número <b><?php echo $row['numero_registo']; ?></b>, com inscrição em vigor nesta Ordem, 
            encontra-se na presente data com a sua situação contributiva perfeitamente <b>REGULARIZADA</b> perante a Tesouraria desta Instituição.
            <br><br>
            Mais se certifica que, à data de hoje, não consta no processamento interno qualquer debito ou incumprimento de natureza pecuniária 
            relativo a quotas mensais ou outras taxas institucionais obrigatorias para o exercicio da advocacia na Guiné-Bissau.
            <br><br>
            A presente certidão é emitida por via electrónica e automatizada, sendo válida por 30 (trinta) dias a contar da data da sua emissão.
        </div>
        
        <div class="footer-sig">
            <div class="date mb-5">Bissau, <?php echo $data_emissao; ?></div>
            <br><br><br>
            <div class="sig-line"></div>
            <div class="sig-name">O Bastonário</div>
            <div class="sig-role">Ordem dos Advogados da Guiné-Bissau</div>
        </div>
        
        <div class="meta-info">
            <span>Cod. Verificação: <?php echo strtoupper(substr(md5($lid . time()), 0, 8)); ?></span>
            <span>Documento emitido digitalmente em oagb.gw (Área Reservada)</span>
            <span>Ref: PORTAL-<?php echo date('Y'); ?>/<?php echo $lid; ?></span>
        </div>
    </div>

</body>
</html>
