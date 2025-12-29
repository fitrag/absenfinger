<!-- Sidebar -->
@php
    $appSettings = \App\Models\Setting::getAllSettings();
    $systemName = $appSettings['system_name'] ?? 'AbsenFinger';
    $schoolLogo = $appSettings['school_logo'] ?? null;

    // Get user roles and level from session
    $userRoles = session('user_roles', []);
    $userLevel = session('user_level', '');

    // Define role-based access (sesuai nama_role di tabel m_roles)
    $isAdmin = in_array('Admin', $userRoles);
    $isKepsek = in_array('Kepsek', $userRoles);
    $isWaliKelas = in_array('Wali Kelas', $userRoles);
    $isPDS = in_array('PDS', $userRoles) || in_array('BK', $userRoles);
    $isGuru = in_array('Piket', $userRoles) || $userLevel === 'guru'; // Guru dari level atau role Piket
    $isPKL = in_array('PKL', $userRoles); // Role PKL

    // Menu access permissions - Kepsek TIDAK bisa akses Master, Kesiswaan, Adm Guru, Settings
    $canAccessMaster = $isAdmin; // Hanya Admin
    $canAccessPresensi = $isAdmin || $isKepsek || $isWaliKelas; // Admin, Kepsek, Wali Kelas
    $canAccessKesiswaan = $isAdmin || $isPDS; // Hanya Admin dan PDS/BK (bukan Kepsek)
    $canAccessMenuBK = in_array('BK', $userRoles);
    $canAccessAdmGuru = $isAdmin || $isGuru; // Hanya Admin dan Guru (bukan Kepsek)
    $canAccessSettings = $isAdmin; // Hanya Admin (bukan Kepsek)
    $canAccessPKL = $isAdmin || $isPKL; // Admin dan role PKL
@endphp
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed top-0 left-0 z-50 w-72 h-full bg-slate-900/95 backdrop-blur-xl border-r border-slate-800/50 transition-transform duration-300 ease-in-out lg:translate-x-0">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800/50">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3">
                @if($schoolLogo)
                    <img src="{{ asset('storage/' . $schoolLogo) }}" alt="Logo"
                        class="w-10 h-10 object-contain rounded-xl bg-white p-1 shadow-lg">
                @else
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-bold text-white">{{ $systemName }}</h1>
                    <p class="text-xs text-slate-400 -mt-0.5">Admin Panel</p>
                </div>
            </a>
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ url('/admin/dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/dashboard') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/dashboard') ? 'text-blue-400' : '' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
                @if(request()->is('admin/dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gradient-to-r from-blue-400 to-purple-400"></span>
                @endif
            </a>

            @if($canAccessMaster)
                <!-- Data Master (Dropdown) -->
                <div
                    x-data="{ open: {{ request()->is('admin/students*') || request()->is('admin/kelas*') || request()->is('admin/jurusan*') || (request()->is('admin/guru*') && !request()->is('admin/guru-piket*')) || request()->is('admin/role*') || request()->is('admin/mapel*') || request()->is('admin/guruajar*') || request()->is('admin/walas*') || request()->is('admin/tp*') || request()->is('admin/users*') || request()->is('admin/user-guru*') || request()->is('admin/user-siswa*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                          {{ request()->is('admin/students*') || request()->is('admin/kelas*') || request()->is('admin/jurusan*') || (request()->is('admin/guru*') && !request()->is('admin/guru-piket*')) || request()->is('admin/role*') || request()->is('admin/mapel*') || request()->is('admin/guruajar*') || request()->is('admin/walas*') || request()->is('admin/tp*') || request()->is('admin/users*') || request()->is('admin/user-guru*') || request()->is('admin/user-siswa*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ request()->is('admin/students*') || request()->is('admin/kelas*') || request()->is('admin/jurusan*') || (request()->is('admin/guru*') && !request()->is('admin/guru-piket*')) || request()->is('admin/role*') || request()->is('admin/mapel*') || request()->is('admin/guruajar*') || request()->is('admin/walas*') || request()->is('admin/tp*') || request()->is('admin/users*') || request()->is('admin/user-guru*') || request()->is('admin/user-siswa*') ? 'text-blue-400' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <span>Data Master</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1">
                        <!-- Students -->
                        <a href="{{ url('/admin/students') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/students*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Siswa</span>
                        </a>
                        <!-- Guru -->
                        <a href="{{ url('/admin/guru') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/guru') || request()->is('admin/guru/*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Guru</span>
                        </a>
                        <!-- Guru Mengajar -->
                        <a href="{{ url('/admin/guruajar') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/guruajar*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            <span>Guru Mengajar</span>
                        </a>
                        <!-- Wali Kelas -->
                        <a href="{{ url('/admin/walas') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/walas*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Wali Kelas</span>
                        </a>
                        <!-- Kelas -->
                        <a href="{{ url('/admin/kelas') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/kelas*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Kelas</span>
                        </a>
                        <!-- Jurusan -->
                        <a href="{{ url('/admin/jurusan') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/jurusan*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Jurusan</span>
                        </a>
                        <!-- Mapel -->
                        <a href="{{ url('/admin/mapel') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/mapel*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Mapel</span>
                        </a>
                        <!-- Role -->
                        <a href="{{ url('/admin/role') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/role*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Role</span>
                        </a>
                        <!-- Tahun Pelajaran -->
                        <a href="{{ url('/admin/tp') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                              {{ request()->is('admin/tp*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Tahun Pelajaran</span>
                        </a>
                        <!-- Users -->
                        <!-- Users (Dropdown) -->
                        <div
                            x-data="{ userOpen: {{ request()->is('admin/users*') || request()->is('admin/user-guru*') || request()->is('admin/user-siswa*') ? 'true' : 'false' }} }">
                            <button @click="userOpen = !userOpen"
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                              {{ request()->is('admin/users*') || request()->is('admin/user-guru*') || request()->is('admin/user-siswa*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Users</span>
                                </div>
                                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': userOpen }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="userOpen" x-collapse class="mt-1 ml-4 space-y-1">
                                <!-- Users Guru -->
                                <a href="{{ route('admin.users.guru') }}"
                                    class="flex items-center gap-3 ml-8 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                      {{ request()->routeIs('admin.users.guru') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                    <span>User Guru</span>
                                </a>
                                <!-- Users Siswa -->
                                <a href="{{ route('admin.users.siswa') }}"
                                    class="flex items-center gap-3 ml-8 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                      {{ request()->routeIs('admin.users.siswa') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                    <span>User Siswa</span>
                                </a>
                                <!-- Users All -->
                                <a href="{{ route('admin.users.index') }}"
                                    class="flex items-center gap-3 ml-8 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                          {{ request()->is('admin/users') && !request()->query('level') ? 'text-blue-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                    <span>Semua User</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($canAccessPresensi)
                <!-- Section: Attendance -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Presensi</p>
                </div>

                <!-- Attendance Records -->
                <a href="{{ url('/admin/attendance') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                      {{ request()->is('admin/attendance*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/attendance*') ? 'text-blue-400' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Presensi</span>
                </a>

                <!-- Reports (Dropdown) -->
                <div x-data="{ open: {{ request()->is('admin/reports*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/reports*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ request()->is('admin/reports*') ? 'text-blue-400' : '' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Reports</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1">
                        <a href="{{ route('admin.reports.daily') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                              {{ request()->is('admin/reports/daily*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Per-tanggal</span>
                        </a>
                        <a href="{{ route('admin.reports.monthly') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                              {{ request()->is('admin/reports/monthly*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Perbulan</span>
                        </a>
                    </div>
                </div>
            @endif

            @if($canAccessKesiswaan)
                <!-- Section: Kesiswaan -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kesiswaan</p>
                </div>

                <!-- PDS Kesiswaan (Dropdown) -->
                <div x-data="{ open: {{ request()->is('admin/kesiswaan*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                      {{ request()->is('admin/kesiswaan*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ request()->is('admin/kesiswaan*') ? 'text-blue-400' : '' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>PDS Kesiswaan</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1">
                        <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/kesiswaan/siswa-terlambat*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Siswa Terlambat</span>
                        </a>
                        <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/kesiswaan/pelanggaran*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Pelanggaran</span>
                        </a>

                        <a href="{{ route('admin.kesiswaan.konseling.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/kesiswaan/konseling*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Konseling</span>
                        </a>
                    </div>
                </div>
            @endif

            @if($canAccessAdmGuru && !$isKepsek)
                <!-- Section: Adm Guru -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Adm Guru</p>
                </div>

                <!-- PDS Guru (Dropdown) -->
                <div
                    x-data="{ open: {{ request()->is('admin/guru*') && !request()->is('admin/guru-piket*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                                                                                                                                      {{ request()->is('admin/guru*') && !request()->is('admin/guru-piket*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ request()->is('admin/guru*') && !request()->is('admin/guru-piket*') ? 'text-blue-400' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Adm Guru</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1">
                        <a href="{{ url('/admin/guru/jurnal') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/guru/jurnal*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4 {{ request()->is('admin/guru/jurnal*') ? 'text-blue-400' : 'text-slate-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Jurnal</span>
                        </a>

                        <a href="{{ url('/admin/guru/nilai') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                          {{ request()->is('admin/guru/nilai*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4 {{ request()->is('admin/guru/nilai*') ? 'text-blue-400' : 'text-slate-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Nilai Harian</span>
                        </a>
                    </div>
                </div>
            @endif

            @if($isAdmin || $isKepsek)
                <!-- Section: Guru Piket -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Guru Piket</p>
                </div>
                <!-- Ketidakhadiran Guru -->
                <a href="{{ route('admin.guru-piket.ketidakhadiran') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                      {{ request()->is('admin/guru-piket/ketidakhadiran*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/guru-piket/ketidakhadiran*') ? 'text-blue-400' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Ketidakhadiran Guru</span>
                </a>
            @endif

            @if($canAccessPKL)
                <!-- Section: PKL -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">PKL</p>
                </div>
                <!-- PKL Dropdown -->
                <div
                    x-data="{ open: {{ request()->is('admin/pkl*') || request()->is('admin/dudi*') || request()->is('admin/sertifikat*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                              {{ request()->is('admin/pkl*') || request()->is('admin/dudi*') || request()->is('admin/sertifikat*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ request()->is('admin/pkl*') || request()->is('admin/dudi*') || request()->is('admin/sertifikat*') ? 'text-blue-400' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Manajemen PKL</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1">
                        <!-- Data Dudi -->
                        <a href="{{ url('/admin/dudi') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                              {{ request()->is('admin/dudi*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Data Dudi</span>
                        </a>
                        <!-- Data PKL -->
                        <a href="{{ url('/admin/pkl') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                              {{ request()->is('admin/pkl*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Data PKL</span>
                        </a>
                        <!-- Sertifikat -->
                        <a href="{{ url('/admin/sertifikat') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer
                                                              {{ request()->is('admin/sertifikat*') ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <span>Sertifikat</span>
                        </a>
                    </div>
                </div>
            @endif

            @if($canAccessMenuBK)
                <!-- Section: Menu BK -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu BK</p>
                </div>

                <!-- Siswa Terlambat -->
                <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                  {{ request()->is('admin/kesiswaan/siswa-terlambat*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/kesiswaan/siswa-terlambat*') ? 'text-blue-400' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Siswa Terlambat</span>
                </a>

                <!-- Konseling -->
                <a href="{{ route('admin.kesiswaan.konseling.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                  {{ request()->is('admin/kesiswaan/konseling*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/kesiswaan/konseling*') ? 'text-blue-400' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Konseling</span>
                </a>

                <!-- Pelanggaran -->
                <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                  {{ request()->is('admin/kesiswaan/pelanggaran*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/kesiswaan/pelanggaran*') ? 'text-blue-400' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Pelanggaran</span>
                </a>
            @endif

            @if($canAccessSettings)
                <!-- Section: Settings -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Settings</p>
                </div> <!-- Settings -->
                <a href="{{ url('/admin/settings') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                                                                                                                                                                  {{ request()->is('admin/settings*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-5 h-5 {{ request()->is('admin/settings*') ? 'text-blue-400' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Settings</span>
                </a>
            @endif
        </nav>

        <!-- User Section at Bottom -->
        <div class="p-4 border-t border-slate-800/50">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/30">
                @php
                    $userName = session('user_name', 'User');
                    $userUsername = session('user_username', 'user');
                    $userRolesDisplay = session('user_roles', []);
                    $userInitial = strtoupper(substr($userName, 0, 1)) . strtoupper(substr(explode(' ', $userName)[1] ?? '', 0, 1));
                    $userRoleLabel = !empty($userRolesDisplay) ? implode(', ', $userRolesDisplay) : 'User';
                @endphp
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-purple-500/20">
                    {{ $userInitial ?: 'U' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $userName }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $userRoleLabel }}</p>
                </div>
                <a href="{{ url('/logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-700/50 transition-colors cursor-pointer"
                    title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</aside>