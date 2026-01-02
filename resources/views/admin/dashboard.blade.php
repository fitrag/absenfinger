@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ url('/admin/kesiswaan/siswa-terlambat') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
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
                                        {{ \Carbon\Carbon::parse($terlambat->tanggal)->format('d M Y') }} •
                                        <span class="text-amber-400">{{ $terlambat->jam_datang }}</span>
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    +{{ $terlambat->keterlambatan }} menit
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ route('admin.kesiswaan.konseling.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
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
                            @if(session('user_level') !== 'guru')
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
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
                        @else
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"></span>
                                    <span class="text-xs text-slate-400">Hadir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="text-xs text-slate-400">Tidak Hadir</span>
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
                    <!-- Weekday Attendance Chart (Mon-Fri) -->
                    <div class="p-6">
                        <div class="flex items-end justify-between h-48 gap-4">
                            @foreach($weekdayData ?? [] as $data)
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    @php
                                        $total = $totalStudents > 0 ? $totalStudents : 1;
                                        $presentPercent = ($data['present'] / $total) * 100;
                                        $absentPercent = ($data['absent'] / $total) * 100;
                                    @endphp
                                    <div class="w-full flex flex-col gap-0.5" style="height: 100%">
                                        <div class="flex-1 flex flex-col justify-end">
                                            @if($data['present'] > 0)
                                                <div class="w-full bg-gradient-to-t from-emerald-600 to-teal-500 rounded-t-lg transition-all hover:opacity-80"
                                                    style="height: {{ $presentPercent }}%" title="Hadir: {{ $data['present'] }}">
                                                </div>
                                            @endif
                                            @if($data['absent'] > 0)
                                                <div class="w-full bg-rose-500 {{ $data['present'] == 0 ? 'rounded-t-lg' : '' }} rounded-b-lg transition-all hover:opacity-80"
                                                    style="height: {{ $absentPercent }}%" title="Tidak Hadir: {{ $data['absent'] }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-xs text-slate-400 block">{{ $data['day'] }}</span>
                                        <span class="text-xs text-emerald-400 font-medium">{{ $data['present'] }}</span>
                                    </div>
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
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500"></span>
                            <span class="text-xs text-slate-400">Hadir</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="text-xs text-slate-400">Tidak Hadir</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Bar Chart -->
                <div class="flex items-end justify-between h-48 gap-4">
                    @foreach($weeklyData as $data)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            @php
                                $total = $totalStudents > 0 ? $totalStudents : 1;
                                $presentPercent = ($data['present'] / $total) * 100;
                                $absentPercent = ($data['absent'] / $total) * 100;
                            @endphp
                            <div class="w-full flex flex-col gap-0.5" style="height: 100%">
                                <div class="flex-1 flex flex-col justify-end">
                                    @if($data['present'] > 0)
                                        <div class="w-full bg-gradient-to-t from-blue-600 to-purple-500 rounded-t-lg transition-all hover:opacity-80"
                                            style="height: {{ $presentPercent }}%" title="Hadir: {{ $data['present'] }}">
                                        </div>
                                    @endif
                                    @if($data['absent'] > 0)
                                        <div class="w-full bg-rose-500 {{ $data['present'] == 0 ? 'rounded-t-lg' : '' }} rounded-b-lg transition-all hover:opacity-80"
                                            style="height: {{ $absentPercent }}%" title="Tidak Hadir: {{ $data['absent'] }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-slate-400 block">{{ $data['day'] }}</span>
                                <span class="text-xs text-slate-500">{{ $data['present'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50 h-[400px] overflow-y-auto custom-scrollbar">
                        @forelse($siswaTerlambat ?? [] as $terlambat)
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
                                        <span class="text-amber-400">{{ $terlambat->jam_datang }}</span>
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20 flex-shrink-0">
                                    +{{ $terlambat->keterlambatan_menit }} m
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ route('admin.kesiswaan.konseling.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50 h-[400px] overflow-y-auto custom-scrollbar">
                        @forelse($siswaKonseling ?? [] as $konseling)
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
                            @if(session('user_level') !== 'guru')
                                <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    Lihat semua →
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="divide-y divide-slate-800/50 h-[400px] overflow-y-auto custom-scrollbar">
                        @forelse($pelanggaranSiswa ?? [] as $item)
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
        @endif
    </div>
@endsection