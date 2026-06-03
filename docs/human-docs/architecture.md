# Admin CRUD refactor blueprint

Bu paket yalnızca `source-categories` CRUD'ını tam refactor eder ve aynı mimariyi diğer CRUD'lara kopyalayabilmen için bir şablon bırakır.

## Önerilen klasör yapısı

```text
app/
  Http/
    Controllers/
      Admin/
        SourceCategoryController.php
        SourceAccountController.php
        RawTweetController.php
        StoryClusterController.php
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
```

## Akış

- Controller kısa kalır.
- Validasyon FormRequest içinde olur.
- Listeleme ve filtreleme Query sınıfında olur.
- CRUD iş kuralları Service katmanında olur.
- UI tekrarları Blade componentleri ile yönetilir.
- Checkbox seçimi ve bulk delete state'i JS tarafında saklanır.
```

