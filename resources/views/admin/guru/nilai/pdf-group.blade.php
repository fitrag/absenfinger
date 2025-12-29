<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Nilai Harian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            padding: 15px 70px;
        }

        /* KOP Sekolah */
        .kop-sekolah {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-sekolah img {
            max-width: 100%;
            height: auto;
        }

        .page-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* Info Header */
        .info-header {
            margin-bottom: 15px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 1px;
        }

        .info-cell {
            display: table-cell;
            width: 50%;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .info-value {
            display: inline-block;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 2px 2px;
        }

        .data-table th {
            background-color: #e0e0e0;
            text-align: center;
            font-weight: bold;
        }

        .data-table td.center {
            text-align: center;
        }

        .grade-cell {
            text-align: center;
            min-width: 25px;
        }

        .text-green {
            color: #000;
        }

        .text-red {
            color: #000;
        }

        /* Footer */
        .footer-avg {
            font-weight: bold;
            text-align: right;
            padding: 8px;
            background-color: #d4edda;
            border: 1px solid #333;
            margin-top: -1px;
            font-size: 11px;
        }

        /* Signature */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
        }

        .signature-box {
            text-align: center;
        }
    </style>
</head>

<body>
    {{-- KOP image disabled for testing
    @if($kopImage)
    <div class="kop-sekolah">
        <img src="{{ public_path('storage/' . $kopImage) }}" alt="Kop Sekolah">
    </div>
    @endif
    --}}

    <div class="page-title">
        LAPORAN NILAI HARIAN
    </div>

    <div class="info-header">
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">MATA PELAJARAN</span>
                <span>: </span>
                <span class="info-value">{{ $mapel->nm_mapel ?? '-' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">TP</span>
                <span>: </span>
                <span class="info-value">{{ $tp->nm_tp ?? '-' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">KELAS</span>
                <span>: </span>
                <span class="info-value">{{ $kelas->nm_kls ?? '-' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">SEMESTER</span>
                <span>: </span>
                <span class="info-value">{{ $semester ?? '-' }}</span>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th width="10%" rowspan="2">NIS</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="{{ count($harianKeList) }}">Harian Ke-</th>
                <th width="8%" rowspan="2">Rata-rata</th>
            </tr>
            <tr>
                @foreach($harianKeList as $hk)
                    <th class="grade-cell">{{ $hk }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($studentsData as $data)
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td class="center">{{ $data['student']->nis ?? '-' }}</td>
                    <td>{{ $data['student']->name ?? '-' }}</td>
                    @foreach($harianKeList as $hk)
                        <td
                            class="center {{ isset($data['grades'][$hk]) && $data['grades'][$hk] >= 75 ? 'text-green' : (isset($data['grades'][$hk]) && $data['grades'][$hk] < 75 ? 'text-red' : '') }}">
                            {{ $data['grades'][$hk] ?? '-' }}
                        </td>
                    @endforeach
                    <td class="center {{ $data['average'] >= 75 ? 'text-green' : 'text-red' }}">
                        <strong>{{ number_format($data['average'], 1) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-avg">
        Rata-Rata Kelas: {{ number_format($classAverage, 2) }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="60%"></td>
                <td width="40%" class="signature-box">
                    <p>
                        Seputih Agung, {{ now()->translatedFormat('d F Y') }}<br>
                        Guru Mata Pelajaran
                        <br><br><br>
                        <u><b>{{ $guru->nama ?? '-' }}</b></u><br>
                        NIP. {{ $guru->nip ?? '-' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>