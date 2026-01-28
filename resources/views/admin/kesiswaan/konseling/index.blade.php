@extends('layouts.admin')

@section('title', 'Data Konseling')
@section('page-title', 'Data Konseling')

@push('styles')
    <style>
        /* Make date/time input icons white */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="konselingPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Konseling Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data bimbingan dan konseling siswa</p>
            </div>
            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
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
            <form action="{{ route('admin.kesiswaan.konseling.index') }}" method="GET"
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
                <a href="{{ route('admin.kesiswaan.konseling.index') }}"
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
                                Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Permasalahan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Hasil/Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @php $currentStudentId = null;
                        $studentIndex = 0; @endphp
                        @forelse($konseling as $index => $item)
                            @if($currentStudentId !== $item->student_id)
                                @php 
                                                                $currentStudentId = $item->student_id;
                                    $studentIndex++;
                                @endphp
                                            <!-- Student Group Header -->
                                            <tr class="bg-gradient-to-r from-blue-900/30 to-indigo-900/30 border-t-2 border-blue-500/30">
                                                <td colspan="6" class="px-4 py-2">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                            {{ strtoupper(substr($item->student->name, 0, 2)) }}
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="text-sm font-bold text-white">{{ $item->student->name }}</div>
                                                            <div class="text-xs text-slate-400">{{ $item->student->nis }} • {{ $item->student->kelas->nm_kls ?? '-' }}</div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-xs font-medium border border-blue-500/30">
                                                                {{ $konseling->where('student_id', $item->student_id)->count() }} sesi
                                                            </span>
                                                            <a href="{{ route('admin.kesiswaan.konseling.print', $item->student_id) }}" 
                                                               target="_blank"
                                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 transition-all text-xs font-medium shadow-lg">
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
                                        <td class="px-4 py-2 text-sm text-slate-400 pl-8">{{ $konseling->firstItem() + $index }}</td>
                                        <td class="px-4 py-2 text-sm text-slate-300">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                                        <td class="px-4 py-2 text-sm text-slate-500">—</td>
                                        <td class="px-4 py-2 text-sm text-slate-300">
                                            <div class="line-clamp-2" title="{{ $item->permasalahan }}">
                                                {{ $item->permasalahan }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border mb-1 {{ $item->status_badge }}">
                                                {{ $item->status_label }}
                                            </span>
                                            @if($item->hasil)
                                                <div class="text-xs text-slate-400 line-clamp-1" title="{{ $item->hasil }}">
                                                    {{ $item->hasil }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                                    <button @click="openEditModal({{ $item->toJson() }})"
                                                        class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-colors cursor-pointer"
                                                        title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.kesiswaan.konseling.destroy', $item) }}" method="POST"
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
                                                @endif
                                                @if(in_array('BK', session('user_roles', [])))
                                                    <button @click="openTindakanModal({{ $item->toJson() }})"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 transition-colors cursor-pointer border border-emerald-500/30"
                                                        title="Tindakan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-xs font-medium">Tindakan</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                        @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                        Tidak ada data konseling
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($konseling->hasPages())
                <div class="flex justify-center">
                    {{ $konseling->links() }}
                </div>
            @endif

            <!-- Add Modal - Wide and Top Positioned -->
            <div x-show="showAddModal" x-cloak
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
                @keydown.escape.window="showAddModal = false">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 mb-10"
                    @click.outside="showAddModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Tambah Data Konseling</h3>
                        <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.kesiswaan.konseling.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column: Student & Problem Data -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-blue-400 border-b border-blue-500/20 pb-2">Informasi Siswa &
                                    Permasalahan</h4>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                        <input type="date" name="tanggal" required x-model="selectedTanggal"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Kelas Filter</label>
                                        <select x-model="filterKelas" @change="filterStudents()"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Tahun Pelajaran dan Semester -->
                                <div class="grid grid-cols-2 gap-4">
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
                                            <option value="Genap" selected>Genap</option>
                                            <option value="Ganjil">Ganjil</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Siswa *</label>
                                    <select name="student_id" required x-model="selectedStudent" @change="updatePelanggaranInfo()"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                        <option value="">-- Pilih Siswa --</option>
                                        <template x-for="student in filteredStudents" :key="student.id">
                                            <option :value="student.id" x-text="student.text"></option>
                                        </template>
                                    </select>
                                    <!-- Display pelanggaran info when student is selected -->
                                    <div x-show="pelanggaranList.length > 0" class="mt-2 p-3 bg-rose-500/10 border border-rose-500/30 rounded-lg">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-rose-400">Pilih Pelanggaran (klik untuk menambahkan ke Permasalahan):</span>
                                            <span class="text-xs text-white font-bold px-2 py-0.5 bg-rose-500 rounded">Total: <span x-text="selectedTotalPoin"></span> Poin</span>
                                        </div>
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            <template x-for="(p, index) in pelanggaranList" :key="index">
                                                <label class="flex items-start gap-2 p-2 bg-slate-800/50 rounded-lg cursor-pointer hover:bg-slate-700/50 transition-colors">
                                                    <input type="checkbox" :value="p.jenis" @change="togglePelanggaran(p.jenis, $event.target.checked)"
                                                        class="mt-0.5 rounded border-slate-600 bg-slate-700 text-rose-500 focus:ring-rose-500">
                                                    <div class="flex-1">
                                                        <span class="text-sm text-white" x-text="p.jenis"></span>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <span class="text-xs text-slate-400" x-text="p.tanggal"></span>
                                                            <span class="text-xs px-1.5 py-0.5 bg-rose-500/20 text-rose-300 rounded" x-text="p.poin + ' poin'"></span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Permasalahan *</label>
                                    <textarea name="permasalahan" required rows="4" x-model="permasalahanText"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Jelaskan permasalahan siswa..."></textarea>
                                </div>
                            </div>

                            <!-- Right Column: Handling & Result -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-emerald-400 border-b border-emerald-500/20 pb-2">Penanganan & Hasil
                                </h4>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Penanganan / Tindakan</label>
                                    <textarea name="penanganan" rows="3"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Tindakan yang dilakukan..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Hasil Konseling</label>
                                    <textarea name="hasil" rows="3"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Hasil yang dicapai..."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Status *</label>
                                        <select name="status" required
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                            <option value="pending">Pending</option>
                                            <option value="diproses">Diproses</option>
                                            <option value="selesai" selected>Selesai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan Lain</label>
                                        <input type="text" name="keterangan"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    </div>
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
                                        <canvas id="signaturePadKonseling" class="w-full" style="height: 120px; touch-action: none;"></canvas>
                                    </div>
                                    <input type="hidden" name="ttd_siswa" id="ttdSiswaInputKonseling">
                                    <div class="flex gap-2 mt-2">
                                        <button type="button" onclick="clearSignatureKonseling()"
                                            class="px-3 py-1.5 bg-slate-700 text-slate-300 text-xs rounded-lg hover:bg-slate-600 transition-colors cursor-pointer">
                                            Hapus Tanda Tangan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-6 mt-2 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Simpan Data
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Modal -->
            <div x-show="showEditModal" x-cloak
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
                @keydown.escape.window="showEditModal = false">
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 mb-10"
                    @click.outside="showEditModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-white">Edit Data Konseling</h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'/admin/kesiswaan/konseling/' + editData.id" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-blue-400 border-b border-blue-500/20 pb-2">Informasi Siswa &
                                    Permasalahan</h4>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Siswa</label>
                                    <input type="hidden" name="student_id" x-model="editData.student_id">
                                    <input type="text" readonly :value="editData.student?.nis + ' - ' + editData.student?.name"
                                        class="w-full px-4 py-2.5 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                    <input type="date" name="tanggal" required x-model="editData.tanggal_formatted"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Permasalahan *</label>
                                    <textarea name="permasalahan" required rows="4" x-model="editData.permasalahan"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Jelaskan permasalahan siswa..."></textarea>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-emerald-400 border-b border-emerald-500/20 pb-2">Penanganan & Hasil
                                </h4>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Penanganan / Tindakan</label>
                                    <textarea name="penanganan" rows="3" x-model="editData.penanganan"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Tindakan yang dilakukan..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Hasil Konseling</label>
                                    <textarea name="hasil" rows="3" x-model="editData.hasil"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                        placeholder="Hasil yang dicapai..."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Status *</label>
                                        <select name="status" required x-model="editData.status"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                            <option value="pending">Pending</option>
                                            <option value="diproses">Diproses</option>
                                            <option value="selesai">Selesai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan Lain</label>
                                        <input type="text" name="keterangan" x-model="editData.keterangan"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-6 mt-2 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Update Data
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <!-- Tindakan Modal (BK Role) -->
                <div x-show="showTindakanModal" x-cloak
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
                    @keydown.escape.window="showTindakanModal = false">
                    <div class="bg-slate-900 rounded-2xl border border-emerald-500/30 p-6 w-full max-w-2xl m-4 mb-10"
                        @click.outside="showTindakanModal = false">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white">Tindakan Konseling</h3>
                                <p class="text-sm text-slate-400">Berikan tindakan dan tanggapan untuk konseling ini</p>
                            </div>
                            <button @click="showTindakanModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form :action="'/admin/kesiswaan/konseling/' + tindakanData.id" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="student_id" x-model="tindakanData.student_id">
                            <input type="hidden" name="tanggal" x-model="tindakanData.tanggal_formatted">
                            <input type="hidden" name="permasalahan" x-model="tindakanData.permasalahan">

                            <!-- Student Info (Read Only) -->
                            <div class="bg-slate-800/50 rounded-xl p-4 mb-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs text-slate-500">Siswa</span>
                                        <p class="text-sm text-white font-medium" x-text="tindakanData.student?.name"></p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-500">Tanggal</span>
                                        <p class="text-sm text-white" x-text="tindakanData.tanggal_formatted"></p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="text-xs text-slate-500">Permasalahan</span>
                                    <p class="text-sm text-slate-300" x-text="tindakanData.permasalahan"></p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Penanganan / Tindakan *</label>
                                    <textarea name="penanganan" required rows="3" x-model="tindakanData.penanganan"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50"
                                        placeholder="Tindakan yang dilakukan..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Hasil Konseling</label>
                                    <textarea name="hasil" rows="3" x-model="tindakanData.hasil"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50"
                                        placeholder="Hasil yang dicapai..."></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Status *</label>
                                        <select name="status" required x-model="tindakanData.status"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50">
                                            <option value="pending">Pending</option>
                                            <option value="diproses">Diproses</option>
                                            <option value="selesai">Selesai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                                        <input type="text" name="keterangan" x-model="tindakanData.keterangan"
                                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50">
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-6 mt-4 border-t border-slate-700/50">
                                <button type="submit"
                                    class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                                    Simpan Tindakan
                                </button>
                                <button type="button" @click="showTindakanModal = false"
                                    class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function konselingPage() {
                    return {
                        showAddModal: false,
                        showEditModal: false,
                        showTindakanModal: false,
                        selectedTanggal: '{{ date("Y-m-d") }}',
                        filterKelas: '',
                        selectedStudent: '',
                        pelanggaranList: [],
                        selectedTotalPoin: 0,
                        permasalahanText: '',
                        selectedItems: [],
                        allStudents: @json($students),
                        filteredStudents: [],
                        editData: {},
                        tindakanData: {},
                        fotoPreview: null,

                        init() {
                            this.filteredStudents = this.allStudents;
                        },

                        openAddModal() {
                            this.showAddModal = true;
                            this.selectedStudent = '';
                            this.pelanggaranList = [];
                            this.selectedTotalPoin = 0;
                            this.permasalahanText = '';
                            this.selectedItems = [];
                            this.filterKelas = '';
                            this.filteredStudents = this.allStudents;
                            this.fotoPreview = null;
                            // Initialize signature pad after modal is shown
                            this.$nextTick(() => {
                                setTimeout(initSignaturePadKonseling, 100);
                            });
                        },

                        filterStudents() {
                            if (this.filterKelas) {
                                this.filteredStudents = this.allStudents.filter(s => s.kelas_id == this.filterKelas);
                            } else {
                                this.filteredStudents = this.allStudents;
                            }
                            this.selectedStudent = ''; // Reset selection
                            this.pelanggaranList = [];
                            this.selectedTotalPoin = 0;
                            this.permasalahanText = '';
                            this.selectedItems = [];
                        },

                        updatePelanggaranInfo() {
                            if (this.selectedStudent) {
                                const student = this.allStudents.find(s => s.id == this.selectedStudent);
                                if (student) {
                                    this.pelanggaranList = student.pelanggaranList || [];
                                    this.selectedTotalPoin = student.total_poin || 0;
                                }
                            } else {
                                this.pelanggaranList = [];
                                this.selectedTotalPoin = 0;
                            }
                            // Reset selections when student changes
                            this.permasalahanText = '';
                            this.selectedItems = [];
                        },

                        togglePelanggaran(jenis, isChecked) {
                            if (isChecked) {
                                // Add to selected items
                                if (!this.selectedItems.includes(jenis)) {
                                    this.selectedItems.push(jenis);
                                }
                            } else {
                                // Remove from selected items
                                this.selectedItems = this.selectedItems.filter(item => item !== jenis);
                            }
                            // Update permasalahan text
                            if (this.selectedItems.length > 0) {
                                this.permasalahanText = 'Siswa melakukan pelanggaran: ' + this.selectedItems.join(', ');
                            } else {
                                this.permasalahanText = '';
                            }
                        },

                        openEditModal(data) {
                            this.editData = {
                                ...data,
                                tanggal_formatted: data.tanggal.split('T')[0],
                            };
                            this.showEditModal = true;
                        },

                        openTindakanModal(data) {
                            this.tindakanData = {
                                ...data,
                                tanggal_formatted: data.tanggal.split('T')[0],
                            };
                            this.showTindakanModal = true;
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
            </script>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
            <script>
                let signaturePadKonseling = null;

                function initSignaturePadKonseling() {
                    const canvas = document.getElementById('signaturePadKonseling');
                    if (canvas && !signaturePadKonseling) {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = 120 * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                        
                        signaturePadKonseling = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: 'rgb(0, 0, 0)'
                        });
                    }
                }

                function clearSignatureKonseling() {
                    if (signaturePadKonseling) {
                        signaturePadKonseling.clear();
                    }
                }

                function getSignatureDataKonseling() {
                    if (signaturePadKonseling && !signaturePadKonseling.isEmpty()) {
                        return signaturePadKonseling.toDataURL('image/png');
                    }
                    return '';
                }

                // Update hidden input before form submit
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form[action*="konseling"][method="POST"]');
                    if (form) {
                        form.addEventListener('submit', function() {
                            const ttdInput = document.getElementById('ttdSiswaInputKonseling');
                            if (ttdInput) {
                                ttdInput.value = getSignatureDataKonseling();
                            }
                        });
                    }
                });
            </script>
@endsection