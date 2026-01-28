@extends('layouts.admin')

@section('title', 'Rekap Siswa Terlambat')
@section('page-title', 'Rekap Siswa Terlambat')

@push('styles')
    <style>
        /* Blink animation for students with 3+ violations */
        @keyframes blink-red {
            0%, 100% {
                background-color: rgba(239, 68, 68, 0.3);
                border-color: rgba(239, 68, 68, 0.6);
            }
            50% {
                background-color: rgba(239, 68, 68, 0.1);
                border-color: rgba(239, 68, 68, 0.3);
            }
        }

        .blink-warning {
            animation: blink-red 1.5s ease-in-out infinite;
            border-left: 4px solid rgb(239, 68, 68) !important;
        }

        .blink-warning td {
            color: #fca5a5 !important;
        }

        .blink-warning .nama-siswa {
            color: #f87171 !important;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Rekap Keterlambatan Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Data siswa diurutkan berdasarkan jumlah keterlambatan terbanyak</p>
            </div>
            <a href="{{ route('admin.kesiswaan.siswa-terlambat.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Data Detail
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Info Box -->
        <div class="bg-amber-500/10 border border-amber-500/30 px-4 py-3 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="text-sm text-amber-400 font-medium">Perhatian!</p>
                <p class="text-xs text-amber-400/80 mt-0.5">Siswa dengan keterlambatan ≥ 3 kali akan ditandai dengan warna merah berkedip.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form id="filterForm" action="{{ route('admin.kesiswaan.siswa-terlambat.rekap') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500 flex-1 min-w-[200px]">

                <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                    class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan.siswa-terlambat.rekap') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Reset
                </a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $rekapSiswa->count() }}</p>
                        <p class="text-xs text-slate-400">Total Siswa Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $rekapSiswa->sum('total_terlambat') }}</p>
                        <p class="text-xs text-slate-400">Total Kejadian Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $rekapSiswa->where('total_terlambat', '>=', 3)->count() }}</p>
                        <p class="text-xs text-slate-400">Siswa ≥ 3x Terlambat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Terlambat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Menit</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($rekapSiswa as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition-colors {{ $item->total_terlambat >= 3 ? 'blink-warning' : '' }}">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $item->student->nis ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm nama-siswa {{ $item->total_terlambat >= 3 ? 'text-rose-400' : 'text-white' }} font-medium">
                                    {{ $item->student->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold 
                                        {{ $item->total_terlambat >= 3 ? 'bg-rose-500/30 text-rose-300 border border-rose-500/50' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                        {{ $item->total_terlambat }}x
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700/50 text-slate-300 border border-slate-600/50">
                                        {{ $item->total_menit }} menit
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->total_terlambat >= 3)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Perlu Perhatian
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada data siswa terlambat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
