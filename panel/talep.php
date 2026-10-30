<?php
require __DIR__ . '/../inc/layout.php';
$u = giris_gerekli();
csrf_kontrol();
$admin = $u['rol'] === 'admin';
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
function talep_getir(int $id): ?array
{
    $s = db()->prepare('SELECT t.*, k.ad personel_ad, k.telefon personel_tel FROM talepler t LEFT JOIN kullanicilar k ON k.id = t.personel_id WHERE t.id = ?');
    $s->execute([$id]);
    return $s->fetch() ?: null;
}
$t = talep_getir($id);
if (!$t || (!$admin && (int)$t['personel_id'] !== (int)$u['id'])) {
    http_response_code(404);
    panel_ust('Bulunamadı', $u);
    echo '<div class="bos">Bu kayıt bulunamadı veya size atanmamış. <a href="talepler.php">Listeye dön</a></div>';
    panel_alt();
    exit;
}

$ekip = $pdo->query("SELECT id, ad, rol FROM kullanicilar WHERE aktif = 1 ORDER BY rol DESC, ad")->fetchAll();
$ekipAd = array_column($ekip, 'ad', 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $islem = $_POST['islem'] ?? '';
    $geri = 'talep.php?id=' . $id;

    // --- Durum / ücret / not (yönetici + atanan personel) ---
    if ($islem === 'durum') {
        $yeni = (string)($_POST['durum'] ?? $t['durum']);
        $izinli = $admin ? array_keys(DURUMLAR) : PERSONEL_DURUMLARI;
        if (!in_array($yeni, $izinli, true)) { flash('Geçersiz durum.', 'hata'); yonlendir($geri); }

        $ucretHam = trim(str_replace(['.', ' ', '₺'], '', (string)($_POST['ucret'] ?? '')));
        $ucretHam = str_replace(',', '.', $ucretHam);
        $ucret = $ucretHam === '' ? null : (is_numeric($ucretHam) ? round((float)$ucretHam, 2) : false);
        if ($ucret === false || ($ucret !== null && $ucret < 0)) { flash('Ücret sayı olmalı.', 'hata'); yonlendir($geri); }
        $odeme = in_array($_POST['odeme'] ?? '', ODEMELER, true) ? $_POST['odeme'] : null;
        $not = kisalt((string)($_POST['not'] ?? ''), 2000);

        if ($yeni === 'tamamlandi' && $ucret === null) { flash('İşi tamamlamak için ücreti girin (ücretsizse 0 yazın).', 'hata'); yonlendir($geri); }
        if ($yeni !== 'yeni' && $yeni !== 'iptal' && !$t['personel_id']) { flash('Önce talebi bir ustaya atayın.', 'hata'); yonlendir($geri); }

        $tamamlanma = $yeni === 'tamamlandi' ? ($t['durum'] === 'tamamlandi' ? $t['tamamlanma'] : simdi()) : null;
        $pdo->prepare('UPDATE talepler SET durum = ?, ucret = ?, odeme = ?, tamamlanma = ?, guncelleme = ? WHERE id = ?')
            ->execute([$yeni, $ucret, $odeme, $tamamlanma, simdi(), $id]);

        if ($yeni !== $t['durum']) gecmis_ekle($id, (int)$u['id'], 'Durum: ' . durum_adi($t['durum']) . ' → ' . durum_adi($yeni));
        $eskiUcret = $t['ucret'] === null ? null : (float)$t['ucret'];
        if ($ucret !== null && $ucret !== $eskiUcret) gecmis_ekle($id, (int)$u['id'], 'Ücret: ' . para($ucret) . ($odeme ? ' (' . $odeme . ')' : ''));
        if ($not !== '') gecmis_ekle($id, (int)$u['id'], $not, 'not');
        flash('Kaydedildi.');
        yonlendir($geri);
    }

    // --- Talep bilgileri ve atama (yalnızca yönetici) ---
    if ($islem === 'guncelle' && $admin) {
        $ad = kisalt((string)($_POST['ad'] ?? ''), 100);
        $tel = tel_temizle((string)($_POST['telefon'] ?? ''));
        if (strlen($tel) < 10) { flash('Geçerli bir telefon girin.', 'hata'); yonlendir($geri); }
        $pid = (int)($_POST['personel_id'] ?? 0);
        if ($pid && !isset($ekipAd[$pid])) $pid = 0;
        $randevu = trim((string)($_POST['randevu'] ?? ''));
        $randevu = $randevu !== '' && strtotime($randevu) ? date('Y-m-d H:i:s', strtotime($randevu)) : null;

        $durum = $t['durum'];
        if ($pid && $durum === 'yeni') $durum = 'atandi';
        if (!$pid && in_array($durum, ['atandi', 'yolda', 'islemde'], true)) $durum = 'yeni';

        $pdo->prepare('UPDATE talepler SET ad=?, telefon=?, hizmet=?, ilce=?, adres=?, aciklama=?, kaynak=?, randevu=?, personel_id=?, durum=?, guncelleme=? WHERE id=?')
            ->execute([
                $ad, $tel,
                kisalt((string)($_POST['hizmet'] ?? ''), 100),
                kisalt((string)($_POST['ilce'] ?? ''), 60),
                kisalt((string)($_POST['adres'] ?? ''), 400),
                kisalt((string)($_POST['aciklama'] ?? ''), 2000),
                in_array($_POST['kaynak'] ?? '', KAYNAKLAR, true) ? $_POST['kaynak'] : $t['kaynak'],
                $randevu, $pid ?: null, $durum, simdi(), $id,
            ]);

        if ((int)$t['personel_id'] !== $pid) {
            gecmis_ekle($id, (int)$u['id'], $pid ? 'Atandı: ' . $ekipAd[$pid] : 'Usta ataması kaldırıldı');
        }
        if ($randevu !== $t['randevu'] && $randevu) gecmis_ekle($id, (int)$u['id'], 'Randevu: ' . tarih($randevu));
        if ($durum !== $t['durum']) gecmis_ekle($id, (int)$u['id'], 'Durum: ' . durum_adi($t['durum']) . ' → ' . durum_adi($durum));
        flash('Talep güncellendi.');
        yonlendir($geri);
    }

    if ($islem === 'sil' && $admin) {
        $pdo->prepare('DELETE FROM talepler WHERE id = ?')->execute([$id]);
        flash('Talep #' . $id . ' silindi.');
        yonlendir('talepler.php');
    }

    yonlendir($geri);
}

$s = $pdo->prepare('SELECT g.*, k.ad kullanici FROM talep_gecmisi g LEFT JOIN kullanicilar k ON k.id = g.kullanici_id WHERE g.talep_id = ? ORDER BY g.id DESC');
$s->execute([$id]);
$gecmis = $s->fetchAll();
$bolgeler = aktif_bolgeler();

$waMesaj = 'Merhaba ' . ($t['ad'] ?: '') . ', ' . SITE_ADI . ' su tesisatı servisinden yazıyoruz. ' . ($t['hizmet'] ? $t['hizmet'] . ' talebiniz için ' : '') . 'ustamız sizinle iletişime geçiyor.';
$durumSecenek = $admin ? array_keys(DURUMLAR) : PERSONEL_DURUMLARI;

panel_ust('Talep #' . $id, $u);
?>
<p><a href="talepler.php">← <?= $admin ? 'Talepler' : 'İşlerim' ?></a></p>
<h1 style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">#<?= $id ?> · <?= e($t['ad'] ?: 'İsimsiz') ?> <?= durum_rozet($t['durum']) ?></h1>
<p class="alt-baslik"><?= e($t['hizmet']) ?> · <?= tarih($t['olusturma']) ?> · <?= e($t['kaynak']) ?></p>

<div class="hizli">
  <a class="dugme turuncu" href="tel:<?= e(tel_temizle((string)$t['telefon'])) ?>">📞 Ara</a>
  <a class="dugme yesil" href="<?= e(wa_link($t['telefon'], $waMesaj)) ?>" target="_blank" rel="noopener">WhatsApp</a>
  <a class="dugme gri" href="<?= e(harita_link($t)) ?>" target="_blank" rel="noopener">📍 Harita</a>
</div>

<div class="izgara iki">
  <div>
    <section class="kart">
      <h2>Talep bilgileri</h2>
      <dl class="bilgi">
        <dt>Müşteri</dt><dd><?= e($t['ad'] ?: '—') ?></dd>
        <dt>Telefon</dt><dd><a href="tel:<?= e(tel_temizle((string)$t['telefon'])) ?>"><?= e(tel_goster($t['telefon'])) ?></a></dd>
        <dt>Hizmet</dt><dd><?= e($t['hizmet'] ?: '—') ?></dd>
        <dt>İlçe</dt><dd><?= e($t['ilce'] ?: '—') ?></dd>
        <dt>Adres</dt><dd><?= nl2br(e($t['adres'] ?: '—')) ?></dd>
        <dt>Açıklama</dt><dd><?= nl2br(e($t['aciklama'] ?: '—')) ?></dd>
        <?php if (!empty($t['kvkk_onay'])): ?><dt>KVKK onayı</dt><dd><?= e($t['kvkk_onay']) ?></dd><?php endif; ?>
        <dt>Randevu</dt><dd><?= tarih($t['randevu']) ?></dd>
        <dt>Usta</dt><dd><?= e($t['personel_ad'] ?: 'Atanmadı') ?></dd>
        <dt>Ücret</dt><dd><?= para($t['ucret']) ?><?= $t['odeme'] ? ' · ' . e($t['odeme']) : '' ?></dd>
        <?php if ($t['tamamlanma']): ?><dt>Tamamlanma</dt><dd><?= tarih($t['tamamlanma']) ?></dd><?php endif; ?>
      </dl>
    </section>

    <section class="kart">
      <h2>İşlem geçmişi</h2>
      <?php if (!$gecmis): ?><p class="alt-baslik">Henüz kayıt yok.</p><?php endif; ?>
      <ul class="gecmis">
        <?php foreach ($gecmis as $g): ?>
          <li class="<?= $g['tur'] === 'not' ? 'not' : '' ?>">
            <?= nl2br(e($g['metin'])) ?>
            <small><?= e($g['kullanici'] ?: 'Sistem') ?> · <?= tarih($g['olusturma']) ?></small>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>

  <div>
    <section class="kart">
      <h2>İş durumunu güncelle</h2>
      <form method="post">
        <?= csrf_alan() ?>
        <input type="hidden" name="islem" value="durum">
        <div class="durum-sec">
          <?php foreach ($durumSecenek as $d): ?>
            <label><input type="radio" name="durum" value="<?= $d ?>" <?= $t['durum'] === $d ? 'checked' : '' ?>> <?= e(durum_adi($d)) ?></label>
          <?php endforeach; ?>
        </div>
        <div class="satir">
          <div>
            <label for="ucret">Ücret (₺)</label>
            <input id="ucret" name="ucret" inputmode="decimal" value="<?= $t['ucret'] !== null ? e((float)$t['ucret']) : '' ?>" placeholder="Örn. 1500">
          </div>
          <div>
            <label for="odeme">Ödeme</label>
            <select id="odeme" name="odeme">
              <option value="">Seçin</option>
              <?php foreach (ODEMELER as $o): ?><option <?= $t['odeme'] === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <label for="not">Not ekle (yapılan iş, kullanılan malzeme vb.)</label>
        <textarea id="not" name="not" placeholder="Örn. Banyo gider hattı açıldı, sifon değiştirildi."></textarea>
        <button class="dugme turuncu tam">Kaydet</button>
      </form>
    </section>

    <?php if ($admin): ?>
    <section class="kart">
      <h2>Ustaya ata ve bilgileri düzenle</h2>
      <form method="post">
        <?= csrf_alan() ?>
        <input type="hidden" name="islem" value="guncelle">
        <label for="personel_id">Atanan usta</label>
        <select id="personel_id" name="personel_id">
          <option value="0">— Atanmadı —</option>
          <?php foreach ($ekip as $p): ?>
            <option value="<?= $p['id'] ?>" <?= (int)$t['personel_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['ad']) ?><?= $p['rol'] === 'admin' ? ' (yönetici)' : '' ?></option>
          <?php endforeach; ?>
        </select>
        <label for="randevu">Randevu</label>
        <input id="randevu" name="randevu" type="datetime-local" value="<?= $t['randevu'] ? date('Y-m-d\TH:i', strtotime($t['randevu'])) : '' ?>">
        <div class="satir">
          <div><label for="ad">Müşteri adı</label><input id="ad" name="ad" value="<?= e($t['ad']) ?>"></div>
          <div><label for="telefon">Telefon</label><input id="telefon" name="telefon" value="<?= e($t['telefon']) ?>" inputmode="tel" required></div>
        </div>
        <div class="satir">
          <div>
            <label for="hizmet">Hizmet</label>
            <select id="hizmet" name="hizmet">
              <?php foreach (array_unique(array_merge(hizmet_secenekleri(), [$t['hizmet'] ?: 'Diğer'])) as $h): ?><option <?= $t['hizmet'] === $h ? 'selected' : '' ?>><?= e($h) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="ilce">İlçe</label>
            <input id="ilce" name="ilce" list="ilce-liste" value="<?= e($t['ilce']) ?>">
            <datalist id="ilce-liste"><?php foreach ($bolgeler as $b): ?><option value="<?= e($b) ?>"><?php endforeach; ?></datalist>
          </div>
        </div>
        <label for="adres">Adres</label>
        <textarea id="adres" name="adres"><?= e($t['adres']) ?></textarea>
        <label for="aciklama">Açıklama</label>
        <textarea id="aciklama" name="aciklama"><?= e($t['aciklama']) ?></textarea>
        <label for="kaynak">Talep kaynağı</label>
        <select id="kaynak" name="kaynak">
          <?php foreach (KAYNAKLAR as $k): ?><option <?= $t['kaynak'] === $k ? 'selected' : '' ?>><?= e($k) ?></option><?php endforeach; ?>
        </select>
        <button class="dugme tam">Bilgileri Kaydet</button>
      </form>
    </section>

    <form method="post" onsubmit="return confirm('Talep #<?= $id ?> kalıcı olarak silinsin mi?')">
      <?= csrf_alan() ?>
      <input type="hidden" name="islem" value="sil">
      <button class="dugme kirmizi tam">Talebi Sil</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<?php panel_alt();
