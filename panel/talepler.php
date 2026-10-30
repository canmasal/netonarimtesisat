<?php
require __DIR__ . '/../inc/layout.php';
$u = giris_gerekli();
$admin = $u['rol'] === 'admin';

$sekme = $_GET['durum'] ?? 'aktif';
if ($sekme !== 'aktif' && $sekme !== 'tumu' && !isset(DURUMLAR[$sekme])) $sekme = 'aktif';
$q = trim($_GET['q'] ?? '');
$personel = $admin ? (int)($_GET['personel'] ?? 0) : (int)$u['id'];

$where = [];
$par = [];
if ($sekme === 'aktif') $where[] = "t.durum IN ('yeni','atandi','yolda','islemde')";
elseif ($sekme !== 'tumu') { $where[] = 't.durum = ?'; $par[] = $sekme; }
if ($personel > 0) { $where[] = 't.personel_id = ?'; $par[] = $personel; }
if ($personel === -1) $where[] = 't.personel_id IS NULL';
if ($q !== '') {
    $where[] = '(t.ad LIKE ? OR t.telefon LIKE ? OR t.ilce LIKE ? OR t.adres LIKE ? OR t.id = ?)';
    $like = '%' . $q . '%';
    $telLike = '%' . (preg_replace('/\D+/', '', $q) ?: $q) . '%';
    array_push($par, $like, $telLike, $like, $like, (int)$q);
}

$sql = 'SELECT t.*, k.ad personel_ad FROM talepler t LEFT JOIN kullanicilar k ON k.id = t.personel_id'
    . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
    . " ORDER BY CASE t.durum WHEN 'yeni' THEN 0 WHEN 'islemde' THEN 1 WHEN 'yolda' THEN 2 WHEN 'atandi' THEN 3 ELSE 4 END,
        COALESCE(t.randevu, t.olusturma) DESC LIMIT 300";
$s = db()->prepare($sql);
$s->execute($par);
$liste = $s->fetchAll();

$ekip = $admin ? db()->query("SELECT id, ad FROM kullanicilar WHERE aktif = 1 ORDER BY ad")->fetchAll() : [];

function sekme_link(string $d): string
{
    $p = $_GET;
    $p['durum'] = $d;
    return 'talepler.php?' . http_build_query($p);
}

panel_ust($admin ? 'Talepler' : 'İşlerim', $u);
?>
<h1><?= $admin ? 'Talepler' : 'İşlerim' ?></h1>
<p class="alt-baslik"><?= $admin ? 'Gelen tüm servis talepleri. Bir talebe tıklayıp ustaya atayın.' : 'Size atanan işler. İşe tıklayıp durumunu güncelleyin.' ?></p>

<div class="sekmeler">
  <a href="<?= e(sekme_link('aktif')) ?>" class="<?= $sekme === 'aktif' ? 'secili' : '' ?>">Açık işler</a>
  <?php foreach (DURUMLAR as $k => [$etiket]): if (!$admin && $k === 'yeni') continue; ?>
    <a href="<?= e(sekme_link($k)) ?>" class="<?= $sekme === $k ? 'secili' : '' ?>"><?= e($etiket) ?></a>
  <?php endforeach; ?>
  <a href="<?= e(sekme_link('tumu')) ?>" class="<?= $sekme === 'tumu' ? 'secili' : '' ?>">Tümü</a>
</div>

<form class="filtre" method="get">
  <input type="hidden" name="durum" value="<?= e($sekme) ?>">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Ad, telefon, ilçe veya talep no ara">
  <?php if ($admin): ?>
    <select name="personel">
      <option value="0">Tüm personel</option>
      <option value="-1" <?= $personel === -1 ? 'selected' : '' ?>>Atanmamış</option>
      <?php foreach ($ekip as $p): ?>
        <option value="<?= $p['id'] ?>" <?= $personel === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['ad']) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>
  <button class="dugme gri">Filtrele</button>
</form>

<?php if (!$liste): ?>
  <div class="bos">Bu filtreye uyan kayıt yok.</div>
<?php else: ?>
  <div class="isler">
    <?php foreach ($liste as $t): ?>
      <a class="is" href="talep.php?id=<?= $t['id'] ?>">
        <div class="is-ust"><span class="is-no">#<?= $t['id'] ?> · <?= e($t['kaynak']) ?></span><?= durum_rozet($t['durum']) ?></div>
        <h3><?= e($t['ad'] ?: 'İsimsiz') ?></h3>
        <p><?= e($t['hizmet']) ?></p>
        <div class="meta">
          <span>📞 <?= e(tel_goster($t['telefon'])) ?></span>
          <?php if ($t['ilce']): ?><span>📍 <?= e($t['ilce']) ?></span><?php endif; ?>
          <?php if ($t['randevu']): ?><span>🗓 <?= tarih($t['randevu']) ?></span><?php else: ?><span>⏱ <?= tarih($t['olusturma']) ?></span><?php endif; ?>
          <?php if ($admin): ?><span>👷 <?= e($t['personel_ad'] ?: 'Atanmadı') ?></span><?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php panel_alt();
