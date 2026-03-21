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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
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
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <img src="/oagb/img/logo3.png" alt="OAGB" class="sidebar-logo">
                <span class="ms-2 fw-bold text-white small" style="letter-spacing: 1px;">OAGB 2.0</span>
            </div>
            
            <div class="list-group list-group-flush list-unstyled custom-scrollbar" style="overflow-y: auto; height: calc(100vh - var(--header-height));">
                <a href="/oagb/admin/index.php" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/admin/index.php') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="/oagb/admin/modules/estatisticas/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/estatisticas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i> Inteligência & Dados
                </a>
                
                <div class="sidebar-section-title">Conteúdo Institucional</div>
                <a href="/oagb/admin/modules/paginas/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/paginas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice"></i> Páginas (OAGB/História)
                </a>
                <a href="/oagb/admin/modules/noticias/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/noticias/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-newspaper"></i> Notícias & Artigos
                </a>
                <a href="/oagb/admin/modules/agenda/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/agenda/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-calendar-alt"></i> Agenda & Eventos
                </a>
                <a href="/oagb/admin/modules/comunicados/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/comunicados/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn"></i> Comunicados
                </a>
                <a href="/oagb/admin/modules/carousel/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/carousel/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Banners (Slider)
                </a>
                
                <div class="sidebar-section-title">Membros & Pedidos</div>
                <a href="/oagb/admin/modules/advogados/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/advogados/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-tie"></i> Advogados Ativos
                </a>
                <a href="/oagb/admin/modules/estagiarios/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/estagiarios/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i> Estagiários
                </a>
                <a href="/oagb/admin/modules/inscricoes/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/inscricoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Novas Inscrições
                </a>
                <a href="/oagb/admin/modules/comissoes/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/comissoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> Comissões & Órgãos
                </a>
                <a href="/oagb/admin/modules/actas/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/actas/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-file-signature"></i> Livro de Actas
                </a>
                <a href="/oagb/admin/modules/formacao/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/formacao/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap"></i> Formação Contínua
                </a>
                <a href="/oagb/admin/modules/disciplinar/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/disciplinar/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-gavel"></i> Ética & Disciplina
                </a>

                <div class="sidebar-section-title">Arquivo & Decisões</div>
                <a href="/oagb/admin/modules/documentos/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/documentos/') !== false ? 'active' : ''; ?>">
                    <i class="far fa-file-pdf"></i> Repositório Público
                </a>
                <a href="/oagb/admin/modules/pareceres/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/pareceres/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-balance-scale"></i> Pareceres & Juris
                </a>

                <div class="sidebar-section-title">Gestão Operacional</div>
                <a href="/oagb/admin/modules/financeiro/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/financeiro/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-wallet"></i> Tesouraria & Quotas
                </a>
                <a href="/oagb/admin/modules/eleicoes/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/eleicoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-vote-yea"></i> Atos Eleitorais
                </a>
                <a href="/oagb/admin/modules/newsletter/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/newsletter/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-mail-bulk"></i> Newsletter & Envio
                </a>
                
                <div class="sidebar-section-title">Administração</div>
                <a href="/oagb/admin/modules/utilizadores/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/utilizadores/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i> Equipa (Staff)
                </a>
                <a href="/oagb/admin/modules/configuracoes/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/configuracoes/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Configurações Site
                </a>
                <a href="/oagb/admin/modules/logs/" class="list-group-item <?php echo strpos($_SERVER['PHP_SELF'], '/modules/logs/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Logs de Auditoria
                </a>
                
                <div class="py-4 px-4 mt-auto">
                    <a href="/oagb/admin/auth/logout.php" class="btn btn-outline-danger w-100 p-2 small border-0 bg-danger-subtle text-danger fw-bold">
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
                        <li><a class="dropdown-item py-2 px-3 rounded small" href="/oagb/admin/modules/utilizadores/edit.php?id=<?php echo $_SESSION['admin_id']; ?>"><i class="fas fa-user-circle me-2 opacity-50"></i> O meu Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 px-3 rounded small text-danger" href="/oagb/admin/auth/logout.php"><i class="fas fa-sign-out-alt me-2 opacity-50"></i> Terminar Sessão</a></li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid p-4 p-md-5">
