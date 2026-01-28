@extends('layouts.admin')

@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
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

        <!-- Student Detail Sections -->
        @if($student->detail)
            <!-- Data Identitas -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Identitas</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">NIK</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->nik ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. KK</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_kk ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">NPD</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->npd ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. Reg Akta Lahir</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_reg_akta_lhr ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Alamat -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Alamat</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">RT/RW</p>
                        <p class="text-sm text-white font-medium">
                            {{ ($student->detail->rt ?? '-') }}/{{ ($student->detail->rw ?? '-') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Dusun</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->dusun ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Kelurahan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->kelurahan ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Kecamatan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->kecamatan ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Kode Pos</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->kode_pos ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jenis Tinggal</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->jns_tinggal ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Alat Transportasi</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->alt_transp ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jarak Rumah ke Sekolah</p>
                        <p class="text-sm text-white font-medium">
                            {{ $student->detail->jarak_rmh_skul ? $student->detail->jarak_rmh_skul . ' km' : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Kontak -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Kontak</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Telepon</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->telp ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">HP</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->hp ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->e_mail ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Ayah -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Ayah</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Nama</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_nama ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">NIK</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_nik ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Tahun Lahir</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_th_lhr ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jenjang Pendidikan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_jenjang ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Pekerjaan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_pekerjaan ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Penghasilan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ayah_penghasilan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Ibu -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Ibu</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Nama</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_nama ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">NIK</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_nik ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Tahun Lahir</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_th_lahir ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jenjang Pendidikan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_jenjang ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Pekerjaan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_pekerjaan ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Penghasilan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->ibu_penghasilan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Wali -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Wali</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Nama</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_nama ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">NIK</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_nik ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Tahun Lahir</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_th_lahir ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jenjang Pendidikan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_jenjang ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Pekerjaan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_pekerjaan ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Penghasilan</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->wali_penghasilan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Akademik -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Akademik</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Sekolah Asal</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->sekolah_asal ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">SKHUN</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->skhun ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. Peserta Ujian</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_pes_ujian ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. Seri Ijazah</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_seri_ijazah ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Anak Ke</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->anak_ke ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Jumlah Saudara Kandung</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->jml_sdr_kandung ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Kebutuhan Khusus</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->kebutuhan_khusus ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Bantuan -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Bantuan</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Penerima KPS</p>
                        <p class="text-sm font-medium {{ $student->detail->p_kps ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ $student->detail->p_kps ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Penerima KIP</p>
                        <p
                            class="text-sm font-medium {{ $student->detail->penerima_kip ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ $student->detail->penerima_kip ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. KIP</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_kip ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. KKS</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_kks ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Layak PIP</p>
                        <p
                            class="text-sm font-medium {{ $student->detail->layak_pip ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ $student->detail->layak_pip ? 'Ya' : 'Tidak' }}</p>
                    </div>
                    <div class="space-y-1 lg:col-span-2">
                        <p class="text-xs text-slate-500">Alasan Layak PIP</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->alasan_layak_pip ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Bank -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Bank</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Nama Bank</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->bank ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">No. Rekening</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->no_rek ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Atas Nama</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->an_rek ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Fisik & Lokasi -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                <div class="p-4 border-b border-slate-800/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white">Data Fisik & Lokasi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Berat Badan</p>
                        <p class="text-sm text-white font-medium">
                            {{ $student->detail->berat_bdn ? $student->detail->berat_bdn . ' kg' : '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Tinggi Badan</p>
                        <p class="text-sm text-white font-medium">
                            {{ $student->detail->tinggi_bdn ? $student->detail->tinggi_bdn . ' cm' : '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Lingkar Kepala</p>
                        <p class="text-sm text-white font-medium">
                            {{ $student->detail->lingkar_kep ? $student->detail->lingkar_kep . ' cm' : '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Lintang (Latitude)</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->lintang ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-500">Bujur (Longitude)</p>
                        <p class="text-sm text-white font-medium">{{ $student->detail->bujur ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @else
            <!-- No Detail Data -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-slate-400 mb-4">Belum ada data detail siswa</p>
                <a href="{{ route('admin.students.edit', $student) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-xl text-sm font-medium hover:bg-blue-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Data Detail
                </a>
            </div>
        @endif

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