<?php

namespace RedlineCms\Service;

class Str
{
    public static function url_safe_string(string $string): string
    {
        // Convert Unicode characters to their ASCII equivalents
        if (function_exists('transliterator_transliterate')) {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
        }

        // Convert the string to lowercase
        $string = strtolower($string);

        // Remove any special characters except letters, numbers, and spaces
        $string = preg_replace('/[^a-z0-9\s\-]/u', '', $string);

        // Replace multiple spaces or whitespace with a single dash
        $string = preg_replace('/\s+/', '-', $string);

        // Trim dashes from both ends of the string (optional)
        $string = trim($string, '-');

        return $string;
    }
}
