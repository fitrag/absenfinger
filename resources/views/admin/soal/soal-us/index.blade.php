@extends('layouts.admin')

@section('title', 'Soal US')

@section('content')
    <div class="space-y-6" x-data="soalUsPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Soal US</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola bank soal untuk ujian US (Ujian Sekolah)</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Soal</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="mapel_id" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Mapel</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nm_mapel }}</option>
                    @endforeach
                </select>
                <select name="tingkat" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                    <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                    <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                </select>
                <select name="semester" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Pertanyaan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Mapel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tingkat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($soals as $index => $soal)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $soal->no_soal }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-white line-clamp-2">{{ Str::limit($soal->pertanyaan, 100) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-lg {{ $soal->tipe_soal == 'pilihan_ganda' ? 'bg-purple-500/20 text-purple-300' : 'bg-amber-500/20 text-amber-300' }}">
                                        {{ $soal->tipe_soal == 'pilihan_ganda' ? 'PG' : 'Essay' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $soal->mapel->nm_mapel ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $soal->tingkat ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="openEditModal({{ json_encode($soal) }})" title="Edit"
                                            class="p-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/40 rounded-lg transition-colors cursor-pointer border border-blue-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.soal.soal-us.destroy', $soal->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
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
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada soal US</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($soals->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">{{ $soals->links() }}</div>
            @endif
        </div>

        <!-- Add Modal (Same structure as Soal MID) -->
        <div x-show="showAddModal" x-transition
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showAddModal = false" style="display: none;">
            <div
                class="w-full max-w-3xl bg-gradient-to-br from-slate-900/95 to-slate-800/95 border border-slate-700/50 rounded-2xl shadow-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Tambah Soal US</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.soal.soal-us.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mapel <span
                                    class="text-red-400">*</span></label>
                            <select name="mapel_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nm_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas <span
                                    class="text-red-400">*</span></label>
                            <select name="tingkat" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih</option>
                                <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                    <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                    <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">No. Soal <span
                                    class="text-red-400">*</span></label>
                            <input type="number" name="no_soal" required min="1"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Semester</label>
                            <select name="semester" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">TP</label>
                            <select name="tp_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                @foreach($tahunPelajarans as $tp)
                                    <option value="{{ $tp->id }}" {{ ($activeTp && $activeTp->id == $tp->id) ? 'selected' : '' }}>
                                        {{ $tp->nm_tp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tipe Soal</label>
                            <select name="tipe_soal" x-model="tipeSoal"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pertanyaan <span
                                class="text-red-400">*</span></label>
                        <textarea name="pertanyaan" required rows="3"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                    </div>
                    <div x-show="tipeSoal === 'pilihan_ganda'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="opsi_a" placeholder="Opsi A"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <input type="text" name="opsi_b" placeholder="Opsi B"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <input type="text" name="opsi_c" placeholder="Opsi C"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <input type="text" name="opsi_d" placeholder="Opsi D"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <input type="text" name="opsi_e" placeholder="Opsi E"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                        <select name="jawaban_benar"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                            <option value="">Jawaban Benar</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 text-slate-400 hover:text-white">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-transition
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showEditModal = false" style="display: none;">
            <div
                class="w-full max-w-3xl bg-gradient-to-br from-slate-900/95 to-slate-800/95 border border-slate-700/50 rounded-2xl shadow-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Edit Soal US</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white p-1"><svg class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <form :action="`{{ url('admin/soal/soal-us') }}/${editSoal.id}`" method="POST" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div><label class="block text-sm font-medium text-slate-300 mb-2">Pertanyaan</label><textarea
                            name="pertanyaan" x-model="editSoal.pertanyaan" required rows="3"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 text-slate-400 hover:text-white">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function soalUsPage() {
            return {
                showAddModal: false, showEditModal: false, tipeSoal: 'pilihan_ganda', editSoal: {},
                init() { this.$watch('showAddModal', v => document.body.classList.toggle('overflow-hidden', v)); this.$watch('showEditModal', v => document.body.classList.toggle('overflow-hidden', v)); },
                openEditModal(soal) { this.editSoal = { ...soal }; this.showEditModal = true; }
            }
        }
    </script>
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>{{ session('success') }}</div>@endif
@endsection


