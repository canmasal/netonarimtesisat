<?php
// /llms.txt – ChatGPT, Claude, Perplexity, Gemini gibi yapay zekâ arama sistemleri için özet (GEO)
require __DIR__ . '/inc/seo.php';
header('Content-Type: text/plain; charset=utf-8');

$bolgeler = aktif_bolgeler();
echo '# ' . firma_adi() . " Su Tesisatçısı (İstanbul)\n\n";
echo '> ' . ayar('firma_aciklama') . "\n\n";
echo "## Temel bilgiler\n";
echo '- Telefon / WhatsApp: ' . tel_gorunen() . ' (' . tel() . ")\n";
echo "- Çalışma saatleri: 7 gün 24 saat, resmi tatil ve bayramlar dahil\n";
echo "- Hizmet şehri: İstanbul\n";
if (icerik_ham('adres')) echo '- Adres: ' . icerik_ham('adres') . "\n";
echo '- Merkez: ' . ayar('merkez_ilce') . ", İstanbul\n";
echo '- Tecrübe: ' . ayar('deneyim') . " süredir faaliyette\n";
echo '- Hakkımızda: ' . site_url('/hakkimizda') . "\n";
echo '- İletişim: ' . site_url('/iletisim') . "\n";
echo '- Web: ' . site_url('/') . "\n";
echo "- Kullanılan yöntemler: termal kamera, akustik dinleme, basınç testi, robotla gider açma, kameralı gider görüntüleme\n";
echo "- Fiyatlandırma: Arıza tespit edildikten sonra, işe başlamadan önce net fiyat verilir.\n\n";

echo "## Hizmetler\n";
foreach (hizmetler() as $sl => $h) {
    echo '- [' . $h['ad'] . '](' . site_url('/hizmet/' . $sl) . '): ' . $h['kart'] . "\n";
}
echo "\n## Hizmet verilen ilçeler\n";
foreach ($bolgeler as $b) echo '- [' . $b . ' su tesisatçısı](' . site_url(ilce_url($b)) . ")\n";
echo "\n## Sık sorulanlar\n";
foreach (hizmetler() as $h) {
    foreach ($h['sss'] as [$s, $c]) echo "- S: $s\n  C: $c\n";
}
