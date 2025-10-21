# Comandos IA - Referência Rápida

## 🚀 Forma 1: Python (Mais Rápida)

### Sintaxe Básica
```bash
python ask_ai.py "sua pergunta"
```

### Comandos Comuns

#### Gerar Código PHP
```bash
python ask_ai.py --code "sua descrição"
```

**Exemplos:**
```bash
python ask_ai.py --code "validar email"
python ask_ai.py --code "função de login com sessão"
python ask_ai.py --code "upload de imagem seguro"
python ask_ai.py --code "validar telefone +245"
```

#### Gerar SQL
```bash
python ask_ai.py --sql "sua descrição"
```

**Exemplos:**
```bash
python ask_ai.py --sql "buscar advogados ativos"
python ask_ai.py --sql "relatório mensal de registros"
python ask_ai.py --sql "encontrar advogados em Bissau"
```

#### Conteúdo em Português
```bash
python ask_ai.py --pt "sua descrição"
```

**Exemplos:**
```bash
python ask_ai.py --pt "artigo sobre ética profissional"
python ask_ai.py --pt "descrição dos serviços jurídicos"
python ask_ai.py --pt "notícia sobre novo evento"
```

#### Revisar Código
```bash
python ask_ai.py --review "seu código aqui"
```

## 🐘 Forma 2: PHP

### Mesma sintaxe, mas com PHP
```bash
php ask_ai.php "sua pergunta"
php ask_ai.php --code "descrição"
php ask_ai.php --sql "descrição"
php ask_ai.php --pt "descrição"
```

## 💬 Forma 3: Claude Code Chat (Aqui!)

Você pode simplesmente **perguntar aqui no chat**:

- "Gere uma função PHP para validar email"
- "Crie uma query SQL para buscar advogados"
- "Escreva um artigo em português sobre..."
- "Revise este código: ..."

## 📋 Exemplos Prontos para Copiar

### Para OAGB - Copie e Use!

#### 1. Validação de Formulário
```bash
python ask_ai.py --code "validar formulário de registro de advogado com campos: nome completo, email, telefone +245, número OAB. Mensagens de erro em português, proteção XSS e SQL injection"
```

#### 2. Busca de Advogados
```bash
python ask_ai.py --sql "buscar advogados com filtros: região, especialização, status ativo, ordenar por nome"
```

#### 3. Artigo para Site
```bash
python ask_ai.py --pt "escrever artigo de 300 palavras sobre a importância da ética profissional na advocacia na Guiné-Bissau, tom profissional"
```

#### 4. Função de Login
```bash
python ask_ai.py --code "função de login segura com verificação de senha hash, proteção contra brute force, criar sessão PHP, mensagens em português"
```

#### 5. Relatório SQL
```bash
python ask_ai.py --sql "relatório de novos advogados registrados por mês nos últimos 12 meses, agrupado por região e especialização"
```

#### 6. Upload de Foto
```bash
python ask_ai.py --code "função para upload seguro de foto de perfil de advogado, validar tipo JPG/PNG, tamanho máximo 2MB, redimensionar para 300x300"
```

#### 7. Validação de Telefone
```bash
python ask_ai.py --code "validar e formatar número de telefone da Guiné-Bissau formato +245 XXX XXX XXX"
```

#### 8. Notícia
```bash
python ask_ai.py --pt "notícia sobre inauguração da nova sede da Ordem dos Advogados, 200 palavras, estilo jornalístico"
```

#### 9. FAQ
```bash
python ask_ai.py --pt "responder a pergunta: Como posso me registrar como advogado na Guiné-Bissau? Resposta clara e profissional"
```

#### 10. Otimizar Query
```bash
python ask_ai.py --review "SELECT * FROM advogados WHERE nome LIKE '%busca%'"
```

## 💾 Salvando Resultados

### Salvar em Arquivo
```bash
# Salvar código gerado
python ask_ai.py --code "classe Database PDO" > includes/Database.php

# Salvar SQL
python ask_ai.py --sql "criar tabela certificações" > db/migration.sql

# Salvar conteúdo
python ask_ai.py --pt "sobre a ordem" > content/sobre.txt
```

### Ver e Salvar
```bash
# Ver resultado e depois salvar se gostar
python ask_ai.py --code "sua descrição"

# Se gostar, rodar novamente com > arquivo
python ask_ai.py --code "sua descrição" > arquivo.php
```

## ⚡ Comandos Mais Usados

```bash
# O comando que você mais vai usar:
python ask_ai.py --code "sua descrição aqui"

# Segundo mais usado:
python ask_ai.py --sql "sua query aqui"

# Terceiro mais usado:
python ask_ai.py --pt "seu conteúdo aqui"

# Para perguntas gerais:
python ask_ai.py "sua pergunta"
```

## 🎯 Dicas de Prompts

### ❌ Prompt Ruim
```bash
python ask_ai.py --code "função de login"
```

### ✅ Prompt Bom
```bash
python ask_ai.py --code "função de login segura com: verificação de email e senha, hash bcrypt, proteção SQL injection, criar sessão PHP, redirecionar para dashboard, mensagens de erro em português"
```

### ✅ Prompt Muito Bom
```bash
python ask_ai.py --code "função de login para site OAGB com:
- Verificar email e senha no banco MySQL (tabela advogados)
- Usar password_verify para senha hash bcrypt
- Proteção contra SQL injection com PDO
- Limite de 5 tentativas por IP em 15 minutos
- Criar sessão PHP com dados do usuário
- Registrar log de acesso
- Mensagens de erro claras em português
- Retornar true/false e mensagem"
```

## 🔧 Troubleshooting

### Erro: "No module named 'requests'"
```bash
pip install requests
```

### Resposta muito curta
```bash
# Adicione mais detalhes no prompt
python ask_ai.py --code "gerar função COMPLETA com documentação e exemplos de uso: validar email"
```

### Resposta em inglês
```bash
# Force português
python ask_ai.py --pt "sua pergunta"

# Ou adicione no final do prompt
python ask_ai.py --code "sua descrição. Comentários e mensagens em português"
```

### Timeout
```bash
# Aumente o timeout (padrão: 2048 tokens)
python ask_ai.py --tokens 4096 "pergunta complexa"
```

## 📊 Qual Comando Usar?

| Situação | Comando | Exemplo |
|----------|---------|---------|
| Gerar código PHP | `--code` | `python ask_ai.py --code "validar CPF"` |
| Gerar SQL | `--sql` | `python ask_ai.py --sql "buscar usuários"` |
| Conteúdo PT | `--pt` | `python ask_ai.py --pt "artigo sobre X"` |
| Revisar código | `--review` | `python ask_ai.py --review "código aqui"` |
| Pergunta geral | (sem flag) | `python ask_ai.py "como fazer X?"` |
| Explicar erro | (sem flag) | `python ask_ai.py "explique erro: X"` |

## ✨ Teste Agora!

```bash
# Teste simples
python ask_ai.py "Você está funcionando?"

# Teste geração de código
python ask_ai.py --code "função que retorna Hello World"

# Teste português
python ask_ai.py --pt "escrever uma frase de boas-vindas"
```

## 📚 Documentação Completa

- [COMO_USAR_AI.md](COMO_USAR_AI.md) - Guia completo de uso
- [ZAI_QUICK_START.md](ZAI_QUICK_START.md) - Início rápido
- [ZAI_GUIDE.md](ZAI_GUIDE.md) - Documentação técnica

---

**Dúvidas?** Pergunte aqui no Claude Code chat! 😊
