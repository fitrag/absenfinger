@extends('layouts.frontend')

@section('title', 'Beranda')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section - Enhanced -->
        <div class="relative rounded-3xl overflow-hidden mb-12">
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.2),transparent_50%)]">
            </div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(255,255,255,0.15),transparent_50%)]">
            </div>

            <!-- Floating Particles Effect -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute w-72 h-72 bg-white/10 rounded-full blur-3xl -top-20 -left-20 animate-pulse"></div>
                <div class="absolute w-96 h-96 bg-pink-500/20 rounded-full blur-3xl -bottom-32 -right-32 animate-pulse"
                    style="animation-delay: 1s;"></div>
                <div class="absolute w-64 h-64 bg-cyan-400/15 rounded-full blur-3xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 animate-pulse"
                    style="animation-delay: 2s;"></div>
            </div>

            <div class="relative px-6 py-16 pb-20 sm:px-12 sm:py-24 sm:pb-28 text-center">
                <!-- Logo with Glow Effect -->
                <div class="flex justify-center mb-8" style="margin-top: 20px;">
                    @php
                        $settings = \App\Models\Setting::getAllSettings();
                        $logoPath = $settings['school_logo'] ?? null;
                    @endphp
                    <div class="relative">
                        <div class="absolute inset-0 bg-white/30 rounded-2xl blur-xl scale-110"></div>
                        @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo"
                                class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl shadow-2xl ring-4 ring-white/30">
                        @else
                            <div
                                class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white/20 backdrop-blur-lg flex items-center justify-center ring-4 ring-white/30">
                                <svg class="w-12 h-12 sm:w-14 sm:h-14 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Title with Better Typography -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-4 tracking-tight">
                    Selamat Datang di <span
                        class="bg-gradient-to-r from-yellow-200 to-pink-200 bg-clip-text text-transparent">SIAKAD</span>
                </h1>
                <p class="text-xl sm:text-2xl lg:text-3xl font-medium text-white/90 mb-3">
                    {{ $settings['school_name'] ?? 'SMK Negeri 1 Seputih Agung' }}
                </p>
                <p class="text-base sm:text-lg text-white/70 mx-auto mb-10 whitespace-nowrap">
                    Sistem Informasi Akademik untuk pengelolaan data presensi dan kesiswaan secara digital
                </p>

                <!-- Enhanced Animated Stats with Icons -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 max-w-4xl mx-auto" x-data="{
                                                                    siswa: 0, guru: 0, kelas: 0, jurusan: 0,
                                                                    targetSiswa: {{ $stats['total_students'] ?? 0 }},
                                                                    targetGuru: {{ $stats['total_gurus'] ?? 0 }},
                                                                    targetKelas: {{ $stats['total_kelas'] ?? 0 }},
                                                                    targetJurusan: {{ $stats['total_jurusan'] ?? 0 }},
                                                                    animateCount(target, prop, duration = 2000) {
                                                                        const startTime = performance.now();
                                                                        const animate = (currentTime) => {
                                                                            const elapsed = currentTime - startTime;
                                                                            const progress = Math.min(elapsed / duration, 1);
                                                                            const easeOut = 1 - Math.pow(1 - progress, 3);
                                                                            this[prop] = Math.floor(easeOut * target);
                                                                            if (progress < 1) requestAnimationFrame(animate);
                                                                        };
                                                                        requestAnimationFrame(animate);
                                                                    }
                                                                }" x-init="
                                                                    setTimeout(() => animateCount(targetSiswa, 'siswa'), 100);
                                                                    setTimeout(() => animateCount(targetGuru, 'guru'), 200);
                                                                    setTimeout(() => animateCount(targetKelas, 'kelas'), 300);
                                                                    setTimeout(() => animateCount(targetJurusan, 'jurusan'), 400);
                                                                ">
                    <!-- Siswa Card -->
                    <div
                        class="group rounded-2xl bg-white/10 backdrop-blur-lg p-5 border border-white/20 hover:bg-white/20 hover:scale-105 transition-all duration-300 cursor-default">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-400 flex items-center justify-center shadow-lg group-hover:shadow-emerald-400/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <p class="text-4xl sm:text-5xl font-bold text-white" x-text="siswa">0</p>
                        <p class="text-sm text-white/70 mt-1 font-medium">Siswa</p>
                    </div>

                    <!-- Guru Card -->
                    <div
                        class="group rounded-2xl bg-white/10 backdrop-blur-lg p-5 border border-white/20 hover:bg-white/20 hover:scale-105 transition-all duration-300 cursor-default">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-lg group-hover:shadow-blue-400/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-4xl sm:text-5xl font-bold text-white" x-text="guru">0</p>
                        <p class="text-sm text-white/70 mt-1 font-medium">Guru</p>
                    </div>

                    <!-- Kelas Card -->
                    <div
                        class="group rounded-2xl bg-white/10 backdrop-blur-lg p-5 border border-white/20 hover:bg-white/20 hover:scale-105 transition-all duration-300 cursor-default">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg group-hover:shadow-amber-400/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                        <p class="text-4xl sm:text-5xl font-bold text-white" x-text="kelas">0</p>
                        <p class="text-sm text-white/70 mt-1 font-medium">Kelas</p>
                    </div>

                    <!-- Jurusan Card -->
                    <div
                        class="group rounded-2xl bg-white/10 backdrop-blur-lg p-5 border border-white/20 hover:bg-white/20 hover:scale-105 transition-all duration-300 cursor-default">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center shadow-lg group-hover:shadow-pink-400/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <p class="text-4xl sm:text-5xl font-bold text-white" x-text="jurusan">0</p>
                        <p class="text-sm text-white/70 mt-1 font-medium">Jurusan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards - Enhanced -->
        <div class="grid md:grid-cols-2 gap-8 mb-12" style="margin-top: 10px;">
            <!-- Presensi Card -->
            <div
                class="group relative rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-700/50 p-8 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500">
                <!-- Decorative Gradient -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/20 to-cyan-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700">
                </div>

                <div class="relative">
                    <div class="flex items-center gap-5 mb-6">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-xl shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Presensi Digital</h2>
                            <p class="text-slate-400">Absensi otomatis dengan fingerprint</p>
                        </div>
                    </div>
                    <p class="text-slate-300 mb-6 leading-relaxed">
                        Sistem presensi menggunakan teknologi fingerprint untuk memastikan kehadiran siswa tercatat secara
                        akurat dan real-time. Data presensi dapat diakses oleh wali kelas dan orang tua kapan saja.
                    </p>
                    <ul class="space-y-3 text-slate-400 mb-8">
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Rekam kehadiran otomatis</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Deteksi keterlambatan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Laporan harian & bulanan</span>
                        </li>
                    </ul>
                    <a href="{{ url('/presensi') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/25 group-hover:shadow-blue-500/40">
                        <span>Lihat Presensi</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kesiswaan Card -->
            <div
                class="group relative rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-700/50 p-8 overflow-hidden hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500">
                <!-- Decorative Gradient -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-500/20 to-pink-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-700">
                </div>

                <div class="relative">
                    <div class="flex items-center gap-5 mb-6">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-xl shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Data Kesiswaan</h2>
                            <p class="text-slate-400">Pengelolaan masalah siswa</p>
                        </div>
                    </div>
                    <p class="text-slate-300 mb-6 leading-relaxed">
                        Pendataan permasalahan kesiswaan secara terstruktur untuk membantu sekolah dalam pembinaan siswa.
                        Mencakup pelanggaran tata tertib, konseling, dan catatan prestasi.
                    </p>
                    <ul class="space-y-3 text-slate-400 mb-8">
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Catatan pelanggaran</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Pembinaan & konseling</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span>Riwayat dan prestasi</span>
                        </li>
                    </ul>
                    <a href="{{ url('/kesiswaan') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg shadow-purple-500/25 group-hover:shadow-purple-500/40">
                        <span>Lihat Kesiswaan</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endsection