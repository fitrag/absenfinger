@extends('layouts.admin')

@section('title', 'Detail Nilai ' . ($type === 'soft' ? 'Soft Skill' : ($type === 'wirausaha' ? 'Aspek Berwirausaha' : 'Hard Skill')))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('admin.pkl.nilai.index', ['tab' => $type]) }}"
                        class="inline-flex items-center gap-2 px-3 py-2 text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-white">
                    Detail Nilai
                    {{ $type === 'soft' ? 'Soft Skill' : ($type === 'wirausaha' ? 'Aspek Berwirausaha' : 'Hard Skill') }}
                </h1>
                <p class="text-slate-400 text-sm mt-1">
                    Lihat dan edit nilai komponen
                    {{ $type === 'soft' ? 'soft skill' : ($type === 'wirausaha' ? 'aspek berwirausaha' : 'hard skill') }}
                </p>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-6">
            <div class="flex items-start gap-4">
                <div
                    class="w-14 h-14 rounded-xl {{ $type === 'soft' ? 'bg-emerald-500/20' : ($type === 'wirausaha' ? 'bg-amber-500/20' : 'bg-blue-500/20') }} flex items-center justify-center flex-shrink-0">
                    @if ($type === 'soft')
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif ($type === 'wirausaha')
                        <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white">{{ $pkl->student->name ?? '-' }}</h3>
                    <p class="text-sm text-slate-400">NIS: {{ $pkl->student->nis ?? '-' }}</p>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <span class="text-sm text-slate-400">
                            <span class="text-slate-500">Kelas:</span> {{ $pkl->student->kelas->nm_kls ?? '-' }}
                        </span>
                        <span class="text-sm text-slate-400">
                            <span class="text-slate-500">DUDI:</span> {{ $pkl->dudi->nama ?? '-' }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500 mb-1">Rata-rata Nilai</p>
                    @php
                        $avgNilai = $nilaiList->avg('nilai') ?? 0;
                    @endphp
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-lg font-bold
                                {{ $avgNilai >= 80 ? 'bg-emerald-500/20 text-emerald-400' : ($avgNilai >= 60 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                        {{ number_format($avgNilai, 1) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div
            class="rounded-xl bg-slate-800/50 border {{ $type === 'soft' ? 'border-emerald-500/30' : ($type === 'wirausaha' ? 'border-amber-500/30' : 'border-blue-500/30') }} overflow-hidden">
            <div
                class="flex items-center gap-3 p-4 border-b border-slate-700/50 {{ $type === 'soft' ? 'bg-emerald-500/5' : ($type === 'wirausaha' ? 'bg-amber-500/5' : 'bg-blue-500/5') }}">
                @if ($type === 'soft')
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-emerald-400">Komponen Soft Skill</h3>
                @elseif ($type === 'wirausaha')
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-amber-400">Komponen Aspek Berwirausaha</h3>
                @else
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-400">Komponen Hard Skill</h3>
                @endif
            </div>

            <form action="{{ route('admin.pkl.nilai.bulk_update') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                @if ($nilaiList->count() > 0)
                    <div class="space-y-4">
                        @foreach ($nilaiList as $nilai)
                            <div
                                class="flex items-center justify-between gap-4 p-4 rounded-xl bg-slate-900/50 border border-slate-700/30 hover:border-slate-600/50 transition-colors">
                                <div class="flex-1">
                                    <label for="nilai_{{ $nilai->id }}" class="text-sm font-medium text-white">
                                        @if ($type === 'soft')
                                            {{ $nilai->komponenSoft->nama ?? 'Komponen tidak ditemukan' }}
                                        @elseif ($type === 'wirausaha')
                                            {{ $nilai->komponenWirausaha->nama ?? 'Komponen tidak ditemukan' }}
                                        @else
                                            {{ $nilai->komponenHard->nama ?? 'Komponen tidak ditemukan' }}
                                        @endif
                                    </label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="number" id="nilai_{{ $nilai->id }}" name="scores[{{ $nilai->id }}]"
                                        value="{{ $nilai->nilai }}" step="0.01" min="0" max="100"
                                        class="w-28 px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm text-center focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <span class="text-xs text-slate-500 w-8">/ 100</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-700/50">
                        <a href="{{ route('admin.pkl.nilai.index', ['tab' => $type]) }}"
                            class="px-6 py-2.5 text-slate-400 hover:text-white font-medium rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div
                            class="w-16 h-16 rounded-full {{ $type === 'soft' ? 'bg-emerald-500/10' : ($type === 'wirausaha' ? 'bg-amber-500/10' : 'bg-blue-500/10') }} flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 {{ $type === 'soft' ? 'text-emerald-400/50' : ($type === 'wirausaha' ? 'text-amber-400/50' : 'text-blue-400/50') }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-slate-400 mb-2">Belum ada data nilai untuk siswa ini</p>
                        <p class="text-slate-500 text-sm">Silakan tambahkan nilai melalui halaman penilaian PKL</p>
                        <a href="{{ route('admin.pkl.nilai.index', ['tab' => $type]) }}"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2 text-sm text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Penilaian PKL
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
@endsection