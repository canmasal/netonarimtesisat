<?php
require_once __DIR__ . '/inc/site-layout.php';
http_response_code(404);
site_ust([
    'baslik' => 'Sayfa bulunamadı – Net Onarım',
    'aciklama' => 'Aradığınız sayfa bulunamadı.',
    'yol' => '/',
    'robots' => 'noindex, follow',
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <h1>Sayfa <span>bulunamadı</span></h1>
    <p class="lead">Aradığınız sayfa taşınmış veya kaldırılmış olabilir. Tesisat sorununuz için hemen arayabilirsiniz.</p>
    <div class="hero-actions">
      <a class="btn btn-call btn-lg js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
      <a class="btn btn-ghost btn-lg" href="/">Ana sayfaya dön</a>
    </div>
  </div>
</section>
</main>
<?php site_alt();
