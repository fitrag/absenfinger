@extends('layouts.admin')

@section('title', 'Jurnal Guru')
@section('page-title', 'Jurnal Guru')

@section('content')
    <div class="space-y-6" x-data="jurnalPage()">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Jurnal Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola jurnal mengajar harian</p>
            </div>
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Jurnal
            </button>
        </div>

        <!-- Filter & Search -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form action="{{ route('admin.guru.jurnal.index') }}" method="GET">
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

                    <!-- Kelas Filter -->
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Kelas</label>
                        <select name="kelas_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:border-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nm_kls }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grouped Jurnal Display -->
        @if($groupedJurnals->isEmpty())
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-slate-400">Belum ada jurnal untuk tanggal ini.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($groupedJurnals as $group)
                    <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                        <!-- Group Header -->
                        <div class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 px-4 py-3 border-b border-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold">{{ $group['mapel']->nm_mapel ?? 'Mapel tidak diketahui' }}
                                    </h3>
                                    <p class="text-sm text-slate-400">
                                        Kelas: {{ $group['kelas']->nm_kls ?? 'Kelas tidak diketahui' }}
                                        <span class="mx-2 text-slate-600">|</span>
                                        Semester {{ $group['items']->first()->semester }}
                                        <span class="mx-2 text-slate-600">|</span>
                                        TP: {{ $group['tp']->nm_tp ?? '-' }}
                                    </p>
                                </div>
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-medium">
                                        {{ $group['items']->count() }} entri
                                    </span>
                                    <button
                                        @click="openAddModalWithGroup({{ $group['mapel']->id ?? 'null' }}, {{ $group['kelas']->id ?? 'null' }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg hover:bg-emerald-500/30 text-xs font-medium cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah
                                    </button>
                                    <a href="{{ route('admin.guru.jurnal.pdf', ['mapel_id' => $group['mapel']->id ?? 0, 'kelas_id' => $group['kelas']->id ?? 0, 'tp_id' => $tpId, 'semester' => $semester]) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-orange-500/20 to-amber-500/20 text-orange-400 rounded-lg hover:from-orange-500/30 hover:to-amber-500/30 text-xs font-medium border border-orange-500/30">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Group Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-800/50 bg-slate-800/20">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Tanggal</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Jam Ke</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Tm Ke</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Absensi</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">Materi</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    @foreach($group['items'] as $idx => $jurnal)
                                        <tr class="hover:bg-slate-800/30 transition-colors">
                                            <td class="px-4 py-2 text-sm text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-300">
                                                {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('D, d M Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-slate-300">{{ $jurnal->jam_ke ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-300">{{ $jurnal->tmke ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-300">{{ $jurnal->absensi ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-slate-300 max-w-xs truncate">{{ $jurnal->materi ?? '-' }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        @click="openEditModal({{ $jurnal->id }}, '{{ $jurnal->tanggal }}', {{ json_encode($jurnal->tp_id ?? '') }}, {{ json_encode($jurnal->semester ?? '') }}, {{ json_encode($jurnal->kelas_id ?? '') }}, {{ json_encode($jurnal->mapel_id ?? '') }}, {{ json_encode($jurnal->jam_ke ?? '') }}, {{ json_encode($jurnal->tmke ?? '') }}, {{ json_encode($jurnal->absensi ?? '') }}, {{ json_encode($jurnal->materi ?? '') }}, {{ json_encode($jurnal->kegiatan ?? '') }}, {{ json_encode($jurnal->catatan ?? '') }})"
                                                        class="text-amber-400 hover:text-amber-300 transition-colors cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.guru.jurnal.destroy', $jurnal->id) }}" method="POST"
                                                        class="inline" onsubmit="return confirm('Hapus jurnal ini?')">
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="showAddModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Tambah Jurnal</h3>
                        <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.guru.jurnal.store') }}" method="POST" @submit="populateAttendanceData">
                        @csrf
                        <input type="hidden" name="student_attendance" :value="JSON.stringify(studentAttendance)">
                        <div class="space-y-4">
                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal</label>
                                <input type="date" name="tanggal" x-model="formData.tanggal"
                                    @change="if(formData.kelas_id) loadStudents(formData.kelas_id, false)"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>

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

                            <!-- Absensi Siswa (Full Width, Below Kelas) -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Absensi Siswa</label>
                                <textarea name="absensi" x-model="formData.absensi" rows="3"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 mb-2"
                                    placeholder="Hasil absensi siswa..."></textarea>

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
                                    class="bg-slate-800/50 rounded-xl overflow-hidden">
                                    <!-- Summary -->
                                    <div class="px-3 py-2 bg-slate-700/50 flex items-center justify-between text-xs">
                                        <span class="text-slate-300">Total: <span class="font-semibold text-white"
                                                x-text="students.length"></span> siswa</span>
                                        <div class="flex gap-3">
                                            <span class="text-green-400">H: <span
                                                    x-text="getAttendanceCount('H')"></span></span>
                                            <span class="text-yellow-400">S: <span
                                                    x-text="getAttendanceCount('S')"></span></span>
                                            <span class="text-blue-400">I: <span
                                                    x-text="getAttendanceCount('I')"></span></span>
                                            <span class="text-red-400">A: <span
                                                    x-text="getAttendanceCount('A')"></span></span>
                                            <span class="text-purple-400">AL: <span
                                                    x-text="getAttendanceCount('AL')"></span></span>
                                        </div>
                                    </div>
                                    <!-- Student list with scroll -->
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="(student, index) in students" :key="student.id">
                                            <div
                                                class="flex items-center justify-between px-3 py-2 border-b border-slate-700/50 hover:bg-slate-700/30">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-slate-500 text-xs w-6" x-text="index + 1"></span>
                                                    <span class="text-white text-sm" x-text="student.name"></span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button type="button" @click="setAttendance(student.id, 'H')"
                                                        :class="studentAttendance[student.id] === 'H' || !studentAttendance[student.id] ? 'bg-green-500 text-white shadow-lg shadow-green-500/30 ring-2 ring-green-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-green-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">H</button>

                                                    <button type="button" @click="setAttendance(student.id, 'S')"
                                                        :class="studentAttendance[student.id] === 'S' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/30 ring-2 ring-yellow-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-yellow-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">S</button>

                                                    <button type="button" @click="setAttendance(student.id, 'I')"
                                                        :class="studentAttendance[student.id] === 'I' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30 ring-2 ring-blue-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-blue-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">I</button>

                                                    <button type="button" @click="setAttendance(student.id, 'A')"
                                                        :class="studentAttendance[student.id] === 'A' ? 'bg-red-500 text-white shadow-lg shadow-red-500/30 ring-2 ring-red-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-red-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">A</button>

                                                    <button type="button" @click="setAttendance(student.id, 'AL')"
                                                        :class="studentAttendance[student.id] === 'AL' ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30 ring-2 ring-purple-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-purple-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">AL</button>
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Jam Ke -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Jam Ke</label>
                                    <input type="text" name="jam_ke" x-model="formData.jam_ke"
                                        placeholder="Contoh: 1-2, 3-4"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>

                                <!-- Temu Ke -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Temu Ke</label>
                                    <input type="text" name="tmke" x-model="formData.tmke" placeholder="Contoh: 1, 2, 3"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>
                            </div>



                            <!-- Materi -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Materi</label>
                                <textarea name="materi" x-model="formData.materi" rows="2"
                                    placeholder="Materi yang diajarkan..."
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                            </div>

                            <!-- Kegiatan -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Kegiatan</label>
                                <textarea name="kegiatan" x-model="formData.kegiatan" rows="2"
                                    placeholder="Kegiatan pembelajaran..."
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Catatan</label>
                                <textarea name="catatan" x-model="formData.catatan" rows="2"
                                    placeholder="Catatan tambahan (opsional)..."
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
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto"
            @keydown.escape.window="showEditModal = false">
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl"
                    @click.outside="showEditModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Edit Jurnal</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form :action="`{{ url('admin/guru/jurnal') }}/${editId}`" method="POST"
                        @submit="populateAttendanceData">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="student_attendance" :value="JSON.stringify(studentAttendance)">
                        <div class="space-y-4">
                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Tanggal</label>
                                <input type="date" name="tanggal" x-model="formData.tanggal"
                                    @change="if(formData.kelas_id) loadStudents(formData.kelas_id, false)"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>

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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Jam Ke -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Jam Ke</label>
                                    <input type="text" name="jam_ke" x-model="formData.jam_ke"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>

                                <!-- Temu Ke -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-1">Temu Ke</label>
                                    <input type="text" name="tmke" x-model="formData.tmke"
                                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Absensi Siswa -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Absensi Siswa</label>
                                <textarea name="absensi" x-model="formData.absensi" rows="6"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500 mb-2"
                                    placeholder="Hasil absensi siswa..."></textarea>

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
                                    class="bg-slate-800/50 rounded-xl overflow-hidden">
                                    <!-- Summary -->
                                    <div class="px-3 py-2 bg-slate-700/50 flex items-center justify-between text-xs">
                                        <span class="text-slate-300">Total: <span class="font-semibold text-white"
                                                x-text="students.length"></span> siswa</span>
                                        <div class="flex gap-3">
                                            <span class="text-green-400">H: <span
                                                    x-text="getAttendanceCount('H')"></span></span>
                                            <span class="text-yellow-400">S: <span
                                                    x-text="getAttendanceCount('S')"></span></span>
                                            <span class="text-blue-400">I: <span
                                                    x-text="getAttendanceCount('I')"></span></span>
                                            <span class="text-red-400">A: <span
                                                    x-text="getAttendanceCount('A')"></span></span>
                                            <span class="text-purple-400">AL: <span
                                                    x-text="getAttendanceCount('AL')"></span></span>
                                        </div>
                                    </div>
                                    <!-- Student list with scroll -->
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="(student, index) in students" :key="student.id">
                                            <div
                                                class="flex items-center justify-between px-3 py-2 border-b border-slate-700/50 hover:bg-slate-700/30">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-slate-500 text-xs w-6" x-text="index + 1"></span>
                                                    <span class="text-white text-sm" x-text="student.name"></span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button type="button" @click="setAttendance(student.id, 'H')"
                                                        :class="studentAttendance[student.id] === 'H' || !studentAttendance[student.id] ? 'bg-green-500 text-white shadow-lg shadow-green-500/30 ring-2 ring-green-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-green-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">H</button>

                                                    <button type="button" @click="setAttendance(student.id, 'S')"
                                                        :class="studentAttendance[student.id] === 'S' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/30 ring-2 ring-yellow-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-yellow-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">S</button>

                                                    <button type="button" @click="setAttendance(student.id, 'I')"
                                                        :class="studentAttendance[student.id] === 'I' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30 ring-2 ring-blue-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-blue-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">I</button>

                                                    <button type="button" @click="setAttendance(student.id, 'A')"
                                                        :class="studentAttendance[student.id] === 'A' ? 'bg-red-500 text-white shadow-lg shadow-red-500/30 ring-2 ring-red-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-red-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">A</button>

                                                    <button type="button" @click="setAttendance(student.id, 'AL')"
                                                        :class="studentAttendance[student.id] === 'AL' ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30 ring-2 ring-purple-400/50 scale-105' : 'bg-slate-700 text-slate-400 hover:bg-slate-600 hover:text-purple-400'"
                                                        class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200 cursor-pointer flex items-center justify-center hover:scale-110 active:scale-95">AL</button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Materi -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Materi</label>
                                <textarea name="materi" x-model="formData.materi" rows="2"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                            </div>

                            <!-- Kegiatan -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Kegiatan</label>
                                <textarea name="kegiatan" x-model="formData.kegiatan" rows="2"
                                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Catatan</label>
                                <textarea name="catatan" x-model="formData.catatan" rows="2"
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

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg z-50 max-w-md">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                    <ul class="text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="show = false" class="absolute top-2 right-2 text-white/80 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <script>
        function jurnalPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                editId: null,
                students: [],
                studentAttendance: {},
                loadingStudents: false,
                formData: {
                    tanggal: '{{ now()->toDateString() }}',
                    tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                    semester: '{{ $semester ?? "" }}',
                    kelas_id: '',
                    mapel_id: '',
                    jam_ke: '',
                    tmke: '',
                    absensi: '',
                    materi: '',
                    kegiatan: '',
                    catatan: ''
                },

                init() {
                    // Watcher removed to use explicit @change
                },

                async loadStudents(kelasId, updateSummary = true) {
                    this.loadingStudents = true;
                    this.students = [];
                    // Keep existing attendance if we are just reloading but keeping same date/class context? 
                    // No, we should reload from server truth unless we want to persist unsaved changes.
                    // For now, reload from server.
                    this.studentAttendance = {};

                    try {
                        const tanggal = this.formData.tanggal;
                        const url = `{{ route('admin.guru.jurnal.students', ['kelas_id' => ':id']) }}?tanggal=${tanggal}`.replace(':id', kelasId);
                        const response = await fetch(url);

                        if (!response.ok) throw new Error('Network response was not ok');

                        const data = await response.json();
                        this.students = data;

                        // Set attendance based on server data (initial_status)
                        data.forEach(student => {
                            if (student.initial_status) {
                                this.studentAttendance[student.id] = student.initial_status;
                            } else {
                                this.studentAttendance[student.id] = 'H';
                            }
                        });

                        // Only update summary if requested (e.g. new journal)
                        if (updateSummary) {
                            this.updateAbsensiSummary();
                        }
                    } catch (error) {
                        console.error('Error loading students:', error);
                        alert('Gagal memuat data siswa. Silakan coba lagi.');
                    } finally {
                        this.loadingStudents = false;
                    }
                },

                setAttendance(studentId, status) {
                    this.studentAttendance[studentId] = status;
                    this.updateAbsensiSummary();
                },

                getAttendanceCount(status) {
                    if (status === 'H') {
                        return this.students.filter(s => !this.studentAttendance[s.id] || this.studentAttendance[s.id] === 'H').length;
                    }
                    return Object.values(this.studentAttendance).filter(s => s === status).length;
                },

                updateAbsensiSummary() {
                    const hCount = this.getAttendanceCount('H');
                    const sCount = this.getAttendanceCount('S');
                    const iCount = this.getAttendanceCount('I');
                    const aCount = this.getAttendanceCount('A');
                    const alCount = this.getAttendanceCount('AL');

                    // Helper to shorten name (Title Case, 1st word + 1st letter of 2nd word)
                    const formatName = (name) => {
                        const parts = name.toLowerCase().split(' ');
                        let formatted = parts[0].charAt(0).toUpperCase() + parts[0].slice(1);
                        if (parts.length > 1) {
                            formatted += ' ' + parts[1].charAt(0).toUpperCase();
                        }
                        return formatted;
                    };

                    // Helper to get names for a status
                    const getNames = (status) => {
                        return this.students
                            .filter(s => this.studentAttendance[s.id] === status)
                            .map(s => formatName(s.name))
                            .join(', ');
                    };

                    const sNames = sCount > 0 ? `(${getNames('S')})` : '';
                    const iNames = iCount > 0 ? `(${getNames('I')})` : '';
                    const aNames = aCount > 0 ? `(${getNames('A')})` : '';
                    const alNames = alCount > 0 ? `(${getNames('AL')})` : '';

                    // Format: H: 10 | S: 2 (Name1, Name2) | I: 0 | A: 1 (Name3) | AL: 1 (Name4)
                    let summary = `H: ${hCount} | S: ${sCount} ${sNames} | I: ${iCount} ${iNames} | A: ${aCount} ${aNames}`;

                    if (alCount > 0) {
                        summary += ` | AL: ${alCount} ${alNames}`;
                    }

                    this.formData.absensi = summary;
                },

                openAddModal() {
                    this.students = [];
                    this.studentAttendance = {};
                    this.formData = {
                        tanggal: '{{ now()->toDateString() }}',
                        tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                        semester: '{{ $semester ?? "" }}',
                        kelas_id: '',
                        mapel_id: '',
                        jam_ke: '',
                        tmke: '',
                        absensi: '',
                        materi: '',
                        kegiatan: '',
                        catatan: ''
                    };
                    this.showAddModal = true;
                },

                openEditModal(id, tanggal, tpId, semester, kelasId, mapelId, jamKe, tmke, absensi, materi, kegiatan, catatan) {
                    this.editId = id;
                    this.students = [];
                    this.studentAttendance = {};
                    this.formData = {
                        tanggal: tanggal || '{{ now()->toDateString() }}',
                        tp_id: tpId || '',
                        semester: semester || '',
                        kelas_id: kelasId || '',
                        mapel_id: mapelId || '',
                        jam_ke: jamKe || '',
                        tmke: tmke || '',
                        absensi: absensi || '',
                        materi: materi || '',
                        kegiatan: kegiatan || '',
                        catatan: catatan || ''
                    };
                    this.showEditModal = true;

                    // Load students for existing class without overwriting summary
                    if (kelasId) {
                        this.loadStudents(kelasId, false);
                    }
                },

                openAddModalWithGroup(mapelId, kelasId) {
                    this.students = [];
                    this.studentAttendance = {};
                    this.formData = {
                        tanggal: '{{ now()->toDateString() }}',
                        tp_id: '{{ $tpId ?? $activeTp->id ?? "" }}',
                        semester: '{{ $semester ?? "" }}',
                        kelas_id: kelasId || '',
                        mapel_id: mapelId || '',
                        jam_ke: '',
                        tmke: '',
                        absensi: '',
                        materi: '',
                        kegiatan: '',
                        catatan: ''
                    };
                    this.showAddModal = true;
                    // Load students if kelas is pre-selected
                    if (kelasId) {
                        this.loadStudents(kelasId, true);
                    }
                },

                // Populate default H status for students without explicit status before form submit
                populateAttendanceData() {
                    // Ensure all students have a status (default to H if not set)
                    this.students.forEach(student => {
                        if (!this.studentAttendance[student.id]) {
                            this.studentAttendance[student.id] = 'H';
                        }
                    });
                    // Allow form submission to continue
                    return true;
                }
            }
        }
    </script>
@endsection