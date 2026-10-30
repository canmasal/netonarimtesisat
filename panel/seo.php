<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();

$alanlar = [
    'site_url'         => ['Site adresi', 'https://netonarimtesisat.com.tr', 'Canonical, sitemap ve schema adreslerinde kullanılır. Sonunda / olmadan yazın.'],
    'firma_aciklama'   => ['Firma tanımı (Google ve yapay zekâ için)', '', 'Bir iki cümle; ne yaptığınızı ve nerede hizmet verdiğinizi net yazın.'],
    'deneyim'          => ['Tecrübe süresi', '20 yılı aşkın', 'Sitede "20 yılı aşkın süredir..." şeklinde kullanılır.'],
    'merkez_ilce'      => ['Merkez ilçe', 'Bahçelievler', 'Firmanızın bulunduğu ilçe.'],
    'enlem'            => ['Enlem', 'Örn. 40.9780', 'Google Haritalar\'da işletmenize sağ tıklayınca çıkan ilk sayı.'],
    'boylam'           => ['Boylam', 'Örn. 28.8720', 'İkinci sayı.'],
    'gbp_url'          => ['Google İşletme Profili linki', 'https://g.page/...', 'Haritalarda "Paylaş" ile aldığınız link.'],
    'google_dogrulama' => ['Google Search Console doğrulama kodu', 'Yalnızca content="..." içindeki değer', 'Search Console → Mülk ekle → HTML etiketi.'],
    'bing_dogrulama'   => ['Bing Webmaster doğrulama kodu', '', 'Bing ayrıca ChatGPT aramasına veri sağlar.'],
    'yandex_dogrulama' => ['Yandex Webmaster doğrulama kodu', '', 'Türkiye\'de Yandex kullanımı için.'],
    'ga4_id'           => ['Google Analytics 4 ölçüm kimliği', 'G-XXXXXXXXXX', ''],
    'ads_id'           => ['Google Ads etiket kimliği', 'AW-XXXXXXXXX', 'Google Ads → Hedefler → Dönüşümler → Etiket ayarları → "Kendiniz yükleyin" bölümündeki AW- ile başlayan kimlik.'],
    'ads_tel_etiket'   => ['Ads dönüşüm etiketi: Telefon tıklaması', 'Örn. AbC1dEfGhIjK', 'Google Ads\'te "Telefon tıklaması" dönüşümünü oluşturunca verilen send_to değerinde / işaretinden SONRAKİ kısım.'],
    'ads_wa_etiket'    => ['Ads dönüşüm etiketi: WhatsApp tıklaması', 'Örn. XyZ2aBcDeFgH', '"WhatsApp tıklaması" dönüşümünün etiketi.'],
    'ads_form_etiket'  => ['Ads dönüşüm etiketi: Form gönderimi', 'Örn. QwE3rTyUiOpA', '"Servis talebi formu" dönüşümünün etiketi.'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = db()->prepare('INSERT INTO ayarlar (anahtar, deger) VALUES (?, ?) ON CONFLICT(anahtar) DO UPDATE SET deger = excluded.deger');
    foreach ($alanlar as $k => $_) {
        $v = trim((string)($_POST[$k] ?? ''));
        if ($k === 'site_url') $v = rtrim($v, '/') ?: AYAR_VARSAYILAN['site_url'];
        if (in_array($k, ['enlem', 'boylam'], true)) $v = str_replace(',', '.', $v);
        if (in_array($k, ['enlem', 'boylam'], true) && $v !== '' && !is_numeric($v)) $v = '';
        if (in_array($k, ['google_dogrulama', 'bing_dogrulama', 'yandex_dogrulama'], true) && preg_match('/content="([^"]+)"/', $v, $m)) $v = $m[1];
        // "AW-123456789/AbCdEf" şeklinde yapıştırılırsa parçalara ayır
        if ($k === 'ads_id' && str_contains($v, '/')) $v = strstr($v, '/', true);
        if (str_starts_with($k, 'ads_') && $k !== 'ads_id' && str_contains($v, '/')) $v = substr(strrchr($v, '/'), 1);
        if (str_starts_with($k, 'ads_') || $k === 'ga4_id') $v = preg_replace('/[^A-Za-z0-9_\-]/', '', $v);
        $s->execute([$k, kisalt($v, 600)]);
    }
    flash('SEO ayarları kaydedildi.');
    yonlendir('seo.php');
}

$site = site_url('');
panel_ust('SEO Ayarları', $u);
?>
<h1>SEO ve GEO ayarları</h1>
<p class="alt-baslik">Adres, telefon ve sosyal medya için <a href="site.php">Site Ayarları</a>. Buradaki bilgiler sitenin tüm sayfalarına, site haritasına ve Google'ın okuduğu yapılandırılmış veriye otomatik eklenir.</p>

<div class="izgara iki">
  <form class="kart" method="post">
    <?= csrf_alan() ?>
    <?php foreach ($alanlar as $k => [$etiket, $ornek, $ipucu]): ?>
      <label for="<?= $k ?>"><?= e($etiket) ?></label>
      <?php if ($k === 'firma_aciklama'): ?>
        <textarea id="<?= $k ?>" name="<?= $k ?>"><?= e(ayar($k)) ?></textarea>
      <?php else: ?>
        <input id="<?= $k ?>" name="<?= $k ?>" value="<?= e(ayar($k)) ?>" placeholder="<?= e($ornek) ?>">
      <?php endif; ?>
      <?php if ($ipucu): ?><p class="not-kucuk" style="margin:-6px 0 14px"><?= e($ipucu) ?></p><?php endif; ?>
    <?php endforeach; ?>
    <button class="dugme turuncu">Kaydet</button>
  </form>

  <div>
    <section class="kart">
      <h2>Otomatik oluşan dosyalar</h2>
      <ul class="gecmis">
        <li><a href="/sitemap.xml" target="_blank">/sitemap.xml</a><small>Ana sayfa, <?= count(hizmetler()) ?> hizmet ve <?= count(aktif_bolgeler()) ?> ilçe sayfası</small></li>
        <li><a href="/robots.txt" target="_blank">/robots.txt</a><small>Panel arama motorlarına kapalı, site haritası bildirili</small></li>
        <li><a href="/llms.txt" target="_blank">/llms.txt</a><small>Yapay zekâ aramaları için firma özeti</small></li>
      </ul>
    </section>
    <section class="kart">
      <h2>Kontrol araçları</h2>
      <ul class="gecmis">
        <li><a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a><small>Siteyi ekleyin, Site haritaları bölümüne <b>sitemap.xml</b> yazıp gönderin</small></li>
        <li><a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a><small>Search Console'dan tek tıkla içe aktarılabilir</small></li>
        <li><a href="https://search.google.com/test/rich-results?url=<?= rawurlencode($site . '/') ?>" target="_blank" rel="noopener">Zengin sonuç testi</a><small>Schema (işletme, SSS) hatasız mı?</small></li>
        <li><a href="https://pagespeed.web.dev/analysis?url=<?= rawurlencode($site . '/') ?>" target="_blank" rel="noopener">PageSpeed Insights</a><small>Mobil hız puanı</small></li>
        <li><a href="https://business.google.com/" target="_blank" rel="noopener">Google İşletme Profili</a><small>Haritalarda çıkmanın en önemli adımı</small></li>
      </ul>
    </section>
  </div>
</div>
<?php panel_alt();
