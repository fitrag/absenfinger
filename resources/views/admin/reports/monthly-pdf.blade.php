<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Per-Periode Bulan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            margin: 10px 10px 10px 30px;
            line-height: 1.1;
        }

        h1 {
            text-align: center;
            font-size: 12px;
            margin: 0 0 5px 0;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .info {
            margin-bottom: 8px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 2px 1px;
            text-align: center;
            line-height: 1;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        .name-col {
            text-align: left;
            white-space: nowrap;
            padding-left: 3px;
        }

        .percentage {
            background-color: #dbeafe;
            font-weight: bold;
        }

        .total {
            background-color: #fed7aa;
            font-weight: bold;
        }

        .legend {
            margin-top: 8px;
            font-size: 8px;
            line-height: 1.2;
        }

        .signature {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            line-height: 1.2;
        }

        .signature p {
            margin: 0;
        }
    </style>
</head>

<body>
    <h1>LAPORAN PRESENSI PER-PERIODE BULAN</h1>
    <p class="subtitle">
        Periode: {{ \Carbon\Carbon::parse($startMonth)->format('F Y') }} s.d
        {{ \Carbon\Carbon::parse($endMonth)->format('F Y') }}
    </p>

    @if($kelasInfo)
        <div class="info">
            <strong>Kelas:</strong> {{ $kelasInfo->nm_kls }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>H%</th>
                <th>S</th>
                <th>I</th>
                <th>A</th>
                <th>B</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $summary = $monthlySummary[$student->nis] ?? [
                        'hadir' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'alpha' => 0,
                        'bolos' => 0,
                        'percentage' => 0,
                        'total_absent' => 0
                    ];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nis }}</td>
                    <td class="name-col">{{ $student->name }}</td>
                    <td>{{ $student->kelas->nm_kls ?? '-' }}</td>
                    <td class="percentage">{{ $summary['percentage'] }}%</td>
                    <td>{{ $summary['sakit'] }}</td>
                    <td>{{ $summary['izin'] }}</td>
                    <td>{{ $summary['alpha'] }}</td>
                    <td>{{ $summary['bolos'] }}</td>
                    <td class="total">{{ $summary['total_absent'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <strong>Keterangan:</strong> H% = Persentase Kehadiran (dihitung dari tanggal yang ada data absensi)<br>
        S = Sakit | I = Izin | A = Alpha | B = Bolos | Total = Jumlah S + I + A + B
    </div>

    <div class="signature">
        <p>{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <p>Wali Kelas,</p>
        <br><br><br>
        <p>____________________________</p>
        @if($walas && $walas->guru)
            <p>{{ $walas->guru->nama }}</p>
            @if($walas->guru->nip)
                <p style="font-size: 8px;">NIP: {{ $walas->guru->nip }}</p>
            @endif
        @else
            <p>_________________________</p>
        @endif
    </div>
</body>

</html>