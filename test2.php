<?php

/**
 * StringHelper Class to perform common string operations.
 */
class StringHelper {

    /**
     * Reverses the provided string.
     *
     * @param string $str The input string to reverse.
     * @return string The reversed string.
     */
    public function reverse($str) {
        return strrev($str);
    }

    /**
     * Capitalizes the first letter of each word in the provided string.
     *
     * @param string $str The input string to capitalize.
     * @return string The capitalized string.
     */
    public function capitalize($str) {
        return ucwords($str);
    }
}

// Example usage:
$stringHelper = new StringHelper();
echo "Reversed: " . $stringHelper->reverse("Hello World") . "<br>";
echo "Capitalized: " . $stringHelper->capitalize("hello world");

?>
