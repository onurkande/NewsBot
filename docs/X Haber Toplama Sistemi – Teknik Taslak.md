# X Haber Toplama Sistemi – Teknik Taslak

## 1. Amaç

Bu sistemin amacı, belirlenen X (Twitter) haber kaynaklarını düzenli olarak taramak, yeni paylaşımları toplamak, aynı haberi farklı kaynaklarda tekrar eden içerik olarak algılamak, bunları tek bir haber hikâyesi altında birleştirmek, en uygun içeriği seçmek ve sonunda yayınlama aşamasına göndermektir.

Sistem tweet bazlı değil, **hikâye (story)** bazlı çalışacaktır. Böylece aynı olay farklı hesaplarda ayrı tweet olarak görünse de sistem bunu tek bir haber olarak değerlendirebilir.

---

## 2. Genel Çalışma Mantığı

### 2.1 Kaynak Yönetimi

Kaynaklar Laravel admin panel üzerinden dinamik olarak yönetilir.

Her kaynak için:

* X kullanıcı adı tutulur
* kategori atanır
* güven puanı verilir
* kontrol sıklığı dakika cinsinden belirlenir
* aktif/pasif durumu ayarlanır
* son kontrol zamanı saklanır
* son görülen tweet ID saklanır

Bu yapı sayesinde her kaynak farklı sıklıkta taranabilir.

---

### 2.2 Tarama Mantığı

Sistem bir scheduler ve queue yapısıyla çalışır.

Önerilen akış:

1. Laravel scheduler belirli aralıkla çalışır.
2. Scheduler, kontrol zamanı gelen kaynakları queue’ya atar.
3. Python tarafında twscrape ile ilgili hesap taranır.
4. Yeni tweetler çekilir.
5. Tweetler normalize edilerek veritabanına kaydedilir.
6. Duplicate ve similarity kontrolü yapılır.
7. Tweetler story cluster yapısına bağlanır.
8. Story skorlanır.
9. Yayınlama adımına uygun olan story seçilir.

---

### 2.3 Sliding Window Mantığı

Sabit 15 dakikalık bloklar yerine kayan bir zaman penceresi kullanılır.

Örnek:

* son 5 dakika
* son 10 dakika
* son 15 dakika

Her çalışmada sistem son N dakika içindeki yayınlanmamış ve işlenmemiş story’lere bakar.

Bu yaklaşım sayesinde:

* haber geç kalmaz
* aynı haberin farklı kaynaklardan tekrar çıkışı görülebilir
* hızlı büyüyen içerikler daha iyi yakalanır

---

### 2.4 Yeni Tweet Kontrolü

Her kaynak için son görülen tweet ID saklanır.

Yeni veri çekilirken:

* daha önce kaydedilmiş tweet ID’ler atlanır
* son görülen ID güncellenir
* sadece yeni tweetler işleme alınır

Bu, aynı tweetin tekrar işlenmesini engeller.

---

### 2.5 Aynı Haber Tekrarını Engelleme

Aynı haber farklı hesaplardan farklı tweet olarak gelebilir. Bunu engellemek için 3 katman kullanılır:

#### Katman 1: Tweet ID

Aynı tweet tekrar mı? Kontrol edilir.

#### Katman 2: URL

Tweet içindeki linkler normalize edilir. Aynı haber linki daha önce işlendi mi bakılır.

#### Katman 3: Metin Benzerliği

Tweet metni temizlenir ve benzerlik puanı hesaplanır.

Eğer benzerlik yüksekse tweet yeni haber değil, mevcut story’nin parçası olarak kabul edilir.

---

## 3. Hikâye (Story) Bazlı Mimari

Tweetleri tek tek haber gibi değil, bir olayın parçaları gibi düşünmek gerekir.

Örnek:

* A hesabı bir haber attı
* 3 dakika sonra B hesabı aynı haberi farklı cümlelerle yazdı
* 5 dakika sonra C hesabı aynı olayı doğruladı

Bu durumda sistem bunu 3 ayrı haber değil, tek bir story olarak görür.

---

### 3.1 Story Cluster Nedir?

Benzer tweetlerin toplandığı gruptur.

Örnek:

* Story Cluster #124

  * Tweet 1
  * Tweet 2
  * Tweet 3

Bu cluster’ın bir başlığı, skoru ve yayın durumu olur.

---

### 3.2 Story’nin Avantajı

* aynı haberi tekrar paylaşma riski düşer
* hangi tweetin daha güçlü olduğu görülebilir
* aynı haberin farklı kaynaklardaki varyasyonları birleştirilir
* yayın kararı daha doğru verilir

---

## 4. Yayınlama Kararı Mantığı

Sistem doğrudan ilk gelen tweeti paylaşmaz.

Bunun yerine story bazında karar verir.

### Yayın senaryoları

#### Hızlı haber

Çok sıcak bir haber ise kısa bir bekleme sonrası yayınlanır.

#### Normal haber

Biraz bekletilir, başka kaynaklarda doğrulama var mı bakılır.

#### Zayıf haber

Skoru düşükse yayınlanmaz veya manuel onaya gönderilir.

---

## 5. Ana Bileşenler

### 5.1 Laravel

Ana beyin.

Görevleri:

* admin panel
* kaynak yönetimi
* scheduler
* queue
* loglama
* mail gönderimi
* istatistikler
* yayın akışı
* onay mekanizması

### 5.2 Python

İşçi servis.

Görevleri:

* twscrape ile veri çekme
* tweet analizi
* metin normalizasyonu
* benzerlik hesaplama
* AI entegrasyonu
* çıktı üretme

### 5.3 twscrape

Tweet çekme ve kullanıcı verisi toplama için.

### 5.4 gpt4free

İçeriği haber formatına çevirmek için.

### 5.5 twitter-api-client

Yayınlama ve X hesap işlemleri için.

---

## 6. Veritabanı Taslağı

Aşağıdaki tablolar önerilir.

---

### 6.1 `source_accounts`

Takip edilecek haber kaynakları.

Alanlar:

* `id`
* `username`
* `display_name`
* `category_id`
* `priority_score`
* `check_interval_minutes`
* `is_active`
* `last_checked_at`
* `last_seen_tweet_id`
* `trust_score`
* `notes`
* `created_at`
* `updated_at`

Amaç:

* kaynakları dinamik yönetmek
* her kaynak için ayrı tarama sıklığı belirlemek
* öncelik ve güven puanı tutmak

---

### 6.2 `source_categories`

Kaynak kategorileri.

Alanlar:

* `id`
* `name`
* `slug`
* `description`
* `created_at`
* `updated_at`

Örnek kategoriler:

* genel haber
* ekonomi
* teknoloji
* spor
* kripto
* dünya

---

### 6.3 `raw_tweets`

Çekilen ham tweet verileri.

Alanlar:

* `id`
* `tweet_id`
* `source_account_id`
* `tweet_url`
* `tweet_text`
* `raw_payload`
* `tweeted_at`
* `like_count`
* `retweet_count`
* `reply_count`
* `view_count`
* `quote_count`
* `fetched_at`
* `is_processed`
* `created_at`
* `updated_at`

Amaç:

* ham veriyi saklamak
* tekrar işlememek
* analiz için kaynak tutmak

---

### 6.4 `tweet_normalized_texts`

Tweetlerin temizlenmiş ve analiz için hazırlanmış halleri.

Alanlar:

* `id`
* `raw_tweet_id`
* `normalized_text`
* `normalized_hash`
* `language`
* `tokens`
* `created_at`
* `updated_at`

Amaç:

* benzerlik ve duplicate analizi
* metin karşılaştırması

---

### 6.5 `duplicate_checks`

Duplicate kontrol kayıtları.

Alanlar:

* `id`
* `raw_tweet_id`
* `matched_tweet_id`
* `match_type` (tweet_id / url / text)
* `similarity_score`
* `is_duplicate`
* `checked_at`
* `created_at`
* `updated_at`

Amaç:

* aynı içerik tekrar mı geldi takip etmek

---

### 6.6 `story_clusters`

Benzer tweetlerin oluşturduğu haber hikâyeleri.

Alanlar:

* `id`
* `cluster_hash`
* `title`
* `summary`
* `category_id`
* `main_source_account_id`
* `first_seen_at`
* `last_updated_at`
* `status` (open / selected / published / ignored)
* `story_score`
* `published_post_id`
* `created_at`
* `updated_at`

Amaç:

* aynı haberi tek yerde toplamak
* yayın kararını story bazında vermek

---

### 6.7 `story_cluster_items`

Story cluster içindeki tweetler.

Alanlar:

* `id`
* `story_cluster_id`
* `raw_tweet_id`
* `relation_type` (same_news / confirmation / alternate_source)
* `item_score`
* `created_at`
* `updated_at`

Amaç:

* hangi tweetlerin aynı story’ye ait olduğunu tutmak

---

### 6.8 `story_scores`

Story puanlama kayıtları.

Alanlar:

* `id`
* `story_cluster_id`
* `source_trust_score`
* `engagement_score`
* `recency_score`
* `velocity_score`
* `duplicate_penalty`
* `similarity_bonus`
* `final_score`
* `scored_at`
* `created_at`
* `updated_at`

Amaç:

* hikâyelerin neden seçildiğini açıklamak

---

### 6.9 `ai_drafts`

AI tarafından üretilen taslak içerikler.

Alanlar:

* `id`
* `story_cluster_id`
* `prompt_text`
* `ai_response`
* `draft_title`
* `draft_body`
* `draft_status` (pending / approved / rejected)
* `generated_at`
* `created_at`
* `updated_at`

Amaç:

* AI çıktısını kaydetmek
* farklı sürümleri karşılaştırmak

---

### 6.10 `published_posts`

Paylaşılan içerikler.

Alanlar:

* `id`
* `story_cluster_id`
* `ai_draft_id`
* `x_post_id`
* `posted_text`
* `posted_at`
* `publish_status` (success / failed / retry)
* `created_at`
* `updated_at`

Amaç:

* hangi story’nin yayınlandığını tutmak
* tekrar paylaşımı engellemek

---

### 6.11 `publish_jobs`

Yayınlama iş kuyruğu kayıtları.

Alanlar:

* `id`
* `story_cluster_id`
* `status`
* `attempt_count`
* `last_error`
* `scheduled_at`
* `executed_at`
* `created_at`
* `updated_at`

Amaç:

* yayın sürecini izlemek

---

### 6.12 `system_logs`

Genel sistem logları.

Alanlar:

* `id`
* `level` (info / warning / error / critical)
* `module`
* `message`
* `context_json`
* `created_at`
* `updated_at`

Amaç:

* her adımı kayıt altına almak

---

### 6.13 `package_health_logs`

Paket sağlık kayıtları.

Alanlar:

* `id`
* `package_name`
* `status`
* `version`
* `last_check_at`
* `error_message`
* `response_time_ms`
* `created_at`
* `updated_at`

Amaç:

* twscrape, gpt4free ve diğer paketlerin sağlık durumunu izlemek

---

### 6.14 `alerts`

Bildirim kayıtları.

Alanlar:

* `id`
* `alert_type`
* `title`
* `message`
* `severity`
* `is_sent`
* `sent_via` (mail / dashboard)
* `sent_at`
* `created_at`
* `updated_at`

Amaç:

* kritik hata ve uyarıları yönetmek

---

### 6.15 `monitoring_checks`

Periyodik sağlık kontrol kayıtları.

Alanlar:

* `id`
* `check_name`
* `status`
* `response_time_ms`
* `details_json`
* `checked_at`
* `created_at`
* `updated_at`

Amaç:

* sistemin canlı kalıp kalmadığını görmek

---

## 7. İş Akışı

### A. Kaynak Tanımlama

* Admin panelden kaynak eklenir
* kategori atanır
* kontrol sıklığı belirlenir
* güven puanı girilir

### B. Scheduler Çalışması

* Laravel scheduler çalışır
* kontrol zamanı gelen kaynakları bulur
* queue job oluşturur

### C. Tweet Çekme

* Python worker twscrape çalıştırır
* son tweetler alınır
* ham veri kaydedilir

### D. Ön İşleme

* tweet temizlenir
* URL çıkarılır
* metin normalleştirilir
* hash üretilir

### E. Duplicate / Benzerlik Kontrolü

* aynı tweet mi?
* aynı URL mi?
* aynı haber mi?

### F. Story Cluster

* benzer tweetler aynı kümeye girer
* yeni cluster açılır veya mevcut cluster güncellenir

### G. Skorlama

* kaynak puanı
* etkileşim
* güncellik
* hız
* benzerlik
* tekrar cezası

### H. Yayın Adayı Seçimi

* en yüksek skorlu story seçilir
* gerekiyorsa bekleme penceresine alınır

### I. AI Üretimi

* seçilen story AI’ya gönderilir
* haber formatında taslak çıkar

### J. Yayınlama

* onay varsa paylaşılır
* başarı/başarısızlık kaydedilir

### K. Loglama ve Uyarı

* her aşama loglanır
* kritik hata varsa mail gönderilir

---

## 8. Bekleme ve Zamanlama Stratejisi

Sistemin haberi hemen paylaşması her zaman doğru değildir.

Bu yüzden story’nin önemine göre bekleme süresi belirlenir.

Örnek:

* sıcak haber: 1–3 dakika
* normal haber: 5 dakika
* düşük öncelik: 10 dakika

Bu süre içinde:

* başka kaynaklarda doğrulama var mı bakılır
* benzer tweetler eşleşir
* story skoru güncellenir

---

## 9. Önceliklendirme Mantığı

Her story için şu sorular sorulur:

* Haber ne kadar güncel?
* Kaynak ne kadar güvenilir?
* Başka kaynaklar doğruluyor mu?
* Aynı haber daha önce yayınlandı mı?
* Etkileşim hızı yüksek mi?

Bu cevaplara göre final karar verilir.

---

## 10. Bildirim ve Hata Yönetimi

Kritik durumlar:

* paket bozulması
* publish başarısızlığı
* scheduler durması
* queue durması
* veri gelmemesi
* duplicate patlaması
* hesap erişim problemi

Bu durumlarda:

* log oluşturulur
* dashboard uyarısı verilir
* mail gönderilir

---

## 11. Dashboard’da Görüntülenecekler

* aktif kaynak sayısı
* kontrol bekleyen kaynaklar
* son çekilen tweetler
* oluşturulan story sayısı
* yayınlanan story sayısı
* başarısız yayın sayısı
* kritik hata sayısı
* paket sağlık durumu
* son uyarılar
* günlük istatistikler

---

## 12. MVP Sırası

İlk sürümde yapılacaklar:

1. Kaynak tablosu
2. Tweet çekme sistemi
3. Son tweet ID saklama
4. Duplicate kontrolü
5. Story cluster oluşturma
6. Basit skor sistemi
7. AI taslak üretimi
8. Manuel onaylı yayın
9. Log sistemi
10. Mail alarm sistemi

---

## 13. Sonuç

Bu sistemin ana mantığı şudur:

* kaynaklar dinamik olacak
* her kaynak farklı sıklıkta taranacak
* yeni tweetler saklanacak
* aynı haber farklı hesaplardan gelse bile story bazında birleştirilecek
* bir haber yalnızca bir kez yayınlanacak
* sistem hatalı çalışırsa hemen haber verilecek

Bu taslak, Laravel tarafında kurulacak çekirdek mimarinin temelini oluşturur.
