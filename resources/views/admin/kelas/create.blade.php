@extends('layouts.admin')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kelas.index') }}"
                class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-white">Tambah Kelas Baru</h2>
                <p class="text-sm text-slate-400 mt-1">Isi form berikut untuk menambahkan kelas baru</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.kelas.store') }}" method="POST"
            class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6 space-y-6">
            @csrf

            <!-- Nama Kelas -->
            <div>
                <label for="nm_kls" class="block text-sm font-medium text-slate-300 mb-2">
                    Nama Kelas <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="nm_kls" name="nm_kls" value="{{ old('nm_kls') }}" maxlength="15"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 @error('nm_kls') border-rose-500/50 @enderror"
                    placeholder="Contoh: X IPA 1">
                @error('nm_kls')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Maksimal 15 karakter</p>
            </div>

            <!-- Alias -->
            <div>
                <label for="alias" class="block text-sm font-medium text-slate-300 mb-2">
                    Alias <span class="text-rose-400">*</span>
                </label>
                <input type="text" id="alias" name="alias" value="{{ old('alias') }}" maxlength="6"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 @error('alias') border-rose-500/50 @enderror"
                    placeholder="Contoh: XIA1">
                @error('alias')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Maksimal 6 karakter</p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                    Simpan Kelas
                </button>
                <a href="{{ route('admin.kelas.index') }}"
                    class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection