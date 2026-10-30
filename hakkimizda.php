<?php
// /hakkimizda
require __DIR__ . '/inc/site-layout.php';

$merkez = ayar('merkez_ilce') ?: 'İstanbul';
$deneyim = ayar('deneyim') ?: '20 yılı aşkın';
$bolgeler = aktif_bolgeler();

site_ust([
    'baslik'   => 'Hakkımızda | ' . $merkez . ' Merkezli Su Tesisatçısı – Net Onarım Tesisat',
    'aciklama' => 'Net Onarım Tesisat, ' . $merkez . ' merkezli olarak ' . $deneyim . ' süredir kırmadan su kaçağı tespiti, tıkanıklık açma ve sıhhi tesisat hizmeti veriyor. Termal kamera ve akustik dinleme ile noktasal tespit.',
    'yol'      => '/hakkimizda',
    'yer'      => $merkez . ', İstanbul',
    'schema'   => [
        schema_isletme(),
        ['@type' => 'AboutPage', '@id' => site_url('/hakkimizda#sayfa'), 'url' => site_url('/hakkimizda'), 'name' => 'Hakkımızda – Net Onarım Tesisat', 'inLanguage' => 'tr-TR', 'about' => ['@id' => site_url('/#isletme')]],
        schema_breadcrumb([['Ana Sayfa', '/'], ['Hakkımızda', '/hakkimizda']]),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li>Hakkımızda</li></ol>
    <h1><?= vurgu(metin('hak_baslik')) ?></h1>
    <p class="lead"><?= e(metin('hak_metin')) ?></p>
  </div>
</section>

<section class="trust" aria-label="Rakamlarla Net Onarım">
  <div class="container">
    <div class="trust-item"><div class="ico"><svg><use href="#i-shield"/></svg></div><div><b><?= e(deneyim_kisa()) ?></b><span>Sahada tecrübe</span></div></div>
    <div class="trust-item"><div class="ico"><svg><use href="#i-pin"/></svg></div><div><b><?= e($merkez) ?></b><span>Merkez ofisimiz</span></div></div>
    <div class="trust-item"><div class="ico"><svg><use href="#i-radar"/></svg></div><div><b>Cihazla tespit</b><span>Termal kamera · akustik dinleme</span></div></div>
    <div class="trust-item"><div class="ico"><svg><use href="#i-clock"/></svg></div><div><b>7/24</b><span>Bayram ve tatil dahil</span></div></div>
  </div>
</section>

<section class="section">
  <div class="container content-grid">
    <article class="prose">
      <?= blog_html(metin('hak_icerik')) ?>

      <h2>Çalışma prensiplerimiz</h2>
      <ul class="ticks">
        <?php foreach (ciftler('hak_ilkeler') as [$b1, $b2]): ?><li><svg><use href="#i-check"/></svg><span><b><?= e($b1) ?>:</b> <?= e($b2) ?></span></li><?php endforeach; ?>
      </ul>

      <h2>Hizmetlerimiz</h2>
      <div class="mini-grid" style="margin-top:8px">
        <?php foreach (hizmetler() as $sl => $h): ?>
          <a class="mini" href="/hizmet/<?= $sl ?>"><div class="ico"><svg><use href="#<?= $h['ikon'] ?>"/></svg></div><h3><?= e($h['ad']) ?></h3><p><?= e($h['kart']) ?></p></a>
        <?php endforeach; ?>
      </div>

      <?php if ($bolgeler): ?>
        <h2>Hizmet bölgelerimiz</h2>
        <div class="chips"><?php foreach ($bolgeler as $b): ?><a href="<?= ilce_url($b) ?>"><?= e($b) ?></a><?php endforeach; ?></div>
      <?php endif; ?>
    </article>

    <aside class="side-box">
      <h3>Merkezimiz</h3>
      <?php if (icerik_ham('adres')): ?><p><?= e(icerik_ham('adres')) ?></p><?php endif; ?>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
      <a class="btn btn-ghost-dark" href="/iletisim">İletişim bilgileri →</a>
    </aside>
  </div>
</section>

<?php site_cta('Tesisat sorununuzu bize bırakın.', 'Arayın ya da WhatsApp\'tan yazın; ' . $deneyim . ' tecrübemizle sorununuzu hızlı ve kalıcı şekilde çözelim.'); ?>
</main>
<?php site_alt();
