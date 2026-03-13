<?php
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

// Brute-force koruması — 5 başarısız deneme → 15 dk kilit
if (!isset($_SESSION['login_attempts']))    $_SESSION['login_attempts']    = 0;
if (!isset($_SESSION['login_locked_until'])) $_SESSION['login_locked_until'] = 0;

$locked           = time() < $_SESSION['login_locked_until'];
$lockout_remaining = max(0, $_SESSION['login_locked_until'] - time());

// CSRF token üret
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    // CSRF doğrula
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username && $password) {
            $pdo  = getPDO();
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in']   = true;
                $_SESSION['admin_user']        = $username;
                $_SESSION['login_attempts']    = 0;
                $_SESSION['login_locked_until'] = 0;
                header('Location: /admin/');
                exit;
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_locked_until'] = time() + 900;
                    $error = 'Too many failed attempts. Try again in 15 minutes.';
                } else {
                    $left  = 5 - $_SESSION['login_attempts'];
                    $error = 'Invalid username or password.' . ($left > 0 ? ' (' . $left . ' attempts left)' : '');
                }
            }
        } else {
            $error = 'Please fill in all fields.';
        }
    }
} elseif ($locked) {
    $error = 'Account locked. Try again in ' . ceil($lockout_remaining / 60) . ' minute(s).';
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Royal Turkish Cuisine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { background:#0A0A0A; font-family:'Inter',sans-serif; }
    </style>
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div style="width:100%;max-width:400px;padding:24px;">
        <!-- Logo -->
        <div style="text-align:center;margin-bottom:40px;">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;color:#B8860B;letter-spacing:0.1em;margin:0;">ROYAL</h1>
            <p style="color:#52525b;font-size:0.8rem;letter-spacing:0.15em;margin-top:4px;">ADMIN PANEL</p>
        </div>

        <div style="background:#111111;border:1px solid #2A2A2A;padding:40px 32px;">
            <?php if ($error): ?>
            <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:12px 16px;margin-bottom:24px;font-size:0.85rem;">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div style="margin-bottom:20px;">
                    <label style="display:block;color:#a1a1aa;font-size:0.75rem;letter-spacing:0.1em;margin-bottom:8px;">USERNAME</label>
                    <input type="text" name="username" required autocomplete="username"
                           style="width:100%;background:#0A0A0A;border:1px solid #2A2A2A;color:white;padding:10px 14px;font-size:0.9rem;outline:none;font-family:'Inter',sans-serif;"
                           onfocus="this.style.borderColor='#B8860B'" onblur="this.style.borderColor='#2A2A2A'">
                </div>
                <div style="margin-bottom:28px;">
                    <label style="display:block;color:#a1a1aa;font-size:0.75rem;letter-spacing:0.1em;margin-bottom:8px;">PASSWORD</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           style="width:100%;background:#0A0A0A;border:1px solid #2A2A2A;color:white;padding:10px 14px;font-size:0.9rem;outline:none;font-family:'Inter',sans-serif;"
                           onfocus="this.style.borderColor='#B8860B'" onblur="this.style.borderColor='#2A2A2A'">
                </div>
                <button type="submit" <?= $locked ? 'disabled' : '' ?>
                        style="width:100%;background:<?= $locked ? '#444' : '#B8860B' ?>;color:#000;border:none;padding:12px;font-size:0.85rem;letter-spacing:0.1em;cursor:<?= $locked ? 'not-allowed' : 'pointer' ?>;font-family:'Inter',sans-serif;font-weight:500;">
                    SIGN IN
                </button>
            </form>
        </div>
    </div>
</body>
</html>
