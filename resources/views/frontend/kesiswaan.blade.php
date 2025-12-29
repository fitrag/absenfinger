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
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
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
            <div
                class="rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 border border-emerald-500/30 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>

        <!-- Filter -->
        <div class="mb-6">
            <form method="GET" action="{{ url('/kesiswaan') }}" class="flex flex-wrap gap-4 items-end">
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

        <!-- Two Column Layout -->
        <div class="grid lg:grid-cols-2 gap-6">
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
                <div class="divide-y divide-slate-800/50">
                    @forelse($pelanggarans as $pelanggaran)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-medium text-white truncate">{{ $pelanggaran->student->nama ?? 'N/A' }}</span>
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $pelanggaran->status_badge }} border">
                                            {{ $pelanggaran->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400 mb-1">{{ $pelanggaran->student->kelas->nm_kls ?? '-' }}</p>
                                    <p class="text-sm text-rose-400 font-medium">{{ $pelanggaran->jenis_pelanggaran }}</p>
                                    @if($pelanggaran->deskripsi)
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $pelanggaran->deskripsi }}</p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span
                                        class="inline-flex px-2.5 py-1 text-sm font-bold rounded-lg {{ $pelanggaran->poin_badge }} border">
                                        {{ $pelanggaran->poin }} Poin
                                    </span>
                                    <p class="text-xs text-slate-500 mt-1">{{ $pelanggaran->tanggal->format('d M Y') }}</p>
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
                <div class="divide-y divide-slate-800/50">
                    @forelse($konselings as $konseling)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-medium text-white truncate">{{ $konseling->student->nama ?? 'N/A' }}</span>
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $konseling->status_badge }} border">
                                            {{ $konseling->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400 mb-1">{{ $konseling->student->kelas->nm_kls ?? '-' }}</p>
                                    <p class="text-sm text-blue-400 font-medium">{{ Str::limit($konseling->permasalahan, 50) }}
                                    </p>
                                    @if($konseling->penanganan)
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">Penanganan:
                                            {{ Str::limit($konseling->penanganan, 80) }}</p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs text-slate-500">{{ $konseling->tanggal->format('d M Y') }}</p>
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
            </div>
        </div>
    </div>
@endsection