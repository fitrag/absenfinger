@extends('layouts.admin')

@section('title', 'Data Siswa Terlambat')
@section('page-title', 'Data Siswa Terlambat')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Siswa Terlambat</h2>
                <p class="text-sm text-slate-400 mt-1">Daftar siswa yang terlambat, diurutkan berdasarkan frekuensi</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-3">
            <form action="{{ route('admin.kesiswaan-view.siswa-terlambat') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="flex-1 min-w-[180px] px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 placeholder-slate-500">

                <select name="kelas_id" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors text-sm cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan-view.siswa-terlambat') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-600 transition-colors text-sm">
                    Reset
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-900">
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-rose-400 uppercase tracking-wider">
                                Jumlah Terlambat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-amber-400 uppercase tracking-wider">
                                Total Menit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($groupedData as $index => $data)
                            <tr
                                class="hover:bg-slate-800/30 transition-colors {{ $data['total_terlambat'] >= 3 ? 'bg-rose-500/10' : '' }}">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $data['student_name'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $data['student_nis'] }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $data['student_kelas'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold {{ $data['total_terlambat'] >= 3 ? 'bg-rose-500/30 text-rose-300 border border-rose-500/50' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        {{ $data['total_terlambat'] }}x
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        {{ $data['total_menit'] }} menit
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data siswa terlambat</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection