<?php
/**
 * header_nav.php
 * Inclui Topbar e Navbar com lógica para Diferenciar Index de Outras Páginas
 */
$is_index = (basename($_SERVER['PHP_SELF']) == 'index.php');
?>

<!-- Desktop Header -->
<div class="d-none d-lg-block">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<!-- Mobile Header -->
<div class="d-block d-lg-none">
    <?php if ($is_index): ?>
        <!-- No index o mobile header é integrado no carousel (logica atual do index) -->
    <?php else: ?>
        <!-- Nas outras páginas, um header mobile fixo e limpo -->
        <div class="mobile-header-simple bg-header-dark py-3 shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="index.php" class="mobile-logo">
                    <img src="img/logo3.png" alt="OAGB" style="height: 40px;">
                </a>
                <div class="mobile-actions d-flex align-items-center gap-3">
                    <button type="button" class="btn text-white p-0" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fa fa-search"></i>
                    </button>
                    <a href="portal/login.php" class="text-white">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                </div>
            </div>
            <?php include 'includes/navbar.php'; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.bg-header-dark { background: #091e3e; }
.mobile-header-simple { position: relative; z-index: 1000; }
.mobile-header-simple .navbar { padding-top: 10px !important; }
.mobile-header-simple .navbar-toggler { margin-top: 10px !important; border-color: rgba(255,255,255,0.1); color: #fff; }
</style>
