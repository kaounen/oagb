# Estado Actual da Sessão - Alterações no Site OAGB

**Data**: 2025
**Sessão**: Correções do Index.php e Navbar

## Ficheiros Modificados

### 1. index.php
**Localização**: `C:\xampp\htdocs\oagb\index.php`

#### Alterações Principais:

##### A. Estrutura HTML
- **Desktop Navbar & Carousel**: Reorganizados dentro do mesmo `container-fluid p-0` (sem padding, largura total)
- **Mobile Header**: Estrutura com carousel de fundo e elementos sobrepostos (contactos, navbar, conteúdo)
- **Facts Cards**: Posicionamento com `margin-top: -105px` para sobreposição ao slider

##### B. CSS Desktop (linhas ~459-598)
```css
/* Desktop navbar scroll effect */
@media (min-width: 992px) {
    /* Navbar fixo */
    .navbar-dark {
        position: fixed !important;
        top: 45px !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1030 !important;
        width: 100% !important;
        transition: all 0.3s ease !important;
        background: transparent !important;
        padding: 15px 0 !important;
    }

    /* Logo padrão */
    .navbar-dark .navbar-brand img {
        width: 70% !important;
        height: auto !important;
        padding-top: 5% !important;
        transition: all 0.3s ease !important;
    }

    /* Links brancos no estado inicial */
    .navbar-dark .navbar-nav .nav-link {
        color: white !important;
        transition: color 0.3s ease;
    }

    /* Estado scrolled */
    .navbar-scrolled {
        background-color: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
        top: 45px !important;
        padding: 8px 0 !important;
    }

    /* Logo menor durante scroll */
    .navbar-scrolled .navbar-brand img {
        width: 50% !important;
        padding-top: 2% !important;
    }

    /* Links dourados quando scrolled */
    .navbar-scrolled .navbar-nav .nav-link {
        color: #B1A276 !important;
    }

    /* Conteúdo do carousel mais baixo */
    #header-carousel .carousel-caption {
        top: 60% !important;
        bottom: auto !important;
        transform: translateY(-50%);
    }
}
```

##### C. CSS Mobile (linhas ~398-457)
```css
/* Mobile navbar - corrigir layout do botão menu */
@media (max-width: 991.98px) {
    /* Navbar mobile deve ter layout em coluna */
    .d-block.d-lg-none .navbar {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        padding: 1rem !important;
        background: transparent !important;
    }

    /* Logo centralizado */
    .d-block.d-lg-none .navbar-brand {
        margin-bottom: 1.5rem;
        order: 1;
    }

    .d-block.d-lg-none .navbar-brand img {
        width: 220px !important;
        max-width: 90% !important;
    }

    /* Botão menu abaixo do logo com texto MENU */
    .d-block.d-lg-none .navbar-toggler {
        order: 2;
        margin: 0 auto !important;
        position: relative !important;
        right: auto !important;
        top: auto !important;
        transform: none !important;
        display: block !important;
    }

    /* Adicionar texto MENU ao botão */
    .d-block.d-lg-none .navbar-toggler::after {
        content: ' MENU';
        margin-left: 8px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 14px;
    }

    /* Menu expandido */
    .d-block.d-lg-none .navbar-collapse {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.2);
        background: rgba(0, 0, 0, 0.9) !important;
        border-radius: 10px !important;
        backdrop-filter: blur(10px) !important;
    }
}
```

##### D. Classes de Texto (linhas ~178-218)
```css
/* Classes de texto do slider (do index2.html) */
.fonText {
    font-family: 'Open Sans', sans-serif;
    font-weight: bold;
    font-style: normal;
}

.fonText2 {
    font-family: 'Open Sans', sans-serif;
    font-weight: 400;
    font-style: normal;
}

.fonText3 {
    font-family: 'Open Sans', sans-serif;
    font-weight: 600;
    font-style: normal;
}

.fonText4 {
    font-family: 'Open Sans', sans-serif;
    font-weight: 300;
    font-style: normal;
    font-size: 90%;
}
```

##### E. Desktop Carousel HTML (linhas ~675-720)
```html
<!-- Desktop Carousel -->
<div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="w-100" src="..." alt="Slide">
            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center" style="padding-top: 5rem;">
                <div class="p-3" style="max-width: 900px;">
                    <h1 class="display-1 text-white mb-md-4 animated zoomIn fonText" style="text-decoration:underline;">
                        Título
                    </h1>
                    <h5 class="text-white mb-3 animated slideInDown fonText2">
                        Subtítulo
                    </h5>
                    <a href="#" class="btn btn-outline-light py-md-3 px-md-5 animated slideInRight">
                        Saiba mais
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

##### F. Mobile Carousel HTML (linhas ~723-795)
```html
<!-- Mobile Carousel with overlay content -->
<div id="header-carousel-mobile" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active" style="min-height: 100vh;">
            <img class="w-100" src="..." alt="Slide" style="position: absolute; width: 100%; height: 100vh; object-fit: cover;">
            
            <!-- Mobile Contact Info -->
            <div class="container-fluid px-3 py-3" style="position: absolute; top: 0; left: 0; right: 0; z-index: 1000;">
                <!-- Contactos -->
            </div>

            <!-- Mobile Navbar -->
            <div class="container-fluid position-relative p-0" style="position: absolute; top: 80px; left: 0; right: 0; z-index: 1000;">
                <?php include 'includes/navbar.php'; ?>
            </div>

            <!-- Mobile Slide Content -->
            <div class="carousel-caption d-flex flex-column align-items-center justify-content-end" style="bottom: 100px; padding: 1rem 1.5rem;">
                <!-- Conteúdo do slide -->
            </div>
        </div>
    </div>
</div>
```

### 2. navbar_include.php
**Localização**: `C:\xampp\htdocs\oagb\navbar_include.php`

#### Alterações:
- Removido `<center>` tag do botão menu
- Adicionado "O Bastonário" no menu ORDEM
- Removido link "Advogados" duplicado no menu ADVOGADOS
- Adicionado "Anúncios" no menu COMUNICAÇÃO
- Dropdown COMUNICAÇÃO alinhado à direita: `style="left: auto; right: 0;"`

### 3. NAVBAR_MOBILE_FIX_SUMMARY.md
**Localização**: `C:\xampp\htdocs\oagb\NAVBAR_MOBILE_FIX_SUMMARY.md`

Documentação das correções do navbar mobile.

## Funcionalidades Implementadas

### Desktop
✅ Navbar fixo com scroll effect
✅ Logo: 70% → 50% ao fazer scroll (sem deslocamento de posição)
✅ Links: branco → dourado (#B1A276) ao fazer scroll
✅ Topbar: escuro → branco ao fazer scroll
✅ Transições suaves (0.3s ease)
✅ Conteúdo do slider mais baixo (top: 60%)
✅ Carousel com fade transition
✅ Facts cards com sobreposição ao slider (-105px)

### Mobile
✅ Logo centralizado no topo
✅ Botão menu abaixo do logo com ícone + texto "MENU"
✅ Menu expansível com fundo escuro quando clicado
✅ Dropdowns com fundo branco e hover dourado
✅ Carousel com foto de fundo (100vh)
✅ Contactos, navbar e conteúdo sobrepostos na foto
✅ Text-shadow para melhor legibilidade

## Estado dos Componentes

### Navbar Desktop
- **Position**: Fixed em top: 45px (sempre)
- **Background inicial**: Transparent
- **Background scrolled**: White com blur e shadow
- **Padding inicial**: 15px 0
- **Padding scrolled**: 8px 0

### Navbar Mobile
- **Layout**: Flex column (centralizado)
- **Logo**: 220px, centralizado
- **Menu**: Abaixo do logo, centralizado
- **Expansão**: Fundo escuro com blur

### Carousel Desktop
- **Altura**: 650px
- **Fade**: Suave (carousel-fade)
- **Conteúdo**: Top 60%, transform translateY(-50%)
- **Título**: display-1, fonText (Open Sans bold), underline
- **Subtítulo**: h5, fonText2 (Open Sans 400)

### Carousel Mobile
- **Altura**: 100vh
- **Background**: Imagem absolute full screen
- **Camadas**: Contactos → Navbar → Conteúdo (bottom: 100px)
- **Texto**: Tamanhos reduzidos, text-shadow forte

## JavaScript

### Scroll Effect (linhas ~968-990)
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar-dark');
    const topbar = document.querySelector('.container-fluid.bg-dark.px-5.d-none.d-lg-block');

    if (navbar && window.innerWidth >= 992) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 45) {
                navbar.classList.add('navbar-scrolled');
                if (topbar) {
                    topbar.classList.add('topbar-scrolled');
                }
            } else {
                navbar.classList.remove('navbar-scrolled');
                if (topbar) {
                    topbar.classList.remove('topbar-scrolled');
                }
            }
        });
    }
});
```

## Referências de Código

### Baseado em:
- `apresentacao-historia.php` - Navbar scroll behavior e mobile structure
- `index2.html` - Classes de texto (fonText, fonText2, etc) e tamanhos de slider

## Próximos Passos Sugeridos

1. ✅ Desktop navbar scroll - COMPLETO
2. ✅ Mobile navbar layout - COMPLETO
3. ✅ Carousel desktop positioning - COMPLETO
4. ✅ Carousel mobile com overlay - COMPLETO
5. ⏸️ Overlay escuro nas fotos - REMOVIDO (causava problemas)
6. 🔄 Continuar ajustes conforme necessário

## Notas Importantes

- **Sem overlay**: Overlay foi removido pois não cobria completamente as imagens
- **Transições**: Carousel-fade funciona corretamente
- **Z-index**: Estrutura correta (topbar: 1040, navbar: 1030, mobile content: 1000)
- **Compatibilidade**: Bootstrap 5.0.0
- **Fontes**: Open Sans e Libre Baskerville

## Cores Usadas

- **Primária**: #B1A276 (dourado)
- **Hover**: #9d8f64 (dourado escuro)
- **Backgrounds**: 
  - Desktop normal: transparent
  - Desktop scrolled: rgba(255, 255, 255, 0.95)
  - Mobile menu: rgba(0, 0, 0, 0.9)

---

**Fim do Resumo**
