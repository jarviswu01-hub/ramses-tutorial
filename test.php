<?php
/**
 * OllamaCodeGenerator - Simple PHP class to generate code using Ollama (Continue)
 */
class OllamaCodeGenerator
{
    private string $model;
    private string $apiBase;
    private string $lastError;
    
    public function __construct(string $model = "qwen2.5-coder:7b", string $apiBase = "http://localhost:11434")
    {
        $this->model = $model;
        $this->apiBase = $apiBase;
        $this->lastError = "";
    }
    
    /**
     * Generate code based on prompt
     */
    public function generate(string $prompt): string
    {
        $data = [
            "model" => $this->model,
            "prompt" => $prompt,
            "stream" => false
        ];
        
        $ch = curl_init($this->apiBase . "/api/generate");
        if (!$ch) {
            $this->lastError = "Failed to initialize cURL";
            echo "Error: " . $this->lastError . "\n";
            return "";
        }
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            $this->lastError = curl_error($ch);
            echo "cURL Error: " . $this->lastError . "\n";
            curl_close($ch);
            return "";
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->lastError = "HTTP Error: " . $httpCode;
            echo "Error: " . $this->lastError . "\n";
            return "";
        }
        
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->lastError = "JSON Parse Error: " . json_last_error_msg();
            echo "Error: " . $this->lastError . "\n";
            return "";
        }
        
        if (empty($result['response'])) {
            $this->lastError = "Empty response from model";
            echo "Warning: " . $this->lastError . "\n";
        }
        
        return $result['response'] ?? '';
    }
    
    /**
     * Get model name
     */
    public function getModel(): string
    {
        return $this->model;
    }
    
    /**
     * Get last error message
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }
    
    /**
     * Check if there was an error
     */
    public function hasError(): bool
    {
        return !empty($this->lastError);
    }
}

// Usage example
echo "=== Ollama Code Generator Test ===\n\n";

$generator = new OllamaCodeGenerator();
echo "Model: " . $generator->getModel() . "\n\n";

$prompt = "Write a simple PHP function that calculates the Fibonacci sequence up to n numbers and returns them as an array.";

echo "Prompt: " . $prompt . "\n\n";
echo "Generating response...\n\n";

$response = $generator->generate($prompt);

if ($generator->hasError()) {
    echo "Error occurred: " . $generator->getLastError() . "\n";
} else {
    echo "Response:\n" . $response . "\n";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ollama Code Generator Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #569cd6; }
        .info { background: #264f78; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .response { background: #1e1e1e; border: 1px solid #3c3c3c; padding: 15px; border-radius: 5px; white-space: pre-wrap; }
        .error { background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Ollama Code Generator</h1>
        <div class="info">
            <strong>Model:</strong> <?= $generator->getModel() ?>
        </div>
        
        <?php if ($generator->hasError()): ?>
        <div class="error">
            Error: <?= htmlspecialchars($generator->getLastError()) ?>
        </div>
        <?php endif; ?>
        
        <h3>Prompt:</h3>
        <div class="info"><?= htmlspecialchars($prompt) ?></div>
        <h3>Response:</h3>
        <div class="response"><?= htmlspecialchars($response) ?></div>
    </div>
</body>
</html>
