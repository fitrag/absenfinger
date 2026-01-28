@extends('layouts.admin')

@section('title', 'Guru Piket')
@section('page-title', 'Guru Piket')

@section('content')
    <div class="space-y-6" x-data="{ 
                showAddModal: false,
                showEditModal: false,
                editData: { id: null, guru_id: '', hari: '', is_active: true },
                selectedGurus: [],
                guruSearch: '',
                showGuruDropdown: false,
                gurus: {{ Js::from($availableGurus) }},
                get filteredGurus() {
                    return this.gurus.filter(g => 
                        (g.nama.toLowerCase().includes(this.guruSearch.toLowerCase()) || 
                         (g.nip && g.nip.includes(this.guruSearch))) &&
                        !this.selectedGurus.includes(g.id)
                    );
                },
                addGuru(guru) {
                    if (!this.selectedGurus.includes(guru.id)) {
                        this.selectedGurus.push(guru.id);
                    }
                    this.guruSearch = '';
                    this.showGuruDropdown = false;
                },
                removeGuru(id) {
                    this.selectedGurus = this.selectedGurus.filter(g => g !== id);
                },
                getGuruName(id) {
                    const guru = this.gurus.find(g => g.id === id);
                    return guru ? guru.nama : '';
                }
            }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Guru Piket</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data guru piket berdasarkan hari</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg shadow-purple-500/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Guru Piket
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($days as $day)
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-white">{{ $stats[$day] ?? 0 }}</p>
                            <p class="text-xs text-slate-400">{{ $day }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Search Box & Filter -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form id="searchForm" action="{{ route('admin.guru-piket-data.index') }}" method="GET"
                class="flex items-center gap-3">
                <div class="relative w-64">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        placeholder="Cari guru..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50">
                    @if(request('search'))
                        <a href="{{ route('admin.guru-piket-data.index', ['hari' => request('hari')]) }}"
                            class="absolute inset-y-0 right-0 pr-2 flex items-center cursor-pointer">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
                <select name="hari" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-purple-500/50">
                    <option value="">Semua Hari</option>
                    @foreach($days as $day)
                        <option value="{{ $day }}" {{ $filterHari == $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-3 py-2 bg-purple-500/20 text-purple-400 rounded-lg hover:bg-purple-500/30 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let debounceTimer;
                const searchInput = document.getElementById('searchInput');
                const searchForm = document.getElementById('searchForm');

                if (searchInput && searchForm) {
                    searchInput.addEventListener('input', function () {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function () {
                            searchForm.submit();
                        }, 500);
                    });
                }
            });
        </script>

        <!-- Table -->
        <!-- Grouped Data Display -->
        <div class="space-y-6">
            @foreach($days as $day)
                @php
                    $piketForDay = $guruPiketData->where('hari', $day);
                @endphp
                @if($piketForDay->count() > 0)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                        <!-- Day Header -->
                        <div class="px-6 py-4 border-b border-slate-800/50 flex items-center justify-between bg-slate-900">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">{{ $day }}</h3>
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-800 text-slate-400 border border-slate-700">
                                    {{ $piketForDay->count() }} Guru
                                </span>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-800/20 border-b border-slate-800/50">
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Guru</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIP</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($piketForDay as $piket)
                                        <tr class="hover:bg-slate-800/30 transition-colors">
                                            <td class="px-6 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-2 text-sm font-medium text-white">{{ $piket->guru->nama ?? '-' }}</td>
                                            <td class="px-6 py-2 text-sm text-slate-300 font-mono">{{ $piket->guru->nip ?? '-' }}</td>
                                            <td class="px-6 py-2">
                                                @if($piket->is_active)
                                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aktif</span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-2">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" 
                                                        @click="editData = { id: {{ $piket->id }}, guru_id: '{{ $piket->guru_id }}', hari: '{{ $piket->hari }}', is_active: {{ $piket->is_active ? 'true' : 'false' }} }; showEditModal = true"
                                                        class="p-2 bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 rounded-lg transition-colors cursor-pointer" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.guru-piket-data.destroy', $piket->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini? Role Piket akan dihapus jika guru tidak memiliki jadwal piket lain.')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($guruPiketData->isEmpty())
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-1">Belum ada data</h3>
                    <p class="text-slate-400">Silakan tambahkan guru piket baru</p>
                </div>
            @endif
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-lg m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Guru Piket</h3>
                        <p class="text-sm text-slate-400 mt-1">Pilih guru dan hari piket</p>
                    </div>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru-piket-data.store') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Guru Selector -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru <span
                                    class="text-rose-400">*</span></label>

                            <!-- Selected Gurus Chips -->
                            <div class="flex flex-wrap gap-2 mb-3" x-show="selectedGurus.length > 0">
                                <template x-for="id in selectedGurus" :key="id">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                        <span x-text="getGuruName(id)"></span>
                                        <button type="button" @click="removeGuru(id)" class="hover:text-rose-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="guru_id[]" :value="id">
                                    </span>
                                </template>
                            </div>

                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" x-model="guruSearch" @focus="showGuruDropdown = true"
                                    @click.outside="showGuruDropdown = false" placeholder="Ketik untuk mencari guru..."
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50 placeholder-slate-500">

                                <!-- Dropdown List -->
                                <div x-show="showGuruDropdown && filteredGurus.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    <template x-for="guru in filteredGurus" :key="guru.id">
                                        <button type="button" @click="addGuru(guru)"
                                            class="w-full px-4 py-2.5 text-left text-sm text-white hover:bg-slate-700/50 flex items-center justify-between">
                                            <span x-text="guru.nama"></span>
                                            <span x-show="guru.nip" x-text="guru.nip" class="text-slate-400 text-xs"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Ketik nama guru lalu klik untuk memilih</p>
                        </div>

                        <!-- Hari -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Hari <span
                                    class="text-rose-400">*</span></label>
                            <select name="hari" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                <option value="">-- Pilih Hari --</option>
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-slate-700/50 pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Info -->
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-300">Role "Piket" akan otomatis ditambahkan ke user guru saat
                                    data disimpan.</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg shadow-purple-500/20 cursor-pointer">
                                Simpan
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                                Batal
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
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-lg m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Edit Guru Piket</h3>
                        <p class="text-sm text-slate-400 mt-1">Perbarui data guru piket</p>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'{{ route('admin.guru-piket-data.index') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <!-- Guru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru <span
                                    class="text-rose-400">*</span></label>
                            <select name="guru_id" x-model="editData.guru_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                @foreach($allGurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}
                                        {{ $guru->nip ? '(' . $guru->nip . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hari -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Hari <span
                                    class="text-rose-400">*</span></label>
                            <select name="hari" x-model="editData.hari" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-bind:checked="editData.is_active"
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg z-50">
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection