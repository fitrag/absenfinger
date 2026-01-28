@extends('layouts.admin')

@section('title', 'Siswa Terlambat')
@section('page-title', 'Siswa Terlambat')

@push('styles')
    <style>
        /* Make date/time input icons white */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator,
        .date-white-icon::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        /* Scrollable table with fixed header */
        .terlambat-table {
            display: flex;
            flex-direction: column;
        }

        .terlambat-table thead,
        .terlambat-table tbody {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .terlambat-table tbody {
            display: block;
            max-height: 1200px;
            /* Approximately 30 rows */
            overflow-y: auto;
        }

        .terlambat-table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .terlambat-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Blinking animation for high-frequency late students */
        @keyframes blink-warning {

            0%,
            100% {
                background-color: rgba(239, 68, 68, 0.2);
                border-color: rgba(239, 68, 68, 0.5);
            }

            50% {
                background-color: rgba(239, 68, 68, 0.4);
                border-color: rgba(239, 68, 68, 0.8);
            }
        }

        .blink-warning {
            animation: blink-warning 1.5s ease-in-out infinite;
        }

        /* Subtle pulse for badges */
        @keyframes pulse-badge {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 8px 2px rgba(239, 68, 68, 0.3);
            }
        }

        .pulse-badge {
            animation: pulse-badge 2s ease-in-out infinite;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="siswaTerlambatPage()">
        <!-- Header & Stats -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Siswa Terlambat</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data keterlambatan siswa</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button @click="showPrintModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-medium rounded-xl hover:from-rose-600 hover:to-pink-600 transition-all shadow-lg shadow-rose-500/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Terlambat
                </button>
                <a href="{{ route('admin.kesiswaan.siswa-terlambat.rekap') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Rekap per Siswa
                </a>
                @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                    <button @click="openAddModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Data
                    </button>
                @endif
            </div>
        </div>

        <!-- Compact Stats -->
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="text-slate-400">Rekap:</span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <span class="font-bold">{{ $totalCount }}</span> Total
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">
                <span class="font-bold">{{ $pendingCount }}</span> Pending
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20">
                <span class="font-bold">{{ $diprosesCount }}</span> Diproses
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="font-bold">{{ $selesaiCount }}</span> Selesai
            </span>
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

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-3">
            <form id="filterForm" action="{{ route('admin.kesiswaan.siswa-terlambat.index') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="flex-1 min-w-[180px] px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500">

                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    onchange="document.getElementById('filterForm').submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 date-white-icon">

                <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <select name="semester" onchange="document.getElementById('filterForm').submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors text-sm cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-600 transition-colors text-sm">
                    Reset
                </a>
            </form>
        </div>

        <!-- Bulk Delete Action Bar -->
        @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
            <div x-show="deleteSelected.length > 0" x-cloak
                class="rounded-xl bg-red-500/10 border border-red-500/30 p-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-red-400">
                        <span class="font-bold" x-text="deleteSelected.length"></span> data dipilih
                    </span>
                    <button type="button" @click="deleteSelected = []"
                        class="text-xs px-2 py-1 bg-slate-700 text-slate-300 rounded-lg hover:bg-slate-600 cursor-pointer">
                        Batal Pilih
                    </button>
                </div>
                <form action="{{ route('admin.kesiswaan.siswa-terlambat.bulk-destroy') }}" method="POST"
                    @submit.prevent="if(confirm('Yakin hapus ' + deleteSelected.length + ' data terpilih?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in deleteSelected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih
                    </button>
                </form>
            </div>
        @endif

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
            <div class="overflow-x-auto">
                <table class="w-full terlambat-table">
                    <thead class="bg-slate-900">
                        <tr class="border-b border-slate-800/50">
                            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                <th
                                    class="px-3 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900 w-10">
                                    <input type="checkbox" @click="toggleAllDelete()" :checked="isAllDeleteSelected()"
                                        class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-red-500 focus:ring-red-500 cursor-pointer"
                                        title="Pilih Semua">
                                </th>
                            @endif
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Tanggal</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Jam Datang</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-rose-400 uppercase tracking-wider bg-slate-900">
                                Terlambat</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Alasan</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Status</th>
                            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                    Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($groupedData as $group)
                            <!-- Student Group Header -->
                            <tr
                                class="border-t-2 border-slate-700/50 {{ $group['total_terlambat'] >= 3 ? 'blink-warning' : 'bg-slate-800/40' }}">
                                <td colspan="{{ session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])) ? '8' : '6' }}"
                                    class="px-4 py-2">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            @if($group['total_terlambat'] >= 3)
                                                <span class="flex h-3 w-3 relative">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                                </span>
                                            @endif
                                            <div>
                                                <span
                                                    class="text-sm font-semibold {{ $group['total_terlambat'] >= 3 ? 'text-red-300' : 'text-white' }}">{{ $group['student_name'] }}</span>
                                                <span class="text-xs text-slate-400 ml-2">{{ $group['student_nis'] }} •
                                                    {{ $group['student_kelas'] }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium border {{ $group['total_terlambat'] >= 3 ? 'bg-red-500/30 text-red-300 border-red-500/50 pulse-badge' : 'bg-rose-500/20 text-rose-400 border-rose-500/30' }}">
                                                {{ $group['total_terlambat'] }}x terlambat
                                            </span>
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                                Total {{ $group['total_menit'] }} menit
                                            </span>
                                            <a href="{{ route('admin.kesiswaan.siswa-terlambat.print', $group['student_id']) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/30 transition-colors"
                                                title="Cetak laporan {{ $group['student_name'] }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                Cetak
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Student Items -->
                            @foreach($group['items'] as $index => $item)
                                <tr class="hover:bg-slate-800/30 transition-colors"
                                    :class="deleteSelected.includes({{ $item->id }}) ? 'bg-red-500/10' : ''">
                                    @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" value="{{ $item->id }}"
                                                :checked="deleteSelected.includes({{ $item->id }})"
                                                @click="toggleDeleteItem({{ $item->id }})"
                                                class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-red-500 focus:ring-red-500 cursor-pointer">
                                        </td>
                                    @endif
                                    <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-300">{{ $item->tanggal->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-center text-slate-300">
                                        {{ \Carbon\Carbon::parse($item->jam_datang)->format('H:i') }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            {{ $item->keterlambatan_menit }} mnt
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-slate-400 max-w-[200px] truncate">{{ $item->alasan ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium border {{ $item->status_badge }}">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="openEditModal({{ $item->toJson() }})"
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 rounded-lg text-xs font-medium transition-colors border border-blue-500/20 cursor-pointer"
                                                    title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('admin.kesiswaan.siswa-terlambat.destroy', $item) }}"
                                                    method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2 py-1.5 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg text-xs font-medium transition-colors border border-red-500/20 cursor-pointer"
                                                        title="Hapus">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])) ? '8' : '6' }}"
                                    class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data siswa terlambat</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>




        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-20"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[75vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Tambah Data Siswa Terlambat</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.kesiswaan.siswa-terlambat.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Row 1: Tanggal dan Jam -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal <span
                                        class="text-rose-400">*</span></label>
                                <input type="date" name="tanggal" required x-model="selectedTanggal"
                                    @change="loadLateStudents()"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Jam Masuk Seharusnya <span
                                        class="text-rose-400">*</span></label>
                                <input type="time" name="jam_masuk_seharusnya" required x-model="jamMasuk"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Jam Datang <span
                                        class="text-rose-400">*</span></label>
                                <input type="time" name="jam_datang" required x-model="jamDatang"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Row 2: Tahun Pelajaran dan Semester -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tahun Pelajaran</label>
                                <input type="hidden" name="tp_id" value="{{ $tpAktif->id ?? '' }}">
                                <input type="text" readonly value="{{ $tpAktif->nm_tp ?? 'Tidak ada TP aktif' }}"
                                    class="w-full px-4 py-2 bg-slate-800/30 border border-slate-700 rounded-xl text-slate-400 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Semester <span
                                        class="text-rose-400">*</span></label>
                                <select name="semester" required
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="Ganjil" {{ ($semesterAktif ?? 'Ganjil') === 'Ganjil' ? 'selected' : '' }}>
                                        Ganjil</option>
                                    <option value="Genap" {{ ($semesterAktif ?? 'Ganjil') === 'Genap' ? 'selected' : '' }}>
                                        Genap</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kelas Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Kelas <span
                                        class="text-rose-400">*</span></label>
                                <select x-model="selectedKelas" @change="loadLateStudents()"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Status <span
                                        class="text-rose-400">*</span></label>
                                <select name="status" required
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="selesai" selected>Selesai</option>
                                    <option value="pending">Pending</option>
                                    <option value="diproses">Diproses</option>
                                </select>
                            </div>
                        </div>

                        <!-- Selected Students Summary -->
                        <div x-show="selectedStudentsData.length > 0" class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Siswa Terpilih <span
                                    class="text-blue-400" x-text="'(' + selectedStudentsData.length + ')'"></span></label>
                            <div class="border border-blue-500/30 rounded-xl bg-blue-500/10 p-3">
                                <div class="space-y-2">
                                    <template x-for="student in selectedStudentsData" :key="student.id">
                                        <div class="p-2 bg-blue-500/20 border border-blue-500/30 rounded-lg">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2">
                                                    <input type="hidden" name="student_ids[]" :value="student.id">
                                                    <span class="text-sm text-white font-medium"
                                                        x-text="student.name"></span>
                                                    <span class="text-xs text-slate-400"
                                                        x-text="'(' + student.kelas + ')'"></span>
                                                    <span x-show="student.jumlah_terlambat > 0"
                                                        class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                                                        :class="student.jumlah_terlambat >= 3 ? 'bg-rose-500/30 text-rose-300' : 'bg-amber-500/20 text-amber-400'"
                                                        x-text="student.jumlah_terlambat + 'x'"></span>
                                                </div>
                                                <button type="button" @click="removeSelectedStudent(student.id)"
                                                    class="text-red-400 hover:text-red-300 cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <input type="text" :name="'alasan[' + student.id + ']'"
                                                placeholder="Alasan keterlambatan..." x-model="student.alasan"
                                                class="w-full px-2 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white text-xs focus:border-blue-500 placeholder-slate-500">
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="clearAllSelections()"
                                    class="mt-3 text-xs text-red-400 hover:text-red-300 cursor-pointer">
                                    Hapus Semua Pilihan
                                </button>
                            </div>
                        </div>

                        <!-- Student Selection Box -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Siswa dari Kelas</label>
                            <div class="border border-slate-700 rounded-xl overflow-hidden bg-slate-800/30">
                                <div
                                    class="bg-slate-800/50 px-4 py-2 border-b border-slate-700 flex flex-wrap justify-between items-center gap-2">
                                    <span class="text-sm text-slate-400">
                                        <span x-show="loading">(Memuat...)</span>
                                        <span x-show="!loading && lateStudents.length > 0"
                                            x-text="lateStudents.length + ' siswa tersedia'"></span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="toggleSelectAll(true)"
                                            x-show="lateStudents.length > 0"
                                            class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-300 rounded-lg hover:bg-emerald-500/30 cursor-pointer">
                                            Pilih Semua Kelas Ini
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <!-- Loading State -->
                                    <template x-if="loading">
                                        <div class="text-center text-slate-400 text-sm py-4">
                                            <svg class="animate-spin h-5 w-5 mx-auto mb-2 text-blue-400" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Memuat data...
                                        </div>
                                    </template>
                                    <!-- Empty State: No Kelas Selected -->
                                    <template x-if="!loading && !selectedKelas">
                                        <div class="text-center text-slate-400 text-sm py-4">
                                            Pilih kelas untuk menampilkan siswa
                                        </div>
                                    </template>
                                    <!-- Empty State: No Students -->
                                    <template x-if="!loading && selectedKelas && lateStudents.length === 0">
                                        <div class="text-center text-slate-400 text-sm py-4">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-emerald-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Semua siswa sudah terdata
                                        </div>
                                    </template>
                                    <!-- Student List -->
                                    <div x-show="!loading && lateStudents.length > 0" class="grid grid-cols-1 gap-2">
                                        <template x-for="student in lateStudents" :key="student.id">
                                            <div class="p-2 rounded-lg transition-colors border cursor-pointer"
                                                :class="isStudentSelected(student.id) ? 'bg-blue-500/20 border-blue-500/50' : (student.jumlah_terlambat >= 3 ? 'bg-rose-500/10 border-rose-500/30 hover:bg-rose-500/20' : 'bg-slate-800/30 border-slate-700/30 hover:bg-slate-800/50')"
                                                @click="toggleStudent(student)">
                                                <div class="flex items-start gap-2">
                                                    <input type="checkbox" :value="student.id"
                                                        :checked="isStudentSelected(student.id)"
                                                        @click.stop="toggleStudent(student)"
                                                        class="w-4 h-4 mt-0.5 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500 cursor-pointer">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex flex-wrap items-center gap-1">
                                                            <p class="text-sm text-white font-medium" x-text="student.name">
                                                            </p>
                                                            <span x-show="isStudentSelected(student.id)"
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/30 text-blue-300">
                                                                ✓
                                                            </span>
                                                            <span x-show="student.jumlah_terlambat > 0"
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                                                                :class="student.jumlah_terlambat >= 3 ? 'bg-rose-500/30 text-rose-300' : 'bg-amber-500/20 text-amber-400'"
                                                                x-text="student.jumlah_terlambat + 'x'"></span>
                                                        </div>
                                                        <p class="text-xs text-slate-400"
                                                            x-text="student.nis + ' • ' + student.kelas"></p>
                                                        <input type="text" :name="'alasan[' + student.id + ']'"
                                                            placeholder="Alasan keterlambatan..." @click.stop
                                                            x-show="isStudentSelected(student.id)"
                                                            class="w-full mt-1.5 px-2 py-1 bg-slate-800 border border-slate-700 rounded-lg text-white text-xs focus:border-blue-500 placeholder-slate-500">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"
                                placeholder="Keterangan tambahan"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 text-slate-400 hover:text-white cursor-pointer">Batal</button>
                            <button type="submit" :disabled="selectedStudents.length === 0"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-20"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[75vh] overflow-y-auto"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Edit Data Siswa Terlambat</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/kesiswaan/siswa-terlambat/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <!-- Siswa (readonly) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Siswa</label>
                            <input type="hidden" name="student_id" x-model="editData.student_id">
                            <input type="text" readonly :value="editData.student?.nis + ' - ' + editData.student?.name"
                                class="w-full px-4 py-2 bg-slate-800/30 border border-slate-700 rounded-xl text-slate-400">
                        </div>

                        <!-- Row: Tanggal dan Jam -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal <span
                                        class="text-rose-400">*</span></label>
                                <input type="date" name="tanggal" required x-model="editData.tanggal_formatted"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Jam Masuk <span
                                        class="text-rose-400">*</span></label>
                                <input type="time" name="jam_masuk_seharusnya" required
                                    x-model="editData.jam_masuk_seharusnya"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Jam Datang <span
                                        class="text-rose-400">*</span></label>
                                <input type="time" name="jam_datang" required x-model="editData.jam_datang"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Alasan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Alasan</label>
                            <input type="text" name="alasan" x-model="editData.alasan"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"
                                placeholder="Alasan keterlambatan">
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2" x-model="editData.keterangan"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"
                                placeholder="Keterangan tambahan"></textarea>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status <span
                                    class="text-rose-400">*</span></label>
                            <select name="status" required x-model="editData.status"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="showEditModal = false"
                                class="px-4 py-2 text-slate-400 hover:text-white cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl cursor-pointer">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <!-- Print Modal -->
            <div x-show="showPrintModal" x-cloak
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-20"
                @keydown.escape.window="showPrintModal = false">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-md m-4"
                    @click.outside="showPrintModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Cetak Laporan Terlambat</h3>
                        <button @click="showPrintModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.kesiswaan.siswa-terlambat.print-by-period') }}" method="GET" target="_blank">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Dari Tanggal</label>
                                    <input type="date" name="start_date" x-model="printStartDate" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 date-white-icon">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" x-model="printEndDate" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 date-white-icon">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Kelas</label>
                                <select name="kelas_id" x-model="printKelasId"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                                <button type="button" @click="showPrintModal = false"
                                    class="px-4 py-2 text-slate-400 hover:text-white cursor-pointer">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white rounded-xl cursor-pointer inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function siswaTerlambatPage() {
                return {
                    showAddModal: false,
                    showEditModal: false,
                    showPrintModal: false,
                        printStartDate: '{{ date("Y-m-01") }}',
                        printEndDate: '{{ date("Y-m-d") }}',
                        printKelasId: '',
                        selectedTanggal: '{{ date("Y-m-d") }}',
                        jamMasuk: '07:00',
                        selectedKelas: '',
                        selectedStudents: [],
                        selectedStudentsData: [], // Store full student data
                        jamDatang: '',
                        lateStudents: [],
                        loading: false,
                        editData: {},
                        deleteSelected: [],
                        allItemIds: [
                            @foreach($groupedData as $group)
                                @foreach($group['items'] as $item)
                                    {{ $item->id }},
                                @endforeach
                            @endforeach
                            ],

                        getCurrentTime() {
                            const now = new Date();
                            return now.toTimeString().slice(0, 5);
                        },

                        openAddModal() {
                            this.showAddModal = true;
                            this.selectedStudents = [];
                            this.selectedStudentsData = [];
                            this.selectedKelas = '';
                            this.jamDatang = this.getCurrentTime();
                            this.lateStudents = [];
                            this.$nextTick(() => {
                                this.loadLateStudents();
                            });
                        },

                        loadLateStudents() {
                            if (!this.selectedTanggal || !this.selectedKelas) {
                                this.lateStudents = [];
                                return;
                            }

                            this.loading = true;
                            let url = `{{ url('/admin/kesiswaan/siswa-terlambat/late-students') }}?tanggal=${this.selectedTanggal}`;
                            if (this.selectedKelas) {
                                url += `&kelas_id=${this.selectedKelas}`;
                            }

                            fetch(url)
                                .then(response => response.json())
                                .then(data => {
                                    this.lateStudents = data;
                                    // Don't reset selectedStudents - keep previous selections
                                    this.loading = false;
                                })
                                .catch(() => {
                                    this.loading = false;
                                });
                        },

                        toggleStudent(student) {
                            const index = this.selectedStudents.indexOf(student.id);
                            if (index === -1) {
                                this.selectedStudents.push(student.id);
                                // Add alasan property to student data
                                this.selectedStudentsData.push({ ...student, alasan: '' });
                            } else {
                                this.selectedStudents.splice(index, 1);
                                this.selectedStudentsData = this.selectedStudentsData.filter(s => s.id !== student.id);
                            }
                        },

                        isStudentSelected(studentId) {
                            return this.selectedStudents.includes(studentId);
                        },

                        updateAlasan(studentId, value) {
                            const student = this.selectedStudentsData.find(s => s.id === studentId);
                            if (student) {
                                student.alasan = value;
                            }
                        },

                        getAlasan(studentId) {
                            const student = this.selectedStudentsData.find(s => s.id === studentId);
                            return student ? student.alasan : '';
                        },

                        toggleSelectAll(checked) {
                            if (checked) {
                                // Add all visible students to selection
                                this.lateStudents.forEach(student => {
                                    if (!this.selectedStudents.includes(student.id)) {
                                        this.selectedStudents.push(student.id);
                                        this.selectedStudentsData.push({ ...student, alasan: '' });
                                    }
                                });
                            }
                        },

                        clearAllSelections() {
                            this.selectedStudents = [];
                            this.selectedStudentsData = [];
                        },

                        removeSelectedStudent(studentId) {
                            this.selectedStudents = this.selectedStudents.filter(id => id !== studentId);
                            this.selectedStudentsData = this.selectedStudentsData.filter(s => s.id !== studentId);
                        },

                        openEditModal(data) {
                            this.editData = {
                                ...data,
                                tanggal_formatted: data.tanggal.split('T')[0],
                                jam_datang: data.jam_datang.substring(0, 5),
                                jam_masuk_seharusnya: data.jam_masuk_seharusnya.substring(0, 5),
                            };
                            this.showEditModal = true;
                        },

                        toggleDeleteItem(id) {
                            const index = this.deleteSelected.indexOf(id);
                            if (index === -1) {
                                this.deleteSelected.push(id);
                            } else {
                                this.deleteSelected.splice(index, 1);
                            }
                        },

                        toggleAllDelete() {
                            if (this.isAllDeleteSelected()) {
                                this.deleteSelected = [];
                            } else {
                                this.deleteSelected = [...this.allItemIds];
                            }
                        },

                        isAllDeleteSelected() {
                            return this.allItemIds.length > 0 && this.deleteSelected.length === this.allItemIds.length;
                        }
                    }
                }
            </script>
@endsection