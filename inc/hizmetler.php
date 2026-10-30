<?php
// Hizmet sayfalarının içeriği. Metinleri buradan düzenleyebilirsiniz.
// Anahtar = adres: /hizmet/{anahtar}
// Bunlar ilk kurulumdaki varsayılanlardır; sonra hizmetler Panel > Hizmetler'den eklenir, düzenlenir, kaldırılır.

const SITE_HIZMETLERI = [
    'su-kacagi-tespiti' => [
        'ad' => 'Su Kaçağı Tespiti',
        'kisa_ad' => 'su kaçağı tespiti',
        'ikon' => 'i-radar',
        'kart' => 'Akustik dinleme ve termal kamera ile duvar ve zemini kırmadan su kaçağının yerini nokta atışı buluyoruz. Yalnızca gereken noktaya müdahale.',
        'baslik' => 'Su Kaçağı Tespiti İstanbul | Kırmadan Cihazla Tespit – Net Onarım',
        'meta' => 'İstanbul\'da kırmadan dökmeden cihazla su kaçağı tespiti. Akustik dinleme, termal kamera ve basınç testiyle kaçak noktası bulunur. 7/24: 0539 576 17 72',
        'giris' => 'Su faturanız beklenmedik şekilde yükseldiyse, duvarda nem, kabarma ya da küf varsa veya alt kata su sızıyorsa gizli bir su kaçağı olabilir. Kaçağın yerini tahminle değil, cihazla buluyor; fayans ve duvarları gereksiz yere kırmadan yalnızca sorunlu noktaya müdahale ediyoruz.',
        'bolumler' => [
            ['Su kaçağı belirtileri', 'Tüm musluklar kapalıyken su sayacının dönmesi, duvar diplerinde ve tavanlarda lekelenme, boya kabarması, parkede şişme, kötü koku ve kombinin sık sık basınç kaybetmesi en sık görülen belirtilerdir. Belirtileri fark ettiğinizde erken tespit, hem su israfını hem de yapıya verilecek zararı önler.'],
            ['Hangi cihazları kullanıyoruz?', 'Akustik dinleme cihazı, basınçlı borudan kaçan suyun çıkardığı sesi duvar ve zemin üzerinden dinleyerek noktayı belirler. Termal kamera, sıcak su ve kalorifer hatlarındaki kaçakları sıcaklık farkından gösterir. Basınç testi ise hattın hangi bölümünde kayıp olduğunu ortaya koyar. Gerekirse nem ölçer ve kameralı inceleme ile sonuç doğrulanır.'],
            ['Tespitten sonra ne oluyor?', 'Kaçak noktası işaretlendikten sonra yapılacak işi ve fiyatı size net olarak söylüyoruz. Onayınızla yalnızca o noktayı açıp onarıyor, hattı tekrar test ediyor ve alanı temiz teslim ediyoruz.'],
        ],
        'maddeler' => ['Temiz su ve sıcak su hatlarında kaçak tespiti', 'Kalorifer ve yerden ısıtma hattı kaçakları', 'Banyo, mutfak ve balkon zemin altı kaçakları', 'Sayaç sonrası bina giriş hattı kontrolü', 'Kaçak onarımı ve basınç testiyle teslim'],
        'sss' => [
            ['Su kaçağını kırmadan nasıl buluyorsunuz?', 'Akustik dinleme cihazı, termal kamera ve basınç testi ile kaçağın yerini noktasal olarak belirliyoruz. Böylece yalnızca sorunlu noktaya küçük bir müdahale yapılır.'],
            ['Su faturam yüksek geldi, kaçak olabilir mi?', 'Evdeki tüm musluklar kapalıyken sayaç dönmeye devam ediyorsa büyük ihtimalle gizli bir kaçak vardır. Cihazla tespit ederek kesin sonucu söyleyebiliriz.'],
            ['Tespit ne kadar sürer?', 'Çoğu konutta tespit aynı ziyarette tamamlanır. Süre; tesisatın uzunluğuna, kaçağın türüne ve ortamın gürültüsüne göre değişir.'],
        ],
    ],
    'tikaniklik-acma' => [
        'ad' => 'Tıkanıklık Açma',
        'kisa_ad' => 'tıkanıklık açma',
        'ikon' => 'i-cam',
        'kart' => 'Lavabo, tuvalet, banyo ve mutfak gideri, logar ve kanalizasyon tıkanıklıkları robot ve kameralı sistemlerle açılır, nedeni tespit edilir.',
        'baslik' => 'Tıkanıklık Açma İstanbul | Lavabo, Tuvalet, Gider Açma – Net Onarım',
        'meta' => 'İstanbul\'da lavabo, tuvalet, mutfak ve banyo gideri, logar ve kanalizasyon tıkanıklığı açma. Robot ve kameralı sistem, 7/24 servis: 0539 576 17 72',
        'giris' => 'Yavaş akan lavabo, taşan tuvalet ya da kötü koku yayan gider günlük hayatı hemen durdurur. Tıkanıklığı makineyle açıyor, gerekirse kamerayla hattın içine bakarak sorunun tekrar etmemesi için asıl nedenini buluyoruz.',
        'bolumler' => [
            ['Hangi tıkanıklıkları açıyoruz?', 'Mutfak lavabosu ve bulaşık makinesi giderleri, banyo lavabosu, duş ve küvet giderleri, klozet ve rezervuar kaynaklı tıkanıklıklar, balkon ve teras süzgeçleri, bina pis su kolonları, logar ve kanalizasyon bağlantıları.'],
            ['Kimyasal yerine makine', 'Hazır kimyasallar boruya zarar verebilir ve çoğu zaman tıkanıklığı yalnızca geçici olarak açar. Spiral (robot) makine ve basınçlı su ile hattı mekanik olarak temizliyor, borulara zarar vermeden kalıcı sonuç alıyoruz.'],
            ['Kameralı inceleme', 'Sık tekrar eden tıkanıklıklarda hattın içine kamera gönderilir. Kırık, çökmüş ya da eğimi bozuk boru, kök girmesi veya yağ birikimi gibi asıl sorun görüntülenir ve size gösterilir.'],
        ],
        'maddeler' => ['Lavabo, duş ve küvet gideri açma', 'Tuvalet ve klozet tıkanıklığı açma', 'Mutfak gideri ve yağ birikimi temizliği', 'Logar ve kanalizasyon açma', 'Kameralı gider hattı görüntüleme'],
        'sss' => [
            ['Tıkanıklık kırmadan açılır mı?', 'Tıkanıklıkların büyük çoğunluğu gider ağzından makineyle, kırım yapmadan açılır. Kırım yalnızca boru kırık ya da çökmüşse ve kamerayla doğrulandıktan sonra önerilir.'],
            ['Aynı gider sürekli tıkanıyor, neden?', 'Tekrarlayan tıkanıklıklarda genellikle eğim hatası, boru içi yağ ve kireç birikimi ya da hasarlı boru vardır. Kameralı inceleme ile nedeni tespit ediyoruz.'],
            ['Gece de geliyor musunuz?', 'Evet, taşan tuvalet ve gider gibi acil durumlarda 7/24 hizmet veriyoruz.'],
        ],
    ],
    'tesisat-yenileme' => [
        'ad' => 'Tesisat Yenileme',
        'kisa_ad' => 'tesisat yenileme',
        'ikon' => 'i-pipe',
        'kart' => 'Eskiyen, paslanan ya da sık arıza yapan temiz su ve pis su tesisatınızı kaliteli malzemeyle baştan yeniliyoruz.',
        'baslik' => 'Tesisat Yenileme İstanbul | Temiz Su ve Pis Su Tesisatı – Net Onarım',
        'meta' => 'İstanbul\'da eski ve paslı su tesisatı yenileme, banyo ve mutfak tesisatı, pis su hattı değişimi. Kaliteli malzeme, garantili işçilik: 0539 576 17 72',
        'giris' => 'Eski binalardaki galvaniz borular zamanla paslanır, içi daralır ve sık sık kaçak yapar. Sürekli tamir yerine tesisatı planlı şekilde yenilemek hem güvenli hem uzun vadede ekonomiktir.',
        'bolumler' => [
            ['Ne zaman yenilemek gerekir?', 'Musluklardan paslı ya da bulanık su geliyorsa, su basıncı düştüyse, sık sık farklı noktalarda kaçak oluşuyorsa veya banyo-mutfak tadilatı yapılıyorsa tesisatın yenilenmesi önerilir.'],
            ['Nasıl çalışıyoruz?', 'Önce mevcut hattı inceleyip yenilenecek bölümleri ve malzemeyi birlikte belirliyoruz. Kaliteli PPR veya kompozit borularla temiz su, uygun çap ve eğimle pis su hattı döşenir. İş sonunda basınç testi yapılır ve hat çalışır şekilde teslim edilir.'],
            ['Tadilat sırasında tesisat', 'Banyo ve mutfak yenilemelerinde, fayans döşenmeden önce tesisatı yenilemek en doğru zamandır. Yeni armatür ve cihaz yerlerine göre hat planlanır.'],
        ],
        'maddeler' => ['Komple daire temiz su tesisatı', 'Banyo ve mutfak tesisatı yenileme', 'Pis su ve gider hattı değişimi', 'Kolon ve ana hat değişimi', 'Basınç testi ile teslim'],
        'sss' => [
            ['Tesisat yenileme kaç gün sürer?', 'Kapsama göre değişir. Tek banyo genellikle kısa sürede biter; komple daire tesisatı keşifte netleşen plana göre birkaç gün sürebilir.'],
            ['Hangi boruları kullanıyorsunuz?', 'İhtiyaca göre temiz suda PPR veya kompozit boru, pis suda uygun çaplı PVC boru kullanıyoruz.'],
            ['Fiyatı nasıl öğrenirim?', 'Keşif sonrası yapılacak iş ve malzeme belli olduğunda net fiyat veriyoruz. Onayınız olmadan işe başlanmaz.'],
        ],
    ],
    'batarya-musluk-degisimi' => [
        'ad' => 'Batarya & Musluk Değişimi',
        'kisa_ad' => 'batarya ve musluk değişimi',
        'ikon' => 'i-faucet',
        'kart' => 'Damlatan musluk, arızalı batarya, rezervuar ve klozet takımı değişimi ve montajı. Temiz ve hızlı işçilik.',
        'baslik' => 'Batarya ve Musluk Değişimi İstanbul | Rezervuar Tamiri – Net Onarım',
        'meta' => 'İstanbul\'da damlatan musluk tamiri, batarya değişimi ve montajı, rezervuar iç takımı, klozet ve lavabo montajı. Hızlı servis: 0539 576 17 72',
        'giris' => 'Damlatan bir musluk ayda ciddi miktarda suyu boşa harcar. Arızalı batarya, sürekli su kaçıran rezervuar ya da sallanan klozet gibi küçük işleri aynı gün, temiz bir işçilikle çözüyoruz.',
        'bolumler' => [
            ['Yaptığımız montaj ve değişimler', 'Lavabo, eviye ve banyo bataryası değişimi; ankastre ve termostatik batarya montajı; rezervuar iç takımı ve flatör değişimi; klozet, lavabo ve duş seti montajı; ara musluk ve fleks hortum değişimi.'],
            ['Malzeme', 'İsterseniz kendi aldığınız ürünü takıyoruz, isterseniz ihtiyaca uygun kaliteli ürünü biz getiriyoruz. Montaj sonrası bağlantılar sızdırmazlık için kontrol edilir.'],
        ],
        'maddeler' => ['Damlatan musluk ve batarya tamiri', 'Batarya ve eviye bataryası değişimi', 'Rezervuar iç takımı değişimi', 'Klozet ve lavabo montajı', 'Fleks hortum ve ara musluk değişimi'],
        'sss' => [
            ['Kendi aldığım bataryayı takar mısınız?', 'Evet, kendi aldığınız ürünün montajını yapıyoruz.'],
            ['Rezervuar sürekli su kaçırıyor, ne yapmalıyım?', 'Genellikle iç takımdaki conta veya flatör arızasıdır. İç takım değişimiyle sorun çoğunlukla aynı gün çözülür.'],
        ],
    ],
    'acil-tesisatci' => [
        'ad' => 'Acil Arıza Onarımı',
        'kisa_ad' => 'acil tesisatçı',
        'ikon' => 'i-wrench',
        'kart' => 'Patlak boru, su basması, sayaç ve vana arızaları gibi acil durumlarda 7/24 hızlı müdahale ile hasarı büyümeden durduruyoruz.',
        'baslik' => 'Acil Tesisatçı İstanbul | 7/24 Patlak Boru ve Su Basması – Net Onarım',
        'meta' => 'İstanbul\'da 7/24 acil tesisatçı. Patlak boru, su basması, vana ve sayaç arızalarına gece-gündüz hızlı müdahale. Hemen arayın: 0539 576 17 72',
        'giris' => 'Patlayan bir boru ya da su basması dakikalar içinde büyük hasara yol açabilir. Gece, hafta sonu ya da bayram fark etmeksizin telefonu açıyor, en yakın ustamızı yönlendiriyoruz.',
        'bolumler' => [
            ['Usta gelene kadar ne yapmalısınız?', 'Önce ana su vanasını kapatın (genellikle sayacın yanındadır). Su elektrik prizlerine ya da cihazlara yaklaşıyorsa ilgili sigortayı kapatın. Suyu havlu ve kovayla toplayın, alt kat komşunuzu bilgilendirin ve mümkünse hasarın fotoğrafını çekin.'],
            ['Acil müdahale ettiğimiz durumlar', 'Patlak ve çatlak borular, su basması, kapanmayan vana ve ana vana arızası, sayaç bağlantısında kaçak, kombi ve termosifon bağlantı kaçakları, taşan tuvalet ve gider.'],
        ],
        'maddeler' => ['Patlak boru onarımı', 'Su basması müdahalesi', 'Ana vana ve sayaç bağlantı arızası', 'Kombi ve termosifon bağlantı kaçağı', 'Taşan tuvalet ve gider'],
        'sss' => [
            ['Gece veya bayramda geliyor musunuz?', 'Evet, 7 gün 24 saat, resmi tatil ve bayramlar dahil acil servis veriyoruz.'],
            ['Ana vana nerede olur?', 'Genellikle su sayacının hemen yanında, bazen mutfak lavabosunun altında veya banyo girişindedir. Bulamazsanız telefonda tarif ediyoruz.'],
        ],
    ],
    'petek-temizligi' => [
        'aktif' => false, // panelden açılabilir
        'ad' => 'Petek Temizliği',
        'kisa_ad' => 'petek temizliği',
        'ikon' => 'i-heat',
        'kart' => 'Makineli ve ilaçlı petek temizliği ile kalorifer sisteminizin verimini artırın, doğalgaz faturanızı düşürün.',
        'baslik' => 'Petek Temizliği İstanbul | Makineli Kalorifer Temizliği – Net Onarım',
        'meta' => 'İstanbul\'da makineli ve ilaçlı petek temizliği. Isınmayan petekler, yüksek doğalgaz faturası ve kombi sorunlarına çözüm. Randevu: 0539 576 17 72',
        'giris' => 'Zamanla peteklerin ve kalorifer borularının içinde çamur, pas ve kireç birikir. Petekler üstten ısınıp alttan soğuk kalır, kombi daha çok çalışır ve fatura yükselir. Makineli temizlikle sistemi yıkayarak ısıyı yeniden eşit dağıtıyoruz.',
        'bolumler' => [
            ['Peteklerin temizlenmesi gerektiğini nasıl anlarım?', 'Peteğin alt kısmı soğuk kalıyorsa, bazı odalar diğerlerinden geç ısınıyorsa, kombi sık sık devreye giriyorsa veya tesisattan su sesi geliyorsa birikinti oluşmuş olabilir.'],
            ['Temizlik nasıl yapılıyor?', 'Petek temizleme makinesi kombi ya da kalorifer hattına bağlanır. Sisteme tesisata uygun temizleme solüsyonu verilir ve su ters-düz yönde basınçla dolaştırılarak birikinti dışarı alınır. Ardından sistem temiz suyla durulanır, gerekirse koruyucu eklenir ve basınç ayarı yapılır.'],
            ['Ne zaman yaptırmalı?', 'En uygun zaman ısınma sezonu başlamadan önceki dönemdir. Tesisatın yaşına ve suyun sertliğine bağlı olarak birkaç yılda bir yapılması önerilir.'],
        ],
        'maddeler' => ['Makineli petek ve kalorifer hattı temizliği', 'Kombi bağlantısından sistem yıkama', 'Koruyucu ve kireç önleyici uygulama', 'Hava alma ve basınç ayarı', 'Petek vanası ve purjör değişimi'],
        'sss' => [
            ['Petek temizliği faturayı düşürür mü?', 'Birikinti temizlendiğinde petekler daha hızlı ve eşit ısınır; kombi daha az çalıştığı için tüketim genellikle azalır.'],
            ['Petekleri sökmek gerekir mi?', 'Makineli temizlikte petekler sökülmez. Sistem kombi veya hat bağlantısından yıkanır.'],
            ['Temizlik ne kadar sürer?', 'Ortalama bir dairede birkaç saat sürer; petek sayısına ve kirlilik durumuna göre değişir.'],
        ],
    ],
];
