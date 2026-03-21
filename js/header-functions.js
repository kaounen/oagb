/* ================================================================
   HEADER FUNCTIONS - JavaScript reutilizável para o header
   Inclui: Quick Actions, Navbar Scroll Effect, etc.
   ================================================================ */

// ===== QUICK ACTIONS FUNCTIONS =====

// Variable to track if speaking
let isSpeaking = false;
let utterance = null;

/**
 * Share current page using native share API or clipboard fallback
 */
function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: 'Confira esta página da OAGB',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copiado para a área de transferência!');
        }).catch(() => {
            // Further fallback: show URL in prompt
            prompt('Copie este link:', window.location.href);
        });
    }
}

/**
 * Translate page using Google Translate
 */
function translatePage() {
    const googleTranslateScript = document.getElementById('google-translate-script');

    if (!googleTranslateScript) {
        // Add Google Translate script
        const script = document.createElement('script');
        script.id = 'google-translate-script';
        script.type = 'text/javascript';
        script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.onerror = function() {
            console.error('Erro ao carregar Google Translate');
            alert('Desculpe, não conseguimos carregar a ferramenta de tradução. Por favor, tente novamente.');
        };
        document.head.appendChild(script);

        // Initialize Google Translate
        window.googleTranslateElementInit = function() {
            try {
                if (window.google && window.google.translate) {
                    const translateElement = document.getElementById('google_translate_element');
                    if (!translateElement) {
                        console.error('Elemento de tradução não encontrado');
                        return;
                    }

                    new google.translate.TranslateElement({
                        pageLanguage: 'pt',
                        includedLanguages: 'en,fr,es,pt,es',
                        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                    }, 'google_translate_element');
                }
            } catch (e) {
                console.error('Erro ao inicializar Google Translate:', e);
            }
        };

        // Create translate element container if it doesn't exist
        if (!document.getElementById('google_translate_element')) {
            const translateDiv = document.createElement('div');
            translateDiv.id = 'google_translate_element';
            translateDiv.style.position = 'fixed';
            translateDiv.style.top = '60px';
            translateDiv.style.right = '20px';
            translateDiv.style.zIndex = '9999';
            translateDiv.style.backgroundColor = 'white';
            translateDiv.style.padding = '15px';
            translateDiv.style.borderRadius = '8px';
            translateDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            translateDiv.style.border = '1px solid #ddd';
            document.body.appendChild(translateDiv);
        }
    } else {
        // Toggle translate element visibility
        const translateElement = document.getElementById('google_translate_element');
        if (translateElement) {
            translateElement.style.display = translateElement.style.display === 'none' ? 'block' : 'none';
        }
    }
}

/**
 * Read aloud the content from container div using Web Speech API
 */
function readAloud() {
    // Stop if already speaking
    if (isSpeaking) {
        speechSynthesis.cancel();
        isSpeaking = false;
        // Update button text back
        const readBtn = document.querySelector('[data-action="read-aloud"]');
        if (readBtn) {
            readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            readBtn.title = 'Ler em voz alta';
        }
        return;
    }

    // Get container with content
    const container = document.querySelector('.container');
    if (!container) {
        alert('Conteúdo não encontrado para leitura em voz alta.');
        return;
    }

    // Extract text from container
    let text = '';
    const paragraphs = container.querySelectorAll('p, h3, h4, h5, li');
    paragraphs.forEach(element => {
        if (element.offsetParent !== null) { // Check if element is visible
            text += element.innerText + ' ';
        }
    });

    if (!text.trim()) {
        alert('Nenhum conteúdo disponível para leitura em voz alta.');
        return;
    }

    // Check browser support
    if (!('speechSynthesis' in window)) {
        alert('O seu navegador não suporta leitura em voz alta.');
        return;
    }

    // Create utterance
    utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'pt-PT'; // Portuguese (Portugal)
    utterance.rate = 1.0;
    utterance.pitch = 1.0;
    utterance.volume = 1.0;

    // Update button when speaking
    utterance.onstart = function() {
        isSpeaking = true;
        const readBtn = document.querySelector('[data-action="read-aloud"]');
        if (readBtn) {
            readBtn.innerHTML = '<i class="fas fa-stop"></i>';
            readBtn.title = 'Parar leitura';
        }
    };

    // Update button when finished
    utterance.onend = function() {
        isSpeaking = false;
        const readBtn = document.querySelector('[data-action="read-aloud"]');
        if (readBtn) {
            readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            readBtn.title = 'Ler em voz alta';
        }
    };

    // Handle errors
    utterance.onerror = function(event) {
        console.error('Erro na leitura em voz alta:', event.error);
        isSpeaking = false;
        const readBtn = document.querySelector('[data-action="read-aloud"]');
        if (readBtn) {
            readBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            readBtn.title = 'Ler em voz alta';
        }
    };

    // Start speaking
    speechSynthesis.speak(utterance);
}

// ===== DESKTOP NAVBAR SCROLL EFFECT =====

/**
 * Initialize navbar scroll effect - changes navbar appearance when scrolling
 */
function initializeNavbarScrollEffect() {
    const navbar = document.querySelector('.navbar-dark');
    const topbar = document.querySelector('#topbar'); // Desktop topbar fixed selector

    if (navbar && window.innerWidth >= 992) { // Only apply on desktop
        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    if (window.scrollY > 45) {
                        navbar.classList.add('navbar-scrolled');
                        if (topbar) {
                            topbar.classList.add('topbar-scrolled');
                            topbar.classList.add('is-scrolled');
                        }
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                        if (topbar) {
                            topbar.classList.remove('topbar-scrolled');
                            topbar.classList.remove('is-scrolled');
                        }
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }
}

// Auto-initialize navbar scroll effect when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeNavbarScrollEffect);
} else {
    // DOM is already loaded
    initializeNavbarScrollEffect();
}
