<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();
$upload_dir  = __DIR__ . '/../public/images/menu/';
// Magic bytes → gerçek MIME doğrulama
$allowed_mime = ['image/jpeg','image/png','image/webp','image/gif'];
$allowed_ext  = ['jpg','jpeg','png','webp','gif'];
$max_size     = 15 * 1024 * 1024; // 15 MB

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = '';
$error   = '';

// ── Upload (multiple files) ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['menu_images'])) {
    // CSRF doğrula
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403); die('Forbidden.');
    }
    $branch = in_array($_POST['branch'] ?? '', ['chaweng','lamai']) ? $_POST['branch'] : '';
    if (!$branch) {
        $error = 'Please select a branch.';
    } else {
        $files  = $_FILES['menu_images'];
        $count  = count($files['name']);
        $ok     = 0;
        $errors = [];

        $ms = $pdo->prepare("SELECT COALESCE(MAX(sort_order),0) FROM menu_images WHERE branch=?");
        $ms->execute([$branch]);
        $sort = (int)$ms->fetchColumn();

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = htmlspecialchars($files['name'][$i]) . ': upload error';
                continue;
            }
            if ($files['size'][$i] > $max_size) {
                $errors[] = htmlspecialchars($files['name'][$i]) . ': too large (max 15 MB)';
                continue;
            }
            // Hem MIME hem magic bytes kontrol
            $real_mime = mime_content_type($files['tmp_name'][$i]);
            if (!in_array($real_mime, $allowed_mime)) {
                $errors[] = htmlspecialchars($files['name'][$i]) . ': unsupported type';
                continue;
            }
            // Extension güvenli map
            $ext_map = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
            $ext = $ext_map[$real_mime]; // Client extension'ı değil, MIME'dan türet
            $filename = $branch . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $dest     = $upload_dir . $filename;
            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                $sort++;
                $safe_original = mb_substr(basename($files['name'][$i]), 0, 200);
                $pdo->prepare("INSERT INTO menu_images (branch,filename,original_name,sort_order) VALUES (?,?,?,?)")
                    ->execute([$branch, $filename, $safe_original, $sort]);
                $ok++;
            } else {
                $errors[] = htmlspecialchars($files['name'][$i]) . ': could not save';
            }
        }

        if ($ok)     $success = $ok . ' image' . ($ok > 1 ? 's' : '') . ' uploaded successfully.';
        if ($errors) $error   = implode(' · ', $errors);
    }
}

// ── Delete (CSRF via token in URL — GET isteklerinde nonce ile koru) ──────────
if (isset($_GET['delete'])) {
    if (!isset($_GET['csrf']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf'] ?? '')) {
        http_response_code(403); die('Forbidden.');
    }
    $row = $pdo->prepare("SELECT * FROM menu_images WHERE id=?");
    $row->execute([(int)$_GET['delete']]);
    $img = $row->fetch();
    if ($img) {
        $f = $upload_dir . basename($img['filename']); // basename ile path traversal engelle
        if (file_exists($f) && strpos(realpath($f), realpath($upload_dir)) === 0) {
            unlink($f);
        }
        $pdo->prepare("DELETE FROM menu_images WHERE id=?")->execute([$img['id']]);
    }
    header('Location: /admin/menu-images.php?branch=' . ($img['branch'] ?? 'chaweng'));
    exit;
}

// ── Move ──────────────────────────────────────────────────────────────────────
if (isset($_GET['move'])) {
    if (!isset($_GET['csrf']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf'] ?? '')) {
        http_response_code(403); die('Forbidden.');
    }
    $cur = $pdo->prepare("SELECT * FROM menu_images WHERE id=?");
    $cur->execute([(int)$_GET['move']]);
    $cur = $cur->fetch();
    if ($cur) {
        $dir = $_GET['dir'] === 'up' ? 'up' : 'down';
        $op  = $dir === 'up'
            ? $pdo->prepare("SELECT * FROM menu_images WHERE branch=? AND sort_order < ? ORDER BY sort_order DESC LIMIT 1")
            : $pdo->prepare("SELECT * FROM menu_images WHERE branch=? AND sort_order > ? ORDER BY sort_order ASC LIMIT 1");
        $op->execute([$cur['branch'], $cur['sort_order']]);
        $other = $op->fetch();
        if ($other) {
            $pdo->prepare("UPDATE menu_images SET sort_order=? WHERE id=?")->execute([$other['sort_order'], $cur['id']]);
            $pdo->prepare("UPDATE menu_images SET sort_order=? WHERE id=?")->execute([$cur['sort_order'],  $other['id']]);
        }
    }
    header('Location: /admin/menu-images.php?branch=' . ($cur['branch'] ?? 'chaweng'));
    exit;
}

$active_branch = (isset($_GET['branch']) && $_GET['branch'] === 'lamai') ? 'lamai' : 'chaweng';

$chaweng_imgs = $pdo->query("SELECT * FROM menu_images WHERE branch='chaweng' ORDER BY sort_order ASC")->fetchAll();
$lamai_imgs   = $pdo->query("SELECT * FROM menu_images WHERE branch='lamai'   ORDER BY sort_order ASC")->fetchAll();
$active_imgs  = $active_branch === 'lamai' ? $lamai_imgs : $chaweng_imgs;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Images - Royal Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#0A0A0A;color:white;display:flex;min-height:100vh;}
        a{color:inherit;text-decoration:none;}
        /* Sidebar */
        .sidebar{width:220px;background:#111111;border-right:1px solid #2A2A2A;flex-shrink:0;padding:24px 0;}
        .sidebar-logo{padding:0 20px 24px;border-bottom:1px solid #2A2A2A;margin-bottom:16px;}
        .sidebar-logo h1{font-family:'Playfair Display',serif;font-size:1.2rem;color:#B8860B;}
        .sidebar-logo p{color:#52525b;font-size:0.7rem;margin-top:4px;}
        nav{padding:0 12px;}
        nav a{display:block;padding:10px 12px;color:#a1a1aa;font-size:0.85rem;margin-bottom:4px;border-radius:4px;transition:color .2s;}
        nav a:hover,nav a.active{color:#B8860B;background:rgba(184,134,11,.1);}
        nav a.logout{color:#ef4444;margin-top:16px;}
        /* Main */
        main{flex:1;padding:40px;overflow-y:auto;}
        /* Branch tabs */
        .branch-tabs{display:flex;gap:0;margin-bottom:32px;}
        .branch-tab{padding:10px 32px;font-size:0.75rem;letter-spacing:.15em;text-transform:uppercase;border:1px solid #2A2A2A;color:#71717a;cursor:pointer;transition:all .2s;background:transparent;}
        .branch-tab:first-child{border-right:none;}
        .branch-tab.active{background:#B8860B;color:#000;border-color:#B8860B;}
        .branch-tab:hover:not(.active){color:#B8860B;border-color:#B8860B;}
        /* Upload */
        .upload-box{background:#111111;border:1px solid #2A2A2A;padding:24px;margin-bottom:32px;}
        label.field-label{display:block;color:#a1a1aa;font-size:.72rem;letter-spacing:.1em;margin-bottom:6px;}
        select,input[type=file]{background:#0A0A0A;border:1px solid #2A2A2A;color:white;padding:9px 12px;font-size:.85rem;font-family:'Inter',sans-serif;outline:none;}
        select:focus,input[type=file]:focus{border-color:#B8860B;}
        .btn-gold{padding:10px 24px;background:#B8860B;color:#000;border:none;font-size:.8rem;letter-spacing:.1em;cursor:pointer;font-family:'Inter',sans-serif;font-weight:500;}
        .btn-gold:hover{background:#9A7209;}
        /* Grid */
        .img-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
        @media(max-width:900px){.img-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:560px){.img-grid{grid-template-columns:1fr;}}
        .img-card{background:#111111;border:1px solid #2A2A2A;overflow:hidden;}
        .img-card img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block;cursor:pointer;transition:opacity .2s;}
        .img-card img:hover{opacity:.8;}
        .img-card-foot{padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px;}
        .img-name{font-size:.72rem;color:#52525b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;}
        .img-actions{display:flex;gap:4px;flex-shrink:0;}
        .img-actions a{font-size:.7rem;padding:3px 8px;border:1px solid #2A2A2A;color:#a1a1aa;border-radius:2px;}
        .img-actions a:hover{border-color:#B8860B;color:#B8860B;}
        .img-actions a.del{border-color:rgba(239,68,68,.3);color:#f87171;}
        .img-actions a.del:hover{border-color:#ef4444;}
        .empty-note{color:#52525b;font-size:.85rem;padding:48px;text-align:center;border:1px dashed #2A2A2A;}
        .alert{padding:12px 16px;margin-bottom:24px;font-size:.85rem;border:1px solid;}
        .alert-ok{background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.3);color:#4ade80;}
        .alert-err{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.3);color:#f87171;}
        /* Lightbox */
        .lb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.94);z-index:9999;align-items:center;justify-content:center;}
        .lb.open{display:flex;}
        .lb img{max-width:90vw;max-height:88vh;object-fit:contain;display:block;user-select:none;}
        .lb-close{position:absolute;top:18px;right:24px;font-size:1.8rem;color:#a1a1aa;cursor:pointer;background:none;border:none;line-height:1;}
        .lb-prev,.lb-next{position:absolute;top:50%;transform:translateY(-50%);font-size:2.4rem;color:#a1a1aa;cursor:pointer;background:none;border:none;padding:0 20px;user-select:none;}
        .lb-prev{left:0;}.lb-next{right:0;}
        .lb-close:hover,.lb-prev:hover,.lb-next:hover{color:#B8860B;}
        .lb-counter{position:absolute;bottom:18px;left:50%;transform:translateX(-50%);color:#52525b;font-size:.78rem;letter-spacing:.1em;}
        @media (max-width: 768px) {
            body { flex-direction: column !important; }
            .sidebar { width: 100% !important; border-right: none !important; border-bottom: 1px solid #2A2A2A; padding: 12px 0 !important; }
            .sidebar-logo { padding: 8px 16px 12px !important; }
            nav { display: flex; flex-wrap: wrap; padding: 0 8px !important; }
            nav a { display: inline-block !important; padding: 6px 10px !important; font-size: 0.75rem !important; margin-bottom: 2px; }
            main { padding: 20px 16px !important; }
            .branch-tab { padding: 8px 20px; }
        }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo"><h1>ROYAL</h1><p>Admin Panel</p></div>
    <nav>
        <a href="/admin/">Dashboard</a>
        <a href="/admin/menu-images.php" class="active">Menu Images</a>
        <a href="/admin/content.php">Content</a>
        <a href="/admin/?logout=1&csrf=<?= urlencode($_SESSION['csrf_token']) ?>" class="logout">Logout</a>
    </nav>
</aside>

<main>
    <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:4px;">Menu Images</h2>
    <p style="color:#52525b;font-size:.85rem;margin-bottom:28px;">Upload menu photos — they appear on the public menu pages</p>

    <?php if ($success): ?><div class="alert alert-ok"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-err"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Branch Tabs -->
    <div class="branch-tabs">
        <a href="?branch=chaweng" class="branch-tab <?= $active_branch==='chaweng'?'active':'' ?>">
            Chaweng <span style="font-size:.65rem;opacity:.7;">(<?= count($chaweng_imgs) ?>)</span>
        </a>
        <a href="?branch=lamai" class="branch-tab <?= $active_branch==='lamai'?'active':'' ?>">
            Lamai <span style="font-size:.65rem;opacity:.7;">(<?= count($lamai_imgs) ?>)</span>
        </a>
    </div>

    <!-- Upload -->
    <div class="upload-box">
        <p style="color:#a1a1aa;font-size:.7rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:16px;">Upload New Image</p>
        <form method="POST" enctype="multipart/form-data" style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="branch" value="<?= $active_branch ?>">
            <div style="flex:1;min-width:220px;">
                <label class="field-label">IMAGE FILES (JPG · PNG · WebP — max 15 MB each)</label>
                <input type="file" name="menu_images[]" accept="image/*" multiple required style="width:100%;">
            </div>
            <button type="submit" class="btn-gold">UPLOAD TO <?= strtoupper($active_branch) ?></button>
        </form>
    </div>

    <!-- Grid -->
    <?php if (empty($active_imgs)): ?>
    <div class="empty-note">No images uploaded for <?= ucfirst($active_branch) ?> yet.</div>
    <?php else: ?>
    <div class="img-grid" id="grid">
        <?php foreach ($active_imgs as $i => $img): ?>
        <div class="img-card">
            <img src="/public/images/menu/<?= htmlspecialchars($img['filename']) ?>"
                 alt="" onclick="lb(<?= $i ?>)" loading="lazy">
            <div class="img-card-foot">
                <span class="img-name"><?= htmlspecialchars($img['original_name']) ?></span>
                <div class="img-actions">
                    <a href="?branch=<?= $active_branch ?>&move=<?= $img['id'] ?>&dir=up&csrf=<?= urlencode($_SESSION['csrf_token']) ?>">↑</a>
                    <a href="?branch=<?= $active_branch ?>&move=<?= $img['id'] ?>&dir=down&csrf=<?= urlencode($_SESSION['csrf_token']) ?>">↓</a>
                    <a href="?branch=<?= $active_branch ?>&delete=<?= $img['id'] ?>&csrf=<?= urlencode($_SESSION['csrf_token']) ?>" class="del"
                       onclick="return confirm('Delete this image?')">✕</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Lightbox -->
    <div class="lb" id="lb" onclick="if(event.target===this)closeLb()">
        <button class="lb-close" onclick="closeLb()">&#10005;</button>
        <button class="lb-prev" onclick="shift(-1)">&#8249;</button>
        <img id="lb-img" src="" alt="">
        <button class="lb-next" onclick="shift(1)">&#8250;</button>
        <span class="lb-counter" id="lb-counter"></span>
    </div>
    <script>
    var files = <?= json_encode(array_column($active_imgs,'filename')) ?>;
    var cur = 0;
    function lb(i){ cur=i; show(); document.getElementById('lb').classList.add('open'); document.body.style.overflow='hidden'; }
    function closeLb(){ document.getElementById('lb').classList.remove('open'); document.body.style.overflow=''; }
    function shift(d){ cur=(cur+d+files.length)%files.length; show(); }
    function show(){
        document.getElementById('lb-img').src='/public/images/menu/'+files[cur];
        document.getElementById('lb-counter').textContent=(cur+1)+' / '+files.length;
    }
    document.addEventListener('keydown',function(e){
        if(e.key==='Escape')closeLb();
        if(e.key==='ArrowRight')shift(1);
        if(e.key==='ArrowLeft')shift(-1);
    });
    </script>
    <?php endif; ?>
</main>
</body>
</html>
