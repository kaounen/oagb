<?php /* O banner de subscrição foi removido daqui e passado para as páginas específicas onde deve aparecer */ ?>

<!-- Footer Section Start -->
<footer class="footer-section">
    <div class="container pb-0">
        <div class="row">
            <!-- Column 1: About -->
            <div class="col-lg-4 col-md-12 mb-4 pe-lg-4 text-lg-start text-center">
                <a href="index.php">
                    <img src="img/logo3.png" alt="OAGB Logo" class="footer-logo mb-4" style="height: 55px;">
                </a>
                <p class="footer-about-text pe-lg-4">
                    A Ordem dos Advogados da Guiné-Bissau (OAGB) é uma associação pública dedicada à promoção da justiça, defesa dos direitos e excelência da advocacia guineense.
                </p>
                <div class="footer-social-links mt-4">
                    <a href="https://www.facebook.com/profile.php?id=100087015439692" target="_blank" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Column 2: Geral -->
            <div class="col-lg-2 col-md-12 col-12 mb-4 text-lg-start text-center">
                <h4 class="footer-heading">Geral</h4>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item"><a href="index.php" class="footer-nav-link">Início</a></li>
                    <li class="footer-nav-item"><a href="agenda.php" class="footer-nav-link">Agenda</a></li>
                    <li class="footer-nav-item"><a href="noticias.php" class="footer-nav-link">Notícias</a></li>
                    <li class="footer-nav-item"><a href="contacto.php" class="footer-nav-link">Contacto</a></li>
                </ul>
            </div>

            <!-- Column 3: Links rápidos -->
            <div class="col-lg-3 col-md-12 col-12 mb-4 text-lg-start text-center">
                <h4 class="footer-heading">Links rápidos</h4>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item"><a href="solicitacao-advogados.php" class="footer-nav-link">Solicitação de Advogados</a></li>
                    <li class="footer-nav-item"><a href="inscricao-ordem.php" class="footer-nav-link">Inscrição na Ordem</a></li>
                    <li class="footer-nav-item"><a href="comunicados.php" class="footer-nav-link">Comunicados</a></li>
                    <li class="footer-nav-item"><a href="anuncios.php" class="footer-nav-link">Anúncios</a></li>
                </ul>
            </div>

            <!-- Column 4: Newsletter -->
            <div class="col-lg-3 col-md-12 mb-4 text-lg-start text-center">
                <h4 class="footer-heading">Newsletter</h4>
                <p class="footer-newsletter-text mb-3">Subscreva as principais novidades da OAGB.</p>
                <form action="subscricao.php" method="POST" id="newsletter-form" class="footer-newsletter-form">
                    <input type="email" name="email" class="footer-newsletter-input" placeholder="O seu e-mail" required>
                    <button type="submit" class="footer-newsletter-btn" aria-label="Subscrever"><i class="fas fa-paper-plane"></i></button>
                </form>
                <div id="newsletter-message" class="mt-2 text-start"></div>
            </div>
        </div>

        <!-- Integrated Copyright Bar -->
        <div class="row align-items-center footer-copyright-row pt-3 pb-4">
            <div class="col-md-9 text-lg-start text-center mb-md-0 mb-3">
                <p class="footer-copyright">
                    &copy; 2026 <a href="#">Ordem dos Advogados da Guiné-Bissau</a>. Todos direitos reservados.
                </p>
            </div>
            <div class="col-md-3 text-lg-end text-center">
                <a href="https://ada.gw" target="_blank" class="d-inline-block">
                    <img src="img/LogotipoADA.png" alt="ADA Logo" class="footer-partner-logo">
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Newsletter Script -->
<script>
if (document.getElementById('newsletter-form')) {
    document.getElementById('newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = this.querySelector('input[name="email"]').value;
        const messageDiv = document.getElementById('newsletter-message');
        
        fetch('subscricao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageDiv.innerHTML = '<span class="text-success" style="font-size: 0.8rem;">Sucesso!</span>';
                this.reset();
                setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
            } else {
                messageDiv.innerHTML = '<span class="text-danger" style="font-size: 0.8rem;">' + (data.message || 'Erro.') + '</span>';
            }
        })
        .catch(error => {
            messageDiv.innerHTML = '<span class="text-success" style="font-size: 0.8rem;">Enviado.</span>';
            this.reset();
            setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
        });
    });
}
</script>

<!-- Full Screen Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" style="z-index: 2050;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: rgba(45, 30, 25, 0.85); z-index: 2051;">
            <div class="modal-header" style="border-bottom: none !important;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 30px; top: 30px; z-index: 2060;"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center">
                <form action="pesquisa.php" method="GET" class="input-group" style="max-width: 600px;">
                    <input type="text" name="q" class="form-control bg-transparent text-white p-3" placeholder="O que procura?" style="border: 2px solid #B1A276; border-right: none; border-radius: 20px 0 0 20px;" required>
                    <button class="btn px-4" type="submit" style="background-color: #B1A276; color: white; border: 2px solid #B1A276; border-radius: 0 20px 20px 0; transition: .3s;"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Full Screen Search End -->

<!-- Header Functions (Componente Reutilizável) -->
<script src="js/header-functions.js?v=<?php echo time(); ?>"></script>