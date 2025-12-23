@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-6 sm:p-8">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">Selamat Datang! 👋</h2>
            <p class="text-blue-100 text-sm sm:text-base max-w-xl">
                Kelola sistem absensi fingerprint siswa dengan mudah. Pantau kehadiran dan lihat laporan secara real-time.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Students -->
        <div class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-blue-500/30 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-bl-full"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Siswa</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($totalStudents) }}</p>
                    <p class="text-sm text-slate-400 mt-1">
                        Siswa aktif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-emerald-500/30 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-500/10 to-transparent rounded-bl-full"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($presentToday) }}</p>
                    <p class="text-sm text-slate-400 mt-1">
                        @if($totalStudents > 0)
                            <span class="text-emerald-400">{{ round(($presentToday / $totalStudents) * 100) }}%</span> kehadiran
                        @else
                            <span class="text-slate-500">-</span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Checked Out -->
        <div class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-amber-500/30 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-500/10 to-transparent rounded-bl-full"></div>
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
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="group relative overflow-hidden rounded-2xl bg-slate-900/50 border border-slate-800/50 p-6 hover:border-rose-500/30 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-rose-500/10 to-transparent rounded-bl-full"></div>
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
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Attendance -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Kehadiran Terbaru</h3>
                    <a href="{{ url('/admin/attendance') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                        Lihat semua →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-slate-800/50">
                @forelse($recentAttendances as $attendance)
                <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $attendance->checktype == 0 ? 'from-emerald-600 to-emerald-700' : 'from-amber-600 to-amber-700' }} flex items-center justify-center text-white font-medium text-sm">
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
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $attendance->checktype == 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                        {{ $attendance->checktype == 0 ? 'Masuk' : 'Pulang' }}
                    </span>
                </div>
                @empty
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-slate-400 text-sm">Belum ada data kehadiran hari ini</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <h3 class="text-lg font-semibold text-white">Aksi Cepat</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4">
                <!-- Add Student -->
                <a href="{{ url('/admin/students/create') }}" class="group flex flex-col items-center gap-3 p-4 rounded-xl bg-slate-800/30 border border-slate-700/50 hover:border-blue-500/30 hover:bg-slate-800/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-white text-center">Tambah Siswa</span>
                </a>

                <!-- View Students -->
                <a href="{{ url('/admin/students') }}" class="group flex flex-col items-center gap-3 p-4 rounded-xl bg-slate-800/30 border border-slate-700/50 hover:border-emerald-500/30 hover:bg-slate-800/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-white text-center">Data Siswa</span>
                </a>

                <!-- View Attendance -->
                <a href="{{ url('/admin/attendance') }}" class="group flex flex-col items-center gap-3 p-4 rounded-xl bg-slate-800/30 border border-slate-700/50 hover:border-amber-500/30 hover:bg-slate-800/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-white text-center">Data Absensi</span>
                </a>

                <!-- Reports -->
                <a href="{{ url('/admin/reports') }}" class="group flex flex-col items-center gap-3 p-4 rounded-xl bg-slate-800/30 border border-slate-700/50 hover:border-purple-500/30 hover:bg-slate-800/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-white text-center">Laporan</span>
                </a>
            </div>
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
                                 style="height: {{ $presentPercent }}%" 
                                 title="Hadir: {{ $data['present'] }}">
                            </div>
                            @endif
                            @if($data['absent'] > 0)
                            <div class="w-full bg-rose-500 {{ $data['present'] == 0 ? 'rounded-t-lg' : '' }} rounded-b-lg transition-all hover:opacity-80" 
                                 style="height: {{ $absentPercent }}%" 
                                 title="Tidak Hadir: {{ $data['absent'] }}">
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
</div>
@endsection
