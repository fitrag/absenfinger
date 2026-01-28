@extends('layouts.frontend')

@section('title', 'Kesiswaan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Data Kesiswaan</h1>
            <p class="text-slate-400 mt-1">Pendataan permasalahan dan pembinaan siswa</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
            <div class="rounded-xl bg-gradient-to-br from-rose-500/20 to-rose-600/10 border border-rose-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_pelanggaran'] }}</p>
                        <p class="text-xs text-slate-400">Total Pelanggaran</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 border border-amber-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['pelanggaran_pending'] }}</p>
                        <p class="text-xs text-slate-400">Pelanggaran Pending</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 border border-blue-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_konseling'] }}</p>
                        <p class="text-xs text-slate-400">Total Konseling</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 border border-cyan-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-cyan-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['konseling_pending'] }}</p>
                        <p class="text-xs text-slate-400">Konseling Pending</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_terlambat'] }}</p>
                        <p class="text-xs text-slate-400">Total Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 border border-yellow-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['terlambat_pending'] }}</p>
                        <p class="text-xs text-slate-400">Terlambat Pending</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-6">
            <form method="GET" action="{{ url('/kesiswaan') }}" class="flex flex-wrap gap-4 items-end">
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran</label>
                    <select name="tp_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="">Semua TP</option>
                        @foreach($tpList as $tp)
                            <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nm_tp }} {{ $tp->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Filter Kelas</label>
                    <select name="kelas_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nm_kls }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Three Column Layout -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Pelanggaran Section -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Data Pelanggaran</h2>
                        <p class="text-xs text-slate-400">Catatan pelanggaran tata tertib siswa</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-800/50 max-h-[500px] overflow-y-auto">
                    @forelse($pelanggaranGrouped as $data)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-medium text-white truncate text-sm">{{ $data['student']->name ?? 'N/A' }}</span>
                                        @php
                                            $statusBadge = match ($data['status']) {
                                                'selesai' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                'diproses' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                            };
                                            $statusLabel = match ($data['status']) {
                                                'selesai' => 'Selesai',
                                                'diproses' => 'Diproses',
                                                default => 'Pending',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $statusBadge }} border">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mb-1">{{ $data['student']->kelas->nm_kls ?? '-' }}</p>
                                    <p class="text-sm text-rose-400 font-medium">{{ $data['jumlah'] }} Pelanggaran</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-bold rounded-lg bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        {{ $data['total_poin'] }} Poin
                                    </span>
                                    <a href="{{ route('frontend.kesiswaan.pelanggaran.detail', $data['encrypted_id']) }}"
                                        class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 border border-rose-500/30 transition-colors">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800/50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-slate-500">Tidak ada data pelanggaran</p>
                        </div>
                    @endforelse
                </div>
                @if($pelanggaranGrouped->hasPages())
                    <div class="px-4 py-3 border-t border-slate-800/50">
                        {{ $pelanggaranGrouped->links() }}
                    </div>
                @endif
            </div>

            <!-- Konseling Section -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Data Konseling</h2>
                        <p class="text-xs text-slate-400">Riwayat sesi bimbingan konseling</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-800/50 max-h-[500px] overflow-y-auto">
                    @forelse($konselingGrouped as $data)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-medium text-white truncate text-sm">{{ $data['student']->name ?? 'N/A' }}</span>
                                        @php
                                            $statusBadge = match ($data['status']) {
                                                'selesai' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                'diproses' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                            };
                                            $statusLabel = match ($data['status']) {
                                                'selesai' => 'Selesai',
                                                'diproses' => 'Diproses',
                                                default => 'Pending',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $statusBadge }} border">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mb-1">{{ $data['student']->kelas->nm_kls ?? '-' }}</p>
                                    <p class="text-sm text-blue-400 font-medium">{{ $data['jumlah'] }} Sesi Konseling</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <a href="{{ route('frontend.kesiswaan.konseling.detail', $data['encrypted_id']) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 border border-blue-500/30 transition-colors">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800/50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="text-slate-500">Tidak ada data konseling</p>
                        </div>
                    @endforelse
                </div>
                @if($konselingGrouped->hasPages())
                    <div class="px-4 py-3 border-t border-slate-800/50">
                        {{ $konselingGrouped->links() }}
                    </div>
                @endif
            </div>

            <!-- Terlambat Section -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/50">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Data Terlambat</h2>
                                <p class="text-xs text-slate-400">Catatan keterlambatan siswa</p>
                            </div>
                        </div>
                        <form method="GET" action="{{ url('/kesiswaan') }}" class="flex items-center gap-2">
                            <input type="hidden" name="tp_id" value="{{ $tpId }}">
                            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                            <input type="date" name="tanggal_terlambat" value="{{ $tanggalTerlambat }}"
                                onchange="this.form.submit()"
                                class="px-3 py-1.5 text-sm bg-slate-800/50 border border-slate-700/50 rounded-lg text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors">
                            @if($tanggalTerlambat)
                                <a href="{{ url('/kesiswaan') }}?tp_id={{ $tpId }}&kelas_id={{ $kelasId }}"
                                    class="p-1.5 rounded-lg bg-slate-700/50 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors"
                                    title="Reset tanggal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="divide-y divide-slate-800/50 max-h-[500px] overflow-y-auto">
                    @forelse($terlambatGrouped as $data)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-medium text-white truncate text-sm">{{ $data['student']->name ?? 'N/A' }}</span>
                                        @php
                                            $statusBadge = match ($data['status']) {
                                                'selesai' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                'diproses' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                            };
                                            $statusLabel = match ($data['status']) {
                                                'selesai' => 'Selesai',
                                                'diproses' => 'Diproses',
                                                default => 'Pending',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $statusBadge }} border">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mb-1">{{ $data['student']->kelas->nm_kls ?? '-' }}</p>
                                    <p class="text-sm text-orange-400 font-medium">{{ $data['jumlah'] }}x Terlambat
                                        ({{ $data['total_menit'] }} menit)</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <a href="{{ route('frontend.kesiswaan.terlambat.detail', $data['encrypted_id']) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-orange-500/20 text-orange-400 hover:bg-orange-500/30 border border-orange-500/30 transition-colors">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-800/50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-slate-500">Tidak ada data terlambat</p>
                        </div>
                    @endforelse
                </div>
                @if($terlambatGrouped->hasPages())
                    <div class="px-4 py-3 border-t border-slate-800/50">
                        {{ $terlambatGrouped->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Perkembangan Kesiswaan Chart -->
        <div class="mt-8 rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
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

                            <!-- Data Points with tooltips -->
                            @foreach($latePoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#1e293b" stroke="#f59e0b"
                                    stroke-width="2" class="transition-all cursor-pointer hover:r-4">
                                    <title>Terlambat - {{ $point['week'] }} ({{ $point['date'] }}): {{ $point['count'] }}
                                    </title>
                                </circle>
                            @endforeach

                            @foreach($violationPoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#1e293b" stroke="#f43f5e"
                                    stroke-width="2" class="transition-all cursor-pointer hover:r-4">
                                    <title>Pelanggaran - {{ $point['week'] }} ({{ $point['date'] }}): {{ $point['count'] }}
                                    </title>
                                </circle>
                            @endforeach

                            @foreach($counselingPoints as $point)
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
    </div>
@endsection