<?php
require __DIR__ . '/../inc/layout.php';
$u = giris_gerekli();
csrf_kontrol();
$admin = $u['rol'] === 'admin';
$ekip = $admin ? db()->query("SELECT id, ad, rol FROM kullanicilar WHERE aktif = 1 ORDER BY rol DESC, ad")->fetchAll() : [];
$ekipAd = array_column($ekip, 'ad', 'id');
$hata = '';
$v = $_POST + ['ad' => '', 'telefon' => '', 'hizmet' => hizmet_secenekleri()[0], 'ilce' => '', 'adres' => '', 'aciklama' => '', 'randevu' => '', 'kaynak' => 'Telefon', 'personel_id' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tel = tel_temizle((string)$v['telefon']);
    if (strlen($tel) < 10) $hata = 'Geçerli bir telefon numarası girin.';
    else {
        $pid = $admin ? (int)$v['personel_id'] : (int)$u['id'];
        if ($admin && $pid && !isset($ekipAd[$pid])) $pid = 0;
        $randevu = trim((string)$v['randevu']);
        $randevu = $randevu !== '' && strtotime($randevu) ? date('Y-m-d H:i:s', strtotime($randevu)) : null;
        $now = simdi();
        db()->prepare('INSERT INTO talepler (ad, telefon, hizmet, ilce, adres, aciklama, kaynak, durum, personel_id, randevu, olusturma, guncelleme) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([
                kisalt((string)$v['ad'], 100), $tel,
                in_array($v['hizmet'], hizmet_secenekleri(), true) ? $v['hizmet'] : 'Diğer',
                kisalt((string)$v['ilce'], 60),
                kisalt((string)$v['adres'], 400),
                kisalt((string)$v['aciklama'], 2000),
                in_array($v['kaynak'], KAYNAKLAR, true) ? $v['kaynak'] : 'Telefon',
                $pid ? 'atandi' : 'yeni', $pid ?: null, $randevu, $now, $now,
            ]);
        $id = (int)db()->lastInsertId();
        gecmis_ekle($id, (int)$u['id'], 'Talep panelden oluşturuldu');
        if ($pid) gecmis_ekle($id, (int)$u['id'], 'Atandı: ' . ($admin ? $ekipAd[$pid] : $u['ad']));
        flash('Talep #' . $id . ' oluşturuldu.');
        yonlendir('talep.php?id=' . $id);
    }
}

panel_ust($admin ? 'Yeni Talep' : 'Yeni İş', $u);
?>
<h1><?= $admin ? 'Yeni talep' : 'Yeni iş' ?></h1>
<p class="alt-baslik">Telefonla veya WhatsApp'tan gelen işleri buradan kaydedin.<?= $admin ? '' : ' Kayıt otomatik olarak size atanır.' ?></p>
<?php if ($hata): ?><div class="uyari uyari-hata"><?= e($hata) ?></div><?php endif; ?>

<form class="kart" method="post" style="max-width:760px">
  <?= csrf_alan() ?>
  <div class="satir">
    <div><label for="ad">Müşteri adı</label><input id="ad" name="ad" value="<?= e($v['ad']) ?>" autofocus></div>
    <div><label for="telefon">Telefon *</label><input id="telefon" name="telefon" value="<?= e($v['telefon']) ?>" inputmode="tel" required placeholder="05xx xxx xx xx"></div>
  </div>
  <div class="satir">
    <div>
      <label for="hizmet">Hizmet</label>
      <select id="hizmet" name="hizmet"><?php foreach (hizmet_secenekleri() as $h): ?><option <?= $v['hizmet'] === $h ? 'selected' : '' ?>><?= e($h) ?></option><?php endforeach; ?></select>
    </div>
    <div>
      <label for="ilce">İlçe</label>
      <input id="ilce" name="ilce" list="ilce-liste" value="<?= e($v['ilce']) ?>">
      <datalist id="ilce-liste"><?php foreach (aktif_bolgeler() as $b): ?><option value="<?= e($b) ?>"><?php endforeach; ?></datalist>
    </div>
  </div>
  <label for="adres">Açık adres</label>
  <textarea id="adres" name="adres"><?= e($v['adres']) ?></textarea>
  <label for="aciklama">Sorun / açıklama</label>
  <textarea id="aciklama" name="aciklama"><?= e($v['aciklama']) ?></textarea>
  <div class="satir">
    <div><label for="randevu">Randevu (isteğe bağlı)</label><input id="randevu" name="randevu" type="datetime-local" value="<?= e($v['randevu']) ?>"></div>
    <div>
      <label for="kaynak">Kaynak</label>
      <select id="kaynak" name="kaynak"><?php foreach (KAYNAKLAR as $k): ?><option <?= $v['kaynak'] === $k ? 'selected' : '' ?>><?= e($k) ?></option><?php endforeach; ?></select>
    </div>
  </div>
  <?php if ($admin): ?>
    <label for="personel_id">Ustaya ata</label>
    <select id="personel_id" name="personel_id">
      <option value="0">— Sonra atanacak —</option>
      <?php foreach ($ekip as $p): ?><option value="<?= $p['id'] ?>" <?= (int)$v['personel_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['ad']) ?></option><?php endforeach; ?>
    </select>
  <?php endif; ?>
  <button class="dugme turuncu">Talebi Kaydet</button>
</form>
<?php panel_alt();
