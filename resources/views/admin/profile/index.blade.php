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
        <div class="flex gap-2 mb-6">
            <button @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Profile
            </button>
            <button @click="activeTab = 'password'"
                :class="activeTab === 'password' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'"
                class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Ubah Password
            </button>
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
                        <div class="relative">
                            @if ($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Profile Photo"
                                    class="w-24 h-24 rounded-xl object-cover border-2 border-slate-700">
                            @else
                                <div
                                    class="w-24 h-24 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Foto Profile</label>
                            <input type="file" name="foto" accept="image/*"
                                class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer cursor-pointer">
                            <p class="mt-1 text-xs text-slate-500">JPG, PNG, GIF. Maks 2MB.</p>
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
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
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