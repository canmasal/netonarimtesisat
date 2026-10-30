<?php
require __DIR__ . '/inc/site-layout.php';
try { $bolgeler = aktif_bolgeler(); } catch (Throwable $e) { $bolgeler = []; }

$sss = ciftler('ana_sss');
$sss[] = ['Hangi bölgelere hizmet veriyorsunuz?', ($bolgeler ? implode(', ', $bolgeler) . ' ilçelerinde' : 'İstanbul\'da') . ' hizmet veriyoruz. Size en yakın ustamızı yönlendiriyoruz.'];

site_ust([
    'baslik'   => 'Su Tesisatçısı İstanbul | 7/24 Acil Tesisatçı – Net Onarım',
    'aciklama' => 'İstanbul su tesisatçısı: kırmadan dökmeden cihazla su kaçağı tespiti, tıkanıklık açma, tesisat yenileme. 7/24 acil servis. Hemen arayın: ' . tel_gorunen(),
    'yol'      => '/',
    'schema'   => [
        schema_isletme(),
        ['@type' => 'WebSite', '@id' => site_url('/#site'), 'url' => site_url('/'), 'name' => firma_adi(), 'inLanguage' => 'tr-TR', 'publisher' => ['@id' => site_url('/#isletme')]],
        schema_sss($sss),
    ],
]);
?>

<main>
<!-- HERO -->
<section class="hero">
  <div class="container">
    <div>
      <span class="badge"><svg width="16" height="16"><use href="#i-bolt"/></svg> <?= e(metin('ana_rozet')) ?></span>
      <h1><?= vurgu(metin('ana_baslik')) ?></h1>
      <p class="lead"><?= e(metin('ana_metin')) ?></p>
      <div class="hero-actions">
        <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
        <a class="btn btn-wa btn-lg js-wa" href="<?= e("https://wa.me/" . wa_no()) ?>?text=Merhaba%2C%20acil%20tesisat%C3%A7%C4%B1%20laz%C4%B1m." target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp'tan Yaz</a>
      </div>
      <ul class="hero-checks">
        <?php foreach (liste('ana_maddeler') as $m): ?><li><svg><use href="#i-check"/></svg><?= e($m) ?></li><?php endforeach; ?>
      </ul>
    </div>

    <form class="quick" id="talep" aria-label="Hızlı servis talebi">
      <h3><?= e(metin('ana_form_baslik')) ?></h3>
      <p><?= e(metin('ana_form_metin')) ?></p>
      <label for="f-name">Adınız</label>
      <input id="f-name" name="name" type="text" placeholder="Adınız Soyadınız" required>
      <label for="f-phone">Telefonunuz</label>
      <input id="f-phone" name="phone" type="tel" inputmode="tel" placeholder="05xx xxx xx xx" required pattern="[0-9 +()\-]{10,}">
      <label for="f-service">Sorununuz</label>
      <select id="f-service" name="service">
        <?php foreach (hizmet_secenekleri() as $h): ?><option><?= e($h) ?></option><?php endforeach; ?>
      </select>
      <label for="f-district">İlçe / Semt</label>
      <select id="f-district" name="district">
        <option value="">Seçin</option>
        <?php foreach ($bolgeler as $b): ?><option><?= e($b) ?></option><?php endforeach; ?>
        <option>Diğer</option>
      </select>
      <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px">
      <label class="kvkk-onay"><input type="checkbox" name="kvkk" value="1" required> <span><?= kvkk_onay_html() ?></span></label>
      <div class="note" id="f-ok" role="status" style="display:none;color:#16a34a;font-weight:700;margin:0 0 12px">✓ Talebiniz alındı, en kısa sürede dönüş yapacağız.</div>
      <button class="btn btn-wa" type="submit"><svg><use href="#i-wa"/></svg>Talebi Gönder</button>
      <div class="note">ya da hemen arayın: <a href="tel:<?= e(tel()) ?>" class="js-call"><b><?= e(tel_gorunen()) ?></b></a></div>
    </form>
  </div>
</section>

<!-- TRUST -->
<section class="trust" aria-label="Güvencelerimiz">
  <div class="container">
    <?php $ikonlar = ['i-clock', 'i-pin', 'i-shield', 'i-tag']; foreach (array_slice(ciftler('ana_guven'), 0, 4) as $i => [$b1, $b2]): ?>
    <div class="trust-item"><div class="ico"><svg><use href="#<?= $ikonlar[$i] ?>"/></svg></div><div><b><?= e($b1) ?></b><span><?= e($b2) ?></span></div></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SERVICES -->
<section class="section services" id="hizmetler">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Hizmetlerimiz</span>
      <h2><?= e(metin('ana_hiz_baslik')) ?></h2>
      <p><?= e(metin('ana_hiz_metin')) ?></p>
    </div>
    <div class="grid-3">
      <?php $ilk = true; foreach (hizmetler() as $sl => $h): ?>
      <article class="card<?= $ilk ? ' featured' : '' ?>">
        <?php if ($ilk): ?><span class="tag">En Çok Tercih Edilen</span><?php endif; $ilk = false; ?>
        <div class="ico"><svg><use href="#<?= $h['ikon'] ?>"/></svg></div>
        <h3><a href="/hizmet/<?= $sl ?>"><?= e($h['ad']) ?></a></h3>
        <p><?= e($h['kart']) ?></p>
        <a class="more" href="/hizmet/<?= $sl ?>">Detaylı bilgi <svg width="18" height="18"><use href="#i-arrow"/></svg></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- EMERGENCY -->
<section class="emergency" aria-label="Acil servis">
  <div class="container">
    <div>
      <h2><?= e(metin('ana_acil_baslik')) ?></h2>
      <p><?= e(metin('ana_acil_metin')) ?></p>
    </div>
    <a class="btn btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg>Acil Ara: <?= e(tel_gorunen()) ?></a>
  </div>
</section>

<!-- WHY -->
<section class="section why" id="neden-biz">
  <div class="container">
    <div>
      <span class="eyebrow"><?= e(metin('ana_neden_ust')) ?></span>
      <h2><?= e(metin('ana_neden_baslik')) ?></h2>
      <p class="intro"><?= e(metin('ana_neden_metin')) ?> <a href="/hakkimizda" style="color:var(--blue);font-weight:700">Bizi daha yakından tanıyın →</a></p>
      <ul class="why-list">
        <?php $ikonlar = ['i-radar', 'i-tag', 'i-shield', 'i-clock']; foreach (array_slice(ciftler('ana_neden'), 0, 4) as $i => [$b1, $b2]): ?>
        <li><div class="ico"><svg><use href="#<?= $ikonlar[$i] ?>"/></svg></div><div><b><?= e($b1) ?></b><span><?= e($b2) ?></span></div></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="visual" aria-hidden="true">
      <svg viewBox="0 0 400 320" width="100%" style="max-width:420px">
        <defs>
          <linearGradient id="g1" x1="0" x2="1"><stop offset="0" stop-color="#1d6fe8"/><stop offset="1" stop-color="#0b2545"/></linearGradient>
        </defs>
        <rect x="20" y="40" width="360" height="240" rx="18" fill="#fff" stroke="#dbe5f3"/>
        <rect x="20" y="40" width="360" height="36" rx="18" fill="#0b2545"/>
        <rect x="20" y="58" width="360" height="18" fill="#0b2545"/>
        <circle cx="44" cy="58" r="5" fill="#ff7a1a"/><circle cx="62" cy="58" r="5" fill="#ffd166"/><circle cx="80" cy="58" r="5" fill="#25d366"/>
        <path d="M50 230 H150 V140 H260 V200 H350" fill="none" stroke="#c9d7ec" stroke-width="14" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M50 230 H150 V140 H260 V200 H350" fill="none" stroke="url(#g1)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="10 10"/>
        <circle cx="260" cy="170" r="34" fill="#ff7a1a" opacity=".15"/>
        <circle cx="260" cy="170" r="20" fill="#ff7a1a" opacity=".25"/>
        <circle cx="260" cy="170" r="8" fill="#ff7a1a"/>
        <path d="M260 120c-6 8-10 13-10 18a10 10 0 0 0 20 0c0-5-4-10-10-18z" fill="#1d6fe8"/>
        <text x="200" y="262" text-anchor="middle" font-family="Inter Site,Inter,sans-serif" font-size="13" font-weight="700" fill="#5b6b82">Kaçak noktası tespit edildi</text>
      </svg>
      <div class="float-card fc1"><span class="fc-ico" style="background:var(--green)"><svg width="20" height="20"><use href="#i-check"/></svg></span><div>Kırım yok<small>Noktasal müdahale</small></div></div>
      <div class="float-card fc2"><span class="fc-ico" style="background:var(--orange)"><svg width="20" height="20"><use href="#i-bolt"/></svg></span><div>7/24 Acil<small>Aynı gün servis</small></div></div>
    </div>
  </div>
</section>

<!-- STEPS -->
<section class="section steps" aria-label="Nasıl çalışıyoruz">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Nasıl Çalışıyoruz?</span>
      <h2><?= e(metin('ana_adim_baslik')) ?></h2>
      <p><?= e(metin('ana_adim_metin')) ?></p>
    </div>
    <div class="step-grid">
      <?php foreach (array_slice(ciftler('ana_adimlar'), 0, 4) as [$b1, $b2]): ?><div class="step"><h3><?= e($b1) ?></h3><p><?= e($b2) ?></p></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- AREAS -->
<section class="section" id="bolgeler">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Hizmet Bölgeleri</span>
      <h2><?= e(metin('ana_bolge_baslik')) ?></h2>
      <p><?= e(metin('ana_bolge_metin')) ?></p>
    </div>
    <div class="areas-wrap">
      <?php foreach ($bolgeler as $b): ?><a class="area" href="<?= ilce_url($b) ?>"><?= e($b) ?></a><?php endforeach; ?>
    </div>
    <p class="areas-note">Bölgeniz listede yok mu? <a href="tel:<?= e(tel()) ?>" class="js-call">Arayın, hemen yönlendirelim.</a></p>
  </div>
</section>

<!-- GALERI -->
<?php try { $sonResimler = galeri_resimleri(6); } catch (Throwable $e) { $sonResimler = []; } if ($sonResimler): ?>
<section class="section" id="galeri">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Galeri</span>
      <h2><?= vurgu(metin('galeri_baslik')) ?></h2>
      <p><?= e(metin('galeri_metin')) ?></p>
    </div>
    <div class="galeri-grid">
      <?php foreach ($sonResimler as $r): ?>
        <figure class="galeri-oge"><a href="/galeri"><img src="<?= e($r['kucuk']) ?>" alt="<?= e($r['baslik'] ?: firma_adi() . ' Tesisat çalışması') ?>" loading="lazy" width="640" height="480"></a><?php if ($r['baslik']): ?><figcaption><?= e($r['baslik']) ?></figcaption><?php endif; ?></figure>
      <?php endforeach; ?>
    </div>
    <p class="areas-note"><a href="/galeri">Tüm fotoğraflar →</a></p>
  </div>
</section>
<?php endif; ?>

<!-- BLOG -->
<?php try { $sonYazilar = blog_yazilari(3); } catch (Throwable $e) { $sonYazilar = []; } if ($sonYazilar): ?>
<section class="section" id="blog" style="background:var(--soft)">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Blog</span>
      <h2>Tesisat Rehberleri</h2>
      <p>Ustalarımızdan evde işinize yarayacak pratik bilgiler.</p>
    </div>
    <div class="blog-grid">
      <?php foreach ($sonYazilar as $y): ?>
        <article class="blog-card">
          <a href="/blog/<?= e($y['slug']) ?>" class="blog-card-link">
            <div class="blog-meta"><time datetime="<?= e($y['yayin']) ?>"><?= e(tr_tarih($y['yayin'])) ?></time></div>
            <h3 style="font-size:19px;line-height:1.35;color:var(--navy)"><?= e($y['baslik']) ?></h3>
            <p><?= e($y['ozet']) ?></p>
            <span class="blog-more">Devamını oku <svg><use href="#i-arrow"/></svg></span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="areas-note"><a href="/blog">Tüm yazılar →</a></p>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="section faq" id="sss">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Sıkça Sorulan Sorular</span>
      <h2>Merak Ettikleriniz</h2>
    </div>
    <div class="faq-list">
      <?php foreach ($sss as $i => [$s, $c]): ?>
      <details<?= $i === 0 ? ' open' : '' ?>><summary><?= e($s) ?></summary><p><?= e($c) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php site_cta(); ?>
</main>

<script>
  // Form -> panele kaydet + WhatsApp mesajı
  document.getElementById('talep').addEventListener('submit', function (e) {
    e.preventDefault();
    var f = e.target;
    if (f.website.value) return;
    try { fetch('/talep.php', { method: 'POST', body: new FormData(f), keepalive: true }); } catch (err) {}
    var msg = 'Merhaba, servis talebim var.\nAd: ' + f.name.value +
              '\nTelefon: ' + f.phone.value +
              '\nSorun: ' + f.service.value +
              (f.district.value ? '\nİlçe: ' + f.district.value : '');
    track('form_whatsapp');
    window.open('<?= e("https://wa.me/" . wa_no()) ?>?text=' + encodeURIComponent(msg), '_blank');
    f.reset();
    document.getElementById('f-ok').style.display = 'block';
  });
</script>
<?php site_alt();
