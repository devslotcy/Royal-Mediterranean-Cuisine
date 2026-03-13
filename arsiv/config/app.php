<?php
define('SITE_NAME', 'Royal Mediterranean Cuisine');
define('SITE_URL', 'https://samuiroyal.com');
define('DEFAULT_LANG', 'en');
define('SUPPORTED_LANGS', ['en','tr','ar','th','de','fr','it']);
define('WHATSAPP_NUMBER', '66982567595');
define('CHAWENG_PHONE', '+66982567595');
define('LAMAI_PHONE', '+66943358904');
define('CHAWENG_ADDRESS', '4/3 Moo 3, Chaweng Beach Road, Koh Samui, Surat Thani 84320');
define('LAMAI_ADDRESS', '124/7 Moo 3, Lamai Beach Road, Koh Samui, Surat Thani 84310');

function url($lang, $page, $branch = '') {
    $base = '/' . $lang;
    if ($page === 'home') return $base;
    if ($page === 'about') return $base . '/about';
    if ($page === 'menu' && $branch) return $base . '/menu/' . $branch;
    return $base;
}
