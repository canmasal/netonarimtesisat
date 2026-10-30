<?php
require __DIR__ . '/../inc/layout.php';
csrf_kontrol();

if ((int)db()->query('SELECT COUNT(*) FROM kullanicilar')->fetchColumn() > 0) yonlendir('giris.php');

$hata = '';
$ad = trim($_POST['ad'] ?? '');
$kadi = strtolower(trim($_POST['kullanici_adi'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sifre = (string)($_POST['sifre'] ?? '');
    if (!hash_equals(KURULUM_ANAHTARI, trim((string)($_POST['anahtar'] ?? '')))) $hata = 'Kurulum anahtarı hatalı (inc/config.php içinde yazar).';
    elseif ($ad === '' || !preg_match('/^[a-z0-9._-]{3,30}$/', $kadi)) $hata = 'Ad ve geçerli bir kullanıcı adı girin (küçük harf, rakam, . _ -).';
    elseif (strlen($sifre) < 8) $hata = 'Şifre en az 8 karakter olmalı.';
    elseif ($sifre !== ($_POST['sifre2'] ?? '')) $hata = 'Şifreler aynı değil.';
    else {
        db()->prepare('INSERT INTO kullanicilar (ad, kullanici_adi, sifre, rol, olusturma) VALUES (?,?,?,?,?)')
            ->execute([kisalt($ad, 80), $kadi, password_hash($sifre, PASSWORD_DEFAULT), 'admin', simdi()]);
        $u = db()->query('SELECT * FROM kullanicilar ORDER BY id LIMIT 1')->fetch();
        oturum_ac($u);
        flash('Kurulum tamam. Şimdi personel hesaplarını ekleyebilirsiniz.');
        yonlendir('personel.php');
    }
}

panel_ust('İlk Kurulum');
?>
<div class="giris-sar">
  <form class="giris" method="post">
    <div class="marka"><img class="marka-ikon" src="../assets/logo/logo-simge.svg" alt="" width="34" height="34"><span><?= e(SITE_ADI) ?><small>İlk kurulum</small></span></div>
    <h1>Yönetici hesabını oluşturun</h1>
    <?php if ($hata): ?><div class="uyari uyari-hata"><?= e($hata) ?></div><?php endif; ?>
    <?= csrf_alan() ?>
    <label for="anahtar">Kurulum anahtarı</label>
    <input id="anahtar" name="anahtar" required autocomplete="off">
    <label for="ad">Adınız Soyadınız</label>
    <input id="ad" name="ad" value="<?= e($ad) ?>" required>
    <label for="kadi">Kullanıcı adı</label>
    <input id="kadi" name="kullanici_adi" value="<?= e($kadi) ?>" required autocapitalize="none">
    <label for="sifre">Şifre (en az 8 karakter)</label>
    <input id="sifre" name="sifre" type="password" required minlength="8" autocomplete="new-password">
    <label for="sifre2">Şifre (tekrar)</label>
    <input id="sifre2" name="sifre2" type="password" required minlength="8" autocomplete="new-password">
    <button class="dugme turuncu tam">Hesabı Oluştur</button>
    <p class="not-kucuk">Bu sayfa yalnızca bir kez çalışır; ilk hesap oluşunca kapanır.</p>
  </form>
</div>
<?php panel_alt();
