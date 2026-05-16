<?php
require_once __DIR__ . '/../../includes/db.php';
$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM advogados WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if(!$row) exit("Membro não encontrado.");
} catch (PDOException $e) { exit("Erro ao processar."); }

$data_emissao = date('d \d\e F \d\e Y');
$meses_pt = [
    'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março', 'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
    'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro', 'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
];
$data_emissao = strtr($data_emissao, $meses_pt);

// Fetch Assigned Signer for Certificate
$sig_certidao = $pdo->query("SELECT valor FROM configuracoes_site WHERE chave = 'sig_certidao'")->fetchColumn();
$signer = null;
if ($sig_certidao) {
    list($type, $sid) = explode(':', $sig_certidao);
    if ($type === 'b') {
        $st = $pdo->prepare("SELECT nome_completo as nome, assinatura_url as assinatura, 'Bastonário da Ordem dos Advogados' as cargo FROM bastonarios WHERE id = ?");
    } else {
        $st = $pdo->prepare("SELECT nome, assinatura, cargo FROM orgaos_sociais WHERE id = ?");
    }
    $st->execute([$sid]);
    $signer = $st->fetch(PDO::FETCH_ASSOC);
}

// Fallback to Current Bastonario if no specific signer assigned
if (!$signer) {
    $signer = $pdo->query("SELECT nome_completo as nome, assinatura_url as assinatura, 'Bastonário da Ordem dos Advogados' as cargo FROM bastonarios WHERE is_atual = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>CERTIDÃO DE NADA A DECLARAR - <?php echo $row['numero_registo']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Libre Baskerville', serif; margin: 0; padding: 0; background: #e0e0e0; }
        .cert-page { 
            width: 210mm; height: 297mm; padding: 25mm; 
            margin: 20mm auto; background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative; box-sizing: border-box;
            background-image: url('../../../img/logo-oa-big.png'); 
            background-repeat: no-repeat; background-position: center; background-size: 50%;
            background-blend-mode: overlay; opacity: 0.96;
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
            <img src="../../../img/logo3.png" alt="OAGB LOGO">
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
            <div class="date mb-4">Bissau, <?php echo $data_emissao; ?></div>
            
            <?php if ($signer && !empty($signer['assinatura'])): ?>
                <div class="sig-image-wrap" style="margin-bottom: -15px;">
                    <img src="../../../uploads/assinaturas/<?php echo $signer['assinatura']; ?>" style="max-height: 80px; width: auto; margin: 0 auto;">
                </div>
            <?php else: ?>
                <br><br><br>
            <?php endif; ?>

            <div class="sig-line"></div>
            <div class="sig-name"><?php echo $signer ? $signer['nome'] : 'O Bastonário'; ?></div>
            <div class="sig-role"><?php echo $signer ? $signer['cargo'] : 'Ordem dos Advogados'; ?></div>
        </div>
        
        <div class="meta-info">
            <span>Cod. Verificação: <?php echo strtoupper(substr(md5($id . time()), 0, 8)); ?></span>
            <span>Documento emitido digitalmente em oagb.gw</span>
            <span>Ref: CEF-<?php echo date('Y'); ?>/<?php echo $id; ?></span>
        </div>
    </div>

</body>
</html>
