<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

const IKONLAR = [
    'i-radar' => 'Radar (tespit)', 'i-cam' => 'Kamera', 'i-pipe' => 'Boru', 'i-faucet' => 'Musluk',
    'i-wrench' => 'İngiliz anahtarı', 'i-heat' => 'Petek', 'i-drop' => 'Damla', 'i-bolt' => 'Şimşek (acil)', 'i-shield' => 'Kalkan',
];

// Metin <-> dizi dönüşümleri
$bolumMetin = fn(array $b) => implode("\n\n", array_map(fn($x) => '## ' . $x[0] . "\n" . $x[1], $b));
$ciftMetin = fn(array $b) => implode("\n", array_map(fn($x) => $x[0] . ' | ' . $x[1], $b));
$metinBolum = function (string $t): array {
    $out = []; $bas = null; $buf = [];
    foreach (preg_split('/\R/', $t) as $sat) {
        if (str_starts_with(trim($sat), '## ')) {
            if ($bas !== null) $out[] = [$bas, trim(implode("\n", $buf))];
            $bas = trim(substr(trim($sat), 3)); $buf = [];
        } else $buf[] = $sat;
    }
    if ($bas !== null) $out[] = [$bas, trim(implode("\n", $buf))];
    return array_values(array_filter($out, fn($x) => $x[0] !== '' && $x[1] !== ''));
};
$metinCift = function (string $t): array {
    $out = [];
    foreach (preg_split('/\R/', $t) as $sat) {
        [$a, $b] = array_pad(array_map('trim', explode('|', $sat, 2)), 2, '');
        if ($a !== '' && $b !== '') $out[] = [$a, $b];
    }
    return $out;
};
$j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';
    $sl = (string)($_POST['s'] ?? '');

    if ($islem === 'liste') {
        $s = $pdo->prepare('UPDATE hizmetler SET sira = ?, aktif = ? WHERE slug = ?');
        foreach ((array)($_POST['sira'] ?? []) as $k => $sira) $s->execute([(int)$sira, isset($_POST['aktif'][$k]) ? 1 : 0, (string)$k]);
        flash('Hizmet listesi kaydedildi. Sitede hemen görünür.');
        yonlendir('hizmetler.php');
    }

    if ($islem === 'ekle') {
        $ad = kisalt(trim((string)($_POST['ad'] ?? '')), 80);
        $yeni = slug($ad);
        if ($ad === '' || $yeni === '') { flash('Hizmet adı yazın.', 'hata'); yonlendir('hizmetler.php'); }
        $ana = $yeni; $i = 2;
        $var = $pdo->prepare('SELECT 1 FROM hizmetler WHERE slug = ?');
        while (true) { $var->execute([$yeni]); if (!$var->fetchColumn()) break; $yeni = $ana . '-' . $i++; }
        $kisa = mb_strtolower($ad);
        $sira = (int)$pdo->query('SELECT COALESCE(MAX(sira), 0) + 10 FROM hizmetler')->fetchColumn();
        $pdo->prepare('INSERT INTO hizmetler (slug, ad, kisa_ad, ikon, kart, baslik, meta, giris, bolumler, maddeler, sss, sira, aktif, guncelleme) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,0,?)')
            ->execute([
                $yeni, $ad, $kisa, 'i-wrench',
                $ad . ' işlerinizi deneyimli ustalarımızla hızlı ve temiz şekilde yapıyoruz.',
                $ad . ' İstanbul | 7/24 Hizmet – ' . firma_adi(),
                'İstanbul\'da ' . $kisa . ' hizmeti. İşe başlamadan net fiyat, garantili işçilik. 7/24 ulaşın: {telefon}',
                $ad . ' için ustalarımız adresinize gelir, sorunu yerinde tespit eder ve işe başlamadan size net fiyat verir.',
                $j([['Nasıl çalışıyoruz?', 'Bizi arayın ya da WhatsApp\'tan fotoğraf gönderin. Ustamız adresinize gelir, işi yerinde inceler, fiyatı söyler ve onayınızla işe başlar.']]),
                $j([]), $j([]), $sira, simdi(),
            ]);
        flash($ad . ' eklendi. Yazılarını doldurup "Sitede göster" kutusunu işaretleyin.');
        yonlendir('hizmetler.php?s=' . $yeni . '#duzenle');
    }

    $var = $pdo->prepare('SELECT * FROM hizmetler WHERE slug = ?');
    $var->execute([$sl]);
    if (!$var->fetch()) { flash('Hizmet bulunamadı.', 'hata'); yonlendir('hizmetler.php'); }

    if ($islem === 'sil') {
        $pdo->prepare('DELETE FROM hizmetler WHERE slug = ?')->execute([$sl]);
        flash('Hizmet silindi. Eski adresine gelenler hizmetler bölümüne yönlendirilir.');
        yonlendir('hizmetler.php');
    }

    if ($islem === 'sifirla' && isset(SITE_HIZMETLERI[$sl])) {
        $h = SITE_HIZMETLERI[$sl];
        $pdo->prepare('UPDATE hizmetler SET ad = ?, kisa_ad = ?, ikon = ?, kart = ?, baslik = ?, meta = ?, giris = ?, bolumler = ?, maddeler = ?, sss = ?, guncelleme = ? WHERE slug = ?')
            ->execute([$h['ad'], $h['kisa_ad'], $h['ikon'], $h['kart'], $h['baslik'], $h['meta'], $h['giris'], $j($h['bolumler']), $j($h['maddeler']), $j($h['sss']), simdi(), $sl]);
        flash($h['ad'] . ' ilk hâline döndürüldü.');
        yonlendir('hizmetler.php?s=' . $sl . '#duzenle');
    }

    if ($islem === 'kaydet') {
        $p = fn($k, $n = 600) => kisalt(str_replace("\r", '', trim((string)($_POST[$k] ?? ''))), $n);
        $ad = $p('ad', 80);
        if ($ad === '') { flash('Hizmet adı boş olamaz.', 'hata'); yonlendir('hizmetler.php?s=' . $sl . '#duzenle'); }
        $ikon = array_key_exists($_POST['ikon'] ?? '', IKONLAR) ? $_POST['ikon'] : 'i-wrench';
        $pdo->prepare('UPDATE hizmetler SET ad = ?, kisa_ad = ?, ikon = ?, kart = ?, baslik = ?, meta = ?, giris = ?, bolumler = ?, maddeler = ?, sss = ?, aktif = ?, guncelleme = ? WHERE slug = ?')
            ->execute([
                $ad, $p('kisa_ad', 80) ?: mb_strtolower($ad), $ikon, $p('kart', 400), $p('baslik', 120), $p('meta', 300), $p('giris', 1200),
                $j($metinBolum($p('bolumler', 12000))),
                $j(array_values(array_filter(array_map('trim', preg_split('/\R/', $p('maddeler', 3000)))))),
                $j($metinCift($p('sss', 8000))),
                isset($_POST['aktif']) ? 1 : 0, simdi(), $sl,
            ]);
        flash($ad . ' kaydedildi.');
        yonlendir('hizmetler.php?s=' . $sl . '#duzenle');
    }
    yonlendir('hizmetler.php');
}

$tum = hizmet_tum();
$sl = (string)($_GET['s'] ?? '');
$h = $tum[$sl] ?? null;
panel_ust('Hizmetler', $u);
?>
<h1>Hizmetler</h1>
<p class="alt-baslik">Sitedeki hizmetleri buradan ekleyin, kaldırın, sıralayın ve yazılarını düzenleyin. Menü, ana sayfa kartları, form seçenekleri ve site haritası kendiliğinden güncellenir.</p>

<div class="izgara iki">
  <section class="kart">
    <h2>Hizmet listesi</h2>
    <form method="post" id="liste-form">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="liste">
      <div class="tablo-sar"><table>
        <tr><th>Hizmet</th><th style="width:80px">Sıra</th><th>Sitede</th><th></th></tr>
        <?php foreach ($tum as $k => $x): ?>
          <tr<?= $x['aktif'] ? '' : ' style="opacity:.6"' ?>>
            <td><b><?= e($x['ad']) ?></b><br><small style="color:var(--muted)">/hizmet/<?= e($k) ?></small></td>
            <td><input type="number" name="sira[<?= e($k) ?>]" value="<?= (int)$x['sira'] ?>" style="margin:0;padding:6px 8px" aria-label="<?= e($x['ad']) ?> sırası"></td>
            <td><input type="checkbox" name="aktif[<?= e($k) ?>]" value="1" <?= $x['aktif'] ? 'checked' : '' ?> aria-label="<?= e($x['ad']) ?> sitede"></td>
            <td style="white-space:nowrap"><a class="dugme gri kucuk" href="hizmetler.php?s=<?= e($k) ?>#duzenle">Düzenle</a>
              <button class="dugme kirmizi kucuk" form="sil-<?= e($k) ?>">Sil</button></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$tum): ?><tr><td colspan="4">Henüz hizmet yok. Sağdan ekleyin.</td></tr><?php endif; ?>
      </table></div>
      <button class="dugme turuncu" style="margin-top:12px">Değişiklikleri kaydet</button>
      <p class="not-kucuk" style="margin-top:8px">"Sitede" işaretini kaldırırsanız hizmet sitede gizlenir ama silinmez; istediğiniz zaman geri açarsınız. Küçük sıra numarası önce gösterilir.</p>
    </form>
    <?php foreach ($tum as $k => $x): ?>
      <form method="post" id="sil-<?= e($k) ?>" onsubmit="return confirm('<?= e($x['ad']) ?> tamamen silinsin mi? Gizlemek için Sitede işaretini kaldırmanız yeterli.')">
        <?= csrf_alan() ?><input type="hidden" name="islem" value="sil"><input type="hidden" name="s" value="<?= e($k) ?>">
      </form>
    <?php endforeach; ?>
  </section>

  <section class="kart">
    <h2>Yeni hizmet ekle</h2>
    <form method="post">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="ekle">
      <label for="yeni-ad">Hizmet adı</label>
      <input id="yeni-ad" name="ad" required placeholder="Örn. Kombi bakımı">
      <button class="dugme">Ekle</button>
      <p class="not-kucuk" style="margin-top:8px">Yeni hizmet önce gizli eklenir. Yazılarını doldurup "Sitede göster" kutusunu işaretleyince yayına girer.</p>
    </form>
  </section>
</div>

<?php if ($h): ?>
<div class="izgara iki" id="duzenle">
  <form class="kart" method="post">
    <?= csrf_alan() ?>
    <input type="hidden" name="islem" value="kaydet">
    <input type="hidden" name="s" value="<?= e($sl) ?>">
    <h2><?= e($h['ad']) ?> sayfası</h2>

    <label style="display:flex;gap:8px;align-items:center;margin-bottom:14px"><input type="checkbox" name="aktif" value="1" <?= $h['aktif'] ? 'checked' : '' ?>> Sitede göster</label>
    <div class="satir">
      <div><label for="ad">Hizmet adı</label><input id="ad" name="ad" value="<?= e($h['ad']) ?>" required></div>
      <div><label for="kisa_ad">Kısa ad (cümle içinde)</label><input id="kisa_ad" name="kisa_ad" value="<?= e($h['kisa_ad']) ?>"></div>
    </div>
    <label for="ikon">Simge</label>
    <select id="ikon" name="ikon"><?php foreach (IKONLAR as $k => $ad): ?><option value="<?= $k ?>" <?= $h['ikon'] === $k ? 'selected' : '' ?>><?= e($ad) ?></option><?php endforeach; ?></select>
    <label for="kart">Ana sayfadaki kart yazısı</label>
    <textarea id="kart" name="kart" rows="3" style="min-height:0"><?= e($h['kart']) ?></textarea>

    <label for="baslik">Google başlığı (sekme başlığı)</label>
    <input id="baslik" name="baslik" value="<?= e($h['baslik']) ?>">
    <label for="meta">Google açıklaması</label>
    <textarea id="meta" name="meta" rows="2" style="min-height:0"><?= e($h['meta']) ?></textarea>
    <p class="not-kucuk" style="margin:-6px 0 14px">Başlık 60, açıklama 155 karakteri geçmezse Google'da kesilmeden görünür.</p>

    <label for="giris">Sayfanın giriş yazısı</label>
    <textarea id="giris" name="giris" rows="4" style="min-height:0"><?= e($h['giris']) ?></textarea>

    <label for="bolumler">Sayfa bölümleri</label>
    <textarea id="bolumler" name="bolumler" rows="10"><?= e($bolumMetin($h['bolumler'])) ?></textarea>
    <p class="not-kucuk" style="margin:-6px 0 14px">Her bölüm <b>## Başlık</b> satırıyla başlar, altına yazısı gelir.</p>

    <label for="maddeler">"Bu hizmet kapsamında" maddeleri</label>
    <textarea id="maddeler" name="maddeler" rows="5"><?= e(implode("\n", $h['maddeler'])) ?></textarea>
    <p class="not-kucuk" style="margin:-6px 0 14px">Her satır bir madde.</p>

    <label for="sss">Sık sorulan sorular</label>
    <textarea id="sss" name="sss" rows="6"><?= e($ciftMetin($h['sss'])) ?></textarea>
    <p class="not-kucuk" style="margin:-6px 0 14px">Her satır: <b>Soru | Cevap</b></p>

    <div class="dugmeler" style="position:sticky;bottom:0;background:var(--card);padding-top:10px">
      <button class="dugme turuncu">Kaydet</button>
      <?php if ($h['aktif']): ?><a class="dugme gri" href="/hizmet/<?= e($sl) ?>" target="_blank">Sitede gör</a><?php endif; ?>
      <a class="dugme gri" href="hizmetler.php">Kapat</a>
    </div>
  </form>

  <div>
    <section class="kart">
      <h2>Bilgi</h2>
      <ul class="gecmis">
        <li>Adres: <b>/hizmet/<?= e($sl) ?></b><small>Ad değişse de adres değişmez, Google'daki sıralama korunur.</small></li>
        <li>Telefon geçen yerlerde <b>{telefon}</b> yazın; Site Ayarları'ndaki numara kullanılır.</li>
      </ul>
    </section>
    <?php if (isset(SITE_HIZMETLERI[$sl])): ?>
    <section class="kart">
      <h2>İlk hâline döndür</h2>
      <p class="not-kucuk" style="margin-bottom:10px">Yazılar sitenin ilk kurulumdaki hâline döner. Sitede gösterilme ayarı değişmez.</p>
      <form method="post" onsubmit="return confirm('Bu hizmetin yazıları ilk hâline dönsün mü?')">
        <?= csrf_alan() ?><input type="hidden" name="s" value="<?= e($sl) ?>"><input type="hidden" name="islem" value="sifirla">
        <button class="dugme kirmizi kucuk">İlk hâline döndür</button>
      </form>
    </section>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
<?php panel_alt();
