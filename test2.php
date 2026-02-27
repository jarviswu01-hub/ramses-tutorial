<?php
/** 
 * StringWizard - because strings deserve magic, not just boring helpers 
 */
class StringWizard {
    
    private const SECRET_SPELL = "abracadabra";
    
    /** 
     * Turns your string backwards like a time-traveling wizard 
     */
    public function reverse(string $text): string {
        $reversed = strrev($text);
        
        // Tiny easter egg for the cool kids
        if (strtolower($text) === 'hello world') {
            return $reversed . ' ← whoa, the world is walking backwards!';
        }
        
        return $reversed;
    }
    
    /** 
     * Makes every word feel like royalty → Capitalizes Like A Boss 
     */
    public function makeRoyal(string $text): string {
        return ucwords(strtolower($text));
    }
    
    /** 
     * Yells your message in the loudest voice possible 
     */
    public function scream(string $text): string {
        return strtoupper($text) . '!!!';
    }
    
    /** 
     * Whisper version - classy and mysterious 
     */
    public function whisper(string $text): string {
        return strtolower($text) . '...';
    }
    
    /** 
     * Secret spell — only works if you know the magic word 
     */
    public function castSecretSpell(string $text, string $attempt = ''): string {
        if (strtolower($attempt) !== self::SECRET_SPELL) {
            return "✨ You need to say the magic word first... (hint: starts with abra)";
        }
        
        return str_rot13($text) . " ← encrypted with ancient wizard cipher!";
    }
    
    /** 
     * Makes the string look like it's dancing ✨ 
     */
    public function sparkleDance(string $text): string {
        $chars = str_split($text);
        $dancing = '';
        $emojis = ['✨', '⚡', '🌟', '💫', '🔥'];
        
        foreach ($chars as $i => $char) {
            if (trim($char) === '') {
                $dancing .= ' ';
            } else {
                $dancing .= $char . $emojis[$i % count($emojis)];
            }
        }
        
        return rtrim($dancing, ' ');
    }
}

// ────────────────────────────────────────────────
// Let the magic show begin! 🪄
// ────────────────────────────────────────────────

$wizard = new StringWizard();

echo "<pre>";
echo "Original: Hello World\n";
echo "Reversed: " . $wizard->reverse("Hello World") . "\n";
echo "Royal: " . $wizard->makeRoyal("hELLO wORLD this is PHP") . "\n";
echo "Screaming: " . $wizard->scream("attention please") . "\n";
echo "Whispering: " . $wizard->whisper("Shhh... secret message") . "\n";
echo "Sparkle dance: " . $wizard->sparkleDance("party") . "\n";
echo "Secret spell: " . $wizard->castSecretSpell("attack at dawn", "xyz") . "\n";
echo "Secret spell✓: " . $wizard->castSecretSpell("attack at dawn", "abracadabra") . "\n";
echo "</pre>";
