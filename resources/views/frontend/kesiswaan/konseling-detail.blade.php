@extends('layouts.frontend')

@section('title', 'Detail Konseling - ' . $student->name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button & Header -->
        <div class="mb-6">
            <a href="{{ route('frontend.kesiswaan') }}"
                class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Kesiswaan
            </a>
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500/30 to-blue-600/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $student->name }}</h1>
                    <p class="text-slate-400">{{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 border border-blue-500/30 p-5 mb-6">
            <p class="text-3xl font-bold text-white">{{ $konselings->count() }}</p>
            <p class="text-sm text-slate-400">Total Sesi Konseling</p>
        </div>

        <!-- Konseling List -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/50">
                <h2 class="text-lg font-semibold text-white">Riwayat Konseling</h2>
            </div>
            <div class="divide-y divide-slate-800/50">
                @forelse($konselings as $konseling)
                    <div class="p-4 hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $konseling->status_badge }} border">
                                    {{ $konseling->status_label }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $konseling->tanggal->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Permasalahan:</p>
                                <p class="text-sm text-blue-400">{{ $konseling->permasalahan }}</p>
                            </div>
                            @if($konseling->penanganan)
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Penanganan:</p>
                                    <p class="text-sm text-slate-300">{{ $konseling->penanganan }}</p>
                                </div>
                            @endif
                            @if($konseling->hasil)
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Hasil:</p>
                                    <p class="text-sm text-emerald-400">{{ $konseling->hasil }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-slate-500">Tidak ada data konseling</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection