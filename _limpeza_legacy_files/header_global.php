<?php
/**
 * header_global.php
 * Unified header for all pages (Desktop & Mobile)
 * Includes Topbar/Mobile Contacts and Navbar
 */
?>

<!-- Desktop Header (hidden on mobile) -->
<div class="d-none d-lg-block <?php echo (isset($header_light) && $header_light) ? 'header-forced-light' : ''; ?>">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<!-- Mobile Header Start -->
<?php 
$mobile_header_class = '';
if (isset($header_light) && $header_light) {
    $mobile_header_class = 'mobile-header-forced-light';
}
?>
<div id="mobile-header" class="d-block d-lg-none <?php echo $mobile_header_class; ?>" style="background: #091E3E; position: relative; z-index: 2000; transition: all 0.3s ease; padding-bottom: 5px;">
    <!-- Mobile Contact Info & Buttons (Exact clone from index.php) -->
    <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
        <div class="row mb-3">
            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                <small class="contact-text text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-map-marker-alt me-1"></i>Bissau, Guiné-Bissau</small>
                <small class="contact-text text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-phone-alt me-1"></i>+245 955 475 889</small>
                <small class="contact-text text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-envelope-open me-1"></i>info@oagb.gw</small>
            </div>
        </div>
        
        <div class="row mb-1">
            <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; width: 100%;">
                <!-- Botão Pesquisa -->
                <button type="button" class="btn btn-sm px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                     <i class="fa fa-search" style="font-size: 1rem;"></i>
                </button>
                
                <!-- Botão Tradução -->
                <div class="dropdown">
                    <button type="button" class="btn btn-sm px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="dropdown" data-bs-display="static" title="Mudar Idioma">
                        <i class="fa fa-globe" style="font-size: 1rem;"></i>
                    </button>
                    <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2000; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                        <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                        <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                        <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                        <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                        <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                        <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                        <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                    </div>
                </div>
                
                <!-- Botão Área Reservada -->
                <a href="portal/login.php" class="btn btn-sm px-2 fw-bold text-uppercase d-flex align-items-center mobile-pill-btn">
                    <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Navbar -->
    <div class="mobile-navbar-wrapper container-fluid position-relative p-0">
        <?php include 'includes/navbar.php'; ?>
    </div>
</div>
