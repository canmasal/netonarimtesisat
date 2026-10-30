<?php
// Mahalle sayfaları için varsayılan veriler.
// - MAHALLE_VARSAYILAN: ilçenin mahallelerini panelde boşsa ilk kurulumda doldurur.
//   (Sonra Panel > Hizmet Bölgeleri'nden eklenip çıkarılabilir.)
// - MAHALLE_METINLERI: mahalleye özel giriş metni. Panelden yazılan metin bunun yerine geçer.

const MAHALLE_VARSAYILAN = [
    'Bahçelievler' => ['Şirinevler', 'Yenibosna Merkez', 'Kocasinan Merkez', 'Siyavuşpaşa', 'Soğanlı', 'Çobançeşme',
        'Bahçelievler', 'Fevzi Çakmak', 'Hürriyet', 'Cumhuriyet', 'Zafer'],
    'Bakırköy' => ['Ataköy 1. Kısım', 'Ataköy 2-5-6. Kısım', 'Ataköy 3-4-11. Kısım', 'Ataköy 7-8-9-10. Kısım', 'Basınköy',
        'Cevizlik', 'Kartaltepe', 'Osmaniye', 'Sakızağacı', 'Şenlikköy', 'Yenimahalle', 'Yeşilköy', 'Yeşilyurt',
        'Zeytinlik', 'Zuhuratbaba'],
    'Güngören' => ['Merkez', 'Haznedar', 'Gençosman', 'Güneştepe', 'Tozkoparan', 'Akıncılar', 'Güven', 'Mareşal Çakmak',
        'Sanayi', 'Abdurrahman Nafiz Gürman', 'Mehmet Nezih Özmen'],
    'Bağcılar' => ['Güneşli', 'Mahmutbey', 'Kirazlı', 'Yıldıztepe', 'Demirkapı', 'Bağlar', 'Barbaros', 'Çınar', 'Evren',
        'Fatih', 'Fevzi Çakmak', 'Göztepe', 'Hürriyet', 'İnönü', 'Kazım Karabekir', 'Kemalpaşa', 'Merkez', 'Sancaktepe',
        'Yenigün', 'Yenimahalle', '15 Temmuz', '100. Yıl'],
    'Zeytinburnu' => ['Merkezefendi', 'Kazlıçeşme', 'Seyitnizam', 'Beştelsiz', 'Çırpıcı', 'Gökalp', 'Maltepe', 'Nuripaşa',
        'Sümer', 'Telsiz', 'Veliefendi', 'Yenidoğan', 'Yeşiltepe'],
    'Küçükçekmece' => ['Halkalı Merkez', 'Atakent', 'Cennet', 'Sefaköy', 'Kanarya', 'Yarımburgaz', 'İnönü',
        'Fatih', 'Cumhuriyet', 'Gültepe', 'Kartaltepe', 'Kemalpaşa', 'Mehmet Akif', 'Söğütlüçeşme', 'Sultanmurat',
        'Yeşilova', 'Beşyol', 'Atatürk', 'Fevzi Çakmak', 'İstasyon', 'Yeni Mahalle'],
];

const MAHALLE_METINLERI = [
    'Bahçelievler' => [
        'Şirinevler' => "Şirinevler, Bahçelievler'in en kalabalık ve en hareketli mahallelerinden biri. D-100 (E-5) çevresindeki yoğun apartman dokusunda, özellikle 80'li ve 90'lı yıllarda yapılmış binalarda eskiyen borular, kolon hattı kaçakları ve ortak gider tıkanıklıkları sık karşılaştığımız arızalar arasında.\n\nŞirinevler'deki dükkân, kafe ve iş yerlerinde de mutfak gideri tıkanıklığı ve tuvalet arızalarına iş saatini aksatmadan müdahale ediyoruz. Merkezimiz aynı ilçede olduğu için Şirinevler'e çoğu zaman kısa sürede ulaşıyoruz.",
        'Yenibosna Merkez' => "Yenibosna Merkez'de yeni yapılan siteler ile eski apartmanlar bir arada. Yeni sitelerde yerden ısıtma ve kombi hattı kaçakları, eski binalarda ise çürüyen su boruları ve banyo zemin altı kaçakları en çok çağrıldığımız işler.\n\nYenibosna'daki iş yerleri ve atölyeler için de gider açma, rezervuar ve batarya değişimi gibi işleri hızlıca hallediyoruz.",
        'Kocasinan Merkez' => "Kocasinan Merkez'de çoğunlukla orta yaşlı apartmanlar bulunuyor. Bu binalarda eskiyen su boruları, kombi bağlantı kaçakları ve banyo-mutfak altından alt kata su sızması sık görülüyor.\n\nKocasinan'da kaçağın yerini termal kamera ve dinleme cihazıyla bulup fayansı gereksiz yere kırmadan onarıyoruz.",
        'Siyavuşpaşa' => "Siyavuşpaşa'da apartmanların önemli bir kısmı uzun yıllardır kullanılıyor; eski galvaniz borularda paslanma, düşük su basıncı ve sık tekrarlayan kaçaklar bu yüzden yaygın.\n\nSiyavuşpaşa'da tesisat yenileme, kolon hattı değişimi ve kırmadan su kaçağı tespiti için ücretsiz ön bilgi veriyor, işe başlamadan net fiyat söylüyoruz.",
        'Soğanlı' => "Soğanlı'da bina yoğunluğu yüksek ve bodrum katlı apartmanlar çok. Bodrum ve zemin katlarda logar taşması, kanalizasyon tıkanıklığı ve nem sorunları en sık karşılaştığımız konular.\n\nSoğanlı'da robotla gider açma ve kameralı gider görüntüleme ile tıkanıklığın nedenini bulup tekrarlamasını önlüyoruz.",
        'Çobançeşme' => "Çobançeşme'de konutlarla birlikte iş yerleri ve plazalar da var. Konutlarda su kaçağı ve tesisat yenileme, iş yerlerinde ise lavabo-tuvalet tıkanıklığı ve batarya arızaları en çok talep gelen işler.\n\nÇobançeşme'de mesai saatlerine uygun randevu veriyor, acil durumlarda 7/24 geliyoruz.",
        'Bahçelievler' => "Bahçelievler Mahallesi, ilçeye adını veren ve eski bahçeli evlerin yerini zamanla apartmanların aldığı köklü bir yerleşim. Eski yapılardan kalma tesisatlarda boru çürümesi, yeni binalarda ise kombi ve yerden ısıtma kaçakları sık görülüyor.\n\nBahçelievler Mahallesi'ne merkezimize yakınlığı sayesinde hızlı ulaşıyor, aynı gün çözüm sunuyoruz.",
        'Fevzi Çakmak' => "Merkezimiz Fevzi Çakmak Mahallesi'nde. Bu yüzden Fevzi Çakmak'ta acil tesisat çağrılarına en hızlı ulaştığımız bölge burası.\n\nFevzi Çakmak'ta patlak boru, su basması, tıkanıklık ve su kaçağı gibi arızalarda komşunuz gibi yanınızdayız; gece ya da hafta sonu fark etmeksizin telefonu açıyoruz.",
        'Hürriyet' => "Hürriyet Mahallesi'nde ağırlıklı olarak çok katlı apartmanlar bulunuyor. Üst katlarda su basıncı düşüklüğü, hidrofor kaynaklı sorunlar ve banyo altından sızan kaçaklar sık karşılaştığımız arızalar.\n\nHürriyet'te kaçağı cihazla tespit edip yalnızca gereken noktaya müdahale ediyoruz.",
        'Cumhuriyet' => "Cumhuriyet Mahallesi'nde eski ve yeni binalar iç içe. Eski binalarda tesisat yenileme ve kolon hattı kaçakları, yeni binalarda ise armatür, batarya ve kombi bağlantı arızaları en çok çağrıldığımız işler.\n\nCumhuriyet Mahallesi'nde işe başlamadan fiyatı net söylüyor, onayınız olmadan işlem yapmıyoruz.",
        'Zafer' => "Zafer Mahallesi'nde aileler için en can sıkıcı arızalar genellikle mutfak ve banyo gideri tıkanıklığı ile damlatan batarya ve rezervuarlar. Tıkanıklıkları robotla kırmadan açıyor, batarya ve rezervuar değişimini aynı gün yapıyoruz.\n\nZafer Mahallesi'nde acil su kaçağı ve patlak boru durumlarında 7/24 hizmet veriyoruz.",
    ],
];
