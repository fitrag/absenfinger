@extends('layouts.admin')

@section('title', 'Data PKL')

@section('content')
    <div class="space-y-6" x-data="pklPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Data PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola data Praktik Kerja Lapangan siswa</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- TP Setting Combobox -->
                <form action="{{ route('admin.pkl.setTp') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <label class="text-sm text-slate-400">TP Aktif:</label>
                    <select name="tp_id" onchange="this.form.submit()"
                        class="px-3 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-lg text-indigo-300 text-sm font-medium cursor-pointer">
                        @foreach($tpList as $tp)
                            <option value="{{ $tp->id }}" {{ $selectedTp && $selectedTp->id == $tp->id ? 'selected' : '' }} class="bg-slate-800 text-white">
                                {{ $tp->nm_tp }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <button @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah PKL</span>
                </button>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-400">Total Data PKL</p>
                    </div>
                </div>
                @if($selectedTp)
                    <span
                        class="inline-flex px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ $selectedTp->nm_tp }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" action="{{ route('admin.pkl.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tp_id" value="{{ $tpId }}">
                <select name="kelas_id"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>
                <select name="dudi_id"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua DUDI</option>
                    @foreach($dudiList as $dudi)
                        <option value="{{ $dudi->id }}" {{ $dudiId == $dudi->id ? 'selected' : '' }}>{{ $dudi->nama }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari..."
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs w-32">
                <button type="submit"
                    class="px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tempat PKL (DUDI)
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Pembimbing</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($pkls as $index => $pkl)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $pkls->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }} •
                                        {{ $pkl->student->kelas->nm_kls ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-white">{{ $pkl->dudi->nama ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 truncate max-w-xs">{{ $pkl->dudi->alamat ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-slate-300">{{ $pkl->pembimbingSekolah->nama ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openEditModal({{ json_encode($pkl) }})"
                                            class="p-1.5 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.pkl.destroy', $pkl->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer">
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
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data PKL</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pkls->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $pkls->links() }}
                </div>
            @endif
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showAddModal = false" style="display: none;">

            <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-sky-900/95 to-blue-900/95 border border-sky-500/30 rounded-2xl shadow-2xl shadow-blue-500/20">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Data PKL</h3>
                        <p class="text-sm text-sky-300/70 mt-0.5">TP: {{ $selectedTp?->nm_tp ?? '-' }}</p>
                    </div>
                    <button @click="showAddModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.pkl.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="tp_id" value="{{ $tpId }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Tempat PKL (DUDI) <span
                                    class="text-red-400">*</span></label>
                            <select name="dudi_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih DUDI</option>
                                @foreach($dudiList as $dudi)
                                    <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Pembimbing Sekolah</label>
                            <select name="pembimbing_sekolah_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Guru</option>
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Student Selection -->
                    <div class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                        <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <span class="text-sm font-medium text-sky-200">Pilih Siswa <span
                                        class="text-red-400">*</span></span>
                                <div class="flex items-center gap-2">
                                    <select x-model="selectedKelas" @change="loadStudents()"
                                        class="px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelasPklList as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="selectAll()"
                                        class="text-xs px-3 py-1.5 bg-emerald-500/20 text-emerald-300 rounded-lg hover:bg-emerald-500/30 transition-colors border border-emerald-500/30">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="deselectAll()"
                                        class="text-xs px-3 py-1.5 bg-slate-500/20 text-slate-300 rounded-lg hover:bg-slate-500/30 transition-colors border border-slate-500/30">
                                        Hapus Pilihan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto p-4">
                            <template x-if="isLoading">
                                <div class="text-center text-sky-300 text-sm py-4">
                                    <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-sky-400" fill="none"
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
                            <template x-if="!isLoading && students.length === 0 && selectedKelas">
                                <div class="text-center text-sky-300 text-sm py-4">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Semua siswa di kelas ini sudah terdaftar PKL
                                </div>
                            </template>
                            <template x-if="!isLoading && !selectedKelas">
                                <div class="text-center text-slate-400 text-sm py-4">
                                    Pilih kelas untuk menampilkan siswa
                                </div>
                            </template>
                            <div x-show="!isLoading && students.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <template x-for="student in students" :key="student.id">
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-lg bg-sky-800/20 hover:bg-sky-800/40 cursor-pointer transition-colors border border-sky-500/20">
                                        <input type="checkbox" name="student_ids[]" :value="student.id"
                                            x-model="selectedStudents"
                                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500">
                                        <div>
                                            <p class="text-sm text-white font-medium" x-text="student.name"></p>
                                            <p class="text-xs text-slate-400" x-text="student.nis"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-5 border-t border-sky-500/30">
                        <span class="text-sm text-sky-300" x-text="selectedStudents.length + ' siswa dipilih'"></span>
                        <div class="flex gap-3">
                            <button type="button" @click="showAddModal = false"
                                class="px-5 py-2.5 bg-sky-800/50 border border-sky-500/30 text-white font-medium rounded-xl hover:bg-sky-800 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="selectedStudents.length === 0"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-sky-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showEditModal = false" style="display: none;">

            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg bg-slate-900 border border-slate-700/50 rounded-2xl shadow-2xl">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Edit Data PKL</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="`/admin/pkl/${editPkl.id}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="student_id" :value="editPkl.student_id">
                    <input type="hidden" name="tp_id" :value="editPkl.tp_id">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Siswa</label>
                        <input type="text" :value="editPkl.student ? editPkl.student.name : '-'" disabled
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-700/50 rounded-xl text-slate-400 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tempat PKL (DUDI) <span
                                class="text-red-400">*</span></label>
                        <select name="dudi_id" x-model="editPkl.dudi_id" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="">Pilih DUDI</option>
                            @foreach($dudiList as $dudi)
                                <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pembimbing Sekolah</label>
                        <select name="pembimbing_sekolah_id" x-model="editPkl.pembimbing_sekolah_id"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="">Pilih Guru</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>     function pklPage() {         return {             showAddModal: false,             showEditModal: false,             selectedKelas: '',             selectedTp: '{{ $tpId ?? '' }}',             students: [],             selectedStudents: [],             isLoading: false,             editPkl: {},
                 init() {                 this.$watch('showAddModal', value => {                     document.body.classList.toggle('overflow-hidden', value);                     if (!value) {                         this.selectedKelas = '';                         this.students = [];                         this.selectedStudents = [];                     }                 });                 this.$watch('showEditModal', value => {                     document.body.classList.toggle('overflow-hidden', value);                 });             },
                 loadStudents() {                 if (!this.selectedKelas) {                     this.students = [];                     return;                 }                 this.isLoading = true;                 this.students = [];                 this.selectedStudents = [];
                     fetch(`/admin/pkl/students/${this.selectedKelas}?tp_id=${this.selectedTp}`)                     .then(res => res.json())                     .then(data => {                         this.students = data;                         this.isLoading = false;                     })                     .catch(() => {                         this.isLoading = false;                     });             },
                 selectAll() {                 this.selectedStudents = this.students.map(s => s.id.toString());             },
                 deselectAll() {                 this.selectedStudents = [];             },
                 openEditModal(pkl) {                 this.editPkl = { ...pkl };                 this.showEditModal = true;             }         }     }
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

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection