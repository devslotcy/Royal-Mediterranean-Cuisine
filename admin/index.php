<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

// Handle logout (CSRF korumalı)
if (isset($_GET['logout'])) {
    if (!isset($_GET['csrf']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'] ?? '')) {
        http_response_code(403); die('Forbidden.');
    }
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pdo = getPDO();

// Get stats
$item_count = $pdo->query("SELECT COUNT(*) FROM menu_items WHERE is_active = 1")->fetchColumn();
$cat_count = $pdo->query("SELECT COUNT(*) FROM menu_categories")->fetchColumn();
$content_count = $pdo->query("SELECT COUNT(*) FROM content_blocks")->fetchColumn();

// Traffic — filtre
$range = $_GET['range'] ?? '7';
$range = in_array($range, ['1','7','30','90']) ? (int)$range : 7;

// Toplam ziyaret (seçili dönem)
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM site_visits WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY)");
$total_stmt->execute([$range]);
$total_visits = $total_stmt->fetchColumn();

// Bugün
$today = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE DATE(visited_at) = CURDATE()")->fetchColumn();

// Dün
$yesterday = $pdo->query("SELECT COUNT(*) FROM site_visits WHERE DATE(visited_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)")->fetchColumn();

// Günlük grafik verisi (son N gün)
$daily_stmt = $pdo->prepare("
    SELECT DATE(visited_at) as day, COUNT(*) as cnt
    FROM site_visits
    WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    GROUP BY DATE(visited_at)
    ORDER BY day ASC
");
$daily_stmt->execute([$range]);
$daily_data = $daily_stmt->fetchAll();

// Sayfa dağılımı
$pages_stmt = $pdo->prepare("
    SELECT page, branch, COUNT(*) as cnt
    FROM site_visits
    WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    GROUP BY page, branch
    ORDER BY cnt DESC
");
$pages_stmt->execute([$range]);
$pages_data = $pages_stmt->fetchAll();

// Dil dağılımı
$lang_stmt = $pdo->prepare("
    SELECT lang, COUNT(*) as cnt
    FROM site_visits
    WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    GROUP BY lang
    ORDER BY cnt DESC
");
$lang_stmt->execute([$range]);
$lang_data = $lang_stmt->fetchAll();

// Son ziyaretler
$recent_stmt = $pdo->prepare("SELECT * FROM site_visits ORDER BY visited_at DESC LIMIT 50");
$recent_stmt->execute();
$recent = $recent_stmt->fetchAll();

// Grafik için JS array
$graph_labels = [];
$graph_values = [];
$daily_map = [];
foreach ($daily_data as $row) {
    $daily_map[$row['day']] = (int)$row['cnt'];
}
for ($i = $range - 1; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $graph_labels[] = date('M d', strtotime($d));
    $graph_values[] = $daily_map[$d] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Royal Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0A0A0A; color: white; display: flex; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        .sidebar { width: 220px; background: #111111; border-right: 1px solid #2A2A2A; flex-shrink: 0; padding: 24px 0; }
        .sidebar-logo { padding: 0 20px 24px; border-bottom: 1px solid #2A2A2A; margin-bottom: 16px; }
        .sidebar-logo h1 { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: #B8860B; }
        .sidebar-logo p { color: #52525b; font-size: 0.7rem; margin-top: 4px; }
        nav { padding: 0 12px; }
        nav a { display: block; padding: 10px 12px; color: #a1a1aa; font-size: 0.85rem; margin-bottom: 4px; border-radius: 4px; transition: color 0.2s; }
        nav a:hover, nav a.active { color: #B8860B; background: rgba(184,134,11,0.1); }
        nav a.logout { color: #ef4444; margin-top: 16px; }
        main { flex: 1; padding: 40px; overflow-y: auto; }
        .stat-card { background: #111111; border: 1px solid #2A2A2A; padding: 24px; }
        .stat-label { color: #52525b; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 8px; text-transform: uppercase; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: #B8860B; }
        .stat-sub { color: #52525b; font-size: 0.75rem; margin-top: 6px; }
        .section-title { color: #a1a1aa; font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 16px; }
        .divider { border: none; border-top: 1px solid #2A2A2A; margin: 40px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        th { color: #52525b; font-size: 0.7rem; letter-spacing: 0.1em; text-transform: uppercase; padding: 10px 12px; border-bottom: 1px solid #2A2A2A; text-align: left; font-weight: 400; }
        td { padding: 10px 12px; border-bottom: 1px solid #161616; color: #a1a1aa; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 0.7rem; letter-spacing: 0.05em; background: rgba(184,134,11,0.12); color: #B8860B; border: 1px solid rgba(184,134,11,0.2); }
        .bar-wrap { background: #1a1a1a; height: 6px; border-radius: 3px; margin-top: 4px; }
        .bar-fill { background: #B8860B; height: 6px; border-radius: 3px; }
        .range-btn { padding: 6px 14px; font-size: 0.75rem; border: 1px solid #2A2A2A; color: #a1a1aa; background: transparent; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .range-btn.active, .range-btn:hover { border-color: #B8860B; color: #B8860B; }
        canvas { width: 100% !important; }
        @media (max-width: 768px) {
            body { flex-direction: column !important; }
            .sidebar { width: 100% !important; border-right: none !important; border-bottom: 1px solid #2A2A2A; padding: 12px 0 !important; }
            .sidebar-logo { padding: 8px 16px 12px !important; }
            nav { display: flex; flex-wrap: wrap; padding: 0 8px !important; }
            nav a { display: inline-block !important; padding: 6px 10px !important; font-size: 0.75rem !important; margin-bottom: 2px; }
            main { padding: 20px 16px !important; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>ROYAL</h1>
            <p>Admin Panel</p>
        </div>
        <nav>
            <a href="/admin/" class="active">Dashboard</a>
            <a href="/admin/menu-images.php">Menu Images</a>
            <a href="/admin/content.php">Content</a>
            <a href="/admin/?logout=1&csrf=<?= urlencode($_SESSION['csrf_token']) ?>" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:4px;">Dashboard</h2>
        <p style="color:#52525b;font-size:0.85rem;margin-bottom:36px;">Welcome, <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></p>

        <!-- Site Stats -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:32px;">
            <div class="stat-card">
                <div class="stat-label">Menu Items</div>
                <div class="stat-value"><?= $item_count ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Categories</div>
                <div class="stat-value"><?= $cat_count ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Content Blocks</div>
                <div class="stat-value"><?= $content_count ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:48px;">
            <a href="/admin/menu-images.php" style="padding:12px 24px;background:#B8860B;color:#000;text-decoration:none;font-size:0.85rem;letter-spacing:0.05em;">Manage Menus</a>
            <a href="/admin/content.php" style="padding:12px 24px;border:1px solid #2A2A2A;color:#a1a1aa;text-decoration:none;font-size:0.85rem;">Manage Content</a>
            <a href="/en" target="_blank" style="padding:12px 24px;border:1px solid #2A2A2A;color:#a1a1aa;text-decoration:none;font-size:0.85rem;">View Site &#8594;</a>
        </div>

        <hr class="divider">

        <!-- Traffic Section -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px;">
            <div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin-bottom:4px;">Site Traffic</h3>
                <p style="color:#52525b;font-size:0.85rem;">Visitor analytics</p>
            </div>
            <div style="display:flex;gap:8px;">
                <?php foreach ([1=>'Today',7=>'7 Days',30=>'30 Days',90=>'90 Days'] as $val=>$label): ?>
                <a href="?range=<?= $val ?>" class="range-btn <?= $range==$val?'active':'' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Traffic Stats -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:40px;">
            <div class="stat-card">
                <div class="stat-label">Total (<?= $range ?>d)</div>
                <div class="stat-value"><?= number_format($total_visits) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Today</div>
                <div class="stat-value"><?= number_format($today) ?></div>
                <?php if ($yesterday > 0): ?>
                <div class="stat-sub"><?= $today >= $yesterday ? '+' : '' ?><?= round(($today - $yesterday) / $yesterday * 100) ?>% vs yesterday</div>
                <?php endif; ?>
            </div>
            <div class="stat-card">
                <div class="stat-label">Yesterday</div>
                <div class="stat-value"><?= number_format($yesterday) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg / Day</div>
                <div class="stat-value"><?= $range > 0 ? round($total_visits / $range) : 0 ?></div>
            </div>
        </div>

        <!-- Chart -->
        <div style="background:#111111;border:1px solid #2A2A2A;padding:28px;margin-bottom:32px;">
            <p class="section-title">Daily Visits</p>
            <canvas id="chart" height="80"></canvas>
        </div>

        <!-- Pages & Languages side by side -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-bottom:32px;">

            <!-- Page breakdown -->
            <div style="background:#111111;border:1px solid #2A2A2A;padding:24px;">
                <p class="section-title">By Page</p>
                <?php
                $max_p = max(array_column($pages_data, 'cnt') ?: [1]);
                foreach ($pages_data as $row):
                    $label = $row['page'];
                    if ($row['branch']) $label .= ' / ' . $row['branch'];
                ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:0.82rem;color:white;text-transform:capitalize;"><?= htmlspecialchars($label) ?></span>
                        <span style="font-size:0.82rem;color:#B8860B;font-weight:500;"><?= number_format($row['cnt']) ?></span>
                    </div>
                    <div class="bar-wrap"><div class="bar-fill" style="width:<?= round($row['cnt']/$max_p*100) ?>%"></div></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($pages_data)): ?><p style="color:#52525b;font-size:0.82rem;">No data yet.</p><?php endif; ?>
            </div>

            <!-- Language breakdown -->
            <div style="background:#111111;border:1px solid #2A2A2A;padding:24px;">
                <p class="section-title">By Language</p>
                <?php
                $max_l = max(array_column($lang_data, 'cnt') ?: [1]);
                $lang_names = ['en'=>'English','tr'=>'Turkish','ar'=>'Arabic','th'=>'Thai','de'=>'German','fr'=>'French','it'=>'Italian'];
                foreach ($lang_data as $row):
                ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:0.82rem;color:white;"><?= htmlspecialchars($lang_names[$row['lang']] ?? strtoupper($row['lang'])) ?></span>
                        <span style="font-size:0.82rem;color:#B8860B;font-weight:500;"><?= number_format($row['cnt']) ?></span>
                    </div>
                    <div class="bar-wrap"><div class="bar-fill" style="width:<?= round($row['cnt']/$max_l*100) ?>%"></div></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($lang_data)): ?><p style="color:#52525b;font-size:0.82rem;">No data yet.</p><?php endif; ?>
            </div>
        </div>

        <!-- Recent visits table -->
        <div style="background:#111111;border:1px solid #2A2A2A;padding:24px;">
            <p class="section-title" style="margin-bottom:20px;">Recent Visits (last 50)</p>
            <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Page</th>
                        <th>Lang</th>
                        <th>IP</th>
                        <th>Referer</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent as $v): ?>
                <tr>
                    <td style="white-space:nowrap;color:#71717a;"><?= htmlspecialchars(date('M d, H:i', strtotime($v['visited_at']))) ?></td>
                    <td>
                        <span style="color:white;"><?= htmlspecialchars($v['page']) ?></span>
                        <?php if ($v['branch']): ?><span style="color:#52525b;"> / <?= htmlspecialchars($v['branch']) ?></span><?php endif; ?>
                    </td>
                    <td><span class="badge"><?= strtoupper(htmlspecialchars($v['lang'])) ?></span></td>
                    <td style="color:#52525b;font-size:0.78rem;"><?= htmlspecialchars($v['ip']) ?></td>
                    <td style="color:#52525b;font-size:0.78rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($v['referer'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?>
                <tr><td colspan="5" style="text-align:center;color:#52525b;padding:40px;">No visits recorded yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </main>

    <script>
    (function(){
        var canvas = document.getElementById('chart');
        var ctx = canvas.getContext('2d');
        var labels = <?= json_encode($graph_labels) ?>;
        var values = <?= json_encode($graph_values) ?>;

        function draw() {
            var W = canvas.parentElement.clientWidth - 56;
            var H = 160;
            canvas.width = W;
            canvas.height = H;

            var max = Math.max.apply(null, values.concat([1]));
            var pad = { top: 20, bottom: 30, left: 40, right: 10 };
            var chartW = W - pad.left - pad.right;
            var chartH = H - pad.top - pad.bottom;
            var step = chartW / (labels.length - 1 || 1);

            ctx.clearRect(0, 0, W, H);

            // Grid lines
            ctx.strokeStyle = '#1e1e1e';
            ctx.lineWidth = 1;
            for (var g = 0; g <= 4; g++) {
                var gy = pad.top + chartH * (1 - g / 4);
                ctx.beginPath(); ctx.moveTo(pad.left, gy); ctx.lineTo(W - pad.right, gy); ctx.stroke();
                ctx.fillStyle = '#3f3f46';
                ctx.font = '10px Inter';
                ctx.textAlign = 'right';
                ctx.fillText(Math.round(max * g / 4), pad.left - 6, gy + 4);
            }

            // Area fill
            var grad = ctx.createLinearGradient(0, pad.top, 0, pad.top + chartH);
            grad.addColorStop(0, 'rgba(184,134,11,0.25)');
            grad.addColorStop(1, 'rgba(184,134,11,0)');
            ctx.beginPath();
            for (var i = 0; i < values.length; i++) {
                var x = pad.left + i * step;
                var y = pad.top + chartH * (1 - values[i] / max);
                if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
            }
            ctx.lineTo(pad.left + (values.length - 1) * step, pad.top + chartH);
            ctx.lineTo(pad.left, pad.top + chartH);
            ctx.closePath();
            ctx.fillStyle = grad;
            ctx.fill();

            // Line
            ctx.beginPath();
            ctx.strokeStyle = '#B8860B';
            ctx.lineWidth = 2;
            for (var i = 0; i < values.length; i++) {
                var x = pad.left + i * step;
                var y = pad.top + chartH * (1 - values[i] / max);
                if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
            }
            ctx.stroke();

            // Dots + labels
            var skip = Math.ceil(labels.length / 10);
            for (var i = 0; i < values.length; i++) {
                var x = pad.left + i * step;
                var y = pad.top + chartH * (1 - values[i] / max);
                ctx.beginPath();
                ctx.arc(x, y, 3, 0, Math.PI * 2);
                ctx.fillStyle = '#B8860B';
                ctx.fill();
                if (i % skip === 0 || i === values.length - 1) {
                    ctx.fillStyle = '#52525b';
                    ctx.font = '10px Inter';
                    ctx.textAlign = 'center';
                    ctx.fillText(labels[i], x, H - 6);
                }
            }
        }

        draw();
        window.addEventListener('resize', draw);
    })();
    </script>
</body>
</html>
