@extends('layouts.admin')

@section('title', 'Naik Kelas')
@section('page-title', 'Naik Kelas')

@section('content')
    <div class="space-y-6" x-data="naikKelasPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Naik Kelas</h2>
                <p class="text-sm text-slate-400 mt-1">Pindahkan siswa ke kelas baru</p>
            </div>
            <a href="{{ route('admin.students.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Select Source Class -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <div class="flex flex-wrap items-center gap-4">
                <label class="text-sm font-medium text-slate-300">Pilih Kelas Asal:</label>
                <select x-model="selectedKelas" @change="loadStudents()"
                    class="px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm min-w-[200px]">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                    @endforeach
                </select>
                <div x-show="students.length > 0" class="text-sm text-slate-400">
                    <span x-text="students.length"></span> siswa ditemukan
                </div>
            </div>
        </div>

        <!-- Students List with Class Selection -->
        <form action="{{ route('admin.students.naikKelas.process') }}" method="POST">
            @csrf
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-slate-700/50 bg-slate-800/30 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-white">Daftar Siswa</h3>
                    <div x-show="students.length > 0" class="flex items-center gap-3">
                        <label class="text-xs text-slate-400">Set Semua ke:</label>
                        <select x-model="bulkKelas" @change="setBulkKelas()"
                            class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                            <option value="">-- Pilih --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="isLoading" class="p-8 text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-blue-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-slate-400 text-sm mt-2">Memuat data...</p>
                </div>

                <!-- Empty State -->
                <div x-show="!isLoading && !selectedKelas" class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-slate-400">Pilih kelas asal untuk menampilkan daftar siswa</p>
                </div>

                <!-- No Students -->
                <div x-show="!isLoading && selectedKelas && students.length === 0" class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-slate-400">Tidak ada siswa aktif di kelas ini</p>
                </div>

                <!-- Students Table -->
                <div x-show="!isLoading && students.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-700/50 bg-slate-900/30">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">NIS</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Naik ke Kelas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            <template x-for="(student, index) in students" :key="student.id">
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-sm text-slate-400" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-mono text-slate-300" x-text="student.nis"></span>
                                        <input type="hidden" :name="'students[' + index + '][nis]'" :value="student.nis">
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-white font-medium" x-text="student.name"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select :name="'students[' + index + '][kelas_id]'" x-model="student.newKelasId"
                                            class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm w-full max-w-xs">
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer with Save Button -->
                <div x-show="students.length > 0"
                    class="px-4 py-4 border-t border-slate-700/50 bg-slate-800/30 flex justify-end">
                    <button type="submit" onclick="return confirm('Yakin ingin menyimpan perubahan kelas?')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function naikKelasPage() {
            return {
                selectedKelas: '',
                students: [],
                isLoading: false,
                bulkKelas: '',

                loadStudents() {
                    if (!this.selectedKelas) {
                        this.students = [];
                        return;
                    }

                    this.isLoading = true;
                    this.students = [];
                    this.bulkKelas = '';

                    fetch(`/admin/students/naik-kelas/students/${this.selectedKelas}`)
                        .then(res => res.json())
                        .then(data => {
                            // Add newKelasId property to each student (default to current class)
                            this.students = data.map(s => ({
                                ...s,
                                newKelasId: String(this.selectedKelas)
                            }));
                            this.isLoading = false;
                        })
                        .catch(() => {
                            this.isLoading = false;
                        });
                },

                setBulkKelas() {
                    if (this.bulkKelas) {
                        // Create new array to trigger reactivity
                        this.students = this.students.map(s => ({
                            ...s,
                            newKelasId: String(this.bulkKelas)
                        }));
                    }
                }
            }
        }
    </script>
@endsection