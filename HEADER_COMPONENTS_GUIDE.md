# Header Components Guide

## Visão Geral

Este documento descreve os componentes reutilizáveis de CSS e JavaScript para o header de todas as páginas do site OAGB.

**Objetivo:** Evitar duplicação de código, garantir consistência e facilitar manutenção entre todas as páginas.

---

## Componentes Incluídos

### 1. CSS - header-styles.css
**Localização:** `css/header-styles.css`

#### O que contém:
- **Quick Actions (Botões circulares):**
  - Estilos para botões de ações rápidas (voltar, imprimir, partilhar, traduzir)
  - Hover effects e animações
  - Responsivo para desktop e mobile

- **Navbar e Botões:**
  - Estilos para navegação principal
  - Cores e transições
  - Estados hover e active

- **Carousel/Slider (se usado):**
  - Layout para carousels com overlay
  - Caption positioning
  - Responsividade

- **Mobile Header:**
  - Informações de contato no mobile
  - Menu toggle responsivo
  - Navbar wrapper para mobile

- **Scroll Effects:**
  - Navbar transformação durante scroll
  - Topbar (barra de endereços) mudança de cor
  - Desktop e mobile separados

---

### 2. JavaScript - header-functions.js
**Localização:** `js/header-functions.js`

#### Funções incluídas:

##### `sharePage()`
- Compartilha a página atual usando:
  - Native Share API (se disponível)
  - Clipboard fallback
  - Prompt fallback para navegadores antigos

##### `translatePage()`
- Integra Google Translate na página
- Cria o elemento de tradução dinamicamente
- Toggle de visibilidade

##### `readAloud()`
- Lê em voz alta o conteúdo da página
- Usa Web Speech API
- Suporte para múltiplos idiomas (PT-PT)
- Botão muda estado durante leitura

##### `initializeNavbarScrollEffect()`
- Inicializa o efeito de scroll do navbar
- Adiciona/remove classes CSS quando scroll
- Auto-inicializado ao carregar a página

---

## Como Usar

### Passo 1: Incluir o CSS na página

No `<head>` da página, adicione:

```html
<!-- Header Styles -->
<link href="css/header-styles.css" rel="stylesheet">
```

**Importante:** Deve ser incluído **depois** do Bootstrap CSS para ter prioridade correta.

---

### Passo 2: Incluir o JavaScript na página

No final do `<body>` (antes de fechar `</body>`), adicione:

```html
<!-- Header Functions -->
<script src="js/header-functions.js"></script>
```

---

### Passo 3: Usar na página

#### Quick Actions (Botões circulares)
```html
<div class="quick-actions mt-3">
    <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2" title="Voltar atrás">
        <i class="fas fa-arrow-left"></i>
    </a>
    <a href="javascript:window.print()" class="btn btn-outline-light btn-sm me-2" title="Imprimir">
        <i class="fas fa-print"></i>
    </a>
    <a href="#" class="btn btn-outline-light btn-sm me-2" title="Partilhar" onclick="sharePage()">
        <i class="fas fa-share-alt"></i>
    </a>
    <a href="#" class="btn btn-outline-light btn-sm" title="Traduzir" onclick="translatePage()">
        <i class="fas fa-language"></i>
    </a>
</div>
```

#### Navbar Scroll Effect
O navbar scroll effect é **auto-inicializado** quando `header-functions.js` é carregado.

Ele detecta automaticamente:
- Elemento `.navbar-dark`
- Elemento `.bg-dark` (topbar)
- Breakpoint de desktop (992px+)

---

## Estrutura CSS

### Secções principais:

1. **QUICK ACTIONS** - Botões circulares
2. **NAVBAR BUTTONS** - Estilos de botões da navbar
3. **CAROUSEL/SLIDER OVERLAYS** - Para páginas com carousel (se usado)
4. **MOBILE HEADER CONTACTS** - Contato no mobile
5. **MOBILE NAVBAR WRAPPER** - Menu mobile
6. **RESPONSIVE ADJUSTMENTS** - Media queries (768px)
7. **MOBILE NAVBAR STYLING** - Mobile específico (até 991.98px)
8. **DESKTOP NAVBAR WITH SCROLL EFFECT** - Desktop específico (992px+)

### Breakpoints usados:
- `max-width: 768px` - Devices muito pequenos
- `max-width: 991.98px` - Tablets e abaixo
- `min-width: 992px` - Desktop e acima

---

## Cores e Variáveis

### Cores principais usadas:
- **Dourado:** `#B1A276` - Cor dourada para hover no scroll
- **Branco:** `rgba(255,255,255,...)` - Transparências variadas
- **Preto:** `rgba(0,0,0,...)` - Overlays e transparências
- **Primária:** `var(--primary)` - Cor primária do Bootstrap

### Customizações:
Para alterar cores, edite o ficheiro `css/header-styles.css` e substitua os valores de cor.

---

## Exemplo de Inclusão Completa

```html
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Exemplo</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Header Styles - IMPORTANTE: Depois do Bootstrap -->
    <link href="css/header-styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <!-- Desktop topbar -->
    </div>

    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>

        <!-- Header com quick actions -->
        <div class="container-fluid bg-primary py-5 bg-header">
            <div class="row py-5">
                <div class="col-12 text-center">
                    <h1 class="text-white">Título da Página</h1>

                    <!-- Quick Actions -->
                    <div class="quick-actions mt-3">
                        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <a href="javascript:window.print()" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2" onclick="sharePage()">
                            <i class="fas fa-share-alt"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm" onclick="translatePage()">
                            <i class="fas fa-language"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteúdo da página -->
    <div class="container">
        <!-- Seu conteúdo aqui -->
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Header Functions - IMPORTANTE: No final do body -->
    <script src="js/header-functions.js"></script>
</body>
</html>
```

---

## Checklist de Implementação

Para cada página nova que use o header:

- [ ] Incluir `css/header-styles.css` no `<head>`
- [ ] Incluir `js/header-functions.js` no final do `<body>`
- [ ] Adicionar classe `quick-actions` aos botões circulares
- [ ] Adicionar classes CSS apropriadas:
  - `.navbar-dark` - Navbar principal
  - `.bg-header` - Header container
  - `.mobile-navbar-wrapper` - Mobile navbar (se aplicável)
- [ ] Verificar responsividade em mobile e desktop
- [ ] Testar botões: voltar, imprimir, partilhar, traduzir

---

## Troubleshooting

### Navbar não fica com scroll effect
- Verificar se elemento `.navbar-dark` existe
- Verificar se width do viewport é >= 992px
- Verificar se `js/header-functions.js` está carregado

### Quick actions buttons aparecem achatados
- Confirmar que `css/header-styles.css` está carregado
- Verificar se classe `.quick-actions` está no container
- Verificar se resolve de CSS não está sobrescrevendo

### Tradução não funciona
- Verificar se elemento `#google_translate_element` foi criado
- Abrir console e verificar se há erros de script
- Verificar conexão com Google Translate (internet necessária)

### Share button não funciona
- Verificar se navegador suporta Web Share API (Chrome mobile, Safari, etc)
- Fallback para clipboard deve funcionar em todos os navegadores
- Alguns navegadores podem solicitar permissão

---

## Manutenção e Updates

### Quando adicionar novas funcionalidades ao header:

1. **Se for CSS:**
   - Adicionar em `css/header-styles.css`
   - Usar comentários para organizar por secção
   - Respeitar media queries existentes

2. **Se for JavaScript:**
   - Adicionar função em `js/header-functions.js`
   - Documentar com comentário explicativo
   - Auto-inicializar se necessário

3. **Depois de alterar:**
   - Testar em desktop (1920px, 1366px, 1024px)
   - Testar em tablet (768px, 812px)
   - Testar em mobile (375px, 414px)
   - Verificar em múltiplos navegadores

---

## Ficheiros Relacionados

- `includes/navbar.php` - Componente navbar
- `includes/footer.php` - Componente footer
- `css/style.css` - Estilos globais
- `js/main.js` - Scripts globais
- `apresentacao-historia.php` - Exemplo de implementação

---

## Suporte

Para dúvidas ou problemas:
1. Consultar este guia
2. Verificar `apresentacao-historia.php` como referência
3. Revisar os comentários no CSS/JS

---

**Versão:** 1.0
**Data:** Novembro 2024
**Autor:** Sistema OAGB
