<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi PKL - {{ $dudi->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 11px;
            color: #555;
        }

        .info-section {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }

        .info-section .left,
        .info-section .right {
            width: 48%;
        }

        .info-item {
            display: flex;
            margin-bottom: 4px;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: center;
            font-size: 10px;
        }

        table th {
            background: #f0f0f0;
            font-weight: 600;
        }

        table th.name-col {
            text-align: left;
            width: 180px;
        }

        table td.name-col {
            text-align: left;
        }

        .status-h {
            color: #16a34a;
            font-weight: bold;
        }

        .status-s {
            color: #d97706;
            font-weight: bold;
        }

        .status-i {
            color: #2563eb;
            font-weight: bold;
        }

        .status-a {
            color: #dc2626;
            font-weight: bold;
        }

        .status-x {
            color: #9ca3af;
        }

        .date-header {
            font-size: 9px;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            white-space: nowrap;
        }

        .legend {
            margin-top: 15px;
            font-size: 10px;
        }

        .legend-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .legend-items {
            display: flex;
            gap: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn:hover {
            background: #2563eb;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak</button>

    <div class="header">
        <h1>LAPORAN ABSENSI PKL</h1>
        <h2>{{ $dudi->nama }}</h2>
        <p>{{ $dudi->alamat ?? '-' }}</p>
    </div>

    <div class="info-section">
        <div class="left">
            <div class="info-item">
                <span class="info-label">Tahun Pelajaran</span>
                <span class="info-value">: {{ $selectedTp->nm_tp ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jumlah Siswa</span>
                <span class="info-value">: {{ count($attendanceMatrix) }} siswa</span>
            </div>
        </div>
        <div class="right">
            <div class="info-item">
                <span class="info-label">Periode</span>
                <span class="info-value">: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="name-col">Nama Siswa</th>
                <th style="width: 70px">Kelas</th>
                @foreach($dates as $date)
                    <th style="width: 25px">
                        <div class="date-header">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                    </th>
                @endforeach
                <th style="width: 30px" class="status-h">H</th>
                <th style="width: 30px" class="status-s">S</th>
                <th style="width: 30px" class="status-i">I</th>
                <th style="width: 30px" class="status-a">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceMatrix as $index => $student)
                @php
                    $totalH = 0;
                    $totalS = 0;
                    $totalI = 0;
                    $totalA = 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="name-col">{{ $student['pkl']->student->name }}</td>
                    <td>{{ $student['pkl']->student->kelas->nm_kls ?? '-' }}</td>
                    @foreach($dates as $date)
                        @php
                            $dayData = $student['dates'][$date] ?? null;
                            $status = $dayData['status'] ?? 'Belum Absen';

                            $statusCode = match ($status) {
                                'Lengkap', 'Belum Pulang' => '✓',
                                'Sakit' => 'S',
                                'Izin' => 'I',
                                'Alpha' => 'A',
                                default => '-',
                            };

                            $statusClass = match ($status) {
                                'Lengkap', 'Belum Pulang' => 'status-h',
                                'Sakit' => 'status-s',
                                'Izin' => 'status-i',
                                'Alpha' => 'status-a',
                                default => 'status-x',
                            };

                            // Count totals
                            if ($status === 'Lengkap' || $status === 'Belum Pulang')
                                $totalH++;
                            elseif ($status === 'Sakit')
                                $totalS++;
                            elseif ($status === 'Izin')
                                $totalI++;
                            elseif ($status === 'Alpha')
                                $totalA++;
                        @endphp
                        <td class="{{ $statusClass }}">{{ $statusCode }}</td>
                    @endforeach
                    <td class="status-h">{{ $totalH }}</td>
                    <td class="status-s">{{ $totalS }}</td>
                    <td class="status-i">{{ $totalI }}</td>
                    <td class="status-a">{{ $totalA }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <div class="legend-title">Keterangan:</div>
        <div class="legend-items">
            <div class="legend-item"><span class="status-h">✓</span> = Hadir</div>
            <div class="legend-item"><span class="status-s">S</span> = Sakit</div>
            <div class="legend-item"><span class="status-i">I</span> = Izin</div>
            <div class="legend-item"><span class="status-a">A</span> = Alpha</div>
            <div class="legend-item"><span class="status-x">-</span> = Belum Absen</div>
        </div>
    </div>

    <div class="footer">
        <div class="signature">
            <p>{{ now()->format('d F Y') }}</p>
            <p>Mengetahui,</p>
            <div class="signature-line">
                <strong>Administrator</strong>
            </div>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>