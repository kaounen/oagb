<nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
    <a href="index.php" class="navbar-brand p-0">
        <img src="img/logo3.png" style="width:70%;height:auto;padding-top:5%;" align="center" border="0" alt="OAGB Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <a href="index.php" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">INÍCIO</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ORDEM</a>
                <div class="dropdown-menu m-0">
                    <a href="apresentacao-historia.php" class="dropdown-item">Apresentação e História</a>
					<a href="bastonario-ordem.php" class="dropdown-item">O Bastonário</a>  
                    <a href="orgaos-sociais.php" class="dropdown-item">Órgãos Sociais</a>                            
                    <a href="comissoes-especializadas.php" class="dropdown-item">Comissões Especializadas</a>
                    <a href="cooperacao-institucional.php" class="dropdown-item">Cooperação Institucional</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">ADVOGADOS</a>
                <div class="dropdown-menu m-0">
                    <!-- <a href="advogados.php" class="dropdown-item">Advogados</a>
 -->                    <a href="pesquisa-advogados.php" class="dropdown-item">Pesquisa de Advogados</a>                            
                    <a href="advogados-inscritos.php" class="dropdown-item">Advogados Inscritos em vigor</a>
                    <a href="pesquisa-estagiarios.php" class="dropdown-item">Pesquisa de Advogados Estagiários</a>                            
                    <a href="estagiarios-inscritos.php" class="dropdown-item">Advogados Estagiários Inscritos em vigor</a>
                    <a href="solicitacao-advogados.php" class="dropdown-item">Solicitação de Advogados</a>                           
                    <a href="inscricao-ordem.php" class="dropdown-item">Inscrição na Ordem</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">PÚBLICO</a>
                <div class="dropdown-menu m-0">
                    <a href="pareceres-deliberacoes.php" class="dropdown-item">Pareceres e Deliberações</a>
                    <a href="comunicados.php" class="dropdown-item">Comunicados</a>
                    <a href="publicacoes.php" class="dropdown-item">Publicações</a>
                    <a href="orcamento.php" class="dropdown-item">Orçamento</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">COMUNICAÇÃO</a>
                <div class="dropdown-menu m-0" style="left: auto; right: 0;">
                    <a href="agenda.php" class="dropdown-item">Agenda</a>
                    <a href="noticias.php" class="dropdown-item">Notícias</a>
                    <a href="anuncios.php" class="dropdown-item">Anúncios</a>
                </div>
            </div>
            <a href="contacto.php" class="nav-item nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contacto.php') ? 'active' : ''; ?>">CONTACTO</a>
        </div>
        <button type="button" class="btn text-primary ms-3" data-bs-toggle="modal" data-bs-target="#searchModal">
            <i class="fa fa-search"></i>
        </button>
        <div id="" class="">&nbsp;</div>
    </div>
</nav>

<style>
/* Correção para menu mobile */
@media (max-width: 991.98px) {
    .navbar-toggler {
        position: relative !important;
        right: auto !important;
        top: auto !important;
        transform: none !important;
    }
    
    .navbar-brand img {
        width: 150px !important;
        max-width: 60% !important;
    }
    
    .navbar-dark .navbar-nav .dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
    }
    
    .navbar-dark .navbar-nav .dropdown-menu .dropdown-item {
        color: #091E3E;
    }
    
    .navbar-dark .navbar-nav .dropdown-menu .dropdown-item:hover {
        background-color: var(--primary);
        color: white;
    }
}

/* Menu dropdowns no mouseover para desktop */
@media (min-width: 992px) {
    .navbar-nav .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }
    
    .navbar-nav .dropdown .dropdown-menu {
        margin-top: 0;
    }
}

/* Correção alinhamento dropdown COMUNICAÇÃO */
.navbar-nav .dropdown:last-of-type .dropdown-menu {
    left: auto !important;
    right: 0 !important;
}
</style>