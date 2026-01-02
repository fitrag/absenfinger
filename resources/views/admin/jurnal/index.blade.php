@extends('layouts.admin')

@section('title', 'Data Jurnal Guru')
@section('page-title', 'Data Jurnal Guru')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Jurnal Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Daftar guru dan status pengisian jurnal</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran</label>
                    <select name="tp_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua TP</option>
                        @foreach($tpList as $tp)
                            <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nm_tp }} {{ $tp->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Cari Guru</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIP..."
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500">
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50 bg-slate-800/30">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Nama Guru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">NIP</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">Jumlah Jurnal</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($gurus as $index => $guru)
                            @php
                                $jurnalCount = $guruJurnalCounts[$guru->id] ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-white font-medium">{{ $guru->nama }}</td>
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $guru->nip ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $jurnalCount > 0 ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-500/20 text-slate-400' }}">
                                        {{ $jurnalCount }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($jurnalCount > 0)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Sudah Mengisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Belum Mengisi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($jurnalCount > 0)
                                        <a href="{{ route('admin.jurnal.show', $guru->id) }}?tp_id={{ $tpId }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-blue-400 hover:text-blue-300 bg-blue-500/20 hover:bg-blue-500/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-slate-500 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data guru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
