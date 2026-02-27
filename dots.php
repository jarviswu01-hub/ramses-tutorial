<?php
// ────────────────────────────────────────────────
// Photo → Unicode Braille Dots Art
// (copy-paste friendly for messengers)
// ────────────────────────────────────────────────
function imageToBrailleDots(string $imagePath, int $maxWidth = 80, float $threshold = 0.5): string {
    if (!file_exists($imagePath)) {
        return "Error: Image not found at path: " . $imagePath;
    }
    
    $fileInfo = getimagesize($imagePath);
    if ($fileInfo === false) {
        return "Error: Not a valid image file";
    }
    
    $mimeType = $fileInfo['mime'];
    $img = false;
    
    // Load image based on MIME type
    switch ($mimeType) {
        case 'image/jpeg':
            $img = @imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $img = @imagecreatefrompng($imagePath);
            break;
        case 'image/gif':
            $img = @imagecreatefromgif($imagePath);
            break;
        case 'image/webp':
            $img = @imagecreatefromwebp($imagePath);
            break;
        case 'image/bmp':
        case 'image/x-ms-bmp':
            $img = @imagecreatefrombmp($imagePath);
            break;
        default:
            return "Error: Unsupported image type: " . $mimeType;
    }
    
    if (!$img) {
        return "Error: Failed to create image resource from file";
    }
    
    // Get dimensions
    $width = imagesx($img);
    $height = imagesy($img);
    
    // Calculate new size (keep aspect ratio)
    $newWidth = min($maxWidth, $width);
    $newHeight = (int) round($height * $newWidth / $width);
    
    // Resize
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    
    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagedestroy($img);
    
    $output = "";
    
    // Each Braille char covers 2×4 pixels
    for ($y = 0; $y < $newHeight; $y += 4) {
        for ($x = 0; $x < $newWidth; $x += 2) {
            $code = 0;
            
            // Braille dot positions (0-7)
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
            
            foreach ($dots as $i => list($px, $py)) {
                if ($py >= $newHeight || $px >= $newWidth) continue;
                
                $rgb = imagecolorat($resized, $px, $py);
                
                // Handle transparency
                $alpha = ($rgb >> 24) & 0x7F;
                if ($alpha > 127) {
                    // Transparent pixel - treat as white
                    $r = $g = $b = 255;
                } else {
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                }
                
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
        .error {
            background: #ff4444;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📷 Photo → Braille Dots</h1>
        
        <?php 
        $error = "";
        $result = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                $error = "Error: No file uploaded or upload error: " . ($_FILES['photo']['error'] ?? 'unknown');
            } else {
                $tmp = $_FILES['photo']['tmp_name'];
                $result = imageToBrailleDots($tmp, 80, 0.5);
            }
        }
        ?>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($result) && strpos($result, 'Error:') === 0): ?>
            <div class="error"><?= htmlspecialchars($result) ?></div>
        <?php elseif (!empty($result)): ?>
            <div class="result"><?= htmlspecialchars($result) ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" required>
            <button type="submit">Convert to dots</button>
        </form>
        
        <p style="text-align: center; color: #666;">Supported: JPG, PNG, GIF, WebP</p>
    </div>
</body>
</html>
