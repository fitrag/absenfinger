@extends('layouts.admin')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="p-6 border-b border-slate-800/50">
                <h3 class="text-lg font-semibold text-white">Form Tambah Siswa</h3>
                <p class="text-sm text-slate-400 mt-1">Lengkapi data siswa baru</p>
            </div>

            <form action="{{ route('admin.students.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <!-- Data Identitas -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Identitas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIS -->
                        <div>
                            <label for="nis" class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nis" id="nis" value="{{ old('nis') }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Contoh: 2024001">
                            <p class="text-xs text-slate-500 mt-1">Digunakan sebagai username login</p>
                            @error('nis')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- NISN -->
                        <div>
                            <label for="nisn" class="block text-sm font-medium text-slate-300 mb-1.5">NISN <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="10 digit NISN">
                            <p class="text-xs text-slate-500 mt-1">Digunakan sebagai password login</p>
                            @error('nisn')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Pribadi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Masukkan nama lengkap">
                            @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tmpt_lhr" class="block text-sm font-medium text-slate-300 mb-1.5">Tempat
                                Lahir</label>
                            <input type="text" name="tmpt_lhr" id="tmpt_lhr" value="{{ old('tmpt_lhr') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Contoh: Jakarta">
                            @error('tmpt_lhr')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tgl_lhr" class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal
                                Lahir</label>
                            <input type="date" name="tgl_lhr" id="tgl_lhr" value="{{ old('tgl_lhr') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('tgl_lhr')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="jen_kel" class="block text-sm font-medium text-slate-300 mb-1.5">Jenis
                                Kelamin</label>
                            <select name="jen_kel" id="jen_kel"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jen_kel') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jen_kel') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jen_kel')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Agama -->
                        <div>
                            <label for="agama" class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                            <select name="agama" id="agama"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Agama</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                            @error('agama')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- No Telepon -->
                        <div>
                            <label for="no_tlp" class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon</label>
                            <input type="text" name="no_tlp" id="no_tlp" value="{{ old('no_tlp') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Contoh: 08123456789">
                            @error('no_tlp')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Nama Ayah -->
                        <div>
                            <label for="nm_ayah" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ayah</label>
                            <input type="text" name="nm_ayah" id="nm_ayah" value="{{ old('nm_ayah') }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Nama lengkap ayah">
                            @error('nm_ayah')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="almt_siswa" class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
                            <textarea name="almt_siswa" id="almt_siswa" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500"
                                placeholder="Alamat lengkap">{{ old('almt_siswa') }}</textarea>
                            @error('almt_siswa')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Akademik</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kelas -->
                        <div>
                            <label for="kelas_id" class="block text-sm font-medium text-slate-300 mb-1.5">Kelas</label>
                            <select name="kelas_id" id="kelas_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nm_kls }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Jurusan -->
                        <div>
                            <label for="m_jurusan_id"
                                class="block text-sm font-medium text-slate-300 mb-1.5">Jurusan</label>
                            <select name="m_jurusan_id" id="m_jurusan_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('m_jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                            @error('m_jurusan_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Is Active -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
                    <label for="is_active" class="text-sm text-slate-300">Siswa Aktif</label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all">
                        Simpan
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