<?php
// Detectar página atual e menus ativos
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Páginas do menu ORDEM
$ordem_pages = ['apresentacao-historia.php', 'bastonario-ordem.php', 'orgaos-sociais.php', 'comissoes-especializadas.php', 'cooperacao-institucional.php'];

// Páginas do menu ADVOGADOS
$advogados_pages = ['pesquisa-advogados.php', 'advogados-inscritos.php', 'pesquisa-estagiarios.php', 'estagiarios-inscritos.php', 'solicitacao-advogados.php', 'inscricao-ordem.php'];

// Páginas do menu PÚBLICO
$publico_pages = ['pareceres-deliberacoes.php', 'comunicados.php', 'publicacoes.php', 'orcamento.php'];

// Páginas do menu COMUNICAÇÃO
$comunicacao_pages = ['agenda.php', 'noticias.php', 'anuncios.php'];

// Verificar se está em uma página de dropdown
$ordem_ativo = in_array($pagina_atual, $ordem_pages);
$advogados_ativo = in_array($pagina_atual, $advogados_pages);
$publico_ativo = in_array($pagina_atual, $publico_pages);
$comunicacao_ativo = in_array($pagina_atual, $comunicacao_pages);
?>

<nav class="navbar navbar-expand-lg navbar-dark ps-5 pe-lg-5 py-3 py-lg-0" style="padding-right: 4rem !important;">
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
                    <a href="apresentacao-historia.php" class="dropdown-item">Apresentação e História</a>
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
                    <a href="estagiarios-inscritos.php" class="dropdown-item">Advogados Estagiários Inscritos em vigor</a>
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
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo $comunicacao_ativo ? 'active' : ''; ?>" data-bs-toggle="dropdown">COMUNICAÇÃO</a>
                <div class="dropdown-menu m-0" style="left: auto; right: 0;">
                    <a href="agenda.php" class="dropdown-item">Agenda</a>
                    <a href="noticias.php" class="dropdown-item">Notícias</a>
                    <a href="anuncios.php" class="dropdown-item">Anúncios</a>
                </div>
            </div>
            <a href="contacto.php" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contacto.php') ? 'active' : ''; ?>">CONTACTO</a>
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
</nav>

<style>
/* Correção para menu mobile */
@media (max-width: 991.98px) {
    .navbar {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .navbar-brand {
        margin: 0 auto 1.5rem !important;
        width: 100%;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important;
        position: relative;
    }
}
</style>
