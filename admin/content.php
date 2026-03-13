<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Section grupları ve key'leri
$sections = [
    'Hero' => [
        'hero_badge_label'  => 'Üst küçük etiket',
        'hero_subtitle'     => 'Altbaşlık',
        'hero_desc'         => 'Açıklama',
        'hero_btn_menu'     => 'Buton – Menü',
        'hero_btn_locations'=> 'Buton – Lokasyonlar',
    ],
    'A Taste of the Mediterranean' => [
        'taste_label'    => 'Üst küçük etiket',
        'taste_title'    => 'Section başlığı',
        'taste_f1_title' => 'Özellik 1 – Başlık',
        'taste_f1_desc'  => 'Özellik 1 – Açıklama',
        'taste_f2_title' => 'Özellik 2 – Başlık',
        'taste_f2_desc'  => 'Özellik 2 – Açıklama',
        'taste_f3_title' => 'Özellik 3 – Başlık',
        'taste_f3_desc'  => 'Özellik 3 – Açıklama',
        'taste_f4_title' => 'Özellik 4 – Başlık',
        'taste_f4_desc'  => 'Özellik 4 – Açıklama',
        'taste_btn'      => 'Buton – Menüye Git',
    ],
    'Our Story' => [
        'story_label' => 'Üst küçük etiket',
        'story_title' => 'Başlık',
        'story_p1'    => 'Paragraf 1',
        'story_p2'    => 'Paragraf 2',
        'story_quote' => 'Alıntı (quote)',
        'story_btn'   => 'Buton – Hikayemiz',
    ],
    'About Page' => [
        'about_small' => 'Üst küçük etiket',
        'story_label' => 'İkinci küçük etiket',
        'story_title' => 'Sayfa başlığı',
        'story_p1'    => 'Paragraf 1',
        'story_p2'    => 'Paragraf 2',
    ],
];

$langs = ['en' => 'English', 'tr' => 'Turkish', 'ar' => 'Arabic', 'th' => 'Thai', 'de' => 'German', 'fr' => 'French', 'it' => 'Italian'];

// POST: update
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF doğrula
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die('Forbidden.');
    }
    $key = $_POST['block_key'] ?? '';
    // block_key whitelist — sadece izin verilen key'ler
    $all_keys = array_merge(...array_values(array_map('array_keys', $sections)));
    if ($key && in_array($key, $all_keys)) {
        $sets = [];
        $params = [];
        foreach (array_keys($langs) as $l) {
            $sets[] = "value_$l = ?";
            $params[] = $_POST["value_$l"] ?? '';
        }
        $params[] = $key;
        $pdo->prepare("UPDATE content_blocks SET " . implode(', ', $sets) . " WHERE block_key = ?")->execute($params);
    }
    header('Location: /admin/content.php?section=' . urlencode($_POST['section'] ?? '') . '&saved=1');
    exit;
}

// Tüm block'ları çek
$rows = $pdo->query("SELECT * FROM content_blocks")->fetchAll(PDO::FETCH_ASSOC);
$blocks = [];
foreach ($rows as $r) {
    $blocks[$r['block_key']] = $r;
}

$active_section = $_GET['section'] ?? array_key_first($sections);
$saved = isset($_GET['saved']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content – Royal Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0A0A0A; color: white; display: flex; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        aside { width: 220px; background: #111111; border-right: 1px solid #2A2A2A; flex-shrink: 0; padding: 24px 0; }
        .logo { padding: 0 20px 24px; border-bottom: 1px solid #2A2A2A; margin-bottom: 16px; }
        .logo h1 { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: #B8860B; }
        .logo p { color: #52525b; font-size: 0.7rem; margin-top: 4px; }
        nav { padding: 0 12px; }
        nav a { display: block; padding: 10px 12px; color: #a1a1aa; font-size: 0.85rem; margin-bottom: 4px; border-radius: 4px; }
        nav a:hover, nav a.active { color: #B8860B; background: rgba(184,134,11,0.1); }
        nav a.logout { color: #ef4444; margin-top: 16px; }
        main { flex: 1; padding: 40px; overflow-y: auto; max-width: 900px; }
        h2 { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin-bottom: 4px; }
        .section-tabs { display: flex; gap: 0; margin-bottom: 36px; border-bottom: 1px solid #2A2A2A; flex-wrap: wrap; }
        .section-tabs a { padding: 10px 20px; font-size: 0.78rem; color: #71717a; border-bottom: 2px solid transparent; margin-bottom: -1px; letter-spacing: 0.05em; }
        .section-tabs a:hover { color: #B8860B; }
        .section-tabs a.active { color: #B8860B; border-bottom-color: #B8860B; }
        .field-group { background: #111111; border: 1px solid #2A2A2A; padding: 24px; margin-bottom: 20px; }
        .field-label { color: #a1a1aa; font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 16px; }
        .lang-tabs { display: flex; gap: 4px; margin-bottom: 12px; flex-wrap: wrap; }
        .lang-tab { padding: 5px 12px; font-size: 0.72rem; border: 1px solid #2A2A2A; color: #71717a; cursor: pointer; background: transparent; font-family: 'Inter', sans-serif; transition: all 0.15s; }
        .lang-tab.active, .lang-tab:hover { border-color: #B8860B; color: #B8860B; }
        .lang-panel { display: none; }
        .lang-panel.active { display: block; }
        input[type=text], textarea { width: 100%; background: #0A0A0A; border: 1px solid #2A2A2A; color: white; padding: 10px 12px; font-size: 0.85rem; font-family: 'Inter', sans-serif; outline: none; }
        input[type=text]:focus, textarea:focus { border-color: #B8860B; }
        textarea { resize: vertical; min-height: 80px; }
        .save-btn { padding: 10px 28px; background: #B8860B; color: #000; border: none; font-size: 0.82rem; font-family: 'Inter', sans-serif; font-weight: 600; letter-spacing: 0.08em; cursor: pointer; }
        .save-btn:hover { background: #9A7209; }
        .success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; padding: 12px 16px; font-size: 0.82rem; margin-bottom: 24px; }
        @media (max-width: 768px) {
            body { flex-direction: column !important; }
            aside { width: 100% !important; border-right: none !important; border-bottom: 1px solid #2A2A2A; padding: 12px 0 !important; }
            .logo { padding: 8px 16px 12px !important; }
            nav { display: flex; flex-wrap: wrap; padding: 0 8px !important; }
            nav a { display: inline-block !important; padding: 6px 10px !important; font-size: 0.75rem !important; margin-bottom: 2px; }
            main { padding: 20px 16px !important; max-width: 100% !important; }
            .section-tabs a { padding: 8px 12px; font-size: 0.72rem; }
        }
    </style>
</head>
<body>
<aside>
    <div class="logo">
        <h1>ROYAL</h1>
        <p>Admin Panel</p>
    </div>
    <nav>
        <a href="/admin/">Dashboard</a>
        <a href="/admin/menu-images.php">Menu Images</a>
        <a href="/admin/content.php" class="active">Content</a>
        <a href="/admin/?logout=1&csrf=<?= urlencode($_SESSION['csrf_token']) ?>" class="logout">Logout</a>
    </nav>
</aside>

<main>
    <h2>Content</h2>
    <p style="color:#52525b;font-size:0.85rem;margin-bottom:32px;">Ana sayfa section içeriklerini düzenle</p>

    <?php if ($saved): ?>
    <div class="success">Kaydedildi.</div>
    <?php endif; ?>

    <!-- Section tabs -->
    <div class="section-tabs">
        <?php foreach ($sections as $sname => $keys): ?>
        <a href="?section=<?= urlencode($sname) ?>" class="<?= $active_section === $sname ? 'active' : '' ?>"><?= htmlspecialchars($sname) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Active section fields -->
    <?php
    $current_keys = $sections[$active_section] ?? [];
    foreach ($current_keys as $key => $label):
        $block = $blocks[$key] ?? null;
        $is_long = str_contains($key, 'desc') || str_contains($key, '_p1') || str_contains($key, '_p2') || str_contains($key, 'quote');
    ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="block_key" value="<?= htmlspecialchars($key) ?>">
        <input type="hidden" name="section" value="<?= htmlspecialchars($active_section) ?>">
        <div class="field-group">
            <div class="field-label"><?= htmlspecialchars($label) ?></div>

            <!-- Lang tabs -->
            <div class="lang-tabs" id="tabs-<?= $key ?>">
                <?php foreach ($langs as $code => $name): ?>
                <button type="button" class="lang-tab <?= $code === 'en' ? 'active' : '' ?>"
                    onclick="switchLang('<?= $key ?>', '<?= $code ?>')"><?= strtoupper($code) ?></button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($langs as $code => $name): ?>
            <div class="lang-panel <?= $code === 'en' ? 'active' : '' ?>" id="panel-<?= $key ?>-<?= $code ?>">
                <?php $val = $block["value_$code"] ?? ''; ?>
                <?php if ($is_long): ?>
                <textarea name="value_<?= $code ?>" rows="4"><?= htmlspecialchars($val) ?></textarea>
                <?php else: ?>
                <input type="text" name="value_<?= $code ?>" value="<?= htmlspecialchars($val) ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div style="margin-top:14px;">
                <button type="submit" class="save-btn">Kaydet</button>
            </div>
        </div>
    </form>
    <?php endforeach; ?>
</main>

<script>
function switchLang(key, code) {
    // Panels
    document.querySelectorAll('[id^="panel-' + key + '-"]').forEach(p => p.classList.remove('active'));
    const target = document.getElementById('panel-' + key + '-' + code);
    if (target) target.classList.add('active');
    // Tabs
    document.querySelectorAll('#tabs-' + key + ' .lang-tab').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
</body>
</html>
