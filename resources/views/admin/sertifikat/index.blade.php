@extends('layouts.admin')

@section('title', 'Sertifikat PKL')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Sertifikat PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola sertifikat Praktik Kerja Lapangan</p>
            </div>
            <button onclick="openModal('addModal')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Sertifikat</span>
            </button>
        </div>

        <!-- Stats -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-400">Total Sertifikat</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" action="{{ route('admin.sertifikat.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor sertifikat/nama siswa..."
                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nomor Sertifikat
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Tanggal Terbit
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Nilai</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Predikat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">File</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($sertifikats as $index => $sertifikat)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $sertifikats->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-white">{{ $sertifikat->nomor_sertifikat }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-white">{{ $sertifikat->pkl->student->nama ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $sertifikat->pkl->student->nis ?? '-' }} •
                                        {{ $sertifikat->pkl->student->kelas->nm_kls ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-slate-300">
                                    {{ $sertifikat->tanggal_terbit?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-white font-semibold">
                                    {{ $sertifikat->nilai ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs font-medium rounded-lg {{ $sertifikat->predikat_badge }} border">
                                        {{ $sertifikat->predikat }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sertifikat->file_sertifikat)
                                        <a href="{{ asset('storage/' . $sertifikat->file_sertifikat) }}" target="_blank"
                                            class="text-blue-400 hover:text-blue-300">
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="openEditModal({{ json_encode($sertifikat) }})"
                                            class="p-1.5 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.sertifikat.destroy', $sertifikat->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer">
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
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data sertifikat</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sertifikats->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $sertifikats->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-lg p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-6">Tambah Sertifikat</h3>
                <form action="{{ route('admin.sertifikat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">PKL (Siswa) <span
                                    class="text-red-400">*</span></label>
                            <select name="pkl_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih PKL</option>
                                @foreach($pklList as $pkl)
                                    <option value="{{ $pkl->id }}">{{ $pkl->student->nis ?? '-' }} -
                                        {{ $pkl->student->nama ?? '-' }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Hanya menampilkan PKL dengan status "Selesai" yang belum
                                memiliki sertifikat</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nomor Sertifikat <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nomor_sertifikat" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Terbit <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="tanggal_terbit" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nilai</label>
                                <input type="number" name="nilai" min="0" max="100" step="0.01"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Predikat <span
                                        class="text-red-400">*</span></label>
                                <select name="predikat" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="Sangat Baik">Sangat Baik</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Cukup">Cukup</option>
                                    <option value="Kurang">Kurang</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">File Sertifikat</label>
                            <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <p class="text-xs text-slate-500 mt-1">Format: PDF, JPG, PNG. Maks: 2MB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-lg p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-6">Edit Sertifikat</h3>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Siswa</label>
                            <input type="text" id="editSiswa" disabled
                                class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-700/50 rounded-xl text-slate-400 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nomor Sertifikat <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nomor_sertifikat" id="editNomorSertifikat" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Terbit <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="tanggal_terbit" id="editTanggalTerbit" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nilai</label>
                                <input type="number" name="nilai" id="editNilai" min="0" max="100" step="0.01"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Predikat <span
                                        class="text-red-400">*</span></label>
                                <select name="predikat" id="editPredikat" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="Sangat Baik">Sangat Baik</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Cukup">Cukup</option>
                                    <option value="Kurang">Kurang</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">File Sertifikat (kosongkan jika
                                tidak diubah)</label>
                            <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" id="editKeterangan" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        function openEditModal(sertifikat) {
            document.getElementById('editForm').action = `/admin/sertifikat/${sertifikat.id}`;
            document.getElementById('editSiswa').value = sertifikat.pkl && sertifikat.pkl.student ? `${sertifikat.pkl.student.nis} - ${sertifikat.pkl.student.nama}` : '-';
            document.getElementById('editNomorSertifikat').value = sertifikat.nomor_sertifikat || '';
            document.getElementById('editTanggalTerbit').value = sertifikat.tanggal_terbit ? sertifikat.tanggal_terbit.split('T')[0] : '';
            document.getElementById('editNilai').value = sertifikat.nilai || '';
            document.getElementById('editPredikat').value = sertifikat.predikat || 'Baik';
            document.getElementById('editKeterangan').value = sertifikat.keterangan || '';
            openModal('editModal');
        }
    </script>
@endsection