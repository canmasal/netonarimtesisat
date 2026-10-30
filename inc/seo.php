<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/hizmetler.php';
require_once __DIR__ . '/icerik.php';

const AYAR_VARSAYILAN = [
    'site_url'         => 'https://netonarimtesisat.com.tr',
    'firma_aciklama'   => 'Net Onarım Tesisat, Bahçelievler merkezli olarak 20 yılı aşkın süredir İstanbul\'da kırmadan su kaçağı tespiti, tıkanıklık açma ve genel sıhhi tesisat hizmetleri sunan bir firmadır. Termal kamera ve akustik dinleme cihazlarıyla noktasal tespit yapar, 7/24 hizmet verir.',
    'deneyim'          => '20 yılı aşkın',
    'merkez_ilce'      => 'Bahçelievler',
    'adres'            => 'Fevzi Çakmak Mah. Gülibrişim Sk. No:2, Bahçelievler / İstanbul',
    'enlem'            => '',
    'boylam'           => '',
    'google_dogrulama' => '',
    'bing_dogrulama'   => '',
    'yandex_dogrulama' => '',
    'ga4_id'           => '',
    'ads_id'           => '',
    'ads_tel_etiket'   => '',
    'ads_wa_etiket'    => '',
    'ads_form_etiket'  => '',
    'gbp_url'          => '',
    'instagram'        => '',
    'facebook'         => '',
];

function ayar(string $k): string
{
    static $c = null;
    if ($c === null) {
        $c = AYAR_VARSAYILAN;
        try {
            foreach (db()->query('SELECT anahtar, deger FROM ayarlar') as $r) $c[$r['anahtar']] = (string)$r['deger'];
        } catch (Throwable $e) {
        }
    }
    return $c[$k] ?? '';
}

function site_url(string $yol = '/'): string
{
    return rtrim(ayar('site_url') ?: AYAR_VARSAYILAN['site_url'], '/') . $yol;
}

function slug(string $s): string
{
    $s = strtr($s, ['ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i', 'İ' => 'i', 'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u', 'â' => 'a', 'î' => 'i', 'û' => 'u']);
    $s = strtolower($s);
    return trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
}

function ilce_url(string $ad): string
{
    return '/su-tesisatcisi/' . slug($ad);
}

// ---- Türkçe ekler: Bakırköy'de, Fatih'te, Ümraniye'ye, Esenyurt'a ----
function _son_unlu(string $s): string
{
    preg_match_all('/[aeıioöuüAEIİOÖUÜ]/u', $s, $m);
    $u = end($m[0]) ?: 'e';
    return in_array($u, ['a', 'ı', 'o', 'u', 'A', 'I', 'O', 'U'], true) ? 'a' : 'e';
}
function _unluyle_biter(string $s): bool
{
    return (bool)preg_match('/[aeıioöuü]$/u', $s);
}
function _ozel_ek(string $ad): ?string
{
    // Tamlama ile biten adlar "n" kaynaştırma alır (Beylikdüzü'nde)
    return preg_match('/(düzü|köyü|tepesi|deresi|yalısı)$/u', $ad) ? 'n' : null;
}
function ek_de(string $ad): string
{
    $u = _son_unlu($ad);
    if (_ozel_ek($ad)) return $ad . "'nd" . $u;
    $sert = (bool)preg_match('/[fstkçşhp]$/u', $ad);
    return $ad . "'" . ($sert ? 't' : 'd') . $u;
}
function ek_e(string $ad): string
{
    $u = _son_unlu($ad);
    if (_ozel_ek($ad)) return $ad . "'n" . $u;
    return $ad . "'" . (_unluyle_biter($ad) ? 'y' : '') . $u;
}

function bolge_getir(string $slug): ?array
{
    foreach (db()->query('SELECT * FROM bolgeler WHERE aktif = 1 ORDER BY sira, id') as $b) {
        if (slug($b['ad']) === $slug) return $b;
    }
    return null;
}

// ---- Mahalle sayfaları: /su-tesisatcisi/{ilce}/{mahalle} ----
function mahalle_url(string $ilce, string $mahalle): string
{
    return ilce_url($ilce) . '/' . slug($mahalle);
}

function bolge_mahalleleri(array $b): array
{
    $l = array_values(array_unique(array_filter(array_map('trim', explode(',', (string)($b['mahalleler'] ?? ''))))));
    return array_values(array_filter($l, fn($m) => slug($m) !== ''));
}

/** Tüm aktif ilçe + mahalle çiftleri (site haritası ve iç linkler için). */
function tum_mahalleler(): array
{
    $out = [];
    foreach (db()->query('SELECT * FROM bolgeler WHERE aktif = 1 ORDER BY sira, id') as $b) {
        foreach (bolge_mahalleleri($b) as $m) $out[] = ['ilce' => $b['ad'], 'mahalle' => $m, 'bolge' => $b];
    }
    return $out;
}

/** Mahalleye özel metin: önce panelde yazılan, yoksa hazır metin, yoksa ''. */
function mahalle_metni(array $b, string $mahalle): array
{
    $s = db()->prepare('SELECT aciklama, guncelleme FROM mahalle_icerik WHERE bolge_id = ? AND ad = ?');
    $s->execute([(int)$b['id'], $mahalle]);
    $r = $s->fetch();
    if ($r && trim((string)$r['aciklama']) !== '') return ['metin' => trim($r['aciklama']), 'guncelleme' => $r['guncelleme'], 'kaynak' => 'panel'];
    require_once __DIR__ . '/mahalleler.php';
    $hazir = MAHALLE_METINLERI[$b['ad']][$mahalle] ?? '';
    return ['metin' => $hazir, 'guncelleme' => null, 'kaynak' => $hazir !== '' ? 'hazir' : ''];
}

// ---- Blog ----
function blog_yazilari(?int $limit = null): array
{
    return db()->query('SELECT * FROM blog WHERE aktif = 1 ORDER BY yayin DESC, id DESC' . ($limit ? ' LIMIT ' . (int)$limit : ''))->fetchAll();
}

function blog_getir(string $slug): ?array
{
    $s = db()->prepare('SELECT * FROM blog WHERE aktif = 1 AND slug = ?');
    $s->execute([$slug]);
    return $s->fetch() ?: null;
}

function tr_tarih(?string $t): string
{
    if (!$t) return '';
    $aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $ts = strtotime($t);
    return date('j', $ts) . ' ' . $aylar[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

function okuma_suresi(string $metin): int
{
    return max(1, (int)round(count(preg_split('/\s+/u', trim($metin))) / 200));
}

/** Basit yazı biçimi: boş satır = paragraf, "## " = ara başlık, "- " = madde. */
function blog_html(string $metin): string
{
    $out = '';
    foreach (preg_split('/\R{2,}/', trim(str_replace("\r", '', $metin))) as $blok) {
        $satirlar = explode("\n", trim($blok));
        $liste = [];
        $metinler = [];
        $bosalt = function () use (&$liste, &$metinler, &$out) {
            if ($metinler) { $out .= '<p>' . implode('<br>', array_map('e', $metinler)) . "</p>\n"; $metinler = []; }
            if ($liste) { $out .= '<ul>' . implode('', array_map(fn($l) => '<li>' . e($l) . '</li>', $liste)) . "</ul>\n"; $liste = []; }
        };
        foreach ($satirlar as $sat) {
            $sat = rtrim($sat);
            if (str_starts_with($sat, '## ')) { $bosalt(); $out .= '<h2>' . e(substr($sat, 3)) . "</h2>\n"; }
            elseif (str_starts_with($sat, '- ')) { if ($metinler) $bosalt(); $liste[] = substr($sat, 2); }
            elseif ($sat !== '') { if ($liste) $bosalt(); $metinler[] = $sat; }
        }
        $bosalt();
    }
    return $out;
}

/** Aynı cümleler her sayfada birebir tekrarlanmasın diye mahalle adına göre sabit seçim. */
function sec(string $anahtar, array $secenekler): string
{
    return $secenekler[crc32($anahtar) % count($secenekler)];
}

// ---- Yapılandırılmış veri (schema.org) ----
function schema_isletme(array $ek = []): array
{
    $sameAs = array_values(array_filter([ayar('gbp_url'), icerik_ham('instagram'), icerik_ham('facebook')]));
    $s = [
        '@type'       => 'Plumber',
        '@id'         => site_url('/#isletme'),
        'name'        => 'Net Onarım Tesisat',
        'alternateName' => 'Net Onarım',
        'url'         => site_url('/'),
        'telephone'   => tel(),
        'description' => ayar('firma_aciklama'),
        'image'       => site_url('/assets/og.jpg'),
        'logo'        => site_url('/assets/logo/logo-simge-512.png'),
        'priceRange'  => '₺₺',
        'slogan'      => 'Kırmadan dökmeden, cihazla noktasal tespit',
        'knowsAbout'  => ['Su kaçağı tespiti', 'Termal kamera ile kaçak tespiti', 'Akustik dinleme', 'Robotla gider açma', 'Sıhhi tesisat', 'Tesisat yenileme'],
        'hasMap'      => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(firma_adi() . ' Tesisat ' . icerik_ham('adres')),
        'address'     => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => icerik_ham('adres') ? trim(preg_replace('/,?\s*' . preg_quote(ayar('merkez_ilce'), '/') . '\s*\/\s*İstanbul\s*$/u', '', icerik_ham('adres'))) : null,
            'addressLocality' => ayar('merkez_ilce') ?: 'İstanbul',
            'addressRegion' => 'İstanbul',
            'addressCountry' => 'TR',
        ]),
        'areaServed' => array_map(fn($b) => ['@type' => 'AdministrativeArea', 'name' => $b . ', İstanbul'], aktif_bolgeler()),
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => '00:00', 'closes' => '23:59',
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Tesisat Hizmetleri',
            'itemListElement' => array_map(fn($h, $sl) => [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => $h['ad'], 'url' => site_url('/hizmet/' . $sl)],
            ], hizmetler(), array_keys(hizmetler())),
        ],
    ];
    if (ayar('enlem') !== '' && ayar('boylam') !== '') {
        $s['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float)ayar('enlem'), 'longitude' => (float)ayar('boylam')];
    }
    if ($sameAs) $s['sameAs'] = $sameAs;
    if (icerik_ham('eposta')) $s['email'] = icerik_ham('eposta');
    return array_merge($s, $ek);
}

function schema_sss(array $sss): array
{
    return [
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($q) => [
            '@type' => 'Question', 'name' => $q[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q[1]],
        ], $sss),
    ];
}

function schema_breadcrumb(array $adimlar): array
{
    $i = 0;
    return [
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(function ($a) use (&$i) {
            return ['@type' => 'ListItem', 'position' => ++$i, 'name' => $a[0], 'item' => site_url($a[1])];
        }, $adimlar),
    ];
}

function schema_yaz(array $grafik): string
{
    return '<script type="application/ld+json">' .
        json_encode(['@context' => 'https://schema.org', '@graph' => $grafik], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) .
        "</script>\n";
}

function harita_embed(): string
{
    $kod = trim(ayar('harita'));
    if ($kod !== '') {
        if (preg_match('/src="([^"]+)"/', $kod, $m)) $kod = html_entity_decode($m[1]);
        if (preg_match('#^https://(www\.)?google\.[a-z.]+/maps/#', $kod)) return $kod;
    }
    $q = firma_adi() . ' Tesisat, ' . (icerik_ham('adres') ?: 'Bahçelievler İstanbul');
    return 'https://www.google.com/maps?q=' . rawurlencode($q) . '&output=embed';
}
function yol_tarifi(): string
{
    return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode(icerik_ham('adres') ?: 'Bahçelievler İstanbul');
}

/** Veritabanındaki bir hizmet satırını sayfaların kullandığı diziye çevirir. */
function hizmet_dizi(array $r): array
{
    $j = fn($v) => is_array($x = json_decode((string)$v, true)) ? $x : [];
    return [
        'id' => (int)$r['id'], 'slug' => $r['slug'], 'ad' => (string)$r['ad'], 'kisa_ad' => (string)($r['kisa_ad'] ?: mb_strtolower($r['ad'])),
        'ikon' => $r['ikon'] ?: 'i-wrench', 'kart' => (string)$r['kart'], 'baslik' => (string)$r['baslik'], 'meta' => (string)$r['meta'],
        'giris' => (string)$r['giris'], 'bolumler' => $j($r['bolumler']), 'maddeler' => $j($r['maddeler']), 'sss' => $j($r['sss']),
        'sira' => (int)$r['sira'], 'aktif' => (int)$r['aktif'],
    ];
}

/** Tüm hizmetler (pasifler dahil), panel için. Telefon {telefon} olarak kalır. */
function hizmet_tum(): array
{
    $out = [];
    foreach (db()->query('SELECT * FROM hizmetler ORDER BY sira, id') as $r) {
        $h = hizmet_dizi($r);
        array_walk_recursive($h, function (&$v) { if (is_string($v)) $v = str_replace('0539 576 17 72', '{telefon}', $v); });
        $out[$r['slug']] = $h;
    }
    return $out;
}

/** Tek hizmetin panelde düzenlenecek hâli (pasif olsa da). */
function hizmet_ham(string $sl): ?array
{
    return hizmet_tum()[$sl] ?? null;
}

/** Sitede gösterilen (aktif) hizmetler, sıralı. */
function hizmetler(): array
{
    static $c = null;
    if ($c !== null) return $c;
    $c = [];
    try {
        foreach (db()->query('SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY sira, id') as $r) {
            $h = hizmet_dizi($r);
            array_walk_recursive($h, function (&$v) { if (is_string($v)) $v = yer_tut(str_replace('0539 576 17 72', '{telefon}', $v)); });
            $c[$r['slug']] = $h;
        }
    } catch (Throwable $e) {
        foreach (SITE_HIZMETLERI as $sl => $h) if ($h['aktif'] ?? true) $c[$sl] = $h;
    }
    return $c;
}

function deneyim_kisa(): string
{
    $d = ayar('deneyim');
    return preg_match('/(\d+)/', $d, $m) ? $m[1] . '+ yıl' : $d;
}

// ---- Galeri ----
function galeri_resimleri(?int $limit = null): array
{
    return db()->query('SELECT * FROM galeri WHERE aktif = 1 ORDER BY sira, id DESC' . ($limit ? ' LIMIT ' . (int)$limit : ''))->fetchAll();
}

/**
 * Yüklenen resmi doğrular, küçültür (GD varsa) ve uploads/galeri/ içine kaydeder.
 * Dönen: ['dosya' => ..., 'kucuk' => ..., 'en' => .., 'boy' => ..] ya da hata metni (string).
 */
function galeri_kaydet(string $tmp, string $orijinalAd): array|string
{
    $bilgi = @getimagesize($tmp);
    $izinli = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$bilgi || !isset($izinli[$bilgi[2]])) return $orijinalAd . ': yalnızca JPG, PNG veya WEBP yükleyebilirsiniz.';
    $klasor = dirname(__DIR__) . '/uploads/galeri';
    if (!is_dir($klasor)) @mkdir($klasor, 0755, true);
    $ad = date('Ymd') . '-' . bin2hex(random_bytes(5));
    [$en, $boy] = $bilgi;

    $gd = function_exists('imagecreatetruecolor');
    if (!$gd) {
        $uz = $izinli[$bilgi[2]];
        if (!move_uploaded_file($tmp, "$klasor/$ad.$uz") && !rename($tmp, "$klasor/$ad.$uz")) return $orijinalAd . ': dosya kaydedilemedi.';
        return ['dosya' => "/uploads/galeri/$ad.$uz", 'kucuk' => "/uploads/galeri/$ad.$uz", 'en' => $en, 'boy' => $boy];
    }

    $kaynak = match ($bilgi[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($tmp),
        IMAGETYPE_PNG => @imagecreatefrompng($tmp),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : false,
    };
    if (!$kaynak) return $orijinalAd . ': resim okunamadı.';

    // Telefondan çekilen fotoğrafların yönünü düzelt
    if ($bilgi[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $o = (int)(@exif_read_data($tmp)['Orientation'] ?? 1);
        $aci = [3 => 180, 6 => -90, 8 => 90][$o] ?? 0;
        if ($aci) { $kaynak = imagerotate($kaynak, $aci, 0); $en = imagesx($kaynak); $boy = imagesy($kaynak); }
    }

    $webp = function_exists('imagewebp');
    $uz = $webp ? 'webp' : 'jpg';
    $yaz = function ($img, string $yol, int $kalite) use ($webp) {
        return $webp ? imagewebp($img, $yol, $kalite) : imagejpeg($img, $yol, $kalite);
    };
    $boyutla = function (int $max) use ($kaynak, $en, $boy) {
        $oran = min(1, $max / max($en, $boy));
        $yE = max(1, (int)round($en * $oran)); $yB = max(1, (int)round($boy * $oran));
        $hedef = imagecreatetruecolor($yE, $yB);
        imagefill($hedef, 0, 0, imagecolorallocate($hedef, 255, 255, 255));
        imagecopyresampled($hedef, $kaynak, 0, 0, 0, 0, $yE, $yB, $en, $boy);
        return [$hedef, $yE, $yB];
    };
    [$buyuk, $bE, $bB] = $boyutla(1600);
    [$kucuk] = $boyutla(640);
    $ok = $yaz($buyuk, "$klasor/$ad.$uz", 82) && $yaz($kucuk, "$klasor/k-$ad.$uz", 78);
    imagedestroy($buyuk); imagedestroy($kucuk); imagedestroy($kaynak);
    if (!$ok) return $orijinalAd . ': resim kaydedilemedi (klasör yazma izni?).';
    return ['dosya' => "/uploads/galeri/$ad.$uz", 'kucuk' => "/uploads/galeri/k-$ad.$uz", 'en' => $bE, 'boy' => $bB];
}
