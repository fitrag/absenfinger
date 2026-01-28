@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-bold text-white">Pengaturan Sistem</h2>
            <p class="text-sm text-slate-400 mt-1">Atur informasi sistem dan sekolah yang akan ditampilkan di panel dan
                laporan</p>
        </div>

        <!-- Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- System Settings -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Pengaturan Sistem
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Sistem</label>
                        <input type="text" name="system_name" value="{{ $settings['system_name'] ?? 'AbsenFinger' }}"
                            placeholder="Nama sistem yang ditampilkan di sidebar"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        <p class="text-xs text-slate-500 mt-1">Nama ini akan ditampilkan di sidebar panel.</p>
                    </div>
                    <div class="md:col-span-2 md:max-w-md">
                        <!-- Preview -->
                        <label class="block text-sm font-medium text-slate-300 mb-2">Preview Sidebar</label>
                        <div class="p-4 bg-slate-800/70 rounded-xl flex items-center gap-3">
                            @if($settings['school_logo'] ?? null)
                                <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Logo"
                                    class="w-8 h-8 object-contain rounded-lg bg-white p-0.5">
                            @else
                                <div
                                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h1 class="text-base font-bold text-white">{{ $settings['system_name'] ?? 'AbsenFinger' }}
                                </h1>
                                <p class="text-xs text-slate-400 -mt-0.5">Admin Panel</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Year Settings -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Tahun Pelajaran & Semester
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran Aktif</label>
                        <select name="active_academic_year"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-teal-500/50 focus:ring-1 focus:ring-teal-500/50">
                            <option value="">-- Pilih Tahun Pelajaran --</option>
                            @foreach($tahunPelajaranList as $tp)
                                <option value="{{ $tp->id }}" {{ ($settings['active_academic_year'] ?? '') == $tp->id ? 'selected' : '' }}>{{ $tp->nm_tp }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Pilih tahun pelajaran yang sedang berjalan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Semester Aktif</label>
                        <select name="active_semester"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-teal-500/50 focus:ring-1 focus:ring-teal-500/50">
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil" {{ ($settings['active_semester'] ?? '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ ($settings['active_semester'] ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Semester yang sedang berjalan</p>
                    </div>
                </div>
            </div>

            <!-- Logo Section (Smaller) -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Logo Sekolah
                </h3>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @if($settings['school_logo'] ?? null)
                            <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Logo Sekolah"
                                class="w-20 h-20 object-contain rounded-xl bg-white p-1.5">
                        @else
                            <div class="w-20 h-20 rounded-xl bg-slate-800 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Upload Logo</label>
                            <input type="file" name="school_logo" accept="image/*"
                                class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-500 file:text-white hover:file:bg-blue-600 file:cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, GIF. Maks 2MB.</p>
                        </div>
                        @if($settings['school_logo'] ?? null)
                            <a href="{{ route('admin.settings.remove-logo') }}"
                                class="inline-flex items-center gap-1 text-xs text-red-400 hover:text-red-300"
                                onclick="return confirm('Yakin ingin menghapus logo?')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Logo
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kop Surat Section (Image Only) -->
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Kop Surat untuk Laporan
                    </h3>
                    <p class="text-sm text-slate-400 mb-4">Upload gambar kop surat yang akan ditampilkan pada laporan resmi.</p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Upload Form -->
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @if($settings['kop_image'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['kop_image']) }}" alt="Gambar Kop"
                                        class="w-24 h-24 object-contain rounded-lg bg-white p-2 border-2 border-purple-500/20">
                                @else
                                    <div class="w-24 h-24 rounded-lg bg-slate-800 flex items-center justify-center border-2 border-dashed border-slate-700">
                                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-300 mb-1">Upload Gambar Kop Surat</label>
                                <input type="file" name="kop_image" accept="image/*"
                                    class="block w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-purple-500 file:text-white hover:file:bg-purple-600 file:cursor-pointer">
                                <p class="text-xs text-slate-500 mt-1">Gambar kop surat lengkap untuk laporan. Format: JPG, PNG, GIF. Maks 2MB.</p>
                                @if($settings['kop_image'] ?? null)
                                    <a href="{{ route('admin.settings.remove-kop-image') }}"
                                        class="inline-flex items-center gap-1 text-xs text-red-400 hover:text-red-300 mt-2"
                                        onclick="return confirm('Yakin ingin menghapus gambar kop?')">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Gambar Kop
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Preview -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Preview di Laporan</label>
                            <div class="p-4 bg-white rounded-xl">
                                @if($settings['kop_image'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['kop_image']) }}" alt="Preview Kop" 
                                        class="w-full h-auto object-contain">
                                @else
                                    <div class="text-center py-8 text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs">Upload gambar kop untuk melihat preview</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Information -->
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Informasi Sekolah
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1">Nama Sekolah *</label>
                            <input type="text" name="school_name" value="{{ $settings['school_name'] ?? '' }}" required
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-300 mb-1">Alamat Sekolah</label>
                            <textarea name="school_address" rows="2"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">{{ $settings['school_address'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Telepon</label>
                            <input type="text" name="school_phone" value="{{ $settings['school_phone'] ?? '' }}"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                            <input type="email" name="school_email" value="{{ $settings['school_email'] ?? '' }}"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Website</label>
                            <input type="text" name="school_website" value="{{ $settings['school_website'] ?? '' }}"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Principal Information -->
                <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Kepala Sekolah
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Nama Kepala Sekolah</label>
                            <input type="text" name="principal_name" value="{{ $settings['principal_name'] ?? '' }}"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">NIP</label>
                            <input type="text" name="principal_nip" value="{{ $settings['principal_nip'] ?? '' }}"
                                class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed bottom-4 right-4 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
@endsection