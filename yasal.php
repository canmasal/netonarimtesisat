<?php
// /kvkk-aydinlatma-metni  ve  /cerez-politikasi
require __DIR__ . '/inc/site-layout.php';

$sayfa = ($_GET['s'] ?? '') === 'cerez' ? 'cerez' : 'kvkk';
$unvan = metin('kvkk_unvan');
$adres = icerik_ham('adres');
$eposta = icerik_ham('kvkk_eposta') ?: icerik_ham('eposta');
$kep = icerik_ham('kvkk_kep');
$vergi = icerik_ham('kvkk_vergi');
$tarih = metin('kvkk_tarih');
$site = preg_replace('#^https?://#', '', site_url(''));
$ga = ayar('ga4_id') || ayar('ads_id');

if ($sayfa === 'kvkk') {
    $yol = '/kvkk-aydinlatma-metni';
    $baslik = 'KVKK Aydınlatma Metni';
} else {
    $yol = '/cerez-politikasi';
    $baslik = 'Çerez Politikası';
}

site_ust([
    'baslik'   => $baslik . ' – ' . firma_adi() . ' Tesisat',
    'aciklama' => firma_adi() . ' Tesisat ' . mb_strtolower($baslik) . ': kişisel verilerinizin hangi amaçla işlendiği, kimlerle paylaşıldığı ve haklarınız.',
    'yol'      => $yol,
    'schema'   => [schema_isletme(), schema_breadcrumb([['Ana Sayfa', '/'], [$baslik, $yol]])],
]);
?>
<main>
<section class="page-hero">
  <div class="container">
    <ol class="crumbs"><li><a href="/">Ana Sayfa</a></li><li><?= e($baslik) ?></li></ol>
    <h1><?= e($baslik) ?></h1>
    <p class="lead">Son güncelleme: <?= e($tarih) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <article class="prose yasal">
<?php if ($sayfa === 'kvkk'): ?>
      <p>Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu'nun (“KVKK”) 10. maddesi ve Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ uyarınca, <b><?= e($site) ?></b> internet sitesini kullanan, bizi arayan veya bize WhatsApp üzerinden yazan kişileri bilgilendirmek amacıyla hazırlanmıştır.</p>

      <h2>1. Veri sorumlusu</h2>
      <ul>
        <li><b>Veri sorumlusu:</b> <?= e($unvan) ?></li>
        <?php if ($vergi): ?><li><b>Vergi / MERSİS bilgisi:</b> <?= e($vergi) ?></li><?php endif; ?>
        <?php if ($adres): ?><li><b>Adres:</b> <?= e($adres) ?></li><?php endif; ?>
        <li><b>Telefon:</b> <?= e(tel_gorunen()) ?></li>
        <?php if ($eposta): ?><li><b>E-posta:</b> <?= e($eposta) ?></li><?php endif; ?>
        <?php if ($kep): ?><li><b>KEP adresi:</b> <?= e($kep) ?></li><?php endif; ?>
      </ul>

      <h2>2. İşlenen kişisel verileriniz</h2>
      <ul>
        <li><b>Kimlik ve iletişim:</b> Adınız soyadınız, telefon numaranız.</li>
        <li><b>Hizmet bilgisi:</b> Talep ettiğiniz hizmet, ilçe / semt, adres, arızaya ilişkin açıklamanız ve gönderdiğiniz fotoğraflar, randevu tarihi.</li>
        <li><b>Müşteri işlem bilgisi:</b> Yapılan iş, ücret ve ödeme şekli, iş geçmişi notları.</li>
        <li><b>İşlem güvenliği:</b> Form gönderdiğiniz sıradaki IP adresi ve zaman bilgisi.</li>
        <?php if ($ga): ?><li><b>Kullanım bilgisi:</b> Yalnızca analiz çerezlerine izin verirseniz; ziyaret ettiğiniz sayfalar, cihaz ve tarayıcı bilgisi, telefon / WhatsApp düğmelerine tıklama.</li><?php endif; ?>
      </ul>
      <p>Sitemizde özel nitelikli kişisel veri (sağlık, din, biyometrik veri vb.) istenmez. Lütfen form ve mesajlarda bu tür bilgileri paylaşmayın.</p>

      <h2>3. İşleme amaçları</h2>
      <ul>
        <li>Servis talebinizi almak, sizinle iletişime geçmek ve randevu oluşturmak,</li>
        <li>Tesisat hizmetini yerine getirmek, ustamızı adresinize yönlendirmek,</li>
        <li>Ücretlendirme, ödeme ve garanti süreçlerini yürütmek,</li>
        <li>Talep ve şikâyetlerinizi yanıtlamak, hizmet kalitesini artırmak,</li>
        <li>Yasal yükümlülüklerimizi (vergi, muhasebe vb.) yerine getirmek,</li>
        <li>Sitenin güvenliğini sağlamak ve kötüye kullanımı (spam) önlemek<?= $ga ? ',' : '.' ?></li>
        <?php if ($ga): ?><li>İzin vermeniz hâlinde site kullanımını ölçmek ve reklamlarımızın etkisini görmek.</li><?php endif; ?>
      </ul>

      <h2>4. Hukuki sebepler ve toplama yöntemi</h2>
      <p>Kişisel verileriniz; internet sitemizdeki formlar, telefon görüşmeleri, WhatsApp mesajları ve yerinde verilen hizmet sırasında, kısmen otomatik ve kısmen otomatik olmayan yollarla toplanır. Verileriniz KVKK'nın 5. maddesindeki şu hukuki sebeplere dayanılarak işlenir:</p>
      <ul>
        <li>Bir sözleşmenin kurulması veya ifasıyla doğrudan ilgili olması (md. 5/2-c): talebinizin karşılanması ve hizmetin verilmesi,</li>
        <li>Kanunlarda açıkça öngörülmesi ve hukuki yükümlülüğümüzü yerine getirmemiz (md. 5/2-a, ç): fatura, vergi ve muhasebe kayıtları,</li>
        <li>Temel hak ve özgürlüklerinize zarar vermemek kaydıyla meşru menfaatimiz (md. 5/2-f): site güvenliği, iş kayıtlarının tutulması,</li>
        <li>Açık rızanız (md. 5/1 ve md. 9): WhatsApp üzerinden yazışma<?= $ga ? ' ile analiz ve reklam çerezleri' : '' ?>.</li>
      </ul>

      <h2>5. Kişisel verilerin aktarılması</h2>
      <p>Kişisel verileriniz satılmaz ve pazarlama amacıyla üçüncü kişilere verilmez. Yalnızca yukarıdaki amaçlarla sınırlı olarak şu kişilere aktarılabilir:</p>
      <ul>
        <li>İşi yapacak ustamız ve çalışanlarımız,</li>
        <li>Sitemizin barındırıldığı hosting hizmet sağlayıcısı (Türkiye),</li>
        <li>Muhasebe hizmeti aldığımız kişiler ile talep hâlinde yetkili kamu kurum ve kuruluşları,</li>
        <li><b>Yurt dışı aktarım:</b> Bize WhatsApp üzerinden yazdığınızda mesajlarınız WhatsApp LLC / Meta Platforms Inc. altyapısı üzerinden iletilir<?= $ga ? '; analiz çerezlerine izin verirseniz kullanım verileri Google LLC (Google Analytics) sunucularına aktarılır' : '' ?>. Bu aktarımlar KVKK'nın 9. maddesi kapsamında açık rızanıza dayanır. WhatsApp kullanmak istemezseniz bizi doğrudan telefonla arayabilirsiniz.</li>
      </ul>

      <h2>6. Saklama süresi</h2>
      <p>Servis talebi ve iş kayıtları, ilgili mevzuatta öngörülen süreler (vergi ve ticari kayıtlar için 10 yıl) ile olası uyuşmazlıklar için zamanaşımı süresi boyunca saklanır. İşe dönüşmeyen talepler en geç 2 yıl içinde silinir veya anonim hâle getirilir. Süresi dolan veriler Kişisel Verilerin Silinmesi, Yok Edilmesi veya Anonim Hale Getirilmesi Hakkında Yönetmelik'e uygun şekilde imha edilir.</p>

      <h2>7. KVKK 11. madde kapsamındaki haklarınız</h2>
      <p>Bize başvurarak;</p>
      <ul>
        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme ve işlenmişse bilgi talep etme,</li>
        <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme,</li>
        <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme,</li>
        <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme,</li>
        <li>KVKK'nın 7. maddesindeki şartlar çerçevesinde silinmesini veya yok edilmesini isteme,</li>
        <li>Düzeltme, silme ve yok etme işlemlerinin aktarıldığı üçüncü kişilere bildirilmesini isteme,</li>
        <li>Otomatik sistemlerle analiz edilmesi sonucu aleyhinize bir sonuç çıkmasına itiraz etme,</li>
        <li>Kanuna aykırı işleme nedeniyle zarara uğramanız hâlinde zararın giderilmesini talep etme</li>
      </ul>
      <p>haklarına sahipsiniz. Verdiğiniz açık rızayı dilediğiniz zaman geri alabilirsiniz; çerez izninizi sayfanın altındaki <b>Çerez tercihleri</b> bağlantısından değiştirebilirsiniz.</p>

      <h2>8. Başvuru yöntemi</h2>
      <p>Haklarınıza ilişkin taleplerinizi, Veri Sorumlusuna Başvuru Usul ve Esasları Hakkında Tebliğ'e uygun olarak kimliğinizi doğrulayan bilgilerle birlikte;</p>
      <ul>
        <?php if ($adres): ?><li>Islak imzalı dilekçe ile <b><?= e($adres) ?></b> adresine elden teslim ederek veya noter aracılığıyla,</li><?php endif; ?>
        <?php if ($kep): ?><li><b><?= e($kep) ?></b> KEP adresine,</li><?php endif; ?>
        <?php if ($eposta): ?><li>Daha önce bize bildirdiğiniz ve sistemimizde kayıtlı e-posta adresinizden <b><?= e($eposta) ?></b> adresine</li><?php endif; ?>
      </ul>
      <p>iletebilirsiniz. Başvurunuz en geç 30 gün içinde ücretsiz olarak sonuçlandırılır. İşlemin ayrıca bir maliyet gerektirmesi hâlinde Kişisel Verileri Koruma Kurulu'nca belirlenen tarifedeki ücret alınabilir.</p>
<?php else: ?>
      <p>Bu politika, <b><?= e($site) ?></b> sitesinde hangi çerezlerin kullanıldığını ve bunları nasıl yönetebileceğinizi açıklar. Çerezler, siteyi ziyaret ettiğinizde tarayıcınıza kaydedilen küçük metin dosyalarıdır. Kişisel verilerinizin işlenmesi hakkında ayrıntılı bilgi için <a href="/kvkk-aydinlatma-metni">KVKK Aydınlatma Metni</a>'ni inceleyebilirsiniz.</p>

      <h2>Kullandığımız çerezler</h2>
      <div class="tablo-yasal"><table>
        <tr><th>Çerez</th><th>Türü</th><th>Amacı</th><th>Süresi</th></tr>
        <tr><td><code>cerez_tercih</code></td><td>Zorunlu</td><td>Çerez tercihinizi hatırlamak</td><td>12 ay</td></tr>
        <tr><td><code>PHPSESSID</code></td><td>Zorunlu</td><td>Yalnızca yönetim paneline giriş yapan çalışanlarımız için oturum</td><td>Tarayıcı kapanınca</td></tr>
        <?php if ($ga): ?>
        <tr><td><code>_ga</code>, <code>_ga_*</code></td><td>Analiz (izne bağlı)</td><td>Google Analytics ile ziyaretçi sayısını ve hangi sayfaların okunduğunu ölçmek</td><td>En fazla 2 yıl</td></tr>
        <?php if (ayar('ads_id')): ?><tr><td><code>_gcl_*</code></td><td>Reklam (izne bağlı)</td><td>Google Ads reklamlarından gelen aramaları ölçmek</td><td>90 gün</td></tr><?php endif; ?>
        <?php endif; ?>
      </table></div>

      <h2>Zorunlu çerezler</h2>
      <p>Sitenin çalışması ve tercihinizin hatırlanması için gereklidir; izin gerektirmez ve kapatılamaz. Hukuki sebebi KVKK md. 5/2-c ve 5/2-f'dir.</p>

      <h2>Analiz ve reklam çerezleri</h2>
      <p>Bu çerezler <b>yalnızca “Kabul et” derseniz</b> çalışır. Reddederseniz Google Analytics sitemizde hiç yüklenmez. Bu çerezler aracılığıyla toplanan veriler Google LLC'ye (ABD) aktarılır; hukuki sebebi açık rızanızdır (KVKK md. 5/1 ve md. 9).</p>

      <h2>Tercihinizi değiştirme</h2>
      <p>İzninizi istediğiniz zaman geri alabilir veya değiştirebilirsiniz:</p>
      <p><button type="button" class="btn btn-call js-cerez-ac">Çerez tercihlerini aç</button></p>
      <p>Ayrıca tarayıcınızın ayarlarından çerezleri silebilir veya engelleyebilirsiniz. Zorunlu çerezleri engellerseniz sitenin bazı bölümleri çalışmayabilir.</p>

      <h2>İletişim</h2>
      <p>Sorularınız için: <?= e($unvan) ?> · <?= e(tel_gorunen()) ?><?= $eposta ? ' · ' . e($eposta) : '' ?></p>
<?php endif; ?>
    </article>
  </div>
</section>
</main>
<?php site_alt();
