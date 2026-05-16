# Resumo de Estilos CSS Compartilhados - OAGB Website

**Data**: 2025-11-10  
**Versão**: 1.0  
**Objetivo**: Documentação completa de todos os estilos reutilizáveis

---

## 🎨 Estilos CSS por Componente

### HEADER (css/header-styles.css)

#### 1. Navbar Desktop
```css
- cor de fundo: transparente (até scroll)
- cor de fundo (scrolled): rgba(255, 255, 255, 0.95)
- texto: branco → #B1A276 (ao scroll)
- transição: 0.3s ease
- z-index: 1040
```

#### 2. Topbar (Desktop apenas)
```css
- altura: 45px
- cor de fundo: #000 (preto escuro)
- texto: branco → #B1A276 (ao scroll)
- posição: fixed, top: 0, width: 100%
- ícones: branco → dourado (ao scroll)
```

#### 3. Search Modal
```css
- fundo: rgba(9, 30, 62, 0.7)
- z-index: 2050/2051
- animação: fade in/out
```

#### 4. Carousel Desktop
```css
- altura: 650px
- min-height: 650px
- posição: relativa com caption centrada
- animações: zoomIn, slideInDown, slideInRight
```

#### 5. Animações
- **zoomIn**: scale3d(0.3, 0.3, 0.3) → 1.0
- **slideInDown**: translate3d(0, -100%, 0) → (0, 0, 0)
- **slideInRight**: translate3d(100%, 0, 0) → (0, 0, 0)
- **duration**: 1s

---

### FOOTER (css/footer-styles.css)

#### 1. Títulos do Menu
```css
- font-size: 1.3rem
- font-weight: 600
- font-family: 'Open Sans', sans-serif
- color: #5B463F (castanho escuro)
- letter-spacing: 0px
```

#### 2. Underline/Tracejado dos Títulos
```css
- tipo: pseudo-elemento ::after
- width: 45%
- height: 3px
- background-color: #c18046 (laranja)
- position: absolute, bottom: 0, left: 0
- z-index: 10
- mobile: left: 50%, transform: translateX(-50%) [centrado]
```

#### 3. Newsletter Formulário
```css
- height: 36px (input + botão)
- padding: 0.5rem 1rem
- border: white
- background input: transparente
- background botão: #c18046 (laranja)
- cor texto botão: white
```

#### 4. Logo Footer
```css
- width: 35% (relativo ao container)
- height: auto (manter proporção)
- desktop: padding-top: 3rem
- mobile: margin-top: -40px (sobrepor newsletter)
```

#### 5. Ícones Sociais
```css
- desktop: flex-direction: column
- mobile: flex-direction: row, flex-wrap: nowrap
- gap: 0.5rem
- tamanho botão: 36x36px
```

#### 6. Copyright Section
```css
- mobile: margin-top: -4rem
- text-align: center
- flex-direction: column
- height: auto
- padding-bottom: 1.5rem
```

---

## 📐 Layout Responsivo

### Breakpoints

```css
/* Desktop */
@media (min-width: 992px)
- Navbar normal com scroll effects
- Carousel height: 650px
- Topbar visível
- Footer com 4 colunas (logo + 3 menus)

/* Mobile */
@media (max-width: 991.98px)
- Navbar colapsa (hamburger menu)
- Carousel adaptado
- Topbar oculta (d-none)
- Footer 1 coluna, centralizado
- Títulos underline centrado
- Ícones sociais em linha
```

---

## 🎯 Cores Principais

| Componente | Cor | Hex | RGB |
|------------|-----|-----|-----|
| Títulos Footer | Castanho | #5B463F | rgb(91, 70, 63) |
| Underline | Laranja | #c18046 | rgb(193, 128, 70) |
| Texto Geral | Cinzento Escuro | #111923 | rgb(17, 25, 35) |
| Link Hover | Dourado | #B1A276 | rgb(177, 162, 118) |
| Link Hover Dark | Dourado Escuro | #9d8f64 | rgb(157, 143, 100) |
| Fundo Navbar | Preto | #000000 | rgb(0, 0, 0) |

---

## 🔤 Tipografia

| Elemento | Font | Weight | Size | Line-Height |
|----------|------|--------|------|-------------|
| Títulos Footer | Open Sans | 600 | 1.3rem | normal |
| Links Menu | Open Sans | 500 | 1rem | normal |
| Texto Geral | Open Sans | 400 | 0.95rem | normal |
| Headings | Libre Baskerville | 400/700 | variável | normal |
| Botões | Open Sans | 600 | 0.95rem | normal |

---

## 🔧 Classes e IDs Importantes

```css
/* Header */
.navbar-dark                          /* Navbar container */
.navbar-scrolled                      /* Applied on scroll */
.topbar-scrolled                      /* Applied on scroll */
#header-carousel                      /* Desktop carousel */
#header-carousel-mobile               /* Mobile carousel */
#searchModal                          /* Search modal */

/* Footer */
.section-title-sm                     /* Títulos do footer */
.section-title-sm::after              /* Underline dos títulos */
.col-lg-4.col-md-6.footer-about      /* Logo container */
#newsletter-form                      /* Newsletter form */
.row.justify-content-end               /* Copyright section */
.d-flex.align-items-center.justify-content-center  /* Copyright content */
```

---

## 🎪 Estados de Elementos

### Navbar Links
```css
- padrão: branco
- hover: #B1A276
- active: #B1A276
- scrolled: #B1A276
- scrolled hover: #9d8f64
```

### Botões Sociais
```css
- padrão: border branco, ícone branco
- hover: fundo #B1A276, transição 0.3s
- scrolled: border #B1A276, ícone #B1A276
```

### Form Control (Newsletter)
```css
- background: transparente
- border: white
- padding: 0.5rem 1rem
- height: 36px
- font-size: 0.95rem
```

---

## 📦 Dependências Externas

```html
<!-- Fonts -->
Google Fonts: Open Sans, Libre Baskerville

<!-- Icons -->
Font Awesome 5.10.0
Bootstrap Icons 1.4.1

<!-- Framework -->
Bootstrap 5.0.0
jQuery 3.4.1 (opcional)

<!-- Libs -->
easing.min.js
waypoints.min.js
counterup.min.js
owl.carousel.min.js
```

---

## ✅ Checklist de Implementação

Para cada página que incorporar header + footer:

- [ ] Incluir `header-styles.css`
- [ ] Incluir `footer-styles.css`
- [ ] Incluir Google Fonts
- [ ] Incluir Font Awesome + Bootstrap Icons
- [ ] Include `navbar.php`
- [ ] Include `footer.php`
- [ ] Incluir `main.js` com scroll effects
- [ ] Testar desktop (992px+)
- [ ] Testar mobile (320px-991px)
- [ ] Validar cores
- [ ] Validar fonts
- [ ] Validar responsividade
- [ ] Testar links navbar
- [ ] Testar search modal
- [ ] Testar newsletter formulário
- [ ] Testar scroll effects

---

## 🔄 Fluxo de Inclusão

```
HTML Page
    ↓
Meta Tags (meta_tags_include.php)
    ↓
Google Fonts
    ↓
Font Awesome + Bootstrap Icons
    ↓
Bootstrap CSS
    ↓
header-styles.css ← Navbar, topbar, search
    ↓
footer-styles.css ← Footer, newsletter
    ↓
[Page-specific CSS]
    ↓
navbar.php include ← Topbar + navbar + search modal
    ↓
[Page Content]
    ↓
footer.php include ← Footer + newsletter + copyright
    ↓
Bootstrap JS
    ↓
jQuery (if needed)
    ↓
main.js (scroll effects)
    ↓
[Page-specific JS]
```

---

## 🚀 Performance Notes

- CSS files são compactos (~2KB cada)
- Uso de `!important` é mínimo e estratégico
- Media queries bem organizadas
- Transições CSS suaves (0.3s)
- Z-index gerenciado (topbar: 1040, modal: 2050)

---

**Última Atualização**: 2025-11-10  
**Criado por**: Análise de Projeto  
**Status**: Documento de Referência
