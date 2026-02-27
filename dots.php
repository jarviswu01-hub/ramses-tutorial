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
// USAGE
// ────────────────────────────────────────────────
if (php_sapi_name() === 'cli' && isset($argv[1])) {
    // Command line usage: php dots.php photo.jpg [width] [threshold]
    $file = $argv[1];
    $width = isset($argv[2]) ? (int)$argv[2] : 80;
    $thresh = isset($argv[3]) ? (float)$argv[3] : 0.5;
    $result = imageToBrailleDots($file, $width, $thresh);
    echo $result . "\n";
} else {
    // Web usage example
    header('Content-Type: text/plain; charset=utf-8');
    
    // Change this path to your image
    $photo = 'example.jpg';
    
    echo imageToBrailleDots($photo, 90, 0.48);
    // Try threshold between 0.35 – 0.65 depending on your photo
    // Smaller width = smaller message size
}
