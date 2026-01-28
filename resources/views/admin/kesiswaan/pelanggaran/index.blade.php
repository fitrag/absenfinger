@extends('layouts.admin')

@section('title', 'Pelanggaran')
@section('page-title', 'Pelanggaran')

@push('styles')
    <style>
        /* Make date input icons white */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="pelanggaranPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Pelanggaran</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data pelanggaran siswa</p>
            </div>
            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-red-600 text-white font-medium rounded-xl hover:from-rose-600 hover:to-red-700 transition-all shadow-lg shadow-rose-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Data
                </button>
            @endif
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form action="{{ route('admin.kesiswaan.pelanggaran.index') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500 flex-1 min-w-[200px]">

                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">

                <select name="kelas_id" onchange="this.form.submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <select name="semester" onchange="this.form.submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>

                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan.pelanggaran.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Reset
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jenis Pelanggaran</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Poin</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Status</th>
                            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                    Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @php $currentStudentId = null; $studentIndex = 0; @endphp
                        @forelse($pelanggarans as $index => $item)
                            @if($currentStudentId !== $item->student_id)
                                @php 
                                    $currentStudentId = $item->student_id; 
                                    $studentIndex++;
                                    $studentTotalPoin = $pelanggarans->where('student_id', $item->student_id)->sum('poin');
                                @endphp
                                <!-- Student Group Header -->
                                <tr class="bg-gradient-to-r from-rose-900/30 to-red-900/30 border-t-2 border-rose-500/30">
                                    <td colspan="9" class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                {{ strtoupper(substr($item->student->name, 0, 2)) }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-sm font-bold text-white">{{ $item->student->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $item->student->nis }} • {{ $item->student->kelas->nm_kls ?? '-' }}</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 rounded-lg bg-rose-500/20 text-rose-300 text-xs font-medium border border-rose-500/30">
                                                    Total: {{ $studentTotalPoin }} poin
                                                </span>
                                                <a href="{{ route('admin.kesiswaan.pelanggaran.print', $item->student_id) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 transition-all text-xs font-medium shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-800/30 transition-colors {{ $studentIndex % 2 == 0 ? 'bg-slate-800/10' : '' }}">
                                <td class="px-4 py-3 text-sm text-slate-400 pl-8">{{ $pelanggarans->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">—</td>
                                <td class="px-4 py-3 text-sm text-slate-500">—</td>
                                <td class="px-4 py-3 text-sm text-slate-500">—</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->jenis_pelanggaran }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $item->poin_badge }}">
                                        {{ $item->poin }} poin
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $item->status_badge }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openEditModal({{ $item->toJson() }})"
                                                class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-colors cursor-pointer"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.kesiswaan.pelanggaran.destroy', $item) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer"
                                                    title="Hapus">
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
                                <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Tidak ada data pelanggaran
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($pelanggarans->hasPages())
            <div class="flex justify-center">
                {{ $pelanggarans->links() }}
            </div>
        @endif

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 mb-10"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Tambah Data Pelanggaran</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.kesiswaan.pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <!-- Tanggal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Kelas (Filter)</label>
                                <select x-model="selectedKelas" @change="loadStudents()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tahun Pelajaran dan Semester -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran</label>
                                <input type="hidden" name="tp_id" value="{{ $tpAktif->id ?? '' }}">
                                <input type="text" readonly value="{{ $tpAktif->nm_tp ?? 'Tidak ada TP aktif' }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Semester *</label>
                                <select name="semester" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="Ganjil" {{ ($semesterAktif ?? 'Ganjil') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ ($semesterAktif ?? 'Ganjil') === 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                        </div>

                        <!-- Siswa -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Siswa *
                                <span class="text-slate-500 text-xs ml-1" x-show="loading">(Memuat...)</span>
                                <span class="text-slate-500 text-xs ml-1" x-show="!loading && students.length > 0"
                                    x-text="'(' + students.length + ' siswa)'"></span>
                            </label>
                            <select name="student_id" required x-model="selectedStudent"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">-- Pilih Siswa --</option>
                                <template x-for="student in students" :key="student.id">
                                    <option :value="student.id"
                                        x-text="student.nis + ' - ' + student.name + ' (' + student.kelas + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Jenis Pelanggaran dan Poin -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Pelanggaran *</label>
                                <input type="text" name="jenis_pelanggaran" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="Contoh: Terlambat, Tidak berseragam, dll">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Poin *</label>
                                <input type="number" name="poin" required min="0" value="5"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="Jumlah poin pelanggaran">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Deskripsi detail pelanggaran"></textarea>
                        </div>

                        <!-- Tindakan dan Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tindakan</label>
                                <input type="text" name="tindakan"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="Tindakan yang diberikan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Status *</label>
                                <select name="status" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="selesai" selected>Selesai</option>
                                    <option value="pending">Pending</option>
                                    <option value="diproses">Diproses</option>
                                </select>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Keterangan tambahan"></textarea>
                        </div>

                        <!-- Foto Bukti -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Foto Bukti</label>
                            <div class="flex items-center gap-4">
                                <input type="file" name="foto_bukti" accept="image/*" @change="previewFoto($event)"
                                    class="flex-1 px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-500 file:text-white hover:file:bg-blue-600 cursor-pointer">
                                <img x-show="fotoPreview" :src="fotoPreview" class="w-16 h-16 rounded-lg object-cover border border-slate-600">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
                        </div>

                        <!-- Tanda Tangan Siswa -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanda Tangan Siswa</label>
                            <div class="border border-slate-700/50 rounded-xl overflow-hidden bg-white">
                                <canvas id="signaturePad" class="w-full" style="height: 150px; touch-action: none;"></canvas>
                            </div>
                            <input type="hidden" name="ttd_siswa" id="ttdSiswaInput">
                            <div class="flex gap-2 mt-2">
                                <button type="button" onclick="clearSignature()"
                                    class="px-3 py-1.5 bg-slate-700 text-slate-300 text-xs rounded-lg hover:bg-slate-600 transition-colors cursor-pointer">
                                    Hapus Tanda Tangan
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit" onclick="document.getElementById('ttdSiswaInput').value = getSignatureData();"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white font-medium rounded-xl hover:from-rose-600 hover:to-red-700 transition-all cursor-pointer">
                                Simpan
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4 mb-10"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Edit Data Pelanggaran</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="editFormAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tp_id" :value="editData.tp_id">
                    <div class="space-y-4">
                        <!-- Tanggal dan Semester -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                <input type="date" name="tanggal" required x-model="editData.tanggal"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Semester *</label>
                                <select name="semester" required x-model="editData.semester"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>

                        <!-- Siswa -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Siswa *</label>
                            <select name="student_id" required x-model="editData.student_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @foreach($studentsList as $student)
                                    <option value="{{ $student->id }}">{{ $student->nis }} - {{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Pelanggaran dan Poin -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Pelanggaran *</label>
                                <input type="text" name="jenis_pelanggaran" required x-model="editData.jenis_pelanggaran"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Poin *</label>
                                <input type="number" name="poin" required min="0" x-model="editData.poin"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" x-model="editData.deskripsi"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"></textarea>
                        </div>

                        <!-- Tindakan dan Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tindakan</label>
                                <input type="text" name="tindakan" x-model="editData.tindakan"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Status *</label>
                                <select name="status" required x-model="editData.status"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="pending">Pending</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2" x-model="editData.keterangan"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"></textarea>
                        </div>

                        <!-- Foto Bukti (Existing + New) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Foto Bukti</label>
                            <div class="flex items-center gap-4">
                                <template x-if="editData.foto_bukti">
                                    <img :src="'/storage/' + editData.foto_bukti" class="w-20 h-20 rounded-lg object-cover border border-slate-600">
                                </template>
                                <input type="file" name="foto_bukti" accept="image/*"
                                    class="flex-1 px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-500 file:text-white hover:file:bg-blue-600 cursor-pointer">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                        </div>

                        <!-- Tanda Tangan Siswa (Existing) -->
                        <div x-show="editData.ttd_siswa">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanda Tangan Siswa (Saat Ini)</label>
                            <div class="border border-slate-700/50 rounded-xl overflow-hidden bg-white p-2 inline-block">
                                <img :src="editData.ttd_siswa" class="h-24 max-w-full">
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePad = null;

        function initSignaturePad() {
            const canvas = document.getElementById('signaturePad');
            if (canvas) {
                // Reset signaturePad if it exists
                signaturePad = null;
                
                // Set canvas size based on container width
                const container = canvas.parentElement;
                const width = container.offsetWidth || 400;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                
                canvas.width = width * ratio;
                canvas.height = 150 * ratio;
                canvas.style.width = width + 'px';
                canvas.style.height = '150px';
                
                const ctx = canvas.getContext("2d");
                ctx.scale(ratio, ratio);
                
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
                
                // Clear canvas with white background
                signaturePad.clear();
                console.log('Signature pad initialized successfully');
            }
        }

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
            }
        }

        function getSignatureData() {
            if (signaturePad && !signaturePad.isEmpty()) {
                const data = signaturePad.toDataURL('image/png');
                console.log('Signature data length:', data.length);
                return data;
            }
            console.log('Signature pad is empty or not initialized');
            return '';
        }

        // Watch for modal open with MutationObserver
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        const canvas = document.getElementById('signaturePad');
                        if (canvas && canvas.offsetParent !== null && !signaturePad) {
                            setTimeout(initSignaturePad, 200);
                        }
                    }
                });
            });
            
            // Observe the body for changes
            observer.observe(document.body, { 
                attributes: true, 
                subtree: true,
                attributeFilter: ['style', 'class']
            });
        });

        function pelanggaranPage() {
            // Prepare students data from PHP
            const allStudentsData = [
                @foreach($studentsList as $student)
                        {
                    id: {{ $student->id }},
                    nis: '{{ $student->nis }}',
                    name: '{{ addslashes($student->name) }}',
                    kelas: '{{ $student->kelas->nm_kls ?? "-" }}',
                    kelas_id: {{ $student->kelas_id ?? 'null' }}
                        },
                @endforeach
                    ];

            return {
                showAddModal: false,
                showEditModal: false,
                selectedKelas: '',
                selectedStudent: '',
                loading: false,
                students: allStudentsData,
                allStudents: allStudentsData,
                editData: {},
                editFormAction: '',
                fotoPreview: null,

                openAddModal() {
                    this.selectedKelas = '';
                    this.selectedStudent = '';
                    this.students = this.allStudents;
                    this.fotoPreview = null;
                    this.showAddModal = true;
                    // Initialize signature pad after modal is shown
                    this.$nextTick(() => {
                        setTimeout(initSignaturePad, 100);
                    });
                },

                openEditModal(item) {
                    this.editData = {
                        id: item.id,
                        student_id: item.student_id,
                        tanggal: item.tanggal ? item.tanggal.split('T')[0] : '',
                        jenis_pelanggaran: item.jenis_pelanggaran,
                        poin: item.poin,
                        deskripsi: item.deskripsi || '',
                        tindakan: item.tindakan || '',
                        keterangan: item.keterangan || '',
                        status: item.status,
                        tp_id: item.tp_id || '',
                        semester: item.semester || 'Genap',
                        foto_bukti: item.foto_bukti || '',
                        ttd_siswa: item.ttd_siswa || '',
                    };
                    this.editFormAction = '{{ route("admin.kesiswaan.pelanggaran.index") }}/' + item.id;
                    this.showEditModal = true;
                },

                loadStudents() {
                    if (this.selectedKelas === '') {
                        this.students = this.allStudents;
                    } else {
                        this.students = this.allStudents.filter(s => s.kelas_id == this.selectedKelas);
                    }
                    this.selectedStudent = '';
                },

                previewFoto(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.fotoPreview = URL.createObjectURL(file);
                    } else {
                        this.fotoPreview = null;
                    }
                }
            }
        }

        // Update hidden input before form submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action*="pelanggaran"]');
            if (form) {
                form.addEventListener('submit', function() {
                    const ttdInput = document.getElementById('ttdSiswaInput');
                    if (ttdInput) {
                        ttdInput.value = getSignatureData();
                    }
                });
            }
        });
    </script>
@endsection