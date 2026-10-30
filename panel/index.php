<?php
require __DIR__ . '/../inc/layout.php';

$u = kullanici();
if (!$u) yonlendir((int)db()->query('SELECT COUNT(*) FROM kullanicilar')->fetchColumn() === 0 ? 'kurulum.php' : 'giris.php');
if ($u['rol'] !== 'admin') yonlendir('talepler.php');

$pdo = db();
$bugun = date('Y-m-d') . ' 00:00:00';
$ayBasi = date('Y-m-01') . ' 00:00:00';

$sayilar = array_fill_keys(array_keys(DURUMLAR), 0);
foreach ($pdo->query('SELECT durum, COUNT(*) c FROM talepler GROUP BY durum') as $r) $sayilar[$r['durum']] = (int)$r['c'];

$s = $pdo->prepare('SELECT COUNT(*) FROM talepler WHERE olusturma >= ?');
$s->execute([$bugun]);
$bugunGelen = (int)$s->fetchColumn();

$s = $pdo->prepare("SELECT COUNT(*) c, COALESCE(SUM(ucret),0) t FROM talepler WHERE durum = 'tamamlandi' AND tamamlanma >= ?");
$s->execute([$ayBasi]);
$ay = $s->fetch();

$atanmamis = $pdo->query("SELECT * FROM talepler WHERE durum = 'yeni' ORDER BY olusturma DESC LIMIT 10")->fetchAll();

$s = $pdo->prepare("
  SELECT k.id, k.ad, k.telefon,
    SUM(CASE WHEN t.durum IN ('atandi','yolda','islemde') THEN 1 ELSE 0 END) aktif,
    SUM(CASE WHEN t.durum = 'tamamlandi' AND t.tamamlanma >= ? THEN 1 ELSE 0 END) ay_is,
    SUM(CASE WHEN t.durum = 'tamamlandi' AND t.tamamlanma >= ? THEN COALESCE(t.ucret,0) ELSE 0 END) ay_ciro
  FROM kullanicilar k LEFT JOIN talepler t ON t.personel_id = k.id
  WHERE k.aktif = 1 AND k.rol = 'personel'
  GROUP BY k.id ORDER BY k.ad");
$s->execute([$ayBasi, $ayBasi]);
$ekip = $s->fetchAll();

panel_ust('Özet', $u);
?>
<h1>Merhaba, <?= e(explode(' ', $u['ad'])[0]) ?></h1>
<p class="alt-baslik"><?= date('d.m.Y') ?> · Güncel durum özeti</p>

<div class="istat">
  <div><b><?= $sayilar['yeni'] ?></b><span>Atanmayı bekleyen</span></div>
  <div><b><?= $sayilar['atandi'] + $sayilar['yolda'] + $sayilar['islemde'] ?></b><span>Sahadaki iş</span></div>
  <div><b><?= $bugunGelen ?></b><span>Bugün gelen talep</span></div>
  <div><b><?= (int)$ay['c'] ?></b><span>Bu ay tamamlanan</span></div>
  <div><b><?= para($ay['t']) ?></b><span>Bu ay ciro</span></div>
</div>

<div class="izgara iki">
  <section class="kart">
    <h2>Atanmayı bekleyen talepler</h2>
    <?php if (!$atanmamis): ?>
      <p class="alt-baslik">Bekleyen talep yok.</p>
    <?php else: ?>
      <div class="tablo-sar"><table>
        <tr><th>#</th><th>Müşteri</th><th>Hizmet</th><th>İlçe</th><th>Geliş</th></tr>
        <?php foreach ($atanmamis as $t): ?>
          <tr>
            <td><a href="talep.php?id=<?= $t['id'] ?>">#<?= $t['id'] ?></a></td>
            <td><a href="talep.php?id=<?= $t['id'] ?>"><?= e($t['ad']) ?></a><br><small><?= e(tel_goster($t['telefon'])) ?></small></td>
            <td><?= e($t['hizmet']) ?></td>
            <td><?= e($t['ilce']) ?></td>
            <td><?= tarih($t['olusturma']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
    <?php endif; ?>
    <p style="margin-top:12px"><a href="talepler.php">Tüm taleplere git →</a></p>
  </section>

  <section class="kart">
    <h2>Ekip durumu</h2>
    <?php if (!$ekip): ?>
      <p class="alt-baslik">Henüz personel eklenmedi.</p>
      <a class="dugme" href="personel.php">Personel ekle</a>
    <?php else: ?>
      <div class="tablo-sar"><table>
        <tr><th>Usta</th><th>Aktif iş</th><th>Bu ay</th><th>Ciro</th></tr>
        <?php foreach ($ekip as $p): ?>
          <tr>
            <td><a href="talepler.php?personel=<?= $p['id'] ?>"><?= e($p['ad']) ?></a></td>
            <td><?= (int)$p['aktif'] ?></td>
            <td><?= (int)$p['ay_is'] ?></td>
            <td><?= para($p['ay_ciro']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
    <?php endif; ?>
  </section>
</div>
<?php panel_alt();
