<?php
// /blog  ve  /blog/{yazi}
require __DIR__ . '/inc/site-layout.php';

$slug = (string)($_GET['s'] ?? '');

if ($slug === '') {
    // ---- Blog listesi ----
    $yazilar = blog_yazilari();
    site_ust([
        'baslik'   => 'Tesisat Blogu | Su Kaçağı, Tıkanıklık ve Acil Durum Rehberleri – Net Onarım',
        'aciklama' => 'Su kaçağı belirtileri, gider tıkanıklığını önleme ve acil durumlarda yapılması gerekenler. Net Onarım ustalarından pratik tesisat rehberleri.',
        'yol'      => '/blog',
        'schema'   => [
            schema_isletme(),
            [
                '@type' => 'Blog', '@id' => site_url('/blog#blog'), 'url' => site_url('/blog'), 'name' => 'Net Onarım Tesisat Blogu', 'inLanguage' => 'tr-TR',
                'publisher' => ['@id' => site_url('/#isletme')],
                'blogPost' => array_map(fn($y) => ['@type' => 'BlogPosting', 'headline' => $y['baslik'], 'url' => site_url('/blog/' . $y['slug']), 'datePublished' => $y['yayin']], $yazilar),
            ],
            schema_breadcrumb([['Ana Sayfa', '/'], ['Blog', '/blog']]),
        ],
    ]);
    ?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li>Blog</li></ol>
    <h1>Tesisat <span>Blogu</span></h1>
    <p class="lead">Ustalarımızın sahada en sık karşılaştığı sorunlar ve evde uygulayabileceğiniz pratik çözümler.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($yazilar): ?>
    <div class="blog-grid">
      <?php foreach ($yazilar as $y): $h = hizmetler()[$y['hizmet']] ?? null; ?>
        <article class="blog-card">
          <a href="/blog/<?= e($y['slug']) ?>" class="blog-card-link">
            <div class="blog-meta"><?php if ($h): ?><span class="blog-tag"><svg><use href="#<?= $h['ikon'] ?>"/></svg><?= e($h['ad']) ?></span><?php endif; ?><time datetime="<?= e($y['yayin']) ?>"><?= e(tr_tarih($y['yayin'])) ?></time></div>
            <h2><?= e($y['baslik']) ?></h2>
            <p><?= e($y['ozet']) ?></p>
            <span class="blog-more">Devamını oku <svg><use href="#i-arrow"/></svg></span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p>Yakında burada tesisat rehberleri yayınlayacağız.</p>
    <?php endif; ?>
  </div>
</section>
<?php site_cta(); ?>
</main>
<?php
    site_alt();
    exit;
}

// ---- Tek yazı ----
$y = blog_getir($slug);
if (!$y) {
    // Silinmiş ya da yayından kaldırılmış yazı: blog sayfasına yönlendir
    header('Location: /blog', true, 301);
    exit;
}
$h = hizmetler()[$y['hizmet']] ?? null;
$yol = '/blog/' . $y['slug'];
$digerleri = array_values(array_filter(blog_yazilari(), fn($x) => $x['id'] !== $y['id']));
$digerleri = array_slice($digerleri, 0, 3);

site_ust([
    'baslik'   => $y['baslik'] . ' – Net Onarım',
    'aciklama' => $y['ozet'],
    'yol'      => $yol,
    'schema'   => [
        schema_isletme(),
        [
            '@type' => 'BlogPosting', '@id' => site_url($yol . '#yazi'), 'url' => site_url($yol),
            'headline' => $y['baslik'], 'description' => $y['ozet'], 'inLanguage' => 'tr-TR',
            'datePublished' => $y['yayin'], 'dateModified' => $y['guncelleme'] ?: $y['yayin'],
            'image' => site_url('/assets/og.jpg'),
            'author' => ['@type' => 'Organization', 'name' => 'Net Onarım Tesisat', 'url' => site_url('/')],
            'publisher' => ['@id' => site_url('/#isletme')],
            'mainEntityOfPage' => site_url($yol),
        ],
        schema_breadcrumb([['Ana Sayfa', '/'], ['Blog', '/blog'], [$y['baslik'], $yol]]),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li><a href="/blog">Blog</a></li><li><?= e($y['baslik']) ?></li></ol>
    <h1><?= e($y['baslik']) ?></h1>
    <p class="lead"><?= e($y['ozet']) ?></p>
    <p class="blog-hero-meta"><time datetime="<?= e($y['yayin']) ?>"><?= e(tr_tarih($y['yayin'])) ?></time> · <?= okuma_suresi($y['icerik']) ?> dk okuma · Net Onarım Tesisat</p>
  </div>
</section>

<section class="section">
  <div class="container content-grid">
    <article class="prose blog-body">
      <?= blog_html($y['icerik']) ?>
      <?php if ($h): ?>
        <div class="blog-service">
          <div class="ico"><svg><use href="#<?= $h['ikon'] ?>"/></svg></div>
          <div>
            <b><?= e($h['ad']) ?> hizmetimiz</b>
            <p><?= e($h['kart']) ?></p>
            <a href="/hizmet/<?= e($y['hizmet']) ?>">Hizmet detayları →</a>
          </div>
        </div>
      <?php endif; ?>
    </article>

    <aside class="side-box">
      <h3>Bu sorunu mu yaşıyorsunuz?</h3>
      <p>Telefonu 7/24 açıyoruz. Sorunun fotoğrafını WhatsApp'tan gönderirseniz ön bilgi verelim.</p>
      <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-wa js-wa" href="<?= e(wa('Merhaba, "' . $y['baslik'] . '" yazınızı okudum, bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener"><svg><use href="#i-wa"/></svg>WhatsApp</a>
    </aside>
  </div>
</section>

<?php if ($digerleri): ?>
<section class="section" style="padding-top:0">
  <div class="container">
    <h2 style="font-size:24px;margin-bottom:18px">Diğer yazılar</h2>
    <div class="blog-grid">
      <?php foreach ($digerleri as $d): ?>
        <article class="blog-card">
          <a href="/blog/<?= e($d['slug']) ?>" class="blog-card-link">
            <div class="blog-meta"><time datetime="<?= e($d['yayin']) ?>"><?= e(tr_tarih($d['yayin'])) ?></time></div>
            <h2><?= e($d['baslik']) ?></h2>
            <span class="blog-more">Devamını oku <svg><use href="#i-arrow"/></svg></span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php site_cta(); ?>
</main>
<?php site_alt();
