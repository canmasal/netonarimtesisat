<?php
require_once __DIR__ . '/db.php';

const DURUMLAR = [
    'yeni'       => ['Yeni', '#1d6fe8'],
    'atandi'     => ['Ustaya atandı', '#7c3aed'],
    'yolda'      => ['Usta yolda', '#0891b2'],
    'islemde'    => ['İşlemde', '#ea580c'],
    'tamamlandi' => ['Tamamlandı', '#16a34a'],
    'iptal'      => ['İptal', '#64748b'],
];
const AKTIF_DURUMLAR = ['yeni', 'atandi', 'yolda', 'islemde'];
const PERSONEL_DURUMLARI = ['atandi', 'yolda', 'islemde', 'tamamlandi'];

const HIZMETLER = [
    'Su kaçağı tespiti',
    'Tıkanıklık açma (lavabo, tuvalet, gider)',
    'Tesisat yenileme',
    'Batarya / musluk değişimi',
    'Acil arıza (patlak boru vb.)',
    'Diğer',
];
const KAYNAKLAR = ['Telefon', 'WhatsApp', 'Web formu', 'Tavsiye', 'Diğer'];
const ODEMELER = ['Nakit', 'Kredi kartı', 'Havale / EFT', 'Ödenmedi'];

function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function simdi(): string
{
    return date('Y-m-d H:i:s');
}

function kisalt(string $s, int $n): string
{
    $s = trim($s);
    preg_match('/^.{0,' . $n . '}/su', $s, $m);
    return $m[0] ?? '';
}

function durum_adi(string $d): string
{
    return DURUMLAR[$d][0] ?? $d;
}

function durum_rozet(string $d): string
{
    [$l, $c] = DURUMLAR[$d] ?? [$d, '#64748b'];
    return '<span class="rozet" style="--c:' . $c . '">' . e($l) . '</span>';
}

function tel_temizle(string $t): string
{
    $d = preg_replace('/\D+/', '', $t);
    if (strlen($d) === 12 && str_starts_with($d, '90')) $d = '0' . substr($d, 2);
    if (strlen($d) === 10 && $d[0] === '5') $d = '0' . $d;
    return $d;
}

function tel_goster(?string $t): string
{
    $d = tel_temizle((string)$t);
    if (strlen($d) === 11) return substr($d, 0, 4) . ' ' . substr($d, 4, 3) . ' ' . substr($d, 7, 2) . ' ' . substr($d, 9, 2);
    return (string)$t;
}

function wa_link(?string $tel, string $mesaj = ''): string
{
    $d = tel_temizle((string)$tel);
    if (strlen($d) === 11 && $d[0] === '0') $d = '9' . $d;
    return 'https://wa.me/' . $d . ($mesaj !== '' ? '?text=' . rawurlencode($mesaj) : '');
}

function harita_link(array $t): string
{
    $q = trim(($t['adres'] ?? '') . ' ' . ($t['ilce'] ?? '') . ' İstanbul');
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($q);
}

function tarih(?string $t): string
{
    return $t ? date('d.m.Y H:i', strtotime($t)) : '—';
}

function para($v): string
{
    return ($v === null || $v === '') ? '—' : number_format((float)$v, 0, ',', '.') . ' ₺';
}

function ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function yonlendir(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function gecmis_ekle(int $talep_id, ?int $uid, string $metin, string $tur = 'sistem'): void
{
    db()->prepare('INSERT INTO talep_gecmisi (talep_id, kullanici_id, tur, metin, olusturma) VALUES (?,?,?,?,?)')
        ->execute([$talep_id, $uid, $tur, $metin, simdi()]);
}
