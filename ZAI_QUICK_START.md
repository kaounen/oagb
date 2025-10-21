# Z.AI Quick Start Guide

## ✅ Your Setup is Complete!

Your Z.AI API key is **working** and ready to use!

**API Key**: `636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V`
**Model**: GLM-4.6 (via Claude-compatible API)
**Status**: ✅ Tested and working

## 🚀 Quick Usage

### 1. Python Example (Immediate Use)

```python
import requests

api_key = "636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V"
base_url = "https://api.z.ai/api/anthropic/v1/messages"

def ask_ai(question):
    headers = {
        "x-api-key": api_key,
        "anthropic-version": "2023-06-01",
        "content-type": "application/json"
    }

    data = {
        "model": "claude-3-5-sonnet-20241022",
        "max_tokens": 1024,
        "messages": [{"role": "user", "content": question}]
    }

    response = requests.post(base_url, headers=headers, json=data)
    return response.json()['content'][0]['text']

# Use it!
result = ask_ai("Generate PHP function to validate email")
print(result)
```

### 2. PHP Example (For Website)

```php
<?php
require_once 'includes/zai_helper.php';

$ai = new ZAI();

// Generate code
$code = $ai->generateCode("login form validation");
echo $code;

// Generate Portuguese content
$article = $ai->generatePortugueseContent("Direitos dos Advogados");
echo $article;

// Optimize SQL
$sql = $ai->optimizeSQL("SELECT * FROM advogados");
echo $sql;
?>
```

### 3. Interactive Tool (Best for Learning)

```bash
cd c:\xampp\htdocs\oagb
python examples\zai_code_generator.py
```

Then choose from the menu:
1. Generate PHP Function
2. Generate SQL Query
3. Generate Form Validation
4. Improve Existing Code
5. Generate Portuguese Content
6. Quick Test

## 📝 Common Tasks

### Generate Lawyer Registration Validation

**Python:**
```python
result = ask_ai("""
Generate PHP validation for lawyer registration form:
- Full Name (required)
- Email (required, valid)
- Phone (Guinea-Bissau format: +245)
- OAB Number (required, numeric)

Error messages in Portuguese.
""")
print(result)
```

**PHP:**
```php
$validation = $ai->generateValidation([
    'Nome completo (obrigatório)',
    'Email (obrigatório, formato válido)',
    'Telefone (formato Guiné-Bissau: +245)',
    'Número OAB (obrigatório, numérico)'
]);
echo $validation;
```

### Generate News Article

```php
$article = $ai->generateNewsArticle(
    "Nova Sede da Ordem dos Advogados",
    "Inauguração dia 15, localização central, capacidade 200 pessoas"
);
echo $article;
```

### Optimize Database Query

```php
$query = "SELECT * FROM advogados WHERE nome LIKE '%search%'";
$optimized = $ai->optimizeSQL($query);
echo $optimized;
```

## 🎯 Available Tools

### Files Created:
- ✅ `ai_config.php` - Configuration (auto-created)
- ✅ `.gitignore` - Security (auto-updated)
- ✅ `test_zai_simple.py` - Connection tester
- ✅ `includes/zai_helper.php` - PHP helper class
- ✅ `examples/zai_code_generator.py` - Interactive generator
- ✅ `ZAI_GUIDE.md` - Complete documentation

### Commands:
```bash
# Test connection
python test_zai_simple.py

# Interactive code generator
python examples\zai_code_generator.py
```

## 💡 Real-World Example

Let's add AI-powered lawyer bio generation to your website:

**1. Create endpoint** (`ajax/generate_bio.php`):
```php
<?php
require_once '../includes/zai_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $experience = $_POST['experience'] ?? 0;

    $ai = new ZAI();
    $bio = $ai->generateLawyerBio($name, $specialization, $experience);

    echo json_encode([
        'success' => true,
        'bio' => $bio
    ]);
}
?>
```

**2. Add to admin form** (JavaScript):
```javascript
document.getElementById('generate-bio').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('name', document.getElementById('lawyer-name').value);
    formData.append('specialization', document.getElementById('specialization').value);
    formData.append('experience', document.getElementById('experience').value);

    fetch('ajax/generate_bio.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('bio-field').value = data.bio;
    });
});
```

**3. Test it!**
- Fill in lawyer details
- Click "Generate Bio"
- AI creates professional bio in Portuguese

## 🔐 Security Notes

✅ **Done for you:**
- API key stored in `ai_config.php` (not in git)
- `.gitignore` updated automatically
- Environment variables set (Windows)

⚠️ **Remember:**
- Never commit `ai_config.php` to git
- Don't share your API key publicly
- Review AI-generated code before using

## 💰 Usage Tips

1. **Model Selection** (already optimal):
   - Currently using: GLM-4.6
   - Best balance of speed and quality

2. **Token Management**:
   ```php
   // Short responses
   $ai->query($prompt, 512);

   // Normal responses
   $ai->query($prompt, 1024);

   // Long content
   $ai->query($prompt, 2048);
   ```

3. **Caching**:
   ```php
   // Cache common queries
   $cache_key = 'ai_' . md5($prompt);
   if ($cached = apcu_fetch($cache_key)) {
       return $cached;
   }
   $result = $ai->query($prompt);
   apcu_store($cache_key, $result, 3600);
   ```

## 🎓 Next Steps

### Immediate (5 minutes):
1. ✅ Connection tested
2. ✅ Configuration saved
3. Try: `python examples\zai_code_generator.py`

### Short-term (1 hour):
1. Read [ZAI_GUIDE.md](ZAI_GUIDE.md)
2. Test PHP helper: `includes/zai_helper.php`
3. Generate some code for your project

### Long-term (this week):
1. Integrate into one OAGB feature
2. Test with real use cases
3. Refine prompts for best results
4. Add error handling

## 📚 Documentation

- **Complete Guide**: [ZAI_GUIDE.md](ZAI_GUIDE.md)
- **Project Context**: [CLAUDE.md](CLAUDE.md)
- **AI Overview**: [README_AI.md](README_AI.md)

## 🆘 Troubleshooting

### Connection fails?
```bash
# Re-test
python test_zai_simple.py
```

### PHP errors?
```php
// Check config exists
if (!file_exists('ai_config.php')) {
    echo "Run: python test_zai_simple.py";
}

// Check curl enabled
if (!function_exists('curl_init')) {
    echo "Enable curl in php.ini";
}
```

### Python errors?
```bash
# Install requests
pip install requests

# Update pip if needed
python -m pip install --upgrade pip
```

## ✨ You're Ready!

Your Z.AI integration is **complete** and **working**!

**Test it now:**
```bash
python examples\zai_code_generator.py
```

**Or try quick test:**
```python
import requests

api_key = "636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V"
url = "https://api.z.ai/api/anthropic/v1/messages"

response = requests.post(url, headers={
    "x-api-key": api_key,
    "anthropic-version": "2023-06-01",
    "content-type": "application/json"
}, json={
    "model": "claude-3-5-sonnet-20241022",
    "max_tokens": 100,
    "messages": [{"role": "user", "content": "Say hello in Portuguese"}]
})

print(response.json()['content'][0]['text'])
```

**Happy Coding! 🚀**
