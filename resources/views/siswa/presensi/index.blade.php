@extends('layouts.admin')

@section('title', 'Riwayat Presensi')
@section('page-title', 'Riwayat Presensi')

@section('content')
    <div class="space-y-6" x-data="presensiPage()">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Riwayat Presensi</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-blue-200 text-sm mt-1">NIS: {{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form id="filterForm" action="{{ route('siswa.presensi.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                        @change="submitForm()">
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                        @change="submitForm()">
                </div>
                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-emerald-400">{{ $totalHadir }}</p>
                <p class="text-xs text-slate-400 mt-1">Hadir</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-amber-400">{{ $totalSakit }}</p>
                <p class="text-xs text-slate-400 mt-1">Sakit</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-blue-400">{{ $totalIzin }}</p>
                <p class="text-xs text-slate-400 mt-1">Izin</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-rose-400">{{ $totalAlpha }}</p>
                <p class="text-xs text-slate-400 mt-1">Alpha</p>
            </div>
        </div>

        <!-- Presensi List -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/50 flex items-center justify-between">
                <h3 class="text-sm font-medium text-slate-300">Daftar Presensi</h3>
                <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</span>
            </div>

            @if($groupedAttendances->count() > 0)
                <div class="divide-y divide-slate-800/50">
                    @foreach($groupedAttendances as $date => $records)
                        <div class="p-4">
                            <!-- Date Header -->
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center">
                                    <span class="text-lg font-bold text-blue-400">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</p>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($date)->translatedFormat('F Y') }}</p>
                                </div>
                            </div>

                            <!-- Records for this date -->
                            <div class="ml-12 space-y-2">
                                @foreach($records as $record)
                                    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-800/30">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $iconClass = match($record->checktype) {
                                                    0 => 'text-emerald-400 bg-emerald-500/20',
                                                    1 => 'text-purple-400 bg-purple-500/20',
                                                    2 => 'text-amber-400 bg-amber-500/20',
                                                    3 => 'text-blue-400 bg-blue-500/20',
                                                    4 => 'text-rose-400 bg-rose-500/20',
                                                    default => 'text-slate-400 bg-slate-500/20'
                                                };
                                                $icon = match($record->checktype) {
                                                    0 => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1', // Masuk
                                                    1 => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1', // Pulang
                                                    2 => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', // Sakit
                                                    3 => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', // Izin
                                                    4 => 'M6 18L18 6M6 6l12 12', // Alpha
                                                    default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
                                                };
                                            @endphp
                                            <div class="w-8 h-8 rounded-lg {{ $iconClass }} flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-white">{{ $record->checktype_label }}</p>
                                                <p class="text-xs text-slate-400">{{ $record->checktime->format('H:i') }} WIB</p>
                                            </div>
                                        </div>
                                        @php
                                            $badgeClass = match($record->checktype) {
                                                0 => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                1 => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                                2 => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                                3 => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                4 => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                                default => 'bg-slate-500/20 text-slate-400 border-slate-500/30'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                            {{ $record->checktype_label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-700/30 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-slate-400 text-sm">Tidak ada data presensi</p>
                    <p class="text-slate-500 text-xs mt-1">Tidak ditemukan catatan presensi pada periode ini</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function presensiPage() {
        return {
            submitForm() {
                document.getElementById('filterForm').submit();
            }
        }
    }
</script>
@endpush
