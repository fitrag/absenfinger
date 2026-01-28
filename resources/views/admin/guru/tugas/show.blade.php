@extends('layouts.admin')

@section('title', 'Detail Tugas')

@section('content')
            <div class="space-y-6" x-data="{ 
                selectedKelasId: '',
                students: {{ Js::from($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'nisn' => $s->nisn,
        'kelas_id' => $s->kelas_id,
        'kelas_nm' => $s->kelas->nm_kls ?? '-'
    ])) }},
                submissions: {{ Js::from($submissionsMap->mapWithKeys(fn($sub, $key) => [
        $key => [
            'id' => $sub->id,
            'submitted_at' => $sub->submitted_at->format('d M Y, H:i'),
            'file_path' => Storage::url($sub->file_path),
            'is_late' => $sub->submitted_at->gt($tugas->tanggal_deadline->format('Y-m-d') . ' ' . $tugas->jam_deadline),
            'nilai' => $sub->nilai
        ]
    ])) }},
                get filteredStudents() {
                    if (!this.selectedKelasId) {
                        return this.students;
                    }
                    return this.students.filter(s => s.kelas_id == this.selectedKelasId);
                },
                get submittedCount() {
                    return this.filteredStudents.filter(s => this.submissions[s.id]).length;
                },
                get notSubmittedCount() {
                    return this.filteredStudents.filter(s => !this.submissions[s.id]).length;
                }
            }">
                <!-- Header / Back -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.guru.tugas.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Detail Tugas</h1>
                        <p class="text-slate-400 text-sm mt-0.5">Submission siswa untuk tugas ini</p>
                    </div>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Main Info Card -->
                    <div class="lg:col-span-2 bg-slate-800/50 border border-slate-700/50 rounded-xl p-5">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    {{ $tugas->mapel->nm_mapel }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-700/50 text-slate-300">
                                    {{ $tugas->tahunPelajaran->nm_tp }} - {{ $tugas->semester }}
                                </span>
                            </div>
                            @if($tugas->isDeadlinePassed)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                    Deadline Lewat
                                </span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <h2 class="text-lg font-bold text-white mb-2">{{ $tugas->judul }}</h2>

                        @if($tugas->keterangan)
                            <p class="text-slate-400 text-sm mb-4 line-clamp-2">{{ $tugas->keterangan }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                            <div class="flex items-center gap-2 text-slate-400">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="{{ $tugas->isDeadlinePassed ? 'text-red-400' : '' }}">{{ $tugas->deadlineFormatted }}</span>
                            </div>

                            {{-- File Soal Section --}}
                            @if(!empty($tugas->file_path))
                                <a href="{{ asset('storage/' . $tugas->file_path) }}" target="_blank" 
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors border border-blue-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Download Soal</span>
                                </a>
                            @else
                                <div class="flex items-center gap-2 text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="italic text-xs">Tidak ada file soal</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-700/50">
                            <div class="flex items-center gap-2 text-sm text-slate-400">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Kelas: </span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($tugas->kelas as $kls)
                                        <span class="px-2 py-0.5 rounded bg-slate-700/50 text-slate-300 text-xs">{{ $kls->nm_kls }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="space-y-4">
                        <!-- Total Siswa -->
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Siswa</p>
                                    <p class="text-2xl font-bold text-white mt-1">{{ $students->count() }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Sudah Mengumpulkan -->
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Sudah Kumpul</p>
                                    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $tugas->submissions->count() }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="w-full bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $students->count() > 0 ? ($tugas->submissions->count() / $students->count()) * 100 : 0 }}%"></div>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ $students->count() > 0 ? round(($tugas->submissions->count() / $students->count()) * 100) : 0 }}% dari total siswa</p>
                            </div>
                        </div>

                        <!-- Belum Mengumpulkan -->
                        <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Belum Kumpul</p>
                                    <p class="text-2xl font-bold text-red-400 mt-1">{{ $students->count() - $tugas->submissions->count() }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Submission List -->
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-700/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-semibold text-white">Daftar Pengumpulan</h3>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-300" x-text="filteredStudents.length + ' siswa'"></span>
                        </div>

                        <!-- Filter Kelas -->
                        <div class="flex items-center gap-3">
                            <select x-model="selectedKelasId" 
                                class="px-3 py-1.5 bg-slate-700/50 border border-slate-600/50 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none min-w-[140px]">
                                <option value="">Semua Kelas</option>
                                @foreach($tugas->kelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="px-5 py-3 bg-slate-900/30 border-b border-slate-700/50 flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="text-slate-400">Sudah kumpul: </span>
                            <span class="text-emerald-400 font-medium" x-text="submittedCount"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            <span class="text-slate-400">Belum kumpul: </span>
                            <span class="text-red-400 font-medium" x-text="notSubmittedCount"></span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-5 py-3 font-medium">No</th>
                                    <th class="px-5 py-3 font-medium">Nama Siswa</th>
                                    <th class="px-5 py-3 font-medium">Kelas</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium">Waktu Submit</th>
                                    <th class="px-5 py-3 font-medium">Nilai</th>
                                    <th class="px-5 py-3 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/30 text-sm">
                                <template x-for="(student, index) in filteredStudents" :key="student.id">
                                    <tr class="hover:bg-slate-700/20 transition-colors">
                                        <td class="px-5 py-3 text-slate-500 w-12" x-text="index + 1"></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-blue-400" x-text="student.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div>
                                                    <p class="text-white font-medium" x-text="student.name"></p>
                                                    <p class="text-slate-500 text-xs" x-text="student.nisn"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded bg-slate-700/50 text-slate-300 text-xs" x-text="student.kelas_nm"></span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <template x-if="submissions[student.id]">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                                    <span class="text-emerald-400 text-xs font-medium">Sudah</span>
                                                    <template x-if="submissions[student.id].is_late">
                                                        <span class="text-amber-400 text-xs">(Terlambat)</span>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!submissions[student.id]">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                                    <span class="text-red-400 text-xs font-medium">Belum</span>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-5 py-3 text-slate-400 text-xs">
                                            <template x-if="submissions[student.id]">
                                                <span x-text="submissions[student.id].submitted_at"></span>
                                            </template>
                                            <template x-if="!submissions[student.id]">
                                                <span class="text-slate-600">-</span>
                                            </template>
                                        </td>
                                        <!-- Nilai Column -->
                                        <td class="px-5 py-3">
                                            <template x-if="submissions[student.id]">
                                                <div x-data="{ showNilaiForm: false, nilaiInput: submissions[student.id].nilai || '' }">
                                                    <!-- Display nilai if exists -->
                                                    <template x-if="submissions[student.id].nilai !== null && !showNilaiForm">
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-semibold text-sm" x-text="submissions[student.id].nilai"></span>
                                                            <button @click="showNilaiForm = true" type="button" class="text-slate-400 hover:text-blue-400 transition-colors" title="Edit Nilai">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <!-- Show form if no nilai or editing -->
                                                    <template x-if="submissions[student.id].nilai === null || showNilaiForm">
                                                        <form :action="'{{ url('admin/guru/tugas/' . $tugas->id . '/submission') }}/' + submissions[student.id].id + '/nilai'" method="POST" class="flex items-center gap-1">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="nilai" x-model="nilaiInput" min="0" max="100" step="0.01" placeholder="0-100" 
                                                                class="w-16 px-2 py-1 text-xs bg-slate-700/50 border border-slate-600/50 rounded-lg text-white focus:border-blue-500 focus:outline-none" />
                                                            <button type="submit" class="p-1 rounded bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 transition-colors" title="Simpan Nilai">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                            <template x-if="showNilaiForm">
                                                                <button @click="showNilaiForm = false" type="button" class="p-1 rounded bg-red-500/20 text-red-400 hover:bg-red-500/30 transition-colors" title="Batal">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </template>
                                                        </form>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!submissions[student.id]">
                                                <span class="text-slate-600 text-xs">-</span>
                                            </template>
                                        </td>
                                        <!-- Aksi Column -->
                                        <td class="px-5 py-3">
                                            <template x-if="submissions[student.id]">
                                                <a :href="submissions[student.id].file_path" target="_blank" 
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors text-xs font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </template>
                                            <template x-if="!submissions[student.id]">
                                                <span class="text-slate-600 text-xs">-</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredStudents.length === 0">
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-slate-500">Tidak ada siswa di kelas yang dipilih</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection