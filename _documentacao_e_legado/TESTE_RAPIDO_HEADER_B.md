# 🚀 GUIA DE TESTE RÁPIDO - apresentacao-historia.php

## ✅ Implementação Completa

**Status**: ✅ PRONTO PARA TESTAR  
**Data**: 10 de Novembro de 2025  

---

## 📋 O que foi feito

### 1. Header Variant B Implementado
```
✅ Desktop: Navbar + Header com foto (400px) + Breadcrumbs + Quick Actions
✅ Mobile: Contact info + Navbar + Header (60vh)
✅ Breadcrumbs: Início > Ordem > Apresentação e História
✅ Quick Actions: 4 botões (Voltar, Imprimir, Partilhar, Traduzir)
```

### 2. Novo Botão: Ler em Voz Alta
```
✅ Localização: Topo direito da container, antes do conteúdo
✅ Funcionalidade: Lê TODO o conteúdo do container
✅ Idioma: Português (Portugal)
✅ Estados: "Ler" / "Parar" (toggle)
✅ Suporte: Chrome, Firefox, Safari, Edge
```

### 3. Função Traduzir Melhorada
```
✅ Error handling completo
✅ Melhor posicionamento (top 60px, right 20px)
✅ Idiomas: PT, EN, FR, ES
✅ Verificações adicionais de compatibilidade
```

---

## 🧪 Como Testar

### Abrir no Navegador
```
http://localhost/oagb/apresentacao-historia.php
```

---

## 📱 DESKTOP (992px+) - Checklist

### Header
- [ ] Topbar (endereço, telefone, social) - FIXA
- [ ] Navbar com logo - TRANSPARENTE
- [ ] Header com FOTO DE FUNDO (400px height)
- [ ] Breadcrumbs: **Início > Ordem > Apresentação e História**
- [ ] Texto título em branco, grande

### Quick Actions (4 botões)
- [ ] Botão ← (Voltar) - clica e volta página
- [ ] Botão 🖨️ (Imprimir) - clica e abre print dialog
- [ ] Botão 📤 (Partilhar) - clica e compartilha (ou copia link)
- [ ] Botão 🌐 (Traduzir) - clica e mostra widget de tradução

### Scroll Effects
- [ ] Faz scroll: Navbar fica BRANCO
- [ ] Faz scroll: Logo ENCOLHE
- [ ] Faz scroll: Textos ficam DOURADOS (#B1A276)
- [ ] Topbar fica branca tb
- [ ] Tudo com transição suave

### Conteúdo
- [ ] Botão "Ler em Voz Alta" visível (topo direito, antes do conteúdo)
- [ ] Botão: "🔊 Ler em Voz Alta"
- [ ] Seção "Introdução" com stats boxes
- [ ] Timeline histórico visível
- [ ] Seção "Valores" com 4 cards
- [ ] Botões CTA no final (Inscrever-se, Contactar)

---

## 📱 MOBILE (375px) - Checklist

### Header Mobile
- [ ] Contact info no topo (endereço, tel, email)
- [ ] Logo centralizado
- [ ] Botão MENU visível
- [ ] Header com foto de fundo (60vh)
- [ ] Breadcrumbs: **Início > Ordem > Apresentação e História**
- [ ] Título visível

### Mobile Menu
- [ ] Clique em MENU: abre/fecha menu
- [ ] Menu com opções principais
- [ ] Search button funciona
- [ ] Menu tem fundo escuro (overlay)

### Quick Actions Mobile
- [ ] 4 botões visíveis e funcionais
- [ ] Mesma funcionalidade que desktop

### Conteúdo Mobile
- [ ] Botão "Ler em Voz Alta" visível
- [ ] Conteúdo adaptado para mobile
- [ ] Sem overlaps ou erros visuais

---

## 🎤 Teste: Ler em Voz Alta

### Passo 1: Encontrar o Botão
```
Scroll para a seção "Conteúdo"
Procurar por botão no topo: "🔊 Ler em Voz Alta"
```

### Passo 2: Clique no Botão
```
Resultado esperado:
- Botão muda para "⏹️ Parar leitura"
- Começa a ler conteúdo COM VOZ
- Volume do sistema controla volume da leitura
```

### Passo 3: Verificar o Conteúdo Lido
```
✅ Deve ler:
  - Seção "Introdução"
  - Seção "Missão"
  - Seção "Atribuições"
  - Seção "Valores"
  - Lista de pontos
  
✅ NÃO deve ler:
  - Navbar/Menu
  - Rodapé
  - Stats boxes (fora do container principal)
```

### Passo 4: Parar a Leitura
```
Clique no botão novamente
Resultado esperado:
- Para de ler
- Botão volta para "🔊 Ler em Voz Alta"
```

### Passo 5: Testar Novamente
```
Clique novamente = volta a ler do início
(Cada clique = toggle pause/resume)
```

---

## 🌐 Teste: Traduzir

### Passo 1: Clique no Botão Traduzir
```
Botão 🌐 nos Quick Actions
```

### Passo 2: Widget Aparece
```
Resultado esperado:
- Widget Google Translate no topo direito
- Com selector de idioma
- Botões: English, Français, Español, Português
```

### Passo 3: Selecione um Idioma
```
Exemplo: English
Resultado esperado:
- Página INTEIRA traduzida para inglês
- Header, conteúdo, footer - TUDO traduzido
```

### Passo 4: Voltar para Português
```
Clique em "Português" no widget
Resultado esperado:
- Página volta para português original
```

### Passo 5: Fechar Widget
```
Clique novamente no botão 🌐
Resultado esperado:
- Widget desaparece
- Botão continua funcional se clicar novamente
```

---

## 📤 Teste: Partilhar

### Desktop
```
Clique em botão 📤
Resultado esperado:
- Se suportado: abre menu de partilha
- Fallback: copia link para clipboard + alert "Link copiado"
```

### Mobile
```
Clique em botão 📤
Resultado esperado:
- Abre Web Share menu (Gmail, WhatsApp, etc)
- Ou copia link para clipboard
```

---

## 🖨️ Teste: Imprimir

```
Clique em botão 🖨️
Resultado esperado:
- Abre print preview do navegador
- Layout otimizado para impressão
- Todas as seções visíveis
- Sem banners/ads desnecessários
```

---

## ← Teste: Voltar

```
Clique em botão ←
Resultado esperado:
- Volta para página anterior (history.back())
```

---

## 🐛 Troubleshooting

### ❌ Problema: Header não aparece

**Solução**:
1. Abrir console (F12)
2. Procurar por erro vermelho
3. Recarregar página (Ctrl+Shift+R hard refresh)
4. Se persisti, verificar se arquivo existe:
   ```
   includes/header-variant-b.php
   ```

### ❌ Problema: Breadcrumbs não aparecem

**Solução**:
- Verificar se `$breadcrumbs` array está correto em apresentacao-historia.php
- Recarregar página

### ❌ Problema: "Ler em Voz Alta" não funciona

**Solução**:
1. Verificar suporte do navegador:
   - Chrome ✅
   - Firefox ✅
   - Safari (iOS 14.5+) ✅
   - Edge ✅
   - IE ❌ (não suportado)

2. Se não funciona:
   - Tentar outro navegador
   - Verificar se tem som (volume ≠ 0)
   - Abrir console (F12) e procurar erros

### ❌ Problema: Traduzir não funciona

**Solução**:
1. Verificar conexão internet (Google Translate precisa online)
2. Permitir scripts de terceiros se tiver bloqueador
3. Verificar console (F12) para erros
4. Tentar limpar cache do navegador

### ❌ Problema: Scroll effects não funcionam

**Solução**:
- Scroll effects só em DESKTOP (≥992px)
- Se mobile, é esperado não funcionar
- Verificar se está em desktop com viewport ≥992px

---

## 📊 Resultados Esperados

| Item | Status | Notas |
|---|---|---|
| Header Variant B | ✅ | Desktop + Mobile |
| Breadcrumbs | ✅ | Dinâmicas |
| Quick Actions | ✅ | 4 funcionais |
| Ler em Voz Alta | ✅ | Novo - testar bem |
| Traduzir | ✅ | Melhorado |
| Scroll Effects | ✅ | Desktop only |
| Mobile Responsive | ✅ | 375px+ |
| Sem Erros Console | ✅ | Verificar F12 |

---

## 📸 Screenshots Para Comparar

### Desktop - Header Section
```
┌──────────────────────────────────────┐
│ [Logo]     [Menu Items]     [Search] │ ← Navbar
├──────────────────────────────────────┤
│                                      │
│   [Foto de Fundo]                   │
│   Apresentação e História            │ ← Header
│   Início > Ordem > Apresentação      │
│   [← 🖨️ 📤 🌐]                       │ ← Quick Actions
│                                      │
└──────────────────────────────────────┘
```

### Desktop - Conteúdo Section
```
┌──────────────────────────────────────┐
│ [🔊 Ler em Voz Alta]                 │ ← New Button
├──────────────────────────────────────┤
│ Introdução                           │
│ A nossa história... [Stats boxes]    │
│                                      │
│ Timeline Histórico                   │
│ 1974 - 1991 - 2005 - 2020           │
│                                      │
│ Os Nossos Pilares                   │
│ [Justiça] [Integridade]             │
│ [Excelência] [Cooperação]           │
└──────────────────────────────────────┘
```

---

## ✅ Checklist Final

Antes de considerar COMPLETO:

- [ ] Header aparece e é modular ✅
- [ ] Breadcrumbs funcionam ✅
- [ ] Quick Actions funcionam (4) ✅
- [ ] Botão "Ler em Voz Alta" funciona ✅
- [ ] Traduzir funciona ✅
- [ ] Desktop responsivo ✅
- [ ] Mobile responsivo ✅
- [ ] Scroll effects funcionam ✅
- [ ] Sem erros em console ✅
- [ ] Sem visual glitches ✅

---

## 🎯 Próximos Passos (Depois de Testado)

1. **Implementar em outras páginas:**
   - `agenda.php` → Variant C
   - `noticias.php` → Variant C
   - `comissoes-especializadas.php` → Variant B

2. **Adicionar botão em todas páginas?**
   - Adicionar "Ler em Voz Alta" globalmente?
   - Ou manter apenas nesta página?

3. **Melhorias futuras:**
   - [ ] Preferência de velocidade de leitura
   - [ ] Seleção de voz (homem/mulher)
   - [ ] Progresso visual enquanto lê
   - [ ] Armazenar preferências (localStorage)

---

## 📞 Se Encontrar Problemas

1. **Anota o erro exato** (copiar do console F12)
2. **Indicar browser e versão**
3. **Indicar o que clicou**
4. **Indicar o resultado esperado vs real**

Exemplo:
```
Browser: Chrome 119
Página: apresentacao-historia.php
Ação: Cliquei em "Ler em Voz Alta"
Erro: "Uncaught ReferenceError: readAloud is not defined"
Resultado: Botão não faz nada
```

---

## 🎉 Sucesso!

Todas as funcionalidades estão implementadas e prontas para testes!

**Boa sorte! 🚀**
