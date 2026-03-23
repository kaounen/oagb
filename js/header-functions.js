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
 * Initialize Google Translate
 */
window.googleTranslateElementInit = function() {
    new google.translate.TranslateElement({
        pageLanguage: 'pt',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
};

/**
 * Changes the site language using Google Translate
 * @param {string} langCode - The ISO code of the language (en, fr, es, etc.)
 */
function changeLanguage(langCode) {
    // If it's portuguese, we want to clear translation
    if (langCode === 'pt') {
        const clearBtn = document.querySelector('.goog-close-link');
        if (clearBtn) {
            clearBtn.click();
        } else {
            // Fallback: clear cookie and reload
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + location.hostname;
            location.reload();
        }
        return;
    }

    // Try to find the google translate combo box
    const selectElement = document.querySelector('select.goog-te-combo');
    if (selectElement) {
        selectElement.value = langCode;
        selectElement.dispatchEvent(new Event('change'));
    } else {
        // If script not loaded, load it and then try to translate
        if (!document.getElementById('google-translate-script')) {
            const script = document.createElement('script');
            script.id = 'google-translate-script';
            script.type = 'text/javascript';
            script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            document.head.appendChild(script);
            
            // Create hidden container if needed
            if (!document.getElementById('google_translate_element')) {
                const div = document.createElement('div');
                div.id = 'google_translate_element';
                div.style.display = 'none';
                document.body.appendChild(div);
            }

            // Wait for script to load and then try again
            setTimeout(() => changeLanguage(langCode), 1000);
        } else {
            // Script is there but maybe widget not ready
            document.cookie = "googtrans=/pt/" + langCode + "; path=/";
            location.reload();
        }
    }
}

/**
 * Legacy support for translate toggle
 */
function translatePage() {
    const translateElement = document.getElementById('google_translate_element');
    if (translateElement) {
        translateElement.style.display = translateElement.style.display === 'none' ? 'block' : 'none';
        translateElement.style.position = 'fixed';
        translateElement.style.top = '80px';
        translateElement.style.right = '20px';
        translateElement.style.zIndex = '10000';
        translateElement.style.background = 'white';
        translateElement.style.padding = '10px';
        translateElement.style.borderRadius = '5px';
        translateElement.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    } else {
        changeLanguage('en'); // Default to English if toggled and not exists
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
        // Efeito imediato caso a página seja carregada a meio do scroll
        if (window.scrollY > 45) {
            navbar.classList.add('navbar-scrolled');
            if (topbar) {
                topbar.classList.add('topbar-scrolled');
                topbar.classList.add('is-scrolled');
            }
        }
        
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
