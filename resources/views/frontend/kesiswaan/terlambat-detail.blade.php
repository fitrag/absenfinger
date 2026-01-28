@extends('layouts.frontend')

@section('title', 'Detail Keterlambatan - ' . $student->name)

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
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500/30 to-orange-600/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $student->name }}</h1>
                    <p class="text-slate-400">{{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/30 p-5">
                <p class="text-3xl font-bold text-white">{{ $totalMenit }}</p>
                <p class="text-sm text-slate-400">Total Menit Terlambat</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 border border-amber-500/30 p-5">
                <p class="text-3xl font-bold text-white">{{ $terlambats->count() }}</p>
                <p class="text-sm text-slate-400">Jumlah Keterlambatan</p>
            </div>
        </div>

        <!-- Terlambat List -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/50">
                <h2 class="text-lg font-semibold text-white">Riwayat Keterlambatan</h2>
            </div>
            <div class="divide-y divide-slate-800/50">
                @forelse($terlambats as $terlambat)
                    <div class="p-4 hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-orange-400">Terlambat {{ $terlambat->keterlambatan_menit }}
                                        menit</span>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $terlambat->status_badge }} border">
                                        {{ $terlambat->status_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">Jam datang: {{ $terlambat->jam_datang }}</p>
                                @if($terlambat->alasan)
                                    <p class="text-sm text-slate-400 mt-1">Alasan: {{ $terlambat->alasan }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-slate-500">{{ $terlambat->tanggal->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-slate-500">Tidak ada data keterlambatan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection