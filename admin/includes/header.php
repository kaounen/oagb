<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão OAGB 2.0 | Administração</title>

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #B1A276;
            --sidebar-bg: #111923;
            --header-height: 70px;
            --sidebar-width: 260px;
            --body-bg: #f5f6f8;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --login-gold: #B1A276;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Open Sans', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-heading {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 25px;
            background-color: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-logo {
            height: 35px;
            filter: brightness(0) invert(1);
        }

        .list-group-item {
            background-color: transparent !important;
            border: none !important;
            padding: 12px 25px;
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .list-group-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.7;
        }

        .list-group-item:hover, .list-group-item.active {
            color: var(--primary-gold) !important;
            background-color: rgba(177, 162, 118, 0.05) !important;
        }

        .list-group-item.active {
            border-left: 3px solid var(--primary-gold) !important;
            color: white !important;
            font-weight: 600;
        }

        .sidebar-section-title {
            padding: 25px 25px 10px 25px;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.25);
            font-weight: 800;
        }

        /* Topbar Styling */
        #page-content-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .top-navbar {
            height: var(--header-height);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e1e4e8;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Buttons & Badges */
        .btn-login { background-color: var(--login-gold); color: #111923; border: none; font-weight: 600; border-radius: 8px; transition: all 0.3s; }
        .btn-login:hover { background-color: #9a8c63; color: white; transform: translateY(-2px); }
        .bg-login-subtle { background-color: rgba(177, 162, 118, 0.1); }
        .text-login { color: #B1A276; }

        /* Generic Admin Styles */
        .cursor-pointer { cursor: pointer; }
        .x-small { font-size: 0.7rem; }
        .border-dashed { border-style: dashed !important; }

        @media (max-width: 991px) {
            #sidebar-wrapper { margin-left: calc(-1 * var(--sidebar-width)); }
            #sidebar-wrapper.show { margin-left: 0; }
            #page-content-wrapper { margin-left: 0; width: 100%; }
        }

        /* Safeguard against ghost overlays and orphan modal backdrops */
        body:not(.modal-open) .modal-backdrop {
            display: none !important;
            z-index: -1 !important;
            pointer-events: none !important;
        }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <img src="<?php echo ROOT_URL; ?>/img/logo3.png" alt="OAGB" class="sidebar-logo">
                <span class="ms-2 fw-bold text-white small" style="letter-spacing: 1px;">OAGB 2.0</span>
            </div>
            
            <div class="list-group list-group-flush list-unstyled custom-scrollbar" style="overflow-y: auto; height: calc(100vh - var(--header-height));">
                <div class="sidebar-section-title">Dashboard & Inteligência</div>
                <a href="<?php echo ADMIN_PATH; ?>/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/index.php') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Painel de Controlo
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/estatisticas/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/estatisticas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i> Estatísticas & Dados
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/logs/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/logs/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Segurança & Auditoria
                </a>

                <div class="sidebar-section-title">A Ordem & Institucional</div>
                <a href="<?php echo ADMIN_PATH; ?>/modules/paginas/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/paginas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Páginas Institucionais
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/actas/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/actas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Livro de Actas
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/cooperacao/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/cooperacao/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i> Cooperação Institucional
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/bastonarios/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/bastonarios/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i> Galeria Bastonários
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/timeline/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/timeline/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Linha do Tempo
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/orgaos-sociais/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/orgaos-sociais/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-sitemap"></i> Órgãos Sociais
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/comissoes/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/comissoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> Comissões Especializadas
                </a>
                
                <div class="sidebar-section-title">Membros & Advocacia</div>
                <a href="<?php echo ADMIN_PATH; ?>/modules/advogados/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/advogados/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-tie"></i> Quadro de Advogados
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/estagiarios/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/estagiarios/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-clock"></i> Estagiários
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/inscricoes/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/inscricoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Novas Inscrições
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/solicitacoes/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/solicitacoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-balance-scale"></i> Pedidos de Advogado
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/disciplinar/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/disciplinar/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-gavel"></i> Ética & Disciplina
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/formacao/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/formacao/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap"></i> Formação & Cursos
                </a>

                <div class="sidebar-section-title">Conteúdo & Comunicação</div>
                <a href="<?php echo ADMIN_PATH; ?>/modules/noticias/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/noticias/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-newspaper"></i> Notícias & Artigos
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/agenda/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/agenda/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-calendar-alt"></i> Agenda de Eventos
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/comunicados/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/comunicados/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn"></i> Comunicados & Anúncios
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/contactos/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/contactos/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-envelope-open-text"></i> Mensagens & Contactos
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/departamentos-contactos/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/departamentos-contactos/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> Departamentos & Sedes
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/carousel/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/carousel/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Banners (Slider)
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/newsletter/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/newsletter/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-mail-bulk"></i> Newsletter & Envio
                </a>

                <div class="sidebar-section-title">Publicações & Biblioteca</div>
                <a href="<?php echo ADMIN_PATH; ?>/modules/revistas/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/revistas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Revista da OAGB
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/legislacao/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/legislacao/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-scroll"></i> Legislação Nacional
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/glossario/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/glossario/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-book-open"></i> Glossário Jurídico
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/biblioteca/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/biblioteca/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-university"></i> Biblioteca Digital
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/cidadaos/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/cidadaos/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Informação ao Cidadão
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/pareceres/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/pareceres/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-stamp"></i> Atos Oficiais (Pareceres)
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/documentos/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/documentos/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-file-pdf"></i> Repositório de Ficheiros
                </a>

                <div class="sidebar-section-title">Gestão & Auditoria</div>
                <a href="<?php echo ADMIN_PATH; ?>/modules/conteudo-paginas/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/conteudo-paginas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-cubes"></i> Builder de Conteúdo (Secções)
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/financeiro/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/financeiro/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-wallet"></i> Tesouraria & Quotas
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/eleicoes/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/eleicoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-vote-yea"></i> Atos Eleitorais
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/utilizadores/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/utilizadores/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i> Equipa (Staff)
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/configuracoes/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/configuracoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Configurações do Portal
                </a>
                <a href="<?php echo ADMIN_PATH; ?>/modules/configuracoes/assinaturas.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/configuracoes/assinaturas.php') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Atribuição de Assinaturas
                </a>
                
                <div class="py-4 px-4 mt-auto">
                    <a href="<?php echo ADMIN_PATH; ?>/auth/logout.php" class="btn btn-outline-danger w-100 p-2 small border-0 bg-danger-subtle text-danger fw-bold">
                        <i class="fas fa-power-off me-2"></i> SAIR DO SISTEMA
                    </a>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div class="d-flex align-items-center">
                    <button class="btn border-0 d-lg-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-3 d-none d-md-block text-muted small fw-bold">
                        <?php echo date('d \d\e F \d\e Y'); ?>
                    </div>
                </div>

                <div class="dropdown">
                    <div class="d-flex align-items-center cursor-pointer" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-sm-block me-3">
                            <div class="fw-bold small lh-1"><?php echo $_SESSION['admin_name'] ?? 'Utilizador'; ?></div>
                            <span class="text-muted x-small"><?php echo strtoupper($_SESSION['admin_role'] ?? 'Staff'); ?></span>
                        </div>
                        <div class="bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <?php echo substr($_SESSION['admin_name'] ?? 'U', 0, 1); ?>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3 p-2 rounded-3 animate-fade-in">
                        <li><a class="dropdown-item py-2 px-3 rounded small" href="<?php echo ADMIN_PATH; ?>/modules/utilizadores/edit.php?id=<?php echo $_SESSION['admin_id']; ?>"><i class="fas fa-user-circle me-2 opacity-50"></i> O meu Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 px-3 rounded small text-danger" href="<?php echo ADMIN_PATH; ?>/auth/logout.php"><i class="fas fa-sign-out-alt me-2 opacity-50"></i> Terminar Sessão</a></li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid p-4 p-md-5">
