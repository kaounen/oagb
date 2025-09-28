<?php
/**
 * Meta tags para SEO e compartilhamento em redes sociais
 * Incluir este arquivo no <head> de cada página
 */

// Configurações padrão
$site_url = "https://oagb.gw";
$site_name = "Ordem dos Advogados da Guiné-Bissau";
$default_image = $site_url . "/img/logo3.png";
$default_description = "Site oficial da Ordem dos Advogados da Guiné-Bissau (OAGB)";

// Meta tags específicas da página (podem ser definidas antes de incluir este arquivo)
$meta_title = isset($meta_title) ? $meta_title : $page_title ?? "OAGB - Ordem dos Advogados da Guiné-Bissau";
$meta_description = isset($meta_description) ? $meta_description : $default_description;
$meta_image = isset($meta_image) ? $meta_image : $default_image;
$meta_url = isset($meta_url) ? $meta_url : $site_url . $_SERVER['REQUEST_URI'];
$meta_type = isset($meta_type) ? $meta_type : "website";

// Garantir URLs absolutas (compatível PHP 7.4+)
if (strpos($meta_image, 'http') !== 0) {
    $meta_image = $site_url . "/" . ltrim($meta_image, '/');
}
?>

<!-- Meta Tags Básicas -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
<meta name="keywords" content="OAGB, Ordem dos Advogados, Guiné-Bissau, Advogados, Direito, Justiça">
<meta name="author" content="Ordem dos Advogados da Guiné-Bissau">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo htmlspecialchars($meta_type); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($meta_url); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($meta_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($meta_image); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="<?php echo htmlspecialchars($site_name); ?>">
<meta property="og:locale" content="pt_PT">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo htmlspecialchars($meta_url); ?>">
<meta property="twitter:title" content="<?php echo htmlspecialchars($meta_title); ?>">
<meta property="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">
<meta property="twitter:image" content="<?php echo htmlspecialchars($meta_image); ?>">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo htmlspecialchars($meta_url); ?>">

<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link href="img/favicon.ico" rel="icon">
