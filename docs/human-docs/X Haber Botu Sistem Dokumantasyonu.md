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
* Günlük ve saatlik limit kontrolü yapar
* Gece modunda paylaşım yapmaz
* Warmup mode ile yeni hesapları korur
* Random gecikme ile doğal davranış sağlar
* Çoklu hesap desteği sunar

**Kütüphane Ayrımı:**
- twscrape: Sadece veri toplama (tweet çekme, profil bilgisi çekme, istatistik)
- twitter-api-client: Sadece yayınlama (tweet paylaşma, like, retweet, reply, follow)

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

* FetchSourceAccountTweets
* FetchSourceAccountTweets
* PoolSelectionJob
* AIQueueJob
* AIGenerationJob
* PublishPostJob
* PublishSchedulerJob
* SyncPublishAccountJob
* DownloadTweetMediaJob
* MediaCleanupJob
* HealthCheckJob
* SendAlertJob

Scheduler düzenli olarak bu jobları çalıştıracaktır.

**Scheduler tanımları (routes/console.php):**

```php
Schedule::command('news:fetch-due-sources')->everyMinute()->withoutOverlapping();
Schedule::job(new PoolSelectionJob)->everyMinute()->withoutOverlapping();
Schedule::job(new AIQueueJob)->everyMinute()->withoutOverlapping();
Schedule::job(new MediaCleanupJob)->hourly()->withoutOverlapping();
Schedule::job(new PublishSchedulerJob)->everyMinute()->withoutOverlapping();
```

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
* Tweet toplama sistemi tamamlandı
* Tweet medya yönetimi tamamlandı
* Tweet seçim havuzu sistemi tamamlandı
* AI içerik üretim sistemi tamamlandı
* Publish yönetimi sistemi tamamlandı

Şu anda kalan temel aşamalar:

* Monitoring sistemi
* Log sistemi geliştirmeleri
* Paket health check sistemi
* Dashboard geliştirmeleri
* Gelecek planları (Instagram, çoklu dil, trend tespit vb.)

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

### 3.1 Tweet Medya Bilgileri

Python scripti tweetlerle birlikte medya bilgilerini de cikariyor. Tweet cekilirken medya dosyalari fiziksel olarak indirilmez — sadece URL metadata'si toplanir ve Laravel tarafinda veritabanina kaydedilir. Boylece gereksiz depolama kullanimi onlenir.

Scriptin cikardigi alanlar:

- `photo_urls`: Tweete eklenmis fotoğraf linkleri
- `video_urls`: Tweete eklenmis video linkleri (en yuksek bitrate'li variant secilir)
- `animated_gif_urls`: Tweete eklenmis animasyonlu GIF linkleri (en yuksek bitrate'li variant secilir)
- `media_urls`: Yukaridaki uclarin tamaminin birlestirilmis hali

Laravel tarafinda `TweetMediaService::extractMediaFromPayload()` bu bilgileri isleme alir ve sunlari kaydeder:

- `media_urls` (JSON dizisi)
- `media_count` (toplam medya sayisi)
- `media_type`: Tek tur varsa `photo` / `video` / `animated_gif`, birden fazla tur varsa `mixed`

Ilgili migration: `2026_06_04_000001_add_media_fields_to_raw_tweets_table.php`

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

---

## 6. Twscrape Yönetim Paneli (`/admin/twscrape/*`)

Sistemin Twscrape altyapısını ve X hesaplarını (scraper) yönetmek için geliştirilmiş özel paneldir. Mevcut `accounts.db` SQLite veritabanı ile Laravel ana veritabanı entegre çalışır.

* **Hesaplar (`/admin/twscrape/accounts`)**: `accounts.db` içerisindeki hesaplar listelenir. Hesapların ağırlıkları (weight), aktif/pasif durumları ve kullanım istatistikleri yönetilir. Sistem **"Weighted Round Robin"** mantığı ile en yüksek ağırlığa sahip ve en uzun süredir kullanılmayan hesabı seçerek API limit (rate limit) riskini dağıtır.
* **Kullanım İstatistikleri (`/admin/twscrape/stats`)**: Hangi hesabın toplam kaç defa kullanıldığı, kaç kez hata aldığı ve yük dağılım oranları görsel olarak listelenir.
* **Komutlar (`/admin/twscrape/commands`)**: Sunucu terminaline bağlanmaya gerek kalmadan Twscrape CLI komutları (`accounts`, `stats`, `login_accounts`, `relogin` vb.) doğrudan panel üzerinden çalıştırılır ve terminal çıktısı ekranda gösterilir.
* **Sağlık Durumu (`/admin/twscrape/health`)**: `accounts.db` veritabanının erişilebilirliği, aktif hesap sayısı, hatalı hesaplar ve en son başarılı/başarısız scraping zamanları izlenir.
* **İşlem Logları (`/admin/twscrape/logs`)**: Twscrape üzerinden atılan tüm login denemeleri, komut çalıştırmaları ve fetch işlemleri, işlem süresi (ms) ve başarılı/başarısız durumlarıyla birlikte loglanarak geriye dönük takibi sağlanır.

---

# Tweet Seçim Havuzu Sistemi

Tweet toplama aşamasından sonra çalışan, toplanan tweetler arasından en değerli olanları otomatik olarak seçen, seçilenlerin medyalarını indiren, geçmişi kayıt altına alan ve tekrar seçim yapmayan profesyonel bir havuz seçim sistemidir.

Bu aşamada AI API entegrasyonu yapılmaz. Sadece tweetleri toplamak, puanlamak, sıralamak, seçmek ve seçilenlerin medyasını indirmek amaçlanır. AI tarafına gönderme işlemi sonraki aşamada yapılır.

---

## 1. Sistem Çalışma Akışı

Sistem tamamen otomatik çalışır. İş akışı şu şekildedir:

1. Laravel Scheduler her dakika `PoolSelectionJob`'ı tetikler.
2. Job, `PoolSetting.next_run_at` alanını kontrol eder. Zamanı gelmemişse işlem yapmadan çıkar.
3. Zamanı geldiyse `NewsCollection\PoolSelectionService::executeSafe()` çağrılır.
4. Rastgele bir tweet toplama penceresi üretilir (`tweet_window_min` ile `tweet_window_max` arasında).
5. Son X dakika içinde çekilmiş ve `selected_for_pool = false` olan tweetler aday olarak alınır.
6. Her aday tweet `PoolScoringService` ile puanlanır.
7. Tweetler final puana göre azalan sırada sıralanır.
8. Rastgele bir seçilecek tweet sayısı üretilir (`tweet_count_min` ile `tweet_count_max` arasında).
9. En yüksek puanlı N tweet seçilir.
10. Seçilen tweetler `raw_tweets.selected_for_pool = true` ve `selected_at = now()` olarak işaretlenir.
11. Bir `PoolBatch` kaydı ve tüm adaylar için `PoolBatchItem` kayıtları oluşturulur.
12. Seçilen tweetlerin medya indirme job'ları dispatch edilir (eğer `media_download_enabled = true`).
13. İşlem `system_logs` tablosuna `pool_selection` modülü olarak loglanır.
14. Rastgele bir bekleme süresi üretilir (`selection_interval_min` ile `selection_interval_max` arasında).
15. `PoolSetting.next_run_at` güncellenir ve scheduler beklemeye geçer.

---

## 2. Puanlama Algoritması

Her aday tweet için iki temel kriter kullanılarak bir final puan üretilir:

### 2.1 Öncelik Puanı (Priority Score)

Kaynak hesabın `priority_score` alanıdır (0-100). Yüksek öncelikli kaynaklardan gelen tweetler öne çıkar.

### 2.2 Etkileşim Puanı (Engagement Score)

Tweetin ham etkileşim değeri şu formülle hesaplanır:

```
Raw Engagement = likes + (retweets × 2) + (replies × 1.5) + (quotes × 1.2) + (views × 0.001)
```

Daha sonra mevcut aday setindeki en yüksek ham etkileşim değerine göre normalize edilir:

```
Engagement Score = (Raw Engagement / Max Raw Engagement) × 100
```

Hiç etkileşim yoksa tüm tweetlerin etkileşim puanı 0 olur.

### 2.3 Final Puan

```
Final Score = (Priority Score × 0.40) + (Engagement Score × 0.60)
```

Ağırlıklar:
- **%40** Kaynak Öncelik Puanı
- **%60** Etkileşim Puanı

Amaç: Yüksek öncelikli kaynaklardan gelen ve yüksek etkileşim alan tweetlerin öne çıkmasıdır.

---

## 3. Tekrar Seçilmeme Kuralı

Bir tweet bir kez havuza seçildikten sonra bir daha asla havuza aday olamaz.

Bu kural `raw_tweets.selected_for_pool` boolean alanı ile sağlanır:

* `selected_for_pool = false` olan tweetler aday olarak değerlendirilir.
* `selected_for_pool = true` olan tweetler aday havuzundan kesinlikle dışlanır.

Aday olup ancak seçilmeyen tweetler sonraki döngülerde tekrar aday olabilir. Sadece **seçilen** tweetler dışlanır.

---

## 4. Havuz Ayarları

Admin panelde dinamik olarak yönetilen ayarlar tablosudur (`pool_settings`). Sistemde tek bir kayıt bulunur (singleton pattern).

| Alan | Varsayılan | Açıklama |
|------|------------|----------|
| `tweet_window_min` | 10 | Tweet toplama penceresi minimum (dakika) |
| `tweet_window_max` | 40 | Tweet toplama penceresi maksimum (dakika) |
| `selection_interval_min` | 10 | Seçim çalışma aralığı minimum (dakika) |
| `selection_interval_max` | 15 | Seçim çalışma aralığı maksimum (dakika) |
| `tweet_count_min` | 3 | Seçilecek minimum tweet sayısı |
| `tweet_count_max` | 5 | Seçilecek maksimum tweet sayısı |
| `is_active` | true | Havuz seçimi aktif mi |
| `media_download_enabled` | true | Seçilen tweetlerin medyasını indir |
| `published_media_retention_hours` | 24 | Yayınlanmış medya saklama süresi (saat) |
| `unpublished_media_retention_hours` | 48 | Yayınlanmamış medya saklama süresi (saat) |
| `next_run_at` | null | Bir sonraki çalışma zamanı (otomatik hesaplanır) |

Her seçim döngüsünde:
- Rastgele tweet penceresi: `tweet_window_min` ile `tweet_window_max` arası
- Rastgele seçilecek tweet sayısı: `tweet_count_min` ile `tweet_count_max` arası
- Rastgele bekleme süresi: `selection_interval_min` ile `selection_interval_max` arası

---

## 5. Veritabanı Yapısı

### 5.1 pool_settings

Havuz ayarlarının tutulduğu tek kayıtlık tablo.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| tweet_window_min | smallint unsigned | Minimum tweet aralığı (dakika) |
| tweet_window_max | smallint unsigned | Maksimum tweet aralığı (dakika) |
| selection_interval_min | smallint unsigned | Minimum seçim aralığı (dakika) |
| selection_interval_max | smallint unsigned | Maksimum seçim aralığı (dakika) |
| tweet_count_min | tinyint unsigned | Minimum seçilecek tweet sayısı |
| tweet_count_max | tinyint unsigned | Maksimum seçilecek tweet sayısı |
| is_active | boolean | Havuz seçimi aktif mi |
| media_download_enabled | boolean | Seçilen tweetlerin medyasını indir |
| published_media_retention_hours | smallint unsigned | Yayınlanmış medya saklama süresi (saat) |
| unpublished_media_retention_hours | smallint unsigned | Yayınlanmamış medya saklama süresi (saat) |
| next_run_at | timestamp nullable | Bir sonraki çalışma zamanı |
| created_at, updated_at | timestamps | Zaman damgaları |

İlgili migration: `2026_06_04_000002_add_media_settings_to_pool_settings_table.php`

### 5.2 pool_batches

Her seçim döngüsünün kaydedildiği tablo. Batch numarası formatı: `B-{Ymd}-{seq}` (örn: `B-20260601-001`).

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| batch_no | varchar(32) unique | Batch numarası |
| tweet_window_minutes | smallint unsigned | Kullanılan tweet aralığı |
| candidate_count | int unsigned | Toplam aday tweet sayısı |
| selected_count | int unsigned | Seçilen tweet sayısı |
| wait_duration_minutes | smallint unsigned | Bir sonraki çalışmaya bekleme süresi |
| next_run_at | timestamp nullable | Bir sonraki çalışma zamanı |
| started_at | timestamp nullable | Başlangıç zamanı |
| completed_at | timestamp nullable | Tamamlanma zamanı |
| status | enum(running, completed, failed) | Batch durumu |
| error_message | text nullable | Hata mesajı |
| created_at, updated_at | timestamps | Zaman damgaları |

### 5.3 pool_batch_items

Her seçim döngüsünde değerlendirilen tüm aday tweetlerin puanları ve seçim durumları.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| pool_batch_id | bigint FK | İlişkili batch |
| raw_tweet_id | bigint FK | İlişkili tweet |
| priority_score | decimal(8,2) | Kaynak öncelik puanı (0-100) |
| engagement_score | decimal(8,2) | Etkileşim puanı (0-100) |
| final_score | decimal(8,2) | Hesaplanmış final puan |
| is_selected | boolean | Bu tweet seçildi mi |
| rank | int unsigned | Sıralama pozisyonu |
| created_at, updated_at | timestamps | Zaman damgaları |

### 5.4 raw_tweets (güncelleme)

Tweet medya yönetimi ve havuz seçim sistemi için eklenen kolonlar:

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| media_urls | json nullable | Tweet medya URL listesi (photo, video, animated_gif) |
| media_count | tinyint unsigned | Toplam medya dosyası sayısı |
| media_type | varchar(20) nullable | Medya türü: photo / video / animated_gif / mixed |
| media_downloaded_at | timestamp nullable | Medyaların indirilme zamanı |
| media_paths | json nullable | Storage'daki lokal dosya yolları |
| selected_for_pool | boolean default false | Bir kez havuza seçildi mi |
| selected_at | timestamp nullable | Seçilme tarihi |
| selected_for_ai | boolean default false | AI aşamasına seçildi mi |

İlgili migration: `2026_06_04_000001_add_media_fields_to_raw_tweets_table.php`

---

## 6. Job ve Scheduler

### 6.1 PoolSelectionJob

Laravel Queue_job'ıdır. Her dakika scheduler tarafından tetiklenir.

**Akış:**
1. `PoolSetting::singleton()` ile ayarları alır.
2. `is_active = false` ise işlem yapmaz.
3. `next_run_at` gelecekte bir zamansa işlem yapmaz.
4. `PoolSelectionService::executeSafe()` çağırır.
5. Başarısızlık durumunda `SystemLog` ve `Alert` kaydı oluşturur.

**Özellikler:**
- `tries = 2` (maksimum 2 deneme)
- `withoutOverlapping()` ile aynı anda birden fazla örnek çalışması engellenir.

### 6.2 Scheduler Tanımı

`routes/console.php` dosyasında tanımlıdır:

```php
Schedule::job(new PoolSelectionJob)->everyMinute()->withoutOverlapping();
Schedule::job(new MediaCleanupJob)->hourly()->withoutOverlapping();
```

---

### 6.3 Medya İndirme Akışı

Seçim tamamlandıktan sonra, eğer `media_download_enabled = true` ise seçilen her tweet için `DownloadTweetMediaJob` dispatch edilir:

1. `DownloadTweetMediaJob` Queue worker tarafından alınır.
2. `TweetMediaService::downloadForTweet()` çağrılır.
3. `raw_tweets.media_urls` içindeki URL'ler HTTP ile indirilir.
4. Dosyalar `storage/app/media/tweets/{tweet_id}/` altına kaydedilir.
5. `raw_tweets.media_paths` JSON dizisi olarak güncellenir.
6. `raw_tweets.media_downloaded_at` zaman damgası kaydedilir.
7. SystemLog'a `media_downloaded` olayı yazılır.

**Hata toleransı:** Bir medya dosyasının indirilmesi başarısız olursa AI workflow durmaz. Sadece hata loglanır. Tweet metni AI'ya gönderilmeye devam eder. Publish aşamasında dosya yoksa sadece metin paylaşılır.

---

## 7. Servis Katmanı

İş kuralları Controller'da değil, Service katmanında yer alır.

### 7.1 PoolSelectionService (`app/Services/NewsCollection/PoolSelectionService.php`)

Ana seçim orchestrator'ı. Tüm iş mantığını barındırır:

- `execute()`: Tam seçim akışını yürütür (adayları al, puanla, sırala, seç, işaretle, batch oluştur, medya indirme job'larını dispatch, logla, sonraki çalışma zamanını hesapla).
- Secim sonrasi: Eger `media_download_enabled = true` ise her secilen tweet icin `DownloadTweetMediaJob::dispatch($tweetId)` cagirir.
- `executeSafe()`: `execute()`'i try-catch ile sarar. Başarısızlık durumunda failed batch kaydı oluşturur ve sonraki çalışma zamanını günceller.
- Batch numarası üretimi: `B-{Ymd}-{seq}` formatında, günlük artan sıra numarası ile.
- `scheduleNextRun()`: Rastgele bekleme süresi hesaplayıp `next_run_at` günceller.
- Tüm veritabanı yazımları `DB::transaction()` içinde yapılır.

### 7.2 PoolScoringService (`app/Services/NewsCollection/PoolScoringService.php`)

Puanlama hesaplamalarını yapar:

- `scoreCollection()`: Aday tweet koleksiyonunu alır, her tweet için priority_score, engagement_score ve final_score hesaplar.
- Öncelik puanı: Kaynak hesabın `priority_score` alanı.
- Etkileşim puanı: Ham etkileşim değerini koleksiyondaki maksimum değere göre 0-100 arası normalize eder.
- Final puan: `(priority × 0.40) + (engagement × 0.60)` formülüyle hesaplanır.

### 7.3 PoolSettingService (`app/Services/Admin/PoolSettingService.php`)

Admin paneldeki havuz ayarları güncelleme işini yürütür:

- `update()`: Ayarları transaksiyon içinde günceller.
- Normalizasyon: Minimum değerlerin maksimumlardan büyük olmamasını sağlar.

### 7.4 TweetMediaService (`app/Services/NewsCollection/TweetMediaService.php`)

Tweet medya yönetimi servisidir:

- `extractMediaFromPayload()`: Python payload'indan `photo_urls`, `video_urls`, `animated_gif_urls` bilgilerini cikartir, `media_urls`, `media_count`, `media_type` dondurur.
- `downloadForTweet()`: Tweet'in `media_urls` URL'lerini HTTP ile indirir, `storage/app/media/tweets/{tweet_id}/` altina kaydeder, `media_paths` ve `media_downloaded_at` gunceller.
- `deleteMedia()`: Tweet'in medya dosyalarini fiziksel olarak siler, `media_paths` ve `media_downloaded_at` kayitlarini temizler.
- `cleanupExpired()`: Retention kurallarina gore eskimiş medya dosyalarini temizler.

---

## 8. Admin Panel Sayfaları

### 8.1 Havuz Ayarları (`/admin/pool-settings`)

Tek sayfalık dinamik ayar formudur. Yeni ekleme veya silme yoktur; sadece mevcut tek kayıt güncellenir.

**İçerik:**
- Tweet Toplama Aralığı (minimum / maksimum dakika)
- Seçim Çalışma Aralığı (minimum / maksimum dakika)
- Seçilecek Tweet Sayısı (minimum / maksimum)
- Havuz Seçimi Aktif/Pasif toggle
- Medya İndirme Aktif/Pasif toggle
- Yayınlanmış Medya Saklama Süresi (saat)
- Yayınlanmamış Medya Saklama Süresi (saat)
- Bir Sonraki Çalışma Zamanı (salt okunur bilgi)
- Puanlama Algoritması açıklama kartı

**CRUD Katmanı:**
- Controller: `PoolSettingController` (edit, update)
- Request: `PoolSetting\UpdateRequest`
- Service: `Admin\PoolSettingService`
- Blade: `pool-settings/edit.blade.php`

### 8.2 Tweet Havuzu (`/admin/pool-selection`)

En son havuz seçim döngüsünün sonuçlarını gösterir.

**Tablo Sütunları:**
- Tweet ID
- Kaynak Hesap
- Tweet İçeriği
- Öncelik Puanı
- Etkileşim Puanı
- Final Puan
- Sıra
- Durum (Seçildi / Seçilmedi)

**Görsel Ayrım:**
- Seçilen tweetler: Yeşil arka plan, `t-active` badge
- Baraj çizgisi: Seçilenler ile seçilmeyenler arasında görsel ayırıcı
- Seçilmeyen tweetler: Kırmızı tonlu arka plan, `t-unavail` badge

**Filtreler:**
- Tüm tweetler / Seçilenler / Seçilmeyenler
- Arama (tweet içeriği veya ID)
- Sıralama (final puan, öncelik puanı, etkileşim puanı, sıra)

**CRUD Katmanı:**
- Controller: `PoolSelectionController` (index)
- Request: `PoolSelection\IndexRequest`
- Query: `PoolSelectionQuery`
- Blade: `pool-selection/index.blade.php`

### 8.3 Havuz Geçmişi (`/admin/pool-history`)

Tüm seçim döngülerinin listesini ve detaylarını gösterir.

**Liste Sütunları:**
- Batch No
- Çalışma Tarihi
- Kullanılan Aralık (dakika)
- Aday Sayısı
- Seçilen Sayısı
- Bekleme Süresi (dakika)
- Sonraki Çalışma Zamanı
- Durum (Tamamlanmış / Başarısız)
- Detay butonu

**Batch Detay Sayfası (`/admin/pool-history/{id}`):**

Özet kartı: Batch no, çalışma tarihi, tweet aralığı, aday sayısı, seçilen sayısı, bekleme süresi, sonraki çalışma, durum.

İki ayrı tablo:
- **Seçilenler:** Yeşil tonlu, tüm puan detaylarıyla
- **Seçilmeyenler:** Kırmızı tonlu, tüm puan detaylarıyla

**Filtreler:**
- Tüm durumlar / Tamamlanmış / Başarısız
- Arama (batch no)
- Sıralama (tarih, aday sayısı, seçilen sayısı, bekleme süresi)

**CRUD Katmanı:**
- Controller: `PoolHistoryController` (index, show)
- Request: `PoolHistory\IndexRequest`
- Query: `PoolHistoryQuery`
- Blade: `pool-history/index.blade.php`, `pool-history/show.blade.php`

---

## 9. Loglama

Her seçim döngüsü `system_logs` tablosuna `pool_selection` modülü olarak loglanır.

**Loglanan bilgiler:**
- Aday tweet sayısı
- Seçilen tweet sayısı
- Kullanılan tweet aralığı (dakika)
- Bekleme süresi (dakika)
- Bir sonraki çalışma zamanı
- Batch numarası
- Hata durumu (başarısızlık olursa)

Ayrıca başarısızlık durumlarında `alerts` tablosuna `pool_selection_failed` tipinde bildirim kaydı oluşturulur.

### 9.1 Medya Loglama

Tweet medya işlemleri `system_logs` tablosuna `media_management` modülü olarak loglanır.

**Olaylar:**
- `media_downloaded`: Tüm medyalar başarıyla indirildi
- `media_download_failed`: Bazı medyalar indirilemedi (hata devam eder)
- `media_deleted`: Tweet medyaları temizlendi
- `media_exists`: Medya zaten indirilmiş, tekrar indirilmedi
- `cleanup_completed`: Toplu temizlik tamamlandı
- `cleanup_failed`: Temizlik sırasında hata oluştu

Ayrıca başarısızlık durumlarında `alerts` tablosuna `pool_selection_failed` tipinde bildirim kaydı oluşturulur.

---

## 10. Dosya Yapısı

Oluşturulan ve güncellenen dosyalar:

### Yeni Dosyalar

```
database/migrations/
  2026_06_01_150001_create_pool_settings_table.php
  2026_06_01_150002_create_pool_batches_table.php
  2026_06_01_150003_create_pool_batch_items_table.php
  2026_06_01_150004_add_pool_flags_to_raw_tweets_table.php
  2026_06_04_000001_add_media_fields_to_raw_tweets_table.php
  2026_06_04_000002_add_media_settings_to_pool_settings_table.php

services/twscrape/
  fetch_user_tweets.py (guncellendi: photo_urls, video_urls, animated_gif_urls)

app/Models/
  PoolSetting.php
  PoolBatch.php
  PoolBatchItem.php

app/Services/NewsCollection/
  PoolScoringService.php
  PoolSelectionService.php
  TweetMediaService.php (yeni)

app/Services/Admin/
  PoolSettingService.php

app/Jobs/
  PoolSelectionJob.php
  DownloadTweetMediaJob.php (yeni)
  MediaCleanupJob.php (yeni)

app/Http/Controllers/Admin/
  PoolSettingController.php
  PoolSelectionController.php
  PoolHistoryController.php

app/Http/Requests/Admin/
  PoolSetting/UpdateRequest.php
  PoolSelection/IndexRequest.php
  PoolHistory/IndexRequest.php

app/Queries/Admin/
  PoolSelectionQuery.php
  PoolHistoryQuery.php

resources/views/admin/
  pool-settings/edit.blade.php
  pool-selection/index.blade.php
  pool-history/index.blade.php
  pool-history/show.blade.php
```

### Güncellenen Dosyalar

```
app/Models/RawTweet.php              (fillable ve casts güncellendi: media alanları)
app/Models/PoolSetting.php           (fillable ve casts güncellendi: media ayarları)
routes/admin.php                     (pool route'ları eklendi)
routes/console.php                    (PoolSelectionJob + MediaCleanupJob scheduler)
app/Services/NewsCollection/
  TweetIngestionService.php           (medya metadata kaydetme eklendi)
resources/views/admin/layouts/partials/sidebar.blade.php  (Havuz menüsü eklendi)
```

---

## 11. CRUD Mimari Uyumu

Tüm geliştirme mevcut Laravel CRUD mimari dokümantasyonuna uygun olarak yapılmıştır:

| Katman | Standart | Uygulama |
|--------|----------|----------|
| Controller | İnce, sadece request alır ve service/query çağırır | ✅ PoolSettingController, PoolSelectionController, PoolHistoryController |
| FormRequest | Validasyon ve prepareForValidation burada | ✅ UpdateRequest, IndexRequest'ler |
| Query | Arama, filtre, sıralama, pagination burada | ✅ PoolSelectionQuery, PoolHistoryQuery |
| Service | Create/update/delete iş mantığı burada | ✅ PoolSettingService, PoolSelectionService, PoolScoringService |
| Blade | Component tabanlı, _form partial kullanımı | ✅ x-admin.* componentleri kullanıldı |
| Route | admin middleware grubu, resource ve özel route'lar | ✅ pool-settings, pool-selection, pool-history |
| Job | Queue job, ShouldQueue trait, failed hook | ✅ PoolSelectionJob |

---

## 12. Sidebar Menü

Yönetim paneli sidebar'ına "Havuz Yönetimi" bölümü eklenmiştir:

```
Havuz Yönetimi
├── Havuz Ayarları       /admin/pool-settings
├── Tweet Havuzu        /admin/pool-selection
└── Havuz Geçmişi       /admin/pool-history
```

---

# AI Workflow Modülü (Havuz → AI İçerik Üretimi)

Havuz seçiminden sonra seçilen tweetlerin AI ile haberleştirilmesini sağlayan tam kapsamlı üretim sistemidir. Sistem iki provider (GPT4Free ve OpenCode) destekler, kategori bazlı prompt yönetimi kullanır ve manuel onay (review) akışı içerir.

---

## 1. Genel Akış

```
Pool Selection (havuz seçimi tamamlanır)
    ↓
AI Queue Job (completed batch'leri kuyruğa alır)
    ↓
AI Queue (pending → processing)
    ↓
AI Generation Job (batch içindeki tweetleri tek tek işler)
    ↓ Her tweet için ayrı:
    ↓   1. Kategoriye göre aktif prompt seçilir
    ↓   2. Placeholder ({tweet_content}) doldurulur
    ↓   3. AI provider çalıştırılır
    ↓   4. AiGeneration kaydı oluşturulur (1 üretim = 1 tweet)
    ↓
Review (admin onaylar / reddeder)
    ↓
Published (onaylanan içerik yayına hazır)
```

Her adımda `ai_generation_logs` tablosuna detaylı log kaydı yapılır.

**Önemli:** Üretim tweet bazlıdır. Bir batch'teki her tweet için ayrı bir `ai_generations` kaydı oluşur. Bir üretim kaydına birden fazla tweet bağlanmaz.

---

## 2. Providerlar

Sistemde iki AI provider bulunur. Admin panelinden aktif provider tek tıkla değiştirilebilir.

### 2.1 GPT4Free

Ücretsiz, Python `g4f` kütüphanesi üzerinden çalışır. API key gerektirmez.

**Çalışma mantığı:**
1. Laravel `Gpt4freeClient` servisi `Symfony\Component\Process` ile Python scripti başlatır.
2. Script (`services/gpt4free/ai_generate.py`) model listesini `havuz` dosyasından okur.
3. Belirtilen model varsa onu dener, yoksa havuzdaki modelleri sırayla dener.
4. Başarılı yanıt JSON formatında stdout'a yazılır.
5. Laravel JSON'ı parse eder ve sonucu döndürür.

**Hata yönetimi:**
- İlk model çalışmazsa otomatik olarak sıradaki modele geçer.
- Tüm modeller başarısız olursa hata loglanır ve exception fırlatılır.
- Windows ortamında `SYSTEMROOT`/`USERPROFILE` ortam değişkenleri otomatik aktarılır.

**Model havuzu:**
`services/gpt4free/havuz` dosyasında tanımlıdır. Her satırda bir provider adı bulunur. Dosya düzenlenerek model listesi değiştirilebilir, kod değişikliği gerektirmez.

### 2.2 OpenCode

OpenAI-compatible HTTP API üzerinden çalışır. API key, Base URL ve Model gerektirir.

**Varsayılan ayarlar:**
- Base URL: `https://opencode.ai/zen/go/v1`
- Model: `deepseek-v4-flash`

**Çalışma mantığı:**
1. `OpenCodeProvider` servisi `GuzzleHttp` ile HTTP POST isteği gönderir.
2. Endpoint: `{base_url}/chat/completions`
3. Header: `Authorization: Bearer {api_key}`
4. Body: OpenAI-compatible JSON formatı.
5. Response parse edilerek içerik döndürülür.

**Admin panelden değiştirilebilir alanlar:**
- OpenCode API Key
- OpenCode Base URL
- OpenCode Model

---

## 3. Prompt Sistemi

### 3.1 Kategori Bazlı Prompt Yönetimi

Promptlar `source_categories` tablosuna bağlıdır. Her kategorinin kendi aktif promptu olabilir.

**Kural:** Her kategoride sadece bir adet aktif prompt bulunabilir. Yeni prompt aktif edildiğinde aynı kategorideki diğer aktif prompt otomatik olarak pasif yapılır.

**Kullanım akışı:**
1. Tweet'in geldiği kaynak hesabın `category_id` değeri alınır.
2. O kategoriye ait aktif prompt aranır.
3. Kategoriye özel prompt yoksa global aktif prompt (kategori boş olan) kullanılır.
4. O da yoksa sistem varsayılan prompt'u kullanır.

**Üretim akışı (tweet bazlı):**
1. Her tweet için kendi kategorisine göre prompt seçilir.
2. Prompt `{tweet_content}` placeholder'ı tweet metni ile değiştirilir.
3. AI provider'a istek gönderilir.
4. Sonuç ilgili tweet için ayrı bir `ai_generations` kaydı olarak saklanır.

### 3.2 Değişkenler (Placeholder)

Prompt metinleri değişken destekler. AI isteği gönderilmeden önce sistem bu değişkenleri gerçek verilerle değiştirir.

**Zorunlu değişken:**

| Değişken | Açıklama |
|----------|----------|
| `{tweet_content}` | Tek tweet metni (tweet bazlı üretimde tek bir tweet'in içeriği) |

**Opsiyonel değişkenler:**

| Değişken | Açıklama |
|----------|----------|
| `{tweet_count}` | Sabit 1 (tweet bazlı üretimde her zaman 1) |
| `{sources}` | Tweet'in geldiği kaynak hesap kullanıcı adı |
| `{total_score}` | Tweet'in final puanı |
| `{first_tweet}` | Aynı tweet metni (tweet bazlı üretimde tek tweet olduğu için ilk ve tek) |

**Validasyon:** Prompt kaydedilirken sistem metin içindeki tüm `{...}` ifadelerini tarar. Geçersiz bir placeholder (örn: `{tweet_contents}`) tespit edilirse kayıt reddedilir ve kullanıcı uyarı mesajı görür.

### 3.3 Prompt Şablonu Örneği

```
Aşağıdaki tweetleri profesyonel haber dilinde yeniden yaz:

{tweet_content}

Haber başlığı ve gövde metni oluştur. Toplam 250-400 kelime arasında olsun.

Çıktı formatı:
BAŞLIK: [Haber Başlığı]
İÇERİK: [Haber Metni]
```

---

## 4. AI Ayarları (`/admin/ai-settings`)

AI üretim sisteminin tüm konfigürasyonu bu sayfadan yönetilir. Tabloda tek kayıt bulunur (singleton pattern).

### 4.1 Ayar Alanları

| Alan | Açıklama |
|------|----------|
| AI Provider | Aktif provider: GPT4Free veya OpenCode |
| Aktif Prompt | Varsayılan olarak kullanılacak prompt şablonu |
| Tekrar Deneme Sayısı | Başarısız olunca kaç kez deneneceği (varsayılan: 3) |
| Timeout | AI isteği için maksimum bekleme süresi saniye (varsayılan: 300) |
| Eş Zamanlı Job Sayısı | Aynı anda çalışabilecek AI job sayısı (varsayılan: 1) |
| AI Üretimini Aktif Et | Tüm AI üretimini açıp kapatır |
| Otomatik Onay | AI üretimlerini otomatik onaylar (varsayılan: kapalı) |

### 4.2 OpenCode'a Özel Alanlar

| Alan | Açıklama |
|------|----------|
| OpenCode API Key | OpenCode servisi için API anahtarı |
| OpenCode Base URL | API endpoint adresi |
| OpenCode Model | Kullanılacak model adı (dinamik, admin istediği zaman değiştirebilir) |

### 4.3 Bağlantı Testi

Ayarlar sayfasının alt kısmında "Bağlantı Testi" bölümü bulunur.

**Kullanım:**
1. Test promptu yazılır (varsayılan: "Merhaba, bu bir bağlantı testidir.")
2. "Bağlantıyı Test Et" butonuna basılır.
3. Aktif provider'a test isteği gönderilir.
4. Sonuç sayfa içinde gösterilir (başarılı/hata, provider, model, süre).

Bu özellik hem GPT4Free hem OpenCode için çalışır.

---

## 5. Veritabanı Yapısı

### 5.1 ai_settings

AI ayarlarının tutulduğu tek kayıtlık tablo.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| provider | varchar | Aktif provider: gpt4free / opencode |
| model_name | varchar nullable | GPT4Free için opsiyonel model adı |
| retry_count | tinyint unsigned | Tekrar deneme sayısı |
| timeout | int unsigned | Timeout süresi (saniye) |
| concurrent_jobs | tinyint unsigned | Eş zamanlı job sayısı |
| active_prompt_id | bigint FK nullable | Aktif prompt şablonu |
| opencode_api_key | text nullable | OpenCode API anahtarı |
| opencode_base_url | varchar nullable | OpenCode Base URL |
| opencode_model | varchar nullable | OpenCode model adı |
| is_active | boolean | AI üretimi aktif mi |
| auto_approve | boolean default false | AI üretimlerini otomatik onaylar |
| created_at, updated_at | timestamps | Zaman damgaları |

### 5.2 prompts

AI prompt şablonlarının tutulduğu tablo. SoftDeletes kullanılır.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| name | varchar | Prompt adı |
| source_category_id | bigint FK nullable | Bağlı olduğu kaynak kategori |
| prompt_text | text | Prompt şablon metni |
| version | smallint unsigned | Versiyon numarası |
| is_active | boolean | Bu prompt aktif mi |
| created_at, updated_at | timestamps | Zaman damgaları |
| deleted_at | timestamp nullable | Soft delete |

### 5.3 ai_queues

Havuz seçiminden sonra AI kuyruğuna alınan batch'lerin kaydı.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| pool_batch_id | bigint FK | İlişkili havuz batch'i |
| batch_no | varchar unique | Batch numarası |
| tweet_count | int unsigned | Seçilen tweet sayısı |
| story_score | decimal(8,2) | Ortalama final puan |
| status | varchar | Durum: pending / processing / completed / failed |
| error_message | text nullable | Hata mesajı |
| started_at | timestamp nullable | İşlem başlangıcı |
| completed_at | timestamp nullable | İşlem bitişi |
| created_at, updated_at | timestamps | Zaman damgaları |

### 5.4 ai_generations

AI tarafından üretilen içeriklerin kaydı. Her kayıt bir tweet'e karşılık gelir (tweet bazlı üretim).

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| ai_queue_id | bigint FK | İlişkili AI kuyruk kaydı |
| raw_tweet_id | bigint FK nullable | İlişkili tweet (yeni - tweet bazlı üretim) |
| source_account_id | bigint FK nullable | Kaynak hesap (denormalize, hızlı erişim için) |
| category_id | bigint FK nullable | Kategori (hangi kategori promptu kullanıldı) |
| provider | varchar nullable | Kullanılan provider: gpt4free / opencode |
| prompt_id | bigint FK nullable | Kullanılan prompt şablonu |
| model | varchar nullable | Kullanılan model adı |
| prompt_version | smallint unsigned | Prompt versiyonu |
| title | varchar nullable | Üretilen haber başlığı |
| input | json nullable | Girdi verisi (tweet ID, metin, kullanıcı adı) |
| prompt | text nullable | Ham prompt şablonu |
| full_prompt | text nullable | AI'ye gönderilen tam prompt |
| ai_response | longText nullable | AI'dan gelen ham yanıt |
| generated_news | text nullable | Parse edilmiş haber metni |
| token_usage | json nullable | Token bilgileri |
| duration | int unsigned | İşlem süresi (milisaniye) |
| status | varchar | Durum: draft / approved / rejected / published |
| approved_at | timestamp nullable | Onay tarihi (admin veya sistem onayı) |
| error | text nullable | Hata mesajı |
| generated_at | timestamp nullable | Üretim zamanı |
| created_at, updated_at | timestamps | Zaman damgaları |

**Indexler:** `[raw_tweet_id, status]`, `[category_id, status]`, `[provider, status]`

**İlişkiler:**
- belongsTo RawTweet
- belongsTo SourceAccount
- belongsTo SourceCategory
- belongsTo AiQueue
- belongsTo Prompt
- hasMany AiGenerationLog

### 5.5 ai_generation_logs

AI workflow boyunca tüm işlemlerin loglandığı tablo.

| Kolon | Tür | Açıklama |
|-------|-----|----------|
| id | bigint | Birincik anahtar |
| ai_queue_id | bigint FK nullable | İlişkili AI kuyruk kaydı |
| ai_generation_id | bigint FK nullable | İlişkili AI üretimi |
| level | varchar | Log seviyesi: info / warning / error / critical |
| message | text | Log mesajı |
| context_json | json nullable | Ek bağlam verisi |
| created_at, updated_at | timestamps | Zaman damgaları |

---

## 6. Job Mimarisi

### 6.1 AIQueueJob

Her dakika scheduler tarafından tetiklenir. Completed durumundaki PoolBatch'leri AI kuyruğuna alır.

**Akış:**
1. `status = completed` ve `aiQueue` ilişkisi olmayan PoolBatch'leri bulur.
2. Her batch için `AiQueueService::createFromPoolBatch()` çağırır.
3. Seçili tweet'leri sayar, ortalama final puanı hesaplar.
4. `ai_queues` tablosuna kayıt oluşturur.
5. `raw_tweets.selected_for_ai = true` ve `ai_sent_at = now()` olarak işaretler.
6. `AIGenerationJob` dispatch eder.

### 6.2 AIGenerationJob

AI provider ile tweet bazlı içerik üretimini yürütür.

**Akış:**
1. AiQueue kaydını `processing` durumuna alır.
2. Batch'teki seçili tweetleri toplar (`PoolBatchItem` → `rawTweet`).
3. Her tweet için döngü başlatır:
   a. Tweetin kaynak hesabının kategorisine göre aktif prompt'u bulur.
   b. Prompt değişkenlerini (`{tweet_content}` vb.) tweet verisiyle değiştirir.
   c. AI provider'a istek gönderir.
   d. Sonucu `ai_generations` tablosuna tweet bazlı kaydeder (1 tweet = 1 AiGeneration).
   e. Hata olursa loglar ve diğer tweetlere devam eder.
4. Tüm tweetler işlendikten sonra AiQueue'yu `completed` durumuna alır.
5. Tüm tweetler başarısız olursa `failed` durumuna alır ve hata loglar.

**Önemli:** Bir tweet'in hatası diğer tweetlerin işlenmesini engellemez. Her tweet bağımsız olarak üretilir.

---

## 7. Admin Panel Sayfaları

### 7.1 AI Ayarları (`/admin/ai-settings`)

Tek sayfalık konfigürasyon formu ve bağlantı testi.

**İçerik:**
- Provider seçimi (GPT4Free / OpenCode)
- OpenCode'a özel alanlar (API Key, Base URL, Model)
- Genel ayarlar (retry, timeout, concurrent jobs)
- Aktif prompt seçimi
- Otomatik Onay toggle'ı (AI üretimlerini otomatik onaylama)
- Bağlantı testi bölümü

### 7.2 Prompt Yönetimi (`/admin/prompts`)

Prompt şablonlarının CRUD yönetimi.

**İşlemler:**
- Yeni prompt oluşturma
- Prompt düzenleme
- Soft delete ile silme
- Toplu silme
- Aktif/Pasif yapma (aynı kategorideki diğerleri otomatik pasif yapılır)

**Form alanları:**
- Prompt Adı
- Kategori (source_categories dropdown)
- Versiyon
- Prompt Metni (değişken destekli)
- Aktif/Pasif

### 7.3 AI Kuyruğu (`/admin/ai-queue`)

AI kuyruğuna alınan batch'lerin listesi.

**Tablo sütunları:**
- ID
- Tarih
- Batch No
- Tweet Sayısı
- Hikaye Puanı
- Durum (Bekliyor / İşleniyor / Tamamlandı / Başarısız)

**Detay sayfası (`/admin/ai-queue/{id}`):**
- Kuyruk bilgileri
- Bu kuyruktan üretilen tüm AI üretimlerinin listesi (tweet bazlı, her satırda 1 tweet, 1 çıktı, durum badge'i ve detay linki)
- İşlem logları

### 7.4 AI Üretimleri (`/admin/ai-generations`)

AI tarafından üretilen içeriklerin listesi ve detayları. Her satır bir tweet'e karşılık gelir.

**Liste sütunları:**
- ID
- Tweet (içerik ve tweet ID)
- Kaynak Hesap (kullanıcı adı)
- Kategori (badge)
- Provider
- Model
- Tarih
- Prompt Versiyonu
- Süre (ms)
- Durum (Taslak / Onaylandı / Reddedildi / Yayınlandı)

**Arama:** Tweet içeriği, model veya üretilen içerik içinde arama yapılabilir.

**Detay sayfası (`/admin/ai-generations/{id}`):**
- **Kaynak Tweet:** Kullanıcı adı, tweet metni, etkileşim sayıları (like, RT, reply, view), tarih, kategori
- **Üretim Bilgileri:** Provider, Model, Prompt Versiyonu, Süre, Durum, Onay Tarihi, Üretim Tarihi, Batch linki
- **Token Kullanımı:** Prompt tokens, completion tokens, total tokens
- **Review butonları** (Onayla / Reddet / Yayınla)
- **Prompt:** Ham prompt şablonu
- **Render Edilmiş Prompt:** AI'ye gönderilen tam prompt
- **AI Çıktısı:** Üretilen haber metni
- **Hata detayı** (varsa)
- **İşlem logları

---

## 8. Review (Onay) Akışı

AI üretimi tamamlandığında `draft` durumunda oluşur. Onay akışı `ai_settings.auto_approve` ayarına göre değişir.

### 8.1 auto_approve = false (Varsayılan)

Admin panelinden manuel onay süreci başlar.

```
draft → approved  (Onayla butonu, approved_at = now())
draft → rejected  (Reddet butonu)
approved → rejected  (Reddet butonu, approved_at = null yapılır)
approved → publish_queue  (Yayınla butonu, PublishSchedulerService aracılığıyla)
publish_failed → publish_queue  (Yeniden Yayınla butonu)
```

### 8.2 auto_approve = true

AI üretimi tamamlandığında otomatik olarak `approved` durumuna geçer.

```
draft → approved  (Sistem tarafından otomatik, approved_at = now())
```

Admin hala:
- **Reddet** yapabilir (`approved` → `rejected`, `approved_at` null yapılır)
- **Yayınla** yapabilir (`approved` → `publish_queue`)

Not:
- Auto approve ile `approved` olan kayıtlar `published`'a geçmez, maksimum durum `approved`'dır.
- Admin yetkisi korunur: auto approve yalnızca ilk onayı verir.
- `auto_publish_enabled = true` ise approved → publish_queue otomatik olarak planlanır.

### 8.3 Yeni Durumlar

| Durum | Açıklama |
|-------|----------|
| `draft` | AI tarafından üretildi, onay bekliyor |
| `approved` | Admin veya auto_approve ile onaylandı |
| `rejected` | Admin tarafından reddedildi |
| `publishing` | Yayınlanıyor (PublishPostJob çalışıyor) |
| `published` | Başarıyla paylaşıldı |
| `publish_failed` | Yayın başarısız oldu |

Her geçiş `ai_generation_logs` tablosuna loglanır.

---

## 9. Publish Sistemi

AI tarafından üretilen haberlerin kuyruk tabanlı, rate limit korumalı, çoklu hesap destekli ve loglanabilir şekilde X'te paylaşılmasını sağlayan sistemdir.

### 9.1 Genel Akış

```
AI Generation (approved)
    ↓
PublishSchedulerService
    ↓ Kurallar kontrolü (limit, gece modu, gap, warmup)
publish_queue (pending, scheduled_at ile)
    ↓ Her dakika PublishSchedulerJob
    ↓
PublishPostJob
    ↓ PublishService (Python: publish_tweet.py)
    ↓
published
```

### 9.2 Publish Scheduler Kuralları

| Kural | Açıklama |
|-------|----------|
| Günlük Limit | `daily_post_limit` kadar paylaşım yapılabilir |
| Saatlik Limit | `hourly_post_limit` kadar paylaşım yapılabilir |
| Random Delay | `min_delay_minutes` ile `max_delay_minutes` arası rastgele gecikme |
| Minimum Gap | İki paylaşım arası en az `min_gap_minutes` süre |
| Gece Modu | `publish_start_hour` ile `publish_end_hour` arasında paylaşım yapılır |
| Warmup Mode | Yeni hesaplar için daha düşük günlük limit |

### 9.3 Publish Hesapları

`publish_accounts` tablosu ile yönetilir. Her hesap için:
- auth_token ve ct0 bilgileri
- Warmup için account_created_at
- Son senkronizasyon zamanı
- Aktif/pasif durumu

### 9.4 Publish Kuyruğu

`publish_queue` tablosu ile yönetilir. Durumlar:
- `pending`: Zaman bekleniyor
- `processing`: Yayınlanıyor
- `published`: Başarıyla tamamlandı
- `failed`: Başarısız oldu (retry destekli)

### 9.5 Publish Geçmişi

`publish_logs` tablosu ile tüm yayın işlemleri loglanır.

---

## 10. Sidebar Menü

Yönetim paneli sidebar'ına "AI Yönetimi" bölümü eklenmiştir:

```
AI Yönetimi
├── AI Ayarları           /admin/ai-settings
├── AI Kuyruğu            /admin/ai-queue
├── AI Üretimleri         /admin/ai-generations
└── Prompt Yönetimi       /admin/prompts

Yayın Yönetimi
├── Yayın Ayarları        /admin/publish-settings
├── Yayın Testi           /admin/publish-test
├── Hesaplar              /admin/publish-accounts
├── Yayın Kuyruğu         /admin/publish-queue
└── Yayın Geçmişi         /admin/publish-history
```

---

## 10. Dosya Yapısı

### Yeni Dosyalar

```
database/migrations/
  2026_06_02_100001_create_prompts_table.php
  2026_06_02_100002_create_ai_settings_table.php
  2026_06_02_100003_create_ai_queues_table.php
  2026_06_02_100004_create_ai_generations_table.php
  2026_06_02_100005_create_ai_generation_items_table.php
  2026_06_02_100006_create_ai_generation_logs_table.php
  2026_06_02_100007_update_ai_settings_for_providers.php
  2026_06_02_100008_add_source_category_to_prompts.php
  2026_06_03_000001_drop_ai_generation_items_and_update_ai_generations.php
  2026_06_03_100001_add_auto_approve_to_ai_settings.php
  2026_06_03_100002_add_approved_at_to_ai_generations.php

services/gpt4free/
  ai_generate.py

app/Models/
  AiSetting.php
  Prompt.php
  AiQueue.php
  AiGeneration.php
  AiGenerationLog.php

app/Services/NewsCollection/
  Gpt4freeClient.php
  Gpt4freeProvider.php
  OpenCodeProvider.php
  AIProviderFactory.php
  AIGenerationService.php
  AIGenerationLogService.php
  PromptResolverService.php
  AITestService.php

app/Services/Admin/
  AiSettingService.php
  PromptService.php
  AiQueueService.php

app/Jobs/
  AIQueueJob.php
  AIGenerationJob.php

app/Http/Controllers/Admin/
  AiSettingController.php
  PromptController.php
  AiQueueController.php
  AiGenerationController.php

app/Http/Requests/Admin/
  AiSetting/UpdateRequest.php
  Prompt/IndexRequest.php
  Prompt/StoreRequest.php
  Prompt/UpdateRequest.php
  Prompt/BulkDestroyRequest.php
  AiQueue/IndexRequest.php
  AiGeneration/IndexRequest.php

app/Queries/Admin/
  PromptQuery.php
  AiQueueQuery.php
  AiGenerationQuery.php

resources/views/admin/
  ai-settings/edit.blade.php
  prompts/index.blade.php
  prompts/create.blade.php
  prompts/edit.blade.php
  prompts/_form.blade.php
  ai-queue/index.blade.php
  ai-queue/show.blade.php
  ai-generations/index.blade.php
  ai-generations/show.blade.php
```

### Güncellenen Dosyalar

```
app/Models/RawTweet.php                  (aiGenerations ilişkisi eklendi, aiGenerationItems kaldırıldı)
app/Models/AiQueue.php                   (generations hasMany ilişkisi)
app/Models/AiGeneration.php              (rawTweet, sourceAccount, category ilişkileri; raw_tweet_id, provider, approved_at kolonları)
app/Models/AiSetting.php                 (auto_approve, auto_publish, publish_delay fillable ve cast eklendi)
app/Services/NewsCollection/AIGenerationService.php  (tweet bazlı üretime geçildi, auto_approve, auto_publish kontrolü eklendi)
app/Services/NewsCollection/AIReviewService.php       (approve/reject approved_at yönetimi, PublishSchedulerService entegrasyonu)
app/Services/NewsCollection/PromptResolverService.php (resolveForTweet metodu eklendi)
app/Services/Admin/AiSettingService.php               (normalize() auto_approve, auto_publish, publish_delay eklendi)
app/Http/Controllers/Admin/AiGenerationController.php (show load ilişkileri güncellendi)
app/Http/Controllers/Admin/AiQueueController.php      (show load ilişkileri güncellendi)
app/Http/Controllers/Admin/RawTweetController.php     (aiGenerations ilişkisi güncellendi)
app/Http/Requests/Admin/AiSetting/UpdateRequest.php   (auto_approve, auto_publish, publish_delay validasyonu)
app/Queries/Admin/AiGenerationQuery.php               (tweet bazlı arama, yeni durumlar: publishing, publish_failed)
resources/views/admin/ai-settings/edit.blade.php      (auto_approve, auto_publish, publish_delay eklendi)
resources/views/admin/ai-generations/index.blade.php  (tweet bazlı kolonlar, yeni durum badge'leri)
resources/views/admin/ai-generations/show.blade.php   (tweet bazlı detay, approved_at, publish_failed durumu)
resources/views/admin/ai-queue/show.blade.php          (generations listesi)
resources/views/admin/raw-tweets/show.blade.php       (aiGenerations ilişkisi düzeltildi)
routes/admin.php                      (AI route'ları, Publish route'ları)
routes/console.php                    (AIQueueJob, PublishSchedulerJob scheduler)
resources/views/admin/layouts/partials/sidebar.blade.php (AI menüsü, Yayın menüsü)
config/news_collection.php            (gpt4free, twitter_api_client konfigürasyonu)
```

### Kaldırılan Dosyalar

```
app/Models/AiGenerationItem.php  (tablo düşürüldü, model kaldırıldı)
```

---

## Tweet Medya Yönetimi Sistemi

Bu bölüm, tweet medya yönetiminin tam olarak nasıl çalıştığını detaylı olarak açıklar.

## 1. Temel İlkeler

- **Tweet çekiminde indirme yok**: Tweet çekilirken medya dosyaları indirilmez. Sadece URL metadata'sı toplanır.
- **Sadece seçilenler indirilir**: Havuza seçilmeyen tweetlerin medyası asla indirilmez.
- **AI medyayı görmez**: AI workflow sadece `{tweet_content}` placeholder'ını kullanır. Medya dosyaları AI'ya gönderilmez.
- **Publish hazırlığı**: `media_paths` üzerinden `storage/app/media/tweets/{tweet_id}/` altındaki dosyalara doğrudan erişilebilir. Publish aşamasında tekrar indirme gerekmez.
- **Hata toleransı**: Bir medya dosyasının indirilmesi başarısız olursa AI workflow durmaz. Sadece warning/error log atılır. Publish aşamasında dosya yoksa sadece metin paylaşılır.

## 2. Akış Diyagramı

```
Tweet Fetch (Python)
  └─ photo_urls, video_urls, animated_gif_urls, media_urls
  └─ TweetIngestionService (medya metadata kaydedilir)
  └─ raw_tweets: media_urls, media_count, media_type (indirilmez)

Pool Selection (PoolSelectionService)
  └─ Seçilen tweetler: DownloadTweetMediaJob::dispatch($tweetId)
  └─ Seçilmeyen tweetler: işlem yapılmaz

DownloadTweetMediaJob (Queue worker)
  └─ TweetMediaService::downloadForTweet()
  └─ HTTP ile indirme → storage/app/media/tweets/{id}/
  └─ raw_tweets: media_paths, media_downloaded_at

AI Workflow (AIGenerationService)
  └─ Sadece tweet metni kullanılır
  └─ {tweet_content} placeholder'ı doldurulur
  └─ Medya dosyalarına dokunulmaz

MediaCleanupJob (hourly scheduler)
  └─ published_media_retention_hours kontrolü
  └─ unpublished_media_retention_hours kontrolü
  └─ Süresi dolan dosyalar fiziksel olarak silinir
  └─ raw_tweets: media_paths = null, media_downloaded_at = null
```

## 3. Desteklenen Medya Türleri

Python scripti üç tür medya destekler:

| Tür | Açıklama | Örnek |
|-----|----------|-------|
| `photo` | Statik görseller | JPG, PNG, WebP, GIF |
| `video` | Video dosyaları | MP4, WebM |
| `animated_gif` | Animasyonlu GIF'ler | GIF (video olarak gelir) |
| `mixed` | Birden fazla tür bir arada | 2 foto + 1 video |

## 4. Medya İndirme Mechanizması

### 4.1 İndirme Sırası

1. Job Queue worker tarafından alınır.
2. Tweet veritabanından çekilir.
3. `media_urls` kontrol edilir — boşsa işlem yapılmaz.
4. `media_paths` kontrol edilir — doluysa "zaten indirilmiş" logu atılır, atlanır.
5. Her URL için:
   - HTTP GET isteği gönderilir (30sn timeout).
   - Content-Type header'ına göre uzantı belirlenir.
   - Dosya `storage/app/media/tweets/{tweet_id}/{hash}.{ext}` olarak kaydedilir.
6. Tüm başarılı yollar `media_paths` JSON dizisine kaydedilir.
7. `media_downloaded_at` zaman damgası güncellenir.

### 4.2 Dosya Yolu Formatı

```
storage/app/media/tweets/
  {tweet_id}/
    {md5_url_0}.jpg
    {md5_url_1}.jpg
    {md5_url_2}.mp4
```

URL hash'lenerek benzersiz dosya adı oluşturulur. Böylece aynı tweet yeniden indirilirse aynı dosya adı kullanılır.

### 4.3 Hata Yönetimi

- HTTP hatası: O URL atlanır, diğerlerine devam edilir. Hata listesi loglanır.
- İndirilen dosya yoksa: O URL atlanır.
- Tümü başarısız olursa: `media_downloaded_at` güncellenmez, hata loglanır.
- Job başarısızlığı: `failed()` hook log atar, queue otomatik retry dener.

## 5. Retention ve Cleanup

### 5.1 Retention Süreleri

| Ortam | Varsayılan | Açıklama |
|-------|------------|----------|
| Yayınlanmış (published) | 24 saat | Paylaşımdan sonra medya saklanma süresi |
| Yayınlanmamış | 48 saat | Onaylanmış ama henüz paylaşılmamış |

### 5.2 Cleanup Kriteri

- **Published**: `ai_generations.status = 'published'` ve `approved_at + published_media_retention_hours < now()`
- **Unpublished**: `ai_generations.status IN ('draft', 'approved', 'rejected')` ve `created_at + unpublished_media_retention_hours < now()`

### 5.3 Cleanup İşlemi

1. Yukarıdaki kriterlere göre `raw_tweet_id` listesi alınır.
2. Her tweet için `media_paths` kontrol edilir.
3. Dolu olanlar için fiziksel dosyalar `Storage::delete()` ile silinir.
4. `media_paths = null`, `media_downloaded_at = null` güncellenir.
5. `cleanup_completed` logu atılır (silinen sayısı ile).

**Not:** Publish sistemi (`ai_generations.status = 'published'`) henüz aktif olmadığı için cleanup şu anda published medya silmez. Publish eklendiğinde otomatik olarak devreye girer.

## 6. AI Context Sorularına Cevaplar

### Soru 1: Tweet paylaşıldıktan sonra medya siliniyor mu?

**Şu an hayır.** Mevcut sistemde `ai_generations.status = 'published'` durumu oluşmadığı için published retention mekanizması tetiklenmez. Publish sistemi eklendiğinde `approved_at + published_media_retention_hours` süresi dolan medyalar `MediaCleanupJob` tarafından silinecektir.

Varsayılan published retention süresi: **24 saat**.

### Soru 2: Havuzda seçilmeyen tweetlerin medyası indirilmiyor mu?

**Evet, doğru.** Havuza seçilmeyen tweetlerin sadece metadata'sı (`media_urls`) veritabanında durur, fiziksel dosya indirilmez. Seçim anında sadece `is_selected = true` olan tweetler için `DownloadTweetMediaJob` dispatch edilir. Seçilmeyen tweetler `is_selected = false` olarak kalır ve medya indirme tetiklenmez.

## 7. ai_generations.status Yapısı — Değişiklik Yapıldı mı?

**Hayır, değişiklik yapılmadı.** Bugünkü medya yönetimi implementasyonunda `ai_generations.status` sütunu hiç dokunulmadı. Mevcut durum aynen korundu:

| Durum | Açıklama |
|-------|----------|
| `draft` | AI tarafından üretildi, onay bekliyor |
| `approved` | Admin veya auto_approve ile onaylandı |
| `rejected` | Admin tarafından reddedildi |
| `published` | (Şu an oluşmuyor) Paylaşıldı — publish sistemi henüz yok |

Bu dört durumun dışında başka bir durum eklenmedi. Medya cleanup sistemi `status = 'published'` bekliyor ancak bu durum publish sistemi eklendiğinde oluşacak. Şu an için cleanup `published` durumundaki kayıt bulamaz ve işlem yapmaz.
