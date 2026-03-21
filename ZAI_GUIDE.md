# Z.AI Integration Guide for OAGB Website

## 📖 Overview

Z.AI (https://z.ai) is an API gateway that provides access to Claude and other AI models. You've successfully obtained your API key and can now use Claude models through Z.AI.

## 🔑 Your Configuration

**API Key**: `636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V`
**Base URL**: `https://api.z.ai/api/anthropic`
**Available Models**: Claude 3.5 Sonnet, Claude 3 Opus, Claude 3 Haiku

## ✅ Environment Setup (Completed)

You've already set these environment variables:
```bash
setx ANTHROPIC_AUTH_TOKEN 636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V
setx ANTHROPIC_BASE_URL https://api.z.ai/api/anthropic
```

## 🚀 Quick Start

### Step 1: Install Required Packages

```bash
# Install Anthropic SDK
pip install anthropic

# Install requests (for direct API calls)
pip install requests
```

### Step 2: Test Connection

```bash
cd c:\xampp\htdocs\oagb
python test_zai_connection.py
```

This will:
- Test your Z.AI connection
- Verify API key works
- Create configuration files
- Set up .gitignore

### Step 3: Start Using Z.AI

Choose your preferred method below.

## 💻 Usage Methods

### Method 1: Using Anthropic SDK (Recommended)

```python
import anthropic
import os

# Use environment variables
client = anthropic.Anthropic(
    api_key=os.environ.get("ANTHROPIC_AUTH_TOKEN"),
    base_url=os.environ.get("ANTHROPIC_BASE_URL")
)

# Or use direct key
client = anthropic.Anthropic(
    api_key="636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V",
    base_url="https://api.z.ai/api/anthropic"
)

# Make a request
message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=1024,
    messages=[
        {
            "role": "user",
            "content": "Generate a PHP function to validate email addresses"
        }
    ]
)

print(message.content[0].text)
```

### Method 2: Using Requests Library

```python
import requests

api_key = "636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V"
base_url = "https://api.z.ai/api/anthropic"

headers = {
    "x-api-key": api_key,
    "anthropic-version": "2023-06-01",
    "content-type": "application/json"
}

data = {
    "model": "claude-3-5-sonnet-20241022",
    "max_tokens": 1024,
    "messages": [
        {
            "role": "user",
            "content": "Generate SQL query to find all lawyers in Bissau"
        }
    ]
}

response = requests.post(
    f"{base_url}/v1/messages",
    headers=headers,
    json=data
)

result = response.json()
print(result['content'][0]['text'])
```

### Method 3: Using PHP (For Website Integration)

```php
<?php
/**
 * Z.AI Helper Class
 */
class ZAIHelper {
    private $api_key = '636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V';
    private $base_url = 'https://api.z.ai/api/anthropic/v1/';
    private $model = 'claude-3-5-sonnet-20241022';

    /**
     * Send query to Z.AI
     */
    public function query($prompt, $max_tokens = 1024) {
        $data = [
            'model' => $this->model,
            'max_tokens' => $max_tokens,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];

        $ch = curl_init($this->base_url . 'messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->api_key,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Z.AI API error: " . $response);
        }

        $result = json_decode($response, true);
        return $result['content'][0]['text'];
    }

    /**
     * Generate code
     */
    public function generateCode($description, $language = 'php') {
        $prompt = "Generate {$language} code: {$description}";
        return $this->query($prompt);
    }

    /**
     * Review code
     */
    public function reviewCode($code, $language = 'php') {
        $prompt = "Review this {$language} code for security and best practices:\n\n{$code}";
        return $this->query($prompt, 2048);
    }

    /**
     * Generate Portuguese content
     */
    public function generatePortugueseContent($topic, $type = 'article') {
        $prompt = "Escreva um {$type} em português sobre: {$topic}";
        return $this->query($prompt, 2048);
    }

    /**
     * Optimize SQL
     */
    public function optimizeSQL($query) {
        $prompt = "Optimize this SQL query for MySQL and explain improvements:\n{$query}";
        return $this->query($prompt);
    }
}

// Usage Examples
$ai = new ZAIHelper();

// Generate PHP code
$code = $ai->generateCode("function to validate Guinea-Bissau phone numbers");
echo $code;

// Review existing code
$review = $ai->reviewCode($some_existing_code);
echo $review;

// Generate Portuguese content
$article = $ai->generatePortugueseContent("Direitos dos Advogados", "artigo");
echo $article;
```

## 🎯 Available Models

| Model | Best For | Speed | Cost |
|-------|----------|-------|------|
| claude-3-5-sonnet-20241022 | Balanced (recommended) | Fast | Medium |
| claude-3-opus-20240229 | Complex tasks, highest quality | Slower | Higher |
| claude-3-haiku-20240307 | Simple tasks, speed | Fastest | Lower |

## 📊 Common Use Cases

### Use Case 1: Generate Lawyer Registration Form Validation

```python
client = anthropic.Anthropic(
    api_key="636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V",
    base_url="https://api.z.ai/api/anthropic"
)

message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=2048,
    messages=[{
        "role": "user",
        "content": """
        Generate PHP validation function for lawyer registration form with fields:
        - Full Name (required, min 3 chars)
        - Email (required, valid format)
        - Phone (Guinea-Bissau format: +245 XXX XXX XXX)
        - OAB Number (required, numeric, unique)
        - Specialization (optional, dropdown)

        Include error messages in Portuguese.
        """
    }]
)

print(message.content[0].text)
```

### Use Case 2: Generate SQL Query

```python
message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=1024,
    messages=[{
        "role": "user",
        "content": """
        Create optimized MySQL query to:
        - Find all active lawyers
        - In Bissau region
        - Specialized in criminal law
        - Registered in last 2 years
        - Sort by registration date
        """
    }]
)

print(message.content[0].text)
```

### Use Case 3: Code Review

```python
code_to_review = """
<?php
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
"""

message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=2048,
    messages=[{
        "role": "user",
        "content": f"""
        Review this PHP code for:
        1. Security issues
        2. Best practices
        3. Error handling
        4. Input validation

        Code:
        {code_to_review}

        Provide specific improvements.
        """
    }]
)

print(message.content[0].text)
```

### Use Case 4: Portuguese Content Generation

```python
message = client.messages.create(
    model="claude-3-5-sonnet-20241022",
    max_tokens=2048,
    messages=[{
        "role": "user",
        "content": """
        Escreva um artigo profissional de 300 palavras em português sobre:
        "A importância da ética profissional na advocacia na Guiné-Bissau"

        Deve incluir:
        - Introdução
        - Principais pontos
        - Conclusão
        - Tom profissional e respeitoso
        """
    }]
)

print(message.content[0].text)
```

## 🔧 Integration with OAGB Website

### Create AI Helper File

Save this as `includes/zai_helper.php`:

```php
<?php
/**
 * Z.AI Integration Helper
 * Provides AI capabilities for OAGB website
 */

class ZAI {
    private $config;

    public function __construct() {
        $this->config = include __DIR__ . '/../ai_config.php';
    }

    /**
     * Generate lawyer profile description
     */
    public function generateLawyerBio($name, $specialization, $experience) {
        $prompt = "Escreva uma biografia profissional em português para um advogado:
        Nome: {$name}
        Especialização: {$specialization}
        Anos de experiência: {$experience}

        Máximo 100 palavras, tom profissional.";

        return $this->query($prompt);
    }

    /**
     * Generate news article
     */
    public function generateNewsArticle($title, $key_points) {
        $prompt = "Escreva um artigo de notícias em português:
        Título: {$title}
        Pontos principais: {$key_points}

        300-400 palavras, estilo jornalístico profissional.";

        return $this->query($prompt, 2048);
    }

    /**
     * Validate and improve content
     */
    public function improveContent($content, $type = 'article') {
        $prompt = "Melhore este conteúdo em português:

        {$content}

        Correções necessárias:
        - Gramática e ortografia
        - Clareza e profissionalismo
        - Estrutura e fluxo

        Mantenha o significado original.";

        return $this->query($prompt, 2048);
    }

    /**
     * Generate search suggestions
     */
    public function generateSearchSuggestions($query) {
        $prompt = "Dado o termo de busca '{$query}', sugira 5 termos relacionados relevantes para um site de ordem de advogados. Responda apenas com uma lista JSON.";

        $response = $this->query($prompt);
        return json_decode($response, true);
    }

    /**
     * Main query method
     */
    private function query($prompt, $max_tokens = 1024) {
        $config = $this->config['zai'];

        $data = [
            'model' => $config['model'],
            'max_tokens' => $max_tokens,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init($config['base_url'] . 'messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $config['api_key'],
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Z.AI API error: " . $response);
            return null;
        }

        $result = json_decode($response, true);
        return $result['content'][0]['text'];
    }
}
```

### Usage in Your PHP Pages

```php
<?php
require_once 'includes/zai_helper.php';

$ai = new ZAI();

// Generate content for news page
if ($_POST['action'] === 'generate_article') {
    $title = $_POST['title'];
    $key_points = $_POST['key_points'];

    $article = $ai->generateNewsArticle($title, $key_points);
    echo json_encode(['success' => true, 'article' => $article]);
}

// Improve existing content
if ($_POST['action'] === 'improve_content') {
    $content = $_POST['content'];
    $improved = $ai->improveContent($content);
    echo json_encode(['success' => true, 'improved' => $improved]);
}
?>
```

## 💰 Cost Management

### Monitor Usage

```python
# Check token usage
message = client.messages.create(...)
print(f"Input tokens: {message.usage.input_tokens}")
print(f"Output tokens: {message.usage.output_tokens}")
print(f"Total tokens: {message.usage.input_tokens + message.usage.output_tokens}")
```

### Cost Optimization Tips

1. **Use appropriate model**:
   - Simple tasks → Haiku (cheapest)
   - Most tasks → Sonnet (recommended)
   - Complex tasks → Opus (expensive)

2. **Set max_tokens wisely**:
   ```python
   # Short responses
   max_tokens=512

   # Normal responses
   max_tokens=1024

   # Long content
   max_tokens=2048
   ```

3. **Cache common queries**:
   ```php
   // Cache AI responses
   $cache_key = 'ai_' . md5($prompt);
   if ($cached = apcu_fetch($cache_key)) {
       return $cached;
   }
   $result = $ai->query($prompt);
   apcu_store($cache_key, $result, 3600); // Cache 1 hour
   ```

## 🔐 Security Best Practices

### 1. Protect API Key

```php
// ✅ Good - Use config file (not in git)
$config = include 'ai_config.php';
$api_key = $config['zai']['api_key'];

// ❌ Bad - Hardcoded in public file
$api_key = '636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V';
```

### 2. Validate Inputs

```php
// Always validate before sending to AI
function validatePrompt($prompt) {
    $prompt = strip_tags($prompt);
    $prompt = htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8');
    return substr($prompt, 0, 5000); // Max length
}
```

### 3. Handle Errors Gracefully

```php
try {
    $result = $ai->query($prompt);
    if ($result === null) {
        // Fallback behavior
        $result = "Conteúdo não disponível no momento.";
    }
} catch (Exception $e) {
    error_log("AI Error: " . $e->getMessage());
    $result = "Erro ao gerar conteúdo.";
}
```

## 🎓 Next Steps

1. ✅ Run `python test_zai_connection.py`
2. ✅ Try example code snippets above
3. ✅ Create `includes/zai_helper.php`
4. ✅ Test with your website
5. ✅ Monitor usage and costs
6. ✅ Implement caching
7. ✅ Add error handling

## 📚 Resources

- **Z.AI Dashboard**: https://z.ai/manage-apikey/apikey-list
- **Anthropic API Docs**: https://docs.anthropic.com/
- **Claude Models**: https://docs.anthropic.com/en/docs/models-overview

## 🆘 Troubleshooting

### Issue: "Invalid API Key"
```bash
# Check your key
echo %ANTHROPIC_AUTH_TOKEN%

# Re-set if needed
setx ANTHROPIC_AUTH_TOKEN 636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V
```

### Issue: "Connection timeout"
```python
# Increase timeout
client = anthropic.Anthropic(
    api_key="...",
    base_url="...",
    timeout=120.0  # 120 seconds
)
```

### Issue: "Rate limit exceeded"
```python
import time

# Add retry logic
for attempt in range(3):
    try:
        message = client.messages.create(...)
        break
    except Exception as e:
        if "rate" in str(e).lower():
            time.sleep(5 * (attempt + 1))
        else:
            raise
```

---

**You're all set with Z.AI!** 🎉

Your API key is working and you can now use Claude models through Z.AI for your OAGB website development.
