<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { exit("Edição não encontrada."); }

$stmt = $pdo->prepare("SELECT * FROM newsletter_edicoes WHERE id = ?");
$stmt->execute([$id]);
$edicao = $stmt->fetch(PDO::FETCH_ASSOC);

$blocks = json_decode($edicao['conteudo_json'] ?? '[]', true);

if (!function_exists('resolve_newsletter_img')) {
    function resolve_newsletter_img($base_url, $rawPath) {
        if (empty($rawPath)) return '';
        $normalized = str_replace('\\', '/', trim((string)$rawPath));
        if (preg_match('#^https?://#i', $normalized)) {
            return $normalized;
        }
        $normalized = ltrim($normalized, '/');
        if (strpos($normalized, 'uploads/') === 0 || strpos($normalized, 'img/') === 0) {
            return $base_url . '/' . $normalized;
        }
        return $base_url . '/uploads/' . $normalized;
    }
}

// Pre-render the HTML content
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo htmlspecialchars($edicao['titulo']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style type="text/css">
        body { margin: 0; padding: 0; min-width: 100%; background-color: #f4f4f4 !important; font-family: Arial, sans-serif; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; }
        table { border-collapse: collapse !important; }
        .content { width: 100%; max-width: 600px; }
        .m-header { background-color: #111923 !important; padding: 40px 0; border-bottom: 6px solid #B1A276; }
        .m-title { color: #ffffff !important; font-size: 24px; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 2px; }
        .m-date { color: #B1A276 !important; font-size: 12px; margin-top: 10px; font-weight: bold; }
        .block-container { background-color: #ffffff; padding: 40px 30px; border-bottom: 1px solid #eeeeee; }
        .section-title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0 0 20px 0; color: #111923; }
        .editorial-text { font-size: 15px; line-height: 1.8; color: #444444; }
        .editorial-img { border-radius: 50%; border: 3px solid #B1A276; }
        .btn-link { background-color: #111923; color: #ffffff !important; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block; }
        .item-box { background-color: #f9f9f9; padding: 25px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #eeeeee; }
        .item-label { font-size: 11px; font-weight: bold; color: #B1A276; text-transform: uppercase; margin-bottom: 10px; }
        .item-title { font-size: 19px; font-weight: bold; color: #111923; margin-bottom: 10px; line-height: 1.3; }
        .item-meta { font-size: 12px; color: #888888; margin-bottom: 15px; }
        .m-footer { background-color: #111923 !important; padding: 40px 20px; color: #888888 !important; font-size: 12px; text-align: center; }
        .m-footer a { color: #B1A276 !important; text-decoration: none; font-weight: bold; }
    </style>
    <?php if(isset($_GET['print'])): ?>
    <script>window.onload = function() { window.print(); }</script>
    <?php endif; ?>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <center>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f4f4;">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="600" class="content" style="background-color: #ffffff;">
                        <!-- HEADER -->
                        <tr>
                            <td class="m-header" align="center">
                                <?php 
                                $base_url = (defined('SITE_URL') && SITE_URL !== 'https://oagb.gw') ? SITE_URL : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/oagb");
                                // Overwrite for production if not on localhost
                                if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false) {
                                    $base_url = "https://oagb.gw";
                                }
                                ?>
                                <img src="<?php echo $base_url; ?>/img/LogoOA.png" alt="OAGB" width="120" style="width: 120px; display: block;" />
                                <h1 class="m-title" style="color: #ffffff;"><?php echo htmlspecialchars($edicao['titulo']); ?></h1>
                                <div class="m-date"><?php echo date('d \d\e F, Y'); ?></div>
                            </td>
                        </tr>

                        <!-- BLOCKS -->
                        <tr>
                            <td>
                                <?php foreach($blocks as $block): 
                                    $bg = $block['bg_color'] ?? '#ffffff';
                                    $color = $block['text_color'] ?? '#333333';
                                ?>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td class="block-container" style="background-color: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                                                <?php if(!empty($block['title'])): ?>
                                                    <h3 class="section-title" style="color: <?php echo $color; ?>;"><?php echo $block['title']; ?></h3>
                                                <?php endif; ?>

                                                <?php if($block['type'] === 'editorial'): ?>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td class="editorial-text" style="color: <?php echo $color; ?>;">
                                                                <?php echo $block['content']; ?>
                                                            </td>
                                                            <?php if(!empty($block['image'])): ?>
                                                                <td width="150" align="right" valign="top" style="padding-left: 20px;">
                                                                    <img src="<?php echo $base_url; ?>/uploads/newsletter/<?php echo $block['image']; ?>" width="120" height="120" class="editorial-img" style="width: 120px; height: 120px;" />
                                                                </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </table>

                                                <?php elseif($block['type'] === 'generic'): ?>
                                                    <?php if(!empty($block['image'])): ?>
                                                        <img src="<?php echo $base_url; ?>/uploads/newsletter/<?php echo $block['image']; ?>" width="540" style="width: 100%; border-radius: 10px; margin-bottom: 25px;" />
                                                    <?php endif; ?>
                                                    <div style="line-height: 1.8;"><?php echo $block['content']; ?></div>
                                                    <?php if(!empty($block['link'])): ?>
                                                        <table border="0" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td style="padding-top: 20px;">
                                                                    <a href="<?php echo $block['link']; ?>" class="btn-link">SABER MAIS</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    <?php endif; ?>

                                                <?php elseif($block['type'] === 'site_content'): ?>
                                                    <div style="margin-bottom: 30px;"><?php echo $block['content'] ?? ''; ?></div>
                                                    <?php 
                                                    foreach($block['items'] ?? [] as $packed): 
                                                        list($origin, $item_id) = explode(':', $packed);
                                                        $item = null; $label = '';
                                                        
                                                        if($origin === 'noticia') {
                                                            $st = $pdo->prepare("SELECT * FROM noticias WHERE id = ?"); $st->execute([$item_id]); $item = $st->fetch(PDO::FETCH_ASSOC);
                                                            $label = 'Notícia';
                                                            $img_path = resolve_newsletter_img($base_url, $item['imagem_destaque'] ?? '');
                                                        } elseif($origin === 'anuncio') {
                                                            $st = $pdo->prepare("SELECT titulo, descricao as conteudo, created_at, imagem FROM anuncios WHERE id = ?"); $st->execute([$item_id]); $item = $st->fetch(PDO::FETCH_ASSOC);
                                                            $label = 'Anúncio';
                                                            $img_path = resolve_newsletter_img($base_url, $item['imagem'] ?? '');
                                                        } elseif($origin === 'agenda') {
                                                            $st = $pdo->prepare("SELECT titulo, descricao as conteudo, data_evento as created_at, imagem_destaque FROM agenda WHERE id = ?"); $st->execute([$item_id]); $item = $st->fetch(PDO::FETCH_ASSOC);
                                                            $label = 'Agenda / Evento';
                                                            $img_path = resolve_newsletter_img($base_url, $item['imagem_destaque'] ?? '');
                                                        } elseif($origin === 'pagina') {
                                                            $st = $pdo->prepare("SELECT titulo, conteudo, created_at, imagem FROM paginas_ordem WHERE id = ?"); $st->execute([$item_id]); $item = $st->fetch(PDO::FETCH_ASSOC);
                                                            $label = 'Página Institucional';
                                                            $raw_img = $item['imagem'] ?? '';
                                                            if (!empty($raw_img) && strpos($raw_img, 'paginas/') === false && strpos($raw_img, 'uploads/') === false) {
                                                                $raw_img = 'paginas/' . $raw_img;
                                                            }
                                                            $img_path = resolve_newsletter_img($base_url, $raw_img);
                                                        } else {
                                                            $st = $pdo->prepare("SELECT assunto as titulo, conteudo, data_emissao as created_at, imagem FROM pareceres_deliberacoes WHERE id = ?"); $st->execute([$item_id]); $item = $st->fetch(PDO::FETCH_ASSOC);
                                                            $label = ucfirst($origin);
                                                            $img_path = resolve_newsletter_img($base_url, $item['imagem'] ?? '');
                                                        }
                                                        
                                                        if($item):
                                                    ?>
                                                        <div class="item-box">
                                                            <div class="item-label"><?php echo $label; ?></div>
                                                            <div class="item-title"><?php echo $item['titulo']; ?></div>
                                                            <div class="item-meta"><?php echo date('d/m/Y', strtotime($item['created_at'] ?? $item['data_publicacao'] ?? 'now')); ?></div>
                                                            <?php if(!empty($img_path)): ?>
                                                                <img src="<?php echo $img_path; ?>" width="490" style="width: 100%; border-radius: 8px; margin-bottom: 20px;" />
                                                            <?php endif; ?>
                                                            <div style="font-size: 14px; line-height: 1.6; color: #444;">
                                                                <?php echo !empty($item['conteudo']) ? $item['conteudo'] : ($item['resumo'] ?? ''); ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; endforeach; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endforeach; ?>
                            </td>
                        </tr>

                        <!-- FOOTER -->
                        <?php 
                        $sig_newsletter = $pdo->query("SELECT valor FROM configuracoes_site WHERE chave = 'sig_newsletter'")->fetchColumn();
                        $signer_n = null;
                        if ($sig_newsletter) {
                            list($type_n, $sid_n) = explode(':', $sig_newsletter);
                            if ($type_n === 'b') {
                                $st_n = $pdo->prepare("SELECT nome_completo as nome, assinatura_url as assinatura, 'Bastonário' as cargo FROM bastonarios WHERE id = ?");
                            } else {
                                $st_n = $pdo->prepare("SELECT nome, assinatura, cargo FROM orgaos_sociais WHERE id = ?");
                            }
                            $st_n->execute([$sid_n]);
                            $signer_n = $st_n->fetch(PDO::FETCH_ASSOC);
                        }
                        ?>
                        <tr>
                            <td class="m-footer">
                                <?php if($signer_n && !empty($signer_n['assinatura'])): ?>
                                    <div style="margin-bottom: 25px;">
                                        <img src="<?php echo $base_url; ?>/uploads/assinaturas/<?php echo $signer_n['assinatura']; ?>" width="120" style="width: 120px; margin: 0 auto 10px; filter: brightness(0) invert(1); opacity: 0.8;">
                                        <div style="color: #ffffff; font-size: 13px; font-weight: bold;"><?php echo $signer_n['nome']; ?></div>
                                        <div style="color: #B1A276; font-size: 11px;"><?php echo $signer_n['cargo']; ?></div>
                                    </div>
                                <?php endif; ?>
                                <img src="<?php echo $base_url; ?>/img/LogoOA.png" alt="OAGB" width="70" style="display: block; margin: 0 auto 20px;" />
                                &copy; <?php echo date('Y'); ?> Ordem dos Advogados da Guiné-Bissau<br />
                                Avenida Combatentes da Liberdade da Pátria, Bissau
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
<?php
$html = ob_get_clean();

if (isset($_GET['download']) && $_GET['download'] === 'html') {
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="newsletter_oagb_'.time().'.html"');
    echo $html; exit;
}
if (isset($_GET['raw'])) {
    echo $html;
    // Don't exit if we're being included
    if (basename($_SERVER['PHP_SELF']) == 'preview_edition.php') exit;
}
echo $html;
?>
