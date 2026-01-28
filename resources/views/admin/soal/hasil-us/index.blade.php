@extends('layouts.admin')
@section('title', 'Hasil US')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Hasil US</h1>
                <p class="text-slate-400 text-sm mt-1">Lihat hasil Ujian Sekolah (US)</p>
            </div>
        </div>
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="tingkat" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                    <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                    <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                </select>
                <select name="semester" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </form>
        </div>
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Nama Ujian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Tingkat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Jumlah Hasil</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">@forelse($ujians as $i => $u)<tr class="hover:bg-slate-800/30">
                    <td class="px-4 py-3 text-sm text-slate-400">{{ $ujians->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-sm text-white">{{ $u->nama_ujian }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->mapel->nm_mapel ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->tingkat ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->tanggal->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->hasil_uss_count ?? $u->hasilUss->count() }}
                        siswa</td>
                    <td class="px-4 py-3 text-center"><a href="{{ route('admin.soal.hasil-us.show', $u->id) }}"
                            class="px-3 py-1.5 bg-purple-500/20 text-purple-400 rounded-lg text-xs">Lihat Hasil</a></td>
                </tr>@empty<tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">Belum ada hasil ujian US</td>
                    </tr>@endforelse</tbody>
            </table>
            @if($ujians->hasPages())
            <div class="px-4 py-3 border-t border-slate-700/50">{{ $ujians->links() }}</div>@endif
        </div>
    </div>
@endsection