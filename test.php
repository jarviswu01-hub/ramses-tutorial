<?php
/**
 * OllamaCodeGenerator - Simple PHP class to generate code using Ollama (Continue)
 */
class OllamaCodeGenerator
{
    private string $model;
    private string $apiBase;
    
    public function __construct(string $model = "qwen2.5-coder:7b", string $apiBase = "http://localhost:11434")
    {
        $this->model = $model;
        $this->apiBase = $apiBase;
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
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['response'] ?? 'No response';
    }
    
    /**
     * Get model name
     */
    public function getModel(): string
    {
        return $this->model;
    }
}

// Usage example
$generator = new OllamaCodeGenerator();
$prompt = "Write a simple PHP function that calculates the Fibonacci sequence up to n numbers and returns them as an array.";
$response = $generator->generate($prompt);
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Ollama Code Generator</h1>
        <div class="info">
            <strong>Model:</strong> <?= $generator->getModel() ?>
        </div>
        <h3>Prompt:</h3>
        <div class="info"><?= htmlspecialchars($prompt) ?></div>
        <h3>Response:</h3>
        <div class="response"><?= htmlspecialchars($response) ?></div>
    </div>
</body>
</html>
