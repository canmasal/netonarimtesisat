<?php
require_once __DIR__ . '/auth.php';

function panel_ust(string $baslik, ?array $u = null): void
{
    // Panel açıldığında sunucu önbelleğindeki eski sayfaları temizle
    if (!headers_sent()) {
        header('X-LiteSpeed-Purge: *');
        header('Cache-Control: no-store');
    }
    $sayfa = basename($_SERVER['SCRIPT_NAME']);
    $menu = [];
    $yeni = 0;
    if ($u) {
        if ($u['rol'] === 'admin') {
            $yeni = (int)db()->query("SELECT COUNT(*) FROM talepler WHERE durum = 'yeni'")->fetchColumn();
            $menu = [
                ['index.php', 'Özet'],
                ['talepler.php', 'Talepler'],
                ['yeni-talep.php', '+ Yeni Talep'],
                ['personel.php', 'Personel'],
                ['site.php', 'Site Ayarları'],
                ['hizmetler.php', 'Hizmetler'],
                ['galeri.php', 'Galeri'],
                ['bolgeler.php', 'Hizmet Bölgeleri'],
                ['blog.php', 'Blog'],
                ['seo.php', 'SEO Ayarları'],
                ['sifre.php', 'Şifrem'],
            ];
        } else {
            $menu = [
                ['talepler.php', 'İşlerim'],
                ['yeni-talep.php', '+ Yeni İş'],
                ['sifre.php', 'Şifrem'],
            ];
        }
    }
    ?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($baslik) ?> · <?= e(SITE_ADI) ?> Panel</title>
<link rel="icon" href="../assets/logo/logo-simge.svg" type="image/svg+xml">
<link rel="stylesheet" href="../assets/panel.css?v=<?= @filemtime(__DIR__ . '/../assets/panel.css') ?: 1 ?>">
</head>
<body>
<?php if ($u): ?>
<header class="ust">
  <div class="ust-ic">
    <a class="marka" href="<?= $u['rol'] === 'admin' ? 'index.php' : 'talepler.php' ?>">
      <img class="marka-ikon" src="../assets/logo/logo-simge.svg" alt="" width="34" height="34">
      <span><?= e(SITE_ADI) ?><small><?= $u['rol'] === 'admin' ? 'Yönetim Paneli' : 'Personel Paneli' ?></small></span>
    </a>
    <div class="kim">
      <span><?= e($u['ad']) ?></span>
      <a class="cikis" href="cikis.php?t=<?= csrf_token() ?>">Çıkış</a>
    </div>
  </div>
  <nav class="menu" aria-label="Panel menüsü">
    <?php foreach ($menu as [$href, $etiket]): ?>
      <a href="<?= $href ?>" class="<?= $sayfa === $href ? 'secili' : '' ?>"><?= e($etiket) ?><?php if ($href === 'talepler.php' && $yeni > 0): ?> <b class="say"><?= $yeni ?></b><?php endif; ?></a>
    <?php endforeach; ?>
  </nav>
</header>
<?php endif; ?>
<main class="icerik">
<?php if ($u && ($f = flash())): ?>
  <div class="uyari uyari-<?= e($f[1]) ?>"><?= e($f[0]) ?></div>
<?php endif;
}

function panel_alt(): void
{
    echo "</main>\n</body>\n</html>";
}
