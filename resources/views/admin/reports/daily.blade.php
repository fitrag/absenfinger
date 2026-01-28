@extends('layouts.admin')

@section('title', 'Laporan Per-Periode')
@section('page-title', 'Laporan Per-Periode')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Laporan Presensi Per-Periode</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s.d
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @if(isset($isWaliKelas) && $isWaliKelas && isset($walasKelasInfo) && !$isAdmin)
                        <span class="ml-2 px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded-lg text-xs">
                            Kelas {{ $walasKelasInfo->nm_kls }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2 print:hidden">
                <button onclick="openPdfModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 print:hidden">
            <form id="filterForm" action="{{ route('admin.reports.daily') }}" method="GET"
                class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        onchange="document.getElementById('filterForm').submit()"
                        class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        onchange="document.getElementById('filterForm').submit()"
                        class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Kelas</label>
                    @if(isset($isWaliKelas) && $isWaliKelas && isset($walasKelasInfo) && !$isAdmin)
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                        <div class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            {{ $walasKelasInfo->nm_kls }}
                        </div>
                    @else
                        <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                            class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                            <option value="">-</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nm_kls }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="px-5 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Print Header (only visible when printing) -->
        <div class="hidden print:block mb-4">
            <h1 class="text-xl font-bold text-center">LAPORAN PRESENSI SISWA</h1>
            <p class="text-center text-gray-600">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d
                {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            </p>
            @if($kelasId)
                @php $selectedKelas = $kelasList->find($kelasId); @endphp
                <p class="text-center text-gray-600">Kelas: {{ $selectedKelas->nm_kls ?? '-' }}</p>
            @endif
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4 text-sm print:text-xs print:gap-2 print:mb-2">
            <span class="text-slate-400">Keterangan:</span>
            <span class="flex items-center gap-1"><span class="text-emerald-400 font-bold">✓</span> <span
                    class="text-slate-300">Hadir</span></span>
            <span class="flex items-center gap-1"><span class="text-rose-400 font-bold">B</span> <span
                    class="text-slate-300">Bolos</span></span>
            <span class="flex items-center gap-1"><span class="text-purple-400 font-bold">S</span> <span
                    class="text-slate-300">Sakit</span></span>
            <span class="flex items-center gap-1"><span class="text-cyan-400 font-bold">I</span> <span
                    class="text-slate-300">Izin</span></span>
            <span class="flex items-center gap-1"><span class="text-slate-400 font-bold">A</span> <span
                    class="text-slate-300">Alpha</span></span>
            <span class="flex items-center gap-1"><span class="text-slate-500 font-bold">-</span> <span
                    class="text-slate-300">Belum Absen</span></span>
        </div>

        <!-- Table -->
        <div
            class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden print:border-black print:bg-white print:rounded-none">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/50 print:border-black print:bg-gray-100">
                            <th
                                class="px-2 py-2 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider print:text-black whitespace-nowrap sticky left-0 bg-slate-900 print:bg-gray-100">
                                No</th>
                            <th
                                class="px-2 py-2 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider print:text-black whitespace-nowrap sticky left-8 bg-slate-900 print:bg-gray-100">
                                NIS</th>
                            <th
                                class="px-2 py-2 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider print:text-black whitespace-nowrap sticky left-24 bg-slate-900 print:bg-gray-100 min-w-[150px]">
                                Nama Siswa</th>
                            @foreach($dates as $date)
                                <th
                                    class="px-1 py-2 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider print:text-black whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px]">{{ \App\Http\Controllers\Admin\ReportController::getIndonesianDay($date->dayOfWeek) }}</span>
                                        <span>{{ $date->format('d') }}</span>
                                    </div>
                                </th>
                            @endforeach
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-blue-400 uppercase tracking-wider print:text-black whitespace-nowrap bg-blue-500/10 print:bg-gray-200">
                                H%</th>
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-purple-400 uppercase tracking-wider print:text-black whitespace-nowrap">
                                S</th>
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-cyan-400 uppercase tracking-wider print:text-black whitespace-nowrap">
                                I</th>
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider print:text-black whitespace-nowrap">
                                A</th>
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-rose-400 uppercase tracking-wider print:text-black whitespace-nowrap">
                                B</th>
                            <th
                                class="px-2 py-2 text-center text-xs font-semibold text-orange-400 uppercase tracking-wider print:text-black whitespace-nowrap bg-orange-500/10 print:bg-gray-200">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 print:divide-gray-300">
                        @forelse($students as $index => $student)
                            @php
                                $matrix = $attendanceMatrix[$student->nis] ?? [];
                                $summary = $summaryData[$student->nis] ?? [
                                    'hadir' => 0,
                                    'sakit' => 0,
                                    'izin' => 0,
                                    'alpha' => 0,
                                    'bolos' => 0,
                                    'percentage' => 0,
                                    'total_absent' => 0
                                ];
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors print:hover:bg-transparent">
                                <td
                                    class="px-2 py-2 text-slate-400 print:text-black whitespace-nowrap sticky left-0 bg-slate-900/95 print:bg-white">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-2 py-2 text-slate-300 font-mono print:text-black whitespace-nowrap sticky left-8 bg-slate-900/95 print:bg-white text-xs">
                                    {{ $student->nis }}
                                </td>
                                <td
                                    class="px-2 py-2 text-white font-medium print:text-black whitespace-nowrap sticky left-24 bg-slate-900/95 print:bg-white">
                                    {{ $student->name }}
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $status = $matrix[$date->format('Y-m-d')] ?? '-';
                                        $displayStatus = $status === 'H' ? '✓' : $status;
                                        $statusClass = match ($status) {
                                            'H' => 'text-emerald-400 print:text-emerald-600',
                                            'B' => 'text-rose-400 print:text-rose-600',
                                            'S' => 'text-purple-400 print:text-purple-600',
                                            'I' => 'text-cyan-400 print:text-cyan-600',
                                            'A' => 'text-slate-400 print:text-gray-600',
                                            default => 'text-slate-600 print:text-gray-400'
                                        };
                                    @endphp
                                    <td class="px-1 py-2 text-center font-bold {{ $statusClass }}">{{ $displayStatus }}</td>
                                @endforeach
                                <td
                                    class="px-2 py-2 text-center font-bold text-blue-400 print:text-black bg-blue-500/5 print:bg-gray-50">
                                    {{ $summary['percentage'] }}%
                                </td>
                                <td class="px-2 py-2 text-center font-medium text-purple-400 print:text-black">
                                    {{ $summary['sakit'] }}
                                </td>
                                <td class="px-2 py-2 text-center font-medium text-cyan-400 print:text-black">
                                    {{ $summary['izin'] }}
                                </td>
                                <td class="px-2 py-2 text-center font-medium text-slate-400 print:text-black">
                                    {{ $summary['alpha'] }}
                                </td>
                                <td class="px-2 py-2 text-center font-medium text-rose-400 print:text-black">
                                    {{ $summary['bolos'] }}
                                </td>
                                <td
                                    class="px-2 py-2 text-center font-bold text-orange-400 print:text-black bg-orange-500/5 print:bg-gray-50">
                                    {{ $summary['total_absent'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dates) + 8 }}" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada data siswa
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Print Footer -->
        <div class="hidden print:block mt-8">
            <div class="flex justify-between items-start">
                <div class="text-sm">
                    <p>Keterangan:</p>
                    <p>H% = Persentase Kehadiran</p>
                    <p>S = Sakit, I = Izin, A = Alpha, B = Bolos</p>
                    <p>Total = Jumlah S + I + A + B</p>
                </div>
                <div class="text-center">
                    <p class="mb-16">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                    <p class="font-medium">____________________________</p>
                    <p class="text-sm text-gray-600">Kepala Sekolah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                font-size: 10px !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .print\:block {
                display: block !important;
            }

            aside,
            nav,
            .no-print {
                display: none !important;
            }

            main {
                margin: 0 !important;
                padding: 10px !important;
            }

            table {
                font-size: 9px !important;
            }

            th,
            td {
                padding: 2px 4px !important;
            }
        }
    </style>

    <!-- PDF Options Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Opsi Download PDF</h3>
                <button onclick="closePdfModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="pdfForm" action="{{ route('admin.reports.daily.pdf') }}" method="GET" target="_blank">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Ukuran Kertas</label>
                        <select name="paper_size"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            <option value="a4">A4 (210 x 297 mm)</option>
                            <option value="letter">Letter (8.5 x 11 inch)</option>
                            <option value="legal">Legal (8.5 x 14 inch)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Orientasi</label>
                        <select name="orientation"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                            <option value="portrait">Portrait (Vertikal)</option>
                            <option value="landscape" selected>Landscape (Horizontal)</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-6 mt-4 border-t border-slate-700/50">
                        <button type="button" onclick="closePdfModal()"
                            class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 hover:text-white transition-all duration-200 border border-slate-600 hover:border-slate-500 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.02] cursor-pointer">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPdfModal() {
            document.getElementById('pdfModal').classList.remove('hidden');
            document.getElementById('pdfModal').classList.add('flex');
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfModal').classList.remove('flex');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closePdfModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('pdfModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closePdfModal();
            }
        });
    </script>
@endsection