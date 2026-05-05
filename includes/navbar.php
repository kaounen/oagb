<?php
// Detectar página atual e menus ativos
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Páginas do menu ORDEM
$ordem_pages = ['ordem-dos-advogados.php', 'bastonario-ordem.php', 'orgaos-sociais.php', 'comissoes-especializadas.php', 'cooperacao-institucional.php'];

// Páginas do menu ADVOGADOS
$advogados_pages = ['pesquisa-advogados.php', 'advogados-inscritos.php', 'pesquisa-estagiarios.php', 'estagiarios-inscritos.php', 'solicitacao-advogados.php', 'inscricao-ordem.php'];

// Páginas do menu PÚBLICO
$publico_pages = ['pareceres-deliberacoes.php', 'comunicados.php', 'publicacoes.php', 'orcamento.php', 'revista-oagb.php', 'legislacao-nacional.php', 'legislacao-internacional.php', 'glossario-juridico.php', 'biblioteca-oagb.php'];

// Páginas do menu COMUNICAÇÃO
$comunicacao_pages = ['agenda.php', 'noticias.php', 'anuncios.php', 'cidadaos.php'];


// Verificar se está em uma página de dropdown
$ordem_ativo = in_array($pagina_atual, $ordem_pages);
$advogados_ativo = in_array($pagina_atual, $advogados_pages);
$publico_ativo = in_array($pagina_atual, $publico_pages);
$comunicacao_ativo = in_array($pagina_atual, $comunicacao_pages);
?>

<nav class="navbar navbar-expand-lg navbar-dark py-3 py-lg-0">
    <div class="container">
        <a href="index.php" class="navbar-brand p-0">
        <img src="img/logo3.png" class="oagb-logo" alt="OAGB Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <a href="index.php" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">INÍCIO</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo $ordem_ativo ? 'active' : ''; ?>" data-bs-toggle="dropdown">ORDEM</a>
                <div class="dropdown-menu m-0">
                    <a href="ordem-dos-advogados.php" class="dropdown-item">Apresentação e História</a>
					<a href="bastonario-ordem.php" class="dropdown-item">O Bastonário</a>
                    <a href="orgaos-sociais.php" class="dropdown-item">Órgãos Sociais</a>
                    <a href="comissoes-especializadas.php" class="dropdown-item">Comissões Especializadas</a>
                    <a href="cooperacao-institucional.php" class="dropdown-item">Cooperação Institucional</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo $advogados_ativo ? 'active' : ''; ?>" data-bs-toggle="dropdown">ADVOGADOS</a>
                <div class="dropdown-menu m-0">
                    <a href="pesquisa-advogados.php" class="dropdown-item">Pesquisa de Advogados</a>
                    <a href="advogados-inscritos.php" class="dropdown-item">Advogados Inscritos em vigor</a>
                    <a href="pesquisa-estagiarios.php" class="dropdown-item">Pesquisa de Advogados Estagiários</a>
                    <a href="estagiarios-inscritos.php" class="dropdown-item">Estagiários Inscritos em vigor</a>
                    <a href="solicitacao-advogados.php" class="dropdown-item">Solicitação de Advogados</a>
                    <a href="inscricao-ordem.php" class="dropdown-item">Inscrição na Ordem</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo $publico_ativo ? 'active' : ''; ?>" data-bs-toggle="dropdown">PÚBLICO</a>
                <div class="dropdown-menu m-0">
                    <a href="pareceres-deliberacoes.php" class="dropdown-item">Pareceres e Deliberações</a>
                    <a href="comunicados.php" class="dropdown-item">Comunicados</a>
                    <a href="publicacoes.php" class="dropdown-item">Publicações</a>
                    <a href="orcamento.php" class="dropdown-item">Orçamento</a>
                    <a href="revista-oagb.php" class="dropdown-item">Revista da OAGB</a>
                    <a href="legislacao-nacional.php" class="dropdown-item">Legislação Nacional</a>
                    <a href="legislacao-internacional.php" class="dropdown-item">Legislação Internacional</a>
                    <a href="glossario-juridico.php" class="dropdown-item">Glossário Jurídico</a>
                    <a href="biblioteca-oagb.php" class="dropdown-item">Biblioteca OAGB</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo $comunicacao_ativo ? 'active' : ''; ?>" data-bs-toggle="dropdown">COMUNICAÇÃO</a>
                <div class="dropdown-menu m-0" style="left: auto; right: 0;">
                    <a href="agenda.php" class="dropdown-item">Agenda</a>
                    <a href="noticias.php" class="dropdown-item">Notícias</a>
                    <a href="anuncios.php" class="dropdown-item">Anúncios</a>
                    <a href="cidadaos.php" class="dropdown-item">Cidadãos</a>
                </div>
            </div>
            <a href="contacto.php" class="nav-item nav-link pe-lg-0 <?php echo (basename($_SERVER['PHP_SELF']) == 'contacto.php') ? 'active' : ''; ?>">CONTACTO</a>
        </div>
        
        <!-- Mobile Buttons (Visible only on mobile) -->
        <button type="button" class="btn text-primary ms-3 d-lg-none" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fa fa-search"></i>
        </button>
        <a href="portal/login.php" class="btn text-primary ms-2 d-lg-none" title="Portal do Membro">
            <i class="fas fa-user-circle"></i>
        </a>
        <div id="" class="d-lg-none">&nbsp;</div>
    </div>
    </div> <!-- Fecha .container -->
</nav>

<style>

/* Dropdown compacto (desktop) */
@media (min-width: 992px) {
    .navbar .dropdown-menu .dropdown-item {
        font-size: 0.90rem;
        padding: 6px 18px;
        line-height: 1.4;
        font-weight: 400;
    }
    .navbar .dropdown-menu {
        padding: 6px 0;
    }
}

/* Correção para menu mobile */
@media (max-width: 991.98px) {
    .navbar {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 3rem !important; /* Aumento expressivo para garantir que a margem é visível */
    }

    .navbar-brand {
        margin: 0 auto 2rem !important;
        width: 100%;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important; 
        position: relative;
    }

    .navbar-brand img {
        margin: 0 auto !important;
        display: block !important;
    }

    /* Toggler button adjusted */
    .navbar-toggler {
        margin: 2.5rem auto 1rem !important; /* Aumento ainda maior do espaçamento entre logo e menu */
        display: block;
    }

    /* Menu collapse centralizado */
    .navbar-collapse {
        text-align: center !important;
        background: rgba(9,30,62,0.95);
        border-radius: 8px;
        padding: 1rem 0;
        margin-top: 1rem;
    }

    .navbar-collapse .nav-link {
        color: rgba(255,255,255,0.9) !important;
        padding: 0.7rem 1rem !important;
    }

    .navbar-collapse .nav-link.active {
        color: #B1A276 !important;
        font-weight: bold;
    }

    /* Dropdowns centralizados abertos no mobile em vez de flutuar */
    .navbar-collapse .dropdown-menu {
        position: static !important;
        float: none !important;
        width: 100%;
        text-align: center !important;
        border: none;
        background: rgba(0,0,0,0.3) !important;
        box-shadow: none;
        margin: 0;
        padding: 0.5rem 0;
    }

    .navbar-collapse .dropdown-menu .dropdown-item {
        color: rgba(255,255,255,0.85) !important;
        text-align: center !important;
        padding: 0.5rem 1rem;
    }

    .navbar-collapse .dropdown-menu .dropdown-item:hover {
        color: #B1A276 !important;
        background: transparent !important;
    }
}
</style>

