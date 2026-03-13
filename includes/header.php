<?php
// ── Güvenlik HTTP Header'ları ─────────────────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Determine SEO meta based on current page
$seo_title = $lang['seo_home_title'] ?? SITE_NAME;
$seo_desc = $lang['seo_home_desc'] ?? '';
if ($page === 'about') {
    $seo_title = $lang['seo_about_title'] ?? SITE_NAME;
    $seo_desc = $lang['seo_about_desc'] ?? '';
} elseif ($page === 'menu') {
    if ($branch === 'lamai') {
        $seo_title = $lang['seo_menu_lamai_title'] ?? SITE_NAME;
        $seo_desc = $lang['seo_menu_lamai_desc'] ?? '';
    } else {
        $seo_title = $lang['seo_menu_chaweng_title'] ?? SITE_NAME;
        $seo_desc = $lang['seo_menu_chaweng_desc'] ?? '';
    }
}

// Build canonical URL
$canonical = SITE_URL . '/' . $lang_code;
if ($page === 'about') $canonical .= '/about';
elseif ($page === 'menu') $canonical .= '/menu/' . ($branch ?: 'chaweng');

// OG image — fallback to front.png if og-image.jpg doesn't exist
$og_image_file = __DIR__ . '/../public/images/og-image.jpg';
$og_image_url  = file_exists($og_image_file)
    ? SITE_URL . '/public/images/og-image.jpg'
    : SITE_URL . '/public/images/front.png';

// Flag emojis for languages
$lang_flags = [
    'en' => '🇬🇧', 'tr' => '🇹🇷', 'ar' => '🇸🇦',
    'th' => '🇹🇭', 'de' => '🇩🇪', 'fr' => '🇫🇷', 'it' => '🇮🇹'
];
$lang_names = [
    'en' => 'EN', 'tr' => 'TR', 'ar' => 'AR',
    'th' => 'TH', 'de' => 'DE', 'fr' => 'FR', 'it' => 'IT'
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang_code) ?>"<?= $is_rtl ? ' dir="rtl"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($seo_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo_desc) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($lang['seo_keywords'] ?? 'Turkish cuisine, Mediterranean restaurant, Koh Samui, Chaweng, Lamai, halal food Thailand') ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#0A0A0A">
    <meta name="author" content="Royal Turkish Cuisine">
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

    <!-- Open Graph -->
    <meta property="og:site_name" content="Royal Turkish Cuisine">
    <meta property="og:title" content="<?= htmlspecialchars($seo_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo_desc) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
    <meta property="og:type" content="restaurant">
    <meta property="og:image" content="<?= $og_image_url ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">
    <meta property="og:image:alt" content="Royal Turkish Cuisine - Koh Samui">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo_desc) ?>">
    <meta name="twitter:image" content="<?= $og_image_url ?>">

    <!-- hreflang -->
    <?php foreach (SUPPORTED_LANGS as $l): ?>
    <link rel="alternate" hreflang="<?= $l ?>" href="<?= SITE_URL . url($l, $page, $branch) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= SITE_URL ?>/en">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'gold': '#B8860B',
                    'royal-black': '#0A0A0A',
                    'card-bg': '#111111',
                    'border-color': '#2A2A2A',
                },
                fontFamily: {
                    'playfair': ['"Playfair Display"', 'serif'],
                    'inter': ['Inter', 'sans-serif'],
                }
            }
        }
    }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #B8860B;
            --royal-black: #0A0A0A;
            --card-bg: #111111;
            --border-color: #2A2A2A;
        }
        * { box-sizing: border-box; }
        body {
            background-color: #0A0A0A;
            color: white;
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        .gold { color: #B8860B; }
        .bg-gold { background-color: #B8860B; }
        .border-gold { border-color: #B8860B; }
        .nav-link {
            color: #a1a1aa;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            transition: color 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .nav-link:hover, .nav-link.active { color: #B8860B; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #111111;
            border: 1px solid #2A2A2A;
            min-width: 180px;
            z-index: 100;
            padding-top: 8px;
        }
        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 0;
            right: 0;
            height: 8px;
        }
        .dropdown:hover .dropdown-menu { display: block; }
        .dropdown-menu a {
            display: block;
            padding: 10px 16px;
            color: #a1a1aa;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: color 0.2s, background 0.2s;
        }
        .dropdown-menu a:hover { color: #B8860B; background: rgba(184,134,11,0.05); }
        .lang-dropdown { position: relative; display: inline-block; }
        .lang-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #111111;
            border: 1px solid #2A2A2A;
            min-width: 140px;
            z-index: 100;
            padding-top: 8px;
        }
        .lang-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 0;
            right: 0;
            height: 8px;
        }
        .lang-dropdown:hover .lang-dropdown-menu { display: block; }
        .lang-dropdown-menu a {
            display: block;
            padding: 8px 14px;
            color: #a1a1aa;
            font-size: 0.8rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .lang-dropdown-menu a:hover, .lang-dropdown-menu a.current { color: #B8860B; }
        .card-hover {
            transition: border-color 0.3s, transform 0.3s;
            border: 1px solid #2A2A2A;
        }
        .card-hover:hover {
            border-color: #B8860B;
            transform: translateY(-2px);
        }
        html { scroll-behavior: smooth; }
        .gold-line {
            width: 60px;
            height: 2px;
            background: #B8860B;
            margin: 16px auto;
        }

        /* ── Responsive globals ── */
        img { max-width: 100%; height: auto; }

        /* Tablet */
        @media (max-width: 1024px) {
            nav > div { padding: 0 16px !important; }
        }

        /* Mobile */
        @media (max-width: 767px) {
            main { overflow-x: hidden; }
        }

        /* Very small / Apple Watch (≤ 200px) */
        @media (max-width: 200px) {
            body { font-size: 10px; }
            nav > div { height: auto !important; padding: 8px !important; flex-wrap: wrap; gap: 6px; }
            .gold-line { width: 30px; }
        }

        /* TV / large screens (≥ 1800px) */
        @media (min-width: 1800px) {
            nav > div { max-width: 1600px !important; }
            body { font-size: 1.05rem; }
        }
    </style>

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    [
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Royal Turkish Cuisine",
        "url": "<?= SITE_URL ?>",
        "inLanguage": ["en","tr","ar","th","de","fr","it"],
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?= SITE_URL ?>/en/menu/chaweng",
            "query-input": "required name=search_term_string"
        }
    },
    {
        "@context": "https://schema.org",
        "@type": "Restaurant",
        "@id": "<?= SITE_URL ?>/#chaweng",
        "name": "Royal Turkish Cuisine – Chaweng",
        "description": "Authentic Turkish and Mediterranean cuisine on Chaweng Beach, Koh Samui. Halal meats, fresh ingredients, 48 years chef experience.",
        "servesCuisine": ["Turkish", "Mediterranean", "Halal"],
        "priceRange": "$$",
        "url": "<?= SITE_URL ?>/en/menu/chaweng",
        "telephone": "<?= CHAWENG_PHONE ?>",
        "image": "<?= $og_image_url ?>",
        "logo": "<?= SITE_URL ?>/public/images/logo.png",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "4/3 Moo 3, Chaweng Beach Road",
            "addressLocality": "Koh Samui",
            "addressRegion": "Surat Thani",
            "postalCode": "84320",
            "addressCountry": "TH"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "9.5314",
            "longitude": "100.0624"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
            "opens": "11:00",
            "closes": "23:00"
        },
        "hasMap": "https://maps.app.goo.gl/WCPnF4hLMdbMzU1X7",
        "sameAs": ["https://maps.app.goo.gl/WCPnF4hLMdbMzU1X7"]
    },
    {
        "@context": "https://schema.org",
        "@type": "Restaurant",
        "@id": "<?= SITE_URL ?>/#lamai",
        "name": "Royal Turkish Cuisine – Lamai",
        "description": "Authentic Turkish and Mediterranean cuisine on Lamai Beach, Koh Samui. Halal meats, fresh ingredients, 48 years chef experience.",
        "servesCuisine": ["Turkish", "Mediterranean", "Halal"],
        "priceRange": "$$",
        "url": "<?= SITE_URL ?>/en/menu/lamai",
        "telephone": "<?= LAMAI_PHONE ?>",
        "image": "<?= $og_image_url ?>",
        "logo": "<?= SITE_URL ?>/public/images/logo.png",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "124/7 Moo 3, Lamai Beach Road",
            "addressLocality": "Koh Samui",
            "addressRegion": "Surat Thani",
            "postalCode": "84310",
            "addressCountry": "TH"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "9.4672",
            "longitude": "100.0584"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
            "opens": "11:00",
            "closes": "23:00"
        },
        "hasMap": "https://maps.app.goo.gl/nZU9Sy1mJpsSGtLg9",
        "sameAs": ["https://maps.app.goo.gl/nZU9Sy1mJpsSGtLg9"]
    }
    <?php if ($page !== 'home'): ?>,
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?= SITE_URL ?>/<?= $lang_code ?>"
            }
            <?php if ($page === 'about'): ?>,
            {
                "@type": "ListItem",
                "position": 2,
                "name": "About",
                "item": "<?= SITE_URL ?>/<?= $lang_code ?>/about"
            }
            <?php elseif ($page === 'menu'): ?>,
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Menu",
                "item": "<?= SITE_URL ?>/<?= $lang_code ?>/menu/<?= $branch ?>"
            }
            <?php endif; ?>
        ]
    }
    <?php endif; ?>
    ]
    </script>
</head>
<body style="background-color:#0A0A0A;">

<!-- Navbar -->
<nav style="background:rgba(0,0,0,0.95);position:sticky;top:0;z-index:50;border-bottom:1px solid #2A2A2A;">
    <div style="max-width:1200px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;height:64px;">
        <!-- Logo -->
        <a href="<?= url($lang_code, 'home') ?>" style="display:flex;align-items:center;text-decoration:none;">
            <?php if (file_exists(__DIR__ . '/../public/images/logo.png')): ?>
            <img src="/public/images/logo.png" alt="Royal Turkish Cuisine" style="height:40px;">
            <?php else: ?>
            <span style="font-family:'Playfair Display',serif;font-size:1.25rem;color:#B8860B;letter-spacing:0.1em;">ROYAL</span>
            <?php endif; ?>
        </a>

        <!-- Desktop Nav -->
        <div id="desktop-nav" style="display:flex;align-items:center;gap:28px;">
            <a href="<?= url($lang_code, 'home') ?>" class="nav-link<?= $page === 'home' ? ' active' : '' ?>"><?= htmlspecialchars($lang['nav_home']) ?></a>
            <a href="<?= url($lang_code, 'about') ?>" class="nav-link<?= $page === 'about' ? ' active' : '' ?>"><?= htmlspecialchars($lang['nav_about']) ?></a>

            <!-- Menu Dropdown -->
            <div class="dropdown">
                <span class="nav-link<?= $page === 'menu' ? ' active' : '' ?>" style="cursor:pointer;">
                    <?= htmlspecialchars($lang['nav_menu']) ?> &#9662;
                </span>
                <div class="dropdown-menu">
                    <a href="<?= url($lang_code, 'menu', 'chaweng') ?>"><?= htmlspecialchars($lang['nav_chaweng']) ?></a>
                    <a href="<?= url($lang_code, 'menu', 'lamai') ?>"><?= htmlspecialchars($lang['nav_lamai']) ?></a>
                </div>
            </div>

            <!-- Language Dropdown -->
            <div class="lang-dropdown">
                <span class="nav-link" style="cursor:pointer;">
                    <?= $lang_flags[$lang_code] ?? '&#127760;' ?> <?= strtoupper($lang_code) ?> &#9662;
                </span>
                <div class="lang-dropdown-menu">
                    <?php foreach (SUPPORTED_LANGS as $l): ?>
                    <a href="<?= url($l, $page, $branch) ?>"
                       class="<?= $l === $lang_code ? 'current' : '' ?>">
                        <?= $lang_flags[$l] ?> <?= $lang_names[$l] ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Hamburger -->
        <button id="hamburger" style="display:none;background:none;border:none;cursor:pointer;padding:8px;" aria-label="Menu">
            <span style="display:block;width:22px;height:2px;background:white;margin:4px 0;transition:0.3s;"></span>
            <span style="display:block;width:22px;height:2px;background:white;margin:4px 0;transition:0.3s;"></span>
            <span style="display:block;width:22px;height:2px;background:white;margin:4px 0;transition:0.3s;"></span>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" style="display:none;background:#111;border-top:1px solid #2A2A2A;padding:16px 24px;">
        <a href="<?= url($lang_code, 'home') ?>" style="display:block;padding:10px 0;color:#a1a1aa;text-decoration:none;font-size:0.85rem;letter-spacing:0.08em;"><?= htmlspecialchars($lang['nav_home']) ?></a>
        <a href="<?= url($lang_code, 'about') ?>" style="display:block;padding:10px 0;color:#a1a1aa;text-decoration:none;font-size:0.85rem;letter-spacing:0.08em;"><?= htmlspecialchars($lang['nav_about']) ?></a>
        <a href="<?= url($lang_code, 'menu', 'chaweng') ?>" style="display:block;padding:10px 0;color:#a1a1aa;text-decoration:none;font-size:0.85rem;letter-spacing:0.08em;"><?= htmlspecialchars($lang['nav_chaweng']) ?></a>
        <a href="<?= url($lang_code, 'menu', 'lamai') ?>" style="display:block;padding:10px 0;color:#a1a1aa;text-decoration:none;font-size:0.85rem;letter-spacing:0.08em;"><?= htmlspecialchars($lang['nav_lamai']) ?></a>
        <div style="margin-top:12px;padding-top:12px;padding-bottom:12px;border-top:1px solid #2A2A2A;display:flex;flex-wrap:wrap;gap:8px;">
            <?php foreach (SUPPORTED_LANGS as $l): ?>
            <a href="<?= url($l, $page, $branch) ?>" style="color:<?= $l === $lang_code ? '#B8860B' : '#a1a1aa' ?>;text-decoration:none;font-size:0.8rem;">
                <?= $lang_flags[$l] ?> <?= $lang_names[$l] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<main>

<script>
// Mobile menu toggle
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');
const desktopNav = document.getElementById('desktop-nav');

function handleResize() {
    if (window.innerWidth < 768) {
        hamburger.style.display = 'block';
        desktopNav.style.display = 'none';
    } else {
        hamburger.style.display = 'none';
        desktopNav.style.display = 'flex';
        mobileMenu.style.display = 'none';
    }
}
handleResize();
window.addEventListener('resize', handleResize);

hamburger.addEventListener('click', function() {
    mobileMenu.style.display = mobileMenu.style.display === 'none' ? 'block' : 'none';
});
</script>
