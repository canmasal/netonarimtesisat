<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';
    if ($islem === 'ekle') {
        $ad = kisalt((string)($_POST['ad'] ?? ''), 60);
        if ($ad !== '') {
            $sira = (int)$pdo->query('SELECT COALESCE(MAX(sira),0) + 10 FROM bolgeler')->fetchColumn();
            $pdo->prepare('INSERT OR IGNORE INTO bolgeler (ad, sira) VALUES (?, ?)')->execute([$ad, $sira]);
            flash($ad . ' eklendi.');
        }
    } elseif ($islem === 'kaydet') {
        $s = $pdo->prepare('UPDATE bolgeler SET sira = ?, aktif = ? WHERE id = ?');
        foreach ((array)($_POST['sira'] ?? []) as $id => $sira) {
            $s->execute([(int)$sira, isset($_POST['aktif'][$id]) ? 1 : 0, (int)$id]);
        }
        flash('Bölgeler kaydedildi. Sitedeki liste güncellendi.');
    } elseif ($islem === 'icerik') {
        $id = (int)($_POST['id'] ?? 0);
        $mah = implode(', ', array_filter(array_map('trim', preg_split('/[,\n]+/', (string)($_POST['mahalleler'] ?? '')))));
        $pdo->prepare('UPDATE bolgeler SET aciklama = ?, mahalleler = ?, guncelleme = ? WHERE id = ?')
            ->execute([kisalt((string)($_POST['aciklama'] ?? ''), 5000), kisalt($mah, 3000), simdi(), $id]);
        flash('İlçe sayfası içeriği kaydedildi.');
        yonlendir('bolgeler.php?id=' . $id);
    } elseif ($islem === 'mahalle_icerik') {
        $id = (int)($_POST['id'] ?? 0);
        $mh = kisalt((string)($_POST['mahalle'] ?? ''), 80);
        $metin = kisalt((string)($_POST['aciklama'] ?? ''), 5000);
        if ($mh !== '') {
            $pdo->prepare('INSERT INTO mahalle_icerik (bolge_id, ad, aciklama, guncelleme) VALUES (?, ?, ?, ?)
                ON CONFLICT(bolge_id, ad) DO UPDATE SET aciklama = excluded.aciklama, guncelleme = excluded.guncelleme')
                ->execute([$id, $mh, $metin, simdi()]);
            flash($mh . ' mahalle sayfası kaydedildi.');
        }
        yonlendir('bolgeler.php?id=' . $id . '&mh=' . rawurlencode($mh) . '#mahalle');
    } elseif ($islem === 'sil') {
        $pdo->prepare('DELETE FROM bolgeler WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
        flash('Bölge silindi.');
    }
    yonlendir('bolgeler.php');
}

$liste = $pdo->query('SELECT * FROM bolgeler ORDER BY sira, id')->fetchAll();
$secili = null;
foreach ($liste as $b) if ((int)$b['id'] === (int)($_GET['id'] ?? 0)) $secili = $b;
panel_ust('Hizmet Bölgeleri', $u);
?>
<h1>Hizmet bölgeleri</h1>
<p class="alt-baslik">Sitedeki “Hizmet Bölgeleri” bölümü ve formdaki ilçe listesi buradan gelir. Pasif yaptığınız ilçe sitede görünmez.</p>

<div class="izgara iki">
  <section class="kart">
    <form method="post" id="bolge-form">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="kaydet">
      <div class="tablo-sar"><table>
        <tr><th>İlçe</th><th style="width:90px">Sıra</th><th>Sitede</th><th>Sayfa içeriği</th><th></th></tr>
        <?php foreach ($liste as $b): ?>
          <tr>
            <td><b><?= e($b['ad']) ?></b></td>
            <td><input type="number" name="sira[<?= $b['id'] ?>]" value="<?= (int)$b['sira'] ?>" style="margin:0;padding:6px 8px"></td>
            <td><input type="checkbox" name="aktif[<?= $b['id'] ?>]" value="1" <?= $b['aktif'] ? 'checked' : '' ?>></td>
            <td><a class="dugme gri kucuk" href="bolgeler.php?id=<?= $b['id'] ?>#icerik"><?= trim((string)$b['aciklama']) !== '' ? '✓ Düzenle' : 'Yaz' ?></a></td>
            <td><button class="dugme kirmizi kucuk" form="sil-<?= $b['id'] ?>">Sil</button></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
      <button class="dugme turuncu" style="margin-top:12px">Değişiklikleri Kaydet</button>
    </form>
    <?php foreach ($liste as $b): ?>
      <form method="post" id="sil-<?= $b['id'] ?>" onsubmit="return confirm('<?= e($b['ad']) ?> silinsin mi?')">
        <?= csrf_alan() ?><input type="hidden" name="islem" value="sil"><input type="hidden" name="id" value="<?= $b['id'] ?>">
      </form>
    <?php endforeach; ?>
  </section>

  <div>
  <?php if ($secili): ?>
  <section class="kart" id="icerik">
    <h2><?= e($secili['ad']) ?> sayfası içeriği</h2>
    <p class="not-kucuk" style="margin:-4px 0 14px">Sayfa: <a href="<?= ilce_url($secili['ad']) ?>" target="_blank"><?= e(ilce_url($secili['ad'])) ?></a>. Her ilçeye <b>kendine özgü</b> metin yazmak, Google'ın bu sayfaları kopya saymaması için önemlidir.</p>
    <form method="post">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="icerik">
      <input type="hidden" name="id" value="<?= $secili['id'] ?>">
      <label for="aciklama">İlçeye özel açıklama</label>
      <textarea id="aciklama" name="aciklama" style="min-height:180px" placeholder="Örn. <?= e($secili['ad']) ?>'de eski binalarda sık görülen galvaniz boru kaçakları, bölgede yaptığımız işler, ulaşım süremiz... Paragrafları boş satırla ayırın."><?= e($secili['aciklama']) ?></textarea>
      <label for="mahalleler">Hizmet verilen mahalleler (virgülle ayırın)</label>
      <textarea id="mahalleler" name="mahalleler" placeholder="Örn. Ataköy, Yeşilköy, Kartaltepe"><?= e($secili['mahalleler']) ?></textarea>
      <div class="dugmeler"><button class="dugme turuncu">Kaydet</button><a class="dugme gri" href="bolgeler.php">Kapat</a></div>
    </form>
  </section>
  <?php
    $mListe = bolge_mahalleleri($secili);
    $mSecili = null;
    foreach ($mListe as $m) if ($m === (string)($_GET['mh'] ?? '')) $mSecili = $m;
  ?>
  <section class="kart" id="mahalle">
    <h2><?= e($secili['ad']) ?> mahalle sayfaları</h2>
    <p class="not-kucuk" style="margin:-4px 0 14px">Yukarıdaki mahalle listesindeki her mahalle için sitede ayrı sayfa oluşur (örn. “Şirinevler tesisatçı”). Mahalleye <b>özgün</b> 2-3 cümle yazmak Google'da çıkma şansını belirgin şekilde artırır.</p>
    <?php if ($mListe): ?>
      <div class="tablo-sar"><table>
        <tr><th>Mahalle</th><th>Sayfa</th><th>Özel metin</th></tr>
        <?php foreach ($mListe as $m): $mm = mahalle_metni($secili, $m); ?>
          <tr>
            <td><b><?= e($m) ?></b></td>
            <td><a href="<?= e(mahalle_url($secili['ad'], $m)) ?>" target="_blank">Görüntüle</a></td>
            <td><a class="dugme gri kucuk" href="bolgeler.php?id=<?= $secili['id'] ?>&mh=<?= rawurlencode($m) ?>#mahalle"><?= $mm['kaynak'] === 'panel' ? '✓ Düzenle' : ($mm['kaynak'] === 'hazir' ? 'Hazır metin · Düzenle' : 'Yaz') ?></a></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
    <?php else: ?>
      <p class="not-kucuk">Önce yukarıya mahalleleri virgülle ayırarak yazıp kaydedin.</p>
    <?php endif; ?>
    <?php if ($mSecili !== null): $mm = mahalle_metni($secili, $mSecili); ?>
      <form method="post" style="margin-top:16px">
        <?= csrf_alan() ?>
        <input type="hidden" name="islem" value="mahalle_icerik">
        <input type="hidden" name="id" value="<?= $secili['id'] ?>">
        <input type="hidden" name="mahalle" value="<?= e($mSecili) ?>">
        <label for="m-aciklama"><?= e($mSecili) ?> için özel metin</label>
        <textarea id="m-aciklama" name="aciklama" style="min-height:160px" placeholder="Örn. <?= e($mSecili) ?>'de yaptığımız işler, bölgedeki binaların tesisat sorunları, ulaşım süremiz... Paragrafları boş satırla ayırın."><?= e($mm['metin']) ?></textarea>
        <p class="not-kucuk" style="margin:-6px 0 14px">Boş bırakırsanız hazır metin (varsa) kullanılır.</p>
        <div class="dugmeler"><button class="dugme turuncu">Kaydet</button><a class="dugme gri" href="bolgeler.php?id=<?= $secili['id'] ?>#mahalle">Kapat</a></div>
      </form>
    <?php endif; ?>
  </section>
  <?php endif; ?>
  <section class="kart">
    <h2>Yeni ilçe ekle</h2>
    <form method="post">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="ekle">
      <label for="ad">İlçe adı</label>
      <input id="ad" name="ad" required placeholder="örn. Kadıköy">
      <button class="dugme">Ekle</button>
    </form>
  </section>
  </div>
</div>
<?php panel_alt();
