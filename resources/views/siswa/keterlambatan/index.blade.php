@extends('layouts.admin')

@section('title', 'Riwayat Keterlambatan')
@section('page-title', 'Riwayat Keterlambatan')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-600 to-orange-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-amber-100 text-sm">Riwayat Keterlambatan</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-amber-200 text-sm mt-1">NIS: {{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-white">{{ $totalKeterlambatan }}</p>
                <p class="text-xs text-slate-400 mt-1">Total Keterlambatan</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-amber-400">{{ $totalMenit }}</p>
                <p class="text-xs text-slate-400 mt-1">Total Menit</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-blue-400">{{ $totalDiproses }}</p>
                <p class="text-xs text-slate-400 mt-1">Diproses</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-emerald-400">{{ $totalSelesai }}</p>
                <p class="text-xs text-slate-400 mt-1">Selesai</p>
            </div>
        </div>

        <!-- Keterlambatan List -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/50">
                <h3 class="text-sm font-medium text-slate-300">Daftar Keterlambatan</h3>
            </div>

            @if($keterlambatans->count() > 0)
                <div class="divide-y divide-slate-800/50">
                    @foreach($keterlambatans as $item)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-white">
                                            {{ $item->tanggal->translatedFormat('l, d F Y') }}
                                        </span>
                                        @php
                                            $statusClass = match ($item->status) {
                                                'selesai' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                'diproses' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                default => 'bg-amber-500/20 text-amber-400 border-amber-500/30'
                                            };
                                            $statusLabel = match ($item->status) {
                                                'selesai' => 'Selesai',
                                                'diproses' => 'Diproses',
                                                default => 'Pending'
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-slate-300">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Terlambat {{ $item->durasi ?? 0 }} menit</span>
                                        </div>
                                        @if($item->jam_masuk)
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                </svg>
                                                <span>Masuk: {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($item->keterangan)
                                        <div class="mt-2 text-xs text-slate-400">
                                            <span class="text-slate-500">Keterangan:</span> {{ $item->keterangan }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-emerald-500/20 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-slate-400 text-sm">Tidak ada riwayat keterlambatan</p>
                    <p class="text-slate-500 text-xs mt-1">Anda tidak pernah tercatat terlambat</p>
                </div>
            @endif
        </div>
    </div>
@endsection