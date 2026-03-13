<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();
$message = '';
$error = '';

// ─── POST ACTIONS ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD CATEGORY
    if ($action === 'add_category') {
        $branch_id = (int)($_POST['branch_id'] ?? 0);
        $name_en   = trim($_POST['name_en'] ?? '');
        $name_tr   = trim($_POST['name_tr'] ?? '');
        $sort      = (int)($_POST['sort_order'] ?? 0);
        if ($branch_id && $name_en) {
            $stmt = $pdo->prepare("INSERT INTO menu_categories (branch_id, name_en, name_tr, sort_order) VALUES (?,?,?,?)");
            $stmt->execute([$branch_id, $name_en, $name_tr, $sort]);
            $message = 'Category added successfully.';
        } else {
            $error = 'Branch and English name are required.';
        }
    }

    // EDIT CATEGORY
    elseif ($action === 'edit_category') {
        $id      = (int)($_POST['id'] ?? 0);
        $name_en = trim($_POST['name_en'] ?? '');
        $name_tr = trim($_POST['name_tr'] ?? '');
        $sort    = (int)($_POST['sort_order'] ?? 0);
        if ($id && $name_en) {
            $stmt = $pdo->prepare("UPDATE menu_categories SET name_en=?, name_tr=?, sort_order=? WHERE id=?");
            $stmt->execute([$name_en, $name_tr, $sort, $id]);
            $message = 'Category updated.';
        } else {
            $error = 'ID and English name are required.';
        }
    }

    // DELETE CATEGORY
    elseif ($action === 'delete_category') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM menu_items WHERE category_id=?")->execute([$id]);
            $pdo->prepare("DELETE FROM menu_categories WHERE id=?")->execute([$id]);
            $message = 'Category and its items deleted.';
        }
    }

    // ADD ITEM
    elseif ($action === 'add_item') {
        $cat_id      = (int)($_POST['category_id'] ?? 0);
        $name_en     = trim($_POST['name_en'] ?? '');
        $name_tr     = trim($_POST['name_tr'] ?? '');
        $desc_en     = trim($_POST['description_en'] ?? '');
        $desc_tr     = trim($_POST['description_tr'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $sort        = (int)($_POST['sort_order'] ?? 0);
        $is_active   = isset($_POST['is_active']) ? 1 : 0;
        if ($cat_id && $name_en && $price > 0) {
            $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name_en, name_tr, description_en, description_tr, price, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$cat_id, $name_en, $name_tr, $desc_en, $desc_tr, $price, $sort, $is_active]);
            $message = 'Menu item added.';
        } else {
            $error = 'Category, English name, and price are required.';
        }
    }

    // EDIT ITEM
    elseif ($action === 'edit_item') {
        $id          = (int)($_POST['id'] ?? 0);
        $name_en     = trim($_POST['name_en'] ?? '');
        $name_tr     = trim($_POST['name_tr'] ?? '');
        $desc_en     = trim($_POST['description_en'] ?? '');
        $desc_tr     = trim($_POST['description_tr'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $sort        = (int)($_POST['sort_order'] ?? 0);
        $is_active   = isset($_POST['is_active']) ? 1 : 0;
        if ($id && $name_en && $price > 0) {
            $stmt = $pdo->prepare("UPDATE menu_items SET name_en=?, name_tr=?, description_en=?, description_tr=?, price=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$name_en, $name_tr, $desc_en, $desc_tr, $price, $sort, $is_active, $id]);
            $message = 'Menu item updated.';
        } else {
            $error = 'ID, English name, and price are required.';
        }
    }

    // DELETE ITEM
    elseif ($action === 'delete_item') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM menu_items WHERE id=?")->execute([$id]);
            $message = 'Item deleted.';
        }
    }

    header('Location: /admin/menus.php' . ($message ? '?msg=' . urlencode($message) : ($error ? '?err=' . urlencode($error) : '')));
    exit;
}

if (isset($_GET['msg'])) $message = htmlspecialchars($_GET['msg']);
if (isset($_GET['err'])) $error   = htmlspecialchars($_GET['err']);

// ─── FETCH DATA ─────────────────────────────────────────────────────────────
$branches = $pdo->query("SELECT * FROM branches ORDER BY id")->fetchAll();
$branch_map = [];
foreach ($branches as $b) $branch_map[$b['id']] = $b;

$categories = $pdo->query("SELECT mc.*, b.name as branch_name FROM menu_categories mc JOIN branches b ON mc.branch_id = b.id ORDER BY mc.branch_id, mc.sort_order")->fetchAll();

$items_by_cat = [];
$all_items = $pdo->query("SELECT * FROM menu_items ORDER BY category_id, sort_order")->fetchAll();
foreach ($all_items as $item) {
    $items_by_cat[$item['category_id']][] = $item;
}

// Edit modal data
$edit_cat  = null;
$edit_item = null;
if (isset($_GET['edit_cat'])) {
    $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE id=?");
    $stmt->execute([(int)$_GET['edit_cat']]);
    $edit_cat = $stmt->fetch();
}
if (isset($_GET['edit_item'])) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id=?");
    $stmt->execute([(int)$_GET['edit_item']]);
    $edit_item = $stmt->fetch();
}

$input_style = "width:100%;background:#0A0A0A;border:1px solid #2A2A2A;color:white;padding:8px 12px;font-size:0.85rem;outline:none;font-family:'Inter',sans-serif;";
$label_style = "display:block;color:#a1a1aa;font-size:0.72rem;letter-spacing:0.08em;margin-bottom:6px;";
$btn_gold    = "padding:8px 18px;background:#B8860B;color:#000;border:none;cursor:pointer;font-size:0.8rem;letter-spacing:0.05em;font-family:'Inter',sans-serif;";
$btn_danger  = "padding:6px 12px;background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);cursor:pointer;font-size:0.75rem;font-family:'Inter',sans-serif;";
$btn_outline = "padding:6px 12px;background:transparent;color:#a1a1aa;border:1px solid #2A2A2A;cursor:pointer;font-size:0.75rem;font-family:'Inter',sans-serif;text-decoration:none;display:inline-block;";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Royal Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:#0A0A0A; color:white; }
        input, select, textarea { color-scheme: dark; }
        input:focus, select:focus, textarea:focus { border-color:#B8860B !important; outline:none; }
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:200; align-items:center; justify-content:center; }
        .modal-overlay.active { display:flex; }
    </style>
</head>
<body style="display:flex;min-height:100vh;">

    <!-- Sidebar -->
    <aside style="width:220px;background:#111111;border-right:1px solid #2A2A2A;flex-shrink:0;padding:24px 0;">
        <div style="padding:0 20px 24px;border-bottom:1px solid #2A2A2A;margin-bottom:16px;">
            <h1 style="font-family:'Playfair Display',serif;font-size:1.2rem;color:#B8860B;margin:0;">ROYAL</h1>
            <p style="color:#52525b;font-size:0.7rem;margin:4px 0 0;">Admin Panel</p>
        </div>
        <nav style="padding:0 12px;">
            <a href="/admin/" style="display:block;padding:10px 12px;color:#a1a1aa;text-decoration:none;font-size:0.85rem;margin-bottom:4px;border-radius:4px;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#a1a1aa'">Dashboard</a>
            <a href="/admin/menus.php" style="display:block;padding:10px 12px;color:#B8860B;background:rgba(184,134,11,0.1);border-radius:4px;text-decoration:none;font-size:0.85rem;margin-bottom:4px;">Menu Management</a>
            <a href="/admin/menu-images.php" style="display:block;padding:10px 12px;color:#a1a1aa;text-decoration:none;font-size:0.85rem;margin-bottom:4px;border-radius:4px;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#a1a1aa'">Menu Images</a>
            <a href="/admin/content.php" style="display:block;padding:10px 12px;color:#a1a1aa;text-decoration:none;font-size:0.85rem;margin-bottom:4px;border-radius:4px;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#a1a1aa'">Content</a>
            <a href="/admin/?logout=1" style="display:block;padding:10px 12px;color:#ef4444;text-decoration:none;font-size:0.85rem;margin-top:16px;border-radius:4px;">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main style="flex:1;padding:40px;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;flex-wrap:wrap;gap:16px;">
            <div>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin:0 0 4px;">Menu Management</h2>
                <p style="color:#52525b;font-size:0.85rem;margin:0;">Manage categories and items for both branches</p>
            </div>
            <div style="display:flex;gap:12px;">
                <button onclick="openModal('add-cat-modal')" style="<?= $btn_gold ?>">+ Add Category</button>
                <button onclick="openModal('add-item-modal')" style="<?= $btn_gold ?>">+ Add Item</button>
            </div>
        </div>

        <?php if ($message): ?>
        <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;padding:12px 16px;margin-bottom:24px;font-size:0.85rem;"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:12px 16px;margin-bottom:24px;font-size:0.85rem;"><?= $error ?></div>
        <?php endif; ?>

        <!-- Categories & Items by Branch -->
        <?php foreach ($branches as $branch): ?>
        <div style="margin-bottom:48px;">
            <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;color:#B8860B;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid #2A2A2A;">
                <?= htmlspecialchars($branch['name']) ?> Branch
            </h3>

            <?php $branch_cats = array_filter($categories, fn($c) => $c['branch_id'] == $branch['id']); ?>
            <?php if (empty($branch_cats)): ?>
            <p style="color:#52525b;font-size:0.85rem;">No categories yet. Add one above.</p>
            <?php else: ?>
            <?php foreach ($branch_cats as $cat): ?>
            <div style="background:#111111;border:1px solid #2A2A2A;margin-bottom:16px;">
                <!-- Category header row -->
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #1a1a1a;">
                    <div>
                        <span style="font-weight:600;color:white;font-size:0.9rem;"><?= htmlspecialchars($cat['name_en']) ?></span>
                        <?php if ($cat['name_tr']): ?>
                        <span style="color:#52525b;font-size:0.8rem;margin-left:8px;">/ <?= htmlspecialchars($cat['name_tr']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <a href="?edit_cat=<?= $cat['id'] ?>" style="<?= $btn_outline ?>">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category and all its items?');">
                            <input type="hidden" name="action" value="delete_category">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <button type="submit" style="<?= $btn_danger ?>">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Items table -->
                <?php $items = $items_by_cat[$cat['id']] ?? []; ?>
                <?php if (!empty($items)): ?>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #1a1a1a;">
                            <th style="padding:10px 20px;text-align:left;color:#52525b;font-size:0.72rem;letter-spacing:0.08em;font-weight:500;">NAME (EN / TR)</th>
                            <th style="padding:10px 20px;text-align:left;color:#52525b;font-size:0.72rem;letter-spacing:0.08em;font-weight:500;">PRICE</th>
                            <th style="padding:10px 20px;text-align:left;color:#52525b;font-size:0.72rem;letter-spacing:0.08em;font-weight:500;">STATUS</th>
                            <th style="padding:10px 20px;text-align:right;color:#52525b;font-size:0.72rem;letter-spacing:0.08em;font-weight:500;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr style="border-bottom:1px solid #0f0f0f;">
                            <td style="padding:10px 20px;">
                                <div style="color:white;font-size:0.85rem;"><?= htmlspecialchars($item['name_en']) ?></div>
                                <?php if ($item['name_tr']): ?><div style="color:#52525b;font-size:0.75rem;"><?= htmlspecialchars($item['name_tr']) ?></div><?php endif; ?>
                            </td>
                            <td style="padding:10px 20px;color:#B8860B;font-size:0.85rem;"><?= number_format((float)$item['price'], 0) ?> &#3647;</td>
                            <td style="padding:10px 20px;">
                                <span style="font-size:0.72rem;padding:3px 8px;border-radius:2px;<?= $item['is_active'] ? 'background:rgba(34,197,94,0.1);color:#86efac;' : 'background:rgba(239,68,68,0.1);color:#f87171;' ?>">
                                    <?= $item['is_active'] ? 'Active' : 'Hidden' ?>
                                </span>
                            </td>
                            <td style="padding:10px 20px;text-align:right;">
                                <a href="?edit_item=<?= $item['id'] ?>" style="<?= $btn_outline ?>margin-right:6px;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this item?');">
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" style="<?= $btn_danger ?>">Del</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="padding:16px 20px;color:#52525b;font-size:0.82rem;margin:0;">No items in this category.</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </main>
</body>

<!-- ── MODALS ─────────────────────────────────────────────────────────────── -->

<!-- Add Category Modal -->
<div id="add-cat-modal" class="modal-overlay">
    <div style="background:#111111;border:1px solid #2A2A2A;padding:32px;width:100%;max-width:480px;position:relative;">
        <button onclick="closeModal('add-cat-modal')" style="position:absolute;top:12px;right:16px;background:none;border:none;color:#71717a;font-size:1.2rem;cursor:pointer;">&#10005;</button>
        <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin:0 0 24px;">Add Category</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_category">
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">BRANCH</label>
                <select name="branch_id" required style="<?= $input_style ?>">
                    <option value="">— Select Branch —</option>
                    <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">NAME (EN) *</label>
                    <input type="text" name="name_en" required style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">NAME (TR)</label>
                    <input type="text" name="name_tr" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:24px;">
                <label style="<?= $label_style ?>">SORT ORDER</label>
                <input type="number" name="sort_order" value="0" style="<?= $input_style ?>width:100px;">
            </div>
            <button type="submit" style="<?= $btn_gold ?>">Add Category</button>
        </form>
    </div>
</div>

<!-- Add Item Modal -->
<div id="add-item-modal" class="modal-overlay">
    <div style="background:#111111;border:1px solid #2A2A2A;padding:32px;width:100%;max-width:600px;position:relative;max-height:90vh;overflow-y:auto;">
        <button onclick="closeModal('add-item-modal')" style="position:absolute;top:12px;right:16px;background:none;border:none;color:#71717a;font-size:1.2rem;cursor:pointer;">&#10005;</button>
        <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin:0 0 24px;">Add Menu Item</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_item">
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">CATEGORY *</label>
                <select name="category_id" required style="<?= $input_style ?>">
                    <option value="">— Select Category —</option>
                    <?php foreach ($branches as $b): ?>
                    <optgroup label="<?= htmlspecialchars($b['name']) ?>">
                        <?php foreach ($categories as $c): if ($c['branch_id'] != $b['id']) continue; ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name_en']) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">NAME (EN) *</label>
                    <input type="text" name="name_en" required style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">NAME (TR)</label>
                    <input type="text" name="name_tr" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">DESCRIPTION (EN)</label>
                <textarea name="description_en" rows="2" style="<?= $input_style ?>resize:vertical;"></textarea>
            </div>
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">DESCRIPTION (TR)</label>
                <textarea name="description_tr" rows="2" style="<?= $input_style ?>resize:vertical;"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">PRICE (THB) *</label>
                    <input type="number" name="price" step="0.01" min="0" required style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">SORT ORDER</label>
                    <input type="number" name="sort_order" value="0" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="is_active" id="add_active" checked style="width:16px;height:16px;accent-color:#B8860B;">
                <label for="add_active" style="color:#a1a1aa;font-size:0.85rem;cursor:pointer;">Active (visible on menu)</label>
            </div>
            <button type="submit" style="<?= $btn_gold ?>">Add Item</button>
        </form>
    </div>
</div>

<!-- Edit Category Modal (shown if ?edit_cat= is set) -->
<?php if ($edit_cat): ?>
<div id="edit-cat-modal" class="modal-overlay active">
    <div style="background:#111111;border:1px solid #2A2A2A;padding:32px;width:100%;max-width:480px;position:relative;">
        <a href="/admin/menus.php" style="position:absolute;top:12px;right:16px;background:none;border:none;color:#71717a;font-size:1.2rem;cursor:pointer;text-decoration:none;">&#10005;</a>
        <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin:0 0 24px;">Edit Category</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_category">
            <input type="hidden" name="id" value="<?= $edit_cat['id'] ?>">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">NAME (EN) *</label>
                    <input type="text" name="name_en" required value="<?= htmlspecialchars($edit_cat['name_en']) ?>" style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">NAME (TR)</label>
                    <input type="text" name="name_tr" value="<?= htmlspecialchars($edit_cat['name_tr'] ?? '') ?>" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:24px;">
                <label style="<?= $label_style ?>">SORT ORDER</label>
                <input type="number" name="sort_order" value="<?= (int)$edit_cat['sort_order'] ?>" style="<?= $input_style ?>width:100px;">
            </div>
            <button type="submit" style="<?= $btn_gold ?>">Save Changes</button>
            <a href="/admin/menus.php" style="<?= $btn_outline ?>margin-left:10px;">Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Edit Item Modal (shown if ?edit_item= is set) -->
<?php if ($edit_item): ?>
<div id="edit-item-modal" class="modal-overlay active">
    <div style="background:#111111;border:1px solid #2A2A2A;padding:32px;width:100%;max-width:600px;position:relative;max-height:90vh;overflow-y:auto;">
        <a href="/admin/menus.php" style="position:absolute;top:12px;right:16px;background:none;border:none;color:#71717a;font-size:1.2rem;cursor:pointer;text-decoration:none;">&#10005;</a>
        <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin:0 0 24px;">Edit Menu Item</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_item">
            <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">NAME (EN) *</label>
                    <input type="text" name="name_en" required value="<?= htmlspecialchars($edit_item['name_en']) ?>" style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">NAME (TR)</label>
                    <input type="text" name="name_tr" value="<?= htmlspecialchars($edit_item['name_tr'] ?? '') ?>" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">DESCRIPTION (EN)</label>
                <textarea name="description_en" rows="2" style="<?= $input_style ?>resize:vertical;"><?= htmlspecialchars($edit_item['description_en'] ?? '') ?></textarea>
            </div>
            <div style="margin-bottom:16px;">
                <label style="<?= $label_style ?>">DESCRIPTION (TR)</label>
                <textarea name="description_tr" rows="2" style="<?= $input_style ?>resize:vertical;"><?= htmlspecialchars($edit_item['description_tr'] ?? '') ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="<?= $label_style ?>">PRICE (THB) *</label>
                    <input type="number" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($edit_item['price']) ?>" style="<?= $input_style ?>">
                </div>
                <div>
                    <label style="<?= $label_style ?>">SORT ORDER</label>
                    <input type="number" name="sort_order" value="<?= (int)$edit_item['sort_order'] ?>" style="<?= $input_style ?>">
                </div>
            </div>
            <div style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="is_active" id="edit_active" <?= $edit_item['is_active'] ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:#B8860B;">
                <label for="edit_active" style="color:#a1a1aa;font-size:0.85rem;cursor:pointer;">Active (visible on menu)</label>
            </div>
            <button type="submit" style="<?= $btn_gold ?>">Save Changes</button>
            <a href="/admin/menus.php" style="<?= $btn_outline ?>margin-left:10px;">Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});
</script>
</html>
