<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="font-inter bg-gray-100 min-h-screen">
    <!-- Sidebar -->
    <div class="flex">
        <div class="w-64 bg-blue-800 text-white min-h-screen">
            <div class="p-4">
                <h1 class="text-xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.posts.index') }}" class="block py-2 px-4 {{ request()->routeIs('admin.posts.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                    Posts
                </a>
                <a href="{{ route('admin.comments.index') }}" class="block py-2 px-4 {{ request()->routeIs('admin.comments.*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}">
                    Comments
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left py-2 px-4 hover:bg-blue-700">
                        Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>