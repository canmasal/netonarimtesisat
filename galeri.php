<?php
// /galeri
require __DIR__ . '/inc/site-layout.php';

$resimler = galeri_resimleri();

site_ust([
    'baslik'   => 'Galeri | Çalışmalarımızdan Fotoğraflar – ' . firma_adi() . ' Tesisat',
    'aciklama' => firma_adi() . ' Tesisat çalışmalarından fotoğraflar: kırmadan su kaçağı tespiti, tıkanıklık açma ve tesisat yenileme işleri.',
    'yol'      => '/galeri',
    'schema'   => [
        schema_isletme(),
        [
            '@type' => 'ImageGallery', '@id' => site_url('/galeri#galeri'), 'url' => site_url('/galeri'),
            'name' => firma_adi() . ' Tesisat Galeri', 'inLanguage' => 'tr-TR',
            'image' => array_map(fn($r) => ['@type' => 'ImageObject', 'contentUrl' => site_url($r['dosya']), 'caption' => $r['baslik'] ?: firma_adi() . ' Tesisat'], $resimler),
        ],
        schema_breadcrumb([['Ana Sayfa', '/'], ['Galeri', '/galeri']]),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li>Galeri</li></ol>
    <h1><?= vurgu(metin('galeri_baslik')) ?></h1>
    <p class="lead"><?= e(metin('galeri_metin')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($resimler): ?>
      <div class="galeri-grid">
        <?php foreach ($resimler as $i => $r): ?>
          <figure class="galeri-oge">
            <a href="<?= e($r['dosya']) ?>" data-i="<?= $i ?>" class="js-buyut" aria-label="<?= e($r['baslik'] ?: 'Fotoğrafı büyüt') ?>">
              <img src="<?= e($r['kucuk']) ?>" alt="<?= e($r['baslik'] ?: firma_adi() . ' Tesisat çalışması') ?>" loading="lazy" width="640" height="480">
            </a>
            <?php if ($r['baslik']): ?><figcaption><?= e($r['baslik']) ?></figcaption><?php endif; ?>
          </figure>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="galeri-bos">
        <svg><use href="#i-image"/></svg>
        <p>Fotoğraflar yakında burada olacak.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($resimler): ?>
<div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="Fotoğraf görüntüleyici">
  <button class="lb-kapat" type="button" aria-label="Kapat">×</button>
  <button class="lb-ok lb-onceki" type="button" aria-label="Önceki fotoğraf">‹</button>
  <figure><img alt=""><figcaption></figcaption></figure>
  <button class="lb-ok lb-sonraki" type="button" aria-label="Sonraki fotoğraf">›</button>
</div>
<script>
(function () {
  var linkler = [].slice.call(document.querySelectorAll('.js-buyut'));
  var lb = document.getElementById('lb'), img = lb.querySelector('img'), cap = lb.querySelector('figcaption'), n = 0;
  function goster(i) {
    n = (i + linkler.length) % linkler.length;
    var a = linkler[n], k = a.querySelector('img');
    img.src = a.getAttribute('href'); img.alt = k.alt;
    var f = a.parentNode.querySelector('figcaption'); cap.textContent = f ? f.textContent : '';
    lb.hidden = false; document.body.style.overflow = 'hidden';
  }
  function kapat() { lb.hidden = true; document.body.style.overflow = ''; img.src = ''; }
  linkler.forEach(function (a, i) { a.addEventListener('click', function (e) { e.preventDefault(); goster(i); }); });
  lb.querySelector('.lb-kapat').onclick = kapat;
  lb.querySelector('.lb-onceki').onclick = function () { goster(n - 1); };
  lb.querySelector('.lb-sonraki').onclick = function () { goster(n + 1); };
  lb.addEventListener('click', function (e) { if (e.target === lb) kapat(); });
  document.addEventListener('keydown', function (e) {
    if (lb.hidden) return;
    if (e.key === 'Escape') kapat();
    if (e.key === 'ArrowLeft') goster(n - 1);
    if (e.key === 'ArrowRight') goster(n + 1);
  });
})();
</script>
<?php endif; ?>

<?php site_cta(); ?>
</main>
<?php site_alt();
