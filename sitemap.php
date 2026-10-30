<?php
// /sitemap.xml – yeni ilçe veya hizmet eklendiğinde kendiliğinden güncellenir.
require __DIR__ . '/inc/seo.php';
header('Content-Type: application/xml; charset=utf-8');

$tarih = fn(string $dosya) => date('Y-m-d', @filemtime(__DIR__ . '/' . $dosya) ?: time());
$hizmetTarih = max(@filemtime(__DIR__ . '/inc/hizmetler.php') ?: 0, @filemtime(__DIR__ . '/hizmet.php') ?: 0);
$bolgeTarih = @filemtime(__DIR__ . '/bolge.php') ?: time();

$urller = [[site_url('/'), $tarih('index.php'), 'daily', '1.0']];
$urller[] = [site_url('/hakkimizda'), $tarih('hakkimizda.php'), 'monthly', '0.7'];
$urller[] = [site_url('/iletisim'), $tarih('iletisim.php'), 'monthly', '0.8'];
$urller[] = [site_url('/galeri'), $tarih('galeri.php'), 'monthly', '0.5'];
$urller[] = [site_url('/kvkk-aydinlatma-metni'), $tarih('yasal.php'), 'yearly', '0.2'];
$urller[] = [site_url('/cerez-politikasi'), $tarih('yasal.php'), 'yearly', '0.2'];
$blog = blog_yazilari();
$urller[] = [site_url('/blog'), $blog ? date('Y-m-d', strtotime($blog[0]['guncelleme'] ?: $blog[0]['yayin'])) : $tarih('blog.php'), 'weekly', '0.6'];
foreach ($blog as $y) $urller[] = [site_url('/blog/' . $y['slug']), date('Y-m-d', strtotime($y['guncelleme'] ?: $y['yayin'])), 'monthly', '0.6'];
foreach (array_keys(hizmetler()) as $sl) {
    $urller[] = [site_url('/hizmet/' . $sl), date('Y-m-d', $hizmetTarih ?: time()), 'weekly', '0.9'];
}
foreach (db()->query('SELECT ad, guncelleme FROM bolgeler WHERE aktif = 1 ORDER BY sira, id') as $b) {
    $t = max($bolgeTarih, $b['guncelleme'] ? strtotime($b['guncelleme']) : 0);
    $urller[] = [site_url(ilce_url($b['ad'])), date('Y-m-d', $t), 'weekly', '0.8'];
}
$mahalleTarih = @filemtime(__DIR__ . '/mahalle.php') ?: time();
$mt = [];
foreach (db()->query('SELECT bolge_id, ad, guncelleme FROM mahalle_icerik') as $r) $mt[$r['bolge_id'] . '|' . $r['ad']] = $r['guncelleme'];
foreach (tum_mahalleler() as $x) {
    $g = $mt[$x['bolge']['id'] . '|' . $x['mahalle']] ?? null;
    $t = max($mahalleTarih, $g ? strtotime($g) : 0);
    $urller[] = [site_url(mahalle_url($x['ilce'], $x['mahalle'])), date('Y-m-d', $t), 'weekly', '0.7'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urller as [$loc, $lastmod, $freq, $pri]) {
    echo "  <url><loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc><lastmod>$lastmod</lastmod><changefreq>$freq</changefreq><priority>$pri</priority></url>\n";
}
echo '</urlset>';
