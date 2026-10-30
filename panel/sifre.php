<?php
require __DIR__ . '/../inc/layout.php';
$u = giris_gerekli();
csrf_kontrol();
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yeni = (string)($_POST['yeni'] ?? '');
    if (!password_verify((string)($_POST['mevcut'] ?? ''), $u['sifre'])) $hata = 'Mevcut şifre hatalı.';
    elseif (strlen($yeni) < 8) $hata = 'Yeni şifre en az 8 karakter olmalı.';
    elseif ($yeni !== ($_POST['yeni2'] ?? '')) $hata = 'Yeni şifreler aynı değil.';
    else {
        db()->prepare('UPDATE kullanicilar SET sifre = ? WHERE id = ?')->execute([password_hash($yeni, PASSWORD_DEFAULT), $u['id']]);
        session_regenerate_id(true);
        flash('Şifreniz değiştirildi.');
        yonlendir('sifre.php');
    }
}

panel_ust('Şifrem', $u);
?>
<h1>Şifre değiştir</h1>
<p class="alt-baslik"><?= e($u['ad']) ?> · <?= e($u['kullanici_adi']) ?></p>
<?php if ($hata): ?><div class="uyari uyari-hata"><?= e($hata) ?></div><?php endif; ?>
<form class="kart" method="post" style="max-width:420px">
  <?= csrf_alan() ?>
  <label for="mevcut">Mevcut şifre</label>
  <input id="mevcut" name="mevcut" type="password" required autocomplete="current-password">
  <label for="yeni">Yeni şifre</label>
  <input id="yeni" name="yeni" type="password" required minlength="8" autocomplete="new-password">
  <label for="yeni2">Yeni şifre (tekrar)</label>
  <input id="yeni2" name="yeni2" type="password" required minlength="8" autocomplete="new-password">
  <button class="dugme turuncu">Şifreyi Değiştir</button>
</form>
<?php panel_alt();
