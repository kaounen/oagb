<!-- Mobile Header Subpage -->
<div class="d-block d-lg-none" style="overflow: hidden !important; width: 100%; position: relative;">
    <div id="header-carousel-mobile" class="carousel slide" data-bs-ride="false" style="position: relative; overflow: hidden !important;">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="<?php echo htmlspecialchars($header_image ?? 'img/close-up-scales-justice.jpg'); ?>" alt="OAGB Mobile Header">
                
                <!-- Contacts -->
                <div class="mobile-header-contacts container-fluid px-1 pt-3 pb-1">
                    <div class="row mb-3 mx-0">
                        <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 8px; overflow-x: auto; width: 100%;">
                            <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-map-marker-alt text-white-50 me-1"></i>Rua 15, Bissau</small>
                            <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-phone-alt text-white-50 me-1"></i>+245 955475889</small>
                            <small class="text-white text-nowrap" style="font-size: 0.70rem;"><i class="fa fa-envelope-open text-white-50 me-1"></i>info@oagb.gw</small>
                        </div>
                    </div>
                    
                    <div class="row mb-1 mx-0">
                        <div class="col-12" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; justify-content: center !important; align-items: center !important; gap: 12px; width: 100%;">
                            <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="modal" data-bs-target="#searchModal">
                                 <i class="fa fa-search" style="font-size: 1rem;"></i>
                            </button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-light px-2 fw-bold d-flex align-items-center mobile-pill-btn" data-bs-toggle="dropdown" data-bs-display="static">
                                    <i class="fa fa-globe" style="font-size: 1rem;"></i>
                                </button>
                                <div class="dropdown-menu m-0 border-0 rounded-3 shadow-lg p-1 dropdown-menu-center" style="min-width: 150px; z-index: 2050; margin-top: 10px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); position: absolute; left: 50%; transform: translateX(-50%); right: auto;">
                                    <a href="#" onclick="changeLanguage('pt'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇵🇹</span> <span class="text-dark">Português</span></a>
                                    <a href="#" onclick="changeLanguage('en'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇺🇸</span> <span class="text-dark">English</span></a>
                                    <a href="#" onclick="changeLanguage('fr'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇫🇷</span> <span class="text-dark">Français</span></a>
                                    <a href="#" onclick="changeLanguage('es'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇪🇸</span> <span class="text-dark">Español</span></a>
                                    <a href="#" onclick="changeLanguage('ar'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇸🇦</span> <span class="text-dark">العربية</span></a>
                                    <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2 mb-0" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇨🇳</span> <span class="text-dark">中文</span></a>
                                    <a href="#" onclick="changeLanguage('ru'); return false;" class="dropdown-item py-1 d-flex align-items-center rounded-2" style="transition: .3s; font-size: 0.8rem;"><span class="me-3" style="font-size: 1.1rem;">🇷🇺</span> <span class="text-dark">Русский</span></a>
                                </div>
                            </div>
                            <a href="portal/login.php" class="btn btn-sm btn-outline-light px-2 fw-bold text-uppercase d-flex align-items-center mobile-pill-btn">
                                <i class="fas fa-user-circle me-1" style="font-size: 1rem;"></i> Área Reservada
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navbar wrapper -->
                <div class="mobile-navbar-wrapper container-fluid position-relative p-0">
                    <?php include 'includes/navbar.php'; ?>
                </div>

                <!-- Breadcrumbs -->
                <div class="mobile-breadcrumb-bar">
                    <div class="container d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <?php if (isset($mobile_breadcrumbs) && is_array($mobile_breadcrumbs)): ?>
                                <?php foreach ($mobile_breadcrumbs as $index => $bc): ?>
                                    <?php if ($index > 0): ?>
                                        <span class="dot-sep" style="width: 4px; height: 4px; background: #B1A276; display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                    <?php endif; ?>
                                    <?php if (isset($bc['active']) && $bc['active']): ?>
                                        <span class="bc-active"><?php echo htmlspecialchars($bc['label']); ?></span>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($bc['url']); ?>" class="text-white opacity-75"><?php echo htmlspecialchars($bc['label']); ?></a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <a href="index.php" class="text-white opacity-75">Início</a>
                                <span class="dot-sep" style="width: 4px; height: 4px; background: #B1A276; display: inline-block; border-radius: 50%; margin: 0 8px; vertical-align: middle;"></span>
                                <span class="bc-active"><?php echo $page_title; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="quick-links d-flex gap-1">
                            <a href="javascript:history.back()" title="Voltar"><i class="fas fa-arrow-left"></i></a>
                            <a href="javascript:window.print()" title="Imprimir"><i class="fas fa-print"></i></a>
                            <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}" title="Partilhar"><i class="fas fa-share-alt"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
