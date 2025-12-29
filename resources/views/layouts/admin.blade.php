<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - AbsenFinger</title>

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

    @stack('styles')
</head>

<body class="bg-slate-950 text-white min-h-screen antialiased">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-72">
            <!-- Header -->
            <header
                class="sticky top-0 z-30 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/50 overflow-visible">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden p-2 -ml-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <div class="hidden lg:block">
                        <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-3">
                        <!-- Notifications -->
                        <button
                            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                class="absolute top-1.5 right-1.5 w-2 h-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></span>
                        </button>

                        <!-- User Menu with Dropdown -->
                        <div class="relative flex items-center gap-3 pl-3 border-l border-slate-700/50"
                            x-data="{ userDropdown: false }">
                            @php
                                $headerUserName = session('user_name', 'User');
                                $headerUserRoles = session('user_roles', []);
                                $headerUserFoto = session('user_foto');
                                $headerUserInitial = strtoupper(substr($headerUserName, 0, 1)) . strtoupper(substr(explode(' ', $headerUserName)[1] ?? '', 0, 1));
                                $headerRoleLabel = !empty($headerUserRoles) ? $headerUserRoles[0] : 'User';
                            @endphp

                            <!-- Dropdown Trigger -->
                            <button @click="userDropdown = !userDropdown" @click.outside="userDropdown = false"
                                class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
                                <div class="hidden sm:block text-right">
                                    <p class="text-sm font-medium text-white">{{ $headerUserName }}</p>
                                    <p class="text-xs text-slate-400">{{ $headerRoleLabel }}</p>
                                </div>
                                @if($headerUserFoto)
                                    <img src="{{ asset('storage/' . $headerUserFoto) }}" alt="Profile"
                                        class="w-9 h-9 rounded-lg object-cover shadow-lg shadow-purple-500/20">
                                @else
                                    <div
                                        class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm shadow-lg shadow-purple-500/20">
                                        {{ $headerUserInitial ?: 'U' }}
                                    </div>
                                @endif
                                <svg class="w-4 h-4 text-slate-400 transition-transform"
                                    :class="{ 'rotate-180': userDropdown }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userDropdown" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="fixed right-4 w-48 bg-slate-800 rounded-xl shadow-xl border border-slate-700/50 overflow-hidden z-50"
                                style="top: 70px;" x-cloak>

                                <!-- Profile Link -->
                                <a href="{{ route('admin.profile') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>

                                <hr class="border-slate-700/50">

                                <!-- Logout Link -->
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-rose-400 hover:bg-slate-700/50 hover:text-rose-300 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-800/50 px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-sm text-slate-500">
                    &copy; {{ date('Y') }} AbsenFinger. All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>

</html>