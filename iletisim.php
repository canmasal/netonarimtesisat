<?php
// /iletisim
require __DIR__ . '/inc/site-layout.php';

$merkez = ayar('merkez_ilce') ?: 'İstanbul';
$bolgeler = aktif_bolgeler();
$adres = icerik_ham('adres');
$eposta = icerik_ham('eposta');

site_ust([
    'baslik'   => 'İletişim | 7/24 Su Tesisatçısı ' . $merkez . ' – Net Onarım Tesisat',
    'aciklama' => 'Net Onarım Tesisat iletişim: ☎ ' . tel_gorunen() . ', WhatsApp ve adres bilgileri' . ($adres ? ' (' . $adres . ')' : '') . '. 7 gün 24 saat ulaşabilirsiniz.',
    'yol'      => '/iletisim',
    'yer'      => $merkez . ', İstanbul',
    'schema'   => [
        schema_isletme(),
        ['@type' => 'ContactPage', '@id' => site_url('/iletisim#sayfa'), 'url' => site_url('/iletisim'), 'name' => 'İletişim – Net Onarım Tesisat', 'inLanguage' => 'tr-TR', 'about' => ['@id' => site_url('/#isletme')]],
        schema_breadcrumb([['Ana Sayfa', '/'], ['İletişim', '/iletisim']]),
    ],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li>İletişim</li></ol>
    <h1><?= vurgu(metin('ilt_baslik')) ?></h1>
    <p class="lead"><?= e(metin('ilt_metin')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-top">
    <div class="contact-cards">
      <a class="contact-card js-call" href="tel:<?= e(tel()) ?>">
        <span class="ico"><svg><use href="#i-phone"/></svg></span>
        <small>Telefon · 7/24</small><b><?= e(tel_gorunen()) ?></b><em>Hemen ara →</em>
      </a>
      <a class="contact-card js-wa" href="<?= e(wa('Merhaba, tesisat için bilgi almak istiyorum.')) ?>" target="_blank" rel="noopener">
        <span class="ico" style="background:#e6f9ee;color:#16a34a"><svg><use href="#i-wa"/></svg></span>
        <small>WhatsApp</small><b><?= e(icerik_ham('whatsapp') ? icerik_ham('whatsapp') : tel_gorunen()) ?></b><em>Fotoğraf gönderin →</em>
      </a>
      <?php if ($adres): ?>
      <a class="contact-card" href="<?= e(yol_tarifi()) ?>" target="_blank" rel="noopener">
        <span class="ico" style="background:#fff1e6;color:var(--orange-d)"><svg><use href="#i-pin"/></svg></span>
        <small>Merkez adresimiz</small><b style="font-size:16px"><?= e($adres) ?></b><em>Yol tarifi al →</em>
      </a>
      <?php endif; ?>
      <?php if ($eposta): ?>
      <a class="contact-card" href="mailto:<?= e($eposta) ?>">
        <span class="ico"><svg><use href="#i-mail"/></svg></span>
        <small>E-posta</small><b style="font-size:16px;overflow-wrap:anywhere"><?= e($eposta) ?></b><em>E-posta gönderin →</em>
      </a>
      <?php endif; ?>
      <div class="contact-card">
        <span class="ico"><svg><use href="#i-clock"/></svg></span>
        <small>Çalışma saatleri</small><b><?= e(metin('calisma_saatleri')) ?></b><em style="color:var(--muted)"><?= e(metin('calisma_not')) ?></em>
      </div>
    </div>
      <div class="map-box contact-map">
        <iframe title="<?= e(firma_adi()) ?> konumu – Google Haritalar" src="<?= e(harita_embed()) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        <a class="map-open" href="<?= e(yol_tarifi()) ?>" target="_blank" rel="noopener"><svg><use href="#i-route"/></svg>Google Haritalar'da aç</a>
      </div>
    </div>

    <div class="contact-grid">
      <form class="quick" id="talep" aria-label="İletişim formu" style="box-shadow:var(--shadow);border:1px solid var(--line)">
        <h3>Servis talebi bırakın</h3>
        <p>Bilgilerinizi bırakın, WhatsApp üzerinden hemen dönüş yapalım.</p>
        <label for="f-name">Adınız</label>
        <input id="f-name" name="name" type="text" placeholder="Adınız Soyadınız" required>
        <label for="f-phone">Telefonunuz</label>
        <input id="f-phone" name="phone" type="tel" inputmode="tel" placeholder="05xx xxx xx xx" required pattern="[0-9 +()\-]{10,}">
        <label for="f-service">Sorununuz</label>
        <select id="f-service" name="service"><?php foreach (hizmet_secenekleri() as $h): ?><option><?= e($h) ?></option><?php endforeach; ?></select>
        <label for="f-district">İlçe</label>
        <select id="f-district" name="district">
          <option value="">Seçin</option>
          <?php foreach ($bolgeler as $b): ?><option><?= e($b) ?></option><?php endforeach; ?>
          <option>Diğer</option>
        </select>
        <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px">
        <label class="kvkk-onay"><input type="checkbox" name="kvkk" value="1" required> <span><?= kvkk_onay_html() ?></span></label>
      <div class="note" id="f-ok" role="status" style="display:none;color:#16a34a;font-weight:700;margin:0 0 12px">✓ Talebiniz alındı, en kısa sürede dönüş yapacağız.</div>
        <button class="btn btn-wa" type="submit"><svg><use href="#i-wa"/></svg>Talebi Gönder</button>
      </form>

      <aside class="contact-side">
        <h3>Nasıl çalışıyoruz?</h3>
        <ol class="contact-steps">
          <?php foreach (array_slice(ciftler('ana_adimlar'), 0, 4) as [$b1, $b2]): ?><li><b><?= e($b1) ?></b><span><?= e($b2) ?></span></li><?php endforeach; ?>
        </ol>
        <div class="contact-acil">
          <b><?= e(metin('ana_acil_baslik')) ?></b>
          <p><?= e(metin('ana_acil_metin')) ?></p>
          <a class="btn btn-call js-call" href="tel:<?= e(tel()) ?>"><svg><use href="#i-phone"/></svg><?= e(tel_gorunen()) ?></a>
        </div>
      </aside>
    </div>
  </div>
</section>
</main>

<script>
  document.getElementById('talep').addEventListener('submit', function (e) {
    e.preventDefault();
    var f = e.target;
    if (f.website.value) return;
    try { fetch('/talep.php', { method: 'POST', body: new FormData(f), keepalive: true }); } catch (err) {}
    var msg = 'Merhaba, servis talebim var.\nAd: ' + f.name.value + '\nTelefon: ' + f.phone.value + '\nSorun: ' + f.service.value + (f.district.value ? '\nİlçe: ' + f.district.value : '');
    track('form_whatsapp');
    window.open('<?= e("https://wa.me/" . wa_no()) ?>?text=' + encodeURIComponent(msg), '_blank');
    f.reset();
    document.getElementById('f-ok').style.display = 'block';
  });
</script>
<?php site_alt();
