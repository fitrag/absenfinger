<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $settings = \App\Models\Setting::getAllSettings();
        $appName = $settings['system_name'] ?? 'AbsenFinger';
        $schoolName = $settings['school_name'] ?? 'Sekolah';
        $logoPath = $settings['school_logo'] ?? null;
    @endphp
    <title>@yield('title', 'Dashboard') - {{ $appName }}</title>

    <!-- Favicon -->
    @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logoPath) }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-white min-h-screen antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/50"
            x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <a href="{{ route('frontend.home') }}" class="flex items-center gap-2 sm:gap-3">
                        @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $appName }}"
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl object-cover">
                        @else
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center font-bold text-white text-sm sm:text-base shadow-lg shadow-purple-500/20">
                                {{ strtoupper(substr($appName, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h1 class="text-sm sm:text-lg font-bold text-white">{{ $appName }}</h1>
                            <p class="text-xs text-slate-400">{{ $schoolName }}</p>
                        </div>
                    </a>

                    <!-- Desktop Navigation (>= 640px) -->
                    <nav class="hidden sm:flex items-center gap-4 md:gap-6">
                        <a href="{{ route('frontend.home') }}"
                            class="text-sm font-medium {{ request()->routeIs('frontend.home') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">
                            Home
                        </a>
                        <a href="{{ url('/presensi') }}"
                            class="text-sm font-medium {{ request()->is('presensi*') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">
                            Presensi
                        </a>
                        <a href="{{ url('/kesiswaan') }}"
                            class="text-sm font-medium {{ request()->is('kesiswaan*') ? 'text-blue-400' : 'text-slate-400 hover:text-white' }} transition-colors">
                            Kesiswaan
                        </a>
                        <a href="{{ url('/login') }}"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all">
                            Login
                        </a>
                    </nav>

                    <!-- Mobile Menu Button (< 640px) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="sm:hidden p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800/50 transition-colors">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Navigation (< 640px) -->
                <nav x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="sm:hidden pb-4 space-y-1">
                    <a href="{{ route('frontend.home') }}"
                        class="block px-4 py-2.5 text-sm font-medium {{ request()->routeIs('frontend.home') ? 'text-blue-400 bg-blue-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }} rounded-lg transition-colors">
                        Home
                    </a>
                    <a href="{{ url('/presensi') }}"
                        class="block px-4 py-2.5 text-sm font-medium {{ request()->is('presensi*') ? 'text-blue-400 bg-blue-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }} rounded-lg transition-colors">
                        Presensi
                    </a>
                    <a href="{{ url('/kesiswaan') }}"
                        class="block px-4 py-2.5 text-sm font-medium {{ request()->is('kesiswaan*') ? 'text-blue-400 bg-blue-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }} rounded-lg transition-colors">
                        Kesiswaan
                    </a>
                    <a href="{{ url('/login') }}"
                        class="block px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg text-center mt-2">
                        Login
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/50 bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <span class="text-sm font-medium text-slate-400">{{ $appName }}</span>
                    <p class="text-sm text-slate-500">
                        &copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>

</html>