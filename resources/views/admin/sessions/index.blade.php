@extends('layouts.admin')

@section('title', 'Monitor Sesi Pengguna')

@section('content')
    <div class="space-y-6" x-data="sessionMonitor()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Monitor Sesi Pengguna</h1>
                <p class="text-slate-400 text-sm mt-1">Pantau aktivitas dan status online pengguna</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-2 px-3 py-2 bg-emerald-500/20 border border-emerald-500/30 rounded-xl">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 font-medium text-sm"
                        x-text="onlineCount + ' Online'">{{ $stats['onlineNow'] }} Online</span>
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white">{{ $stats['totalStaff'] }}</p>
                        <p class="text-xs text-slate-400">Guru & Admin</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white">{{ $stats['totalSiswa'] }}</p>
                        <p class="text-xs text-slate-400">Total Siswa</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white" x-text="onlineCount">{{ $stats['onlineNow'] }}</p>
                        <p class="text-xs text-slate-400">Online</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white">{{ $stats['todayLogins'] }}</p>
                        <p class="text-xs text-slate-400">Login Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.sessions.index', array_merge(request()->except('tab'), ['tab' => 'staff'])) }}"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200
                        {{ $activeTab === 'staff' ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'bg-slate-800/50 text-slate-400 hover:text-white hover:bg-slate-700/50 border border-slate-700/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Guru & Admin
                    <span
                        class="px-1.5 py-0.5 text-xs rounded-md {{ $activeTab === 'staff' ? 'bg-white/20' : 'bg-slate-700' }}">
                        {{ $stats['staffOnline'] }} online
                    </span>
                </span>
            </a>
            <a href="{{ route('admin.sessions.index', array_merge(request()->except('tab'), ['tab' => 'siswa'])) }}"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200
                        {{ $activeTab === 'siswa' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg' : 'bg-slate-800/50 text-slate-400 hover:text-white hover:bg-slate-700/50 border border-slate-700/50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Siswa
                    <span
                        class="px-1.5 py-0.5 text-xs rounded-md {{ $activeTab === 'siswa' ? 'bg-white/20' : 'bg-slate-700' }}">
                        {{ $stats['siswaOnline'] }} online
                    </span>
                </span>
            </a>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" action="{{ route('admin.sessions.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                @if($activeTab === 'siswa')
                    <select name="kelas_id" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nm_kls }}
                            </option>
                        @endforeach
                    </select>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <option value="36" {{ $perPage == 36 ? 'selected' : '' }}>36 / halaman</option>
                        <option value="72" {{ $perPage == 72 ? 'selected' : '' }}>72 / halaman</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / halaman</option>
                    </select>
                @endif
                <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                    <option value="">Semua Status</option>
                    <option value="online" {{ $statusFilter === 'online' ? 'selected' : '' }}>🟢 Online</option>
                    <option value="offline" {{ $statusFilter === 'offline' ? 'selected' : '' }}>⚫ Offline</option>
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/username..."
                    class="w-48 px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors cursor-pointer text-sm">
                    Cari
                </button>
                @if($search || $statusFilter || $kelasId)
                    <a href="{{ route('admin.sessions.index', ['tab' => $activeTab]) }}"
                        class="px-4 py-2 bg-slate-700 text-slate-300 font-medium rounded-lg hover:bg-slate-600 transition-colors text-sm">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tab Content: Staff (Guru & Admin) -->
        @if($activeTab === 'staff')
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Guru & Admin
                    </h3>
                    <span class="text-xs text-slate-400">{{ $staffUsers->count() }} pengguna</span>
                </div>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-slate-800 z-10">
                            <tr class="border-b border-slate-700/50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Pengguna</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Level</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Perangkat</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Aktivitas
                                    Terakhir</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($staffUsers as $index => $user)
                                @include('admin.sessions._user_row', ['user' => $user, 'loop' => $loop])
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        Tidak ada data guru/admin
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tab Content: Siswa -->
        @if($activeTab === 'siswa')
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Siswa
                    </h3>
                    <span class="text-xs text-slate-400">{{ $siswaUsers->total() }} pengguna</span>
                </div>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-slate-800 z-10">
                            <tr class="border-b border-slate-700/50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Pengguna</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Perangkat</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Aktivitas
                                    Terakhir</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($siswaUsers as $index => $user)
                                @php
                                    $session = $user->latestSession;
                                    $isOnline = $session &&
                                        $session->is_online &&
                                        $session->last_activity &&
                                        $session->last_activity->diffInMinutes(now()) < 5;
                                @endphp
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                @if($user->foto)
                                                    <img src="{{ asset('storage/' . $user->foto) }}"
                                                        class="w-10 h-10 rounded-lg object-cover" alt="">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-slate-800 
                                                            {{ $isOnline ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                                <p class="text-xs text-slate-400">{{ $user->username }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-slate-300">
                                        {{ $user->student->kelas->nm_kls ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if($isOnline)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Online
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                                Offline
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-slate-300">
                                        {{ $session->device_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-slate-300">
                                        {{ $session->location ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-slate-400">
                                        @if($session && $session->last_activity)
                                            {{ $session->last_activity->diffForHumans() }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.sessions.activities', $user->id) }}"
                                                class="p-2 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-lg transition-colors"
                                                title="Lihat Aktivitas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if($session && !$session->logged_out_at)
                                                <form action="{{ route('admin.sessions.forceLogout', $session->id) }}" method="POST"
                                                    onsubmit="return confirm('Logout paksa sesi ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 bg-red-500/20 text-red-400 hover:bg-red-500/30 rounded-lg transition-colors cursor-pointer"
                                                        title="Force Logout">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        Tidak ada data siswa
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            <!-- Pagination -->
                    @if($siswaUsers->hasPages())
                        <div class="px-4 py-3 border-t border-slate-700/50 flex items-center justify-between">
                            <div class="text-sm text-slate-400">
                                Menampilkan {{ $siswaUsers->firstItem() }} - {{ $siswaUsers->lastItem() }} dari {{ $siswaUsers->total() }} siswa
                            </div>
                            <div>
                                {{ $siswaUsers->links() }}
                            </div>
                        </div>
                    @endif
                </div>
        @endif
        </div>

        <script>
            function sessionMonitor() {
                return {
                    onlineCount: {{ $stats['onlineNow'] }},

                    init() {
                        // Poll for online users every 30 seconds
                        setInterval(() => this.fetchOnlineCount(), 30000);
                    },

                    async fetchOnlineCount() {
                        try {
                            const response = await fetch('{{ route("admin.sessions.online") }}');
                            const data = await response.json();
                            this.onlineCount = data.count;
                        } catch (error) {
                            console.error('Failed to fetch online users:', error);
                        }
                    }
                }
            }
        </script>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
@endsection
