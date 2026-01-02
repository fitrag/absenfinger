@extends('layouts.admin')

@section('title', 'Mengajar Kelas')
@section('page-title', 'Mengajar Kelas')

@section('content')
    <div class="space-y-6" x-data="{ 
                                                                showAddModal: false,
                                                                showMapelModal: false,
                                                                selectedKelasList: [],
                                                                selectedMapelList: [],
                                                                kelasSearch: '',
                                                                mapelSearchNew: '',
                                                                showKelasDropdown: false,
                                                                showMapelSearchDropdown: false,
                                                                kelasList: {{ Js::from($kelasList) }},
                                                                allMapels: {{ Js::from($allMapels) }},
                                                                assignedMapelIds: {{ Js::from($mapelList->pluck('id')) }},
                                                                selectedTpId: {{ $activeTp ? $activeTp->id : 'null' }},
                                                                selectedMapelId: '',
                                                                existingAssignments: {{ Js::from($existingAssignments) }},
                                                                get filteredKelas() {
                                                                    let available = this.kelasList;

                                                                    // Filter assignments
                                                                    if (this.selectedTpId && this.selectedMapelId && 
                                                                        this.existingAssignments[this.selectedTpId] && 
                                                                        this.existingAssignments[this.selectedTpId][this.selectedMapelId]) {

                                                                        const assigned = this.existingAssignments[this.selectedTpId][this.selectedMapelId];
                                                                        available = available.filter(k => !assigned.includes(k.id));
                                                                    }

                                                                    if (this.kelasSearch === '') {
                                                                        return available;
                                                                    }
                                                                    return available.filter(k => 
                                                                        k.nm_kls.toLowerCase().includes(this.kelasSearch.toLowerCase())
                                                                    );
                                                                },
                                                                get availableMapels() {
                                                                    // Filter out already assigned mapels
                                                                    let available = this.allMapels.filter(m => !this.assignedMapelIds.includes(m.id));
                                                                    // Filter by search
                                                                    if (this.mapelSearchNew === '') {
                                                                        return available;
                                                                    }
                                                                    return available.filter(m => 
                                                                        m.nm_mapel.toLowerCase().includes(this.mapelSearchNew.toLowerCase())
                                                                    );
                                                                },
                                                                addKelas(kelas) {
                                                                    if (!this.selectedKelasList.some(k => k.id === kelas.id)) {
                                                                        this.selectedKelasList.push(kelas);
                                                                    }
                                                                    this.kelasSearch = '';
                                                                    this.showKelasDropdown = false;
                                                                },
                                                                addMapelNew(mapel) {
                                                                    if (!this.selectedMapelList.some(m => m.id === mapel.id)) {
                                                                        this.selectedMapelList.push(mapel);
                                                                    }
                                                                    this.mapelSearchNew = '';
                                                                    this.showMapelSearchDropdown = false;
                                                                },
                                                                removeKelas(index) {
                                                                    this.selectedKelasList.splice(index, 1);
                                                                },
                                                                removeMapelNew(index) {
                                                                    this.selectedMapelList.splice(index, 1);
                                                                },
                                                                isKelasSelected(id) {
                                                                    return this.selectedKelasList.some(k => k.id === id);
                                                                },
                                                                isMapelSelected(id) {
                                                                    return this.selectedMapelList.some(m => m.id === id);
                                                                }
                                                            }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Mengajar Kelas</h2>
                <p class="text-sm text-slate-400 mt-1">Daftar kelas yang anda ajar</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showMapelModal = true; mapelSearchNew = ''; selectedMapelList = []"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Tambah Mapel
                </button>
                <button @click="showAddModal = true; kelasSearch = ''; selectedKelasList = []"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kelas Ajar
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($groupedKelasAjar as $tpName => $mapelGroups)
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-800/50 bg-slate-800/30 flex items-center justify-between">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tahun Pelajaran: {{ $tpName }}
                        </h3>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-700 text-slate-300">
                            {{ $mapelGroups->count() }} Mapel
                        </span>
                    </div>
                    <div class="p-6 space-y-6">
                        @foreach($mapelGroups as $mapelName => $items)
                            <div class="space-y-3">
                                <!-- Mapel Header -->
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                                    <h4 class="font-semibold text-purple-300">{{ $mapelName }}</h4>
                                    <span class="text-xs text-slate-500">({{ $items->count() }} kelas)</span>
                                </div>
                                <!-- Classes Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 ml-4">
                                    @foreach($items as $item)
                                        <div class="relative group">
                                            <div
                                                class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-800/50 border border-slate-700/50 hover:border-blue-500/50 hover:bg-slate-800 transition-all duration-300">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                                    <span
                                                        class="text-sm font-bold text-blue-400">{{ substr($item->kelas->nm_kls, 0, 2) }}</span>
                                                </div>
                                                <h5 class="font-medium text-white text-center text-sm">{{ $item->kelas->nm_kls }}</h5>

                                                <!-- Delete Button (Overlay) -->
                                                <div
                                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <form action="{{ route('admin.guru.kelas-ajar.destroy', $item->id) }}" method="POST"
                                                        onsubmit="return confirm('Hapus kelas ini dari daftar mengajar anda?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1 rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white transition-colors"
                                                            title="Hapus">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white">Belum Ada Data</h3>
                    <p class="text-slate-400 mt-1">Anda belum memiliki kelas ajar.</p>
                </div>
            @endforelse
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-16 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[85vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Kelas Ajar</h3>
                        <p class="text-sm text-slate-400 mt-1">Tambahkan kelas yang anda ajar</p>
                    </div>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru.kelas-ajar.store') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Tahun Pelajaran -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran <span
                                    class="text-rose-400">*</span></label>
                            <select name="tp_id" required x-model="selectedTpId" @change="selectedKelasList = []"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @foreach($tpList as $tp)
                                    <option value="{{ $tp->id }}">
                                        {{ $tp->nm_tp }} {{ $tp->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mata Pelajaran <span
                                    class="text-rose-400">*</span></label>
                            <select name="m_mapel_id" required x-model="selectedMapelId" @change="selectedKelasList = []"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="" disabled selected>Pilih Mata Pelajaran</option>
                                @foreach($mapelList as $mapel)
                                    <option value="{{ $mapel->id }}">
                                        {{ $mapel->nm_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kelas (Multi-Select) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas <span
                                    class="text-rose-400">*</span></label>
                            <div class="relative">
                                <!-- Selected Chips -->
                                <div class="flex flex-wrap gap-2 mb-2" x-show="selectedKelasList.length > 0">
                                    <template x-for="(kelas, index) in selectedKelasList" :key="kelas.id">
                                        <div
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-sm border border-blue-500/30">
                                            <span x-text="kelas.nm_kls"></span>
                                            <button type="button" @click="removeKelas(index)" class="hover:text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <!-- Hidden input array -->
                                            <input type="hidden" name="kelas_ids[]" :value="kelas.id">
                                        </div>
                                    </template>
                                </div>

                                <input type="text" x-model="kelasSearch" @focus="showKelasDropdown = true"
                                    @click.outside="showKelasDropdown = false"
                                    placeholder="Ketik nama kelas untuk menambahkan..."
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500"
                                    @keydown.enter.prevent="if(filteredKelas.length > 0) addKelas(filteredKelas[0])">

                                <!-- Dropdown -->
                                <div x-show="showKelasDropdown && filteredKelas.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    <template x-for="kelas in filteredKelas" :key="kelas.id">
                                        <button type="button" @click="addKelas(kelas)"
                                            class="w-full px-4 py-2.5 text-left text-sm text-white hover:bg-slate-700/50 flex justify-between items-center group">
                                            <span x-text="kelas.nm_kls"></span>
                                            <span x-show="isKelasSelected(kelas.id)"
                                                class="text-blue-400 text-xs">Terpilih</span>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-500 mt-2" x-show="selectedKelasList.length === 0">Silakan pilih
                                    satu atau lebih kelas.</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20">
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

        <!-- Tambah Mapel Modal -->
        <div x-show="showMapelModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showMapelModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 max-h-[85vh] overflow-y-auto"
                @click.outside="showMapelModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Mapel yang Diajar</h3>
                        <p class="text-sm text-slate-400 mt-1">Pilih mata pelajaran yang anda ajarkan</p>
                    </div>
                    <button @click="showMapelModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru.kelas-ajar.storeMapel') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Selected Mapels -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mapel yang dipilih</label>

                            <!-- Selected chips -->
                            <div class="flex flex-wrap gap-2 mb-3" x-show="selectedMapelList.length > 0">
                                <template x-for="(mapel, index) in selectedMapelList" :key="mapel.id">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <span x-text="mapel.nm_mapel"></span>
                                        <button type="button" @click="removeMapelNew(index)" class="hover:text-rose-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="mapel_ids[]" :value="mapel.id">
                                    </span>
                                </template>
                            </div>

                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" x-model="mapelSearchNew" @focus="showMapelSearchDropdown = true"
                                    @click.outside="showMapelSearchDropdown = false"
                                    placeholder="Ketik untuk mencari mapel..."
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50 placeholder-slate-500">

                                <!-- Dropdown -->
                                <div x-show="showMapelSearchDropdown && availableMapels.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    <template x-for="mapel in availableMapels" :key="mapel.id">
                                        <button type="button" @click="addMapelNew(mapel)"
                                            class="w-full px-4 py-2.5 text-left text-sm text-white hover:bg-slate-700/50 flex justify-between items-center">
                                            <span x-text="mapel.nm_mapel"></span>
                                            <span x-show="isMapelSelected(mapel.id)"
                                                class="text-emerald-400 text-xs">Terpilih</span>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-500 mt-2" x-show="selectedMapelList.length === 0">Silakan pilih
                                    satu atau lebih mapel.</p>
                                <p class="text-xs text-slate-500 mt-2"
                                    x-show="availableMapels.length === 0 && mapelSearchNew === ''">Semua mapel sudah
                                    ditambahkan.</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit" :disabled="selectedMapelList.length === 0"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan
                            </button>
                            <button type="button" @click="showMapelModal = false"
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