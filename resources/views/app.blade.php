<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Portal Berita') }}</title>
    
    <!-- Preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    @vite('resources/css/app.css')
</head>
<body class="font-inter bg-gray-50">
    <div id="app">
        <!-- Vue.js akan di-mount di sini -->
        <app-layout>
            <router-view></router-view>
        </app-layout>
    </div>

    @vite('resources/js/app.js')
    
    <script>
       
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: {!! auth()->check() ? json_encode(auth()->user()) : 'null' !!},
            appName: '{{ config("app.name") }}',
            appUrl: '{{ config("app.url") }}'
        };
    </script>
</body>
</html>