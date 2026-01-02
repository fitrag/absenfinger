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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $user->guru->nama ?? $user->name) }}" required
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                        </div>

                        {{-- NIP --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $user->guru->nip ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan NIP">
                        </div>

                        {{-- NUPTK --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">NUPTK</label>
                            <input type="text" name="nuptk" value="{{ old('nuptk', $user->guru->nuptk ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan NUPTK">
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                            <select name="jen_kel"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jen_kel', $user->guru->jen_kel ?? '') === 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P" {{ old('jen_kel', $user->guru->jen_kel ?? '') === 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        {{-- Tempat Lahir --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir</label>
                            <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $user->guru->tmpt_lhr ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan tempat lahir">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tgl_lhr" value="{{ old('tgl_lhr', $user->guru->tgl_lhr ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                        </div>

                        {{-- No. Telepon --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">No. Telepon</label>
                            <input type="text" name="no_tlp" value="{{ old('no_tlp', $user->guru->no_tlp ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan nomor telepon">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data Pribadi
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Student Personal Data Tab --}}
        @if($user->level === 'siswa')
            <div x-show="activeTab === 'student_personal'" x-transition
                class="bg-slate-900/50 rounded-2xl border border-slate-800/50 p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Data Pribadi Siswa</h2>

                {{-- Info Display --}}
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-slate-800/30 rounded-xl border border-slate-700/30">
                    <div>
                        <span class="text-xs text-slate-500">NIS</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500">Kelas</span>
                        <p class="text-sm font-medium text-white">{{ $user->student->kelas->nm_kls ?? '-' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.student') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->student->name ?? $user->name) }}"
                                required
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                        </div>

                        {{-- NISN --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $user->student->nisn ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan NISN">
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                            <select name="jen_kel"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jen_kel', $user->student->jen_kel ?? '') === 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P" {{ old('jen_kel', $user->student->jen_kel ?? '') === 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        {{-- Tempat Lahir --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir</label>
                            <input type="text" name="tmpt_lhr" value="{{ old('tmpt_lhr', $user->student->tmpt_lhr ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan tempat lahir">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tgl_lhr"
                                value="{{ old('tgl_lhr', $user->student->tgl_lhr ? $user->student->tgl_lhr->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                        </div>

                        {{-- Agama --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Agama</label>
                            <select name="agama"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                                <option value="">-- Pilih --</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $user->student->agama ?? '') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No. Telepon --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">No. Telepon</label>
                            <input type="text" name="no_tlp" value="{{ old('no_tlp', $user->student->no_tlp ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        {{-- Nama Ayah --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Ayah</label>
                            <input type="text" name="nm_ayah" value="{{ old('nm_ayah', $user->student->nm_ayah ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan nama ayah">
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Alamat</label>
                            <textarea name="almt_siswa" rows="3"
                                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                placeholder="Masukkan alamat lengkap">{{ old('almt_siswa', $user->student->almt_siswa ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer">
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