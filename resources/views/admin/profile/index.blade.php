@extends('layouts.admin')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ activeTab: 'profile' }">
        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/30 rounded-xl text-rose-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            <button @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Profile
            </button>
            @if($user->level === 'guru')
                <button @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Data Pribadi
                </button>
                <button @click="activeTab = 'riwayat'"
                    :class="activeTab === 'riwayat' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat
                </button>
            @endif
            @if($user->level === 'siswa')
                <button @click="activeTab = 'student_personal'"
                    :class="activeTab === 'student_personal' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Data Pribadi
                </button>
            @endif
            @if($user->level !== 'siswa')
                <button @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Ubah Password
                </button>
            @endif
        </div>

        {{-- Profile Information Tab --}}
        <div x-show="activeTab === 'profile'" x-transition
            class="bg-slate-900/50 rounded-2xl border border-slate-800/50 p-6">
            <h2 class="text-xl font-semibold text-white mb-6">Informasi Profile</h2>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Photo Section --}}
                    <div class="md:col-span-2 flex items-center gap-6">
                        <div class="relative cursor-pointer group shrink-0"
                            onclick="document.getElementById('foto_input').click()">
                            @if ($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Profile Photo"
                                    class="w-20 h-[104px] rounded-xl object-cover border-2 border-slate-700 group-hover:border-blue-500 transition-colors">
                            @else
                                <div
                                    class="w-20 h-[104px] rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold group-hover:from-blue-600 group-hover:to-purple-700 transition-all">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                            <div
                                class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Foto Profile</label>
                            <input type="file" name="foto" id="foto_input" accept="image/*"
                                class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer cursor-pointer">
                            <p class="mt-1 text-xs text-slate-500">JPG, PNG, GIF. Maks 2MB. Klik foto untuk ganti.</p>
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                        @if($user->level === 'siswa' || $user->level === 'guru')
                            <input type="text" value="{{ $user->username }}" disabled
                                class="w-full px-4 py-3 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400 cursor-not-allowed">
                            <input type="hidden" name="username" value="{{ $user->username }}">
                        @else
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                        @endif
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                    </div>

                    {{-- Level (Read Only) --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Level</label>
                        <input type="text" value="{{ ucfirst($user->level) }}" readonly
                            class="w-full px-4 py-3 bg-slate-800/30 border border-slate-700/50 rounded-xl text-slate-400 cursor-not-allowed">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Personal Data Tab (Guru Only) --}}
        @if($user->level === 'guru')
            <div x-show="activeTab === 'personal'" x-transition
                class="bg-slate-900/50 rounded-2xl border border-slate-800/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Data Pribadi Guru</h2>

                <form action="{{ route('admin.profile.guru') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Data Dasar --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                        class="text-rose-400">*</span></label>
                                <input type="text" name="nama" value="{{ old('nama', $user->guru->nama ?? $user->name) }}"
                                    required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Gelar</label>
                                <input type="text" name="detail[gelar]"
                                    value="{{ old('detail.gelar', $user->guru->detail?->gelar ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="S.Pd, M.Pd">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip', $user->guru->nip ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NUPTK</label>
                                <input type="text" name="nuptk" value="{{ old('nuptk', $user->guru->nuptk ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK</label>
                                <input type="text" name="detail[nik]"
                                    value="{{ old('detail.nik', $user->guru->detail?->nik ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin</label>
                                <select name="jen_kel"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jen_kel', $user->guru->jen_kel ?? '') === 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P" {{ old('jen_kel', $user->guru->jen_kel ?? '') === 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tempat Lahir</label>
                                <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $user->guru->tmpt_lhr ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr" value="{{ old('tgl_lhr', $user->guru->tgl_lhr ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                                <select name="detail[agama]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('detail.agama', $user->guru->detail?->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Status Perkawinan</label>
                                <select name="detail[status_perkawinan]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                        <option value="{{ $sp }}" {{ old('detail.status_perkawinan', $user->guru->detail?->status_perkawinan ?? '') == $sp ? 'selected' : '' }}>{{ $sp }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ibu Kandung</label>
                                <input type="text" name="detail[nm_ibu_kandung]"
                                    value="{{ old('detail.nm_ibu_kandung', $user->guru->detail?->nm_ibu_kandung ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    {{-- Data Kontak --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Kontak</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon</label>
                                <input type="text" name="no_tlp" value="{{ old('no_tlp', $user->guru->no_tlp ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. HP</label>
                                <input type="text" name="detail[no_hp]"
                                    value="{{ old('detail.no_hp', $user->guru->detail?->no_hp ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                                <input type="email" name="detail[email]"
                                    value="{{ old('detail.email', $user->guru->detail?->email ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    {{-- Data Alamat --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Alamat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat Jalan</label>
                                <textarea name="detail[alamat_jln]" rows="2"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">{{ old('detail.alamat_jln', $user->guru->detail?->alamat_jln ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">RT/RW</label>
                                <div class="flex gap-2">
                                    <input type="text" name="detail[rt]"
                                        value="{{ old('detail.rt', $user->guru->detail?->rt ?? '') }}" placeholder="RT"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <input type="text" name="detail[rw]"
                                        value="{{ old('detail.rw', $user->guru->detail?->rw ?? '') }}" placeholder="RW"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Dusun</label>
                                <input type="text" name="detail[nama_dusun]"
                                    value="{{ old('detail.nama_dusun', $user->guru->detail?->nama_dusun ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelurahan</label>
                                <input type="text" name="detail[kelurahan]"
                                    value="{{ old('detail.kelurahan', $user->guru->detail?->kelurahan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kecamatan</label>
                                <input type="text" name="detail[kecamatan]"
                                    value="{{ old('detail.kecamatan', $user->guru->detail?->kecamatan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode Pos</label>
                                <input type="text" name="detail[kode_pos]"
                                    value="{{ old('detail.kode_pos', $user->guru->detail?->kode_pos ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    {{-- Data Kepegawaian --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Kepegawaian</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Status Pegawai</label>
                                <select name="detail[status_pegawai]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['PNS', 'GTT', 'Honorer', 'PPPK', 'Pegawai Tetap Yayasan', 'Pegawai Tidak Tetap'] as $sp)
                                        <option value="{{ $sp }}" {{ old('detail.status_pegawai', $user->guru->detail?->status_pegawai ?? '') == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis PTK</label>
                                <select name="detail[jenis_ptk]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Guru Kelas', 'Guru Mata Pelajaran', 'Guru BK', 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Tenaga Administrasi'] as $jptk)
                                        <option value="{{ $jptk }}" {{ old('detail.jenis_ptk', $user->guru->detail?->jenis_ptk ?? '') == $jptk ? 'selected' : '' }}>{{ $jptk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIY</label>
                                <input type="text" name="detail[niy]"
                                    value="{{ old('detail.niy', $user->guru->detail?->niy ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NPWP</label>
                                <input type="text" name="detail[npwp]"
                                    value="{{ old('detail.npwp', $user->guru->detail?->npwp ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data Pribadi
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Riwayat Tab (Guru Only) --}}
        @if($user->level === 'guru' && $user->guru)
            <div x-show="activeTab === 'riwayat'" x-transition x-data="{ riwayatTab: 'sertifikasi' }">
                {{-- Riwayat Sub-tabs --}}
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-2 mb-4">
                    <div class="flex flex-wrap gap-1">
                        @php
                            $riwayatTabs = [
                                'sertifikasi' => 'Sertifikasi',
                                'pendidikan' => 'Pendidikan',
                                'kompetensi' => 'Kompetensi',
                                'anak' => 'Anak',
                                'beasiswa' => 'Beasiswa',
                                'buku' => 'Buku',
                                'diklat' => 'Diklat',
                                'karya' => 'Karya Tulis',
                                'kesejahteraan' => 'Kesejahteraan',
                                'tunjangan' => 'Tunjangan',
                                'tugas' => 'Tugas Tambahan',
                                'inpasing' => 'Inpasing',
                                'gaji' => 'Gaji Berkala',
                                'karir' => 'Karir',
                                'jabatan' => 'Jabatan',
                                'pangkat' => 'Pangkat',
                                'jab_fungsional' => 'Jab. Fungsional',
                            ];
                        @endphp
                        @foreach($riwayatTabs as $key => $label)
                            <button @click="riwayatTab = '{{ $key }}'"
                                :class="riwayatTab === '{{ $key }}' ? 'bg-blue-500 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800'"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors cursor-pointer">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
                    <!-- Sertifikasi -->
                    <div x-show="riwayatTab === 'sertifikasi'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Riwayat Sertifikasi</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenis', 'No.', 'Tahun', 'Bidang Studi', 'NRG', 'No. Peserta'], 'data' => $user->guru->sertifikasis, 'fields' => ['jenis_serti', 'no', 'thn', 'bidang_studi', 'nrg', 'no_pes'], 'type' => 'sertifikasi', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenis_serti', 'label' => 'Jenis'], ['name' => 'no', 'label' => 'Nomor'], ['name' => 'thn', 'label' => 'Tahun'], ['name' => 'bidang_studi', 'label' => 'Bidang Studi'], ['name' => 'nrg', 'label' => 'NRG'], ['name' => 'no_pes', 'label' => 'No. Peserta']]])
                    </div>
                    <!-- Pendidikan -->
                    <div x-show="riwayatTab === 'pendidikan'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Riwayat Pendidikan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenjang', 'Gelar', 'Satuan Pendidikan', 'Bidang Studi', 'IPK'], 'data' => $user->guru->pendidikans, 'fields' => ['jenjang_pendidikan', 'gelar', 'satuan_pendidikan', 'bidang_studi', 'ipk'], 'type' => 'pendidikan', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenjang_pendidikan', 'label' => 'Jenjang'], ['name' => 'gelar', 'label' => 'Gelar'], ['name' => 'satuan_pendidikan', 'label' => 'Satuan'], ['name' => 'bidang_studi', 'label' => 'Bidang Studi'], ['name' => 'thn_masuk', 'label' => 'Thn Masuk'], ['name' => 'ipk', 'label' => 'IPK', 'type' => 'number']]])
                    </div>
                    <!-- Kompetensi -->
                    <div x-show="riwayatTab === 'kompetensi'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Kompetensi</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Bidang Studi', 'Urutan'], 'data' => $user->guru->kompetensis, 'fields' => ['bidang_studi', 'urutan'], 'type' => 'kompetensi', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'bidang_studi', 'label' => 'Bidang Studi'], ['name' => 'urutan', 'label' => 'Urutan', 'type' => 'number']]])
                    </div>
                    <!-- Anak -->
                    <div x-show="riwayatTab === 'anak'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Data Anak</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Nama', 'Status', 'Jenjang', 'JK', 'Tgl Lahir'], 'data' => $user->guru->anaks, 'fields' => ['nama', 'status', 'jenjang', 'jk', 'tgl_lhr'], 'type' => 'anak', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'nama', 'label' => 'Nama'], ['name' => 'status', 'label' => 'Status'], ['name' => 'jenjang', 'label' => 'Jenjang'], ['name' => 'jk', 'label' => 'JK', 'type' => 'select', 'options' => ['L', 'P']], ['name' => 'tgl_lhr', 'label' => 'Tgl Lahir', 'type' => 'date']]])
                    </div>
                    <!-- Beasiswa -->
                    <div x-show="riwayatTab === 'beasiswa'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Beasiswa</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenis', 'Keterangan', 'Thn Mulai', 'Thn Akhir'], 'data' => $user->guru->beasiswas, 'fields' => ['jenis', 'ket', 'thn_mulai', 'thn_akhir'], 'type' => 'beasiswa', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenis', 'label' => 'Jenis'], ['name' => 'ket', 'label' => 'Keterangan'], ['name' => 'thn_mulai', 'label' => 'Thn Mulai'], ['name' => 'thn_akhir', 'label' => 'Thn Akhir']]])
                    </div>
                    <!-- Buku -->
                    <div x-show="riwayatTab === 'buku'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Buku</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Judul', 'Tahun', 'Penerbit', 'ISBN'], 'data' => $user->guru->bukus, 'fields' => ['judul', 'thn', 'penerbit', 'isbn'], 'type' => 'buku', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'judul', 'label' => 'Judul'], ['name' => 'thn', 'label' => 'Tahun'], ['name' => 'penerbit', 'label' => 'Penerbit'], ['name' => 'isbn', 'label' => 'ISBN']]])
                    </div>
                    <!-- Diklat -->
                    <div x-show="riwayatTab === 'diklat'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Diklat</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenis', 'Nama', 'Penyelenggara', 'Tahun', 'Jam'], 'data' => $user->guru->diklats, 'fields' => ['jns_diklat', 'nama', 'penyelenggara', 'thn', 'brp_jam'], 'type' => 'diklat', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jns_diklat', 'label' => 'Jenis'], ['name' => 'nama', 'label' => 'Nama'], ['name' => 'penyelenggara', 'label' => 'Penyelenggara'], ['name' => 'thn', 'label' => 'Tahun'], ['name' => 'brp_jam', 'label' => 'Jam', 'type' => 'number']]])
                    </div>
                    <!-- Karya Tulis -->
                    <div x-show="riwayatTab === 'karya'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Karya Tulis</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Judul', 'Tahun', 'Publikasi'], 'data' => $user->guru->karyaTuliss, 'fields' => ['judul', 'thn_pembuatan', 'publikasi'], 'type' => 'karya', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'judul', 'label' => 'Judul'], ['name' => 'thn_pembuatan', 'label' => 'Tahun'], ['name' => 'publikasi', 'label' => 'Publikasi']]])
                    </div>
                    <!-- Kesejahteraan -->
                    <div x-show="riwayatTab === 'kesejahteraan'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Kesejahteraan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenis', 'Nama', 'Penyelenggara', 'Status'], 'data' => $user->guru->kesejahteraans, 'fields' => ['jenis', 'nama', 'penyelenggara', 'status'], 'type' => 'kesejahteraan', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenis', 'label' => 'Jenis'], ['name' => 'nama', 'label' => 'Nama'], ['name' => 'penyelenggara', 'label' => 'Penyelenggara'], ['name' => 'dari_th', 'label' => 'Dari Th'], ['name' => 'sampai_th', 'label' => 'Sampai Th'], ['name' => 'status', 'label' => 'Status']]])
                    </div>
                    <!-- Tunjangan -->
                    <div x-show="riwayatTab === 'tunjangan'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Tunjangan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenis', 'Nama', 'Instansi', 'Nominal'], 'data' => $user->guru->tunjangans, 'fields' => ['jenis', 'nama', 'instansi', 'nominal'], 'type' => 'tunjangan', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenis', 'label' => 'Jenis'], ['name' => 'nama', 'label' => 'Nama'], ['name' => 'instansi', 'label' => 'Instansi'], ['name' => 'nominal', 'label' => 'Nominal', 'type' => 'number']]])
                    </div>
                    <!-- Tugas Tambahan -->
                    <div x-show="riwayatTab === 'tugas'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Tugas Tambahan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jabatan', 'No. SK', 'TMT Tugas', 'TST Tugas'], 'data' => $user->guru->tugasTambahans, 'fields' => ['jabatan', 'no_sk', 'tmt_tugas', 'tst_tugas'], 'type' => 'tugas', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jabatan', 'label' => 'Jabatan'], ['name' => 'no_sk', 'label' => 'No. SK'], ['name' => 'tmt_tugas', 'label' => 'TMT', 'type' => 'date'], ['name' => 'tst_tugas', 'label' => 'TST', 'type' => 'date']]])
                    </div>
                    <!-- Inpasing -->
                    <div x-show="riwayatTab === 'inpasing'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Inpasing</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Pangkat/Gol', 'No. SK', 'TMT SK'], 'data' => $user->guru->inpasings, 'fields' => ['pangkat_gol', 'no_sk_inpasing', 'tmt_sk'], 'type' => 'inpasing', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'pangkat_gol', 'label' => 'Pangkat/Gol'], ['name' => 'no_sk_inpasing', 'label' => 'No. SK'], ['name' => 'tgl_sk', 'label' => 'Tgl SK', 'type' => 'date'], ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date']]])
                    </div>
                    <!-- Gaji Berkala -->
                    <div x-show="riwayatTab === 'gaji'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Gaji Berkala</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Pangkat/Gol', 'No. SK', 'TMT SK', 'Gapok'], 'data' => $user->guru->gajiBerkalas, 'fields' => ['pangkat_gol', 'nomor_sk', 'tmt_sk', 'gapok'], 'type' => 'gaji', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'pangkat_gol', 'label' => 'Pangkat/Gol'], ['name' => 'nomor_sk', 'label' => 'Nomor SK'], ['name' => 'tanggal_sk', 'label' => 'Tgl SK', 'type' => 'date'], ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date'], ['name' => 'gapok', 'label' => 'Gapok', 'type' => 'number']]])
                    </div>
                    <!-- Karir -->
                    <div x-show="riwayatTab === 'karir'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Karir</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jenjang', 'Jenis Lembaga', 'Jns PTK', 'Mapel'], 'data' => $user->guru->karirGurus, 'fields' => ['jenjang_pendidikan', 'jenis_lembaga', 'jns_ptk', 'mapel'], 'type' => 'karir', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jenjang_pendidikan', 'label' => 'Jenjang'], ['name' => 'jenis_lembaga', 'label' => 'Jenis Lembaga'], ['name' => 'jns_ptk', 'label' => 'Jenis PTK'], ['name' => 'mapel', 'label' => 'Mapel']]])
                    </div>
                    <!-- Jabatan -->
                    <div x-show="riwayatTab === 'jabatan'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Jabatan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jabatan PTK', 'SK Jabatan', 'TMT SK'], 'data' => $user->guru->jabatans, 'fields' => ['jabatan_ptk', 'sk_jabatan', 'tmt_sk'], 'type' => 'jabatan', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jabatan_ptk', 'label' => 'Jabatan PTK'], ['name' => 'sk_jabatan', 'label' => 'SK Jabatan'], ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date']]])
                    </div>
                    <!-- Pangkat -->
                    <div x-show="riwayatTab === 'pangkat'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Pangkat Golongan</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Pangkat/Gol', 'No. SK', 'TMT Pangkat'], 'data' => $user->guru->pangkatGols, 'fields' => ['pangkat_gol', 'no_sk', 'tmt_pangkat'], 'type' => 'pangkat', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'pangkat_gol', 'label' => 'Pangkat/Gol'], ['name' => 'no_sk', 'label' => 'No. SK'], ['name' => 'tgl_pangkat', 'label' => 'Tgl Pangkat', 'type' => 'date'], ['name' => 'tmt_pangkat', 'label' => 'TMT Pangkat', 'type' => 'date']]])
                    </div>
                    <!-- Jabatan Fungsional -->
                    <div x-show="riwayatTab === 'jab_fungsional'" x-cloak>
                        <div class="p-4 border-b border-slate-800/50">
                            <h3 class="text-base font-semibold text-white">Jabatan Fungsional</h3>
                        </div>
                        @include('admin.guru.partials.table', ['headers' => ['Jabatan Fungsional', 'SK Jabatan', 'TMT SK'], 'data' => $user->guru->jabatanFungsionals, 'fields' => ['jabatan_fungsional', 'sk_jabatan', 'tmt_sk'], 'type' => 'jab_fungsional', 'guruId' => $user->guru->id, 'formFields' => [['name' => 'jabatan_fungsional', 'label' => 'Jabatan Fungsional'], ['name' => 'sk_jabatan', 'label' => 'SK Jabatan'], ['name' => 'tmt_sk', 'label' => 'TMT SK', 'type' => 'date']]])
                    </div>
                </div>
            </div>
        @endif

        {{-- Student Personal Data Tab --}}
        @if($user->level === 'siswa')
            <div x-show="activeTab === 'student_personal'" x-transition
                class="bg-slate-900/50 rounded-2xl border border-slate-800/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Data Pribadi Siswa</h2>

                {{-- Info Display --}}
                <div
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-slate-800/30 rounded-xl border border-slate-700/30">
                    <div><span class="text-xs text-slate-500">NIS</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->nis ?? '-' }}</p>
                    </div>
                    <div><span class="text-xs text-slate-500">NISN</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->nisn ?? '-' }}</p>
                    </div>
                    <div><span class="text-xs text-slate-500">Kelas</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->kelas->nm_kls ?? '-' }}</p>
                    </div>
                    <div><span class="text-xs text-slate-500">Jurusan</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->jurusan->paket_keahlian ?? '-' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.student') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Data Dasar --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                        class="text-rose-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->student->name ?? $user->name) }}"
                                    required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NISN</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $user->student->nisn ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK</label>
                                <input type="text" name="detail[nik]"
                                    value="{{ old('detail.nik', $user->student->detail?->nik ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KK</label>
                                <input type="text" name="detail[no_kk]"
                                    value="{{ old('detail.no_kk', $user->student->detail?->no_kk ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Kelamin</label>
                                <select name="jen_kel"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jen_kel', $user->student->jen_kel ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jen_kel', $user->student->jen_kel ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tempat Lahir</label>
                                <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $user->student->tmpt_lhr ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr"
                                    value="{{ old('tgl_lhr', $user->student->tgl_lhr ? $user->student->tgl_lhr->format('Y-m-d') : '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                                <select name="agama"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama', $user->student->agama ?? '') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Anak Ke</label>
                                <input type="number" name="detail[anak_ke]"
                                    value="{{ old('detail.anak_ke', $user->student->detail?->anak_ke ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jumlah Saudara Kandung</label>
                                <input type="number" name="detail[jml_sdr_kandung]"
                                    value="{{ old('detail.jml_sdr_kandung', $user->student->detail?->jml_sdr_kandung ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    {{-- Data Kontak & Alamat --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Kontak & Alamat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon</label>
                                <input type="text" name="no_tlp" value="{{ old('no_tlp', $user->student->no_tlp ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. HP</label>
                                <input type="text" name="detail[hp]"
                                    value="{{ old('detail.hp', $user->student->detail?->hp ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                                <input type="email" name="detail[e_mail]"
                                    value="{{ old('detail.e_mail', $user->student->detail?->e_mail ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
                                <textarea name="almt_siswa" rows="2"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">{{ old('almt_siswa', $user->student->almt_siswa ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">RT/RW</label>
                                <div class="flex gap-2">
                                    <input type="text" name="detail[rt]"
                                        value="{{ old('detail.rt', $user->student->detail?->rt ?? '') }}" placeholder="RT"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <input type="text" name="detail[rw]"
                                        value="{{ old('detail.rw', $user->student->detail?->rw ?? '') }}" placeholder="RW"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Dusun</label>
                                <input type="text" name="detail[dusun]"
                                    value="{{ old('detail.dusun', $user->student->detail?->dusun ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kelurahan</label>
                                <input type="text" name="detail[kelurahan]"
                                    value="{{ old('detail.kelurahan', $user->student->detail?->kelurahan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kecamatan</label>
                                <input type="text" name="detail[kecamatan]"
                                    value="{{ old('detail.kecamatan', $user->student->detail?->kecamatan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode Pos</label>
                                <input type="text" name="detail[kode_pos]"
                                    value="{{ old('detail.kode_pos', $user->student->detail?->kode_pos ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Tinggal</label>
                                <select name="detail[jns_tinggal]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Bersama Orang Tua', 'Wali', 'Kost', 'Asrama', 'Panti Asuhan', 'Lainnya'] as $jt)
                                        <option value="{{ $jt }}" {{ old('detail.jns_tinggal', $user->student->detail?->jns_tinggal ?? '') == $jt ? 'selected' : '' }}>{{ $jt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Alat Transportasi</label>
                                <select name="detail[alt_transp]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Ojek Online', 'Lainnya'] as $at)
                                        <option value="{{ $at }}" {{ old('detail.alt_transp', $user->student->detail?->alt_transp ?? '') == $at ? 'selected' : '' }}>{{ $at }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Data Orang Tua --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Ayah</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ayah</label>
                                <input type="text" name="nm_ayah" value="{{ old('nm_ayah', $user->student->nm_ayah ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Ayah</label>
                                <input type="text" name="detail[ayah_nik]"
                                    value="{{ old('detail.ayah_nik', $user->student->detail?->ayah_nik ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Lahir</label>
                                <input type="number" name="detail[ayah_th_lhr]"
                                    value="{{ old('detail.ayah_th_lhr', $user->student->detail?->ayah_th_lhr ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Pendidikan</label>
                                <select name="detail[ayah_jenjang]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $jj)
                                        <option value="{{ $jj }}" {{ old('detail.ayah_jenjang', $user->student->detail?->ayah_jenjang ?? '') == $jj ? 'selected' : '' }}>{{ $jj }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                                <input type="text" name="detail[ayah_pekerjaan]"
                                    value="{{ old('detail.ayah_pekerjaan', $user->student->detail?->ayah_pekerjaan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                                <select name="detail[ayah_penghasilan]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< 500.000', '500.000 - 1.000.000', '1.000.000 - 2.000.000', '2.000.000 - 5.000.000', '> 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                        <option value="{{ $ph }}" {{ old('detail.ayah_penghasilan', $user->student->detail?->ayah_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Ibu</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ibu</label>
                                <input type="text" name="detail[ibu_nama]"
                                    value="{{ old('detail.ibu_nama', $user->student->detail?->ibu_nama ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Ibu</label>
                                <input type="text" name="detail[ibu_nik]"
                                    value="{{ old('detail.ibu_nik', $user->student->detail?->ibu_nik ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Tahun Lahir</label>
                                <input type="number" name="detail[ibu_th_lahir]"
                                    value="{{ old('detail.ibu_th_lahir', $user->student->detail?->ibu_th_lahir ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Pendidikan</label>
                                <select name="detail[ibu_jenjang]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $jj)
                                        <option value="{{ $jj }}" {{ old('detail.ibu_jenjang', $user->student->detail?->ibu_jenjang ?? '') == $jj ? 'selected' : '' }}>{{ $jj }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                                <input type="text" name="detail[ibu_pekerjaan]"
                                    value="{{ old('detail.ibu_pekerjaan', $user->student->detail?->ibu_pekerjaan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                                <select name="detail[ibu_penghasilan]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< 500.000', '500.000 - 1.000.000', '1.000.000 - 2.000.000', '2.000.000 - 5.000.000', '> 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                        <option value="{{ $ph }}" {{ old('detail.ibu_penghasilan', $user->student->detail?->ibu_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Data Wali --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Wali (Opsional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Wali</label>
                                <input type="text" name="detail[wali_nama]"
                                    value="{{ old('detail.wali_nama', $user->student->detail?->wali_nama ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">NIK Wali</label>
                                <input type="text" name="detail[wali_nik]"
                                    value="{{ old('detail.wali_nik', $user->student->detail?->wali_nik ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Pekerjaan</label>
                                <input type="text" name="detail[wali_pekerjaan]"
                                    value="{{ old('detail.wali_pekerjaan', $user->student->detail?->wali_pekerjaan ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Penghasilan</label>
                                <select name="detail[wali_penghasilan]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    @foreach(['< 500.000', '500.000 - 1.000.000', '1.000.000 - 2.000.000', '2.000.000 - 5.000.000', '> 5.000.000', 'Tidak Berpenghasilan'] as $ph)
                                        <option value="{{ $ph }}" {{ old('detail.wali_penghasilan', $user->student->detail?->wali_penghasilan ?? '') == $ph ? 'selected' : '' }}>{{ $ph }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Data Akademik --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data Akademik</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Sekolah Asal</label>
                                <input type="text" name="detail[sekolah_asal]"
                                    value="{{ old('detail.sekolah_asal', $user->student->detail?->sekolah_asal ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. SKHUN</label>
                                <input type="text" name="detail[skhun]"
                                    value="{{ old('detail.skhun', $user->student->detail?->skhun ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Peserta Ujian</label>
                                <input type="text" name="detail[no_pes_ujian]"
                                    value="{{ old('detail.no_pes_ujian', $user->student->detail?->no_pes_ujian ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. Seri Ijazah</label>
                                <input type="text" name="detail[no_seri_ijazah]"
                                    value="{{ old('detail.no_seri_ijazah', $user->student->detail?->no_seri_ijazah ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                        </div>
                    </div>

                    {{-- Data KIP/Bantuan --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 mb-4 uppercase tracking-wider">Data KIP/Bantuan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Penerima KIP</label>
                                <select name="detail[penerima_kip]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('detail.penerima_kip', $user->student->detail?->penerima_kip ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('detail.penerima_kip', $user->student->detail?->penerima_kip ?? '') === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KIP</label>
                                <input type="text" name="detail[no_kip]"
                                    value="{{ old('detail.no_kip', $user->student->detail?->no_kip ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">No. KKS</label>
                                <input type="text" name="detail[no_kks]"
                                    value="{{ old('detail.no_kks', $user->student->detail?->no_kks ?? '') }}"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">Layak PIP</label>
                                <select name="detail[layak_pip]"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('detail.layak_pip', $user->student->detail?->layak_pip ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('detail.layak_pip', $user->student->detail?->layak_pip ?? '') === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data Pribadi
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Password Tab --}}
        <div x-show="activeTab === 'password'" x-transition
            class="bg-slate-900/50 rounded-2xl border border-slate-800/50 p-6">
            <h2 class="text-xl font-semibold text-white mb-6">Ubah Password</h2>

            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6 max-w-md">
                    {{-- Current Password --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                            placeholder="Masukkan password saat ini">
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Password Baru</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                            placeholder="Masukkan password baru">
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                            placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection