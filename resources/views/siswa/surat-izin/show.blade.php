@extends('layouts.admin')

@section('title', 'Detail Surat Izin')

@section('page-title', 'Detail Surat Izin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-700/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('siswa.surat-izin.index') }}"
                            class="p-2 hover:bg-slate-700 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-lg font-bold text-white">Detail Surat Izin</h2>
                            <p class="text-slate-400 text-sm">Diajukan {{ $suratIzin->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                @if($suratIzin->status == 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                @elseif($suratIzin->status == 'disetujui') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                @else bg-rose-500/10 text-rose-400 border border-rose-500/20 @endif">
                        {{ $suratIzin->status_label }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">
                <!-- Jenis & Tanggal -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Jenis Izin</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium
                                    @if($suratIzin->jenis_izin == 'sakit') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @elseif($suratIzin->jenis_izin == 'izin') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                    @else bg-slate-500/10 text-slate-400 border border-slate-500/20 @endif">
                            {{ $suratIzin->jenis_izin_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Durasi</p>
                        <p class="text-white font-medium">{{ $suratIzin->jumlah_hari }} Hari</p>
                    </div>
                </div>

                <!-- Periode -->
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Periode Izin</p>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-800/50 rounded-lg">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-white text-sm">{{ $suratIzin->tanggal_mulai->format('d M Y') }}</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-800/50 rounded-lg">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-white text-sm">{{ $suratIzin->tanggal_selesai->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Keterangan</p>
                    <p class="text-slate-300 text-sm leading-relaxed">{{ $suratIzin->keterangan }}</p>
                </div>

                <!-- File -->
                @if($suratIzin->file_path)
                    <div>
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Lampiran</p>
                        <a href="{{ route('siswa.surat-izin.download', $suratIzin->id) }}" target="_blank"
                            class="inline-flex items-center gap-3 px-4 py-3 bg-slate-800/50 hover:bg-slate-800 rounded-xl transition-colors">
                            <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="text-white text-sm font-medium">Surat Izin.pdf</p>
                                <p class="text-blue-400 text-xs">Klik untuk preview</p>
                            </div>
                        </a>
                    </div>
                @endif

                <!-- PKL Info -->
                @if($suratIzin->pkl)
                    <div>
                        <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Terkait PKL</p>
                        <div class="flex items-center gap-3 px-4 py-3 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div>
                                <p class="text-white text-sm font-medium">{{ $suratIzin->pkl->dudi->nama ?? '-' }}</p>
                                <p class="text-blue-400/70 text-xs">{{ $suratIzin->pkl->dudi->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Approval Info -->
                @if($suratIzin->status != 'pending')
                    <div class="p-4 rounded-xl
                                        @if($suratIzin->status == 'disetujui') bg-emerald-500/10 border border-emerald-500/20
                                        @else bg-rose-500/10 border border-rose-500/20 @endif">
                        <div class="flex items-start gap-3">
                            @if($suratIzin->status == 'disetujui')
                                <svg class="w-5 h-5 text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                            <div>
                                <p
                                    class="text-sm font-medium {{ $suratIzin->status == 'disetujui' ? 'text-emerald-300' : 'text-rose-300' }}">
                                    {{ $suratIzin->status == 'disetujui' ? 'Disetujui' : 'Ditolak' }} oleh
                                    {{ $suratIzin->approver->nama ?? '-' }}
                                </p>
                                @if($suratIzin->approved_at)
                                    <p
                                        class="text-xs {{ $suratIzin->status == 'disetujui' ? 'text-emerald-400/70' : 'text-rose-400/70' }}">
                                        {{ $suratIzin->approved_at->format('d M Y H:i') }}
                                    </p>
                                @endif
                                @if($suratIzin->catatan_guru)
                                    <p
                                        class="mt-2 text-sm {{ $suratIzin->status == 'disetujui' ? 'text-emerald-300/80' : 'text-rose-300/80' }}">
                                        "{{ $suratIzin->catatan_guru }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-between pt-4 border-t border-slate-700/50">
                    <a href="{{ route('siswa.surat-izin.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </a>
                    @if($suratIzin->status == 'pending')
                        <div class="flex gap-2">
                            <a href="{{ route('siswa.surat-izin.edit', $suratIzin->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 text-sm font-medium rounded-lg border border-amber-500/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-sm font-medium rounded-lg border border-rose-500/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection