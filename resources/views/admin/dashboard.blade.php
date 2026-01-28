@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Guru Piket Popup --}}
    @if(isset($todayGuruPiket) && $todayGuruPiket->count() > 0)
        <div x-data="{ show: true }" x-show="show" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4" class="fixed top-24 right-4 z-50 w-72 max-w-full">
            <div
                class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border border-slate-200/50 dark:border-slate-700/50 shadow-xl rounded-2xl overflow-hidden ring-1 ring-slate-900/5">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="text-white font-semibold text-sm drop-shadow-sm">Guru Piket Hari Ini</h3>
                    </div>
                    <button @click="show = false" class="text-white/70 hover:text-white transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- List -->
                <div class="p-3 space-y-3 max-h-80 overflow-y-auto custom-scrollbar bg-slate-50/50 dark:bg-slate-900/20">
                    @foreach($todayGuruPiket as $piket)
                        @if(!$piket->guru) @continue @endif
                        @php
                            $guru = $piket->guru;
                            $foto = $guru->user->foto ?? null;
                            $nama = $guru->nama ?? 'Unknown';
                            $inisial = strtoupper(substr($nama, 0, 1) . substr(explode(' ', $nama)[1] ?? '', 0, 1));
                        @endphp
                        <div
                            class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/60 dark:hover:bg-slate-700/40 transition-colors border border-transparent hover:border-slate-200/50 dark:hover:border-slate-700/50">
                            @if($foto)
                                <img src="{{ asset('storage/' . $foto) }}"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-slate-700 shadow-sm"
                                    alt="{{ $nama }}">
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs border-2 border-white dark:border-slate-700 shadow-sm">
                                    {{ $inisial }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 dark:text-slate-100 text-xs sm:text-sm truncate">
                                    {{ $nama }}
                                </p>
                                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">NIP.
                                    {{ $guru->nip ?? '-' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Footer decorative -->
                <div class="h-1 w-full bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 opacity-80"></div>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-6 sm:p-8">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\" 60\" height=\"60\" viewBox=\"0 0 60
                60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg
                fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36
                34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6
                4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
            <div class="relative">
                @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo))
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-sm text-white font-medium">
                            Wali Kelas {{ $kelasInfo->nm_kls }}
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Selamat Datang, {{ session('user_name') }}! 👋
                    </h2>
                    <p class="text-blue-100 text-sm sm:text-base max-w-xl">
                        Pantau kehadiran siswa kelas <strong>{{ $kelasInfo->nm_kls }}</strong> dengan mudah. Data di bawah ini
                        khusus untuk kelas Anda.
                    </p>
                @else
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Selamat Datang, {{ session('user_name') }}! 👋
                    </h2>
                    <p class="text-blue-100 text-sm sm:text-base max-w-xl">
                        Kelola sistem absensi fingerprint siswa dengan mudah. Pantau kehadiran dan lihat laporan secara
                        real-time.
                    </p>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Students -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-blue-500/30 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-bl-full">
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Total Siswa</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalStudents) }}</p>
                        <p class="text-sm text-slate-400 mt-1">
                            @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo))
                                Kelas {{ $kelasInfo->nm_kls }}
                            @else
                                Siswa aktif
                            @endif
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Present Today -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-emerald-500/30 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-500/10 to-transparent rounded-bl-full">
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Hadir Hari Ini</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($presentToday) }}</p>
                        <p class="text-sm text-slate-400 mt-1">
                            @if($totalStudents > 0)
                                <span class="text-emerald-400">{{ round(($presentToday / $totalStudents) * 100) }}%</span>
                                kehadiran
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Checked Out -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-amber-500/30 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-500/10 to-transparent rounded-bl-full">
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Sudah Pulang</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($checkOutToday) }}</p>
                        <p class="text-sm text-amber-400 mt-1">
                            @if($presentToday > 0)
                                {{ round(($checkOutToday / $presentToday) * 100) }}% dari hadir
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Absent Today -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-rose-500/30 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-rose-500/10 to-transparent rounded-bl-full">
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Tidak Hadir</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ number_format($absentToday) }}</p>
                        <p class="text-sm text-rose-400 mt-1">
                            @if($totalStudents > 0)
                                {{ round(($absentToday / $totalStudents) * 100) }}% dari total
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo) && isset($siswaTerlambat))
                <!-- Siswa Terlambat (for Wali Kelas) -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Siswa Terlambat</h3>
                            <a href="{{ url('/admin/kesiswaan/siswa-terlambat') }}"
                                class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                Lihat semua →
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse($siswaTerlambat as $terlambat)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-600 to-orange-700 flex items-center justify-center text-white font-medium text-sm">
                                    @if($terlambat->student)
                                        {{ strtoupper(substr($terlambat->student->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $terlambat->student->name)[1] ?? '', 0, 1)) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $terlambat->student->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Total Terlambat: <span
                                            class="text-amber-400 font-bold">{{ $terlambat->total_terlambat }}x</span>
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    {{ $terlambat->total_menit }} m
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-slate-400 text-sm">Tidak ada siswa terlambat</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Siswa Konseling (for Wali Kelas) -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden mt-6">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Siswa Konseling</h3>
                            <a href="{{ route('admin.kesiswaan-view.konseling') }}"
                                class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                Lihat semua →
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse($siswaKonseling ?? [] as $konseling)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-700 flex items-center justify-center text-white font-medium text-sm">
                                    @if($konseling->student)
                                        {{ strtoupper(substr($konseling->student->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $konseling->student->name)[1] ?? '', 0, 1)) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $konseling->student->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-300 truncate" title="{{ $konseling->permasalahan }}">
                                        {{ $konseling->permasalahan }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $konseling->tanggal->format('d M Y') }}
                                    </p>
                                </div>
                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $konseling->status_badge }}">
                                        {{ $konseling->status_label }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-slate-400 text-sm">Tidak ada data konseling</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <!-- Recent Attendance (for Admin) -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Kehadiran Terbaru</h3>
                            @if(session('user_level') !== 'guru' && session('user_level') !== 'siswa')
                                <a href="{{ url('/admin/attendance') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse($recentAttendances as $attendance)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $attendance->checktype == 0 ? 'from-emerald-600 to-emerald-700' : 'from-amber-600 to-amber-700' }} flex items-center justify-center text-white font-medium text-sm">
                                    @if($attendance->student)
                                        {{ strtoupper(substr($attendance->student->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $attendance->student->name)[1] ?? '', 0, 1)) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $attendance->student->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $attendance->student->class ?? '-' }} • {{ $attendance->checktime->format('H:i') }}
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $attendance->checktype == 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $attendance->checktype == 0 ? 'Masuk' : 'Pulang' }}
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-slate-400 text-sm">Belum ada data kehadiran hari ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Quick Actions / Pelanggaran Siswa -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hiddenself-start">
                <div class="p-6 border-b border-slate-800/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">
                            @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo))
                                Pelanggaran Siswa
                            @else
                                Kehadiran Mingguan
                            @endif
                        </h3>
                        @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo))
                            <a href="{{ route('admin.kesiswaan-view.pelanggaran') }}"
                                class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                Lihat semua →
                            </a>
                        @else
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5" title="Hadir & Lengkap">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-[10px] text-slate-400">Hadir</span>
                                </div>
                                <div class="flex items-center gap-1.5" title="Check-in tapi belum/tidak check-out">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span class="text-[10px] text-slate-400">Bolos</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span class="text-[10px] text-slate-400">Sakit</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                    <span class="text-[10px] text-slate-400">Izin</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                    <span class="text-[10px] text-slate-400">Alpha</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if(isset($isWaliKelas) && $isWaliKelas && isset($kelasInfo))
                    <!-- Pelanggaran Siswa List -->
                    <div class="divide-y divide-slate-800/50">
                        @forelse($pelanggaranSiswa ?? [] as $item)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-600 to-red-700 flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($item['student']->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $item['student']->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-300 truncate"
                                        title="{{ implode(', ', $item['jenis_pelanggaran']) }}">
                                        {{ implode(', ', $item['jenis_pelanggaran']) }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $item['student']->nis ?? '-' }} • {{ $item['jumlah_pelanggaran'] }} pelanggaran
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @if($item['total_poin'] >= 50) bg-red-500/20 text-red-400 border border-red-500/30
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @elseif($item['total_poin'] >= 25) bg-amber-500/20 text-amber-400 border border-amber-500/30
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @else bg-slate-500/20 text-slate-400 border border-slate-500/30 @endif">
                                        {{ $item['total_poin'] }} poin
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-emerald-600 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-slate-400 text-sm">Tidak ada siswa dengan pelanggaran</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Weekday Attendance Chart (Mon-Fri) with Donut Visualization -->
                    <div class="p-4">
                        <div class="grid grid-cols-5 gap-3">
                            @foreach($weekdayData ?? [] as $index => $data)
                                <div class="flex flex-col items-center">
                                    <div id="weekdayDonut{{ $index }}" style="min-height: 100px; width: 100%;"></div>
                                    <span class="text-xs text-slate-400 mt-1 font-medium">{{ substr($data['day'], 0, 3) }}</span>
                                    <span class="text-sm font-bold text-emerald-400">{{ $data['hadir'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Weekly Chart -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Statistik Kehadiran 7 Hari Terakhir</h3>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5" title="Hadir & Lengkap">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] text-slate-400">Hadir</span>
                        </div>
                        <div class="flex items-center gap-1.5" title="Check-in tapi belum/tidak check-out">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-[10px] text-slate-400">Bolos</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-[10px] text-slate-400">Sakit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <span class="text-[10px] text-slate-400">Izin</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-[10px] text-slate-400">Alpha</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Curved Line Chart -->
                <div id="weeklyAttendanceChart" style="height: 280px;"></div>
            </div>
        </div>

        @if(!isset($isWaliKelas) || !$isWaliKelas)
            <!-- Admin Dashboard: Student Issues Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Siswa Terlambat -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Siswa Terlambat</h3>
                            @if(session('user_level') === 'admin')
                                <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @elseif(in_array('Kepsek', session('user_roles', [])))
                                <a href="{{ route('admin.kesiswaan-view.siswa-terlambat') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat selengkapnya →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse(collect($siswaTerlambat ?? [])->take(5) as $terlambat)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-600 to-orange-700 flex items-center justify-center text-white font-medium text-sm flex-shrink-0">
                                    @if($terlambat->student)
                                        {{ strtoupper(substr($terlambat->student->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $terlambat->student->name)[1] ?? '', 0, 1)) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $terlambat->student->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $terlambat->student->kelas->nm_kls ?? '-' }} •
                                        <span class="text-amber-400 font-bold">{{ $terlambat->total_terlambat }}x</span> Terlambat
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20 flex-shrink-0">
                                    {{ $terlambat->total_menit }} m
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <p class="text-slate-400 text-sm">Tidak ada siswa terlambat</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Siswa Konseling -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Siswa Konseling</h3>
                            @if(session('user_level') === 'admin')
                                <a href="{{ route('admin.kesiswaan.konseling.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @elseif(in_array('Kepsek', session('user_roles', [])))
                                <a href="{{ route('admin.kesiswaan-view.konseling') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat selengkapnya →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse(collect($siswaKonseling ?? [])->take(5) as $konseling)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-blue-700 flex items-center justify-center text-white font-medium text-sm flex-shrink-0">
                                    @if($konseling->student)
                                        {{ strtoupper(substr($konseling->student->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $konseling->student->name)[1] ?? '', 0, 1)) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $konseling->student->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-300 truncate" title="{{ $konseling->permasalahan }}">
                                        {{ $konseling->permasalahan }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $konseling->student->kelas->nm_kls ?? '-' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $konseling->status_badge }}">
                                        {{ $konseling->status_label }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <p class="text-slate-400 text-sm">Tidak ada data konseling</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pelanggaran Siswa -->
                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">Pelanggaran Siswa</h3>
                            @if(session('user_level') === 'admin')
                                <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @elseif(in_array('Kepsek', session('user_roles', [])))
                                <a href="{{ route('admin.kesiswaan-view.pelanggaran') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat selengkapnya →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @forelse(collect($pelanggaranSiswa ?? [])->take(5) as $item)
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-600 to-red-700 flex items-center justify-center text-white font-medium text-sm flex-shrink-0">
                                    {{ strtoupper(substr($item['student']->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">
                                        {{ $item['student']->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-slate-300 truncate"
                                        title="{{ implode(', ', $item['jenis_pelanggaran']) }}">
                                        {{ implode(', ', $item['jenis_pelanggaran']) }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $item['student']->kelas->nm_kls ?? '-' }} • {{ $item['jumlah_pelanggaran'] }} x
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold 
                                                                                                                                                                                                                                                                                                                                                                                                                                                    @if($item['total_poin'] >= 50) bg-red-500/20 text-red-400 border border-red-500/30
                                                                                                                                                                                                                                                                                                                                                                                                                                                    @elseif($item['total_poin'] >= 25) bg-amber-500/20 text-amber-400 border border-amber-500/30
                                                                                                                                                                                                                                                                                                                                                                                                                                                    @else bg-slate-500/20 text-slate-400 border border-slate-500/30 @endif">
                                        {{ $item['total_poin'] }} Poin
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <p class="text-slate-400 text-sm">Tidak ada pelanggaran</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Late Student, Violation & Counseling Trend Line Chart -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden col-span-1 lg:col-span-3">
                <div class="p-6 border-b border-slate-800/50">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <h3 class="text-lg font-semibold text-white">Perkembangan Kesiswaan - Semester Genap (Jan - Jun)</h3>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full bg-gradient-to-r from-amber-500 to-orange-500"></span>
                                <span class="text-[10px] text-slate-400">Terlambat</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-4 h-0.5" style="border-top: 2px dashed #f43f5e;"></span>
                                <span class="text-[10px] text-slate-400">Pelanggaran</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-4 h-0.5" style="border-top: 2px dotted #6366f1;"></span>
                                <span class="text-[10px] text-slate-400">Konseling</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        // Find max value from all datasets for proper scaling
                        $maxValue = 1;
                        foreach ($lateTrendData ?? [] as $data) {
                            if ($data['count'] > $maxValue)
                                $maxValue = $data['count'];
                        }
                        foreach ($violationTrendData ?? [] as $data) {
                            if ($data['count'] > $maxValue)
                                $maxValue = $data['count'];
                        }
                        foreach ($counselingTrendData ?? [] as $data) {
                            if ($data['count'] > $maxValue)
                                $maxValue = $data['count'];
                        }
                        $chartHeight = 200;
                    @endphp

                    <!-- Line Chart Container -->
                    <div class="relative" style="height: {{ $chartHeight }}px;">
                        <!-- Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                            @for($i = 0; $i <= 4; $i++)
                                <div class="flex items-center w-full">
                                    <span
                                        class="text-xs text-slate-500 w-8 text-right pr-2">{{ round($maxValue - ($maxValue / 4 * $i)) }}</span>
                                    <div class="flex-1 border-t border-slate-700/30 {{ $i == 4 ? 'border-slate-700' : '' }}"></div>
                                </div>
                            @endfor
                        </div>

                        <!-- SVG Line Chart -->
                        <div class="ml-10 h-full relative">
                            <svg class="w-full h-full" viewBox="0 0 700 200" preserveAspectRatio="none">
                                <!-- Gradient Definition -->
                                <defs>
                                    <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#f59e0b" />
                                        <stop offset="100%" stop-color="#ea580c" />
                                    </linearGradient>
                                    <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.15" />
                                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0" />
                                    </linearGradient>
                                    <linearGradient id="violationGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#f43f5e" />
                                        <stop offset="100%" stop-color="#e11d48" />
                                    </linearGradient>
                                    <linearGradient id="violationAreaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.1" />
                                        <stop offset="100%" stop-color="#f43f5e" stop-opacity="0" />
                                    </linearGradient>
                                    <linearGradient id="counselingGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#6366f1" />
                                        <stop offset="100%" stop-color="#4f46e5" />
                                    </linearGradient>
                                    <linearGradient id="counselingAreaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.1" />
                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                    </linearGradient>
                                </defs>

                                @php
                                    // Late student points
                                    $latePoints = [];
                                    $dataCount = count($lateTrendData ?? []);
                                    $stepX = $dataCount > 1 ? 700 / ($dataCount - 1) : 700;

                                    foreach ($lateTrendData ?? [] as $index => $data) {
                                        $x = $index * $stepX;
                                        $y = $maxValue > 0 ? 200 - (($data['count'] / $maxValue) * 180) : 190;
                                        $latePoints[] = ['x' => $x, 'y' => $y, 'count' => $data['count'], 'week' => $data['week'], 'date' => $data['date'], 'month' => $data['month'] ?? ''];
                                    }

                                    // Violation points
                                    $violationPoints = [];
                                    foreach ($violationTrendData ?? [] as $index => $data) {
                                        $x = $index * $stepX;
                                        $y = $maxValue > 0 ? 200 - (($data['count'] / $maxValue) * 180) : 190;
                                        $violationPoints[] = ['x' => $x, 'y' => $y, 'count' => $data['count'], 'week' => $data['week'], 'date' => $data['date']];
                                    }

                                    // Counseling points
                                    $counselingPoints = [];
                                    foreach ($counselingTrendData ?? [] as $index => $data) {
                                        $x = $index * $stepX;
                                        $y = $maxValue > 0 ? 200 - (($data['count'] / $maxValue) * 180) : 190;
                                        $counselingPoints[] = ['x' => $x, 'y' => $y, 'count' => $data['count'], 'week' => $data['week'], 'date' => $data['date']];
                                    }

                                    // Late path
                                    $latePathD = '';
                                    $lateAreaD = '';
                                    if (count($latePoints) > 0) {
                                        $latePathD = 'M ' . $latePoints[0]['x'] . ' ' . $latePoints[0]['y'];
                                        $lateAreaD = 'M ' . $latePoints[0]['x'] . ' 200 L ' . $latePoints[0]['x'] . ' ' . $latePoints[0]['y'];
                                        for ($i = 1; $i < count($latePoints); $i++) {
                                            $latePathD .= ' L ' . $latePoints[$i]['x'] . ' ' . $latePoints[$i]['y'];
                                            $lateAreaD .= ' L ' . $latePoints[$i]['x'] . ' ' . $latePoints[$i]['y'];
                                        }
                                        $lateAreaD .= ' L ' . $latePoints[count($latePoints) - 1]['x'] . ' 200 Z';
                                    }

                                    // Violation path
                                    $violationPathD = '';
                                    $violationAreaD = '';
                                    if (count($violationPoints) > 0) {
                                        $violationPathD = 'M ' . $violationPoints[0]['x'] . ' ' . $violationPoints[0]['y'];
                                        $violationAreaD = 'M ' . $violationPoints[0]['x'] . ' 200 L ' . $violationPoints[0]['x'] . ' ' . $violationPoints[0]['y'];
                                        for ($i = 1; $i < count($violationPoints); $i++) {
                                            $violationPathD .= ' L ' . $violationPoints[$i]['x'] . ' ' . $violationPoints[$i]['y'];
                                            $violationAreaD .= ' L ' . $violationPoints[$i]['x'] . ' ' . $violationPoints[$i]['y'];
                                        }
                                        $violationAreaD .= ' L ' . $violationPoints[count($violationPoints) - 1]['x'] . ' 200 Z';
                                    }

                                    // Counseling path
                                    $counselingPathD = '';
                                    $counselingAreaD = '';
                                    if (count($counselingPoints) > 0) {
                                        $counselingPathD = 'M ' . $counselingPoints[0]['x'] . ' ' . $counselingPoints[0]['y'];
                                        $counselingAreaD = 'M ' . $counselingPoints[0]['x'] . ' 200 L ' . $counselingPoints[0]['x'] . ' ' . $counselingPoints[0]['y'];
                                        for ($i = 1; $i < count($counselingPoints); $i++) {
                                            $counselingPathD .= ' L ' . $counselingPoints[$i]['x'] . ' ' . $counselingPoints[$i]['y'];
                                            $counselingAreaD .= ' L ' . $counselingPoints[$i]['x'] . ' ' . $counselingPoints[$i]['y'];
                                        }
                                        $counselingAreaD .= ' L ' . $counselingPoints[count($counselingPoints) - 1]['x'] . ' 200 Z';
                                    }
                                @endphp

                                <!-- Late Area Fill -->
                                <path d="{{ $lateAreaD }}" fill="url(#areaGradient)" />

                                <!-- Violation Area Fill -->
                                <path d="{{ $violationAreaD }}" fill="url(#violationAreaGradient)" />

                                <!-- Counseling Area Fill -->
                                <path d="{{ $counselingAreaD }}" fill="url(#counselingAreaGradient)" />

                                <!-- Late Line (solid) -->
                                <path d="{{ $latePathD }}" fill="none" stroke="url(#lineGradient)" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round" />

                                <!-- Violation Line (dashed) -->
                                <path d="{{ $violationPathD }}" fill="none" stroke="url(#violationGradient)" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="8,4" />

                                <!-- Counseling Line (dotted) -->
                                <path d="{{ $counselingPathD }}" fill="none" stroke="url(#counselingGradient)" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4,4" />

                                <!-- Late Data Points (all points with tooltips) -->
                                @foreach($latePoints as $index => $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#1e293b" stroke="#f59e0b"
                                        stroke-width="2" class="transition-all cursor-pointer hover:r-4">
                                        <title>Terlambat - {{ $point['week'] }} ({{ $point['date'] }}): {{ $point['count'] }}
                                        </title>
                                    </circle>
                                @endforeach

                                <!-- Violation Data Points (all points with tooltips) -->
                                @foreach($violationPoints as $index => $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#1e293b" stroke="#f43f5e"
                                        stroke-width="2" class="transition-all cursor-pointer hover:r-4">
                                        <title>Pelanggaran - {{ $point['week'] }} ({{ $point['date'] }}): {{ $point['count'] }}
                                        </title>
                                    </circle>
                                @endforeach

                                <!-- Counseling Data Points (all points with tooltips) -->
                                @foreach($counselingPoints as $index => $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#1e293b" stroke="#6366f1"
                                        stroke-width="2" class="transition-all cursor-pointer hover:r-4">
                                        <title>Konseling - {{ $point['week'] }} ({{ $point['date'] }}): {{ $point['count'] }}
                                        </title>
                                    </circle>
                                @endforeach
                            </svg>
                        </div>
                    </div>
                    <!-- X-Axis Labels (show month names) -->
                    <div class="ml-10 mt-4 relative h-6">
                        @php
                            $prevMonth = '';
                            $dataCount = count($lateTrendData ?? []);
                        @endphp
                        @foreach($lateTrendData ?? [] as $index => $data)
                            @php
                                $currentMonth = $data['month'] ?? '';
                                $showLabel = $currentMonth !== $prevMonth;
                                $prevMonth = $currentMonth;
                                $leftPos = $dataCount > 1 ? ($index / ($dataCount - 1)) * 100 : 0;
                            @endphp
                            @if($showLabel)
                                <span class="absolute text-xs text-slate-400 font-medium"
                                    style="left: {{ $leftPos }}%; transform: translateX(-50%);">
                                    {{ $currentMonth }}
                                </span>
                            @endif
                        @endforeach
                    </div>

                    <!-- Summary Stats -->
                    <div class="mt-3 flex justify-center gap-2 flex-wrap">
                        @php
                            $totalLate = collect($lateTrendData ?? [])->sum('count');
                            $totalViolation = collect($violationTrendData ?? [])->sum('count');
                            $totalCounseling = collect($counselingTrendData ?? [])->sum('count');
                            $totalWeeks = count($lateTrendData ?? []);
                            $avgLatePerWeek = $totalWeeks > 0 ? round($totalLate / $totalWeeks, 1) : 0;
                            $avgViolationPerWeek = $totalWeeks > 0 ? round($totalViolation / $totalWeeks, 1) : 0;
                            $avgCounselingPerWeek = $totalWeeks > 0 ? round($totalCounseling / $totalWeeks, 1) : 0;
                        @endphp
                        <div class="bg-slate-800/30 rounded px-2 py-1 border border-amber-500/30 flex items-center gap-1">
                            <span class="text-sm font-bold text-amber-400">{{ $totalLate }}</span>
                            <span class="text-[10px] text-slate-400">Terlambat</span>
                        </div>
                        <div class="bg-slate-800/30 rounded px-2 py-1 border border-rose-500/30 flex items-center gap-1">
                            <span class="text-sm font-bold text-rose-400">{{ $totalViolation }}</span>
                            <span class="text-[10px] text-slate-400">Pelanggaran</span>
                        </div>
                        <div class="bg-slate-800/30 rounded px-2 py-1 border border-indigo-500/30 flex items-center gap-1">
                            <span class="text-sm font-bold text-indigo-400">{{ $totalCounseling }}</span>
                            <span class="text-[10px] text-slate-400">Konseling</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Prepare data from PHP
            const weeklyData = @json($weeklyData);

            const weekdayData = @json($weekdayData ?? []);
            const totalStudents = {{ $totalStudents ?? 1 }};

            // Weekly Attendance Line Chart
            const categories = weeklyData.map(item => item.day);
            const hadirData = weeklyData.map(item => item.hadir);
            const bolosData = weeklyData.map(item => item.bolos);
            const sakitData = weeklyData.map(item => item.sakit);
            const izinData = weeklyData.map(item => item.izin);
            const alphaData = weeklyData.map(item => item.alpha);

            const lineOptions = {
                series: [
                    { name: 'Hadir', data: hadirData },
                    { name: 'Bolos', data: bolosData },
                    { name: 'Sakit', data: sakitData },
                    { name: 'Izin', data: izinData },
                    { name: 'Alpha', data: alphaData }
                ],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#10b981', '#f59e0b', '#3b82f6', '#a855f7', '#f43f5e'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: 'rgba(148, 163, 184, 0.1)',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        },
                        formatter: function (val) {
                            return Math.round(val);
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    style: { fontSize: '12px' },
                    x: { show: true },
                    y: {
                        formatter: function (val) {
                            return val + ' siswa';
                        }
                    }
                },
                legend: {
                    show: false
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: { size: 6 }
                }
            };

            const lineChart = new ApexCharts(document.querySelector("#weeklyAttendanceChart"), lineOptions);
            lineChart.render();

            // Weekday Donut Charts
            weekdayData.forEach((data, index) => {
                const el = document.querySelector(`#weekdayDonut${index}`);
                if (!el) return;

                const total = data.hadir + data.bolos + data.sakit + data.izin + data.alpha;
                const donutOptions = {
                    series: [data.hadir, data.bolos, data.sakit, data.izin, data.alpha],
                    chart: {
                        type: 'donut',
                        height: 100,
                        background: 'transparent',
                        sparkline: { enabled: false }
                    },
                    colors: ['#10b981', '#f59e0b', '#3b82f6', '#a855f7', '#f43f5e'],
                    labels: ['Hadir', 'Bolos', 'Sakit', 'Izin', 'Alpha'],
                    stroke: {
                        width: 0
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%',
                                labels: {
                                    show: true,
                                    name: { show: false },
                                    value: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 700,
                                        color: '#10b981',
                                        formatter: function () {
                                            return data.hadir;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: '',
                                        fontSize: '14px',
                                        fontWeight: 700,
                                        color: '#10b981',
                                        formatter: function () {
                                            return data.hadir;
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        theme: 'dark',
                        y: {
                            formatter: function (val) {
                                return val + ' siswa';
                            }
                        }
                    },
                    legend: { show: false },
                    dataLabels: { enabled: false }
                };

                const donutChart = new ApexCharts(el, donutOptions);
                donutChart.render();
            });
        });
    </script>
@endpush