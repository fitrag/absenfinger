<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Nilai Harian Ke-{{ $nilai->harian_ke }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            position: relative;
        }

        .kop-image {
            width: 80px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .page-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .data-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
        }

        .signature-box {
            width: 40%;
            text-align: center;
            float: right;
        }

        .footer-avg {
            font-weight: bold;
            text-align: right;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #000;
            margin-top: -1px;
            /* Join with table */
        }
    </style>
</head>

<body>
    <div class="header">
        @if($kopBase64)
            <img src="{{ $kopBase64 }}" class="kop-image" alt="Logo">
        @endif
        <h2 style="margin: 0;">{{ $settings['school_name'] ?? 'NAMA SEKOLAH' }}</h2>
        <p style="margin: 5px 0;">{{ $settings['school_address'] ?? 'Alamat Sekolah' }}</p>
    </div>

    <div class="page-title">
        Laporan Nilai Harian
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Guru</td>
            <td width="2%">:</td>
            <td width="33%">{{ $guru->nm_guru }}</td>
            <td width="15%">Tahun Pelajaran</td>
            <td width="2%">:</td>
            <td width="33%">{{ $nilai->tp->nm_tp }}</td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $nilai->mapel->nm_mapel }}</td>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $nilai->semester }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $nilai->kelas->nm_kls }}</td>
            <td>Harian Ke-</td>
            <td>:</td>
            <td>{{ $nilai->harian_ke }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS</th>
                <th>Nama Siswa</th>
                <th width="15%">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilai->details as $idx => $detail)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ $detail->student->nis ?? '-' }}</td>
                    <td>{{ $detail->student->name }}</td>
                    <td style="text-align: center;">{{ $detail->nilai }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-avg">
        Rata-Rata Kelas: {{ number_format($rataRata, 2) }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="center">
                    <p>
                        Seputih Agung, {{ now()->translatedFormat('d F Y') }}<br>
                        Guru Mata Pelajaran
                        <br><br><br><br>
                        <u><b>{{ $guru->nm_guru }}</b></u><br>
                        NIP. {{ $guru->nip ?? '-' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>