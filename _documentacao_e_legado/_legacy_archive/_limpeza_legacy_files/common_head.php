<?php
// includes/common_head.php
?>
<?php include 'includes/meta_tags_include.php'; ?>

<!-- Google Web Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Icon Font Stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Libraries Stylesheet -->
<link href="lib/animate/animate.min.css" rel="stylesheet">
<link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

<!-- Customized Bootstrap Stylesheet -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">

<!-- Global Components Styles -->
<link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
<link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

<style>
/* Estilo para páginas sem imagem de fundo (Header Limpo) */
.bg-header-light {
    background: #fff !important;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 0 !important;
}
.bg-header-light h1 { color: #333 !important; }
.bg-header-light a, .bg-header-light .breadcrumb-item { color: #666 !important; }
.bg-header-light .breadcrumb-item.active { color: #B1A276 !important; }

/* Correção de Navbar fixa para mobile quando não há slider */
@media (max-width: 991.98px) {
    .navbar {
        background: #091e3e !important;
        position: relative !important;
    }
}
</style>
