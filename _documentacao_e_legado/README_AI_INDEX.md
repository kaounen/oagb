# 🤖 Guia de IA para OAGB - Índice Completo

## 🎯 Por Onde Começar?

### Se você quer usar AGORA (5 minutos):
→ **[COMANDOS_AI.md](COMANDOS_AI.md)** - Lista de comandos prontos para copiar e usar

### Se você quer entender como funciona (10 minutos):
→ **[COMO_USAR_AI.md](COMO_USAR_AI.md)** - Guia completo de uso com exemplos

### Se você quer setup técnico (15 minutos):
→ **[ZAI_QUICK_START.md](ZAI_QUICK_START.md)** - Guia de configuração

### Se você quer documentação completa (30 minutos):
→ **[ZAI_GUIDE.md](ZAI_GUIDE.md)** - Documentação técnica completa

## 📋 Todos os Documentos

### Guias Rápidos (Comece Aqui!)
1. **[COMANDOS_AI.md](COMANDOS_AI.md)** ⭐ **MAIS IMPORTANTE**
   - Lista de comandos prontos
   - Exemplos para copiar/colar
   - Referência rápida
   - **USE ESTE PRIMEIRO!**

2. **[COMO_USAR_AI.md](COMO_USAR_AI.md)**
   - Como usar as 3 ferramentas
   - Workflows recomendados
   - Dicas e truques
   - Casos de uso específicos OAGB

3. **[ZAI_QUICK_START.md](ZAI_QUICK_START.md)**
   - Setup rápido (5 min)
   - Exemplos de uso
   - Primeiros passos

### Documentação Técnica
4. **[ZAI_GUIDE.md](ZAI_GUIDE.md)**
   - Documentação completa
   - Integração PHP
   - Métodos disponíveis
   - API reference

5. **[README_AI.md](README_AI.md)**
   - Visão geral das opções de IA
   - GLM vs Z.AI vs Claude
   - Quando usar cada uma

6. **[AI_DEVELOPMENT_GUIDE.md](AI_DEVELOPMENT_GUIDE.md)**
   - Guia completo GLM 4.5
   - Integração dual (GLM + Z.AI)
   - Workflows avançados

### Scripts e Ferramentas
7. **[ask_ai.py](ask_ai.py)** ⭐
   - Script Python para terminal
   - **A ferramenta que você mais vai usar!**
   - Uso: `python ask_ai.py --code "sua descrição"`

8. **[ask_ai.php](ask_ai.php)**
   - Versão PHP do script
   - Uso: `php ask_ai.php --code "sua descrição"`

9. **[test_zai_simple.py](test_zai_simple.py)**
   - Testar conexão Z.AI
   - Verificar se está funcionando

10. **[includes/zai_helper.php](includes/zai_helper.php)**
    - Classe PHP para usar no site
    - Métodos prontos para integração

### Exemplos Avançados
11. **[examples/zai_code_generator.py](examples/zai_code_generator.py)**
    - Gerador interativo de código
    - Menu com várias opções

## 🚀 Comandos Mais Importantes

### 1. Gerar Código (MAIS USADO)
```bash
python ask_ai.py --code "sua descrição aqui"
```

### 2. Gerar SQL
```bash
python ask_ai.py --sql "sua query aqui"
```

### 3. Conteúdo em Português
```bash
python ask_ai.py --pt "seu conteúdo aqui"
```

### 4. Pergunta Geral
```bash
python ask_ai.py "sua pergunta"
```

## 📊 Fluxo de Aprendizado Recomendado

```
DIA 1 (15 minutos)
├─ 1. Ler COMANDOS_AI.md
├─ 2. Testar: python ask_ai.py "Hello, você funciona?"
├─ 3. Testar: python ask_ai.py --code "validar email"
└─ 4. Testar: python ask_ai.py --pt "olá mundo"

DIA 2 (30 minutos)
├─ 1. Ler COMO_USAR_AI.md
├─ 2. Gerar código real para OAGB
├─ 3. Salvar resultado em arquivo
└─ 4. Testar código gerado

DIA 3 (1 hora)
├─ 1. Ler ZAI_QUICK_START.md
├─ 2. Integrar no site (includes/zai_helper.php)
├─ 3. Criar uma feature com IA
└─ 4. Revisar com Claude Code

SEMANA 2+
├─ 1. Explorar workflows avançados
├─ 2. Otimizar prompts
├─ 3. Automatizar tarefas
└─ 4. Compartilhar com equipe
```

## 🎯 Casos de Uso por Tarefa

### Desenvolvimento PHP
- **Documento**: [COMANDOS_AI.md](COMANDOS_AI.md)
- **Comando**: `python ask_ai.py --code "descrição"`
- **Exemplos**: Validações, funções, classes, APIs

### Banco de Dados
- **Documento**: [COMANDOS_AI.md](COMANDOS_AI.md)
- **Comando**: `python ask_ai.py --sql "descrição"`
- **Exemplos**: Queries, relatórios, otimizações

### Conteúdo do Site
- **Documento**: [COMANDOS_AI.md](COMANDOS_AI.md)
- **Comando**: `python ask_ai.py --pt "descrição"`
- **Exemplos**: Artigos, notícias, descrições

### Integração no Site
- **Documento**: [ZAI_GUIDE.md](ZAI_GUIDE.md)
- **Arquivo**: [includes/zai_helper.php](includes/zai_helper.php)
- **Uso**: Gerar conteúdo dinâmico

### Debugging
- **Documento**: [COMO_USAR_AI.md](COMO_USAR_AI.md)
- **Comando**: `python ask_ai.py "explique erro: ..."`
- **Uso**: Resolver problemas

## 💡 Exemplos Prontos para OAGB

### Validação de Registro de Advogado
```bash
python ask_ai.py --code "validar formulário de registro de advogado: nome completo, email, telefone +245, número OAB, especialização. Mensagens em português"
```

### Busca de Advogados
```bash
python ask_ai.py --sql "buscar advogados com filtros: região, especialização, status, ordenar por nome"
```

### Artigo para Site
```bash
python ask_ai.py --pt "artigo 300 palavras sobre ética profissional na advocacia, tom profissional"
```

### Função de Login
```bash
python ask_ai.py --code "login seguro com hash bcrypt, proteção SQL injection, sessão PHP, mensagens PT"
```

### Relatório Mensal
```bash
python ask_ai.py --sql "relatório de novos registros últimos 12 meses, agrupado por região"
```

## 🔧 Ferramentas Disponíveis

| Ferramenta | Arquivo | Uso | Melhor Para |
|------------|---------|-----|-------------|
| **Python CLI** | ask_ai.py | `python ask_ai.py --code "X"` | Geração rápida |
| **PHP CLI** | ask_ai.php | `php ask_ai.php --code "X"` | Quem prefere PHP |
| **PHP Class** | includes/zai_helper.php | `$ai->generateCode("X")` | Integração site |
| **Interactive** | examples/zai_code_generator.py | `python examples/...` | Aprender/explorar |
| **Claude Code** | (este chat) | Perguntar aqui | Revisão/discussão |

## 📚 Recursos Adicionais

### Arquivos de Configuração
- **ai_config.php** - Configuração (auto-gerado, não commitar)
- **.gitignore** - Proteção de segurança (auto-atualizado)
- **.env** - Variáveis de ambiente (opcional)

### Testes
- **test_zai_simple.py** - Testar conexão
- **test_zai_connection.py** - Teste completo
- **test_glm_connection.py** - GLM (se instalar)

### Contexto do Projeto
- **[CLAUDE.md](CLAUDE.md)** - Contexto do projeto OAGB
- **[README.md](README.md)** - README principal do projeto

## 🆘 Ajuda Rápida

### Problema: Não sei qual comando usar
→ **Solução**: Abra [COMANDOS_AI.md](COMANDOS_AI.md) e copie um exemplo

### Problema: Comando não funciona
→ **Solução**:
```bash
# Testar instalação
python test_zai_simple.py

# Instalar dependências
pip install requests
```

### Problema: Resultado em inglês
→ **Solução**: Use `--pt` ou adicione "em português" no prompt

### Problema: Quero integrar no site
→ **Solução**: Leia [ZAI_GUIDE.md](ZAI_GUIDE.md) seção "Integration"

### Problema: Não entendi nada
→ **Solução**: Comece por [COMANDOS_AI.md](COMANDOS_AI.md) e apenas copie/cole os exemplos

## ✅ Checklist Inicial

- [ ] Li [COMANDOS_AI.md](COMANDOS_AI.md)
- [ ] Testei: `python ask_ai.py "teste"`
- [ ] Testei: `python ask_ai.py --code "hello world"`
- [ ] Salvei resultado em arquivo
- [ ] Li [COMO_USAR_AI.md](COMO_USAR_AI.md)
- [ ] Gerei código real para OAGB
- [ ] Revisei com Claude Code (este chat)
- [ ] Integrei no projeto

## 🎓 Níveis de Conhecimento

### Iniciante (Você está aqui!)
- ✅ Use [COMANDOS_AI.md](COMANDOS_AI.md)
- ✅ Copie e cole exemplos
- ✅ Teste comandos básicos

### Intermediário (Meta: 1 semana)
- 📖 Leia [COMO_USAR_AI.md](COMO_USAR_AI.md)
- 💻 Crie prompts próprios
- 🔧 Integre no workflow

### Avançado (Meta: 1 mês)
- 📚 Leia [ZAI_GUIDE.md](ZAI_GUIDE.md)
- 🏗️ Integre no site OAGB
- ⚡ Automatize tarefas

### Expert (Meta: 3 meses)
- 🚀 Crie ferramentas próprias
- 📖 Contribua com docs
- 👥 Ensine a equipe

## 🎯 Próximos Passos

**AGORA (2 minutos):**
1. Abra [COMANDOS_AI.md](COMANDOS_AI.md)
2. Copie um comando
3. Execute no terminal
4. Veja a mágica acontecer! ✨

**HOJE (30 minutos):**
1. Leia [COMO_USAR_AI.md](COMO_USAR_AI.md)
2. Teste 5 comandos diferentes
3. Gere algo útil para OAGB

**ESTA SEMANA:**
1. Integre IA em uma feature
2. Automatize uma tarefa
3. Compartilhe com equipe

---

## 📞 Suporte

**Perguntas?**
- Pergunte aqui no Claude Code chat
- Revise os documentos listados acima
- Execute `python test_zai_simple.py` para testar

**Está funcionando?**
```bash
python ask_ai.py "Você está funcionando?"
```

Se sim, você está pronto! 🚀

---

**Criado para**: Ordem dos Advogados da Guiné-Bissau (OAGB)
**Última atualização**: 2025-10-12
**Versão**: 1.0
