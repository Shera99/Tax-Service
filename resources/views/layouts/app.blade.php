<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tax Service') - Панель управления</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center h-16 bg-slate-900">
                <span class="text-white text-xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>Tax Service
                </span>
            </div>

            <nav class="mt-6">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span>Статистика</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-6 py-3 text-gray-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('users.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-users w-5 mr-3"></i>
                        <span>Пользователи</span>
                    </a>

                    <a href="{{ route('api-keys.index') }}"
                        class="flex items-center px-6 py-3 text-gray-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('api-keys.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-key w-5 mr-3"></i>
                        <span>API Ключи</span>
                    </a>
                @endif
            </nav>

            <div class="absolute bottom-0 w-full p-4 border-t border-slate-700">
                <div class="flex items-center text-gray-300">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->getRoleName() }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navbar -->
            <header class="bg-white shadow-sm z-40">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex-1 lg:flex-none"></div>

                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600 text-sm hidden sm:block">{{ auth()->user()->email }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="hidden sm:inline ml-1">Выход</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Overlay for mobile sidebar -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden" x-cloak></div>
    </div>
</body>

</html>