# X Haber Botu - AI Context

Bu dosya AI ajanlarının projeyi hızlı anlaması için hazırlanmıştır.

Detaylı dokümantasyonlar:

- X Haber Botu Sistem Dokümantasyonu
- Laravel CRUD Mimari Dokümantasyonu

Bu dosya özet sürümdür.

---

# Proje Amacı

Bu proje X (Twitter) üzerindeki haber kaynaklarını takip eden, tweetleri toplayan, puanlayan, seçen, AI ile haberleştiren ve paylaşan otomasyon sistemidir.

Sistem:

Kaynak Hesaplar
→ Tweet Toplama
→ Tweet Analizi
→ Tweet Seçim Havuzu
→ AI İçerik Üretimi
→ Yayınlama
→ Loglama / Monitoring

akışı ile çalışır.

---

# Teknolojiler

Backend:
- Laravel 12

Yardımcı Servis:
- Python 3.11

Veri Toplama (twscrape):
- Tweet çekme
- Profil bilgisi çekme
- Takipçi bilgisi çekme
- Tweet istatistikleri çekme

AI:
- gpt4free (değiştirilebilir mimari)

Yayınlama (twitter-api-client):
- Tweet paylaşma
- Like / Retweet / Reply / Follow

Queue:
- Laravel Queue

Scheduler:
- Laravel Scheduler

---

# Mimari Kurallar

En önemli kural:

Controller ince olmalıdır.

Controller içerisinde:

- iş mantığı
- puanlama
- veri işleme
- sorgu mantığı

bulunamaz.

---

Katmanlar:

Controller
↓
Request
↓
Service
↓
Model

Listeleme işlemleri:

Controller
↓
Query

şeklinde ilerler.

---

# CRUD Standartları

Her CRUD aşağıdaki yapıyı kullanmalıdır.

app/

Http/Controllers/Admin/
EntityController.php

Http/Requests/Admin/Entity/
IndexRequest.php
StoreRequest.php
UpdateRequest.php
BulkDestroyRequest.php

Queries/Admin/
EntityQuery.php

Services/Admin/
EntityService.php

resources/views/admin/entity/

index.blade.php
create.blade.php
edit.blade.php
_form.blade.php

---

# Controller Kuralları

Controller yalnızca:

- request alır
- service çağırır
- query çağırır
- view döndürür
- redirect döndürür

İş mantığı içermez.

---

# Request Kuralları

Validasyonlar Request içerisinde bulunmalıdır.

Örnek:

StoreRequest
UpdateRequest
IndexRequest

---

# Query Kuralları

Aşağıdaki işlemler Query katmanında yapılmalıdır:

- search
- filter
- sort
- pagination
- summary hesapları

Controller içerisinde query oluşturulamaz.

---

# Service Kuralları

Aşağıdaki işlemler Service içerisinde yapılmalıdır:

- create
- update
- delete
- bulk delete
- transaction
- veri normalizasyonu
- iş kuralları

Tüm kritik işlemler DB transaction kullanmalıdır.

---

# Blade Kuralları

Tekrar eden yapılar component olmalıdır.

Kullanılan componentler:

x-admin.page-header
x-admin.card
x-admin.modal
x-admin.button
x-admin.flash-message
x-admin.empty-state

Form alanları:

x-admin.form.input
x-admin.form.select
x-admin.form.textarea
x-admin.form.checkbox

---

# Route Kuralları

Admin route'ları:

routes/admin.php

içinde tanımlanır.

Kullanılan grup:

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')

URL içerisinde tekrar /admin yazılmaz.

Route isminde tekrar admin. yazılmaz.

---

# Soft Delete Politikası

Mümkün olan CRUD'larda SoftDeletes kullanılır.

Silinen veriler doğrudan yok edilmez.

---

# Queue Mimarisi

Tüm uzun işlemler Queue üzerinden çalışmalıdır.

Örnek:

FetchSourceAccountTweets
PoolSelectionJob
GenerateAiContentJob
PublishPostJob
PublishSchedulerJob
DownloadTweetMediaJob
MediaCleanupJob
SyncPublishAccountJob

Uzun işlemler Controller içerisinde çalıştırılmaz.

---

# Scheduler Mimarisi

Scheduler her dakika çalışır.

Zamanı gelen işleri Queue'ya bırakır.

Örnek:

news:fetch-due-sources

PoolSelectionJob
AIQueueJob
PublishSchedulerJob

Scheduler doğrudan ağır işlem yapmaz.

---

# Loglama Kuralları

Kritik işlemler loglanmalıdır.

Örnek:

- Tweet toplama
- Havuz seçimi
- AI üretimi
- Yayınlama
- Hatalar
- Retry işlemleri

Log hedefleri:

- system_logs
- laravel.log
- alerts

---

# Mevcut Sistemler

Tamamlanan:

- Auth sistemi
- Admin panel
- Source Categories CRUD
- Source Accounts CRUD
- Raw Tweets sistemi
- Story Cluster sistemi
- Twscrape yönetim paneli
- Tweet toplama sistemi
- Duplicate kontrol sistemi
- Tweet seçim havuzu sistemi
- Tweet medya yönetimi sistemi
- AI içerik üretim sistemi
- Publish yönetimi sistemi (kuyruk tabanlı, çoklu hesap destekli)
- Publish scheduler sistemi (rate limit, gece modu, warmup)

---

# Tweet Toplama Sistemi

Kaynak hesaplar:

source_accounts

tablosunda tutulur.

Scheduler:

news:fetch-due-sources

komutunu çalıştırır.

Komut:

FetchSourceAccountTweets

joblarını oluşturur.

Job:

TwscrapeClient

üzerinden Python servisini çalıştırır.

Python:

fetch_user_tweets.py

scriptini çalıştırır.

Sonuç:

TweetIngestionService

ile sisteme kaydedilir.

---

# Tweet Medya Yönetimi

Amac: Gereksiz depolamayı önlemek, sadece seçilen tweetlerin medyasını indirmek, publish aşamasında orijinal medyayı kullanabilmek.

Akış:

Tweet Fetch
→ Media Metadata Kaydı (indirmeden URL'leri kaydet)
→ Pool Selection
→ Seçilirse DownloadTweetMediaJob dispatch
→ Media Download → storage/app/media/tweets/{tweet_id}/
→ AI Workflow (medya yok, sadece metin)
→ Approved
→ Published (gelecek, medya hazır)
→ MediaCleanupJob (hourly, retention bazlı temizlik)

---

## Tweet Toplama

Tweet cekilirken medya dosyaları indirilmez. Sadece medya bilgileri kaydedilir.

Python fetch_user_tweets.py artık şu alanları cikariyor:

- photo_urls
- video_urls
- animated_gif_urls
- media_urls (hepsinin birlesik hali)

TweetIngestionService TweetMediaService::extractMediaFromPayload() ile bilgileri cikartip kaydeder:

- media_urls (JSON)
- media_count (integer)
- media_type (photo / video / animated_gif / mixed)

---

## Havuz Secimi Sonrasi

Kural: Havuza seçilmeyen tweetlerde medya indirilmez.

PoolSelectionService secim sonrasi DownloadTweetMediaJob::dispatch($tweetId) cagirir.

Indirme Queue uzerinden async yapilir.

---

## AI Workflow

Mevcut AI workflow korunur. AI yalnızca tweet metnini kullanir. {tweet_content} placeholder'i degismez. Medya AI'ya gönderilmez.

---

## Publish Uyumluluğu

Publish sistemi şu an yapılmadı. Ancak medya yapısı publish aşamasında tekrar indirme gerektirmeyecek şekilde tasarlandı: media_paths uzerinden storage'daki dosyalara direk erisim saglanir.

---

## Admin Ayarları

pool_settings tablosunda uclu ayar:

- media_download_enabled (default true)
- published_media_retention_hours (default 24)
- unpublished_media_retention_hours (default 48)

---

## Media Cleanup

MediaCleanupJob saatlik calisir.

Gorevleri:

1. published_media_retention_hours suresi dolmus yayinlanmis medyalari temizle
2. unpublished_media_retention_hours suresi dolmus yayinlanmamis medyalari temizle
3. Veritabanı kayitlarini guncelle (media_paths = null, media_downloaded_at = null)
4. SystemLog uzerinden log olustur

Not: Published durumu icin ai_generations.status = 'published' beklenir. Publish sistemi eklenmeden once bu durum olusmayacagi icin cleanup bos isler.

---

## Loglama

SystemLog uzerinden module = 'media_management' ile loglanir.

Olaylar:

- media_downloaded
- media_download_failed
- media_deleted
- media_exists
- cleanup_completed
- cleanup_failed

---

## Veritabanı Alanları

raw_tweets:

- media_urls (json)
- media_count (tinyint unsigned)
- media_type (varchar 20)
- media_downloaded_at (timestamp nullable)
- media_paths (json, lokal dosya yollari)

pool_settings:

- media_download_enabled (boolean)
- published_media_retention_hours (smallint unsigned)
- unpublished_media_retention_hours (smallint unsigned)

---

## Service Katmanı

NewsCollection:
- TweetMediaService (extractMediaFromPayload, downloadForTweet, deleteMedia, cleanupExpired)

---

## Job Katmanı

- DownloadTweetMediaJob: Seçilen tweetin medyasini async indirir
- MediaCleanupJob: Saatlik retention bazli temizlik yapar

---

## Scheduler

routes/console.php:

Schedule::job(new MediaCleanupJob)->hourly()->withoutOverlapping();

---

# Duplicate Mantığı

Aynı tweet ikinci kez kaydedilemez.

Kontrol:

raw_tweets.tweet_id

unique alanı ile yapılır.

Benzer haber kontrolü:

- normalize edilmiş metin
- ortak URL
- similarity threshold

ile yapılır.

Sonuçlar:

duplicate_checks

tablosunda tutulur.

---

# Twscrape Sistemi

Panel:

/admin/twscrape/*

özelliğine sahiptir.

Özellikler:

- hesap yönetimi
- ağırlık yönetimi
- komut çalıştırma
- sağlık kontrolü
- log görüntüleme

Hesap seçimi:

Weighted Round Robin

algoritması ile yapılır.

Amaç:

rate limit riskini dağıtmak.

---

# Tweet Seçim Havuzu

Amaç:

Toplanan tweetler arasından en değerli olanları seçmek.

AI entegrasyonu bu aşamada yapılmaz.

---

Akış:

Scheduler
↓
PoolSelectionJob
↓
PoolSelectionService
↓
Aday Tweetler
↓
Puanlama
↓
Sıralama
↓
Seçim
↓
Pool Batch
↓
Loglama

---

# Havuz Puanlama

Priority Score:

Kaynak hesabın priority_score değeri

0-100

---

Engagement Score:

likes
+ retweets × 2
+ replies × 1.5
+ quotes × 1.2
+ views × 0.001

sonucunun normalize edilmesi

---

Final Score:

(priority_score × 0.40)
+
(engagement_score × 0.60)

---

# Tekrar Seçilmeme Kuralı

Bir tweet bir kez seçildiyse:

selected_for_pool = true

olur.

Bu tweet:

bir daha asla aday olamaz.

---

# Havuz Veritabanı

Temel tablolar:

pool_settings
pool_batches
pool_batch_items

Ek alanlar:

raw_tweets

- selected_for_pool
- selected_at
- selected_for_ai
- ai_sent_at

---

# Admin Sayfaları

Kaynak Hesaplar

/admin/source-accounts

---

Kaynak Kategorileri

/admin/source-categories

---

Ham Tweetler

/admin/raw-tweets

---

Story Cluster

/admin/story-clusters

---

Havuz Ayarları

/admin/pool-settings

---

Tweet Havuzu

/admin/pool-selection

---

Havuz Geçmişi

/admin/pool-history

---

AI Ayarlari

/admin/ai-settings

---

AI Kuyrugu

/admin/ai-queue

---

AI Uretimleri

/admin/ai-generations

---

Prompt Yonetimi

/admin/prompts

---

Yayin Ayarlari

/admin/publish-settings

---

Yayin Testi

/admin/publish-test

---

Yayin Hesaplari

/admin/publish-accounts

---

Yayin Kuyrugu

/admin/publish-queue

---

Yayin Gecmisi

/admin/publish-history

---

# Geliştirme Kuralları

Yeni geliştirme yaparken:

1. Önce mevcut mimariyi analiz et.
2. CRUD standartlarına uy.
3. Controller'a iş mantığı yazma.
4. Query ve Service katmanlarını kullan.
5. Queue gerektiren işleri Queue'ya taşı.
6. Scheduler mantığını bozma.
7. Loglama ekle.
8. Mevcut admin temasını koru.
9. Component yapısını kullan.
10. Kod yazmadan önce etkilenecek dosyaları listele.

---

# AI Çalışma Talimatı

Yeni geliştirmeye başlamadan önce:

1. Mevcut ilgili dosyaları analiz et.
2. Etkilenecek dosyaları listele.
3. Yeni oluşturulacak dosyaları listele.
4. Veritabanı değişikliklerini listele.
5. Mimari uyumluluğu kontrol et.
6. Sonra implementasyona geç.

Bu kurallar zorunludur.

---

# AI Workflow Modülü

Havuz seçiminden sonra seçilen tweetlerin AI ile haberleştirilmesini sağlayan sistemdir.

## Akış

### auto_approve = false (varsayılan)

Pool Selection
→ AI Queue
→ Her tweet için ayrı AI üretimi
→ AI Generation (1 üretim = 1 tweet) → `draft`
→ Admin Onayı (approve/reject)
→ `approved` / `rejected`

Yeni kayıtlar `draft` olarak oluşur. Admin onayı bekler.

### auto_approve = true

Pool Selection
→ AI Queue
→ Her tweet için ayrı AI üretimi
→ AI Generation (1 üretim = 1 tweet) → `draft`
→ Sistem Onayı (otomatik approve) → `approved` + `approved_at`
→ Admin hala reject edebilir

Not:
- Published durumuna geçmez, maksimum durum `approved`'ır.
- Admin yetkisi korunur: auto approve ile onaylanan kayıtlar admin tarafından reddedilebilir.
- Auto approve yalnızca ilk onayı (draft → approved) verir.
- Auto publish aktifse approved → publish_queue otomatik olarak planlanır.

Her adımda detaylı loglama yapılır.

Üretim tweet bazlıdır: Her tweet kendi AI üretim kaydını oluşturur.

## Providerlar

Sistem iki provider destekler:

- GPT4Free (ücretsiz, Python servisi, model havuzundan otomatik seçim)
- OpenCode (HTTP API, OpenAI-compatible endpoint)

Admin panelden aktif provider değiştirilebilir.

## Prompt Sistemi

Promptlar source_categories ile entegredir.

Her kategorinin kendi aktif promptu olabilir.

Tweet hangi kategoriden geldiyse o kategorinin aktif promptu kullanılır.

Kategoriye özel prompt yoksa global aktif prompt kullanılır.

Üretim tweet bazlıdır: Her tweet için ayrı prompt seçilir ve ayrı AI çağrısı yapılır.

## Değişkenler

Zorunlu: {tweet_content} (tek tweet metni)

Opsiyonel: {tweet_count} (1), {sources}, {total_score}, {first_tweet}

Geçersiz placeholder varsa kayıt sırasında uyarı verilir.

PromptResolverService::resolveForTweet() metodu tek tweet için placeholder doldurma yapar.

## Tablolar

- ai_settings (singleton, provider ayarları, auto_approve)
- prompts (şablonlar, SoftDeletes, source_category_id FK)
- ai_queues (havuz batch → AI kuyruk)
- ai_generations (AI çıktıları, tweet bazlı: her kayıt 1 tweet'e karşılık gelir, approved_at)
- ai_generation_logs (işlem logları)

Not: ai_generation_items tablosu kaldırılmıştır. Tweet ilişkisi doğrudan ai_generations.raw_tweet_id üzerindendir.

## Model Katmanı

- AiSetting (singleton, provider, opencode_api_key, opencode_base_url, opencode_model, auto_approve)
- Prompt (name, source_category_id, prompt_text, version, is_active)
- AiQueue (pool_batch_id, batch_no, status)
- AiGeneration (ai_queue_id, raw_tweet_id, source_account_id, category_id, provider, model, prompt, full_prompt, ai_response, generated_news, token_usage, status, approved_at)
- AiGenerationLog (ai_queue_id, ai_generation_id, level, message)

AiGeneration ilişkileri:
- belongsTo RawTweet
- belongsTo SourceAccount
- belongsTo SourceCategory
- belongsTo AiQueue
- belongsTo Prompt
- hasMany AiGenerationLog

## Service Katmanı

Admin:
- AiSettingService (ayar güncelleme)
- PromptService (CRUD, kategori bazlı activate/deactivate)
- AiQueueService (kuyruk oluşturma, durum yönetimi)

NewsCollection:
- AIGenerationService (ana üretim orchestrator, tweet bazlı: processQueue, generateForTweet, auto_approve kontrolü)
- AIGenerationLogService (log yardımcısı)
- PromptResolverService (değişken çözümleme, placeholder validasyon, resolveForTweet metodu)
- AIProviderFactory (provider seçimi)
- Gpt4freeProvider + Gpt4freeClient (Python g4f entegrasyonu)
- OpenCodeProvider (HTTP API)
- AITestService (provider bağlantı testi)
- AIReviewService (onay/red/yayınlama: approve, reject, publish; approved_at yönetimi)

## Job Katmanı

- AIQueueJob: Completed PoolBatch'leri AI kuyruğuna alır
- AIGenerationJob: AI provider ile tweet bazlı içerik üretir (her tweet için ayrı üretim)

## Controller Katmanı

- AiSettingController (edit, update, test)
- PromptController (CRUD + activate/deactivate)
- AiQueueController (index, show)
- AiGenerationController (index, show, approve, reject, publish)

## Admin Sayfaları

/admin/ai-settings → AI provider ayarları ve bağlantı testi
/admin/prompts → Prompt şablon yönetimi
/admin/ai-queue → AI kuyruk listesi
/admin/ai-generations → AI üretimleri ve review

## Review Durumları

draft → approved → publishing → published
draft → rejected
approved → rejected
approved → publish_failed
publish_failed → approved (retry)

Auto Approve ile draft → approved otomatik olarak yapılır (approved_at = now()). Auto Publish ile approved → publish_queue otomatik olarak planlanır.

## Publish Akışı

Onaylanan (approved) içerikler PublishSchedulerService tarafından publish_queue'ya alınır.

```
approved → PublishSchedulerService → Kurallar Kontrolü → publish_queue
                                                              ↓
                                                    PublishPostJob
                                                              ↓
                                                           published
```

Kurallar:
- Günlük paylasim limiti
- Saatlik paylasim limiti
- Minimum gap suresi
- Random delay (min/max)
- Gece modu (baslangic/bitis saati)
- Warmup mode (hesap yasina gore limit)

Scheduler: PublishSchedulerJob her dakika calisir, pending ve zamani gelmis kayitlari PublishPostJob ile dispatch eder.

## Scheduler

routes/console.php:

Schedule::job(new AIQueueJob)->everyMinute()->withoutOverlapping();
Schedule::job(new PublishSchedulerJob)->everyMinute()->withoutOverlapping();

## GPT4Free Python Entegrasyonu

Script: services/gpt4free/ai_generate.py

Referans: test3.py + havuz dosyası

Model listesini havuz dosyasından okur.

Hata durumunda diğer modellere geçer.

Çıktı JSON formatında stdout'a yazılır.

Laravel tarafı: Gpt4freeClient (Symfony Process ile çalıştırır, stdout/stderr okur, JSON parse, timeout, Windows env fix).

## OpenCode Entegrasyonu

OpenAI-compatible HTTP API.

Base URL: https://opencode.ai/zen/go/v1

Laravel tarafı: OpenCodeProvider (GuzzleHttp ile POST, Bearer auth).

Admin panelden API key, base URL ve model değiştirilebilir.

---

# Publish Sistemi

AI tarafından üretilen haberleri medya desteğiyle paylaşabilen, kuyruk tabanlı çalışan, çoklu hesap desteğine hazırlı, loglanabilir ve ileride farklı platformlara genişletilebilecek profesyonel bir yayın sistemidir.

## Mimari Ayrım

- twscrape: Veri toplama (tweet çekme, profil bilgisi çekme, istatistik)
- twitter-api-client: Yayınlama (tweet paylaşma, like, retweet, reply, follow)

## Publish Akışı

```
AI Generation (approved)
    ↓
PublishSchedulerService
    ↓ Kurallar kontrolü:
    ↓ - Günlük limit
    ↓ - Saatlik limit
    ↓ - Gece modu
    ↓ - Minimum gap
    ↓ - Random delay
    ↓ - Warmup mode
publish_queue (pending, scheduled_at ile)
    ↓ Her dakika PublishSchedulerJob
    ↓
PublishPostJob
    ↓ PublishService (Python script çalıştırır)
    ↓
published
```

## Publish Scheduler Kuralları

- daily_post_limit: Günlük paylaşım limiti (varsayılan: 10)
- hourly_post_limit: Saatlik paylaşım limiti (varsayılan: 1)
- min_delay_minutes / max_delay_minutes: Random gecikme (45-90 dk)
- min_gap_minutes: İki paylaşım arası minimum süre (3 dk)
- publish_start_hour / publish_end_hour: Gece modu (08-23)
- warmup_mode_enabled: Yeni hesap koruma sistemi (0-30 gün: 3, 30-60: 5, 60+: 10)
- auto_publish_enabled: Otomatik yayını aktif/pasif

## Tablolar

- publish_settings (singleton, ayarlar)
- publish_accounts (X hesapları, cookie yönetimi, warmup için account_created_at)
- publish_queue (yayın kuyruğu: pending, processing, published, failed)
- publish_logs (yayın geçmişi: tweet_id_x, duration, error_message)

## Service Katmanı

Admin:
- PublishSettingsService (ayar güncelleme)
- PublishAccountService (CRUD, profil güncelleme)
- PublishQueueService (markProcessing, markPublished, markFailed, retry)
- PublishSchedulerService (kurallar kontrolü, publish_queue oluşturma, pending dispatch)

NewsCollection:
- PublishService (Python script çalıştırır, JSON I/O, timeout, hata yönetimi)

## Job Katmanı

- PublishSchedulerJob: Her dakika pending ve zamani gelmis kayitlari dispatch eder
- PublishPostJob: PublishService üzerinden tweet paylasir
- SyncPublishAccountJob: Hesap profil bilgilerini günceller

## Python Entegrasyonu

Script: services/twitter-api-client/publish_tweet.py

- JSON payload: {text, media_paths, cookies}
- auth_token ve ct0 publish_accounts tablosundan alınır
- Mevcut medya dosyaları storage'dan kullanılır, tekrar indirilmez
- Çıktı JSON formatında stdout'a yazılır

Laravel tarafı: PublishService (Symfony Process ile çalıştırır, payload dosyası oluşturur/temizler, stdout parse, timeout, Windows env fix).

## Admin Sayfaları

/admin/publish-settings → Yayın ayarları (limitler, gecikme, warmup)
/admin/publish-test → Manuel test paylaşımı
/admin/publish-accounts → X hesap yönetimi
/admin/publish-queue → Yayın kuyruğu (pending, processing, published, failed)
/admin/publish-history → Yayın geçmişi

## Loglama

SystemLog module = 'publish_scheduler':
- Publish Scheduler çalıştı
- Limit nedeniyle ertelendi
- Saatlik limite takıldı
- Günlük limite takıldı
- Gece moduna takıldı
- Publish Queue'ya alındı
- Publish başladı
- Publish başarılı
- Publish başarısız
- Retry çalıştı