@extends('layouts.admin')

@section('title', 'Data Dudi')

@section('content')
    <div class="space-y-6" x-data="{ showImportModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Data Dudi</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola data Dunia Usaha dan Dunia Industri</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showImportModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/10 text-emerald-400 font-medium rounded-xl hover:bg-emerald-500/20 transition-all border border-emerald-500/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.dudi.export') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-500/10 text-teal-400 font-medium rounded-xl hover:bg-teal-500/20 transition-all border border-teal-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Excel
                </a>
                <a href="{{ route('admin.dudi.exportPdf') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-500/10 text-rose-400 font-medium rounded-xl hover:bg-rose-500/20 transition-all border border-rose-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDF
                </a>
                <button onclick="openModal('addModal')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Dudi</span>
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-400">Total Dudi</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" action="{{ route('admin.dudi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/alamat/bidang usaha..."
                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama Dudi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Alamat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Telepon</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Bidang Usaha</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($dudis as $index => $dudi)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $dudis->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-white">{{ $dudi->nama }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 max-w-xs truncate">{{ $dudi->alamat ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $dudi->telepon ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $dudi->bidang_usaha ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="openEditModal({{ json_encode($dudi) }})" title="Edit"
                                            class="p-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/40 rounded-lg transition-colors cursor-pointer border border-blue-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.dudi.destroy', $dudi->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
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
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data Dudi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($dudis->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $dudis->links() }}
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
                        <h3 class="text-lg font-bold text-white">Import Data Dudi</h3>
                        <button @click="showImportModal = false"
                            class="p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.dudi.import') }}" method="POST" enctype="multipart/form-data"
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
                                        <li>Kolom wajib: nama</li>
                                        <li>Kolom opsional: alamat, telepon, bidang_usaha</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('admin.dudi.template') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors text-sm">
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
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20">
                                Import Data
                            </button>
                            <button type="button" @click="showImportModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-6">Tambah Data Dudi</h3>
                <form action="{{ route('admin.dudi.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Dudi <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Telepon</label>
                            <input type="text" name="telepon"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Bidang Usaha</label>
                            <input type="text" name="bidang_usaha"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        
                        <!-- Location Section -->
                        <div class="pt-4 border-t border-slate-700/50">
                            <h4 class="text-sm font-semibold text-slate-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Lokasi untuk Absensi PKL
                            </h4>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Latitude</label>
                                    <input type="text" name="latitude" placeholder="-6.200000"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Longitude</label>
                                    <input type="text" name="longitude" placeholder="106.816666"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Radius (meter)</label>
                                    <input type="number" name="radius" value="100" min="10" max="1000"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Koordinat digunakan untuk validasi absensi PKL siswa.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-6">Edit Data Dudi</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Dudi <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" id="editNama" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Alamat</label>
                            <textarea name="alamat" id="editAlamat" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Telepon</label>
                            <input type="text" name="telepon" id="editTelepon"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Bidang Usaha</label>
                            <input type="text" name="bidang_usaha" id="editBidangUsaha"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        
                        <!-- Location Section -->
                        <div class="pt-4 border-t border-slate-700/50">
                            <h4 class="text-sm font-semibold text-slate-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Lokasi untuk Absensi PKL
                            </h4>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Latitude</label>
                                    <input type="text" name="latitude" id="editLatitude" placeholder="-6.200000"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Longitude</label>
                                    <input type="text" name="longitude" id="editLongitude" placeholder="106.816666"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-1">Radius (meter)</label>
                                    <input type="number" name="radius" id="editRadius" value="100" min="10" max="1000"
                                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Koordinat digunakan untuk validasi absensi PKL siswa.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        function openEditModal(dudi) {
            document.getElementById('editForm').action = `/admin/dudi/${dudi.id}`;
            document.getElementById('editNama').value = dudi.nama || '';
            document.getElementById('editAlamat').value = dudi.alamat || '';
            document.getElementById('editTelepon').value = dudi.telepon || '';
            document.getElementById('editBidangUsaha').value = dudi.bidang_usaha || '';
            document.getElementById('editLatitude').value = dudi.latitude || '';
            document.getElementById('editLongitude').value = dudi.longitude || '';
            document.getElementById('editRadius').value = dudi.radius || 100;
            openModal('editModal');
        }
    </script>

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