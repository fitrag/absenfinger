@extends('layouts.admin')

@section('title', 'Ketidakhadiran Guru')
@section('page-title', 'Ketidakhadiran Guru')

@section('content')
    @php
        $userRoles = session('user_roles', []);
        $isKepsek = in_array('Kepsek', $userRoles);
    @endphp
    <div class="space-y-6" x-data="ketidakhadiranPage()">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Ketidakhadiran Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data ketidakhadiran guru (sakit, izin, alpha)</p>
            </div>
            @if(!$isKepsek)
                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Data
                </button>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['sakit'] }}</p>
                        <p class="text-xs text-slate-400">Sakit</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['izin'] }}</p>
                        <p class="text-xs text-slate-400">Izin</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['alpha'] }}</p>
                        <p class="text-xs text-slate-400">Alpha</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-400">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.guru-piket.ketidakhadiran') }}" method="GET"
                class="flex items-center gap-3 w-full sm:w-auto">
                <input type="date" name="date" value="{{ $date }}"
                    class="px-4 py-2 bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50"
                    onchange="this.form.submit()">
            </form>
            <form action="{{ route('admin.guru-piket.ketidakhadiran') }}" method="GET"
                class="flex items-center gap-3 w-full sm:w-auto">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari guru..."
                    class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500">
                <button type="submit"
                    class="px-3 py-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50 bg-slate-800/20">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-16">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Guru</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Jam Ke</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Keterangan</th>
                            @if(!$isKepsek)
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                              @endif
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($absences as $index => $absence)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $absence->guru->nama ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $absence->guru->nip ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $colors = [
                                            'sakit' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'izin' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'alpha' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                        ];
                                        $color = $colors[$absence->status] ?? 'bg-slate-500/10 text-slate-400';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                        {{ ucfirst($absence->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    {{ $absence->kelas_names }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    {{ $absence->jam_ke ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-400 truncate max-w-xs">
                                    {{ $absence->ket ?? '-' }}
                                </td>
                                        @if(!$isKepsek)
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        @click="openEditModal({{ $absence->id }}, '{{ $absence->status }}', {{ json_encode($absence->kelas_ids ?? []) }}, '{{ addslashes($absence->jam_ke ?? '') }}', '{{ addslashes($absence->ket ?? '') }}')"
                                                        class="text-amber-400 hover:text-amber-300 transition-colors cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.guru-piket.ketidakhadiran.destroy', $absence->id) }}"
                                                        method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                   </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data ketidakhadiran untuk tanggal ini.</p>
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
                    <h3 class="text-lg font-bold text-white">Tambah Ketidakhadiran</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.guru-piket.ketidakhadiran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div class="space-y-4">
                        <!-- Guru Select -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Guru <span
                                    class="text-rose-400">*</span></label>
                            <select name="guru_id" x-model="formData.guru_id"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }} {{ $g->nip ? '(' . $g->nip . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status <span
                                    class="text-rose-400">*</span></label>
                            <div class="flex gap-4">
                                @foreach(['sakit', 'izin', 'alpha'] as $s)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="{{ $s }}" x-model="formData.status"
                                            class="text-blue-500 focus:ring-blue-500 bg-slate-800 border-slate-700">
                                        <span class="text-sm text-slate-300 capitalize">{{ $s }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kelas Multi-Select -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas</label>

                            <!-- Selected Kelas Tags -->
                            <div class="flex flex-wrap gap-2 mb-3" x-show="formData.selectedKelas.length > 0">
                                <template x-for="id in formData.selectedKelas" :key="id">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                        <span x-text="getKelasName(id)"></span>
                                        <button type="button" @click="removeKelas(id)"
                                            class="hover:text-rose-400 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="kelas_ids[]" :value="id">
                                    </span>
                                </template>
                            </div>

                            <!-- Kelas Dropdown -->
                            <div class="relative">
                                <select @change="addKelas($event.target.value); $event.target.value = ''"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Tambah Kelas --</option>
                                    <template x-for="kelas in availableKelas" :key="kelas.id">
                                        <option :value="kelas.id" x-text="kelas.nm_kls"></option>
                                    </template>
                                </select>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Pilih satu atau lebih kelas</p>
                        </div>

                        <!-- Jam Ke -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Jam Ke</label>
                            <input type="text" name="jam_ke" x-model="formData.jam_ke" placeholder="Contoh: 1-2, 3, 5-6"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Keterangan</label>
                            <textarea name="ket" x-model="formData.ket" rows="3" placeholder="Keterangan tambahan..."
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 text-slate-400 hover:text-white cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl cursor-pointer">Simpan</button>
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
                    <h3 class="text-lg font-bold text-white">Edit Ketidakhadiran</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form :action="'{{ route('admin.guru-piket.ketidakhadiran') }}/' + editId" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status <span
                                    class="text-rose-400">*</span></label>
                            <div class="flex gap-4">
                                @foreach(['sakit', 'izin', 'alpha'] as $s)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="{{ $s }}" x-model="formData.status"
                                            class="text-blue-500 focus:ring-blue-500 bg-slate-800 border-slate-700">
                                        <span class="text-sm text-slate-300 capitalize">{{ $s }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kelas Multi-Select -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas</label>
                            <div class="flex flex-wrap gap-2 mb-3" x-show="formData.selectedKelas.length > 0">
                                <template x-for="id in formData.selectedKelas" :key="id">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                        <span x-text="getKelasName(id)"></span>
                                        <button type="button" @click="removeKelas(id)"
                                            class="hover:text-rose-400 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="kelas_ids[]" :value="id">
                                    </span>
                                </template>
                            </div>
                            <div class="relative">
                                <select @change="addKelas($event.target.value); $event.target.value = ''"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Tambah Kelas --</option>
                                    <template x-for="kelas in availableKelas" :key="kelas.id">
                                        <option :value="kelas.id" x-text="kelas.nm_kls"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Jam Ke -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Jam Ke</label>
                            <input type="text" name="jam_ke" x-model="formData.jam_ke" placeholder="Contoh: 1-2, 3, 5-6"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Keterangan</label>
                            <textarea name="ket" x-model="formData.ket" rows="3"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
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

    <script>
        function ketidakhadiranPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                editId: null,
                kelasList: @json($kelasList),
                formData: {
                    guru_id: '',
                    status: 'sakit',
                    selectedKelas: [],
                    jam_ke: '',
                    ket: ''
                },

                get availableKelas() {
                    return this.kelasList.filter(k => !this.formData.selectedKelas.includes(k.id));
                },

                openAddModal() {
                    this.formData = { guru_id: '', status: 'sakit', selectedKelas: [], jam_ke: '', ket: '' };
                    this.showAddModal = true;
                },

                openEditModal(id, status, kelasIds, jamKe, ket) {
                    this.editId = id;
                    this.formData = {
                        guru_id: '',
                        status: status,
                        selectedKelas: kelasIds || [],
                        jam_ke: jamKe || '',
                        ket: ket
                    };
                    this.showEditModal = true;
                },

                addKelas(id) {
                    if (id && !this.formData.selectedKelas.includes(parseInt(id))) {
                        this.formData.selectedKelas.push(parseInt(id));
                    }
                },

                removeKelas(id) {
                    this.formData.selectedKelas = this.formData.selectedKelas.filter(k => k !== id);
                },

                getKelasName(id) {
                    const kelas = this.kelasList.find(k => k.id === id);
                    return kelas ? kelas.nm_kls : '';
                }
            }
        }
    </script>
@endsection