<?php

namespace App\Core;

class Helpers
{
    /**
     * Génère une URL absolue en se basant sur BASE_URL.
     * Ex:
     *  Helpers::url('/') => http://localhost/tomtroc_mvc/public/
     *  Helpers::url('/livres') => http://localhost/tomtroc_mvc/public/livres
     */
    public static function url(string $path = ''): string
    {
        $base = defined('BASE_URL') ? BASE_URL : '';

        // Normalise base (sans slash final)
        $base = rtrim($base, '/');

        // Normalise path (avec slash initial)
        $path = '/' . ltrim($path, '/');

        // Cas spécial racine
        if ($path === '/') {
            return $base . '/';
        }

        return $base . $path;
    }

    /**
     * Redirection
     */
    public static function redirect(string $path): void
    {
        header('Location: ' . self::url($path));
        exit;
    }

    /**
     * Échappement HTML (anti-XSS)
     */
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
