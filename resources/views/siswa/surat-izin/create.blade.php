@extends('layouts.admin')

@section('title', 'Ajukan Surat Izin')

@section('page-title', 'Ajukan Surat Izin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <a href="{{ route('siswa.surat-izin.index') }}"
                        class="p-2 hover:bg-slate-700 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-lg font-bold text-white">Ajukan Surat Izin</h2>
                        <p class="text-slate-400 text-sm">Isi form di bawah untuk mengajukan izin</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('siswa.surat-izin.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-5">
                @csrf

                <!-- Jenis Izin -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Izin <span
                            class="text-rose-400">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        <label
                            class="flex items-center justify-center px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl cursor-pointer transition-all hover:border-blue-500/50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                            <input type="radio" name="jenis_izin" value="sakit" class="sr-only"
                                {{ old('jenis_izin') == 'sakit' ? 'checked' : '' }}>
                            <span class="text-slate-300 text-sm font-medium">🤒 Sakit</span>
                        </label>
                        <label
                            class="flex items-center justify-center px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl cursor-pointer transition-all hover:border-blue-500/50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                            <input type="radio" name="jenis_izin" value="izin" class="sr-only"
                                {{ old('jenis_izin', 'izin') == 'izin' ? 'checked' : '' }}>
                            <span class="text-slate-300 text-sm font-medium">📋 Izin</span>
                        </label>
                        <label
                            class="flex items-center justify-center px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl cursor-pointer transition-all hover:border-blue-500/50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                            <input type="radio" name="jenis_izin" value="lainnya" class="sr-only"
                                {{ old('jenis_izin') == 'lainnya' ? 'checked' : '' }}>
                            <span class="text-slate-300 text-sm font-medium">📝 Lainnya</span>
                        </label>
                    </div>
                    @error('jenis_izin')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Mulai <span
                                class="text-rose-400">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                        @error('tanggal_mulai')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Selesai <span
                                class="text-rose-400">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm">
                        @error('tanggal_selesai')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan <span
                            class="text-rose-400">*</span></label>
                    <textarea name="keterangan" rows="4" placeholder="Jelaskan alasan izin Anda..."
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm resize-none">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Lampiran (PDF)</label>
                    <div class="relative">
                        <input type="file" name="file" accept=".pdf" id="file-input" class="hidden"
                            onchange="updateFileName(this)">
                        <label for="file-input"
                            class="flex items-center justify-center gap-3 px-4 py-4 bg-slate-800/50 border-2 border-dashed border-slate-700/50 rounded-xl cursor-pointer hover:border-blue-500/50 transition-all">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <div class="text-center">
                                <p class="text-slate-300 text-sm font-medium" id="file-name">Klik untuk upload file PDF</p>
                                <p class="text-slate-500 text-xs">Maksimal 2MB</p>
                            </div>
                        </label>
                    </div>
                    @error('file')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info PKL -->
                @if($pkl)
                    <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-blue-300 text-sm font-medium">Surat izin akan terkait dengan PKL Anda</p>
                                <p class="text-blue-400/70 text-xs mt-1">DUDI: {{ $pkl->dudi->nama ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                    <a href="{{ route('siswa.surat-izin.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Klik untuk upload file PDF';
            document.getElementById('file-name').textContent = fileName;
        }
    </script>
@endsection
