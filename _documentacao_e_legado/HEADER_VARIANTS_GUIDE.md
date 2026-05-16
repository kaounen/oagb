# 📐 Guia de Variantes de Header - OAGB Website

**Data**: 10 de Novembro de 2025  
**Versão**: 1.0  
**Status**: 🎨 Especificação de Design

---

## 🎯 Objetivo

Criar **3 variantes flexíveis de header** que mantêm consistência visual enquanto se adaptam ao conteúdo específico de cada página.

---

## 📊 Matriz de Variantes

| Variante | Slider | Foto Fundo | Breadcrumbs | Quick Actions | Altura | Páginas Exemplo |
|----------|--------|-----------|-------------|---------------|--------|-----------------|
| **A** | ✅ Sim | ✅ Sim (Slider) | ❌ Não | ❌ Não | 650px | index.php |
| **B** | ❌ Não | ✅ Sim (Estática) | ✅ Sim | ✅ Sim (Voltar/Imprimir/Partilhar/Traduzir) | 400px (como advogados-inscritos.php) | apresentacao-historia.php |
| **C** | ❌ Não | ❌ Não | ✅ Sim | ✅ Sim (Voltar/Imprimir/Partilhar/Traduzir) | Auto | agenda.php, noticias.php |

---

## 🔧 Componentes Obrigatórios (Todas as Variantes)

Todos os headers mantêm **SEMPRE**:

```html
✅ Desktop Topbar (endereço, telefone, ícones)
✅ Desktop Navbar (logo, menu, search)
✅ Mobile Contact Info (telefone, email, horário)
✅ Mobile Navbar (logo + menu)
✅ Search Modal (fullscreen)
✅ Scroll Effects (topbar + navbar mudam de cor)
✅ Responsividade (992px breakpoint)
```

---

## 📱 Variante A: Header com Slider (index.php)

### Estrutura

```html
<!-- DESKTOP -->
├── Topbar (bg-dark)
├── Navbar Fixo (logo + menu)
└── Header Content
    ├── Slider/Carousel
    │   ├── Imagem 1
    │   ├── Imagem 2
    │   └── Imagem 3
    └── Slide Content (título + resumo + botão)

<!-- MOBILE -->
├── Contact Info (topo)
├── Navbar (logo + menu)
└── Mobile Header Slide
    ├── Background image
    ├── Mobile slide content (título + resumo + botão)
    └── Carousel indicators (pontos)
```

### Dimensões

**Desktop:**
- Header altura: 650px
- Carousel altura: 650px
- Slide content: posição absolute, bottom: ~140px

**Mobile:**
- Min-height: 110vh (full height + mais)
- Slide content: position absolute, bottom: 140px

### Elementos Específicos

```css
/* Desktop Carousel */
#header-carousel {
    height: 650px;
    position: relative;
}

#header-carousel .carousel-item {
    background-size: cover;
    background-position: center;
    height: 650px;
}

/* Mobile background */
.mobile-header-slide {
    background-image: url('...');
    background-size: cover;
    background-position: center;
    min-height: 110vh;
}

.mobile-slide-content {
    position: absolute;
    bottom: 140px;
    z-index: 500;
    padding: 1rem 1.5rem;
}
```

### Conteúdo do Slide

```html
<!-- Titulo + Resumo + Botão -->
<h1>{{ slide_title }}</h1>
<p>{{ slide_description }}</p>
<a href="{{ slide_link }}" class="btn btn-light rounded-pill">Saber Mais</a>
```

---

## 📋 Variante B: Header com Breadcrumbs (apresentacao-historia.php)

### Estrutura

```html
<!-- DESKTOP -->
├── Topbar (bg-dark)
├── Navbar Fixo (logo + menu)
└── Header Content (COM FOTO DE FUNDO ESTÁTICA)
    ├── Background image (como advogados-inscritos.php)
    └── Conteúdo sobreposto
        ├── Breadcrumbs (Início > Ordem > Apresentação e História)
        ├── Título da página
        ├── Quick Actions (ícones)
        │   ├── Voltar (← back button)
        │   ├── Imprimir (🖨️ print)
        │   ├── Partilhar (📤 share)
        │   └── Traduzir (🌐 translate)
        └── [Overlay transparente para legibilidade]

<!-- MOBILE -->
├── Contact Info (topo)
├── Navbar (logo + menu)
└── Mobile Header Content (COM FOTO DE FUNDO)
    ├── Background image
    ├── Título
    ├── Breadcrumbs (stacked)
    └── Quick Actions (inline)
```

### Dimensões

**Desktop:**
- Header altura: 400px (mesma que advogados-inscritos.php)
- Topbar: 45px
- Navbar: ~70px
- Conteúdo: altura 400px, centered verticalmente, overlay rgba(0,0,0,0.4)

**Mobile:**
- Min-height: 50vh (proporcional)
- Conteúdo: centered, com espaço superior/inferior

### Elementos Específicos

```css
/* Header Container */
.bg-header-breadcrumbs {
    background-image: url('img/close-up-scales-justice.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
    padding: 40px 0;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Overlay escuro para legibilidade */
.bg-header-breadcrumbs::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

/* Conteúdo sobre overlay */
.bg-header-breadcrumbs > div {
    position: relative;
    z-index: 2;
}

/* Breadcrumbs */
.breadcrumb {
    background: transparent;
    padding: 0.5rem 0;
    margin-bottom: 1rem;
}

.breadcrumb-item {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.breadcrumb-item.active {
    color: #c18046;
    font-weight: 600;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
}

.quick-actions .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    transition: all 0.3s ease;
}

.quick-actions .btn:hover {
    background: #c18046;
    border-color: #c18046;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(193, 128, 70, 0.4);
}
```

### Conteúdo Específico

```html
<!-- Breadcrumbs HTML -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Início</a></li>
        <li class="breadcrumb-item"><a href="#ordem">Ordem</a></li>
        <li class="breadcrumb-item active">{{ page_title }}</li>
    </ol>
</nav>

<!-- Título -->
<h1 class="text-white mb-3">{{ page_title }}</h1>

<!-- Quick Actions -->
<div class="quick-actions">
    <button class="btn" title="Voltar atrás" onclick="history.back()">
        <i class="bi bi-arrow-left"></i>
    </button>
    <button class="btn" title="Imprimir" onclick="window.print()">
        <i class="bi bi-printer"></i>
    </button>
    <button class="btn" title="Partilhar" onclick="sharePage()">
        <i class="bi bi-share"></i>
    </button>
    <button class="btn" title="Traduzir" onclick="translatePage()">
        <i class="bi bi-globe"></i>
    </button>
</div>
```

---

## 📌 Variante C: Header Simples (agenda.php, noticias.php)

### Estrutura C

```html
<!-- DESKTOP -->
├── Topbar (bg-dark)
├── Navbar Fixo (logo + menu)
└── Header Content (SEM SLIDER, SEM FOTO)
    ├── Background: Gradiente simples (azul)
    └── Conteúdo
        ├── Breadcrumbs (Início > Agenda / Notícias)
        ├── Título da página
        ├── Quick Actions (ícones)
        │   ├── Voltar
        │   ├── Imprimir
        │   ├── Partilhar
        │   └── Traduzir
        └── [Sem overlay, sem foto]

<!-- MOBILE -->
├── Contact Info (topo)
├── Navbar (logo + menu)
└── Mobile Header Content
    ├── Título
    ├── Breadcrumbs (stacked)
    └── Quick Actions (inline)
```

### Dimensões C

**Desktop:**
- Header altura: Auto (mínimo 200px)
- Topbar: 45px
- Navbar: ~70px
- Conteúdo: altura adaptável, centered verticalmente

**Mobile:**
- Min-height: 35vh
- Conteúdo: centered

### Elementos Específicos C

```css
/* Header Container */
.bg-header-simple {
    background: linear-gradient(135deg, #091E3E 0%, #1a3a5c 100%);
    position: relative;
    padding: 40px 0;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-header-simple h1 {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.bg-header-simple p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

@media (max-width: 991.98px) {
    .bg-header-simple {
        min-height: 35vh;
    }
    
    .bg-header-simple h1 {
        font-size: 1.8rem;
    }
    
    .bg-header-simple p {
        font-size: 0.95rem;
    }
}
```

### Conteúdo C

```html
<!-- Breadcrumbs + Título + Quick Actions -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Início</a></li>
        <li class="breadcrumb-item active">{{ page_title }}</li>
    </ol>
</nav>

<h1 class="text-white mb-3">{{ page_title }}</h1>

<div class="quick-actions">
    <button class="btn" title="Voltar atrás" onclick="history.back()">
        <i class="bi bi-arrow-left"></i>
    </button>
    <button class="btn" title="Imprimir" onclick="window.print()">
        <i class="bi bi-printer"></i>
    </button>
    <button class="btn" title="Partilhar" onclick="sharePage()">
        <i class="bi bi-share"></i>
    </button>
    <button class="btn" title="Traduzir" onclick="translatePage()">
        <i class="bi bi-globe"></i>
    </button>
</div>
```

---

## 🔄 Elementos Mantidos em Todas as Variantes

### Desktop

```
┌─────────────────────────────────────────────┐
│ Topbar (45px, bg-dark)                     │ ← SEMPRE
│ Endereço • Telefone • Horário • Ícones    │
├─────────────────────────────────────────────┤
│ Navbar Fixo (70px, transparent→white)     │ ← SEMPRE
│ Logo • Menu • Search                      │
├─────────────────────────────────────────────┤
│ Header Conteúdo (Variável)                │ ← Muda
│ [Slider / Breadcrumbs / Simples]          │
├─────────────────────────────────────────────┤
│ Conteúdo da Página                        │
└─────────────────────────────────────────────┘
```

### Mobile

```
┌─────────────────────────────────────────────┐
│ Contact Info (Telefone • Email • Horário) │ ← SEMPRE
├─────────────────────────────────────────────┤
│ Navbar (Logo + Menu Button)               │ ← SEMPRE
├─────────────────────────────────────────────┤
│ Header Conteúdo (Variável)                │ ← Muda
│ [Slider / Breadcrumbs / Simples]          │
├─────────────────────────────────────────────┤
│ Conteúdo da Página                        │
└─────────────────────────────────────────────┘
```

---

## 🎨 Scroll Effects (Todas as Variantes)

**Desktop:**
- Topbar: branca (bg-light) com texto #B1A276
- Navbar: logo menor (70% width), fundo branco
- Menu: texto #B1A276
- Transição suave (0.3s ease)

**Mobile:**
- Menu: funciona sempre igual (no modal collapse)
- Sem mudanças no scroll

---

## 📐 Breakpoints

| Breakpoint | Tipo | Aplicação |
|-----------|------|-----------|
| 992px | Desktop/Mobile | Navbar responsive, Header layout |
| 768px | Tablet/Mobile | Adjustments de espaçamento |
| 576px | Mobile pequeno | Font sizes, padding |

---

## 🔍 Detalhes de Implementação

### Quick Actions (Variante B)

```javascript
// Voltar
function goBack() {
    window.history.back();
}

// Imprimir
function printPage() {
    window.print();
}

// Partilhar (Web Share API)
function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        });
    } else {
        // Fallback: copiar link
        copyToClipboard(window.location.href);
    }
}

// Traduzir (Google Translate)
function translatePage() {
    const googleTranslateScript = document.getElementById('google-translate-script');
    if (!googleTranslateScript) {
        const script = document.createElement('script');
        script.id = 'google-translate-script';
        script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        document.head.appendChild(script);
        
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement(
                {pageLanguage: 'pt'},
                'google_translate_element'
            );
        };
    }
}
```

---

## 📋 Páginas e Suas Variantes

### Variante A (Com Slider)
- `index.php` ✅

### Variante B (Com Breadcrumbs)
- `apresentacao-historia.php` ✅
- `comissoes-especializadas.php`
- `estatutos.php`
- Outras páginas institucionais

### Variante C (Simples)
- `agenda.php`
- `noticias.php`
- `pesquisa-advogados.php`
- `advogados-inscritos.php`
- `estagiarios-inscritos.php`
- `publicacoes.php`
- `contacto.php`
- `inscricao-ordem.php`
- Outras páginas de conteúdo

---

## 🛠️ Arquivos a Criar

1. ✅ **header-variants.css** - CSS para as 3 variantes
2. ✅ **header-variant-a.php** - Header com slider
3. ✅ **header-variant-b.php** - Header com breadcrumbs
4. ✅ **header-variant-c.php** - Header simples
5. ✅ **header-functions.js** - Funções JS (share, print, translate)

---

## 📝 Notas Importantes

1. **Navbar sempre igual** em todas as variantes
2. **Topbar sempre igual** em desktop (45px, bg-dark)
3. **Mobile Contact Info sempre igual**
4. **Muda apenas** o conteúdo do header (slider/breadcrumbs/simples)
5. **Todos scroll effects mantidos**
6. **Responsividade mantida** (992px breakpoint)
7. **Cores e fonts mantidas** (Open Sans, Libre Baskerville, #B1A276, #c18046)

---

## ✅ Checklist de Validação

- [ ] Desktop navbar scroll effects funcionando
- [ ] Mobile navbar collapse funcionando
- [ ] Topbar muda cor no scroll (desktop)
- [ ] Logo muda tamanho no scroll (desktop)
- [ ] Search modal abre/fecha
- [ ] Quick actions funcionam (breadcrumbs)
- [ ] Slider funciona (index.php)
- [ ] Responsividade em 992px
- [ ] Mobile layout correto
- [ ] Sem overlap de elementos
- [ ] Z-index correto em todos elementos
- [ ] Transições suaves (0.3s ease)

---

**Próximo Passo**: Criar os 3 headers em PHP modular
