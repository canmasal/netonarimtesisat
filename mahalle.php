<?php
// Mahalle sayfası: /su-tesisatcisi/{ilce}/{mahalle}  (örn. /su-tesisatcisi/bahcelievler/sirinevler)
require __DIR__ . '/inc/site-layout.php';

$b = bolge_getir((string)($_GET['i'] ?? ''));
$mh = null;
if ($b) {
    foreach (bolge_mahalleleri($b) as $m) {
        if (slug($m) === (string)($_GET['m'] ?? '')) { $mh = $m; break; }
    }
}
if (!$b || $mh === null) {
    require __DIR__ . '/404.php';
    exit;
}

$ilce = $b['ad'];
$ad = $mh;                       // Şirinevler
$de = ek_de($ad);                // Şirinevler'de
$e_ = ek_e($ad);                 // Şirinevler'e
$ilceDe = ek_de($ilce);          // Bahçelievler'de
$yol = mahalle_url($ilce, $ad);
$merkezde = $ilce === ayar('merkez_ilce');
$ozel = mahalle_metni($b, $ad);
$kardesler = array_values(array_filter(bolge_mahalleleri($b), fn($x) => $x !== $ad));
$yer = $ad . ', ' . $ilce . ', İstanbul';

// Mahalle adı tek başına ilçeyle aynıysa (örn. Bahçelievler Mah.) "Mahallesi" ekle
$gorunen = $ad === $ilce ? $ad . ' Mahallesi' : $ad;

$giris = sec($ad . 'g', [
    $gorunen . ' tesisatçı arıyorsanız doğru yerdesiniz. ' . $de . ' su kaçağı, tıkanıklık, patlak boru ve tüm tesisat arızalarına 7/24 hızlı müdahale ediyoruz.',
    $de . ' acil tesisat ihtiyacınız için telefonumuz gece gündüz açık. Kırmadan su kaçağı tespiti, gider açma ve tesisat onarımını tek seferde çözüyoruz.',
    $gorunen . ' ve çevresinde ev ve iş yerlerine profesyonel tesisat hizmeti veriyoruz. Arızayı cihazla buluyor, işe başlamadan fiyatı net söylüyoruz.',
]);
$acil = sec($ad . 'a', [
    'Boru patladığında ya da su bastığında her dakika önemli. ' . $de . ' acil tesisatçı çağrılarında en yakın ustamızı yönlendiriyor, telefonda size tahmini varış saatini söylüyoruz.',
    'Gece yarısı patlayan bir boru, taşan bir tuvalet ya da kapanmayan bir vana… ' . $de . ' bu tür acil durumlarda 7 gün 24 saat, bayram ve tatillerde de hizmet veriyoruz.',
    $de . ' su basması, patlak boru, sayaç ve vana arızalarında hemen yola çıkıyoruz. Usta gelene kadar ana vanayı kapatmanız hasarın büyümesini önler.',
]);
$genel = sec($ad . 'x', [
    $gorunen . ' bölgesindeki apartman ve iş yerlerinde en sık karşılaştığımız sorunlar; duvar ve zemin altındaki gizli su kaçakları, mutfak ve banyo gideri tıkanıklıkları ile eskiyen borulardaki kaçaklardır. Kaçağın yerini termal kamera ve akustik dinleme cihazıyla bulduğumuz için gereksiz kırım yapmıyoruz.',
    $de . ' eski binalarda boruların zamanla çürümesi su basıncını düşürür ve kaçaklara yol açar; yeni binalarda ise kombi, batarya ve yerden ısıtma bağlantıları daha sık arıza verir. Her iki durumda da önce cihazla tespit yapıyor, sonra yalnızca gereken noktaya müdahale ediyoruz.',
    $gorunen . ' için tesisat hizmetlerimiz; kırmadan su kaçağı tespiti, robotla tıkanıklık açma, tesisat yenileme ile batarya ve rezervuar değişimini kapsar. İş bitince hattı test ediyor, alanı temiz teslim ediyoruz.',
]);

$sss = [
    [$gorunen . ' tesisatçı hizmeti veriyor musunuz?', 'Evet. ' . $de . ' su kaçağı tespiti, tıkanıklık açma, tesisat yenileme, batarya-rezervuar değişimi ve acil arıza onarımı yapıyoruz.'],
    [$de . ' acil tesisatçı gece de geliyor mu?', 'Evet. ' . $de . ' 7 gün 24 saat, resmi tatil ve bayramlar dahil acil tesisat servisi veriyoruz. Arayın, en yakın ustamız yola çıksın.'],
    [$e_ . ' ne kadar sürede geliyorsunuz?', ($merkezde ? 'Merkezimiz de ' . $ilceDe . ' olduğu için ' . $e_ . ' genellikle kısa sürede ulaşıyoruz. ' : '') . 'Aradığınızda trafik durumuna göre tahmini varış saatini telefonda net olarak söylüyoruz.'],
    [$de . ' su kaçağı tespiti ve tesisat fiyatları ne kadar?', 'Fiyat; arızanın türüne ve yapılacak işe göre değişir. Tespitten sonra, işe başlamadan net fiyat veriyoruz; onayınız olmadan işlem yapılmaz.'],
];

$guncel = $ozel['guncelleme'] ?: ($b['guncelleme'] ?: null);
$guncelTs = max(@filemtime(__FILE__) ?: 0, $guncel ? strtotime($guncel) : 0);

site_ust([
    'baslik'   => $gorunen . ' Tesisatçı | ' . $gorunen . ' Acil Tesisat 7/24 – Net Onarım',
    'aciklama' => $gorunen . ' tesisatçı: acil tesisat, kırmadan su kaçağı tespiti, tıkanıklık açma, tesisat yenileme. ' . $ilce . ' ' . $de . ' 7/24 hızlı servis. ☎ ' . tel_gorunen(),
    'yol'      => $yol,
    'yer'      => $yer,
    'schema'   => [
        schema_isletme(),
        [
            '@type' => 'Service',
            '@id' => site_url($yol . '#hizmet'),
            'name' => $gorunen . ' Tesisatçı – Acil Tesisat',
            'serviceType' => 'Su tesisatı ve acil tesisat hizmetleri',
            'provider' => ['@id' => site_url('/#isletme')],
            'areaServed' => [
                '@type' => 'Place',
                'name' => $yer,
                'containedInPlace' => ['@type' => 'AdministrativeArea', 'name' => $ilce . ', İstanbul'],
            ],
            'availableChannel' => ['@type' => 'ServiceChannel', 'servicePhone' => ['@type' => 'ContactPoint', 'telephone' => tel(), 'contactType' => 'customer service', 'availableLanguage' => 'Turkish', 'hoursAvailable' => 'Mo-Su 00:00-23:59']],
            'url' => site_url($yol),
        ],
        schema_breadcrumb([['Ana Sayfa', '/'], [$ilce . ' Tesisatçı', ilce_url($ilce)], [$gorunen . ' Tesisatçı', $yol]]),
        schema_sss($sss),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li><a href="<?= e(ilce_url($ilce)) ?>"><?= e($ilce) ?> Tesisatçı</a></li><li><?= e($gorunen) ?></li></ol>
    <h1><span><?= e($gorunen) ?></span> Tesisatçı – Acil Tesisat 7/24</h1>
    <p class="lead"><?= e($giris) ?></p>
    <div class="hero-actions">
      <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa btn-lg js-wa" href="<?= e(wa('Merhaba, ' . $gorunen . ' (' . $ilce . ') için tesisatçı lazım.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp'tan Yaz</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container content-grid">
    <div class="prose">
      <h2><?= e($gorunen) ?> tesisatçı – <?= e($ilce) ?></h2>
      <?php if ($ozel['metin'] !== ''): ?>
        <?php foreach (preg_split('/\R{2,}/', $ozel['metin']) as $p): ?><p><?= nl2br(e($p)) ?></p><?php endforeach; ?>
      <?php endif; ?>
      <p><?= e($genel) ?></p>
      <?php if ($merkezde): ?>
        <p><b>Merkezimiz <?= e($ilceDe) ?>.</b> <?= e(ayar('deneyim')) ?> süredir <?= e($gorunen) ?> ve çevresindeki mahallelere hizmet veriyoruz. <a href="/iletisim" style="color:var(--blue);font-weight:700">Yol tarifi ve iletişim →</a></p>
      <?php endif; ?>

      <h2><?= e($gorunen) ?> acil tesisatçı</h2>
      <p><?= e($acil) ?></p>
      <ul class="ticks">
        <li><svg><use href="#i-check"/></svg><?= e($de) ?> 7/24 acil tesisat servisi</li>
        <li><svg><use href="#i-check"/></svg>Kırmadan, cihazla noktasal su kaçağı tespiti</li>
        <li><svg><use href="#i-check"/></svg>İşe başlamadan net fiyat, sürpriz ücret yok</li>
        <li><svg><use href="#i-check"/></svg>Garantili ve temiz işçilik</li>
      </ul>

      <h2><?= e($de) ?> verdiğimiz tesisat hizmetleri</h2>
      <div class="mini-grid" style="margin:16px 0 8px">
        <?php foreach (hizmetler() as $sl => $h): ?>
          <a class="mini" href="/hizmet/<?= $sl ?>">
            <div class="ico"><svg><use href="#<?= $h['ikon'] ?>"/></svg></div>
            <h3><?= e($gorunen . ' ' . $h['kisa_ad']) ?></h3>
            <p><?= e($h['kart']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>

      <h2>Usta gelene kadar ne yapmalısınız?</h2>
      <p>Boru patladıysa ya da su basıyorsa önce ana su vanasını kapatın (genellikle sayacın yanındadır). Su prizlere yaklaşıyorsa ilgili sigortayı indirin. Ardından bizi arayın; <?= e($e_) ?> en yakın ustamızı yönlendirelim.</p>

      <h2><?= e($gorunen) ?> tesisatçı – sık sorulanlar</h2>
      <div class="faq-list" style="margin:0">
        <?php foreach ($sss as $i => [$s, $c]): ?><details<?= $i === 0 ? ' open' : '' ?>><summary><?= e($s) ?></summary><p><?= e($c) ?></p></details><?php endforeach; ?>
      </div>
      <p class="updated">Son güncelleme: <?= date('d.m.Y', $guncelTs ?: time()) ?></p>
    </div>

    <aside class="side-box">
      <h3><?= e($gorunen) ?> için hemen ulaşın</h3>
      <p>Telefonu 7/24 açıyoruz. Sorunun fotoğrafını WhatsApp'tan gönderirseniz ön bilgi verelim.</p>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, ' . $gorunen . ' (' . $ilce . ') için tesisatçı lazım.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
    </aside>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <?php if ($kardesler): ?>
    <h2 style="font-size:22px;margin-bottom:14px"><?= e($ilce) ?> ilçesindeki diğer mahallelerde tesisatçı</h2>
    <div class="chips" style="margin-bottom:22px"><?php foreach ($kardesler as $k): ?><a href="<?= e(mahalle_url($ilce, $k)) ?>"><?= e(($k === $ilce ? $k . ' Mahallesi' : $k)) ?> tesisatçı</a><?php endforeach; ?></div>
    <?php endif; ?>
    <p><a href="<?= e(ilce_url($ilce)) ?>" style="color:var(--blue);font-weight:700">← <?= e($ilce) ?> tesisatçı sayfasına dön</a></p>
  </div>
</section>

<?php site_cta($de . ' tesisat sorununuzu bugün çözelim.'); ?>
</main>
<?php site_alt();
