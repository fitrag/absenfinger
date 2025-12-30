@extends('layouts.admin')

@section('title', 'Belum Terdaftar PKL')

@section('page-title', 'Absen PKL')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="w-20 h-20 rounded-2xl bg-amber-500/20 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-white mb-3">Belum Terdaftar PKL</h2>
        <p class="text-slate-400 max-w-md mb-6">
            Anda belum terdaftar dalam program PKL. Silakan hubungi admin atau pembimbing untuk mendaftarkan Anda ke program
            PKL.
        </p>

        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
            Kembali ke Dashboard
        </a>
    </div>
@endsection