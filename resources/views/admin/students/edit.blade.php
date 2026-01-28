@extends('layouts.admin')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.students.index') }}"
                        class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Edit Data Siswa</h3>
                        <p class="text-sm text-slate-400 mt-1">Perbarui informasi siswa lengkap</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Data Identitas -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0" />
                            </svg>
                        </span>
                        Data Identitas Siswa
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            @error('nis')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK</label>
                            <input type="text" name="detail[nik]"
                                value="{{ old('detail.nik', $student->detail->nik ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KK</label>
                            <input type="text" name="detail[no_kk]"
                                value="{{ old('detail.no_kk', $student->detail->no_kk ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NPD</label>
                            <input type="text" name="detail[npd]"
                                value="{{ old('detail.npd', $student->detail->npd ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Reg Akta Lahir</label>
                            <input type="text" name="detail[no_reg_akta_lhr]"
                                value="{{ old('detail.no_reg_akta_lhr', $student->detail->no_reg_akta_lhr ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Pribadi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tempat Lahir</label>
                            <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $student->tmpt_lhr) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="tgl_lhr"
                                value="{{ old('tgl_lhr', $student->tgl_lhr?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin</label>
                            <select name="jen_kel"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('jen_kel', $student->jen_kel) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('jen_kel', $student->jen_kel) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                            <select name="agama"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $student->agama) == $agama ? 'selected' : '' }}>
                                        {{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Anak Ke</label>
                            <input type="number" name="detail[anak_ke]"
                                value="{{ old('detail.anak_ke', $student->detail->anak_ke ?? '') }}" min="1"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jumlah Saudara Kandung</label>
                            <input type="number" name="detail[jml_sdr_kandung]"
                                value="{{ old('detail.jml_sdr_kandung', $student->detail->jml_sdr_kandung ?? '') }}" min="0"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Alamat -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </span>
                        Data Alamat
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
                            <textarea name="almt_siswa" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">{{ old('almt_siswa', $student->almt_siswa) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">RT</label>
                            <input type="text" name="detail[rt]" value="{{ old('detail.rt', $student->detail->rt ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">RW</label>
                            <input type="text" name="detail[rw]" value="{{ old('detail.rw', $student->detail->rw ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Dusun</label>
                            <input type="text" name="detail[dusun]"
                                value="{{ old('detail.dusun', $student->detail->dusun ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelurahan</label>
                            <input type="text" name="detail[kelurahan]"
                                value="{{ old('detail.kelurahan', $student->detail->kelurahan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kecamatan</label>
                            <input type="text" name="detail[kecamatan]"
                                value="{{ old('detail.kecamatan', $student->detail->kecamatan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode Pos</label>
                            <input type="text" name="detail[kode_pos]"
                                value="{{ old('detail.kode_pos', $student->detail->kode_pos ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Tinggal</label>
                            <select name="detail[jns_tinggal]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan', 'Lainnya'] as $jt)
                                    <option value="{{ $jt }}" {{ old('detail.jns_tinggal', $student->detail->jns_tinggal ?? '') == $jt ? 'selected' : '' }}>{{ $jt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Alat Transportasi</label>
                            <select name="detail[alt_transp]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Ojek', 'Andong/Dokar', 'Perahu/Sampan', 'Lainnya'] as $at)
                                    <option value="{{ $at }}" {{ old('detail.alt_transp', $student->detail->alt_transp ?? '') == $at ? 'selected' : '' }}>{{ $at }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Kontak -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Kontak</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon (Siswa)</label>
                            <input type="text" name="no_tlp" value="{{ old('no_tlp', $student->no_tlp) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Telepon Rumah</label>
                            <input type="text" name="detail[telp]"
                                value="{{ old('detail.telp', $student->detail->telp ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">HP</label>
                            <input type="text" name="detail[hp]" value="{{ old('detail.hp', $student->detail->hp ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                            <input type="email" name="detail[e_mail]"
                                value="{{ old('detail.e_mail', $student->detail->e_mail ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Akademik</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelas</label>
                            <select name="kelas_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $student->kelas_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jurusan</label>
                            <select name="m_jurusan_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('m_jurusan_id', $student->m_jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Sekolah Asal</label>
                            <input type="text" name="detail[sekolah_asal]"
                                value="{{ old('detail.sekolah_asal', $student->detail->sekolah_asal ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">SKHUN</label>
                            <input type="text" name="detail[skhun]"
                                value="{{ old('detail.skhun', $student->detail->skhun ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Peserta Ujian</label>
                            <input type="text" name="detail[no_pes_ujian]"
                                value="{{ old('detail.no_pes_ujian', $student->detail->no_pes_ujian ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Seri Ijazah</label>
                            <input type="text" name="detail[no_seri_ijazah]"
                                value="{{ old('detail.no_seri_ijazah', $student->detail->no_seri_ijazah ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Kebutuhan Khusus</label>
                            <input type="text" name="detail[kebutuhan_khusus]"
                                value="{{ old('detail.kebutuhan_khusus', $student->detail->kebutuhan_khusus ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Ayah -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-sky-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        Data Ayah
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ayah</label>
                            <input type="text" name="nm_ayah" value="{{ old('nm_ayah', $student->nm_ayah) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Ayah</label>
                            <input type="text" name="detail[ayah_nik]"
                                value="{{ old('detail.ayah_nik', $student->detail->ayah_nik ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Lahir</label>
                            <input type="number" name="detail[ayah_th_lhr]"
                                value="{{ old('detail.ayah_th_lhr', $student->detail->ayah_th_lhr ?? '') }}" min="1900"
                                max="2100"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenjang Pendidikan</label>
                            <select name="detail[ayah_jenjang]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Tidak Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $jj)
                                    <option value="{{ $jj }}" {{ old('detail.ayah_jenjang', $student->detail->ayah_jenjang ?? '') == $jj ? 'selected' : '' }}>{{ $jj }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                            <input type="text" name="detail[ayah_pekerjaan]"
                                value="{{ old('detail.ayah_pekerjaan', $student->detail->ayah_pekerjaan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                            <select name="detail[ayah_penghasilan]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['< Rp 500.000', 'Rp 500.000 - 1.000.000', 'Rp 1.000.000 - 2.000.000', 'Rp 2.000.000 - 5.000.000', '> Rp 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                    <option value="{{ $ph }}" {{ old('detail.ayah_penghasilan', $student->detail->ayah_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-pink-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        Data Ibu
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ibu</label>
                            <input type="text" name="detail[ibu_nama]"
                                value="{{ old('detail.ibu_nama', $student->detail->ibu_nama ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Ibu</label>
                            <input type="text" name="detail[ibu_nik]"
                                value="{{ old('detail.ibu_nik', $student->detail->ibu_nik ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Lahir</label>
                            <input type="number" name="detail[ibu_th_lahir]"
                                value="{{ old('detail.ibu_th_lahir', $student->detail->ibu_th_lahir ?? '') }}" min="1900"
                                max="2100"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenjang Pendidikan</label>
                            <select name="detail[ibu_jenjang]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Tidak Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $jj)
                                    <option value="{{ $jj }}" {{ old('detail.ibu_jenjang', $student->detail->ibu_jenjang ?? '') == $jj ? 'selected' : '' }}>{{ $jj }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                            <input type="text" name="detail[ibu_pekerjaan]"
                                value="{{ old('detail.ibu_pekerjaan', $student->detail->ibu_pekerjaan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                            <select name="detail[ibu_penghasilan]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['< Rp 500.000', 'Rp 500.000 - 1.000.000', 'Rp 1.000.000 - 2.000.000', 'Rp 2.000.000 - 5.000.000', '> Rp 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                    <option value="{{ $ph }}" {{ old('detail.ibu_penghasilan', $student->detail->ibu_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Wali -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        Data Wali (Opsional)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Wali</label>
                            <input type="text" name="detail[wali_nama]"
                                value="{{ old('detail.wali_nama', $student->detail->wali_nama ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Wali</label>
                            <input type="text" name="detail[wali_nik]"
                                value="{{ old('detail.wali_nik', $student->detail->wali_nik ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Lahir</label>
                            <input type="number" name="detail[wali_th_lahir]"
                                value="{{ old('detail.wali_th_lahir', $student->detail->wali_th_lahir ?? '') }}" min="1900"
                                max="2100"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenjang Pendidikan</label>
                            <select name="detail[wali_jenjang]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['Tidak Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $jj)
                                    <option value="{{ $jj }}" {{ old('detail.wali_jenjang', $student->detail->wali_jenjang ?? '') == $jj ? 'selected' : '' }}>{{ $jj }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                            <input type="text" name="detail[wali_pekerjaan]"
                                value="{{ old('detail.wali_pekerjaan', $student->detail->wali_pekerjaan ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                            <select name="detail[wali_penghasilan]"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">Pilih</option>
                                @foreach(['< Rp 500.000', 'Rp 500.000 - 1.000.000', 'Rp 1.000.000 - 2.000.000', 'Rp 2.000.000 - 5.000.000', '> Rp 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                    <option value="{{ $ph }}" {{ old('detail.wali_penghasilan', $student->detail->wali_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Data Bantuan -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-teal-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Data Bantuan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="detail[p_kps]" value="1" id="p_kps" {{ old('detail.p_kps', $student->detail->p_kps ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500">
                            <label for="p_kps" class="text-sm text-slate-300">Penerima KPS</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="detail[penerima_kip]" value="1" id="penerima_kip" {{ old('detail.penerima_kip', $student->detail->penerima_kip ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500">
                            <label for="penerima_kip" class="text-sm text-slate-300">Penerima KIP</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="detail[layak_pip]" value="1" id="layak_pip" {{ old('detail.layak_pip', $student->detail->layak_pip ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500">
                            <label for="layak_pip" class="text-sm text-slate-300">Layak PIP</label>
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KIP</label>
                            <input type="text" name="detail[no_kip]"
                                value="{{ old('detail.no_kip', $student->detail->no_kip ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KKS</label>
                            <input type="text" name="detail[no_kks]"
                                value="{{ old('detail.no_kks', $student->detail->no_kks ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Alasan Layak PIP</label>
                            <input type="text" name="detail[alasan_layak_pip]"
                                value="{{ old('detail.alasan_layak_pip', $student->detail->alasan_layak_pip ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Bank -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Bank</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Bank</label>
                            <input type="text" name="detail[bank]"
                                value="{{ old('detail.bank', $student->detail->bank ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Rekening</label>
                            <input type="text" name="detail[no_rek]"
                                value="{{ old('detail.no_rek', $student->detail->no_rek ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Atas Nama</label>
                            <input type="text" name="detail[an_rek]"
                                value="{{ old('detail.an_rek', $student->detail->an_rek ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Data Fisik & Lokasi -->
                <div class="border-b border-slate-800/50 pb-5">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Fisik & Lokasi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="detail[berat_bdn]"
                                value="{{ old('detail.berat_bdn', $student->detail->berat_bdn ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Tinggi Badan (cm)</label>
                            <input type="number" step="0.01" name="detail[tinggi_bdn]"
                                value="{{ old('detail.tinggi_bdn', $student->detail->tinggi_bdn ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.01" name="detail[lingkar_kep]"
                                value="{{ old('detail.lingkar_kep', $student->detail->lingkar_kep ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Lintang</label>
                            <input type="text" name="detail[lintang]"
                                value="{{ old('detail.lintang', $student->detail->lintang ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Bujur</label>
                            <input type="text" name="detail[bujur]"
                                value="{{ old('detail.bujur', $student->detail->bujur ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Jarak Rumah ke Sekolah
                                (km)</label>
                            <input type="number" step="0.01" name="detail[jarak_rmh_skul]"
                                value="{{ old('detail.jarak_rmh_skul', $student->detail->jarak_rmh_skul ?? '') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Is Active -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $student->is_active ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
                    <label for="is_active" class="text-sm text-slate-300">Siswa Aktif</label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all">
                        Update
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                        class="px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection