@extends('layouts.admin')

@section('title', 'Grafik Presensi')
@section('page-title', 'Grafik Presensi')

@section('content')
    <div class="space-y-6" x-data="grafikPresensi()">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Grafik Presensi</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-indigo-200 text-sm mt-1">NIS: {{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form id="filterForm" action="{{ route('siswa.presensi.grafik') }}" method="GET"
                class="flex flex-wrap items-end gap-4">
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

        <!-- Legend -->
        <div class="flex items-center justify-center gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-0.5 bg-emerald-400 rounded"></span>
                <span class="text-slate-400">Jam Masuk</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-0.5 bg-purple-400 rounded" style="border-bottom: 2px dashed #a78bfa;"></span>
                <span class="text-slate-400">Jam Pulang</span>
            </div>
        </div>

        <!-- Chart -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 sm:p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-medium text-slate-300">Grafik Waktu Presensi</h3>
                <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</span>
            </div>

            @php
                $chartData = $chartDataArray;
                $dataCount = count($chartData);
                $chartWidth = 800;
                $chartHeight = 300;
                $paddingLeft = 50;
                $paddingRight = 20;
                $paddingTop = 20;
                $paddingBottom = 60;
                $graphWidth = $chartWidth - $paddingLeft - $paddingRight;
                $graphHeight = $chartHeight - $paddingTop - $paddingBottom;

                // Time range: 5:00 to 18:00 (300 to 1080 minutes)
                $minTime = 300; // 5:00
                $maxTime = 1080; // 18:00
                $timeRange = $maxTime - $minTime;

                // Calculate positions
                $masukPoints = [];
                $pulangPoints = [];

                foreach ($chartData as $i => $data) {
                    $x = $paddingLeft + ($graphWidth / max(1, $dataCount - 1)) * $i;
                    if ($dataCount === 1) {
                        $x = $paddingLeft + $graphWidth / 2;
                    }

                    if ($data['masuk'] !== null) {
                        $y = $paddingTop + $graphHeight - (($data['masuk'] - $minTime) / $timeRange) * $graphHeight;
                        $masukPoints[] = ['x' => $x, 'y' => max($paddingTop, min($paddingTop + $graphHeight, $y)), 'time' => $data['masuk_time'], 'date' => $data['date'], 'day' => $data['day']];
                    }

                    if ($data['pulang'] !== null) {
                        $y = $paddingTop + $graphHeight - (($data['pulang'] - $minTime) / $timeRange) * $graphHeight;
                        $pulangPoints[] = ['x' => $x, 'y' => max($paddingTop, min($paddingTop + $graphHeight, $y)), 'time' => $data['pulang_time'], 'date' => $data['date'], 'day' => $data['day']];
                    }
                }

                // Generate smooth curve path
                function generateSmoothPath($points)
                {
                    if (count($points) < 2)
                        return '';

                    $path = "M {$points[0]['x']} {$points[0]['y']}";

                    for ($i = 0; $i < count($points) - 1; $i++) {
                        $p0 = $points[max(0, $i - 1)];
                        $p1 = $points[$i];
                        $p2 = $points[$i + 1];
                        $p3 = $points[min(count($points) - 1, $i + 2)];

                        $cp1x = $p1['x'] + ($p2['x'] - $p0['x']) / 6;
                        $cp1y = $p1['y'] + ($p2['y'] - $p0['y']) / 6;
                        $cp2x = $p2['x'] - ($p3['x'] - $p1['x']) / 6;
                        $cp2y = $p2['y'] - ($p3['y'] - $p1['y']) / 6;

                        $path .= " C {$cp1x} {$cp1y}, {$cp2x} {$cp2y}, {$p2['x']} {$p2['y']}";
                    }

                    return $path;
                }

                $masukPath = generateSmoothPath($masukPoints);
                $pulangPath = generateSmoothPath($pulangPoints);
            @endphp

            <div class="overflow-x-auto">
                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full min-w-[600px]" style="height: 300px;">
                    <defs>
                        <!-- Masuk gradient -->
                        <linearGradient id="masukGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color: #10b981; stop-opacity: 0.3" />
                            <stop offset="100%" style="stop-color: #10b981; stop-opacity: 0" />
                        </linearGradient>
                        <!-- Pulang gradient -->
                        <linearGradient id="pulangGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color: #a78bfa; stop-opacity: 0.3" />
                            <stop offset="100%" style="stop-color: #a78bfa; stop-opacity: 0" />
                        </linearGradient>
                    </defs>

                    <!-- Grid lines and Y-axis labels -->
                    @for ($hour = 6; $hour <= 17; $hour += 2)
                        @php
                            $minutes = $hour * 60;
                            $yPos = $paddingTop + $graphHeight - (($minutes - $minTime) / $timeRange) * $graphHeight;
                        @endphp
                        <line x1="{{ $paddingLeft }}" y1="{{ $yPos }}" x2="{{ $chartWidth - $paddingRight }}" y2="{{ $yPos }}"
                            stroke="#334155" stroke-width="1" stroke-dasharray="4,4" />
                        <text x="{{ $paddingLeft - 8 }}" y="{{ $yPos + 4 }}" text-anchor="end" class="fill-slate-500 text-xs">
                            {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                        </text>
                    @endfor

                    <!-- X-axis labels -->
                    @foreach ($chartData as $i => $data)
                        @php
                            $x = $paddingLeft + ($graphWidth / max(1, $dataCount - 1)) * $i;
                            if ($dataCount === 1) {
                                $x = $paddingLeft + $graphWidth / 2;
                            }
                        @endphp
                        <text x="{{ $x }}" y="{{ $chartHeight - $paddingBottom + 20 }}" text-anchor="middle"
                            class="fill-slate-400 text-xs">
                            {{ $data['date'] }}
                        </text>
                        <text x="{{ $x }}" y="{{ $chartHeight - $paddingBottom + 35 }}" text-anchor="middle"
                            class="fill-slate-500 text-xs">
                            {{ $data['day'] }}
                        </text>
                    @endforeach

                    <!-- Masuk area fill -->
                    @if(count($masukPoints) >= 2)
                        <path
                            d="{{ $masukPath }} L {{ end($masukPoints)['x'] }} {{ $paddingTop + $graphHeight }} L {{ $masukPoints[0]['x'] }} {{ $paddingTop + $graphHeight }} Z"
                            fill="url(#masukGradient)" />
                    @endif

                    <!-- Pulang area fill -->
                    @if(count($pulangPoints) >= 2)
                        <path
                            d="{{ $pulangPath }} L {{ end($pulangPoints)['x'] }} {{ $paddingTop + $graphHeight }} L {{ $pulangPoints[0]['x'] }} {{ $paddingTop + $graphHeight }} Z"
                            fill="url(#pulangGradient)" />
                    @endif

                    <!-- Masuk line -->
                    @if(count($masukPoints) >= 2)
                        <path d="{{ $masukPath }}" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round" />
                    @endif

                    <!-- Pulang line -->
                    @if(count($pulangPoints) >= 2)
                        <path d="{{ $pulangPath }}" fill="none" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round" stroke-dasharray="8,4" />
                    @endif

                    <!-- Masuk points -->
                    @foreach ($masukPoints as $point)
                        <g class="cursor-pointer" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="6" fill="#10b981" stroke="#064e3b"
                                stroke-width="2" />
                            <g x-show="show" x-cloak>
                                <rect x="{{ $point['x'] - 40 }}" y="{{ $point['y'] - 45 }}" width="80" height="38" rx="6"
                                    fill="#1e293b" stroke="#334155" />
                                <text x="{{ $point['x'] }}" y="{{ $point['y'] - 30 }}" text-anchor="middle"
                                    class="fill-emerald-400 text-xs font-medium">Masuk</text>
                                <text x="{{ $point['x'] }}" y="{{ $point['y'] - 15 }}" text-anchor="middle"
                                    class="fill-white text-sm font-bold">{{ $point['time'] }}</text>
                            </g>
                        </g>
                    @endforeach

                    <!-- Pulang points -->
                    @foreach ($pulangPoints as $point)
                        <g class="cursor-pointer" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="6" fill="#a78bfa" stroke="#5b21b6"
                                stroke-width="2" />
                            <g x-show="show" x-cloak>
                                <rect x="{{ $point['x'] - 40 }}" y="{{ $point['y'] - 45 }}" width="80" height="38" rx="6"
                                    fill="#1e293b" stroke="#334155" />
                                <text x="{{ $point['x'] }}" y="{{ $point['y'] - 30 }}" text-anchor="middle"
                                    class="fill-purple-400 text-xs font-medium">Pulang</text>
                                <text x="{{ $point['x'] }}" y="{{ $point['y'] - 15 }}" text-anchor="middle"
                                    class="fill-white text-sm font-bold">{{ $point['time'] }}</text>
                            </g>
                        </g>
                    @endforeach

                    @if(count($masukPoints) === 0 && count($pulangPoints) === 0)
                        <text x="{{ $chartWidth / 2 }}" y="{{ $chartHeight / 2 }}" text-anchor="middle"
                            class="fill-slate-500 text-sm">
                            Tidak ada data presensi pada periode ini
                        </text>
                    @endif
                </svg>
            </div>
        </div>

        <!-- Summary Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/50">
                <h3 class="text-sm font-medium text-slate-300">Detail Waktu Presensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Hari</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-400 uppercase">Jam Masuk
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-purple-400 uppercase">Jam Pulang
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach ($chartData as $data)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-white">{{ $data['date'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $data['day'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($data['masuk_time'])
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            {{ $data['masuk_time'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($data['pulang_time'])
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                            {{ $data['pulang_time'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function grafikPresensi() {
            return {
                submitForm() {
                    document.getElementById('filterForm').submit();
                }
            }
        }
    </script>
@endpush