<?php
// Hizmet sayfası: /hizmet/{slug}
require __DIR__ . '/inc/site-layout.php';

$sl = (string)($_GET['s'] ?? '');
$h = hizmetler()[$sl] ?? null;
if (!$h) {
    // Kaldırılmış ya da yayından alınmış hizmet: ziyaretçiyi hizmetler bölümüne yönlendir
    header('Location: /#hizmetler', true, 301);
    exit;
}
$yol = '/hizmet/' . $sl;
$bolgeler = aktif_bolgeler();

site_ust([
    'baslik'   => $h['baslik'],
    'aciklama' => $h['meta'],
    'yol'      => $yol,
    'schema'   => [
        schema_isletme(),
        [
            '@type' => 'Service',
            '@id' => site_url($yol . '#hizmet'),
            'name' => $h['ad'],
            'serviceType' => $h['ad'],
            'description' => $h['giris'],
            'provider' => ['@id' => site_url('/#isletme')],
            'areaServed' => array_map(fn($b) => ['@type' => 'AdministrativeArea', 'name' => $b . ', İstanbul'], $bolgeler),
            'url' => site_url($yol),
        ],
        schema_breadcrumb([['Ana Sayfa', '/'], ['Hizmetler', '/#hizmetler'], [$h['ad'], $yol]]),
        schema_sss($h['sss']),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li><a href="/#hizmetler">Hizmetler</a></li><li><?= e($h['ad']) ?></li></ol>
    <h1><?= e($h['ad']) ?> <span>İstanbul</span></h1>
    <p class="lead"><?= e($h['giris']) ?></p>
    <div class="hero-actions">
      <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa btn-lg js-wa" href="<?= e(wa('Merhaba, ' . $h['kisa_ad'] . ' için bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp'tan Yaz</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container content-grid">
    <article class="prose">
      <?php foreach ($h['bolumler'] as [$bas, $metin]): ?>
        <h2><?= e($bas) ?></h2>
        <p><?= e($metin) ?></p>
      <?php endforeach; ?>

      <h2>Bu hizmet kapsamında</h2>
      <ul class="ticks"><?php foreach ($h['maddeler'] as $m): ?><li><svg><use href="#i-check"/></svg><?= e($m) ?></li><?php endforeach; ?></ul>

      <h2>Sık sorulan sorular</h2>
      <div class="faq-list" style="margin:0">
        <?php foreach ($h['sss'] as $i => [$s, $c]): ?><details<?= $i === 0 ? ' open' : '' ?>><summary><?= e($s) ?></summary><p><?= e($c) ?></p></details><?php endforeach; ?>
      </div>

      <?php if ($bolgeler): ?>
        <h2><?= e($h['ad']) ?> hizmet verdiğimiz ilçeler</h2>
        <div class="chips"><?php foreach ($bolgeler as $b): ?><a href="<?= ilce_url($b) ?>"><?= e($b . ' ' . $h['kisa_ad']) ?></a><?php endforeach; ?></div>
      <?php endif; ?>
    </article>

    <aside class="side-box">
      <h3>Ücretsiz bilgi alın</h3>
      <p>Sorununuzu anlatın ya da fotoğraf gönderin; ustamız size ön bilgi versin. 7/24 açığız.</p>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, ' . $h['kisa_ad'] . ' için bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
      <h3 style="margin-top:18px">Diğer hizmetler</h3>
      <ul class="ticks" style="margin:8px 0 0">
        <?php foreach (hizmetler() as $k => $d): if ($k === $sl) continue; ?><li><a href="/hizmet/<?= $k ?>" style="color:var(--blue)"><?= e($d['ad']) ?></a></li><?php endforeach; ?>
      </ul>
    </aside>
  </div>
</section>

<?php site_cta(); ?>
</main>
<?php site_alt();
