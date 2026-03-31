<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/counterup/counterup.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Template Javascript -->
<script src="js/main.js?v=<?php echo time(); ?>"></script>

<!-- Global Sharing Functions -->
<script>
function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).then(() => console.log('Successful share'))
        .catch((error) => console.log('Error sharing', error));
    } else {
        // Fallback or alert
        alert("O seu navegador não suporta compartilhamento direto. Copie o URL da barra de endereços.");
    }
}

function translatePage() {
    // Basic Google Translate widget integration or redirect
    window.open('https://translate.google.com/translate?sl=pt&tl=en&u=' + encodeURIComponent(window.location.href), '_blank');
}
</script>
