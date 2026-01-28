@extends('layouts.admin')

@section('title', 'Surat Izin')

@section('page-title', 'Surat Izin')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Daftar Surat Izin</h2>
                    <p class="text-slate-400 text-sm mt-1">
                        Kelola pengajuan surat izin Anda
                    </p>
                </div>
                <a href="{{ route('siswa.surat-izin.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Izin Baru
                </a>
            </div>

            <!-- Filters -->
            <form action="{{ route('siswa.surat-izin.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-4 pt-4 mt-4 border-t border-slate-800/50">
                <div class="flex-1">
                    <select name="jenis_izin"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm"
                        onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="sakit" {{ request('jenis_izin') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ request('jenis_izin') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="lainnya" {{ request('jenis_izin') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="flex-1">
                    <select name="status"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all text-sm"
                        onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- List -->
        <div class="space-y-4">
            @forelse($suratIzins as $suratIzin)
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                    @if($suratIzin->jenis_izin == 'sakit') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                                    @elseif($suratIzin->jenis_izin == 'izin') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                                    @else bg-slate-500/10 text-slate-400 border border-slate-500/20 @endif">
                                    {{ $suratIzin->jenis_izin_label }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                    @if($suratIzin->status == 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                                    @elseif($suratIzin->status == 'disetujui') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                                    @else bg-rose-500/10 text-rose-400 border border-rose-500/20 @endif">
                                    {{ $suratIzin->status_label }}
                                </span>
                            </div>
                            <h3 class="text-white font-medium">
                                {{ $suratIzin->tanggal_mulai->format('d M Y') }}
                                @if($suratIzin->tanggal_mulai != $suratIzin->tanggal_selesai)
                                    - {{ $suratIzin->tanggal_selesai->format('d M Y') }}
                                @endif
                                <span class="text-slate-400 text-sm">({{ $suratIzin->jumlah_hari }} hari)</span>
                            </h3>
                            <p class="text-slate-400 text-sm mt-1 line-clamp-2">{{ $suratIzin->keterangan }}</p>
                            @if($suratIzin->file_path)
                                <div class="flex items-center gap-2 mt-2">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <a href="{{ route('siswa.surat-izin.download', $suratIzin->id) }}" target="_blank"
                                        class="text-blue-400 text-sm hover:underline">Lihat File</a>
                                </div>
                            @endif
                            @if($suratIzin->catatan_guru)
                                <div class="mt-2 p-2 bg-slate-800/50 rounded-lg">
                                    <p class="text-xs text-slate-400">Catatan: {{ $suratIzin->catatan_guru }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex sm:flex-col gap-2">
                            <a href="{{ route('siswa.surat-izin.show', $suratIzin->id) }}"
                                class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-slate-700/50 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </a>
                            @if($suratIzin->status == 'pending')
                                <a href="{{ route('siswa.surat-izin.edit', $suratIzin->id) }}"
                                    class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 text-xs font-medium rounded-lg border border-amber-500/30 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('siswa.surat-izin.destroy', $suratIzin->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-medium rounded-lg border border-rose-500/30 transition-colors w-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-1">Belum Ada Surat Izin</h3>
                    <p class="text-slate-400 text-sm mb-4">Anda belum pernah mengajukan surat izin.</p>
                    <a href="{{ route('siswa.surat-izin.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Izin Baru
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($suratIzins->hasPages())
            <div class="mt-6">
                {{ $suratIzins->links() }}
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection