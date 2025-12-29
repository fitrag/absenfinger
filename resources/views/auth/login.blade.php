@extends('layouts.frontend')

@section('title', 'Login')

@section('content')
    <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Login Card -->
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 shadow-2xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-700/50">
                    <div>
                        @php
                            $settings = \App\Models\Setting::getAllSettings();
                            $logoPath = $settings['school_logo'] ?? null;
                            $appName = $settings['system_name'] ?? 'SIAKAD';
                        @endphp
                        <h3 class="text-lg font-bold text-white">Login {{ $appName }}</h3>
                        <p class="text-sm text-slate-400 mt-1">Masuk ke akun Anda</p>
                    </div>
                    @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-12 h-12 rounded-xl">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div
                        class="mb-6 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Masukkan username" autofocus>
                        </div>

                        <!-- Password -->
                        <div x-data="{ showPassword: false }">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="Masukkan password">
                            <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                <input type="checkbox" x-model="showPassword"
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                <span class="text-sm text-slate-400">Tampilkan password</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Masuk
                            </button>
                            <a href="{{ route('frontend.home') }}"
                                class="block text-center text-sm text-slate-400 hover:text-blue-400 transition-colors mt-3">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection