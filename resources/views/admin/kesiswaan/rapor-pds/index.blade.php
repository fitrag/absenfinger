@extends('layouts.admin')

@section('title', 'Rapor PDS')
@section('page-title', 'Rapor PDS')

@push('styles')
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Rapor PDS Kesiswaan</h2>
                <p class="text-sm text-slate-400 mt-1">Lihat dan cetak rapor data PDS (Keterlambatan, Pelanggaran,
                    Konseling)</p>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Form -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Filter Rapor</h3>
            <form action="{{ route('admin.kesiswaan.rapor-pds.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Kelas <span
                                class="text-rose-400">*</span></label>
                        <select name="kelas_id" required
                            class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nm_kls }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Pelajaran -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Tahun Pelajaran <span
                                class="text-rose-400">*</span></label>
                        <select name="tp_id" required
                            class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            <option value="">-- Pilih Tahun Pelajaran --</option>
                            @foreach($tpList as $tp)
                                <option value="{{ $tp->id }}" {{ request('tp_id') == $tp->id || ($tpAktif && $tp->id == $tpAktif->id && !request('tp_id')) ? 'selected' : '' }}>
                                    {{ $tp->nm_tp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Semester <span
                                class="text-rose-400">*</span></label>
                        <select name="semester" required
                            class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all cursor-pointer">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Tampilkan Data
                    </button>
                    <a href="{{ route('admin.kesiswaan.rapor-pds.index') }}"
                        class="px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                        Reset
                    </a>
                    @if($data)
                        <a href="{{ route('admin.kesiswaan.rapor-pds.print', ['kelas_id' => request('kelas_id'), 'tp_id' => request('tp_id'), 'semester' => request('semester')]) }}"
                            target="_blank"
                            class="px-6 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-medium rounded-xl hover:from-rose-600 hover:to-pink-600 transition-all inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Rapor
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($data)
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Keterlambatan Card -->
                <div class="rounded-xl bg-gradient-to-br from-amber-500/10 to-orange-500/10 border border-amber-500/30 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white">Keterlambatan</h4>
                    </div>
                    <div class="space-y-1">
                        <p class="text-2xl font-bold text-amber-400">{{ $data->summary->total_siswa_terlambat }} <span
                                class="text-sm font-normal text-slate-400">siswa</span></p>
                        <p class="text-sm text-slate-400">{{ $data->summary->total_keterlambatan }}x terlambat •
                            {{ $data->summary->total_menit }} menit
                        </p>
                    </div>
                </div>

                <!-- Pelanggaran Card -->
                <div class="rounded-xl bg-gradient-to-br from-rose-500/10 to-red-500/10 border border-rose-500/30 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white">Pelanggaran</h4>
                    </div>
                    <div class="space-y-1">
                        <p class="text-2xl font-bold text-rose-400">{{ $data->summary->total_siswa_pelanggaran }} <span
                                class="text-sm font-normal text-slate-400">siswa</span></p>
                        <p class="text-sm text-slate-400">{{ $data->summary->total_pelanggaran }}x pelanggaran •
                            {{ $data->summary->total_poin }} poin
                        </p>
                    </div>
                </div>

                <!-- Konseling Card -->
                <div class="rounded-xl bg-gradient-to-br from-cyan-500/10 to-blue-500/10 border border-cyan-500/30 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white">Konseling</h4>
                    </div>
                    <div class="space-y-1">
                        <p class="text-2xl font-bold text-cyan-400">{{ $data->summary->total_siswa_konseling }} <span
                                class="text-sm font-normal text-slate-400">siswa</span></p>
                        <p class="text-sm text-slate-400">{{ $data->summary->total_konseling }}x sesi konseling</p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-400">Kelas:</span>
                    <span class="font-medium text-white">{{ $kelasInfo->nm_kls ?? '-' }}</span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400">Tahun Pelajaran:</span>
                    <span class="font-medium text-white">{{ $tpInfo->nm_tp ?? '-' }}</span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400">Semester:</span>
                    <span class="font-medium text-white">{{ request('semester') }}</span>
                </div>
            </div>

            <!-- Data Tables -->
            <div class="space-y-6">
                <!-- Keterlambatan Table -->
                @if($data->keterlambatan->count() > 0)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
                        <div class="px-6 py-4 border-b border-slate-800/50">
                            <h4 class="text-lg font-semibold text-amber-400">📋 Data Keterlambatan</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama Siswa
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Jumlah
                                            Terlambat</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Total Menit
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($data->keterlambatan as $index => $item)
                                        <tr class="hover:bg-slate-800/30">
                                            <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-white">{{ $item->student->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $item->student->nis }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $item->total_terlambat >= 3 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                                    {{ $item->total_terlambat }}x
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-slate-300">{{ $item->total_menit }} menit</td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('admin.kesiswaan.rapor-pds.print-student', ['student' => $item->student->id, 'tp_id' => request('tp_id'), 'semester' => request('semester')]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/30 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Pelanggaran Table -->
                @if($data->pelanggaran->count() > 0)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
                        <div class="px-6 py-4 border-b border-slate-800/50">
                            <h4 class="text-lg font-semibold text-rose-400">⚠️ Data Pelanggaran</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama Siswa
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Jumlah
                                            Pelanggaran</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Total Poin
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($data->pelanggaran as $index => $item)
                                        <tr class="hover:bg-slate-800/30">
                                            <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-white">{{ $item->student->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $item->student->nis }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                    {{ $item->total_pelanggaran }}x
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $item->total_poin }} poin
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('admin.kesiswaan.rapor-pds.print-student', ['student' => $item->student->id, 'tp_id' => request('tp_id'), 'semester' => request('semester')]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/30 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Konseling Table -->
                @if($data->konseling->count() > 0)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
                        <div class="px-6 py-4 border-b border-slate-800/50">
                            <h4 class="text-lg font-semibold text-cyan-400">💬 Data Konseling</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama Siswa
                                        </th>
                                        Konseling</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($data->konseling as $index => $item)
                                        <tr class="hover:bg-slate-800/30">
                                            <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-white">{{ $item->student->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $item->student->nis }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                                    {{ $item->total_konseling }}x
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('admin.kesiswaan.rapor-pds.print-student', ['student' => $item->student->id, 'tp_id' => request('tp_id'), 'semester' => request('semester')]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/30 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Empty State -->
                @if($data->keterlambatan->count() == 0 && $data->pelanggaran->count() == 0 && $data->konseling->count() == 0)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-400 text-lg">Tidak ada data PDS untuk filter yang dipilih</p>
                        <p class="text-slate-500 text-sm mt-2">Silakan pilih kelas, tahun pelajaran, dan semester yang berbeda</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Initial State -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-slate-400 text-lg">Pilih filter untuk menampilkan Rapor PDS</p>
                <p class="text-slate-500 text-sm mt-2">Pilih kelas, tahun pelajaran, dan semester di form filter di atas</p>
            </div>
        @endif
    </div>
@endsection