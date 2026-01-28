@extends('layouts.admin')

@section('title', 'File Soal')

@section('content')
    <div class="space-y-6" x-data="fileSoalPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">File Soal</h1>
                <p class="text-slate-400 text-sm mt-1">Upload dan kelola file soal ujian</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Upload File Soal</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="jenis" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Jenis</option>
                    <option value="MID" {{ request('jenis') == 'MID' ? 'selected' : '' }}>MID</option>
                    <option value="US" {{ request('jenis') == 'US' ? 'selected' : '' }}>US</option>
                </select>
                <select name="mapel_id" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Mapel</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nm_mapel }}
                        </option>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nama File</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase"></th>Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Mapel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tingkat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Semester</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($fileSoals as $index => $file)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $fileSoals->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-white">{{ $file->nama_file }}</p>
                                    <p class="text-xs text-slate-400">{{ $file->keterangan ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                        $typeColors = [
                                            'pdf' => 'bg-red-500/20 text-red-300',
                                            'doc' => 'bg-blue-500/20 text-blue-300',
                                            'docx' => 'bg-blue-500/20 text-blue-300',
                                            'xls' => 'bg-green-500/20 text-green-300',
                                            'xlsx' => 'bg-green-500/20 text-green-300',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-lg {{ $typeColors[$ext] ?? 'bg-slate-500/20 text-slate-300' }}">
                                        {{ strtoupper($ext) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    @php
                                        $fullPath = storage_path('app/public/' . $file->file_path);
                                        $size = file_exists($fullPath) ? filesize($fullPath) : 0;
                                        if ($size >= 1048576) {
                                            $sizeFormatted = number_format($size / 1048576, 2) . ' MB';
                                        } else {
                                            $sizeFormatted = number_format($size / 1024, 1) . ' KB';
                                        }
                                    @endphp
                                    {{ $sizeFormatted }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-lg {{ $file->jenis == 'MID' ? 'bg-blue-500/20 text-blue-300' : 'bg-purple-500/20 text-purple-300' }}">
                                        {{ $file->jenis }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $file->mapel->nm_mapel ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $file->tingkat }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $file->semester }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.soal.file-soal.download', $file->id) }}" title="Download"
                                            class="p-1.5 bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/40 rounded-lg transition-colors cursor-pointer border border-emerald-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.soal.file-soal.destroy', $file->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
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
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada file soal</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($fileSoals->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $fileSoals->links() }}
                </div>
            @endif
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showAddModal = false" style="display: none;">

            <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-2xl bg-gradient-to-br from-slate-900/95 to-slate-800/95 border border-slate-700/50 rounded-2xl shadow-2xl">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Upload File Soal</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.soal.file-soal.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama File <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama_file" required placeholder="Contoh: Soal UTS Matematika Kelas X"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">File <span
                                    class="text-red-400">*</span></label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500/20 file:text-blue-300 hover:file:bg-blue-500/30">
                            <p class="text-xs text-slate-500 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX (Max 10MB)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jenis <span
                                    class="text-red-400">*</span></label>
                            <select name="jenis" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Jenis</option>
                                <option value="MID">MID</option>
                                <option value="US">US</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Mapel <span
                                    class="text-red-400">*</span></label>
                            <select name="mapel_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Mapel</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nm_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tingkat <span
                                    class="text-red-400">*</span></label>
                            <select name="tingkat" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Tingkat</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Semester <span
                                    class="text-red-400">*</span></label>
                            <select name="semester" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran <span
                                    class="text-red-400">*</span></label>
                            <select name="tp_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                @foreach($tahunPelajarans as $tp)
                                    <option value="{{ $tp->id }}" {{ ($activeTp && $activeTp->id == $tp->id) ? 'selected' : '' }}>
                                        {{ $tp->nm_tp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan..."
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fileSoalPage() {
            return {
                showAddModal: false,
                init() {
                    this.$watch('showAddModal', value => {
                        document.body.classList.toggle('overflow-hidden', value);
                    });
                }
            }
        }
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection