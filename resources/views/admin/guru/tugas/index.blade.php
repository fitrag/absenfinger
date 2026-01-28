@extends('layouts.admin')

@section('title', 'Tugas Siswa')

@section('content')
    <div x-data="tugasHandler()" x-init="init()" class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Tugas Siswa</h1>
                <p class="text-slate-400 mt-1">Kelola tugas dan submission siswa</p>
            </div>
            <button @click="openAddModal()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tugas
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-6 backdrop-blur-xl">
            <form action="{{ route('admin.guru.tugas.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tahun Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Tahun Pelajaran</label>
                    <div class="relative">
                        <select name="tp_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none">
                            <option value="">Semua TP</option>
                            @foreach ($tpList as $tp)
                                <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}>
                                    {{ $tp->nm_tp }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Semester -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Semester</label>
                    <div class="relative">
                        <select name="semester" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none">
                            <option value="">Semua Semester</option>
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Mapel -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Mata Pelajaran</label>
                    <div class="relative">
                        <select name="mapel_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none">
                            <option value="">Semua Mapel</option>
                            @foreach ($mapelList as $mapel)
                                <option value="{{ $mapel->id }}" {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nm_mapel }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Kelas</label>
                    <div class="relative">
                        <select name="kelas_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nm_kls }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tugas List -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($tugasList as $tugas)
                <div
                    class="group relative bg-slate-800 border border-slate-700 rounded-2xl p-6 hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex-1 space-y-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            {{ $tugas->mapel->nm_mapel }}
                                        </span>
                                        <span class="text-sm text-slate-400">{{ $tugas->tahunPelajaran->nm_tp }} -
                                            {{ $tugas->semester }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white group-hover:text-blue-400 transition-colors">
                                        <a href="{{ route('admin.guru.tugas.show', $tugas->id) }}" class="hover:underline">
                                            {{ $tugas->judul }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="p-2 text-slate-400 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                            class="absolute right-0 mt-2 w-48 bg-slate-800 border border-slate-700 rounded-xl shadow-xl z-10 py-1"
                                            style="display: none;">
                                            <button @click="openEditModal({{ $tugas }}); open = false"
                                                class="w-full px-4 py-2 text-left text-sm text-slate-300 hover:bg-slate-700 hover:text-white flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                            <button
                                                @click="confirmDelete('{{ $tugas->id }}', '{{ $tugas->judul }}'); open = false"
                                                class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-red-500/10 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-slate-400 text-sm line-clamp-2">{{ Str::limit($tugas->keterangan, 150) }}</p>

                            <div class="flex items-center gap-4 text-sm text-slate-400">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="{{ $tugas->isDeadlinePassed ? 'text-red-400' : '' }}">
                                        Deadline: {{ $tugas->deadlineFormatted }}
                                    </span>
                                </div>
                                @if($tugas->file_path)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a href="{{ Storage::url($tugas->file_path) }}" target="_blank"
                                            class="text-blue-400 hover:text-blue-300 hover:underline">
                                            Lihat Soal
                                        </a>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>{{ $tugas->kelas->count() }} Kelas</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Box -->
                        <div class="flex flex-row md:flex-col gap-2 min-w-[200px]">
                            <div class="flex-1 bg-slate-900/50 rounded-xl p-4 border border-slate-700/50">
                                <div class="text-sm text-slate-400 mb-1">Total Submissions</div>
                                <div class="flex items-end gap-2">
                                    <span class="text-2xl font-bold text-white">{{ $tugas->submissions->count() }}</span>
                                    <span class="text-sm text-slate-500 mb-1">siswa</span>
                                </div>
                            </div>
                            <div class="flex-1 bg-slate-900/50 rounded-xl p-4 border border-slate-700/50">
                                <a href="{{ route('admin.guru.tugas.show', $tugas->id) }}"
                                    class="w-full h-full flex items-center justify-center text-blue-400 hover:text-white font-medium transition-colors">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Classes Badges -->
                    <div class="mt-4 pt-4 border-t border-slate-700/50 flex flex-wrap gap-2">
                        @foreach($tugas->kelas as $kelas)
                            <span class="px-2 py-1 rounded-md text-xs font-medium bg-slate-700 text-slate-300">
                                {{ $kelas->nm_kls }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-slate-800 rounded-2xl p-12 text-center border border-slate-700">
                    <div class="w-16 h-16 bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Belum ada tugas</h3>
                    <p class="text-slate-400">Buat tugas baru untuk siswa Anda.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6">
                {{ $tugasList->links() }}
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="closeModal()">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="closeModal()">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white" x-text="editMode ? 'Edit Tugas' : 'Tambah Tugas'"></h3>
                        <button @click="closeModal()" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="formUrl" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                        <div class="space-y-4">
                            <!-- Alert Error -->
                            @if ($errors->any())
                                <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                                    <ul class="list-disc list-inside text-sm text-red-500">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Tahun Pelajaran</label>
                                    <select name="tp_id" x-model="formData.tp_id" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        @foreach ($tpList as $tp)
                                            <option value="{{ $tp->id }}">{{ $tp->nm_tp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Semester</label>
                                    <select name="semester" x-model="formData.semester" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Mata Pelajaran</label>
                                <select name="mapel_id" x-model="formData.mapel_id" @change="loadKelasByMapel()" required
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach ($mapelList as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nm_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Kelas</label>
                                
                                <!-- Loading state -->
                                <div x-show="loadingKelas" class="text-center py-3 text-slate-400 bg-slate-800 border border-slate-700 rounded-xl">
                                    <svg class="animate-spin h-5 w-5 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memuat kelas...
                                </div>

                                <!-- No mapel selected -->
                                <div x-show="!formData.mapel_id && !loadingKelas" 
                                    class="text-center py-3 text-slate-500 bg-slate-800 border border-slate-700 rounded-xl">
                                    Pilih mapel terlebih dahulu
                                </div>

                                <!-- Kelas selection with chips -->
                                <div x-show="formData.mapel_id && !loadingKelas && kelasList.length > 0" class="relative">
                                    <!-- Selected Chips -->
                                    <div class="flex flex-wrap gap-2 mb-2" x-show="selectedKelasList.length > 0">
                                        <template x-for="(kelas, index) in selectedKelasList" :key="kelas.id">
                                            <div class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-sm border border-blue-500/30">
                                                <span x-text="kelas.nm_kls"></span>
                                                <button type="button" @click="removeSelectedKelas(index)" class="hover:text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <!-- Hidden input array -->
                                                <input type="hidden" name="kelas_ids[]" :value="kelas.id">
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Search Input -->
                                    <input type="text" x-model="kelasSearch" @focus="showKelasDropdown = true"
                                        @click.outside="showKelasDropdown = false"
                                        placeholder="Ketik nama kelas untuk menambahkan..."
                                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 placeholder-slate-500"
                                        @keydown.enter.prevent="if(filteredKelasList.length > 0) addSelectedKelas(filteredKelasList[0])">

                                    <!-- Dropdown -->
                                    <div x-show="showKelasDropdown && filteredKelasList.length > 0"
                                        class="absolute z-10 w-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                        <template x-for="kelas in filteredKelasList" :key="kelas.id">
                                            <button type="button" @click="addSelectedKelas(kelas)"
                                                class="w-full px-4 py-2.5 text-left text-sm text-white hover:bg-slate-700/50 flex justify-between items-center group">
                                                <span x-text="kelas.nm_kls"></span>
                                                <span x-show="isKelasSelected(kelas.id)" class="text-blue-400 text-xs">Terpilih</span>
                                            </button>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2" x-show="selectedKelasList.length === 0">Silakan pilih satu atau lebih kelas</p>
                                </div>

                                <!-- No class available -->
                                <div x-show="formData.mapel_id && !loadingKelas && kelasList.length === 0"
                                    class="text-center py-3 text-amber-400 bg-slate-800 border border-amber-500/30 rounded-xl">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Tidak ada kelas untuk mapel ini. Silakan tambah di menu Kelas Ajar.
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Judul Tugas</label>
                                <input type="text" name="judul" x-model="formData.judul" required
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 placeholder-slate-600"
                                    placeholder="Contoh: Tugas Bab 1 - Aljabar">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Keterangan / Instruksi</label>
                                <textarea name="keterangan" x-model="formData.keterangan" rows="3"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 placeholder-slate-600"
                                    placeholder="Jelaskan detail tugas..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal Deadline</label>
                                    <input type="date" name="tanggal_deadline" x-model="formData.tanggal_deadline" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Jam Deadline</label>
                                    <input type="time" name="jam_deadline" x-model="formData.jam_deadline" required
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">File Soal/Materi (PDF, Max
                                    10MB)</label>
                                <input type="file" name="file" accept=".pdf"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                                <p class="text-xs text-slate-500 mt-1" x-show="editMode">Biarkan kosong jika tidak ingin
                                    mengubah file.</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800 mt-4">
                            <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-slate-400 hover:text-white cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl cursor-pointer">
                                <span x-text="editMode ? 'Simpan Perubahan' : 'Buat Tugas'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <form id="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        function tugasHandler() {
            return {
                showModal: false,
                editMode: false,
                formUrl: '',
                kelasList: [],
                selectedKelasList: [],
                kelasSearch: '',
                showKelasDropdown: false,
                loadingKelas: false,
                formData: {
                    tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                    semester: '{{ $activeSemester ?? "Ganjil" }}',
                    mapel_id: '',
                    kelas_ids: [],
                    judul: '',
                    keterangan: '',
                    tanggal_deadline: '',
                    jam_deadline: '23:59'
                },

                init() {
                    @if ($errors->any())
                        this.showModal = true;
                    @endif
                },

                // Computed property for filtered kelas list
                get filteredKelasList() {
                    if (this.kelasSearch === '') {
                        return this.kelasList;
                    }
                    return this.kelasList.filter(k => 
                        k.nm_kls.toLowerCase().includes(this.kelasSearch.toLowerCase())
                    );
                },

                addSelectedKelas(kelas) {
                    if (!this.selectedKelasList.some(k => k.id === kelas.id)) {
                        this.selectedKelasList.push(kelas);
                    }
                    this.kelasSearch = '';
                    this.showKelasDropdown = false;
                },

                removeSelectedKelas(index) {
                    this.selectedKelasList.splice(index, 1);
                },

                isKelasSelected(id) {
                    return this.selectedKelasList.some(k => k.id === id);
                },

                async loadKelasByMapel() {
                    if (!this.formData.mapel_id) {
                        this.kelasList = [];
                        this.selectedKelasList = [];
                        return;
                    }

                    this.loadingKelas = true;
                    this.kelasList = [];
                    this.selectedKelasList = [];

                    try {
                        const url = `{{ route('admin.guru.tugas.kelasByMapel') }}?mapel_id=${this.formData.mapel_id}&tp_id=${this.formData.tp_id}`;
                        const response = await fetch(url);
                        
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        this.kelasList = data;
                    } catch (error) {
                        console.error('Error loading kelas:', error);
                        alert('Gagal memuat data kelas. Silakan coba lagi.');
                    } finally {
                        this.loadingKelas = false;
                    }
                },

                openAddModal() {
                    this.editMode = false;
                    this.formUrl = "{{ route('admin.guru.tugas.store') }}";
                    this.kelasList = [];
                    this.selectedKelasList = [];
                    this.kelasSearch = '';
                    this.formData = {
                        tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                        semester: '{{ $activeSemester ?? "Ganjil" }}',
                        mapel_id: '',
                        kelas_ids: [],
                        judul: '',
                        keterangan: '',
                        tanggal_deadline: '',
                        jam_deadline: '23:59'
                    };
                    this.showModal = true;
                },

                async openEditModal(tugas) {
                    this.editMode = true;
                    this.formUrl = "{{ route('admin.guru.tugas.update', ':id') }}".replace(':id', tugas.id);
                    this.kelasSearch = '';

                    this.formData = {
                        tp_id: tugas.tp_id,
                        semester: tugas.semester,
                        mapel_id: tugas.mapel_id,
                        kelas_ids: [],
                        judul: tugas.judul,
                        keterangan: tugas.keterangan || '',
                        // Format date for input type=date (YYYY-MM-DD)
                        tanggal_deadline: new Date(tugas.tanggal_deadline).toISOString().split('T')[0],
                        // Format time for input type=time (HH:MM)
                        jam_deadline: tugas.jam_deadline.substring(0, 5)
                    };
                    
                    this.showModal = true;

                    // Load kelas first, then set selected kelas
                    await this.loadKelasByMapel();
                    
                    // Set selected kelas from tugas data
                    this.selectedKelasList = tugas.kelas.map(k => ({
                        id: k.id,
                        nm_kls: k.nm_kls
                    }));
                },

                closeModal() {
                    this.showModal = false;
                    this.kelasList = [];
                    this.selectedKelasList = [];
                    this.kelasSearch = '';
                },

                confirmDelete(id, title) {
                    if (confirm('Apakah Anda yakin ingin menghapus tugas "' + title + '"? Data submission siswa juga akan terhapus.')) {
                        let form = document.getElementById('deleteForm');
                        form.action = "{{ route('admin.guru.tugas.destroy', ':id') }}".replace(':id', id);
                        form.submit();
                    }
                }
            }
        }
    </script>
@endsection