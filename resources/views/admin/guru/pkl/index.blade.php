@extends('layouts.admin')

@section('title', 'Data PKL (Pembimbing)')

@section('page-title', 'Data PKL Bimbingan')

@section('content')
    <div class="space-y-6" x-data="gradePage()">
        <!-- Header & Filter -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">Daftar Siswa Bimbingan PKL</h2>
                        <p class="text-slate-400 text-sm mt-1">
                            Tahun Pelajaran: <span
                                class="text-blue-400 font-medium">{{ $selectedTp->nm_tp ?? 'Semua' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Filters -->
                <form action="{{ route('admin.guru.pkl.index') }}" method="GET"
                    class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-800/50">

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
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama siswa, NIS, atau DUDI..."
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

        <!-- Grade Input Modal -->
        <div x-show="showGradeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
            @keydown.escape.window="showGradeModal = false">

            <!-- Backdrop -->
            <div x-show="showGradeModal" x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                @click="showGradeModal = false">
            </div>

            <!-- Modal Panel wrapper -->
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div x-show="showGradeModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="w-full max-w-2xl bg-slate-900 rounded-2xl border border-slate-800 shadow-2xl relative flex flex-col pointer-events-auto"
                    @click.stop>

                    <!-- Loading State -->
                    <div x-show="loadingGrade"
                        class="absolute inset-0 bg-slate-900 z-50 flex items-center justify-center rounded-2xl">
                        <div class="text-center">
                            <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-3"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-slate-400">Memuat data nilai...</p>
                        </div>
                    </div>

                    <!-- Modal Content Container -->
                    <div id="gradeModalContent" class="min-h-[200px]">
                        <!-- Content injected via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Student Modal -->
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

                    <div class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                        <div>
                            <h3 class="text-lg font-bold text-white">Tambah Siswa PKL</h3>
                            <p class="text-sm text-sky-300/70 mt-0.5">TP: {{ $selectedTp?->nm_tp ?? '-' }}</p>
                        </div>
                        <button @click="showAddModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.guru.pkl.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        <input type="hidden" name="tp_id" value="{{ $tpId }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-sky-200 mb-2">Tempat PKL (DUDI) <span class="text-red-400">*</span></label>
                                <select name="dudi_id" x-model="addForm.dudi_id" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih DUDI</option>
                                    @foreach($allDudiList as $dudi)
                                        <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-sky-200 mb-2">Pilih Kelas</label>
                                <select x-model="selectedKelas" @change="loadStudents()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Student Selection -->
                        <div class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                            <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <span class="text-sm font-medium text-sky-200">Pilih Siswa <span class="text-red-400">*</span></span>
                                    <div class="flex items-center gap-2">
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
                                        <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-sky-400" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memuat data...
                                    </div>
                                </template>
                                <template x-if="!isLoading && students.length === 0 && selectedKelas">
                                    <div class="text-center text-sky-300 text-sm py-4">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                        <label class="flex items-center gap-3 p-3 rounded-lg bg-sky-800/20 hover:bg-sky-800/40 cursor-pointer transition-colors border border-sky-500/20">
                                            <input type="checkbox" name="student_ids[]" :value="student.id" x-model="selectedStudents"
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
                                    class="px-5 py-2.5 bg-sky-800/50 border border-sky-500/30 text-white font-medium rounded-xl hover:bg-sky-800 transition-colors cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" :disabled="selectedStudents.length === 0 || !addForm.dudi_id"
                                    class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-sky-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none cursor-pointer">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Content grouped by DUDI -->
            <div class="space-y-6">
                @forelse($groupedPkls as $dudiName => $pkls)
                    <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl overflow-hidden">
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
                                        {{ $pkls->first()->dudi->alamat ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full bg-slate-700/50 text-xs font-medium text-slate-300">
                                    {{ count($pkls) }} Siswa
                                </span>
                                <button type="button" @click="openAddModal({{ $pkls->first()->dudi_id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 text-xs font-medium rounded-lg border border-blue-500/30 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Siswa</span>
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-800/30 border-b border-slate-700/50 text-left">
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No
                                        </th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Siswa
                                        </th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas
                                        </th>
                                        <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Waktu
                                            PKL</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">
                                            Status Nilai</th>
                                        <th
                                            class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700/50">
                                    @foreach($pkls as $index => $pkl)
                                                                            <tr class="hover:bg-slate-800/30 transition-colors">
                                                                                <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                                                                                <td class="px-6 py-4">
                                                                                    <div class="flex items-center gap-3">
                                                                                        <div
                                                                                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                                                                            {{ substr($pkl->student->name, 0, 1) }}
                                                                                        </div>
                                                                                        <div>
                                                                                            <p class="text-sm font-medium text-white">{{ $pkl->student->name }}</p>
                                                                                            <p class="text-xs text-slate-400">{{ $pkl->student->nis }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="px-6 py-4">
                                                                                    <span
                                                                                        class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium text-slate-300">
                                                                                        {{ $pkl->student->kelas->nm_kls ?? '-' }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="px-6 py-4">
                                                                                    <div class="flex flex-col gap-1">
                                                                                        @php
                                                                                            $sertifikat = $sertifikatsByTp[$pkl->tp_id] ?? null;
                                                                                        @endphp
                                                                                        <div class="text-xs text-slate-300">
                                                                                            <span class="text-slate-500">Mulai:</span>
                                                                                            {{ $sertifikat && $sertifikat->tgl_mulai ? \Carbon\Carbon::parse($sertifikat->tgl_mulai)->format('d/m/Y') : '-' }}
                                                                                        </div>
                                                                                        <div class="text-xs text-slate-300">
                                                                                            <span class="text-slate-500">Selesai:</span>
                                                                                            {{ $sertifikat && $sertifikat->tgl_selesai ? \Carbon\Carbon::parse($sertifikat->tgl_selesai)->format('d/m/Y') : '-' }}
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="px-6 py-4 text-right">
                                                                                    @php
                                                                                        $hasSoft = $pkl->softNilai()->exists();
                                                                                        $hasHard = $pkl->hardNilai()->exists();
                                                                                        $complete = $hasSoft && $hasHard;
                                                                                    @endphp
                                                                                    @if($complete)
                                                                                        <span
                                                                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                                                            Dinilai
                                                                                        </span>
                                                                                    @else
                                                                                        <span
                                                                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                                                            Belum Lengkap
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-6 py-4 text-right">
                                                                                    <div class="flex items-center justify-end gap-2">
                                                                                        <button type="button"
                                                                                            @click="openGradeModal('{{ route('admin.guru.pkl.input_nilai', $pkl->id) }}')"
                                                                                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer inline-flex items-center gap-1
                                                                                                                                {{ $complete
                                        ? 'bg-blue-500/10 text-blue-400 border border-blue-500/30 hover:bg-blue-500/20'
                                        : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' }}">
                                                                                            {{ $complete ? 'Update Nilai' : 'Input Nilai' }}
                                                                                        </button>
                                                                                        <form action="{{ route('admin.guru.pkl.destroy', $pkl->id) }}" method="POST"
                                                                                            class="inline"
                                                                                            onsubmit="return confirm('Yakin ingin menghapus siswa ini dari PKL?')">
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
                            <h3 class="text-sm font-medium text-white mb-1">Belum ada data PKL</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                Belum ada siswa bimbingan PKL yang ditemukan untuk filter yang dipilih.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Script for Alpine Component -->
        <script>
            function gradePage() {
                return {
                    showGradeModal: false,
                    loadingGrade: false,
                    showAddModal: false,
                    addForm: {
                        dudi_id: '',
                    },
                    selectedKelas: '',
                    selectedTp: '{{ $tpId ?? '' }}',
                    students: [],
                    selectedStudents: [],
                    isLoading: false,

                    init() {
                        this.$watch('showAddModal', value => {
                            document.body.classList.toggle('overflow-hidden', value);
                            if (!value) {
                                this.selectedKelas = '';
                                this.students = [];
                                this.selectedStudents = [];
                                this.addForm.dudi_id = '';
                            }
                        });
                        this.$watch('showGradeModal', value => {
                            document.body.classList.toggle('overflow-hidden', value);
                        });
                    },

                    loadStudents() {
                        if (!this.selectedKelas) {
                            this.students = [];
                            return;
                        }
                        this.isLoading = true;
                        this.students = [];
                        this.selectedStudents = [];

                        fetch(`/admin/guru/pkl/students/${this.selectedKelas}?tp_id=${this.selectedTp}`)
                            .then(res => res.json())
                            .then(data => {
                                this.students = data;
                                this.isLoading = false;
                            })
                            .catch(() => {
                                this.isLoading = false;
                            });
                    },

                    selectAll() {
                        this.selectedStudents = this.students.map(s => s.id.toString());
                    },

                    deselectAll() {
                        this.selectedStudents = [];
                    },

                    openAddModal(dudiId = '') {
                        this.addForm.dudi_id = dudiId;
                        this.showAddModal = true;
                    },

                    async openGradeModal(url) {
                        this.showGradeModal = true;
                        this.loadingGrade = true;
                        // Reset content
                        const contentDiv = document.getElementById('gradeModalContent');
                        contentDiv.innerHTML = '';

                        try {
                            const response = await fetch(url);
                            if (!response.ok) throw new Error('Network response was not ok');

                            const html = await response.text();
                            contentDiv.innerHTML = html;
                        } catch (error) {
                            console.error('Error fetching grade form:', error);
                            // Show error state in modal
                            contentDiv.innerHTML = `
                                    <div class="h-full flex items-center justify-center flex-col gap-4 text-rose-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <p>Gagal memuat data. Silakan coba lagi.</p>
                                        <button type="button" @click="showGradeModal = false" class="px-4 py-2 bg-slate-800 rounded-lg text-white hover:bg-slate-700">Tutup</button>
                                    </div>
                                `;
                        } finally {
                            this.loadingGrade = false;
                        }
                    }
                };
            }
        </script>
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