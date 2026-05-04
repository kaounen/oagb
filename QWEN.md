# 🏛️ Ordem dos Advogados da Guiné-Bissau (OAGB) - Projeto Web

## 📋 Visão Geral do Projeto

Este é o site oficial da **Ordem dos Advogados da Guiné-Bissau (OAGB)**, uma plataforma web completa que serve como:

- **Portal Institucional**: Informação sobre a Ordem, história, órgãos diretivos e serviços
- **Sistema de Gestão (CMS)**: Notícias, comunicados, pareceres e deliberações
- **Portal de Membros**: Gestão de advogados inscritos, estagiários e comissões especializadas
- **Sistema de Formação**: Gestão de cursos, workshops e formação contínua
- **Portal de Transparência**: Acesso a editais, anúncios e documentos oficiais

### 🌐 URL Oficial
- **Produção**: https://oagb.gw
- **Local**: http://localhost/oagb (XAMPP)

---

## 🏗️ Arquitetura Técnica

### Stack Tecnológico

| Categoria | Tecnologia | Versão |
|-----------|-----------|--------|
| **Backend** | PHP | 7.4+ / 8.x |
| **Base de Dados** | MySQL / MariaDB | 5.7+ / 10.x |
| **Framework Admin** | CodeIgniter | 3.x |
| **Frontend CSS** | Bootstrap | 5.x |
| **JavaScript** | Vanilla JS + jQuery | 3.x |
| **Ícones** | Font Awesome + Bootstrap Icons | 5.x / 1.x |
| **Servidor Web** | Apache (XAMPP) | 2.4+ |

### Estrutura do Projeto

```
oagb/
├── includes/           # Componentes PHP reutilizáveis
│   ├── functions.php   # Funções auxiliares (format_date_pt, truncate_text, etc.)
│   ├── navbar.php      # Navegação principal
│   ├── topbar.php      # Barra de topo (contactos, login)
│   ├── footer.php      # Rodapé
│   └── meta_tags_include.php  # Meta tags SEO
├── gestao/             # Painel administrativo (CodeIgniter)
│   ├── application/    # Lógica da aplicação
│   ├── assets/         # Assets do admin
│   └── system/         # Core CodeIgniter
├── admin/              # Páginas administrativas
├── css/                # Folhas de estilo
│   ├── bootstrap.min.css
│   ├── style.css       # Estilos principais
│   ├── header-styles.css
│   ├── index-styles.css
│   └── footer-styles.css
├── js/                 # JavaScript
├── lib/                # Bibliotecas de terceiros
│   ├── animate/        # Animações
│   └── owlcarousel/    # Carousel
├── img/                # Imagens e media
├── uploads/            # Uploads de utilizadores
├── docs/               # Documentação
├── Base de Dados/      # Scripts SQL e documentação DB
└── *.php               # Páginas públicas
    ├── index.php
    ├── apresentacao-historia.php
    ├── advogados-inscritos.php
    ├── pesquisa-advogados.php
    ├── contacto.php
    └── ...
```

---

## 🚀 Configuração e Execução

### Pré-requisitos

- **XAMPP** ou stack LAMP/LEMP
- **PHP** 7.4+ (8.x recomendado)
- **MySQL** 5.7+ ou MariaDB 10.x
- **Node.js** (opcional, para ferramentas de desenvolvimento)

### Instalação Local

1. **Clonar/Copiar projeto**:
   ```bash
   # Copiar para htdoc do XAMPP
   c:\xampp\htdocs\oagb\
   ```

2. **Configurar Base de Dados**:
   ```sql
   -- Importar schema
   mysql -u root -p < "Base de Dados para Site da Ordem dos Advogados/oag_schema.sql"
   
   -- Importar dados de exemplo (opcional)
   mysql -u root -p < "Base de Dados para Site da Ordem dos Advogados/oag_data.sql"
   ```

3. **Configurar Conexão** (`connect.php`):
   ```php
   // Desenvolvimento
   $host = 'localhost';
   $dbname = 'korakund_ordem';
   $username = 'korakund_advogados';
   $password = 'GV@R4ra&rI{4';
   
   // OU local (XAMPP padrão)
   $host = 'localhost';
   $dbname = 'oagb_db';
   $username = 'root';
   $password = '';
   ```

4. **Aceder**:
   - **Frontend**: http://localhost/oagb/
   - **Admin**: http://localhost/oagb/gestao/

### Comandos de Desenvolvimento

```bash
# Iniciar Apache + MySQL (XAMPP)
# Via painel de controlo XAMPP

# Testar conexão PHP
php -r "include 'connect.php'; echo 'Conectado!';"

# Limpar cache
# Apagar ficheiros em tmp/ e logs/
```

---

## 📁 Páginas Principais

### Páginas Públicas

| Ficheiro | Descrição |
|----------|-----------|
| `index.php` | Página inicial com carousel, notícias e eventos |
| `apresentacao-historia.php` | História e apresentação da OAGB |
| `advogados-inscritos.php` | Lista de advogados inscritos |
| `pesquisa-advogados.php` | Pesquisa avançada de advogados |
| `bastonario-ordem.php` | Página do Bastonário |
| `comissoes-especializadas.php` | Comissões especializadas |
| `cooperacao-institucional.php` | Parcerias e cooperação |
| `contacto.php` | Formulário de contacto |
| `agenda.php` | Agenda de eventos |
| `noticias.php` | Lista de notícias |
| `estatutos.php` | Estatutos da Ordem |

### Componentes Partilhados

```php
// Incluir em todas as páginas
require_once 'connect.php';
require_once 'includes/functions.php';

// Header
include 'includes/topbar.php';
include 'includes/navbar.php';

// Footer
include 'includes/footer.php';
```

---

## 🗄️ Base de Dados

### Tabelas Principais

#### Módulo Institucional
- `instituicao_info` - Dados da instituição
- `bastonarios` - Histórico de bastonários
- `orgaos_diretivos` - Órgãos diretivos
- `membros_orgaos` - Membros dos órgãos

#### Módulo de Membros
- `advogados` - Registo de advogados (core)
- `estagiarios` - Estagiários
- `estagios` - Processos de estágio
- `avaliacoes_estagio` - Avaliações de estágio

#### Módulo de Comunicação
- `categorias_anuncios` - Categorias de notícias
- `anuncios` - Notícias e comunicados
- `pareceres_deliberacoes` - Pareceres e deliberações
- `comunicados` - Comunicados oficiais

#### Módulo de Comissões
- `comissoes` - Comissões especializadas
- `membros_comissoes` - Membros das comissões

#### Módulo de Formação
- `formacoes` - Cursos e formações

#### Administração
- `utilizadores` - Utilizadores do sistema
- `carousel_slides` - Slides do carousel
- `agenda` - Eventos

### Conexão PDO

```php
// Padrão usado em todo o projeto
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('Erro BD: ' . $e->getMessage());
    die('Erro interno do servidor.');
}
```

---

## 🎨 Frontend & Design

### Cores da Marca

```css
/* Cores Principais */
--cor-primaria: #1a237e;    /* Azul escuro institucional */
--cor-secundaria: #c5a059;  /* Dourado */
--cor-destaque: #b71c1c;    /* Vermelho */

/* Classes Bootstrap personalizadas */
.bg-color-1, .bg-color-3, .bg-color-4
```

### Tipografia

```html
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Uso -->
font-family: 'Libre Baskerville', serif;  /* Títulos */
font-family: 'Open Sans', sans-serif;     /* Corpo */
```

### Componentes UI

- **Carousel**: Bootstrap 5 com fallback para mobile
- **Navbar**: Responsiva com hamburger menu
- **Cards**: Cards de notícias, eventos e informações
- **Modais**: Pesquisa, tradução, login
- **Spinner**: Loading spinner global

---

## 🔧 Funções Auxiliares (`includes/functions.php`)

### Funções Disponíveis

```php
// Limpar input
clean_input($data)

// Truncar texto
truncate_text($text, $length = 100, $suffix = '...')

// Formatar data em português
format_date_pt($date, $include_time = false)

// Formatar datetime
format_datetime($datetime, $format = 'd/m/Y H:i')

// Gerar slug
generate_slug($text)

// Validar email
is_valid_email($email)

// Validar URL
is_valid_url($url)

// Obter IP
get_visitor_ip()

// Redirecionar
redirect($url, $permanent = false)

// Debug
dd($var)  // Die and dump
```

### Uso de Exemplo

```php
<?php
require_once 'includes/functions.php';

// Formatar data
$data = format_date_pt('2024-01-15', true);
// Output: "15 de Janeiro de 2024 às 14:30"

// Truncar texto
$resumo = truncate_text($noticia->conteudo, 150);

// Validar email
if (is_valid_email($_POST['email'])) {
    // Processar
}
?>
```

---

## 🤖 Integração com IA

### Ferramentas Disponíveis

O projeto inclui integração com múltiplos modelos de IA:

#### 1. GLM 4.5 (Zhipu AI)
- **Uso**: Geração de código PHP, SQL, conteúdo em português
- **Instalação**: `pip install zhipuai`
- **Configuração**: Ver `ai_config.php` (não commitar)

#### 2. Claude Code
- **Uso**: Code review, arquitetura, debugging
- **Acesso**: VSCode extension

#### 3. DeepSeek CLI
- **Uso**: Geração de código e consultas rápidas
- **Instalação**: `npm install -g run-deepseek-cli`
- **Comando**: `deepseek-cli chat`

### Scripts de IA

```bash
# Python AI Helper
python ask_ai.py "Como validar email em PHP?"
python ask_ai.py --code "função de login segura"
python ask_ai.py --sql "query para relatório mensal"
python ask_ai.py --pt "artigo sobre ética na advocacia"

# PHP AI Helper
php ask_ai.php "sua pergunta"
```

### Exemplo: AI Helper Class

```php
<?php
// includes/ai_helper.php (planeado)
class AIHelper {
    public function generateCode($prompt, $language = 'php') {
        // Integração com GLM/Claude
    }
    
    public function generatePortugueseContent($topic) {
        // Geração de conteúdo em PT
    }
}
?>
```

---

## 📝 Convenções de Desenvolvimento

### Estrutura de Páginas

```php
<?php
// 1. Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Incluir dependencies
require_once 'connect.php';
require_once 'includes/functions.php';

// 3. Lógica da página
$dados = [];
if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT * FROM tabela WHERE ativo = 1");
    $stmt->execute();
    $dados = $stmt->fetchAll();
}

// 4. Meta tags
$page_title = "Título da Página";
$meta_description = "Descrição para SEO";

// 5. Incluir template
include 'includes/topbar.php';
include 'includes/navbar.php';
?>

<!-- HTML Content -->

<?php include 'includes/footer.php'; ?>
```

### Nomenclatura

- **Ficheiros**: `kebab-case.php` (ex: `apresentacao-historia.php`)
- **Variáveis**: `$snake_case`
- **Funções**: `snake_case()`
- **Classes**: `PascalCase`
- **Tabelas DB**: `snake_case` (plural)

### Segurança

```php
// Sempre usar prepared statements
$stmt = $pdo->prepare("SELECT * FROM advogados WHERE id = ?");
$stmt->execute([$id]);

// Escapar output
echo htmlspecialchars($variavel, ENT_QUOTES, 'UTF-8');

// Validar input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// Headers de segurança (em connect.php)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
```

---

## 🧪 Testing & Debugging

### Debug Mode

```php
// Em desenvolvimento (connect.php)
$is_dev = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

if ($is_dev) {
    // Mostrar erros detalhados
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

### Log de Erros

```php
// Todos os erros são registados
error_log('Erro específico: ' . $mensagem);

// Ver logs em:
// logs/error_log
// logs/app.log
```

### Debug Helper

```php
// Função dd() em functions.php
dd($variavel);  // Die and dump
```

---

## 📊 Workflow de Desenvolvimento

### Adicionar Nova Página

1. Criar ficheiro `nome-pagina.php` na raiz
2. Incluir estrutura padrão (topbar, navbar, footer)
3. Adicionar lógica de base de dados
4. Estilizar com CSS (criar `nome-pagina-styles.css` se necessário)
5. Adicionar ao menu em `navbar.php`
6. Testar em desktop e mobile

### Adicionar Funcionalidade

1. Analisar impacto na base de dados
2. Criar/atualizar tabelas (schema migration)
3. Implementar backend (PHP)
4. Implementar frontend (HTML/CSS/JS)
5. Testar integração
6. Documentar em `docs/`

### Correção de Bugs

1. Identificar origem (log de erros)
2. Reproduzir localmente
3. Corrigir código
4. Testar regressão
5. Commit com mensagem descritiva

---

## 📚 Documentação

### Ficheiros de Documentação

| Ficheiro | Descrição |
|----------|-----------|
| `INDICE_DOCUMENTACAO_PROJETO.md` | Índice central de documentação |
| `AI_DEVELOPMENT_GUIDE.md` | Guia de integração com IA |
| `COMO_USAR_AI.md` | Como usar ferramentas de IA |
| `COMANDOS_AI.md` | Comandos rápidos de IA |
| `CSS_SHARED_REFERENCE.md` | Referência de estilos CSS |
| `MOBILE_GUIDE.md` | Guia de responsividade |
| `Base de Dados/Documentação Técnica_...md` | Documentação da base de dados |

### Documentação Gerada por Projeto

O projeto usa **BMad-METHOD** para documentação estruturada:

- `APRESENTACAO_HISTORIA_FIXES_FINAL.md` - Fixes técnicos detalhados
- `ANTES_E_DEPOIS_SUMMARY.md` - Comparação visual
- `VISUAL_DIAGRAM_FIXES.md` - Diagramas e fluxos
- `VALIDACAO_FINAL_APRESENTACAO.md` - Validação e checklist

---

## 🔐 Segurança

### Credenciais (NUNCA COMMITAR)

```php
// connect.php - Credenciais de produção
$password = 'GV@R4ra&rI{4';  // ⚠️ Não commitar!

// SMTP
define('SMTP_PASSWORD', 'sua_senha_email');  // ⚠️ Não commitar!
```

### .gitignore

O projeto já inclui proteção para:
- `.env`, `ai_config.php`
- `*_backup.php`, `*.bak`
- `logs/`, `cache/`
- Credenciais Google Cloud

### Boas Práticas

- ✅ Prepared statements (PDO)
- ✅ `htmlspecialchars()` para output
- ✅ Validação de input
- ✅ Headers de segurança
- ✅ Session management
- ⚠️ Hash de passwords (implementar bcrypt)

---

## 🚧 Roadmap & Melhorias Futuras

### Planeado

1. **Sistema de Autenticação Modernizado**
   - Hash bcrypt para passwords
   - 2FA para admin
   - Password recovery

2. **API REST**
   - endpoints para advogados
   - endpoints para notícias
   - Autenticação JWT

3. **Painel Administrativo Unificado**
   - Migrar para CodeIgniter 4
   - Dashboard moderno
   - Gestão de media library

4. **Acessibilidade**
   - Dark/Light mode
   - Ajuste de tamanho de fonte
   - Suporte para leitores de ecrã

5. **Multilinguismo**
   - PT/EN/FR
   - Sistema de tradução integrado

---

## 🆘 Resolução de Problemas

### Erro de Conexão à Base de Dados

```php
// Verificar em connect.php
// 1. Credenciais corretas?
// 2. Base de dados existe?
// 3. Utilizador tem permissões?
// 4. MySQL está a correr?

// Testar conexão
php -r "$pdo = new PDO('mysql:host=localhost;dbname=korakund_ordem', 'user', 'pass');"
```

### Páginas Não Carregam

```bash
# Verificar logs
tail -f logs/error_log

# Verificar permissões
chmod -R 755 c:\xampp\htdocs\oagb\

# Verificar Apache
# http://localhost/dashboard/
```

### Carousel Não Funciona

```javascript
// Verificar Bootstrap JS
<script src="lib/bootstrap/bootstrap.bundle.min.js"></script>

// Verificar jQuery
<script src="lib/jquery/jquery.min.js"></script>
```

---

## 📞 Contactos e Recursos

### Links Úteis

- **Site Oficial**: https://oagb.gw
- **Bootstrap 5**: https://getbootstrap.com/docs/5.0/
- **Font Awesome**: https://fontawesome.com/
- **CodeIgniter 3**: https://codeigniter.com/userguide3/

### Equipa de Desenvolvimento

- **Tech Lead**: [A definir]
- **Backend**: [A definir]
- **Frontend**: [A definir]

---

## ✅ Checklist de Onboarding

Para novos desenvolvedores:

- [ ] Instalar XAMPP e configurar
- [ ] Importar base de dados
- [ ] Configurar `connect.php` para local
- [ ] Testar site em http://localhost/oagb/
- [ ] Ler `AI_DEVELOPMENT_GUIDE.md`
- [ ] Configurar ferramentas de IA (opcional)
- [ ] Ler documentação da base de dados
- [ ] Testar painel admin (gestao/)

---

**Última Atualização**: 1 de abril de 2026  
**Versão**: 1.0  
**Mantido por**: OAGB Development Team
