@extends('layouts.admin')
@section('title', 'Detail Hasil MID')
@section('content')
    <div class="space-y-6" x-data="hasilMidShow()">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $ujian->nama_ujian }}</h1>
                <p class="text-slate-400 text-sm mt-1">{{ $ujian->mapel->nm_mapel ?? '' }} -
                    {{ $ujian->kelas->nm_kls ?? '' }} | {{ $ujian->tanggal->format('d M Y') }}</p>
            </div>
            <a href="{{ route('admin.soal.hasil-mid.index') }}"
                class="px-4 py-2 bg-slate-700 text-white rounded-xl">Kembali</a>
        </div>
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <form action="{{ route('admin.soal.hasil-mid.bulkStore') }}" method="POST">@csrf<input type="hidden"
                    name="ujian_mid_id" value="{{ $ujian->id }}">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">NISN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Nama Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400">Nilai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">@foreach($students as $i => $s)<tr
                        class="hover:bg-slate-800/30">
                        <td class="px-4 py-3 text-sm text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $s->nisn }}</td>
                        <td class="px-4 py-3 text-sm text-white">{{ $s->nama }}<input type="hidden"
                                name="hasil[{{ $i }}][student_id]" value="{{ $s->id }}"></td>
                        <td class="px-4 py-3 text-center"><input type="number" name="hasil[{{ $i }}][nilai]" min="0"
                                max="100" step="0.01" value="{{ $hasilMap[$s->id]->nilai ?? '' }}"
                                class="w-20 px-2 py-1 bg-slate-800 border border-slate-700 rounded text-white text-center text-sm">
                        </td>
                        <td class="px-4 py-3"><input type="text" name="hasil[{{ $i }}][catatan]"
                                value="{{ $hasilMap[$s->id]->catatan ?? '' }}"
                                class="w-full px-2 py-1 bg-slate-800 border border-slate-700 rounded text-white text-sm">
                        </td>
                    </tr>@endforeach</tbody>
                </table>
                <div class="px-4 py-3 border-t border-slate-700/50 flex justify-end"><button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-xl">Simpan Nilai</button></div>
            </form>
        </div>
    </div>
    <script>function hasilMidShow() { return {} }</script>
    @if(session('success'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl z-50">{{ session('success') }}</div>
    @endif
@endsection
