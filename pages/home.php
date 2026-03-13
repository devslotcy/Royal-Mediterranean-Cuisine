<?php
// Home page — content blocks DB'den çekilir
$pdo = getPDO();
$cb_rows = $pdo->query("SELECT block_key, value_{$lang_code} as val, value_en as fallback FROM content_blocks")->fetchAll();
$cb = [];
foreach ($cb_rows as $r) {
    $cb[$r['block_key']] = $r['val'] ?: $r['fallback'];
}
function cb($cb, $key) { return htmlspecialchars($cb[$key] ?? ''); }
?>

<style>
@keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(8px); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
.hero-anim { animation: fadeInUp 1s ease forwards; }
.hero-anim-2 { animation: fadeInUp 1s ease 0.2s both; }
.hero-anim-3 { animation: fadeInUp 1s ease 0.4s both; }
.hero-anim-4 { animation: fadeInUp 1s ease 0.6s both; }

.branch-card {
    background: #0e0e0e;
    border: 1px solid #1e1e1e;
    padding: 36px 32px;
    transition: border-color 0.3s;
}
.branch-card:hover { border-color: #B8860B; }

.taste-feature {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    padding: 20px 0;
    border-bottom: 1px solid #1a1a1a;
}
.taste-feature:last-child { border-bottom: none; }

.story-corner-box {
    position: relative;
    padding: 48px 40px;
    background: transparent;
}
.story-corner-box::before,
.story-corner-box::after,
.story-corner-box .corner-br,
.story-corner-box .corner-bl {
    content: '';
    position: absolute;
    width: 28px;
    height: 28px;
}
.story-corner-box::before  { top: 0; left: 0;  border-top: 1px solid #B8860B; border-left: 1px solid #B8860B; }
.story-corner-box::after   { top: 0; right: 0; border-top: 1px solid #B8860B; border-right: 1px solid #B8860B; }
.story-corner-box .corner-br { bottom: 0; right: 0; border-bottom: 1px solid #B8860B; border-right: 1px solid #B8860B; }
.story-corner-box .corner-bl { bottom: 0; left: 0;  border-bottom: 1px solid #B8860B; border-left: 1px solid #B8860B; }

/* ── Mobile ── */
@media (max-width: 768px) {
    .two-col { grid-template-columns: 1fr !important; gap: 40px !important; }
    .branches-grid { grid-template-columns: 1fr !important; gap: 16px !important; }
    .taste-img-col { display: none; }
    .hero-section { padding: 40px 16px !important; }
    .branches-section { padding: 60px 16px !important; }
    .branches-section .branch-map { height: 160px !important; }
    .taste-section { padding: 60px 16px !important; }
    .story-section { padding: 60px 16px !important; }
}

/* ── Tablet ── */
@media (max-width: 1024px) and (min-width: 769px) {
    .branches-grid { gap: 16px !important; }
    .two-col { gap: 40px !important; }
}

/* ── Apple Watch / very small (≤ 200px) ── */
@media (max-width: 200px) {
    .branches-grid, .two-col { grid-template-columns: 1fr !important; gap: 16px !important; }
    .taste-img-col { display: none; }
    .taste-feature { flex-direction: column; gap: 8px !important; padding: 10px 0 !important; }
    section { padding: 32px 8px !important; }
}

/* ── TV / 4K ── */
@media (min-width: 1800px) {
    section > div { max-width: 1600px !important; }
}
</style>

<!-- ═══════════════════════════════════════════
     SECTION 1 — HERO
═══════════════════════════════════════════ -->
<section class="hero-section" style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#0A0A0A;text-align:center;padding:40px 24px;position:relative;overflow:hidden;">

    <!-- subtle background texture lines -->
    <div style="position:absolute;inset:0;pointer-events:none;opacity:0.03;">
        <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:#B8860B;"></div>
        <div style="position:absolute;top:0;bottom:0;left:50%;width:1px;background:#B8860B;"></div>
    </div>

    <!-- top line decoration -->
    <div class="hero-anim" style="display:flex;align-items:center;gap:16px;margin-bottom:40px;margin-top:-30px;">
        <div style="width:40px;height:1px;background:#B8860B;opacity:0.6;"></div>
        <span style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.35em;color:#ffffff;text-transform:uppercase;"><?= cb($cb, 'hero_badge_label') ?></span>
        <div style="width:40px;height:1px;background:#B8860B;opacity:0.6;"></div>
    </div>

    <h1 class="hero-anim-2" style="font-family:'Playfair Display',serif;font-size:clamp(3rem,7vw,5.5rem);font-weight:700;color:#B8860B;margin:0;line-height:0.95;letter-spacing:0.02em;">ROYAL</h1>

    <h2 class="hero-anim-3" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,5vw,3.2rem);color:#B8860B;font-weight:400;margin:12px 0 28px 0;letter-spacing:0.05em;">
        <?= cb($cb, 'hero_subtitle') ?>
    </h2>

    <p class="hero-anim-3" style="color:#71717a;font-size:0.9rem;line-height:1.7;max-width:520px;margin:0 0 44px 0;">
        <?= cb($cb, 'hero_desc') ?>
    </p>

    <div class="hero-anim-4" style="display:flex;gap:16px;flex-wrap:wrap;justify-content:center;">
        <a href="<?= url($lang_code, 'menu', 'chaweng') ?>"
           style="padding:14px 36px;background:#B8860B;color:#000;text-decoration:none;font-size:0.78rem;letter-spacing:0.15em;font-family:'Inter',sans-serif;font-weight:600;transition:background 0.2s;text-transform:uppercase;"
           onmouseover="this.style.background='#9A7209'" onmouseout="this.style.background='#B8860B'">
            <?= cb($cb, 'hero_btn_menu') ?>
        </a>
        <a href="#branches"
           style="padding:14px 36px;border:1px solid #2A2A2A;color:white;text-decoration:none;font-size:0.78rem;letter-spacing:0.15em;font-family:'Inter',sans-serif;transition:border-color 0.2s;text-transform:uppercase;"
           onmouseover="this.style.borderColor='#B8860B';this.style.color='#B8860B'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='white'">
            <?= cb($cb, 'hero_btn_locations') ?>
        </a>
    </div>

    <!-- scroll indicator -->
    <div style="position:absolute;bottom:32px;left:50%;animation:bounce 2s infinite;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
            <div style="width:1px;height:40px;background:linear-gradient(to bottom,transparent,#B8860B);"></div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     SECTION 2 — OUR BRANCHES
═══════════════════════════════════════════ -->
<section id="branches" class="branches-section" style="padding:100px 24px;background:#050505;">
    <div style="max-width:1200px;margin:0 auto;">

        <!-- Section header -->
        <div style="text-align:center;margin-bottom:64px;">
            <p style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.3em;color:#B8860B;margin:0 0 14px;text-transform:uppercase;">KOH SAMUI, THAILAND</p>
            <h2 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,4vw,3.2rem);color:white;margin:0 0 16px;"><?= htmlspecialchars($lang['branches_title']) ?></h2>
            <div style="width:50px;height:1px;background:#B8860B;margin:0 auto;"></div>
        </div>

        <!-- Branch Cards — her birinde kendi haritası -->
        <div class="branches-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

            <!-- Chaweng -->
            <div class="branch-card" style="padding:0;overflow:hidden;">
                <!-- Map -->
                <div class="branch-map" style="width:100%;height:220px;overflow:hidden;position:relative;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3934.8522620339463!2d100.0573125!3d9.5215625!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3054f1001917c25f%3A0x1795066ab36cf535!2sROYAL%20Turkish%20Cuisine%20Chaweng!5e0!3m2!1sen!2sth!4v1773311388067!5m2!1sen!2sth"
                        width="100%" height="100%" style="border:0;filter:invert(85%) hue-rotate(180deg) saturate(0.8);display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <!-- Info -->
                <div style="padding:28px 32px 32px;">
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;color:#71717a;margin:0 0 2px;font-weight:400;">Royal Turkish Cuisine –</h3>
                    <h4 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:white;margin:0 0 20px;font-weight:600;">Chaweng</h4>

                    <div style="display:flex;flex-direction:column;gap:11px;margin-bottom:24px;">
                        <div style="display:flex;align-items:flex-start;gap:11px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;margin-top:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span style="color:#71717a;font-size:0.83rem;line-height:1.6;">Chaweng Beach Road, Bophut, Koh Samui, Surat Thani 84320, Thailand</span>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;margin-top:3px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.58 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l1.02-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="color:#71717a;font-size:0.83rem;">098 256 7595</span>
                                    <a href="<?= url('en', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">English</a>
                                    <span style="color:#B8860B;">·</span>
                                    <a href="<?= url('tr', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">Turkish</a>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="color:#71717a;font-size:0.83rem;">094 335 8904</span>
                                    <a href="<?= url('en', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">English</a>
                                    <span style="color:#B8860B;">·</span>
                                    <a href="<?= url('th', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">Thai</a>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span style="color:#B8860B;font-size:0.8rem;">Monday-Sunday:</span>
                            <span style="color:#71717a;font-size:0.8rem;">11:00–23:00</span>
                        </div>
                    </div>

                    <!-- Buttons: 50/50 -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                        <a href="<?= url($lang_code, 'menu', 'chaweng') ?>"
                           style="display:flex;align-items:center;justify-content:center;padding:13px 8px;border:1px solid #B8860B;color:#B8860B;text-decoration:none;font-size:0.72rem;letter-spacing:0.12em;font-family:'Inter',sans-serif;transition:all 0.2s;text-transform:uppercase;"
                           onmouseover="this.style.background='#B8860B';this.style.color='#000'" onmouseout="this.style.background='transparent';this.style.color='#B8860B'">
                            VIEW MENU
                        </a>
                        <a href="https://maps.app.goo.gl/WCPnF4hLMdbMzU1X7" target="_blank" rel="noopener"
                           style="display:flex;align-items:center;justify-content:center;padding:13px 8px;border:1px solid #B8860B;border-left:none;color:#B8860B;text-decoration:none;font-size:0.72rem;letter-spacing:0.12em;font-family:'Inter',sans-serif;transition:all 0.2s;text-transform:uppercase;"
                           onmouseover="this.style.background='#B8860B';this.style.color='#000'" onmouseout="this.style.background='transparent';this.style.color='#B8860B'">
                            GET DIRECTIONS
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lamai -->
            <div class="branch-card" style="padding:0;overflow:hidden;">
                <!-- Map -->
                <div class="branch-map" style="width:100%;height:220px;overflow:hidden;position:relative;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3935.5054616448338!2d100.03880027502575!3d9.46468798201689!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3054f38e03d1fa35%3A0xb1e19cfaebfb6407!2sRoyal%20Turkish%20Cuisine%20Lamai!5e0!3m2!1sen!2sth!4v1773311405376!5m2!1sen!2sth"
                        width="100%" height="100%" style="border:0;filter:invert(85%) hue-rotate(180deg) saturate(0.8);display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <!-- Info -->
                <div style="padding:28px 32px 32px;">
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;color:#71717a;margin:0 0 2px;font-weight:400;">Royal Turkish Cuisine –</h3>
                    <h4 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:white;margin:0 0 20px;font-weight:600;">Lamai</h4>

                    <div style="display:flex;flex-direction:column;gap:11px;margin-bottom:24px;">
                        <div style="display:flex;align-items:flex-start;gap:11px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;margin-top:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span style="color:#71717a;font-size:0.83rem;line-height:1.6;">Lamai Beach Road, Maret, Koh Samui, Surat Thani 84310, Thailand</span>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;margin-top:3px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.58 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l1.02-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="color:#71717a;font-size:0.83rem;">098 256 7595</span>
                                    <a href="<?= url('en', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">English</a>
                                    <span style="color:#B8860B;">·</span>
                                    <a href="<?= url('tr', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">Turkish</a>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="color:#71717a;font-size:0.83rem;">094 335 8904</span>
                                    <a href="<?= url('en', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">English</a>
                                    <span style="color:#B8860B;">·</span>
                                    <a href="<?= url('th', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;">Thai</a>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span style="color:#B8860B;font-size:0.8rem;">Monday-Sunday:</span>
                            <span style="color:#71717a;font-size:0.8rem;">11:00–23:00</span>
                        </div>
                    </div>

                    <!-- Buttons: 50/50 -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                        <a href="<?= url($lang_code, 'menu', 'lamai') ?>"
                           style="display:flex;align-items:center;justify-content:center;padding:13px 8px;border:1px solid #B8860B;color:#B8860B;text-decoration:none;font-size:0.72rem;letter-spacing:0.12em;font-family:'Inter',sans-serif;transition:all 0.2s;text-transform:uppercase;"
                           onmouseover="this.style.background='#B8860B';this.style.color='#000'" onmouseout="this.style.background='transparent';this.style.color='#B8860B'">
                            VIEW MENU
                        </a>
                        <a href="https://maps.app.goo.gl/nZU9Sy1mJpsSGtLg9" target="_blank" rel="noopener"
                           style="display:flex;align-items:center;justify-content:center;padding:13px 8px;border:1px solid #B8860B;border-left:none;color:#B8860B;text-decoration:none;font-size:0.72rem;letter-spacing:0.12em;font-family:'Inter',sans-serif;transition:all 0.2s;text-transform:uppercase;"
                           onmouseover="this.style.background='#B8860B';this.style.color='#000'" onmouseout="this.style.background='transparent';this.style.color='#B8860B'">
                            GET DIRECTIONS
                        </a>
                    </div>
                </div>
            </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     SECTION 3 — A TASTE OF THE MEDITERRANEAN
═══════════════════════════════════════════ -->
<section class="taste-section" style="padding:100px 24px;background:#0A0A0A;overflow:hidden;">
    <div style="max-width:1200px;margin:0 auto;">
        <div class="two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;">

            <!-- Left: food collage image -->
            <div class="taste-img-col" style="display:flex;align-items:center;justify-content:center;">
                <img src="/public/images/food-collage.png" alt="Mediterranean Food" style="width:100%;max-width:560px;height:auto;object-fit:contain;display:block;">
            </div>

            <!-- Right: text -->
            <div>
                <p style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.3em;color:#B8860B;margin:0 0 16px;text-transform:uppercase;"><?= cb($cb, 'taste_label') ?></p>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,3.5vw,3rem);color:white;margin:0 0 28px;line-height:1.2;font-weight:700;"><?= cb($cb, 'taste_title') ?></h2>
                <div style="width:50px;height:1px;background:#B8860B;margin-bottom:36px;"></div>

                <div class="taste-feature">
                    <div style="flex-shrink:0;width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                    </div>
                    <div>
                        <p style="font-family:'Inter',sans-serif;font-size:0.7rem;letter-spacing:0.15em;color:white;margin:0 0 6px;text-transform:uppercase;"><?= cb($cb, 'taste_f1_title') ?></p>
                        <p style="color:#52525b;font-size:0.85rem;line-height:1.6;margin:0;"><?= cb($cb, 'taste_f1_desc') ?></p>
                    </div>
                </div>

                <div class="taste-feature">
                    <div style="flex-shrink:0;width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <div>
                        <p style="font-family:'Inter',sans-serif;font-size:0.7rem;letter-spacing:0.15em;color:white;margin:0 0 6px;text-transform:uppercase;"><?= cb($cb, 'taste_f2_title') ?></p>
                        <p style="color:#52525b;font-size:0.85rem;line-height:1.6;margin:0;"><?= cb($cb, 'taste_f2_desc') ?></p>
                    </div>
                </div>

                <div class="taste-feature">
                    <div style="flex-shrink:0;width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <p style="font-family:'Inter',sans-serif;font-size:0.7rem;letter-spacing:0.15em;color:white;margin:0 0 6px;text-transform:uppercase;"><?= cb($cb, 'taste_f3_title') ?></p>
                        <p style="color:#52525b;font-size:0.85rem;line-height:1.6;margin:0;"><?= cb($cb, 'taste_f3_desc') ?></p>
                    </div>
                </div>

                <div class="taste-feature">
                    <div style="flex-shrink:0;width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <p style="font-family:'Inter',sans-serif;font-size:0.7rem;letter-spacing:0.15em;color:white;margin:0 0 6px;text-transform:uppercase;"><?= cb($cb, 'taste_f4_title') ?></p>
                        <p style="color:#52525b;font-size:0.85rem;line-height:1.6;margin:0;"><?= cb($cb, 'taste_f4_desc') ?></p>
                    </div>
                </div>

                <div style="margin-top:36px;">
                    <a href="<?= url($lang_code, 'menu', 'chaweng') ?>"
                       style="display:inline-flex;align-items:center;gap:10px;padding:13px 28px;background:#B8860B;color:#000;text-decoration:none;font-size:0.78rem;letter-spacing:0.15em;font-family:'Inter',sans-serif;font-weight:600;transition:background 0.2s;text-transform:uppercase;"
                       onmouseover="this.style.background='#9A7209'" onmouseout="this.style.background='#B8860B'">
                        <?= cb($cb, 'taste_btn') ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     SECTION 4 — OUR STORY
═══════════════════════════════════════════ -->
<section class="story-section" style="padding:100px 24px;background:#050505;">
    <div style="max-width:1200px;margin:0 auto;">
        <div class="two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:start;">

            <!-- Left: text -->
            <div>
                <p style="font-family:'Inter',sans-serif;font-size:0.65rem;letter-spacing:0.3em;color:#B8860B;margin:0 0 12px;text-transform:uppercase;"><?= cb($cb, 'story_label') ?></p>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,4vw,3.5rem);color:white;margin:0 0 20px;"><?= cb($cb, 'story_title') ?></h2>
                <div style="width:50px;height:1px;background:#B8860B;margin-bottom:32px;"></div>

                <p style="color:white;font-size:0.95rem;line-height:1.9;margin-bottom:20px;">
                    <?= cb($cb, 'story_p1') ?>
                </p>
                <p style="color:white;font-size:0.95rem;line-height:1.9;margin-bottom:36px;">
                    <?= cb($cb, 'story_p2') ?>
                </p>

                <a href="<?= url($lang_code, 'about') ?>"
                   style="display:inline-flex;align-items:center;gap:10px;color:#B8860B;text-decoration:none;font-size:0.75rem;letter-spacing:0.2em;font-family:'Inter',sans-serif;text-transform:uppercase;transition:gap 0.2s;"
                   onmouseover="this.style.gap='16px'" onmouseout="this.style.gap='10px'">
                    <?= cb($cb, 'story_btn') ?>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Right: front.png with quote overlay -->
            <div style="position:relative;display:flex;align-items:center;justify-content:center;">
                <img src="/public/images/front.png" alt="Royal Turkish Cuisine" style="width:100%;max-width:560px;height:auto;object-fit:cover;display:block;border:1px solid #1a1a1a;">
                <!-- Quote overlay -->
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;padding:32px;background:linear-gradient(to bottom,rgba(0,0,0,0.15),rgba(0,0,0,0.55));">
                    <div style="text-align:center;">
                        <div style="width:40px;height:1px;background:#B8860B;opacity:0.7;margin:0 auto 20px;"></div>
                        <p style="font-family:'Playfair Display',serif;font-size:clamp(1rem,2vw,1.3rem);color:#B8860B;font-style:italic;line-height:1.7;margin:0 0 20px;text-shadow:0 1px 8px rgba(0,0,0,0.8);">
                            <?= cb($cb, 'story_quote') ?>
                        </p>
                        <div style="width:40px;height:1px;background:#B8860B;opacity:0.7;margin:0 auto;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
