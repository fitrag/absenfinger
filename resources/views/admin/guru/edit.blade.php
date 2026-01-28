@extends('layouts.admin')

@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.guru.index') }}"
                        class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Edit Data Guru</h3>
                        <p class="text-sm text-slate-400 mt-1">Perbarui informasi guru lengkap</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.guru.update', $guru) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Data Dasar -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-blue-500/20 flex items-center justify-center"><svg
                                class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg></span>
                        Data Dasar
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Username <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="username" value="{{ old('username', $guru->username) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Gelar</label>
                            <input type="text" name="detail[gelar]"
                                value="{{ old('detail.gelar', $guru->detail->gelar ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="S.Pd, M.Pd">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NUPTK</label>
                            <input type="text" name="nuptk" value="{{ old('nuptk', $guru->nuptk) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK</label>
                            <input type="text" name="detail[nik]" value="{{ old('detail.nik', $guru->detail->nik ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KK</label>
                            <input type="text" name="detail[no_kk]"
                                value="{{ old('detail.no_kk', $guru->detail->no_kk ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tempat Lahir</label>
                            <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $guru->tmpt_lhr) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="tgl_lhr" value="{{ old('tgl_lhr', $guru->tgl_lhr) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin</label>
                            <select name="jen_kel"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('jen_kel', $guru->jen_kel) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('jen_kel', $guru->jen_kel) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                            <select name="detail[agama]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('detail.agama', $guru->detail->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kewarganegaraan</label>
                            <input type="text" name="detail[kewarganegaraan]"
                                value="{{ old('detail.kewarganegaraan', $guru->detail->kewarganegaraan ?? 'Indonesia') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ibu Kandung</label>
                            <input type="text" name="detail[nm_ibu_kandung]"
                                value="{{ old('detail.nm_ibu_kandung', $guru->detail->nm_ibu_kandung ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Alamat -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-emerald-500/20 flex items-center justify-center"><svg
                                class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg></span>
                        Data Alamat
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat Jalan</label>
                            <textarea name="detail[alamat_jln]" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">{{ old('detail.alamat_jln', $guru->detail->alamat_jln ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">RT</label>
                            <input type="text" name="detail[rt]" value="{{ old('detail.rt', $guru->detail->rt ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">RW</label>
                            <input type="text" name="detail[rw]" value="{{ old('detail.rw', $guru->detail->rw ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Dusun</label>
                            <input type="text" name="detail[nama_dusun]"
                                value="{{ old('detail.nama_dusun', $guru->detail->nama_dusun ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelurahan</label>
                            <input type="text" name="detail[kelurahan]"
                                value="{{ old('detail.kelurahan', $guru->detail->kelurahan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kecamatan</label>
                            <input type="text" name="detail[kecamatan]"
                                value="{{ old('detail.kecamatan', $guru->detail->kecamatan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode Pos</label>
                            <input type="text" name="detail[kode_pos]"
                                value="{{ old('detail.kode_pos', $guru->detail->kode_pos ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Lintang</label>
                            <input type="text" name="detail[lintang]"
                                value="{{ old('detail.lintang', $guru->detail->lintang ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Bujur</label>
                            <input type="text" name="detail[bujur]"
                                value="{{ old('detail.bujur', $guru->detail->bujur ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Kontak -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Kontak</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon</label>
                            <input type="text" name="no_tlp" value="{{ old('no_tlp', $guru->no_tlp) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. HP</label>
                            <input type="text" name="detail[no_hp]"
                                value="{{ old('detail.no_hp', $guru->detail->no_hp ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                            <input type="email" name="detail[email]"
                                value="{{ old('detail.email', $guru->detail->email ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Pernikahan -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-pink-500/20 flex items-center justify-center"><svg
                                class="w-3 h-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg></span>
                        Data Pernikahan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Status Perkawinan</label>
                            <select name="detail[status_perkawinan]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                    <option value="{{ $sp }}" {{ old('detail.status_perkawinan', $guru->detail->status_perkawinan ?? '') == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Istri/Suami</label>
                            <input type="text" name="detail[nm_istri_suami]"
                                value="{{ old('detail.nm_istri_suami', $guru->detail->nm_istri_suami ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIP Istri/Suami</label>
                            <input type="text" name="detail[nip_istri_suami]"
                                value="{{ old('detail.nip_istri_suami', $guru->detail->nip_istri_suami ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan Istri/Suami</label>
                            <input type="text" name="detail[pekerjaan_istri_suami]"
                                value="{{ old('detail.pekerjaan_istri_suami', $guru->detail->pekerjaan_istri_suami ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Kepegawaian -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-amber-500/20 flex items-center justify-center"><svg
                                class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg></span>
                        Data Kepegawaian
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Status Pegawai</label>
                            <select name="detail[status_pegawai]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['PNS', 'GTT', 'Honorer', 'PPPK', 'Pegawai Tetap Yayasan', 'Pegawai Tidak Tetap'] as $sp)
                                    <option value="{{ $sp }}" {{ old('detail.status_pegawai', $guru->detail->status_pegawai ?? '') == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIY</label>
                            <input type="text" name="detail[niy]" value="{{ old('detail.niy', $guru->detail->niy ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis PTK</label>
                            <select name="detail[jenis_ptk]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Guru Kelas', 'Guru Mata Pelajaran', 'Guru BK', 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Tenaga Administrasi', 'Tenaga Perpustakaan', 'Tenaga Laboratorium'] as $jptk)
                                    <option value="{{ $jptk }}" {{ old('detail.jenis_ptk', $guru->detail->jenis_ptk ?? '') == $jptk ? 'selected' : '' }}>{{ $jptk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pangkat</label>
                            <input type="text" name="detail[pangkat]"
                                value="{{ old('detail.pangkat', $guru->detail->pangkat ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="III/a, IV/b, dst">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">SK Pengangkatan</label>
                            <input type="text" name="detail[sk_pengangkatan]"
                                value="{{ old('detail.sk_pengangkatan', $guru->detail->sk_pengangkatan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">TMT Pengangkatan</label>
                            <input type="date" name="detail[tmt_pengangkatan]"
                                value="{{ old('detail.tmt_pengangkatan', $guru->detail?->tmt_pengangkatan?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Lembaga Pengangkat</label>
                            <input type="text" name="detail[lembaga_pengangkat]"
                                value="{{ old('detail.lembaga_pengangkat', $guru->detail->lembaga_pengangkat ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Sumber Gaji</label>
                            <select name="detail[sumber_gaji]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['APBN', 'APBD', 'BOS', 'Yayasan', 'Sekolah', 'Lainnya'] as $sg)
                                    <option value="{{ $sg }}" {{ old('detail.sumber_gaji', $guru->detail->sumber_gaji ?? '') == $sg ? 'selected' : '' }}>{{ $sg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">SK CPNS</label>
                            <input type="text" name="detail[sk_cpns]"
                                value="{{ old('detail.sk_cpns', $guru->detail->sk_cpns ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">TMT Tugas PNS</label>
                            <input type="date" name="detail[tmt_tugas_pns]"
                                value="{{ old('detail.tmt_tugas_pns', $guru->detail?->tmt_tugas_pns?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Karpeg</label>
                            <input type="text" name="detail[karpeg]"
                                value="{{ old('detail.karpeg', $guru->detail->karpeg ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kartu Karis/Karsu</label>
                            <input type="text" name="detail[kartu_karis]"
                                value="{{ old('detail.kartu_karis', $guru->detail->kartu_karis ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Pajak -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Pajak</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NPWP</label>
                            <input type="text" name="detail[npwp]"
                                value="{{ old('detail.npwp', $guru->detail->npwp ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Wajib Pajak</label>
                            <input type="text" name="detail[nm_wajib_pajak]"
                                value="{{ old('detail.nm_wajib_pajak', $guru->detail->nm_wajib_pajak ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all">
                        Update
                    </button>
                    <a href="{{ route('admin.guru.index') }}"
                        class="px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection