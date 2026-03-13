<?php
// About page — content blocks DB'den çekilir
$pdo_about = getPDO();
$cb_rows_about = $pdo_about->query("SELECT block_key, value_{$lang_code} as val, value_en as fallback FROM content_blocks")->fetchAll();
$cb_about = [];
foreach ($cb_rows_about as $r) {
    $cb_about[$r['block_key']] = $r['val'] ?: $r['fallback'];
}
?>

<style>
.about-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: start; }
@media (max-width: 768px) {
    .about-two-col { grid-template-columns: 1fr !important; gap: 40px; }
    .about-hero { padding: 60px 16px 40px !important; }
    .about-content { padding: 24px 16px 60px !important; }
}
</style>

<!-- Hero -->
<section class="about-hero" style="padding:100px 24px 60px;background:#0A0A0A;text-align:center;">
    <p style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.3em;color:#B8860B;margin-bottom:16px;text-transform:uppercase;"><?= htmlspecialchars($cb_about['about_small'] ?? 'ROYAL MEDITERRANEAN CUISINE') ?></p>
    <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2.5rem,5vw,4rem);color:white;margin:0 0 20px 0;"><?= htmlspecialchars($cb_about['story_title'] ?? '') ?></h1>
    <div class="gold-line"></div>
</section>

<!-- Content — Our Story layout -->
<section class="about-content" style="padding:40px 24px 100px;background:#0A0A0A;">
    <div style="max-width:1200px;margin:0 auto;">
        <div class="about-two-col">

            <!-- Left: Text -->
            <div>
                <p style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.3em;color:#B8860B;margin:0 0 12px;text-transform:uppercase;"><?= htmlspecialchars($cb_about['story_label'] ?? 'ROYAL MEDITERRANEAN CUISINE') ?></p>
                <div style="width:50px;height:1px;background:#B8860B;margin-bottom:32px;"></div>

                <p style="color:white;font-size:0.95rem;line-height:1.9;margin-bottom:20px;">
                    <?= htmlspecialchars($cb_about['story_p1'] ?? '') ?>
                </p>
                <p style="color:#52525b;font-size:0.95rem;line-height:1.9;margin-bottom:36px;">
                    <?= htmlspecialchars($cb_about['story_p2'] ?? '') ?>
                </p>

            </div>

            <!-- Right: Branch Cards -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card-hover" style="background:#111111;padding:32px;border-radius:2px;">
                    <p style="font-size:0.65rem;letter-spacing:0.2em;color:#B8860B;margin:0 0 10px 0;">BRANCH 01</p>
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;color:white;margin:0 0 12px 0;">Chaweng Beach</h3>
                    <p style="color:#52525b;font-size:0.85rem;line-height:1.7;margin:0 0 20px 0;"><?= CHAWENG_ADDRESS ?></p>
                    <a href="<?= url($lang_code, 'menu', 'chaweng') ?>" style="font-size:0.75rem;color:#B8860B;text-decoration:none;letter-spacing:0.12em;">VIEW MENU &#8594;</a>
                </div>
                <div class="card-hover" style="background:#111111;padding:32px;border-radius:2px;">
                    <p style="font-size:0.65rem;letter-spacing:0.2em;color:#B8860B;margin:0 0 10px 0;">BRANCH 02</p>
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;color:white;margin:0 0 12px 0;">Lamai Beach</h3>
                    <p style="color:#52525b;font-size:0.85rem;line-height:1.7;margin:0 0 20px 0;"><?= LAMAI_ADDRESS ?></p>
                    <a href="<?= url($lang_code, 'menu', 'lamai') ?>" style="font-size:0.75rem;color:#B8860B;text-decoration:none;letter-spacing:0.12em;">VIEW MENU &#8594;</a>
                </div>
            </div>

        </div>
    </div>
</section>
