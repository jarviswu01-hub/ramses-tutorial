<?php
error_reporting(0);
ini_set('display_errors', 0);
// Increase limits just in case
ini_set('memory_limit', '256M');

// ────────────────────────────────────────────────
// Text → Unicode Braille Dots Art
// ────────────────────────────────────────────────
function textToBrailleDots(string $text, int $fontSize = 12): string {
    if (empty(trim($text))) {
        return "";
    }

    // Path to a font that supports Chinese (Arial Unicode on macOS)
    $fontPath = '/System/Library/Fonts/Supplemental/Arial Unicode.ttf';
    if (!file_exists($fontPath)) {
        $fontPath = '/System/Library/Fonts/Arial Unicode.ttf';
    }
    if (!file_exists($fontPath)) {
        // Fallback
        $fontPath = '/System/Library/Fonts/Helvetica.ttc';
    }

    $angle = 0;

    // Process text: Wrap every 4 characters
    $originalLines = explode("\n", $text);
    $lines = [];

    foreach ($originalLines as $originalLine) {
        // clean up
        $originalLine = str_replace(["\r", "\t"], ["", " "], $originalLine);
        if ($originalLine === "") {
            $lines[] = "";
            $lines[] = ""; // Add extra spacing for empty lines too
        } else {
            // Split into chunks of 4 characters (multibyte safe)
            $length = mb_strlen($originalLine);
            for ($i = 0; $i < $length; $i += 4) {
                $lines[] = mb_substr($originalLine, $i, 4);
                $lines[] = ""; // Add empty line after every chunk
            }
        }
    }

    // Calculate dimensions
    $maxWidth = 0;
    $lineHeight = $fontSize * 1.1;

    foreach ($lines as $line) {
        $line = str_replace("\t", " ", $line);
        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $line);
        if ($bbox) {
            $width = abs($bbox[2] - $bbox[0]);
            $maxWidth = max($maxWidth, $width);
        }
    }

    $totalHeight = count($lines) * $lineHeight;
    if ($maxWidth === 0) $maxWidth = 100;

    // Add padding
    $padding = 2;
    $width = $maxWidth + ($padding * 2);
    $height = $totalHeight + ($padding * 2);

    // Create canvas
    $img = imagecreatetruecolor((int)$width, (int)$height);
    $black = imagecolorallocate($img, 0, 0, 0);
    $white = imagecolorallocate($img, 255, 255, 255);

    // Fill background black
    imagefill($img, 0, 0, $black);

    // Write text in white
    $y = $padding + $fontSize;
    foreach ($lines as $line) {
        $line = str_replace("\t", " ", $line);
        imagettftext($img, $fontSize, $angle, $padding, $y, $white, $fontPath, $line);
        $y += $lineHeight;
    }

    $output = "";

    // Convert to Braille (2x4 blocks)
    for ($y = 0; $y < $height; $y += 4) {
        $rowString = "";
        for ($x = 0; $x < $width; $x += 2) {
            $code = 0;

            // Braille Dot Mapping
            // 1 (0,0) 4 (1,0)
            // 2 (0,1) 5 (1,1)
            // 3 (0,2) 6 (1,2)
            // 7 (0,3) 8 (1,3)
            $dots = [
                0 => [$x, $y],
                1 => [$x, $y+1],
                2 => [$x, $y+2],
                3 => [$x+1, $y],
                4 => [$x+1, $y+1],
                5 => [$x+1, $y+2],
                6 => [$x, $y+3],
                7 => [$x+1, $y+3]
            ];

            foreach ($dots as $bit => $pos) {
                $px = $pos[0];
                $py = $pos[1];
                if ($px < $width && $py < $height) {
                    $rgb = imagecolorat($img, $px, $py);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $brightness = ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;
                    if ($brightness > 0.5) {
                        $code |= (1 << $bit);
                    }
                }
            }

            // Braille characters start at U+2800
            $output .= mb_chr(0x2800 + $code, 'UTF-8');
        }
        $output .= "\n";
    }

    imagedestroy($img);
    return rtrim($output, "\n");
}

// Handle form submission
$resultDots = "";
$defaultText = "Hello\nWorld\n你好";
$selectedSize = 12;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';
    $selectedSize = isset($_POST['size']) ? (int)$_POST['size'] : 12;
    if (!empty(trim($text))) {
        $resultDots = textToBrailleDots($text, $selectedSize);
        $defaultText = $text;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to Braille Dots</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            resize: vertical;
            margin-bottom: 15px;
        }
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 14px;
            flex: 1;
        }
        .submit-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .result-container {
            background: #0f0f1a;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(102, 126, 234, 0.2);
            position: relative;
            margin-top: 20px;
        }
        .result {
            color: #00ff88;
            font-family: monospace;
            font-size: 12px;
            line-height: 1;
            white-space: pre;
            overflow-x: auto;
            text-align: left;
            padding-top:30px; /* Space for copy button */
        }
        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #aaa;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔠 Text → Dots Art</h1>
        
        <form method="post">
            <textarea name="text" placeholder="Type text here to convert to Braille dots..." required><?= htmlspecialchars($defaultText) ?></textarea>
            
            <div class="controls">
                <select name="size">
                    <option value="9" <?= $selectedSize == 9 ? 'selected' : '' ?>>Size: Tiny (9pt)</option>
                    <option value="10" <?= $selectedSize == 10 ? 'selected' : '' ?>>Size: Extra Small (10pt)</option>
                    <option value="12" <?= $selectedSize == 12 ? 'selected' : '' ?>>Size: Small (12pt)</option>
                    <option value="18" <?= $selectedSize == 18 ? 'selected' : '' ?>>Size: Medium (18pt)</option>
                    <option value="24" <?= $selectedSize == 24 ? 'selected' : '' ?>>Size: Large (24pt)</option>
                    <option value="36" <?= $selectedSize == 36 ? 'selected' : '' ?>>Size: Huge (36pt)</option>
                </select>
                
                <button type="submit" class="submit-btn">Convert to Dots</button>
            </div>
        </form>
        
        <?php if (!empty($resultDots)): ?>
            <div class="result-container">
                <button class="copy-btn" onclick="copyToClipboard(this)">Copy to Clipboard</button>
                <div class="result" id="brailleOutput"><?= $resultDots ?></div>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            Generates copy-paste friendly Braille patterns from text input.
        </div>
    </div>
    
    <script>
        function copyToClipboard(btn) {
            const text = document.getElementById('brailleOutput').innerText;
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerText;
                btn.innerText = 'Copied!';
                btn.style.background = 'rgba(0, 255, 136, 0.2)';
                btn.style.color = '#00ff88';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = '';
                    btn.style.color = '';
                }, 2000);
            });
        }
    </script>
</body>
</html>
