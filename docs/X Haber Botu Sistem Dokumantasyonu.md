# X Haber Botu – Sistem Dokümantasyonu

## 1. Proje Tanımı

Bu projenin amacı, belirlenen X (Twitter) haber kaynaklarını düzenli olarak takip eden, bu kaynaklardaki paylaşımları analiz eden, en değerli içerikleri seçen, yapay zeka ile haber formatına dönüştüren ve belirlenen X hesabında paylaşan bir otomasyon sistemi geliştirmektir.

Sistem tamamen otomatik çalışabilecek şekilde tasarlanacaktır ancak kritik aşamalarda manuel onay sistemi de desteklenecektir.

Bu proje temel olarak:

* Veri toplama
* Veri analizi
* İçerik seçimi
* Yapay zeka ile içerik üretimi
* İçerik yayınlama
* Loglama
* İzleme
* Bildirim
* Hata yönetimi

özelliklerinden oluşacaktır.

---

# 2. Projenin Genel Çalışma Mantığı

## 2.1 Kaynak Hesapların Takibi

Sistem içerisinde yönetici tarafından belirlenen X hesapları düzenli aralıklarla kontrol edilir.

Örneğin:

* Haber hesapları
* Teknoloji sayfaları
* Gündem hesapları
* Kripto hesapları
* Spor hesapları

Laravel Scheduler belirli aralıklarla çalışarak Python servislerini tetikler.

Python servisleri ilgili hesapları kontrol eder:

* Yeni paylaşım var mı?
* Paylaşım silinmiş mi?
* Etkileşim artışı var mı?
* Gönderi trend oluyor mu?

Bu bilgiler veritabanına kaydedilir.

---

## 2.2 Gönderi Analizi

Sistem sadece en yüksek beğeniye sahip gönderiyi seçmeyecektir.

Bunun yerine çok daha gelişmiş bir puanlama sistemi kullanılacaktır.

Örnek kriterler:

* Beğeni sayısı
* Retweet sayısı
* Yorum sayısı
* Görüntülenme artış hızı
* Gönderinin ne kadar hızlı büyüdüğü
* Kaynağın güvenilirliği
* İçeriğin haber kategorisine uygunluğu
* İçeriğin tekrar içerik olup olmadığı
* Spam riski
* Haber değeri

Bu sistem sayesinde daha kaliteli içerik seçimi yapılacaktır.

---

## 2.3 Yapay Zeka İçerik Üretimi

Seçilen gönderi yapay zeka sistemine gönderilir.

Yapay zeka:

* Gönderiyi analiz eder
* Haber formatına çevirir
* Gerekirse başlık üretir
* Kısa açıklama üretir
* Belirlenen haber diline uygun çıktı verir

Amaç:

* Daha profesyonel içerik üretmek
* Aynı tweeti birebir paylaşmamak
* Haber dilini standartlaştırmak
* Kaliteli içerik üretmek

---

## 2.4 Yayınlama Sistemi

Hazırlanan içerik X hesabında paylaşılır.

Yayınlama sistemi:

* Başarılı paylaşımı loglar
* Başarısız paylaşımı loglar
* Hata durumunda retry uygular
* Gerekirse yöneticiye bildirim gönderir

---

## 2.5 Loglama ve İzleme Sistemi

Bu proje için en kritik yapılardan biri loglama sistemidir.

Çünkü:

* Paketler bozulabilir
* X tarafı değişiklik yapabilir
* Rate limit oluşabilir
* Hesap kısıtlanabilir
* Yapay zeka servisleri hata verebilir
* Veri çekme sistemi durabilir

Bu nedenle sistemdeki her işlem kayıt altına alınmalıdır.

Loglanacak örnek veriler:

* Hangi hesap tarandı
* Kaç gönderi bulundu
* Hangi gönderi seçildi
* Yapay zeka çıktısı
* Yayın sonucu
* API hataları
* Paket hataları
* Rate limit durumları
* Retry işlemleri
* Queue hataları

---

# 3. Kullanılan Teknolojiler

## 3.1 Laravel

Projenin ana beyni Laravel olacaktır.

Laravel'in görevleri:

* Yönetim paneli
* Kullanıcı sistemi
* Authentication
* Queue sistemi
* Scheduler sistemi
* Log sistemi
* Veritabanı yönetimi
* Ayar yönetimi
* Bildirim sistemi
* Mail sistemi
* İş akışının yönetimi
* API katmanı
* Dashboard
* İstatistik sistemi

Laravel projenin merkezi olacaktır.

---

## 3.2 Python

Python tarafı yardımcı servis olarak kullanılacaktır.

Python görevleri:

* X veri çekme işlemleri
* Tweet analizi
* Etkileşim hesaplamaları
* AI işlemleri
* İçerik işleme
* Veri filtreleme
* Ranking işlemleri

Python servisleri Laravel tarafından tetiklenecektir.

---

## 3.3 gpt4free

Kullanım amacı:

* AI içerik üretimi
* Tweet yeniden yazımı
* Haber dili oluşturma
* Başlık üretimi
* İçerik düzenleme

Riskler:

* Stabil olmama ihtimali
* Provider değişiklikleri
* Rate limit sorunları
* Servis kesintileri

Bu nedenle AI sistemi değiştirilebilir mimaride tasarlanacaktır.

---

## 3.4 twscrape

Kullanım amacı:

* Tweet çekme
* Kullanıcı analizleri
* Gönderi verileri toplama
* Etkileşim analizi

Riskler:

* X değişiklikleri sonrası bozulma
* Rate limit
* Hesap doğrulama problemleri
* Geçici erişim sorunları

Bu nedenle:

* Retry sistemi
* Monitoring sistemi
* Hata bildirim sistemi
* Paket sağlık kontrol sistemi

kurulacaktır.

---

## 3.5 twitter-api-client

Kullanım amacı:

* Tweet paylaşımı
* Hesap işlemleri
* X etkileşim işlemleri

Riskler:

* API değişiklikleri
* Oturum problemleri
* Hesap güvenlik sorunları

Bu nedenle paylaşım sistemi sürekli takip edilecektir.

---

# 4. Sistem Mimarisi

## 4.1 Genel Mimari

Laravel
↓
Scheduler
↓
Queue Jobs
↓
Python Servisleri
↓
AI İşlemleri
↓
Sonuç Analizi
↓
Paylaşım Sistemi
↓
Loglama ve Bildirim

---

# 5. Veritabanı Yapısı

Temel tablolar:

## monitored_accounts

Takip edilen hesaplar.

## fetched_posts

Çekilen gönderiler.

## post_metrics

Gönderi istatistikleri.

## ranking_results

Puanlama sonuçları.

## ai_generations

AI çıktıları.

## publish_logs

Paylaşım kayıtları.

## system_logs

Sistem logları.

## package_health_logs

Paket sağlık kayıtları.

## alerts

Bildirim kayıtları.

---

# 6. Queue ve Scheduler Yapısı

Laravel Queue kullanılacaktır.

Örnek joblar:

* FetchPostsJob
* AnalyzePostsJob
* CalculateScoresJob
* GenerateAiContentJob
* PublishPostJob
* HealthCheckJob
* SendAlertJob

Scheduler düzenli olarak bu jobları çalıştıracaktır.

---

# 7. Monitoring Sistemi

Sistem sürekli kontrol altında tutulacaktır.

Kontrol edilecek durumlar:

* Paket çalışıyor mu?
* Veri geliyor mu?
* Queue çalışıyor mu?
* AI cevap veriyor mu?
* Publish sistemi çalışıyor mu?
* Rate limit oluştu mu?
* Son paylaşım ne zaman yapıldı?
* Sistem durmuş mu?

---

# 8. Bildirim Sistemi

Hata oluştuğunda sistem yöneticiyi bilgilendirecektir.

Bildirim türleri:

* Mail bildirimi
* Dashboard bildirimi
* Kritik hata bildirimi
* Sistem durdu bildirimi
* Paket çalışmıyor bildirimi
* Publish başarısız bildirimi

Örnek:

* twscrape çalışmıyor
* X erişimi başarısız
* AI servis cevap vermiyor
* Queue durmuş
* Publish başarısız

Bu durumlarda otomatik mail gönderilecektir.

---

# 9. Riskler

## 9.1 X Hesap Riski

Ana hesabın:

* Rate limit yemesi
* Kısıtlanması
* Güvenlik doğrulaması istemesi
* Spam algılanması

riskleri vardır.

Bu nedenle:

* Kontrollü paylaşım
* Spam davranışlarından kaçınma
* Sürekli monitoring
* Manuel kontrol sistemi

önemlidir.

---

## 9.2 Paket Riski

Kullanılan paketler bozulabilir.

Özellikle:

* X güncellemeleri
* HTML değişiklikleri
* API değişiklikleri
* Login sistem değişiklikleri

sistemi etkileyebilir.

Bu nedenle:

* Paket sağlık kontrolü
* Yedek strateji
* Alternatif servis planı
* Hata alarm sistemi

zorunludur.

---

# 10. Güvenlik

Sistem güvenliği için:

* API anahtarları .env dosyasında tutulacak
* Queue güvenliği sağlanacak
* Rate limit uygulanacak
* Hata logları saklanacak
* Yetkisiz erişim engellenecek
* Kritik işlemler loglanacak

---

# 11. Yönetim Paneli

Laravel admin panelinde:

* Kaynak hesap yönetimi
* Gönderi listesi
* AI çıktıları
* Sistem logları
* Paket durumları
* Yayın geçmişi
* Hata kayıtları
* İstatistikler
* Monitoring ekranları

bulunacaktır.

---

# 12. İstatistik Sistemi

Sistem istatistikleri tutulacaktır.

Örnek:

* Günlük çekilen gönderi sayısı
* Günlük paylaşım sayısı
* Başarısız paylaşım oranı
* En başarılı kaynak hesaplar
* AI başarı oranı
* Ortalama etkileşim
* Sistem uptime oranı
* Paket hata oranları

---

# 13. Gelecek Planları

İleride eklenebilecek özellikler:

* Aynı botu İnstagram içinde yapacağız
* Çoklu dil desteği
* Otomatik kategori sistemi
* Gelişmiş AI analizleri
* Trend tespit sistemi
* Görsel üretimi
* Video içerik sistemi
* Telegram entegrasyonu
* Discord entegrasyonu
* Çoklu hesap yönetimi
* İnsan onay sistemi

---

# 14. Mevcut Proje Durumu

Şu ana kadar:

* Laravel altyapısı kuruldu
* Admin tema kuruldu
* Auth sistemi yapıldı
* Layout sistemi hazırlandı
* Kullanılacak paketler test edildi
* gpt4free test edildi
* twscrape test edildi
* twitter-api-client test edildi

Şu anda kalan temel aşamalar:

* Laravel ile Python entegrasyonu
* Queue sistemi
* Monitoring sistemi
* Log sistemi
* Mail sistemi
* Paket health check sistemi
* AI workflow sistemi
* Yayın workflow sistemi
* Dashboard geliştirmeleri

---

# 15. Sonuç

Bu proje basit bir bot sistemi değil, tam kapsamlı bir otomasyon ve içerik yönetim sistemidir.

Sistemin en kritik noktaları:

* Stabilite
* Monitoring
* Loglama
* Hata yönetimi
* Paket sağlık kontrolü
* Veri analizi
* AI kalite kontrolü

olacaktır.

Proje büyüdükçe sistem daha modüler hale getirilecek ve servis mimarisi geliştirilecektir.

---

# Sayfalar

Projede yer alan yönetim paneli sayfaları ve işlevleri aşağıda detaylandırılmıştır:

## 1. Kaynak Hesaplar (`/admin/source-accounts`)
Takip edilen ve düzenli olarak tweet toplanacak kaynak X (Twitter) hesaplarının yönetildiği sayfadır.
* **CRUD İşlemleri**: Yeni kaynak hesap ekleme (kullanıcı adı, görünen ad, öncelik puanı, güven puanı, kontrol aralığı vb.), düzenleme ve silme.
* **Toplu İşlemler**: Seçilen birden fazla kaynak hesabın tek seferde silinmesini (toplu silme) sağlar.
* **Manuel Tweet Çekimi (Fetch)**: Her hesabın yanında bulunan "Topla" (Fetch) butonu yardımıyla, arka plan kuyruğuna (`FetchSourceAccountTweets` job'ı) anlık veri toplama görevi gönderilir.
* **Detaylı Özellikler**:
  * `is_active`: Hesabın otomatik taramaya dahil edilip edilmeyeceğini belirtir (Aktif/Pasif).
  * `priority_score` & `trust_score`: Tweetlerin puanlanmasında kullanılan hesap önceliği ve güven derecesidir (0-100).
  * `check_interval_minutes`: Scheduler'ın bu hesabı kaç dakikada bir kontrol edeceğini tanımlar.

## 2. Kaynak Kategorileri (`/admin/source-categories`)
Takip edilen kaynak hesapların ve bunlardan derlenecek hikayelerin konu başlıklarına göre gruplandırılmasını sağlayan yönetim ekranıdır (Örn: Siyaset, Spor, Teknoloji, Kripto vb.).
* **Kategori Yönetimi**: Yeni kategoriler tanımlama, slug (URL dostu yapı) oluşturma, açıklama ekleme, düzenleme ve silme.
* **İlişkili Hesap Takibi**: Kategori listesinde, o kategoriye atanmış aktif kaynak hesap sayıları (`sources_label`) doğrudan görüntülenebilir.
* **Soft Delete**: Silinen kategoriler veritabanından kalıcı olarak yok edilmez, `SoftDeletes` mekanizması ile silindi olarak işaretlenir.

## 3. Ham Tweetler (`/admin/raw-tweets`)
Python servisi (`twscrape`) aracılığıyla X üzerinden çekilen ancak henüz işleme tabi tutulup yapay zekaya gönderilmemiş tüm ham tweetlerin listelendiği loglama ve izleme sayfasıdır.
* **İçerik Görüntüleme**: Gelen tweetlerin ham metinlerini, benzersiz tweet ID'lerini ve gönderilme tarihlerini gösterir.
* **Etkileşim İzleme**: Tweetlerin anlık beğeni (like), retweet (RT), yorum ve görüntülenme sayıları takip edilir.
* **Arama ve Durum**: Tweet içeriğine veya ID'sine göre arama yapılabilir. Tweetin sistem tarafından işlenip bir hikaye kümesine (Story Cluster) atanıp atanmadığı (`is_processed` -> İşlendi / Bekliyor) bu ekrandan gözlemlenir.

## 4. Story Cluster (`/admin/story-clusters`)
Farklı kaynak hesaplardan toplanan benzer veya aynı konudaki tweetlerin algoritma tarafından tespit edilerek gruplandığı "Hikaye Kümeleri" listesidir. Burası yapay zeka ile haber yazma sürecinden bir önceki ortak havuzdur.
* **Kümeleme Analizi**: Benzer tweetlerin oluşturduğu kümeleri (başlık, benzersiz küme hash'i, ana kaynak hesap) gösterir.
* **Tweet Sayısı**: İlgili haber odağında kaç adet tweetin birleştiğini (`items_count`) belirtir.
* **Story Score**: Kümedeki tweetlerin etkileşimleri, öncelikleri ve güven puanları hesaplanarak oluşturulmuş olan haber değerini (skorunu) gösterir.
* **Durum Kontrolü**: Kümenin mevcut durumuna göre filtreleme yapılabilir:
  * `open` (Açık): Yeni toplanmış, değerlendirilen hikayeler.
  * `selected` (Seçildi): Yapay zeka ile haberleştirilmek üzere seçilenler.
  * `published` (Yayınlandı): Başarıyla X üzerinde paylaşılanlar.
  * `ignored` (Yoksayıldı): Haber değeri düşük görüldüğü için elenenler.

---

# Sistem Çalıştırma ve Kurulum Adımları

Sistemin tam ve otomatik bir şekilde çalışabilmesi için hem yönetim panelinde yapılması gereken işlemler hem de arka planda çalıştırılması gereken terminal komutları mevcuttur.

## 1. Yönetim Panelinde Yapılması Gerekenler

Sistemin veri toplamaya ve hikaye oluşturmaya başlayabilmesi için öncelikle panel üzerinden şu adımların tamamlanması gerekir:

1. **Kategori Tanımlama (`/admin/source-categories`)**:
   * Sistemdeki tüm X kaynak hesapları bir kategoriye bağlı olmak zorundadır.
   * Panelde "Yeni Kategori" butonuna basarak en az bir aktif kategori (örn: *Gündem, Teknoloji, Kripto*) tanımlanmalıdır.

2. **Kaynak Hesap Ekleme (`/admin/source-accounts`)**:
   * Takip edilmek istenen kaynak X hesapları (örn: `@bpthaber`) sisteme eklenmelidir.
   * Hesap eklerken:
     * **Kategori**: İlgili kategori seçilmelidir.
     * **Kullanıcı Adı**: X (Twitter) kullanıcı adı (başına `@` koymadan) yazılmalıdır.
     * **Durum**: "Aktif" olarak işaretlenmelidir (`is_active = true`).
     * **Kontrol Aralığı**: Hesabın kaç dakikada bir kontrol edileceği belirlenmelidir (örn: 15 dakika).

3. **Manuel İlk Tetikleme (İsteğe Bağlı)**:
   * Hesapları ekledikten sonra otomatik zamanlayıcıyı beklemek istemiyorsanız, ilgili hesabın satırındaki "Topla" (Fetch) butonuna tıklayarak ilk tweet toplama görevini elle başlatabilirsiniz.

---

## 2. Terminalde Çalıştırılması Gereken Komutlar

Sistemin web arayüzünü sunması, arka plan işlerini yürütmesi ve twscrape (Python) aracılığıyla veri toplaması için aşağıdaki komutların çalışıyor olması gerekir.

### A. Laravel Web Sunucusu (Geliştirme Ortamı)
Panel arayüzüne tarayıcıdan erişebilmek için Laravel sunucusu başlatılmalıdır:
```bash
php artisan serve
```

### B. Kuyruk İşleyicisi (Queue Worker)
Tweet çekme, AI ile metin üretimi ve tweet paylaşımı gibi tüm arka plan görevleri Laravel Queue (Kuyruk) mimarisiyle yürütülür. `.env` dosyasında `QUEUE_CONNECTION=database` olarak ayarlandığı için bu kuyruğu işleyecek worker'ın arka planda sürekli çalışması şarttır:
```bash
php artisan queue:work
```

### C. Laravel Zamanlayıcı (Scheduler)
Kontrol zamanı gelen kaynak hesapları otomatik olarak taramak için zamanlanmış görevlerin tetiklenmesi gerekir.
* **Geliştirme (Local) Ortamında**: Scheduler'ı sürekli çalışır tutmak için terminalde şu komut açık bırakılmalıdır:
  ```bash
  php artisan schedule:work
  ```
* **Canlı (Production) Ortamında**: Sunucudaki sistem crontab'ine her dakika çalışacak şekilde şu satır eklenmelidir:
  ```bash
  * * * * * cd /proje-dizini && php artisan schedule:run >> /dev/null 2>&1
  ```

### D. Twscrape Python Entegrasyonu (fetch_user_tweets.py)
Laravel'in Python tabanlı `twscrape` kütüphanesini kullanarak X üzerinden tweet çekme işlemi `app/Services/NewsCollection/TwscrapeClient.php` servisi tarafından yönetilmektedir.

Süreç şu şekilde işler:
1. `FetchDueSourceAccountsCommand` zamanlayıcı ile tetiklendiğinde kontrol zamanı gelmiş kaynak hesaplarını tespit eder.
2. Bu hesapların her biri için `FetchSourceAccountTweets` job'u (kuyruk görevi) oluşturulur.
3. Queue worker bu görevi işlerken `TwscrapeClient::fetchUserTweets` metodunu çağırır.
4. Bu metod, `Symfony\Component\Process\Process` bileşenini kullanarak arka planda senkron bir terminal işlemi başlatır ve `services/twscrape/fetch_user_tweets.py` scriptini çalıştırır.
5. Python kodu, kendi dizinindeki `accounts.db` SQLite veritabanındaki oturum bilgilerini kullanarak hedef X hesabından JSON formatında tweetleri döndürür.
6. Laravel bu JSON'ı ayrıştırarak (`TweetIngestionService` üzerinden) veritabanına kaydeder.

Oluşabilecek herhangi bir hata durumunda detaylar anlık olarak `storage/logs/laravel.log` dosyasına, veritabanındaki `system_logs` tablosuna ve yönetici paneli bildirimlerine (`alerts` tablosu) kaydedilmektedir.

### D. Python `twscrape` (Veri Çekme Servisi) Hazırlığı
Sistemin X üzerinden veri çekebilmesi için Python sanal ortamında `twscrape` API'sinin X hesaplarıyla yetkilendirilmesi gerekmektedir.
1. Terminalden `services/twscrape` dizinine gidin ve Python sanal ortamını aktif edin:
   * **Windows için**:
     ```powershell
     cd services/twscrape
     .venv\Scripts\activate
     ```
   * **macOS / Linux için**:
     ```bash
     cd services/twscrape
     source .venv/bin/activate
     ```
2. `twscrape` veritabanına (`accounts.db`) en az bir adet çalışan X hesabı ekleyin:
   ```bash
   twscrape add_accounts accounts.txt
   ```
   *(Not: `accounts.txt` dosyası `kullanici_adi:sifre:eposta:eposta_sifresi` formatında hazırlanmış olmalıdır.)*
3. Eklenen hesapların sisteme giriş yapabilmesi için login komutunu çalıştırın:
   ```bash
   twscrape login_accounts
   ```
   *(Tüm hesapların giriş durumunun başarılı (Active) olduğundan emin olunmalıdır.)*

---

# Mevcut Tweet Toplama Çalışma Mantığı

Bu bölüm, projedeki mevcut Laravel + Queue + twscrape akışının nasıl çalıştığını ve aynı tweetlerin tekrar kaydedilmesinin nasıl engellendiğini açıklar.

## 1. Kaynak hesabın kuyruğa düşmesi

Kaynak hesaplar `source_accounts` tablosunda tutulur. Bir kaynak hesabın otomatik taramaya dahil olması için:

* `is_active = true` olmalıdır.
* `last_checked_at` boş olmalıdır ya da son kontrol zamanının üzerinden `check_interval_minutes` kadar süre geçmiş olmalıdır.

Laravel scheduler `routes/console.php` içinde `news:fetch-due-sources` komutunu her dakika çalıştırır. Bu komut zamanı gelen aktif kaynakları bulur ve her kaynak için `FetchSourceAccountTweets` queue job'ını kuyruğa ekler.

Örnek: Bir hesabın `check_interval_minutes` değeri 15 ise scheduler her dakika kontrol eder, ama bu hesap ancak son başarılı kontrolden 15 dakika sonra tekrar kuyruğa alınır.

## 2. İlk ekleme ve yeniden aktifleştirme limiti

Yeni bir kaynak hesap eklendiğinde sistem bu hesabı hemen normal tweet limitiyle taramaz. İlk başarılı toplama işleminde sadece 5 tweet ister.

Aynı kural, daha önce pasife alınmış bir kaynak hesabın tekrar aktif edilmesi için de geçerlidir:

* Kaynak yeni eklendiğinde `limited_initial_fetch_pending = true` olur.
* Kaynak `is_active = false` durumundan tekrar `is_active = true` durumuna getirilirse `limited_initial_fetch_pending` tekrar `true` yapılır.
* Queue job çalıştığında bu değer `true` ise `twscrape` sadece `TWSCRAPE_INITIAL_ACTIVATION_LIMIT` kadar tweet çeker. Varsayılan değer 5'tir.
* Toplama işlemi başarılı tamamlanınca `limited_initial_fetch_pending = false` yapılır.
* Sonraki periyodik kontrollerde normal limit kullanılır. Normal limit `TWSCRAPE_FETCH_LIMIT` ile belirlenir, varsayılan değer 20'dir.

Bu davranışın amacı yeni veya yeniden aktif edilmiş kaynaklarda bir anda çok fazla tweet çekmemek ve X tarafında bot/rate-limit riskini azaltmaktır.

## 3. twscrape şu anda hangi tweetleri kontrol ediyor?

Mevcut Python scripti `services/twscrape/fetch_user_tweets.py` içinde şu çağrıyı yapar:

```python
tweets = await gather(api.user_tweets(user.id, limit=args.limit))
```

Yani sistem şu anda ilgili kullanıcının son tweetlerini ister. Kaç tweet isteneceği Laravel tarafından `--limit` parametresiyle gönderilir:

* İlk ekleme veya yeniden aktifleştirme sonrası ilk başarılı çalışmada: 5 tweet.
* Normal periyodik çalışmalarda: varsayılan olarak 20 tweet.

Mevcut yapıda `last_seen_tweet_id` Python tarafına gönderilip "sadece bu ID'den sonrasını getir" şeklinde kullanılmıyor. Bu alan şu anda son görülen tweet ID'sini kayıt altında tutmak için güncelleniyor.

## 4. Aynı tweetler tekrar nasıl kaydedilmiyor?

Sistem her çalışmada son N tweeti tekrar görebilir. Aynı tweetlerin veritabanına tekrar yazılmasını engelleyen ana mekanizma `raw_tweets.tweet_id` alanıdır:

* `raw_tweets.tweet_id` veritabanında unique olarak tanımlıdır.
* `TweetIngestionService` her tweet için önce `RawTweet::where('tweet_id', $tweetId)->exists()` kontrolü yapar.
* Tweet daha önce kaydedilmişse yeni kayıt açılmaz ve `skipped` sayısına eklenir.
* Tweet daha önce yoksa `raw_tweets` tablosuna kaydedilir, normalize edilir, duplicate/story cluster kontrollerinden geçirilir ve işlenmiş olarak işaretlenir.

Bu nedenle sistem "tüm eski tweetleri tekrar tarıyor" şeklinde çalışmaz. Her fetch işleminde sadece twscrape'e verilen limit kadar son tweet alınır. Ancak alınan bu son tweetlerin içinde daha önce kaydedilmiş olanlar varsa, ingest aşamasında atlanır.

## 5. Benzer haber/duplicate kontrolü nasıl çalışıyor?

Tweet ID tekrar kontrolünden ayrı olarak, haber benzerliği için normalize edilmiş metin ve URL bazlı bir kontrol de vardır:

* Yeni tweetin metni normalize edilir ve `tweet_normalized_texts` tablosuna kaydedilir.
* Sistem son 50 normalize tweet kaydını aday olarak alır.
* Ortak URL varsa duplicate kabul eder.
* Ortak URL yoksa metin benzerliği `similar_text` ile hesaplanır.
* Benzerlik oranı `NEWS_SIMILARITY_THRESHOLD` değerinin üzerindeyse duplicate/benzer haber olarak işaretlenir.
* Bu sonuç `duplicate_checks` tablosunda saklanır ve tweet uygun story cluster'a bağlanır.

Özetle:

* Aynı tweet tekrar kaydedilmez: `tweet_id` kontrolü ile engellenir.
* Benzer haberler gruplanır: URL ve metin benzerliği ile story cluster'a bağlanır.
* Fetch aşamasında sadece son N tweet istenir; geçmişteki tüm tweetler baştan sona taranmaz.
