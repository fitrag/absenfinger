@extends('layouts.admin')

@section('title', 'Absensi Siswa PKL')

@section('page-title', 'Absensi Siswa PKL')

@section('content')
    <div class="space-y-6" x-data="{ showPrintModal: false }">
        <!-- Header & Filter -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">Absensi Siswa PKL Bimbingan</h2>
                        <p class="text-slate-400 text-sm mt-1">
                            Tahun Pelajaran: <span
                                class="text-blue-400 font-medium">{{ $selectedTp->nm_tp ?? 'Semua' }}</span>
                        </p>
                    </div>
                    <button type="button" @click="showPrintModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-sm font-medium rounded-xl border border-emerald-500/30 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Absensi
                    </button>
                </div>

                <!-- Filters -->
                <form action="{{ route('admin.guru.pkl.absensi') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-800/50">

                    <!-- Tanggal -->
                    <div class="w-full sm:w-48">
                        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                    </div>

                    <!-- Tahun Pelajaran -->
                    <div class="w-full sm:w-48">
                        <select name="tp_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                            <option value="">Semua TP</option>
                            @foreach ($tpList as $tp)
                                <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}>
                                    {{ $tp->nm_tp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DUDI Filter -->
                    <div class="w-full sm:w-48">
                        <select name="dudi_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                            <option value="">Semua Tempat DUDI</option>
                            @foreach ($dudiList as $dudi)
                                <option value="{{ $dudi->id }}" {{ $dudiId == $dudi->id ? 'selected' : '' }}>
                                    {{ $dudi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa atau NIS..."
                            class="w-full pl-11 pr-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                        <svg class="w-5 h-5 text-slate-500 absolute left-4 top-2.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        @php
            $totalStudents = 0;
            $presentCount = 0;
            $notPresentCount = 0;
            $incompleteCount = 0;

            foreach ($groupedData as $items) {
                foreach ($items as $item) {
                    $totalStudents++;
                    if ($item['status'] === 'Lengkap') {
                        $presentCount++;
                    } elseif ($item['status'] === 'Belum Pulang') {
                        $incompleteCount++;
                    } else {
                        $notPresentCount++;
                    }
                }
            }
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Total Siswa</p>
                        <p class="text-xl font-bold text-white">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Hadir Lengkap</p>
                        <p class="text-xl font-bold text-emerald-400">{{ $presentCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Belum Pulang</p>
                        <p class="text-xl font-bold text-amber-400">{{ $incompleteCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Belum Absen</p>
                        <p class="text-xl font-bold text-red-400">{{ $notPresentCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content grouped by DUDI -->
        <div class="space-y-6">
            @forelse($groupedData as $dudiName => $items)
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl overflow-visible">
                    <!-- Group Header -->
                    <div
                        class="px-6 py-4 bg-slate-800/50 border-b border-slate-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ $dudiName }}</h3>
                                <p class="text-xs text-slate-400">
                                    {{ $items->first()['pkl']->dudi->alamat ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-slate-700/50 text-xs font-medium text-slate-300">
                            {{ count($items) }} Siswa
                        </span>
                    </div>

                    <!-- Table -->
                    <div>
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-800/30 border-b border-slate-700/50 text-left">
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Siswa
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Masuk
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                        Pulang
                                    </th>
                                    <th
                                        class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @foreach($items as $index => $item)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ substr($item['pkl']->student->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-white">{{ $item['pkl']->student->name }}</p>
                                                    <p class="text-xs text-slate-400">{{ $item['pkl']->student->nis }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium text-slate-300">
                                                {{ $item['pkl']->student->kelas->nm_kls ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($item['check_in'])
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-emerald-400 font-medium">
                                                        {{ \Carbon\Carbon::parse($item['check_in']->checktime)->format('H:i') }}
                                                    </span>
                                                    <span class="text-xs text-slate-500">Masuk</span>
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($item['check_out'])
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-blue-400 font-medium">
                                                        {{ \Carbon\Carbon::parse($item['check_out']->checktime)->format('H:i') }}
                                                    </span>
                                                    <span class="text-xs text-slate-500">Pulang</span>
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($item['status'] === 'Lengkap')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Lengkap
                                                </span>
                                            @elseif($item['status'] === 'Belum Pulang')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Belum Pulang
                                                </span>
                                            @elseif($item['status'] === 'Sakit')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5a.75.75 0 00-1.5 0v3.25H6a.75.75 0 000 1.5h4.25a.75.75 0 00.75-.75V7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Sakit
                                                </span>
                                            @elseif($item['status'] === 'Izin')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Izin
                                                </span>
                                            @elseif($item['status'] === 'Alpha')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Alpha
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Belum Absen
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" @click.outside="open = false" type="button"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-700/50 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg border border-slate-600/50 transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Ubah Status
                                                    <svg class="w-3 h-3" :class="{'rotate-180': open}" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="absolute right-0 mt-2 w-36 bg-slate-800 border border-slate-700 rounded-lg shadow-xl z-50"
                                                    style="display: none;">
                                                    <form action="{{ route('admin.guru.pkl.absensi.update_status') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="pkl_id" value="{{ $item['pkl']->id }}">
                                                        <input type="hidden" name="date" value="{{ $date }}">
                                                        <button type="submit" name="status" value="2"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-amber-400 hover:bg-slate-700/50 transition-colors cursor-pointer rounded-t-lg">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5a.75.75 0 00-1.5 0v3.25H6a.75.75 0 000 1.5h4.25a.75.75 0 00.75-.75V7z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Sakit
                                                        </button>
                                                        <button type="submit" name="status" value="3"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-blue-400 hover:bg-slate-700/50 transition-colors cursor-pointer">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Izin
                                                        </button>
                                                        <button type="submit" name="status" value="4"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-slate-700/50 transition-colors cursor-pointer rounded-b-lg">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Alpha
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-white mb-1">Belum ada data absensi</h3>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">
                            Belum ada siswa bimbingan PKL yang ditemukan untuk filter yang dipilih.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Print Modal -->
        <div x-show="showPrintModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.outside="showPrintModal = false"
                class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">Cetak Absensi PKL</h3>
                    </div>
                    <button @click="showPrintModal = false" type="button"
                        class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('admin.guru.pkl.absensi.print') }}" method="GET" target="_blank"
                    class="p-5 space-y-5">
                    <input type="hidden" name="tp_id" value="{{ $tpId }}">

                    <!-- DUDI Selection -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tempat PKL (DUDI)</label>
                        <select name="dudi_id" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all text-sm">
                            <option value="">Pilih DUDI</option>
                            @foreach ($dudiList as $dudi)
                                <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Dari Tanggal</label>
                            <input type="date" name="start_date" required
                                value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Sampai Tanggal</label>
                            <input type="date" name="end_date" required value="{{ now()->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all text-sm">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-700">
                        <button type="button" @click="showPrintModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection