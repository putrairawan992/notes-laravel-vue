<?php

/**
 * Railway Router for PHP Built-in Server
 * 
 * Menangani URL rewriting agar semua request diarahkan ke index.php,
 * kecuali file statis yang memang ada di public/.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Jika file statis benar-benar ada, serve langsung
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Semua yang lain → index.php (Laravel front controller)
require_once __DIR__ . '/index.php';
