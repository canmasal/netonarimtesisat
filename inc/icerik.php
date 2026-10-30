<?php
// Panel > Site Ayarları'ndan değiştirilebilen tüm site bilgileri ve metinleri.
// Her kayıt: anahtar => [grup, etiket, varsayılan değer, tip, ipucu]
// tip: 'satir' (tek satır), 'metin' (çok satır), 'liste' (her satır bir madde), 'cift' (her satır "Başlık | açıklama")
// Metinlerde şu kısaltmalar otomatik değişir: {firma} {telefon} {merkez} {deneyim} {deneyim_kisa}
// Başlıklarda *yıldız arasındaki* kısım turuncu renkte vurgulanır.

const ICERIK_GRUPLAR = [
    'firma'    => 'Firma ve iletişim bilgileri',
    'ust'      => 'Sitenin üst kısmı',
    'ana'      => 'Ana sayfa',
    'hak'      => 'Hakkımızda sayfası',
    'ilt'      => 'İletişim sayfası',
    'galeri'   => 'Galeri sayfası',
    'alt'      => 'Sitenin alt kısmı (footer) ve çağrı kutusu',
    'kvkk'     => 'KVKK ve çerezler',
];

const ICERIK = [
    // ---- Firma ----
    'site_adi'         => ['firma', 'Firma adı (logo yanında)', 'Net Onarım', 'satir', ''],
    'slogan'           => ['firma', 'Logo altındaki yazı', 'Su Tesisatçısı · İstanbul', 'satir', ''],
    'telefon'          => ['firma', 'Telefon (arama için, +90 ile)', '+905395761772', 'satir', 'Örn. +905395761772 — boşluksuz yazın.'],
    'telefon_gorunen'  => ['firma', 'Telefon (sitede görünen)', '0539 576 17 72', 'satir', ''],
    'whatsapp'         => ['firma', 'WhatsApp numarası', '', 'satir', 'Telefonla aynıysa boş bırakın. Farklıysa +90 ile yazın.'],
    'eposta'           => ['firma', 'E-posta', '', 'satir', 'Boş bırakırsanız sitede gösterilmez.'],
    'adres'            => ['firma', 'Açık adres', 'Fevzi Çakmak Mah. Gülibrişim Sk. No:2, Bahçelievler / İstanbul', 'satir', 'Google İşletme Profili\'ndeki adresle aynı olmalı.'],
    'calisma_saatleri' => ['firma', 'Çalışma saatleri', '7 gün 24 saat', 'satir', ''],
    'calisma_not'      => ['firma', 'Çalışma saatleri notu', 'Resmi tatil ve bayramlar dahil', 'satir', ''],
    'harita'           => ['firma', 'Google Haritalar yerleştirme kodu', '', 'metin', 'Google Haritalar\'da işletmenizi açın → Paylaş → Haritayı yerleştir → HTML\'yi kopyala ve buraya yapıştırın. Boş bırakırsanız harita adresten otomatik oluşur.'],

    // ---- Üst ----
    'ust_serit'        => ['ust', 'En üstteki ince şerit yazısı', 'Ustalarımız şu an sahada – 7/24 hizmet veriyoruz', 'satir', ''],

    // ---- Ana sayfa ----
    'ana_rozet'        => ['ana', 'Büyük başlığın üstündeki rozet', '7/24 Acil Servis · Aynı Gün Müdahale', 'satir', ''],
    'ana_baslik'       => ['ana', 'Büyük başlık', 'İstanbul\'da *Su Tesisatçısı* mı Arıyorsunuz?', 'satir', 'Turuncu olacak kısmı *yıldız* içine alın.'],
    'ana_metin'        => ['ana', 'Başlık altı yazısı', 'Kırmadan dökmeden, cihazla su kaçağı tespiti, tıkanıklık açma ve tüm tesisat arızalarında uzman ustalarımız kapınızda. Evinize zarar vermeden tesisatınıza sağlık katıyoruz.', 'metin', ''],
    'ana_maddeler'     => ['ana', 'Düğmelerin altındaki tikler', "Ücretsiz keşif & fiyat bilgisi\nKırmadan tespit\nİşçilik garantisi", 'liste', ''],
    'ana_form_baslik'  => ['ana', 'Form başlığı', 'Hızlı Servis Talebi', 'satir', ''],
    'ana_form_metin'   => ['ana', 'Form açıklaması', 'Formu doldurun, WhatsApp üzerinden hemen dönüş yapalım.', 'satir', ''],
    'ana_guven'        => ['ana', 'Güven şeridi (4 madde)', "7/24 Hizmet | Gece, hafta sonu, bayram\n{deneyim_kisa} Tecrübe | {merkez} merkezli\nGarantili İşçilik | Yapılan işin arkasındayız\nŞeffaf Fiyat | İş öncesi net fiyat", 'cift', 'Her satır: Başlık | açıklama'],
    'ana_hiz_baslik'   => ['ana', 'Hizmetler bölümü başlığı', 'Tüm Tesisat Sorunlarınıza Tek Adres', 'satir', ''],
    'ana_hiz_metin'    => ['ana', 'Hizmetler bölümü yazısı', 'Son teknoloji cihazlarla arızayı noktasal olarak tespit ediyor, gereksiz kırım yapmadan kalıcı çözüm üretiyoruz.', 'metin', ''],
    'ana_acil_baslik'  => ['ana', 'Acil şerit başlığı', 'Su mu basıyor? Boru mu patladı?', 'satir', ''],
    'ana_acil_metin'   => ['ana', 'Acil şerit yazısı', 'Önce ana vanayı kapatın, sonra bizi arayın. Ustalarımız en kısa sürede yolda.', 'satir', ''],
    'ana_neden_ust'    => ['ana', '"Neden biz" küçük başlık', 'Neden {firma}?', 'satir', ''],
    'ana_neden_baslik' => ['ana', '"Neden biz" başlığı', 'Evinize Zarar Vermeden, Kalıcı Çözüm', 'satir', ''],
    'ana_neden_metin'  => ['ana', '"Neden biz" yazısı', '{merkez} merkezli ekibimizle {deneyim} süredir İstanbul\'da tesisat sorunlarını hızlı, temiz ve dürüst şekilde çözüyoruz.', 'metin', ''],
    'ana_neden'        => ['ana', '"Neden biz" maddeleri (4 madde)', "Cihazla Noktasal Tespit | Akustik dinleme ve termal kamera ile arıza yerini kırmadan buluyoruz.\nSürpriz Ücret Yok | Arızayı tespit ettikten sonra, işe başlamadan önce fiyatı net olarak söylüyoruz.\nGarantili ve Temiz İşçilik | Kaliteli malzeme kullanıyor, işi bitirince ortamı temiz teslim ediyoruz.\nDakik ve Ulaşılabilir | Telefonu her saat açıyoruz, verdiğimiz saatte kapınızdayız.", 'cift', 'Her satır: Başlık | açıklama'],
    'ana_adim_baslik'  => ['ana', 'Çalışma adımları başlığı', '4 Adımda Sorununuz Çözülsün', 'satir', ''],
    'ana_adim_metin'   => ['ana', 'Çalışma adımları yazısı', 'Aramanızdan işin teslimine kadar her adımda sizi bilgilendiriyoruz.', 'satir', ''],
    'ana_adimlar'      => ['ana', 'Adımlar (4 madde)', "Bizi Arayın | Telefon ya da WhatsApp'tan sorununuzu kısaca anlatın.\nUsta Yola Çıksın | Size en yakın ustamız ekipmanıyla adresinize gelir.\nTespit & Fiyat | Arıza cihazla tespit edilir, işe başlamadan fiyat netleşir.\nGarantili Çözüm | Onarım yapılır, test edilir ve temiz şekilde teslim edilir.", 'cift', 'Her satır: Başlık | açıklama'],
    'ana_bolge_baslik' => ['ana', 'Hizmet bölgeleri başlığı', 'Hizmet Verdiğimiz İlçeler', 'satir', ''],
    'ana_bolge_metin'  => ['ana', 'Hizmet bölgeleri yazısı', 'Aşağıdaki ilçelerde hızlı su tesisatçısı hizmeti veriyoruz.', 'satir', ''],
    'ana_sss'          => ['ana', 'Sık sorulan sorular', "Su kaçağını kırmadan nasıl tespit ediyorsunuz? | Akustik dinleme cihazları, termal kameralar ve basınç testleri ile kaçağın yerini noktasal olarak belirliyoruz. Böylece yalnızca sorunlu noktaya küçük bir müdahale yapılıyor, fayans ve duvarlarınız gereksiz yere kırılmıyor.\nGece veya hafta sonu da geliyor musunuz? | Evet. 7 gün 24 saat, resmi tatil ve bayramlar dahil acil servis hizmeti veriyoruz. Su basması gibi acil durumlarda önce ana vanayı kapatıp bizi aramanız yeterli.\nFiyatı ne zaman öğrenirim? | Arızayı tespit ettikten sonra, işe başlamadan önce size net fiyat bilgisi veriyoruz. Onayınız olmadan işlem yapılmaz, sürpriz ücret çıkmaz.\nYapılan işin garantisi var mı? | Evet, yaptığımız işçiliğin arkasındayız. Kullanılan malzemeler de üretici garantisi kapsamındadır. Garanti detayları iş tesliminde size iletilir.\nSu faturam çok yüksek geldi, kaçak olabilir mi? | Evde hiçbir musluk açık değilken sayaç dönüyorsa büyük ihtimalle gizli bir su kaçağı vardır. Duvarda nem, kabarma, küf veya alt kata su sızması da kaçak belirtisidir. Bizi arayın, cihazla tespit edelim.", 'cift', 'Her satır: Soru | Cevap. "Hangi bölgelere hizmet veriyorsunuz?" sorusu otomatik eklenir.'],

    // ---- Hakkımızda ----
    'hak_baslik'       => ['hak', 'Sayfa başlığı', 'Tecrübe ve güvenle *{firma} Tesisat*', 'satir', ''],
    'hak_metin'        => ['hak', 'Başlık altı yazısı', '{merkez} merkezli {firma} Tesisat, {deneyim} süredir İstanbul\'da kırmadan su kaçağı tespiti, tıkanıklık açma ve genel sıhhi tesisat hizmetleri sunuyor.', 'metin', ''],
    'hak_icerik'       => ['hak', 'Sayfa metni', "## Biz kimiz?\n{firma} Tesisat, {deneyim} süredir evlerde, iş yerlerinde ve apartmanlarda su tesisatı sorunlarına kalıcı çözümler üreten bir sıhhi tesisat firmasıdır. {merkez} merkezimizden İstanbul'un birçok ilçesine hizmet veriyoruz.\n\nMesleğe başladığımız günden bu yana değişmeyen ilkemiz şudur: Arızayı tahminle değil, doğru tespitle çözmek. Bu yüzden gereksiz kırım yapmadan sorunun kaynağını bulmayı, müşterimize işe başlamadan önce ne yapılacağını ve ne kadar tutacağını açıkça anlatmayı işimizin temeli olarak görüyoruz.\n\n## Kırmadan dökmeden, cihazla tespit\nSu kaçaklarında termal kamera ve akustik dinleme cihazlarıyla kaçağın yerini noktasal olarak belirliyoruz. Fayans ve duvarlar yalnızca gereken noktada, gereken kadar açılıyor. Gider ve kanalizasyon tıkanıklıklarında ise robot ve kameralı sistemlerle hattı borulara zarar vermeden açıyor, sorunun tekrar etmemesi için asıl nedeni tespit ediyoruz.", 'metin', 'Ara başlık için satırı ## ile başlatın, paragrafları boş satırla ayırın.'],
    'hak_ilkeler'      => ['hak', 'Çalışma prensipleri', "Doğru tespit | Önce cihazla arızanın yerini ve nedenini buluruz.\nŞeffaf fiyat | İşe başlamadan net fiyat veririz; onayınız olmadan işlem yapılmaz.\nTemiz işçilik | Kaliteli malzeme kullanır, çalıştığımız alanı temiz teslim ederiz.\nGaranti | Yaptığımız işçiliğin arkasında dururuz.\nUlaşılabilirlik | Telefonumuz 7/24 açıktır; acil durumlarda öncelikli müdahale ederiz.", 'cift', 'Her satır: Başlık | açıklama'],

    // ---- İletişim ----
    'ilt_baslik'       => ['ilt', 'Sayfa başlığı', 'Bize *ulaşın*', 'satir', ''],
    'ilt_metin'        => ['ilt', 'Başlık altı yazısı', 'Su kaçağı, tıkanıklık ya da acil bir arıza mı var? Telefonumuz 7 gün 24 saat açık. Arayın, WhatsApp\'tan yazın ya da aşağıdaki formu doldurun; en kısa sürede size dönüş yapalım.', 'metin', ''],

    // ---- Galeri ----
    'galeri_baslik'    => ['galeri', 'Sayfa başlığı', 'Çalışmalarımızdan *kareler*', 'satir', ''],
    'galeri_metin'     => ['galeri', 'Başlık altı yazısı', 'Sahada yaptığımız su kaçağı tespiti, tıkanıklık açma ve tesisat yenileme işlerinden fotoğraflar.', 'metin', ''],

    // ---- Alt ----
    'alt_metin'        => ['alt', 'Alt kısımdaki firma tanıtımı', '{merkez} merkezli, {deneyim} süredir kırmadan su kaçağı tespiti ve sıhhi tesisat hizmeti. 7/24 açığız.', 'metin', ''],
    'cta_baslik'       => ['alt', 'Sayfa sonu çağrı kutusu başlığı', 'Tesisat sorununuzu bugün çözelim.', 'satir', ''],
    'cta_metin'        => ['alt', 'Sayfa sonu çağrı kutusu yazısı', 'Hemen arayın ya da WhatsApp\'tan fotoğraf gönderin, ustamız size ön bilgi versin.', 'satir', ''],
    'instagram'        => ['alt', 'Instagram adresi', '', 'satir', 'https://instagram.com/... — boşsa gösterilmez.'],
    'facebook'         => ['alt', 'Facebook adresi', '', 'satir', ''],

    // ---- KVKK ----
    'kvkk_unvan'       => ['kvkk', 'Veri sorumlusu (ad soyad / unvan)', '{firma} Tesisat', 'satir', 'Şahıs işletmesiyseniz işletme sahibinin adı soyadıyla yazın. Örn. Ahmet Yılmaz – Net Onarım Tesisat. Şirketse ticaret unvanını yazın.'],
    'kvkk_vergi'       => ['kvkk', 'Vergi dairesi / no ya da MERSİS no (isteğe bağlı)', '', 'satir', 'Boş bırakırsanız metinde gösterilmez.'],
    'kvkk_eposta'      => ['kvkk', 'KVKK başvuruları için e-posta', '', 'satir', 'Boş bırakırsanız Firma bilgilerindeki e-posta kullanılır.'],
    'kvkk_kep'         => ['kvkk', 'KEP adresi (isteğe bağlı)', '', 'satir', 'Kayıtlı elektronik posta adresiniz varsa yazın.'],
    'kvkk_tarih'       => ['kvkk', 'Metinlerin son güncellenme tarihi', '20.10.2026', 'satir', 'Metinlerde değişiklik yaptığınızda tarihi güncelleyin.'],
    'kvkk_form_onay'   => ['kvkk', 'Formdaki onay kutusu yazısı', 'KVKK Aydınlatma Metni\'ni okudum. Talebime dönüş yapılması için bilgilerimin işlenmesini ve WhatsApp üzerinden iletilmesini kabul ediyorum.', 'metin', '"KVKK Aydınlatma Metni" ifadesi otomatik olarak sayfaya link olur.'],
    'cerez_serit'      => ['kvkk', 'Çerez şeridi yazısı', 'Sitemizde zorunlu çerezlerin yanında, izniniz olursa ziyaretleri ölçmek için analiz çerezleri (Google Analytics) kullanıyoruz.', 'metin', ''],
];

/** Ayardan ham değer (yer tutucular değiştirilmeden). */
function icerik_ham(string $k): string
{
    $v = ayar($k);
    if ($v !== '') return $v;
    return ICERIK[$k][2] ?? '';
}

/** Kısaltmaları değiştirilmiş düz metin. */
function metin(string $k): string
{
    return yer_tut(icerik_ham($k));
}

function yer_tut(string $s): string
{
    return strtr($s, [
        '{firma}' => firma_adi(),
        '{telefon}' => tel_gorunen(),
        '{merkez}' => ayar('merkez_ilce') ?: 'İstanbul',
        '{deneyim}' => ayar('deneyim') ?: '20 yılı aşkın',
        '{deneyim_kisa}' => deneyim_kisa(),
    ]);
}

/** Başlık: güvenli HTML, *vurgu* turuncu span olur. */
function vurgu(string $s): string
{
    return preg_replace('/\*([^*]+)\*/u', '<span>$1</span>', e($s));
}

/** 'liste' tipindeki metni satır dizisine çevirir. */
function liste(string $k): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', metin($k)))));
}

/** 'cift' tipindeki metni [başlık, açıklama] çiftlerine çevirir. */
function ciftler(string $k): array
{
    $out = [];
    foreach (liste($k) as $sat) {
        [$a, $b] = array_pad(array_map('trim', explode('|', $sat, 2)), 2, '');
        if ($a !== '') $out[] = [$a, $b];
    }
    return $out;
}

/** Formdaki onay yazısı; "KVKK Aydınlatma Metni" ifadesi sayfaya link olur. */
function kvkk_onay_html(): string
{
    $m = e(metin('kvkk_form_onay'));
    $link = '<a href="/kvkk-aydinlatma-metni" target="_blank">KVKK Aydınlatma Metni</a>';
    return str_contains($m, 'KVKK Aydınlatma Metni') ? preg_replace('/KVKK Aydınlatma Metni/u', $link, $m, 1) : $m . ' ' . $link;
}

// ---- Firma bilgileri kısayolları ----
function firma_adi(): string { return icerik_ham('site_adi') ?: 'Net Onarım'; }
function tel(): string { return preg_replace('/[^0-9+]/', '', icerik_ham('telefon')) ?: '+905395761772'; }
function tel_gorunen(): string { return icerik_ham('telefon_gorunen') ?: tel(); }
function wa_no(): string
{
    $w = preg_replace('/\D/', '', icerik_ham('whatsapp') ?: tel());
    if (str_starts_with($w, '0')) $w = '90' . substr($w, 1);
    return $w;
}
function wa(string $mesaj = ''): string
{
    return 'https://wa.me/' . wa_no() . ($mesaj !== '' ? '?text=' . rawurlencode($mesaj) : '');
}
