@extends('layouts.admin')

@section('title', 'Detail Kelas')
@section('page-title', 'Detail Kelas')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.kelas.index') }}"
                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-white">Detail Kelas</h2>
                    <p class="text-sm text-slate-400 mt-1">Informasi lengkap kelas {{ $kelas->nm_kls }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.kelas.edit', $kelas) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 text-amber-400 font-medium rounded-xl hover:bg-amber-500/20 transition-colors border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <!-- Header with Avatar -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($kelas->nm_kls, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $kelas->nm_kls }}</h3>
                        <span class="inline-flex px-2 py-1 mt-2 rounded-lg text-xs font-medium bg-white/20 text-white">
                            {{ $kelas->alias }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/30">
                        <p class="text-xs text-slate-400 mb-1">Nama Kelas</p>
                        <p class="text-lg font-semibold text-white">{{ $kelas->nm_kls }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/30">
                        <p class="text-xs text-slate-400 mb-1">Alias</p>
                        <p class="text-lg font-semibold text-white">{{ $kelas->alias }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/30">
                        <p class="text-xs text-slate-400 mb-1">Dibuat Pada</p>
                        <p class="text-sm font-medium text-white">{{ $kelas->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/30">
                        <p class="text-xs text-slate-400 mb-1">Terakhir Diupdate</p>
                        <p class="text-sm font-medium text-white">{{ $kelas->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 border-t border-slate-800/50 flex items-center justify-between">
                <a href="{{ route('admin.kelas.index') }}"
                    class="text-sm text-slate-400 hover:text-white transition-colors">
                    ← Kembali ke daftar
                </a>
                <form action="{{ route('admin.kelas.destroy', $kelas) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-500/10 text-rose-400 font-medium rounded-xl hover:bg-rose-500/20 transition-colors border border-rose-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Kelas
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection