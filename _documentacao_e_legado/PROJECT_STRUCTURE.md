# OAGB Website - Estrutura Modular de CSS e Footer

**Data**: 2025-11-10  
**Status**: Em Implementação  
**Objetivo**: Reutilizar header, navbar e footer em todas as páginas do website

---

## 📋 Estado Atual

### ✅ Completo
- Footer estilizado (desktop + mobile)
- Navbar com scroll effects
- Carousel com conteúdo dinâmico
- Newsletter formulário (36px height)
- Todos os CSS integrados no `index.php`

### 🔄 Em Modularização
- CSS separado em arquivos reutilizáveis
- Footer HTML em arquivo include
- Header/Navbar estruturado para replicação

---

## 📁 Estrutura de Arquivos CSS

### Novos Arquivos Criados

```
c:\xampp\htdocs\oagb\
├── css/
│   ├── header-styles.css       (Novo - Header + Navbar + Search)
│   ├── footer-styles.css       (Novo - Footer styling completo)
│   └── shared-styles.css       (A criar - Estilos compartilhados)
└── includes/
    └── footer.php              (Já existe - HTML do footer)
```

---

## 🔧 Instruções de Implementação

### Para CADA página do website (agenda.php, artigo.php, etc):

#### 1. **No `<head>` da página, adicionar:**

```html
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

<!-- Shared CSS Files -->
<link rel="stylesheet" href="css/header-styles.css">
<link rel="stylesheet" href="css/footer-styles.css">
```

#### 2. **No local do `<body>` onde o header deve aparecer:**

```php
<!-- Include Navbar (desktop + mobile) -->
<?php include 'includes/navbar.php'; ?>
```

#### 3. **No local do `<body>` onde o footer deve aparecer:**

```php
<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>
```

#### 4. **Scripts necessários (antes de `</body>`):**

```html
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (opcional, se necessário) -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<!-- Main JS with scroll effects -->
<script src="js/main.js"></script>
```

---

## 📝 CSS Inclusos em Cada Arquivo

### **header-styles.css** 
Estilos para:
- ✅ Navbar desktop com scroll effects
- ✅ Topbar com informações de contacto
- ✅ Search modal
- ✅ Carousel styles
- ✅ Animações (zoomIn, slideIn)
- ✅ Link hover effects
- ✅ Responsive mobile navbar

### **footer-styles.css**
Estilos para:
- ✅ Títulos com underline (#5B463F, Open Sans)
- ✅ Newsletter formulário (height: 36px)
- ✅ Footer logo (alinhado com títulos)
- ✅ Menu columns (desktop vs mobile)
- ✅ Social icons (em linha no mobile)
- ✅ Copyright section
- ✅ Responsividade completa

---

## 🎯 Cores Utilizadas

| Elemento | Cor | Código |
|----------|-----|--------|
| Títulos Footer | Castanho | #5B463F |
| Underline Títulos | Laranja | #c18046 |
| Texto Geral | Cinzento Escuro | #111923 |
| Links (Scroll) | Dourado | #B1A276 |
| Hover Links | Dourado Escuro | #9d8f64 |

---

## 🔤 Tipografia

| Elemento | Font | Weight | Size |
|----------|------|--------|------|
| Títulos | Open Sans | 600 | 1.3rem |
| Textos Geral | Open Sans | 400 | 0.95rem |
| Headings | Libre Baskerville | 400/700 | Variável |

---

## 📱 Breakpoints Responsivos

- **Desktop**: `@media (min-width: 992px)`
- **Mobile**: `@media (max-width: 991.98px)`

---

## 🚀 Próximos Passos

1. ✅ Criar `css/header-styles.css` - **CONCLUÍDO**
2. ✅ Criar `css/footer-styles.css` - **CONCLUÍDO**
3. ⏳ Criar `css/shared-styles.css` - Estilos de página (hero, cards, etc)
4. ⏳ Atualizar `index.php` - Remover CSS inline, incluir novos CSS
5. ⏳ Replicar header + footer em: `agenda.php`, `artigo.php`, `noticias.php`, etc
6. ⏳ Testar responsividade em todas as páginas
7. ⏳ Validar cores, fonts, espaçamentos

---

## 🔍 Validação

Para cada página que incorporar header + footer:

- [ ] Header aparece corretamente (desktop + mobile)
- [ ] Footer aparece com logo alinhado (desktop)
- [ ] Footer centralizado (mobile)
- [ ] Newsletter formulário tem altura 36px
- [ ] Cores e fonts corretas
- [ ] Links funcionam
- [ ] Responsivo em todos os breakpoints

---

## 💾 Backup

Backup do estado atual guardado em:  
`c:\xampp\htdocs\oagb\backups\index_backup_2025-11-10.php.bak`

---

## 📞 Referência de Includes

```php
// Navbar (contém topbar + navbar + search modal)
<?php include 'includes/navbar.php'; ?>

// Footer (contém newsletter + menus + copyright)
<?php include 'includes/footer.php'; ?>

// Meta tags
<?php include 'includes/meta_tags_include.php'; ?>

// Functions auxiliares
require_once 'includes/functions.php';

// Conexão BD
require_once 'connect.php';
```

---

**Notas Importantes:**
- Todos os CSS têm `!important` onde necessário para garantir aplicação
- Mobile-first approach para melhor UX
- Animações CSS suavizam transições
- Colors são variáveis reutilizáveis em todo o site
