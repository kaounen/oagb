# AI Development Guide: GLM 4.5 & Claude Integration

## Overview
This guide explains how to integrate GLM 4.5 (Zhipu AI) alongside Claude Code for OAGB website development and future projects.

## System Status

### Current Environment
- **Python**: 3.13.7 ✓ (Installed)
- **Node.js**: v22.18.0 ✓ (Installed)
- **GLM 4.5**: ⚠️ Not detected in PATH
- **ZhipuAI SDK**: ❌ Not installed

## Installation Guide

### Option 1: Python SDK (Recommended for Backend)

```bash
# Install ZhipuAI Python SDK
pip install zhipuai

# Verify installation
pip show zhipuai
```

### Option 2: Node.js SDK (Recommended for Frontend)

```bash
# Install globally
npm install -g @zhipuai/sdk

# Or install locally in project
cd c:\xampp\htdocs\oagb
npm init -y
npm install @zhipuai/sdk
```

### Option 3: REST API (Language Agnostic)

No installation needed - use direct HTTP requests with your API key.

## Configuration

### 1. Get API Key
1. Visit: https://open.bigmodel.cn/
2. Register/Login to ZhipuAI platform
3. Generate API key from dashboard
4. Store securely (never commit to git)

### 2. Create Configuration File

Create `ai_config.php` in the root directory:

```php
<?php
/**
 * AI Models Configuration
 * DO NOT commit this file to git - add to .gitignore
 */

return [
    'glm' => [
        'api_key' => 'your-glm-api-key-here',
        'base_url' => 'https://open.bigmodel.cn/api/paas/v4/',
        'model' => 'glm-4-plus', // or 'glm-4-air', 'glm-4-flash'
        'timeout' => 60
    ],
    'anthropic' => [
        'api_key' => 'your-claude-api-key-here',
        'base_url' => 'https://api.anthropic.com/v1/',
        'model' => 'claude-3-5-sonnet-20241022',
        'timeout' => 60
    ],
    'default_provider' => 'glm' // or 'anthropic'
];
```

### 3. Update .gitignore

Add to `.gitignore`:
```
ai_config.php
.env
node_modules/
```

## Usage Patterns

### Scenario 1: Code Generation (Use GLM 4.5)

**Use GLM for:**
- PHP code generation
- Database queries
- CRUD operations
- Form validation
- Local language support (Portuguese/Chinese)

**Python Example:**
```python
from zhipuai import ZhipuAI

client = ZhipuAI(api_key="your-api-key")
response = client.chat.completions.create(
    model="glm-4-plus",
    messages=[
        {"role": "user", "content": "Generate PHP code for lawyer registration form validation"}
    ],
)
print(response.choices[0].message.content)
```

**PHP Example (via REST API):**
```php
<?php
function call_glm($prompt) {
    $config = include 'ai_config.php';

    $data = [
        'model' => $config['glm']['model'],
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ]
    ];

    $ch = curl_init($config['glm']['base_url'] . 'chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['glm']['api_key'],
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Usage
$result = call_glm("Generate SQL query to find lawyers by region");
echo $result['choices'][0]['message']['content'];
```

### Scenario 2: Code Review (Use Claude)

**Use Claude for:**
- Code quality analysis
- Security audits
- Architecture decisions
- Documentation review
- Complex refactoring

**Via Claude Code:**
- Use the current VSCode extension you're using now
- Ask Claude to review specific files or functions
- Request security analysis of sensitive code

### Scenario 3: Content Generation (Both)

**Use GLM for:**
- Portuguese content (news articles, descriptions)
- Chinese/Portuguese translations
- Local context understanding

**Use Claude for:**
- English content
- Technical documentation
- API documentation
- Complex reasoning tasks

### Scenario 4: Database Operations (GLM Preferred)

**GLM is better for:**
- SQL query generation
- Database schema design
- Migration scripts
- Data validation logic

## Integration Example: AI-Powered Helper Class

Create `includes/ai_helper.php`:

```php
<?php
class AIHelper {
    private $config;
    private $provider;

    public function __construct($provider = null) {
        $this->config = include __DIR__ . '/../ai_config.php';
        $this->provider = $provider ?? $this->config['default_provider'];
    }

    /**
     * Generate code using AI
     */
    public function generateCode($prompt, $language = 'php') {
        $fullPrompt = "Generate {$language} code: {$prompt}";
        return $this->query($fullPrompt);
    }

    /**
     * Validate and improve SQL query
     */
    public function optimizeSQL($query) {
        $prompt = "Optimize this SQL query for MySQL and explain improvements:\n{$query}";
        return $this->query($prompt);
    }

    /**
     * Generate Portuguese content
     */
    public function generatePortugueseContent($topic, $type = 'article') {
        $prompt = "Escreva um {$type} em português sobre: {$topic}";
        return $this->query($prompt);
    }

    /**
     * Switch between providers
     */
    public function setProvider($provider) {
        if (!in_array($provider, ['glm', 'anthropic'])) {
            throw new Exception("Invalid provider: {$provider}");
        }
        $this->provider = $provider;
        return $this;
    }

    /**
     * Main query method
     */
    private function query($prompt) {
        if ($this->provider === 'glm') {
            return $this->queryGLM($prompt);
        } else {
            return $this->queryClaude($prompt);
        }
    }

    private function queryGLM($prompt) {
        $config = $this->config['glm'];

        $data = [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init($config['base_url'] . 'chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config['api_key'],
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("GLM API error: " . $response);
        }

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'];
    }

    private function queryClaude($prompt) {
        $config = $this->config['anthropic'];

        $data = [
            'model' => $config['model'],
            'max_tokens' => 4096,
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
            throw new Exception("Claude API error: " . $response);
        }

        $result = json_decode($response, true);
        return $result['content'][0]['text'];
    }
}

// Usage Examples:

// Use GLM for Portuguese content
$ai = new AIHelper('glm');
$article = $ai->generatePortugueseContent('Direitos dos Advogados na Guiné-Bissau');

// Use Claude for code review
$ai->setProvider('anthropic');
$review = $ai->generateCode('Review this function for security issues: ...');

// Use default provider
$ai = new AIHelper();
$sql = $ai->optimizeSQL('SELECT * FROM advogados WHERE status = 1');
```

## Development Workflow

### Daily Development

1. **Start with Claude Code (Current Setup)**
   - Open VSCode with Claude extension
   - Use for architecture decisions
   - Use for code review
   - Use for debugging complex issues

2. **Switch to GLM for Specific Tasks**
   - Open terminal/Python script
   - Use for code generation
   - Use for Portuguese content
   - Use for SQL optimization

3. **Hybrid Approach**
   - Ask Claude to review GLM-generated code
   - Use GLM for initial generation
   - Use Claude for refinement

### Example Workflow: Adding New Feature

```bash
# Step 1: Plan with Claude (VSCode)
# Ask: "Help me plan a feature for lawyer certification status tracking"

# Step 2: Generate code with GLM (Terminal)
python generate_code.py "Create PHP class for lawyer certification management"

# Step 3: Review with Claude (VSCode)
# Ask: "Review this generated code for security and best practices"

# Step 4: Refine with GLM if needed
# Use GLM for specific adjustments

# Step 5: Final review with Claude
# Comprehensive code review before commit
```

## Cost Comparison

### GLM 4.5 Pricing
- **glm-4-plus**: Higher quality, best for complex tasks
- **glm-4-air**: Balanced performance/cost
- **glm-4-flash**: Fast and cheap for simple tasks
- Generally more affordable than Claude for high-volume usage

### Claude Pricing
- **Claude Sonnet**: Best balance (current)
- **Claude Opus**: Highest quality, more expensive
- Better for complex reasoning and code review

### Cost Optimization Strategy
1. Use GLM for repetitive/simple tasks
2. Use Claude for critical decisions
3. Use GLM Flash for bulk operations
4. Use Claude for security reviews

## Quick Command Reference

### GLM Commands

```bash
# Install Python SDK
pip install zhipuai

# Install Node SDK
npm install @zhipuai/sdk

# Test connection
python test_glm_connection.py
```

### Switching Between AIs

```bash
# In VSCode - Use Claude (current setup)
# Just type in the chat as you're doing now

# In Terminal - Use GLM
python
>>> from zhipuai import ZhipuAI
>>> client = ZhipuAI(api_key="your-key")
>>> # Start coding with GLM
```

## Best Practices

### 1. Security
- ✅ Never commit API keys
- ✅ Use environment variables or config files
- ✅ Add config files to .gitignore
- ✅ Rotate keys regularly
- ✅ Use different keys for dev/prod

### 2. Provider Selection
- ✅ Use GLM for Chinese/Portuguese content
- ✅ Use Claude for English technical docs
- ✅ Use GLM for cost-effective bulk operations
- ✅ Use Claude for critical security reviews

### 3. Code Quality
- ✅ Always review AI-generated code
- ✅ Test thoroughly before deployment
- ✅ Use AI as assistant, not replacement
- ✅ Validate all database queries
- ✅ Sanitize all user inputs

### 4. Performance
- ✅ Cache AI responses when appropriate
- ✅ Use streaming for long responses
- ✅ Implement request timeouts
- ✅ Handle API failures gracefully

## Troubleshooting

### GLM Not Working
```bash
# Check installation
pip show zhipuai

# Reinstall if needed
pip uninstall zhipuai
pip install zhipuai

# Test API key
python test_glm_connection.py
```

### Claude Not Working
- Check VSCode extension is enabled
- Verify API key in extension settings
- Check internet connection
- Review extension logs

### Both Not Working
- Check internet connection
- Verify API keys are valid
- Check if APIs are experiencing downtime
- Review firewall/proxy settings

## Future Enhancements

### Planned Features
1. **AI Model Router**: Automatically select best AI for task
2. **Cost Tracking**: Monitor API usage and costs
3. **Response Caching**: Cache common queries
4. **Batch Processing**: Process multiple requests efficiently
5. **A/B Testing**: Compare output quality between models

### Integration Ideas
1. **Admin Panel AI Helper**: Add AI tools to CodeIgniter admin
2. **Content Generator**: Auto-generate lawyer bios, news
3. **Query Optimizer**: Analyze slow queries
4. **Form Generator**: Create forms from descriptions
5. **Translation Tool**: PT ↔ EN ↔ ZH translations

## Resources

### Official Documentation
- [GLM 4.5 Docs](https://open.bigmodel.cn/dev/api)
- [ZhipuAI Python SDK](https://github.com/zhipuai/zhipuai-sdk-python)
- [Claude API Docs](https://docs.anthropic.com/)
- [Claude Code Docs](https://docs.claude.com/en/docs/claude-code)

### Useful Links
- [GLM Playground](https://open.bigmodel.cn/console/playground)
- [Claude Workbench](https://console.anthropic.com/workbench)
- [API Status Pages](https://status.anthropic.com/)

## Quick Start Checklist

- [ ] Install Python SDK: `pip install zhipuai`
- [ ] Get GLM API key from https://open.bigmodel.cn/
- [ ] Create `ai_config.php` with both API keys
- [ ] Add `ai_config.php` to `.gitignore`
- [ ] Test GLM connection
- [ ] Create `includes/ai_helper.php`
- [ ] Test switching between providers
- [ ] Review security best practices
- [ ] Start using AI for development tasks!

## Support

For issues or questions:
1. Check this guide first
2. Review official documentation
3. Check API status pages
4. Search community forums
5. Contact API support if needed

---

**Last Updated**: 2025-10-12
**Version**: 1.0
**Maintained by**: OAGB Development Team
