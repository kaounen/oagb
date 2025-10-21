<?php
/**
 * Z.AI Helper Class for OAGB Website
 * Provides AI capabilities using Z.AI API
 *
 * Usage:
 *   require_once 'includes/zai_helper.php';
 *   $ai = new ZAI();
 *   $result = $ai->generateCode("create login function");
 */

class ZAI {
    private $api_key;
    private $base_url;
    private $model;
    private $timeout;

    /**
     * Constructor
     */
    public function __construct() {
        // Load configuration
        $config_file = __DIR__ . '/../ai_config.php';

        if (file_exists($config_file)) {
            $config = include $config_file;
            $this->api_key = $config['zai']['api_key'];
            $this->base_url = $config['zai']['base_url'];
            $this->model = $config['zai']['model'];
            $this->timeout = $config['zai']['timeout'];
        } else {
            // Fallback to hardcoded values
            $this->api_key = '636cfb102961401cb739a9cc09d328a9.P65ZiN2jAnbQ5X4V';
            $this->base_url = 'https://api.z.ai/api/anthropic/v1/';
            $this->model = 'claude-3-5-sonnet-20241022';
            $this->timeout = 60;
        }
    }

    /**
     * Main query method
     *
     * @param string $prompt The prompt to send
     * @param int $max_tokens Maximum tokens in response
     * @return string|null Response text or null on error
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
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Z.AI API error [{$httpCode}]: " . substr($response, 0, 500));
            return null;
        }

        $result = json_decode($response, true);

        if (isset($result['content'][0]['text'])) {
            return $result['content'][0]['text'];
        }

        error_log("Z.AI unexpected response format: " . substr($response, 0, 500));
        return null;
    }

    /**
     * Generate code
     *
     * @param string $description What the code should do
     * @param string $language Programming language (default: php)
     * @return string|null Generated code
     */
    public function generateCode($description, $language = 'php') {
        $prompt = "Generate {$language} code: {$description}\n\n";
        $prompt .= "Include error handling, validation, and comments.";

        return $this->query($prompt, 2048);
    }

    /**
     * Review and improve code
     *
     * @param string $code The code to review
     * @param string $language Programming language
     * @return string|null Review and suggestions
     */
    public function reviewCode($code, $language = 'php') {
        $prompt = "Review this {$language} code for security, performance, and best practices:\n\n";
        $prompt .= "```{$language}\n{$code}\n```\n\n";
        $prompt .= "Provide specific improvements.";

        return $this->query($prompt, 2048);
    }

    /**
     * Generate Portuguese content
     *
     * @param string $topic Content topic
     * @param string $type Content type (article, description, etc.)
     * @return string|null Generated content
     */
    public function generatePortugueseContent($topic, $type = 'article') {
        $prompt = "Escreva um {$type} profissional em português sobre: {$topic}\n\n";
        $prompt .= "Apropriado para o site da Ordem dos Advogados da Guiné-Bissau.\n";
        $prompt .= "Tom profissional, 200-400 palavras.";

        return $this->query($prompt, 2048);
    }

    /**
     * Improve existing Portuguese content
     *
     * @param string $content The content to improve
     * @return string|null Improved content
     */
    public function improvePortugueseContent($content) {
        $prompt = "Melhore este conteúdo em português:\n\n{$content}\n\n";
        $prompt .= "Corrija gramática, melhore clareza e profissionalismo.";

        return $this->query($prompt, 2048);
    }

    /**
     * Generate SQL query
     *
     * @param string $description What the query should do
     * @return string|null Generated SQL
     */
    public function generateSQL($description) {
        $prompt = "Generate optimized MySQL query for: {$description}\n\n";
        $prompt .= "Consider OAGB database tables: advogados, noticias, agenda.\n";
        $prompt .= "Include comments and index suggestions.";

        return $this->query($prompt, 1024);
    }

    /**
     * Optimize existing SQL query
     *
     * @param string $query The SQL query to optimize
     * @return string|null Optimization suggestions
     */
    public function optimizeSQL($query) {
        $prompt = "Optimize this MySQL query and explain improvements:\n\n{$query}";

        return $this->query($prompt, 1024);
    }

    /**
     * Generate form validation
     *
     * @param array $fields Array of field descriptions
     * @return string|null Validation code
     */
    public function generateValidation($fields) {
        $fields_str = implode("\n", $fields);

        $prompt = "Generate PHP server-side validation for these fields:\n\n{$fields_str}\n\n";
        $prompt .= "Error messages in Portuguese. Prevent XSS and SQL injection.";

        return $this->query($prompt, 2048);
    }

    /**
     * Generate lawyer bio
     *
     * @param string $name Lawyer name
     * @param string $specialization Specialization area
     * @param int $experience Years of experience
     * @return string|null Generated bio
     */
    public function generateLawyerBio($name, $specialization, $experience) {
        $prompt = "Escreva uma biografia profissional em português para um advogado:\n";
        $prompt .= "Nome: {$name}\n";
        $prompt .= "Especialização: {$specialization}\n";
        $prompt .= "Anos de experiência: {$experience}\n\n";
        $prompt .= "Máximo 150 palavras, tom profissional e respeitoso.";

        return $this->query($prompt, 512);
    }

    /**
     * Generate news article
     *
     * @param string $title Article title
     * @param string $key_points Main points to cover
     * @return string|null Generated article
     */
    public function generateNewsArticle($title, $key_points) {
        $prompt = "Escreva um artigo de notícias em português:\n";
        $prompt .= "Título: {$title}\n";
        $prompt .= "Pontos principais: {$key_points}\n\n";
        $prompt .= "300-400 palavras, estilo jornalístico profissional.";

        return $this->query($prompt, 2048);
    }

    /**
     * Translate text
     *
     * @param string $text Text to translate
     * @param string $from Source language
     * @param string $to Target language
     * @return string|null Translated text
     */
    public function translate($text, $from = 'pt', $to = 'en') {
        $languages = [
            'pt' => 'Portuguese',
            'en' => 'English',
            'fr' => 'French'
        ];

        $from_lang = $languages[$from] ?? $from;
        $to_lang = $languages[$to] ?? $to;

        $prompt = "Translate this text from {$from_lang} to {$to_lang}:\n\n{$text}";

        return $this->query($prompt, 1024);
    }

    /**
     * Answer legal question (for FAQ)
     *
     * @param string $question The question
     * @return string|null Answer
     */
    public function answerLegalQuestion($question) {
        $prompt = "Responda esta pergunta sobre direito na Guiné-Bissau:\n\n{$question}\n\n";
        $prompt .= "Resposta profissional, clara e precisa em português. 100-200 palavras.";

        return $this->query($prompt, 1024);
    }

    /**
     * Generate search suggestions
     *
     * @param string $query Search query
     * @return array Search suggestions
     */
    public function generateSearchSuggestions($query) {
        $prompt = "Dado o termo de busca '{$query}', sugira 5 termos relacionados ";
        $prompt .= "relevantes para um site de ordem de advogados. ";
        $prompt .= "Responda apenas com uma lista JSON: [\"termo1\", \"termo2\", ...]";

        $response = $this->query($prompt, 256);

        if ($response) {
            // Extract JSON from response
            if (preg_match('/\[.*?\]/s', $response, $matches)) {
                $suggestions = json_decode($matches[0], true);
                if (is_array($suggestions)) {
                    return $suggestions;
                }
            }
        }

        return [];
    }
}

// Example usage (commented out)
/*
require_once 'includes/zai_helper.php';

$ai = new ZAI();

// Generate code
$code = $ai->generateCode("function to validate Guinea-Bissau phone numbers");
echo $code;

// Generate Portuguese content
$article = $ai->generatePortugueseContent("Ética profissional na advocacia");
echo $article;

// Optimize SQL
$optimized = $ai->optimizeSQL("SELECT * FROM advogados WHERE nome LIKE '%search%'");
echo $optimized;
*/
