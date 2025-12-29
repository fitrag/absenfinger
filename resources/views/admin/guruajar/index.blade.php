@extends('layouts.admin')

@section('title', 'Guru Mengajar')
@section('page-title', 'Guru Mengajar')

@section('content')
    <div class="space-y-6" x-data="{ 
                    showAddModal: false,
                    showEditModal: false,
                    editData: { id: null, guru_id: '', mapel_id: '', is_active: true },
                    selectedMapels: [],
                    mapelSearch: '',
                    showMapelDropdown: false,
                    mapels: {{ Js::from($mapels) }},
                    get filteredMapels() {
                        return this.mapels.filter(m => 
                            m.nm_mapel.toLowerCase().includes(this.mapelSearch.toLowerCase()) &&
                            !this.selectedMapels.includes(m.id)
                        );
                    },
                    addMapel(mapel) {
                        if (!this.selectedMapels.includes(mapel.id)) {
                            this.selectedMapels.push(mapel.id);
                        }
                        this.mapelSearch = '';
                        this.showMapelDropdown = false;
                    },
                    removeMapel(id) {
                        this.selectedMapels = this.selectedMapels.filter(m => m !== id);
                    },
                    getMapelName(id) {
                        const mapel = this.mapels.find(m => m.id === id);
                        return mapel ? mapel.nm_mapel : '';
                    }
                }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Guru Mengajar</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data guru dan mata pelajaran yang diajarkan</p>
            </div>
            <button @click="showAddModal = true; selectedMapels = []"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Guru Mengajar
            </button>
        </div>

        <!-- Stats Card -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $groupedData->count() }}</p>
                    <p class="text-xs text-slate-400">Total Guru dengan Mapel</p>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.guruajar.index') }}" method="GET" class="flex items-center gap-3">
                <div class="relative w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari guru atau mapel..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @if(request('search'))
                        <a href="{{ route('admin.guruajar.index') }}" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
                <button type="submit"
                    class="px-3 py-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Table (Grouped by Guru) -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">
                                No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Guru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NIP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Mapel yang Diajarkan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($groupedData as $index => $data)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-white">{{ $data['guru']->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $data['guru']->nip ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($data['mapels'] as $mapelData)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium {{ $mapelData['is_active'] ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20 ' }}">
                                                {{ $mapelData['mapel']->alias ?? $mapelData['mapel']->nm_mapel }}
                                                <button type="button"
                                                    @click="editData = { id: {{ $mapelData['id'] }}, guru_id: '{{ $data['guru']->id }}', mapel_id: '{{ $mapelData['mapel']->id }}', is_active: {{ $mapelData['is_active'] ? 'true' : 'false' }} }; showEditModal = true"
                                                    class="hover:text-amber-400" title="Edit">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('admin.guruajar.destroy', $mapelData['id']) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Hapus mapel ini dari guru?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="hover:text-rose-400" title="Hapus">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <span class="text-xs text-slate-500">{{ count($data['mapels']) }} mapel</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data guru mengajar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Guru Mengajar</h3>
                        <p class="text-sm text-slate-400 mt-1">Pilih guru dan mapel yang diajarkan</p>
                    </div>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guruajar.store') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Guru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru <span
                                    class="text-rose-400">*</span></label>
                            <select name="guru_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}
                                        {{ $guru->nip ? '(' . $guru->nip . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mapel (Searchable Combobox) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mapel <span
                                    class="text-rose-400">*</span></label>

                            <!-- Selected Mapels -->
                            <div class="flex flex-wrap gap-2 mb-3" x-show="selectedMapels.length > 0">
                                <template x-for="id in selectedMapels" :key="id">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                        <span x-text="getMapelName(id)"></span>
                                        <button type="button" @click="removeMapel(id)" class="hover:text-rose-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="mapels[]" :value="id">
                                    </span>
                                </template>
                            </div>

                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" x-model="mapelSearch" @focus="showMapelDropdown = true"
                                    @click.outside="showMapelDropdown = false" placeholder="Ketik untuk mencari mapel..."
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500">

                                <!-- Dropdown -->
                                <div x-show="showMapelDropdown && filteredMapels.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    <template x-for="mapel in filteredMapels" :key="mapel.id">
                                        <button type="button" @click="addMapel(mapel)"
                                            class="w-full px-4 py-2.5 text-left text-sm text-white hover:bg-slate-700/50 flex items-center justify-between">
                                            <span x-text="mapel.nm_mapel"></span>
                                            <span x-show="mapel.alias" x-text="'(' + mapel.alias + ')'"
                                                class="text-slate-400 text-xs"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Ketik nama mapel lalu klik untuk memilih</p>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-slate-700/50 pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit" :disabled="selectedMapels.length === 0"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
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
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-xl m-4"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Edit Guru Mengajar</h3>
                        <p class="text-sm text-slate-400 mt-1">Perbarui data</p>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'{{ route('admin.guruajar.index') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <!-- Guru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru</label>
                            <select name="guru_id" x-model="editData.guru_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mapel -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mapel</label>
                            <select name="mapel_id" x-model="editData.mapel_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nm_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-bind:checked="editData.is_active"
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
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

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection