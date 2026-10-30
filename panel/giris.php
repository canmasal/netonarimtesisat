<?php
require __DIR__ . '/../inc/layout.php';
csrf_kontrol();

if ((int)db()->query('SELECT COUNT(*) FROM kullanicilar')->fetchColumn() === 0) yonlendir('kurulum.php');
if (kullanici()) yonlendir('index.php');

$hata = '';
$kadi = strtolower(trim($_POST['kullanici_adi'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db();
    $pdo->prepare('DELETE FROM giris_denemeleri WHERE zaman < ?')->execute([time() - 900]);
    $s = $pdo->prepare('SELECT COUNT(*) FROM giris_denemeleri WHERE ip = ?');
    $s->execute([ip()]);

    if ((int)$s->fetchColumn() >= 5) {
        $hata = 'Çok fazla hatalı deneme. Lütfen 15 dakika sonra tekrar deneyin.';
    } else {
        $s = $pdo->prepare('SELECT * FROM kullanicilar WHERE kullanici_adi = ? AND aktif = 1');
        $s->execute([$kadi]);
        $u = $s->fetch();
        if ($u && password_verify((string)($_POST['sifre'] ?? ''), $u['sifre'])) {
            $pdo->prepare('DELETE FROM giris_denemeleri WHERE ip = ?')->execute([ip()]);
            if (password_needs_rehash($u['sifre'], PASSWORD_DEFAULT)) {
                $pdo->prepare('UPDATE kullanicilar SET sifre = ? WHERE id = ?')->execute([password_hash($_POST['sifre'], PASSWORD_DEFAULT), $u['id']]);
            }
            oturum_ac($u);
            yonlendir($u['rol'] === 'admin' ? 'index.php' : 'talepler.php');
        }
        $pdo->prepare('INSERT INTO giris_denemeleri (ip, zaman) VALUES (?, ?)')->execute([ip(), time()]);
        usleep(400000);
        $hata = 'Kullanıcı adı veya şifre hatalı.';
    }
}

panel_ust('Giriş');
?>
<div class="giris-sar">
  <form class="giris" method="post">
    <div class="marka"><img class="marka-ikon" src="../assets/logo/logo-simge.svg" alt="" width="34" height="34"><span><?= e(SITE_ADI) ?><small>Yönetim ve Personel Paneli</small></span></div>
    <h1>Giriş yapın</h1>
    <?php if ($hata): ?><div class="uyari uyari-hata"><?= e($hata) ?></div><?php endif; ?>
    <?php if ($f = flash()): ?><div class="uyari uyari-<?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
    <?= csrf_alan() ?>
    <label for="kadi">Kullanıcı adı</label>
    <input id="kadi" name="kullanici_adi" value="<?= e($kadi) ?>" required autofocus autocapitalize="none" autocomplete="username">
    <label for="sifre">Şifre</label>
    <input id="sifre" name="sifre" type="password" required autocomplete="current-password">
    <button class="dugme turuncu tam">Giriş Yap</button>
    <p class="not-kucuk">Şifrenizi unuttuysanız yöneticinizden yeni şifre isteyin.</p>
  </form>
</div>
<?php panel_alt();
