<?php
require_once __DIR__ . '/seo.php';

/**
 * Sitenin ortak üst kısmı.
 * $m: baslik, aciklama, yol (canonical yolu, örn. /hizmet/su-kacagi-tespiti), schema (dizi), robots
 */
/** Sitenin stilleri sayfaya gömülür: ayrı bir CSS isteği sayfanın açılmasını bekletmez. */
function site_css(): string
{
    static $css = null;
    if ($css === null) {
        $css = (string)@file_get_contents(__DIR__ . '/../assets/site.css');
        $css = preg_replace('#/\*.*?\*/#s', '', $css);          // yorumları at
        $css = preg_replace('/\s*\n\s*/', '', $css);             // satır sonlarını at
        $css = str_replace('</', '<\/', $css);                    // güvenlik
    }
    return $css;
}

function site_ust(array $m): void
{
    // Sayfalar panelden anında değişebildiği için sunucu (LiteSpeed) önbelleğinde tutulmaz
    if (!headers_sent()) {
        header('X-LiteSpeed-Cache-Control: no-cache');
        header('Cache-Control: no-cache, must-revalidate');
    }
    $canonical = site_url($m['yol'] ?? '/');
    $baslik = $m['baslik'];
    $aciklama = $m['aciklama'];
    $ga4 = ayar('ga4_id');
    $ads = ayar('ads_id');
    $tagId = $ga4 ?: $ads;
    $versiyon = @filemtime(__DIR__ . '/../assets/site.css') ?: 1;
    $simdiki = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $aktif = fn(string $on) => ($on === '/' ? $simdiki === '/' : str_starts_with($simdiki, $on)) ? ' class="aktif" aria-current="page"' : '';
    ?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($baslik) ?></title>
<meta name="description" content="<?= e($aciklama) ?>">
<meta name="robots" content="<?= e($m['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1') ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="tr-TR" href="<?= e($canonical) ?>">
<?php if (ayar('google_dogrulama')): ?><meta name="google-site-verification" content="<?= e(ayar('google_dogrulama')) ?>">
<?php endif; if (ayar('bing_dogrulama')): ?><meta name="msvalidate.01" content="<?= e(ayar('bing_dogrulama')) ?>">
<?php endif; if (ayar('yandex_dogrulama')): ?><meta name="yandex-verification" content="<?= e(ayar('yandex_dogrulama')) ?>">
<?php endif; ?>
<!-- Yerel (geo) etiketleri -->
<meta name="geo.region" content="TR-34">
<meta name="geo.placename" content="<?= e($m['yer'] ?? 'İstanbul') ?>">
<?php if (ayar('enlem') !== '' && ayar('boylam') !== ''): ?><meta name="geo.position" content="<?= e(ayar('enlem')) ?>;<?= e(ayar('boylam')) ?>">
<meta name="ICBM" content="<?= e(ayar('enlem')) ?>, <?= e(ayar('boylam')) ?>">
<?php endif; ?>
<!-- Sosyal paylaşım -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(firma_adi()) ?>">
<meta property="og:title" content="<?= e($baslik) ?>">
<meta property="og:description" content="<?= e($aciklama) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(site_url('/assets/og.jpg')) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="628">
<meta property="og:locale" content="tr_TR">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#0b2545">
<link rel="icon" href="/assets/logo/logo-simge.svg" type="image/svg+xml">
<link rel="icon" href="/assets/logo/favicon-48.png" sizes="48x48" type="image/png">
<link rel="apple-touch-icon" href="/assets/logo/apple-touch-icon.png">
<link rel="sitemap" type="application/xml" href="/sitemap.xml">
<link rel="preload" href="/assets/fonts/inter-800.woff" as="font" type="font/woff" crossorigin fetchpriority="high">
<link rel="preload" href="/assets/fonts/inter-400.woff" as="font" type="font/woff" crossorigin fetchpriority="high">
<link rel="preload" href="/assets/fonts/inter-700.woff" as="font" type="font/woff" crossorigin>
<style><?= site_css() ?></style>
<script>
  // Google Analytics / Ads yalnızca ziyaretçi analiz çerezlerine izin verirse yüklenir (KVKK).
  window.CEREZ_ETIKET = <?= json_encode(array_values(array_filter([$ga4, $ads]))) ?>;
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  function cerezTercih(){ var m = document.cookie.match(/(?:^|; )cerez_tercih=([^;]+)/); return m ? m[1] : ''; }
  function etiketYukle(){
    if (window.ETIKET_YUKLENDI || !window.CEREZ_ETIKET.length) return; window.ETIKET_YUKLENDI = true;
    var s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + window.CEREZ_ETIKET[0]; document.head.appendChild(s);
    gtag('js', new Date()); window.CEREZ_ETIKET.forEach(function (id) { gtag('config', id); });
  }
  if (cerezTercih() === 'kabul') etiketYukle();
</script>
<?= schema_yaz($m['schema'] ?? [schema_isletme()]) ?>
</head>
<body>

<!-- SVG ikon seti -->
<svg width="0" height="0" style="position:absolute">
  <defs>
    <symbol id="i-phone" viewBox="0 0 24 24"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></symbol>
    <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.25-.12-1.47-.72-1.7-.8s-.39-.13-.56.12-.64.8-.78.97-.29.19-.54.06a6.7 6.7 0 0 1-3.34-2.92c-.25-.43.25-.4.72-1.34a.45.45 0 0 0-.02-.43c-.06-.12-.56-1.34-.76-1.84s-.4-.42-.56-.43h-.48a.92.92 0 0 0-.66.31 2.8 2.8 0 0 0-.87 2.07 4.85 4.85 0 0 0 1.02 2.58 11.1 11.1 0 0 0 4.25 3.76c1.58.68 2.2.74 2.99.62a2.55 2.55 0 0 0 1.68-1.18 2.07 2.07 0 0 0 .14-1.18c-.06-.1-.23-.17-.48-.29z"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></g></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6z"/><path d="m9 12 2 2 4-4" stroke-linecap="round"/></g></symbol>
    <symbol id="i-tag" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="8" cy="8" r="1.5"/></g></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></g></symbol>
    <symbol id="i-drop" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" d="M12 3c-4 5.5-7 9-7 12.5a7 7 0 0 0 14 0C19 12 16 8.5 12 3z"/></symbol>
    <symbol id="i-radar" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><path d="M12 12l6-6"/><circle cx="12" cy="12" r="1" fill="currentColor"/></g></symbol>
    <symbol id="i-pipe" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 8h8a3 3 0 0 1 3 3v10"/><path d="M3 4h8a7 7 0 0 1 7 7v10"/><path d="M12 21h8"/></g></symbol>
    <symbol id="i-cam" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3z"/></g></symbol>
    <symbol id="i-heat" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 5v14M11 5v14M15 5v14M19 5v14"/></g></symbol>
    <symbol id="i-faucet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10h9a4 4 0 0 1 4 4v1"/><path d="M8 10V6M5 6h6"/><path d="M17 19v.5M17 22v.01"/><path d="M4 14h6"/></g></symbol>
    <symbol id="i-wrench" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0 5 5L21 13l-8 8-3-3 6.7-6.7M14.7 6.3 13 4.6a4 4 0 0 0-5 5L3 14.6 6.4 18 11.3 13"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></g></symbol>
    <symbol id="i-insta" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></g></symbol>
    <symbol id="i-fb" viewBox="0 0 24 24"><path fill="currentColor" d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v8h4v-8h3l1-4h-4V8.5a.5.5 0 0 1 .5-.5z"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24"><path fill="currentColor" d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9z"/></symbol>
    <symbol id="i-route" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11 21 3l-8 18-2-8z"/></g></symbol>
    <symbol id="i-image" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-9 9"/></g></symbol>
    <symbol id="i-bolt" viewBox="0 0 24 24"><path fill="currentColor" d="M13 2 4 14h7l-1 8 9-12h-7z"/></symbol>
  </defs>
</svg>

<div class="topbar">
  <div class="container">
    <div class="live"><span class="dot"></span> <?= e(metin('ust_serit')) ?></div>
    <div>7/24 Acil hat: <a href="tel:<?= e(tel()) ?>" class="js-call"><?= e(tel_gorunen()) ?></a></div>
  </div>
</div>

<header>
  <div class="container">
    <a href="/" class="logo logo-img" aria-label="<?= e(firma_adi()) ?> ana sayfa">
      <img src="/assets/logo/logo-yatay.svg?v=<?= @filemtime(__DIR__ . '/../assets/logo/logo-yatay.svg') ?: 1 ?>" alt="<?= e(firma_adi()) ?> Tesisat" width="361" height="84">
    </a>
    <nav aria-label="Ana menü" id="ana-menu">
      <ul>
        <li><a href="/"<?= $aktif('/') ?>>Anasayfa</a></li>
        <li class="has-sub">
          <a href="/#hizmetler"<?= $aktif('/hizmet/') ?>>Hizmetler <svg class="caret" viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg></a>
          <ul class="sub">
            <?php foreach (hizmetler() as $sl => $h): ?><li><a href="/hizmet/<?= $sl ?>"><svg><use href="#<?= $h['ikon'] ?>"/></svg><?= e($h['ad']) ?></a></li><?php endforeach; ?>
          </ul>
        </li>
        <li><a href="/hakkimizda"<?= $aktif('/hakkimizda') ?>>Hakkımızda</a></li>
        <li><a href="/galeri"<?= $aktif('/galeri') ?>>Galeri</a></li>
        <li><a href="/blog"<?= $aktif('/blog') ?>>Blog</a></li>
        <li><a href="/iletisim"<?= $aktif('/iletisim') ?>>İletişim</a></li>
      </ul>
    </nav>
    <div class="header-cta">
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, tesisat için bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>" aria-label="Hemen ara"><svg><use href="#i-phone"/></svg><span>Hemen Ara</span></a>
      <button class="menu-btn" type="button" aria-label="Menüyü aç" aria-expanded="false" aria-controls="ana-menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
<?php
}

function site_alt(): void
{
    $bolgeler = aktif_bolgeler();
    $adres = icerik_ham('adres');
    $eposta = icerik_ham('eposta');
    $sosyal = array_filter(['i-insta' => ['Instagram', icerik_ham('instagram')], 'i-fb' => ['Facebook', icerik_ham('facebook')]], fn($x) => $x[1] !== '');
    ?>
<footer>
  <div class="container">
    <div class="foot-grid">
      <div class="foot-brand">
        <a href="/" class="logo logo-img"><img src="/assets/logo/logo-yatay-beyaz.svg?v=<?= @filemtime(__DIR__ . '/../assets/logo/logo-yatay-beyaz.svg') ?: 1 ?>" alt="<?= e(firma_adi()) ?> Tesisat" width="361" height="84" loading="lazy"></a>
        <p><?= e(metin('alt_metin')) ?></p>
        <div class="foot-social">
          <?php foreach ($sosyal as $ikon => [$ad, $url]): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($ad) ?>"><svg><use href="#<?= $ikon ?>"/></svg></a><?php endforeach; ?>
          <a href="<?= e(wa()) ?>" class="js-wa" target="_blank" rel="noopener" aria-label="WhatsApp"><svg><use href="#i-wa"/></svg></a>
          <?php if (ayar('gbp_url')): ?><a href="<?= e(ayar('gbp_url')) ?>" target="_blank" rel="noopener" aria-label="Google'da değerlendirin"><svg><use href="#i-star"/></svg></a><?php endif; ?>
        </div>
      </div>
      <div>
        <h4>Hizmetlerimiz</h4>
        <ul class="foot-links">
          <?php foreach (hizmetler() as $sl => $h): ?><li><a href="/hizmet/<?= $sl ?>"><?= e($h['ad']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4>Kurumsal</h4>
        <ul class="foot-links">
          <li><a href="/">Anasayfa</a></li>
          <li><a href="/hakkimizda">Hakkımızda</a></li>
          <li><a href="/galeri">Galeri</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/iletisim">İletişim</a></li>
          <li><a href="/#bolgeler">Hizmet Bölgeleri</a></li>
          <li><a href="/kvkk-aydinlatma-metni">KVKK Aydınlatma Metni</a></li>
          <li><a href="/cerez-politikasi">Çerez Politikası</a></li>
        </ul>
      </div>
      <div>
        <h4>İletişim</h4>
        <ul class="foot-contact">
          <li><span class="fc"><svg><use href="#i-phone"/></svg></span><div><small>7/24 Telefon</small><a href="tel:<?= e(tel()) ?>" class="js-call foot-phone"><?= e(tel_gorunen()) ?></a></div></li>
          <li><span class="fc fc-wa"><svg><use href="#i-wa"/></svg></span><div><small>WhatsApp</small><a href="<?= e(wa('Merhaba, tesisat için bilgi almak istiyorum.')) ?>" class="js-wa" target="_blank" rel="noopener">Mesaj gönderin</a></div></li>
          <?php if ($eposta): ?><li><span class="fc"><svg><use href="#i-mail"/></svg></span><div><small>E-posta</small><a href="mailto:<?= e($eposta) ?>"><?= e($eposta) ?></a></div></li><?php endif; ?>
          <?php if ($adres): ?><li><span class="fc"><svg><use href="#i-pin"/></svg></span><div><small>Adres</small><a href="<?= e(yol_tarifi()) ?>" target="_blank" rel="noopener"><?= e($adres) ?></a></div></li><?php endif; ?>
          <li><span class="fc"><svg><use href="#i-clock"/></svg></span><div><small>Çalışma saatleri</small><span><?= e(metin('calisma_saatleri')) ?></span></div></li>
        </ul>
      </div>
    </div>

    <div class="foot-map">
      <iframe title="<?= e(firma_adi()) ?> konumu – Google Haritalar" src="<?= e(harita_embed()) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      <div class="foot-map-card">
        <b><?= e(firma_adi()) ?> Tesisat</b>
        <?php if ($adres): ?><p><?= e($adres) ?></p><?php endif; ?>
        <a class="btn btn-call" href="<?= e(yol_tarifi()) ?>" target="_blank" rel="noopener"><svg><use href="#i-route"/></svg>Yol tarifi al</a>
      </div>
    </div>

    <?php if ($bolgeler): ?>
    <div class="foot-regions"><b>Hizmet bölgelerimiz:</b> <?php foreach ($bolgeler as $i => $b): ?><?= $i ? ' · ' : '' ?><a href="<?= ilce_url($b) ?>"><?= e($b) ?> tesisatçı</a><?php endforeach; ?></div>
    <?php endif; ?>

    <div class="copy"><span>© <?= date('Y') ?> <?= e(firma_adi()) ?> Tesisat. Tüm hakları saklıdır.</span><span><a href="/kvkk-aydinlatma-metni">KVKK</a> · <a href="/cerez-politikasi">Çerez Politikası</a> · <button type="button" class="link-btn js-cerez-ac">Çerez tercihleri</button> · <a href="/sitemap.xml">Site haritası</a></span></div>
  </div>
</footer>

<div class="cerez" id="cerez" hidden role="dialog" aria-live="polite" aria-label="Çerez tercihleri">
  <p><?= e(metin('cerez_serit')) ?> <a href="/cerez-politikasi">Çerez Politikası</a></p>
  <div class="cerez-dugmeler">
    <button type="button" class="btn cerez-red" data-tercih="red">Sadece zorunlu</button>
    <button type="button" class="btn btn-call cerez-kabul" data-tercih="kabul">Kabul et</button>
  </div>
</div>

<div class="fab">
  <a class="f-wa js-wa" href="<?= e(wa('Merhaba, tesisat için bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><svg><use href="#i-wa"/></svg></a>
  <a class="f-call js-call" href="tel:<?= e(tel()) ?>" aria-label="Hemen ara"><svg><use href="#i-phone"/></svg></a>
</div>

<div class="mobile-bar">
  <a class="m-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg>Hemen Ara</a>
  <a class="m-wa js-wa" href="<?= e(wa('Merhaba, acil tesisatçı lazım.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
</div>

<script>
  var ADS_ID = <?= json_encode(ayar('ads_id')) ?>;
  // Google Ads dönüşüm etiketleri (Panel > SEO Ayarları). Boşsa yalnızca Analytics olayı gönderilir.
  var ADS_ETIKET = <?= json_encode(array_filter(['telefon_tikla' => ayar('ads_tel_etiket'), 'whatsapp_tikla' => ayar('ads_wa_etiket'), 'form_whatsapp' => ayar('ads_form_etiket')])) ?>;
  function track(olay) {
    if (!window.ETIKET_YUKLENDI) return; // çerez izni yoksa hiçbir şey gönderilmez
    gtag('event', olay, { event_category: 'iletisim', sayfa: location.pathname });
    if (ADS_ID && ADS_ETIKET[olay]) gtag('event', 'conversion', { send_to: ADS_ID + '/' + ADS_ETIKET[olay] });
  }
  (function () {
    var ser = document.getElementById('cerez');
    if (!ser) return;
    if (!cerezTercih()) ser.hidden = false;
    ser.querySelectorAll('[data-tercih]').forEach(function (b) {
      b.addEventListener('click', function () {
        var t = b.getAttribute('data-tercih');
        document.cookie = 'cerez_tercih=' + t + '; Max-Age=31536000; Path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
        ser.hidden = true;
        if (t === 'kabul') etiketYukle();
        else if (window.ETIKET_YUKLENDI) location.reload();
      });
    });
    document.querySelectorAll('.js-cerez-ac').forEach(function (b) { b.addEventListener('click', function () { ser.hidden = false; }); });
  })();
  var mb = document.querySelector('.menu-btn');
  if (mb) mb.addEventListener('click', function () {
    var acik = document.body.classList.toggle('menu-acik');
    mb.setAttribute('aria-expanded', acik ? 'true' : 'false');
    mb.setAttribute('aria-label', acik ? 'Menüyü kapat' : 'Menüyü aç');
  });
  document.querySelectorAll('.js-call').forEach(function (a) { a.addEventListener('click', function () { track('telefon_tikla'); }); });
  document.querySelectorAll('.js-wa').forEach(function (a) { a.addEventListener('click', function () { track('whatsapp_tikla'); }); });
</script>
</body>
</html>
<?php
}

function site_cta(?string $baslik = null, ?string $metin = null): void
{
    $baslik ??= metin('cta_baslik');
    $metin ??= metin('cta_metin');
    ?>
<section class="final">
  <div class="container">
    <div class="final-box">
      <div>
        <h2><?= e($baslik) ?></h2>
        <p><?= e($metin) ?></p>
      </div>
      <div class="final-actions">
        <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><span class="big-phone"><?= e(tel_gorunen()) ?></span></a>
        <a class="btn btn-wa btn-lg js-wa" href="<?= e(wa('Merhaba, tesisat sorunum için fotoğraf göndermek istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp'tan Fotoğraf Gönder</a>
      </div>
    </div>
  </div>
</section>
<?php
}
