<?php
// Increase upload limit for this script
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');

// Photo → Unicode Braille Dots Art

function imageToBrailleDots(string $imagePath, int $maxWidth = 100, float $threshold = 0.5): string {
    if (!file_exists($imagePath)) {
        return "Error: Image not found at path: " . $imagePath;
    }
    
    $fileInfo = getimagesize($imagePath);
    if ($fileInfo === false) {
        return "Error: Not a valid image file";
    }
    
    $mimeType = $fileInfo['mime'];
    $img = false;
    
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
        default:
            return "Error: Unsupported image type: " . $mimeType;
    }
    
    if (!$img) {
        return "Error: Failed to create image resource from file";
    }
    
    $width = imagesx($img);
    $height = imagesy($img);
    
    $newWidth = min($maxWidth, $width);
    $newHeight = (int) round($height * $newWidth / $width);
    
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $white = imagecolorallocate($resized, 255, 255, 255);
    imagefill($resized, 0, 0, $white);
    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagedestroy($img);
    
    // Convert to grayscale with contrast boost
    $gray = imagecreatetruecolor($newWidth, $newHeight);
    imagecopy($gray, $resized, 0, 0, 0, 0, $newWidth, $newHeight);
    imagefilter($gray, IMG_FILTER_GRAYSCALE);
    imagefilter($gray, IMG_FILTER_CONTRAST, 30);
    
    $output = "";
    
    for ($y = 0; $y < $newHeight; $y += 4) {
        for ($x = 0; $x < $newWidth; $x += 2) {
            $code = 0;
            
            $dots = [
                [$x, $y],
                [$x, $y+1],
                [$x, $y+2],
                [$x+1, $y],
                [$x+1, $y+1],
                [$x+1, $y+2],
                [$x, $y+3],
                [$x+1, $y+3],
            ];
            
            foreach ($dots as $i => list($px, $py)) {
                if ($py >= $newHeight || $px >= $newWidth) continue;
                
                $rgb = imagecolorat($gray, $px, $py);
                $grayVal = $rgb & 0xFF;
                $brightness = $grayVal / 255;
                
                if ($brightness < $threshold) {
                    $code |= (1 << $i);
                }
            }
            
            $output .= mb_chr(0x2800 + $code, 'UTF-8');
        }
        $output .= "\n";
    }
    
    imagedestroy($resized);
    imagedestroy($gray);
    
    return rtrim($output, "\n");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo to Braille Dots</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #1a1a2e, #16213e); min-height: 100vh; padding: 20px; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #667eea; text-align: center; margin-bottom: 20px; }
        .controls { background: #f5f7fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .control-group { margin-bottom: 15px; }
        .control-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .control-group input[type="range"] { width: 100%; }
        form { text-align: center; margin-bottom: 20px; }
        input[type="file"] { padding: 10px; border: 2px dashed #667eea; border-radius: 10px; margin-right: 10px; }
        button { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 12px 30px; border-radius: 25px; cursor: pointer; font-weight: bold; font-size: 16px; }
        button:hover { transform: scale(1.05); }
        .result { background: #000; color: #fff; padding: 20px; border-radius: 10px; overflow-x: auto; white-space: pre; font-family: monospace; font-size: 6px; line-height: 0.6; text-align: center; }
        .error { background: #ff4444; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📷 Photo → Braille Dots</h1>
        
        <?php 
        $error = "";
        $result = "";
        $width = isset($_POST['width']) ? (int)$_POST['width'] : 100;
        $threshold = isset($_POST['threshold']) ? (int)$_POST['threshold'] : 50;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['photo']['tmp_name'];
            $result = imageToBrailleDots($tmp, $width, $threshold / 100);
        }
        ?>
        
        <div class="controls">
            <form method="post" enctype="multipart/form-data">
                <div class="control-group">
                    <label>Width (Detail): <span id="widthVal"><?= $width ?></span></label>
                    <input type="range" name="width" min="40" max="150" value="<?= $width ?>" oninput="document.getElementById('widthVal').textContent=this.value">
                </div>
                <div class="control-group">
                    <label>Threshold (Darkness): <span id="threshVal"><?= $threshold ?>%</span></label>
                    <input type="range" name="threshold" min="20" max="80" value="<?= $threshold ?>" oninput="document.getElementById('threshVal').textContent=this.value+'%'">
                </div>
                <input type="file" name="photo" accept="image/*" required>
                <button type="submit">Convert to dots</button>
            </form>
        </div>
        
        <?php if (!empty($result)): ?>
            <?php if (strpos($result, 'Error:') === 0): ?>
                <div class="error"><?= htmlspecialchars($result) ?></div>
            <?php else: ?>
                <div class="result"><?= htmlspecialchars($result) ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
