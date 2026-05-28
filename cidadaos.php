<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/functions.php';
require_once 'connect.php';
require_once 'admin/includes/AttachmentHelper.php';
require_once 'admin/includes/GalleryHelper.php';

$page_title = "Cidadãos";
$meta_description = "Informação para cidadãos — acesso ao direito, direitos fundamentais, como encontrar advogado e glossário jurídico da OAGB.";
$header_image = 'uploads/truth-concept-arrangement-with-balance-ouro.jpg';

try {
    $stmt = $pdo->prepare("SELECT * FROM info_cidadaos WHERE status = 'ativo' ORDER BY ordem ASC");
    $stmt->execute();
    $seccoes = $stmt->fetchAll();
} catch (Exception $e) { $seccoes = []; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/index-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        .bg-header { background-attachment: scroll !important; }
        html, body { overflow-x: hidden !important; width: 100%; margin: 0; padding: 0; }
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.85rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.85rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }
        .quick-links a { width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }
        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; z-index: 1045 !important; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); }
            .mobile-breadcrumb-bar .bc-active { font-weight: 600; font-size: 0.72rem !important; }
            #header-carousel-mobile .carousel-item { min-height: 62vh !important; }
        }
        .info-card { background: #fff; border: 1px solid #f0ece4; border-radius: 20px; padding: 35px; margin-bottom: 25px; transition: .3s; }
        .info-card:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.04); transform: translateY(-2px); }
        .info-icon { width: 60px; height: 60px; background: rgba(77,28,33,0.08); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary-maroon); margin-bottom: 20px; }
        .info-card h3 { font-family: 'Libre Baskerville', serif; font-weight: 500; color: var(--primary-maroon); font-size: 1.15rem; margin-bottom: 15px; line-height: 1.3; }
        .info-card p, .info-card a { font-size: 0.92rem; line-height: 1.7; }
        .info-card a { color: var(--primary-gold); font-weight: 600; }
        .info-card a:hover { color: var(--primary-maroon); }
        .cta-banner { background: linear-gradient(135deg, var(--primary-maroon), #3a1218); border-radius: 20px; padding: 40px; color: #fff; text-align: center; }
        .cta-banner h4 { font-family: 'Libre Baskerville', serif; margin-bottom: 15px; color: #fff; }
        .cta-banner .btn { background: var(--primary-gold); color: #fff; border-radius: 50px; padding: 10px 30px; font-weight: 600; border: none; transition: .3s; }
        .cta-banner .btn:hover { background: #fff; color: var(--primary-maroon); }
        .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px dashed #dcd8cf; }

        /* Expansão e Elementos Ricos */
        .expand-btn { background: none; border: none; color: var(--primary-gold); font-weight: 600; font-size: 0.9rem; padding: 0; margin-top: 15px; display: inline-flex; align-items: center; cursor: pointer; transition: 0.3s; }
        .expand-btn i { margin-left: 8px; transition: transform 0.3s; }
        .expand-btn.active i { transform: rotate(180deg); }
        .expand-btn:hover { color: var(--primary-maroon); }
        .rich-content-panel { display: none; margin-top: 25px; padding-top: 25px; border-top: 1px dashed #dcd8cf; animation: fadeIn 0.5s; }
        .attachment-card { display: flex; align-items: center; padding: 12px 15px; border: 1px solid #eee; border-radius: 10px; background: #fafafa; transition: .3s; text-decoration: none !important; margin-bottom: 10px; }
        .attachment-card:hover { border-color: var(--primary-gold); background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transform: translateY(-2px); }
        .lightbox { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(17,25,35,0.92); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .lightbox-content { margin: auto; display: block; max-width: 90%; max-height: 75vh; margin-top: 8vh; border-radius: 12px; box-shadow: 0 15px 50px rgba(0,0,0,0.5); object-fit: contain; }
        .lightbox-caption { margin: auto; display: block; width: 90%; max-width: 800px; text-align: center; color: rgba(255,255,255,0.9); padding: 20px 0; font-size: 1.1rem; font-family: 'Open Sans', sans-serif; letter-spacing: 0.3px; font-weight: 300; }
        .lightbox-close { position: absolute; top: 25px; right: 40px; color: rgba(255,255,255,0.7); font-size: 40px; font-weight: 300; cursor: pointer; transition: 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.1); }
        .lightbox-close:hover { color: #fff; background: rgba(77,28,33,0.8); transform: scale(1.1); }transition: .3s; }
        .gallery-thumb:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div style="overflow-x: hidden; width: 100%; position: relative;">
    <?php include 'includes/topbar.php'; ?>
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a><span class="bc-sep"></span><a href="#">Público</a><span class="bc-sep"></span><span class="bc-active"><?php echo $page_title; ?></span>
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
    <?php $mobile_breadcrumbs = [['label'=>'Início','url'=>'index.php'],['label'=>'Público','url'=>'#'],['label'=>$page_title,'active'=>true]]; include 'includes/mobile-header-subpage.php'; ?>

    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <span style="font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold);">Informação ao Cidadão</span>
                        <h2 style="font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2rem; margin-top: 10px;">O Direito ao seu alcance</h2>
                        <p class="text-muted mt-3" style="max-width: 600px; margin: 0 auto;">A OAGB trabalha para que todos os cidadãos tenham acesso à informação jurídica e à justiça.</p>
                    </div>

                    <?php if (count($seccoes) > 0): ?>
                        <?php foreach ($seccoes as $sec): ?>
                            <?php 
                                $attachments = AttachmentHelper::get($pdo, 'cidadaos', $sec->id);
                                $gallery = GalleryHelper::get($pdo, 'cidadaos', $sec->id);
                                $hasExtra = !empty($sec->imagem_destaque) || !empty($sec->ficheiro_anexo) || !empty($sec->resumo) || count($attachments) > 0 || count($gallery) > 0;
                            ?>
                            <div class="info-card wow fadeInUp" id="<?php echo htmlspecialchars($sec->slug); ?>">
                                <div class="info-icon"><i class="<?php echo htmlspecialchars($sec->icone); ?>"></i></div>
                                <h3><?php echo htmlspecialchars($sec->titulo); ?></h3>
                                <div class="text-muted"><?php echo $sec->conteudo; ?></div>

                                <?php if($hasExtra): ?>
                                    <button class="expand-btn" onclick="toggleRichContent('rich_<?php echo $sec->id; ?>', this)">
                                        Ver Materiais e Anexos <i class="fas fa-chevron-down"></i>
                                    </button>
                                    
                                    <div class="rich-content-panel" id="rich_<?php echo $sec->id; ?>">
                                        <?php if(!empty($sec->resumo)): ?>
                                            <div class="p-3 mb-4 rounded" style="background: rgba(177, 162, 118, 0.1); border-left: 4px solid var(--primary-gold);">
                                                <p class="mb-0 text-dark fw-bold" style="font-size: 0.95rem;"><i class="fas fa-info-circle me-2 text-muted"></i><?php echo htmlspecialchars($sec->resumo); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(!empty($sec->imagem_destaque)): ?>
                                            <div class="mb-4 text-center">
                                                <img src="uploads/<?php echo $sec->imagem_destaque; ?>" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover;">
                                            </div>
                                        <?php endif; ?>

                                        <?php if(count($attachments) > 0 || !empty($sec->ficheiro_anexo)): ?>
                                            <div class="mb-4">
                                                <h6 class="mb-3" style="font-family: 'Open Sans', sans-serif; color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;"><i class="fas fa-paperclip me-2"></i>Documentos Anexos</h6>
                                                
                                                <?php
                                                // Consolidar anexo principal com os restantes
                                                $all_files = [];
                                                if (!empty($sec->ficheiro_anexo)) {
                                                    $all_files[] = [
                                                        'caminho_ficheiro' => $sec->ficheiro_anexo,
                                                        'descricao' => $sec->legenda_anexo ?: 'Documento Principal',
                                                        'tipo_mime' => 'application/pdf',
                                                        'tamanho' => 0
                                                    ];
                                                }
                                                if (!empty($attachments)) {
                                                    foreach ($attachments as $att) {
                                                        // avoid duplicate if same name
                                                        if (!empty($sec->ficheiro_anexo) && $att['nome_ficheiro'] === $sec->ficheiro_anexo) continue;
                                                        $all_files[] = [
                                                            'caminho_ficheiro' => $att['nome_ficheiro'],
                                                            'descricao' => $att['descricao'] ?: $att['nome_original'],
                                                            'tipo_mime' => $att['tipo_mime'],
                                                            'tamanho' => $att['tamanho']
                                                        ];
                                                    }
                                                }
                                                ?>
                                                
                                                <?php foreach($all_files as $att): ?>
                                                <a href="uploads/<?php echo htmlspecialchars($att['caminho_ficheiro']); ?>" class="text-decoration-none d-block mb-2" target="_blank" style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(77,28,33,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                                                    <div class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 p-3 rounded-3" style="background: linear-gradient(135deg, #fdfcfa 0%, #f8f5ef 100%); border: 1px solid #ebe6da; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                                                        <div class="d-flex align-items-center">
                                                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, #4D1C21 0%, #6b2a30 100%); border-radius: 10px; flex-shrink: 0;">
                                                                <?php if(strpos($att['tipo_mime'], 'pdf') !== false): ?>
                                                                    <i class="far fa-file-pdf" style="font-size: 1.2rem; color: #fff;"></i>
                                                                <?php elseif(strpos($att['tipo_mime'], 'image') !== false): ?>
                                                                    <i class="far fa-file-image" style="font-size: 1.2rem; color: #fff;"></i>
                                                                <?php else: ?>
                                                                    <i class="far fa-file-alt" style="font-size: 1.2rem; color: #fff;"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold" style="font-family: 'Open Sans', sans-serif; font-size: 0.92rem; color: #333;"><?php echo htmlspecialchars($att['descricao']); ?></div>
                                                                <small style="color: #999; font-family: 'Open Sans', sans-serif; font-size: 0.72rem;"><?php echo $att['tamanho'] > 0 ? number_format($att['tamanho'] / 1024, 0) . ' KB' : 'Clique para descarregar'; ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(77,28,33,0.1); transition: all 0.3s;">
                                                                <i class="fas fa-download" style="color: var(--primary-maroon); font-size: 0.8rem;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(count($gallery) > 0): ?>
                                            <div class="mb-4">
                                                <h6 class="mb-3" style="font-family: 'Open Sans', sans-serif; color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;"><i class="fas fa-images me-2"></i>Galeria de Imagens</h6>
                                                <div class="row g-3">
                                                    <?php foreach($gallery as $img): ?>
                                                        <div class="col-6 col-md-4">
                                                            <div class="gallery-thumb-wrapper" style="overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); cursor: pointer;" onclick="openLightbox('uploads/<?php echo $img['imagem']; ?>', '<?php echo htmlspecialchars(addslashes($img['legenda'] ?? '')); ?>')">
                                                                <img src="uploads/<?php echo $img['imagem']; ?>" class="gallery-thumb w-100" style="height: 180px; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" alt="<?php echo htmlspecialchars($img['legenda'] ?? ''); ?>">
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users" style="font-size:3rem;color:#dcd8cf;"></i>
                            <h5 style="color: var(--primary-maroon); font-family: 'Libre Baskerville';">Conteúdo em preparação</h5>
                        </div>
                    <?php endif; ?>

                    <!-- CTA Banner -->
                    <div class="cta-banner mt-5 wow fadeInUp">
                        <h4>Serviços Digitais ao Cidadão</h4>
                        <p class="mb-4 opacity-75">A Ordem disponibiliza canais oficiais imediatos para encontrar profissionais qualificados e participar infracções éticas.</p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="encontrar-advogado.php" class="btn"><i class="fas fa-search me-2"></i>Encontrar por Especialidade</a>
                            <a href="apresentar-reclamacao.php" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid #fff;"><i class="fas fa-gavel me-2"></i>Apresentar Reclamação Deontológica</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top shadow-lg" style="background-color: var(--primary-maroon); border-color: var(--primary-maroon);"><i class="bi bi-arrow-up text-white"></i></a>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script><script src="lib/easing/easing.min.js"></script><script src="lib/waypoints/waypoints.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        function toggleRichContent(id, btn) {
            const panel = document.getElementById(id);
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
                btn.classList.remove('active');
            } else {
                panel.style.display = 'block';
                btn.classList.add('active');
            }
        }
        
        function openLightbox(src, title) {
            let existing = document.getElementById('customLightbox');
            if (existing) existing.remove();
            let html = `
            <div id="customLightbox" class="lightbox" onclick="this.remove()" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
                <div class="lightbox-close" onclick="document.getElementById('customLightbox').remove()">&times;</div>
                <img src="${src}" class="lightbox-content" onclick="event.stopPropagation()">
                ${title ? `<div class="lightbox-caption">${title}</div>` : ''}
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
        }
    </script>
</div>
</body>
</html>
