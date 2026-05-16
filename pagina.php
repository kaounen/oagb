<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';

$slug = $_GET['s'] ?? '';

if (empty($slug)) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = ? AND ativo = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $pagina = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$pagina) {
        header("Location: index.php");
        exit;
    }
} catch (Exception $e) {
    header("Location: index.php");
    exit;
}

$page_title = $pagina->titulo;
$meta_description = $pagina->meta_description ?? substr(strip_tags($pagina->conteudo), 0, 160);

// Imagens Dinâmicas
$header_image = !empty($pagina->imagem) ? 'uploads/paginas/' . $pagina->imagem : 'uploads/justice-symbol-legal-law.jpg';
$card_image = !empty($pagina->imagem_card) ? 'uploads/paginas/' . $pagina->imagem_card : '';

// Botões Inteligentes
$btn1_url = !empty($pagina->botao1_file) ? 'uploads/paginas/docs/' . $pagina->botao1_file : ($pagina->botao1_link ?? '#');
$btn2_url = !empty($pagina->botao2_file) ? 'uploads/paginas/docs/' . $pagina->botao2_file : ($pagina->botao2_link ?? '#');

// Configurações Estéticas
$t_color = $pagina->titulo_cor ?? '#4D1C21';
$t_size = $pagina->titulo_tamanho ?? '2.5rem';
$txt_color = $pagina->texto_cor ?? '#444444';
$txt_size = $pagina->texto_tamanho ?? '1.05rem';
$f_family = $pagina->fonte_familia ?? "'Open Sans', sans-serif";
$card_enabled = $pagina->card_bg ?? 1;
$parallax_enabled = $pagina->parallax ?? 0;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style id="page-styles-v2">
        :root {
            --primary-gold: #B1A276;
            --primary-maroon: #4D1C21;
            --dark-navy: #111923;
            --page-font: <?php echo $f_family; ?>;
            --nav-font: 'Open Sans', sans-serif;
        }
        
        html, body { overflow-x: hidden !important; width: 100%; position: relative; }
        body { font-family: var(--page-font); background-color: #fafafa; color: <?php echo $txt_color; ?>; font-size: <?php echo $txt_size; ?>; }
        
        .top-bar, .navbar, .subpage-breadcrumb-bar, .mobile-header-subpage, 
        .nav-link, .navbar-brand, footer, .footer, .contact-info, 
        .top-header, .me-4, .small.text-white-50, 
        .dropdown-menu, .dropdown-item, .search-form, .form-control, 
        .goog-te-menu-value, #google_translate_element { 
            font-family: var(--nav-font) !important; 
        }

        .bg-header { background-attachment: <?php echo $parallax_enabled ? 'fixed' : 'scroll'; ?> !important; transition: background-attachment 0.5s ease; }

        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        .page-content-box { 
            background: <?php echo $card_enabled ? '#fff' : 'transparent'; ?>; 
            border-radius: 20px; 
            padding: <?php echo $card_enabled ? '50px' : '0'; ?>; 
            border: <?php echo $card_enabled ? '1px solid #f0ece4' : 'none'; ?>; 
            box-shadow: <?php echo $card_enabled ? '0 10px 30px rgba(0,0,0,0.02)' : 'none'; ?>; 
            margin-top: 0;
        }
        .page-content-box h2 { font-family: 'Libre Baskerville', serif; color: <?php echo $t_color; ?>; font-weight: 700; margin-bottom: 30px; font-size: <?php echo $t_size; ?>; }
        .page-content-box .content-body { line-height: 1.8; color: <?php echo $txt_color; ?>; }
        .page-content-box img.main-img { border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        .sidebar-widget { border-radius: 20px; padding: 30px; margin-bottom: 30px; transition: .3s; position: relative; overflow: hidden; margin-top: 0; }
        .sw-default { background: #fff; border: 1px solid #f0ece4; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .sw-urgent { background: #fdf2f2; border-left: 6px solid var(--primary-maroon); color: var(--primary-maroon); }
        .sw-card { background: #fff; border: 1px solid var(--primary-gold); box-shadow: 0 15px 40px rgba(177, 162, 118, 0.1); }
        .sw-gold { background: #fdfbf7; border-left: 6px solid var(--primary-gold); color: #856404; }
        .sw-dark { background: var(--dark-navy); color: #fff; border: 1px solid rgba(255,255,255,0.1); }

        .sidebar-watermark { position: absolute; right: -15px; bottom: -15px; font-size: 100px; color: rgba(0,0,0,0.03); transform: rotate(-15deg); pointer-events: none; }
        .sidebar-link { display: flex; align-items: center; padding: 12px 18px; border-radius: 10px; background: rgba(0,0,0,0.02); margin-bottom: 8px; text-decoration: none !important; color: inherit; font-weight: 600; transition: all 0.3s; font-size: 0.95rem; position: relative; z-index: 2; }
        .sidebar-link:hover { background: var(--primary-maroon); color: #fff !important; transform: translateX(5px); }
        .sidebar-link i { margin-right: 12px; color: var(--primary-gold); width: 18px; text-align: center; }
        .sidebar-title { font-family: 'Libre Baskerville', serif; font-weight: 700; margin-bottom: 25px; padding-bottom: 12px; border-bottom: 2px solid var(--primary-gold); display: inline-block; position: relative; z-index: 2; }
        
        .btn-maroon-outline { border: 2px solid var(--primary-maroon); color: var(--primary-maroon); transition: .3s; }
        .btn-maroon-outline:hover { background: var(--primary-maroon) !important; color: #fff !important; }

        /* Mobile specific breadcrumbs overlaid on bottom of header */
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { 
                background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; 
                z-index: 1045 !important; pointer-events: auto !important; 
            }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { 
                font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            }
            .mobile-breadcrumb-bar .bc-active { font-weight: 500; font-size: 0.72rem !important; }
            .mobile-breadcrumb-bar .quick-links a { 
                border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; 
            }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }

        @media (max-width: 991.98px) {
            .page-content-box { padding: 30px 20px; margin-top: 0; position: relative; z-index: 10; }
            section.py-5 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
            .container { padding-left: 20px; padding-right: 20px; }
            .row.g-5 { --bs-gutter-x: 0; }
            .main-img { margin-left: -20px; margin-right: -20px; width: calc(100% + 40px) !important; border-radius: 0 !important; }

        }
    </style>
</head>

<body>

    <?php include 'includes/topbar.php'; ?>

    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active"><?php echo $pagina->titulo; ?></span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => $pagina->titulo, 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-4">
            <div class="row g-5 align-items-start">
                
                <?php
                $layout = $pagina->layout_tipo ?? '2col_right';
                $main_col_class = ($layout == '1col') ? 'col-lg-12' : (($layout == '3col') ? 'col-lg-6' : 'col-lg-8');
                $show_sidebar = ($pagina->mostrar_sidebar ?? 1) && ($layout != '1col');
                ?>

                <div class="<?php echo $main_col_class; ?> <?php echo ($layout == '2col_left') ? 'order-lg-2' : ''; ?> pt-0">
                    <div class="page-content-box wow fadeInUp">
                        
                        <?php if(($pagina->imagem_posicao ?? 'topo') == 'topo' && !empty($card_image)): ?>
                            <img src="<?php echo $card_image; ?>" class="img-fluid main-img w-100" alt="<?php echo $pagina->titulo; ?>">
                        <?php endif; ?>

                        <h2><?php echo $pagina->titulo; ?></h2>
                        
                        <div class="content-body">
                            <?php 
                            $content = $pagina->conteudo;
                            if (($pagina->imagem_posicao ?? 'topo') == 'meio' && !empty($card_image)) {
                                $img_tag = '<img src="'.$card_image.'" class="img-fluid rounded-4 my-4 shadow-sm w-100" alt="'.$pagina->titulo.'">';
                                $paragraphs = explode('</p>', $content);
                                if (count($paragraphs) > 2) {
                                    $paragraphs[1] .= '</p>' . $img_tag;
                                    $content = implode('</p>', $paragraphs);
                                } else { $content = $img_tag . $content; }
                            }
                            echo $content; 
                            ?>
                        </div>

                        <?php if($pagina->mostrar_botoes ?? 0): ?>
                            <div class="mt-5 pt-4 border-top d-flex flex-wrap gap-3">
                                <?php if(!empty($pagina->botao1_texto)): ?>
                                    <a href="<?php echo $btn1_url; ?>" class="btn btn-maroon-outline rounded-pill px-4 py-2 fw-bold" <?php echo !empty($pagina->botao1_file) ? 'download' : ''; ?>><i class="<?php echo !empty($pagina->botao1_file) ? 'fas fa-file-download' : 'fas fa-external-link-alt'; ?> me-2"></i><?php echo $pagina->botao1_texto; ?></a>
                                <?php endif; ?>

                                <?php if(!empty($pagina->botao2_texto)): ?>
                                    <a href="<?php echo $btn2_url; ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold" <?php echo !empty($pagina->botao2_file) ? 'download' : ''; ?>><i class="<?php echo !empty($pagina->botao2_file) ? 'fas fa-file-download' : 'fas fa-link'; ?> me-2"></i><?php echo $pagina->botao2_texto; ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($show_sidebar): ?>
                    <div class="<?php echo ($layout == '3col') ? 'col-lg-3' : 'col-lg-4'; ?> <?php echo ($layout == '2col_left') ? 'order-lg-1' : ''; ?> pt-0">
                        <div class="sidebar-widget sw-<?php echo ($pagina->sidebar_widget ?? 'default'); ?>">
                            <?php if(!empty($pagina->sidebar_icon)): ?>
                                <i class="<?php echo $pagina->sidebar_icon; ?> sidebar-watermark"></i>
                            <?php endif; ?>

                            <?php if(!empty($pagina->sidebar_titulo)): ?>
                                <h5 class="sidebar-title"><?php echo $pagina->sidebar_titulo; ?></h5>
                            <?php endif; ?>

                            <div class="mt-0">
                                <?php 
                                $bullet_icon = !empty($pagina->sidebar_icon) ? $pagina->sidebar_icon : 'fas fa-chevron-right';
                                if(!empty($pagina->sidebar_menu_categoria)) {
                                    $stmt_menu = $pdo->prepare("SELECT titulo, slug FROM paginas_ordem WHERE menu_categoria = ? AND ativo = 1 ORDER BY ordem_menu ASC, titulo ASC");
                                    $stmt_menu->execute([$pagina->sidebar_menu_categoria]);
                                    $links = $stmt_menu->fetchAll(PDO::FETCH_OBJ);
                                    foreach($links as $link) {
                                        echo '<a href="pagina.php?s='.$link->slug.'" class="sidebar-link"><i class="'.$bullet_icon.'"></i> '.$link->titulo.'</a>';
                                    }
                                } elseif($pagina->sidebar_widget == 'whats_new') {
                                    // Fetch latest 3 news
                                    $stmt_news = $pdo->query("SELECT id, titulo, data_publicacao, imagem_destaque, slug FROM noticias WHERE ativo = 1 ORDER BY data_publicacao DESC LIMIT 3");
                                    $news = $stmt_news->fetchAll(PDO::FETCH_OBJ);
                                    foreach($news as $n) {
                                        $n_img = !empty($n->imagem_destaque) ? 'uploads/noticias/'.$n->imagem_destaque : 'uploads/OAGB-Placeholder.jpg';
                                        echo '<a href="artigo.php?id='.$n->id.'&slug='.$n->slug.'" class="sidebar-link d-block p-2 mb-3 bg-white border rounded-3 shadow-sm" style="font-size:0.85rem;">';
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<img src="'.$n_img.'" class="rounded me-2" style="width:50px; height:50px; object-fit:cover;">';
                                        echo '<div><div class="fw-bold lh-1 mb-1">'.$n->titulo.'</div><div class="x-small text-muted">'.date('d/m/Y', strtotime($n->data_publicacao)).'</div></div>';
                                        echo '</div></a>';
                                    }
                                } elseif($pagina->sidebar_widget == 'default' && empty($pagina->sidebar_conteudo)) {
                                    echo '<a href="ordem-dos-advogados.php" class="sidebar-link"><i class="fas fa-history"></i> Apresentação e História</a>';
                                    echo '<a href="bastonario-ordem.php" class="sidebar-link"><i class="fas fa-user-tie"></i> O Bastonário</a>';
                                    echo '<a href="orgaos-sociais.php" class="sidebar-link"><i class="fas fa-sitemap"></i> Órgãos Sociais</a>';
                                }
                                ?>
                            </div>

                            <?php if(!empty($pagina->sidebar_conteudo)): ?>
                                <div class="mt-2 position-relative" style="z-index: 5;">
                                    <?php echo $pagina->sidebar_conteudo; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top shadow-lg" style="background-color: var(--primary-maroon); border-color: var(--primary-maroon);"><i class="bi bi-arrow-up text-white"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
