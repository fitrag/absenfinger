@extends('layouts.admin')

@section('title', 'Data Konseling Siswa')
@section('page-title', 'Data Konseling Siswa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Konseling Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Daftar siswa yang menjalani konseling</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-3">
            <form action="{{ route('admin.kesiswaan-view.konseling') }}" method="GET"
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

                <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors text-sm cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.kesiswaan-view.konseling') }}"
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
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jenis</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($konselingData as $item)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $item->student->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->student->nis ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $item->jenis_konseling ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'proses' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'selesai' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        ];
                                        $statusColor = $statusColors[$item->status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                    @endphp
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium border {{ $statusColor }}">
                                        {{ ucfirst($item->status ?? 'N/A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data konseling</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($konselingData->hasPages())
                <div class="p-4 border-t border-slate-800/50">
                    {{ $konselingData->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection