<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';
    if ($islem === 'yukle') {
        $f = $_FILES['resimler'] ?? null;
        $tamam = 0; $hatalar = [];
        if ($f && is_array($f['name'])) {
            $sira = (int)$pdo->query('SELECT COALESCE(MIN(sira), 0) FROM galeri')->fetchColumn();
            $ekle = $pdo->prepare('INSERT INTO galeri (dosya, kucuk, baslik, en, boy, sira, olusturma) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $baslik = kisalt(trim((string)($_POST['baslik'] ?? '')), 140);
            foreach ($f['name'] as $i => $ad) {
                if ($f['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                if ($f['error'][$i] !== UPLOAD_ERR_OK) {
                    $hatalar[] = $ad . ': ' . (in_array($f['error'][$i], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 'dosya çok büyük.' : 'yüklenemedi.');
                    continue;
                }
                $s = galeri_kaydet($f['tmp_name'][$i], $ad);
                if (is_string($s)) { $hatalar[] = $s; continue; }
                $ekle->execute([$s['dosya'], $s['kucuk'], $baslik, $s['en'], $s['boy'], --$sira, simdi()]);
                $tamam++;
            }
        }
        if ($tamam) flash($tamam . ' fotoğraf yüklendi.' . ($hatalar ? ' Yüklenemeyenler: ' . implode(' ', $hatalar) : ''), $hatalar ? 'hata' : 'ok');
        else flash($hatalar ? implode(' ', $hatalar) : 'Fotoğraf seçmediniz.', 'hata');
    } elseif ($islem === 'kaydet') {
        $s = $pdo->prepare('UPDATE galeri SET baslik = ?, sira = ?, aktif = ? WHERE id = ?');
        foreach ((array)($_POST['baslik'] ?? []) as $id => $b) {
            $s->execute([kisalt(trim((string)$b), 140), (int)($_POST['sira'][$id] ?? 0), isset($_POST['aktif'][$id]) ? 1 : 0, (int)$id]);
        }
        flash('Galeri kaydedildi.');
    } elseif ($islem === 'sil') {
        $s = $pdo->prepare('SELECT dosya, kucuk FROM galeri WHERE id = ?');
        $s->execute([(int)($_POST['id'] ?? 0)]);
        if ($r = $s->fetch()) {
            foreach (array_unique([$r['dosya'], $r['kucuk']]) as $yol) {
                $tam = realpath(dirname(__DIR__) . $yol);
                $kok = realpath(dirname(__DIR__) . '/uploads/galeri');
                if ($tam && $kok && str_starts_with($tam, $kok)) @unlink($tam);
            }
            $pdo->prepare('DELETE FROM galeri WHERE id = ?')->execute([(int)$_POST['id']]);
            flash('Fotoğraf silindi.');
        }
    }
    yonlendir('galeri.php');
}

$liste = $pdo->query('SELECT * FROM galeri ORDER BY sira, id DESC')->fetchAll();
$maks = ini_get('upload_max_filesize');
panel_ust('Galeri', $u);
?>
<h1>Galeri</h1>
<p class="alt-baslik">Sitedeki <a href="/galeri" target="_blank">/galeri</a> sayfası ve ana sayfadaki "Çalışmalarımızdan" bölümü. Fotoğraflar otomatik küçültülür, telefondan doğrudan yükleyebilirsiniz.</p>

<section class="kart">
  <h2>Fotoğraf yükle</h2>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_alan() ?>
    <input type="hidden" name="islem" value="yukle">
    <label for="resimler">Fotoğraflar (birden fazla seçebilirsiniz)</label>
    <input id="resimler" name="resimler[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required>
    <label for="g-baslik">Açıklama (isteğe bağlı, hepsine yazılır)</label>
    <input id="g-baslik" name="baslik" placeholder="Örn. Şirinevler'de kırmadan su kaçağı tespiti">
    <p class="not-kucuk" style="margin:-6px 0 14px">JPG, PNG veya WEBP. Sunucunuzun tek dosya sınırı: <?= e($maks) ?>. Açıklama yazmak Google Görseller'de bulunmanıza yardım eder.</p>
    <button class="dugme turuncu">Yükle</button>
  </form>
</section>

<section class="kart">
  <h2>Fotoğraflar (<?= count($liste) ?>)</h2>
  <?php if ($liste): ?>
  <form method="post" id="g-form">
    <?= csrf_alan() ?>
    <input type="hidden" name="islem" value="kaydet">
    <div class="g-liste">
      <?php foreach ($liste as $r): ?>
        <div class="g-oge<?= $r['aktif'] ? '' : ' pasif' ?>">
          <a href="<?= e($r['dosya']) ?>" target="_blank"><img src="<?= e($r['kucuk']) ?>" alt="" loading="lazy"></a>
          <input name="baslik[<?= $r['id'] ?>]" value="<?= e($r['baslik']) ?>" placeholder="Açıklama" aria-label="Açıklama">
          <div class="g-alt">
            <label>Sıra <input type="number" name="sira[<?= $r['id'] ?>]" value="<?= (int)$r['sira'] ?>"></label>
            <label><input type="checkbox" name="aktif[<?= $r['id'] ?>]" value="1" <?= $r['aktif'] ? 'checked' : '' ?>>Sitede</label>
            <button class="dugme kirmizi kucuk" form="sil-<?= $r['id'] ?>">Sil</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="dugme turuncu" style="margin-top:14px">Değişiklikleri kaydet</button>
    <p class="not-kucuk" style="margin-top:8px">Küçük sıra numarası önce gösterilir.</p>
  </form>
  <?php foreach ($liste as $r): ?>
    <form method="post" id="sil-<?= $r['id'] ?>" onsubmit="return confirm('Bu fotoğraf silinsin mi?')"><?= csrf_alan() ?><input type="hidden" name="islem" value="sil"><input type="hidden" name="id" value="<?= $r['id'] ?>"></form>
  <?php endforeach; ?>
  <?php else: ?>
    <div class="bos">Henüz fotoğraf yok. Yukarıdan ilk fotoğraflarınızı yükleyin.</div>
  <?php endif; ?>
</section>

<style>
.g-liste{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:14px}
.g-oge{border:1px solid var(--line);border-radius:12px;padding:10px;background:#fff;min-width:0}
.g-oge.pasif{opacity:.55}
.g-oge img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;margin-bottom:8px;background:#f1f5fa}
.g-oge input:not([type=checkbox]){margin-bottom:8px}
.g-alt{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.g-alt label{display:flex;align-items:center;gap:4px;margin:0;font-size:12px}
.g-alt input[type=number]{width:64px;padding:6px 8px;margin:0}
.g-alt .dugme{margin-left:auto}
</style>
<?php panel_alt();
