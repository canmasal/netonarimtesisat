<?php
// Sitedeki "Hızlı Servis Talebi" formunu panele kaydeder.
require __DIR__ . '/inc/helpers.php';
header('Content-Type: application/json; charset=utf-8');

function cevap(int $kod, array $veri): never
{
    http_response_code($kod);
    echo json_encode($veri, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') cevap(405, ['ok' => false]);
if (!empty($_POST['website'])) cevap(200, ['ok' => true]); // bot tuzağı

$ad = kisalt((string)($_POST['name'] ?? ''), 100);
$tel = tel_temizle((string)($_POST['phone'] ?? ''));
$hizmet = in_array($_POST['service'] ?? '', hizmet_secenekleri(), true) ? $_POST['service'] : 'Diğer';
$ilce = kisalt((string)($_POST['district'] ?? ''), 60);

if ($ad === '' || strlen($tel) < 10 || strlen($tel) > 13) cevap(422, ['ok' => false, 'hata' => 'Ad ve geçerli telefon gerekli.']);
if (($_POST['kvkk'] ?? '') !== '1') cevap(422, ['ok' => false, 'hata' => 'KVKK onayı gerekli.']);

$pdo = db();
$s = $pdo->prepare('SELECT COUNT(*) FROM talepler WHERE ip = ? AND olusturma >= ?');
$s->execute([ip(), date('Y-m-d H:i:s', time() - 3600)]);
if ((int)$s->fetchColumn() >= 5) cevap(429, ['ok' => false, 'hata' => 'Çok fazla talep. Lütfen bizi arayın.']);

$now = simdi();
$pdo->prepare("INSERT INTO talepler (ad, telefon, hizmet, ilce, kaynak, durum, ip, olusturma, guncelleme, kvkk_onay) VALUES (?,?,?,?, 'Web formu', 'yeni', ?, ?, ?, ?)")
    ->execute([$ad, $tel, $hizmet, $ilce, ip(), $now, $now, $now . ' – formdaki KVKK onay kutusu işaretlendi']);
$id = (int)$pdo->lastInsertId();
gecmis_ekle($id, null, 'Web sitesindeki formdan geldi');

cevap(200, ['ok' => true, 'id' => $id]);
