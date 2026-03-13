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
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <a href="tel:<?= CHAWENG_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= CHAWENG_PHONE ?></a>
                    <span style="color:#B8860B;">·</span>
                    <a href="<?= url('en', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                    <span style="color:#B8860B;">·</span>
                    <a href="<?= url('tr', 'menu', 'chaweng') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Turkish</a>
                </div>
            </div>

            <!-- Col 4: Lamai -->
            <div>
                <h4 style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 20px;font-family:'Inter',sans-serif;text-transform:uppercase;"><?= htmlspecialchars($lang['footer_lamai']) ?></h4>
                <p style="color:#71717a;font-size:0.82rem;line-height:1.7;margin:0 0 10px;"><?= LAMAI_ADDRESS ?></p>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
                    <a href="tel:<?= LAMAI_PHONE ?>" style="color:#B8860B;text-decoration:none;font-size:0.82rem;"><?= LAMAI_PHONE ?></a>
                    <span style="color:#B8860B;">·</span>
                    <a href="<?= url('en', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">English</a>
                    <span style="color:#B8860B;">·</span>
                    <a href="<?= url('th', 'menu', 'lamai') ?>" style="color:#B8860B;font-size:0.78rem;text-decoration:none;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Thai</a>
                </div>

                <!-- Follow Us -->
                <p style="color:#B8860B;font-size:0.65rem;letter-spacing:0.22em;margin:0 0 12px;font-family:'Inter',sans-serif;text-transform:uppercase;">FOLLOW US</p>
                <div style="display:flex;gap:8px;">
                    <a href="https://instagram.com" target="_blank" rel="noopener"
                       style="width:36px;height:36px;border:1px solid #2A2A2A;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:border-color 0.2s,color 0.2s;"
                       onmouseover="this.style.borderColor='#B8860B';this.style.color='#B8860B'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#71717a'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="https://facebook.com" target="_blank" rel="noopener"
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

<!-- WhatsApp Floating Buttons -->
<div id="wa-buttons" style="position:fixed;bottom:24px;right:24px;z-index:1000;display:flex;flex-direction:column;align-items:flex-end;gap:12px;">

    <!-- Branch buttons (hidden by default) -->
    <div id="wa-menu" style="display:none;flex-direction:column;align-items:flex-end;gap:10px;">
        <a href="https://wa.me/66982567595" target="_blank" rel="noopener"
           style="display:flex;align-items:center;gap:10px;background:#25D366;color:white;font-family:'Inter',sans-serif;font-size:0.82rem;font-weight:600;letter-spacing:0.05em;padding:10px 18px 10px 14px;border-radius:50px;text-decoration:none;box-shadow:0 4px 12px rgba(37,211,102,0.4);transition:transform 0.2s;"
           onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Chaweng
        </a>
        <a href="https://wa.me/66943358904" target="_blank" rel="noopener"
           style="display:flex;align-items:center;gap:10px;background:#25D366;color:white;font-family:'Inter',sans-serif;font-size:0.82rem;font-weight:600;letter-spacing:0.05em;padding:10px 18px 10px 14px;border-radius:50px;text-decoration:none;box-shadow:0 4px 12px rgba(37,211,102,0.4);transition:transform 0.2s;"
           onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Lamai
        </a>
    </div>

    <!-- Main toggle button -->
    <button id="wa-toggle" onclick="(function(){var m=document.getElementById('wa-menu');var open=m.style.display==='flex';m.style.display=open?'none':'flex';})()"
       style="width:52px;height:52px;background:#25D366;border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(37,211,102,0.4);cursor:pointer;transition:transform 0.2s;"
       onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </button>

</div>

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
/* WhatsApp button — smaller on mobile */
@media (max-width: 480px) {
    #wa-buttons { bottom: 16px; right: 16px; }
    #wa-toggle { width: 46px !important; height: 46px !important; }
}
</style>

</body>
</html>
