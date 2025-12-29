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
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
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
            <form action="{{ route('admin.kesiswaan.konseling.index') }}" method="GET"
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
                        @forelse($konseling as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $konseling->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-white">{{ $item->student->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $item->student->nis }} •
                                        {{ $item->student->kelas->nm_kls ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    <div class="line-clamp-2" title="{{ $item->permasalahan }}">
                                        {{ $item->permasalahan }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
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

                <form action="{{ route('admin.kesiswaan.konseling.store') }}" method="POST">
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

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Siswa *</label>
                                <select name="student_id" required x-model="selectedStudent"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih Siswa --</option>
                                    <template x-for="student in filteredStudents" :key="student.id">
                                        <option :value="student.id" x-text="student.text"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Permasalahan *</label>
                                <textarea name="permasalahan" required rows="4"
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
    </div>

    <script>
        function konselingPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                selectedTanggal: '{{ date("Y-m-d") }}',
                filterKelas: '',
                selectedStudent: '',
                allStudents: @json($students),
                filteredStudents: [],
                editData: {},

                init() {
                    this.filteredStudents = this.allStudents;
                },

                openAddModal() {
                    this.showAddModal = true;
                    this.selectedStudent = '';
                    this.filterKelas = '';
                    this.filteredStudents = this.allStudents;
                },

                filterStudents() {
                    if (this.filterKelas) {
                        this.filteredStudents = this.allStudents.filter(s => s.kelas_id == this.filterKelas);
                    } else {
                        this.filteredStudents = this.allStudents;
                    }
                    this.selectedStudent = ''; // Reset selection
                },

                openEditModal(data) {
                    this.editData = {
                        ...data,
                        tanggal_formatted: data.tanggal.split('T')[0],
                    };
                    this.showEditModal = true;
                }
            }
        }
    </script>
@endsection