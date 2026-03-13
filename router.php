<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve sitemap and robots
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}
if ($uri === '/robots.txt') {
    header('Content-Type: text/plain');
    readfile(__DIR__ . '/robots.txt');
    return true;
}

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}
if ($uri !== '/' && file_exists(__DIR__ . $uri) && pathinfo($uri, PATHINFO_EXTENSION) !== '') {
    return false;
}

// Admin panel — serve directly (whitelist only, no traversal)
if (strncmp($uri, '/admin', 6) === 0) {
    // /admin or /admin/ → index.php
    if ($uri === '/admin' || $uri === '/admin/') {
        require __DIR__ . '/admin/index.php';
        return true;
    }
    // Whitelist: only allow known admin pages
    $allowed_admin = ['login', 'menu-images', 'content'];
    $admin_slug = basename($uri, '.php');
    if (in_array($admin_slug, $allowed_admin)) {
        $admin_file = __DIR__ . '/admin/' . $admin_slug . '.php';
        if (file_exists($admin_file)) {
            require $admin_file;
            return true;
        }
    }
    http_response_code(404);
    echo '404 Not Found';
    return true;
}

// Parse lang + page + branch from URI
$path = ltrim($uri, '/');
$segments = explode('/', $path);

$supported_langs = ['en','tr','ar','th','de','fr','it'];
$lang = 'en';
$page = 'home';
$branch = '';

$lang_found = false;
if (!empty($segments[0]) && in_array($segments[0], $supported_langs)) {
    $lang = array_shift($segments);
    $lang_found = true;
}

if (!empty($segments[0])) {
    $page = $segments[0];
}
if (!empty($segments[1])) {
    $branch = $segments[1];
}

if ($lang_found) {
    $_GET['lang'] = $lang;
}
$_GET['page'] = $page;
if ($branch) $_GET['branch'] = $branch;

$_SERVER['REQUEST_URI'] = '/' . $lang . ($page !== 'home' ? '/' . $page . ($branch ? '/' . $branch : '') : '');

require __DIR__ . '/index.php';
