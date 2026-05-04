<?php
/**
 * Componente de Cabeçalho de Página Reutilizável
 */

if (!isset($page_title)) $page_title = "Página";
if (!isset($breadcrumbs)) {
    $breadcrumbs = [
        'Início' => 'index.php',
        $page_title => '#'
    ];
}
if (!isset($has_header_image)) $has_header_image = false;

// If no image is provided, we use a sophisticated gradient that matches the OAGB brand
// and provides the "photo space" requested.
$header_style = $has_header_image 
    ? "background: linear-gradient(rgba(9, 30, 62, 0.75), rgba(9, 30, 62, 0.75)), url('{$header_image_path}') center center no-repeat; background-size: cover;" 
    : "background: linear-gradient(135deg, #091E3E 0%, #0b1c31 100%); position: relative; overflow: hidden;";

$text_color = "text-white"; // Always white for better contrast on the darkened headers
?>

<!-- Header Start -->
<div class="container-fluid page-hero mb-0 shadow-sm" style="<?php echo $header_style; ?>">
    <!-- Decorative Pattern for when there's no photo -->
    <?php if (!$has_header_image): ?>
    <div class="header-pattern"></div>
    <?php endif; ?>

    <div class="container pb-5">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-9 animated fadeIn">
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0 animated slideInDown">
                        <?php 
                        $count = count($breadcrumbs);
                        $i = 1;
                        foreach($breadcrumbs as $label => $link): 
                            $is_last = ($i == $count);
                        ?>
                            <li class="breadcrumb-item">
                                <?php if(!$is_last): ?>
                                    <a class="text-white-50 text-decoration-none hover-white" href="<?php echo $link; ?>"><?php echo $label; ?></a>
                                <?php else: ?>
                                    <span class="text-white fw-bold" style="font-size: 1.1rem;"><?php echo $label; ?></span>
                                <?php endif; ?>
                            </li>
                        <?php 
                            $i++;
                        endforeach; 
                        ?>
                    </ol>
                </nav>

                <!-- Quick Actions / Links Rápidos (Exactly matching requested buttons) -->
                <div class="quick-actions mt-3 animated fadeInUp">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="javascript:history.back()" class="btn btn-outline-light rounded-circle action-btn" title="Voltar atrás">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <a href="javascript:window.print()" class="btn btn-outline-light rounded-circle action-btn" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light rounded-circle action-btn" title="Partilhar" onclick="sharePage()">
                            <i class="fas fa-share-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-hero {
    padding-top: 250px; /* Clearance for fixed navbar + logo space - Aumentado conforme pedido */
    padding-bottom: 20px;
    min-height: 420px; /* Aumentado conforme pedido */
    display: flex;
    align-items: center;
    position: relative;
    z-index: 1;
}

.title-font {
    font-family: 'Libre Baskerville', serif;
    font-weight: 700 !important;
}

.header-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(rgba(177, 162, 118, 0.08) 1px, transparent 0);
    background-size: 40px 40px;
    opacity: 0.3;
}

.hover-white:hover {
    color: #B1A276 !important;
}

.action-btn {
    width: 45px;
    height: 45px;
    border-color: rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.05);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    color: white !important;
}

.action-btn:hover {
    background: #B1A276;
    border-color: #B1A276;
    transform: translateY(-5px);
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "\f111";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    font-size: 8px;
    color: rgba(255,255,255,0.4) !important;
    padding-top: 10px;
}

/* Mobile Adjustments */
@media (max-width: 991.98px) {
    .page-hero {
        padding-top: 320px; /* More space for mobile stacked logo/menu */
        min-height: 480px;
    }
}
</style>
