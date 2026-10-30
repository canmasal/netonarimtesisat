<?php
// İlçe sayfası: /su-tesisatcisi/{ilce}
require __DIR__ . '/inc/site-layout.php';

$b = bolge_getir((string)($_GET['s'] ?? ''));
if (!$b) {
    require __DIR__ . '/404.php';
    exit;
}

$ad = $b['ad'];
$de = ek_de($ad);   // Bakırköy'de
$e_ = ek_e($ad);    // Bakırköy'e
$yol = ilce_url($ad);
$mahalleler = bolge_mahalleleri($b);
$digerleri = array_values(array_filter(aktif_bolgeler(), fn($x) => $x !== $ad));

$sss = [
    [$ad . ' su tesisatçısı olarak hangi işleri yapıyorsunuz?', $de . ' kırmadan su kaçağı tespiti, lavabo, tuvalet ve gider tıkanıklığı açma, tesisat yenileme, batarya ve rezervuar değişimi ile patlak boru gibi acil arızalara müdahale ediyoruz.'],
    [$e_ . ' ne kadar sürede geliyorsunuz?', 'Aradığınızda ' . $ad . ' bölgesine en yakın ustamızı yönlendiriyor, size tahmini varış saatini telefonda söylüyoruz. Acil durumlarda öncelikli müdahale ediyoruz.'],
    [$de . ' gece ve hafta sonu hizmet var mı?', 'Evet. ' . $de . ' 7 gün 24 saat, resmi tatil ve bayramlar dahil hizmet veriyoruz.'],
    [$de . ' su kaçağı tespiti fiyatı ne kadar?', 'Fiyat; kaçağın türüne, tesisatın uzunluğuna ve yapılacak onarıma göre değişir. Arızayı tespit ettikten sonra işe başlamadan net fiyat veriyoruz, onayınız olmadan işlem yapılmaz.'],
];

site_ust([
    'baslik'   => $ad . ' Tesisatçı | ' . $ad . ' Acil Su Tesisatçısı 7/24 – Net Onarım',
    'aciklama' => $ad . ' tesisatçı ve acil su tesisatçısı: kırmadan su kaçağı tespiti, tıkanıklık açma, tesisat yenileme ve acil tesisat arızaları. ' . $de . ' 7/24 servis. ☎ ' . tel_gorunen(),
    'yol'      => $yol,
    'yer'      => $ad . ', İstanbul',
    'schema'   => [
        schema_isletme(),
        [
            '@type' => 'Service',
            '@id' => site_url($yol . '#hizmet'),
            'name' => $ad . ' Su Tesisatçısı',
            'serviceType' => 'Su tesisatı hizmetleri',
            'provider' => ['@id' => site_url('/#isletme')],
            'areaServed' => ['@type' => 'AdministrativeArea', 'name' => $ad . ', İstanbul'],
            'url' => site_url($yol),
        ],
        schema_breadcrumb([['Ana Sayfa', '/'], ['Hizmet Bölgeleri', '/#bolgeler'], [$ad . ' Tesisatçı', $yol]]),
        schema_sss($sss),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li><a href="/#bolgeler">Hizmet Bölgeleri</a></li><li><?= e($ad) ?></li></ol>
    <h1><span><?= e($ad) ?></span> Tesisatçı – Acil Su Tesisatçısı</h1>
    <p class="lead"><?= e($de) ?> kırmadan dökmeden cihazla su kaçağı tespiti, tıkanıklık açma ve tüm tesisat arızalarında 7/24 hizmetinizdeyiz. Arayın, size en yakın ustamız yola çıksın.</p>
    <div class="hero-actions">
      <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa btn-lg js-wa" href="<?= e(wa('Merhaba, ' . $ad . ' için tesisatçı lazım.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp'tan Yaz</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container content-grid">
    <div class="prose">
      <h2><?= e($ad) ?> için su tesisatı hizmetlerimiz</h2>
      <?php if (trim((string)$b['aciklama']) !== ''): ?>
        <?php foreach (preg_split('/\R{2,}/', trim($b['aciklama'])) as $p): ?><p><?= nl2br(e($p)) ?></p><?php endforeach; ?>
      <?php else: ?>
        <p><?= e($ad) ?> bölgesinde evlerde ve iş yerlerinde karşılaşılan su kaçağı, tıkanıklık ve tesisat arızalarına hızlı çözüm üretiyoruz. Arızanın yerini cihazla bulduğumuz için gereksiz kırım yapmıyor, işe başlamadan önce fiyatı net olarak söylüyoruz.</p>
      <?php endif; ?>
      <?php if ($ad === ayar('merkez_ilce')): ?>
        <p><b>Merkezimiz <?= e($de) ?>.</b> <?= e(ayar('deneyim')) ?> süredir <?= e($ad) ?> ve çevresinde hizmet veriyoruz<?= icerik_ham('adres') ? ': ' . e(icerik_ham('adres')) . '.' : '.' ?> <a href="/iletisim" style="color:var(--blue);font-weight:700">Yol tarifi ve iletişim →</a></p>
      <?php endif; ?>
      <ul class="ticks">
        <li><svg><use href="#i-check"/></svg><?= e($de) ?> 7/24 acil servis</li>
        <li><svg><use href="#i-check"/></svg>Kırmadan, cihazla noktasal tespit</li>
        <li><svg><use href="#i-check"/></svg>İş öncesi net fiyat, sürpriz ücret yok</li>
        <li><svg><use href="#i-check"/></svg>Garantili ve temiz işçilik</li>
      </ul>

      <div class="mini-grid" style="margin:24px 0 8px">
        <?php foreach (hizmetler() as $sl => $h): ?>
          <a class="mini" href="/hizmet/<?= $sl ?>">
            <div class="ico"><svg><use href="#<?= $h['ikon'] ?>"/></svg></div>
            <h3><?= e($ad . ' ' . $h['kisa_ad']) ?></h3>
            <p><?= e($h['kart']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if ($mahalleler): ?>
        <h2><?= e($de) ?> hizmet verdiğimiz mahalleler</h2>
        <p>Mahallenize tıklayın; o mahalleye özel tesisat ve acil tesisat bilgilerini görün.</p>
        <div class="chips"><?php foreach ($mahalleler as $mh): ?><a href="<?= e(mahalle_url($ad, $mh)) ?>"><?= e($mh === $ad ? $mh . ' Mahallesi' : $mh) ?> tesisatçı</a><?php endforeach; ?></div>
      <?php endif; ?>

      <h2>Acil durumda ne yapmalısınız?</h2>
      <p>Boru patladıysa ya da su basıyorsa önce ana su vanasını kapatın, su prizlere yaklaşıyorsa ilgili sigortayı indirin. Ardından bizi arayın; <?= e($e_) ?> en yakın ustamızı yönlendirelim.</p>

      <h2><?= e($ad) ?> su tesisatçısı – sık sorulanlar</h2>
      <div class="faq-list" style="margin:0">
        <?php foreach ($sss as $i => [$s, $c]): ?><details<?= $i === 0 ? ' open' : '' ?>><summary><?= e($s) ?></summary><p><?= e($c) ?></p></details><?php endforeach; ?>
      </div>
      <p class="updated">Son güncelleme: <?= date('d.m.Y', strtotime($b['guncelleme'] ?: '2026-10-19')) ?></p>
    </div>

    <aside class="side-box">
      <h3><?= e($ad) ?> için hemen ulaşın</h3>
      <p>Telefonu 7/24 açıyoruz. Sorunun fotoğrafını WhatsApp'tan gönderirseniz ön bilgi verelim.</p>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, ' . $ad . ' için tesisatçı lazım.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
    </aside>
  </div>
</section>

<?php if ($digerleri): ?>
<section class="section" style="padding-top:0">
  <div class="container">
    <h2 style="font-size:22px;margin-bottom:14px">Diğer hizmet bölgelerimiz</h2>
    <div class="chips"><?php foreach ($digerleri as $d): ?><a href="<?= ilce_url($d) ?>"><?= e($d) ?> tesisatçı</a><?php endforeach; ?></div>
  </div>
</section>
<?php endif; ?>

<?php site_cta($de . ' tesisat sorununuzu bugün çözelim.'); ?>
</main>
<?php site_alt();
