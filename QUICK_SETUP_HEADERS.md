# 🎯 Guia Rápido - 3 Variantes de Header

**Data**: 10 de Novembro de 2025  
**Status**: ✅ Pronto para Implementação

---

## 📂 Arquivos Disponíveis

```
includes/
├── navbar.php              ← Sempre incluído
├── footer.php              ← Footer comum
├── header-variant-a.php    ← Slider (index.php) - JÁ EXISTE
├── header-variant-b.php    ← Com Breadcrumbs + Background (apresentacao-historia.php)
└── header-variant-c.php    ← Simples sem Background (agenda.php, noticias.php)
```

---

## 🔧 Como Implementar

### **Variante A: Com Slider (index.php)** ✅
Já implementado. Sem alterações.

---

### **Variante B: Com Breadcrumbs + Background** 📋

**Usado em**: `apresentacao-historia.php`, `comissoes-especializadas.php`, páginas institucionais

**No `<head>`:**
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
```

**No `<body>` (substituir header existente):**
```php
<?php
// Configurar breadcrumbs
$page_title = 'Apresentação e História';
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Ordem', 'url' => '#'],
    ['label' => 'Apresentação e História']
];
$background_image = 'img/close-up-scales-justice.jpg';

// Incluir header variante B
include 'includes/header-variant-b.php';
?>
```

**Resultado:**
- ✅ Foto de fundo estática (400px altura)
- ✅ Breadcrumbs funcionais
- ✅ Título da página
- ✅ Quick Actions (Voltar/Imprimir/Partilhar/Traduzir)
- ✅ Navbar scroll effect
- ✅ Mobile responsivo

---

### **Variante C: Simples sem Background** 🎫

**Usado em**: `agenda.php`, `noticias.php`, `pesquisa-advogados.php`, `advogados-inscritos.php`, etc

**No `<head>`:**
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
```

**No `<body>` (substituir header existente):**
```php
<?php
// Configurar variáveis
$page_title = 'Agenda de Eventos';
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Agenda']
];

// Incluir header variante C
include 'includes/header-variant-c.php';
?>
```

**Resultado:**
- ✅ Fundo gradiente azul (sem foto)
- ✅ Altura mínima 200px (auto-adapta)
- ✅ Breadcrumbs funcionais
- ✅ Título da página
- ✅ Quick Actions (Voltar/Imprimir/Partilhar/Traduzir)
- ✅ Navbar scroll effect
- ✅ Mobile responsivo

---

## 📋 Breadcrumbs - Exemplos

### Exemplo 1: 2 Níveis
```php
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Agenda']
];
```

### Exemplo 2: 3 Níveis
```php
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Ordem', 'url' => '#'],
    ['label' => 'Apresentação']
];
```

### Exemplo 3: 4 Níveis
```php
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Ordem', 'url' => '#'],
    ['label' => 'Associados', 'url' => '#'],
    ['label' => 'Pesquisa de Advogados']
];
```

**Regra**: Último item NÃO tem `'url'` (é página atual, em cor dourada #c18046)

---

## 🎨 Quick Actions - Funções JS

Adicionar este script no `<body>` de cada página:

```php
<script>
    // Voltar atrás
    function goBack() {
        window.history.back();
    }

    // Imprimir página
    function printPage() {
        window.print();
    }

    // Partilhar (Web Share API)
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                text: document.querySelector('h1')?.textContent || document.title,
                url: window.location.href
            }).catch(err => console.log('Erro ao partilhar:', err));
        } else {
            // Fallback: copiar link
            const link = window.location.href;
            navigator.clipboard.writeText(link).then(() => {
                alert('Link copiado para clipboard!');
            });
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
                    {pageLanguage: 'pt', layout: google.translate.TranslateElement.InlineLayout.SIMPLE},
                    'google-translate-element'
                );
            };
        }
    }
</script>

<!-- Google Translate container (invisible) -->
<div id="google-translate-element" style="display: none;"></div>
```

---

## 📐 Dimensões Resumo

| Elemento | Desktop | Mobile |
|----------|---------|--------|
| Variant A (Slider) | 650px | 110vh |
| Variant B (Breadcrumbs) | 400px | 60vh |
| Variant C (Simples) | 200px+ | 35vh+ |
| Topbar | 45px | Escondido |
| Navbar | ~70px | ~60px |

---

## ✅ Checklist Implementação

Para cada página a atualizar:

- [ ] Página identificada e classificada (Var A/B/C)
- [ ] Includes CSS adicionados (Bootstrap, Font Awesome, Bootstrap Icons)
- [ ] Header variante incluído com `$page_title` e `$breadcrumbs` corretos
- [ ] Arquivo footer.php incluído
- [ ] Scripts JS adicionados (share/print/translate)
- [ ] Desktop testado (1920px, 1366px, 992px)
- [ ] Mobile testado (375px, 414px, 480px)
- [ ] Navbar scroll effects funcionando
- [ ] Quick actions funcionando
- [ ] Breadcrumbs corretos e clickáveis
- [ ] Sem console errors
- [ ] Responsividade 992px OK

---

## 📝 Páginas por Variante

### **Variante B** (Com Breadcrumbs + Background)
- `apresentacao-historia.php`
- `comissoes-especializadas.php`
- `estatutos.php`
- Outras páginas institucionais

### **Variante C** (Simples)
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

## 🔗 Includes Necessários (Todas Páginas)

```php
<?php
// No início do arquivo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Definir variáveis
$page_title = 'Seu Título';
$breadcrumbs = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Sua Página']
];
?>
```

---

## 🎯 Próximo Passo

1. ✅ Documentação criada
2. ✅ Headers PHP criados
3. ⏳ **Próximo**: Implementar em `apresentacao-historia.php` como teste
4. ⏳ **Depois**: Replicar para restantes páginas

Deseja começar com `apresentacao-historia.php`? 🚀
