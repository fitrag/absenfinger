@extends('layouts.admin')

@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Informasi lengkap data siswa</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-xl text-sm font-medium hover:bg-amber-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.students.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-medium hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Student Profile Card -->
        <div
            class="rounded-2xl bg-gradient-to-br from-slate-900/80 to-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 bg-gradient-to-r from-blue-500/10 to-purple-500/10">
                <div class="flex items-center gap-5">
                    <!-- Avatar -->
                    <div
                        class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-500/30">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white">{{ $student->name }}</h3>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/50 text-sm text-slate-300 border border-slate-700/50">
                                <span class="text-slate-500">NIS:</span> {{ $student->nis }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/50 text-sm text-slate-300 border border-slate-700/50">
                                <span class="text-slate-500">NISN:</span> {{ $student->nisn ?? '-' }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/50 text-sm text-slate-300 border border-slate-700/50">
                                <span class="text-slate-500">Finger:</span> {{ $student->finger_id }}
                            </span>
                            <span
                                class="inline-flex px-2.5 py-1 rounded-lg text-sm font-medium {{ $student->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Kelas -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Kelas</p>
                        <p class="text-white font-medium">{{ $student->kelas->nm_kls ?? '-' }}</p>
                    </div>

                    <!-- Jurusan -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Jurusan</p>
                        <p class="text-white font-medium">{{ $student->jurusan->paket_keahlian ?? '-' }}</p>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Jenis Kelamin</p>
                        <p class="text-white font-medium">
                            @if($student->jen_kel == 'L')
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-400"></span> Laki-laki
                                </span>
                            @elseif($student->jen_kel == 'P')
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-pink-400"></span> Perempuan
                                </span>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tempat Lahir</p>
                        <p class="text-white font-medium">{{ $student->tmpt_lhr ?? '-' }}</p>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tanggal Lahir</p>
                        <p class="text-white font-medium">{{ $student->tgl_lhr ? $student->tgl_lhr->format('d F Y') : '-' }}
                        </p>
                    </div>

                    <!-- Agama -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Agama</p>
                        <p class="text-white font-medium">{{ $student->agama ?? '-' }}</p>
                    </div>

                    <!-- No Telepon -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">No. Telepon</p>
                        <p class="text-white font-medium">{{ $student->no_tlp ?? '-' }}</p>
                    </div>

                    <!-- Nama Ayah -->
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Nama Ayah</p>
                        <p class="text-white font-medium">{{ $student->nm_ayah ?? '-' }}</p>
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-1 md:col-span-2 lg:col-span-1">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Alamat</p>
                        <p class="text-white font-medium">{{ $student->almt_siswa ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-5 border-b border-slate-800/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Riwayat Kehadiran Terbaru</h3>
                <span class="text-sm text-slate-400">20 data terakhir</span>
            </div>

            <div class="divide-y divide-slate-800/50">
                @forelse($student->attendances as $attendance)
                    <div class="flex items-center gap-4 p-4 hover:bg-slate-800/30 transition-colors">
                        <div
                            class="w-10 h-10 rounded-lg {{ $attendance->checktype == 0 ? 'bg-emerald-500/20' : 'bg-amber-500/20' }} flex items-center justify-center">
                            @if($attendance->checktype == 0)
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white">{{ $attendance->checktime->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $attendance->checktime->format('H:i:s') }}</p>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $attendance->checktype == 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                            {{ $attendance->checktype == 0 ? 'Masuk' : 'Pulang' }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-slate-400">Belum ada riwayat kehadiran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection