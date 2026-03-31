<?php
// estatutos.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connect.php';
require_once 'includes/functions.php';

$page_title = "Estatutos da OAGB";
$breadcrumbs = [
    'Início' => 'index.php',
    'Ordem' => '#',
    'Estatutos' => '#'
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/common_head.php'; ?>
    <style>
        .estatutos-content {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.8;
            color: #333;
        }
        .estatutos-content h1, .estatutos-content h2, .estatutos-content h3 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        /* Fix for the huge statute document */
        .estatutos-body {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 20px;
        }
        /* Custom scrollbar */
        .estatutos-body::-webkit-scrollbar { width: 8px; }
        .estatutos-body::-webkit-scrollbar-track { background: #f1f1f1; }
        .estatutos-body::-webkit-scrollbar-thumb { background: #B1A276; border-radius: 10px; }
        .estatutos-body::-webkit-scrollbar-thumb:hover { background: #8B6B47; }
    </style>
</head>
<body class="bg-white">
    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>

    <!-- Global Unified Header -->
    <?php 
    $header_light = true; 
    include 'includes/header_global.php'; 
    ?>
    
    <!-- Standard Clean Header -->
    <?php 
    $has_header_image = false;
    include 'includes/page-header.php'; 
    ?>

    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-12">
                <div class="bg-light p-4 p-md-5 rounded-4 shadow-sm border wow fadeInUp">
                    <div class="estatutos-content">
                        <?php 
                        // Instead of 4000 lines here, we include the body we extracted
                        // This keeps the PHP file clean and manageable
                        if (file_exists('estatutos_body.php')) {
                            // Wrap it in a scrollable container if it's too long
                            echo '<div class="estatutos-body">';
                            include 'estatutos_body.php';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-warning">Conteúdo dos estatutos não encontrado.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>
</html>
