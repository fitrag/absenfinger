@extends('layouts.admin')

@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
    <div class="space-y-6" x-data="guruPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data guru dan karyawan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Guru
                </button>
                <button @click="showImportModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-green-700 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if(session('import_errors'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mt-4">
                <p class="font-bold mb-2">Terjadi kesalahan pada baris berikut:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form action="{{ route('admin.guru.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, NIP..."
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500 flex-1 min-w-[200px]">



                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.guru.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Reset
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NIP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NUPTK</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                No Tlp</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($gurus as $index => $guru)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $gurus->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $guru->username }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $guru->nip ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $guru->nuptk ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-white font-medium">{{ $guru->nama }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $guru->no_tlp ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal({{ $guru->toJson() }})"
                                            class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-colors cursor-pointer"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.guru.destroy', $guru) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin hapus data guru ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer"
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
                                <td colspan="11" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada data guru
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($gurus->hasPages())
            <div class="flex justify-center">
                {{ $gurus->links() }}
            </div>
        @endif

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-16 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[85vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Tambah Guru</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Username *</label>
                                <input type="text" name="username" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="guru001">
                                <p class="text-xs text-slate-500 mt-1">Username akan digunakan sebagai password</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap *</label>
                                <input type="text" name="nama" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="Nama Guru">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">NIP</label>
                                <input type="text" name="nip"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="123456789">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">NUPTK</label>
                                <input type="text" name="nuptk"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="9876543210">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir</label>
                                <input type="text" name="tmpt_lhr"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="Kota Lahir">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                                <select name="jen_kel"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">Pilih Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">No Telepon</label>
                                <input type="text" name="no_tlp"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="081234567890">
                            </div>
                        </div>



                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Edit Guru</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/guru/' + editGuru.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Username *</label>
                                <input type="text" name="username" x-model="editGuru.username" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap *</label>
                                <input type="text" name="nama" x-model="editGuru.nama" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">NIP</label>
                                <input type="text" name="nip" x-model="editGuru.nip"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">NUPTK</label>
                                <input type="text" name="nuptk" x-model="editGuru.nuptk"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir</label>
                                <input type="text" name="tmpt_lhr" x-model="editGuru.tmpt_lhr"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr" x-model="editGuru.tgl_lhr"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                                <select name="jen_kel" x-model="editGuru.jen_kel"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">Pilih Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">No Telepon</label>
                                <input type="text" name="no_tlp" x-model="editGuru.no_tlp"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>



                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="showImportModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showImportModal = false" @keydown.escape.window="showImportModal = false">

            <div x-show="showImportModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-emerald-900/95 to-green-900/95 border border-emerald-500/30 rounded-2xl shadow-2xl shadow-emerald-500/20">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-emerald-500/30 bg-emerald-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Import Data Guru</h3>
                        <p class="text-sm text-emerald-300/70 mt-0.5">Upload file Excel untuk import data guru</p>
                    </div>
                    <button @click="showImportModal = false"
                        class="text-emerald-300 hover:text-white transition-colors p-1 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-6">
                    @csrf

                    <!-- File Input -->
                    <div>
                        <label class="block text-sm font-medium text-emerald-200 mb-2">File Excel</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-500 file:text-white file:cursor-pointer file:font-medium hover:file:bg-emerald-600">
                        <p class="text-xs text-emerald-300/60 mt-2">Format yang didukung: .xlsx, .xls (maksimal 10MB)
                        </p>
                    </div>

                    <!-- Format Info -->
                    <div class="border border-emerald-500/30 rounded-xl overflow-hidden bg-emerald-950/30">
                        <div class="bg-emerald-800/30 px-4 py-3 border-b border-emerald-500/30">
                            <span class="text-sm font-medium text-emerald-200">Format Kolom File</span>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-emerald-500/20">
                                            <th
                                                class="px-3 py-2 text-left text-xs font-semibold text-emerald-300 uppercase">
                                                Kolom</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-semibold text-emerald-300 uppercase">
                                                Keterangan</th>
                                            <th
                                                class="px-3 py-2 text-center text-xs font-semibold text-emerald-300 uppercase">
                                                Wajib</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-500/20">
                                        <tr>
                                            <td class="px-3 py-2 text-emerald-100 font-mono">username</td>
                                            <td class="px-3 py-2 text-slate-300">Username untuk login (juga jadi password)
                                            </td>
                                            <td class="px-3 py-2 text-center text-emerald-400">✓</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 text-emerald-100 font-mono">nama</td>
                                            <td class="px-3 py-2 text-slate-300">Nama lengkap guru</td>
                                            <td class="px-3 py-2 text-center text-emerald-400">✓</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 text-emerald-100 font-mono">nip</td>
                                            <td class="px-3 py-2 text-slate-300">Nomor Induk Pegawai</td>
                                            <td class="px-3 py-2 text-center text-slate-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 text-emerald-100 font-mono">nuptk</td>
                                            <td class="px-3 py-2 text-slate-300">Nomor Unik Pendidik dan Tenaga Kependidikan
                                            </td>
                                            <td class="px-3 py-2 text-center text-slate-500">-</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template -->
                    <a href="{{ route('admin.guru.template') }}"
                        class="inline-flex items-center gap-2 text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-5 border-t border-emerald-500/30">
                        <button type="button" @click="showImportModal = false"
                            class="px-5 py-2.5 bg-emerald-800/50 border border-emerald-500/30 text-white font-medium rounded-xl hover:bg-emerald-800 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-green-600 transition-all shadow-lg shadow-emerald-500/20 cursor-pointer">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function guruPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showImportModal: false,
                editGuru: {},

                openEditModal(guru) {
                    this.editGuru = guru;
                    this.showEditModal = true;
                }
            }
        }
    </script>
@endsection