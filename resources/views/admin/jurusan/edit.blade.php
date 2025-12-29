@extends('layouts.admin')

@section('title', 'Edit Jurusan')
@section('page-title', 'Edit Jurusan')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.jurusan.index') }}"
                class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-white">Edit Jurusan</h2>
                <p class="text-sm text-slate-400 mt-1">Perbarui informasi jurusan</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.jurusan.update', $jurusan) }}" method="POST"
            class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Bidang -->
            <div>
                <label for="bidang" class="block text-sm font-medium text-slate-300 mb-2">
                    Bidang <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="bidang" name="bidang" value="{{ old('bidang', $jurusan->bidang) }}"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 @error('bidang') border-rose-500/50 @enderror"
                    placeholder="Contoh: Teknologi dan Rekayasa">
                @error('bidang')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Program -->
            <div>
                <label for="program" class="block text-sm font-medium text-slate-300 mb-2">
                    Program <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="program" name="program" value="{{ old('program', $jurusan->program) }}"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 @error('program') border-rose-500/50 @enderror"
                    placeholder="Contoh: Teknik Komputer dan Informatika">
                @error('program')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Paket Keahlian -->
            <div>
                <label for="paket_keahlian" class="block text-sm font-medium text-slate-300 mb-2">
                    Paket Keahlian <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="paket_keahlian" name="paket_keahlian"
                    value="{{ old('paket_keahlian', $jurusan->paket_keahlian) }}"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 @error('paket_keahlian') border-rose-500/50 @enderror"
                    placeholder="Contoh: Rekayasa Perangkat Lunak">
                @error('paket_keahlian')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                    Update Jurusan
                </button>
                <a href="{{ route('admin.jurusan.index') }}"
                    class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection