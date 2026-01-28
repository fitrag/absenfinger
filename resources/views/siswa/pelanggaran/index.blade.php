@extends('layouts.admin')

@section('title', 'Riwayat Pelanggaran')
@section('page-title', 'Riwayat Pelanggaran')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-rose-600 to-red-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-rose-100 text-sm">Riwayat Pelanggaran</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-rose-200 text-sm mt-1">NIS: {{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-white">{{ $totalPelanggaran }}</p>
                <p class="text-xs text-slate-400 mt-1">Total Pelanggaran</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p
                    class="text-3xl font-bold {{ $totalPoin >= 50 ? 'text-red-400' : ($totalPoin >= 25 ? 'text-amber-400' : 'text-white') }}">
                    {{ $totalPoin }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Total Poin</p>
            </div>
        </div>

        <!-- Pelanggaran List -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/50">
                <h3 class="text-sm font-medium text-slate-300">Daftar Pelanggaran</h3>
            </div>

            @if($pelanggarans->count() > 0)
                <div class="divide-y divide-slate-800/50">
                    @foreach($pelanggarans as $item)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-white">{{ $item->jenis_pelanggaran }}</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $item->poin_badge }}">
                                            {{ $item->poin }} poin
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mb-2">
                                        {{ $item->tanggal->translatedFormat('l, d F Y') }}
                                    </p>
                                    @if($item->deskripsi)
                                        <p class="text-sm text-slate-300 mb-2">{{ $item->deskripsi }}</p>
                                    @endif
                                    @if($item->tindakan)
                                        <div class="flex items-center gap-2 text-xs text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            <span>Tindakan: {{ $item->tindakan }}</span>
                                        </div>
                                    @endif
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $item->status_badge }}">
                                    {{ $item->status_label }}
                                </span>
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
                    <p class="text-slate-400 text-sm">Tidak ada riwayat pelanggaran</p>
                    <p class="text-slate-500 text-xs mt-1">Pertahankan perilaku baikmu!</p>
                </div>
            @endif
        </div>
    </div>
@endsection