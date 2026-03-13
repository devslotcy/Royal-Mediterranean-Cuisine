</main>

<!-- Footer -->
<footer style="background:#000;border-top:1px solid #2A2A2A;margin-top:80px;">
    <div style="max-width:1400px;margin:0 auto;padding:48px 48px 32px;">

        <div class="footer-grid" style="display:grid;grid-template-columns:200px 1fr 1fr 1fr;gap:48px;align-items:start;">

            <!-- Col 1: Logo + Tagline -->
            <div>
                <?php if (file_exists(__DIR__ . '/../public/images/logo.png')): ?>
                <img src="/public/images/logo.png" alt="Royal" style="height:56px;margin-bottom:16px;display:block;">
                <?php else: ?>
                <div style="font-family:'Playfair Display',serif;font-size:1.3rem;color:#B8860B;margin-bottom:16px;">ROYAL</div>
                <?php endif; ?>
                <p style="color:#52525b;font-size:0.8rem;line-height:1.7;margin:0;"><?= htmlspecialchars($lang['footer_tagline']) ?></p>
                <a href="mailto:admin@samuiroyal.com" style="display:inline-block;margin-top:12px;color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">admin@samuiroyal.com</a>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h4 style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 20px;font-family:'Inter',sans-serif;text-transform:uppercase;"><?= htmlspecialchars($lang['footer_quick_links']) ?></h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="<?= url($lang_code, 'home') ?>" style="color:#71717a;text-decoration:none;font-size:0.83rem;transition:color 0.2s;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#71717a'"><?= htmlspecialchars($lang['nav_home']) ?></a></li>
                    <li><a href="<?= url($lang_code, 'about') ?>" style="color:#71717a;text-decoration:none;font-size:0.83rem;transition:color 0.2s;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#71717a'"><?= htmlspecialchars($lang['nav_about']) ?></a></li>
                    <li><a href="<?= url($lang_code, 'menu', 'chaweng') ?>" style="color:#71717a;text-decoration:none;font-size:0.83rem;transition:color 0.2s;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#71717a'"><?= htmlspecialchars($lang['nav_chaweng']) ?></a></li>
                    <li><a href="<?= url($lang_code, 'menu', 'lamai') ?>" style="color:#71717a;text-decoration:none;font-size:0.83rem;transition:color 0.2s;" onmouseover="this.style.color='#B8860B'" onmouseout="this.style.color='#71717a'"><?= htmlspecialchars($lang['nav_lamai']) ?></a></li>
                </ul>
            </div>

            <!-- Col 3: Chaweng -->
            <div>
                <h4 style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 20px;font-family:'Inter',sans-serif;text-transform:uppercase;"><?= htmlspecialchars($lang['footer_chaweng']) ?></h4>
                <p style="color:#71717a;font-size:0.82rem;line-height:1.7;margin:0 0 10px;"><?= CHAWENG_ADDRESS ?></p>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <a href="tel:<?= CHAWENG_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= CHAWENG_PHONE ?></a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('en', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('tr', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Turkish</a>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <a href="tel:<?= LAMAI_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= LAMAI_PHONE ?></a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('en', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('th', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Thai</a>
                    </div>
                </div>
            </div>

            <!-- Col 4: Lamai -->
            <div>
                <h4 style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 20px;font-family:'Inter',sans-serif;text-transform:uppercase;"><?= htmlspecialchars($lang['footer_lamai']) ?></h4>
                <p style="color:#71717a;font-size:0.82rem;line-height:1.7;margin:0 0 10px;"><?= LAMAI_ADDRESS ?></p>
                <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <a href="tel:<?= CHAWENG_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= CHAWENG_PHONE ?></a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('en', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('tr', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Turkish</a>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <a href="tel:<?= LAMAI_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= LAMAI_PHONE ?></a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('en', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                        <span style="color:#B8860B;">·</span>
                        <a href="<?= url('th', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Thai</a>
                    </div>
                </div>

                <!-- Follow Us -->
                <p style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 12px;font-family:'Inter',sans-serif;text-transform:uppercase;">FOLLOW US</p>
                <div style="display:flex;gap:8px;">
                    <a href="https://www.instagram.com/royalturkishcuisine/" target="_blank" rel="noopener"
                       style="width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:border-color 0.2s,color 0.2s;"
                       onmouseover="this.style.borderColor='#B8860B';this.style.color='#B8860B'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#71717a'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="https://www.facebook.com/p/ROYAL-Samui-61577350433698/" target="_blank" rel="noopener"
                       style="width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:border-color 0.2s,color 0.2s;"
                       onmouseover="this.style.borderColor='#B8860B';this.style.color='#B8860B'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#71717a'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                </div>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div style="margin-top:36px;padding-top:20px;border-top:1px solid #1a1a1a;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <span style="color:#3a3a3a;font-size:0.78rem;"><?= htmlspecialchars($lang['footer_copyright']) ?></span>
            <span style="color:#3a3a3a;font-size:0.78rem;"><?= htmlspecialchars($lang['footer_location']) ?></span>
        </div>

    </div>
</footer>

<style>
@media (max-width: 900px) {
    .footer-grid { grid-template-columns: 1fr 1fr !important; gap: 32px !important; }
    footer > div { padding: 40px 24px 24px !important; }
}
@media (max-width: 540px) {
    .footer-grid { grid-template-columns: 1fr !important; gap: 28px !important; }
    footer > div { padding: 32px 16px 20px !important; }
}
/* Apple Watch / very small screens */
@media (max-width: 200px) {
    .footer-grid { grid-template-columns: 1fr !important; }
    footer > div { padding: 16px 8px !important; }
}
/* Wide screens / TV */
@media (min-width: 1800px) {
    footer > div { max-width: 1600px !important; }
}
</style>

</body>
</html>
