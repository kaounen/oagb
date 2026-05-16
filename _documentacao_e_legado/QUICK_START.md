# Quick Start: Using GLM 4.5 with Your OAGB Website

## 🚀 Fast Setup (5 Minutes)

### Step 1: Install GLM SDK

Open Command Prompt or PowerShell:

```bash
pip install zhipuai
```

Verify installation:
```bash
pip show zhipuai
```

### Step 2: Get Your API Key

1. Visit: **https://open.bigmodel.cn/**
2. Click "登录/注册" (Login/Register)
3. Complete registration
4. Go to "控制台" (Console) → "API密钥" (API Keys)
5. Click "创建新的APIKey" (Create New API Key)
6. Copy your key (format: `xxxxx.xxxxxxxxxxxxxxxxxxxxxxxx`)

### Step 3: Test Connection

```bash
cd c:\xampp\htdocs\oagb
python test_glm_connection.py
```

Follow the prompts to enter your API key. This will:
- Test your connection
- Create `ai_config.php` with your settings
- Update `.gitignore` to protect your key

### Step 4: Start Using GLM

You now have **two ways** to work with AI:

#### Option A: Continue with Claude (Current Setup)
- Keep using VSCode with Claude extension
- Great for: code reviews, architecture, complex reasoning

#### Option B: Use GLM (New Capability)
- Run: `python examples/glm_code_generator.py`
- Great for: code generation, SQL queries, Portuguese content

## 📝 Quick Examples

### Example 1: Generate PHP Code

```python
from zhipuai import ZhipuAI

client = ZhipuAI(api_key="your-api-key")
response = client.chat.completions.create(
    model="glm-4-plus",
    messages=[
        {"role": "user", "content": "Generate PHP function to validate email"}
    ],
)
print(response.choices[0].message.content)
```

### Example 2: Generate SQL Query

```python
response = client.chat.completions.create(
    model="glm-4-plus",
    messages=[
        {"role": "user", "content": "Create SQL to find all lawyers in Bissau region"}
    ],
)
print(response.choices[0].message.content)
```

### Example 3: Portuguese Content

```python
response = client.chat.completions.create(
    model="glm-4-plus",
    messages=[
        {"role": "user", "content": "Escreva um artigo sobre os direitos dos advogados"}
    ],
)
print(response.choices[0].message.content)
```

## 🎯 When to Use Which AI?

### Use GLM 4.5 For:
- ✅ **Code Generation**: Creating new PHP functions, classes
- ✅ **SQL Queries**: Generating and optimizing database queries
- ✅ **Portuguese Content**: Articles, descriptions, translations
- ✅ **Bulk Operations**: Processing many requests (cheaper)
- ✅ **Quick Tasks**: Fast responses for simple tasks

### Use Claude For:
- ✅ **Code Review**: Security analysis, best practices
- ✅ **Architecture**: System design, complex decisions
- ✅ **Debugging**: Finding and fixing complex bugs
- ✅ **Refactoring**: Large-scale code improvements
- ✅ **Documentation**: Writing technical docs

### Use Both (Recommended Workflow):
1. **Generate** code with GLM (fast, cheap)
2. **Review** with Claude (quality, security)
3. **Refine** with GLM if needed
4. **Deploy** with confidence

## 🛠️ Interactive Tools

### Tool 1: Code Generator
```bash
python examples/glm_code_generator.py
```
Interactive menu for:
- PHP functions
- SQL queries
- Form validation
- API endpoints
- Code optimization

### Tool 2: AI Comparison
```bash
python examples/ai_comparison.py
```
Compare GLM vs Claude responses side-by-side.

## 💡 Real-World Scenarios

### Scenario 1: Adding Lawyer Search Feature

**Step 1 - Generate with GLM:**
```bash
python
>>> from zhipuai import ZhipuAI
>>> client = ZhipuAI(api_key="your-key")
>>> response = client.chat.completions.create(
...     model="glm-4-plus",
...     messages=[{
...         "role": "user",
...         "content": "Generate PHP function to search lawyers by name and region"
...     }]
... )
>>> print(response.choices[0].message.content)
```

**Step 2 - Review with Claude:**
Open VSCode, ask Claude:
> "Review this code for security issues: [paste GLM's code]"

**Step 3 - Implement:**
Integrate the reviewed code into your website.

### Scenario 2: Database Query Optimization

**Ask GLM:**
```python
"Optimize this SQL query for performance:
SELECT * FROM advogados WHERE nome LIKE '%search%'"
```

**Ask Claude:**
> "Is this optimized query secure against SQL injection?"

### Scenario 3: Creating News Article

**Ask GLM (Portuguese):**
```python
"Escreva um artigo sobre a nova sede da Ordem dos Advogados da Guiné-Bissau"
```

**Ask Claude (Review):**
> "Review this Portuguese article for grammar and professionalism"

## 🔑 Managing API Keys

### Security Best Practices

1. **Never commit keys to git**
   - `ai_config.php` is in `.gitignore`
   - Check before each commit

2. **Use environment variables (optional)**
   ```bash
   # Windows
   setx GLM_API_KEY "your-key"

   # Linux/Mac
   export GLM_API_KEY="your-key"
   ```

3. **Rotate keys regularly**
   - Generate new keys every 3-6 months
   - Delete old keys from dashboard

4. **Different keys for dev/prod**
   - Development key: for testing
   - Production key: for live features

## 📊 Cost Management

### GLM Pricing (Approximate)
- **glm-4-flash**: Fastest, cheapest (simple tasks)
- **glm-4-air**: Balanced (most common use)
- **glm-4-plus**: Best quality (complex tasks)

### Tips to Save Money
1. Use appropriate model for task complexity
2. Cache responses for repeated queries
3. Use flash model for testing
4. Batch similar requests
5. Set token limits

### Example Cost Calculation
```python
# Track your usage
response = client.chat.completions.create(
    model="glm-4-plus",
    messages=[{"role": "user", "content": "..."}]
)
print(f"Tokens used: {response.usage.total_tokens}")
```

## 🐛 Troubleshooting

### Problem: "Module not found: zhipuai"
**Solution:**
```bash
pip install zhipuai
# If that fails:
python -m pip install zhipuai
```

### Problem: "API key invalid"
**Solutions:**
1. Check key format (should be: xxxxx.xxxxxxxxxxxx)
2. Verify key is active in dashboard
3. Check for extra spaces when copying
4. Generate new key if needed

### Problem: "Connection timeout"
**Solutions:**
1. Check internet connection
2. Try again (might be temporary)
3. Check GLM service status
4. Verify firewall settings

### Problem: "Rate limit exceeded"
**Solutions:**
1. Wait a few minutes
2. Upgrade your account tier
3. Implement request throttling
4. Use caching for repeated queries

## 📚 Next Steps

1. **Read Full Guide**: [AI_DEVELOPMENT_GUIDE.md](AI_DEVELOPMENT_GUIDE.md)
2. **Try Examples**: Run scripts in `examples/` folder
3. **Build Integration**: Add AI to your PHP code
4. **Experiment**: Try different prompts and models
5. **Compare**: Test GLM vs Claude for your use cases

## 🎓 Learning Resources

### Official Documentation
- GLM API Docs: https://open.bigmodel.cn/dev/api
- Python SDK: https://github.com/zhipuai/zhipuai-sdk-python
- API Playground: https://open.bigmodel.cn/console/playground

### Community
- GLM Forum: https://open.bigmodel.cn/forum
- GitHub Issues: Report bugs and ask questions
- Stack Overflow: Tag with `zhipuai` or `glm`

## ✅ Checklist

Complete setup checklist:

- [ ] Installed `zhipuai` package
- [ ] Created account at open.bigmodel.cn
- [ ] Generated API key
- [ ] Tested connection with `test_glm_connection.py`
- [ ] Created `ai_config.php`
- [ ] Added to `.gitignore`
- [ ] Tried example scripts
- [ ] Read AI_DEVELOPMENT_GUIDE.md
- [ ] Tested both GLM and Claude
- [ ] Ready to start developing!

## 🆘 Need Help?

1. **Check this guide first**
2. **Review AI_DEVELOPMENT_GUIDE.md**
3. **Test with example scripts**
4. **Check API status**: https://status.zhipuai.cn
5. **Ask Claude** (in VSCode): "Help me troubleshoot GLM setup"

---

**Happy Coding! 🚀**

Start simple, experiment, and gradually integrate AI into your workflow.
