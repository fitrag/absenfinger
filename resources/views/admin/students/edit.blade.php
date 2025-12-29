@extends('layouts.admin')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
    <div class="max-w-4xl mx-auto">
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
                        <p class="text-sm text-slate-400 mt-1">Perbarui informasi siswa</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <!-- Data Identitas -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Identitas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="finger_id" class="block text-sm font-medium text-slate-300 mb-1.5">Finger ID <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="finger_id" id="finger_id"
                                value="{{ old('finger_id', $student->finger_id) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('finger_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nis" class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="nis" id="nis" value="{{ old('nis', $student->nis) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('nis')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nisn" class="block text-sm font-medium text-slate-300 mb-1.5">NISN</label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('nisn')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Pribadi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span
                                    class="text-rose-400">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="tmpt_lhr" class="block text-sm font-medium text-slate-300 mb-1.5">Tempat
                                Lahir</label>
                            <input type="text" name="tmpt_lhr" id="tmpt_lhr"
                                value="{{ old('tmpt_lhr', $student->tmpt_lhr) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('tmpt_lhr')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="tgl_lhr" class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal
                                Lahir</label>
                            <input type="date" name="tgl_lhr" id="tgl_lhr"
                                value="{{ old('tgl_lhr', $student->tgl_lhr?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('tgl_lhr')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="jen_kel" class="block text-sm font-medium text-slate-300 mb-1.5">Jenis
                                Kelamin</label>
                            <select name="jen_kel" id="jen_kel"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jen_kel', $student->jen_kel) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('jen_kel', $student->jen_kel) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            @error('jen_kel')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="agama" class="block text-sm font-medium text-slate-300 mb-1.5">Agama</label>
                            <select name="agama" id="agama"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Agama</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $student->agama) == $agama ? 'selected' : '' }}>
                                        {{ $agama }}</option>
                                @endforeach
                            </select>
                            @error('agama')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="no_tlp" class="block text-sm font-medium text-slate-300 mb-1.5">No. Telepon</label>
                            <input type="text" name="no_tlp" id="no_tlp" value="{{ old('no_tlp', $student->no_tlp) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('no_tlp')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nm_ayah" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Ayah</label>
                            <input type="text" name="nm_ayah" id="nm_ayah" value="{{ old('nm_ayah', $student->nm_ayah) }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            @error('nm_ayah')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="almt_siswa" class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
                            <textarea name="almt_siswa" id="almt_siswa" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">{{ old('almt_siswa', $student->almt_siswa) }}</textarea>
                            @error('almt_siswa')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="border-b border-slate-800/50 pb-4 mb-4">
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Data Akademik</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kelas_id" class="block text-sm font-medium text-slate-300 mb-1.5">Kelas</label>
                            <select name="kelas_id" id="kelas_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ old('kelas_id', $student->kelas_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="m_jurusan_id"
                                class="block text-sm font-medium text-slate-300 mb-1.5">Jurusan</label>
                            <select name="m_jurusan_id" id="m_jurusan_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ old('m_jurusan_id', $student->m_jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                            @error('m_jurusan_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
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