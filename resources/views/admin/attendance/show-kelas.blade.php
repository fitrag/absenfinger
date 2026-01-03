@extends('layouts.admin')

@section('title', 'Detail Presensi Kelas ' . $kelas->nm_kls)
@section('page-title', 'Detail Presensi Kelas')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Presensi Kelas {{ $kelas->nm_kls }}</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.attendance.index', ['date' => $date]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-4">
            <form action="{{ route('admin.attendance.showByKelas', $kelas->id) }}" method="GET" class="flex flex-wrap items-center gap-3">
                <!-- Date -->
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 date-white-icon">

                <!-- Kelas -->
                <select name="kelasId" onchange="window.location.href='{{ route('admin.attendance.showByKelas', ['kelasId' => '__KELAS_ID__', 'date' => $date]) }}'.replace('__KELAS_ID__', this.value)"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $kelas->id == $k->id ? 'selected' : '' }}>
                            {{ $k->nm_kls }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Stats -->
        @php
            $hadir = 0; $sakit = 0; $izin = 0; $alpha = 0; $bolos = 0; $terlambat = 0; $tidakAbsen = 0;
            foreach($students as $student) {
                $attendance = $attendanceData[$student->nis] ?? null;
                $checkIn = $attendance['check_in'] ?? null;
                $checkOut = $attendance['check_out'] ?? null;
                
                if (!$checkIn) {
                    $tidakAbsen++;
                } elseif ($checkIn->checktype == 2) {
                    $sakit++;
                } elseif ($checkIn->checktype == 3) {
                    $izin++;
                } elseif ($checkIn->checktype == 4) {
                    $alpha++;
                } elseif (!$checkOut) {
                    $bolos++;
                } elseif ($checkIn->checktime->format('H:i') > '07:00') {
                    $terlambat++;
                } else {
                    $hadir++;
                }
            }
        @endphp
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="px-3 py-1.5 rounded-xl bg-slate-800/50 border border-slate-700/50 text-slate-300">
                Total: <span class="font-semibold text-white">{{ $students->count() }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                Hadir: <span class="font-semibold">{{ $hadir }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 border border-purple-500/30 text-purple-400">
                Sakit: <span class="font-semibold">{{ $sakit }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400">
                Izin: <span class="font-semibold">{{ $izin }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400">
                Alpha: <span class="font-semibold">{{ $alpha }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400">
                Bolos: <span class="font-semibold">{{ $bolos }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400">
                Terlambat: <span class="font-semibold">{{ $terlambat }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-500/10 border border-slate-500/30 text-slate-400">
                Tidak Absen: <span class="font-semibold">{{ $tidakAbsen }}</span>
            </span>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIS</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($students as $index => $student)
                            @php
                                $attendance = $attendanceData[$student->nis] ?? null;
                                $checkIn = $attendance['check_in'] ?? null;
                                $checkOut = $attendance['check_out'] ?? null;
                                $isLate = $checkIn && $checkIn->checktype == 0 && $checkIn->checktime->format('H:i') > '07:00';
                                $isSakit = $checkIn && $checkIn->checktype == 2;
                                $isIzin = $checkIn && $checkIn->checktype == 3;
                                $isAlpha = $checkIn && $checkIn->checktype == 4;
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $checkIn && !($isSakit || $isIzin || $isAlpha) ? ($isLate ? 'from-amber-600 to-amber-700' : 'from-emerald-600 to-emerald-700') : 'from-slate-600 to-slate-700' }} flex items-center justify-center text-white font-medium text-xs">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-white">{{ $student->name }}</span>
                                            @if($student->jen_kel)
                                                <p class="text-xs text-slate-500">
                                                    {{ $student->jen_kel == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $student->nis }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($checkIn && $checkIn->checktype == 0)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono {{ $isLate ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                            {{ $checkIn->checktime->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($checkOut)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            {{ $checkOut->checktime->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($isSakit)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">Sakit</span>
                                    @elseif($isIzin)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Izin</span>
                                    @elseif($isAlpha)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Alpha</span>
                                    @elseif($checkIn)
                                        @if(!$checkOut)
                                            <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">Bolos</span>
                                        @elseif($isLate)
                                            <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">Terlambat</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hadir</span>
                                        @endif
                                    @else
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Tidak Hadir</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data siswa di kelas ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .date-white-icon::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endsection
