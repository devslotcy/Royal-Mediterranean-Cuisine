<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/db.php';

// ─── Language resolution ─────────────────────────────────────────────────────
$lang_code = $_GET['lang'] ?? DEFAULT_LANG;
if (!in_array($lang_code, SUPPORTED_LANGS)) {
    $lang_code = DEFAULT_LANG;
}

// If no lang in URL (bare root), detect browser language and redirect
if (!isset($_GET['lang'])) {
    $detected = DEFAULT_LANG;
    $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($accept) {
        // Parse Accept-Language header: e.g. "tr-TR,tr;q=0.9,en;q=0.8"
        preg_match_all('/([a-z]{2,3})(?:-[A-Z]{2})?(?:;q=[\d.]+)?/i', $accept, $matches);
        foreach ($matches[1] as $browserLang) {
            $browserLang = strtolower($browserLang);
            if (in_array($browserLang, SUPPORTED_LANGS)) {
                $detected = $browserLang;
                break;
            }
        }
    }
    header('Location: /' . $detected, true, 302);
    exit;
}

// ─── Load language file ──────────────────────────────────────────────────────
$lang_file = __DIR__ . '/lang/' . $lang_code . '.php';
if (!file_exists($lang_file)) {
    $lang_file = __DIR__ . '/lang/en.php';
}
require $lang_file;  // sets $lang[]

// ─── Page / Branch routing ───────────────────────────────────────────────────
$page   = $_GET['page']   ?? 'home';
$branch = $_GET['branch'] ?? '';

$allowed_pages = ['home', 'about', 'menu'];
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

$allowed_branches = ['chaweng', 'lamai', ''];
if (!in_array($branch, $allowed_branches)) {
    $branch = 'chaweng';
}

if ($page === 'menu' && !$branch) {
    $branch = 'chaweng';
}

// ─── RTL languages ───────────────────────────────────────────────────────────
$is_rtl = ($lang_code === 'ar');

// ─── Render ──────────────────────────────────────────────────────────────────
// ─── Track visit ─────────────────────────────────────────────────────────────
try {
    $pdo_track = getPDO();
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    $ip = trim(explode(',', $ip)[0]);
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $ref = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);
    $pdo_track->prepare(
        "INSERT INTO site_visits (page, branch, lang, ip, user_agent, referer) VALUES (?,?,?,?,?,?)"
    )->execute([$page, $branch, $lang_code, $ip, $ua, $ref]);
} catch (Exception $e) { /* sessizce geç */ }

require __DIR__ . '/includes/header.php';

switch ($page) {
    case 'about':
        require __DIR__ . '/pages/about.php';
        break;
    case 'menu':
        if ($branch === 'lamai') {
            require __DIR__ . '/pages/menu-lamai.php';
        } else {
            require __DIR__ . '/pages/menu-chaweng.php';
        }
        break;
    default:
        require __DIR__ . '/pages/home.php';
        break;
}

require __DIR__ . '/includes/footer.php';
