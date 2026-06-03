# X Haber Toplama Sistemi – Teknik Taslak

## 1. Amaç

Bu sistemin amacı, belirlenen X (Twitter) haber kaynaklarını düzenli olarak taramak, yeni paylaşımları toplamak ve uygun içerikleri paylaşım sistemine hazırlamaktır.

İlk sürümde sistem daha sade ve stabil bir yapıda geliştirilecektir.

Bu sürümde:

* haber kaynakları Laravel panelden dinamik yönetilecektir
* her kaynak farklı sıklıkta kontrol edilecektir
* kaynaklar aktif/pasif yapılabilecektir
* yeni tweetler veritabanına kaydedilecektir
* tweet ID bazlı duplicate kontrolü yapılacaktır
* aynı tweet ikinci kez paylaşılmayacaktır
* uygun tweetler AI sistemine gönderilecektir

İlk MVP sürümünde:

* story cluster sistemi
* aday havuzu sistemi
* gelişmiş similarity sistemi

aktif olmayacaktır.

Ancak sistem mimarisi ileride bu özelliklerin eklenmesine uygun şekilde hazırlanacaktır.

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

### 2.3 Çalışma Mantığı

Sistem scheduler + queue yapısıyla çalışacaktır.

Örnek akış:

1. Laravel scheduler çalışır.
2. Kontrol zamanı gelen kaynaklar belirlenir.
3. İlgili kaynaklar queue’ya alınır.
4. Python tarafında twscrape çalıştırılır.
5. Yeni tweetler çekilir.
6. Tweetler veritabanına kaydedilir.
7. Tweet ID duplicate kontrolü yapılır.
8. Uygun içerikler AI sistemine gönderilir.
9. İçerikler paylaşım aşamasına aktarılır.

Bu yapı sayesinde sistem modüler ve geliştirilebilir olur.

---

### 2.4 Yeni Tweet Kontrolü

Her kaynak için son görülen tweet ID saklanır.

Yeni veri çekilirken:

* daha önce kaydedilmiş tweet ID’ler atlanır
* son görülen ID güncellenir
* sadece yeni tweetler işleme alınır

Bu, aynı tweetin tekrar işlenmesini engeller.

---

### 2.5 Aynı Tweetin Tekrar Paylaşılmasını Engelleme

İlk sürümde duplicate kontrolü yalnızca tweet ID üzerinden yapılacaktır.

Mantık:

* Eğer tweet ID daha önce işlendiyse tekrar işlenmez.
* Eğer tweet ID daha önce paylaşılmışsa tekrar paylaşılmaz.

Bu yöntem başlangıç için yeterlidir.

Not:
Farklı kaynakların aynı haberi farklı tweet olarak paylaşması ilk sürümde duplicate olarak kabul edilmeyecektir.

İlerleyen sürümlerde:

* içerik benzerliği
* URL analizi
* story cluster sistemi
* aday havuzu sistemi

gibi gelişmiş yapılar eklenebilir.

---

## 3. Gelecekte Eklenebilecek Gelişmiş Sistemler

İlk MVP sürümünde sistem sade tutulacaktır.

Ancak ilerleyen aşamalarda aşağıdaki sistemler eklenebilir.

### 3.1 Story Cluster Sistemi

Aynı haberi paylaşan farklı tweetleri tek bir haber hikâyesi altında toplamak için kullanılabilir.

### 3.2 Aday Havuzu Sistemi

Yeni tweetler belirli süre aday havuzunda tutulup en iyi içerik seçilebilir.

### 3.3 Similarity Sistemi

İçerik benzerliği analizi ile aynı haberin farklı kaynaklarda tekrar paylaşılması engellenebilir.

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

### 6.6 `processed_tweets`

İşlenen ve yayınlanan tweet kayıtları.

Alanlar:

* `id`
* `raw_tweet_id`
* `tweet_id`
* `is_ai_generated`
* `is_published`
* `published_at`
* `created_at`
* `updated_at`

Amaç:

* aynı tweetin tekrar paylaşılmasını engellemek
* işlenen tweetleri takip etmek

---

### 6.7 `future_story_clusters`

Gelecekte kullanılabilecek story cluster sistemi için ayrılmış yapı.

İlk MVP sürümünde aktif olmayacaktır.

---

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

### 6.8 `future_story_scores`

Gelecekte kullanılabilecek gelişmiş story puanlama sistemi için ayrılmış yapı.

İlk MVP sürümünde aktif olmayacaktır.

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

## 8. Kaynak Kontrol Stratejisi

Her kaynak farklı sıklıkta kontrol edilecektir.

Örnek:

* büyük haber hesapları → 5 dakika
* orta ölçekli hesaplar → 15 dakika
* düşük öncelikli hesaplar → 30 dakika

Bu süreler admin panel üzerinden dinamik olarak değiştirilebilir.

Kaynaklar:

* aktif/pasif yapılabilir
* önceliklendirilebilir
* kategorilere ayrılabilir

---

## 9. Önceliklendirme Mantığı

Her kaynak için güven ve öncelik puanları kullanılacaktır.

Örnek:

* güvenilir kaynaklar daha sık kontrol edilir
* önemli kaynaklar öncelikli işlenir
* düşük kaliteli kaynaklar daha düşük öncelikte tutulur

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
