<?php
$pdo = getPDO();

// Photos from DB
$photos_stmt = $pdo->prepare("SELECT * FROM menu_images WHERE branch='chaweng' ORDER BY sort_order ASC");
$photos_stmt->execute();
$photos = $photos_stmt->fetchAll();

$chaweng_url = url($lang_code, 'menu', 'chaweng');
$lamai_url   = url($lang_code, 'menu', 'lamai');
?>
<style>
/* Branch tabs */
.menu-tabs{display:flex;justify-content:center;gap:0;margin-top:28px;flex-wrap:wrap;}
.menu-tab{
    padding:11px 48px;
    font-size:.72rem;letter-spacing:.2em;text-transform:uppercase;
    font-family:'Inter',sans-serif;border:1px solid #2A2A2A;color:#71717a;
    text-decoration:none;transition:all .2s;background:transparent;
}
.menu-tab:first-child{border-right:none;}
.menu-tab:hover{color:#B8860B;border-color:#B8860B;}
.menu-tab.active{color:#000;background:#B8860B;border-color:#B8860B;}
@media(max-width:768px){
    .menu-tab{padding:10px 20px;flex:1;text-align:center;}
    .menu-tab:first-child{border-right:1px solid #2A2A2A;}
}

/* Photo grid — 3 columns */
.photo-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin-top:40px;
}
@media(max-width:860px){.photo-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:540px){.photo-grid{grid-template-columns:1fr;}}

.photo-grid img{
    width:100%;aspect-ratio:4/3;object-fit:cover;display:block;
    cursor:pointer;transition:transform .3s,opacity .3s;
}
.photo-grid img:hover{transform:scale(1.02);opacity:.85;}

/* Lightbox */
.lb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.93);z-index:9999;align-items:center;justify-content:center;}
.lb.open{display:flex;}
.lb img{max-width:92vw;max-height:88vh;object-fit:contain;display:block;user-select:none;}
.lb-close{position:absolute;top:18px;right:26px;font-size:1.8rem;color:#a1a1aa;cursor:pointer;background:none;border:none;line-height:1;}
.lb-prev,.lb-next{position:absolute;top:50%;transform:translateY(-50%);font-size:2.6rem;color:#a1a1aa;cursor:pointer;background:none;border:none;padding:0 18px;user-select:none;}
.lb-prev{left:0;}.lb-next{right:0;}
.lb-close:hover,.lb-prev:hover,.lb-next:hover{color:#B8860B;}
.lb-counter{position:absolute;bottom:16px;left:50%;transform:translateX(-50%);color:#52525b;font-size:.75rem;letter-spacing:.1em;}
</style>

<!-- Page Header -->
<section style="padding:90px 24px 40px;text-align:center;background:#0A0A0A;">
    <p style="font-size:.65rem;letter-spacing:.35em;color:#B8860B;margin-bottom:14px;font-family:'Inter',sans-serif;text-transform:uppercase;">ROYAL MEDITERRANEAN CUISINE</p>
    <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2.2rem,5vw,3.8rem);color:white;margin:0 0 18px 0;">Chaweng Menu</h1>
    <div class="gold-line"></div>
    <p style="color:#52525b;font-size:.82rem;margin-top:20px;letter-spacing:.05em;"><?= CHAWENG_ADDRESS ?></p>

    <div class="menu-tabs">
        <a href="<?= htmlspecialchars($chaweng_url) ?>" class="menu-tab active">CHAWENG</a>
        <a href="<?= htmlspecialchars($lamai_url) ?>" class="menu-tab">LAMAI</a>
    </div>
</section>

<!-- Photos -->
<section style="padding:10px 24px 100px;background:#0A0A0A;">
    <div style="max-width:1200px;margin:0 auto;">
        <?php if (empty($photos)): ?>
        <div style="text-align:center;padding:80px 0;color:#52525b;">
            <p style="font-size:.9rem;letter-spacing:.15em;">MENU PHOTOS COMING SOON</p>
        </div>
        <?php else: ?>
        <div class="photo-grid">
            <?php foreach ($photos as $i => $photo): ?>
            <div>
                <img src="/public/images/menu/<?= htmlspecialchars($photo['filename']) ?>"
                     alt="Chaweng Menu <?= $i+1 ?>"
                     onclick="openLb(<?= $i ?>)"
                     loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Lightbox -->
        <div class="lb" id="lb" onclick="if(event.target===this)closeLb()">
            <button class="lb-close" onclick="closeLb()">&#10005;</button>
            <button class="lb-prev" onclick="shift(-1)">&#8249;</button>
            <img id="lb-img" src="" alt="">
            <button class="lb-next" onclick="shift(1)">&#8250;</button>
            <span class="lb-counter" id="lb-ctr"></span>
        </div>
        <script>
        var P=<?= json_encode(array_column($photos,'filename')) ?>,c=0;
        function openLb(i){c=i;show();document.getElementById('lb').classList.add('open');document.body.style.overflow='hidden';}
        function closeLb(){document.getElementById('lb').classList.remove('open');document.body.style.overflow='';}
        function shift(d){c=(c+d+P.length)%P.length;show();}
        function show(){document.getElementById('lb-img').src='/public/images/menu/'+P[c];document.getElementById('lb-ctr').textContent=(c+1)+' / '+P.length;}
        document.addEventListener('keydown',function(e){if(e.key==='Escape')closeLb();if(e.key==='ArrowRight')shift(1);if(e.key==='ArrowLeft')shift(-1);});
        </script>
        <?php endif; ?>
    </div>
</section>
