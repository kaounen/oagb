# Correção do Navbar Mobile - Index.php

## Problema Identificado
O navbar mobile no `index.php` está aparecendo "achatado" e sobrepondo o botão menu, ao contrário da página `apresentacao-historia.php` que funciona corretamente.

## Diferença Entre as Versões

### apresentacao-historia.php (FUNCIONA BEM)
- Logo centralizado no topo
- Botão menu abaixo do logo com texto "MENU"
- Layout em coluna (flex-direction: column)
- Menu expansível com fundo escuro semi-transparente
- Z-index correto para evitar sobreposições

### index.php (PROBLEMÁTICO - ANTES DA CORREÇÃO)
- Faltava CSS específico para mobile
- Layout não estava em coluna
- Botão menu sem posicionamento correto
- Sem texto "MENU" ao lado do ícone

## Solução Aplicada

Adicionado CSS mobile específico no `index.php` (linhas ~330-435):

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

    /* Dropdowns funcionais */
    .d-block.d-lg-none .navbar-nav .dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .d-block.d-lg-none .navbar-nav .dropdown-menu .dropdown-item {
        color: #091E3E;
        padding: 0.8rem 1.5rem;
        font-weight: 500;
    }

    .d-block.d-lg-none .navbar-nav .dropdown-menu .dropdown-item:hover {
        background-color: var(--primary);
        color: white;
    }

    /* Esconder botão de pesquisa do navbar mobile */
    .d-block.d-lg-none .navbar .btn {
        display: none !important;
    }
}
```

## Resultado Esperado

### Mobile (< 992px):
1. ✅ Logo OAGB centralizado no topo
2. ✅ Botão com ícone + texto "MENU" centralizado abaixo do logo
3. ✅ Menu expansível com fundo escuro quando clicado
4. ✅ Dropdowns funcionam corretamente com fundo branco
5. ✅ Sem sobreposição de elementos
6. ✅ Botão de pesquisa não duplicado (já existe no header)

### Desktop (≥ 992px):
1. ✅ Navbar fixo com scroll effect
2. ✅ Topbar dourado quando scroll
3. ✅ Transições suaves
4. ✅ Dropdown hover automático

## Arquivos Modificados
- `index.php` - Adicionado CSS mobile específico

## Referência
- CSS baseado em `apresentacao-historia.php` (linhas 124-299)
- Testado e funcional na página de apresentação
