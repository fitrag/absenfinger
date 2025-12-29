@extends('layouts.admin')

@section('title', 'Nilai Harian')
@section('page-title', 'Nilai Harian')

@section('content')
    <div class="space-y-6" x-data="nilaiPage()">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Nilai Harian</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola nilai harian siswa (UH, Tugas, dll)</p>
            </div>
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Nilai
            </button>
        </div>

        <!-- Filter & Search -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form action="{{ route('admin.guru.nilai.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Tahun Pelajaran Filter -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Tahun Pelajaran</label>
                        <select name="tp_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:border-blue-500">
                            @foreach($tpList as $tp)
                                <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}>{{ $tp->nm_tp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Filter -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Semester</label>
                        <select name="semester" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:border-blue-500">
                            <option value="">Semua Semester</option>
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    <!-- Mapel Filter -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Mata Pelajaran</label>
                        <select name="mapel_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:border-blue-500">
                            <option value="">Semua Mapel</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>{{ $m->nm_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>
            </form>
        </div>

        <!-- Grouped Nilai Display -->
        @if(empty($grouped))
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-slate-400">Belum ada data nilai.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($grouped as $group)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                        <!-- Group Header -->
                        <div class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 px-4 py-3 border-b border-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold">{{ $group['mapel']->nm_mapel ?? 'Mapel tidak diketahui' }}
                                    </h3>
                                    <p class="text-sm text-slate-400">
                                        Kelas: {{ $group['kelas']->nm_kls ?? 'Kelas tidak diketahui' }}
                                        <span class="mx-2 text-slate-600">|</span>
                                        Semester {{ count($group['items']) > 0 ? $group['items'][0]->semester : '-' }}
                                        <span class="mx-2 text-slate-600">|</span>
                                        TP: {{ $group['tp']->nm_tp ?? '-' }}
                                    </p>
                                </div>
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-medium">
                                        {{ count($group['items']) }} entri
                                    </span>
                                    <!-- PDF Download Button -->
                                    <a href="{{ route('admin.guru.nilai.pdf', ['mapel_id' => $group['mapel']->id, 'kelas_id' => $group['kelas']->id, 'tp_id' => $group['tp']->id, 'semester' => $group['semester']]) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500/20 text-rose-400 rounded-lg hover:bg-rose-500/30 text-xs font-medium cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        PDF
                                    </a>
                                    <button
                                        @click="openAddModalWithGroup({{ $group['mapel']->id ?? 'null' }}, {{ $group['kelas']->id ?? 'null' }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg hover:bg-emerald-500/30 text-xs font-medium cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Group Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-800/50 bg-slate-800/20">
                                    <tr class="border-b border-slate-800/50 bg-slate-800/20">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Harian Ke-
                                        </th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($group['items'] as $idx => $nilai)
                                        <tr class="hover:bg-slate-800/30 transition-colors">
                                            <td class="px-4 py-2 text-sm text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-300 font-medium">Harian Ke-{{ $nilai->harian_ke }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="flex items-center justify-end gap-2">

                                                    <!-- Detail Button -->
                                                    <button
                                                        @click="openDetailModal({{ $nilai->id }}, '{{ $group['mapel']->nm_mapel }}', '{{ $group['kelas']->nm_kls }}', '{{ $group['tp']->nm_tp }}', '{{ $nilai->semester }}', '{{ $nilai->harian_ke }}', {{ $nilai->kelas_id }}, '{{ number_format($nilai->details_avg_nilai, 2) }}')"
                                                        class="group inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-200 cursor-pointer"
                                                        title="Detail">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Edit Button -->
                                                    <button
                                                        @click="openEditModal({{ $nilai->id }}, {{ json_encode($nilai->tp_id ?? '') }}, {{ json_encode($nilai->semester ?? '') }}, {{ json_encode($nilai->kelas_id ?? '') }}, {{ json_encode($nilai->mapel_id ?? '') }}, {{ json_encode($nilai->harian_ke ?? '') }})"
                                                        class="group inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:scale-105 transition-all duration-200 cursor-pointer"
                                                        title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('admin.guru.nilai.destroy', $nilai->id) }}" method="POST"
                                                        class="inline" onsubmit="return confirm('Hapus nilai ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="group inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-lg shadow-rose-500/30 hover:shadow-rose-500/50 hover:scale-105 transition-all duration-200 cursor-pointer"
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Detail Modal -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="showDetailModal = false">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="showDetailModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Detail Nilai Siswa</h3>
                        <button @click="showDetailModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Detail Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 bg-slate-800/50 p-4 rounded-xl border border-slate-800">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Mata Pelajaran</p>
                            <p class="text-white font-medium" x-text="detailData.mapel"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Kelas</p>
                            <p class="text-white font-medium" x-text="detailData.kelas"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Periode</p>
                            <p class="text-white font-medium"><span x-text="detailData.semester"></span> | TP <span
                                    x-text="detailData.tp"></span></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider">Penilaian</p>
                            <p class="text-white font-medium">Harian Ke-<span x-text="detailData.harian_ke"></span></p>
                        </div>
                        <div class="col-span-2 md:col-span-1 bg-slate-700/30 p-2 rounded-lg border border-slate-700/50">
                            <p class="text-xs text-emerald-400 uppercase tracking-wider font-bold">Rata-Rata Kelas</p>
                            <p class="text-2xl text-white font-bold" x-text="detailData.rata_rata || '0.00'"></p>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="loadingStudents" class="text-center py-8 text-slate-400">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-3 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memuat data nilai...
                    </div>

                    <!-- Students Table -->
                    <div x-show="!loadingStudents"
                        class="bg-slate-800/50 rounded-xl overflow-hidden border border-slate-700/50">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-700/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase w-10">
                                            No</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">NIS
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Nama
                                            Siswa</th>
                                        <th
                                            class="px-4 py-2 text-center text-xs font-semibold text-slate-400 uppercase w-20">
                                            Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700/50">
                                    <template x-for="(student, index) in students" :key="student.id">
                                        <tr class="hover:bg-slate-700/30 transition-colors">
                                            <td class="px-4 py-2 text-sm text-slate-400" x-text="index + 1"></td>
                                            <td class="px-4 py-2 text-sm text-slate-400 font-mono"
                                                x-text="student.nis || '-'"></td>
                                            <td class="px-4 py-2 text-sm text-white font-medium" x-text="student.name"></td>
                                            <td class="px-4 py-2 text-sm text-center">
                                                <span
                                                    :class="{'text-emerald-400 font-bold': student.nilai >= 75, 'text-rose-400 font-bold': student.nilai < 75 && student.nilai !== '', 'text-slate-500': student.nilai === ''}"
                                                    x-text="student.nilai !== '' ? student.nilai : '-'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="students.length === 0">
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                            Tidak ada data siswa
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="button" @click="showDetailModal = false"
                            class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl transition-all font-medium cursor-pointer border border-slate-700">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="showAddModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Tambah Nilai</h3>
                        <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.guru.nilai.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tahun Pelajaran -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Tahun Pelajaran</label>
                                    <select name="tp_id" x-model="formData.tp_id"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih TP --</option>
                                        @foreach($tpList as $tp)
                                            <option value="{{ $tp->id }}" {{ ($activeTp && $activeTp->id == $tp->id) ? 'selected' : '' }}>{{ $tp->nm_tp }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Semester -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Semester</label>
                                    <select name="semester" x-model="formData.semester"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Kelas -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Kelas</label>
                                    <select name="kelas_id" x-model="formData.kelas_id" @change="loadStudents($el.value)"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}">{{ $k->nm_kls }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Mapel -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Mapel</label>
                                    <select name="mapel_id" x-model="formData.mapel_id"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach($mapelList as $m)
                                            <option value="{{ $m->id }}">{{ $m->nm_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Harian Ke -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Harian Ke-</label>
                                <select name="harian_ke" x-model="formData.harian_ke"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Pilih Harian Ke --</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Input Nilai Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Input Nilai Siswa</label>

                                <!-- Loading state -->
                                <div x-show="loadingStudents" class="text-center py-4 text-slate-400">
                                    <svg class="animate-spin h-5 w-5 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memuat data siswa...
                                </div>

                                <!-- No kelas selected -->
                                <div x-show="!formData.kelas_id && !loadingStudents"
                                    class="text-center py-4 text-slate-500 bg-slate-800/50 rounded-xl">
                                    Pilih kelas terlebih dahulu
                                </div>

                                <!-- Students list -->
                                <div x-show="students.length > 0 && !loadingStudents"
                                    class="bg-slate-800/50 rounded-xl overflow-hidden border border-slate-700/50">
                                    <div
                                        class="px-4 py-2 bg-slate-700/50 border-b border-slate-700/50 flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Daftar Siswa</span>
                                        <span class="text-slate-400">Total: <span class="text-white"
                                                x-text="students.length"></span></span>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto p-2 space-y-1">
                                        <template x-for="(student, index) in students" :key="student.id">
                                            <div
                                                class="flex items-center justify-between px-3 py-2 bg-slate-800 rounded-lg hover:bg-slate-700/50 border border-slate-700/30">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-slate-500 text-xs w-6" x-text="index + 1"></span>
                                                    <div>
                                                        <div class="text-white text-sm font-medium" x-text="student.name">
                                                        </div>
                                                        <div class="text-slate-500 text-xs" x-text="student.nis || '-'">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-24">
                                                    <input type="number" :name="`students[${student.id}]`"
                                                        x-model="student.nilai" placeholder="0-100" min="0" max="100"
                                                        class="w-full px-3 py-1 bg-slate-900 border border-slate-700 rounded-lg text-white text-sm text-center focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-600">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Empty state -->
                                <div x-show="formData.kelas_id && students.length === 0 && !loadingStudents"
                                    class="text-center py-4 text-slate-500 bg-slate-800/50 rounded-xl">
                                    Tidak ada siswa di kelas ini
                                </div>
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
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="showEditModal = false">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="showEditModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Edit Nilai</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form :action="`{{ url('admin/guru/nilai') }}/${editId}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tahun Pelajaran -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Tahun Pelajaran</label>
                                    <select name="tp_id" x-model="formData.tp_id"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih TP --</option>
                                        @foreach($tpList as $tp)
                                            <option value="{{ $tp->id }}">{{ $tp->nm_tp }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Semester -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Semester</label>
                                    <select name="semester" x-model="formData.semester"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Kelas -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Kelas</label>
                                    <select name="kelas_id" x-model="formData.kelas_id" @change="loadStudents($el.value)"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}">{{ $k->nm_kls }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Mapel -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Mapel</label>
                                    <select name="mapel_id" x-model="formData.mapel_id"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach($mapelList as $m)
                                            <option value="{{ $m->id }}">{{ $m->nm_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Harian Ke -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Harian Ke-</label>
                                <select name="harian_ke" x-model="formData.harian_ke"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                    <option value="">-- Pilih Harian Ke --</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Input Nilai Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Input Nilai Siswa</label>

                                <div x-show="loadingStudents" class="text-center py-4 text-slate-400">
                                    <svg class="animate-spin h-5 w-5 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memuat data siswa...
                                </div>

                                <div x-show="!formData.kelas_id && !loadingStudents"
                                    class="text-center py-4 text-slate-500 bg-slate-800/50 rounded-xl">
                                    Pilih kelas terlebih dahulu
                                </div>

                                <div x-show="students.length > 0 && !loadingStudents"
                                    class="bg-slate-800/50 rounded-xl overflow-hidden border border-slate-700/50">
                                    <div
                                        class="px-4 py-2 bg-slate-700/50 border-b border-slate-700/50 flex justify-between items-center text-xs">
                                        <span class="text-slate-300 font-semibold">Daftar Siswa</span>
                                        <span class="text-slate-400">Total: <span class="text-white"
                                                x-text="students.length"></span></span>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto p-2 space-y-1">
                                        <template x-for="(student, index) in students" :key="student.id">
                                            <div
                                                class="flex items-center justify-between px-3 py-2 bg-slate-800 rounded-lg hover:bg-slate-700/50 border border-slate-700/30">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-slate-500 text-xs w-6" x-text="index + 1"></span>
                                                    <div>
                                                        <div class="text-white text-sm font-medium" x-text="student.name">
                                                        </div>
                                                        <div class="text-slate-500 text-xs" x-text="student.nis || '-'">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-24">
                                                    <input type="number" :name="`students[${student.id}]`"
                                                        x-model="student.nilai" placeholder="0-100" min="0" max="100"
                                                        class="w-full px-3 py-1 bg-slate-900 border border-slate-700 rounded-lg text-white text-sm text-center focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-600">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <script>
        function nilaiPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                editId: null,
                students: [],
                loadingStudents: false,

                // For Detail Modal
                detailData: {
                    mapel: '',
                    kelas: '',
                    tp: '',
                    semester: '',
                    harian_ke: '',
                    rata_rata: ''
                },

                formData: {
                    tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                    semester: '{{ $semester ?? "" }}',
                    kelas_id: '',
                    mapel_id: '',
                    harian_ke: ''
                },

                async loadStudents(kelasId, nilaiId = null) {
                    this.loadingStudents = true;
                    this.students = [];

                    try {
                        let url = `{{ route('admin.guru.nilai.students-grades') }}?kelas_id=${kelasId}`;
                        if (nilaiId) {
                            url += `&nilai_id=${nilaiId}`;
                        }


                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Network response was not ok');
                        this.students = await response.json();
                    } catch (error) {
                        console.error('Error loading students:', error);
                        // Fallback attempt or just alert
                        // If logic fails, just empty array
                    } finally {
                        this.loadingStudents = false;
                    }
                },

                openAddModal() {
                    this.students = [];
                    this.formData = {
                        tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                        semester: '{{ $semester ?? "" }}',
                        kelas_id: '',
                        mapel_id: '',
                        harian_ke: ''
                    };
                    this.showAddModal = true;
                },

                openAddModalWithGroup(mapelId, kelasId) {
                    this.openAddModal();
                    if (mapelId) this.formData.mapel_id = mapelId;
                    if (kelasId) {
                        this.formData.kelas_id = kelasId;
                        this.loadStudents(kelasId);
                    }
                },

                openEditModal(id, tpId, semester, kelasId, mapelId, harianKe) {
                    this.editId = id;
                    this.students = [];
                    this.formData = {
                        tp_id: tpId,
                        semester: semester,
                        kelas_id: kelasId,
                        mapel_id: mapelId,
                        harian_ke: harianKe
                    };
                    this.showEditModal = true;
                    if (kelasId) {
                        this.loadStudents(kelasId, id);
                    }
                },

                openDetailModal(id, mapel, kelas, tp, semester, harianKe, kelasId, rataRata) {
                    this.detailData = {
                        mapel: mapel,
                        kelas: kelas,
                        tp: tp,
                        semester: semester,
                        harian_ke: harianKe,
                        rata_rata: rataRata
                    };
                    this.students = [];
                    this.showDetailModal = true;
                    if (kelasId && id) {
                        this.loadStudents(kelasId, id);
                    }
                }
            }
        }
    </script>
@endsection