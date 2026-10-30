<?php
require __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/seo.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';
    if ($islem === 'kaydet') {
        $id = (int)($_POST['id'] ?? 0);
        $baslik = kisalt(trim((string)($_POST['baslik'] ?? '')), 160);
        $sl = slug((string)($_POST['slug'] ?? '') ?: $baslik);
        $ozet = kisalt(trim((string)($_POST['ozet'] ?? '')), 300);
        $icerik = kisalt(trim((string)($_POST['icerik'] ?? '')), 30000);
        $hizmet = array_key_exists($_POST['hizmet'] ?? '', hizmet_tum()) ? $_POST['hizmet'] : '';
        $yayin = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_POST['yayin'] ?? '')) ? $_POST['yayin'] : date('Y-m-d');
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        if ($baslik === '' || $sl === '' || $icerik === '') {
            flash('Başlık ve yazı metni boş olamaz.');
            yonlendir('blog.php' . ($id ? '?id=' . $id : '?yeni=1'));
        }
        // Aynı adres başka yazıda varsa sonuna sayı ekle
        $ana = $sl; $i = 2;
        $kontrol = $pdo->prepare('SELECT id FROM blog WHERE slug = ? AND id != ?');
        while (true) { $kontrol->execute([$sl, $id]); if (!$kontrol->fetchColumn()) break; $sl = $ana . '-' . $i++; }

        if ($id) {
            $pdo->prepare('UPDATE blog SET baslik = ?, slug = ?, ozet = ?, icerik = ?, hizmet = ?, yayin = ?, aktif = ?, guncelleme = ? WHERE id = ?')
                ->execute([$baslik, $sl, $ozet, $icerik, $hizmet, $yayin, $aktif, date('Y-m-d'), $id]);
        } else {
            $pdo->prepare('INSERT INTO blog (baslik, slug, ozet, icerik, hizmet, yayin, aktif, guncelleme) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([$baslik, $sl, $ozet, $icerik, $hizmet, $yayin, $aktif, date('Y-m-d')]);
            $id = (int)$pdo->lastInsertId();
        }
        flash('Yazı kaydedildi.');
        yonlendir('blog.php?id=' . $id);
    } elseif ($islem === 'sil') {
        $pdo->prepare('DELETE FROM blog WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
        flash('Yazı silindi.');
        yonlendir('blog.php');
    }
    yonlendir('blog.php');
}

$liste = $pdo->query('SELECT id, baslik, slug, yayin, aktif FROM blog ORDER BY yayin DESC, id DESC')->fetchAll();
$secili = null;
if (!empty($_GET['id'])) {
    $s = $pdo->prepare('SELECT * FROM blog WHERE id = ?');
    $s->execute([(int)$_GET['id']]);
    $secili = $s->fetch() ?: null;
}
$yeni = !$secili && isset($_GET['yeni']);
$f = $secili ?: ['id' => 0, 'baslik' => '', 'slug' => '', 'ozet' => '', 'icerik' => '', 'hizmet' => '', 'yayin' => date('Y-m-d'), 'aktif' => 1];

panel_ust('Blog', $u);
?>
<h1>Blog</h1>
<p class="alt-baslik">Sitedeki <a href="/blog" target="_blank">/blog</a> sayfası. Düzenli yazı eklemek Google'da daha fazla aramada görünmenizi sağlar.</p>

<div class="izgara iki">
  <section class="kart">
    <div class="dugmeler" style="margin:0 0 12px"><a class="dugme turuncu" href="blog.php?yeni=1#form">+ Yeni yazı</a></div>
    <div class="tablo-sar"><table>
      <tr><th>Başlık</th><th style="width:110px">Tarih</th><th>Durum</th><th></th></tr>
      <?php foreach ($liste as $y): ?>
        <tr>
          <td><a href="blog.php?id=<?= $y['id'] ?>#form"><b><?= e($y['baslik']) ?></b></a></td>
          <td><?= e(date('d.m.Y', strtotime($y['yayin']))) ?></td>
          <td><?= $y['aktif'] ? 'Yayında' : 'Taslak' ?></td>
          <td><a class="dugme gri kucuk" href="/blog/<?= e($y['slug']) ?>" target="_blank">Görüntüle</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$liste): ?><tr><td colspan="4">Henüz yazı yok.</td></tr><?php endif; ?>
    </table></div>
  </section>

  <div>
  <?php if ($secili || $yeni): ?>
  <section class="kart" id="form">
    <h2><?= $secili ? 'Yazıyı düzenle' : 'Yeni yazı' ?></h2>
    <form method="post">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="kaydet">
      <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
      <label for="baslik">Başlık</label>
      <input id="baslik" name="baslik" value="<?= e($f['baslik']) ?>" required placeholder="Örn. Kombi basıncı neden düşer?">
      <label for="slug">Adres (boş bırakırsanız başlıktan oluşur)</label>
      <input id="slug" name="slug" value="<?= e($f['slug']) ?>" placeholder="kombi-basinci-neden-duser">
      <label for="ozet">Kısa özet (Google'da başlığın altında görünür, 1-2 cümle)</label>
      <textarea id="ozet" name="ozet" style="min-height:80px"><?= e($f['ozet']) ?></textarea>
      <label for="icerik">Yazı metni</label>
      <textarea id="icerik" name="icerik" style="min-height:320px" required><?= e($f['icerik']) ?></textarea>
      <p class="not-kucuk" style="margin:-6px 0 14px">Paragrafları boş satırla ayırın. Ara başlık için satırı <b>## </b> ile, madde için <b>- </b> ile başlatın.</p>
      <label for="hizmet">İlgili hizmet (yazının altında gösterilir)</label>
      <select id="hizmet" name="hizmet">
        <option value="">Yok</option>
        <?php foreach (hizmet_tum() as $sl => $h): ?><option value="<?= $sl ?>" <?= $f['hizmet'] === $sl ? 'selected' : '' ?>><?= e($h['ad']) ?></option><?php endforeach; ?>
      </select>
      <label for="yayin">Yayın tarihi</label>
      <input id="yayin" name="yayin" type="date" value="<?= e(substr((string)$f['yayin'], 0, 10)) ?>">
      <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="aktif" value="1" <?= $f['aktif'] ? 'checked' : '' ?> style="width:auto;margin:0"> Sitede yayınla</label>
      <div class="dugmeler"><button class="dugme turuncu">Kaydet</button><a class="dugme gri" href="blog.php">Kapat</a></div>
    </form>
    <?php if ($secili): ?>
      <form method="post" onsubmit="return confirm('Bu yazı silinsin mi?')" style="margin-top:12px">
        <?= csrf_alan() ?><input type="hidden" name="islem" value="sil"><input type="hidden" name="id" value="<?= (int)$secili['id'] ?>">
        <button class="dugme kirmizi kucuk">Yazıyı sil</button>
      </form>
    <?php endif; ?>
  </section>
  <?php else: ?>
  <section class="kart">
    <h2>Yazı fikirleri</h2>
    <ul class="gecmis">
      <li>Müşterilerin telefonda en çok sorduğu soruları yazıya çevirin.</li>
      <li>Mevsimlik konular: kışın donan borular, yazın su basıncı.</li>
      <li>Yaptığınız bir işi anlatın: "Şirinevler'de kırmadan su kaçağı tespiti".</li>
    </ul>
  </section>
  <?php endif; ?>
  </div>
</div>
<?php panel_alt();
