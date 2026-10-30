<?php
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0775, true);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    sema_olustur($pdo);
    return $pdo;
}

function sema_olustur(PDO $pdo): void
{
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS kullanicilar (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ad TEXT NOT NULL,
        kullanici_adi TEXT NOT NULL UNIQUE,
        sifre TEXT NOT NULL,
        rol TEXT NOT NULL CHECK (rol IN ('admin','personel')),
        telefon TEXT,
        aktif INTEGER NOT NULL DEFAULT 1,
        olusturma TEXT
    );
    CREATE TABLE IF NOT EXISTS bolgeler (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ad TEXT NOT NULL UNIQUE,
        sira INTEGER NOT NULL DEFAULT 0,
        aktif INTEGER NOT NULL DEFAULT 1
    );
    CREATE TABLE IF NOT EXISTS talepler (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ad TEXT,
        telefon TEXT,
        hizmet TEXT,
        ilce TEXT,
        adres TEXT,
        aciklama TEXT,
        kaynak TEXT NOT NULL DEFAULT 'Web formu',
        durum TEXT NOT NULL DEFAULT 'yeni',
        personel_id INTEGER REFERENCES kullanicilar(id) ON DELETE SET NULL,
        randevu TEXT,
        ucret REAL,
        odeme TEXT,
        ip TEXT,
        olusturma TEXT,
        guncelleme TEXT,
        tamamlanma TEXT
    );
    CREATE INDEX IF NOT EXISTS ix_talep_durum ON talepler(durum);
    CREATE INDEX IF NOT EXISTS ix_talep_personel ON talepler(personel_id);
    CREATE TABLE IF NOT EXISTS talep_gecmisi (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        talep_id INTEGER NOT NULL REFERENCES talepler(id) ON DELETE CASCADE,
        kullanici_id INTEGER,
        tur TEXT NOT NULL DEFAULT 'sistem',
        metin TEXT NOT NULL,
        olusturma TEXT
    );
    CREATE TABLE IF NOT EXISTS giris_denemeleri (
        ip TEXT NOT NULL,
        zaman INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS ayarlar (
        anahtar TEXT PRIMARY KEY,
        deger TEXT
    );
    CREATE TABLE IF NOT EXISTS blog (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        baslik TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        ozet TEXT,
        icerik TEXT,
        hizmet TEXT,
        aktif INTEGER NOT NULL DEFAULT 1,
        yayin TEXT,
        guncelleme TEXT
    );
    CREATE TABLE IF NOT EXISTS galeri (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        dosya TEXT NOT NULL,
        kucuk TEXT NOT NULL,
        baslik TEXT,
        en INTEGER,
        boy INTEGER,
        sira INTEGER NOT NULL DEFAULT 0,
        aktif INTEGER NOT NULL DEFAULT 1,
        olusturma TEXT
    );
    CREATE TABLE IF NOT EXISTS hizmetler (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT NOT NULL UNIQUE,
        ad TEXT NOT NULL,
        kisa_ad TEXT,
        ikon TEXT,
        kart TEXT,
        baslik TEXT,
        meta TEXT,
        giris TEXT,
        bolumler TEXT,
        maddeler TEXT,
        sss TEXT,
        sira INTEGER NOT NULL DEFAULT 0,
        aktif INTEGER NOT NULL DEFAULT 1,
        guncelleme TEXT
    );
    CREATE TABLE IF NOT EXISTS mahalle_icerik (
        bolge_id INTEGER NOT NULL REFERENCES bolgeler(id) ON DELETE CASCADE,
        ad TEXT NOT NULL,
        aciklama TEXT,
        guncelleme TEXT,
        PRIMARY KEY (bolge_id, ad)
    );
    ");

    // Sonradan eklenen sütunlar (eski veritabanlarını da günceller)
    $tsut = array_column($pdo->query('PRAGMA table_info(talepler)')->fetchAll(), 'name');
    if (!in_array('kvkk_onay', $tsut, true)) $pdo->exec('ALTER TABLE talepler ADD COLUMN kvkk_onay TEXT');

    $sutunlar = array_column($pdo->query('PRAGMA table_info(bolgeler)')->fetchAll(), 'name');
    foreach (['aciklama' => 'TEXT', 'mahalleler' => 'TEXT', 'guncelleme' => 'TEXT'] as $s => $tip) {
        if (!in_array($s, $sutunlar, true)) $pdo->exec("ALTER TABLE bolgeler ADD COLUMN $s $tip");
    }

    // Hizmet bölgeleri ilk kurulumda doldurulur; sonra panelden yönetilir.
    if ((int)$pdo->query('SELECT COUNT(*) FROM bolgeler')->fetchColumn() === 0) {
        $ilceler = ['Bakırköy', 'Bahçelievler', 'Bağcılar', 'Güngören', 'Zeytinburnu', 'Küçükçekmece',
            'Avcılar', 'Esenyurt', 'Beylikdüzü', 'Başakşehir', 'Esenler', 'Bayrampaşa', 'Fatih',
            'Eyüpsultan', 'Gaziosmanpaşa', 'Sultangazi'];
        $s = $pdo->prepare('INSERT INTO bolgeler (ad, sira) VALUES (?, ?)');
        foreach ($ilceler as $i => $ad) $s->execute([$ad, ($i + 1) * 10]);
    }

    // Ümraniye hizmet bölgelerinden çıkarıldı (bir kez çalışır)
    if (!$pdo->query("SELECT deger FROM ayarlar WHERE anahtar = 'umraniye_kaldir_v1'")->fetchColumn()) {
        $pdo->exec("DELETE FROM bolgeler WHERE ad = 'Ümraniye'");
        $pdo->exec("INSERT OR REPLACE INTO ayarlar (anahtar, deger) VALUES ('umraniye_kaldir_v1', '1')");
    }

    // Petek temizliği hizmeti kaldırıldı: ilgili blog yazısını yayından kaldır (bir kez çalışır)
    if (!$pdo->query("SELECT deger FROM ayarlar WHERE anahtar = 'petek_kaldir_v1'")->fetchColumn()) {
        $pdo->exec("UPDATE blog SET aktif = 0 WHERE slug = 'petek-temizligi-ne-zaman-yapilmali'");
        $pdo->exec("DELETE FROM ayarlar WHERE anahtar = 'hizmet_petek-temizligi'");
        $pdo->exec("INSERT OR REPLACE INTO ayarlar (anahtar, deger) VALUES ('petek_kaldir_v1', '1')");
    }

    // Hizmetler tablosu ilk kez dosyadaki varsayılanlarla (ve varsa panelde yapılmış değişikliklerle) doldurulur.
    if (!$pdo->query("SELECT deger FROM ayarlar WHERE anahtar = 'hizmet_tablo_v1'")->fetchColumn()) {
        require_once __DIR__ . '/hizmetler.php';
        $ekle = $pdo->prepare('INSERT OR IGNORE INTO hizmetler (slug, ad, kisa_ad, ikon, kart, baslik, meta, giris, bolumler, maddeler, sss, sira, aktif, guncelleme) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $oku = $pdo->prepare('SELECT deger FROM ayarlar WHERE anahtar = ?');
        $i = 0;
        foreach (SITE_HIZMETLERI as $sl => $h) {
            $oku->execute(['hizmet_' . $sl]);
            $o = json_decode((string)$oku->fetchColumn() ?: 'null', true);
            if (is_array($o)) foreach ($o as $k => $v) if (array_key_exists($k, $h) && $k !== 'ikon' && $v !== '' && $v !== []) $h[$k] = $v;
            $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE);
            $ekle->execute([$sl, $h['ad'], $h['kisa_ad'], $h['ikon'], $h['kart'], $h['baslik'], $h['meta'], $h['giris'], $j($h['bolumler']), $j($h['maddeler']), $j($h['sss']), (++$i) * 10, ($h['aktif'] ?? true) ? 1 : 0, date('Y-m-d H:i:s')]);
        }
        $pdo->exec("INSERT OR REPLACE INTO ayarlar (anahtar, deger) VALUES ('hizmet_tablo_v1', '1')");
    }

    // Blog ilk kurulumda örnek yazılarla doldurulur.
    if (!$pdo->query("SELECT deger FROM ayarlar WHERE anahtar = 'blog_tohum_v1'")->fetchColumn()) {
        require_once __DIR__ . '/blog-varsayilan.php';
        $s = $pdo->prepare('INSERT OR IGNORE INTO blog (baslik, slug, ozet, icerik, hizmet, yayin, guncelleme) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach (BLOG_VARSAYILAN as $y) $s->execute([$y['baslik'], $y['slug'], $y['ozet'], $y['icerik'], $y['hizmet'], $y['tarih'], $y['tarih']]);
        $pdo->exec("INSERT OR REPLACE INTO ayarlar (anahtar, deger) VALUES ('blog_tohum_v1', '1')");
    }

    // Mahalle listesi boş olan ilçeleri varsayılan mahallelerle bir kez doldur.
    $tohum = $pdo->query("SELECT deger FROM ayarlar WHERE anahtar = 'mahalle_tohum_v1'")->fetchColumn();
    if (!$tohum) {
        require_once __DIR__ . '/mahalleler.php';
        $s = $pdo->prepare("UPDATE bolgeler SET mahalleler = ? WHERE ad = ? AND (mahalleler IS NULL OR TRIM(mahalleler) = '')");
        foreach (MAHALLE_VARSAYILAN as $ilce => $liste) $s->execute([implode(', ', $liste), $ilce]);
        $pdo->exec("INSERT OR REPLACE INTO ayarlar (anahtar, deger) VALUES ('mahalle_tohum_v1', '1')");
    }
}

function aktif_bolgeler(): array
{
    return db()->query('SELECT ad FROM bolgeler WHERE aktif = 1 ORDER BY sira, id')->fetchAll(PDO::FETCH_COLUMN);
}

/** Formlardaki "Sorununuz" seçenekleri: sitedeki aktif hizmetler + Diğer. */
function hizmet_secenekleri(): array
{
    try {
        $l = db()->query('SELECT ad FROM hizmetler WHERE aktif = 1 ORDER BY sira, id')->fetchAll(PDO::FETCH_COLUMN);
    } catch (Throwable $e) {
        $l = [];
    }
    if (!$l) $l = array_values(array_diff(HIZMETLER, ['Diğer']));
    $l[] = 'Diğer';
    return $l;
}
