<?php
/**
 * Simple Barcode Generator for SKU numbers
 * Based on Code 128B implementation - supports alphanumeric characters
 */

class SimpleBarcodeGenerator {
    
    // Code 128B character set
    private static $code128 = [
        ' ' => [2,1,2,2,2,2], '!' => [2,2,2,1,2,2], '"' => [2,2,2,2,2,1],
        '#' => [1,2,1,2,2,3], '$' => [1,2,1,3,2,2], '%' => [1,3,1,2,2,2],
        '&' => [1,2,2,2,1,3], '\''=> [1,2,2,3,1,2], '(' => [1,3,2,2,1,2],
        ')' => [2,2,1,2,1,3], '*' => [2,2,1,3,1,2], '+' => [2,3,1,2,1,2],
        ',' => [1,1,2,2,3,2], '-' => [1,2,2,1,3,2], '.' => [1,2,2,2,3,1],
        '/' => [1,1,3,2,2,2], '0' => [1,2,3,1,2,2], '1' => [1,2,3,2,2,1],
        '2' => [2,2,3,2,1,1], '3' => [2,2,1,1,3,2], '4' => [2,2,1,2,3,1],
        '5' => [2,1,3,2,1,2], '6' => [2,2,3,1,1,2], '7' => [3,1,2,1,3,1],
        '8' => [3,1,1,2,2,2], '9' => [3,2,1,1,2,2], ':' => [3,2,1,2,2,1],
        ';' => [3,1,2,2,1,2], '<' => [3,2,2,1,1,2], '=' => [3,2,2,2,1,1],
        '>' => [2,1,2,1,2,3], '?' => [2,1,2,3,2,1], '@' => [2,3,2,1,2,1],
        'A' => [1,1,1,3,2,3], 'B' => [1,3,1,1,2,3], 'C' => [1,3,1,3,2,1],
        'D' => [1,1,2,3,1,3], 'E' => [1,3,2,1,1,3], 'F' => [1,3,2,3,1,1],
        'G' => [2,1,1,3,1,3], 'H' => [2,3,1,1,1,3], 'I' => [2,3,1,3,1,1],
        'J' => [1,1,2,1,3,3], 'K' => [1,1,2,3,3,1], 'L' => [1,3,2,1,3,1],
        'M' => [1,1,3,1,2,3], 'N' => [1,1,3,3,2,1], 'O' => [1,3,3,1,2,1],
        'P' => [3,1,3,1,2,1], 'Q' => [2,1,1,3,3,1], 'R' => [2,3,1,1,3,1],
        'S' => [2,1,3,1,1,3], 'T' => [2,1,3,3,1,1], 'U' => [2,1,3,1,3,1],
        'V' => [3,1,1,1,2,3], 'W' => [3,1,1,3,2,1], 'X' => [3,3,1,1,2,1],
        'Y' => [3,1,2,1,1,3], 'Z' => [3,1,2,3,1,1], '[' => [3,3,2,1,1,1],
        '\\' => [3,1,4,1,1,1], ']' => [2,2,1,4,1,1], '^' => [4,3,1,1,1,1],
        '_' => [1,1,1,2,2,4], '`' => [1,1,1,4,2,2], 'a' => [1,2,1,1,2,4],
        'b' => [1,2,1,4,2,1], 'c' => [1,4,1,1,2,2], 'd' => [1,4,1,2,2,1],
        'e' => [1,1,2,2,1,4], 'f' => [1,1,2,4,1,2], 'g' => [1,2,2,1,1,4],
        'h' => [1,2,2,4,1,1], 'i' => [1,4,2,1,1,2], 'j' => [1,4,2,2,1,1],
        'k' => [2,4,1,2,1,1], 'l' => [2,2,1,1,1,4], 'm' => [4,1,3,1,1,1],
        'n' => [2,4,1,1,1,2], 'o' => [1,3,4,1,1,1], 'p' => [1,1,1,2,4,2],
        'q' => [1,2,1,1,4,2], 'r' => [1,2,1,2,4,1], 's' => [1,1,4,2,1,2],
        't' => [1,2,4,1,1,2], 'u' => [1,2,4,2,1,1], 'v' => [4,1,1,2,1,2],
        'w' => [4,2,1,1,1,2], 'x' => [4,2,1,2,1,1], 'y' => [2,1,2,1,4,1],
        'z' => [2,1,4,1,2,1], '{' => [4,1,2,1,2,1], '|' => [1,1,1,1,4,3],
        '}' => [1,1,1,3,4,1], '~' => [1,3,1,1,4,1]
    ];
    
    // Start, Stop and special patterns
    private static $startB = [2,1,1,4,1,2];
    private static $stop = [2,3,3,1,1,1,2];
    
    /**
     * Generate barcode SVG for SKU
     */
    public static function generateSVG($sku, $width = 200, $height = 50) {
        $bars = self::encode($sku);
        if (!$bars) {
            return false;
        }
        
        $totalWidth = array_sum($bars);
        $barWidth = $width / $totalWidth;
        
        $svg = '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="' . $width . '" height="' . $height . '" fill="white"/>';
        
        $x = 0;
        $isBar = true;
        
        foreach ($bars as $barSize) {
            if ($isBar) {
                $svg .= '<rect x="' . $x . '" y="0" width="' . ($barSize * $barWidth) . '" height="' . $height . '" fill="black"/>';
            }
            $x += $barSize * $barWidth;
            $isBar = !$isBar;
        }
        
        $svg .= '</svg>';
        return $svg;
    }
    
    /**
     * Generate barcode PNG as base64 data
     */
    public static function generatePNG($sku, $width = 200, $height = 50) {
        $bars = self::encode($sku);
        if (!$bars) {
            return false;
        }
        
        $totalWidth = array_sum($bars);
        $barWidth = max(1, $width / $totalWidth);
        $actualWidth = $totalWidth * $barWidth;
        
        $image = imagecreate($actualWidth, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        imagefill($image, 0, 0, $white);
        
        $x = 0;
        $isBar = true;
        
        foreach ($bars as $barSize) {
            if ($isBar) {
                imagefilledrectangle($image, $x, 0, $x + ($barSize * $barWidth) - 1, $height - 1, $black);
            }
            $x += $barSize * $barWidth;
            $isBar = !$isBar;
        }
        
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return base64_encode($imageData);
    }
    
    /**
     * Encode string to barcode bars array
     */
    private static function encode($text) {
        if (empty($text)) {
            return false;
        }
        
        // Start with Start B pattern
        $bars = self::$startB;
        $checksum = 104; // Start B value
        
        // Encode each character
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            
            if (!isset(self::$code128[$char])) {
                // Skip unsupported characters
                continue;
            }
            
            $pattern = self::$code128[$char];
            $bars = array_merge($bars, $pattern);
            
            // Calculate checksum
            $value = array_search($pattern, self::$code128, true);
            if ($value !== false) {
                $charValue = ord($value) - 32;
            } else {
                $charValue = 0;
            }
            $checksum += $charValue * ($i + 1);
        }
        
        // Add checksum character
        $checksumChar = chr(($checksum % 103) + 32);
        if (isset(self::$code128[$checksumChar])) {
            $bars = array_merge($bars, self::$code128[$checksumChar]);
        }
        
        // Add stop pattern
        $bars = array_merge($bars, self::$stop);
        
        return $bars;
    }
}
?> 