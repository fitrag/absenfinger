@extends('layouts.admin')

@section('title', 'Jurnal ' . $guru->nama)
@section('page-title', 'Jurnal ' . $guru->nama)

@section('content')
    <div class="space-y-6">
        <!-- Back Button & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.jurnal.index') }}?tp_id={{ $tpId }}"
                    class="p-2 rounded-xl bg-slate-800/50 border border-slate-700/50 text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-white">Jurnal {{ $guru->nama }}</h2>
                    <p class="text-sm text-slate-400 mt-1">NIP: {{ $guru->nip ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
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
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Mapel</label>
                    <select name="mapel_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua Mapel</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}" {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nm_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Kelas</label>
                    <select name="kelas_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nm_kls }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Semester</label>
                    <select name="semester" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua Semester</option>
                        <option value="1" {{ $semester == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                        <option value="2" {{ $semester == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Grouped Journals -->
        @forelse($groupedJurnals as $key => $group)
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-800/50 bg-slate-800/30 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <h3 class="font-bold text-white">{{ $group['mapel']->nm_mapel ?? '-' }}</h3>
                            <p class="text-sm text-slate-400">
                                {{ $group['kelas']->nm_kls ?? '-' }} |
                                {{ $group['tp']->nm_tp ?? '-' }} |
                                Semester {{ $group['semester'] ?? '-' }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                            {{ count($group['items']) }} Entry
                        </span>
                    </div>
                    <a href="{{ route('admin.jurnal.pdf', $guru->id) }}?mapel_id={{ $group['mapel']->id ?? '' }}&kelas_id={{ $group['kelas']->id ?? '' }}&tp_id={{ $group['tp']->id ?? '' }}&semester={{ $group['semester'] ?? '' }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-medium rounded-xl hover:from-rose-600 hover:to-pink-600 transition-all shadow-lg shadow-rose-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-800/50 bg-slate-800/20">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">TM
                                    Ke</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Jam Ke</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Materi</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @foreach($group['items'] as $jurnal)
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-sm text-white font-medium">{{ $jurnal->tmke ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        {{ $jurnal->tanggal ? \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-300">{{ $jurnal->jam_ke ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">{{ Str::limit($jurnal->materi, 50) ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">{{ Str::limit($jurnal->kegiatan, 50) ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-slate-400">Tidak ada data jurnal</p>
                <p class="text-sm text-slate-500 mt-1">Guru ini belum mengisi jurnal untuk filter yang dipilih.</p>
            </div>
        @endforelse
    </div>
@endsection