<?php
require_once __DIR__ . '/config/app.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

$today = date('Y-m-d');
// [url_path, priority_en, changefreq]
$pages = [
    ['',              '1.0', 'weekly'],
    ['/about',        '0.8', 'monthly'],
    ['/menu/chaweng', '0.9', 'weekly'],
    ['/menu/lamai',   '0.9', 'weekly'],
];
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php foreach ($pages as [$path, $priority, $changefreq]): ?>
    <?php foreach (SUPPORTED_LANGS as $l): ?>
    <url>
        <loc><?= SITE_URL ?>/<?= $l ?><?= $path ?></loc>
        <lastmod><?= $today ?></lastmod>
        <changefreq><?= $changefreq ?></changefreq>
        <priority><?= $l === 'en' ? $priority : number_format(max(0.1, (float)$priority - 0.05), 1) ?></priority>
        <?php foreach (SUPPORTED_LANGS as $alt): ?>
        <xhtml:link rel="alternate" hreflang="<?= $alt ?>" href="<?= SITE_URL ?>/<?= $alt ?><?= $path ?>"/>
        <?php endforeach; ?>
        <xhtml:link rel="alternate" hreflang="x-default" href="<?= SITE_URL ?>/en<?= $path ?>"/>
    </url>
    <?php endforeach; ?>
<?php endforeach; ?>
</urlset>
