<?php
require __DIR__ . '/../inc/layout.php';
$u = giris_gerekli('admin');
csrf_kontrol();
$pdo = db();

$duzenle = null;
if (isset($_GET['id'])) {
    $s = $pdo->prepare('SELECT * FROM kullanicilar WHERE id = ?');
    $s->execute([(int)$_GET['id']]);
    $duzenle = $s->fetch() ?: null;
}
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $ad = kisalt((string)($_POST['ad'] ?? ''), 80);
    $kadi = strtolower(trim((string)($_POST['kullanici_adi'] ?? '')));
    $tel = tel_temizle((string)($_POST['telefon'] ?? ''));
    $rol = ($_POST['rol'] ?? '') === 'admin' ? 'admin' : 'personel';
    $aktif = isset($_POST['aktif']) ? 1 : 0;
    $sifre = (string)($_POST['sifre'] ?? '');

    if ($id === (int)$u['id']) { $rol = 'admin'; $aktif = 1; } // kendini kilitlemeyi önle

    $s = $pdo->prepare('SELECT COUNT(*) FROM kullanicilar WHERE kullanici_adi = ? AND id != ?');
    $s->execute([$kadi, $id]);

    if ($ad === '') $hata = 'Ad girin.';
    elseif (!preg_match('/^[a-z0-9._-]{3,30}$/', $kadi)) $hata = 'Kullanıcı adı 3-30 karakter olmalı (küçük harf, rakam, . _ -).';
    elseif ((int)$s->fetchColumn() > 0) $hata = 'Bu kullanıcı adı kullanılıyor.';
    elseif (($id === 0 || $sifre !== '') && strlen($sifre) < 8) $hata = 'Şifre en az 8 karakter olmalı.';
    else {
        if ($id === 0) {
            $pdo->prepare('INSERT INTO kullanicilar (ad, kullanici_adi, sifre, rol, telefon, aktif, olusturma) VALUES (?,?,?,?,?,?,?)')
                ->execute([$ad, $kadi, password_hash($sifre, PASSWORD_DEFAULT), $rol, $tel, $aktif, simdi()]);
            flash($ad . ' eklendi. Giriş bilgilerini kendisine iletin.');
        } else {
            $pdo->prepare('UPDATE kullanicilar SET ad=?, kullanici_adi=?, rol=?, telefon=?, aktif=? WHERE id=?')
                ->execute([$ad, $kadi, $rol, $tel, $aktif, $id]);
            if ($sifre !== '') $pdo->prepare('UPDATE kullanicilar SET sifre=? WHERE id=?')->execute([password_hash($sifre, PASSWORD_DEFAULT), $id]);
            flash($ad . ' güncellendi.' . ($sifre !== '' ? ' Yeni şifre kaydedildi.' : ''));
        }
        yonlendir('personel.php');
    }
    $duzenle = ['id' => $id, 'ad' => $ad, 'kullanici_adi' => $kadi, 'telefon' => $tel, 'rol' => $rol, 'aktif' => $aktif];
}

$liste = $pdo->query("
  SELECT k.*,
    (SELECT COUNT(*) FROM talepler t WHERE t.personel_id = k.id AND t.durum IN ('atandi','yolda','islemde')) aktif_is,
    (SELECT COUNT(*) FROM talepler t WHERE t.personel_id = k.id AND t.durum = 'tamamlandi') biten
  FROM kullanicilar k ORDER BY k.aktif DESC, k.rol, k.ad")->fetchAll();

$f = $duzenle ?? ['id' => 0, 'ad' => '', 'kullanici_adi' => '', 'telefon' => '', 'rol' => 'personel', 'aktif' => 1];
panel_ust('Personel', $u);
?>
<h1>Personel</h1>
<p class="alt-baslik">Ustalarınız için giriş hesabı açın. Personel yalnızca kendisine atanan işleri görür ve günceller.</p>

<div class="izgara iki">
  <section class="kart">
    <h2>Hesaplar</h2>
    <div class="tablo-sar"><table>
      <tr><th>Ad</th><th>Kullanıcı adı</th><th>Rol</th><th>Açık / Biten</th><th></th></tr>
      <?php foreach ($liste as $p): ?>
        <tr style="<?= $p['aktif'] ? '' : 'opacity:.5' ?>">
          <td><b><?= e($p['ad']) ?></b><br><small><?= e(tel_goster($p['telefon'])) ?></small></td>
          <td><?= e($p['kullanici_adi']) ?></td>
          <td><?= $p['rol'] === 'admin' ? 'Yönetici' : 'Personel' ?><?= $p['aktif'] ? '' : ' · Pasif' ?></td>
          <td><a href="talepler.php?personel=<?= $p['id'] ?>"><?= (int)$p['aktif_is'] ?> / <?= (int)$p['biten'] ?></a></td>
          <td><a class="dugme gri kucuk" href="personel.php?id=<?= $p['id'] ?>">Düzenle</a></td>
        </tr>
      <?php endforeach; ?>
    </table></div>
  </section>

  <section class="kart">
    <h2><?= $f['id'] ? 'Düzenle: ' . e($f['ad']) : 'Yeni personel ekle' ?></h2>
    <?php if ($hata): ?><div class="uyari uyari-hata"><?= e($hata) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_alan() ?>
      <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
      <label for="ad">Ad Soyad</label>
      <input id="ad" name="ad" value="<?= e($f['ad']) ?>" required>
      <label for="kadi">Kullanıcı adı</label>
      <input id="kadi" name="kullanici_adi" value="<?= e($f['kullanici_adi']) ?>" required autocapitalize="none" placeholder="örn. ahmet.usta">
      <label for="tel">Telefon</label>
      <input id="tel" name="telefon" value="<?= e($f['telefon']) ?>" inputmode="tel">
      <label for="rol">Rol</label>
      <select id="rol" name="rol" <?= (int)$f['id'] === (int)$u['id'] ? 'disabled' : '' ?>>
        <option value="personel" <?= $f['rol'] === 'personel' ? 'selected' : '' ?>>Personel (usta)</option>
        <option value="admin" <?= $f['rol'] === 'admin' ? 'selected' : '' ?>>Yönetici</option>
      </select>
      <label for="sifre"><?= $f['id'] ? 'Yeni şifre (değiştirmeyecekseniz boş bırakın)' : 'Şifre (en az 8 karakter)' ?></label>
      <input id="sifre" name="sifre" type="text" autocomplete="new-password" <?= $f['id'] ? '' : 'required minlength="8"' ?>>
      <?php if ((int)$f['id'] !== (int)$u['id']): ?>
        <label><input type="checkbox" name="aktif" value="1" <?= $f['aktif'] ? 'checked' : '' ?>> Hesap aktif (işaretsizse giriş yapamaz)</label>
      <?php else: ?>
        <input type="hidden" name="aktif" value="1">
      <?php endif; ?>
      <div class="dugmeler" style="margin-top:12px">
        <button class="dugme turuncu"><?= $f['id'] ? 'Kaydet' : 'Personeli Ekle' ?></button>
        <?php if ($f['id']): ?><a class="dugme gri" href="personel.php">Vazgeç</a><?php endif; ?>
      </div>
    </form>
  </section>
</div>
<?php panel_alt();
