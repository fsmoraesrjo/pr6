<?php

namespace App\Support;

class SearchHighlight
{
    public static function apply(?string $text, string $query): string
    {
        if (! $text) return '';
        $text = e($text);
        $query = trim($query);
        if (mb_strlen($query) < 2) return $text;

        $words = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_filter($words, fn ($w) => mb_strlen($w) >= 2);
        if (empty($words)) return $text;

        $pattern = '/(' . implode('|', array_map(fn ($w) => preg_quote($w, '/'), $words)) . ')/iu';
        return preg_replace($pattern, '<mark>$1</mark>', $text);
    }
}
