<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel')</title>

    <script>
        (function() {
            try {
                var saved = localStorage.getItem('dash26-theme');
                document.documentElement.setAttribute('data-theme', saved || 'light');
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('admin-assets/css/styles.css') }}">
    @stack('css')

    <script defer src="{{ asset('admin-assets/js/app.js') }}"></script>
</head>
<body>
    @yield('content')
    @stack('js')
</body>
</html>