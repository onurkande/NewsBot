# Adminator static template

Bu klasör npm olmadan çalışan, tek başına açılabilen bir HTML/CSS/JS sürümüdür.

Dosyalar:
- `index.html` → dashboard
- `forms.html` → form örnekleri
- `ui-elements.html` → UI bileşenleri
- Diğer HTML dosyaları → menü linklerinin bozulmaması için hafif demo sayfalar

Not:
- Tema geçişi localStorage ile çalışır.
- Sidebar, dropdown, accordion, alert kapatma, sekmeler ve mobil drawer JavaScript ile çalışır.
- Laravel'e taşımak için `assets/` klasörünü public altına alıp HTML parçalarını Blade layout'a bölebilirsin.