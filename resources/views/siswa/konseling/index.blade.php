@extends('layouts.admin')

@section('title', 'Riwayat Konseling')
@section('page-title', 'Riwayat Konseling')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-cyan-100 text-sm">Riwayat Konseling</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-cyan-200 text-sm mt-1">NIS: {{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-white">{{ $totalKonseling }}</p>
                <p class="text-xs text-slate-400 mt-1">Total Konseling</p>
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

        <!-- Konseling List -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/50">
                <h3 class="text-sm font-medium text-slate-300">Daftar Konseling</h3>
            </div>

            @if($konselings->count() > 0)
                <div class="divide-y divide-slate-800/50">
                    @foreach($konselings as $item)
                        <div class="p-4 hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-white">Konseling #{{ $loop->iteration }}</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $item->status_badge }}">
                                            {{ $item->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mb-2">
                                        {{ $item->tanggal->translatedFormat('l, d F Y') }}
                                    </p>
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-slate-500">Permasalahan:</p>
                                            <p class="text-sm text-slate-300">{{ $item->permasalahan }}</p>
                                        </div>
                                        @if($item->penanganan)
                                            <div>
                                                <p class="text-xs text-slate-500">Penanganan:</p>
                                                <p class="text-sm text-slate-300">{{ $item->penanganan }}</p>
                                            </div>
                                        @endif
                                        @if($item->hasil)
                                            <div>
                                                <p class="text-xs text-slate-500">Hasil:</p>
                                                <p class="text-sm text-slate-300">{{ $item->hasil }}</p>
                                            </div>
                                        @endif
                                        @if($item->keterangan)
                                            <div class="flex items-center gap-2 text-xs text-slate-400 mt-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $item->keterangan }}</span>
                                            </div>
                                        @endif
                                    </div>
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
                    <p class="text-slate-400 text-sm">Tidak ada riwayat konseling</p>
                    <p class="text-slate-500 text-xs mt-1">Belum ada catatan konseling untuk Anda</p>
                </div>
            @endif
        </div>
    </div>
@endsection