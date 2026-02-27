<?php
// ────────────────────────────────────────────────
// Photo → Unicode Braille Dots Art
// (copy-paste friendly for messengers)
// ────────────────────────────────────────────────
function imageToBrailleDots(string $imagePath, int $maxWidth = 80, float $threshold = 0.5): string {
    if (!file_exists($imagePath)) {
        return "Error: Image not found.";
    }
    
    // Load image
    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    $img = match ($ext) {
        'jpg', 'jpeg' => imagecreatefromjpeg($imagePath),
        'png' => imagecreatefrompng($imagePath),
        default => false
    };
    
    if (!$img) {
        return "Error: Unsupported or corrupted image.";
    }
    
    // Get dimensions
    $width = imagesx($img);
    $height = imagesy($img);
    
    // Calculate new size (keep aspect ratio)
    $newWidth = min($maxWidth, $width);
    $newHeight = (int) round($height * $newWidth / $width);
    
    // Resize (we use fast resampling)
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $img, 0,0,0,0, $newWidth,$newHeight, $width,$height);
    imagedestroy($img);
    
    $output = "";
    
    // Each Braille char covers 2×4 pixels
    for ($y = 0; $y < $newHeight; $y += 4) {
        for ($x = 0; $x < $newWidth; $x += 2) {
            $code = 0;
            
            // Braille dot positions (0-7)
            // 0 3
            // 1 4
            // 2 5
            // 6 7 ← bottom row
            
            $dots = [
                [$x, $y],         // 0
                [$x, $y+1],       // 1
                [$x, $y+2],       // 2
                [$x+1, $y],       // 3
                [$x+1, $y+1],     // 4
                [$x+1, $y+2],     // 5
                [$x, $y+3],       // 6
                [$x+1, $y+3],     // 7
            ];
            
            foreach ($dots as $i => [$px, $py]) {
                if ($py >= $newHeight || $px >= $newWidth) continue;
                
                $rgb = imagecolorat($resized, $px, $py);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Simple brightness (0–1)
                $brightness = ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;
                
                if ($brightness < $threshold) {
                    $code |= (1 << $i);
                }
            }
            
            // Braille characters start at U+2800
            $output .= mb_chr(0x2800 + $code, 'UTF-8');
        }
        $output .= "\n";
    }
    
    imagedestroy($resized);
    return rtrim($output, "\n");
}

// ────────────────────────────────────────────────
// Upload Form UI
// ────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo to Braille Dots</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="file"] {
            padding: 10px;
            border: 2px dashed #667eea;
            border-radius: 10px;
            margin-right: 10px;
        }
        button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }
        button:hover {
            transform: scale(1.05);
        }
        .result {
            background: #1a1a2e;
            color: #00ff88;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            white-space: pre;
            font-family: monospace;
            font-size: 10px;
            line-height: 1;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📷 Photo → Braille Dots</h1>
        
        <?php if ($_FILES['photo'] ?? false): ?>
            <?php 
            $tmp = $_FILES['photo']['tmp_name'];
            $result = imageToBrailleDots($tmp, 80, 0.5);
            ?>
            <div class="result"><?= htmlspecialchars($result) ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*">
            <button type="submit">Convert to dots</button>
        </form>
    </div>
</body>
</html>
