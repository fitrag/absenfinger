@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
    <div class="space-y-6" x-data="{ showImportModal: false, showPrintModal: false, showExportModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data siswa yang terdaftar</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.naikKelas') }}"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 text-amber-400 font-medium rounded-xl hover:bg-amber-500/20 transition-all border border-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Naik Kelas
                </a>
                <button @click="showExportModal = true"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-sky-500/10 text-sky-400 font-medium rounded-xl hover:bg-sky-500/20 transition-all border border-sky-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </button>
                <button @click="showPrintModal = true"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-rose-500/10 text-rose-400 font-medium rounded-xl hover:bg-rose-500/20 transition-all border border-rose-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Absensi
                </button>
                <button @click="showImportModal = true"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-400 font-medium rounded-xl hover:bg-emerald-500/20 transition-all border border-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.students.create') }}"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $totalStudents }}</p>
                        <p class="text-xs text-slate-400">Total Siswa</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $activeStudents }}</p>
                        <p class="text-xs text-slate-400">Siswa Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form id="filterForm" action="{{ route('admin.students.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Kelas -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Kelas</label>
                    <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nm_kls }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                    <select name="status" onchange="document.getElementById('filterForm').submit()"
                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Semua
                        </option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NIS, NISN, Nama..."
                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500">
                </div>

                <!-- Submit -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="cursor-pointer flex-1 px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                        class="cursor-pointer px-4 py-2 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-600 transition-colors text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Per Page & Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <!-- Per Page Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800/50">
                <span class="text-sm text-slate-400">Total: <span
                        class="font-medium text-white">{{ $students instanceof \Illuminate\Pagination\LengthAwarePaginator ? $students->total() : $students->count() }}</span>
                    siswa</span>
                <form action="{{ route('admin.students.index') }}" method="GET" class="flex items-center gap-2">
                    @if(request('kelas_id'))<input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">@endif
                    @if(request('status') !== null)<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <label class="text-sm text-slate-400">Tampilkan:</label>
                    <select name="perPage" onchange="this.form.submit()"
                        class="cursor-pointer px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500">
                        <option value="36" {{ request('perPage', 36) == 36 ? 'selected' : '' }}>36</option>
                        <option value="72" {{ request('perPage') == 72 ? 'selected' : '' }}>72</option>
                        <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NIS/NISN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jurusan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">
                                    {{ $students instanceof \Illuminate\Pagination\LengthAwarePaginator ? $students->firstItem() + $index : $index + 1 }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-medium text-xs">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-white">{{ $student->name }}</span>
                                            <p class="text-xs text-slate-400">
                                                {{ $student->jen_kel == 'L' ? 'Laki-laki' : ($student->jen_kel == 'P' ? 'Perempuan' : '-') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <span class="text-sm text-slate-300 font-mono">{{ $student->nis }}</span>
                                        @if($student->nisn)
                                            <p class="text-xs text-slate-500 font-mono">{{ $student->nisn }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($student->jurusan)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            {{ $student->jurusan->paket_keahlian }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $student->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.students.show', $student) }}"
                                            class="cursor-pointer p-1.5 text-slate-400 hover:text-blue-400 transition-colors"
                                            title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                            class="cursor-pointer p-1.5 text-slate-400 hover:text-amber-400 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus siswa ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="cursor-pointer p-1.5 text-slate-400 hover:text-rose-400 transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data siswa</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
                <div class="px-4 py-3 border-t border-slate-800/50">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        <!-- Import Modal -->
        <div x-show="showImportModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-init="$watch('showImportModal', value => document.body.classList.toggle('overflow-hidden', value))"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showImportModal = false" style="display: none;">

            <div x-show="showImportModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-2xl">

                <div
                    class="rounded-2xl bg-slate-900 border-4 border-slate-400 shadow-[0_30px_70px_-15px_rgba(0,0,0,0.7)] ring-2 ring-white/20 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/50">
                        <h3 class="text-lg font-bold text-white">Import Data Siswa</h3>
                        <button @click="showImportModal = false"
                            class="cursor-pointer p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4">
                        @csrf
                        <div class="rounded-xl bg-blue-500/10 border border-blue-500/20 p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-300">
                                    <p class="font-medium mb-1">Petunjuk Import:</p>
                                    <ul class="list-disc list-inside text-xs text-blue-300/80 space-y-1">
                                        <li>Download template Excel terlebih dahulu</li>
                                        <li>Isi data sesuai format template</li>
                                        <li>Kolom wajib: finger_id, nis, nisn, name</li>
                                        <li>NIS akan menjadi username, NISN menjadi password</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('admin.students.template') }}"
                                class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Template Excel
                            </a>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">File Excel <span
                                    class="text-rose-400">*</span></label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30">
                            @error('file')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                            <button type="submit"
                                class="cursor-pointer flex-1 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20">
                                Import Data
                            </button>
                            <button type="button" @click="showImportModal = false"
                                class="cursor-pointer px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Print Absensi Modal -->
        <div x-show="showPrintModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showPrintModal = false"></div>
                <div
                    class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-700/50 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Cetak Daftar Hadir</h3>
                        <button @click="showPrintModal = false" class="cursor-pointer text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.students.printAbsensi') }}" method="GET" target="_blank">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-3">Pilih Kelas <span
                                        class="text-red-400">*</span></label>
                                <select name="kelas_id" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-rose-500/50">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-slate-300 mb-3">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-rose-500/50">
                            </div>
                        </div>
                        <div class="flex gap-4 pt-6 mt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="cursor-pointer flex-1 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-medium rounded-xl hover:from-rose-600 hover:to-pink-600 transition-all">
                                Cetak PDF
                            </button>
                            <button type="button" @click="showPrintModal = false"
                                class="cursor-pointer flex-1 px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export Excel Modal -->
        <div x-show="showExportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showExportModal = false"></div>
                <div
                    class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-700/50 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Export Data Siswa</h3>
                        <button @click="showExportModal = false" class="cursor-pointer text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.students.export') }}" method="GET">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-3">Pilih Kelas</label>
                                <select name="kelas_id"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-sky-500/50">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-slate-300 mb-3">Tanggal Export</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-sky-500/50">
                            </div>
                        </div>
                        <div class="flex gap-4 pt-6 mt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="cursor-pointer flex-1 px-4 py-2.5 bg-gradient-to-r from-sky-500 to-blue-500 text-white font-medium rounded-xl hover:from-sky-600 hover:to-blue-600 transition-all">
                                Export Excel
                            </button>
                            <button type="button" @click="showExportModal = false"
                                class="cursor-pointer flex-1 px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('import_errors'))
        <div x-data="{ show: true }" x-show="show"
            class="fixed bottom-20 right-4 max-w-md px-4 py-3 bg-rose-500/90 text-white rounded-xl shadow-lg z-50">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-sm mb-1">Error Import:</p>
                    <ul class="text-xs space-y-0.5">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="ml-auto text-white/80 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
@endsection