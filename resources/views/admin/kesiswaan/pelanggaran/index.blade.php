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
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-red-600 text-white font-medium rounded-xl hover:from-rose-600 hover:to-red-700 transition-all shadow-lg shadow-rose-500/25 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Data
            </button>
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

                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">

                <select name="kelas_id"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($pelanggarans as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $pelanggarans->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $item->student->nis }}</td>
                                <td class="px-4 py-3 text-sm text-white font-medium">{{ $item->student->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->student->kelas->nm_kls ?? '-' }}</td>
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

                <form action="{{ route('admin.kesiswaan.pelanggaran.store') }}" method="POST">
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
                                    <option value="pending" selected>Pending</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
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

                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
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

                <form :action="editFormAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <!-- Tanggal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal *</label>
                                <input type="date" name="tanggal" required x-model="editData.tanggal"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Siswa *</label>
                                <select name="student_id" required x-model="editData.student_id"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    @foreach($studentsList as $student)
                                        <option value="{{ $student->id }}">{{ $student->nis }} - {{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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

    <script>
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

                openAddModal() {
                    this.selectedKelas = '';
                    this.selectedStudent = '';
                    this.students = this.allStudents;
                    this.showAddModal = true;
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
                }
            }
        }
    </script>
@endsection