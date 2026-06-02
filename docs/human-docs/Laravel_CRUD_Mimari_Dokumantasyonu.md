Laravel CRUD Mimari Dokümantasyonu

Source Category refactor bundle temel alınarak hazırlanmıştır

Amaç: tüm CRUD işlemleri için aynı standart, aynı klasör yapısı ve aynı iş akışını tekrar kullanılabilir hale getirmek.



Sürüm: 1.0

Hazırlanan yapı: Controller + FormRequest + Service + Query + Blade Components + Reusable JS



1. Bu dokümanın amacı

Bu doküman, source-categories CRUD'ı üzerinden kurulan yeni mimariyi standart hale getirmek için hazırlandı. Buradaki hedef tek bir CRUD'ı çalıştırmak değil; aynı düzeni proje içindeki diğer tüm CRUD'lara birebir uygulayabilmek.

Yeni CRUD oluştururken hangi klasöre hangi dosyanın konacağı, hangi katmanın ne iş yapacağı, route'ların nasıl yazılacağı, checkbox ile seçim ve toplu silme davranışının nasıl yönetileceği ve UI bileşenlerinin nasıl tekrar kullanılacağı burada kayıt altına alınmıştır.

Kural basit: controller ince ve okunur kalır, validasyon FormRequest'e taşınır, listeleme mantığı Query katmanında toplanır, create/update/delete/bulk delete iş kuralları Service katmanında yaşar, UI tekrarları component olur, sayfa özel davranışlar ise JS modülü ile ayrıştırılır.

2. Mimari özet

Bu projede kullanılan standart akış şu şekildedir:

Route, yetki kontrolü için admin middleware altındadır.

Controller yalnızca request'i alır, ilgili service/query sınıfını çağırır ve response döndürür.

FormRequest tüm validasyon ve prepareForValidation işini taşır.

Query sınıfı arama, filtre, sıralama ve pagination işini çözer.

Service sınıfı create/update/delete/bulk delete iş mantığını tek yerde toplar.

Blade componentleri form alanlarını, kartları, modalı, flash mesajları ve boş durumları standartlaştırır.

JavaScript, seçim durumunu, modal açma-kapatmayı ve flash mesaj otomatik kapanmasını yönetir.

Bu yapı sayesinde yeni CRUD açarken tekrar eden HTML ve iş kurallarını kopyala-yapıştır yapmak yerine, aynı kalıbı kullanıp sadece entity adına özgü alanları değiştirmek yeterli olur.

3. Klasör ağacı ve dosya yerleşimi

Aşağıdaki yapı, şu an refactor edilen paketle birebir uyumlu ve diğer CRUD'lar için de önerilen standarttır.

app/

  Http/

    Controllers/

      Admin/

        SourceCategoryController.php

    Requests/

      Admin/

        SourceCategory/

          IndexRequest.php

          StoreRequest.php

          UpdateRequest.php

          BulkDestroyRequest.php

  Models/

    SourceCategory.php

  Queries/

    Admin/

      SourceCategoryQuery.php

  Services/

    Admin/

      SourceCategoryService.php



resources/

  views/

    admin/

      source-categories/

        index.blade.php

        create.blade.php

        edit.blade.php

        _form.blade.php

      partials/

        flash.blade.php

    components/

      admin/

        page-header.blade.php

        card.blade.php

        modal.blade.php

        flash-message.blade.php

        empty-state.blade.php

        button.blade.php

        validation-error.blade.php

        form/

          input.blade.php

          textarea.blade.php

          select.blade.php

          checkbox.blade.php



database/

  migrations/

    2026_05_28_000002_add_soft_deletes_to_source_categories_table.php



routes/

  admin.php



public/

  admin-assets/

    js/

      app.js

Not: Bu projede statik tema kullanıldığı için JS dosyası public/admin-assets altında tutuluyor. Eğer sonra Vite'e geçilirse aynı mantık resources/js altında modülize edilip build edilmesi gerekir.

4. Route katmanı nasıl kurulmalı

Admin CRUD route'ları routes/admin.php içinde ve tek bir admin middleware grubunda tutulmalıdır. Böylece login olmayan kullanıcılar ve admin olmayan kullanıcılar panelden otomatik olarak dışarı alınır.

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', fn () => view('admin.index'))->name('dashboard');



    Route::resource('source-categories', SourceCategoryController::class)->except('show');

    Route::delete('source-categories/bulk-destroy', [SourceCategoryController::class, 'bulkDestroy'])

        ->name('source-categories.bulk-destroy');

});

Önemli kural: group içinde prefix('admin') zaten olduğu için URL'ye tekrar /admin yazılmaz. Aynı şekilde name('admin.') zaten route adına admin. eklediği için route name'i ayrıca admin.admin... şeklinde yazılmaz.

Doğru: Route::delete('source-categories/bulk-destroy', ...)->name('source-categories.bulk-destroy');

Yanlış: Route::delete('/admin/source-categories/bulk-destroy', ...)->name('admin.source-categories.bulk-destroy');

5. Controller katmanı

Controller sade olmalıdır. İş mantığı controller içinde tutulmaz; controller yalnızca request alır, ilgili query/service'i çağırır ve view veya redirect döndürür.

public function index(IndexRequest $request, SourceCategoryQuery $query): View

{

    return view('admin.source-categories.index', $query->forIndex($request->filters()));

}



public function store(StoreRequest $request, SourceCategoryService $service): RedirectResponse

{

    $service->create($request->validated());



    return redirect()->route('admin.source-categories.index')

        ->with('success', 'Kategori oluşturuldu.');

}

Bu düzenin faydası şudur: controller şişmez, test etmek kolaylaşır, başka CRUD'larda aynı iskeleti tekrar kullanmak mümkün olur.

6. FormRequest katmanı

Tüm validasyonlar StoreRequest ve UpdateRequest gibi ayrı FormRequest sınıflarına taşınır. IndexRequest de listeleme parametrelerini temizlemek ve normalize etmek için kullanılır.

authorize(): yetki kontrolü burada ya middleware'e bırakılır ya da policy ile birlikte kullanılır.

prepareForValidation(): boş slug varsa otomatik slug üretmek için kullanılır.

rules(): alan doğrulaması burada tanımlanır.

messages(): hata mesajları burada özelleştirilir.

attributes(): kullanıcıya gösterilecek alan isimleri burada tanımlanır.

Bu mimaride iki yaklaşım vardır: ya admin middleware yetkiyi tamamen çözer ve authorize() true döner, ya da authorize() içinde aynı admin kontrolü tekrar yapılır. İkisini aynı anda yapmana gerek yoktur; tek bir otorite seçmek yeterlidir.

7. Service katmanı

Service sınıfı, veri oluşturma/güncelleme/silme/toplu silme işini yönetir. Burada transaction, veri temizleme, slug üretimi ve ilişki kontrolleri gibi iş kuralları toplanır.

public function create(array $data): SourceCategory

{

    return DB::transaction(function () use ($data) {

        return SourceCategory::create($this->normalize($data));

    });

}



public function bulkDelete(array $ids): int

{

    $ids = collect($ids)->flatten()->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();



    return (int) DB::transaction(function () use ($ids) {

        return SourceCategory::query()->whereKey($ids)->delete();

    });

}

Service katmanının avantajı, aynı entity'yi başka controller'lardan da yönetmek gerekirse iş kurallarının tek yerde kalmasıdır.

8. Query katmanı

Listeleme ile ilgili tüm detaylar Query sınıfında toplanır. Arama, filtre, sıralama, per-page ve pagination window hesaplaması controller'dan çıkarılır.

Query sınıfı, get() yerine forIndex() gibi üst seviye bir method döndürebilir.

Aynı Query sınıfı filtre seçeneklerini ve sıralanabilir kolonları da üretebilir.

Pagination link penceresi de burada hesaplanabilir.

Controller yalnızca request'ten gelen filtre paketini Query'e verir.

public function forIndex(array $filters = []): array

{

    $filters = $this->normalizeFilters($filters);

    $paginator = $this->paginate($filters);



    return [

        'categories' => $paginator,

        'search' => $filters['q'],

        'filter' => $filters['filter'],

        'sort' => $filters['sort'],

        'dir' => $filters['dir'],

        'perPage' => $filters['per_page'],

        'filterOptions' => $this->filterOptions(),

        'pageSizeOptions' => $this->pageSizeOptions(),

        'sortColumns' => $this->sortColumns($filters),

        'summary' => $this->summary($paginator),

        'pagination' => $this->paginationWindow($paginator, $filters),

        'activeFilters' => $this->activeFiltersCount($filters),

        'selectionKey' => 'source-categories',

    ];

}

Önemli: Query sınıfı entity'ye özel filtreleri kapsamalıdır. Örneğin source-categories için 'with-sources' ve 'without-sources' filtreleri burada yaşar.

9. Model katmanı

Model sade kalır. Sadece ilişkiler, fillable alanlar, cast'ler ve küçük accessor'lar modelde tutulur. Bu paket içinde SourceCategory modeline display_initials, avatar_class, description_text ve sources_label gibi yardımcı accessor'lar eklenmiştir.

getRouteKeyName(): slug ile route model binding kullanmak istiyorsan burada tanımlanabilir.

SoftDeletes kullanıyorsan modelde SoftDeletes trait'i olmalıdır.

Accessor'lar, Blade tarafında tekrar eden string hesaplarını azaltır.

10. Blade component sistemi

UI tarafında tekrar eden parçalar component haline getirildi. Bu sayede form inputları, button'lar, card yapıları, modal, flash mesaj ve empty state tekrar yazılmıyor.

x-admin.page-header: sayfa üst başlığı ve aksiyon alanı

x-admin.card: kart kabuğu

x-admin.flash-message: başarı/hata uyarıları

x-admin.modal: silme onayı ve benzeri dialog'lar

x-admin.button: standart buton

x-admin.form.input / textarea / select / checkbox: form elemanları

x-admin.empty-state: veri yok ekranı

Bileşen kullanmanın ana avantajı şudur: bir sayfanın görünümünde değişiklik gerektiğinde tüm CRUD sayfalarını tek tek düzeltmek yerine componenti güncellersin.

11. Index, create ve edit view standardı

Her CRUD için en az üç sayfa standart olmalıdır: index, create ve edit. Index sayfası liste ve toplu işlemler içindir; create ve edit sayfaları ise aynı _form partial'ını kullanmalıdır.

index.blade.php: listeler, arama/filtre/sıralama, bulk delete, pagination

create.blade.php: yeni kayıt formu

edit.blade.php: mevcut kaydı güncelleme formu

_form.blade.php: create ve edit arasında ortak alanlar

Bu yapıda form alanları doğrudan blade içine gömülmez; mümkün oldukça _form partial veya form componentleri kullanılır. Bu, form alanı değiştiğinde iki ayrı dosyayı birden güncelleme ihtiyacını kaldırır.

12. Checkbox seçimi, toplu silme ve seçim state'i

Bu refactor'ün önemli parçalarından biri tablo satırı seçimidir. Kullanıcı checkbox ile satır seçer, üstteki master checkbox tüm görünür satırları seçer veya bırakır.

Seçim durumu sayfa yenilenince kaybolmamalıdır.

Pagination ile başka sayfaya geçince önceki seçimler korunmalıdır.

Seçilen kayıt sayısı badge üzerinde gösterilmelidir.

Toplu sil butonu yalnızca seçim varsa aktif olmalıdır.

Toplu silme işlemi seçilen id'leri modal üzerinden gönderilmelidir.

Bu davranış için JS tarafında sessionStorage kullanmak pratik bir çözümdür. Her tablo için data-selection-key verilir ve seçim listesi o anahtar altında saklanır. Böylece sayfa değişse bile kullanıcı hangi kayıtları seçtiğini kaybetmez.

<table class="data-table" data-selection-table data-selection-key="source-categories">

...

<input type="checkbox" data-master-checkbox>

...

<input type="checkbox" data-row-checkbox data-row-id="{{ $category->id }}">

JS tarafında seçim mantığını ayrı bir fonksiyonda veya modülde tutmak en doğrusudur. Bu modül master checkbox, row checkbox, badge güncellemesi, local/session storage ve bulk delete formuna hidden input basma işini yönetir.

13. Silme onayı modalı

Tekil silme ve toplu silme işlemleri kesinlikle direkt gerçekleşmemelidir. Kullanıcı onay vermeden form submit edilmemeli, bunun yerine modal açılmalıdır.

Tekil silme butonu data-confirm-delete-trigger ile işaretlenir.

Toplu sil butonu data-bulk-delete-trigger ile işaretlenir.

Modalın formu data-confirm-delete-form alır.

Modal içeriği JS ile her silme senaryosunda güncellenir.

Bu yaklaşım Blade ile JS arasındaki sınırı temiz tutar: Blade sadece tetikleyici attribute'larını taşır, modal davranışı JS yönetir.

14. Flash mesajların otomatik kapanması

Başarı ve uyarı mesajlarının birkaç saniye sonra otomatik kaybolması için flash componentlerine data-auto-dismiss verilir. JS bunu okuyup mesajı fade-out ile kaldırır.

<div class="alert alert-success" data-auto-dismiss="4500">

    <div class="alert-body">Kategori oluşturuldu.</div>

    <button type="button" class="close">...</button>

</div>

Bu sayede sayfa her yüklenişinde uyarılar kalıcı olarak ekranda durmaz; kullanıcı deneyimi temiz kalır.

15. Soft delete ve migration mantığı

SourceCategory modelinde SoftDeletes kullanılması, silinen kayıtların geri alınabilmesi veya audit mantığına uygun şekilde saklanabilmesi için faydalıdır.

Model'de SoftDeletes trait'i eklenir.

Veritabanında deleted_at kolonu için migration oluşturulur.

Query tarafında withoutTrashed() ile silinmiş kayıtlar otomatik dışlanabilir.

Unique alanlarda soft-delete dikkate alınacaksa validation kuralı buna göre yazılır.

Not: Soft delete her CRUD için zorunlu değildir. Ancak silinen kayıtları tamamen yok etmek istemiyorsan en doğru yaklaşımdır.

16. Authorization stratejisi

Bu projede admin alanına giriş zaten admin middleware ile korunur. Bu yüzden FormRequest authorize() metodu iki şekilde kullanılabilir:

Basit panellerde authorize() => true; tüm erişim kontrolünü middleware çözer.

Daha sıkı yapılarda authorize() içinde auth()->check() && auth()->user()->isAdmin === 'admin' kontrolü tutulur.

Büyüyen yapılarda Policy/Gate tercih edilir ve yetkiler model bazlı ayrılır.

Pratik öneri: panel giriş yetkisini middleware, model içi aksiyon yetkilerini policy ile ayır. Böylece CRUD mimarisi büyürken kontrol noktaları dağılmaz.

17. Yeni bir CRUD eklerken yapılacaklar

Yeni entity için Controller, Request, Service, Query ve Model dosyalarını oluştur.

Index, create, edit ve _form view dosyalarını aç.

Gerekli componentleri kullan; yeni component gerekiyorsa genel kütüphaneye ekle.

Admin routes içine resource route ve gerekiyorsa bulk route ekle.

Blade tarafında sadece view mantığı kalsın; iş kuralları controller'a taşınmasın.

Arama/filtre/sıralama/pagination logic controller yerine Query'de olsun.

Create/update/delete işlemleri Service'e konsolide olsun.

Yeniden kullanılabilir JS varsa global modüle ekle, sadece entity'ye özel ise özel modüle ekle.

18. Dosya isimlendirme standardı

Tüm CRUD'larda aynı isimlendirme kullanılmalıdır. Böylece başka biri projeye baktığında klasör yapısı sezgisel olur.

EntityController.php

app/Http/Controllers/Admin/SourceCategoryController.php



IndexRequest.php

StoreRequest.php

UpdateRequest.php

BulkDestroyRequest.php



EntityQuery.php

EntityService.php



index.blade.php

create.blade.php

edit.blade.php

_form.blade.php

İsimlerde tekil entity kullanmak en yaygın standarttır. Örnek: SourceCategory, SourceAccount, StoryCluster, RawTweet.

19. En sık yapılan hatalar

Route içinde prefix('admin') varken URL'ye tekrar /admin yazmak.

Route name prefix('admin.') varken route adını tekrar admin... diye çoğaltmak.

Controller içine validation ve sorgu mantığını doldurmak.

Blade içinde çok fazla PHP hesaplaması yapmak.

Selection state'i sayfa değişiminde kaybetmek.

Modal tetikleyicilerinin data-attribute isimlerini JS ile uyuşmaz hale getirmek.

Flash mesajları kalıcı bırakmak.

20. Önerilen operasyonel kontrol listesi

Route listesi doğru mu?

Admin middleware aktif mi?

FormRequest authorize doğru çalışıyor mu?

Query'de arama/filtre/sıralama var mı?

Service transaction kullanıyor mu?

Blade componentleri tekrar ediyor mu?

Checkbox seçimleri sayfa değişince korunuyor mu?

Silme modalı açılıyor mu?

Flash mesaj otomatik kapanıyor mu?

21. Kısa sonuç

Bu mimari, projedeki CRUD ekranlarını kopyala-yapıştır olmaktan çıkarıp düzenli bir kütüphaneye dönüştürmek için tasarlandı. Bir CRUD tamamlandığında bir sonraki CRUD için yalnızca entity ismini değiştirip aynı şablonu uygularsın.

En önemli ilke: controller ince, query ayrı, service ayrı, request ayrı, view componentli ve JS davranışı modüler olmalı.