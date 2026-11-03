<?php

// Vercel PHP router (php -S): existing files under the document root are
// served statically when this script returns false; everything else goes
// to the Laravel front controller.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (
    $path !== '/' &&
    $path !== '' &&
    !str_contains($path, '..') &&
    !str_ends_with($path, '.htaccess') &&
    is_file(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $path)
) {
    return false;
}

require __DIR__ . '/../public/index.php';
