# 📱 Testar e Debugar no Telemóvel

## 🎯 Opções para Testar Alterações

### **Opção 1: GitHub Pages (Melhor para HTML/CSS/JS)**

#### Configurar (uma vez):
1. No GitHub: `https://github.com/kaounen/oagb`
2. Settings → Pages
3. Source: Deploy from a branch
4. Branch: `002-padronizar-header-mobile`
5. Folder: `/ (root)`
6. Save

#### Visualizar:
- URL: `https://kaounen.github.io/oagb/`
- Atualiza automaticamente após push
- ⚠️ **Limitação**: Não executa PHP

---

### **Opção 2: InfinityFree (Hosting Grátis PHP)**

#### Configurar:
1. Acesse: https://infinityfree.net
2. Crie conta grátis
3. Criar novo site
4. Conectar GitHub via FTP ou Git Deploy

#### Vantagens:
- ✅ Suporta PHP e MySQL
- ✅ Grátis
- ✅ URL personalizado

---

### **Opção 3: Netlify (Mais Simples)**

#### Configurar:
1. Acesse: https://www.netlify.com
2. Login com GitHub
3. "New site from Git"
4. Escolher repo: `kaounen/oagb`
5. Branch: `002-padronizar-header-mobile`
6. Deploy

#### Visualizar:
- URL automático: `https://[random-name].netlify.app`
- Atualiza a cada push
- ⚠️ **Limitação**: Não executa PHP (só HTML/CSS/JS)

---

### **Opção 4: CodeSandbox (Desenvolvimento Direto)**

#### Usar:
1. Acesse: https://codesandbox.io
2. Import from GitHub
3. Repo: `kaounen/oagb`
4. Branch: `002-padronizar-header-mobile`

#### Vantagens:
- ✅ Editor completo no browser
- ✅ Preview instantâneo
- ✅ Funciona no mobile
- ⚠️ PHP limitado

---

## 🔍 Ver Alterações no GitHub (Mobile)

### **Ver Diff dos Ficheiros:**

#### Método 1: Via GitHub App
1. Instale: GitHub Mobile App
2. Abra repo: `kaounen/oagb`
3. Commits → Escolher commit
4. Ver ficheiros alterados

#### Método 2: Via Browser Mobile
```
https://github.com/kaounen/oagb/commit/916fb14
```
- Substitua `916fb14` pelo commit ID
- Verde = adicionado
- Vermelho = removido

### **Comparar Branches:**
```
https://github.com/kaounen/oagb/compare/master...002-padronizar-header-mobile
```

---

## 🧪 Testar PHP no Telemóvel

### **Solução 1: Termux (Android)**

#### Instalar:
1. Instale Termux (F-Droid ou Play Store)
2. No Termux:
```bash
pkg update
pkg install php
pkg install git
```

#### Clonar e Executar:
```bash
cd ~
git clone https://github.com/kaounen/oagb.git
cd oagb
git checkout 002-padronizar-header-mobile
php -S localhost:8000
```

#### Visualizar:
- Browser: `http://localhost:8000`

---

### **Solução 2: Server Online Temporário**

#### PHP Anywhere:
1. https://phptester.net
2. Cole código PHP
3. "Run" para testar
4. ⚠️ Só para snippets pequenos

#### OnlineGDB PHP:
1. https://www.onlinegdb.com/online_php_interpreter
2. Cole código
3. Execute e veja output

---

## 📊 Debugging no Claude Code

### **Ver Alterações Antes de Commit:**

No Claude Code (mobile):
```
git diff index.php
```

### **Ver Histórico de um Ficheiro:**
```
git log --oneline index.php
```

### **Ver Conteúdo de Commit Específico:**
```
git show 916fb14:index.php
```

### **Comparar Duas Versões:**
```
git diff master..002-padronizar-header-mobile index.php
```

---

## 🎨 Testar Visual/CSS

### **Método 1: Chrome DevTools (Android)**

1. Chrome → Site
2. Menu ⋮ → "Desktop site" (para ver desktop)
3. F12 ou Inspect Element (se disponível)
4. Testar responsivo

### **Método 2: Browser Stack (Grátis)**

1. https://www.browserstack.com/live
2. Teste grátis disponível
3. Testar em vários devices

---

## 🔧 Setup Recomendado para Mobile

### **Fluxo Ideal:**

```
1. Editar no Claude Code (mobile)
   ↓
2. Commit & Push
   ↓
3. Ver no GitHub (diffs)
   ↓
4. Testar em:
   - Netlify (preview automático)
   - Ou Termux (se precisa PHP)
```

---

## 📝 Comandos Úteis no Claude Code

### **Ver Status:**
```bash
git status
```

### **Ver Últimos Commits:**
```bash
git log --oneline -10
```

### **Ver Alterações Não Commitadas:**
```bash
git diff
```

### **Ver Ficheiros Modificados:**
```bash
git diff --name-only
```

### **Ver Alteração Específica:**
```bash
git diff HEAD~1 index.php
```

---

## 🌐 Testar Site Atual (XAMPP Online)

### **Se XAMPP está Online:**

Se seu servidor XAMPP está acessível pela internet:

1. Configurar no router:
   - Port forwarding: 80 → IP do PC
   
2. Obter IP público:
   - https://whatismyipaddress.com

3. Acessar de mobile:
   - `http://[seu-ip-publico]/oagb/`

⚠️ **Segurança**: Só para testes! Não deixar aberto permanentemente.

---

## 🎯 Recomendação Final

### **Para Desenvolvimento PHP Completo:**

**Setup Ideal:**
1. **Netlify** - Para preview rápido de HTML/CSS
2. **GitHub Mobile App** - Para ver diffs
3. **Claude Code Web** - Para editar
4. **InfinityFree** - Para testes PHP completos

### **Workflow:**
```
Mobile (Claude Code)
  ↓ Edit
  ↓ Commit
  ↓ Push
  ↓
GitHub (ver diffs)
  ↓
Netlify (preview automático)
  ↓
InfinityFree (teste PHP completo)
```

---

## 📱 Apps Recomendados (Android/iOS)

- **GitHub Mobile** - Ver commits e diffs
- **Working Copy** (iOS) - Git client completo
- **MGit** (Android) - Git client
- **Termux** (Android) - Terminal completo
- **Chrome DevTools** - Debugging

---

## 🆘 Debugging Específico OAGB

### **Testar Navbar:**
```html
<!-- Criar ficheiro test.html -->
<!DOCTYPE html>
<html>
<head>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar_include.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

Upload para Netlify e teste!

---

**Última Atualização**: 15/01/2025
**Para**: Desenvolvimento Mobile OAGB
