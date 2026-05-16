# Como Usar IA no Terminal do Claude Code

## ✅ Você Tem 3 Ferramentas Prontas!

### 1. Python: `ask_ai.py` (Recomendado)

#### Uso Básico
```bash
# Pergunta simples
python ask_ai.py "Como validar email em PHP?"

# Gerar código PHP
python ask_ai.py --code "função para validar CPF"

# Gerar SQL
python ask_ai.py --sql "encontrar todos os advogados ativos em Bissau"

# Conteúdo em Português
python ask_ai.py --pt "artigo sobre ética na advocacia"

# Revisar código
python ask_ai.py --review "<?php function login() { ... }"
```

#### Exemplos Reais

**Exemplo 1: Gerar Função PHP**
```bash
python ask_ai.py --code "validar número de telefone da Guiné-Bissau formato +245"
```

**Exemplo 2: Query SQL**
```bash
python ask_ai.py --sql "buscar advogados por região e especialização com contagem de casos"
```

**Exemplo 3: Artigo em Português**
```bash
python ask_ai.py --pt "escrever artigo de 300 palavras sobre direitos dos advogados na Guiné-Bissau"
```

**Exemplo 4: Pergunta Geral**
```bash
python ask_ai.py "Qual a melhor forma de proteger contra SQL injection em PHP?"
```

### 2. PHP: `ask_ai.php`

```bash
# Mesma sintaxe, mas com PHP
php ask_ai.php "sua pergunta"
php ask_ai.php --code "gerar função de login"
php ask_ai.php --sql "query para relatório mensal"
php ask_ai.php --pt "descrição de serviços jurídicos"
```

### 3. Claude Code (Você já está usando!)

Você pode simplesmente **perguntar aqui no chat**:
- "Gere uma função PHP para..."
- "Revise este código..."
- "Explique como..."

## 🚀 Workflows Recomendados

### Workflow 1: Desenvolvimento Rápido

```bash
# 1. Gerar código com AI
python ask_ai.py --code "validação de formulário de registro de advogado"

# 2. Salvar em arquivo
python ask_ai.py --code "validação de formulário" > includes/validation.php

# 3. Revisar aqui no Claude Code
# (Cole o código e peça revisão no chat)
```

### Workflow 2: Criação de Conteúdo

```bash
# 1. Gerar artigo
python ask_ai.py --pt "artigo sobre nova sede da OAB" > temp_article.txt

# 2. Revisar e melhorar
python ask_ai.py "melhore este artigo: $(cat temp_article.txt)"

# 3. Usar no site
```

### Workflow 3: Otimização de SQL

```bash
# 1. Pegar query existente
python ask_ai.py --sql "otimizar: SELECT * FROM advogados WHERE nome LIKE '%busca%'"

# 2. Testar no banco
# 3. Implementar
```

## 📋 Comandos Rápidos

### Para o Dia a Dia

```bash
# Gerar validação de formulário
python ask_ai.py --code "validar campos: nome, email, telefone, OAB"

# Criar função de busca
python ask_ai.py --code "buscar advogados por múltiplos filtros"

# Gerar conteúdo
python ask_ai.py --pt "descrição dos serviços da ordem dos advogados"

# Query complexa
python ask_ai.py --sql "relatório de advogados registrados por mês e região"

# Tradução
python ask_ai.py "traduzir para inglês: Ordem dos Advogados da Guiné-Bissau"
```

### Para Debugging

```bash
# Explicar erro
python ask_ai.py "explique este erro PHP: Call to undefined function mysql_connect()"

# Corrigir código
python ask_ai.py --review "<?php $sql = 'SELECT * FROM users WHERE id=' . $_GET['id']; ?>"

# Melhorar performance
python ask_ai.py "como otimizar esta query: SELECT * FROM advogados"
```

## 💡 Dicas Pro

### Dica 1: Seja Específico
```bash
# ❌ Ruim
python ask_ai.py "função de login"

# ✅ Bom
python ask_ai.py --code "função de login segura com hash de senha, proteção contra SQL injection, e sessão. Mensagens em português."
```

### Dica 2: Use Contexto
```bash
# ✅ Muito Bom
python ask_ai.py --code "função para validar formulário de registro de advogado no site OAGB. Campos: nome completo, email, telefone (+245), número OAB. Retornar array de erros em português."
```

### Dica 3: Salve Resultados
```bash
# Salvar código gerado
python ask_ai.py --code "classe de conexão PDO" > includes/Database.php

# Salvar documentação
python ask_ai.py "documentar API REST do OAGB" > docs/api.md

# Salvar conteúdo
python ask_ai.py --pt "sobre nós" > content/sobre.txt
```

### Dica 4: Combine com Git
```bash
# Gerar código
python ask_ai.py --code "migration para adicionar campo certificação" > db/migrations/add_certification.sql

# Revisar
git diff

# Commit
git add .
git commit -m "Add certification migration"
```

## 🎯 Casos de Uso Específicos para OAGB

### 1. Validação de Formulários
```bash
python ask_ai.py --code "validar formulário de solicitação de advogado com campos: nome, email, telefone, especialização, mensagem. Validação em português, proteção XSS"
```

### 2. Queries de Relatório
```bash
python ask_ai.py --sql "relatório mensal de novos advogados registrados, agrupado por região e especialização, últimos 12 meses"
```

### 3. Conteúdo do Site
```bash
# Notícia
python ask_ai.py --pt "notícia sobre evento de capacitação para advogados, 200 palavras, tom jornalístico"

# Descrição de serviço
python ask_ai.py --pt "descrever serviço de consulta jurídica online para o site"

# FAQ
python ask_ai.py --pt "responder: Como me tornar advogado na Guiné-Bissau?"
```

### 4. Funções Utilitárias
```bash
# Formatação de telefone
python ask_ai.py --code "função para formatar telefone +245XXXXXXXXX em formato legível"

# Geração de slug
python ask_ai.py --code "função para gerar slug URL-friendly de títulos em português"

# Upload de imagem
python ask_ai.py --code "função segura de upload de foto de perfil, validar tipo, tamanho, redimensionar"
```

## 🔧 Solução de Problemas

### Erro: "requests not found"
```bash
pip install requests
```

### Erro: "curl not enabled" (PHP)
- Editar `php.ini`
- Descomentar: `extension=curl`
- Reiniciar Apache

### Timeout
```bash
# Aumentar timeout
python ask_ai.py --tokens 4096 "pergunta complexa"
```

### Resposta em inglês (quero português)
```bash
# Force português
python ask_ai.py --pt "sua pergunta em português"

# Ou adicione no prompt
python ask_ai.py "responda em português: sua pergunta"
```

## 📊 Comparação das Ferramentas

| Ferramenta | Quando Usar | Vantagem |
|------------|-------------|----------|
| `ask_ai.py` | Gerar código rápido | Rápido, salva arquivos fácil |
| `ask_ai.php` | Se preferir PHP | Integra com scripts PHP |
| Claude Code Chat | Discussões, revisões | Contexto completo do projeto |

## ⚡ Atalhos Úteis

Crie aliases no seu terminal (opcional):

```bash
# Windows (PowerShell)
function ai { python c:\xampp\htdocs\oagb\ask_ai.py $args }

# Uso
ai "sua pergunta"
ai --code "gerar função"
```

## 🎓 Exemplos Completos

### Exemplo Completo 1: Nova Feature

```bash
# 1. Planejar
python ask_ai.py "como implementar sistema de certificação para advogados no site OAGB?"

# 2. Gerar migration
python ask_ai.py --sql "criar tabela certificacoes com campos: id, advogado_id, tipo, data_emissao, data_expiracao, status" > db/cert.sql

# 3. Gerar model
python ask_ai.py --code "classe PHP Certificacao com métodos CRUD usando PDO" > includes/Certificacao.php

# 4. Gerar validação
python ask_ai.py --code "validar formulário de certificação" > includes/validate_cert.php

# 5. Revisar tudo aqui no Claude Code
```

### Exemplo Completo 2: Conteúdo

```bash
# 1. Gerar vários artigos
for topic in "ética profissional" "direitos dos advogados" "como se registrar"
do
    python ask_ai.py --pt "artigo sobre $topic" > "content/${topic// /_}.txt"
done

# 2. Revisar
ls content/

# 3. Publicar
```

## 🎉 Você está Pronto!

**Comando mais usado:**
```bash
python ask_ai.py --code "sua descrição aqui"
```

**Para testar agora:**
```bash
python ask_ai.py "Olá, você está funcionando?"
```

---

**Documentação Completa:**
- [ZAI_QUICK_START.md](ZAI_QUICK_START.md) - Guia rápido
- [ZAI_GUIDE.md](ZAI_GUIDE.md) - Guia completo
- [includes/zai_helper.php](includes/zai_helper.php) - Classe PHP

**Suporte:** Pergunte aqui no Claude Code chat! 😊
