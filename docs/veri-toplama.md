http://127.0.0.1:8000/admin/source-accounts
2) "Topla" Butonuna Basınca Ne Oluyor?
source-accounts sayfasında Topla butonuna bastığınızda, sistem o kaynağın (örneğin @onurkande111) tweetlerini çekme işlemini başlatır.

Ekranda gördüğünüz "Başarılı: @onurkande111 toplama işine eklendi." uyarısının asıl anlamı şudur:

Sistem tweetleri sayfayı yenilerken anlık olarak çekmez. Çünkü X'e bağlanmak, tweetleri taramak ve veri tabanına kaydetmek uzun sürebilir ve bu işlem sayfanın yüklenmesini kilitlerdi (sayfa donar veya timeout hatası verirdi).
Bunun yerine FetchSourceAccountTweets adında bir Arka Plan Görevi (Background Job/Queue) oluşturulur.
Laravel bu görevi sıraya (Queue) ekler ve işlemi hemen bitirerek size "Başarılı" mesajını gösterir.
Arka planda çalışan bir Queue Worker (kuyruk işleyicisi), sırası geldiğinde bu görevi alır ve siz başka sayfalarda dolaşırken veya paneli kapatsanız bile sunucu tarafında sessizce X'ten tweetleri çeker, işler ve veri tabanına kaydeder.
Kısacası bu buton, tweet toplama robotuna "Hemen arka planda bu hesap için çalışmaya başla" talimatı veriyor. Görev bitince o hesaba ait veriler (skorlar, son kontrol tarihi vs.) veri tabanında güncellenir.