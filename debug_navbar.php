<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Debug Navbar - OAGB</title>
    <style>
        body { margin: 0; padding: 0; }
        .test-content { height: 2000px; background: linear-gradient(to bottom, #fff, #ccc); padding: 20px; }
        .navbar-dark { background: red; padding: 10px; }
        .navbar-scrolled { background: blue !important; }
        .debug-info { position: fixed; top: 10px; right: 10px; background: yellow; padding: 10px; z-index: 9999; }
    </style>
</head>
<body>
    <div class="debug-info">
        <div>Scroll: <span id="scroll-pos">0</span></div>
        <div>Classes: <span id="navbar-classes">none</span></div>
    </div>
    
    <div class="navbar navbar-dark">
        <div>Navbar Test</div>
    </div>
    
    <div class="test-content">
        <h1>Teste de Scroll do Navbar</h1>
        <p>Role para baixo para testar...</p>
        <div style="margin-top: 100px;">
            <h2>Seção 1</h2>
            <p>Conteúdo de teste</p>
        </div>
        <div style="margin-top: 200px;">
            <h2>Seção 2</h2>
            <p>Mais conteúdo</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up navbar scroll test...');
            
            const navbar = document.querySelector('.navbar-dark');
            const scrollPos = document.getElementById('scroll-pos');
            const navbarClasses = document.getElementById('navbar-classes');
            
            console.log('Navbar element:', navbar);
            
            if (navbar) {
                window.addEventListener('scroll', function() {
                    const currentScroll = window.scrollY;
                    scrollPos.textContent = currentScroll;
                    navbarClasses.textContent = navbar.className;
                    
                    console.log('Scroll position:', currentScroll);
                    
                    if (currentScroll > 100) {
                        navbar.classList.add('navbar-scrolled');
                        console.log('Added navbar-scrolled class');
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                        console.log('Removed navbar-scrolled class');
                    }
                });
            } else {
                console.error('Navbar element not found!');
            }
        });
    </script>
</body>
</html>