@extends('layouts.frontend')

@section('title', 'Presensi Harian')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Presensi Harian</h1>
            <p class="text-slate-400 mt-1">Rekap kehadiran siswa</p>
        </div>

        <!-- Date Info Card (Moved to top) -->
        <div class="rounded-2xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500/30 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                        <span class="text-2xl font-bold">{{ $today->format('d') }}</span>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-white">{{ $today->translatedFormat('l') }}</p>
                        <p class="text-slate-300">{{ $today->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <!-- Filter -->
                <form action="{{ route('frontend.presensi') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="date" name="date" value="{{ $date }}"
                        class="px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <select name="kelas_id"
                        class="px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nm_kls }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards (Moved to top) -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
            <!-- Hadir -->
            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-emerald-400">{{ $stats['hadir'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Hadir</p>
            </div>
            <!-- Terlambat -->
            <div class="rounded-xl bg-amber-500/10 border border-amber-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-amber-400">{{ $stats['terlambat'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Terlambat</p>
            </div>
            <!-- Sakit -->
            <div class="rounded-xl bg-yellow-500/10 border border-yellow-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-yellow-400">{{ $stats['sakit'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Sakit</p>
            </div>
            <!-- Izin -->
            <div class="rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-blue-400">{{ $stats['izin'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Izin</p>
            </div>
            <!-- Alpha -->
            <div class="rounded-xl bg-rose-500/10 border border-rose-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-rose-400">{{ $stats['alpha'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Alpha</p>
            </div>
            <!-- Bolos -->
            <div class="rounded-xl bg-red-500/10 border border-red-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-red-400">{{ $stats['bolos'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Bolos</p>
            </div>
            <!-- Belum -->
            <div class="rounded-xl bg-slate-500/10 border border-slate-500/20 p-4 text-center">
                <p class="text-3xl font-bold text-slate-400">{{ $stats['belum'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Belum Absen</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monthly Bar Chart -->
            <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Presensi Perbulan</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Yearly Pie Chart -->
            <div class="rounded-2xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">Presensi Pertahun</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="yearlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Data from PHP
        const monthlyData = @json($monthlyData);
        const months = @json($months);
        const yearlyData = @json($yearlyData);

        // Stacked Bar Chart - Monthly
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: monthlyData.map(d => d.tepat_waktu),
                        backgroundColor: '#4ade80',
                        borderRadius: 4,
                    },
                    {
                        label: 'Terlambat Masuk',
                        data: monthlyData.map(d => d.terlambat),
                        backgroundColor: '#fcd34d',
                        borderRadius: 4,
                    },
                    {
                        label: 'Pulang Sebelum Waktunya',
                        data: monthlyData.map(d => d.pulang_awal),
                        backgroundColor: '#fb923c',
                        borderRadius: 4,
                    },
                    {
                        label: 'Tidak Absen',
                        data: monthlyData.map(d => d.tidak_absen),
                        backgroundColor: '#f87171',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15,
                            font: { size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    }
                }
            }
        });

        // Pie Chart - Yearly
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'pie',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Pulang Sebelum Waktunya', 'Tidak Absen'],
                datasets: [{
                    data: [
                        yearlyData.tepat_waktu,
                        yearlyData.terlambat,
                        yearlyData.pulang_awal,
                        yearlyData.tidak_absen
                    ],
                    backgroundColor: ['#a78bfa', '#fcd34d', '#fb923c', '#f87171'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 12,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    </script>
@endsection