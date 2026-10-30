<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

$grup = array_key_exists($_GET['g'] ?? '', ICERIK_GRUPLAR) ? $_GET['g'] : 'firma';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $g = array_key_exists($_POST['g'] ?? '', ICERIK_GRUPLAR) ? $_POST['g'] : 'firma';
    $s = $pdo->prepare('INSERT INTO ayarlar (anahtar, deger) VALUES (?, ?) ON CONFLICT(anahtar) DO UPDATE SET deger = excluded.deger');
    foreach (ICERIK as $k => [$kg, , $varsayilan, $tip]) {
        if ($kg !== $g) continue;
        $v = str_replace("\r", '', trim((string)($_POST[$k] ?? '')));
        if ($tip === 'satir') $v = preg_replace('/\s*\n\s*/', ' ', $v);
        if ($k === 'telefon') $v = preg_replace('/[^0-9+]/', '', $v);
        if ($k === 'whatsapp') $v = preg_replace('/[^0-9+]/', '', $v);
        if ($k === 'eposta' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) { flash('E-posta adresi geçerli görünmüyor.', 'hata'); yonlendir('site.php?g=' . $g); }
        if (in_array($k, ['instagram', 'facebook'], true) && $v !== '' && !preg_match('#^https?://#', $v)) $v = 'https://' . $v;
        if ($k === 'harita' && $v !== '' && !preg_match('#https://(www\.)?google\.[a-z.]+/maps/#', $v)) { flash('Harita kodu Google Haritalar\'dan alınmış görünmüyor.', 'hata'); yonlendir('site.php?g=' . $g); }
        // Varsayılanla aynıysa kaydetme (ileride varsayılan güncellenirse o kullanılsın)
        $s->execute([$k, $v === $varsayilan ? '' : kisalt($v, 8000)]);
    }
    flash(ICERIK_GRUPLAR[$g] . ' kaydedildi. Değişiklikler sitede hemen görünür.');
    yonlendir('site.php?g=' . $g);
}

$ornekSayfa = ['firma' => '/iletisim', 'ust' => '/', 'ana' => '/', 'hak' => '/hakkimizda', 'ilt' => '/iletisim', 'galeri' => '/galeri', 'alt' => '/', 'kvkk' => '/kvkk-aydinlatma-metni'][$grup] ?? '/';
panel_ust('Site Ayarları', $u);
?>
<h1>Site ayarları</h1>
<p class="alt-baslik">Sitedeki yazıları, telefon, adres ve harita bilgilerini buradan değiştirin. Hizmet sayfaları için <a href="hizmetler.php">Hizmetler</a>, fotoğraflar için <a href="galeri.php">Galeri</a>, yazılar için <a href="blog.php">Blog</a> bölümünü kullanın.</p>

<nav class="sekmeler" aria-label="Ayar grupları">
  <?php foreach (ICERIK_GRUPLAR as $k => $ad): ?><a href="site.php?g=<?= $k ?>" class="<?= $k === $grup ? 'secili' : '' ?>"><?= e($ad) ?></a><?php endforeach; ?>
</nav>

<div class="izgara iki">
  <form class="kart" method="post">
    <?= csrf_alan() ?>
    <input type="hidden" name="g" value="<?= e($grup) ?>">
    <h2><?= e(ICERIK_GRUPLAR[$grup]) ?></h2>
    <?php foreach (ICERIK as $k => [$kg, $etiket, $varsayilan, $tip, $ipucu]): if ($kg !== $grup) continue; $deger = icerik_ham($k); ?>
      <label for="<?= $k ?>"><?= e($etiket) ?></label>
      <?php if ($tip === 'satir'): ?>
        <input id="<?= $k ?>" name="<?= $k ?>" value="<?= e($deger) ?>" placeholder="<?= e($varsayilan) ?>">
      <?php else: $satir = max(3, min(14, substr_count($deger, "\n") + 2 + (int)(mb_strlen($deger) / 90))); ?>
        <textarea id="<?= $k ?>" name="<?= $k ?>" rows="<?= $satir ?>" style="min-height:0"><?= e($deger) ?></textarea>
      <?php endif; ?>
      <?php if ($ipucu || $tip === 'liste'): ?><p class="not-kucuk" style="margin:-6px 0 14px"><?= e($ipucu ?: 'Her satır bir madde.') ?></p><?php endif; ?>
    <?php endforeach; ?>
    <div class="dugmeler" style="position:sticky;bottom:0;background:var(--card);padding-top:10px">
      <button class="dugme turuncu">Kaydet</button>
      <a class="dugme gri" href="<?= e($ornekSayfa) ?>" target="_blank">Sitede gör</a>
    </div>
  </form>

  <div>
    <section class="kart">
      <h2>Yazarken işinize yarar</h2>
      <ul class="gecmis">
        <li><b>*yıldız*</b> arasına aldığınız kelime başlıkta turuncu görünür.<small>Örn. İstanbul'da *Su Tesisatçısı*</small></li>
        <li><b>{firma}</b>, <b>{telefon}</b>, <b>{merkez}</b>, <b>{deneyim}</b> yazarsanız ilgili bilgi otomatik yazılır.<small>Telefonu değiştirince tüm sitede güncellenir.</small></li>
        <li>Bir alanı tamamen silip kaydederseniz ilk hâline döner.</li>
      </ul>
    </section>
    <?php if ($grup === 'firma'): ?>
    <section class="kart">
      <h2>Harita önizleme</h2>
      <iframe title="Harita" src="<?= e(harita_embed()) ?>" style="width:100%;height:260px;border:0;border-radius:10px" loading="lazy"></iframe>
      <p class="not-kucuk" style="margin-top:8px">Firma adı, ilçe ve SEO bilgileri için <a href="seo.php">SEO Ayarları</a>.</p>
    </section>
    <?php endif; ?>
  </div>
</div>
<?php panel_alt();
