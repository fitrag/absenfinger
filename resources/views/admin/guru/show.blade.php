@extends('layouts.admin')

@section('title', 'Detail Guru')
@section('page-title', 'Detail Guru')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6" x-data="{ activeTab: 'dasar' }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Informasi lengkap data guru</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.guru.edit', $guru) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-xl text-sm font-medium hover:bg-amber-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Data Dasar
                </a>
                <a href="{{ route('admin.guru.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-medium hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Guru Profile Card -->
        <div class="rounded-2xl bg-gradient-to-br from-slate-900/80 to-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-emerald-500/10 to-teal-500/10">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-emerald-500/30">
                        {{ strtoupper(substr($guru->nama, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white">{{ $guru->detail?->gelar ? $guru->detail->gelar . ' ' : '' }}{{ $guru->nama }}</h3>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            @if($guru->nip)<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/50 text-sm text-slate-300 border border-slate-700/50"><span class="text-slate-500">NIP:</span> {{ $guru->nip }}</span>@endif
                            @if($guru->nuptk)<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/50 text-sm text-slate-300 border border-slate-700/50"><span class="text-slate-500">NUPTK:</span> {{ $guru->nuptk }}</span>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-2">
            <div class="flex flex-wrap gap-1">
                @php
                    $tabs = [
                        'dasar' => 'Data Dasar', 'sertifikasi' => 'Sertifikasi', 'pendidikan' => 'Pendidikan',
                        'kompetensi' => 'Kompetensi', 'anak' => 'Anak', 'beasiswa' => 'Beasiswa', 'buku' => 'Buku',
                        'diklat' => 'Diklat', 'karya' => 'Karya Tulis', 'kesejahteraan' => 'Kesejahteraan',
                        'tunjangan' => 'Tunjangan', 'tugas' => 'Tugas Tambahan', 'inpasing' => 'Inpasing',
                        'gaji' => 'Gaji Berkala', 'karir' => 'Karir', 'jabatan' => 'Jabatan', 'pangkat' => 'Pangkat',
                        'jab_fungsional' => 'Jab. Fungsional',
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                <button @click="activeTab = '{{ $key }}'" :class="activeTab === '{{ $key }}' ? 'bg-blue-500 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors cursor-pointer">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <!-- Data Dasar -->
            <div x-show="activeTab === 'dasar'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Data Dasar & Identitas</h3></div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div><p class="text-xs text-slate-500">Jenis Kelamin</p><p class="text-sm text-white font-medium">{{ $guru->jen_kel == 'L' ? 'Laki-laki' : ($guru->jen_kel == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div><p class="text-xs text-slate-500">Tempat/Tgl Lahir</p><p class="text-sm text-white font-medium">{{ $guru->tmpt_lhr ?? '-' }}, {{ $guru->tgl_lhr ? \Carbon\Carbon::parse($guru->tgl_lhr)->format('d/m/Y') : '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">NIK</p><p class="text-sm text-white font-medium">{{ $guru->detail?->nik ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Agama</p><p class="text-sm text-white font-medium">{{ $guru->detail?->agama ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Status Pegawai</p><p class="text-sm text-white font-medium">{{ $guru->detail?->status_pegawai ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Jenis PTK</p><p class="text-sm text-white font-medium">{{ $guru->detail?->jenis_ptk ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">No. Telepon</p><p class="text-sm text-white font-medium">{{ $guru->no_tlp ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Email</p><p class="text-sm text-white font-medium">{{ $guru->detail?->email ?? '-' }}</p></div>
                </div>
            </div>

            <!-- Sertifikasi -->
            <div x-show="activeTab === 'sertifikasi'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Sertifikasi</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenis', 'No.', 'Tahun', 'Bidang Studi', 'NRG', 'No. Peserta'],
                    'data' => $guru->sertifikasis,
                    'fields' => ['jenis_serti', 'no', 'thn', 'bidang_studi', 'nrg', 'no_pes'],
                    'type' => 'sertifikasi', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenis_serti', 'label' => 'Jenis Sertifikasi'],
                        ['name' => 'no', 'label' => 'Nomor'],
                        ['name' => 'thn', 'label' => 'Tahun'],
                        ['name' => 'bidang_studi', 'label' => 'Bidang Studi'],
                        ['name' => 'nrg', 'label' => 'NRG'],
                        ['name' => 'no_pes', 'label' => 'No. Peserta'],
                    ]
                ])
            </div>

            <!-- Pendidikan -->
            <div x-show="activeTab === 'pendidikan'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Pendidikan Formal</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenjang', 'Gelar', 'Satuan Pendidikan', 'Bidang Studi', 'Thn Masuk', 'IPK'],
                    'data' => $guru->pendidikans,
                    'fields' => ['jenjang_pendidikan', 'gelar', 'satuan_pendidikan', 'bidang_studi', 'thn_masuk', 'ipk'],
                    'type' => 'pendidikan', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenjang_pendidikan', 'label' => 'Jenjang', 'type' => 'select', 'options' => ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']],
                        ['name' => 'gelar', 'label' => 'Gelar'],
                        ['name' => 'satuan_pendidikan', 'label' => 'Satuan Pendidikan'],
                        ['name' => 'bidang_studi', 'label' => 'Bidang Studi'],
                        ['name' => 'thn_masuk', 'label' => 'Tahun Masuk'],
                        ['name' => 'nim', 'label' => 'NIM'],
                        ['name' => 'ipk', 'label' => 'IPK', 'type' => 'number'],
                    ]
                ])
            </div>

            <!-- Kompetensi -->
            <div x-show="activeTab === 'kompetensi'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Kompetensi</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Bidang Studi', 'Urutan'],
                    'data' => $guru->kompetensis, 'fields' => ['bidang_studi', 'urutan'],
                    'type' => 'kompetensi', 'guruId' => $guru->id,
                    'formFields' => [['name' => 'bidang_studi', 'label' => 'Bidang Studi'], ['name' => 'urutan', 'label' => 'Urutan', 'type' => 'number']]
                ])
            </div>

            <!-- Anak -->
            <div x-show="activeTab === 'anak'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Data Anak</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Nama', 'Status', 'Jenjang', 'NISN', 'JK', 'Tgl Lahir'],
                    'data' => $guru->anaks, 'fields' => ['nama', 'status', 'jenjang', 'nisn', 'jk', 'tgl_lhr'],
                    'type' => 'anak', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'nama', 'label' => 'Nama'],
                        ['name' => 'status', 'label' => 'Status'],
                        ['name' => 'jenjang', 'label' => 'Jenjang'],
                        ['name' => 'nisn', 'label' => 'NISN'],
                        ['name' => 'jk', 'label' => 'JK', 'type' => 'select', 'options' => ['L', 'P']],
                        ['name' => 'tmpt_lhr', 'label' => 'Tempat Lahir'],
                        ['name' => 'tgl_lhr', 'label' => 'Tanggal Lahir', 'type' => 'date'],
                        ['name' => 'thn_masuk', 'label' => 'Tahun Masuk'],
                    ]
                ])
            </div>

            <!-- Beasiswa -->
            <div x-show="activeTab === 'beasiswa'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Beasiswa</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenis', 'Keterangan', 'Thn Mulai', 'Thn Akhir', 'Masih Menerima'],
                    'data' => $guru->beasiswas, 'fields' => ['jenis', 'ket', 'thn_mulai', 'thn_akhir', 'masih_menerima'],
                    'type' => 'beasiswa', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenis', 'label' => 'Jenis'],
                        ['name' => 'ket', 'label' => 'Keterangan', 'type' => 'textarea'],
                        ['name' => 'thn_mulai', 'label' => 'Tahun Mulai'],
                        ['name' => 'thn_akhir', 'label' => 'Tahun Akhir'],
                        ['name' => 'masih_menerima', 'label' => 'Masih Menerima', 'type' => 'select', 'options' => ['0', '1']],
                    ]
                ])
            </div>

            <!-- Buku -->
            <div x-show="activeTab === 'buku'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Buku yang Pernah Ditulis</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Judul', 'Tahun', 'Penerbit', 'ISBN'],
                    'data' => $guru->bukus, 'fields' => ['judul', 'thn', 'penerbit', 'isbn'],
                    'type' => 'buku', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'judul', 'label' => 'Judul'],
                        ['name' => 'thn', 'label' => 'Tahun'],
                        ['name' => 'penerbit', 'label' => 'Penerbit'],
                        ['name' => 'isbn', 'label' => 'ISBN'],
                    ]
                ])
            </div>

            <!-- Diklat -->
            <div x-show="activeTab === 'diklat'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Diklat</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenis', 'Nama', 'Penyelenggara', 'Tahun', 'Peran', 'Jam'],
                    'data' => $guru->diklats, 'fields' => ['jns_diklat', 'nama', 'penyelenggara', 'thn', 'peran', 'brp_jam'],
                    'type' => 'diklat', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jns_diklat', 'label' => 'Jenis Diklat'],
                        ['name' => 'nama', 'label' => 'Nama Diklat'],
                        ['name' => 'penyelenggara', 'label' => 'Penyelenggara'],
                        ['name' => 'thn', 'label' => 'Tahun'],
                        ['name' => 'peran', 'label' => 'Peran'],
                        ['name' => 'tingkat', 'label' => 'Tingkat'],
                        ['name' => 'brp_jam', 'label' => 'Jumlah Jam', 'type' => 'number'],
                        ['name' => 'sertifikat_diklat', 'label' => 'No. Sertifikat'],
                    ]
                ])
            </div>

            <!-- Karya Tulis -->
            <div x-show="activeTab === 'karya'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Karya Tulis</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Judul', 'Tahun', 'Publikasi', 'Keterangan'],
                    'data' => $guru->karyaTuliss, 'fields' => ['judul', 'thn_pembuatan', 'publikasi', 'ket'],
                    'type' => 'karya', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'judul', 'label' => 'Judul'],
                        ['name' => 'thn_pembuatan', 'label' => 'Tahun Pembuatan'],
                        ['name' => 'publikasi', 'label' => 'Publikasi'],
                        ['name' => 'ket', 'label' => 'Keterangan', 'type' => 'textarea'],
                        ['name' => 'url_publikasi', 'label' => 'URL Publikasi'],
                    ]
                ])
            </div>

            <!-- Kesejahteraan -->
            <div x-show="activeTab === 'kesejahteraan'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Kesejahteraan</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenis', 'Nama', 'Penyelenggara', 'Dari Th', 'Sampai Th', 'Status'],
                    'data' => $guru->kesejahteraans, 'fields' => ['jenis', 'nama', 'penyelenggara', 'dari_th', 'sampai_th', 'status'],
                    'type' => 'kesejahteraan', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenis', 'label' => 'Jenis'],
                        ['name' => 'nama', 'label' => 'Nama'],
                        ['name' => 'penyelenggara', 'label' => 'Penyelenggara'],
                        ['name' => 'dari_th', 'label' => 'Dari Tahun'],
                        ['name' => 'sampai_th', 'label' => 'Sampai Tahun'],
                        ['name' => 'status', 'label' => 'Status'],
                    ]
                ])
            </div>

            <!-- Tunjangan -->
            <div x-show="activeTab === 'tunjangan'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Tunjangan</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenis', 'Nama', 'Instansi', 'SK', 'Nominal', 'Status'],
                    'data' => $guru->tunjangans, 'fields' => ['jenis', 'nama', 'instansi', 'sk_tunjangan', 'nominal', 'status'],
                    'type' => 'tunjangan', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenis', 'label' => 'Jenis'],
                        ['name' => 'nama', 'label' => 'Nama'],
                        ['name' => 'instansi', 'label' => 'Instansi'],
                        ['name' => 'sk_tunjangan', 'label' => 'SK Tunjangan'],
                        ['name' => 'tgl_sk', 'label' => 'Tanggal SK', 'type' => 'date'],
                        ['name' => 'semester', 'label' => 'Semester'],
                        ['name' => 'sumber_dana', 'label' => 'Sumber Dana'],
                        ['name' => 'dari_th', 'label' => 'Dari Tahun'],
                        ['name' => 'sampai_th', 'label' => 'Sampai Tahun'],
                        ['name' => 'nominal', 'label' => 'Nominal', 'type' => 'number'],
                        ['name' => 'status', 'label' => 'Status'],
                    ]
                ])
            </div>

            <!-- Tugas Tambahan -->
            <div x-show="activeTab === 'tugas'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Tugas Tambahan</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jabatan', 'No. SK', 'TMT Tugas', 'TST Tugas'],
                    'data' => $guru->tugasTambahans, 'fields' => ['jabatan', 'no_sk', 'tmt_tugas', 'tst_tugas'],
                    'type' => 'tugas', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jabatan', 'label' => 'Jabatan'],
                        ['name' => 'no_sk', 'label' => 'No. SK'],
                        ['name' => 'tmt_tugas', 'label' => 'TMT Tugas', 'type' => 'date'],
                        ['name' => 'tst_tugas', 'label' => 'TST Tugas', 'type' => 'date'],
                    ]
                ])
            </div>

            <!-- Inpasing -->
            <div x-show="activeTab === 'inpasing'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Inpasing Non PNS</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Pangkat/Gol', 'No. SK', 'Tgl SK', 'TMT SK', 'Angka Kredit', 'Masa Kerja'],
                    'data' => $guru->inpasings, 'fields' => ['pangkat_gol', 'no_sk_inpasing', 'tgl_sk', 'tmt_sk', 'angka_kredit', 'masa_kerja_thn'],
                    'type' => 'inpasing', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'pangkat_gol', 'label' => 'Pangkat/Golongan'],
                        ['name' => 'no_sk_inpasing', 'label' => 'No. SK Inpasing'],
                        ['name' => 'tgl_sk', 'label' => 'Tanggal SK', 'type' => 'date'],
                        ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date'],
                        ['name' => 'angka_kredit', 'label' => 'Angka Kredit', 'type' => 'number'],
                        ['name' => 'masa_kerja_thn', 'label' => 'Masa Kerja (Tahun)', 'type' => 'number'],
                        ['name' => 'masa_kerja_bln', 'label' => 'Masa Kerja (Bulan)', 'type' => 'number'],
                    ]
                ])
            </div>

            <!-- Gaji Berkala -->
            <div x-show="activeTab === 'gaji'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Gaji Berkala</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Pangkat/Gol', 'No. SK', 'Tgl SK', 'TMT SK', 'Masa Kerja', 'Gapok'],
                    'data' => $guru->gajiBerkalas, 'fields' => ['pangkat_gol', 'nomor_sk', 'tanggal_sk', 'tmt_sk', 'masa_kerja', 'gapok'],
                    'type' => 'gaji', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'pangkat_gol', 'label' => 'Pangkat/Golongan'],
                        ['name' => 'nomor_sk', 'label' => 'Nomor SK'],
                        ['name' => 'tanggal_sk', 'label' => 'Tanggal SK', 'type' => 'date'],
                        ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date'],
                        ['name' => 'masa_kerja', 'label' => 'Masa Kerja'],
                        ['name' => 'gapok', 'label' => 'Gaji Pokok', 'type' => 'number'],
                    ]
                ])
            </div>

            <!-- Karir -->
            <div x-show="activeTab === 'karir'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Karir Guru</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jenjang', 'Jenis Lembaga', 'Jns PTK', 'No. SK', 'TMT Kerja', 'Mapel'],
                    'data' => $guru->karirGurus, 'fields' => ['jenjang_pendidikan', 'jenis_lembaga', 'jns_ptk', 'no_sk_kerja', 'tmt_kerja', 'mapel'],
                    'type' => 'karir', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jenjang_pendidikan', 'label' => 'Jenjang Pendidikan'],
                        ['name' => 'jenis_lembaga', 'label' => 'Jenis Lembaga'],
                        ['name' => 'satuan_pegawai', 'label' => 'Satuan Pegawai'],
                        ['name' => 'jns_ptk', 'label' => 'Jenis PTK'],
                        ['name' => 'lembaga_pengangkat', 'label' => 'Lembaga Pengangkat'],
                        ['name' => 'no_sk_kerja', 'label' => 'No. SK Kerja'],
                        ['name' => 'tgl_sk_kerja', 'label' => 'Tgl SK Kerja', 'type' => 'date'],
                        ['name' => 'tmt_kerja', 'label' => 'TMT Kerja', 'type' => 'date'],
                        ['name' => 'tst_kerja', 'label' => 'TST Kerja', 'type' => 'date'],
                        ['name' => 'mapel', 'label' => 'Mata Pelajaran'],
                    ]
                ])
            </div>

            <!-- Jabatan -->
            <div x-show="activeTab === 'jabatan'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Jabatan</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jabatan PTK', 'SK Jabatan', 'TMT SK'],
                    'data' => $guru->jabatans, 'fields' => ['jabatan_ptk', 'sk_jabatan', 'tmt_sk'],
                    'type' => 'jabatan', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jabatan_ptk', 'label' => 'Jabatan PTK'],
                        ['name' => 'sk_jabatan', 'label' => 'SK Jabatan'],
                        ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date'],
                    ]
                ])
            </div>

            <!-- Pangkat -->
            <div x-show="activeTab === 'pangkat'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Pangkat Golongan</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Pangkat/Gol', 'No. SK', 'Tgl Pangkat', 'TMT Pangkat', 'Masa Kerja Th', 'Masa Kerja Bln'],
                    'data' => $guru->pangkatGols, 'fields' => ['pangkat_gol', 'no_sk', 'tgl_pangkat', 'tmt_pangkat', 'masa_kerja_tahun', 'masa_kerja_bln'],
                    'type' => 'pangkat', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'pangkat_gol', 'label' => 'Pangkat/Golongan'],
                        ['name' => 'no_sk', 'label' => 'No. SK'],
                        ['name' => 'tgl_pangkat', 'label' => 'Tanggal Pangkat', 'type' => 'date'],
                        ['name' => 'tmt_pangkat', 'label' => 'TMT Pangkat', 'type' => 'date'],
                        ['name' => 'masa_kerja_tahun', 'label' => 'Masa Kerja (Tahun)', 'type' => 'number'],
                        ['name' => 'masa_kerja_bln', 'label' => 'Masa Kerja (Bulan)', 'type' => 'number'],
                    ]
                ])
            </div>

            <!-- Jabatan Fungsional -->
            <div x-show="activeTab === 'jab_fungsional'" x-cloak>
                <div class="p-4 border-b border-slate-800/50"><h3 class="text-base font-semibold text-white">Riwayat Jabatan Fungsional</h3></div>
                @include('admin.guru.partials.table', [
                    'headers' => ['Jabatan Fungsional', 'SK Jabatan', 'TMT SK'],
                    'data' => $guru->jabatanFungsionals, 'fields' => ['jabatan_fungsional', 'sk_jabatan', 'tmt_sk'],
                    'type' => 'jab_fungsional', 'guruId' => $guru->id,
                    'formFields' => [
                        ['name' => 'jabatan_fungsional', 'label' => 'Jabatan Fungsional'],
                        ['name' => 'sk_jabatan', 'label' => 'SK Jabatan'],
                        ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date'],
                    ]
                ])
            </div>
        </div>
    </div>
@endsection