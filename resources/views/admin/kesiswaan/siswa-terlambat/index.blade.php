@extends('layouts.admin')

@section('title', 'Siswa Terlambat')
@section('page-title', 'Siswa Terlambat')

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
    <div class="space-y-6" x-data="siswaTerlambatPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Siswa Terlambat</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data keterlambatan siswa</p>
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
            <form id="filterForm" action="{{ route('admin.kesiswaan.siswa-terlambat.index') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500 flex-1 min-w-[200px]">

                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    onchange="document.getElementById('filterForm').submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">

                <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="document.getElementById('filterForm').submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jam Datang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Terlambat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Alasan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Status</th>
                            @if(session('user_level') === 'admin' || in_array('PDS', session('user_roles', [])))
                                <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                    Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($siswaTerlambat as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $siswaTerlambat->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->tanggal->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $item->student->nis }}</td>
                                <td class="px-4 py-3 text-sm text-white font-medium">{{ $item->student->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center text-slate-300">
                                    {{ \Carbon\Carbon::parse($item->jam_datang)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        {{ $item->keterlambatan_menit }} menit
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $item->alasan ?? '-' }}</td>
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
                                            <form action="{{ route('admin.kesiswaan.siswa-terlambat.destroy', $item) }}"
                                                method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
                                <td colspan="10" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada data siswa terlambat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($siswaTerlambat->hasPages())
            <div class="flex justify-center">
                {{ $siswaTerlambat->links() }}
            </div>
        @endif

        <!-- Add Modal - Positioned at Top -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 mb-10"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Tambah Data Siswa Terlambat</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.kesiswaan.siswa-terlambat.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Tanggal dan Jam Masuk Terlebih Dahulu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                <input type="date" name="tanggal" required x-model="selectedTanggal"
                                    @change="loadLateStudents()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jam Masuk Seharusnya *</label>
                                <input type="time" name="jam_masuk_seharusnya" required x-model="jamMasuk"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <!-- Filter Kelas dan Siswa Belum Absen -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Kelas</label>
                                <select x-model="selectedKelas" @change="loadLateStudents()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Siswa Belum Absen *
                                    <span class="text-slate-500 text-xs ml-1" x-show="loading">(Memuat...)</span>
                                    <span class="text-slate-500 text-xs ml-1" x-show="!loading && lateStudents.length > 0"
                                        x-text="'(' + lateStudents.length + ' siswa)'"></span>
                                    <span class="text-blue-400 text-xs ml-2" x-show="selectedStudents.length > 0"
                                        x-text="'Dipilih: ' + selectedStudents.length + ' siswa'"></span>
                                </label>
                                <!-- Checkbox List Container -->
                                <div class="w-full bg-slate-800/50 border border-slate-700/50 rounded-xl max-h-64 overflow-y-auto p-3 space-y-2"
                                    x-show="lateStudents.length > 0">
                                    <!-- Select All Checkbox -->
                                    <div
                                        class="flex items-center gap-2 p-2 rounded-lg bg-slate-700/30 hover:bg-slate-700/50 transition-colors border-b border-slate-700/50 mb-2">
                                        <input type="checkbox" @change="toggleSelectAll($event.target.checked)"
                                            :checked="selectedStudents.length === lateStudents.length && lateStudents.length > 0"
                                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50 cursor-pointer">
                                        <span class="text-sm text-blue-400 font-medium">Pilih Semua</span>
                                    </div>
                                    <!-- Individual Checkboxes with Alasan -->
                                    <template x-for="student in lateStudents" :key="student.id">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center gap-2 p-2 rounded-lg hover:bg-slate-700/30 transition-colors">
                                            <div class="flex items-center gap-2 min-w-[250px]">
                                                <input type="checkbox" :name="'student_ids[]'" :value="student.id"
                                                    :checked="selectedStudents.includes(student.id)"
                                                    @change="toggleStudent(student.id)"
                                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50 cursor-pointer">
                                                <span class="text-sm text-white"
                                                    x-text="student.nis + ' - ' + student.name"></span>
                                                <span class="text-xs text-slate-500"
                                                    x-text="'(' + student.kelas + ')'"></span>
                                            </div>
                                            <input type="text" :name="'alasan[' + student.id + ']'"
                                                placeholder="Alasan keterlambatan..."
                                                class="flex-1 px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-xs focus:outline-none focus:border-blue-500/50 placeholder-slate-500">
                                        </div>
                                    </template>
                                </div>
                                <div x-show="lateStudents.length === 0 && !loading && selectedTanggal"
                                    class="w-full px-4 py-3 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-500 text-sm">
                                    Semua siswa sudah absen pada tanggal ini
                                </div>
                            </div>
                        </div>

                        <!-- Jam Datang (Default: Jam Sistem) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jam Datang *</label>
                            <input type="time" name="jam_datang" required x-model="jamDatang"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
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

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Keterangan tambahan"></textarea>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
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

        <!-- Edit Modal - Positioned at Top -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-10 overflow-y-auto"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 mb-10"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Edit Data Siswa Terlambat</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/kesiswaan/siswa-terlambat/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Siswa</label>
                            <input type="hidden" name="student_id" x-model="editData.student_id">
                            <input type="text" readonly :value="editData.student?.nis + ' - ' + editData.student?.name"
                                class="w-full px-4 py-2.5 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                <input type="date" name="tanggal" required x-model="editData.tanggal_formatted"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jam Masuk Seharusnya *</label>
                                <input type="time" name="jam_masuk_seharusnya" required
                                    x-model="editData.jam_masuk_seharusnya"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jam Datang *</label>
                                <input type="time" name="jam_datang" required x-model="editData.jam_datang"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Alasan</label>
                            <input type="text" name="alasan" x-model="editData.alasan"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Alasan keterlambatan">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2" x-model="editData.keterangan"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Keterangan tambahan"></textarea>
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

                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Update
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

    <script>
        function siswaTerlambatPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                selectedTanggal: '{{ date("Y-m-d") }}',
                jamMasuk: '07:00',
                selectedKelas: '',
                selectedStudents: [],
                jamDatang: '',
                lateStudents: [],
                loading: false,
                editData: {},

                getCurrentTime() {
                    const now = new Date();
                    return now.toTimeString().slice(0, 5);
                },

                openAddModal() {
                    this.showAddModal = true;
                    this.selectedStudents = [];
                    this.selectedKelas = '';
                    this.jamDatang = this.getCurrentTime();
                    this.lateStudents = [];
                    this.$nextTick(() => {
                        this.loadLateStudents();
                    });
                },

                loadLateStudents() {
                    if (!this.selectedTanggal || !this.selectedKelas) {
                        this.lateStudents = [];
                        return;
                    }

                    this.loading = true;
                    let url = `{{ url('/admin/kesiswaan/siswa-terlambat/late-students') }}?tanggal=${this.selectedTanggal}`;
                    if (this.selectedKelas) {
                        url += `&kelas_id=${this.selectedKelas}`;
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            this.lateStudents = data;
                            this.selectedStudents = [];
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                },

                toggleStudent(studentId) {
                    const index = this.selectedStudents.indexOf(studentId);
                    if (index === -1) {
                        this.selectedStudents.push(studentId);
                    } else {
                        this.selectedStudents.splice(index, 1);
                    }
                },

                toggleSelectAll(checked) {
                    if (checked) {
                        this.selectedStudents = this.lateStudents.map(s => s.id);
                    } else {
                        this.selectedStudents = [];
                    }
                },

                openEditModal(data) {
                    this.editData = {
                        ...data,
                        tanggal_formatted: data.tanggal.split('T')[0],
                        jam_datang: data.jam_datang.substring(0, 5),
                        jam_masuk_seharusnya: data.jam_masuk_seharusnya.substring(0, 5),
                    };
                    this.showEditModal = true;
                }
            }
        }
    </script>
@endsection