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
