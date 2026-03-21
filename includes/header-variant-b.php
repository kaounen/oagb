<?php
/**
 * Header Variante B: Com Breadcrumbs e Quick Actions
 * Usado em: apresentacao-historia.php e outras páginas institucionais
 * 
 * Características:
 * - Foto de fundo estática (não slider)
 * - Altura: 400px (igual a advogados-inscritos.php)
 * - Breadcrumbs + Título + Quick Actions (Voltar/Imprimir/Partilhar/Traduzir)
 * 
 * Parâmetros esperados:
 * - $page_title: Título da página
 * - $breadcrumbs: Array com breadcrumbs [['label' => 'Início', 'url' => 'index.php'], ...]
 * - $background_image: URL da imagem de fundo (opcional)
 */

// Valores padrão
$background_image = $background_image ?? 'img/close-up-scales-justice.jpg';
$page_title = $page_title ?? 'Página';
$breadcrumbs = $breadcrumbs ?? [];
?>

<!-- Desktop Header Variant B Start -->
<div class="container-fluid position-relative p-0 d-none d-lg-block">
    <?php include 'navbar.php'; ?>

    <!-- Header Content with Background Image -->
    <div class="bg-header-breadcrumbs" style="background-image: url('<?php echo $background_image; ?>');">
        <div class="container">
            <div class="row g-5 align-items-center" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                <div class="col-lg-12 text-center text-white">
                    <!-- Breadcrumbs -->
                    <?php if (!empty($breadcrumbs)): ?>
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb justify-content-center" style="background: transparent; padding: 0;">
                            <?php foreach ($breadcrumbs as $item): ?>
                                <?php if (isset($item['url'])): ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo $item['url']; ?>" style="color: rgba(255, 255, 255, 0.7); text-decoration: none;">
                                            <?php echo $item['label']; ?>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active" style="color: #c18046; font-weight: 600;">
                                        <?php echo $item['label']; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>

                    <!-- Page Title -->
                    <h1 class="mb-4" style="font-family: 'Libre Baskerville', serif; font-size: 2.5rem; color: white; font-weight: 700;">
                        <?php echo htmlspecialchars($page_title); ?>
                    </h1>

                    <!-- Quick Actions -->
                    <div class="quick-actions" style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                        <!-- Voltar -->
                        <button class="btn" title="Voltar atrás" onclick="history.back()" 
                            style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; transition: all 0.3s ease; cursor: pointer;">
                            <i class="bi bi-arrow-left"></i>
                        </button>

                        <!-- Imprimir -->
                        <button class="btn" title="Imprimir" onclick="window.print()" 
                            style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; transition: all 0.3s ease; cursor: pointer;">
                            <i class="bi bi-printer"></i>
                        </button>

                        <!-- Partilhar -->
                        <button class="btn" title="Partilhar" onclick="sharePage()" 
                            style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; transition: all 0.3s ease; cursor: pointer;">
                            <i class="bi bi-share"></i>
                        </button>

                        <!-- Traduzir -->
                        <button class="btn" title="Traduzir" onclick="translatePage()" 
                            style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; transition: all 0.3s ease; cursor: pointer;">
                            <i class="bi bi-globe"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Desktop Header Variant B End -->

<!-- Mobile Header Variant B Start -->
<div class="d-lg-none">
    <!-- Mobile Background with Image -->
    <div class="mobile-header-breadcrumbs" style="background-image: url('<?php echo $background_image; ?>'); background-size: cover; background-position: center; min-height: 60vh; position: relative;">
        
        <!-- Mobile Contact Info -->
        <div class="container-fluid px-3 py-3" style="position: absolute; top: 0; left: 0; right: 0; z-index: 1000;">
            <div class="row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="col-auto" style="font-size: 0.85rem; color: white;">
                    <div class="mobile-contacts">
                        <div class="contact-line" style="margin-bottom: 0.1rem; font-size: 0.85rem; line-height: 1.1;">
                            <i class="fas fa-phone" style="color: #c18046; margin-right: 5px;"></i>
                            <strong style="color: #c18046;">+245 663 820 820</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navbar -->
        <div class="container-fluid position-relative p-0" style="position: absolute; top: 80px; left: 0; right: 0; z-index: 1000;">
            <?php include 'navbar.php'; ?>
        </div>

        <!-- Mobile Header Content -->
        <div style="position: absolute; bottom: 20px; left: 0; right: 0; z-index: 500; padding: 1rem 1.5rem; text-align: center;">
            <!-- Breadcrumbs -->
            <?php if (!empty($breadcrumbs)): ?>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb justify-content-center" style="background: transparent; padding: 0; margin: 0; flex-wrap: wrap;">
                    <?php foreach ($breadcrumbs as $item): ?>
                        <?php if (isset($item['url'])): ?>
                            <li class="breadcrumb-item" style="margin: 0 5px;">
                                <a href="<?php echo $item['url']; ?>" style="color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 0.85rem;">
                                    <?php echo $item['label']; ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" style="color: #c18046; font-weight: 600; margin: 0 5px; font-size: 0.85rem;">
                                <?php echo $item['label']; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>

            <!-- Title -->
            <h1 style="font-family: 'Libre Baskerville', serif; color: white; font-size: 1.8rem; font-weight: 700; margin: 0.5rem 0; line-height: 1.2;">
                <?php echo htmlspecialchars($page_title); ?>
            </h1>

            <!-- Quick Actions Mobile -->
            <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1rem; flex-wrap: wrap;">
                <button class="btn" title="Voltar" onclick="history.back()" 
                    style="width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; font-size: 0.9rem; cursor: pointer;">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="btn" title="Imprimir" onclick="window.print()" 
                    style="width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; font-size: 0.9rem; cursor: pointer;">
                    <i class="bi bi-printer"></i>
                </button>
                <button class="btn" title="Partilhar" onclick="sharePage()" 
                    style="width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; font-size: 0.9rem; cursor: pointer;">
                    <i class="bi bi-share"></i>
                </button>
                <button class="btn" title="Traduzir" onclick="translatePage()" 
                    style="width: 36px; height: 36px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; font-size: 0.9rem; cursor: pointer;">
                    <i class="bi bi-globe"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Mobile Header Variant B End -->

<style>
    /* Header Variant B Styles */
    .bg-header-breadcrumbs {
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Overlay escuro para legibilidade */
    .bg-header-breadcrumbs::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    /* Conteúdo sobre overlay */
    .bg-header-breadcrumbs > div {
        position: relative;
        z-index: 2;
    }

    /* Breadcrumbs styling */
    .breadcrumb {
        background: transparent;
        padding: 0.5rem 0;
        margin-bottom: 1rem;
        gap: 0.5rem;
    }

    .breadcrumb-item {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }

    .breadcrumb-item.active {
        color: #c18046;
        font-weight: 600;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: #c18046;
    }

    /* Mobile header */
    .mobile-header-breadcrumbs {
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
    }

    .mobile-header-breadcrumbs::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    @media (max-width: 991.98px) {
        .mobile-header-breadcrumbs {
            min-height: 60vh;
        }
    }
</style>
